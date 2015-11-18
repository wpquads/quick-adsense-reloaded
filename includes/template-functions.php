<?php
/**
 * Template Functions
 *
 * @package     QUADS
 * @subpackage  Functions/Templates
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



/* Load Hooks
 * @since 2.0
 * return void
 */

add_shortcode('quadsshare', 'quadsshareShortcodeShow');
add_filter('the_content', 'quadsshare_filter_content', getExecutionOrder(), 1);
add_filter('widget_text', 'do_shortcode');
add_action('quadsshare', 'quadsshare');
add_filter('quads_share_title', 'quads_get_title', 10, 2);


// uncomment for debugging
//global $wp_filter; 
//print_r($wp_filter['the_content']);

/* Get Execution order of injected Share Buttons in $content 
 *
 * @since 2.0.4
 * @return int
 */

function getExecutionOrder(){
    global $quads_options;
    isset($quads_options['execution_order']) && is_numeric($quads_options['execution_order']) ? $priority = trim($quads_options['execution_order']) : $priority = 1000;
    return $priority;
}
    
/* Creates some shares for older posts which has been already 
 * shared dozens of times
 * This smooths the Velocity Graph Add-On if available
 * 
 * @since 2.0.9
 * @return int
 * @deprecated deprecated since version 2.2.8
 */

function quadssbSmoothVelocity($quadssbShareCounts) {
    switch ($quadssbShareCounts) {
        case $quadssbShareCounts >= 1000:
            $quadssbShareCountArr = array(100, 170, 276, 329, 486, 583, 635, 736, 875, $quadssbShareCounts);
            return $quadssbShareCountArr;
            break;
        case $quadssbShareCounts >= 600:
            $quadssbShareCountArr = array(75, 99, 165, 274, 384, 485, 573, $quadssbShareCounts);
            return $quadssbShareCountArr;
            break;
        case $quadssbShareCounts >= 400:
            $quadssbShareCountArr = array(25, 73, 157, 274, 384, 399, $quadssbShareCounts);
            return $quadssbShareCountArr;
            break;
        case $quadssbShareCounts >= 200:
            $quadssbShareCountArr = array(52, 88, 130, 176, 199, $quadssbShareCounts);
            return $quadssbShareCountArr;
            break;
        case $quadssbShareCounts >= 100:
            $quadssbShareCountArr = array(23, 54, 76, 87, 99, $quadssbShareCounts);
            return $quadssbShareCountArr;
            break;
        case $quadssbShareCounts >= 60:
            $quadssbShareCountArr = array(2, 10, 14, 18, 27, 33, 45, 57, $quadssbShareCounts);
            return $quadssbShareCountArr;
            break;
        case $quadssbShareCounts >= 20:
            $quadssbShareCountArr = array(2, 5, 7, 9, 9, 10, 11, 13, 15, 20, $quadssbShareCounts);
            return $quadssbShareCountArr;
            break;
        case $quadssbShareCounts == 0:
            $quadssbShareCountArr = array(0);
            return $quadssbShareCountArr;
            break;
        default:
            $quadssbShareCountArr = array(0);
            return $quadssbShareCountArr;
    }
}

/* Get quadssbShareObject 
 * depending if quadsEngine or sharedcount.com is used
 * 
 * @since 2.0.9
 * @return object
 * @changed 2.2.7
 */

function quadssbGetShareObj($url) {
    global $quads_options;
    $quadsengine = isset($quads_options['quads_sharemethod']) && $quads_options['quads_sharemethod'] === 'quadsengine' ? true : false;
    if ($quadsengine) {
        if(!class_exists('RollingCurlX'))  
        require_once quads_PLUGIN_DIR . 'includes/libraries/RolingCurlX.php';
        if(!class_exists('quadsengine'))         
            require_once(quads_PLUGIN_DIR . 'includes/quadsengine.php');
        $quadssbSharesObj = new quadsengine($url);
        return $quadssbSharesObj;
    } 
        require_once(quads_PLUGIN_DIR . 'includes/sharedcount.class.php');
        $quadssbSharesObj = new quadssbSharedcount($url);
        return $quadssbSharesObj;   
}

/* Get the correct share method depending if quadsshare networks is enabled
 * 
 * @since 2.0.9
 * @return var
 * 
 */

/* Get the sharecounts from sharedcount.com or quadsEngine
 * Creates the share count cache using post_meta db fields.
 * 
 * @since 2.0.9
 * @returns int
 */

function quadssbGetShareMethod($quadssbSharesObj) {
    if (class_exists('Quick AdSense ReloadedNetworks')) {
        $quadssbShareCounts = $quadssbSharesObj->getAllCounts();
        return $quadssbShareCounts;
    } 
        $quadssbShareCounts = $quadssbSharesObj->getFBTWCounts();
        return $quadssbShareCounts;
}

/**
 * Get share count for all pages where $post is empty. E.g. category or blog list pages
 * Uses transient 
 * 
 * @param string $url
 * @param in $cacheexpire
 * @returns integer $shares
 */
/*function quadssbGetNonPostShares($url, $cacheexpire) {
    // Get any existing copy of our transient data
    if (false === ( $non_post_shares = get_transient('non_post_shares') )) {
        // It wasn't there, so regenerate the data and save the transient
        // Get the share Object
        $quadssbSharesObj = quadssbGetShareObj($url);
        // Get the share counts
        $quadssbShareCounts = quadssbGetShareMethod($quadssbSharesObj);
        $transient_name = md5($url);
        // Set the transient
        set_transient('$transient_name', $quadssbShareCounts, $cacheexpire);
    } else {
        $shares = get_transient('non_post_shares');
    }
    if (is_numeric($shares)){    
        return $shares;
        quadsdebug()->info('Share count where $post is_null(): ' . $shares);
    }
}*/

/*
 * Return the share count
 * 
 * @param string url of the page the share count is collected for
 * @returns int
 */
function getSharedcount($url) {
    global $wpdb, $quads_options, $post;
    
    if (is_null($post)) {
    	return apply_filters('filter_get_sharedcount', 0);
    }
    
    isset($quads_options['quickads_cache']) ? $cacheexpire = $quads_options['quickads_cache'] : $cacheexpire = 300;
    /* make sure 300sec is default value */
    $cacheexpire < 300 ? $cacheexpire = 300 : $cacheexpire;

    if (isset($quads_options['disable_cache'])) {
        $cacheexpire = 5;
    }
    
    /* Bypass next lines and return share count for pages with empty $post object
       share count for pages where $post is empty. E.g. category or blog list pages
       Otherwise share counts are requested with every page load 
     *      */
    /*if (is_null($post)) {
    	return apply_filters('filter_get_sharedcount', quadssbGetNonPostShares($url, $cacheexpire));
    }*/
    
    
    $quadssbNextUpdate = (int) $cacheexpire;
    $quadssbLastUpdated = get_post_meta($post->ID, 'quads_timestamp', true);

    if (empty($quadssbLastUpdated)) {
        $quadssbCheckUpdate = true;
        $quadssbLastUpdated = 0;
    }

    if ($quadssbLastUpdated + $quadssbNextUpdate <= time()) {
        quadsdebug()->info("First Update - Frequency: " . $quadssbNextUpdate . " Next update: " . date('Y-m-d H:i:s', $quadssbLastUpdated + $quadssbNextUpdate) . " last updated: " . date('Y-m-d H:i:s', $quadssbLastUpdated) . " Current time: " . date('Y-m-d H:i:s', time()));
        // Get the share Object
        $quadssbSharesObj = quadssbGetShareObj($url);
        // Get the share counts
        $quadssbShareCounts = quadssbGetShareMethod($quadssbSharesObj);
        //$quadssbShareCounts = new stdClass(); // USE THIS FOR DEBUGGING
        //$quadssbShareCounts->total = 13; // USE THIS FOR DEBUGGING
        $quadssbStoredDBMeta = get_post_meta($post->ID, 'quads_shares', true);
        // Write timestamp
        update_post_meta($post->ID, 'quads_timestamp', time());

        /* Update post_meta only when API is requested and
         * API share count is greater than real fresh requested share count ->
         * ### This meas there is an error in the API (Failure or hammering any limits, e.g. X-Rate-Limit) ###
         */

        if ($quadssbShareCounts->total >= $quadssbStoredDBMeta) {
            update_post_meta($post->ID, 'quads_shares', $quadssbShareCounts->total);
            update_post_meta($post->ID, 'quads_jsonshares', json_encode($quadssbShareCounts));
            quadsdebug()->info("updated database with share count: " . $quadssbShareCounts->total);
            /* return counts from getAllCounts() after DB update */
            return apply_filters('filter_get_sharedcount', $quadssbShareCounts->total + getFakecount());
        }
        /* return previous counts from DB Cache | this happens when API has a hiccup and does not return any results as expected */
        return apply_filters('filter_get_sharedcount', $quadssbStoredDBMeta + getFakecount());
    } else {
        /* return counts from post_meta plus fake count | This is regular cached result */
        $cachedCountsMeta = get_post_meta($post->ID, 'quads_shares', true);
        $cachedCounts = $cachedCountsMeta + getFakecount();
        quadsdebug()->info("Cached result - Frequency: " . $quadssbNextUpdate . " Next update: " . date('Y-m-d H:i:s', $quadssbLastUpdated + $quadssbNextUpdate) . " last updated: " . date('Y-m-d H:i:s', $quadssbLastUpdated) . " Current time: " . date('Y-m-d H:i:s', time()));
        return apply_filters('filter_get_sharedcount', $cachedCounts);
    }
}

function quads_subscribe_button(){
        global $quads_options;
        if ($quads_options['networks'][2]){
            $subscribebutton = '<a href="javascript:void(0)" class="quadsicon-subscribe" id="quads-subscribe-control"><span class="icon"></span><span class="text">' . __('Subscribe', 'quads') . '</span></a>';
        } else {
            $subscribebutton = '';    
        }
         return apply_filters('quads_filter_subscribe_button', $subscribebutton );
    }
    
    /* Put the Subscribe container under the share buttons
     * @since 2.0.0.
     * @return string
     */
    
    function quads_subscribe_content(){
        global $quads_options;
        if ($quads_options['networks'][2] && $quads_options['subscribe_behavior'] === 'content'){ //Subscribe content enabled
            $container = '<div class="quads-toggle-container">' . quads_cleanShortcode('quadsshare', $quads_options['subscribe_content']). '</div>';
        } else {
            $container = '';    
        }
         return apply_filters('quads_toggle_container', $container);
    }
    
    
   /* Check if [quadsshare] shortcode is used in subscribe field and deletes it
    * Prevents infinte loop
    * 
    * @since 2.0.9
    * @return string / shortcodes parsed
    */
    
    function quads_cleanShortcode($code, $content){
       global $shortcode_tags;
        $stack = $shortcode_tags;
        $shortcode_tags = array($code => 1);
        $content = strip_shortcodes($content);
        $shortcode_tags = $stack;
        
        return do_shortcode($content);  
    }
        
   
    
    
    /* Round the totalshares
     * 
     * @since 1.0
     * @return string
     */
    
    function roundshares($totalshares){           
         if ($totalshares > 1000000) {
            $totalshares = round($totalshares / 1000000, 1) . 'M';
        } elseif ($totalshares > 1000) {
            $totalshares = round($totalshares / 1000, 1) . 'k';
        }
        return apply_filters('get_rounded_shares', $totalshares);
    }
    
    /* Return the more networks button
     * @since 2.0
     * @return string
     */
    function onOffSwitch(){
        $output = '<div class="onoffswitch"></div>';
        return apply_filters('quadssh_onoffswitch', $output);
    }
    
    /* Return the second more networks button after 
     * last hidden additional service. initial status: hidden
     * Become visible with click on plus icon
     * 
     * @since 2.0
     * @return string
     */
    function onOffSwitch2(){
        $output = '<div class="onoffswitch2" style="display:none;"></div>';
        return apply_filters('quadssh_onoffswitch2', $output);
    }

    /* Delete all services from array which are not enabled
     * @since 2.0.0
     * @return callback
     */
    function isStatus($var){
        return (!empty($var["status"]));
        }
       



/* Array of all available network share urls
    * 
    * @param string $name id of the network
    * @param string $url to share
    * @param string $title to share
    * @param mixed $customurl boolean | string false default
    * 
    * @since 2.1.3
    * @return string
    */   
        
    function arrNetworks($name) {
        global $quads_options, $post, $quads_custom_url, $quads_custom_text;
        $singular = isset( $quads_options['singular'] ) ? $singular = true : $singular = false;     

        $url = $quads_custom_url ? $quads_custom_url : quads_get_url() ;
        $twitter_url = $quads_custom_url ? $quads_custom_url : quads_get_twitter_url();
        $title = $quads_custom_text ? $quads_custom_text : quads_get_title();
        $twitter_title = $quads_custom_text ? $quads_custom_text : quads_get_twitter_title();

        !empty($quads_options['quickads_hashtag']) ? $via = '&amp;via=' . $quads_options['quickads_hashtag'] : $via = '';
       
        $networks = apply_filters('quads_array_networks', array(
            'facebook' => 'http://www.facebook.com/sharer.php?u=' . $url,
            'twitter' =>  'https://twitter.com/intent/tweet?text=' . $twitter_title . $via . '&amp;url=' . $twitter_url,
            'subscribe' => '#',
            'url' => $url,
            'title' => quads_get_title()   
        ));
        
            return isset($networks[$name]) ? $networks[$name] : '';    
        }
        


    /* Returns all available networks
     * 
     * @since 2.0
     * @param string $url to share
     * @param string $title to share
     * @param mixed $customurl boolean | string false default
     * @param string $custom_title a custom title for sharing 
     * @returns string
     */
    function getNetworks() {
        //quadsdebug()->timer('getNetworks');
        global $quads_options, $enablednetworks;

        $output = '';
        $startsecondaryshares = '';
        $endsecondaryshares = '';
        /* content of 'more services' button */
        $onoffswitch = '';
        /* counter for 'Visible Services' */
        $startcounter = 1;
        $maxcounter = $quads_options['visible_services']+1; // plus 1 because our array values start counting from zero
        /* our list of available services, includes the disabled ones! 
         * We have to clean this array first!
         */
        $getnetworks = $quads_options['networks'];
        // Delete disabled services from array. Use callback function here. Only once: array_filter is slow. 
        // Use the newly created array and bypass the callback function than
        if (is_array($getnetworks)){
            if (!is_array($enablednetworks)){
                //echo "is not array";
                //var_dump($enablednetworks);
            $enablednetworks = array_filter($getnetworks, 'isStatus');
            }else {
                //echo "is array";
                //var_dump($enablednetworks);
            $enablednetworks = $enablednetworks;    
            }
        }else{
        $enablednetworks = $getnetworks; 
        }

    if (!empty($enablednetworks)) {
        foreach ($enablednetworks as $key => $network):
            if($quads_options['visible_services'] !== 'all' && $maxcounter != count($enablednetworks) && $quads_options['visible_services'] < count($enablednetworks)){
                if ($startcounter === $maxcounter ){ 
                    $onoffswitch = onOffSwitch();
                    $startsecondaryshares   = '<div class="secondary-shares" style="display:none;">';} else {$onoffswitch = ''; $onoffswitch2 = ''; $startsecondaryshares   = '';}
                if ($startcounter === (count($enablednetworks))){ 
                    $endsecondaryshares     = '</div>'; } else { ;$endsecondaryshares = '';}
                    
                //echo "<h1>Debug: Startcounter " . $startcounter . " Hello: " . $maxcounter+1 .
                //" Debug: Enabled services: " . count($enablednetworks) . "</h1>"; 
            }
            if ($enablednetworks[$key]['name'] !='') {
                /* replace all spaces with $nbsp; This prevents error in css style content: text-intend */
                $name = preg_replace('/\040{1,}/','&nbsp;',$enablednetworks[$key]['name']);
            } else {
                $name = ucfirst($enablednetworks[$key]['id']);
            }
            $enablednetworks[$key]['id'] == 'whatsapp' ? $display = 'display:none;' : $display = ''; // Whatsapp button is made visible via js when opened on mobile devices

            $output .= '<a style="' . $display . '" class="quadsicon-' . $enablednetworks[$key]['id'] . '" href="' . arrNetworks($enablednetworks[$key]['id']) . '" target="_blank" rel="nofollow"><span class="icon"></span><span class="text">' . $name . '</span></a>';
            $output .= $onoffswitch;
            $output .= $startsecondaryshares;
            
            $startcounter++;
        endforeach;
        $output .= onOffSwitch2();
        $output .= $endsecondaryshares;
    }
    //quadsdebug()->timer('getNetworks', true);
    return apply_filters('return_networks', $output);
    
}

    /* Select Share count from database and returns share buttons and share counts
     * @since 1.0
     * @returns string
     */
    function quadsshareShow($atts, $place) {
        quadsdebug()->timer('timer');
        $url = quads_get_url();
        //$title = quads_get_title();
        
        global $wpdb, $quads_options, $post;
        !empty($quads_options['quickads_apikey']) ? $apikey = $quads_options['quickads_apikey'] : $apikey = '';
        !empty($quads_options['sharecount_title']) ? $sharecount_title = $quads_options['sharecount_title'] : $sharecount_title = __('SHARES', 'quads');
        
            
            if (!isset($quads_options['disable_sharecount'])) {
                    /* Get totalshares of the current page */
                    $totalshares = getSharedcount($url);
                    /* Round total shares when enabled */
                    if (isset($quads_options['quickads_round'])) {
                        $totalshares = roundshares($totalshares);
                    }  
                 $sharecount = '<div class="quads-count"><div class="counts quadssbcount">' . $totalshares . '</div><span class="quads-sharetext">' . $sharecount_title . '</span></div>';    
             } else {
                 $sharecount = '';
             }
             
                     
                $return = '<aside class="quads-container">'
                        . quads_content_above().
                    '<div class="quads-box">'
                        . apply_filters('quads_sharecount_filter', $sharecount) .
                    '<div class="quads-buttons">' 
                        . getNetworks() . 
                    '</div></div>
                    <div style="clear:both;"></div>'
                    . quads_subscribe_content()
                    . quads_content_below() .
                    '</aside>
                        <!-- Share buttons by quadsshare.net - Version: ' . quads_VERSION . '-->';
            quadsdebug()->timer('timer', true);
            return apply_filters( 'quads_output_buttons', $return );
            
    }
    
    
    /* Shortcode function
     * Select Share count from database and returns share buttons and share counts
     * @since 1.0
     * @returns string
     */
    function quadsshareShortcodeShow($atts, $place) {
        global $wpdb ,$quads_options, $post, $wp, $quads_custom_url, $quads_custom_text;
        
        //$mainurl = quads_get_url();

        !empty($quads_options['sharecount_title']) ? $sharecount_title = $quads_options['sharecount_title'] : $sharecount_title = __('SHARES', 'quads');
        
        $sharecount = '';

        extract(shortcode_atts(array(
            'cache' => '3600',
            'shares' => 'true',
            'buttons' => 'true',
            'align' => 'left',
            'text' => '',
            'url' => ''
                        ), $atts));

            /* Load hashshag*/       
            if ($quads_options['quickads_hashtag'] != '') {
                $via = '&amp;via=' . $quads_options['quickads_hashtag'];
            } else {
                $via = '';
            }

            // Define custom url var to share
            $quads_custom_url = empty($url) ? false : $url;
            
            // Define custom text to share
            $quads_custom_text = empty($text) ? false : $text;
            
            //$sharecount_url = empty($url) ? quads_get_url() : $url;
            
             if ($shares != 'false') {
                    /* get totalshares of the current page with sharedcount.com */
                    $totalshares = getSharedcount($quads_custom_url);
                    //$totalshares = getSharedcount($mainurl);
                    //$totalshares = $quads_custom_url;
                    /* Round total shares when enabled */
                    $roundenabled = isset($quads_options['quickads_round']) ? $quads_options['quickads_round'] : null;
                        if ($roundenabled) {
                            $totalshares = roundshares($totalshares);
                        }
                    $sharecount = '<div class="quads-count" style="float:' . $align . ';"><div class="counts">' . $totalshares . '</div><span class="quads-sharetext">' . $sharecount_title . '</span></div>';    
                    /*If shortcode [quadsshare shares="true" onlyshares="true"]
                     * return shares and exit;
                     */
                    if ($shares === "true" && $buttons === 'false'){
                       return $sharecount; 
                    }
                    if ($shares === "false" && $buttons === 'true'){
                       $sharecount = '';
                }  
             }
     
                $return = '<aside class="quads-container">'
                    . quads_content_above().
                    '<div class="quads-box">'
                        . $sharecount .
                    '<div class="quads-buttons">' 
                        . getNetworks() . 
                    '</div></div>
                    <div style="clear:both;"></div>'
                    . quads_subscribe_content()
                    . quads_content_below() .
                    '</aside>
                        <!-- Share buttons made by quadsshare.net - Version: ' . quads_VERSION . '-->';
        
        // Do not execute filter for excerpts
        //if(in_array('get_the_excerpt', $GLOBALS['wp_current_filter'])) apply_filters( 'quads_output_buttons', '' );
            
        return apply_filters( 'quads_output_buttons', $return );    
    }
    
    /* Returns active status of Quick AdSense Reloaded.
     * Used for scripts.php $hook
     * @since 2.0.3
     * @return bool True if QUADS is enabled on specific page or post.
     * @TODO: Check if shortcode [quadsshare] is used in widget
     */
   
    function quadssbGetActiveStatus(){
       global $quads_options, $post;

       $frontpage = isset( $quads_options['frontpage'] ) ? $frontpage = 1 : $frontpage = 0;
       $current_post_type = get_post_type();
       $enabled_post_types = isset( $quads_options['post_types'] ) ? $quads_options['post_types'] : array();
       $excluded = isset( $quads_options['excluded_from'] ) ? $quads_options['excluded_from'] : null;
       $singular = isset( $quads_options['singular'] ) ? $singular = true : $singular = false;
       $loadall = isset( $quads_options['loadall'] ) ? $loadall = true : $loadall = false;
       
       /*if ( is_404() )
           return false;*/
           
       if ($loadall){
           quadsdebug()->info("load all quadssb scripts");
           return true;
       }
       
       // Load scripts when shortcode is used
       /* Check if shortcode is used */ 
       if( function_exists('has_shortcode') && is_object($post) && has_shortcode( $post->post_content, 'quadsshare' ) ) {
           quadsdebug()->info("has_shortcode");
            return true;
       } 
       
       
       // Load scripts when do_action('quadsshare') is used
       //if(has_action('quadsshare') && quads_is_excluded() !== true) {
       /*if(has_action('quadsshare')) {
           quadsdebug()->info("action1");
           return true;    
       }*/
       
       // Load scripts when do_action('quadssharer') is used
       //if(has_action('quadssharer') && quads_is_excluded() !== true) {
       /*if(has_action('quadssharer')) {
           quadsdebug()->info("action2");
           return true;    
       }*/ 
       
       // No scripts on non singular page
       if (!is_singular() == 1 && $singular !== true) {
           return false;
       }

        // Load scripts when page is not excluded
        if (strpos($excluded, ',') !== false) {
            //quadsdebug()->error("hoo");
            $excluded = explode(',', $excluded);
            if (!in_array($post->ID, $excluded)) {
                return true;
            }
        }
        if ($post->ID == $excluded) {
            return false;
        }
       
       // Load scripts when post_type is defined (for automatic embeding)
       //if ($enabled_post_types && in_array($currentposttype, $enabled_post_types) && quads_is_excluded() !== true ) {
       //if ($enabled_post_types == null or in_array($current_post_type, $enabled_post_types)) {
       if (in_array($current_post_type, $enabled_post_types)) {
           quadsdebug()->info("100");
           return true;
       }  
       
       /* Check if post types are allowed */
       //quadsdebug()->info("var frontpage enabled: " . $frontpage . " is_front_page(): " . is_front_page());
       //if ($enabled_post_types && in_array($currentposttype, $enabled_post_types) && quads_is_excluded() !== true) {
       /*if ($enabled_post_types && in_array($current_post_type, $enabled_post_types)) {
           quadsdebug()->info("200");
           return true;
       }*/
       
       // No scripts on frontpage when disabled
       //if ($frontpage == 1 && is_front_page() == 1 && quads_is_excluded() !== true) {
       if ($frontpage == 1 && is_front_page() == 1) {
           quadsdebug()->info("300");
            return true;
       }

    }
    


    
    /* Returns Share buttons on specific positions
     * Uses the_content filter
     * @since 1.0
     * @return string
     */
    function quadsshare_filter_content($content){
        global $atts, $quads_options, $post, $wp_current_filter, $wp;
        
        // Do not execute filter for excerpts
        //if(in_array('get_the_excerpt', $GLOBALS['wp_current_filter'])) return $content;
        
        /* define some vars here to reduce multiple execution of basic functions */
        /* Use permalink when its not singular page, so on category pages the permalink is used. */
        $url = quads_get_url();
        $title = quads_get_title();
        /*function_exists('quadsOG') ? $title = quadsOG()->quadsOG_OG_Output->_get_title() : $title = the_title_attribute('echo=0');
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        $title = urlencode($title);
        $title = str_replace('#' , '%23', $title);
        $title = esc_html($title);*/
        
        $position = !empty($quads_options['quickads_position']) ? $quads_options['quickads_position'] : '';
        $enabled_post_types = isset( $quads_options['post_types'] ) ? $quads_options['post_types'] : null;
        $current_post_type = get_post_type();
        $frontpage = isset( $quads_options['frontpage'] ) ? $quads_options['frontpage'] : null;
        $excluded = isset( $quads_options['excluded_from'] ) ? $quads_options['excluded_from'] : null;
        $singular = isset( $quads_options['singular'] ) ? $singular = true : $singular = false;
        
        if (strpos($excluded, ',') !== false) {
             $excluded = explode(',', $excluded);
             if (in_array($post->ID, $excluded)) {
                return $content;
             }  
        }
    
        if ($post->ID == $excluded) {
                return $content;
        }  

        if (!is_singular() == 1 && $singular !== true) {
            return $content;
        }

        if ($frontpage == 0 && is_front_page() == 1) {
            return $content;
        }
        
        if ($enabled_post_types == null or !in_array($current_post_type, $enabled_post_types)) {
            return $content;
        }

        if (in_array('get_the_excerpt', $wp_current_filter)) {
            return $content;
        }
        
        if (is_feed()) {
            return $content;
        }
		
            switch($position){
                case 'manual':
                break;

                case 'both':
                    $content = quadsshareShow($atts, '') . $content . quadsshareShow($atts, "bottom");
                break;

                case 'before':
                    $content = quadsshareShow($atts, '') . $content;
                    
                break;

                case 'after':
                    $content .= quadsshareShow($atts, '');
                break;
            }
            return $content;

        }

/* Template function quadsshare() 
 * @since 2.0.0
 * @return string
*/ 
function quadsshare(){
    global $atts;
    /*global $content;
    global $post;
    global $wp;*/

    /* Use permalink when its not singular page, so on category pages the permalink is used. */
    //is_singular() ? $url = urlencode(home_url( $wp->request )) : $url = urlencode(get_permalink($post->ID));
    //$url = quads_get_url();
    //$title = quads_get_title(); 
    /*function_exists('quadsOG') ? $title = quadsOG()->quadsOG_OG_Output->_get_title() : $title = the_title_attribute('echo=0');
    $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
    $title = urlencode($title);
    $title = str_replace('#' , '%23', $title);
    $title = esc_html($title);*/
    echo quadsshareShow($atts, '');
}

/* Deprecated: Template function quadssharer()
 * @since 1.0
 * @return string
*/ 
function quadssharer(){
    global $atts;
    /*global $content;
    global $post;
    global $wp;*/
    //is_singular() ? $url = urlencode(home_url( $wp->request )) : $url = urlencode(get_permalink($post->ID));
    //$url = quads_get_url();
    //$title = quads_get_title();       
    /*function_exists('quadsOG') ? $title = quadsOG()->quadsOG_OG_Output->_get_title() : $title = the_title_attribute('echo=0');
    $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
    $title = urlencode($title);
    $title = str_replace('#' , '%23', $title);
    $title = esc_html($title);*/
    echo quadsshareShow($atts, '');
}




/**
 * Get Thumbnail image if existed
 *
 * @since 1.0
 * @param int $postID
 * @return string
 */
function quads_get_image($postID){
    quadsdebug()->timer('quads_get_image');
            global $post;
            if (has_post_thumbnail( $post->ID )) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
				return $image[0];
            	}
    quadsdebug()->timer('quads_get_image', true);
	}
add_action( 'quads_get_image', 'quads_get_image' );

/**
 * Get excerpt for Facebook Share
 *
 * @since 1.0
 * @param int $postID
 * @return string
 */
function quads_get_excerpt_by_id($post_id){
    quadsdebug()->timer('quads_get_exerpt');
	$the_post = get_post($post_id); //Gets post ID
	$the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
	$excerpt_length = 35; //Sets excerpt length by word count
	$the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
	$words = explode(' ', $the_excerpt, $excerpt_length + 1);
	if(count($words) > $excerpt_length) :
	array_pop($words);
	array_push($words, '…');
	$the_excerpt = implode(' ', $words);
	endif;
	$the_excerpt = '<p>' . $the_excerpt . '</p>';
	return wp_strip_all_tags($the_excerpt);
    quadsdebug()->timer('quads_get_exerpt', true);
}
add_action( 'quads_get_excerpt_by_id', 'quads_get_excerpt_by_id' );

/**
 * Create a factor for calculating individual fake counts 
 * based on the number of word within a page title
 *
 * @since 2.0
 * @return int
 */
function quads_get_fake_factor() {
    $wordcount = str_word_count(the_title_attribute('echo=0')); //Gets title to be used as a basis for the count
    $factor = $wordcount / 10;
    return apply_filters('quads_fake_factor', $factor);
}

/* Sharecount fake number
 * @return int
 * @since 2.0.9
 * 
 */

function getFakecount() {
    global $quads_options, $wp;
    $fakecountoption = 0;
    if (isset($quads_options['fake_count'])) {
        $fakecountoption = $quads_options['fake_count'];
    }
    $fakecount = round($fakecountoption * quads_get_fake_factor(), 0);
    //quadsdebug()->info("fakecount: " . $fakecount);
    //return apply_filters('filter_get_fakecount', $fakecount);
    return $fakecount;
}

/* Show sharecount only when there is number of x shares. otherwise its hidden via css
 * @return bool
 * @since 2.0.7
 */

function quads_hide_shares(){
    global $quads_options, $post, $wp;
    $url = get_permalink(isset($post->ID));
    $sharelimit = isset($quads_options['hide_sharecount']) ? $quads_options['hide_sharecount'] : 0;
   
    if ($sharelimit > 0){
        //quadsdebug()->error( "getsharedcount: " . getSharedcount($url) . "sharelimit " . $sharelimit);
        if (getSharedcount($url) > $sharelimit){
            return false;
        }else {
            return true;
        }
    }
    return false;
}

/**
 * Add Custom Styles with WP wp_add_inline_style Method
 *
 * @since 1.0
 * 
 * @return string
 */

function quads_styles_method() {
    global $quads_options;
    isset($quads_options['small_buttons']) ? $smallbuttons = true : $smallbuttons = false;
    
    /* VARS */
    isset($quads_options['share_color']) ? $share_color = $quads_options['share_color'] : $share_color = '';
    isset($quads_options['custom_css']) ? $custom_css = $quads_options['custom_css'] : $custom_css = '';
    isset($quads_options['button_width']) ? $button_width = $quads_options['button_width'] : $button_width = '';
    
    /* STYLES */
    $quads_custom_css = "
        .quads-count {
        color: {$share_color};
        }"; 
    if ( !empty($quads_options['border_radius']) && $quads_options['border_radius'] != 'default' ){
    $quads_custom_css .= '
        [class^="quadsicon-"], .onoffswitch-label, .onoffswitch2-label {
            border-radius: ' . $quads_options['border_radius'] . 'px;
        }';   
    }
    if ( !empty($quads_options['quads_style']) && $quads_options['quads_style']  == 'shadow' ){
    $quads_custom_css .= '
        .quads-buttons a, .onoffswitch, .onoffswitch2, .onoffswitch-inner:before, .onoffswitch2-inner:before  {
            -webkit-transition: all 0.07s ease-in;
            -moz-transition: all 0.07s ease-in;
            -ms-transition: all 0.07s ease-in;
            -o-transition: all 0.07s ease-in;
            transition: all 0.07s ease-in;
            box-shadow: 0 1px 0 0 rgba(0, 0, 0, 0.2),inset 0 -1px 0 0 rgba(0, 0, 0, 0.3);
            text-shadow: 0 1px 0 rgba(0, 0, 0, 0.25);
            border: none;
            -moz-user-select: none;
            -webkit-font-smoothing: subpixel-antialiased;
            -webkit-transition: all linear .25s;
            -moz-transition: all linear .25s;
            -o-transition: all linear .25s;
            -ms-transition: all linear .25s;
            transition: all linear .25s;
        }';   
    }
    if ( !empty($quads_options['quads_style']) && $quads_options['quads_style']  == 'gradiant' ){
    $quads_custom_css .= '
        .quads-buttons a  {
            background-image: -webkit-linear-gradient(bottom,rgba(0, 0, 0, 0.17) 0%,rgba(255, 255, 255, 0.17) 100%);
            background-image: -moz-linear-gradient(bottom,rgba(0, 0, 0, 0.17) 0%,rgba(255, 255, 255, 0.17) 100%);
            background-image: linear-gradient(bottom,rgba(0,0,0,.17) 0%,rgba(255,255,255,.17) 100%);
            
        }';   
    }
    if (quads_hide_shares() === true){
    $quads_custom_css .= ' 
        .quads-box .quads-count {
            display: none;
        }';   
    }
    
    if ($smallbuttons === true){
    $quads_custom_css .= '[class^="quadsicon-"] .text, [class*=" quadsicon-"] .text{
        text-indent: -9999px !important;
        line-height: 0px;
        display: block;
        } 
    [class^="quadsicon-"] .text:after, [class*=" quadsicon-"] .text:after {
        content: "" !important;
        text-indent: 0;
        font-size:13px;
        display: block !important;
    }
    [class^="quadsicon-"], [class*=" quadsicon-"] {
        width:25%;
        text-align: center !important;
    }
    [class^="quadsicon-"] .icon:before, [class*=" quadsicon-"] .icon:before {
        float:none;
        margin-right: 0;
    }
    .quads-buttons a{
       margin-right: 3px;
       margin-bottom:3px;
       min-width: 0;
       width: 41px;
    }

    .onoffswitch, 
    .onoffswitch-inner:before, 
    .onoffswitch-inner:after 
    .onoffswitch2,
    .onoffswitch2-inner:before, 
    .onoffswitch2-inner:after  {
        margin-right: 0px;
        width: 41px;
        line-height: 41px;
    }';   
    } else {
    $quads_custom_css .= '
    .quads-buttons a {
    min-width: ' . $button_width . 'px;}';
    }
    
    $quads_custom_css .= $custom_css;
        // ----------- Hook into existed 'quads-style' at /templates/quadssb.min.css -----------
        wp_add_inline_style( 'quads-styles', $quads_custom_css );
}
add_action( 'wp_enqueue_scripts', 'quads_styles_method' );



    /* Additional content above share buttons 
     * 
     * @return string $html
     * @scince 2.3.2
     */
    function quads_content_above(){
        global $quads_options;
        $html = !empty ($quads_options['content_above']) ? '<div class="quads_above_buttons">' . $quads_options['content_above'] . '</div>' : '';
        return apply_filters( 'quads_above_buttons', $html );
    }
    
    /* Additional content above share buttons 
     * 
     * @return string $html
     * @scince 2.3.2
     */
    function quads_content_below(){
        global $quads_options;
        $html = !empty ($quads_options['content_below']) ? '<div class="quads_below_buttons">' .$quads_options['content_below'] . '</div>' : '';
        return apply_filters( 'quads_below_buttons', $html );
    }

/**
 * Return general post title
 * 
 * @param string $title default post title
 * @return string the default post title, shortcode title or custom twitter title
 */
function quads_get_title() {
    function_exists('quadsOG') ? $title = quadsOG()->quadsOG_OG_Output->_get_title() : $title = the_title_attribute('echo=0');
    $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
    $title = urlencode($title);
    $title = str_replace('#' , '%23', $title);
    $title = esc_html($title);
    
    return $title;
}

/**
 * Return twitter custom title
 * 
 * @return string the custom twitter title
 */
function quads_get_twitter_title() {
    if (function_exists('quadsOG')) {
        $title = quadsOG()->quadsOG_OG_Output->_get_tw_title();
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        $title = urlencode($title);
        $title = str_replace('#', '%23', $title);
        $title = esc_html($title);
        $title = str_replace('+', '%20', $title);
    } else {
        $title = quads_get_title();
        $title = str_replace('+', '%20', $title);
    }
    return $title;
}

    
/* Get URL to share
 * 
 * @return url  $string
 * @scince 2.2.8
 */

function quads_get_url(){
    global $wp, $post, $numpages;
    if($numpages > 1){ // check if '<!-- nextpage -->' is used
        $url = urlencode(get_permalink($post->ID));
    } elseif (is_singular()){
        $url = urlencode(get_permalink($post->ID));
    }else{
        $url = urlencode(get_permalink($post->ID));
    }
    return apply_filters('quads_get_url', $url);
}

/* Get twitter URL to share
 * 
 * @return url  $string
 * @scince 2.2.8
 */

function quads_get_twitter_url(){
    global $wp, $post, $numpages; 
       if ( function_exists('quadssuGetShortURL')){
            $url = quads_get_url();
            quadssuGetShortURL($url) !== 0 ? $url = quadssuGetShortURL( $url ) : $url = quads_get_url();
        } else {
            $url = quads_get_url();
        }
    return apply_filters('quads_get_twitter_url', $url);
}
