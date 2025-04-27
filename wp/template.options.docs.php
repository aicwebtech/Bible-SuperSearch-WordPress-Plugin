<?php
    // global $options, $bibles, $interfaces;
    global $BibleSuperSearch_Options;
    $api_version = (float) $BibleSuperSearch_Options->apiVersion();
    $statics = $BibleSuperSearch_Options->getStatics();
    list($api_ready, $api_checklist) = $BibleSuperSearch_Options->apiRequirementsCheck();


    if(!isset($statics['access'])) {
        $access_limit = '(unknown)';
        $access_hits = '(unknown)';
    } else {        
        $access_hits = (int)$statics['access']['hits'];

        if($statics['access']['limit'] == 0) {
            $access_limit = '(unlimited)';
            $access_rem = '(unlimited)';
        } else if($statics['access']['limit'] < 0) {
            $access_limit = '(!!NONE!!)';
            $access_rem = '(!!NONE!!)';
        } else { 
            $access_limit = (int)$statics['access']['limit'];
            $access_rem = $access_limit - $access_hits;
        }
    }
?>

<div class="biblesupersearch-option-tabs wrap">
    <div class="icon32" id="icon-options-general"><br></div>
    <h1><?php esc_html_e( 'Bible SuperSearch Documentation', 'biblesupersearch' ); ?></h1>
    <script>
        <?php
            echo "var bss_options=" . json_encode($options) . ";\n";
            echo "var bss_tab='" . $tab . "';\n";
        ?>
    </script>

    <div class="metabox-holder has-right-sidebar">
        <div class="inner-sidebar" style='margin-top: 44px'>
            <?php if($using_main_api): ?>
                <div class="postbox sm-box" style='background-color: #fffb17;'>
                    <h3 style='color: red'>Recommended Action: Install our API</h3>
                    <div class='inside'>
                        <ul>
                            <li>- Run Bible SuperSearch entirely on your website!</li>
                            <li>- Independant and decentralized.</li>
                            <li>- Import additional Bibles from third party sources.</li>
                            <li>- No usage limits.</li>
                            <!-- <li>- Build a mobile Bible app.</li> -->
                            <li>- <b>FREE</b> and Open Source.</li>
                        </ul>
                        <div style="text-align:center">
                            <a href="https://www.biblesupersearch.com/api"
                               target="_blank"
                               class="button-primary"><?php esc_html_e( 'More Info', 'biblesupersearch' ); ?></a>
                        </div>

                    </div>
                </div>
            <?php endif; ?>            

            <?php if(!$using_main_api && !$download_enabled): ?>
                <div class="postbox sm-box" style='background-color: #fffb17;'>
                    <h3 style='color: red'>Please Enable Bible Downloads in Your Bible SuperSearch API Options.</h3>
                    <div class='inside'>
                        You are using a third-party instance of the Bible SuperSearch API. &nbsp; However, Bible downloads is not enabled in the API. <br /><br />

                        Please enable Bible downloads in the API options.<br /><br />

                        Without downloads enabled, the following features will not work:

                        <ul>
                            <li>- Downloads page via the [biblesupersearch_downloads] short tag. </li>
                            <li>- Bible downloads dialog within the main Bible SuperSearch app (via [biblesupersearch] shortcode). </li>
                        </ul>
                        <div style="text-align:center">
                            <a href="<?php echo $options['apiUrl']; ?>/login"
                               target="_blank"
                               class="button-primary"><?php esc_html_e( 'Log In to API', 'biblesupersearch' ); ?></a>
                        </div>

                    </div>
                </div>
            <?php endif; ?>

            <div class="postbox sm-box">
                <h3><span><?php esc_html_e( 'Need Some Help?', 'biblesupersearch' ); ?></span>
                </h3>
                <div class="inside">
                    <p style="text-align:justify"><?php echo wp_sprintf( esc_html__( '%s today and get support from the developers who are building Bible SuperSearch.', 'biblesupersearch' ), '<a href="https://www.biblesupersearch.com/downloads" target="_blank">' . esc_html__( 'Sign up', 'biblesupersearch' ) . '</a>' ); ?></p>
                    <div style="text-align:center">
                        <a href="https://www.biblesupersearch.com/contact"
                           target="_blank"
                           class="button-secondary"><?php esc_html_e( 'Free&nbsp;Support', 'biblesupersearch' ); ?></a>&nbsp;
                        <a href="https://www.biblesupersearch.com/downloads"
                           class="button-primary"><?php esc_html_e( 'Priority&nbsp;Support', 'biblesupersearch' ); ?></a>
                    </div>
                    <div style="text-align:center;font-size:0.85em;padding:0.7rem 0 0">
                        <span><?php esc_html_e( 'Note: Free support is limited', 'biblesupersearch' ); ?></span>
                    </div>
                </div>
            </div>
            <div class="postbox sm-box">
                <h3>
                    <span><?php esc_html_e( 'Like our Software?  Please leave us a review and follow us!', 'biblesupersearch' ); ?></span>
                </h3>
                <div class="inside">
                    <div style="text-align:center">
                        <a href="https://www.facebook.com/bible.super.search/"
                           target="_blank"
                           class="button-secondary"><?php esc_html_e( 'Facebook', 'biblesupersearch' ); ?></a>

                        <a href="https://twitter.com/bibsupsearch"
                           target="_blank"
                           class="button-secondary"><?php esc_html_e( 'Twitter', 'biblesupersearch' ); ?></a>
                    </div>   
                    <br />                 
                    <div style="text-align:center">
                        <a href="https://wordpress.org/plugins/biblesupersearch/"
                           target="_blank"
                           class="button-secondary"><?php esc_html_e( 'WordPress', 'biblesupersearch' ); ?></a>
                        <a href="https://sourceforge.net/projects/biblesuper/reviews"
                           target="_blank"
                           class="button-secondary"><?php esc_html_e( 'SourceForge', 'biblesupersearch' ); ?></a>
                    </div>
                </div>
            </div>            

            <div class="postbox sm-box">
                <h3>
                    <span><?php esc_html_e( 'Help Us Make Bible SuperSearch Even Better!', 'biblesupersearch' ); ?></span>
                </h3>
                <div class="inside">
                    <p style="text-align:justify"><?php esc_html_e( 'If you have any ideas on how to improve Bible SuperSearch, please let us know!', 'biblesupersearch' ); ?></p>
                    <div style="text-align:center">
                        <a href="https://www.biblesupersearch.com/feature-request/"
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
                        <div class='inside'>
                            <table>
                                <tr><th colspan="2">Today's Bible SuperSearch API Usage</th></tr>
                                <tr><th>Daily hits</th><td><?php echo $access_hits; ?></td></tr>
                                <tr><th>Limit</th><td><?php echo $access_limit; ?></td></tr>
                                <tr><th>Remaining </th><td><?php echo $access_rem; ?></td></tr>
                            </table>
                        </div>

                        <?php if($using_main_api): ?>
                            <div class='inside' style='font-weight: bold'>
                                This plugin uses the Bible SuperSearch API. &nbsp;By installing, activating and using this plugin, you agree to the API
                                <a href='https://api.biblesupersearch.com/documentation#tab_tos' target='_NEW'>Terms of Service</a> and 
                                <a href='https://api.biblesupersearch.com/documentation#tab_privacy' target='_NEW'>Privacy Policy</a>. <br /><br />
                                Did you know that you can install our API on your server for FREE? &nbsp;Enjoy faster API speed and no usage limits.
                                
                                <?php if($api_ready): ?>
                                    Visit our downloads page for details: <a href='https://www.biblesupersearch.com/downloads' target='_NEW'>https://www.biblesupersearch.com/downloads</a>
                                <?php endif; ?>
                            </div>
                            <div class='inside' style='font-weight: bold'>
                                <?php if($api_ready): ?>
                                    Congratulations, your website meets the basic requirements to install the BibleSuperSearch API, see list below.
                                <?php else: ?>
                                    <span style='color: red'>Warning! &nbsp;Please resolve the following before attempting to install the BibleSuperSearch API. &nbsp;</span>

                                    You will need to have your webhost upgrade or enable the missing items. 
                                <?php endif; ?>
                                <br /><br />

                                <table>
                                    <?php foreach($api_checklist as $row): ?>
                                        <?php $rowcount ++; ?>

                                        <?php if($row['type'] == 'header'): ?>
                                            <tr><th colspan='2'><?php echo $row['label']; ?></th></tr>        
                                        <?php elseif($row['type'] == 'error'): ?>
                                            <tr><th colspan='2' class='bad'><?php echo $row['label']; ?></th></tr>
                                        <?php elseif($row['type'] == 'hr'): ?>
                                            <tr><td colspan='2'><hr /></td></tr>
                                        <?php else: ?>
                                            <tr <?php if($rowcount %2 == 0):?>class='zebra'<?php endif;?> >
                                                <td><?php echo $row['label']; ?></td>
                                                <?php if($row['success'] === NULL): ?>
                                                    <td class='ok'>Okay</td>
                                                <?php elseif($row['success'] == TRUE): ?>
                                                    <td class='good'>Good</td>
                                                <?php else: ?>
                                                    <td class='bad'>Bad</td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                </table>

                                <?php if($api_ready): ?>
                                    <br />Visit our downloads page for details: <a href='https://www.biblesupersearch.com/downloads' target='_NEW'>https://www.biblesupersearch.com/downloads</a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class='inside' style='font-weight: bold'>
                                Congratulations! &nbsp;You are successfully using this third party installation of the Bible SuperSearch API: <?php echo $options['apiUrl'] ?>
                            </div>
                        <?php endif; ?>
    
                        <?php if(!empty($reccomended_plugins)): ?>
                            <div class='inside'>
                                Bible SuperSearch recommends these plugins: <br /><br />
                                <ul>
                                    <?php foreach($reccomended_plugins as $p): ?>
                                        <li>
                                            <a href='plugin-install.php?tab=plugin-information&plugin=<?php echo $p['name']; ?>&TB_iframe=true&width=640&height=500' class='thickbox'><?php echo $p['label'] ?></a>
                                            &nbsp; <?php echo $p['description'] ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                        </div>
                        <?php endif; ?>

                        <?php require_once(dirname(__FILE__) . '/templates/options_docs_temp.php'); ?>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
