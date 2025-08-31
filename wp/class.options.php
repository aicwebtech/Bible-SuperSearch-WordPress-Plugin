<?php

defined( 'ABSPATH' ) or die; // exit if accessed directly

class BibleSuperSearch_Options_WP extends BibleSuperSearch_Options_Abstract 
{
    
    protected $option_index = 'biblesupersearch_options';
    protected $debug_count = 0;
    
    public function __construct() 
    {
        parent::__construct();

        // Add some WordPress-specific options and tabs

        $this->default_options['overrideCss'] = TRUE;
        $this->default_options['extraCss'] = '';

        $this->tabs['general']['checkboxes'][] = 'overrideCss';
        $this->tabs['advanced']['texts'][] = 'extraCss';

        $this->tabs['docs'] = [
            'name'          => 'Documentation',
            'type'          => 'static',
        ];

        // Register some stuff with WordPress
        
        // Settings Menu Page
        add_action( 'admin_menu', array($this, 'pluginMenu') );

        register_activation_hook( dirname(__FILE__) . '/biblesupersearch.php', array($this, 'setDefaultOptions' ) );

        // Flush rewrite rules before everything else (if required)
        // add_action( 'init', array( $this, 'maybe_flush_rewrite_rules' ) );
        
        // Set-up Action and Filter Hooks
        add_action( 'admin_init', array( $this, 'adminInit' ) );
    }

    function adminInit() 
    {
        global $wp_version;

        $args = array($this, 'validateOptions');

        if ( version_compare( $wp_version, '4.7.0', '>=' ) ) {
            $args = array(
                'sanitize_callback' => array($this, 'validateOptions')
            );
        }

        register_setting( 'aicwebtech_plugin_options', $this->option_index, $args );
    }

    public function pluginMenu() 
    {

        add_menu_page(
            'Bible SuperSearch', // browser title
            'Bible SuperSearch', // top menu title
            'manage_options',
            'biblesupersearch',
            [$this, 'displayPluginOptionsNew'],
            'dashicons-book-alt',
            76
        );

        add_submenu_page(
            'biblesupersearch',
            'Bible SuperSearch Documentation',
            'Documentation',
            'manage_options',
            'biblesupersearch_docs',
            [$this, 'displayPluginDocumentation']
        );

        global $submenu;

		if ( isset( $submenu[ 'biblesupersearch'] ) ) {
			// @codingStandardsIgnoreStart
			$submenu[ 'biblesupersearch' ][0][0] = 'Settings';
			// @codingStandardsIgnoreEnd
		}
    }

    public function setOptions($options) 
    {
        $this->refresh_statics = false;
        $options = $this->validateOptions($options);
        
        update_option( $this->option_index, $options );
    }

    public function getOptions($dont_set_default = FALSE) 
    {
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

    public function setDefaultOptions() 
    {
        if ( ! is_array( get_option( $this->option_index ) ) ) {
            delete_option( $this->option_index ); // just in case
            update_option( $this->option_index, $this->default_options );
        }

        // Flush rewrite cache
        // flush_rewrite_rules( true );
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
                        if(array_key_exists($field, $incoming)) {
                            if(is_string($incoming[$field])) {
                                $input[$field] = json_decode($incoming[$field], true);
                            } elseif(is_array($incoming[$field])) {
                                $input[$field] = $incoming[$field];
                            } else {
                                $input[$field] = [];
                            }

                            $input[$field] = is_string($incoming[$field]) ? $incoming[$field] : json_encode($incoming[$field]);
                        }

                        $input[$field] = (array_key_exists($field, $incoming)) ? $incoming[$field] : [];

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

    public function displayPluginOptionsNew() 
    {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        // biblesupersearch_enqueue_option(); // not needed for Vue
        $options    = $this->getOptions();
        $interfaces = $this->getInterfaces(); 
        // $bibles     = $this->getBible();
        // $languages  = $this->getLanguages();

        $using_main_api = (empty($options['apiUrl']) || $options['apiUrl'] == $this->default_options['apiUrl']) ? TRUE : FALSE;

        $statics = $this->getStatics();

        $download_enabled = (bool) $statics['download_enabled'];

        $reccomended_plugins = $this->getRecomendedPlugins(TRUE);

        $bootstrap = new \stdclass;
        $bootstrap->options = $options;
        $bootstrap->options_default = $this->getDefaultOptions();

        $tabs = $this->tabs;
        unset($tabs['docs']); // Documentation is platform-dependant, so not building into new, generic options app

        $bootstrap->tabs = array_values($tabs);
        $bootstrap->option_props = $this->options_list;
        $bootstrap->classes = new \stdclass;
        $bootstrap->classes->tabs = 'postbox tab-content';
        $bootstrap->statics = $statics; // Note, statics is an array, not a stdclass ...
        $bootstrap->statics['bibles'] = $this->getBiblesForDisplay();
        $bootstrap->statics['languages'] =  $this->reformatItemsList( $this->getLanguages() );
        $bootstrap->statics['interfaces'] = $this->getInterfaces(); 
        $bootstrap->configHttpHeaders = new \stdclass;
        $bootstrap->configHttpHeaders->{'X-WP-Nonce'} = wp_create_nonce( 'wp_rest' );
        $bootstrap->configUrl = esc_url_raw( rest_url() ) . 'biblesupersearch/v1/config';

        wp_enqueue_script('biblesupersearch_vue', plugins_url('../com_test/js/bin/vue_3.5.13.global.js', __FILE__));
        wp_enqueue_script('biblesupersearch_vuetify', plugins_url('../com_test/js/bin/vuetify_3.7.6.min.js', __FILE__));
        wp_enqueue_script('biblesupersearch_axios', plugins_url('../com_test/js/bin/axios_1.9.0.min.js', __FILE__));
        wp_enqueue_style('biblesupersearch_vuetify_css', plugins_url('../com_test/js/bin/vuetify_3.7.6.min.css', __FILE__));

        wp_localize_script( 'wp-api', 'wpApiSettings', array(
            'root' => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' )
        ) );
        
        // Pulling icons font locally isn't working, 
        // :todo see how I got this working on the API ... 
        // wp_enqueue_style('biblesupersearch_mdi_css', plugins_url('../com_test/js/bin/materialdesignicons_5.x.min.css', __FILE__));
        
        // Including JS / Styles via CDN apparently not allowed per WordPress plugin guidelines?
        // Leaving CDN links here for reference and testing new versions before adopting locally ... 
        // wp_enqueue_script('biblesupersearch_vue', 'https://unpkg.com/vue@3/dist/vue.global.js');
        // wp_enqueue_script('biblesupersearch_vuetify', 'https://cdn.jsdelivr.net/npm/vuetify@3.7.6/dist/vuetify.min.js');
        // wp_enqueue_script('biblesupersearch_axios', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js');
        // wp_enqueue_style('biblesupersearch_vuetify_css', 'https://cdn.jsdelivr.net/npm/vuetify@3.7.6/dist/vuetify.min.css');

        // Including fonts via CDN IS allowed per WordPress plugin guidelines
        wp_enqueue_style('biblesupersearch_mdi_css', 'https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css');

        wp_enqueue_script_module('biblesupersearch_vue_config', plugins_url('./Config.vue.js', __FILE__));

        // wp_localize_script( 'biblesupersearch_vue_config', 'wpApiSettings', array(
        //     'root' => esc_url_raw( rest_url() ),
        //     'nonce' => wp_create_nonce( 'wp_rest' )
        // ) );

        wp_enqueue_style('biblesupersearch_vue_config_css', plugins_url('../com_test/js/configs/assets/style.css', __FILE__));

        if ( ! isset( $_REQUEST['settings-updated'] ) ) {
            $_REQUEST['settings-updated'] = FALSE;
        }

        require( dirname(__FILE__) . '/template.options.new.php');
        return;
    }    

    public function displayPluginDocumentation() 
    {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        
        $options    = $this->getOptions();
        $using_main_api = (empty($options['apiUrl']) || $options['apiUrl'] == $this->default_options['apiUrl']) ? TRUE : FALSE;

        $statics = $this->getStatics();

        $download_enabled = (bool) $statics['download_enabled'];

        $reccomended_plugins = $this->getRecomendedPlugins(TRUE);
        
        wp_enqueue_style('biblesupersearch_docs_css', plugins_url('./options.css', __FILE__));
        require( dirname(__FILE__) . '/template.options.docs.php');
        return;
    }

    protected function fetchLandingPageOptions() 
    {
        global $wpdb;

        $sql = "
            SELECT ID, post_title, post_type, post_content FROM `{$wpdb->prefix}posts`
            WHERE ( post_content LIKE '%[biblesupersearch]%' OR post_content LIKE '%[biblesupersearch %]%' )
            AND post_type IN ('page','post') AND post_status = 'publish'
        ";

        $results = $wpdb->get_results($sql, ARRAY_A);
        $pages = [];

        foreach($results as $res) {
            if(!preg_match('/[^\[]\[biblesupersearch( .*)?]/', ' ' . $res['post_content'])) {
                continue; // Ignore example shortcodes ie [[biblesupersearch]]
            }

            $title = ($res['post_title']) ? $res['post_title'] : '(No Title, ID = ' . $res['ID'] . ')';
            $type = ucfirst($res['post_type']);

            $pages[] = [
                'value' => $res['ID'],
                'label' => $type . ': ' . $title,
            ];
        }

        return $pages;
    }

    // still in use (by widget)
    public function getLandingPageOptionsOld($render_html = FALSE, $value = NULL, $zero_option = 'None', $zero_default = FALSE) {
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

        $html = '';

        if($zero_option) {
            $sel  = (empty($value)) ? "selected = 'selected'" : '';

            if($zero_option == 'Default' || $zero_default) {
                $lp = $this->getLandingPage();
                $zero_option = '(' . $zero_option . ') ' . $lp['title_fmt'];
            }

            $html = "<option value='0' {$sel}> {$zero_option} </option>";
        }

        foreach($results as $res) {
            if(!preg_match('/[^\[]\[biblesupersearch( .*)?]/', ' ' . $res['post_content'])) {
                continue; // Ignore example shortcodes ie [[biblesupersearch]]
            }

            $this->_formatLandingPageOption($res);
            $sel  = ($res['ID'] == $value) ? "selected = 'selected'" : '';
            $html .= "<option value='{$res['ID']}' {$sel}>{$res['title_fmt']}</option>";
        }

        return $html;
    }

    // still in use
    protected function _formatLandingPageOption(&$landing_page) 
    {
        $title = ($landing_page['post_title']) ? $landing_page['post_title'] : '(No Title, ID = ' . $landing_page['ID'] . ')';
        $type = ucfirst($landing_page['post_type']);
        $landing_page['title_fmt'] = $type . ': ' . $title;
    }

    // still in use (by widget)
    public function hasLandingPageOptions() 
    {
        global $wpdb;

        $sql = "
            SELECT ID FROM `{$wpdb->prefix}posts`
            WHERE ( post_content LIKE '%[biblesupersearch]%' OR post_content LIKE '%[biblesupersearch %]%' )
            AND post_type IN ('page','post') AND post_status = 'publish'
            LIMIT 1
        ";

        $results = $wpdb->get_results($sql, ARRAY_A);
        return empty($results) ? FALSE : TRUE;
    }

    public function getLandingPage() {
        $options = $this->getOptions();

        if(!$options['defaultDestinationPage']) {
            return FALSE;
        }

        return $this->_getLandingPageHelper($options['defaultDestinationPage']);
    }
    
    public function getLandingPageById($id) {
        return $this->_getLandingPageHelper($id);
    }

    protected function _getLandingPageHelper($id) {
        global $wpdb;

        $sql = "
            SELECT * FROM `{$wpdb->prefix}posts`
            WHERE ID = {$id}
            AND post_type IN ('page','post') AND post_status = 'publish'
        ";

        $results = $wpdb->get_results($sql, ARRAY_A);

        if(!$results) {
            return FALSE;
        }

        $landing_page = $results[0];
        $this->_formatLandingPageOption($landing_page);
        return $landing_page;
    }

    // TODO - make generic
    protected function _setStaticsReset() {
        $statics               = get_option('biblesupersearch_statics');
        $last_update_timestamp = (is_array($statics) && array_key_exists('timestamp', $statics)) ? $statics['timestamp'] : 0;

        if($last_update_timestamp) {
            $statics['timestamp'] = 0;
            update_option('biblesupersearch_statics', $statics);
        }
    }

    protected function _afterFetchStatics($result) 
    {
        update_option('biblesupersearch_statics', $result['results']);
    }

    public function renderDownloadPage() 
    {

    }

    public function getLanguagesWithGlobalDefault()
    {
        $opts = self::$selector_options['language'];

        $pts = explode('_', get_locale());
        $lang = $pts[0] ?? 'en';
        $lang = strtolower($lang);

        $name = $this->getLanguageNameByCode($lang) ?? $lang;

        $opts['global_default'] = 'Site Language -- ' . $name . ' (Settings => General)';
        return $opts;
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
}

$BibleSuperSearch_Options = new BibleSuperSearch_Options_WP();
