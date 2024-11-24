<?php
    $research_desc = array_key_exists('research_desc', $statics) ? $statics['research_desc'] : 'Intended for research purposes only.  
        These Bibles are not based on the Textus Receptus and/or are not Formal Equivalence 
        translations, and are therefore not recommended for other uses.';
?>

<div class="inside">
    <table class="form-table bss_opt" id='bss_opt_bible'>
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
                    <button id='biblesupersearch_def_bible_add'>Add Bible</button>&nbsp; &nbsp;
                    <button id='biblesupersearch_def_bible_rem'>Remove Bible</button>
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

        <?php $BibleSuperSearch_Options->renderOptions('bible'); ?> 
       
        <tr>
            <th scope="row" style='vertical-align: top'><?php esc_html_e( 'Limit Parallel Bibles by Width', 'biblesupersearch' ); ?></th>
            <td>
                <script>
                    var bssParBibleLimit = <?php echo json_encode($options['parallelBibleLimitByWidth']); ?>;
                </script>
                
                <input type='checkbox' id='parallelBibleLimitByWidthEnable' />
                Whether to limit the number of parallel Bibles allowed based on page width.  <!-- Check this box to configure this option. -->

                <br /><br />

                <div id='parallelBibleLimitByWidthContainer' style='display:none'>

                    <p><small>Please configure the desired limits below.  You can add as many threshold rows as desired.</small></p>
                    <p><small>Note: All values must be positive integers, with the excaption of minimum width on the first row, which is always 0.</small></p>

                    <br /><br />
                    <div class='center' style='width: 90%'>
                        <button class='parallelBibleLimitByWidthAdd'>Add</button>&nbsp; &nbsp;
                        <button class='parallelBibleLimitByWidthRemove'>Remove</button> 
                        <br /><br />

                        <table border='0' id='parallelBibleLimitByWidthTable' class='bss-subform-table'>
                            <tr>
                                <th>Minimum Width (in pixels)</th>
                                <th>Maximum Width (in pixels)</th>
                                <th>Maximum Bibles</th>
                                <th>Minimum Bibles</th>
                                <th>Initial Number of Parallel Bibles</th>
                            </tr>
                            <tr>
                                <td>Minimum page width.&nbsp; Must start with 0 and be in ascending order.</td>
                                <td>Maximum page width.&nbsp; Automatically calculated.</td>
                                <td>Maximum allowable parallel Bibles at this width.</td>
                                <td>Minimum number of parallel Bible selectors displayed at this width.</td>
                                <td>Number of parallel Bible selectors to iniitally display when the app loads.</td>
                            </tr>
                            <tbody id='parallelBibleLimitByWidthTbody'></tbody>
                        </table>

                        <br />
                        <button class='parallelBibleLimitByWidthAdd'>Add</button>&nbsp; &nbsp;
                        <button class='parallelBibleLimitByWidthRemove'>Remove</button> 
                    </div>

                    <br /><br />
                    <input id='biblesupersearch_parallelBibleStartSuperceedsDefaultBibles' type='checkbox' name='biblesupersearch_options[parallelBibleStartSuperceedsDefaultBibles]' value='1' 
                        <?php if($options['parallelBibleStartSuperceedsDefaultBibles'] ) : echo "checked='checked'"; endif; ?>  />
                    <label for='biblesupersearch_parallelBibleStartSuperceedsDefaultBibles'>
                        <?php esc_html_e( '"Initial Number of Parallel Bibles" Superceeds Default Bibles', 'biblesupersearch' ); ?>        
                    </label>

                    <p><small>Forces the number of parallel Bible selectors displayed initially to always equal the "Initial Number of Parallel Bibles,"</small></p>
                    <p><small>regardless to the number of Bibles selected as default.</small></p>
                </div>


            </td>
        </tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
    </table>
</div>