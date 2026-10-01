<?php

namespace BibleSuperSearch\Common;

abstract class OptionsAbstract 
{
    
    protected static $instance = null;

    protected $option_index = 'biblesupersearch_options';

    const DEFAULT_ITEM_TEXT = 'Default for Selected Skin';

    /** Todo = this needs to come from the options list ONLY */
    protected $default_options = [
        'defaultBible'                              => 'kjv',
        'defaultBibles'                             => ['kjv'],
        'apiUrl'                                    => 'https://api.biblesupersearch.com',
        'useJSONP'                                  => FALSE,
        'defaultLanguage'                           => 'en',
        'enabledBibles'                             => [],
        'enableAllBibles'                           => TRUE,
        'textDisplayDefault'                        => 'passage',
        'interface'                                 => 'Classic',  // 'Expanding'
        'toggleAdvanced'                            => TRUE,
        'formatButtonsToggle'                       => FALSE,
        'includeTestament'                          => false,
        'defaultDestinationPage'                    => 0,
        "extraButtonsSeparate"                      => 'default',
        'pager'                                     => 'default',
        'pageScroll'                                => 'instant',
        'pageScrollTopPadding'                      => 0,
        'formatButtons'                             => 'default',
        'navigationButtons'                         => 'default',
        'bibleGrouping'                             => 'language',
        'bibleSorting'                              => 'language_english|name',
        'bibleDefaultLanguageTop'                   => true,
        'bibleChangeUpdateNavigation'               => false,
        'language'                                  => 'global_default',
        'languageList'                              => [],
        'enableAllLanguages'                        => true,
        'landingReference'                          => '',
        'debug'                                     => false,
        'parallelBibleLimitByWidth'                 => [],
        'parallelBibleCleanUpForce'                 => false,
        'parallelBibleStartSuperceedsDefaultBibles' => false,
        'landingReferenceDefault'                   => false,
    ];  

    protected $options = [];
    protected $options_list = [];
    protected $selector_options = null;
    protected $statics = null;

    protected $tabs = [
        'general'  => [
            'name'              => 'General',
            'fully_dynamic'     => true, 
            'backend_dynamic'   => true,
        ],        
        'features' => [
            'name'              => 'Features',
            'fully_dynamic'     => true,
            'backend_dynamic'   => true,
        ],         
        'bible'  => [
            'name'              => 'Bibles',
            'fully_dynamic'     => true, 
            'backend_dynamic'   => true,
        ],        
        'language'  => [
            'name'              => 'Languages',
            'fully_dynamic'     => true, 
            'backend_dynamic'   => true,
        ],        
        // :todo
        // 'style' => [
        //     'name'          => 'Appearance',
        // ],
        'advanced' => [
            'name'              => 'Advanced',
            'fully_dynamic'     => true,
            'backend_dynamic'   => true,
        ],
    ];

    public $refresh_statics = false;
    
    public function __construct() 
    {
        $this->initSelectorOptions();
        $this->initOptions();
    }

    public static function getInstance() 
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function getCurrentInstance() 
    {
        return static::$instance;
    }

    protected function initOptions()
    {
        $options = $this->loadOptions();
        $defaults = [];

        foreach($options as $tab => &$tab_options) {
            $this->tabs[$tab]['id'] = $tab;
            $this->tabs[$tab]['type'] = 'config';
            $this->tabs[$tab]['options'] = []; // Add options array to each tab's settings

            foreach($tab_options as $optidx => &$settings) {
                $opt = $optidx;
                
                // Todo - flesh out option cloning
                // Allow cloning of options from the SAME tab?? ...
                // Currently, the cloned option MUST have the same index as it's parent option
                // Which means it can only appear once on a given tab
                // Use case: IDK ... 
                if(isset($settings['clone']) && $settings['clone']) {
                    $cc = explode('.', $settings['clone']);
                    unset($settings['clone']);
                    //$opt = $cc[1] ?? $opt;
                    
                    $settings = array_replace($settings, $options[ $cc[0] ][$opt]);
                }
                
                $settings['field'] = $opt;

                if(!in_array($opt, $this->tabs[$tab]['options'])) {
                    $this->tabs[$tab]['options'][] = $opt;
                }

                if(isset($settings['items'])) {
                    $items = $settings['items'];

                    if(is_string($items)) {
                        if(isset($this->selector_options[$items])) {
                            $items = $this->selector_options[$items];
                        } else if(is_callable([$this, $items])) {
                            $items = call_user_func([$this, $items]);
                        } else {
                            // String index, do nothing
                            // $items = [];
                        }
                    } else if(!is_array($items)) {
                        $items = [];
                    }

                    $settings['items'] = $this->reformatItemsList($items);
                }

                if(array_key_exists('default', $settings)) {
                    $defaults[$opt] = $settings['default'];
                }
            }

            unset($settings);
        }
        unset($tab_options);

        $this->default_options = array_replace($this->default_options, $defaults);
        $this->options_list = $options;
    }

    protected function initSelectorOptions()
    {
        if(isset($this->selector_options)) {
            return;
        }

        $this->selector_options = require(dirname(__FILE__) . '/../includes/selector_options_list.php');
        $this->selector_options['landing_pages'] = $this->fetchLandingPageOptions();
    }
    
    public function setOptions($options) 
    {
        $this->refresh_statics = false;
        $options = $this->validateOptions($options);
        $this->storeOptions($options);
    }

    public function setDefaultOptions() 
    {
        $this->setOptions($this->default_options);
    }

    /**
     * Fetches raw options (from database or other storage)
     */
    protected abstract function fetchOptions();

    /**
     * Saves options (to database or other storage)
     */
    protected abstract function storeOptions($options);

    /**
     * Terminates the request with a fatal error message.
     * Override to use the platform's native error handler.
     */
    protected function fatalError($msg)
    {
        die($msg);
    }

    protected function reformatItemsList($items, $passthru = [])
    {
        if(!is_string($items) && $items !== [] && array_keys($items) !== range(0, count($items) - 1)) {
            $items_new = [];

            foreach($items as $value => $item) {
                $label = is_array($item) ? $item['name'] : $item;
                
                $item_new = [
                    'value' => $value,
                    'label' => $label,
                ];

                if(is_array($item) && !empty($passthru)) {
                    foreach($passthru as $k) {
                        $item_new[$k] = $item[$k] ?? null;
                    }
                }

                $items_new[] = $item_new;
            }
        } else {
            $items_new = $items;
        }

        return $items_new;
    }

    protected function getBiblesForDisplay()
    {
        $preformatted = $this->reformatItemsList($this->getBibles([], 'language_english', 'none'), ['lang_short', 'lang', 'shortname'] );

        $bibles = [];

        foreach($preformatted as $key => $bible) {
            if(!isset($bibles[$bible['lang_short']])) {                
                $bibles[$bible['lang_short']] = [
                    'type' => 'subheader',
                    'group' => $bible['lang_short'],
                    'label' => $bible['lang'],
                    'itemProps' => [
                        'disabled' => true, 
                        'role' => 'header',
                        // 'type' => 'subheader',
                    ],
                    'header' => 'hh ' . $bible['lang'],
                ];
            }

            $bible['group'] = $bible['lang_short'];

            $bible['itemProps'] = [];

            $bible['itemProps']['subtitle'] 
                = $bible['shortname'] == $bible['label'] || mb_strlen($bible['label']) < 45 ? null : $bible['shortname'];
            
            $bibles[] = $bible;
        }

        return array_values($bibles);
    }

    // Override to add/change/remove options
    protected function loadOptions()
    {
        return require(dirname(__FILE__) . '/../includes/options_list.php');
    }

    public function renderOptions($tab, $section = null, $option_list = [])
    {
        if(!isset($this->tabs[$tab])) {
            return false;
        }

        if(!$option_list) {
            $option_list = $this->tabs[$tab]['options'];
        }

        $list = is_array($this->options_list[$tab]) ? $this->options_list[$tab] : [];

        foreach($option_list as $field) {
            if(!isset($list[$field])) {
                continue;
            }

            // todo - get class info, have class render field
            // $class = $this->makeOptionClass($list[$field]);
        }
    }

    public function getSelectorOptions($selector) 
    {
        if(array_key_exists($selector, $this->selector_options)) {
            return $this->selector_options[$selector];
        }

        return FALSE;
    }

    public function getOptions($dont_set_default = FALSE) 
    {
        $options = $this->fetchOptions();

        if(!is_array($options)) {
            if(!$dont_set_default) {
                $this->setDefaultOptions();
            }
            
            return $this->default_options;
        }

        foreach($this->default_options as $key => $val) {
            if(!array_key_exists($key, $options) || empty($options[$key]) && $options[$key] !== FALSE) {
                $options[$key] = $val;
            }
        }

        if(is_string($options['defaultBible'])) {
            $options['defaultBible'] = explode(',', $options['defaultBible']);
        } elseif(is_array($options['defaultBible'])) {
            $options['defaultBible'] = array_filter($options['defaultBible']);
            $options['defaultBible'] = array_values($options['defaultBible']);
        } else {
            $options['defaultBible'] = [];
        }

        // Ensure Bibles selected as default or langauge default are enabled
        if(!$options['enableAllBibles']) {
            $options['enabledBibles'] = array_merge($options['enabledBibles'], $options['defaultBible']);

            if($options['enableDefaultBiblesByLang'] && is_array($options['defaultBiblesByLanguage'])) {
                $bbl = call_user_func_array('array_merge', array_values($options['defaultBiblesByLanguage']));
                $options['enabledBibles'] = array_merge($options['enabledBibles'], $bbl);
                $options['enabledBibles'] = array_unique($options['enabledBibles']);
                $options['enabledBibles'] = array_values($options['enabledBibles']);
            }
        }

        if(!$options['enableDefaultBiblesByLang']) {
            $options['defaultBiblesByLanguage'] = [];
        }

        return $options;
    }

    public function getDefaultOptions() 
    {
        return $this->default_options;
    }

    public function getOptionsFiltered() 
    {
        $options = $this->getOptions();

        if($options['enableAllBibles']) {
            $options['enabledBibles'] = [];
        }        

        if($options['enableAllLanguages']) {
            $options['languageList'] = [];
        }
    }

    public function validateOptions( $incoming ) 
    {
        $current = $input = $this->getOptions(TRUE);

        if(isset($incoming['_tab'])) {
            $tab = $incoming['_tab'];
            unset($incoming['_tab']);
        } else {
            $tab  = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'general';
        }
        
        $tabs = $tab == 'all' ? array_keys($this->tabs) : [$tab];

        foreach($tabs as $tab) {
            $tab_item = $this->tabs[ $tab ];
            $list = $this->options_list[$tab];

            foreach($tab_item['options'] as $field) {
                if(!isset($list[$field])) {
                    continue;
                }

                switch($list[$field]['type']) {
                    case 'checkbox':
                        $input[$field] = (array_key_exists($field, $incoming) && !empty($incoming[$field])) ? true : false;
                        break;
                    case 'text':
                    case 'textarea':
                    case 'hidden':
                    case 'select':
                        if(array_key_exists($field, $incoming)) {
                            $input[$field] = $incoming[$field];
                        }

                        break;

                    case 'integer':
                    case 'int':
                        if(array_key_exists($field, $incoming)) {
                            $input[$field] = (int)$incoming[$field];
                        }

                        break;

                    case 'json':
                        // Note: fields absent from $incoming keep their current value ($input seeds from getOptions)
                        if(array_key_exists($field, $incoming)) {
                            if(is_string($incoming[$field])) {
                                $decoded = json_decode($incoming[$field], true);
                                $input[$field] = is_array($decoded) ? $decoded : [];
                            } elseif(is_array($incoming[$field])) {
                                $input[$field] = $incoming[$field];
                            } else {
                                $input[$field] = [];
                            }
                        }

                        break;
                }
            }
        }

        // Cherry-pick default values 
        foreach($this->default_options as $item => $value) {
            if(!array_key_exists($item, $input)) {
                $input[$item] = $value;
            }
        }

        // Special cases
        if($input['enableAllBibles']) {
            $input['enabledBibles'] = [];
        }            

        if($input['enableAllLanguages']) {
            $input['languageList'] = [];
        } else {
            // Make sure default language is in list of selected languages
            if(!in_array($input['language'], $input['languageList'])) {
                $input['languageList'][] = $input['language'];
            }
        }

        if(!empty($input['landingReference'])) {
            $input['landingReference'] = trim($input['landingReference']);
            $input['landingReference'] = preg_replace('/[`\'"\\~!@#$%\^&*{}_[\]()=]/', ' ', $input['landingReference']);
            $input['landingReference'] = preg_replace('/\s+/', ' ', $input['landingReference']);
        }
        
        if(empty($input['apiUrl'])) {
            $input['apiUrl'] = $this->default_options['apiUrl'];
        }

        if($input['apiUrl'] != $current['apiUrl']) {
            $this->refresh_statics = true;
        }

        $this->_setStaticsReset(); // Always Force Reload statics when options saved

        return $input;
    }

    public function getLandingPageOptions($exclude_zero = false)
    {
        $options = $this->selector_options['landing_pages'] ?? [];

        if(!$exclude_zero) {
            array_unshift($options, [
                'value' => '0',
                'label' => 'None'
            ]);
        }

        return $options;
    }

    public function hasLandingPageOptions()
    {
        $options = $this->selector_options['landing_pages'] ?? [];

        return is_array($options) && !empty($options);
    }

    /** 
     * Override to pull landing page options (from db or other storage)
     * @return array
     */ 
    protected function fetchLandingPageOptions()
    {
        return [];
    }

    public function getLandingPage() 
    {
        $options = $this->getOptions();

        if(!$options['defaultDestinationPage']) {
            return FALSE;
        }

        return $this->getLandingPageById($options['defaultDestinationPage']);
    }
    
    public function getLandingPageById($id) 
    {
        $options = $this->selector_options['landing_pages'] ?? [];
        
        $lp = array_filter($options, function($o) use ($id) {
            return $o['value'] == $id;
        });

        return !empty($lp) ? array_values($lp)[0] : FALSE;
    }

    public function getBible($module) 
    {
        $statics = $this->getStatics();

        if(
            is_array($statics) && 
            array_key_exists('bibles', $statics) && 
            is_array($statics['bibles']) && 
            array_key_exists($module, $statics['bibles']) && 
            is_array($statics['bibles'][$module])
        ) {
            return $statics['bibles'][$module];
        }

        return false;
    }

    public function getBibles($statics = NULL, $sorting = NULL, $grouping = NULL) 
    {
        $options = $this->getOptions();
        $statics = $statics ? $statics : $this->getStatics();

        if(!$statics || !is_array($statics) || empty($statics['bibles']) || !is_array($statics['bibles'])) {
            return [];
        }
        
        // $sorting = 'year|name'; // Todo - actually apply sort options here
        
        if(!$sorting) {
            $sorting  = array_key_exists('bibleSorting', $options)  ? $options['bibleSorting'] : 'rank';
        }
        
        if(!$grouping) {
            $grouping = array_key_exists('bibleGrouping', $options) ? $options['bibleGrouping'] : NULL;
        }

        switch ($grouping) {
            case 'language':
                $groupOrder = 'lang_native';
                break;            
            case 'language_english':
                $groupOrder = 'lang';
                break;            
            case 'language_and_english':
                $groupOrder = 'lang_native';
                break;
            case 'none':
            default:
                $groupOrder = NULL;
        }

        if($groupOrder) {
            $sorting = $groupOrder . '|' . $sorting;
        }

        $sorting  = explode('|', $sorting);
        $sortable = [];

        foreach($sorting as $k => $s) {
            $sortable[] = [];
            $sortable[] = SORT_REGULAR; // Todo, DESC, ect
        }

        foreach($statics['bibles'] as $module => &$bible) {
            switch($grouping) {
                case 'language': // Language: Endonym
                    $bible['group_value'] = $bible['lang_short'];
                    $n = $bible['lang_native'] ?: $bible['lang']; // Fall back to English name if needed
                    $bible['group_name'] = $n . ' - (' . strtoupper($bible['lang_short']) . ')';
                    break;                
                case 'language_and_english': // Language: Both Endonym and English name
                    $bible['group_value'] = $bible['lang_short'];
                    // If no Endonym, only display English name once
                    $n = ($bible['lang_native'] && $bible['lang_native'] != $bible['lang']) ? $bible['lang_native'] . ' / ' . $bible['lang'] : $bible['lang'];
                    $bible['group_name'] = $n . ' - (' . strtoupper($bible['lang_short']) . ')';
                    break;
                case 'language_english': // Language: English name
                    $bible['group_value'] = $bible['lang_short'];
                    $bible['group_name'] = $bible['lang'] . ' - (' . strtoupper($bible['lang_short']) . ')';
                    break;
                default:
                    $bible['group_value'] = NULL;
                    $bible['group_name']  = NULL;
                    $bible['display'] = $bible['name'] . ' (' . $bible['lang'] . ')';
            }

            foreach($sorting as $k => $s) {
                switch($s) {
                    case 'language_english':
                        $s = 'lang';
                        break;                        
                    case 'language':
                        $s = 'lang_native';
                        break;
                }

                $sortable[$k * 2][$module] = $bible[$s];
            }

            $bible['display_short'] = $bible['name'];
        }
        
        $sortable[] = &$statics['bibles']; // Assign by reference needed
        call_user_func_array('array_multisort', $sortable);
        return $statics['bibles'];
    }

    public function getEnabledBibles($statics = [], $sorting = NULL, $grouping = NULL) 
    {
        $options = $this->getOptions();
        $bibles  = $this->getBibles($statics, $sorting, $grouping);

        if($options['enableAllBibles'] || !is_array($options['enabledBibles']) || empty($options['enabledBibles']) ) {
            return $bibles;
        }

        $enabled = [];

        foreach($bibles as $module => $bible) {
            if(in_array($module, $options['enabledBibles'])) {
                $enabled[$module] = $bible;
            }
        }

        return $enabled;
    }

    // TODO - make generic 
    // This code is WP specific
    protected function _setStaticsReset() 
    {
        // $statics               = get_option('biblesupersearch_statics');
        // $last_update_timestamp = (is_array($statics) && array_key_exists('timestamp', $statics)) ? $statics['timestamp'] : 0;

        // if($last_update_timestamp) {
        //     $statics['timestamp'] = 0;
        //     update_option('biblesupersearch_statics', $statics);
        // }
    }

    protected $statics_loading = FALSE;

    public function getUrl() 
    {
        $options    = $this->getOptions();
        $url        = $options['apiUrl'] ?: $this->default_options['apiUrl'];
        return $url;
    }

    public function apiVersion() 
    {
        $statics = $this->getStatics();
        return (is_array($statics) && array_key_exists('version', $statics)) ?  $statics['version'] : '0.0.0';
    }
    
    /**
     * Fetches statics cache (from db or other storage)
     * @return array|false - the cached statics data, including timestamp, or false if not available
     */
    abstract protected function fetchStaticsCache();
    
    /**
     * Saves statics cache (to db or other storage)
     * @param array $statics - the statics data to cache, including timestamp
     */
    abstract protected function storeStaticsCache($statics);

    public function getStatics($force = FALSE) 
    {
        if($this->statics_loading == TRUE) {
            return FALSE;
        }

        $options    = $this->getOptions();
        $url        = $options['apiUrl'] ?: $this->default_options['apiUrl'];
        $allow_url_fopen       = intval(ini_get('allow_url_fopen'));
        $cached_statics        = $this->statics ?? $this->fetchStaticsCache();
        $last_update_timestamp = (is_array($cached_statics) && array_key_exists('timestamp', $cached_statics)) ? $cached_statics['timestamp'] : 0;

        if(empty($cached_statics['bibles']) || empty($cached_statics['version'])) {
            $force = TRUE; // Force statics load if last load failed
        }

        // Do we really need to pull statics fresh once an hour??
        if($last_update_timestamp > time() - 3600 && !$force) {
            // Check to see if statics have changed.
            $result = $this->_apiActionHelper('statics_changed', $url, []);

            // If statics have NOT changed (or no results), send cached.
            if($result === NULL || $result && $result['results']['updated'] <= $last_update_timestamp) {
                return $cached_statics;
            }
        }

        $this->statics_loading = TRUE;
        $data       = ['language'  => 'en'];
        
        $result     = $this->_apiActionHelper('statics', $url, $data);
        $this->statics_loading = FALSE;
        
        if ($result === FALSE) { 
            if($last_update_timestamp && !$force) {
                return $cached_statics;
            }
            elseif(!function_exists('curl_init') && $allow_url_fopen == 0) {
                $this->fatalError( 'Error: please have your web host turn on php.ini config allow_url_fopen OR install cURL to continue' );
            }
            else {
                if($options['apiUrl'] != $this->default_options['apiUrl']) {
                    $msg = 'Error: unable to load data from a Bible SuperSearch API server at ' . $options['apiUrl'];
                    $msg .= '<br />Reverting back to default of ' . $this->default_options['apiUrl'];
                    
                    $result = $this->_apiActionHelper('statics', $this->default_options['apiUrl'], $data);

                    if($result === FALSE) {
                        $msg .= '<br />Cannot connect to default API url, either';
                    }

                    $options['apiUrl'] = $this->default_options['apiUrl'];
                    $this->storeOptions($options);
                    echo($msg);
                }
                else {
                    // Unable to connect to default API! Not dying out here so user can turn on debug
                }
            }

            return false;
        }

        $result['results']['timestamp'] = time();
        $this->storeStaticsCache($result['results']);
        $this->statics_loading = FALSE;
        $this->statics = $result['results'];
        return $result['results'];
    }

    public function apiRequirementsCheck() 
    {
        $api_url = $this->default_options['apiUrl'];

        if(empty($api_url)) {
            return [false, []];
        }

        $result = $this->_apiActionHelper('requirements', $api_url, []);

        if($result === FALSE) {
            return [false, []];
        }

        $req = $result['results'];

        $php_success = (version_compare(phpversion(), $req['php_version'], '>=') == -1);
        $installed_php_parts = explode('.', PHP_VERSION);
        $installed_php = $installed_php_parts[0] . '.' . $installed_php_parts[1] . '.' . (int)$installed_php_parts[2];

        $checklist = [];

        $checklist[] = ['type' => 'item', 'label' => 'PHP Version >= ' . $req['php_version'] . ' (Current = ' . $installed_php . ')', 'success' => $php_success];

        $req_extensions = $req['php_extensions_required'];
        $rec_extensions = $req['php_extensions_recommended'];

        // :Todo - handle differences between WP and other CMS 
        // API requires MySQL 
        // WP uses MySQL so we know it will be present
        $req_extensions[] = 'PDO_MYSQL';

        sort($req_extensions);
        sort($rec_extensions);

        foreach($req_extensions as $ext) {
            $checklist[] = ['type' => 'item', 'label' => 'PHP Extension: ' . $ext, 'success' => extension_loaded($ext)];
        }

        foreach($rec_extensions as $ext) {
            $checklist[] = ['type' => 'item', 'label' => 'PHP Extension: ' . $ext . ' (recommended)', 'success' => extension_loaded($ext) ?: NULL];
        }

        $success = true;

        foreach($checklist as $row) {
            if($row['type'] == 'item' && $row['success'] === false) {
                $success = false;
                break;
            }
        }

        return [$success, $checklist];
    }

    protected function _apiActionHelper($action, $api_url, $data) 
    {
        $url_action = ($action == 'query') ? '/api' : '/api/' . $action;
        $url = $api_url . $url_action;

        $result = FALSE;
        $allow_url_fopen = (int) ini_get('allow_url_fopen');
        $err = error_reporting();
        error_reporting(E_ERROR | E_PARSE);
        $bss_options = $this->getOptions();
        
        $data['domain'] = static::parseDomain(site_url());
        $method_used = 'None';
        $methods_attempted = [];

        // Attempt 1: Via file_get_contents
        if($allow_url_fopen == 1) {        
            $options = [
                'http' => [        // Use key 'http' even if you send the request to https://
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                ]
            ];
 
            $context = stream_context_create($options);
            $result  = file_get_contents($url, FALSE, $context);
            $methods_attempted[] = $method_used = 'URL FOpen';
        }
        
        // Attempt 2: Fall back to cURL
        if($result === FALSE && function_exists('curl_init')) {        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $result = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            curl_close($ch);
            $methods_attempted[] = $method_used = 'cURL';
        }

        error_reporting($err);

        $result_decoded = ($result === false) ? false : json_decode($result, true);
        
        $eol = '<br />';

        $debug = $bss_options['debug'] || $result === false && $api_url == $this->default_options['apiUrl'];
        $enable_debug = ($debug) ? '' : ' Webmaster: please enable debug mode on Advanced tab for details';

        if($debug) {
            echo 'Bible SuperSearch API Call Log' . $eol;
            echo 'Action: ' . $action . $eol;
            echo 'URL: ' . $url . $eol;
            echo 'Method: ' . $method_used . $eol;
            echo 'Method Attempted: ' . implode(', ', $methods_attempted) . $eol;
            echo 'Data: ' . print_r($data, true) . $eol;
            echo 'Has Results: ' . ($result === false ? 'No' : 'Yes') . $eol;
            echo $eol;

            if(isset($curl_info)) {
                echo 'cURL Info: <pre>';
                print_r($curl_info);
                echo '</pre>' . $eol . $eol;
            }

            echo 'API errors:';

            if(is_array($result_decoded) && is_array($result_decoded['errors']) && !empty($result_decoded['errors'])) {
                echo $eol;

                foreach($result_decoded['errors'] as $e) {
                    echo '&nbsp; &nbsp; * ' . $e . $eol;
                }
            } else {
                echo '(NONE)';
            }    

            echo $eol . $eol;
            echo 'API error level: ';

            if(isset($result_decoded['error_level'])) {
                echo $result_decoded['error_level'];
            } else {
                echo '(NONE)';
            }

            echo $eol . $eol;
        }

        if($result === FALSE && $allow_url_fopen != 1 && !function_exists('curl_init')) {
            echo 'ERROR: Unable to connect to Bible SuperSearch API due to server settings.' . $eol;
            echo 'Please have your system administrator set allow_url_fopen=1 in your php.ini and/or enable cURL.' . $eol;
            echo 'API action: ' . $action . $eol . $eol;
        } else if(empty($result)) {
            echo 'ERROR: Bible SuperSearch API returned empty results.' . $enable_debug . $eol;
            echo 'API action: ' . $action . $eol . $eol;
        } else if(!$this->_validateApiResults($action, $result_decoded, $bss_options['debug'])) {
            echo 'ERROR: Bible SuperSearch API returned invalid results.' . $enable_debug . $eol;
            echo 'API action: ' . $action . $eol . $eol;
        }

        return $result_decoded;
    }

    protected function _validateApiResults($action, $results, $verbose = false) 
    {
        $valid = true;
        $eol = '<br />';
        $every = ['results', 'errors', 'error_level'];

        if(!is_array($results)) {
            return false;
        }

        foreach($every as $k) {
            if(!array_key_exists($k, $results)) {
                if($verbose) {
                    echo 'Results missing array key: ' . $k . $eol;
                }
                
                $valid = false;
            }
        }

        if(!is_array($results['results'])) {
            return false;
        }

        switch($action) {
            case 'statics':
                $reskeys = ['bibles', 'books', 'search_types', 'version', 'shortcuts', 'name', 'environment'];
                break;
            default:
                $reskeys = [];
        }

        foreach($reskeys as $k) {
            if(!array_key_exists($k, $results['results'])) {
                if($verbose) {
                    echo 'Results[results] missing array key: ' . $k . $eol;
                }

                $valid = false;
            }
        }

        return $valid;
    }

    static public function parseDomain($host) 
    {
        if(empty($host)) {
            return null;
        }

        $host = str_replace(['http:','https:'], '', $host);
        $host = trim($host);
        $host = trim($host, '/');
        $pieces = explode('/', $host);
        $domain = $pieces[0];

        if(strpos($domain, 'www.') === 0) {
            $domain = substr($domain, 4);
        }

        $col_pos = strpos($domain, ':');

        if($col_pos !== FALSE) {
            $domain = substr($domain, 0, $col_pos);
        }

        $hash_pos = strpos($domain, '#');

        if($hash_pos !== FALSE) {
            $domain = substr($domain, 0, $hash_pos);
        }

        if($domain == 'localhost') {
            return null;
        }

        return $domain;
    }

    public function getInterfaceByName($name) 
    {
        $interfaces = $this->getInterfaces();
        $proc = $this->_processInterfaceName($name);

        if(array_key_exists($proc, $interfaces)) {
            $interface = $interfaces[$proc];
            $interface['id'] = $proc;
            return $interface;
        }

        foreach($interfaces as $id => $info) {
            $proc2 = $this->_processInterfaceName($info['name']);

            if($info['name'] == $name || $proc == $proc2) {
                $info['id'] = $id;
                return $info;
            }
        }

        return NULL;
    }

    protected function _processInterfaceName($name) 
    {
        $proc = str_replace('-', '', $name);
        // $proc = preg_replace('/\s*/', ' ', $proc);
        $proc = ucwords($proc);
        $proc = str_replace(' ', '', $proc);
        return $proc;
    }

    public function getLanguagesWithGlobalDefault()
    {
        return $this->selector_options['language'];
    }

    public function getLanguages()
    {
        $opts = $this->selector_options['language'];
        unset($opts['global_default']);
        return $opts;
    }

    public function getLanguageNameByCode($code)
    {
        return $this->selector_options['language'][$code] ?? null;
    }

    public function getInterfaces() 
    {
        return $this->selector_options['interfaces'];
    }
}
