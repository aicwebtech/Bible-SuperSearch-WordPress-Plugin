<?php

defined( 'ABSPATH' ) or die; // exit if accessed directly

        // * formatButtons - Which formatting buttons to use?  Options: default, Classic or Stylable
        // * navigationButtons - Which navigation buttons to use?  Options: default, Classic or Stylable
        // * pager - Which pager to use? Options: default, Classic, Clean

class BibleSuperSearch_Shortcodes {
    static protected $instances = 0;

    static public $displayAttributes = array(
        // Attributes must be in underscore_case
        'interface' => array(
            'name'      => 'Skin',
            'desc'      => "Name or ID of the skin to be used, &nbsp; To see and preview the complete list of skins, please visit<br />
                                <a href='https://www.biblesupersearch.com/client/' target='_NEW'>https://www.biblesupersearch.com/client/</a><br /><br />
                                Example: [biblesupersearch interface='Classic - Parallel 2']",
            'map'       => 'interface',
            'default'   => NULL,
        ),
        'destination_url' => array(
            'name'      => 'Destination URL',
            'desc'      => 'URL to page or post where form will redirect to when submitted.<br />
                            The destionation will need to have the <cod>[biblesupersearch]</code> shortcode.<br />
                            Set to \'\' to force to current page and override \'Default Destination Page\' <br /><br />
                            Example: [biblesupersearch destination_url=\'https://example.com/bible\']',
            'map'       => 'destinationUrl',
            'default'   => NULL,
        ),        
        'landing_passage' => array(
            'name'      => 'Landing Passage(s)',
            'desc'      => 'When app is first loaded, these reference(s) will automatically be retrieved. <br />
                            Takes any valid Bible reference, ie \'John 3:16; Romans 3:23; Genesis 1\' <br /><br />
                            Example: [biblesupersearch landing_passage=\'Genesis 1\']',
            'map'       => 'landingReference',
            'default'   => NULL,
        ),             
        'format_buttons' => array(
            'name'      => 'Which formatting buttons to use?',
            'desc'      => 'Options: default, Classic or Stylable <br />
                            (Default selects the defalt for the selected skin) <br /><br />
                            Example: [biblesupersearch format_buttons=\'Stylable\']',
            'map'       => 'formatButtons',
            'default'   => NULL,
        ),        
        'navigation_buttons' => array(
            'name'      => 'Which navigation buttons to use?',
            'desc'      => 'Options: default, Classic or Stylable <br />
                            (Default selects the defalt for the selected skin) <br /><br />
                            Example: [biblesupersearch navigation_buttons=\'Stylable\']',
            'map'       => 'navigationButtons',
            'default'   => NULL,
        ),        
        'pager' => array(
            'name'      => 'Which pager to use?',
            'desc'      => 'Options: default, Classic or Clean <br />
                            (Default selects the defalt for the selected skin) <br /><br />
                            Example: [biblesupersearch pager=\'Clean\']',
            'map'       => 'pager',
            'default'   => NULL,
        ),        
        'suppress_instance_error' => array(
            'name'      => 'Suppress Instance Error',
            'desc'      => 'The framework that Bible SuperSearch uses won\'t allow multiple instances of it to exist on a page.<br />
                            However, some 3rd party plugins will duplicate the shortcode, resulting in an error message and no Bible Search.<br />
                            This config turns off the error message and attempts to display the Bible search anyway.<br />
                            Note: The Bible search will still only display once on the page.<br /><br />
                            [biblesupersearch suppress_instance_error=\'true\']',
            'map'       => 'suppress_instance_error',
            'default'   => FALSE,
        ),
    );    

    static public $downloadAttributes = array(
        'verbose' => array(
            'name'      => 'Verbose',
            'desc'      => 'Displays all Bibles, even if they are not downloadable. (true/false)<br /><br />
                            Example: [biblesupersearch_downloads verbose=\'false\']',
            'map'       => 'verbose',
            'default'   => NULL,
        ),
    );    

    static public $biblesAttributes = array(
        'verbose' => array(
            'name'      => 'Verbose',
            'desc'      => 'Displays some extra columns on the list of Bibles (true/false)<br /><br />
                            Example: [biblesupersearch_bible_list verbose=\'true\']',
            'map'       => 'verbose',
            'default'   => NULL,
        ),
    );
    
    static public function display($atts, $thing, $it) {
        global $BibleSuperSearch_Options, $wp_query;
        $options        = $BibleSuperSearch_Options->getOptions();
        $statics        = $BibleSuperSearch_Options->getStatics();
        biblesupersearch_enqueue_depends($options['overrideCss']);
        $container = 'biblesupersearch_container';
        $attr = static::$displayAttributes;

        $query_vars = array_key_exists('biblesupersearch', $_REQUEST) ? $_REQUEST['biblesupersearch'] : [];

        // Beginning of shareable, SEO-friendly linkage
        // $query_string = array_key_exists('bible_query', $wp_query->query_vars) ? $wp_query->query_vars['bible_query'] : '';
        // $options['query_string'] = $query_string;

        $first_instance = static::$instances == 0 ? TRUE : FALSE;

        unset($options['formStyles']); // Not using this config right now

        $destination_url = NULL;

        if(isset($options['defaultDestinationPage'])) {
            $destination_url = get_permalink($options['defaultDestinationPage']);
            $attr['destination_url']['default'] = $destination_url;
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

        $options['target'] = $a['container'];
        
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

        if(array_key_exists('destinationUrl', $options) && $options['destinationUrl'] == get_permalink()) {
            $options['destinationUrl'] = NULL;
        }
        
        $options_json   = json_encode($options);
        $statics_json   = json_encode($statics);

        $html  = '';

        // $html .= '<p>Container: ' . $container . '</p>';

        if(!empty($options['extraCss'])) {
            $html .= '<style>' . $options['extraCss'] . '</style>';
        }

        $html .= "<script>\n";
        
        // if($first_instance) {
            $html .= "var biblesupersearch_config_options = {$options_json};\n";
            $html .= "var biblesupersearch_statics = {$statics_json};\n";

            // Dynamically generate link to biblesupersearch_root_dir
            // Confirmed needed (by WordPress.com websites)
            // $bss_dir        = plugins_url('app', dirname(__FILE__));
            $bss_dir        = plugins_url('com_test/js/app', dirname(__FILE__));
            $html .= "var biblesupersearch_root_directory = '{$bss_dir}';\n";
            $html .= "var biblesupersearch_instances = {" . $container . ": " . $options_json . "};\n";

            if(!empty($query_vars)) {
                $query_vars['redirected'] = TRUE;
                $query_vars_json = json_encode($query_vars);
                $html .= "var biblesupersearch_form_data = {$query_vars_json};\n";
            }
        // }

        // $html .= "var biblesupersearch_cf7id = {$a['contact-form-7-id']};\n";

        $html .= "</script>\n";

        // Experimental: Using a Contact Form 7 form for the Bible search form.
        // if($a['contact-form-7-id']) {
        //     $html .= static::_displayContactForm7($a);
        // }

        $html .= "<div id='{$a['container']}' class='wp-exclude-emoji'>\n";
        
        if(!$a['suppress_instance_error']) {
            // Limitations of the Enyo app don't allow it to be rendered more than once on a page
            $html .= "   <!--\n";
            $html .= '       NOTE: You can only have one [biblesupersearch] shortcode per page.' . "\n";
            $html .= '       If you are seeing this HTML comment (and are not using CTRL-U view source),' . "\n";
            $html .= '       you may have multiple [biblesupersearch] shortcodes on this page.' . "\n";
            $html .= '       If you are using a SEO plugin, please make sure it isn\'t duplicating the shortcode.' . "\n";
            $html .= '       If you believe you have received this message in error, you may attempt to suppress it and attempt to display the Bible search anyway, ' . "\n";
            $html .= '       by setting suppress_instance_error=\'true\' on the shortcode.  (However, this is not a guaranteed fix.)' . "\n";
            $html .= '   -->' . "\n";
        }

        $html .= "    <noscript class='biblesupersearch_noscript'>Please enable JavaScript to use</noscript>\n";
        $html .= "</div>\n";

        static::$instances ++;
        return $html;
    }    

    static public function getDisplayAttributes()
    {
        global $BibleSuperSearch_Options, $interfaces;

        if(!isset($interfaces) || empty($interfaces)) {
            $interfaces = $BibleSuperSearch_Options->getInterfaces(); 
        } 

        $attr = self::$displayAttributes;

        $attr['interface']['desc'] = "Name or ID of the skin to be used, &nbsp; To preview the complete list of skins, please visit<br />
                                <a href='https://www.biblesupersearch.com/screenshots/#interfaces' target='_NEW'>https://www.biblesupersearch.com/screenshots/#interfaces</a><br />
                                <a href='https://www.biblesupersearch.com/client/' target='_NEW'>https://www.biblesupersearch.com/client/</a><br /><br />
                                <b>How to use this shortcode property for each interface option:</b><br /><br />";

        foreach($interfaces as $key => $int) {
            $attr['interface']['desc'] .= $int['name'] . ':<br />[biblesupersearch interface=\'' . $key . '\']<br /><br />';
        }

        return $attr;
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

        $BibleSuperSearchDownloadFormats = $statics['download_formats'];
        // $BibleSuperSearchBibles          = $statics['bibles'];
        $BibleSuperSearchBibles          = $BibleSuperSearch_Options->getEnabledBibles($statics, 'name', 'language_english');
        $BibleSuperSearchAPIURL          = $BibleSuperSearch_Options->getUrl();
        $BibleSuperSearchDownloadVerbose = $a['verbose'];
        $BibleSuperSearchDownloadLimit   = array_key_exists('download_limit', $statics) ? (int) $statics['download_limit'] : 0;

        if(!array_key_exists('download_enabled', $statics) || !$statics['download_enabled']) {
            $msg = "<div style='background-color: yellow; font-weight: bold'>";
            $msg .= 'NOTICE TO WEBMASTER, RE: [biblesupersearch_downloads] shortcode.<br \><br \>';
            $msg .= 'The Bible downloads feature is not enabled on the Bible SuperSearch API located at the selected API URL of ' . $BibleSuperSearchAPIURL . ' <br />';
            $msg .= 'This will need to be enabled in order for the [biblesupersearch_downloads] shortcode to work.<br /><br />';
            $msg .= 'If you manage this instance of the Bible SuperSearch API, please enable Bible downloads in the API options. &nbsp;Please log in for details.<br />';
            $msg .= 'Once done, you will need to save the Bible SuperSearch plugin configs (without making any changes) to refresh the settings from the API.';
            $msg .= "</div>";

            return $msg;
        }

        wp_enqueue_script('biblesupersearch_download_js', plugins_url('download/download.js', __FILE__));
        wp_enqueue_style('biblesupersearch_download_css', plugins_url('download/download.css', __FILE__));

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

