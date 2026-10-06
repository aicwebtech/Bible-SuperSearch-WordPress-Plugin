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

    /* ---- test-only accessors for protected members ---- */

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
