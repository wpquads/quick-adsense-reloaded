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
     if ( strpos($field['callback'],'header') !== false && !quads_is_excluded(array('help') ) ) { 
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
        echo '<tr class="row"><td class="row th" style="width:150px;vertical-align:top;">';
        //echo "<pre>";
        //var_dump($field);
        if (!empty($field['args']['label_for']))
            echo '<label for="' . esc_attr($field['args']['label_for']) . '">' . $field['title'] . '</label>';
        else
            echo '<div class="col-title">' . $field['title'] . '</div>';
        echo '</td>';
        echo '<td class="row th">';
        call_user_func($field['callback'], $field['args']);
        echo '</td></tr>';
        
        
    }
    echo '</tbody></table>';
    if ($header === true){
    echo '</div>';
    }
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
             <h1 style="text-align:center;"> <?php echo __('WP <strong>QUADS</strong> (Quick AdSense Reloaded) ', 'quick-adsense-reloaded') . QUADS_VERSION; ?></h1>
            <div class="about-text" style="font-weight: 400;line-height: 1.6em;text-align:center;">
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
			<form method="post" action="options.php">
				<?php
				settings_fields( 'quads_settings' );
				quads_do_settings_fields( 'quads_settings_' . $active_tab, 'quads_settings_' . $active_tab );
				?>
				<!--</table>-->
                                
                                <?php  settings_errors(); ?>
				<?php 
                                // do not show save button on add-on page
                                if ($active_tab !== 'addons')
                                    submit_button(); 
                                ?>
			</form>

                    </div> <!-- new //-->
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
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
