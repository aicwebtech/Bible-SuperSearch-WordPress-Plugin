<?php

/*
    Plugin Name: Bible SuperSearch
    Plugin URI:  https://biblesupersearch.com/downloads/
    Description: Bible tool having multiple versions, keyword search, reference retrival, Bible downloader, and more.  Keeps your visitors on your website!
    Version:     5.6.24
    Author:      Bible SuperSearch
    Author URI:  https://www.biblesupersearch.com
    License:     GPLv3 or later
    License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

// Powerful Bible search and reference look up tool, along with a Bible downloader.  Keeps your visitors on your website!
// Search and look up references in multiple Bible versions
// Add the Bible to your website with multiple versions, keyword search, reference retrival, and Bible downloader.  Keeps your visitors on your website!
// Bible tool having multiple versions, keyword search, reference retrival, Bible downloader, and more.  Keeps your visitors on your website!


// com_test is a temp dir name ...

require_once(dirname(__FILE__) . '/com_test/php/init.php');
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

    wp_enqueue_script('biblesupersearch_main', plugins_url('com_test/js/app/biblesupersearch.js', __FILE__));
    wp_enqueue_script('biblesupersearch_wp_add', plugins_url('wp/additional.js', __FILE__));
    wp_enqueue_style('biblesupersearch_css',   plugins_url('com_test/js/app/biblesupersearch.css', __FILE__));    
    wp_enqueue_style('biblesupersearch_css_wp',   plugins_url('wp/style.css', __FILE__));    
    // wp_enqueue_script('biblesupersearch_main', plugins_url('app/biblesupersearch.js', __FILE__));
    // wp_enqueue_script('biblesupersearch_wp_add', plugins_url('wp/additional.js', __FILE__));
    // wp_enqueue_style('biblesupersearch_css',   plugins_url('app/biblesupersearch.css', __FILE__));
    
    if($includeCssOverride) {
        wp_enqueue_style('biblesupersearch_css_wp_add',   plugins_url('wp/additional.css', __FILE__));
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

//add_action( 'admin_menu', 'biblesupersearch_options_page' );

function biblesupersearch_options_page() {
    add_menu_page(
        'Bible SuperSearch',
        'Bible SuperSearch',
        'manage_options',
        'biblesupersearch',
        '', //'wporg_options_page_html',
        plugin_dir_url(__FILE__) . 'images/icon_wporg.png',
        20
    );
}

// Note this currently causes breakage on some hosts ($BibleSuperSearch_Options is null)
// disabled for now
function biblesupersearch_custom_rewrite() {
    global $BibleSuperSearch_Options;

    if(!$BibleSuperSearch_Options) {
        $BibleSuperSearch_Options = new BibleSuperSearch_Options_WP();
    }

    $landing_page = $BibleSuperSearch_Options->getLandingPage();

    if(!$landing_page) {
        return;
    }

    // var_dump($landing_page);
    // die();

    // $landing_page_link = 'bible';
    // $landing_page_id = 11;

    // todo - need to get WHOLE landing page link (relative to domain)
    // this only works for top level pages / posts

    $landing_page_link = $landing_page['post_name'];
    $landing_page_id = (int) $landing_page['ID'];
    $landing_page_url = get_permalink($landing_page_id);
    $url_parsed = parse_url($landing_page_url);
    $landing_page_link = $url_parsed['path'];

    // die($landing_page_link);

    add_rewrite_tag('%bible_query%', '([^&]+)');
    add_rewrite_rule('^' . $landing_page_link  . '/(.*)/?','index.php?page_id=' . $landing_page_id . '&bible_query=$matches[1]','top');
}


//add_action('init', 'biblesupersearch_custom_rewrite', 10, 0);
