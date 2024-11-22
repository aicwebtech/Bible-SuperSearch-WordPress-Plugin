<div class="inside">
    <table class="form-table bss_opt" id='bss_opt_display'>

        <tr><td colspan='2'><h2><?php esc_html_e( 'Display Settings', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
    
        <?php foreach($selectables as $field => $prop): ?>
            <tr>
                <th scope="row" style='vertical-align: top;'><?php esc_html_e( $prop['name'], 'biblesupersearch' ); ?></th>
                <td>
                    <select name='biblesupersearch_options[<?php echo $field ?>]'>
                        <?php foreach($prop['items'] as $key => $item) :?>
                        <option value='<?php echo $key; ?>' <?php selected($key, $options[$field] ); ?> ><?php echo $item['name']?></option>
                        <?php endforeach; ?>
                    </select>
                    <br /><?php echo $prop['desc']?>
                </td>
            </tr>
        <?php endforeach; ?>         
        <?php $BibleSuperSearch_Options->renderOptions('display'); ?> 
        <tr><td colspan='2'><?php submit_button(); ?></td></tr>
    </table>
</div>