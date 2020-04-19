<div class="inside">
    <table class="form-table">
        <tr><td colspan='2'><h2><?php esc_html_e( 'Bibles', 'biblesupersearch' ); ?></h2></td></tr>
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
            <th scope="row"><?php esc_html_e( 'Bible List Grouping', 'biblesupersearch' ); ?></th>
            <td>
                <select name='biblesupersearch_options[bibleGrouping]'>
                    <option value='none' <?php selected('', $options['bibleGrouping'] ); ?> >None</option>
                    <option value='language' <?php selected('language', $options['bibleGrouping'] ); ?> >Language - Endonym</option>
                    <option value='language_english' <?php selected('language_english', $options['bibleGrouping'] ); ?> >Language - English Name</option>
                </select>
            </td>
        </tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
    </table>
</div>