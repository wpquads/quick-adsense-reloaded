<?php
/**
 * Admin Options Page
 *
 * @package     QUADS
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Returns list elements for jQuery tab navigation 
 * based on header callback
 * 
 * @since 2.1.2
 * @todo Use sprintf to sanitize  $field['id'] instead using str_replace() Should be much faster? 
 * @return string
 */

function quads_get_tab_header($page, $section){
    global $quads_options;
    global $wp_settings_fields;
    
    if (!isset($wp_settings_fields[$page][$section]))
        return;
    
    echo '<ul>';
    foreach ((array) $wp_settings_fields[$page][$section] as $field) {  
    $sanitizedID = str_replace('[', '', $field['id'] );
    $sanitizedID = str_replace(']', '', $sanitizedID );     
     if ( strpos($field['callback'],'header') !== false && !quads_is_excluded(array('help', 'licenses') ) ) { 
         echo '<li class="quads-tabs"><a href="#' . $sanitizedID . '">' . $field['title'] .'</a></li>';
     }
    }
    echo '</ul>';
}

/**
 * Check if current page is excluded
 * 
 * @param array $pages
 * @return boolean
 */
function quads_is_excluded($pages){
    if (isset($_GET['tab'])){
        $currentpage = $_GET['tab'];
        if (isset($currentpage) && in_array($currentpage, $pages))
                return true;
    }
}

/**
 * Print out the settings fields for a particular settings section
 *
 * Part of the Settings API. Use this in a settings page to output
 * a specific section. Should normally be called by do_settings_sections()
 * rather than directly.
 *
 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
 * @return string
 *
 * @since 2.1.2
 *
 * @param string $page Slug title of the admin page who's settings fields you want to show.
 * @param section $section Slug title of the settings section who's fields you want to show.
 * 
 * Copied from WP Core 4.0 /wp-admin/includes/template.php do_settings_fields()
 * We use our own function to be able to create jQuery tabs with easytabs()
 * 
*  We dont use tables here any longer. Are we stuck in the nineties?
 * @todo Use sprintf to sanitize  $field['id'] instead using str_replace() Should be faster?
 * @todo some media queries for better responisbility
 */
function quads_do_settings_fields($page, $section) {
    global $wp_settings_fields;
    $header = false;
    $firstHeader = false;
    
    if (!isset($wp_settings_fields[$page][$section]))
        return;
    
    // Check first if any callback header registered
    foreach ((array) $wp_settings_fields[$page][$section] as $field) {
       strpos($field['callback'],'header') !== false ? $header = true : $header = false; 
       
       if ($header === true)
               break;
    }
    
    foreach ((array) $wp_settings_fields[$page][$section] as $field) {
        
       $sanitizedID = str_replace('[', '', $field['id'] );
       $sanitizedID = str_replace(']', '', $sanitizedID );
       
       // Check if header has been created previously
       if (strpos($field['callback'],'header') !== false && $firstHeader === false) { 
           
           echo '<div id="' . $sanitizedID . '">'; 
           echo '<table class="quads-form-table"><tbody>';
           $firstHeader = true;
           
       } elseif (strpos($field['callback'],'header') !== false && $firstHeader === true) { 
       // Header has been created previously so we have to close the first opened div
           echo '</table></div><div id="' . $sanitizedID . '">'; 
           echo '<table class="quads-form-table"><tbody>';  
       }  
        
        if (!empty($field['args']['label_for']) && !quads_is_excluded_title( $field['args']['id'] )){
            echo '<tr class="quads-row">';
            echo '<td class="quads-row th">';
            echo '<label for="' . esc_attr($field['args']['label_for']) . '">' . $field['title'] . '</label>';
            echo '</td></tr>';
        }else if (!empty($field['title']) && !quads_is_excluded_title( $field['args']['id'] ) && !empty($field['args']['helper-desc'])){
            echo '<tr class="quads-row">';
            echo '<td class="quads-row th">';
            echo '<div class="col-title">' . $field['title'] . '<a class="quads-general-helper" href="#"></a><div class="quads-message">' . $field['args']['helper-desc']. '</div></div>';
            echo '</td></tr>';
        }else if (!empty($field['title']) && !quads_is_excluded_title( $field['args']['id'] ) ){
            echo '<tr class="quads-row">';
            echo '<td class="quads-row th">';
            echo '<div class="col-title">' . $field['title'] . '</div>';
            echo '</td></tr>';
        }
        
        else {
            echo '';
        }

        
        echo '<tr><td>';
            call_user_func($field['callback'], $field['args']);
        echo '</td></tr>';  
    }
    echo '</tbody></table>';
    if ($header === true){
    echo '</div>';
    }
}

/**
 * If title is one of these entries do not show it
 */
function quads_is_excluded_title($string){
    $haystack = array('ad1','ad2','ad3','ad4','ad5','ad6','ad7','ad8','ad9','ad10', 
        'ad1_widget',
        'ad2_widget',
        'ad3_widget',
        'ad4_widget',
        'ad5_widget',
        'ad6_widget',
        'ad7_widget',
        'ad8_widget',
        'ad9_widget',
        'ad10_widget',
        'vi_header',
        'vi_signup'
        );

    if (in_array($string, $haystack)){
            return true;
    }
    return false;
}

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0
 * @global $quads_options Array of all the QUADS Options
 * @return void
 */
function quads_options_page() {
	global $quads_options;

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], quads_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';

	ob_start();
	?>
	<div class="wrap quads_admin">
             <h1 style="text-align:center;"> <?php echo QUADS_NAME . ' ' . QUADS_VERSION; ?></h1>
             <div class="about-text" style="font-weight: 400;line-height: 1.6em;text-align:center;">
                <div class='quads-share-button-container'>
                    <div class='quads-share-button quads-share-button-twitter' data-share-url="https://wordpress.org/plugins/quick-adsense-reloaded">
                        <div clas='box'>
                            <a href="https://twitter.com/share?url=http://wpquads.com&text=WPQUADS+-+The quickest+and+most+easiest+way+to+integrate+AdSense+into+WordPress+websites+@wpquads" target='_blank'>
                                <span class='quads-share'><?php echo __('Shout out a tweet','quick-adsense-reloaded'); ?></span>
                            </a>
                        </div>
                    </div>
                    <div class="quads-share-button quads-share-button-facebook" data-share-url="https://wordpress.org/plugins/quick-adsense-reloaded">
                        <div class="box">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=http://wpquads.com" target="_blank">
                                <span class='quads-share'><?php echo __('Share on Facebook','quick-adsense-reloaded'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
		<h2 class="nav-tab-wrapper">
			<?php
			foreach( quads_get_settings_tabs() as $tab_id => $tab_name ) {

				$tab_url = esc_url(add_query_arg( array(
					'settings-updated' => false,
					'tab' => $tab_id
				) ));

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h2>
		<div id="quads_tab_container" class="quads_tab_container">
                        <?php quads_get_tab_header( 'quads_settings_' . $active_tab, 'quads_settings_' . $active_tab ); ?>   
                    <div class="quads-panel-container"> <!-- new //-->
			<form method="post" action="options.php" id="quads_settings">
                            
				<?php
				settings_fields( 'quads_settings' );
				quads_do_settings_fields( 'quads_settings_' . $active_tab, 'quads_settings_' . $active_tab );
				?>                                
                                <?php  settings_errors(); ?>
				<?php 
                                // do not show save button on add-on page
                                if ($active_tab !== 'addons' && $active_tab !== 'imexport' && $active_tab !== 'help'){
                                    $other_attributes = array( 'id' => 'quads-submit-button' );
                                    submit_button(null, 'primary', 'quads-save-settings' , true, $other_attributes ); 
                                    if ($active_tab !== 'licenses'){
                                    ?>
                                    <!--<a href="<?php //echo admin_url() . '/admin.php?page=quads-settings&quads-action=validate'; ?> " id="quads-validate"><?php //_e('Validate Ad Settings','quick-adsense-reloaded')?></a>//-->
                                <?php
                                }
                                
                                    }
                                ?>
			</form>
                        <div id="quads-footer">
                        <?php
                        
                        if ($active_tab !== 'addons' && $active_tab !== 'licenses'){
                        echo sprintf( __( '<strong>If you like this plugin please do us a BIG favor and give us a 5 star rating <a href="%s" target="_blank">here</a> . If you have issues, open a <a href="%2s" target="_blank">support ticket</a>, so that we can sort it out. Thank you!</strong>', 'quick-adsense-reloaded' ),
                           'https://wordpress.org/support/plugin/quick-adsense-reloaded/reviews/#new-post',
                           'http://wpquads.com/support/'
                        );
                        echo '<br/><br/>' . sprintf( __( '<strong>Ads are not showing? Read the <a href="%s" target="_blank">troubleshooting guide</a> to find out how to resolve it.<p> Looking for a quick way to clone your WordPress? Try the free plugin <a href="%s" target="_blank">WP Staging</a>.', 'quick-adsense-reloaded' ),
			'http://wpquads.com/docs/adsense-ads-are-not-showing/?utm_source=plugin&utm_campaign=wpquads-settings&utm_medium=website&utm_term=bottomlink',
                     'https://wp-staging.com/?utm_source=wpquads_plugin&utm_campaign=footer&utm_medium=website&utm_term=bottomlink'
                        );
                        }
                        ?>
                        </div>
                    </div> <!-- new //-->
                    <?php quads_get_advertising(); ?>
		</div><!-- #tab_container-->
                <div id="quads-save-result"></div>
                <div class="quads-admin-debug"><?php echo quads_get_debug_messages(); ?></div>
                <?php echo quads_render_adsense_form(); ?>
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}

function quads_get_debug_messages(){
    global $quads_options;
    
    if (isset($quads_options['debug_mode'])){
        echo '<pre style="clear:both;">';
        var_dump($quads_options);
        echo '</pre>';
    }
}

/**
 * Render ad and return it when plugin is not pro version
 * @return string
 */
function quads_get_advertising() {
    
    if (  quads_is_extra() ){
        return '';
    }
    ob_start();
            ?>
    <div class="quads-panel-sidebar" style="float:left;min-width: 301px;margin-left: 1px;margin-top:0px;">
        <a href="http://wpquads.com/?utm_source=wpquads&utm_medium=banner&utm_term=click-quads&utm_campaign=wpquads" target="_blank">
            <img src="<?php echo QUADS_PLUGIN_URL . '/assets/images/quads_banner_250x521_buy.png'; ?>">
        </a>
        <br>
        <a style="display:block;" href="http://demo.clickfraud-monitoring.com/?utm_source=wpquads&utm_medium=banner&utm_term=click-cfm&utm_campaign=wpquads" target="_blank">
            <img src="<?php echo QUADS_PLUGIN_URL . '/assets/images/banner_250x296-cfm.png'; ?>">
        </a>
    </div>
    <?php
    echo ob_get_clean();
}

/**
 * Render social buttons
 * 
 * @return void
 */
function quads_render_social(){
    ob_start()?>
        
        <div class='quads-share-button-container'>
                        <div class='quads-share-button quads-share-button-twitter' data-share-url="https://wordpress.org/plugins/quick-adsense-reloaded">
                            <div clas='box'>
                                <a href="https://twitter.com/share?url=https://wordpress.org/plugins/quick-adsense-reloaded&text=Quick%20AdSense%20reloaded%20-%20a%20brand%20new%20fork%20of%20the%20popular%20AdSense%20Plugin%20Quick%20Adsense!" target='_blank'>
                                    <span class='quads-share'><?php echo __('Tweet','quick-adsense-reloaded'); ?></span>
                                </a>
                            </div>
                        </div>

                        <div class="quads-share-button quads-share-button-facebook" data-share-url="https://wordpress.org/plugins/quick-adsense-reloaded">
                            <div class="box">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=https://wordpress.org/plugins/quick-adsense-reloaded" target="_blank">
                                    <span class='quads-share'><?php echo __('Share','quick-adsense-reloaded'); ?></span>
                                </a>
                            </div>
                        </div>
            </div>
        
        <?php
        echo ob_get_clean();
}


/**
 * Render AdSense Form
 */
function quads_render_adsense_form(){
    
?>
<div id="quads-adsense-bg-div" style="display: none;">
    <div id="quads-adsense-container">
        <h3><?php _e( 'Enter <a ahref="https://wpquads.com/docs/how-to-create-and-where-to-get-adsense-code/" target="_blank">AdSense text & display ad code</a> here', 'quick-adsense-reloaded' ); ?></h3>
        <?php _e('Do not enter <a href="https://wpquads.com/docs/integrate-page-level-ads-wordpress/" target="_blank">AdSense page level ads</a> or <a href="https://wpquads.com/introducing-new-adsense-auto-ads/" target="_blank">Auto ads!</a> <br> <a href="https://wpquads.com/docs/how-to-create-and-where-to-get-adsense-code/" target="_blank">Learn how to create AdSense ad code</a>', 'quick-adsense-reloaded'); ?>
        <textarea rows="15" cols="55" id="quads-adsense-form"></textarea><hr />
        <button class="button button-primary" id="quads-paste-button"><?php _e( 'Get Code', 'quick-adsense-reloaded' ); ?></button>&nbsp;&nbsp;
        <button class="button button-secondary" id="quads-close-button"><?php _e( 'Close', 'quick-adsense-reloaded' ); ?></button>
        <div id="quads-msg"></div>
        <input type="hidden" id="quads-adsense-id" value="">
    </div>
</div>
<?php
}
