<?php

namespace BibleSuperSearch\Common\Tests\Unit;

use BibleSuperSearch\Common\Tests\Support\Fixtures;
use BibleSuperSearch\Common\Tests\Support\TestOptions;
use PHPUnit\Framework\TestCase;

/**
 * Sorting, grouping and filtering of the Bible list that comes back from the API.
 */
class BiblesTest extends TestCase
{
    protected function makeOptions(array $stored = [], $with_statics = true)
    {
        return new TestOptions([
            'options' => $stored,
            'statics' => $with_statics ? Fixtures::statics() : null,
        ]);
    }

    public function testNoStaticsMeansNoBibles()
    {
        $this->assertSame([], $this->makeOptions([], false)->getBibles());
    }

    public function testUngroupedSortByName()
    {
        $bibles = $this->makeOptions()->getBibles([], 'name', 'none');

        $this->assertSame(['kjv', 'lut', 'rvg', 'web'], array_keys($bibles));
    }

    public function testUngroupedBiblesGetADisplayLabel()
    {
        $bibles = $this->makeOptions()->getBibles([], 'name', 'none');

        $this->assertSame('King James Version (English)', $bibles['kjv']['display']);
        $this->assertSame('King James Version', $bibles['kjv']['display_short']);
        $this->assertNull($bibles['kjv']['group_value']);
        $this->assertNull($bibles['kjv']['group_name']);
    }

    public function testSortByYear()
    {
        $bibles = $this->makeOptions()->getBibles([], 'year', 'none');

        $this->assertSame(['kjv', 'lut', 'web', 'rvg'], array_keys($bibles));
    }

    /** Grouping prepends the group column to the sort, so language wins over name. */
    public function testGroupingByEnglishLanguageNameSortsByLanguageFirst()
    {
        $bibles = $this->makeOptions()->getBibles([], 'name', 'language_english');

        $this->assertSame(['kjv', 'web', 'lut', 'rvg'], array_keys($bibles));
        $this->assertSame('en', $bibles['kjv']['group_value']);
        $this->assertSame('English - (EN)', $bibles['kjv']['group_name']);
        $this->assertSame('German - (DE)', $bibles['lut']['group_name']);
    }

    public function testGroupingByEndonym()
    {
        $bibles = $this->makeOptions()->getBibles([], 'name', 'language');

        $this->assertSame('Deutsch - (DE)', $bibles['lut']['group_name']);
        $this->assertSame('Espanol - (ES)', $bibles['rvg']['group_name']);
    }

    public function testGroupingByEndonymAndEnglishName()
    {
        $bibles = $this->makeOptions()->getBibles([], 'name', 'language_and_english');

        $this->assertSame('Deutsch / German - (DE)', $bibles['lut']['group_name']);
        $this->assertSame('English - (EN)', $bibles['kjv']['group_name'], 'English name is not repeated');
    }

    /** Missing endonyms fall back to the English language name. */
    public function testGroupingByEndonymFallsBackToTheEnglishName()
    {
        $statics = Fixtures::statics();
        $statics['bibles']['lut']['lang_native'] = '';

        $bibles = $this->makeOptions()->getBibles($statics, 'name', 'language');

        $this->assertSame('German - (DE)', $bibles['lut']['group_name']);
    }

    public function testSortingAndGroupingFallBackToTheSavedOptions()
    {
        $options = $this->makeOptions([
            'bibleSorting'  => 'name',
            'bibleGrouping' => 'language_english',
        ]);

        $this->assertSame(['kjv', 'web', 'lut', 'rvg'], array_keys($options->getBibles()));
    }

    public function testGetBibleReturnsASingleBible()
    {
        $bible = $this->makeOptions()->getBible('kjv');

        $this->assertSame('KJV', $bible['shortname']);
    }

    public function testGetBibleReturnsFalseForAnUnknownModule()
    {
        $this->assertFalse($this->makeOptions()->getBible('no_such_module'));
    }

    public function testGetBibleReturnsFalseWhenThereAreNoStatics()
    {
        $this->assertFalse($this->makeOptions([], false)->getBible('kjv'));
    }

    public function testEveryBibleIsEnabledWhenEnableAllBiblesIsSet()
    {
        $options = $this->makeOptions([
            'enableAllBibles' => true,
            'enabledBibles'   => ['kjv'],
        ]);

        $this->assertSame(['kjv', 'lut', 'rvg', 'web'], array_keys($options->getEnabledBibles([], 'name', 'none')));
    }

    public function testOnlySelectedBiblesAreEnabled()
    {
        $options = $this->makeOptions([
            'enableAllBibles' => false,
            'enabledBibles'   => ['web', 'lut'],
            'defaultBible'    => ['web'],
        ]);

        $this->assertSame(['lut', 'web'], array_keys($options->getEnabledBibles([], 'name', 'none')));
    }

    /**
     * Clearing the selection cannot leave the client with nothing to display:
     * getOptions() restores the default Bible and forces it into the list.
     */
    public function testAnEmptySelectionStillEnablesTheDefaultBible()
    {
        $options = $this->makeOptions([
            'enableAllBibles' => false,
            'enabledBibles'   => [],
            'defaultBible'    => [],
        ]);

        $this->assertSame(['kjv'], array_keys($options->getEnabledBibles([], 'name', 'none')));
    }

    /**
     * The admin selector needs a subheader row per language, followed by that
     * language's Bibles.
     */
    public function testBiblesForDisplayAreGroupedUnderLanguageSubheaders()
    {
        $display = $this->makeOptions()->callGetBiblesForDisplay();

        $rows = array_map(function ($row) {
            return (isset($row['type']) ? $row['type'] : 'item') . ':' . $row['label'];
        }, $display);

        $this->assertSame(
            [
                'subheader:English',
                'item:King James Version',
                'item:World English Bible',
                'subheader:German',
                'item:Luther Bible',
                'subheader:Spanish',
                'item:Reina Valera Gomez',
            ],
            $rows
        );
    }

    public function testBiblesForDisplayCarryTheirGroupAndSubtitle()
    {
        $display = $this->makeOptions()->callGetBiblesForDisplay();
        $by_value = [];

        foreach ($display as $row) {
            if (isset($row['value'])) {
                $by_value[$row['value']] = $row;
            }
        }

        $this->assertSame('en', $by_value['kjv']['group']);
        $this->assertNull($by_value['kjv']['itemProps']['subtitle'], 'short names need no subtitle');

        $statics = Fixtures::statics();
        $statics['bibles']['kjv']['name'] = str_repeat('Long Bible Name ', 4);
        $options = new TestOptions(['statics' => $statics]);
        $display = $options->callGetBiblesForDisplay();

        foreach ($display as $row) {
            if (isset($row['value']) && $row['value'] === 'kjv') {
                $this->assertSame('KJV', $row['itemProps']['subtitle'], 'long names get the short name as a subtitle');
            }
        }
    }
}
