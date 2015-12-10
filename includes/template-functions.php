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

// Globals
$ShownAds = 0; // Amount of ads which are shown
$AdsId = array(); // Array of active ad id's
$beginend = 0; //

function quads_process_content($content)
{
	global $quads_options, $ShownAds, $AdsId, $beginend;
        
        // Declare the global vars here again. 
        // Filter 'the_content' is not able to access from outside to these globals
        $ShownAds = 0; // Amount of ads which are shown
        $AdsId = array(); // Array of active ad id's
        $beginend = 0; //
        $adWidgets = 10; // number of widgets
        $numberAds =  10; // number of regular ads
        $AdsWidName = 'AdsWidget%d (Quick Adsense Reloaded)';

        
	/* verifying */ 
	if(	(is_feed()) ||
                (strpos($content,'<!--NoAds-->')!==false) ||
                (strpos($content,'<!--OffAds-->')!==false) ||
                (is_single() && !( isset( $quads_options['visibility']['AppPost'] ) ) ) ||
                (is_page() && !( isset($quads_options['visibility']['AppPage'] ) ) ) ||
                (is_home() && !( isset( $quads_options['visibility']['AppHome'] ) ) ) ||			
                (is_category() && !(isset( $quads_options['visibility']['AppCate'] ) ) ) ||
                (is_archive() && !( isset($quads_options['visibility']['AppArch'] ) ) ) ||
                (is_tag() && !( isset($quads_options['visibility']['AppTags'] ) ) ) ||
                (is_user_logged_in() && ( isset($quads_options['visibility']['AppLogg'] ) ) ) ) 
        {
                    $content = quads_clean_tags($content); 
                    return $content; 
	}
	
	$AdsToShow = $quads_options['maxads'];
	if (strpos($content,'<!--OffWidget-->')===false) {
		for($i=1;$i<=$adWidgets;$i++) {
			$wadsid = sanitize_title(str_replace(array('(',')'),'',sprintf($AdsWidName,$i))); 
                        $AdsToShow -= (is_active_widget('', '',  $wadsid)) ? 1 : 0 ; 
                        //echo "<br>single:" . $AdsToShow .'<br>';
		}
                //echo "<br>total:" . $AdsToShow . ' shownad: ' . $ShownAds;
	}

	if( $ShownAds >= $AdsToShow ) { // ShownAds === 0 or larger/equal than $AdsToShow
            $content = quads_clean_tags($content); 
            return $content; 
        };

        //if( !count($AdsId) ) { // 
	if( count($AdsId) === 0 ) { //   
		for($i=1;$i<=$numberAds;$i++) { 
                        $tmp = trim($quads_options['ad' . $i]['code']);

			if( !empty($tmp) ) {
                                $AdsId[] = $i;
				//array_push($AdsId, $i); //Is throwing error because $AdsId is no array when 0 rhe     
			}
		}
                //var_dump($AdsId);
	}

	if( count($AdsId) === 0 ) { // No ads, so break here
            $content = quads_clean_tags($content); 
            return $content; 
        };

	/* ... Tidy up content ... */
	$content = str_replace("<p></p>", "##QA-TP1##", $content);
	$content = str_replace("<p>&nbsp;</p>", "##QA-TP2##", $content);	
	$offdef = (strpos($content,'<!--OffDef-->')!==false);
	if( !$offdef ) {

		$AdsIdCus = array();
		$cusads = 'CusAds'; 
                $cusrnd = 'CusRnd';
                
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
                
               
		$rc=3;
                $default = 5;
		for($i=1;$i<=$rc;$i++) { 

                        $key = $default +$i; // 6;7;8
                        
                        $para1[$i] = isset($quads_options['pos' . $key]['Par'.$i .'Ads']) ? $quads_options['pos' . $key]['Par'.$i .'Ads'] : 0;
                        $para2[$i] = isset($quads_options['pos' . $key]['Par'.$i .'Rnd']) ? $quads_options['pos' . $key]['Par'.$i .'Rnd'] : 0;	
                        $para3[$i] = isset($quads_options['pos' . $key]['Par'.$i .'Nup']) ? $quads_options['pos' . $key]['Par'.$i .'Nup'] : 0;	
                        $para4[$i] = isset($quads_options['pos' . $key]['Par'.$i .'Con']) ? $quads_options['pos' . $key]['Par'.$i .'Con'] : 0;
                        
		}              
                
		$imageActive    = isset($quads_options['pos9']['Img1Ads']) ? $quads_options['pos9']['Img1Ads'] : false;	
                $imageAdNo      = isset($quads_options['pos9']['Img1Rnd']) ? $quads_options['pos9']['Img1Rnd'] : false;	
                $imageNo        = isset($quads_options['pos9']['Img1Nup']) ? $quads_options['pos9']['Img1Nup'] : false; 
                $imageCaption   = isset($quads_options['pos9']['Img1Con']) ? $quads_options['pos9']['Img1Con'] : false;	
                

                if ( $begn2 == 0 ) { $b1 = $cusrnd; } else { $b1 = $cusads.$begn2; array_push($AdsIdCus, $begn2); };
		if ( $more2 == 0 ) { $r1 = $cusrnd; } else { $r1 = $cusads.$more2; array_push($AdsIdCus, $more2); };		
		if ( $midd2 == 0 ) { $m1 = $cusrnd; } else { $m1 = $cusads.$midd2; array_push($AdsIdCus, $midd2); };
		if ( $lapa2 == 0 ) { $g1 = $cusrnd; } else { $g1 = $cusads.$lapa2; array_push($AdsIdCus, $lapa2); };
		if ( $endi2 == 0 ) { $b2 = $cusrnd; } else { $b2 = $cusads.$endi2; array_push($AdsIdCus, $endi2); };
 
		for($i=1;$i<=$rc;$i++) { 
			if ( $para2[$i] == 0 ) { $b3[$i] = $cusrnd; } else { $b3[$i] = $cusads.$para2[$i]; array_push($AdsIdCus, $para2[$i]); };	
		}
                
                // Check if image ad is random one
		if ( $imageAdNo == 0 ) { 
                    $b4 = $cusrnd;
                    } else { 
                        $b4 = $cusads.$imageAdNo; 
                        array_push($AdsIdCus, $imageAdNo); 
                };
                
                // Check if image ad is middle one
		if( $midd1 && strpos($content,'<!--OffMiddle-->')===false) {
			if( substr_count(strtolower($content), '</p>')>=2 ) {
				$sch = "</p>";
				$content = str_replace("</P>", $sch, $content);
				$arr = explode($sch, $content);			
				$nn = 0; $mm = strlen($content)/2;
				for($i=0;$i<count($arr);$i++) {
					$nn += strlen($arr[$i]) + 4;
					if($nn>$mm) {
						if( ($mm - ($nn - strlen($arr[$i]))) > ($nn - $mm) && $i+1<count($arr) ) {
							$arr[$i+1] = '<!--'.$m1.'-->'.$arr[$i+1];							
						} else {
							$arr[$i] = '<!--'.$m1.'-->'.$arr[$i];
						}
						break;
					}
				}
				$content = implode($sch, $arr);
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
			$arr = explode($sch, $content);
			if ( count($arr) > 2 ) {
				$content = implode($sch, array_slice($arr, 0, count($arr)-1)) .'<!--'.$g1.'-->'. $sch. $arr[count($arr)-1];
			}
		}
		for($i=$rc;$i>=1;$i--) { 
			if ( $para1[$i] ){
				$sch = "</p>";
				$content = str_replace("</P>", $sch, $content);
				$arr = explode($sch, $content);
				if ( (int)$para3[$i] < count($arr) ) {
					$content = implode($sch, array_slice($arr, 0, $para3[$i])).$sch .'<!--'.$b3[$i].'-->'. implode($sch, array_slice($arr, $para3[$i]));
				}	elseif ($para4[$i]) {
					$content = implode($sch, $arr).'<!--'.$b3[$i].'-->';
				}
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
			$arr = explode($imgtag, $content);
			if ( (int)$imageNo < count($arr) ) {
				$arrImages = explode($delimiter, $arr[$imageNo]);
				if ( count($arrImages) > 1 ) {
					$tss = explode($caption, $arr[$imageNo]);
					$ccp = ( count($tss) > 1 ) ? strpos(strtolower($tss[0]),'[caption ')===false : false ;
					$arrAtag = explode($atag, $arr[$imageNo]);
					$cdu = ( count($arrAtag) > 1 ) ? strpos(strtolower($arrAtag[0]),'<a href')===false : false ;					
					if ( $imageCaption && $ccp ) {
						$arr[$imageNo] = implode($caption, array_slice($tss, 0, 1)).$caption. "\r\n".'<!--'.$b4.'-->'."\r\n". implode($caption, array_slice($tss, 1));
					}else if ( $cdu ) {
						$arr[$imageNo] = implode($atag, array_slice($arrAtag, 0, 1)).$atag. "\r\n".'<!--'.$b4.'-->'."\r\n". implode($atag, array_slice($arrAtag, 1));
					}else{
						$arr[$imageNo] = implode($delimiter, array_slice($arrImages, 0, 1)).$delimiter. "\r\n".'<!--'.$b4.'-->'."\r\n". implode($delimiter, array_slice($arrImages, 1));
					}
				}
				$content = implode($imgtag, $arr);
			}	
		}		
	}
	
	/* ... Tidy up content ... */
	$content = '<!--EmptyClear-->'.$content."\n".'<div style="font-size:0px;height:0px;line-height:0px;margin:0;padding:0;clear:both"></div>';
	$content = quads_clean_tags($content, true);	
	$ismany = (!is_single() && !is_page());
	$showall = isset($quads_options['visibility']['AppMaxA']) ? $quads_options['visibility']['AppMaxA'] : 0;
	
	/* ... Replace Beginning/Middle/End Ads1-10 ... */
	if( !$offdef ) {
		for( $i=1; $i<=count($AdsIdCus); $i++ ) {
			if( $showall || !$ismany || $beginend != $i ) {
				if( strpos($content,'<!--'.$cusads.$AdsIdCus[$i-1].'-->')!==false && in_array($AdsIdCus[$i-1], $AdsId)) {
					$content = quads_replace_ads( $content, $cusads.$AdsIdCus[$i-1], $AdsIdCus[$i-1] ); 
                                        $AdsId = quads_del_element($AdsId, array_search($AdsIdCus[$i-1], $AdsId)) ;
					$ShownAds += 1; 
                                        if( $ShownAds >= $AdsToShow || !count($AdsId) ){ 
                                            $content = quads_clean_tags($content); 
                                            return $content; 
                                        };
					$beginend = $i; 
                                        if(!$showall && $ismany){
                                            break;
                                        } 
				}
			}	
		}	
	}
	
	/* ... Replace Ads1 to Ads10 ... */
	if( $showall || !$ismany ) {
		$tcn = count($AdsId); $tt = 0;
		for( $i=1; $i<=$tcn; $i++ ) {
			if( strpos($content, '<!--Ads'.$AdsId[$tt].'-->')!==false ) {
				$content = quads_replace_ads( $content, 'Ads'.$AdsId[$tt], $AdsId[$tt] ); $AdsId = quads_del_element($AdsId, $tt) ;
				$ShownAds += 1; 
                                if( $ShownAds >= $AdsToShow || !count($AdsId) ){ 
                                    $content = clean_tags($content); 
                                    return $content; 
                                    
                                };
			} else {
				$tt += 1;
			}
		}	
	}	

	/* ... Replace Beginning/Middle/End random Ads ... */
	if( strpos($content, '<!--'.$cusrnd.'-->')!==false && ($showall || !$ismany) ) {
		$tcx = count($AdsId);
		$tcy = substr_count($content, '<!--'.$cusrnd.'-->');
		for( $i=$tcx; $i<=$tcy-1; $i++ ) {
			array_push($AdsId, -1);
		}
		shuffle($AdsId);
		for( $i=1; $i<=$tcy; $i++ ) {
			$content = quads_replace_ads( $content, $cusrnd, $AdsId[0] ); $AdsId = quads_del_element($AdsId, 0) ;
			$ShownAds += 1; if( $ShownAds >= $AdsToShow || !count($AdsId) ){ $content = quads_clean_tags($content); return $content; };
		}
	}
	
	/* ... Replace RndAds ... */
	if( strpos($content, '<!--RndAds-->')!==false && ($showall || !$ismany) ) {
		$AdsIdTmp = array();
		shuffle($AdsId);
		for( $i=1; $i<=$AdsToShow-$ShownAds; $i++ ) {
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
			$content = quads_replace_ads( $content, 'RndAds', $AdsIdTmp[0] ); $AdsIdTmp = quads_del_element($AdsIdTmp, 0) ;
			if($tmp != -1){$ShownAds += 1;}; if( $ShownAds >= $AdsToShow || !count($AdsIdTmp) ){ $content = clean_tags($content); return $content; };
		}
	}	

	/* ... That's it. DONE :) ... */
	$content = quads_clean_tags($content); 
        return $content;
}

function quads_clean_tags($content, $trimonly = false) {
	global $QData;
	global $ShownAds;
	global $AdsId;
	global $beginend;
        global $quads_options;
        
	$tagnames = array('EmptyClear','RndAds','NoAds','OffDef','OffAds','OffWidget','OffBegin','OffMiddle','OffEnd','OffBfMore','OffAfLastPara','CusRnd');

        for($i=1;$i<=10;$i++) { array_push($tagnames, 'CusAds'.$i); array_push($tagnames, 'Ads'.$i); };
        
        
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
		$ShownAds = 0;
		$AdsId = array();
		$beginend = 0;
	}	
	return $content;
}
function quads_replace_ads($content, $nme, $adn) {
	if( strpos($content,'<!--'.$nme.'-->')===false ) { return $content; }	
	global $quads_options;

	if ($adn != -1) {
		$arr = array(
			'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
			'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
			'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
			'float:none;margin:0px;');
		$adsalign = $quads_options['ad' . $adn]['align'];
		$adsmargin = $quads_options['ad' . $adn]['margin'];
		$style = sprintf($arr[(int)$adsalign], $adsmargin);
                $adscode = $quads_options['ad' . $adn ]['code'];

		$adscode =
			"\n".'<!-- Quick AdSense Reloaded Plugin v. ' . QUADS_VERSION .' -->'."\n".
			'<div style="'.$style.'">'."\n".
			$adscode."\n".
			'</div>'."\n";
	} else {
		$adscode ='';
	}	
	$cont = explode('<!--'.$nme.'-->', $content, 2);	
	return $cont[0].$adscode.$cont[1];
}
function quads_del_element($array, $idx) {
  $copy = array();
	for( $i=0; $i<count($array) ;$i++) {
		if ( $idx != $i ) {
			array_push($copy, $array[$i]);
		}
	}	
  return $copy;
}
add_filter('the_content', 'quads_process_content');

/**
 * Check if the maximum amount of ads are reached
 * 
 * @global int number of already actived ads
 * @var int amount of ads to activate 

 * @return bool true if max is reached
 * @deprecated since version 0.9.2
 */
function quads_reached_maxads($ShownAds){
    global $ShownAds; 
    if ($ShownAds >= $AdsToShow)
        return true;
}

/**
 * Check if the maximum amount of ads are reached and increment $ShownAds
 * 
 * @global int number of already actived ads
 * @var int amount of ads to activate 
 * @return bool true if max is reached
 * 
 * @deprecated since version 0.9.2
 */
/*function quads_reached_maxads_incr($ShownAds){
    global $ShownAds; 
    
    $ShownAds += 1;
    if ($ShownAds >= $AdsToShow)
        return true;
}*/


