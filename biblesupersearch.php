<?php

/*
Plugin Name: Bible SuperSearch
Plugin URI:  https://biblesupersearch.com/
Description: Official Bible SuperSearch Wordpress plugin.  Free to use for NON COMMERCIAL purposes.
Version:     0.0.1
Author:      Bible SuperSearch / AIC Web Tech
Author URI:  https://biblesupersearch.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

require_once(dirname(__FILE__) . '/options.php');

function biblesupersearch_display($atts) {
    biblesupersearch_enqueue_depends();
    $options = get_option('biblesupersearch_options');
    $options_json = json_encode($options);

    $a = shortcode_atts( array(
        'container' => 'biblesupersearch_container',
        'bar' => 'something else',
    ), $atts );

    $html  = '';
    $html .= "<script> var biblesupersearch_config_options = {$options_json}; </script>";
    $html .= "BSS<div id='biblesupersearch_container' style='width: 800px; height: 400px'></div>";

    return $html;
}

add_shortcode('biblesupersearch', 'biblesupersearch_display');

function biblesupersearch_bible_list($atts) {
    biblesupersearch_enqueue_depends();
    $statics = get_option('biblesupersearch_statics');

    $a = shortcode_atts( array(
        'verbose' => FALSE,
    ), $atts );

    $html = '<table>';
    $html .= '<tr>';
    $html .= '<th>Module</th>';
    $html .= '<th>Language</th>';
    $html .= '<th>Name</th>';
    $html .= '<th>Short Name</th>';
    $html .= '</tr>';

    foreach($statics['bibles'] as $module => $bible) {
        $html .= "<tr>";
        $html .= "<td>{$module}</td>";
        $html .= "<td>{$bible['lang']}</td>";
        $html .= "<td>{$bible['name']}</td>";
        $html .= "<td>{$bible['shortname']}</td>";
        $html .= "</tr>";   
    }

    $html .= '</table>';

    return $html;
}

add_shortcode('biblesupersearch_bible_list', 'biblesupersearch_bible_list');

function biblesupersearch_enqueue_depends() {
    wp_enqueue_script('biblesupresearch_wp',   plugins_url('biblesupersearch.wordpress.js', __FILE__));
    wp_enqueue_script('biblesupresearch_main', plugins_url('biblesupersearch.js', __FILE__));
    wp_enqueue_style('biblesupresearch_css',   plugins_url('biblesupersearch.css', __FILE__));
}

function biblesupersearch_enqueue_option() {
    wp_enqueue_script('biblesupresearch_wp',   plugins_url('biblesupersearch.options.js', __FILE__));
}

add_action('init', 'biblesupersearchStartSession', 1);

function biblesupersearchStartSession() {
    if(!session_id()) {
        session_start();
    }
}
