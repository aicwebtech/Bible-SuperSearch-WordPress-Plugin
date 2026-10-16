<?php

namespace BibleSuperSearch\Common\Tests\Unit;

use BibleSuperSearch\Common\Tests\Support\Fixtures;
use BibleSuperSearch\Common\Tests\Support\TestOptions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * apiProxy() - relaying client requests to the configured API.
 */
class ApiProxyTest extends TestCase
{
    protected function makeOptions(array $stored = [], $statics = null)
    {
        return new TestOptions([
            'options' => $stored,
            'statics' => $statics === null ? Fixtures::statics() : $statics,
        ]);
    }

    public static function disallowedActions()
    {
        return [
            'unknown'         => ['foo'],
            'traversal'       => ['../admin'],
            'requirements'    => ['requirements'],
            'statics_changed' => ['statics_changed'],
            'case mismatch'   => ['Query'],
            'version in action' => ['v2/audio'],
        ];
    }

    /**
     * @dataProvider disallowedActions
     */
    #[DataProvider('disallowedActions')]
    public function testDisallowedActionIsRejectedWithoutARequest($action)
    {
        $options = $this->makeOptions();

        $this->assertFalse($options->apiProxy($action, ['reference' => 'Rom 1']));
        $this->assertSame([], $options->api_requests);
    }

    public function testEmptyActionDefaultsToQuery()
    {
        $options = $this->makeOptions();

        $result = $options->apiProxy(null, ['reference' => 'Rom 1'], null, 'GET');

        $this->assertSame(200, $result['status']);
        $this->assertFalse($result['raw']);
        $this->assertCount(1, $options->api_requests);

        list($url, $data, $method) = $options->api_requests[0];
        $this->assertSame('https://api.biblesupersearch.com/api', $url);
        $this->assertSame('Rom 1', $data['reference']);
        $this->assertSame('example.com', $data['domain']);
        $this->assertSame('GET', $method);
    }

    public static function apiUrls()
    {
        return [
            'un-versioned action' => ['books', null, 'https://bible.example.org/api/books'],
            'versioned action'    => ['audio_check', 'v2', 'https://bible.example.org/api/v2/audio_check'],
            'versioned query'     => ['query', 'v3', 'https://bible.example.org/api/v3'],
            'empty version'       => ['strongs', '', 'https://bible.example.org/api/strongs'],
        ];
    }

    /**
     * @dataProvider apiUrls
     */
    #[DataProvider('apiUrls')]
    public function testActionAndVersionAreAppendedToTheConfiguredApiUrl($action, $version, $expected)
    {
        $options = $this->makeOptions(['apiUrl' => 'https://bible.example.org']);

        $options->apiProxy($action, [], $version);

        $this->assertSame($expected, $options->api_requests[0][0]);
    }

    public static function invalidVersions()
    {
        return [
            'no v'        => ['2'],
            'upper case'  => ['V2'],
            'not integer' => ['v2.1'],
            'traversal'   => ['v2/../admin'],
            'word'        => ['latest'],
        ];
    }

    /**
     * @dataProvider invalidVersions
     */
    #[DataProvider('invalidVersions')]
    public function testInvalidVersionIsRejectedWithoutARequest($version)
    {
        $options = $this->makeOptions();

        $this->assertFalse($options->apiProxy('books', [], $version));
        $this->assertSame([], $options->api_requests);
    }

    public function testClientCannotOverrideTheDomain()
    {
        $options = $this->makeOptions();

        $options->apiProxy('books', ['domain' => 'evil.example']);

        $this->assertSame('example.com', $options->api_requests[0][1]['domain']);
    }

    public function testJsonResponseIsDecodedAndStatusPassedThrough()
    {
        $options = $this->makeOptions();
        $body = ['results' => [], 'errors' => ['Your search for \'zzz\' produced no results.'], 'error_level' => 4];
        $options->api_response = ['status' => 404, 'body' => json_encode($body)];

        $result = $options->apiProxy('query', ['search' => 'zzz']);

        $this->assertSame(404, $result['status']);
        $this->assertSame($body, $result['body']);
    }

    public function testStaticsComeFromGetStaticsWithoutARequest()
    {
        $options = $this->makeOptions();

        $result = $options->apiProxy('statics', ['language' => 'en']);

        $this->assertSame(200, $result['status']);
        $this->assertSame(Fixtures::statics(), $result['body']['results']);
        $this->assertSame([], $result['body']['errors']);
        $this->assertSame(0, $result['body']['error_level']);
        $this->assertSame([], $options->api_requests);
    }

    public function testDefaultVersionStaticsComeFromGetStatics()
    {
        $options = $this->makeOptions();

        $result = $options->apiProxy('statics', [], 'v2');

        $this->assertSame(Fixtures::statics(), $result['body']['results']);
        $this->assertSame([], $options->api_requests);
    }

    public function testOtherVersionStaticsAreRelayed()
    {
        $options = $this->makeOptions();

        $options->apiProxy('statics', ['language' => 'en'], 'v3');

        $this->assertSame('https://api.biblesupersearch.com/api/v3/statics', $options->api_requests[0][0]);
    }

    public function testStaticsFailureIsABadGateway()
    {
        $options = $this->makeOptions([], false);

        $result = $options->apiProxy('statics', []);

        $this->assertSame(502, $result['status']);
        $this->assertNotEmpty($result['body']['errors']);
    }

    public function testUnreachableApiIsABadGateway()
    {
        $options = $this->makeOptions();
        $options->api_response = ['body' => false, 'status' => 0];

        $result = $options->apiProxy('strongs', ['strongs' => 'G25']);

        $this->assertSame(502, $result['status']);
        $this->assertFalse($result['raw']);
        $this->assertArrayHasKey('error_level', $result['body']);
    }

    public function testRawActionPassesBodyAndOnlyAllowedHeaders()
    {
        $options = $this->makeOptions();
        $options->api_response = [
            'body'    => "PK\x03\x04binary",
            'headers' => [
                'content-type'        => 'application/zip',
                'content-disposition' => 'attachment; filename="kjv.zip"',
                'set-cookie'          => 'session=abc',
                'server'              => 'nginx',
            ],
        ];

        $result = $options->apiProxy('download', ['format' => 'json', 'bible' => '["kjv"]'], null, 'GET');

        $this->assertTrue($result['raw']);
        $this->assertSame(200, $result['status']);
        $this->assertSame("PK\x03\x04binary", $result['body']);
        $this->assertSame([
            'content-type'        => 'application/zip',
            'content-disposition' => 'attachment; filename="kjv.zip"',
        ], $result['headers']);
        $this->assertSame('https://api.biblesupersearch.com/api/download', $options->api_requests[0][0]);
        $this->assertSame('example.com', $options->api_requests[0][1]['domain']);
    }

    public function testUnreachableApiOnRawActionIsABadGateway()
    {
        $options = $this->makeOptions();
        $options->api_response = ['body' => false, 'status' => 0];

        $result = $options->apiProxy('audio', [], 'v2');

        $this->assertSame(502, $result['status']);
        $this->assertFalse($result['raw']);
    }

    public function testQuietApiActionHelperEmitsNoOutput()
    {
        $options = $this->makeOptions(['debug' => true]);
        $options->api_response = ['body' => 'not json'];

        // The suite fails on stray output, so any echo here fails the test
        $result = $options->callApiActionHelper('query', 'https://api.biblesupersearch.com', [], null, true);

        $this->assertNull($result);
    }
}
