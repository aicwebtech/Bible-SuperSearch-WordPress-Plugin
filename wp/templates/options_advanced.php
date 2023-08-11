<div class="inside">
    <table class="form-table">
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
        <tr><td colspan='2'><h2><?php esc_html_e( 'Advanced Settings', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'><span><?php esc_html_e( 'Do not change these unless you know what you\'re doing.', 'biblesupersearch' ); ?></span></td></tr>
        <tr valign="top">
            <th scope="row" style='vertical-align: top;'><?php esc_html_e( 'API URL', 'biblesupersearch' ); ?></th>
            <td>
                <input type="text" size="40" name="biblesupersearch_options[apiUrl]" id='biblesupersearch_url'
                       value="<?php echo empty( $options['apiUrl'] ) ? '' : $options['apiUrl']; ?>"/>

                <div style="color:#666666;margin-left:2px;">
                    <?php echo wp_sprintf( esc_html__( 'Leave blank for default of %1$s.', 'biblesupersearch' ),
                        '<code>' . $this->default_options['apiUrl'] . '</code>'); ?>
                    <br />
                </div>
            </td>
        </tr>                            
        <tr valign="top">
            <th scope="row" style='vertical-align: top;'><?php esc_html_e( 'Scroll Top Padding', 'biblesupersearch' ); ?></th>
            <td>
                <input type="text" size="40" name="biblesupersearch_options[pageScrollTopPadding]" id='biblesupersearch_url'
                       value="<?php echo $options['pageScrollTopPadding']; ?>"/> pixels

                <div style="color:#666666;margin-left:2px;">
                    Use to adjust the final position of automatic scrolling.  A positive value will cause it to scroll further down, negative will scroll it further up.
                </div>
            </td>
        </tr>     
        <tr>
            <th scope="row"><label for='biblesupersearch_toggle_format_buttons'><?php esc_html_e( 'Debug Mode', 'biblesupersearch' ); ?></label></th>
            <td>
                <input id='biblesupersearch_toggle_debug' type='checkbox' name='biblesupersearch_options[debug]' value='1' 
                    <?php if($options['debug'] ) : echo "checked='checked'"; endif; ?>  />
                    Enables a mulitude of debugging messages in the console.  Enabling this will slow things down considerably, and is not recommended for a production site.
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
</div>
