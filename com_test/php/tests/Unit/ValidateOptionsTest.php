<?php

namespace BibleSuperSearch\Common\Tests\Unit;

use BibleSuperSearch\Common\Tests\Support\Fixtures;
use BibleSuperSearch\Common\Tests\Support\TestOptions;
use PHPUnit\Framework\TestCase;

/**
 * validateOptions() casts each submitted field according to the 'type' declared
 * for it in options_list.php, then applies a handful of special cases.
 */
class ValidateOptionsTest extends TestCase
{
    /** @var TestOptions */
    protected $options;

    protected function setUp(): void
    {
        $this->options = new TestOptions(['statics' => Fixtures::statics()]);
    }

    public function testCheckboxFieldsBecomeBooleans()
    {
        $valid = $this->options->validateOptions(['_tab' => 'features', 'toggleAdvanced' => '1']);

        $this->assertTrue($valid['toggleAdvanced']);
    }

    /** An unchecked box is simply absent from the submission. */
    public function testOmittedCheckboxFieldsBecomeFalse()
    {
        $valid = $this->options->validateOptions(['_tab' => 'features']);

        $this->assertFalse($valid['toggleAdvanced']);
    }

    public function testIntegerFieldsAreCastToInt()
    {
        $valid = $this->options->validateOptions(['_tab' => 'advanced', 'pageScrollTopPadding' => '42']);

        $this->assertSame(42, $valid['pageScrollTopPadding']);
    }

    public function testJsonFieldsAcceptEncodedStrings()
    {
        $valid = $this->options->validateOptions([
            '_tab' => 'bible',
            'parallelBibleLimitByWidth' => '{"600":1,"900":2}',
        ]);

        $this->assertSame([600 => 1, 900 => 2], $valid['parallelBibleLimitByWidth']);
    }

    public function testJsonFieldsAcceptArraysAsIs()
    {
        $valid = $this->options->validateOptions([
            '_tab' => 'bible',
            'parallelBibleLimitByWidth' => [600 => 1],
        ]);

        $this->assertSame([600 => 1], $valid['parallelBibleLimitByWidth']);
    }

    public function testMalformedJsonBecomesAnEmptyArrayRatherThanGarbage()
    {
        $valid = $this->options->validateOptions([
            '_tab' => 'bible',
            'parallelBibleLimitByWidth' => 'not json at all',
        ]);

        $this->assertSame([], $valid['parallelBibleLimitByWidth']);
    }

    public function testFieldsOnOtherTabsAreLeftAlone()
    {
        $options = new TestOptions([
            'options' => ['interface' => 'Classic', 'apiUrl' => 'https://my.api.example.com'],
            'statics' => Fixtures::statics(),
        ]);

        $valid = $options->validateOptions(['_tab' => 'advanced', 'apiUrl' => 'https://my.api.example.com']);

        $this->assertSame('Classic', $valid['interface'], 'the general tab was not submitted');
    }

    public function testTabAllValidatesEveryTab()
    {
        $valid = $this->options->validateOptions([
            '_tab'                      => 'all',
            'interface'                 => 'Classic',
            'pageScrollTopPadding'      => '15',
            'toggleAdvanced'            => '1',
        ]);

        $this->assertSame('Classic', $valid['interface']);
        $this->assertSame(15, $valid['pageScrollTopPadding']);
        $this->assertTrue($valid['toggleAdvanced']);
    }

    public function testUnknownFieldsAreDropped()
    {
        $valid = $this->options->validateOptions(['_tab' => 'general', 'not_an_option' => 'x']);

        $this->assertArrayNotHasKey('not_an_option', $valid);
        $this->assertArrayNotHasKey('_tab', $valid);
    }

    public function testEveryDefaultIsPresentInTheValidatedResult()
    {
        $valid = $this->options->validateOptions(['_tab' => 'general']);

        foreach ($this->options->getDefaultOptions() as $key => $default) {
            $this->assertArrayHasKey($key, $valid, $key . ' fell out of the validated options');
        }
    }

    public function testAnEmptyApiUrlRevertsToTheDefault()
    {
        $valid = $this->options->validateOptions(['_tab' => 'advanced', 'apiUrl' => '']);

        $this->assertSame('https://api.biblesupersearch.com', $valid['apiUrl']);
        $this->assertFalse($this->options->refresh_statics);
    }

    public function testChangingTheApiUrlFlagsStaticsForRefresh()
    {
        $valid = $this->options->validateOptions(['_tab' => 'advanced', 'apiUrl' => 'https://my.api.example.com']);

        $this->assertSame('https://my.api.example.com', $valid['apiUrl']);
        $this->assertTrue($this->options->refresh_statics);
    }

    public function testEnablingAllBiblesClearsTheEnabledList()
    {
        $valid = $this->options->validateOptions([
            '_tab'            => 'bible',
            'enableAllBibles' => '1',
            'enabledBibles'   => ['kjv'],
        ]);

        $this->assertSame([], $valid['enabledBibles']);
    }

    public function testDisablingAllBiblesKeepsTheEnabledList()
    {
        $valid = $this->options->validateOptions([
            '_tab'          => 'bible',
            'enabledBibles' => ['kjv', 'web'],
        ]);

        $this->assertSame(['kjv', 'web'], $valid['enabledBibles']);
    }

    public function testEnablingAllLanguagesClearsTheLanguageList()
    {
        $valid = $this->options->validateOptions([
            '_tab'                => 'language',
            'enableAllLanguages'  => '1',
            'languageList'        => ['en', 'es'],
        ]);

        $this->assertSame([], $valid['languageList']);
    }

    /** The default language must always be selectable. */
    public function testTheDefaultLanguageIsAddedToARestrictedLanguageList()
    {
        $valid = $this->options->validateOptions([
            '_tab'          => 'language',
            'language'      => 'es',
            'languageList'  => ['en'],
        ]);

        $this->assertSame(['en', 'es'], $valid['languageList']);
    }

    public function testTheDefaultLanguageIsNotDuplicated()
    {
        $valid = $this->options->validateOptions([
            '_tab'          => 'language',
            'language'      => 'en',
            'languageList'  => ['en', 'es'],
        ]);

        $this->assertSame(['en', 'es'], $valid['languageList']);
    }

    public function testLandingReferenceIsTrimmedAndWhitespaceCollapsed()
    {
        $valid = $this->options->validateOptions([
            '_tab'              => 'bible',
            'landingReference'  => "  John   3:16  ",
        ]);

        $this->assertSame('John 3:16', $valid['landingReference']);
    }

    public function testLandingReferenceStripsPunctuationThatIsNotAReference()
    {
        $valid = $this->options->validateOptions([
            '_tab'              => 'bible',
            'landingReference'  => 'John 3:16 & Rom 1:1',
        ]);

        $this->assertSame('John 3:16 Rom 1:1', $valid['landingReference']);
    }
}
