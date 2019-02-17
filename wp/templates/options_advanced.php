
<div id="post-body-content">
    <form method="post" action="options.php">
        <?php settings_fields( 'aicwebtech_plugin_options' ); ?>
        <input type='hidden' name='tab' value='<?php echo $tab ?>' />

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
                    <tr><td colspan='2'><?php submit_button(); ?></td></tr>
                    <tr><td colspan='2'><h2><?php esc_html_e( 'Advanced Settings', 'biblesupersearch' ); ?></h2></td></tr>
                    <tr><td colspan='2'><span><?php esc_html_e( 'Do not change these unless you know what you\'re doing.', 'biblesupersearch' ); ?></span></td></tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e( 'API URL', 'biblesupersearch' ); ?></th>
                        <td>
                            <input type="text" size="40" name="biblesupersearch_options[apiUrl]" id='biblesupersearch_url'
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
                    <tr><td colspan='2'><?php submit_button(); ?></td></tr>
                </table>
                <?php submit_button(); ?>
            </div>
        </div>
    </form>
</div>