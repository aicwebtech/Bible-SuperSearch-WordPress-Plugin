<?php

defined( 'ABSPATH' ) or die; // exit if accessed directly


class BibleSuperSearch_Options {
    protected $option_index = 'biblesupersearch_options';

    protected $default_options = array(
        'defaultBible'      => 'kjv',
        'apiUrl'            => 'https://api.biblesupersearch.com',
        'useJSONP'          => FALSE,
        'defaultLanguage'   => 'en',
        'enabledBibles'     => [],
        'enableAllBibles'   => TRUE,
        'interface'         => 'Classic',
        
        // WordPress specific
        'overrideCss'       => TRUE,
        'extraCss'          => '',
    );  
    
    public function __construct() {
        // Register some stuff with WordPress
        
        // Settings Menu Page
        add_action( 'admin_menu', array($this, 'pluginMenu') );

        register_activation_hook( dirname(__FILE__) . '/biblesupersearch.php', array($this, 'setDefaultOptions' ) );

        // Flush rewrite rules before everything else (if required)
        // add_action( 'init', array( $this, 'maybe_flush_rewrite_rules' ) );
        
        // Set-up Action and Filter Hooks
        add_action( 'admin_init', array( $this, 'adminInit' ) );
    }

    function adminInit() {
        global $wp_version;

        $args = array($this, 'validateOptions');

        if ( version_compare( $wp_version, '4.7.0', '>=' ) ) {
            $args = array(
                'sanitize_callback' => array($this, 'validateOptions')
            );
        }

        register_setting( 'aicwebtech_plugin_options', $this->option_index, $args );
    }

    public function pluginMenu() {
        $Options = new static();
        add_options_page( 'Bible SuperSearch Options', 'Bible SuperSearch', 'manage_options', 'biblesupersearch', array($this, 'displayPluginOptions'));
    }

    public function getOptions($dont_set_default = FALSE) {
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

        return $options;
    }

    public function setDefaultOptions() {
        if ( ! is_array( get_option( $this->option_index ) ) ) {
            delete_option( $this->option_index ); // just in case
            update_option( $this->option_index, $this->default_options );
        }

        // Flush rewrite cache
        // flush_rewrite_rules( true );
    }

    public function validateOptions( $input ) {
        $current = $this->getOptions(TRUE);

        if(!isset($input['enableAllBibles'])) {
            $input['enableAllBibles'] = FALSE;
        }

        $input['overrideCss'] = ($input['overrideCss']) ? TRUE : FALSE;

        // Cherry-pick default values 
        foreach($this->default_options as $item => $value) {
            if(!array_key_exists($item, $input)) {
                $input[$item] = $value;
            }
        }

        if($input['enableAllBibles']) {
            $input['enabledBibles'] = [];
        }
        else {
            // Make sure default Bible is in list of selected Bibles
            if(!in_array($input['defaultBible'], $input['enabledBibles'])) {
                $input['enabledBibles'][] = $input['defaultBible'];
            }
        }

        if($current['apiUrl'] != $input['apiUrl']) {
            $this->_setStaticsReset(); // Force Reload statics here if URL changed
        }

        return $input;
    }

    public function displayPluginOptions() {
        if ( ! isset( $_REQUEST['settings-updated'] ) ) {
            $_REQUEST['settings-updated'] = false;
        }

        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        // $this->setDefaultOptions();
        biblesupersearch_enqueue_option();
        $options    = $this->getOptions();
        $bibles     = $this->getBible();
        $interfaces = $this->getInterfaces(); 

        require( dirname(__FILE__) . '/template.options.php');
        return;
    }

    // TODO - move to helper library

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

    protected function _setStaticsReset() {
        $statics               = get_option('biblesupersearch_statics');
        $last_update_timestamp = (is_array($statics) && array_key_exists('timestamp', $statics)) ? $statics['timestamp'] : 0;

        if($last_update_timestamp) {
            $statics['timestamp'] = 0;
            update_option('biblesupersearch_statics', $statics);
        }
    }

    // TODO - move to helper library
    protected $statics_loading = FALSE;

    public function getStatics($force = FALSE) {
        if($this->statics_loading == TRUE) {
            return FALSE;
        }

        $allow_url_fopen       = intval(ini_get('allow_url_fopen'));
        $this->statics_loading = TRUE;
        $cached_statics        = get_option('biblesupersearch_statics');
        $last_update_timestamp = (is_array($cached_statics) && array_key_exists('timestamp', $cached_statics)) ? $cached_statics['timestamp'] : 0;

        if($last_update_timestamp > time() - 3600 && !$force) {
            return $cached_statics;
        }

        $options    = $this->getOptions();
        $url        = $options['apiUrl'] ?: $this->default_options['apiUrl'];
        $data       = array('language' => 'en');
        $result     = $this->_apiActionHelper('statics', $url, $data);
        
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
                    wp_die($msg);
                    // echo($msg);
                }
                else {
                    wp_die( __( 'Error: unable to load data from the Bible SuperSearch API server at ' . $url ) );
                }
            }
        }

        $result['results']['timestamp'] = time();
        update_option('biblesupersearch_statics', $result['results']);
        $this->statics_loading = FALSE;
        return $result['results'];
    }

    // TODO - move to helper library

    protected function _apiActionHelper($action, $api_url, $data) {
        $url_action = ($action == 'query') ? '/api' : '/api/' . $action;
        $url = $api_url . $url_action;

        $result = FALSE;
        $allow_url_fopen = intval(ini_get('allow_url_fopen'));
        $err = error_reporting();
        error_reporting(E_ERROR | E_PARSE);

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
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $result = curl_exec($ch);
            curl_close($ch);
        }

        error_reporting($err);
        return ($result === FALSE) ? FALSE : json_decode($result, TRUE);
    }

    // TODO - move to helper library

    public function getInterfaces() {
        return array(
            // 'TwentyTwenty' => array(
            //     'name'  => 'Twenty Twenty', 
            //     'class' => 'twentytwenty'
            // ),
            'Classic' => array(
                'name'  => 'Classic', 
                'class' => 'classic'
            ),
            // 'ClassicUserFriendly2' => array(
            //     'name'  => 'Classic - User Friendly 2', 
            //     'class' => 'classic'
            // ),
            'ClassicAdvanced' => array(
                'name'  => 'Classic - Advanced', 
                'class' => 'classic'
            ),                 
            // 'Minimal' => array(
            //     'name'  => 'Minimal', 
            //     'class' => 'minimal'
            // ),            
            // 'CL2' => array(
            //     'name'  => 'CL2', 
            //     'class' => 'classic'
            // ),            
            // 'CL3' => array(
            //     'name'  => 'CL3', 
            //     'class' => 'classic'
            // ),            
            // 'CL4' => array(
            //     'name'  => 'CL4', 
            //     'class' => 'classic'
            // ),            
            // 'CL5' => array(
            //     'name'  => 'CL5', 
            //     'class' => 'classic'
            // ),            
            // 'CL6' => array(
            //     'name'  => 'CL6', 
            //     'class' => 'classic'
            // ),            
            // 'CL7' => array(
            //     'name'  => 'CL7', 
            //     'class' => 'classic'
            // ),            
            // 'CL8' => array(
            //     'name'  => 'CL8', 
            //     'class' => 'classic'
            // ),
        );
    }
}

$BibleSuperSearch_Options = new BibleSuperSearch_Options();
