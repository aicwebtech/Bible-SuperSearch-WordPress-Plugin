<?php

defined( 'ABSPATH' ) or die; // exit if accessed directly

class BibleSuperSearch_Options_WP extends BibleSuperSearch_Options_Abstract {
    
    protected $option_index = 'biblesupersearch_options';

    protected $debug_count = 0;
    
    public function __construct() {
        parent::__construct();

        // Add some WordPress-specific options and tabs

        $this->default_options['overrideCss'] = TRUE;
        $this->default_options['extraCss'] = '';

        $this->tabs['general']['checkboxes'][] = 'overrideCss';
        $this->tabs['advanced']['texts'][] = 'extraCss';

        $this->tabs['docs'] = [
            'name'          => 'Documentation',
            'texts'         => [],
            'selects'       => [],
            'checkboxes'    => [],
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

    function adminInit() {
        global $wp_version;

        $args = array($this, 'validateOptions');

        if ( version_compare( $wp_version, '4.7.0', '>=' ) ) {
            $args = array(
                'sanitize_callback' => array($this, 'validateOptions')
            );
        }

        register_setting( 'aicwebtech_plugin_options', $this->option_index, $args );
    }

    public function pluginMenu() {
        $Options = new static();
        add_options_page( 'Bible SuperSearch Options', 'Bible SuperSearch', 'manage_options', 'biblesupersearch', array($this, 'displayPluginOptions'));
    }

    public function getOptions($dont_set_default = FALSE) {
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
        }

        $options['defaultBible'] = array_filter($options['defaultBible']);
        $options['defaultBible'] = array_values($options['defaultBible']);

        return $options;
    }

    public function setDefaultOptions() {
        if ( ! is_array( get_option( $this->option_index ) ) ) {
            delete_option( $this->option_index ); // just in case
            update_option( $this->option_index, $this->default_options );
        }

        // Flush rewrite cache
        // flush_rewrite_rules( true );
    }

    public function renderOptions($tab, $option_list = [])
    {
        if(!isset($this->tabs[$tab])) {
            return false;
        }

        if(!$option_list) {
            $option_list = $this->tabs[$tab]['options'];
        }

        $list = $this->options_list;
        $options = $this->getOptions();

        foreach($option_list as $f) {
            if(!isset($list[$f])) {
                continue;
            }

            $o = $list[$f];

            // todo - get class info, have class render field
            // $class = $this->makeOptionClass($list[$field]);

            $row_classes = isset($o['row_classes']) ? "class='" . $o['row_classes'] . "'" : '';

            ?>
                <tr <?php echo $row_classes; ?> >
                    <th scope="row" style='vertical-align: top;'>
                        <label for='biblesupersearch_<?php echo $f ?>'><?php esc_html_e( $o['label'], 'biblesupersearch' ); ?></label>
                    </th>
            <?php

            switch($o['type']) {
                case 'section':
                    // do nothing more
                    break;
                case 'checkbox':
                    ?>
                        <td>
                            <input id='biblesupersearch_<?php echo $f; ?>' type='checkbox' name='biblesupersearch_options[<?php echo $f; ?>]' value='1' 
                                <?php if($options[$f] ) : echo "checked='checked'"; endif; ?>  /> 
                            <small>
                                <?php echo $o['desc']?>
                            </small>
                        </td>
                    <?php

                    break;
                case 'text':
                case 'integer':
                case 'int':
                    ?>
                        <td>
                            <input name='biblesupersearch_options[<?php echo $f; ?>]' value='<?php echo $options[$f]?>' style='width:50%' />
                            <p>
                                <small>
                                    <?php echo $o['desc']?>
                                </small>
                            </p>
                        </td>
                    <?php

                    break;
                case 'select':
                    ?>
                        <td>
                            <select name='biblesupersearch_options[<?php echo $f; ?>]' id='biblesupersearch_options[<?php echo $f; ?>]'>
                                <?php foreach($o['options'] as $key => $opt): ?>
                                    <option value='<?php echo $key; ?>' <?php if($options[$f] == $key): echo "selected=selected"; endif; ?> >
                                        <?php echo $opt; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p>
                                <small>
                                    <?php echo $o['desc']?>
                                </small>
                            </p>
                        </td>
                    <?php

                    break;
            }

            ?>
                </tr>
            <?php
        }
    }

    public function validateOptions( $incoming ) 
    {
        $current = $input = $this->getOptions(TRUE);
        
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : 'general';
        $tab_item = $this->tabs[ $tab ];
        $list = $this->options_list;

        foreach($tab_item['options'] as $field) {
            if(!isset($list[$field])) {
                continue;
            }

            switch($list[$field]['type']) {
                case 'checkbox':
                    $input[$field] = (array_key_exists($field, $incoming) && !empty($incoming[$field])) ? true : false;
                    break;
                case 'text':
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
            }
        }

        foreach($tab_item['texts'] as $field) {
            if(array_key_exists($field, $incoming)) {
                $input[$field] = $incoming[$field];
            }
        }          

        foreach($tab_item['selects'] as $field) {
            if(array_key_exists($field, $incoming)) {
                $input[$field] = $incoming[$field];
            }
        }       

        foreach($tab_item['checkboxes'] as $field) {
            $input[$field] = (array_key_exists($field, $incoming) && !empty($incoming[$field])) ? TRUE : FALSE;
        }

        if(is_array($tab_item['json'])) {
            foreach($tab_item['json'] as $field) {
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

                //$input[$field] = (array_key_exists($field, $incoming)) ? json_encode($incoming[$field]) : '[]'; // ?? works?
                

                // $input[$field] = (array_key_exists($field, $incoming)) ? $incoming[$field] : '[]';
            }
        }

        // if(!isset($input['enableAllBibles'])) {
        //     $input['enableAllBibles'] = FALSE;
        // }

        // $input['overrideCss'] = ($input['overrideCss']) ? TRUE : FALSE;

        // Cherry-pick default values 
        foreach($this->default_options as $item => $value) {
            if(!array_key_exists($item, $input)) {
                $input[$item] = $value;
            }
        }

        if($tab == 'bible') {
            if($input['enableAllBibles']) {
                $input['enabledBibles'] = [];
            }            
            else {
                // Make sure default Bible is in list of selected Bibles
                if(!in_array($input['defaultBible'], $input['enabledBibles'])) {
                    $input['enabledBibles'][] = $input['defaultBible'];
                }
            }
        }

        if($tab == 'general') {
            if($input['enableAllLanguages']) {
                $input['languageList'] = [];
            }
            else {
                // Make sure default language is in list of selected languages
                if(!in_array($input['language'], $input['languageList'])) {
                    $input['languageList'][] = $input['language'];
                }
            }
        }

        if($tab == 'advanced' && empty($input['apiUrl'])) {
            $input['apiUrl'] = $this->default_options['apiUrl'];
        }

        $this->_setStaticsReset(); // Always Force Reload statics when options saved

        // if($current['apiUrl'] != $input['apiUrl']) {
            // $this->_setStaticsReset(); // Force Reload statics here if URL changed
        // }

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

    public function displayPluginOptions() {
        $tabs = $this->tabs;
        $tab  = (array_key_exists('tab', $_REQUEST) && $_REQUEST['tab']) ? $_REQUEST['tab'] : 'general';

        if ( ! isset( $_REQUEST['settings-updated'] ) ) {
            $_REQUEST['settings-updated'] = FALSE;
        }

        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        if(!$tabs[ $tab ]) {
            wp_die( __( 'Invalid tab.' ) );
        }

        // $this->setDefaultOptions();
        biblesupersearch_enqueue_option();
        $options    = $this->getOptions();
        $bibles     = $this->getBible();
        $interfaces = $this->getInterfaces(); 
        $selectables = $this->getSelectableItems();
        $languages = static::getSelectorOptions('language');

        $using_main_api = (empty($options['apiUrl']) || $options['apiUrl'] == $this->default_options['apiUrl']) ? TRUE : FALSE;

        $statics = $this->getStatics();

        // print_r($statics);

        $download_enabled = (bool) $statics['download_enabled'];

        $reccomended_plugins = $this->getRecomendedPlugins(TRUE);

        require( dirname(__FILE__) . '/template.options.php');
        return;
    }

    public function getLandingPageOptions($render_html = FALSE, $value = NULL, $zero_option = 'None', $zero_default = FALSE) {
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

    protected function _formatLandingPageOption(&$landing_page) {
        $title = ($landing_page['post_title']) ? $landing_page['post_title'] : '(No Title, ID = ' . $landing_page['ID'] . ')';
        $type = ucfirst($landing_page['post_type']);
        $landing_page['title_fmt'] = $type . ': ' . $title;
    }

    public function hasLandingPageOptions() {
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

    protected function _afterFetchStatics($result) {
        update_option('biblesupersearch_statics', $result['results']);
    }

    public function renderDownloadPage() {

    }

    public function getInterfaceByName($name) {
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
