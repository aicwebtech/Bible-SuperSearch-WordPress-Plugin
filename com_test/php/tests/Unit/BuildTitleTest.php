<?php

namespace BibleSuperSearch\Common\Tests\Unit;

use BibleSuperSearch\Common\QueryStringParser;
use PHPUnit\Framework\TestCase;

/**
 * QueryStringParser::buildTitle() turns form data into a browser/page title.
 *
 * It collects a fixed list of fields in a fixed order and joins them with
 * ' | '. An optional base title (the site name) is joined on with ' - ', on
 * either side depending on $baseFirst.
 */
class BuildTitleTest extends TestCase
{
    const SEP = ' - ';

    public function testNoRecognisedFieldsYieldsAnEmptyTitle()
    {
        $this->assertSame('', QueryStringParser::buildTitle([]));
    }

    public function testRequestBecomesTheTitle()
    {
        $this->assertSame('John 3:16', QueryStringParser::buildTitle(['request' => 'John 3:16']));
    }

    public function testReferenceBecomesTheTitle()
    {
        $this->assertSame('John 3', QueryStringParser::buildTitle(['reference' => 'John 3']));
    }

    public function testSearchBecomesTheTitle()
    {
        $this->assertSame('faith', QueryStringParser::buildTitle(['search' => 'faith']));
    }

    public function testEverySearchVariantContributes()
    {
        $form = [
            'search_all'    => 'all',
            'search_any'    => 'any',
            'search_one'    => 'one',
            'search_none'   => 'none',
            'search_phrase' => 'phrase',
        ];

        $this->assertSame('all | any | one | none | phrase', QueryStringParser::buildTitle($form));
    }

    /** Fields appear in the order buildTitle() lists them, not submission order. */
    public function testFieldOrderIsFixed()
    {
        $form = [
            'search'    => 'faith',
            'reference' => 'John 3',
            'request'   => 'John',
        ];

        $this->assertSame('John | John 3 | faith', QueryStringParser::buildTitle($form));
    }

    public function testPageIsLabelled()
    {
        $this->assertSame(
            'John 3:16 | Page 2',
            QueryStringParser::buildTitle(['request' => 'John 3:16', 'page' => 2])
        );
    }

    public function testPageAloneIsStillLabelled()
    {
        $this->assertSame('Page 3', QueryStringParser::buildTitle(['page' => 3]));
    }

    public function testContextIsFlagged()
    {
        $this->assertSame(
            'John 3:16 | In Context',
            QueryStringParser::buildTitle(['request' => 'John 3:16', 'context' => true])
        );
    }

    public function testContextIsOmittedWhenNotSet()
    {
        $this->assertSame(
            'John 3:16',
            QueryStringParser::buildTitle(['request' => 'John 3:16', 'context' => false])
        );
    }

    /** In Context always sorts last, after every text field. */
    public function testContextComesLast()
    {
        $form = [
            'context'   => true,
            'reference' => 'John 3',
            'request'   => 'John',
        ];

        $this->assertSame('John | John 3 | In Context', QueryStringParser::buildTitle($form));
    }

    public function testEmptyFieldsAreSkipped()
    {
        $form = [
            'request'   => 'John 3:16',
            'reference' => '',
            'search'    => '',
        ];

        $this->assertSame('John 3:16', QueryStringParser::buildTitle($form));
    }

    /** Form data carries plenty that does not belong in a title. */
    public function testUnrecognisedFieldsAreIgnored()
    {
        $form = [
            'bible'                 => ['kjv', 'web'],
            'search_type'           => 'all',
            'results_list_cache_id' => 'uuid123',
        ];

        $this->assertSame('', QueryStringParser::buildTitle($form));
    }

    /* The base title (site name) parameters. */

    public function testBaseTitleIsAppendedAfterTheFormTitle()
    {
        $this->assertSame(
            'John 3:16' . self::SEP . 'My Site',
            QueryStringParser::buildTitle(['request' => 'John 3:16'], 'My Site')
        );
    }

    public function testBaseFirstPutsTheBaseTitleInFront()
    {
        $this->assertSame(
            'My Site' . self::SEP . 'John 3:16',
            QueryStringParser::buildTitle(['request' => 'John 3:16'], 'My Site', true)
        );
    }

    /** With nothing from the form, the base title stands alone - no separator. */
    public function testBaseTitleAloneWhenThereIsNoFormTitle()
    {
        $this->assertSame('My Site', QueryStringParser::buildTitle([], 'My Site'));
        $this->assertSame('My Site', QueryStringParser::buildTitle([], 'My Site', true));
    }

    public function testNoBaseTitleLeavesTheFormTitleAlone()
    {
        $this->assertSame('John 3:16', QueryStringParser::buildTitle(['request' => 'John 3:16'], ''));
    }

    /* Composition with the route parser. */

    public function testTitleBuiltFromAParsedPassageRoute()
    {
        $form = QueryStringParser::parseToFormData('/p/kjv/John/3/16');

        $this->assertSame('John 3:16', QueryStringParser::buildTitle($form));
    }

    public function testTitleBuiltFromAParsedContextRoute()
    {
        $form = QueryStringParser::parseToFormData('/context/kjv/John/3/16');

        $this->assertSame('John 3:16 | In Context', QueryStringParser::buildTitle($form));
    }

    public function testTitleBuiltFromAnEmptyRoute()
    {
        $this->assertSame('', QueryStringParser::buildTitle(QueryStringParser::parseToFormData('')));
    }
}
