<?php

namespace BibleSuperSearch\Common\Tests\Support;

use BibleSuperSearch\Common\OptionsAbstract;

/**
 * In-memory concrete implementation of OptionsAbstract for testing.
 *
 * Nothing here touches a database, the filesystem or the network: options and
 * the statics cache live in plain arrays, and getStatics() is overridden to
 * return an injected fixture so tests never issue an API request.
 */
class TestOptions extends OptionsAbstract
{
    /** @var array|null Raw stored options, as fetchOptions() would return them */
    protected $stored_options = null;

    /** @var array|null Stored statics cache */
    protected $stored_statics = null;

    /** @var array|null Statics returned by getStatics() */
    protected $statics_fixture = null;

    /** @var array Landing pages returned by fetchLandingPageOptions() */
    protected $landing_pages = [];

    /** @var string[] Messages passed to fatalError() */
    public $fatal_errors = [];

    /**
     * @param array $config keys: options, statics, landing_pages
     */
    public function __construct(array $config = [])
    {
        if (array_key_exists('options', $config)) {
            $this->stored_options = $config['options'];
        }

        if (array_key_exists('statics', $config)) {
            $this->statics_fixture = $config['statics'];
        }

        if (array_key_exists('landing_pages', $config)) {
            $this->landing_pages = $config['landing_pages'];
        }

        parent::__construct();
    }

    protected function fetchOptions()
    {
        return $this->stored_options;
    }

    protected function storeOptions($options)
    {
        $this->stored_options = $options;
    }

    protected function fetchStaticsCache()
    {
        return $this->stored_statics;
    }

    protected function storeStaticsCache($statics)
    {
        $this->stored_statics = $statics;
    }

    protected function fetchLandingPageOptions()
    {
        return $this->landing_pages;
    }

    /**
     * Record instead of die(), so a test can assert on the failure.
     */
    protected function fatalError($msg)
    {
        $this->fatal_errors[] = $msg;
    }

    /**
     * Overridden to keep the suite offline - the real implementation calls the
     * Bible SuperSearch API.
     */
    public function getStatics($force = FALSE)
    {
        return $this->statics_fixture === null ? false : $this->statics_fixture;
    }

    /** @var array[] Requests passed to _apiRequest(): each [url, data, method, ignore_errors] */
    public $api_requests = [];

    /** @var array Response returned by _apiRequest() (merged over a 200 JSON default) */
    public $api_response = [];

    /**
     * Overridden to keep the suite offline - records the request and returns
     * the canned $api_response.
     */
    protected function _apiRequest($url, $data, $method = 'POST', $ignore_errors = FALSE)
    {
        $this->api_requests[] = [$url, $data, $method, $ignore_errors];

        $this->last_api_response = array_replace([
            'body'              => json_encode(['results' => [], 'errors' => [], 'error_level' => 0]),
            'status'            => 200,
            'headers'           => [],
            'method_used'       => 'Test',
            'methods_attempted' => ['Test'],
            'curl_info'         => null,
        ], $this->api_response);

        return $this->last_api_response;
    }

    /**
     * Overridden: the real implementation calls WordPress' site_url()
     */
    protected function _apiDomain()
    {
        return 'example.com';
    }

    /* ---- test-only accessors for protected members ---- */

    public function callApiActionHelper($action, $api_url, $data, $version = null, $quiet = false, $method = 'POST')
    {
        return $this->_apiActionHelper($action, $api_url, $data, $version, $quiet, $method);
    }

    public function getStoredOptions()
    {
        return $this->stored_options;
    }

    public function callReformatItemsList($items, $passthru = [])
    {
        return $this->reformatItemsList($items, $passthru);
    }

    public function callGetBiblesForDisplay()
    {
        return $this->getBiblesForDisplay();
    }

    public function callProcessInterfaceName($name)
    {
        return $this->_processInterfaceName($name);
    }

    public function getOptionsList()
    {
        return $this->options_list;
    }

    public function getTabs()
    {
        return $this->tabs;
    }
}
