<?php
    $research_desc = array_key_exists('research_desc', $statics) ? $statics['research_desc'] : 'Intended for research purposes only.  
        These Bibles are not based on the Textus Receptus and/or are not Formal Equivalence 
        translations, and are therefore not recommended for other uses.';
?>

<div class="inside">
    <table class="form-table">
        <tr><td colspan='2'><h2><?php esc_html_e( 'Bibles', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'><?php submit_button('Refresh Bible List (&amp; Save Changes)'); ?></td></tr>
        <tr>
            <th scope="row" style='vertical-align:top'>
                <?php esc_html_e( 'Select Default Bible(s)', 'biblesupersearch' ); ?>
            </th>
            <td >
                <div id='defaultBibleContainer'>
                    <?php foreach($options['defaultBible'] as $key => $def_bible): ?>

                        <select name='biblesupersearch_options[defaultBible][]' id='default_bible_<?php echo $key; ?>'>
                            <?php if($key > 0) {
                                $pb = $key + 1;
                                echo "<option value='0'>Parallel Bible {$pb} - None</option>";
                            }
                            ?>

                            <?php foreach($bibles as $module => $bible) :?>
                            <option value='<?php echo $module; ?>' <?php selected($module, $def_bible); ?> ><?php echo $bible['display']; ?></option>
                            <?php endforeach; ?>
                        </select>

                    <?php endforeach; ?>
                </div>
                <div>
                    <button id='biblesupersearch_def_bible_add'>Add Bible</button>
                </div>

                <p><small>Note: The number of multiple default Bibles is limited by the parallel Bible limit on the selected skin.</small></p>
                <p><small>Bible selections beyond the limit will be ignored.</small></p>
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
                    <span class='biblesupersearch_toggled_bible' style='display:none'>
                        <a href='javascript:void(0)' id='biblesupersearch_check_all_bibles'>Check all Bibles</a>&nbsp; &nbsp;
                        <a href='javascript:void(0)' id='biblesupersearch_uncheck_all_bibles'>Uncheck all Bibles</a><br />
                    </span>
                </div>
                <br /><br />
                <div class='biblesupersearch_enabled_bible biblesupersearch_toggled_bible' style='display:none'>
                    <div>
                    <?php $old_lang = NULL; $lang_count = 0; ?>
                    <?php foreach($bibles as $module => $bible): ?>
                        <?php if($bible['lang'] != $old_lang): ?>
                        </div>

                        <?php if($lang_count >= 4): ?>
                            <?php $lang_count = 0;?>
                            <div style='clear:both'></div>
                        <?php endif; ?>
                        <?php $lang_count ++ ?>

                        <div class='bss_bible_lang' style=''>
                            <b><?php echo $bible['lang']; ?></b><br />
                    <?php endif; ?>
                        <div class='bss_bible' style='' >
                            <input name='biblesupersearch_options[enabledBibles][]' type='checkbox' value='<?php echo $module; ?>' 
                                id='enabled_<?php echo $module; ?>' <?php if(in_array($module, $options['enabledBibles'] ) ) : echo "checked='checked'"; endif; ?> />
                            <?php echo $bible['research'] == 1 ? '* ' : ''; ?>
                            <label for='enabled_<?php echo $module; ?>'><?php echo $bible['display_short']; ?></label><br />
                        </div>
                    <?php $old_lang = $bible['lang']; ?>
                    <?php endforeach; ?>
                    </div>
                    <small>* <?php echo $research_desc; ?></small>
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
                    <option value='language_and_english' <?php selected('language_and_english', $options['bibleGrouping'] ); ?> >Language - Endonym and English Name</option>
                </select>
            </td>
        </tr>        
        <tr>
            <th scope="row" style='vertical-align: top'><?php esc_html_e( 'Bible List Sorting', 'biblesupersearch' ); ?></th>
            <td>
                <select name='biblesupersearch_options[bibleSorting]'>
                    <option value='language_english|name' <?php selected('language_english|name', $options['bibleSorting'] ); ?> >Language - English Name / Full Name</option>
                    <option value='language_english|shortname' <?php selected('language_english|shortname', $options['bibleSorting'] ); ?> >Language - English Name / Short Name</option>
                    <option value='language_english|rank|name' <?php selected('language_english|rank|name', $options['bibleSorting'] ); ?> >Language - English Name / Rank / Full Name</option>
                    <option value='language_english|rank|shortname' <?php selected('language_english|rank|shortname', $options['bibleSorting'] ); ?> >
                        Language - English Name / Rank / Short Name
                    </option>
                    <option value='language_english|rank' <?php selected('language_english|rank', $options['bibleSorting'] ); ?> >Language - English Name / Rank</option>
                    <option value='rank' <?php selected('rank', $options['bibleSorting'] ); ?> >Rank</option>
                    <option value='name' <?php selected('name', $options['bibleSorting'] ); ?> >Full Name</option>
                    <option value='shortname' <?php selected('shortname', $options['bibleSorting'] ); ?> >Short Name</option>
                </select>

                <p><small>Note: This ONLY controls the Bible sorting in the Bible list; it doesn't affect the labels on the options.</small></p>
                <p><small>Note: Rank is a user-defined sort order that is defined on the API.</small></p>
            </td>
        </tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
    </table>
</div>