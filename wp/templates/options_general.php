<div class="inside">
    <table class="form-table">
        <tr><td colspan='2'><h2><?php esc_html_e( 'Installation', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'>To use, simply add the shortcode <code>[biblesupersearch]</code> to any page or post.</td></tr>
        <tr><td colspan='2'>
            <table>
                <tr><th colspan='3'>Shortcode Options</th></tr>
                <?php foreach(BibleSuperSearch_Shortcodes::$displayAttributes as $key => $info): ?>
                    <tr><td><?php echo $key;?></td><td><?php echo $info['name']; ?></td><td><?php echo $info['desc']; ?></td></tr>
                <?php endforeach;?>
            </table>
        </td></tr>
        <tr><td colspan='2'><h2><?php esc_html_e( 'General Settings', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Select Default Skin', 'biblesupersearch' ); ?></th>
            <td>
                <select name='biblesupersearch_options[interface]'>
                    <?php foreach($interfaces as $module => $int) :?>
                    <option value='<?php echo $module; ?>' <?php selected($module, $options['interface'] ); ?> ><?php echo $int['name']?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for='biblesupersearch_override_csss'><?php esc_html_e( 'Override Styles', 'biblesupersearch' ); ?></label></th>
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
        <tr>
            <th scope="row"><label for='biblesupersearch_toggle_format_buttons'><?php esc_html_e( 'Hide Format Buttons', 'biblesupersearch' ); ?></label></th>
            <td>
                <input id='biblesupersearch_toggle_format_buttons' type='checkbox' name='biblesupersearch_options[formatButtonsToggle]' value='1' 
                    <?php if($options['formatButtonsToggle'] ) : echo "checked='checked'"; endif; ?>  />
                Buttons below search form will only show when a search is active.
            </td>
        </tr>                                
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