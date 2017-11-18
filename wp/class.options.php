<?php

defined( 'ABSPATH' ) or die; // exit if accessed directly


class BibleSuperSearch_Options {
    protected $option_index = 'biblesupersearch_options';

    protected $default_options = array(
        'defaultBible'      => 'kjv',
        "apiUrl"            => "http://api.biblesupersearch.com/api",
        "useJSONP"          => FALSE,
        "defaultLanguage"   => "en",
        "enabledBibles"     => [],
        "enableAllBibles"   => TRUE,
        "interface"         => "Classic",
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

        register_setting( 'aicwebtech_plugin_options', 'biblesupersearch_options', $args );
    }

    public function pluginMenu() {
        $Options = new static();
        add_options_page( 'Bible SuperSearch Options', 'Bible SuperSearch', 'manage_options', 'biblesupersearch', array($this, 'displayPluginOptions'));
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
        if(!isset($input['enableAllBibles'])) {
            $input['enableAllBibles'] = FALSE;
        }

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

        $_SESSION['biblesupersearch_statics_loaded'] = FALSE; // Force statics reload
        return $input;
    }

    public function displayPluginOptions() {

        if ( ! isset( $_REQUEST['settings-updated'] ) ) {
            $_REQUEST['settings-updated'] = false;
        }

        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        biblesupersearch_enqueue_option();
        $options = get_option( $this->option_index );
        $bibles = $this->getBible();
        $interfaces = $this->getInterfaces(); 

        require( dirname(__FILE__) . '/template.options.php');
        return;
    }

    public function getBible($module = NULL) {
        if(empty($_SESSION['biblesupersearch_statics_loaded'])) {
            $this->loadStatics();
        }

        $statics = get_option('biblesupersearch_statics');

        $lang = array();
        
        foreach($statics['bibles'] as $module => &$bible) {
            $lang[$module] = $bible['lang'];

            $bible['display'] = $bible['name'] . ' (' . $bible['lang'] . ')';
            $bible['display_short'] = $bible['name'];
        }

        array_multisort($lang, SORT_REGULAR, $statics['bibles']);

        return $statics['bibles'];

        $bibles = array(
            'kjv'           => array('name' => 'Authorized King James Version', 'shortname' => 'KJV'),
            'kjv_strongs'   => array('name' => 'KJV with Strongs',              'shortname' => 'KJV Strongs'),
            'tyndale'       => array('name' => 'Tyndale Bible',                 'shortname' => 'Tyndale'),
            'coverdale'     => array('name' => 'Coverdale Bible',               'shortname' => 'Coverdale'),
            'bishops'       => array('name' => 'Bishops Bible',                 'shortname' => 'Bishops'),
            'geneva'        => array('name' => 'Geneva Bible',                  'shortname' => 'Geneva'),
            'tr'            => array('name' => 'Textus Receptus NT',            'shortname' => 'TR'),
            'trparsed'      => array('name' => 'Textus Receptus Parsed NT',     'shortname' => 'TR Parsed'),
            'rv_1858'       => array('name' => 'Reina Valera 1858 NT',          'shortname' => 'RV 1858'),
            'rv_1909'       => array('name' => 'Reina Valera 1909',             'shortname' => 'RV 1909'),
            'sagradas'      => array('name' => 'Sagradas Escrituras',           'shortname' => 'Sagradas'),
            'rvg'           => array('name' => 'Reina Valera Gómez',            'shortname' => 'RVG'),
            'martin'        => array('name' => 'Martin',                        'shortname' => 'Martin'),
            'epee'          => array('name' => 'La Bible de l\'Épée',           'shortname' => 'Epee'),
            'oster'         => array('name' => 'Ostervald',                     'shortname' => 'Oster'),
            'afri'          => array('name' => 'Afrikaans 1953',                'shortname' => 'Afrikaans'),
            'svd'           => array('name' => 'Smith Van Dyke',                'shortname' => 'SVD'),
            'bkr'           => array('name' => 'Bible Kralicka',                'shortname' => 'BKR'),
            'stve'          => array('name' => 'Staten Vertaling',              'shortname' => 'Stve'),
            'finn'          => array('name' => 'Finnish 1776 (Finnish)',        'shortname' => 'Finn'),
            'luther'        => array('name' => 'Luther Bible',                  'shortname' => 'Luther'),
            'diodati'       => array('name' => 'Diodati',                       'shortname' => 'Diodati'),
            'synodal'       => array('name' => 'Synodal',                       'shortname' => 'Synodal'),
            'karoli'        => array('name' => 'Karoli',                        'shortname' => 'Karoli'),
            'lith'          => array('name' => 'Lithuanian Bible',              'shortname' => 'Lith'),
            'maori'         => array('name' => 'Maori Bible',                   'shortname' => 'Maori'),
            'cornilescu'    => array('name' => 'Cornilescu',                    'shortname' => 'Cornilescu'),
            'thaikjv'       => array('name' => 'Thai KJV',                      'shortname' => 'Thaikjv'),
            'wlc'           => array('name' => 'WLC',                           'shortname' => 'WLC'),
        );

        return $bibles;
    }

    public function loadStatics() {
        $options = get_option( $this->option_index ); ;
        $url = $options['apiUrl'] . '/statics';
        $data = array('language' => 'en');

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        
        $context = stream_context_create($options);
        $result  = file_get_contents($url, false, $context);
        
        if ($result === FALSE) { 
            die('unalble to load statics');
            return;
        }

        else $data = json_decode($result, TRUE);
        // print_r($data);
        update_option('biblesupersearch_statics', $data['results']);
        $_SESSION['biblesupersearch_statics_loaded'] = TRUE;
    }

    public function getInterfaces() {
        return array(
            'TwentyTwenty' => array(
                'name'  => 'Twenty Twenty', 
                'class' => 'twentytwenty'
            ),
            'Classic' => array(
                'name'  => 'Classic', 
                'class' => 'classic'
            ),
            'ClassicUserFriendly2' => array(
                'name'  => 'Classic - User Friendly 2', 
                'class' => 'classic'
            ),
            'ClassicUserAdvanced' => array(
                'name'  => 'Classic - Advanced', 
                'class' => 'classic'
            ),
        );
    }
}

$BibleSuperSearch_Options = new BibleSuperSearch_Options();
