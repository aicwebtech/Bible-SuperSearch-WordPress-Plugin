<?php


abstract class BibleSuperSearch_Options_Abstract {
    protected $option_index = 'biblesupersearch_options';

    protected $default_options = array(
        'defaultBible'              => 'kjv',
        'defaultBibles'             => ['kjv'],
        'apiUrl'                    => 'https://api.biblesupersearch.com',
        'useJSONP'                  => FALSE,
        'defaultLanguage'           => 'en',
        'enabledBibles'             => array(),
        'enableAllBibles'           => TRUE,
        'interface'                 => 'Classic',  // 'Expanding'
        'toggleAdvanced'            => TRUE,
        'formatButtonsToggle'       => FALSE,
        'includeTestament'          => false,
        'defaultDestinationPage'    => 0,
        "extraButtonsSeparate"      => 'default',
        'pager'                     => 'default',
        'pageScroll'                => 'instant',
        'pageScrollTopPadding'      => 0,
        'formatButtons'             => 'default',
        'navigationButtons'         => 'default',
        'bibleGrouping'             => 'language',
        'bibleSorting'              => 'language_english|name',
        'language'                  => 'en',
        'landingReference'          => '',
        'debug'                     => false,
    );  

    public static $selector_options = [
        'bibleGrouping' => [
            'none'                  => 'None',
            'language'              => 'Language - Endonym',
            'language_english'      => 'Language - English Name',
            'language_and_english'  => 'Language - Endonym and English Name',
        ],        
        'bibleSorting' => [
            'language_english|name'         => 'Language - English Name / Full Name',
            'language_english|shortname'    => 'Language - English Name / Short Name',
            'language_english|rank|name'    => 'Language - English Name / Rank / Full Name',
        ],
        'language' => [
            'en'                    => 'English',
            // 'en_pirate'             => 'English - Pirate', // (for debugging purposes)
            // 'ar'                    => 'العربية  / Arabic',
            'fr'                    => 'Français / French',
            'lv'                    => 'Latviešu / Latvian',
            'es'                    => 'Español / Spanish',
            'ro'                    => 'Română / Romanian',
            'ru'                    => 'Русский / Russian',
            'zh_TW'                 => '繁體中文 / Chinese - Traditional',
            'zh_CN'                 => '简体中文 / Chinese - Simplified',
        ],
    ];

    protected $tabs = array(
        'general'  => array(
            'name'          => 'General',
            // need list of fields for each tab.  IF field is not in list, it won't save!
            'texts'         => array(), // input and textarea
            'selects'       => array('defaultDestinationPage', 'interface', 'pager', 'pageScroll', 'formatButtons', 'navigationButtons', 'language'),
            'checkboxes'    => array('overrideCss', 'toggleAdvanced', 'formatButtonsToggle', 'includeTestament'),
        ),        
        'bible'  => array(
            'name'          => 'Bibles',
            'texts'         => array('landingReference'),
            'selects'       => array('defaultBible', 'enabledBibles', 'bibleGrouping', 'bibleSorting'),
            'checkboxes'    => array('enableAllBibles'),
        ),        
        // 'style'  => array(
        //     'name'          => 'Appearance',
        //     'fields'        => array(), // 'formStyles'),
        //     'checkboxes'    => array('overrideCss'),
        // ),
        'advanced' => array(
            'name'          => 'Advanced',
            'texts'         => array('apiUrl', 'pageScrollTopPadding'),
            'selects'       => array(),
            'checkboxes'    => array('debug'),
        ),
    );
    
    public function __construct() {
        // Do Something
    }

    static public function getSelectorOptions($selector) {
        if(array_key_exists($selector, static::$selector_options)) {
            return static::$selector_options[$selector];
        }

        return FALSE;
    }

    public function getOptions($dont_set_default = FALSE) {
        // todo - make this generic!

        $options = get_option( $this->option_index );

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
        }

        $options['defaultBible'] = array_filter($options['defaultBible']);

        return $options;
    }

    public function getOptionsFiltered() {
        $options = $this->getOptions();

        if($options['enableAllBibles']) {
            $options['enabledBibles'] = [];
        }
    }

    protected function _getOptionsFromStorage() {

    }

    // todo - make this generic!
    public function setDefaultOptions() {

        if ( ! is_array( get_option( $this->option_index ) ) ) {
            delete_option( $this->option_index ); // just in case
            update_option( $this->option_index, $this->default_options );
        }

        // Flush rewrite cache
        // flush_rewrite_rules( true );
    }

    // todo - make this generic!
    public function validateOptions( $incoming ) {
        $current = $input = $this->getOptions(TRUE);
        
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : 'general';
        $tab_item = $this->tabs[ $tab ];

        foreach($tab_item['texts'] as $field) {
            if(array_key_exists($field, $incoming)) {
                $input[$field] = $incoming[$field];
            }
        }          

        foreach($tab_item['selects'] as $field) {
            // if(array_key_exists($field, $incoming) && !empty($incoming[$field])) {
            if(array_key_exists($field, $incoming)) {
                $input[$field] = $incoming[$field];
            }
        }       

        foreach($tab_item['checkboxes'] as $field) {
            $input[$field] = (array_key_exists($field, $incoming) && !empty($incoming[$field]));
        }

        print_r($incoming);
        print_r($input);
        die();

        // if(!isset($input['enableAllBibles'])) {
        //     $input['enableAllBibles'] = FALSE;
        // }

        // $input['overrideCss'] = ($input['overrideCss']) ? TRUE : FALSE;

        // Cherry-pick default values 
        foreach($this->default_options as $item => $value) {
            if(!array_key_exists($item, $input)) {
                $input[$item] = $value;
            }
        }

        if($tab == 'bible') {
            if($input['enableAllBibles']) {
                $input['enabledBibles'] = [];
            }
            else {
                // Make sure default Bible is in list of selected Bibles
                if(!in_array($input['defaultBible'], $input['enabledBibles'])) {
                    $input['enabledBibles'][] = $input['defaultBible'];
                }
            }
        }

        if($tab == 'advanced' && empty($input['apiUrl'])) {
            $input['apiUrl'] = $this->default_options['apiUrl'];
        }

        $this->_setStaticsReset(); // Always Force Reload statics when options saved

        return $input;
    }

    public function getRecomendedPlugins($missing_only = FALSE) {
        $plugins = array(
            array(
                'name'          => 'disable-emojis',
                'file'          => 'disable-emojis/disable-emojis.php',
                'label'         => 'Disable Emojis',
                'description'   => ' - WordPress converts some characters to emojis, and this may cause Bible SuperSearch to not look as intended',
            )
        );

        if($missing_only) {
            foreach($plugins as $key => $plugin) {
                if(is_plugin_active($plugin['file'])) {
                    unset($plugins[$key]);
                }
            }
        }

        return $plugins;
    }

    public function displayPluginOptions() {
        $tabs = $this->tabs;
        $tab  = (array_key_exists('tab', $_REQUEST) && $_REQUEST['tab']) ? $_REQUEST['tab'] : 'general';

        if ( ! isset( $_REQUEST['settings-updated'] ) ) {
            $_REQUEST['settings-updated'] = FALSE;
        }

        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        if(!$tabs[ $tab ]) {
            wp_die( __( 'Invalid tab.' ) );
        }

        // $this->setDefaultOptions();
        biblesupersearch_enqueue_option();
        $options    = $this->getOptions();
        $bibles     = $this->getBible();
        $interfaces = $this->getInterfaces(); 
        $selectables = $this->getSelectableItems();
        $statics = $this->getStatics();

        $using_main_api = (empty($options['apiUrl']) || $options['apiUrl'] == $this->default_options['apiUrl']) ? TRUE : FALSE;

        $reccomended_plugins = $this->getRecomendedPlugins(TRUE);

        require( dirname(__FILE__) . '/template.options.php');
        return;
    }

    // todo - make this generic! 
    public function getLandingPageOptions($render_html = FALSE, $value = NULL, $zero_option = 'None', $zero_default = FALSE) {
        global $wpdb;

        $sql = "
            SELECT ID, post_title, post_type, post_content FROM `{$wpdb->prefix}posts`
            WHERE ( post_content LIKE '%[biblesupersearch]%' OR post_content LIKE '%[biblesupersearch %]%' )
            AND post_type IN ('page','post') AND post_status = 'publish'
        ";

        $results = $wpdb->get_results($sql, ARRAY_A);

        if(!$render_html) {
            return $results;
        }

        $html = "<option value='0'> None </option>";

        foreach($results as $res) {
            if(!preg_match('/[^\[]\[biblesupersearch( .*)?]/', ' ' . $res['post_content'])) {
                continue; // Ignore example shortcodes ie [[biblesupersearch]]
            }

            $sel  = ($res['ID'] == $value) ? "selected = 'selected'" : '';
            $title = ($res['post_title']) ? $res['post_title'] : '(No Title, ID = ' . $res['ID'] . ')';
            $type = ucfirst($res['post_type']);
            $html .= "<option value='{$res['ID']}' {$sel}>{$type}: {$title}</option>";
        }

        return $html;
    }

    public function getBible($module = NULL) {
        $statics = $this->getStatics();
        $lang = array();

        if(is_array($statics['bibles'])) {        
            foreach($statics['bibles'] as $module => &$bible) {
                $lang[$module] = $bible['lang'];

                $bible['display'] = $bible['name'] . ' (' . $bible['lang'] . ')';
                $bible['display_short'] = $bible['name'];
            }
            
            array_multisort($lang, SORT_REGULAR, $statics['bibles']);
            return $statics['bibles'];
        }

        $bibles = array(
            'kjv'           => array('name' => 'Authorized King James Version', 'lang' => 'English', 'shortname' => 'KJV'),
            'kjv_strongs'   => array('name' => 'KJV with Strongs',              'lang' => 'English', 'shortname' => 'KJV Strongs'),
            'tyndale'       => array('name' => 'Tyndale Bible',                 'lang' => 'English', 'shortname' => 'Tyndale'),
            'coverdale'     => array('name' => 'Coverdale Bible',               'lang' => 'English', 'shortname' => 'Coverdale'),
            'bishops'       => array('name' => 'Bishops Bible',                 'lang' => 'English', 'shortname' => 'Bishops'),
            'geneva'        => array('name' => 'Geneva Bible',                  'lang' => 'English', 'shortname' => 'Geneva'),
            'tr'            => array('name' => 'Textus Receptus NT',            'lang' => 'English', 'shortname' => 'TR'),
            'trparsed'      => array('name' => 'Textus Receptus Parsed NT',     'lang' => 'English', 'shortname' => 'TR Parsed'),
            'rv_1858'       => array('name' => 'Reina Valera 1858 NT',          'lang' => 'English', 'shortname' => 'RV 1858'),
            'rv_1909'       => array('name' => 'Reina Valera 1909',             'lang' => 'English', 'shortname' => 'RV 1909'),
            'sagradas'      => array('name' => 'Sagradas Escrituras',           'lang' => 'English', 'shortname' => 'Sagradas'),
            'rvg'           => array('name' => 'Reina Valera Gómez',            'lang' => 'English', 'shortname' => 'RVG'),
            'martin'        => array('name' => 'Martin',                        'lang' => 'English', 'shortname' => 'Martin'),
            'epee'          => array('name' => 'La Bible de l\'Épée',           'lang' => 'English', 'shortname' => 'Epee'),
            'oster'         => array('name' => 'Ostervald',                     'lang' => 'English', 'shortname' => 'Oster'),
            'afri'          => array('name' => 'Afrikaans 1953',                'lang' => 'English', 'shortname' => 'Afrikaans'),
            'svd'           => array('name' => 'Smith Van Dyke',                'lang' => 'English', 'shortname' => 'SVD'),
            'bkr'           => array('name' => 'Bible Kralicka',                'lang' => 'English', 'shortname' => 'BKR'),
            'stve'          => array('name' => 'Staten Vertaling',              'lang' => 'English', 'shortname' => 'Stve'),
            'finn'          => array('name' => 'Finnish 1776 (Finnish)',        'lang' => 'English', 'shortname' => 'Finn'),
            'luther'        => array('name' => 'Luther Bible',                  'lang' => 'English', 'shortname' => 'Luther'),
            'diodati'       => array('name' => 'Diodati',                       'lang' => 'English', 'shortname' => 'Diodati'),
            'synodal'       => array('name' => 'Synodal',                       'lang' => 'English', 'shortname' => 'Synodal'),
            'karoli'        => array('name' => 'Karoli',                        'lang' => 'English', 'shortname' => 'Karoli'),
            'lith'          => array('name' => 'Lithuanian Bible',              'lang' => 'English', 'shortname' => 'Lith'),
            'maori'         => array('name' => 'Maori Bible',                   'lang' => 'English', 'shortname' => 'Maori'),
            'cornilescu'    => array('name' => 'Cornilescu',                    'lang' => 'English', 'shortname' => 'Cornilescu'),
            'thaikjv'       => array('name' => 'Thai KJV',                      'lang' => 'English', 'shortname' => 'Thaikjv'),
            'wlc'           => array('name' => 'WLC',                           'lang' => 'English', 'shortname' => 'WLC'),
        );

        return $bibles;
    }

    public function getBibles($statics = NULL, $sorting = NULL, $grouping = NULL) {
        $options = $this->getOptions();
        $statics = $statics ? $statics : $this->getStatics();
        
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
        $sortable = array();

        foreach($sorting as $k => $s) {
            $sortable[] = array();
            $sortable[] = SORT_REGULAR; // Todo, DESC, ect
        }

        if(is_array($statics['bibles'])) {        
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

        return $this->getBible();
    }

    public function getEnabledBibles($statics = array(), $sorting = NULL, $grouping = NULL) {
        $options = $this->getOptions();
        $bibles  = $this->getBibles($statics, $sorting, $grouping);

        if($options['enableAllBibles'] || !is_array($options['enabledBibles']) || empty($options['enabledBibles']) ) {
            return $bibles;
        }

        $enabled = array();

        foreach($bibles as $module => $bible) {
            if(in_array($module, $options['enabledBibles'])) {
                $enabled[$module] = $bible;
            }
        }

        return $enabled;
    }

    // TODO - make generic 
    // This code is WP specific
    protected function _setStaticsReset() {
        // $statics               = get_option('biblesupersearch_statics');
        // $last_update_timestamp = (is_array($statics) && array_key_exists('timestamp', $statics)) ? $statics['timestamp'] : 0;

        // if($last_update_timestamp) {
        //     $statics['timestamp'] = 0;
        //     update_option('biblesupersearch_statics', $statics);
        // }
    }

    protected $statics_loading = FALSE;

    public function getUrl() {
        $options    = $this->getOptions();
        $url        = $options['apiUrl'] ?: $this->default_options['apiUrl'];
        return $url;
    }

    public function apiVersion() {
        $statics = $this->getStatics();
        return $statics['version'];
    }

    public function getStatics($force = FALSE) {
        if($this->statics_loading == TRUE) {
            return FALSE;
        }

        $options    = $this->getOptions();
        $url        = $options['apiUrl'] ?: $this->default_options['apiUrl'];
        $allow_url_fopen       = intval(ini_get('allow_url_fopen'));
        $cached_statics        = get_option('biblesupersearch_statics');
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
        $data       = array('language'  => 'en');
        
        $result     = $this->_apiActionHelper('statics', $url, $data);
        $this->statics_loading = FALSE;
        
        if ($result === FALSE) { 
            if($last_update_timestamp && !$force) {
                return $cached_statics;
            }
            elseif(!function_exists('curl_init') && $allow_url_fopen == 0) {
                wp_die( __( 'Error: please have your web host turn on php.ini config allow_url_fopen OR install cURL to continue') );
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
                    update_option($this->option_index, $options);
                    //wp_die($msg);
                    echo($msg);
                }
                else {
                    wp_die( __( 'Error: unable to load data from the Bible SuperSearch API server at ' . $url ) );
                }
            }
        }

        $result['results']['timestamp'] = time();
        $this->_afterFetchStatics($result);
        $this->statics_loading = FALSE;
        return $result['results'];
    }

    protected function _afterFetchStatics($result) {
        // Persist to DB!
    }

    protected function _apiActionHelper($action, $api_url, $data) {
        $url_action = ($action == 'query') ? '/api' : '/api/' . $action;
        $url = $api_url . $url_action;

        $result = FALSE;
        $allow_url_fopen = (int) ini_get('allow_url_fopen');
        $err = error_reporting();
        error_reporting(E_ERROR | E_PARSE);

        // echo('<pre>');
        // var_dump($url);

        // Attempt 1: Via file_get_contents
        if($allow_url_fopen == 1) {        
            $options = array(
                'http' => array(        // Use key 'http' even if you send the request to https://
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                )
            );
 
            $context = stream_context_create($options);
            $result  = file_get_contents($url, FALSE, $context);
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
        }

        error_reporting($err);

        if($action == 'statics') {
            // var_dump($api_url);
            // var_dump($url);
            // var_dump($curl_info);
            // var_dump($data);
            // var_dump($action);
            // var_dump($result);
            // die();
        }

        return ($result === FALSE) ? FALSE : json_decode($result, TRUE);
    }

    public function renderDownloadPage() {

    }

    public function getInterfaceByName($name) {
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

    protected function _processInterfaceName($name) {
        $proc = str_replace('-', '', $name);
        // $proc = preg_replace('/\s*/', ' ', $proc);
        $proc = ucwords($proc);
        $proc = str_replace(' ', '', $proc);
        return $proc;
    }

    public function getInterfaces() {
        return array(
            // 'TwentyTwenty' => array(
            //     'name'  => 'Twenty Twenty', 
            //     'class' => 'twentytwenty'
            // ),
            // 'Classic' => array(
            //     'name'  => 'Classic (Default Classic Skin)', 
            //     'class' => 'classic',
            // ),
            'Expanding' => array(
                'name'  => 'Expanding', 
                'class' => 'expanding',
            ),
            'ExpandingLargeInput' => array(
                'name'  => 'Expanding - Large Input', 
                'class' => 'expanding',
            ),                          
            'BrowsingBookSelector' => array(
                'name'  => 'Browsing with Book Selector', 
                'class' => 'browsing',
            ),              
            'BrowsingBookSelectorHorizontal' => array(
                'name'  => 'Browsing with Book Selector, Horizontal Form', 
                'class' => 'browsing',
            ),              
            'Classic' => array(
                'name'  => 'Classic (alias of Classic - User Friendly 2)',  // alias ClassicUserFriendly2
                'class' => 'classic',
            ),            
            'ClassicUserFriendly1' => array(
                'name'  => 'Classic - User Friendly 1', 
                'class' => 'classic',
            ),                  
            'ClassicUserFriendly2' => array(
                'name'  => 'Classic - User Friendly 2', 
                'class' => 'classic',
            ),            
            'ClassicParallel2' => array(
                'name'  => 'Classic - Parallel 2', 
                'class' => 'classic',
            ),
            'ClassicAdvanced' => array(
                'name'  => 'Classic - Advanced', 
                'class' => 'classic',
            ),                 
            'Minimal' => array(
                'name'  => 'Minimal', 
                'class' => 'minimal'
            ),              
            'MinimalWithBible' => array(
                'name'  => 'Minimal with Bible', 
                'class' => 'minimal'
            ),               
            'MinimalWithBibleWide' => array(
                'name'  => 'Minimal with Bible - Wide', 
                'class' => 'minimal'
            ),              
            'MinimalWithShortBible' => array(
                'name'  => 'Minimal with Short Bible', 
                'class' => 'minimal'
            ),              
            'MinimalWithParallelBible' => array(
                'name'  => 'Minimal with Parallel Bible', 
                'class' => 'minimal'
            ),               
            'MinimalGoRandom' => array(
                'name'  => 'Minimal Go Random', 
                'class' => 'minimal'
            ),                
            'MinimalGoRandomBible' => array(
                'name'  => 'Minimal Go Random with Bible', 
                'class' => 'minimal'
            ),            
            'MinimalGoRandomParallelBible' => array(
                'name'  => 'Minimal Go Random with Parallel Bible', 
                'class' => 'minimal'
            ),
            'CustomUserFriendly2BookSel' => array(
                'name'  => 'Custom - User Friendly 2 with Book Selector', 
                'class' => 'classic',
            ),   
        );
    }

    public function getSelectableItems() {
        return array(
            'pager' => array(
                'name'  => 'Paginator',
                'desc'  => 'Used to browse through multiple pages of search results.',
                'items' => $this->getPagers(),
            ),            
            'pageScroll' => array(
                'name'  => 'Page Scrolling',
                'desc'  => 'When the page loads or query executes, how to scroll the page up to top of the results.',
                'items' => $this->getPageScrolls(),
            ),
            'navigationButtons' => array(
                'name'  => 'Navigation Buttons',
                'desc'  => 'Used to browse between chapters and books.',
                'items' => $this->getNavigationButtons(),
            ),
            'formatButtons' => array(
                'name'  => 'Formatting Buttons',
                'desc'  => 'The formatting buttons appear below the form, and include size, font, and copy options.',
                'items' => $this->getFormatButtons(),
            ),            
            'extraButtonsSeparate' => array(
                'name'  => 'How to Display Extra Buttons',
                'desc'  => 'These include help and download dialog buttons. &nbsp; * Some skins do not support this option.',
                'items' => $this->getExtraButtons(),
            ),            
        );
    }

    public function getPagers() {
        return array(
            'default' => array(
                'name' => $this->_getDefaultItemText(),
            ),
            'Classic' => array(
                'name'  => 'Classic',
            ),            
            'Clean' => array(
                'name'  => 'Clean',
            ),
        );
    }        

    public function getPageScrolls() {
        return array(
            'instant' => array(
                'name' => 'Instant',
            ),
            'smooth' => array(
                'name'  => 'Smooth',
            ),            
            'none' => array(
                'name'  => 'None - No scrolling',
            ),
        );
    }    

    public function getNavigationButtons() {
        return array(
            'default' => array(
                'name' => $this->_getDefaultItemText(),
            ),
            'Classic' => array(
                'name'  => 'Classic',
            ),            
            'Stylable' => array(
                'name'  => 'Stylable',
            ),
        );
    }    

    public function getFormatButtons() {
        return array(
            'default' => array(
                'name' => $this->_getDefaultItemText(),
            ),
            'Classic' => array(
                'name'  => 'Classic (Old icons from v2 - deprecated)',
            ),            
            'Stylable' => array(
                'name'  => 'Stylable - Wide',
            ),            
            'StylableNarrow' => array(
                'name'  => 'Stylable - Narrow',
            ),           
            'StylableMinimal' => array(
                'name'  => 'Stylable - Minimal buttons, with settings dialog.',
            ),            
            'none' => array(
                'name'  => 'None',
            ),
        );
    }   

    public function getExtraButtons() {
        return array(
            'default' => array(
                'name' => $this->_getDefaultItemText(),
            ),
            'false' => array(
                'name'  => 'With Formatting Buttons',
            ),
            'true' => array(
                'name'  => 'Separate from Formatting Buttons *',
            ),            
            'none' => array(
                'name'  => 'None - Do not display',
            ),
        );
    }        

    protected function _getDefaultItemText() {
        return 'Default for Selected Skin';
    }
}

