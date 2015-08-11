<?php

/* 
	Plugin Name: Quick Adsense
	Plugin URI: http://quicksense.net/
	Description: Quick Adsense offers a quicker & flexible way to insert Google Adsense or any Ads code into a blog post.
	Author: Quicksense
	Version: 1.9.2
	Author URI: http://quicksense.net/
*/

/*	Copyright 2009-2013 BuySellAds [ http://quicksense.net/ ]

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/





global $QData;
$QData['AdsWid'] = 10; /* Ads on Widget */
$QData['Ads'] = 10; /* Ads on Post body */
$QData['Name'] = 'Quick Adsense';
$QData['Version'] = '1.9.2';
$QData['URI'] = 'http://quicksense.net/';
$QData['AdsWidName'] = 'AdsWidget%d (Quick Adsense)';
$QData['Default'] = array(
	'AdsDisp'=>'3',
	'BegnAds'=>true,'BegnRnd'=>'1','EndiAds'=>true,'EndiRnd'=>'0','MiddAds'=>false,'MiddRnd'=>'0','MoreAds'=>false,'MoreRnd'=>'0','LapaAds'=>false,'LapaRnd'=>'0',
	'Par1Ads'=>false,'Par1Rnd'=>'0','Par1Nup'=>'0','Par1Con'=>false,
	'Par2Ads'=>false,'Par2Rnd'=>'0','Par2Nup'=>'0','Par2Con'=>false,
	'Par3Ads'=>false,'Par3Rnd'=>'0','Par3Nup'=>'0','Par3Con'=>false,
	'Img1Ads'=>false,'Img1Rnd'=>'0','Img1Nup'=>'0','Img1Con'=>true,
	'AppPost'=>true,'AppPage'=>true,'AppHome'=>false,'AppCate'=>false,'AppArch'=>false,'AppTags'=>false,'AppMaxA'=>false,'AppSide'=>false,'AppLogg'=>false,
	'QckTags'=>true,'QckRnds'=>false,'QckOffs'=>false,'QckOfPs'=>false
); 
$QData['DefaultAdsOpt'] = array(
	'AdsMargin'=>'10','AdsAlign'=>'2'
);	
$QData['DefaultAdsName'] = array();
for ($i=1;$i<=$QData['Ads'];$i++) { 
	array_push($QData['DefaultAdsName'], 'AdsCode'.$i );
	array_push($QData['DefaultAdsName'], 'AdsAlign'.$i );
	array_push($QData['DefaultAdsName'], 'AdsMargin'.$i );
};
for ($i=1;$i<=$QData['AdsWid'];$i++) { 
	array_push($QData['DefaultAdsName'], 'WidCode'.$i );	
};	

function ads_admin_page_inc() { 
	include('quick-adsense-admin.php');
} 
function ads_admin_page() {  
	add_options_page("Quick Adsense Options", "Quick Adsense", 8, basename(__FILE__), "ads_admin_page_inc");  
} 
function register_ads_settings() {
	global $QData;
	foreach ($QData['Default'] as $key => $value) {
		register_setting( 'qa-options', $key);
	}			
	foreach ($QData['DefaultAdsName'] as $key => $value) {
		register_setting( 'qa-options', $value);
	}		
}
if(is_admin()) {
	add_action('admin_menu', 'ads_admin_page');
	add_action('admin_init', 'register_ads_settings');
}	


function ads_plugin_links($links,$file) {
	if($file==plugin_basename(__FILE__)) {
		array_unshift($links,'<a href="options-general.php?page='.basename(__FILE__).'">'.__('Setting').'</a>');
	}
	return $links;
}
add_filter('plugin_action_links','ads_plugin_links',10,2);


function plugin_activated() {
	global $QData;
	$isold = get_option('AdsDisp');	
	if ( !$isold  && is_bool($isold) ) {
		foreach ($QData['Default'] as $key => $value) {
			update_option($key , $value);
		}		
		for ($i=1;$i<=$QData['Ads'];$i++) {
			update_option('AdsMargin'.$i, $QData['DefaultAdsOpt']['AdsMargin']);
			update_option('AdsAlign'.$i, $QData['DefaultAdsOpt']['AdsAlign']);
		}		
	}	
}
register_activation_hook( __FILE__, 'plugin_activated' );


$wpvcomp = (bool)(version_compare($wp_version, '3.1', '>='));
function ads_head_java() { 
	global $QData; 
	global $wpvcomp; 
	if (get_option('QckTags')) { ?>
	<script type="text/javascript">
		wpvcomp = <?php echo(($wpvcomp==1)?"true":"false"); ?>;
		edaddID = new Array();
		edaddNm = new Array();
		if(typeof(edButtons)!='undefined') {
			edadd = edButtons.length;	
			var dynads={"all":[
				<?php for ($i=1;$i<=$QData['Ads'];$i++) { if(get_option('AdsCode'.$i)!=''){echo('"1",');}else{echo('"0",');}; } ?>
			"0"]};
			for(i=1;i<=<?php echo($QData['Ads']) ?>;i++) {
				if(dynads.all[i-1]=="1") {
					edButtons[edButtons.length]=new edButton("ads"+i.toString(),"Ads"+i.toString(),"\n<!--Ads"+i.toString()+"-->\n","","",-1);
					edaddID[edaddID.length] = "ads"+i.toString();
					edaddNm[edaddNm.length] = "Ads"+i.toString();
				}	
			}
			<?php if(!get_option('QckRnds')){ ?>
				edButtons[edButtons.length]=new edButton("random_ads","RndAds","\n<!--RndAds-->\n","","",-1);
				edaddID[edaddID.length] = "random_ads";
				edaddNm[edaddNm.length] = "RndAds";
			<?php } ?>	
			<?php if(!get_option('QckOffs')){ ?>
				edButtons[edButtons.length]=new edButton("no_ads","NoAds","\n<!--NoAds-->\n","","",-1);
				edaddID[edaddID.length] = "no_ads";
				edaddNm[edaddNm.length] = "NoAds";
				edButtons[edButtons.length]=new edButton("off_def","OffDef","\n<!--OffDef-->\n","","",-1);	
				edaddID[edaddID.length] = "off_def";
				edaddNm[edaddNm.length] = "OffDef";
				edButtons[edButtons.length]=new edButton("off_wid","OffWidget","\n<!--OffWidget-->\n","","",-1);	
				edaddID[edaddID.length] = "off_wid";
				edaddNm[edaddNm.length] = "OffWidget";				
			<?php } ?>
			<?php if(!get_option('QckOfPs')){ ?>
				edButtons[edButtons.length]=new edButton("off_bgn","OffBegin","\n<!--OffBegin-->\n","","",-1);
				edaddID[edaddID.length] = "off_bgn";
				edaddNm[edaddNm.length] = "OffBegin";
				edButtons[edButtons.length]=new edButton("off_mid","OffMiddle","\n<!--OffMiddle-->\n","","",-1);
				edaddID[edaddID.length] = "off_mid";
				edaddNm[edaddNm.length] = "OffMiddle";
				edButtons[edButtons.length]=new edButton("off_end","OffEnd","\n<!--OffEnd-->\n","","",-1);
				edaddID[edaddID.length] = "off_end";
				edaddNm[edaddNm.length] = "OffEnd";				
				edButtons[edButtons.length]=new edButton("off_more","OffAfMore","\n<!--OffAfMore-->\n","","",-1);
				edaddID[edaddID.length] = "off_more";
				edaddNm[edaddNm.length] = "OffAfMore";				
				edButtons[edButtons.length]=new edButton("off_last","OffBfLastPara","\n<!--OffBfLastPara-->\n","","",-1);
				edaddID[edaddID.length] = "off_last";
				edaddNm[edaddNm.length] = "OffBfLastPara";								
			<?php } ?>			
		};
		(function(){
			if(typeof(edButtons)!='undefined' && typeof(jQuery)!='undefined' && wpvcomp){
				jQuery(document).ready(function(){
					for(i=0;i<edaddID.length;i++) {
						jQuery("#ed_toolbar").append('<input type="button" value="' + edaddNm[i] +'" id="' + edaddID[i] +'" class="ed_button" onclick="edInsertTag(edCanvas, ' + (edadd+i) + ');" title="' + edaddNm[i] +'" />');
					}
				});
			}
		}());	
	</script> 
	<?php	}
}
if ($wpvcomp) {
	add_action('admin_print_footer_scripts', 'ads_head_java');
}else{
	add_action('admin_head', 'ads_head_java');
}


$ShownAds = 0;
$AdsId = array();
$beginend = 0;

function process_content($content)
{
	global $QData;
	global $ShownAds;
	global $AdsId;
	global $beginend;
	
	/* verifying */ 
	if(	(is_feed()) ||
			(strpos($content,'<!--NoAds-->')!==false) ||
			(strpos($content,'<!--OffAds-->')!==false) ||
			(is_single() && !(get_option('AppPost'))) ||
			(is_page() && !(get_option('AppPage'))) ||
			(is_home() && !(get_option('AppHome'))) ||			
			(is_category() && !(get_option('AppCate'))) ||
			(is_archive() && !(get_option('AppArch'))) ||
			(is_tag() && !(get_option('AppTags'))) ||
			(is_user_logged_in() && (get_option('AppLogg'))) ) { 
		$content = clean_tags($content); return $content; 
	}
	
	$AdsToShow = get_option('AdsDisp');
	if (strpos($content,'<!--OffWidget-->')===false) {
		for($i=1;$i<=$QData['AdsWid'];$i++) {
			$wadsid = sanitize_title(str_replace(array('(',')'),'',sprintf($QData['AdsWidName'],$i)));
			$AdsToShow -= (is_active_widget(true, $wadsid)) ? 1 : 0 ;
		}		
	}
	if( $ShownAds >= $AdsToShow ) { $content = clean_tags($content); return $content; };

	if( !count($AdsId) ) {  
		for($i=1;$i<=$QData['Ads'];$i++) { 
			$tmp = trim(get_option('AdsCode'.$i));
			if( !empty($tmp) ) {
				array_push($AdsId, $i);
			}
		}
	}	
	if( !count($AdsId) ) { $content = clean_tags($content); return $content; };

	/* ... Tidy up content ... */
	$content = str_replace("<p></p>", "##QA-TP1##", $content);
	$content = str_replace("<p>&nbsp;</p>", "##QA-TP2##", $content);	
	$offdef = (strpos($content,'<!--OffDef-->')!==false);
	if( !$offdef ) {
		$AdsIdCus = array();
		$cusads = 'CusAds'; $cusrnd = 'CusRnd';
		$more1 = get_option('MoreAds'); $more2 = get_option('MoreRnd');	
		$lapa1 = get_option('LapaAds'); $lapa2 = get_option('LapaRnd');		
		$begn1 = get_option('BegnAds'); $begn2 = get_option('BegnRnd');
		$midd1 = get_option('MiddAds'); $midd2 = get_option('MiddRnd');
		$endi1 = get_option('EndiAds');	$endi2 = get_option('EndiRnd');
		$rc=3;
		for($i=1;$i<=$rc;$i++) { 
			$para1[$i] = get_option('Par'.$i.'Ads');	$para2[$i] = get_option('Par'.$i.'Rnd');	$para3[$i] = get_option('Par'.$i.'Nup');	$para4[$i] = get_option('Par'.$i.'Con');
		}
		$imge1 = get_option('Img1Ads');	$imge2 = get_option('Img1Rnd');	$imge3 = get_option('Img1Nup'); $imge4 = get_option('Img1Con');		
		if ( $begn2 == 0 ) { $b1 = $cusrnd; } else { $b1 = $cusads.$begn2; array_push($AdsIdCus, $begn2); };
		if ( $more2 == 0 ) { $r1 = $cusrnd; } else { $r1 = $cusads.$more2; array_push($AdsIdCus, $more2); };		
		if ( $midd2 == 0 ) { $m1 = $cusrnd; } else { $m1 = $cusads.$midd2; array_push($AdsIdCus, $midd2); };
		if ( $lapa2 == 0 ) { $g1 = $cusrnd; } else { $g1 = $cusads.$lapa2; array_push($AdsIdCus, $lapa2); };
		if ( $endi2 == 0 ) { $b2 = $cusrnd; } else { $b2 = $cusads.$endi2; array_push($AdsIdCus, $endi2); };	
		for($i=1;$i<=$rc;$i++) { 
			if ( $para2[$i] == 0 ) { $b3[$i] = $cusrnd; } else { $b3[$i] = $cusads.$para2[$i]; array_push($AdsIdCus, $para2[$i]); };	
		}	
		if ( $imge2 == 0 ) { $b4 = $cusrnd; } else { $b4 = $cusads.$imge2; array_push($AdsIdCus, $imge2); };	
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
		if ( $imge1 ){
			$sch = "<img"; $bch = ">"; $cph = "[/caption]"; $csa = "</a>";			
			$content = str_replace("<IMG", $sch, $content);
			$content = str_replace("</A>", $csa, $content);			
			$arr = explode($sch, $content);
			if ( (int)$imge3 < count($arr) ) {
				$trr = explode($bch, $arr[$imge3]);
				if ( count($trr) > 1 ) {
					$tss = explode($cph, $arr[$imge3]);
					$ccp = ( count($tss) > 1 ) ? strpos(strtolower($tss[0]),'[caption ')===false : false ;
					$tuu = explode($csa, $arr[$imge3]);
					$cdu = ( count($tuu) > 1 ) ? strpos(strtolower($tuu[0]),'<a href')===false : false ;					
					if ( $imge4 && $ccp ) {
						$arr[$imge3] = implode($cph, array_slice($tss, 0, 1)).$cph. "\r\n".'<!--'.$b4.'-->'."\r\n". implode($cph, array_slice($tss, 1));
					}else if ( $cdu ) {	
						$arr[$imge3] = implode($csa, array_slice($tuu, 0, 1)).$csa. "\r\n".'<!--'.$b4.'-->'."\r\n". implode($csa, array_slice($tuu, 1));
					}else{
						$arr[$imge3] = implode($bch, array_slice($trr, 0, 1)).$bch. "\r\n".'<!--'.$b4.'-->'."\r\n". implode($bch, array_slice($trr, 1));
					}
				}
				$content = implode($sch, $arr);
			}	
		}		
	}
	
	/* ... Tidy up content ... */
	$content = '<!--EmptyClear-->'.$content."\n".'<div style="font-size:0px;height:0px;line-height:0px;margin:0;padding:0;clear:both"></div>';
	$content = clean_tags($content, true);	
	$ismany = (!is_single() && !is_page());
	$showall = get_option('AppMaxA');
	
	/* ... Replace Beginning/Middle/End Ads1-10 ... */
	if( !$offdef ) {
		for( $i=1; $i<=count($AdsIdCus); $i++ ) {
			if( $showall || !$ismany || $beginend != $i ) {
				if( strpos($content,'<!--'.$cusads.$AdsIdCus[$i-1].'-->')!==false && in_array($AdsIdCus[$i-1], $AdsId)) {
					$content = replace_ads( $content, $cusads.$AdsIdCus[$i-1], $AdsIdCus[$i-1] ); $AdsId = del_element($AdsId, array_search($AdsIdCus[$i-1], $AdsId)) ;
					$ShownAds += 1; if( $ShownAds >= $AdsToShow || !count($AdsId) ){ $content = clean_tags($content); return $content; };
					$beginend = $i; if(!$showall && $ismany){break;} 
				}
			}	
		}	
	}
	
	/* ... Replace Ads1 to Ads10 ... */
	if( $showall || !$ismany ) {
		$tcn = count($AdsId); $tt = 0;
		for( $i=1; $i<=$tcn; $i++ ) {
			if( strpos($content, '<!--Ads'.$AdsId[$tt].'-->')!==false ) {
				$content = replace_ads( $content, 'Ads'.$AdsId[$tt], $AdsId[$tt] ); $AdsId = del_element($AdsId, $tt) ;
				$ShownAds += 1; if( $ShownAds >= $AdsToShow || !count($AdsId) ){ $content = clean_tags($content); return $content; };
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
			$content = replace_ads( $content, $cusrnd, $AdsId[0] ); $AdsId = del_element($AdsId, 0) ;
			$ShownAds += 1; if( $ShownAds >= $AdsToShow || !count($AdsId) ){ $content = clean_tags($content); return $content; };
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
			$content = replace_ads( $content, 'RndAds', $AdsIdTmp[0] ); $AdsIdTmp = del_element($AdsIdTmp, 0) ;
			if($tmp != -1){$ShownAds += 1;}; if( $ShownAds >= $AdsToShow || !count($AdsIdTmp) ){ $content = clean_tags($content); return $content; };
		}
	}	

	/* ... That's it. DONE :) ... */
	$content = clean_tags($content); return $content;
}
function clean_tags($content, $trimonly = false) {
	global $QData;
	global $ShownAds;
	global $AdsId;
	global $beginend;
	$tagnames = array('EmptyClear','RndAds','NoAds','OffDef','OffAds','OffWidget','OffBegin','OffMiddle','OffEnd','OffBfMore','OffAfLastPara','CusRnd');
	for($i=1;$i<=$QData['Ads'];$i++) { array_push($tagnames, 'CusAds'.$i); array_push($tagnames, 'Ads'.$i); };
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
function replace_ads($content, $nme, $adn) {
	if( strpos($content,'<!--'.$nme.'-->')===false ) { return $content; }	
	global $QData;
	if ($adn != -1) {
		$arr = array('',
			'float:left;margin:%1$dpx %1$dpx %1$dpx 0;',
			'float:none;margin:%1$dpx 0 %1$dpx 0;text-align:center;',
			'float:right;margin:%1$dpx 0 %1$dpx %1$dpx;',
			'float:none;margin:0px;');
		$adsalign = get_option('AdsAlign'.$adn);
		$adsmargin = get_option('AdsMargin'.$adn);
		$style = sprintf($arr[(int)$adsalign], $adsmargin);
		$adscode = get_option('AdsCode'.$adn);
		$adscode =
			"\n".'<!-- '.$QData['Name'].' Wordpress Plugin: '.$QData['URI'].' -->'."\n".
			'<div style="'.$style.'">'."\n".
			$adscode."\n".
			'</div>'."\n";
	} else {
		$adscode ='';
	}	
	$cont = explode('<!--'.$nme.'-->', $content, 2);	
	return $cont[0].$adscode.$cont[1];
}
function del_element($array, $idx) {
  $copy = array();
	for( $i=0; $i<count($array) ;$i++) {
		if ( $idx != $i ) {
			array_push($copy, $array[$i]);
		}
	}	
  return $copy;
}
add_filter('the_content', 'process_content');


function ads_widget_register() {
	global $QData;
	if (!function_exists('wp_register_sidebar_widget')) { return; };
  for($i=1;$i<=$QData['AdsWid'];$i++) {
		if(get_option('WidCode'.$i)!='') {
			$displaystr =
				'$cont = get_the_content();'.
				'if( strpos($cont,"<!--OffAds-->")===false && strpos($cont,"<!--OffWidget-->")===false && !(is_home()&&get_option("AppSide")) ) {'.
				'extract($args);'.
				'$title = get_option("WidCode-title-'.$i.'");'.
				'$codetxt = get_option("WidCode'.$i.'");'.
				'echo "\n"."<!-- Quick Adsense Wordpress Plugin: http://quicksense.net/ -->"."\n";'.
				'echo $before_widget."\n";'.
				'if (!empty($title)) { '.
				'echo $before_title.$title.$after_title."\n"; '.
				'};'.
				'echo $codetxt;'.
				'echo "\n".$after_widget;'.
				'}';
			$displaycall[$i] = create_function('$args', $displaystr);
			$wadnam = sprintf($QData['AdsWidName'],$i);
			$wadsid = sanitize_title(str_replace(array('(',')'),'',$wadnam));
			wp_register_sidebar_widget($wadsid, $wadnam, $displaycall[$i], array('description' => 'Quick Adsense on Sidebar Widget'));
		}			
	}
}	
function get_option_en($nameid)
{
	$txt = get_option($nameid);
	$txt = htmlspecialchars($txt, ENT_QUOTES);
	if(!empty($txt)) { return $txt; }else{ return ""; };
}
function update_option_en($nameid, $text, $opt='')
{
	$txt = stripslashes($text);
	if ($opt=='strip_tags') { $txt = strip_tags($txt); };
	update_option($nameid, $txt);
	if(!empty($txt)) { return $txt; }else{ return ""; };
}
add_action('init', 'ads_widget_register');



?>
