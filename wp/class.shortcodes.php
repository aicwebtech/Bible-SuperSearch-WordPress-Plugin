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
            'interface' => NULL,
        ), $atts );

        $map = array(
            'interface' => 'interface'
        );

        foreach($map as $att_key => $opt_key) {
            if(!empty($a[$att_key])) {
                $options[$opt_key] = $a[$att_key];
            }
        }

        if(static::$instances > 0) {
            return '<div>Error: You can only have one [biblesupersearch] shortcode per page.</div>';
        }
        
        $options_json   = json_encode($options);

        $html  = '';

        if(!empty($options['extraCss'])) {
            $html .= '<style>' . $options['extraCss'] . '</style>';
        }

        $html .= "<script>\n";
        $html .= "var biblesupersearch_config_options = {$options_json};\n";
        // Dynamically generate link to biblesupersearch_root_dir
        // No longer needed?, but retaining for now.
        // $bss_dir        = plugins_url('app', dirname(__FILE__));
        // $html .= "var biblesupersearch_root_directory = '{$bss_dir}';\n";
        $html .= "</script>\n";
        $html .= "<div id='biblesupersearch_container' style='idth: 800px; ax-height: 400px'>\n";
        $html .= "    <noscript class='biblesupersearch_noscript'>Please enable JavaScript to use</noscript>\n";
        $html .= "</div>\n";

        static::$instances ++;
        return $html;
    }    

    static public function demo($atts) {
        global $BibleSuperSearch_Options;
        $interfaces     = $BibleSuperSearch_Options->getInterfaces();
        $options        = $BibleSuperSearch_Options->getOptions();
        $sel_interface  = !empty($_REQUEST['biblesupersearch_interface']) ? $_REQUEST['biblesupersearch_interface'] : $options['interface'];
        $sel_interface  = isset($interfaces[$sel_interface]) ? $sel_interface : $options['interface'];

        $a = shortcode_atts( array(
            'sel_color' => NULL,
            'selector_height' => '200px',
        ), $atts );

        if(static::$instances > 0) {
            return '<div>Error: You can only have one [biblesupersearch] shortcode per page.  (Demo shortcode will create another).</div>';
        }

        $html = '';
        $html .= "<div style='height: {$a['selector_height']}; overflow-y:auto'>";
        $html .= "<table><tr><th>Name</th><th>ID</th><th>Select</th></tr>";
        // $html .= "<tbody style='height: {$a['selector_height']}; overflow-y:auto'>";

        foreach($interfaces as $id => $int) {
            $selected = ($id == $sel_interface) ? TRUE : FALSE;
            $display  = ($selected) ? 'Showing' : 'Show';
            $style    = ($selected && $a['sel_color']) ? "style='background-color:{$a['sel_color']}'" : '';
            $disabled = ($selected) ? "disabled='disabled'" : '';

            $html .= "<tr {$style}><td>{$int['name']}</td><td>{$id}</td><td style='text-align: center'><form>"; 
            $html .= "<input type='hidden' name='biblesupersearch_interface' value='{$id}' />";
            $html .= "<input type='submit' value='{$display}' style='width: 70%' {$disabled} /></form></td></tr>";
        }

        // $html .= "</tbody></table>";
        $html .= "</table></div>";
        $html .= "<br /><h3>Displaying: {$interfaces[$sel_interface]['name']}</h3><br />";
        $html .= do_shortcode("[biblesupersearch interface='{$sel_interface}']");

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
add_shortcode('biblesupersearch_demo', array('BibleSuperSearch_Shortcodes', 'demo'));
add_shortcode('biblesupersearch_bible_list', array('BibleSuperSearch_Shortcodes', 'bibleList'));

