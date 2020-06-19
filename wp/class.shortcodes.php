<?php

defined( 'ABSPATH' ) or die; // exit if accessed directly


class BibleSuperSearch_Shortcodes {
    static protected $instances = 0;

    static public $displayAttributes = array(
        'interface' => array(
            'name'      => 'Skin',
            'desc'      => 'Name of the skin to be used, see list below',
            'map'       => 'interface',
            'default'   => NULL,
        ),
        'destination_url' => array(
            'name'      => 'Destination URL',
            'desc'      => 'URL to page or post where form will redirect to when submitted.<br />
                            The destionation will need to have the <cod>[biblesupersearch]</code> shortcode.<br />
                            Set to \'\' to force to current page and override \'Default Destination Page\'',
            'map'       => 'destinationUrl',
            'default'   => NULL,
        ),
    );    

    static public $downloadAttributes = array(
        'verbose' => array(
            'name'      => 'Verbose',
            'desc'      => 'Displays all Bibles, even if they are not downloadable. (true/false)',
            'map'       => 'verbose',
            'default'   => NULL,
        ),
    );    

    static public $biblesAttributes = array(
        'verbose' => array(
            'name'      => 'Verbose',
            'desc'      => 'Displays some extra columns on the list of Bibles (true/false)',
            'map'       => 'verbose',
            'default'   => NULL,
        ),
    );
    
    static public function display($atts) {
        global $BibleSuperSearch_Options;
        $options        = $BibleSuperSearch_Options->getOptions();
        $statics        = $BibleSuperSearch_Options->getStatics();
        biblesupersearch_enqueue_depends($options['overrideCss']);
        $container = 'biblesupersearch_container';
        $attr = static::$displayAttributes;

        $first_instance = static::$instances == 0 ? TRUE : FALSE;

        unset($options['formStyles']); // Not using this config right now

        $destination_url = NULL;

        if(isset($options['defaultDestinationPage'])) {
            $destination_url = get_permalink($options['defaultDestinationPage']);
            $attr['destination_url']['default'] = $destination_url;
        }

        if(static::$instances > 0) {
            $container .= '_' . static::$instances;
        }        

        if(static::$instances > 0) {
            return '<div>Error: You can only have one [biblesupersearch] shortcode per page.</div>';
        }
        
        $defaults = array(
            'container' => $container,
            'contact-form-7-id' => NULL,
        );

        foreach($attr as $key => $info) {
            $defaults[$key] = $info['default'];
        }
        
        $a = shortcode_atts($defaults, $atts);
        static::_validateAttributes($a);
        $a['contact-form-7-id'] = (int) $a['contact-form-7-id'];

        if($a['interface']) {
            $interface = $BibleSuperSearch_Options->getInterfaceByName($a['interface']);

            if(!$interface) {
                return '<div>Error: Interface does not exist: ' . $a['interface'] . '</div>';
            }

            $a['interface'] = $interface['id'];
        }

        foreach($attr as $att_key => $info) {
            if(!empty($a[$att_key])) {
                $options[ $info['map'] ] = $a[$att_key];
            }
        }
        
        $options_json   = json_encode($options);
        $statics_json   = json_encode($statics);

        $html  = '';

        if(!empty($options['extraCss'])) {
            $html .= '<style>' . $options['extraCss'] . '</style>';
        }

        $html .= "<script>\n";
        
        if($first_instance) {
            $html .= "var biblesupersearch_config_options = {$options_json};\n";
            $html .= "var biblesupersearch_statics = {$statics_json};\n";

            // Dynamically generate link to biblesupersearch_root_dir
            // Confirmed needed (by WordPress.com websites)
            $bss_dir        = plugins_url('app', dirname(__FILE__));
            $html .= "var biblesupersearch_root_directory = '{$bss_dir}';\n";
            $html .= "var biblesupersearch_instances = {" . $container . ": " . $options_json . "};\n";
        }
        else {
            // $html .= "biblesupersearch_instances.{$container} = {$options_json};\n";
        }

        // $html .= "var biblesupersearch_cf7id = {$a['contact-form-7-id']};\n";

        $html .= "</script>\n";

        if($a['contact-form-7-id']) {
            $html .= static::_displayContactForm7($a);
        }

        $html .= "<div id='{$a['container']}' class='wp-exclude-emoji'>\n";
        $html .= "    <noscript class='biblesupersearch_noscript'>Please enable JavaScript to use</noscript>\n";
        $html .= "</div>\n";

        static::$instances ++;
        return $html;
    }    

    static protected function _displayContactForm7($atts) {
        $html = 'CF7 ';

        $html .= do_shortcode('[contact-form-7 id="' . $atts['contact-form-7-id'] . '" title="Test Bible Form" do_not_store="true"]');

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

        // static::_validateAttributes($a);

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
    // (Not just ones enabled in the plugin)
    static public function bibleList($atts) {
        global $BibleSuperSearch_Options;
        // $statics = $BibleSuperSearch_Options->getStatics();
        $bibles  = $BibleSuperSearch_Options->getEnabledBibles();

        $a = shortcode_atts( array(
            'verbose' => FALSE,
        ), $atts );

        static::_validateAttributes($a, array('verbose'));

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

        foreach($bibles as $module => $bible) {
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

    static public function downloadPage($atts) {
        global $BibleSuperSearch_Options;
        $statics = $BibleSuperSearch_Options->getStatics();

        $a = shortcode_atts( array(
            'verbose' => FALSE,
        ), $atts );

        static::_validateAttributes($a, array('verbose'));

        if(!array_key_exists('download_enabled', $statics) || !$statics['download_enabled']) {
            return 'The download feature is not available from the selected Bible SuperSearch API server.';
        }

        wp_enqueue_script('biblesupersearch_download_js', plugins_url('download/download.js', __FILE__));
        wp_enqueue_style('biblesupersearch_download_css',   plugins_url('download/download.css', __FILE__));

        $BibleSuperSearchDownloadFormats = $statics['download_formats'];
        // $BibleSuperSearchBibles          = $statics['bibles'];
        $BibleSuperSearchBibles          = $BibleSuperSearch_Options->getEnabledBibles($statics);
        $BibleSuperSearchAPIURL          = $BibleSuperSearch_Options->getUrl();
        $BibleSuperSearchDownloadVerbose = $a['verbose'];

        ob_start();
        include(dirname(__FILE__) . '/download/download.php');
        $html = ob_get_clean();
        return $html;
    }

    static protected function _validateAttributes(&$attr, $bool = array()) {
        foreach($bool as $idx) {
            $attr[$idx] = (array_key_exists($idx, $attr) && $attr[$idx] && $attr[$idx] != 'false') ? TRUE : FALSE;
        }
    }

}

add_shortcode('biblesupersearch', array('BibleSuperSearch_Shortcodes', 'display'));
add_shortcode('biblesupersearch_demo', array('BibleSuperSearch_Shortcodes', 'demo'));
add_shortcode('biblesupersearch_bible_list', array('BibleSuperSearch_Shortcodes', 'bibleList'));
add_shortcode('biblesupersearch_downloads', array('BibleSuperSearch_Shortcodes', 'downloadPage'));

