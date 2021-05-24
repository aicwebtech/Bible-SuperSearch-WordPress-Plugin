<div class="inside">
    <table class="form-table">
        <tr><td colspan='2'><h2><?php esc_html_e( 'Documentation', 'biblesupersearch' ); ?></h2></td></tr>
        <tr><td colspan='2'>To use, simply add the shortcode <code>[biblesupersearch]</code> to any page or post.</td></tr>
        <tr><td colspan='2'>
            <table class='shortcode-options-table'>
                <tr><th colspan='3'>[biblesupersearch] Shortcode Options</th></tr>
                <tr><td colspan='3'>This shortcode displays the main Bible application.</th></tr>
                <?php foreach(BibleSuperSearch_Shortcodes::$displayAttributes as $key => $info): ?>
                    <tr>
                        <td style='' class='key'><?php echo $key;?></td>
                        <td style='vertical-align:top' class='name'><?php echo $info['name']; ?></td>
                        <td class='desc'><?php echo $info['desc']; ?></td>
                    </tr>
                <?php endforeach;?>
            </table>            
            <table class='shortcode-options-table'>
                <tr><th colspan='3'>[biblesupersearch_downloads] Shortcode Options</th></tr>
                <tr><td colspan='3'>This shortcode displays the Bible download form. &nbsp; From here, users are able to download most enabled Bibles in a selection of formats.</th></tr>
                <?php foreach(BibleSuperSearch_Shortcodes::$downloadAttributes as $key => $info): ?>
                    <tr>
                        <td style='vertical-align:top' class='key'><?php echo $key;?></td>
                        <td style='vertical-align:top' class='name'><?php echo $info['name']; ?></td>
                        <td class='desc'><?php echo $info['desc']; ?></td>
                    </tr>
                <?php endforeach;?>
            </table>            
            <table class='shortcode-options-table'>
                <tr><th colspan='3'>[biblesupersearch_bible_list] Shortcode Options</th></tr>
                <tr><td colspan='3'>This shortcode displays the list of enabled Bible(s).</th></tr>
                <?php foreach(BibleSuperSearch_Shortcodes::$biblesAttributes as $key => $info): ?>
                    <tr>
                        <td style='vertical-align:top' class='key'><?php echo $key;?></td>
                        <td style='vertical-align:top' class='name'><?php echo $info['name']; ?></td>
                        <td class='desc'><?php echo $info['desc']; ?></td>
                    </tr>
                <?php endforeach;?>
            </table>
        </td></tr>
    </table>
</div>