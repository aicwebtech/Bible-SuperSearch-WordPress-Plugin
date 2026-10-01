<?php

namespace BibleSuperSearch\Common\Tests\Unit;

use BibleSuperSearch\Common\Tests\Support\Fixtures;
use BibleSuperSearch\Common\Tests\Support\TestOptions;
use PHPUnit\Framework\TestCase;

/**
 * Landing pages are supplied by the platform (a WordPress page/post containing
 * the shortcode); the abstract only selects among them.
 */
class LandingPageTest extends TestCase
{
    protected function makeOptions(array $stored = [], array $pages = null)
    {
        return new TestOptions([
            'options'       => $stored,
            'statics'       => Fixtures::statics(),
            'landing_pages' => $pages === null ? Fixtures::landingPages() : $pages,
        ]);
    }

    public function testLandingPageOptionsAreOfferedWithANoneEntry()
    {
        $options = $this->makeOptions()->getLandingPageOptions();

        $this->assertSame(['value' => '0', 'label' => 'None'], $options[0]);
        $this->assertCount(3, $options);
    }

    public function testTheNoneEntryCanBeExcluded()
    {
        $options = $this->makeOptions()->getLandingPageOptions(true);

        $this->assertCount(2, $options);
        $this->assertSame('12', $options[0]['value']);
    }

    public function testHasLandingPageOptions()
    {
        $this->assertTrue($this->makeOptions()->hasLandingPageOptions());
        $this->assertFalse($this->makeOptions([], [])->hasLandingPageOptions());
    }

    public function testGetLandingPageById()
    {
        $this->assertSame('Study Page', $this->makeOptions()->getLandingPageById('34')['label']);
        $this->assertFalse($this->makeOptions()->getLandingPageById('999'));
    }

    public function testGetLandingPageUsesTheConfiguredDestination()
    {
        $options = $this->makeOptions(['defaultDestinationPage' => '12']);

        $this->assertSame('Bible Search', $options->getLandingPage()['label']);
    }

    /** 0 is the "None" sentinel - there is no landing page. */
    public function testNoDestinationPageMeansNoLandingPage()
    {
        $this->assertFalse($this->makeOptions(['defaultDestinationPage' => 0])->getLandingPage());
    }

    public function testAStaleDestinationPageIdYieldsNoLandingPage()
    {
        $this->assertFalse($this->makeOptions(['defaultDestinationPage' => '999'])->getLandingPage());
    }
}
