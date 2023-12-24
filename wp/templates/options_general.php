<div class="inside">
    <table class="form-table bss_opt" id='bss_opt_general'>
        <tr><td colspan='2'><h2><?php esc_html_e( 'Installation', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'>
            To use, simply add the shortcode <code>[biblesupersearch]</code> to any page or post.<br />Please see the 
            <a class='' href='?page=biblesupersearch&tab=docs'>Documentation</a> for details.
        </td></tr>
        <tr><td colspan='2'><h2><?php esc_html_e( 'General Settings', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
        <tr>
            <th scope="row" style='width: 220px; vertical-align: top;'><?php esc_html_e( 'Default Skin', 'biblesupersearch' ); ?></th>
            <td>
                <select name='biblesupersearch_options[interface]'>
                    <?php foreach($interfaces as $module => $int) :?>
                    <option value='<?php echo $module; ?>' <?php selected($module, $options['interface'] ); ?> ><?php echo $int['name']?></option>
                    <?php endforeach; ?>
                </select>
                <br />Sets the default skin seen on the [biblesupersearch] shortcode.
                <br />To preview skins, please visit <a href='https://www.biblesupersearch.com/client/' target='_NEW'>https://www.biblesupersearch.com/client/</a>
            </td>
        </tr>
        <tr>
            <th scope="row" style='width: 220px; vertical-align: top;'><?php esc_html_e( 'Default Language', 'biblesupersearch' ); ?></th>
            <td>
                <select name='biblesupersearch_options[language]'>
                    <?php foreach($languages as $key => $label) :?>
                    <option value='<?php echo $key; ?>' <?php selected($key, $options['language'] ); ?> >
                        <?php echo $label . ' (' . strtoupper($key) . ')'?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <br />Sets the default display language seen on the [biblesupersearch] shortcode.
            </td>
        </tr>        
        <?php foreach($selectables as $field => $prop): ?>
            <tr>
                <th scope="row" style='vertical-align: top;'><?php esc_html_e( $prop['name'], 'biblesupersearch' ); ?></th>
                <td>
                    <select name='biblesupersearch_options[<?php echo $field ?>]'>
                        <?php foreach($prop['items'] as $key => $item) :?>
                        <option value='<?php echo $key; ?>' <?php selected($key, $options[$field] ); ?> ><?php echo $item['name']?></option>
                        <?php endforeach; ?>
                    </select>
                    <br /><?php echo $prop['desc']?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th scope="row"><label for='biblesupersearch_toggle_format_buttons'><?php esc_html_e( 'Auto-Hide Formatting Buttons', 'biblesupersearch' ); ?></label></th>
            <td>
                <input id='biblesupersearch_toggle_format_buttons' type='checkbox' name='biblesupersearch_options[formatButtonsToggle]' value='1' 
                    <?php if($options['formatButtonsToggle'] ) : echo "checked='checked'"; endif; ?>  />
                Formatting buttons will only show when a search is active.
            </td>
        </tr>                  
        <tr>
            <th scope="row"><label for='biblesupersearch_toggle_format_buttons'><?php esc_html_e( 'Include Testament', 'biblesupersearch' ); ?></label></th>
            <td>
                <input id='biblesupersearch_include_testament' type='checkbox' name='biblesupersearch_options[includeTestament]' value='1' 
                    <?php if($options['includeTestament'] ) : echo "checked='checked'"; endif; ?>  />
                Includes "Old Testament" or "New Testament" verbiage in some references.
            </td>
        </tr>                                
        <tr>
            <th scope="row"><label for='biblesupersearch_override_css'><?php esc_html_e( 'Override Styles', 'biblesupersearch' ); ?></label></th>
            <td>
                <input id='biblesupersearch_override_csss' type='checkbox' name='biblesupersearch_options[overrideCss]' value='1' 
                    <?php if($options['overrideCss'] ) : echo "checked='checked'"; endif; ?>  />
                Attempts to override some CSS styles from WordPress to make Bible SuperSearch look as was originally designed.
            </td>
        </tr>                         
        <tr>
            <th scope="row"><label for='biblesupersearch_toggle_advanced'><?php esc_html_e( 'Advanced Search Toggle', 'biblesupersearch' ); ?></label></th>
            <td>
                <input id='biblesupersearch_toggle_advanced' type='checkbox' name='biblesupersearch_options[toggleAdvanced]' value='1' 
                    <?php if($options['toggleAdvanced'] ) : echo "checked='checked'"; endif; ?>  />
                Adds a button to toggle an 'advanced search' form
            </td>
        </tr>            
        <?php $BibleSuperSearch_Options->renderOptions('general'); ?> 
        <tr>
            <th scope="row" style='vertical-align: top'><label for='biblesupersearch_default_landing'><?php esc_html_e( 'Default Destination Page', 'biblesupersearch' ); ?></label></th>
            <td>
                <select id='biblesupersearch_default_landing' type='checkbox' name='biblesupersearch_options[defaultDestinationPage]'>
                <?php echo $BibleSuperSearch_Options->getLandingPageOptions(TRUE, $options['defaultDestinationPage']); ?>
                </select>
                <br /><br />
                Select a page or post containing the [biblesupersearch] shortcode, and all other Bible SuperSearch forms on your site will redirect here.
                This allows you to have the form on one page, but display the results on another.  Add the [biblesupersearch] shortcode to any page or post, and it will appear in this list.
            </td>
        </tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
    </table>
</div>