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
 * Adds quicktags, defined via post meta options, to content.
 *
 * @param $content Post content
 *
 * @return string
 */
function quads_post_settings_to_quicktags ( $content ) {
    
        // Return original content if QUADS is not allowed
        if ( !quads_ad_is_allowed($content) )
            return $content;
    
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
function quads_get_visibility_quicktags_str ( $post_id = null ) {
    
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

        $str = '';
        if (false === $post_id){        
            return $str;
        }
        
	$config = get_post_meta( $post_id, '_quads_config_visibility', true );
        
        
        if ( !empty($config))
	foreach ( $config as $qtag_id => $qtag_label ) {
		$str .= '<!--' . $qtag_id . '-->';
	}
        
	return $str;
}

/**
 * Main processing for the_content filter
 * 
 * @global arr $quads_options all plugin settings
 * @global int $visibleContentAds number of active content ads (reseted internally so we have to use a similar global below for external purposes: $visibleContentAdsGlobal)
 * @global arr $AdsId Whitespace trimmed array of ad codes
 * @global int $numberWidgets number of ad widgets
 * @global int $numberAds number of maximum available ads
 * @global string $AdsWidName name of widget
 * @global int $visibleContentAdsGlobal   number of active content ads
 * @global int $visibleShortcodeAds number of active shortcode ads
 * @param string $content
 * 
 * @return string
 * 
 * @since 0.9.0
 */

function quads_process_content($content){
    global $quads_options, $visibleContentAds, $AdsId, $numberWidgets, $numberAds, $AdsWidName, $visibleContentAdsGlobal, $visibleShortcodeAds;
        
        // Return original content if QUADS is not allowed
        if ( !quads_ad_is_allowed($content) ) {
            $content = quads_clean_tags($content); 
            return $content;
        }
        // Maximum allowed ads 
        $maxAds = isset($quads_options['maxads']) ? $quads_options['maxads'] : 10;

	if (strpos($content,'<!--OffWidget-->')===false) {
		for($i=1;$i<=$numberWidgets;$i++) {
			$wadsid = sanitize_title(str_replace(array('(',')'),'',sprintf($AdsWidName,$i))); 
                        $maxAds -= (is_active_widget('', '',  $wadsid)) ? 1 : 0 ; 
		}
	}

        // Return here if max visible ads are exceeded
	if( $visibleContentAds+$visibleShortcodeAds >= $maxAds ) { // ShownAds === 0 or larger/equal than $maxAds
            $content = quads_clean_tags($content); 
            return $content; 
        };

        // Create array of valid id's
        if( count( $AdsId ) === 0 ) { //   
            for ( $i = 1; $i <= $numberAds; $i++ ) {
                $tmp = isset($quads_options['ad' . $i]['code']) ? trim( $quads_options['ad' . $i]['code'] ) : '';
                // id is valid if there is either the plain text field populated or the adsense ad slot and the ad client id
                if( !empty( $tmp ) || (!empty($quads_options['ad' . $i]['g_data_ad_slot']) && !empty($quads_options['ad' . $i]['g_data_ad_client'] ) ) ) {
                    $AdsId[] = $i;
                }
            }
//            echo '<pre>';
//            wp_die(var_dump($AdsId));
        }

        // Ad code array is empty so break here
	if( count($AdsId) === 0 ) { 
            $content = quads_clean_tags($content); 
            return $content; 
        };

	/* ... Tidy up content ... */
        // Replace all <p></p> tags with placeholder ##QA-TP1##
        // Replace all 
	$content = str_replace("<p></p>", "##QA-TP1##", $content);
        // Replace all <p>&nbsp;</p> tags with placeholder ##QA-TP2##
	$content = str_replace("<p>&nbsp;</p>", "##QA-TP2##", $content);	
	$off_default_ads = (strpos($content,'<!--OffDef-->')!==false);
        
	if( !$off_default_ads ) { // disabled default positioned ads

		$AdsIdCus = array(); // ids of used ads
                
                $cusrnd = 'CusRnd'; // placeholder string for random ad
                $cusads = 'CusAds';  // placeholder string for custom ad spots
                
                $begn1 = isset($quads_options['pos1']['BegnAds']) ? true : false; 
                $begn2 = isset($quads_options['pos1']['BegnRnd']) ? $quads_options['pos1']['BegnRnd'] : 0;

		$midd1 = isset($quads_options['pos2']['MiddAds']) ? true : false; 
                $midd2 = isset($quads_options['pos2']['MiddRnd']) ? $quads_options['pos2']['MiddRnd'] : 0;

		$endi1 = isset($quads_options['pos3']['EndiAds']) ? true : false; 
                $endi2 = isset($quads_options['pos3']['EndiRnd']) ? $quads_options['pos3']['EndiRnd'] : 0;
                
		$more1 = isset($quads_options['pos4']['MoreAds']) ? true : false; 
                $more2 = isset($quads_options['pos4']['MoreRnd']) ? $quads_options['pos4']['MoreRnd'] : 0;
                
		$lapa1 = isset($quads_options['pos5']['LapaAds']) ? true : false; 
                $lapa2 = isset($quads_options['pos5']['LapaRnd'])? $quads_options['pos5']['LapaRnd'] : 0 ;	
                
               

		$number = 3; // number of paragraph ads | default value 3. 
                $default = 5; // Position. Let's start with id 5
		for($i=1;$i<=$number;$i++) { 

                        $key = $default +$i; // 6,7,8
                        
                        $paragraph['status'][$i] = isset($quads_options['pos' . $key]['Par'.$i .'Ads']) ? $quads_options['pos' . $key]['Par'.$i .'Ads'] : 0; // Status - active | inactive
                        $paragraph['id'][$i] = isset($quads_options['pos' . $key]['Par'.$i .'Rnd']) ? $quads_options['pos' . $key]['Par'.$i .'Rnd'] : 0; // Ad id	
                        $paragraph['position'][$i] = isset($quads_options['pos' . $key]['Par'.$i .'Nup']) ? $quads_options['pos' . $key]['Par'.$i .'Nup'] : 0; // Paragraph No	
                        $paragraph['end_post'][$i] = isset($quads_options['pos' . $key]['Par'.$i .'Con']) ? $quads_options['pos' . $key]['Par'.$i .'Con'] : 0; // End of post - yes | no
                        
		}              
                // Position 9 Ad after Image
		$imageActive    = isset($quads_options['pos9']['Img1Ads']) ? $quads_options['pos9']['Img1Ads'] : false;	
                $imageAdNo      = isset($quads_options['pos9']['Img1Rnd']) ? $quads_options['pos9']['Img1Rnd'] : false;	
                $imageNo        = isset($quads_options['pos9']['Img1Nup']) ? $quads_options['pos9']['Img1Nup'] : false; 
                $imageCaption   = isset($quads_options['pos9']['Img1Con']) ? $quads_options['pos9']['Img1Con'] : false;	
                

                if ( $begn2 == 0 ) { $b1 = $cusrnd; } else { $b1 = $cusads.$begn2; array_push($AdsIdCus, $begn2); };
		if ( $more2 == 0 ) { $r1 = $cusrnd; } else { $r1 = $cusads.$more2; array_push($AdsIdCus, $more2); };		
		if ( $midd2 == 0 ) { $m1 = $cusrnd; } else { $m1 = $cusads.$midd2; array_push($AdsIdCus, $midd2); };
		if ( $lapa2 == 0 ) { $g1 = $cusrnd; } else { $g1 = $cusads.$lapa2; array_push($AdsIdCus, $lapa2); };
		if ( $endi2 == 0 ) { $b2 = $cusrnd; } else { $b2 = $cusads.$endi2; array_push($AdsIdCus, $endi2); };
 
		for($i=1;$i<=$number;$i++) { 
			if ( $paragraph['id'][$i] == 0 ) { 
                            $paragraph[$i] = $cusrnd; 
                        } else { 
                            $paragraph[$i] = $cusads.$paragraph['id'][$i]; 
                            array_push($AdsIdCus, $paragraph['id'][$i]); 
                            
                        };	
		}
                
                // Create the arguments for filter quads_filter_paragraphs
                $quads_args = array (
                                        'paragraph' => $paragraph,
                                        'cusads' => $cusads,
                                        'cusrnd' => $cusrnd,
                                        'AdsIdCus' => $AdsIdCus,

                        );

                // Execute filter to add more paragraph ad spots
                $quads_filtered = apply_filters('quads_filter_paragraphs', $quads_args);
                
                // The filtered arguments
                $paragraph = $quads_filtered['paragraph'];
                
                // filtered list of ad spots
                $AdsIdCus = $quads_filtered['AdsIdCus'];
                
                // Create paragraph ads
                $number = 6;
                //$number = 3;
		for($i=$number;$i>=1;$i--) { 
			if ( !empty($paragraph['status'][$i]) ){
                            
				$sch = "</p>";
				$content = str_replace("</P>", $sch, $content);
                                // paragraphs in content
				$paragraphsArray = explode($sch, $content);
				if ( (int)$paragraph['position'][$i] < count($paragraphsArray) ) {
					$content = implode($sch, array_slice($paragraphsArray, 0, $paragraph['position'][$i])).$sch .'<!--'.$paragraph[$i].'-->'. implode($sch, array_slice($paragraphsArray, $paragraph['position'][$i]));
				}	elseif ($paragraph['end_post'][$i]) {
					$content = implode($sch, $paragraphsArray).'<!--'.$paragraph[$i].'-->';
				}
			}
		}

        // Check if image ad is random one
		if ( $imageAdNo == 0 ) { 
                    $imageAd = $cusrnd;
                    } else { 
                        $imageAd = $cusads.$imageAdNo; 
                        array_push($AdsIdCus, $imageAdNo);
                };
                
                // Check if image ad is middle one
		if( $midd1 && strpos($content,'<!--OffMiddle-->')===false) {
			if( substr_count(strtolower($content), '</p>')>=2 ) {
				$sch = "</p>";
				$content = str_replace("</P>", $sch, $content);
				$paragraphsArray = explode($sch, $content);			
				$nn = 0; $mm = strlen($content)/2;
				for($i=0;$i<count($paragraphsArray);$i++) {
					$nn += strlen($paragraphsArray[$i]) + 4;
					if($nn>$mm) {
						if( ($mm - ($nn - strlen($paragraphsArray[$i]))) > ($nn - $mm) && $i+1<count($paragraphsArray) ) {
							$paragraphsArray[$i+1] = '<!--'.$m1.'-->'.$paragraphsArray[$i+1];							
						} else {
							$paragraphsArray[$i] = '<!--'.$m1.'-->'.$paragraphsArray[$i];
						}
						break;
					}
				}
				$content = implode($sch, $paragraphsArray);
			}	
		}
                
                // Check if image ad is "More Tag" one
		if( $more1 && strpos($content,'<!--OffAfMore-->')===false) {
			$mmr = '<!--'.$r1.'-->';
			$postid = get_the_ID();
			$content = str_replace('<span id="more-'.$postid.'"></span>', $mmr, $content);		
		}	

		if( $begn1 && strpos($content,'<!--OffBegin-->')===false) {
			$content = '<!--'.$b1.'-->'.$content;
		}
		if( $endi1 && strpos($content,'<!--OffEnd-->')===false) {
			$content = $content.'<!--'.$b2.'-->';
		}
		if( $lapa1 && strpos($content,'<!--OffBfLastPara-->')===false){
			$sch = "<p>";
			$content = str_replace("<P>", $sch, $content);
			$paragraphsArray = explode($sch, $content);
			if ( count($paragraphsArray) > 2 ) {
				$content = implode($sch, array_slice($paragraphsArray, 0, count($paragraphsArray)-1)) .'<!--'.$g1.'-->'. $sch. $paragraphsArray[count($paragraphsArray)-1];
			}
		}

		if ( $imageActive ){

                        // Sanitation
			$imgtag = "<img"; 
                        $delimiter = ">"; 
                        $caption = "[/caption]"; 
                        $atag = "</a>";			
			$content = str_replace("<IMG", $imgtag, $content);
			$content = str_replace("</A>", $atag, $content);
                        
                        // Start
			$paragraphsArray = explode($imgtag, $content);
			if ( (int)$imageNo < count($paragraphsArray) ) {
				$paragraphsArrayImages = explode($delimiter, $paragraphsArray[$imageNo]);
				if ( count($paragraphsArrayImages) > 1 ) {
					$tss = explode($caption, $paragraphsArray[$imageNo]);
					$ccp = ( count($tss) > 1 ) ? strpos(strtolower($tss[0]),'[caption ')===false : false ;
					$paragraphsArrayAtag = explode($atag, $paragraphsArray[$imageNo]);
					$cdu = ( count($paragraphsArrayAtag) > 1 ) ? strpos(strtolower($paragraphsArrayAtag[0]),'<a href')===false : false ;					
					if ( $imageCaption && $ccp ) {
						$paragraphsArray[$imageNo] = implode($caption, array_slice($tss, 0, 1)).$caption. "\r\n".'<!--'.$imageAd.'-->'."\r\n". implode($caption, array_slice($tss, 1));
					}else if ( $cdu ) {
						$paragraphsArray[$imageNo] = implode($atag, array_slice($paragraphsArrayAtag, 0, 1)).$atag. "\r\n".'<!--'.$imageAd.'-->'."\r\n". implode($atag, array_slice($paragraphsArrayAtag, 1));
					}else{
						$paragraphsArray[$imageNo] = implode($delimiter, array_slice($paragraphsArrayImages, 0, 1)).$delimiter. "\r\n".'<!--'.$imageAd.'-->'."\r\n". implode($delimiter, array_slice($paragraphsArrayImages, 1));
					}
				}
				$content = implode($imgtag, $paragraphsArray);
			}	
		}		
	} // end disabled default positioned ads
	
	/* 
         * Tidy up content
         */
	$content = '<!--EmptyClear-->'.$content."\n".'<div style="font-size:0px;height:0px;line-height:0px;margin:0;padding:0;clear:both"></div>';
	$content = quads_clean_tags($content, true);

	
	/* 
         * Replace Beginning/Middle/End/Paragraph Ads1-10
         */

            if( !$off_default_ads ) { // disabled default ads
		for( $i=1; $i<=count($AdsIdCus); $i++ ) {

                    if( strpos($content,'<!--'.$cusads.$AdsIdCus[$i-1].'-->')!==false && in_array($AdsIdCus[$i-1], $AdsId)) {

                            $content = quads_replace_ads( $content, $cusads.$AdsIdCus[$i-1], $AdsIdCus[$i-1] ); 
                            // Comment this to allow the use of the same ad on several ad spots
                            //$AdsId = quads_del_element($AdsId, array_search($AdsIdCus[$i-1], $AdsId)) ;
                            $visibleContentAds += 1;

                            quads_set_ad_count_content();
                            if ( quads_ad_reach_max_count() ){
                                $content = quads_clean_tags($content); 
                            }
                    }	
                }	
            }
	
        /**
         * Replace Quicktags Ads1 to Ads10
         **/

		$tcn = count($AdsId); $tt = 0;
		for( $i=1; $i<=$tcn; $i++ ) {
			if( strpos($content, '<!--Ads'.$AdsId[$tt].'-->')!==false ) {
				$content = quads_replace_ads( $content, 'Ads'.$AdsId[$tt], $AdsId[$tt] ); 
                                $AdsId = quads_del_element($AdsId, $tt) ;
				$visibleContentAds += 1; 
                            quads_set_ad_count_content();
                            if (quads_ad_reach_max_count()){
                                $content = quads_clean_tags($content); 
                                return $content;
                            }

			} else {
				$tt += 1;
			}
		}	
	
       

    /* ... Replace Beginning/Middle/End random Ads ... */
    if( !$off_default_ads ) {
        if( strpos($content, '<!--'.$cusrnd.'-->')!==false && is_singular() ) {
		$tcx = count($AdsId);
		$tcy = substr_count($content, '<!--'.$cusrnd.'-->');

		for( $i=$tcx; $i<=$tcy-1; $i++ ) {
			array_push($AdsId, -1);
		}
		shuffle($AdsId);
		for( $i=1; $i<=$tcy; $i++ ) {
			$content = quads_replace_ads( $content, $cusrnd, $AdsId[0] ); 
                        $AdsId = quads_del_element($AdsId, 0) ;
			$visibleContentAds += 1; 
                        quads_set_ad_count_content();
                            if (quads_ad_reach_max_count()){
                                $content = quads_clean_tags($content); 
                                return $content;
                            }
		}
	}
    }
	
	/*
         * Replace RndAds Random Ads
         */
	if( strpos($content, '<!--RndAds-->')!==false && is_singular() ) {
		$AdsIdTmp = array();
		shuffle($AdsId);
		for( $i=1; $i<=$maxAds-$visibleContentAds; $i++ ) {
			if( $i <= count($AdsId) ) {
				array_push($AdsIdTmp, $AdsId[$i-1]);
			}
		}
		$tcx = count($AdsIdTmp);
		$tcy = substr_count($content, '<!--RndAds-->');
 		for( $i=$tcx; $i<=$tcy-1; $i++ ) {
			array_push($AdsIdTmp, -1);
		}
		shuffle($AdsIdTmp);
		for( $i=1; $i<=$tcy; $i++ ) {
			$tmp = $AdsIdTmp[0];
			$content = quads_replace_ads( $content, 'RndAds', $AdsIdTmp[0] ); 
                        $AdsIdTmp = quads_del_element($AdsIdTmp, 0) ;
			if($tmp != -1){
                            $visibleContentAds += 1;
                        }; 
                        quads_set_ad_count_content();
                            if (quads_ad_reach_max_count()){
                                $content = quads_clean_tags($content); 
                                return $content;
                            }
		}
	}	       

        /* ... That's it. DONE :) ... */
	$content = quads_clean_tags($content); 
        // Reset ad_count - Important!!!
        $visibleContentAdsGlobal = 0; 
        return do_shortcode($content);
    }
/**
 * Revert content into original content without any ad code
 * 
 * @global int $visibleContentAds
 * @global array $AdsId
 * @global array $quads_options
 * @global int $ad_count
 * @param string $content
 * @param boolean $trimonly
 * 
 * @return string content
 */
function quads_clean_tags($content, $trimonly = false) {
	global $visibleContentAds;
	global $AdsId;
        global $quads_options;
        global $ad_count;
        
	$tagnames = array('EmptyClear','RndAds','NoAds','OffDef','OffAds','OffWidget','OffBegin','OffMiddle','OffEnd','OffBfMore','OffAfLastPara','CusRnd');

        for($i=1;$i<=10;$i++) { 
            array_push($tagnames, 'CusAds'.$i); 
            array_push($tagnames, 'Ads'.$i); 
        };
        
        
	foreach ($tagnames as $tgn) {
		if(strpos($content,'<!--'.$tgn.'-->')!==false || $tgn=='EmptyClear') {
			if($trimonly) {
				$content = str_replace('<p><!--'.$tgn.'--></p>', '<!--'.$tgn.'-->', $content);	
			}else{
				$content = str_replace(array('<p><!--'.$tgn.'--></p>','<!--'.$tgn.'-->'), '', $content);	
				$content = str_replace("##QA-TP1##", "<p></p>", $content);
				$content = str_replace("##QA-TP2##", "<p>&nbsp;</p>", $content);
			}
		}
	}
	if(!$trimonly && (is_single() || is_page()) ) {
		$visibleContentAds = 0;
		$AdsId = array();
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
		$paragraphsArray = array(
			'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
			'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
			'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
			'float:none;margin:0px;');
                
		$adsalign = $quads_options['ad' . $id]['align'];
		$adsmargin = isset($quads_options['ad' . $id]['margin']) ? $quads_options['ad' . $id]['margin'] : '3'; // default
                $margin = sprintf($paragraphsArray[(int)$adsalign], $adsmargin);
                
                // Do not create any inline style on AMP site
		$style = !quads_is_amp_endpoint() ? apply_filters ('quads_filter_margins', $margin, 'ad'.$id ) : '';
                $adscode = $quads_options['ad' . $id ]['code'];
                
                $adscode =
			"\n".'<!-- WP QUADS Content Ad Plugin v. ' . QUADS_VERSION .' -->'."\n".
			'<div class="quads-location quads-ad' .$id. '" id="quads-ad' .$id. '" style="'.$style.'">'."\n".
			quads_render_ad('ad'.$id, $adscode)."\n".
			'</div>'. "\n";
              
	} else {
		$adscode ='';
	}	
	$cont = explode('<!--'.$quicktag.'-->', $content, 2);
        
	return $cont[0].$adscode.$cont[1];
}

/**
 * Remove element from array
 * 
 * @param array $paragraphsArrayay
 * @param int $idx key to remove from array
 * @return array
 */
function quads_del_element($paragraphsArrayay, $idx) {
  $copy = array();
	for( $i=0; $i<count($paragraphsArrayay) ;$i++) {
		if ( $idx != $i ) {
			array_push($copy, $paragraphsArrayay[$i]);
		}
	}	
  return $copy;
}