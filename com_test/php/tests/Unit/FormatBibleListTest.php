<?php

namespace BibleSuperSearch\Common\Tests\Unit;

use BibleSuperSearch\Common\QueryStringParser;
use BibleSuperSearch\Common\Tests\Support\Fixtures;
use BibleSuperSearch\Common\Tests\Support\TestOptions;
use PHPUnit\Framework\TestCase;

/**
 * QueryStringParser::formatBibleList() turns the 'bible' element of the form
 * data into human readable text for the page description.
 *
 * Its input comes straight off the URL - the 'f' route hands over whatever JSON
 * a visitor supplied - so most of these cases are about not fataling on junk.
 */
class FormatBibleListTest extends TestCase
{
    /** @var TestOptions */
    protected $options;

    protected function setUp(): void
    {
        $this->options = new TestOptions(['statics' => Fixtures::statics()]);
    }

    protected function format($bible_list)
    {
        return QueryStringParser::formatBibleList($bible_list, $this->options);
    }

    public function testASingleKnownBibleUsesItsFullName()
    {
        $this->assertSame('King James Version', $this->format(['kjv']));
    }

    public function testSeveralBiblesAreJoinedWithCommas()
    {
        $this->assertSame(
            'King James Version, World English Bible',
            $this->format(['kjv', 'web'])
        );
    }

    /** An unknown module still reads sensibly rather than disappearing. */
    public function testAnUnknownModuleFallsBackToItsUpperCasedName()
    {
        $this->assertSame('GENEVA', $this->format(['geneva']));
    }

    public function testUnderscoresBecomeSpacesInTheFallback()
    {
        $this->assertSame('SOME BIBLE', $this->format(['some_bible']));
    }

    public function testSpecialCasesAreNamedExplicitly()
    {
        $this->assertSame("KJV with Strong's", $this->format(['kjv_strongs']));
    }

    public function testKnownAndUnknownModulesMix()
    {
        $this->assertSame('King James Version, GENEVA', $this->format(['kjv', 'geneva']));
    }

    public function testAnEmptyListYieldsNothing()
    {
        $this->assertNull($this->format([]));
    }

    /* ---- untrusted input: none of these may fatal ---- */

    public function testNullYieldsNothing()
    {
        $this->assertNull($this->format(null));
    }

    /** ?q= with no route at all, or a route that carries no bible. */
    public function testAMissingBibleKeyYieldsNothing()
    {
        $form = QueryStringParser::parseToFormData('/c/abc-uuid');

        $this->assertArrayNotHasKey('bible', $form);
        $this->assertNull($this->format(isset($form['bible']) ? $form['bible'] : null));
    }

    /** The s route sets 'bible' => null when the URL carries no bible. */
    public function testTheSearchRouteWithoutABibleYieldsNothing()
    {
        $form = QueryStringParser::parseToFormData('/s//jesus');

        $this->assertNull($form['bible']);
        $this->assertNull($this->format($form['bible']));
    }

    /** ?q=/f/{"bible":"kjv"} - a bare string is treated as a one Bible list. */
    public function testAStringIsTreatedAsASingleBible()
    {
        $this->assertSame('King James Version', $this->format('kjv'));
    }

    public function testAnEmptyStringYieldsNothing()
    {
        $this->assertNull($this->format(''));
    }

    /** ?q=/f/{"bible":[[1]]} */
    public function testNestedArrayEntriesAreSkipped()
    {
        $this->assertNull($this->format([[1]]));
    }

    public function testJunkEntriesAreSkippedButValidOnesSurvive()
    {
        $this->assertSame('King James Version', $this->format(['kjv', ['nested'], null, '']));
    }

    public function testNonStringScalarEntriesAreSkipped()
    {
        $this->assertNull($this->format([1, 2.5, true, false]));
    }

    public function testANonArrayNonStringYieldsNothing()
    {
        $this->assertNull($this->format(42));
        $this->assertNull($this->format(3.14));
        $this->assertNull($this->format(true));
        $this->assertNull($this->format(new \stdClass()));
    }

    /** Every route the parser can produce must survive this call. */
    public function testEveryParsedRouteCanBeFormattedWithoutFataling()
    {
        $routes = [
            '', '/c/abc-uuid', '/s/', '/s//jesus', '/context/kjv/John', '/f/{bad json',
            '/f/{"bible":[[1]]}', '/f/{"bible":"kjv"}', '/p/kjv/John/3/16',
            '/q/kjv,web/John 3:16', '/nope/kjv/john',
        ];

        foreach ($routes as $route) {
            $form = QueryStringParser::parseToFormData($route);
            $bible = is_array($form) && isset($form['bible']) ? $form['bible'] : null;
            $result = $this->format($bible);

            $this->assertTrue(
                $result === null || is_string($result),
                $route . ' produced ' . gettype($result)
            );
        }
    }

    /** Without an Options instance the lookup is skipped, not fataled. */
    public function testWithoutOptionsTheFallbackNameIsUsed()
    {
        $this->assertSame('KJV', QueryStringParser::formatBibleList(['kjv']));
    }
}
