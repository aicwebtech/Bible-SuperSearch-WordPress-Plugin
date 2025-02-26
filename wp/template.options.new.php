<?php
    // global $options, $bibles, $interfaces;
    global $BibleSuperSearch_Options;

    $api_version = (float) $BibleSuperSearch_Options->apiVersion();
    $statics = $BibleSuperSearch_Options->getStatics();

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

<script>
    var biblesupersearch_config_bootstrap = <?php echo json_encode($bootstrap); ?>;
</script>


<div class="biblesupersearch-option-tabs wrap">
    <h1><?php esc_html_e( 'Bible SuperSearch Options', 'biblesupersearch' ); ?></h1>

    <div class="metabox-holder has-right-sidebar">
        <!-- :todo rebuild side bar into vue.js -->
        <div class="inner-sidebar" style='margin-top: 48px; max-width: 281px; width: 100%'>
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
                <!-- tab container -->
            </div>

            <div id="post-body-content">
                <div id='bss_config_app'></div>
            </div>
        </div>
    </div>
</div>