<?php

defined( 'ABSPATH' ) or die; // exit if accessed directly


class BibleSuperSearch_Shortcodes {
    static protected $instances = 0;
    
    static public function display($atts) {
        global $BibleSuperSearch_Options;
        $options        = $BibleSuperSearch_Options->getOptions();
        biblesupersearch_enqueue_depends($options['overrideCss']);

        $a = shortcode_atts( array(
            'container' => 'biblesupersearch_container',
            'bar'       => 'something else',
        ), $atts );

        if(static::$instances > 0) {
            return '<div>Error: You can only have one [biblesupersearch] shortcode per page.</div>';
        }
        
        $bss_dir        = plugins_url('app', dirname(__FILE__));
        $options_json   = json_encode($options);

        $html  = '';

        if(!empty($options['extraCss'])) {
            $html .= '<style>' . $options['extraCss'] . '</style>';
        }

        $html .= "<script>\n";
        $html .= "var biblesupersearch_config_options = {$options_json};\n";
        $html .= "var biblesupersearch_root_directory = '{$bss_dir}';\n";
        $html .= "</script>\n";
        $html .= "<div id='biblesupersearch_container' style='idth: 800px; ax-height: 400px'></div>\n";

        static::$instances ++;
        return $html;
    }

    // Lists all Bibles available
    // (Not just ones enabled)
    static public function bibleList($atts) {
        global $BibleSuperSearch_Options;
        $statics = $BibleSuperSearch_Options->getStatics();

        $a = shortcode_atts( array(
            'verbose' => FALSE,
        ), $atts );

        $html = '<table>';
        $html .= '<tr>';
        $html .= '<th>Module</th>';
        $html .= '<th>Language</th>';
        $html .= '<th>Name</th>';
        $html .= '<th>Short Name</th>';

        if($a['verbose']) {  
            $html .= '<th>Year</th>';
            $html .= '<th>Copyrighted</th>';
        }

        $html .= '</tr>';

        foreach($statics['bibles'] as $module => $bible) {
            $html .= "<tr>";
            $html .= "<td>{$module}</td>";
            $html .= "<td>{$bible['lang']}</td>";
            $html .= "<td>{$bible['name']}</td>";
            $html .= "<td>{$bible['shortname']}</td>";

            if($a['verbose']) {  
                $html .= "<td>{$bible['year']}</td>";
                $html .= "<td>" . (($bible['copyright']) ? 'Yes' : 'No') . "</td>";
            }
            
            $html .= "</tr>";   
        }

        $html .= '</table>';

        return $html;
    }

}

add_shortcode('biblesupersearch', array('BibleSuperSearch_Shortcodes', 'display'));
add_shortcode('biblesupersearch_bible_list', array('BibleSuperSearch_Shortcodes', 'bibleList'));

