<div class="inside">
    <table class="form-table">
        <tr><td colspan='2'><h2><?php esc_html_e( 'Appearance', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>                
        <tr>
            <th scope="row"><?php esc_html_e( 'Form Background Color', 'biblesupersearch' ); ?></th>
            <td>
                
                <input name='biblesupersearch_options[formStyles][background-color]' value='<?php echo $options['formStyles']['background-color'] ?>' type='color' />
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