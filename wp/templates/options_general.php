<div class="inside">
    <table class="form-table bss_opt" id='bss_opt_general'>
        <tr><td colspan='2'><h2><?php esc_html_e( 'Installation', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'>
            To use, simply add the shortcode <code>[biblesupersearch]</code> to any page or post.<br />Please see the 
            <a class='' href='?page=biblesupersearch&tab=docs'>Documentation</a> for details.
        </td></tr>
        <tr><td colspan='2'><h2><?php esc_html_e( 'General Settings', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
        
        <?php $BibleSuperSearch_Options->renderOptions('general', 'general_top'); ?> 

<!--         <tr>
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
                    <option value='global_default' <?php selected('global_default', $options['language'] ); ?>>
                        Global Default (General => Site Language)
                    </option>

                    <?php foreach($languages as $key => $label) :?>
                    <option value='<?php echo $key; ?>' <?php selected($key, $options['language'] ); ?> >
                        <?php echo $label . ' (' . strtoupper($key) . ')'?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <br />Sets the default display language seen on the [biblesupersearch] shortcode.
            </td>
        </tr>        -->     
        <tr>
            <th scope="row" style='width: 220px; vertical-align: top;'><?php esc_html_e( 'Display Language(s)', 'biblesupersearch' ); ?></th>
            <td>
                <input id='biblesupersearch_all_languages' type='checkbox' name='biblesupersearch_options[enableAllLanguages]' value='1' 
                        <?php if($options['enableAllLanguages'] ) : echo "checked='checked'"; endif; ?>  />
                <label for='biblesupersearch_all_languages'>Enable ALL Languages</label><br /><br />

                <div class='biblesupersearch_language_list biblesupersearch_toggled_language'>
                    <?php foreach($languages as $key => $label) :?>
                        <?php 
                            $checked = is_array($options['languageList']) && in_array($key, $options['languageList']) ? "checked='checked'" : '';
                            $id = 'langlist_' . $key;
                        ?>

                        <input type='checkbox' id='<?php echo $id; ?>' value='<?php echo $key ?>' 
                            name='biblesupersearch_options[languageList][]' <?php echo $checked; ?> />
                        <label for='<?php echo $id; ?>'><?php echo $label . ' (' . strtoupper($key) . ')'; ?></label><br />
                    <?php endforeach; ?>
                </div>
                <br />Sets the display language(s) that can be selected by the user.
            </td>
        </tr>                
        <?php $BibleSuperSearch_Options->renderOptions('general', 'general'); ?> 
<!--         <tr>
            <th scope="row" style='vertical-align: top'><label for='biblesupersearch_default_landing'><?php esc_html_e( 'Default Destination Page', 'biblesupersearch' ); ?></label></th>
            <td>
                <select id='biblesupersearch_default_landing' type='checkbox' name='biblesupersearch_options[defaultDestinationPage]'>
                <?php echo $BibleSuperSearch_Options->getLandingPageOptionsOld(TRUE, $options['defaultDestinationPage']); ?>
                </select>
                <br /><br />
                Select a page or post containing the [biblesupersearch] shortcode, and all other Bible SuperSearch forms on your site will redirect here.
                This allows you to have the form on one page, but display the results on another.  Add the [biblesupersearch] shortcode to any page or post, and it will appear in this list.
            </td>
        </tr> -->
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
    </table>
</div>