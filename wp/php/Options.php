<?php

namespace BibleSuperSearch\WordPress;

use BibleSuperSearch\Common\OptionsAbstract;

defined('ABSPATH') or die; // exit if accessed directly

class Options extends OptionsAbstract 
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
    }

    /** WordPress-specific methods */
    function adminInit() 
    {
        global $wp_version;

        $args = [$this, 'validateOptions'];

        if ( version_compare( $wp_version, '4.7.0', '>=' ) ) {
            $args = [
                'sanitize_callback' => [$this, 'validateOptions']
            ];
        }

        register_setting( 'aicwebtech_plugin_options', $this->option_index, $args );
    }

    /** WordPress-specific methods */
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

    protected function storeOptions($options) 
    {
        update_option( $this->option_index, $options );
    }

    protected function fetchOptions() 
    {
        return get_option( $this->option_index );
    }

    /** WordPress-specific override */
    protected function fatalError($msg)
    {
        wp_die( $msg );
    }

    /** Custom Override for WordPress */
    public function setDefaultOptions() 
    {
        if ( ! is_array( get_option( $this->option_index ) ) ) {
            delete_option( $this->option_index ); // just in case
            update_option( $this->option_index, $this->default_options );
        }

        // Flush rewrite cache
        // flush_rewrite_rules( true );
    }

    protected function fetchStaticsCache() 
    {
        return get_option('biblesupersearch_statics');
    }

    protected function storeStaticsCache($statics) 
    {
        update_option('biblesupersearch_statics', $statics);
    }

    /** WordPress-specific methods */
    public function getRecomendedPlugins($missing_only = FALSE) 
    {
        $plugins = [
            [
                'name'          => 'disable-emojis',
                'file'          => 'disable-emojis/disable-emojis.php',
                'label'         => 'Disable Emojis',
                'description'   => ' - WordPress converts some characters to emojis, and this may cause Bible SuperSearch to not look as intended',
            ]
        ];

        if($missing_only) {
            foreach($plugins as $key => $plugin) {
                if(is_plugin_active($plugin['file'])) {
                    unset($plugins[$key]);
                }
            }
        }

        return $plugins;
    }

    /** WordPress-specific methods */
    public function displayPluginOptionsNew() 
    {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        // biblesupersearch_enqueue_option(); // not needed for Vue
        $options    = $this->getOptions();
        $interfaces = $this->getInterfaces(); 

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
        $bootstrap->usingMainApi = $using_main_api;

        wp_enqueue_script('biblesupersearch_vue', plugins_url('com_test/js/bin/vue_3.5.13.global.js', dirname(__FILE__, 2)));
        wp_enqueue_script('biblesupersearch_vuetify', plugins_url('com_test/js/bin/vuetify_3.7.6.min.js', dirname(__FILE__, 2)));
        wp_enqueue_script('biblesupersearch_axios', plugins_url('com_test/js/bin/axios_1.9.0.min.js', dirname(__FILE__, 2)));
        wp_enqueue_style('biblesupersearch_vuetify_css', plugins_url('com_test/js/bin/vuetify_3.7.6.min.css', dirname(__FILE__, 2)));

        wp_localize_script( 'wp-api', 'wpApiSettings', [
            'root' => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' )
        ] );
        
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

        $vue_config_src = plugins_url('wp/js/Config.vue.js', dirname(__FILE__, 2));
        
        if ( function_exists( 'wp_enqueue_script_module' ) ) {
            wp_enqueue_script_module('biblesupersearch_vue_config', $vue_config_src);
        } else {
            wp_enqueue_script('biblesupersearch_vue_config', $vue_config_src, [], null, true);
            wp_script_add_data('biblesupersearch_vue_config', 'type', 'module');
        }


        // wp_localize_script( 'biblesupersearch_vue_config', 'wpApiSettings', array(
        //     'root' => esc_url_raw( rest_url() ),
        //     'nonce' => wp_create_nonce( 'wp_rest' )
        // ) );

        wp_enqueue_style('biblesupersearch_vue_config_css', plugins_url('com_test/js/configs/assets/style.css', dirname(__FILE__, 2)));

        if ( ! isset( $_REQUEST['settings-updated'] ) ) {
            $_REQUEST['settings-updated'] = FALSE;
        }

        require(dirname(__FILE__) . '/../templates/template.options.new.php');
        return;
    }    
    
    /** WordPress-specific methods */
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
        
        wp_enqueue_style('biblesupersearch_docs_css', plugins_url('wp/css/options.css', dirname(__FILE__, 2)));
        require(dirname(__FILE__) . '/../templates/template.options.docs.php');
        return;
    }
    
    //** wordpress specific override */
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

    // TODO - make generic
    protected function _setStaticsReset() 
    {
        $statics               = get_option('biblesupersearch_statics');
        $last_update_timestamp = (is_array($statics) && array_key_exists('timestamp', $statics)) ? $statics['timestamp'] : 0;

        if($last_update_timestamp) {
            $statics['timestamp'] = 0;
            update_option('biblesupersearch_statics', $statics);
        }
    }
    
    //** WordPress specific override? */
    public function getLanguagesWithGlobalDefault()
    {
        $opts = $this->selector_options['language'];

        $pts = explode('_', get_locale());
        $lang = $pts[0] ?? 'en';
        $lang = strtolower($lang);

        $name = $this->getLanguageNameByCode($lang) ?? $lang;

        $opts['global_default'] = 'Site Language -- ' . $name . ' (Settings => General)';
        return $opts;
    }
}
