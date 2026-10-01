<?php

namespace BibleSuperSearch\Common\Tests\Unit;

use BibleSuperSearch\Common\Tests\Support\Fixtures;
use BibleSuperSearch\Common\Tests\Support\TestOptions;
use PHPUnit\Framework\TestCase;

/**
 * getOptions()/setOptions() - reading stored options back out, and the
 * normalisation getOptions() applies on the way.
 */
class OptionsStorageTest extends TestCase
{
    protected function makeOptions(array $stored = null)
    {
        $config = ['statics' => Fixtures::statics()];

        if ($stored !== null) {
            $config['options'] = $stored;
        }

        return new TestOptions($config);
    }

    public function testUnsavedInstallGetsAndStoresTheDefaults()
    {
        $options = $this->makeOptions();

        $this->assertNull($options->getStoredOptions());

        $fetched = $options->getOptions();

        $this->assertSame('https://api.biblesupersearch.com', $fetched['apiUrl']);
        $this->assertIsArray($options->getStoredOptions(), 'defaults should have been written back');
    }

    public function testDontSetDefaultLeavesStorageUntouched()
    {
        $options = $this->makeOptions();

        $fetched = $options->getOptions(TRUE);

        $this->assertSame($options->getDefaultOptions(), $fetched);
        $this->assertNull($options->getStoredOptions(), 'defaults should not have been written back');
    }

    public function testMissingKeysFallBackToTheirDefaults()
    {
        $options = $this->makeOptions(['apiUrl' => 'https://my.api.example.com']);

        $fetched = $options->getOptions();

        $this->assertSame('https://my.api.example.com', $fetched['apiUrl']);
        $this->assertSame('Expanding', $fetched['interface']);
    }

    public function testLegacyCommaSeparatedDefaultBibleIsSplitIntoAnArray()
    {
        $options = $this->makeOptions(['defaultBible' => 'kjv,web']);

        $this->assertSame(['kjv', 'web'], $options->getOptions()['defaultBible']);
    }

    public function testDefaultBibleArrayIsCompactedAndReindexed()
    {
        $options = $this->makeOptions(['defaultBible' => [0 => 'kjv', 1 => '', 2 => 'web']]);

        $this->assertSame(['kjv', 'web'], $options->getOptions()['defaultBible']);
    }

    public function testDefaultBiblesAreForcedIntoTheEnabledListWhenNotAllAreEnabled()
    {
        $options = $this->makeOptions([
            'enableAllBibles' => false,
            'enabledBibles'   => ['web'],
            'defaultBible'    => ['kjv'],
        ]);

        $enabled = $options->getOptions()['enabledBibles'];

        $this->assertContains('kjv', $enabled);
        $this->assertContains('web', $enabled);
    }

    public function testPerLanguageDefaultBiblesAreAlsoForcedIntoTheEnabledList()
    {
        $options = $this->makeOptions([
            'enableAllBibles'           => false,
            'enabledBibles'             => ['web'],
            'defaultBible'              => ['web'],
            'enableDefaultBiblesByLang' => true,
            'defaultBiblesByLanguage'   => ['es' => ['rvg'], 'de' => ['lut']],
        ]);

        $enabled = $options->getOptions()['enabledBibles'];

        $this->assertContains('rvg', $enabled);
        $this->assertContains('lut', $enabled);
        $this->assertSame(array_values(array_unique($enabled)), $enabled, 'enabledBibles should be unique and reindexed');
    }

    public function testPerLanguageDefaultBiblesAreDiscardedWhenTheFeatureIsOff()
    {
        $options = $this->makeOptions([
            'enableDefaultBiblesByLang' => false,
            'defaultBiblesByLanguage'   => ['es' => ['rvg']],
        ]);

        $this->assertSame([], $options->getOptions()['defaultBiblesByLanguage']);
    }

    public function testSetOptionsValidatesThenStores()
    {
        $options = $this->makeOptions();

        $options->setOptions(['_tab' => 'general', 'interface' => 'Classic']);

        $stored = $options->getStoredOptions();

        $this->assertSame('Classic', $stored['interface']);
        $this->assertSame('https://api.biblesupersearch.com', $stored['apiUrl'], 'untouched tabs keep their values');
    }

    public function testSetDefaultOptionsStoresTheDefaults()
    {
        $options = $this->makeOptions(['interface' => 'Classic']);

        $options->setDefaultOptions();

        $this->assertSame('Expanding', $options->getStoredOptions()['interface']);
    }

    public function testGetUrlFallsBackToTheDefaultApiUrl()
    {
        $this->assertSame('https://api.biblesupersearch.com', $this->makeOptions(['apiUrl' => ''])->getUrl());
        $this->assertSame('https://my.api.example.com', $this->makeOptions(['apiUrl' => 'https://my.api.example.com'])->getUrl());
    }

    public function testApiVersionComesFromTheStatics()
    {
        $this->assertSame('5.0.0', $this->makeOptions()->apiVersion());
        $this->assertSame('0.0.0', (new TestOptions())->apiVersion(), 'no statics means no version');
    }
}
