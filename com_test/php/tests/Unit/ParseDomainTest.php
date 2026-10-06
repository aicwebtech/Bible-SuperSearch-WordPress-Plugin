<?php

namespace BibleSuperSearch\Common\Tests\Unit;

use BibleSuperSearch\Common\Tests\Support\TestOptions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * OptionsAbstract::parseDomain() reduces a URL or host to a bare domain, which
 * is sent to the API so a licence/allow-list check can be made.
 */
class ParseDomainTest extends TestCase
{
    #[DataProvider('hostProvider')]
    public function testParseDomain($host, $expected)
    {
        $this->assertSame($expected, TestOptions::parseDomain($host));
    }

    public static function hostProvider()
    {
        return [
            'bare host'         => ['biblesupersearch.com', 'biblesupersearch.com'],
            'https url'         => ['https://api.biblesupersearch.com', 'api.biblesupersearch.com'],
            'http url'          => ['http://biblesupersearch.com', 'biblesupersearch.com'],
            'with path'         => ['https://example.com/wp/index.php', 'example.com'],
            'trailing slash'    => ['https://example.com/', 'example.com'],
            'www stripped'      => ['http://www.example.com/path', 'example.com'],
            'port stripped'     => ['https://example.com:8080/x', 'example.com'],
            'fragment stripped' => ['example.com#top', 'example.com'],
            'surrounding space' => ['  https://example.com  ', 'example.com'],
            'subdomain kept'    => ['https://demo.example.co.uk', 'demo.example.co.uk'],
            'localhost'         => ['localhost', null],
            'localhost port'    => ['http://localhost:8080', null],
            'empty string'      => ['', null],
            'null'              => [null, null],
        ];
    }
}
