<?php
/**
 * Template Functions
 *
 * @package     QUADS
 * @subpackage  Functions/Templates
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// we need to hook into the_content on lower than default priority (that's why we use separate hook)
add_filter('the_content', 'quads_post_settings_to_quicktags', 5);
add_filter('the_content', 'quads_process_content', quads_get_load_priority());

/**
 * Show ads before posts
 * @not used at the moment
 */
//add_action('loop_start', 'quads_inject_ad');

//function quads_inject_ad() {
//   global $quads_options, $post;
//   
//   // Ads are deactivated via post meta settings
//    if( quads_check_meta_setting( 'NoAds' ) === '1' || quads_check_meta_setting( 'OffBegin' ) === '1'){
//        return false;
//    }
//   
//   if( !quads_ad_is_allowed( '' ) || !is_main_query() ) {
//      return false;
//   }
//   // Array of ad codes ids
//   $adsArray = quads_get_active_ads();
//
//   // Return no ads are defined
//   if( count($adsArray) === 0 ) {
//      return false;
//   }
//   
//   $id = 1;
//   
//   $code = !empty($quads_options['ads']['ad' . $id ]['code']) ? $quads_options['ads']['ad' . $id ]['code'] : '';
//   echo quads_render_ad(1, $code, false);
//   
//}

/**
 * Adds quicktags, defined via post meta options, to content.
 *
 * @param $content Post content
 *
 * @return string
 */
function quads_post_settings_to_quicktags ( $content ) {
    
        // Return original content if QUADS is not allowed
        if ( !quads_ad_is_allowed($content)){
            return $content;
        }
    
	$quicktags_str = quads_get_visibility_quicktags_str();

        return $content . $quicktags_str;
}
/**
 * Returns quicktags based on post meta options.
 * These quicktags define which ads should be hidden on current page.
 *
 * @param null $post_id Post id
 *
 * @return string
 */
function quads_get_visibility_quicktags_str( $post_id = null ) {

   if( !$post_id ) {
      $post_id = get_the_ID();
   }

   $str = '';
   if( false === $post_id ) {
      return $str;
   }

   $config = get_post_meta( $post_id, '_quads_config_visibility', true );

   if( !empty( $config ) && is_array($config) ) {
      foreach ( $config as $qtag_id => $qtag_label ) {
         $str .= '<!--' . $qtag_id . '-->';
      }
   }

   return $str;
}

/**
 * Get load priority
 * 
 * @global arr $quads_options
 * @return int
 */
function quads_get_load_priority(){
    global $quads_options;
    
    if (!empty($quads_options['priority'])){
        return intval($quads_options['priority']);
    }
    return 20;
}

/**
 * 
 * @global arr $quads_options
 * @global type $adsArray
 * @param type $content
 * @return type
 */
function quads_process_content( $content ) {
    global $quads_options, $adsArray, $adsArrayCus, $visibleContentAds, $ad_count_widget, $visibleShortcodeAds;
    

    // Do not do anything if ads are not allowed or process is not in the main query
    if( !quads_ad_is_allowed( $content ) || !is_main_query()) {
        $content = quads_clean_tags( $content );
        return $content;
    }
    
    // Do nothing if maximum ads are reached in post content
    if( $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
        $content = quads_clean_tags( $content );
        return $content;
    }

    // Array of ad codes ids
    $adsArray = quads_get_active_ads();
    
    // Return is no ads are defined
    if ($adsArray === 0){
        return $content;
    }

    $content = quads_sanitize_content( $content );

    $content = quads_filter_default_ads( $content );

    /*
     * Tidy up content
     */
    //$content = '<!--EmptyClear-->' . $content . "\n" . '<div style="font-size:0px;height:0px;line-height:0px;margin:0;padding:0;clear:both"></div>';
    $content = '<!--EmptyClear-->' . $content . "\n";
    $content = quads_clean_tags( $content, true );
    
    $content = quads_parse_default_ads($content);
        
    $content = quads_parse_quicktags( $content );
        
    $content = quads_parse_random_quicktag_ads($content);
    
    $content = quads_parse_random_ads( $content );

    /* ... That's it. DONE :) ... */
    $content = quads_clean_tags( $content );

    return do_shortcode( $content );
}


/**
 * Return number of active widget ads
 * @param string the_content
 * @return int amount of widget ads
 */
function quads_get_number_widget_ads() {
    $number_widgets = 0;
    $maxWidgets = 10;
    // count active widget ads
        for ( $i = 1; $i <= $maxWidgets; $i++ ) {
            $AdsWidName = 'AdsWidget%d (Quick Adsense Reloaded)';
            $wadsid = sanitize_title( str_replace( array('(', ')'), '', sprintf( $AdsWidName, $i ) ) );
            $number_widgets += (is_active_widget( '', '', $wadsid )) ? 1 : 0;
        }
    
    return $number_widgets;
}

/**
 * Get list of valid ad ids's where either the plain text code field or the adsense ad slot and the ad client id is populated.
 * @global arr $quads_options
 */
function quads_get_active_ads() {
    global $quads_options;

    
    // Return early
    if (empty($quads_options['ads'])){
       return 0;
    }
   
    // count valid ads
    $i = 0;
    foreach ( $quads_options['ads'] as $ads) {
        $tmp = isset( $quads_options['ads']['ad' . $i]['code'] ) ? trim( $quads_options['ads']['ad' . $i]['code'] ) : '';
        // id is valid if there is either the plain text field populated or the adsense ad slot and the ad client id
        if( !empty( $tmp ) || (!empty( $quads_options['ads']['ad' . $i]['g_data_ad_slot'] ) && !empty( $quads_options['ads']['ad' . $i]['g_data_ad_client'] ) ) ) {
            $adsArray[] = $i;
        }
        $i++;
    }
    return (isset($adsArray) && count($adsArray) > 0) ? $adsArray : 0;
}


/**
 * Get max allowed numbers of ads
 * 
 * @param string $content
 * @return int maximum number of ads
 */
function quads_get_max_allowed_post_ads( $content ) {
    global $quads_options;

    // Maximum allowed general number of ads 
    $maxAds = isset( $quads_options['maxads'] ) ? $quads_options['maxads'] : 10;
    
    $numberWidgets = 10;
    
    $AdsWidName = 'AdsWidget%d (Quick Adsense Reloaded)';
    
    // count number of active widgets and subtract them 
    if( strpos( $content, '<!--OffWidget-->' ) === false &&  !quads_is_amp_endpoint() ) {
        for ( $i = 1; $i <= $numberWidgets; $i++ ) {
            $wadsid = sanitize_title( str_replace( array('(', ')'), '', sprintf( $AdsWidName, $i ) ) );
            $maxAds -= (is_active_widget( '', '', $wadsid )) ? 1 : 0;
        }
    }

    return $maxAds;
}


/**
 * Filter default ads
 * 
 * @global array $quads_options global settings
 * @global array $adsArrayCus List of ad id'S
 * @param string $content
 * @return string content
 */
function quads_filter_default_ads( $content ) {
    global $quads_options, $adsArrayCus;
    
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);

    if( $off_default_ads ) { // If default ads are disabled 
        return $content;
    }
    
    // Default Ads
    $adsArrayCus = array();

    // placeholder string for random ad
    $cusrnd = 'CusRnd';
    
    // placeholder string for custom ad spots
    $cusads = 'CusAds';
    
    // Beginning of Post
    $beginning_position_status = isset( $quads_options['pos1']['BegnAds'] ) ? true : false;
    $beginning_position_ad_id = isset( $quads_options['pos1']['BegnRnd'] ) ? $quads_options['pos1']['BegnRnd'] : 0;

    // Middle of Post
    $middle_position_status = isset( $quads_options['pos2']['MiddAds'] ) ? true : false;
    $middle_position_ad_id = isset( $quads_options['pos2']['MiddRnd'] ) ? $quads_options['pos2']['MiddRnd'] : 0;

    // End of Post
    $end_position_status = isset( $quads_options['pos3']['EndiAds'] ) ? true : false;
    $end_position_ad_id = isset( $quads_options['pos3']['EndiRnd'] ) ? $quads_options['pos3']['EndiRnd'] : 0;

    // After the more tag
    $more_position_status = isset( $quads_options['pos4']['MoreAds'] ) ? true : false;
    $more_position_ad_id = isset( $quads_options['pos4']['MoreRnd'] ) ? $quads_options['pos4']['MoreRnd'] : 0;

    // Right before the last paragraph
    $last_paragraph_position_status = isset( $quads_options['pos5']['LapaAds'] ) ? true : false;
    $last_paragraph_position_ad_id = isset( $quads_options['pos5']['LapaRnd'] ) ? $quads_options['pos5']['LapaRnd'] : 0;

    // After Paragraph option 1 - 3
    $number = 3; // number of paragraph ads | default value 3. 
    $default = 5; // Position. Let's start with id 5
    for ( $i = 1; $i <= $number; $i++ ) {
        $key = $default + $i; // 6,7,8

        $paragraph['status'][$i] = isset( $quads_options['pos' . $key]['Par' . $i . 'Ads'] ) ? $quads_options['pos' . $key]['Par' . $i . 'Ads'] : 0; // Status - active | inactive
        $paragraph['id'][$i] = isset( $quads_options['pos' . $key]['Par' . $i . 'Rnd'] ) ? $quads_options['pos' . $key]['Par' . $i . 'Rnd'] : 0; // Ad id	
        $paragraph['position'][$i] = isset( $quads_options['pos' . $key]['Par' . $i . 'Nup'] ) ? $quads_options['pos' . $key]['Par' . $i . 'Nup'] : 0; // Paragraph No	
        $paragraph['end_post'][$i] = isset( $quads_options['pos' . $key]['Par' . $i . 'Con'] ) ? $quads_options['pos' . $key]['Par' . $i . 'Con'] : 0; // End of post - yes | no        
    }
    // After Image ad
    $imageActive = isset( $quads_options['pos9']['Img1Ads'] ) ? $quads_options['pos9']['Img1Ads'] : false;
    $imageAdNo = isset( $quads_options['pos9']['Img1Rnd'] ) ? $quads_options['pos9']['Img1Rnd'] : false;
    $imageNo = isset( $quads_options['pos9']['Img1Nup'] ) ? $quads_options['pos9']['Img1Nup'] : false;
    $imageCaption = isset( $quads_options['pos9']['Img1Con'] ) ? $quads_options['pos9']['Img1Con'] : false;


    if( $beginning_position_ad_id == 0 ) {
        $b1 = $cusrnd;
    } else {
        $b1 = $cusads . $beginning_position_ad_id;
        array_push( $adsArrayCus, $beginning_position_ad_id );
    };
    
    if( $more_position_ad_id == 0 ) {
        $r1 = $cusrnd;
    } else {
        $r1 = $cusads . $more_position_ad_id;
        array_push( $adsArrayCus, $more_position_ad_id );
    };
    
    if( $middle_position_ad_id == 0 ) {
        $m1 = $cusrnd;
    } else {
        $m1 = $cusads . $middle_position_ad_id;
        array_push( $adsArrayCus, $middle_position_ad_id );
    };
    if( $last_paragraph_position_ad_id == 0 ) {
        $g1 = $cusrnd;
    } else {
        $g1 = $cusads . $last_paragraph_position_ad_id;
        array_push( $adsArrayCus, $last_paragraph_position_ad_id );
    };
    if( $end_position_ad_id == 0 ) {
        $b2 = $cusrnd;
    } else {
        $b2 = $cusads . $end_position_ad_id;
        array_push( $adsArrayCus, $end_position_ad_id );
    };
    for ( $i = 1; $i <= $number; $i++ ) {
        if( $paragraph['id'][$i] == 0 ) {
            $paragraph[$i] = $cusrnd;
        } else {
            $paragraph[$i] = $cusads . $paragraph['id'][$i];
            array_push( $adsArrayCus, $paragraph['id'][$i] );
        };
    }
    //wp_die(print_r($adsArrayCus));

    // Create the arguments for filter quads_filter_paragraphs
    $quads_args = array(
        'paragraph' => $paragraph,
        'cusads' => $cusads,
        'cusrnd' => $cusrnd,
        'AdsIdCus' => $adsArrayCus,
    );

    // Execute filter to add more paragraph ad spots
    $quads_filtered = apply_filters( 'quads_filter_paragraphs', $quads_args );

    // The filtered arguments
    $paragraph = $quads_filtered['paragraph'];

    // filtered list of ad spots
    $adsArrayCus = $quads_filtered['AdsIdCus'];

    // Create paragraph ads
    $number = 11;

    for ( $i = $number; $i >= 1; $i-- ) {
        if( !empty( $paragraph['status'][$i] ) ) {
            $sch = "</p>";
            $content = str_replace( "</P>", $sch, $content );
            
                        
            /**
             * Get all blockquote if there are any
             */
            
            preg_match_all("/<blockquote>(.*?)<\/blockquote>/s", $content, $blockquotes);
            
            /**
             * Replace blockquotes with placeholder
             */
            if(!empty($blockquotes)){
               $bId = 0;
               foreach($blockquotes[0] as $blockquote){
                  $replace = "#QUADSBLOCKQUOTE" . $bId . '#';
                  $content = str_replace(trim($blockquote), $replace, $content);
                  $bId++;
               }
            }
            
            // Get paragraph tags
            $paragraphsArray = explode( $sch, $content );
            
           /**
            * Check if last element is empty and remove it
            */
            if(trim($paragraphsArray[count($paragraphsArray)-1]) == "") array_pop($paragraphsArray);
            
            if( ( int ) $paragraph['position'][$i] <= count( $paragraphsArray ) ) {
                  $content = implode( $sch, array_slice( $paragraphsArray, 0, $paragraph['position'][$i] ) ) . $sch . '<!--' . $paragraph[$i] . '-->' . implode( $sch, array_slice( $paragraphsArray, $paragraph['position'][$i] ) );
            } elseif( $paragraph['end_post'][$i] ) {
                $content = implode( $sch, $paragraphsArray ) . '<!--' . $paragraph[$i] . '-->';
            }
            
            /**
             * Put back blockquotes into content
             */
            
            if(!empty($blockquotes)){
               $bId = 0;
               foreach($blockquotes[0] as $blockquote){
                  $search = '#QUADSBLOCKQUOTE' . $bId . '#'; 
                  $content = str_replace($search, trim($blockquote), $content);
                  $bId++;
               }
            }
        }
    }

    // Check if image ad is random one
    if( $imageAdNo == 0 ) {
        $imageAd = $cusrnd;
    } else {
        $imageAd = $cusads . $imageAdNo;
        array_push( $adsArrayCus, $imageAdNo );
    };


    // Beginning of post ad
    if( $beginning_position_status && strpos( $content, '<!--OffBegin-->' ) === false ) {
        $content = '<!--' . $b1 . '-->' . $content;
    }
    
        // Check if ad is middle one
    if( $middle_position_status && strpos( $content, '<!--OffMiddle-->' ) === false ) {
        if( substr_count( strtolower( $content ), '</p>' ) >= 2 ) {
            $closingTagP = "</p>";
            $content = str_replace( "</P>", $closingTagP, $content );
            $paragraphsArray = explode( $closingTagP, $content );
            
            /**
            * Check if last element is empty and remove it
            */
            if(trim($paragraphsArray[count($paragraphsArray)-1]) == "") array_pop($paragraphsArray);
            
            $nn = 0;
            $mm = strlen( $content ) / 2;
            for ( $i = 0; $i < count( $paragraphsArray ); $i++ ) {
                $nn += strlen( $paragraphsArray[$i] ) + 4;
                if( $nn > $mm ) {
                    if( ($mm - ($nn - strlen( $paragraphsArray[$i] ))) > ($nn - $mm) && $i + 1 < count( $paragraphsArray ) ) {
                        $paragraphsArray[$i + 1] = '<!--' . $m1 . '-->' . $paragraphsArray[$i + 1];
                    } else {
                        $paragraphsArray[$i] = '<!--' . $m1 . '-->' . $paragraphsArray[$i];
                    }
                    break;
                }
            }
            $content = implode( $closingTagP, $paragraphsArray );
        }
    }
    
    // End of Post ad
    if( $end_position_status && strpos( $content, '<!--OffEnd-->' ) === false ) {
        $content = $content . '<!--' . $b2 . '-->';
    }
    
    

    // Check if ad is after "More Tag"
    if( $more_position_status && strpos( $content, '<!--OffAfMore-->' ) === false ) {
        $mmr = '<!--' . $r1 . '-->';
        $postid = get_the_ID();
        $content = str_replace( '<span id="more-' . $postid . '"></span>', $mmr, $content );
    }
    
    // Right before last paragraph ad
    if( $last_paragraph_position_status && strpos( $content, '<!--OffBfLastPara-->' ) === false ) {
        $closingTagP = "</p>";
        $content = str_replace( "</P>", $closingTagP, $content );
        $paragraphsArray = explode( $closingTagP, $content );
        
        
            /**
            * Check if last element is empty and remove it
            */
            if(trim($paragraphsArray[count($paragraphsArray)-1]) == "") array_pop($paragraphsArray);
        
        
        //if( count( $paragraphsArray ) > 2 && !strpos($paragraphsArray[count( $paragraphsArray ) - 1], '</blockquote>')) {
        if( count( $paragraphsArray ) > 2) {
            $content = implode( $closingTagP, array_slice( $paragraphsArray, 0, count( $paragraphsArray ) - 1 ) ) . '<!--' . $g1 . '-->' . $closingTagP . $paragraphsArray[count( $paragraphsArray ) - 1];
        }

    }

    // After Image ad
    if( $imageActive ) {

        // Sanitation
        $imgtag = "<img";
        $delimiter = ">";
        $caption = "[/caption]";
        $atag = "</a>";
        $content = str_replace( "<IMG", $imgtag, $content );
        $content = str_replace( "</A>", $atag, $content );

        // Get all images in content
        $imagesArray = explode( $imgtag, $content );
        // Modify Image ad
        if( ( int ) $imageNo < count( $imagesArray ) ) {
            //Get all tags
            $tagsArray = explode( $delimiter, $imagesArray[$imageNo] );
            if( count( $tagsArray ) > 1 ) {
                $captionArray = explode( $caption, $imagesArray[$imageNo] );
                $ccp = ( count( $captionArray ) > 1 ) ? strpos( strtolower( $captionArray[0] ), '[caption ' ) === false : false;
                $imagesArrayAtag = explode( $atag, $imagesArray[$imageNo] );
                $cdu = ( count( $imagesArrayAtag ) > 1 ) ? strpos( strtolower( $imagesArrayAtag[0] ), '<a href' ) === false : false;
                // Show ad after caption
                if( $imageCaption && $ccp ) {
                    $imagesArray[$imageNo] = implode( $caption, array_slice( $captionArray, 0, 1 ) ) . $caption . "\r\n" . '<!--' . $imageAd . '-->' . "\r\n" . implode( $caption, array_slice( $captionArray, 1 ) );
                } else if( $cdu ) {
                    $imagesArray[$imageNo] = implode( $atag, array_slice( $imagesArrayAtag, 0, 1 ) ) . $atag . "\r\n" . '<!--' . $imageAd . '-->' . "\r\n" . implode( $atag, array_slice( $imagesArrayAtag, 1 ) );
                } else {
                    $imagesArray[$imageNo] = implode( $delimiter, array_slice( $tagsArray, 0, 1 ) ) . $delimiter . "\r\n" . '<!--' . $imageAd . '-->' . "\r\n" . implode( $delimiter, array_slice( $tagsArray, 1 ) );
                }
            }
            $content = implode( $imgtag, $imagesArray );
        }
    }

    return $content;
}
/**
 * Sanitize content and return it cleaned
 * 
 * @param string $content
 * @return string
 */
function quads_sanitize_content($content){
    
    /* ... Tidy up content ... */
    // Replace all <p></p> tags with placeholder ##QA-TP1##
    $content = str_replace( "<p></p>", "##QA-TP1##", $content );

    // Replace all <p>&nbsp;</p> tags with placeholder ##QA-TP2##
    $content = str_replace( "<p>&nbsp;</p>", "##QA-TP2##", $content );
    
    return $content;
}



/**
 * Parse random ads which are created from quicktag <!--RndAds-->
 * 
 * @global array $adsArray
 * @global int $visibleContentAds
 * @return content
 */
function quads_parse_random_quicktag_ads($content){
    global $adsArray, $visibleContentAds, $quads_options;
    
    $maxAds = isset($quads_options['maxads']) ? $quads_options['maxads'] : 10;
    /*
     * Replace RndAds Random Ads
     */
    if( strpos( $content, '<!--RndAds-->' ) !== false && is_singular() ) {
        $adsArrayTmp = array();
        shuffle( $adsArray );
        for ( $i = 1; $i <= $maxAds - $visibleContentAds; $i++ ) {
            if( $i <= count( $adsArray ) ) {
                array_push( $adsArrayTmp, $adsArray[$i - 1] );
            }
        }
        $tcx = count( $adsArrayTmp );
        $tcy = substr_count( $content, '<!--RndAds-->' );
        for ( $i = $tcx; $i <= $tcy - 1; $i++ ) {
            array_push( $adsArrayTmp, -1 );
        }
        shuffle( $adsArrayTmp );
        for ( $i = 1; $i <= $tcy; $i++ ) {
            $tmp = $adsArrayTmp[0];
            $content = quads_replace_ads( $content, 'RndAds', $adsArrayTmp[0] );
            $adsArrayTmp = quads_del_element( $adsArrayTmp, 0 );
            if( $tmp != -1 ) {
                $visibleContentAds += 1;
            };
            //quads_set_ad_count_content();
            //if( quads_ad_reach_max_count() ) {
            if( $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
                $content = quads_clean_tags( $content );
                return $content;
            }
        }
    }
    
    return $content;
}

/**
 * Parse random default ads which can be enabled from general settings
 * 
 * @global array $adsArray
 * @global int $visibleContentAds
 * @return string
 */
function quads_parse_random_ads($content) {
    global $adsRandom, $visibleContentAds;
    
    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);
    if( $off_default_ads ) { // disabled default ads
        return $content;
    }

    if( strpos( $content, '<!--CusRnd-->' ) !== false && is_singular() ) {

        $tcx = count( $adsRandom );
        // How often is a random ad appearing in content
        $number_rand_ads = substr_count( $content, '<!--CusRnd-->' );

        for ( $i = $tcx; $i <= $number_rand_ads - 1; $i++ ) {
            array_push( $adsRandom, -1 );
        }
        shuffle( $adsRandom );
        //wp_die(print_r($adsRandom));
        //wp_die($adsRandom[0]);
        for ( $i = 1; $i <= $number_rand_ads; $i++ ) {
            $content = quads_replace_ads( $content, 'CusRnd', $adsRandom[0] );
            $adsRandom = quads_del_element( $adsRandom, 0 );
            $visibleContentAds += 1;
            //quads_set_ad_count_content();
            //if( quads_ad_reach_max_count() ) {
            if( $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
                $content = quads_clean_tags( $content );
                return $content;
            }
        }
    }

    return $content;
}

/**
 * Parse Quicktags
 * 
 * @global array $adsArray
 * @param string $content
 * @return string
 */
function quads_parse_quicktags($content){
    global $adsArray, $visibleContentAds;
    //print_r(count($adsArray));
    
    $idx = 0;
    for ( $i = 1; $i <= count( $adsArray ); $i++ ) {
        if( strpos( $content, '<!--Ads' . $adsArray[$idx] . '-->' ) !== false ) {
            $content = quads_replace_ads( $content, 'Ads' . $adsArray[$idx], $adsArray[$idx] );
            //$adsArray = quads_del_element( $adsArray, $idx );
            $visibleContentAds += 1;
            $idx +=1;
            //quads_set_ad_count_content();
            if( $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
                $content = quads_clean_tags( $content );
                return $content;
            }
        } else {
            $idx += 1;
        }
    }
    
    return $content;
}

/**
 * Parse default ads Beginning/Middle/End/Paragraph Ads1-10
 * 
 * @param string $content
 * @return string
 */
function quads_parse_default_ads( $content ) {
    global $adsArrayCus, $adsRandom, $adsArray, $visibleContentAds;

    $off_default_ads = (strpos( $content, '<!--OffDef-->' ) !== false);

    if( $off_default_ads ) { // disabled default ads
        return $content;
    }
    // Create the array which contains the random ads
    $adsRandom = $adsArray;

//        echo '<pre>';
//        echo 'adsArrayCus: ';
//        print_r($adsArrayCus);
//        echo 'adsArray: ';
//        print_r( $adsArray );
//        echo '</pre>';

    for ( $i = 0; $i <= count( $adsArrayCus ); $i++ ) {
        if( isset( $adsArrayCus[$i] ) && strpos( $content, '<!--CusAds' . $adsArrayCus[$i] . '-->' ) !== false && in_array( $adsArrayCus[$i], $adsArray ) ) {
            $content = quads_replace_ads( $content, 'CusAds' . $adsArrayCus[$i], $adsArrayCus[$i] );
            // Create array $adsRandom for quads_parse_random_ads() parsing functions to make sure that the random function 
            // is never using ads that are already used on static ad spots which are generated with quads_parse_default_ads()
            if ($i == 0){
                $adsRandom = quads_del_element($adsRandom, array_search($adsArrayCus[$i], $adsRandom));
            }else{
                $adsRandom = quads_del_element($adsRandom, array_search($adsArrayCus[$i-1], $adsRandom));
            }
            
            $visibleContentAds += 1;
            //quads_set_ad_count_content();
            //if( quads_ad_reach_max_count() || $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
            //wp_die(quads_get_max_allowed_post_ads( $content ));
            if( $visibleContentAds >= quads_get_max_allowed_post_ads( $content )  ) {
                $content = quads_clean_tags( $content );
            }
        }
    }
    return $content;
}

/**
 * Replace ad code in content
 * 
 * @global type $quads_options
 * @param string $content
 * @param string $quicktag Quicktag
 * @param string $id id of the ad
 * @return type
 */
function quads_replace_ads($content, $quicktag, $id) {
    	global $quads_options;
   

	if( strpos($content,'<!--'.$quicktag.'-->')===false ) { 
            return $content; 
        }

        
	if ($id != -1) {
                
                $code = !empty($quads_options['ads']['ad' . $id ]['code']) ? $quads_options['ads']['ad' . $id ]['code'] : '';
                $style = quads_get_inline_ad_style($id);
                $adscode =
			"\n".'<!-- WP QUADS Content Ad Plugin v. ' . QUADS_VERSION .' -->'."\n".
			'<div class="quads-location quads-ad' .$id. '" id="quads-ad' .$id. '" style="'.$style.'">'."\n".
			quads_render_ad('ad'.$id, $code)."\n".
			'</div>'. "\n";
              
	} else {
		$adscode ='';
	}	
	$cont = explode('<!--'.$quicktag.'-->', $content, 2);
        
	return $cont[0].$adscode.$cont[1];
}

/**
 * Get ad inline style
 * 
 * @global arr $quads_options
 * @param int $id id of the ad
 * @return string
 */
function quads_get_inline_ad_style( $id ) {
    global $quads_options;

    if( empty($id) ) {
        return '';
    }

    // Basic style
    $styleArray = array(
        'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
        'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
        'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
        'float:none;margin:%1$dpx;');
    
    // Alignment
    $adsalign = ( int )$quads_options['ads']['ad' . $id]['align'];
    
    // Margin
    $adsmargin = isset( $quads_options['ads']['ad' . $id]['margin'] ) ? $quads_options['ads']['ad' . $id]['margin'] : '3'; // default option = 3
    $margin = sprintf( $styleArray[$adsalign], $adsmargin );
    
    //wp_die($quads_options['ads']['ad' . $id]['margin']);
    //wp_die('ad'.$id);

    // Do not create any inline style on AMP site
    $style =  !quads_is_amp_endpoint() ? apply_filters( 'quads_filter_margins', $margin, 'ad' . $id ) : '';
    
    return $style;
}

/**
 * Revert content to original content any remove any processing helper strings
 * 
 * @global int $visibleContentAds
 * @global array $adsArray
 * @global array $quads_options
 * @global int $ad_count
 * @param string $content
 * @param boolean $trimonly
 * 
 * @return string content
 */
function quads_clean_tags($content, $trimonly = false) {
	global $visibleContentAds;
	global $adsArray;
        global $quads_options;
        global $ad_count;
        
	$tagnames = array('EmptyClear','RndAds','NoAds','OffDef','OffAds','OffWidget','OffBegin','OffMiddle','OffEnd','OffBfMore','OffAfLastPara','CusRnd');

        for($i=1;$i<=10;$i++) { 
            array_push($tagnames, 'CusAds'.$i); 
            array_push($tagnames, 'Ads'.$i); 
        };
        
        
	foreach ($tagnames as $tags) {
		if(strpos($content,'<!--'.$tags.'-->')!==false || $tags=='EmptyClear') {
			if($trimonly) {
				$content = str_replace('<p><!--'.$tags.'--></p>', '<!--'.$tags.'-->', $content);	
			}else{
				$content = str_replace(array('<p><!--'.$tags.'--></p>','<!--'.$tags.'-->'), '', $content);	
				$content = str_replace("##QA-TP1##", "<p></p>", $content);
				$content = str_replace("##QA-TP2##", "<p>&nbsp;</p>", $content);
			}
		}
	}
	if(!$trimonly && (is_single() || is_page()) ) {
		$visibleContentAds = 0;
		$adsArray = array();
	}	
	return $content;
}



/**
 * Remove element from array
 * 
 * @param array $paragraphsArrayay
 * @param int $idx key to remove from array
 * @return array
 */
function quads_del_element($array, $idx) {
  $copy = array();
	for( $i=0; $i<count($array) ;$i++) {
		if ( $idx != $i ) {
			array_push($copy, $array[$i]);
		}
	}	
  return $copy;
}