<div class="inside">
    <table class="form-table bss_opt" id='bss_opt_features'>
        <tr><td colspan='2'><h2><?php esc_html_e( 'Features', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
        <?php $BibleSuperSearch_Options->renderOptions('features'); ?> 
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
    </table>
</div>