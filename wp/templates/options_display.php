<div class="inside">
    <table class="form-table bss_opt" id='bss_opt_display'>
        <tr><td colspan='2'><h2><?php esc_html_e( 'Display Settings', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>        
        <?php $BibleSuperSearch_Options->renderOptions('display'); ?> 
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
    </table>
</div>