<?php
    // global $options, $bibles, $interfaces;
?>

<div class="biblesupersearch-option-tabs wrap">
    <div class="icon32" id="icon-options-general"><br></div>
    <h1><?php esc_html_e( 'Bible SuperSearch Options', 'biblesupersearch' ); ?></h1>

    <div class="metabox-holder has-right-sidebar">
<!--             <div class="inner-sidebar">

            <div class="postbox sm-box">
                <h3><span><?php esc_html_e( 'Need Some Help?', 'biblesupersearch' ); ?></span>
                </h3>
                <div class="inside">
                    <p style="text-align:justify"><?php echo wp_sprintf( esc_html__( 'Did you know you can get expert support for only $49 per year! %s today and get support from the developers who are building the Bible SuperSearch.', 'biblesupersearch' ), '<a href="https://biblesupersearch.com/wordpress-plugins/biblesupersearch/?utm_source=biblesupersearch&utm_medium=wordpress" target="_blank">' . esc_html__( 'Sign up', 'biblesupersearch' ) . '</a>' ); ?></p>
                    <div style="text-align:center">
                        <a href="https://wordpress.org/support/plugin/biblesupersearch"
                           target="_blank"
                           class="button-secondary"><?php esc_html_e( 'Free&nbsp;Support', 'biblesupersearch' ); ?></a>&nbsp;
                        <a href="https://biblesupersearch.com/my/clientarea.php"
                           class="button-primary"><?php esc_html_e( 'Priority&nbsp;Support', 'biblesupersearch' ); ?></a>
                    </div>
                    <div style="text-align:center;font-size:0.85em;padding:0.7rem 0 0">
                        <span><?php esc_html_e( 'We offer limited free support via WordPress.org', 'biblesupersearch' ); ?></span>
                    </div>
                </div>
            </div>

            <div class="postbox sm-box">
                <h3>
                    <span><?php esc_html_e( 'Frequently Asked Questions', 'biblesupersearch' ); ?></span>
                </h3>
                <div class="inside">
                    <ul>
                        <li>- <a
                                    href="https://www.biblesupersearch.com/my/knowledgebase/72/Getting-Started-with-biblesupersearch.html"
                                    title="" target="_blank">Getting Started with Bible SuperSearch</a></li>
                        <li>- <a
                                    href="https://www.biblesupersearch.com/my/knowledgebase/75/biblesupersearch-Shortcodes.html"
                                    title="Bible SuperSearch Shortcodes" target="_blank">Bible SuperSearch
                                Shortcodes</a></li>
                        <li>- <a
                                    href="https://www.biblesupersearch.com/my/knowledgebase/67/Troubleshooting-biblesupersearch.html"
                                    title="Troubleshooting Bible SuperSearch" target="_blank">Troubleshooting
                                Bible SuperSearch</a></li>
                    </ul>
                    <div style="text-align:center;font-size:0.85em;padding:0.4rem 0 0">
                        <span><?php echo wp_sprintf( esc_html__( 'Find out more in our %s', 'biblesupersearch' ), '<a href="https://www.biblesupersearch.com/my/knowledgebase.php" title="Knowledgebase" target="_blank">' . esc_html__( 'knowledge base', 'biblesupersearch' ) . '</a>' ); ?></span>
                    </div>
                </div>
            </div>

            <div class="postbox sm-box">
                <h3>
                    <span><?php esc_html_e( 'Lets Make It Even Better!', 'biblesupersearch' ); ?></span>
                </h3>
                <div class="inside">
                    <p style="text-align:justify"><?php esc_html_e( 'If you have ideas on how to make Bible SuperSearch or any of our products better, let us know!', 'biblesupersearch' ); ?></p>
                    <div style="text-align:center">
                        <a href="https://feedback.userreport.com/05ff651b-670e-4eb7-a734-9a201cd22906/"
                           target="_blank"
                           class="button-secondary"><?php esc_html_e( 'Submit&nbsp;Your&nbsp;Idea', 'biblesupersearch' ); ?></a>
                    </div>
                </div>
            </div>
        </div> --> <!-- .inner-sidebar -->

        <div id="post-body">
            <div id="post-body-content">
                <form method="post" action="options.php">
                    <?php settings_fields( 'aicwebtech_plugin_options' ); ?>

                    <div class="postbox tab-content">
                        <div class='inside' style='font-weight: bold'>
                            This plugin uses the Bible SuperSearch API. &nbsp;By installing, activating and using this plugin, you agree to the API
                            <a href='https://api.biblesupersearch.com/documentation#tab_tos' target='_NEW'>Terms of Service</a> and 
                            <a href='https://api.biblesupersearch.com/documentation#tab_privacy' target='_NEW'>Privacy Policy</a>
                        </div>

                        <div class="inside">
                            <table class="form-table">
                                <tr><td colspan='2'><h2><?php esc_html_e( 'Installation', 'biblesupersearch' ); ?></h2></td></tr>
                                <tr><td colspan='2'>To use, simply add the shortcode <code>[biblesupersearch]</code> to any page or post.</td></tr>
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
                                        <div class='biblesupersearch_enabled_bible'>
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
                                    <th scope="row"><label for='biblesupersearch_override_csss'><?php esc_html_e( 'Override Styles', 'biblesupersearch' ); ?></label></th>
                                    <td>
                                        <input id='biblesupersearch_override_csss' type='checkbox' name='biblesupersearch_options[overrideCss]' value='1' 
                                            <?php if($options['overrideCss'] ) : echo "checked='checked'"; endif; ?>  />
                                        Attempts to override some CSS styles from WordPress to make Bible SuperSearch look as was originally designed.
                                    </td>
                                </tr>
                                <tr><td colspan='2'><?php submit_button(); ?></td></tr>
                                <tr><td colspan='2'><h2><?php esc_html_e( 'Advanced Settings', 'biblesupersearch' ); ?></h2></td></tr>
                                <tr><td colspan='2'><span><?php esc_html_e( 'Do not change these unless you know what you\'re doing.', 'biblesupersearch' ); ?></span></td></tr>
                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e( 'API URL', 'biblesupersearch' ); ?></th>
                                    <td>
                                        <input type="text" size="40" name="biblesupersearch_options[apiUrl]"
                                               value="<?php echo empty( $options['apiUrl'] ) ? '' : $options['apiUrl']; ?>"/>

                                        <span style="color:#666666;margin-left:2px;">
                                            <?php echo wp_sprintf( esc_html__( 'Leave blank for default of %1$s.', 'biblesupersearch' ),
                                                '<code>' . $this->default_options['apiUrl'] . '</code>'); ?>
                                            <br />
                                        </span>
                                    </td>
                                </tr>                                
                                <tr valign="top">
                                    <th scope="row" style='vertical-align:top'><?php esc_html_e( 'Extra CSS', 'biblesupersearch' ); ?></th>
                                    <td>
                                        Will be applied everywhere the <code>[biblesuperseach]</code> shortcode is found. <br /><br />
                                        <textarea type="text" size="40" name="biblesupersearch_options[extraCss]" style='width:700px; height: 400px;'
                                            ><?php echo empty( $options['extraCss'] ) ? '' : $options['extraCss']; ?></textarea>

                                        <span style="color:#666666;margin-left:2px;">
                                            <?php //esc_html_e( 'Extra CSS', 'biblesupersearch' ); ?>
                                            <br />
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <?php submit_button(); ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
