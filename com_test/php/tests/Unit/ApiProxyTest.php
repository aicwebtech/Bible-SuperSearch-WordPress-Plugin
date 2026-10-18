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

    public function testRawActionIsReturnedForStreamingWithoutARequest()
    {
        $options = $this->makeOptions();

        $result = $options->apiProxy('download', ['format' => 'json', 'bible' => '["kjv"]', 'domain' => 'evil.example'], null, 'GET');

        $this->assertTrue($result['raw']);
        $this->assertSame(200, $result['status']);
        $this->assertSame('https://api.biblesupersearch.com/api/download', $result['url']);
        $this->assertSame('json', $result['data']['format']);
        $this->assertSame('example.com', $result['data']['domain']);
        $this->assertSame('GET', $result['method']);
        $this->assertSame([], $options->api_requests);
    }

    public function testVersionedRawActionUrl()
    {
        $options = $this->makeOptions();

        $result = $options->apiProxy('audio', ['bible' => 'kjv'], 'v2', 'GET');

        $this->assertTrue($result['raw']);
        $this->assertSame('https://api.biblesupersearch.com/api/v2/audio', $result['url']);
    }

    public function testQuietApiActionHelperEmitsNoOutput()
    {
        $options = $this->makeOptions(['debug' => true]);
        $options->api_response = ['body' => 'not json'];

        // The suite fails on stray output, so any echo here fails the test
        $result = $options->callApiActionHelper('query', 'https://api.biblesupersearch.com', [], null, true);

        $this->assertNull($result);
    }

    public function testNonJsonResultIsFalseAndErrorBodiesAreNotRequested()
    {
        $options = $this->makeOptions();
        $options->api_response = ['body' => '<html>500 Internal Server Error</html>', 'status' => 500];

        // Not quiet: reports the invalid result
        ob_start();
        $result = $options->callApiActionHelper('statics', 'https://api.biblesupersearch.com', []);
        ob_end_clean();

        $this->assertFalse($result);
        $this->assertFalse($options->api_requests[0][3]);
    }

    public function testProxyRequestsAskForErrorBodies()
    {
        $options = $this->makeOptions();
        $options->api_response = ['status' => 404, 'body' => json_encode(['results' => [], 'errors' => ['No results'], 'error_level' => 4])];

        $result = $options->apiProxy('query', ['search' => 'zzz']);

        $this->assertTrue($options->api_requests[0][3]);
        $this->assertSame(['No results'], $result['body']['errors']);
    }

    public static function origins()
    {
        $site = ['https://example.com/', 'https://example.com/wp'];

        return [
            'same-origin fetch'          => ['same-origin', null, $site, true],
            'same-origin POST'           => ['same-origin', 'https://example.com', $site, true],
            'opened directly'            => ['none', null, $site, true],
            'header case'                => ['Same-Origin', null, $site, true],
            'cross-site'                 => ['cross-site', 'https://evil.example', $site, false],
            'same-site subdomain'        => ['same-site', 'https://sub.example.com', $site, false],
            'Sec-Fetch-Site wins'        => ['cross-site', 'https://example.com', $site, false],
            'no headers'                 => [null, null, $site, true],
            'matching Origin'            => [null, 'https://example.com', $site, true],
            'Origin host case'           => [null, 'https://EXAMPLE.com', $site, true],
            'Origin explicit port'       => [null, 'https://example.com:443', $site, true],
            'Origin other port'          => [null, 'https://example.com:8443', $site, false],
            'Origin other scheme'        => [null, 'http://example.com', $site, false],
            'Origin other host'          => [null, 'https://evil.example', $site, false],
            'Origin null'                => [null, 'null', $site, false],
            'Origin matches site_url'    => [null, 'https://wp.example.com', ['https://example.com', 'https://wp.example.com/'], true],
        ];
    }

    /**
     * @dataProvider origins
     */
    #[DataProvider('origins')]
    public function testOriginCheck($sec_fetch_site, $origin, $allowed, $expected)
    {
        $this->assertSame($expected, TestOptions::apiProxyOriginAllowed($sec_fetch_site, $origin, $allowed));
    }
}
