<div class="inside">
    <table class="form-table">
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
        <tr><td colspan='2'><h2><?php esc_html_e( 'Advanced Settings', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'><span><?php esc_html_e( 'Do not change these unless you know what you\'re doing.', 'biblesupersearch' ); ?></span></td></tr>            
        <?php $BibleSuperSearch_Options->renderOptions('advanced'); ?> 
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
    </table>
</div>
