<?php

/*
    Plugin Name: Bible SuperSearch
    Plugin URI:  https://biblesupersearch.com/downloads/
    Description: Add powerful Bible tools to your website, including a Bible search engine with selectable interfaces that allow it to appear as simple or complex as desired, and a Bible download page.  Includes option to install our API on your server and run completely on your website.
    Version:     4.2.5
    Author:      Bible SuperSearch
    Author URI:  https://www.biblesupersearch.com
    License:     GPLv3 or later
    License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

require_once(dirname(__FILE__) . '/wp/class.options.php');
require_once(dirname(__FILE__) . '/wp/class.widgets.php');
require_once(dirname(__FILE__) . '/wp/class.shortcodes.php');

// wp_enqueue_media(); // does not work

function biblesupersearch_enqueue_depends($includeCssOverride = TRUE) {
    // Quick workaround for new Gutenberg editor.
    // When editing a page with a Bible SuperSearch shortcode, it is loading these includes without rendering the shortcode
    // This is causing Bible SuperSearch to throw errors.
    // Quick fix: block these incudes from being loaded from any admin page

    if(is_admin()) {
        return;
    }

    wp_enqueue_script('biblesupersearch_main', plugins_url('app/biblesupersearch.js', __FILE__));
    wp_enqueue_script('biblesupersearch_wp_add', plugins_url('wp/additional.js', __FILE__));
    wp_enqueue_style('biblesupersearch_css',   plugins_url('app/biblesupersearch.css', __FILE__));
    
    if($includeCssOverride) {
        wp_enqueue_style('biblesupersearch_css_wp',   plugins_url('wp/additional.css', __FILE__));
    }
}

function biblesupersearch_enqueue_option() {
    wp_enqueue_script('biblesupersearch_options',  plugins_url('wp/options.js', __FILE__));
    wp_enqueue_style('biblesupersearch_options',   plugins_url('wp/options.css', __FILE__));
}

function biblesupersearch_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=biblesupersearch">' . __( 'Settings' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'biblesupersearch_add_settings_link' );
