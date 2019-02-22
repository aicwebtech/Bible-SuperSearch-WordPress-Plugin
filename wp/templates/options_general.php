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
            <th scope="row"><?php esc_html_e( 'Select Default Bible', 'biblesupersearch' ); ?></th>
            <td>
                <select name='biblesupersearch_options[defaultBible]'>
                    <?php foreach($bibles as $module => $bible) :?>
                    <option value='<?php echo $module; ?>' <?php selected($module, $options['defaultBible'] ); ?> ><?php echo $bible['display']; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Enabled Bibles', 'biblesupersearch' ); ?></th>
            <td>                                                    
                <div style='float:left'>
                    <input id='biblesupersearch_all_bibles' type='checkbox' name='biblesupersearch_options[enableAllBibles]' value='1' 
                        <?php if($options['enableAllBibles'] ) : echo "checked='checked'"; endif; ?>  />
                    <label for='biblesupersearch_all_bibles'>Enable ALL Bibles</label> &nbsp;
                    <!-- (This will also automatically enable any Bibles added in the future.) -->
                </div>
                <br /><br />
                <div class='biblesupersearch_enabled_bible' style='display:none'>
                    <div>
                    <?php $old_lang = NULL ?>
                    <?php foreach($bibles as $module => $bible): ?>
                    <?php if($bible['lang'] != $old_lang): ?>
                    </div>
                    <div _style='clear:both'></div>
                    <div class='bss_bible_lang' style=''>
                        <b><?php echo $bible['lang']; ?></b><br />
                    <?php endif; ?>
                        <div class='bss_bible' style='' >
                            <input name='biblesupersearch_options[enabledBibles][]' type='checkbox' value='<?php echo $module; ?>' 
                                id='enabled_<?php echo $module; ?>' <?php if(in_array($module, $options['enabledBibles'] ) ) : echo "checked='checked'"; endif; ?> />
                            <label for='enabled_<?php echo $module; ?>'><?php echo $bible['display_short']; ?></label><br />
                        </div>
                    <?php $old_lang = $bible['lang']; ?>
                    <?php endforeach; ?>
                    </div>
                </div>
            </td>
        </tr>
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