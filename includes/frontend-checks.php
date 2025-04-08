<?php
/**
 * Helper Functions
 *
 * @package     QUADS
 * @subpackage  INCLUDES
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @author René Hermenau
 * @author Thank you Thomas Maier (advanced ads) for that great inspiration. I borrowed some code. Hope you dont mind:)
 * @since       1.4.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

function quads_frontend_checks_init() {
    if( !is_admin() && is_admin_bar_showing() && current_user_can( 'update_plugins' )
    ) {
        add_action( 'admin_bar_menu', 'quads_add_admin_bar_menu', 1000 );
        global $wp_version;
        if (version_compare( $wp_version, '5.4', '>=') ){
             if( array_key_exists( 'the_content' , $GLOBALS['wp_filter']) ) {
                global $the_content;
                $the_content = true;
            }
        }else {
           add_filter( 'the_content', 'quads_check_the_content_filter' );
        }
        add_filter( 'wp_footer', 'quads_check_adblocker', -101 );
        add_filter( 'quads-ad-output', 'after_ad_output', 10, 2 );
    }
}

add_action( 'init', 'quads_frontend_checks_init' );

/**
 * Create WP QUADS List possible error cases in the admin-bar.
 *
 * @param obj $wp_admin_bar WP_Admin_Bar
 */
function quads_add_admin_bar_menu( $wp_admin_bar ) {
    global $quads_options, $wp_the_query, $post, $wp_scripts, $the_content;

    $error = false;

    $wp_admin_bar->add_node( array(
        'id' => 'quads_ad_check',
        'title' => __( 'Ad Check', 'quick-adsense-reloaded' ),
    ) );

    // Hidden by default
    $wp_admin_bar->add_node( array(
        'parent' => 'quads_ad_check',
        'id' => 'quads_ad_check_jquery',
        'title' => __( '- JavaScript / jQuery error', 'quick-adsense-reloaded' ),
        'href' => 'https://wpquads.com/docs/javascript-issues-breaking-adsense-ads/',
        'meta' => array(
            'class' => 'quads-hidden quads_ad_check_warning',
            'target' => '_blank'
        )
    ) );

    // Hidden by default
    $wp_admin_bar->add_node( array(
        'parent' => 'quads_ad_check',
        'id' => 'quads_ad_check_adblocker_enabled',
        'title' => __( '- Ad blocker enabled', 'quick-adsense-reloaded' ),
        'meta' => array(
            'class' => 'quads-hidden quads_ad_check_warning',
        )
    ) );
    // Hidden by default
    if( $wp_the_query->is_singular() ) {
        // Check if the_content filter is available
        if( !$the_content ) {
            $wp_admin_bar->add_node( array(
                'parent' => 'quads_ad_check',
                'id' => 'quads_ad_check_the_content_not_invoked',
                'title' => __( '- <em>the_content</em> filter does not exist', 'quick-adsense-reloaded' ),
                'href' => 'http://wpquads.com/docs/the_content-filter-missing/',
                'meta' => array(
                    'class' => 'quads_ad_check_warning',
                    'target' => '_blank'
                )
            ) );
            $error = true;
        }
        // Hidden by default
        if( !empty( $post->ID ) ) {
            $ad_settings = get_post_meta( $post->ID, '_quads_config_visibility', true );

            if( !empty( $ad_settings['NoAds'] ) ) {
                $wp_admin_bar->add_node( array(
                    'parent' => 'quads_ad_check',
                    'id' => 'quads_ad_check_disabled_on_page',
                    'title' => __( '- All Ads are disabled on this page', 'quick-adsense-reloaded' ),
                    'href' => get_edit_post_link( $post->ID ) . '#quads-ad-settings',
                    'meta' => array(
                        'class' => 'quads_ad_check_warning',
                        'target' => '_blank'
                    )
                ) );
                $error = true;
            }
            if( !empty( $ad_settings['OffDef'] ) ) {
                $wp_admin_bar->add_node( array(
                    'parent' => 'quads_ad_check',
                    'id' => 'quads_ad_check_disabled_in_content',
                    'title' => __( '- Default Ads disabled in content of this page', 'quick-adsense-reloaded' ),
                    'href' => get_edit_post_link( $post->ID ) . '#quads-ad-settings',
                    'meta' => array(
                        'class' => 'quads_ad_check_warning',
                        'target' => '_blank'
                    )
                ) );
                $error = true;
            }
        } else {
            $wp_admin_bar->add_node( array(
                'parent' => 'quads_ad_check',
                'id' => 'quads_ad_check_post_zero',
                'title' => __( '- Current post ID is 0 ', 'quick-adsense-reloaded' ),
                'href' => 'https://wpadvancedads.com/manual/known-plugin-conflicts/#frontend-issue-post-id-empty',
                'meta' => array(
                    'class' => 'quads_ad_check_warning',
                    'target' => '_blank'
                )
            ) );
            $error = true;
        }
    }

    if( $wp_the_query->is_404() && !empty( $quads_options['disabled-ads']['404'] ) ) {
        $wp_admin_bar->add_node( array(
            'parent' => 'quads_ad_check',
            'id' => 'quads_ad_check_no_404',
            'title' => __( 'Ads are disabled on 404 pages', 'quick-adsense-reloaded' ),
            'href' => admin_url( 'admin.php?page=quads-settings' ),
            'meta' => array(
                'class' => 'quads_ad_check_warning',
                'target' => '_blank'
            )
        ) );
        $error = true;
    }

    if( !$wp_the_query->is_singular() && !empty( $quads_options['disabled-ads']['archives'] ) ) {
        $wp_admin_bar->add_node( array(
            'parent' => 'quads_ad_check',
            'id' => 'quads_ad_check_no_archive',
            'title' => __( 'Ads are disabled on non singular pages', 'quick-adsense-reloaded' ),
            'href' => admin_url( 'admin.php?page=quads-settings' ),
            'meta' => array(
                'class' => 'quads_ad_check_warning',
                'target' => '_blank'
            )
        ) );
        $error = true;
    }

    if( !extension_loaded( 'dom' ) ) {
        $wp_admin_bar->add_node( array(
            'parent' => 'quads_ad_check',
            'id' => 'quads_ad_check_no_dom_document',
            'title' => sprintf( /* translators: %s: DOM extension */ __( 'The %s extension(s) is not loaded', 'quick-adsense-reloaded' ), 'dom' ),
            'href' => 'http://php.net/manual/en/book.dom.php',
            'meta' => array(
                'class' => 'quads_ad_check_warning',
                'target' => '_blank'
            )
        ) );
        $error = true;
    }

    if( !$error ) {
        $wp_admin_bar->add_node( array(
            'parent' => 'quads_ad_check',
            'id' => 'quads_ad_check_fine',
            'title' => __( 'WP QUADS is working fine', 'quick-adsense-reloaded' ),
            'href' => false,
  
        ) );
    }

    $wp_admin_bar->add_node( array(
        'parent' => 'quads_ad_check',
        'id' => 'quads_ad_check_debug_dfp',
        'title' => __( 'debug DFP ads', 'quick-adsense-reloaded' ),
        'href' => esc_url( add_query_arg( 'googfc', '' ) ),
        'meta' => array(
            'class' => 'quads-hidden quads_ad_check_debug_dfp_link',
            'target' => '_blank',
        )
    ) );

    $wp_admin_bar->add_node( array(
        'parent' => 'quads_ad_check',
        'id' => 'quads_ad_check_highlight_ads',
        'title' => sprintf( '<label><input id="quads_highlight_ads_checkbox" type="checkbox"> %s</label>', __( 'Show Adverts', 'quick-adsense-reloaded' ) )
    ) );
}

/**
 * Set variable to 'true' when 'the_content' filter is available.
 *
 * @param string $content
 * @return string $content
 */
function quads_check_the_content_filter( $content ) {
    global $the_content;

    $the_content = true;

    return $content;
}
function quads_disable_add_subscribe() {
    if(quads_disable_ads() || is_404()){
        return false;
    }
    if(isset($_COOKIE['disable_ad_notice'])){
        return false;
    }
    $quads_settings = get_option('quads_settings');
    $disableads = isset($quads_settings['disableads']) ? $quads_settings['disableads'] : '';
    $da_page_id = isset($quads_settings['dapayment_page']) ? $quads_settings['dapayment_page'] : 0;
    $payment_page = get_permalink( $da_page_id );
    
    if($disableads==1 && get_the_ID()!=$da_page_id){
?>
<style>
    .disable-ads-container{background: unset;
    bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    left: 0;
    max-width: 100%;
    position: fixed;
    right: 0;
    width: 100%;
    z-index: 10;}
    .da-top-flexbox{
        border-top: 5px solid #D00;align-items: center;
    background: #2F2E2E;
    color: #FFF;
    display: flex;
    flex-direction: row;
    font-family: 'Work Sans', sans-serif;
    font-size: 18px;
    font-weight: normal;
    justify-content: center;
    padding: 13px;
    text-align: center;
    }
    .da-close-button{
        position: absolute;
    right: 13px;background-color: rgba(0, 0, 0, 0);
    border: 0;
    box-shadow: none;
    color: inherit;
    margin: 0;
    padding: 0;
    text-decoration: none;
    text-shadow: none;
    }
    .da-bottom-flexbox{
        align-items: center;
    background: #181716;
    display: flex;
    flex-direction: row;
    justify-content: center;
    padding: 12px;
    text-align: center;
    }
    .da-lead{
        color: #FFF;
    font-family: 'Work Sans', sans-serif;
    font-size: 16px;
    font-weight: bold;
    line-height: 24px;
    text-align: center;
    }
    .da-sub-lead{
        color: #FFF;
    font: 300 16px / 24px 'Work Sans', sans-serif;
    margin-left: 5px;
    }
    .da-meter-cta{
        align-items: center;
        background-color: #DC0000;
        border: none;
        border-radius: 8px;
        color: #FFF;
        display: inline-flex;
        font: 600 14px / 22px 'Work Sans', sans-serif;
        height: 48px;
        justify-content: center;
        letter-spacing: -0.2px;
        margin-left: 20px;
        outline: none;
        padding: 0 32px;
        text-align: center;
        text-decoration: none;
        white-space: nowrap;
    }
</style>
<div class="disable-ads-container" id="disable-ads-block">
	<div class="da-top-flexbox" style="">
		<div class="meter-display">
            Hide Ads for Premium Members by Subscribing
		</div>
		<button class="da-close-button" style="cursor:pointer" onclick="handleCloseDisableAd()">
			<svg width="16" height="16" viewBox="0 0 16 16" style="margin-left: 13px;
    margin-right: 13px;" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M16.0001 1.23076L14.7693 0L8.00004 6.76927L1.23077 2.85321e-05L0 1.23079L6.76927 8.00003L0.000110543 14.7692L1.23088 15.9999L8.00004 9.23079L14.7692 16L16 14.7692L9.23081 8.00003L16.0001 1.23076Z" fill="#F8F8F8"></path>
			</svg>
		</button>
	</div>

	<div class="da-bottom-flexbox">
		<div class="message">
			<span class="da-lead">Hide Ads for Premium Members.</span>
			<span class="da-sub-lead">Hide Ads for Premium Members by clicking on subscribe button.</span>
		</div>
		<a class="da-meter-cta" href="<?php echo esc_url( $payment_page );?>" target="_blank">Subscribe Now</a>
	</div>
</div>
<script>
    function handleCloseDisableAd(){
        document.getElementById('disable-ads-block').style.display = 'none';
        var expires = (new Date(Date.now()+ 86400*1000)).toUTCString();
        document.cookie = "disable_ad_notice=true; expires=" + expires + ";path=/;"
    }
</script>
<?php
    }
}
add_action( 'wp_footer', 'quads_disable_add_subscribe' );
/**
 * Check conditions and display warning. Conditions: AdBlocker enabled, jQuery is included in header
 */
function quads_check_adblocker() {
    ?>
    <!--noptimize--><style>.quads-hidden { display: none; } .quads-adminbar-is-warnings { background: #abc116 ! important; color: #fff !important; }
        .quads-highlight-ads { outline:6px solid #83c11f !important; }#wp-admin-bar-quads_ad_check_highlight_ads label {color:#b4b9be !important;}</style>
        <?php if(!quads_is_amp_endpoint()){ ?>
    <script>
        (function (d, w) {
           

            var addEvent = function (obj, type, fn) {
                if (obj.addEventListener)
                    obj.addEventListener(type, fn, false);
                else if (obj.attachEvent)
                    obj.attachEvent('on' + type, function () {
                        return fn.call(obj, window.event);
                    });
            };

            function highlight_ads() {
                try {
                    var ad_wrappers = document.querySelectorAll('div[id^="quads-ad"]')
                } catch (e) {
                    return;
                }
                for (i = 0; i < ad_wrappers.length; i++) {
                    if (this.checked) {
                        ad_wrappers[i].className += ' quads-highlight-ads';
                    } else {
                        ad_wrappers[i].className = ad_wrappers[i].className.replace('quads-highlight-ads', '');
                    }
                }
            }

            addEvent(w, 'load', function () {
                var adblock_item = d.getElementById('wp-admin-bar-quads_ad_check_adblocker_enabled'),
                        jQuery_item = d.getElementById('wp-admin-bar-quads_ad_check_jquery'),
                        fine_item = d.getElementById('wp-admin-bar-quads_ad_check_fine'),
                        hide_fine = false;

                var highlight_checkbox = d.getElementById('quads_highlight_ads_checkbox');
                if (highlight_checkbox) {
                    addEvent(highlight_checkbox, 'change', highlight_ads);
                }
                if (adblock_item && typeof wpquads_adblocker_check === 'undefined' || false === wpquads_adblocker_check) {
                    // show quads-hidden item
                    adblock_item.className = adblock_item.className.replace(/quads-hidden/, '');
                    hide_fine = true;
                }


                if (hide_fine && fine_item) {
                    fine_item.className += ' quads-hidden';
                }

                showCount();
            });

            var showCount = function () {
                try {
                    // select not quads-hidden warning items, exclude the 'fine_item'
                    var warning_count = document.querySelectorAll('.quads_ad_check_warning:not(.quads-hidden)').length;
                } catch (e) {
                    return;
                }

                if (warning_count) {
                    var header = document.querySelector('#wp-admin-bar-quads_ad_check > div');

                    if (header) {
                        header.innerHTML += ' <i>(' + warning_count + ')</i>';
                        header.className += ' quads-adminbar-is-warnings';
                    }
                }
            };
        })(document, window);
    </script><!--/noptimize-->
    <?php }
}
