<?php

/*
    Plugin Name: Bible SuperSearch
    Plugin URI:  https://biblesupersearch.com/
    Description: Official Bible SuperSearch Wordpress plugin.  Free to use for NON-COMMERCIAL purposes. 
    Version:     0.0.1
    Author:      Bible SuperSearch
    Author URI:  https://biblesupersearch.com/
    License:     GPL2
    License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

require_once(dirname(__FILE__) . '/wp/class.options.php');
require_once(dirname(__FILE__) . '/wp/class.widgets.php');
require_once(dirname(__FILE__) . '/wp/class.shortcodes.php');

function biblesupersearch_enqueue_depends($includeCssOverride = TRUE) {
    wp_enqueue_script('biblesupersearch_main', plugins_url('app/biblesupersearch.js', __FILE__));
    wp_enqueue_style('biblesupersearch_css',   plugins_url('app/biblesupersearch.css', __FILE__));
    
    if($includeCssOverride) {
        wp_enqueue_style('biblesupersearch_css_wp',   plugins_url('wp/additional.css', __FILE__));
    }
}

function biblesupersearch_enqueue_option() {
    wp_enqueue_script('biblesupersearch_options',  plugins_url('wp/options.js', __FILE__));
    wp_enqueue_style('biblesupersearch_options',   plugins_url('wp/options.css', __FILE__));
}

add_action('init', 'biblesupersearchStartSession', 1);

function biblesupersearchStartSession() {
    if(!session_id()) {
        session_start();
    }
}

function biblesupersearch_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=biblesupersearch">' . __( 'Settings' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'biblesupersearch_add_settings_link' );
