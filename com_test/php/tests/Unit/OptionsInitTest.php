<?php

namespace BibleSuperSearch\Common\Tests\Unit;

use BibleSuperSearch\Common\Tests\Support\Fixtures;
use BibleSuperSearch\Common\Tests\Support\TestOptions;
use PHPUnit\Framework\TestCase;

/**
 * options_list.php is the single source of truth for defaults, the admin UI and
 * validation. These cover the wiring initOptions() does when it reads that file.
 */
class OptionsInitTest extends TestCase
{
    /** @var TestOptions */
    protected $options;

    protected function setUp(): void
    {
        $this->options = new TestOptions([
            'statics'       => Fixtures::statics(),
            'landing_pages' => Fixtures::landingPages(),
        ]);
    }

    public function testDefaultsAreBuiltFromTheOptionsList()
    {
        $defaults = $this->options->getDefaultOptions();

        $this->assertArrayHasKey('interface', $defaults);
        $this->assertArrayHasKey('apiUrl', $defaults);
        $this->assertSame('https://api.biblesupersearch.com', $defaults['apiUrl']);
    }

    /**
     * The 'default' entries in options_list.php win over the hard coded
     * $default_options array in OptionsAbstract.
     */
    public function testOptionsListDefaultsOverrideTheHardCodedDefaults()
    {
        $defaults = $this->options->getDefaultOptions();

        $this->assertSame('Expanding', $defaults['interface']);
        $this->assertFalse($defaults['toggleAdvanced']);
    }

    public function testEveryTabExposesItsOptionFields()
    {
        $tabs = $this->options->getTabs();

        foreach (['general', 'features', 'bible', 'language', 'advanced'] as $tab) {
            $this->assertArrayHasKey($tab, $tabs, $tab . ' tab is missing');
            $this->assertSame($tab, $tabs[$tab]['id']);
            $this->assertSame('config', $tabs[$tab]['type']);
            $this->assertNotEmpty($tabs[$tab]['options'], $tab . ' tab has no options');
        }

        $this->assertContains('apiUrl', $tabs['advanced']['options']);
        $this->assertContains('interface', $tabs['general']['options']);
    }

    public function testEveryFieldIsTaggedWithItsOwnName()
    {
        foreach ($this->options->getOptionsList() as $tab => $fields) {
            foreach ($fields as $field => $settings) {
                $this->assertSame($field, $settings['field'], $tab . '.' . $field . ' has the wrong field tag');
            }
        }
    }

    /**
     * A field's 'items' may name a selector list in selector_options_list.php...
     */
    public function testSelectorNamedItemsAreResolvedAndReformatted()
    {
        $list = $this->options->getOptionsList();
        $items = $list['bible']['bibleGrouping']['items'];

        $this->assertIsArray($items);
        $this->assertSame(['value' => 'none', 'label' => 'None'], $items[0]);
    }

    /** ...or a method on the Options class. */
    public function testMethodNamedItemsAreResolved()
    {
        $list = $this->options->getOptionsList();
        $labels = array_column($list['general']['interface']['items'], 'label');

        $this->assertContains('Expanding', $labels);
    }

    public function testLandingPageItemsComeFromThePlatformLookup()
    {
        $list = $this->options->getOptionsList();
        $items = $list['general']['defaultDestinationPage']['items'];

        $this->assertSame(
            [
                ['value' => '0', 'label' => 'None'],
                ['value' => '12', 'label' => 'Bible Search'],
                ['value' => '34', 'label' => 'Study Page'],
            ],
            $items
        );
    }

    public function testGetSelectorOptionsReturnsFalseForAnUnknownSelector()
    {
        $this->assertIsArray($this->options->getSelectorOptions('bibleGrouping'));
        $this->assertFalse($this->options->getSelectorOptions('no_such_selector'));
    }

    public function testLanguageHelpers()
    {
        $with_default = $this->options->getLanguagesWithGlobalDefault();
        $without = $this->options->getLanguages();

        $this->assertArrayHasKey('global_default', $with_default);
        $this->assertArrayNotHasKey('global_default', $without);
        $this->assertArrayHasKey('en', $without);
        $this->assertSame('English', $this->options->getLanguageNameByCode('en'));
        $this->assertNull($this->options->getLanguageNameByCode('xx'));
    }

    public function testGetInterfaceByNameAcceptsIdNameAndLooseSpelling()
    {
        $by_id = $this->options->getInterfaceByName('Expanding');
        $this->assertSame('Expanding', $by_id['id']);
        $this->assertSame('expanding', $by_id['class']);

        $this->assertSame('Expanding', $this->options->getInterfaceByName('expanding')['id']);
        $this->assertSame('ExpandingLargeInput', $this->options->getInterfaceByName('Expanding - Large Input')['id']);
        $this->assertNull($this->options->getInterfaceByName('Not A Skin'));
    }

    public function testProcessInterfaceNameStripsHyphensSpacesAndCase()
    {
        $this->assertSame('ExpandingLargeInput', $this->options->callProcessInterfaceName('Expanding - Large Input'));
        $this->assertSame('Expanding', $this->options->callProcessInterfaceName('expanding'));
    }

    public function testReformatItemsListConvertsMapsToValueLabelPairs()
    {
        $result = $this->options->callReformatItemsList(['a' => 'Apple', 'b' => 'Ball']);

        $this->assertSame(
            [
                ['value' => 'a', 'label' => 'Apple'],
                ['value' => 'b', 'label' => 'Ball'],
            ],
            $result
        );
    }

    public function testReformatItemsListLeavesPlainListsAlone()
    {
        $this->assertSame(['x', 'y'], $this->options->callReformatItemsList(['x', 'y']));
        $this->assertSame([], $this->options->callReformatItemsList([]));
        $this->assertSame('bibles', $this->options->callReformatItemsList('bibles'));
    }

    public function testReformatItemsListCopiesPassthruKeys()
    {
        $items = [
            'kjv' => ['name' => 'King James Version', 'lang' => 'English', 'rank' => 1],
        ];

        $result = $this->options->callReformatItemsList($items, ['lang', 'missing']);

        $this->assertSame('kjv', $result[0]['value']);
        $this->assertSame('King James Version', $result[0]['label']);
        $this->assertSame('English', $result[0]['lang']);
        $this->assertNull($result[0]['missing']);
        $this->assertArrayNotHasKey('rank', $result[0]);
    }
}
