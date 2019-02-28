<?php
    // global $options, $bibles, $interfaces;
    global $BibleSuperSearch_Options;
?>

<div class="biblesupersearch-option-tabs wrap">
    <div class="icon32" id="icon-options-general"><br></div>
    <h1><?php esc_html_e( 'Bible SuperSearch Options', 'biblesupersearch' ); ?></h1>

    <div class="metabox-holder has-right-sidebar">
        <!--<div class="inner-sidebar">

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
        </div>
        <!-- .inner-sidebar -->


        <div id="post-body">
            <div id='post-body-menu'>
                <?php foreach($tabs as $key => $item): ?>
                    <a class='bss-menu-item <?php if($key == $tab) echo 'selected' ?>' href='?page=biblesupersearch&tab=<?php echo $key ?>'><?php echo $item['name'] ?></a>
                <?php endforeach; ?>
            </div>

            <div id="post-body-content">
                <form method="post" action="options.php">
                    <?php settings_fields( 'aicwebtech_plugin_options' ); ?>
                    <input type='hidden' name='tab' value='<?php echo $tab ?>' />

                    <div class="postbox tab-content">

                        <?php if($using_main_api): ?>
                            <div class='inside' style='font-weight: bold'>
                                This plugin uses the Bible SuperSearch API. &nbsp;By installing, activating and using this plugin, you agree to the API
                                <a href='https://api.biblesupersearch.com/documentation#tab_tos' target='_NEW'>Terms of Service</a> and 
                                <a href='https://api.biblesupersearch.com/documentation#tab_privacy' target='_NEW'>Privacy Policy</a>. <br /><br />
                                Did you know that you can install our API on your server for FREE? &nbsp;Enjoy faster API speed and no usage limits.
                                Visit our downloads page for details: <a href='https://www.biblesupersearch.com/downloads' target='_NEW'>https://www.biblesupersearch.com/downloads</a>
                            </div>
                        <?php else: ?>
                            <div class='inside' style='font-weight: bold'>
                                You are currently using a third party install of the Bible SuperSearch API: <?php echo $options['apiUrl'] ?>
                            </div>
                        <?php endif; ?>

                        <?php require_once(dirname(__FILE__) . '/templates/options_' . $tab . '.php'); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
