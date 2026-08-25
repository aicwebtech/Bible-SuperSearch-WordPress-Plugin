<?php

namespace BibleSuperSearch\Common\Tests\Unit;

use BibleSuperSearch\Common\QueryStringParser;
use PHPUnit\Framework\TestCase;

/**
 * QueryStringParser turns the client app's URL hash routes into form data.
 */
class QueryStringParserTest extends TestCase
{
    public function testAnEmptyRouteYieldsNoFormData()
    {
        $this->assertNull(QueryStringParser::parseToFormData(''));
        $this->assertNull(QueryStringParser::parseToFormData(null));
    }

    public function testAnUnknownRouteYieldsNoFormData()
    {
        $this->assertNull(QueryStringParser::parseToFormData('/nope/kjv/john'));
    }

    public function testCacheRoute()
    {
        $this->assertSame(
            ['hash' => 'abc123', 'page' => '2'],
            QueryStringParser::parseToFormData('/c/abc123/2')
        );
    }

    /** The route is parsed the same with or without a leading slash. */
    public function testALeadingSlashIsOptional()
    {
        $this->assertSame(
            QueryStringParser::parseToFormData('/c/abc123/2'),
            QueryStringParser::parseToFormData('c/abc123/2')
        );
    }

    public function testCacheRouteWithoutAPage()
    {
        $this->assertSame(
            ['hash' => 'abc123', 'page' => null],
            QueryStringParser::parseToFormData('/c/abc123')
        );
    }

    public function testHashCacheWithNoParts()
    {
        $this->assertSame(['hash' => null, 'page' => null], QueryStringParser::hashCache([]));
    }

    public function testRequestRoutePopulatesTheRequestField()
    {
        $this->assertSame(
            [
                'bible'       => ['kjv'],
                'search_type' => null,
                'reference'   => null,
                'page'        => null,
                'request'     => 'john 3:16',
            ],
            QueryStringParser::parseToFormData('/q/kjv/john 3:16')
        );
    }

    public function testRequestRouteReadsEveryPositionalPart()
    {
        $this->assertSame(
            [
                'bible'       => ['kjv', 'web'],
                'search_type' => 'all',
                'reference'   => 'John',
                'page'        => '2',
                'request'     => 'faith',
            ],
            QueryStringParser::hashSearch(['kjv,web', 'faith', '2', 'all', 'John'], true)
        );
    }

    public function testSearchRouteWithoutABibleLeavesTheBibleUnset()
    {
        $form = QueryStringParser::hashSearch(['', 'faith'], true);

        $this->assertNull($form['bible']);
    }

    public function testStrongsLookup()
    {
        $this->assertSame(['search' => 'G1234'], QueryStringParser::hashStrongs(['G1234']));
        $this->assertSame(['search' => null], QueryStringParser::hashStrongs([]));
    }

    public function testJsonFormRoute()
    {
        $this->assertSame(
            ['search' => 'grace', 'page' => 2],
            QueryStringParser::parseToFormData('/f/{"search":"grace","page":2}')
        );
    }

    public function testJsonFormRouteWithNoPayload()
    {
        $this->assertSame([], QueryStringParser::hashForm([]));
        $this->assertSame([], QueryStringParser::hashForm(['']));
    }

    /** The hash is attacker-controllable, so bad JSON must not blow up. */
    public function testMalformedJsonFormPayloadIsIgnored()
    {
        $this->assertNull(QueryStringParser::hashForm(['{not json']));
    }

    /**
     * formHasField() is currently a stub returning true, so the search term ends
     * up in 'request' even when the caller does not force it. If that stub ever
     * starts answering honestly, the 'search' branch of hashSearch() becomes
     * reachable and this expectation changes.
     */
    public function testSearchRouteWithoutTheRequestFlagStillUsesTheRequestField()
    {
        $form = QueryStringParser::hashSearch(['kjv', 'faith'], false);

        $this->assertSame('faith', $form['request']);
        $this->assertArrayNotHasKey('search', $form);
    }

    public function testPassageRouteBuildsABookChapterVerseRequest()
    {
        $this->assertSame(
            ['bible' => ['kjv'], 'request' => 'John 3:16'],
            QueryStringParser::parseToFormData('/p/kjv/John/3/16')
        );
    }

    public function testPassageRouteWithoutAVerse()
    {
        $this->assertSame(
            ['bible' => ['kjv'], 'request' => 'John 3'],
            QueryStringParser::parseToFormData('/p/kjv/John/3')
        );
    }

    public function testPassageRouteWithBookOnly()
    {
        $this->assertSame(
            ['bible' => ['kjv'], 'request' => 'John'],
            QueryStringParser::parseToFormData('/p/kjv/John')
        );
    }

    /** A chapter range cannot carry a verse, so the verse is dropped. */
    public function testPassageRouteDropsTheVerseFromAChapterRange()
    {
        $this->assertSame(
            ['bible' => ['kjv'], 'request' => 'John 3-4'],
            QueryStringParser::parseToFormData('/p/kjv/John/3-4/16')
        );
    }

    public function testPassageRouteWithoutABook()
    {
        $this->assertSame([], QueryStringParser::hashPassage(['kjv']));
    }

    public function testPassageRouteAcceptsMultipleBibles()
    {
        $form = QueryStringParser::parseToFormData('/p/kjv,web/John/3/16');

        $this->assertSame(['kjv', 'web'], $form['bible']);
    }

    /** A cross reference targets the whole book - chapter and verse are cleared. */
    public function testCrossReferenceRouteKeepsOnlyTheBook()
    {
        $this->assertSame(
            ['bible' => ['kjv'], 'request' => 'John'],
            QueryStringParser::parseToFormData('/cr/kjv/John/3/16')
        );
    }

    public function testSearchResultsLinkRouteCarriesTheResultsListCacheId()
    {
        $this->assertSame(
            [
                'bible'                  => ['kjv'],
                'request'                => 'John 3:16',
                'results_list_cache_id'  => 'uuid123',
            ],
            QueryStringParser::parseToFormData('/sl/uuid123/kjv/John/3/16')
        );
    }

    public function testContextRouteFlagsContextualLookup()
    {
        $this->assertSame(
            ['bible' => ['kjv'], 'request' => 'John 3:16', 'context' => true],
            QueryStringParser::parseToFormData('/context/kjv/John/3/16')
        );
    }

    /** Like the cr route, a plain reference targets the whole book. */
    public function testReferenceRouteKeepsOnlyTheBook()
    {
        $this->assertSame(
            ['bible' => ['kjv'], 'request' => 'John'],
            QueryStringParser::parseToFormData('/r/kjv/John/3/16')
        );
    }

    public function testSearchRoute()
    {
        $this->assertSame(
            [
                'bible'       => ['kjv'],
                'search_type' => null,
                'reference'   => null,
                'page'        => null,
                'request'     => 'faith',
            ],
            QueryStringParser::parseToFormData('/s/kjv/faith')
        );
    }

    /** The strongs route goes through hashSearch(), not hashStrongs(). */
    public function testStrongsRoute()
    {
        $form = QueryStringParser::parseToFormData('/strongs/kjv/G1234');

        $this->assertSame('G1234', $form['request']);
    }

    /** A contextual lookup needs one specific verse, so a range is rejected. */
    public function testContextRouteRejectsAChapterRange()
    {
        $this->assertNull(QueryStringParser::parseToFormData('/context/kjv/John/3-4/16'));
    }

    public function testContextRouteRejectsAMissingVerse()
    {
        $this->assertNull(QueryStringParser::parseToFormData('/context/kjv/John'));
    }
}
