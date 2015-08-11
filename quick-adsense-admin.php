<?php

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

?>

<?php
	global $QData;
	$dis = get_option('AdsDisp');if(!$dis&&is_bool($dis)){$dis=$QData['Default']['AdsDisp'];};
	$ra1 = get_option('BegnAds');
	$ra2 = get_option('BegnRnd');
	$rm1 = get_option('MiddAds');
	$rm2 = get_option('MiddRnd');	
	$rb1 = get_option('EndiAds');
	$rb2 = get_option('EndiRnd');	
	$rr1 = get_option('MoreAds');
	$rr2 = get_option('MoreRnd');	
	$rp1 = get_option('LapaAds');
	$rp2 = get_option('LapaRnd');		
	$rc = 3;
	for ($j=1;$j<=$rc;$j++) {
		$rc1[$j] = get_option('Par'.$j.'Ads');	
		$rc2[$j] = get_option('Par'.$j.'Rnd');		
		$rc3[$j] = get_option('Par'.$j.'Nup');			
		$rc4[$j] = get_option('Par'.$j.'Con');			
	}	
	$rd1 = get_option('Img1Ads');	
	$rd2 = get_option('Img1Rnd');		
	$rd3 = get_option('Img1Nup');		
	$rd4 = get_option('Img1Con');			
	$aps = get_option('AppPost');
	$apg = get_option('AppPage');
	$ahm = get_option('AppHome');	
	$act = get_option('AppCate');
	$aar = get_option('AppArch');
	$atg = get_option('AppTags');
	$amx = get_option('AppMaxA');	
	$asd = get_option('AppSide');	
	$lgg = get_option('AppLogg');		
	$aqt = get_option('QckTags');
	$aqr = get_option('QckRnds');
	$aqf = get_option('QckOffs');
	$aqp = get_option('QckOfPs');
	$optionsupdate = '';
	foreach ($QData['Default'] as $key => $value) {
		$optionsupdate .= $key.',' ;
	}			
	foreach ($QData['DefaultAdsName'] as $key => $value) {
		$optionsupdate .= $value.',' ;
	}			
	$optionsupdate = substr($optionsupdate, 0, -1);
	function truefalse($arg) {
		if($arg){ return 'true';}else{ return 'false';}
	}			
?>
	
<script type="text/javascript">
	function defaultoptions() {
		document.getElementById("DisTot<?php echo($QData['Default']['AdsDisp']) ?>").selected = true;
		document.getElementById("BegnAds").checked = <?php echo(truefalse($QData['Default']['BegnAds'])) ?>;
		document.getElementById("BegnRnd").selectedIndex = <?php echo($QData['Default']['BegnRnd']) ?>;
		document.getElementById("MiddAds").checked = <?php echo(truefalse($QData['Default']['MiddAds'])) ?>;
		document.getElementById("MiddRnd").selectedIndex = <?php echo($QData['Default']['MiddRnd']) ?>;		
		document.getElementById("EndiAds").checked = <?php echo(truefalse($QData['Default']['EndiAds'])) ?>;
		document.getElementById("EndiRnd").selectedIndex = <?php echo($QData['Default']['EndiRnd']) ?>;		
		document.getElementById("MoreAds").checked = <?php echo(truefalse($QData['Default']['MoreAds'])) ?>;
		document.getElementById("MoreRnd").selectedIndex = <?php echo($QData['Default']['MoreRnd']) ?>;
		document.getElementById("LapaAds").checked = <?php echo(truefalse($QData['Default']['LapaAds'])) ?>;
		document.getElementById("LapaRnd").selectedIndex = <?php echo($QData['Default']['LapaRnd']) ?>;			
		<?php for ($j=1;$j<=$rc;$j++) { ?>	
			document.getElementById("Par<?php echo $j; ?>Ads").checked = <?php echo(truefalse($QData['Default']['Par'.$j.'Ads'])) ?>;
			document.getElementById("Par<?php echo $j; ?>Rnd").selectedIndex = <?php echo($QData['Default']['Par'.$j.'Rnd']) ?>;		
			document.getElementById("Par<?php echo $j; ?>Nup").selectedIndex = <?php echo($QData['Default']['Par'.$j.'Nup']) ?>;			
			document.getElementById("Par<?php echo $j; ?>Con").checked = <?php echo(truefalse($QData['Default']['Par'.$j.'Con'])) ?>;	
		<?php } ?>
		document.getElementById("Img1Ads").checked = <?php echo(truefalse($QData['Default']['Img1Ads'])) ?>;
		document.getElementById("Img1Rnd").selectedIndex = <?php echo($QData['Default']['Img1Rnd']) ?>;		
		document.getElementById("Img1Nup").selectedIndex = <?php echo($QData['Default']['Img1Nup']) ?>;	
		document.getElementById("Img1Con").checked = <?php echo(truefalse($QData['Default']['Img1Con'])) ?>;		
		document.getElementById("AppHome").checked = <?php echo(truefalse($QData['Default']['AppHome'])) ?>;
		document.getElementById("AppPost").checked = <?php echo(truefalse($QData['Default']['AppPost'])) ?>;
		document.getElementById("AppPage").checked = <?php echo(truefalse($QData['Default']['AppPage'])) ?>;
		document.getElementById("AppCate").checked = <?php echo(truefalse($QData['Default']['AppCate'])) ?>;
		document.getElementById("AppArch").checked = <?php echo(truefalse($QData['Default']['AppArch'])) ?>;
		document.getElementById("AppTags").checked = <?php echo(truefalse($QData['Default']['AppTags'])) ?>;
		document.getElementById("AppMaxA").checked = <?php echo(truefalse($QData['Default']['AppMaxA'])) ?>;		
		document.getElementById("AppSide").checked = <?php echo(truefalse($QData['Default']['AppSide'])) ?>;		
		document.getElementById("AppLogg").checked = <?php echo(truefalse($QData['Default']['AppLogg'])) ?>;		
		document.getElementById("QckTags").checked = <?php echo(truefalse($QData['Default']['QckTags'])) ?>;
		document.getElementById("QckRnds").checked = <?php echo(truefalse($QData['Default']['QckRnds'])) ?>;
		document.getElementById("QckOffs").checked = <?php echo(truefalse($QData['Default']['QckOffs'])) ?>;		
		document.getElementById("QckOfPs").checked = <?php echo(truefalse($QData['Default']['QckOfPs'])) ?>;		
		for(i=1;i<=<?php echo($QData['Ads']) ?>;i++){
			tp=document.getElementById("AdsCode"+i.toString()).innerHTML;
			if(tp==''){
				document.getElementById("AdsMargin"+i.toString()).value = "<?php echo($QData['DefaultAdsOpt']['AdsMargin']) ?>";
				document.getElementById("OptAgn"+i.toString()+"<?php echo($QData['DefaultAdsOpt']['AdsAlign']) ?>").selected = true;
			}
		}		
		deftcheckinfo();
	}
	function selectinfo(ts) {
		if (ts.selectedIndex == 0) { return; }
		cek = new Array(
			document.getElementById('BegnRnd'),
			document.getElementById('MiddRnd'),
			document.getElementById('EndiRnd'),
			document.getElementById('MoreRnd'),
			document.getElementById('LapaRnd'),				
			document.getElementById('Par1Rnd'),
			document.getElementById('Par2Rnd'),
			document.getElementById('Par3Rnd'),
			document.getElementById('Img1Rnd') );
		for (i=0;i<cek.length;i++) {
			if (ts != cek[i] && ts.selectedIndex == cek[i].selectedIndex) {
				cek[i].selectedIndex = 0;
			}
		}
	}
	function checkinfo1(selnme,ts) {
		document.getElementById(selnme).disabled=!ts.checked;
	}
	function checkinfo2(ts,selnm1,selnm2,selnm3,selnm4) {
		if(selnm1){document.getElementById(selnm1).disabled=!ts.checked};
		if(selnm2){document.getElementById(selnm2).disabled=!ts.checked};		
		if(selnm3){document.getElementById(selnm3).disabled=!ts.checked};		
	}	
	function deftcheckinfo() {	
		checkinfo1('BegnRnd',document.getElementById('BegnAds'));
		checkinfo1('MiddRnd',document.getElementById('MiddAds'));
		checkinfo1('EndiRnd',document.getElementById('EndiAds'));
		checkinfo1('MoreRnd',document.getElementById('MoreAds'));
		checkinfo1('LapaRnd',document.getElementById('LapaAds'));		
		for (i=1;i<=3;i++) {
			checkinfo2(document.getElementById('Par'+i+'Ads'),'Par'+i+'Rnd','Par'+i+'Nup','Par'+i+'Con');		
		}	
		checkinfo2(document.getElementById('Img1Ads'),'Img1Rnd','Img1Nup','Img1Con');				
	}	
</script>

<div class="wrap">
<h2>Quick Adsense <?php _e('Setting'); ?> <span style="font-size:9pt;font-style:italic">( Version <?php echo($QData['Version']) ?> )</span></h2>

<form method="post" action="options.php">
<?php /* wp_nonce_field('update-options'); */?>
	
	<h3 style="font-size:120%"><?php _e('Options'); ?></h3>

	<table border="0" cellspacing="0" cellpadding="0">
	<tr valign="top">
		<td style="width:110px"><?php _e('Adsense :'); ?></td>
		<td><?php _e('Place up to '); ?><select name="AdsDisp" style="width:50px;font-weight:bold">
				<?php for ($i=0;$i<=(int)$QData['Ads'];$i++) { ?>
					<option id="DisTot<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($dis==(string)$i){echo('selected');} ?>><?php echo $i; ?></option>
				<?php } ?></select><?php _e(' Ads on a page. Select up to <b>3 Ads</b> only if you are solely using Google Ads.'); ?><br/>
				<span class="description" style="font-style:italic"><?php _e('(Google allows publishers to place up to <b>3 Adsense</b> for Content on a page. If you have placed these ads manually in the page, you will need to take those into account. If you are using other Ads, you may select up to <b>10 Ads</b>.)'); ?></span><br/>
				<br/>
		</td>
	</tr>
	<tr valign="top">
		<td style="width:110px"><?php _e('Position :<br/>(Default)'); ?></td>
		<td>
				<input type="checkbox" id="BegnAds" name="BegnAds" value="true" <?php if($ra1){echo('checked');} ?> onchange="checkinfo1('BegnRnd',this)" /> <?php _e('Assign') ; ?> <select id="BegnRnd" name="BegnRnd" onchange="selectinfo(this)">
					<?php for ($i=0;$i<=$QData['Ads'];$i++) { ?>
						<option id="OptBegn<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($ra2==(string)$i){echo('selected');} ?>><?php _e(($i==0)?'Random Ads':'Ads'.$i) ; ?></option>
					<?php } ?></select> <?php _e('to <b>Beginning of Post</b>') ?><br/>
				<input type="checkbox" id="MiddAds" name="MiddAds" value="false" <?php if($rm1){echo('checked');} ?> onchange="checkinfo1('MiddRnd',this)" /> <?php _e('Assign') ; ?> <select id="MiddRnd" name="MiddRnd" onchange="selectinfo(this)">
					<?php for ($i=0;$i<=$QData['Ads'];$i++) { ?>
						<option id="OptMidd<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($rm2==(string)$i){echo('selected');} ?>><?php _e(($i==0)?'Random Ads':'Ads'.$i) ; ?></option>
					<?php } ?></select> <?php _e('to <b>Middle of Post</b>') ?><br/>					
				<input type="checkbox" id="EndiAds" name="EndiAds" value="false" <?php if($rb1){echo('checked');} ?> onchange="checkinfo1('EndiRnd',this)" /> <?php _e('Assign') ; ?> <select id="EndiRnd" name="EndiRnd" onchange="selectinfo(this)">
					<?php for ($i=0;$i<=$QData['Ads'];$i++) { ?>
						<option id="OptEndi<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($rb2==(string)$i){echo('selected');} ?>><?php _e(($i==0)?'Random Ads':'Ads'.$i) ; ?></option>
					<?php } ?></select> <?php _e('to <b>End of Post</b>') ?><br/> 
				<input type="checkbox" id="MoreAds" name="MoreAds" value="false" <?php if($rr1){echo('checked');} ?> onchange="checkinfo1('MoreRnd',this)" /> <?php _e('Assign') ; ?> <select id="MoreRnd" name="MoreRnd" onchange="selectinfo(this)">
					<?php for ($i=0;$i<=$QData['Ads'];$i++) { ?>
						<option id="OptMore<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($rr2==(string)$i){echo('selected');} ?>><?php _e(($i==0)?'Random Ads':'Ads'.$i) ; ?></option>
					<?php } ?></select> <?php _e('right after <b>the') ?> <span style="font-family:Courier New,Courier,Fixed;">&lt;!--more--&gt;</span> <?php _e('tag') ?></b><br/> 					
				<input type="checkbox" id="LapaAds" name="LapaAds" value="false" <?php if($rp1){echo('checked');} ?> onchange="checkinfo1('LapaRnd',this)" /> <?php _e('Assign') ; ?> <select id="LapaRnd" name="LapaRnd" onchange="selectinfo(this)">
					<?php for ($i=0;$i<=$QData['Ads'];$i++) { ?>
						<option id="OptLapa<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($rp2==(string)$i){echo('selected');} ?>><?php _e(($i==0)?'Random Ads':'Ads'.$i) ; ?></option>
					<?php } ?></select> <?php _e('right before <b>the last Paragraph</b>') ?><span style="color:#a00;"> <b>(New)</b></span><br/> 
				<?php for ($j=1;$j<=$rc;$j++) { ?>	
					<input type="checkbox" id="Par<?php echo $j; ?>Ads" name="Par<?php echo $j; ?>Ads" value="false" <?php if($rc1[$j]){echo('checked');} ?> onchange="checkinfo2(this,'Par<?php echo $j; ?>Rnd','Par<?php echo $j; ?>Nup','Par<?php echo $j; ?>Con')" /> <?php _e('Assign') ; ?> <select id="Par<?php echo $j; ?>Rnd" name="Par<?php echo $j; ?>Rnd" onchange="selectinfo(this)">
						<?php for ($i=0;$i<=$QData['Ads'];$i++) { ?>
							<option id="OptPar<?php echo $j; ?><?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($rc2[$j]==(string)$i){echo('selected');} ?>><?php _e(($i==0)?'Random Ads':'Ads'.$i) ; ?></option>
						<?php } ?></select> <?php _e('<b>After Paragraph</b> ') ?> <select id="Par<?php echo $j; ?>Nup" name="Par<?php echo $j; ?>Nup">
							<?php for ($i=1;$i<=50;$i++) { ?>
								<option id="Opt<?php echo $j; ?>Nu<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($rc3[$j]==(string)$i){echo('selected');} ?>><?php echo $i; ?></option>
							<?php } ?></select> &rarr; 
							<input type="checkbox" id="Par<?php echo $j; ?>Con" name="Par<?php echo $j; ?>Con" value="false" <?php if($rc4[$j]){echo('checked');} ?> /> <?php _e('to <b>End of Post</b> if fewer paragraphs are found.') ; ?><br/>
				<?php } ?>
				<input type="checkbox" id="Img1Ads" name="Img1Ads" value="false" <?php if($rd1){echo('checked');} ?> onchange="checkinfo2(this,'Img1Rnd','Img1Nup','Img1Con')" /> <?php _e('Assign') ; ?> <select id="Img1Rnd" name="Img1Rnd" onchange="selectinfo(this)">
					<?php for ($i=0;$i<=$QData['Ads'];$i++) { ?>
						<option id="OptImg1<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($rd2==(string)$i){echo('selected');} ?>><?php _e(($i==0)?'Random Ads':'Ads'.$i) ; ?></option>
					<?php } ?></select> <?php _e('<b>After Image</b> ') ?> <select id="Img1Nup" name="Img1Nup">
						<?php for ($i=1;$i<=50;$i++) { ?>
							<option id="Opt1Im<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($rd3==(string)$i){echo('selected');} ?>><?php echo $i; ?></option>
						<?php } ?></select> &rarr; 
						<input type="checkbox" id="Img1Con" name="Img1Con" value="false" <?php if($rd4){echo('checked');} ?> /> <?php _e('after <b>Image&#39;s outer</b>'); ?><b><span style="font-family:Courier New,Courier,Fixed;"> &lt;div&gt; wp-caption</span></b> if any.<span style="color:#a00;"> <b>(New)</b></span><br/>
				<br/>
				<script type="text/javascript">deftcheckinfo();</script>
		</td>
	</tr>
	<tr valign="top">
		<td style="width:110px"><?php _e('Appearance :'); ?></td>
		<td>
				<span>[ </span>
					<input type="checkbox" id="AppPost" name="AppPost" value="true" <?php if($aps){echo('checked');} ?> /> <?php _e('Posts'); ?>
					<input type="checkbox" id="AppPage" name="AppPage" value="true" <?php if($apg){echo('checked');} ?> /> <?php _e('Pages'); ?><span> ]</span><br/>
				<span>[ </span>
					<input type="checkbox" id="AppHome" name="AppHome" value="true" <?php if($ahm){echo('checked');} ?> /> <?php _e('Homepage'); ?>				
					<input type="checkbox" id="AppCate" name="AppCate" value="true" <?php if($act){echo('checked');} ?> /> <?php _e('Categories'); ?>
					<input type="checkbox" id="AppArch" name="AppArch" value="true" <?php if($aar){echo('checked');} ?> /> <?php _e('Archives'); ?>
					<input type="checkbox" id="AppTags" name="AppTags" value="true" <?php if($atg){echo('checked');} ?> /> <?php _e('Tags'); ?><span> ] &rarr; </span>
					<input type="checkbox" id="AppMaxA" name="AppMaxA" value="true" <?php if($amx){echo('checked');} ?> /> <?php _e('Place all possible Ads on these pages.'); ?><br/>
				<span>[ </span>
					<input type="checkbox" id="AppSide" name="AppSide" value="true" <?php if($asd){echo('checked');} ?> /> <?php _e('Disable AdsWidget on Homepage'); ?><span> ]</span><br/>
				<span>[ </span>				
					<input type="checkbox" id="AppLogg" name="AppLogg" value="true" <?php if($lgg){echo('checked');} ?> /> <?php _e('Hide Ads when user is logged in to Wordpress'); ?><span> ]</span><br/>
				<br/>
		</td>
	</tr>	
	<tr valign="top">
		<td style="width:110px"><?php _e('Quicktag :'); ?></td>
		<td><span style="display:block;font-style:normal;padding-bottom:0px"><?php _e('Insert Ads into a post, on-the-fly :'); ?></span>
				<ol style="margin-top:5px;">
				<li><?php _e('Insert <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--Ads1--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--Ads2--&gt;</span>, etc. into a post to show the <b>Particular Ads</b> at specific location.'); ?></li>
				<li><?php _e('Insert <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--RndAds--&gt;</span> (or more) into a post to show the <b>Random Ads</b> at specific location.'); ?></li>
				</ol>
				<span style="display:block;font-style:normal;padding-bottom:0px"><?php _e('Disable Ads in a post, on-the-fly :'); ?></span>
				<ol style="margin-top:5px;">				
				<li><?php _e('Insert <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--NoAds--&gt;</span> to <b>disable all Ads</b> in a post.'); ?><span class="description" style="font-style:italic"><?php _e(' (does not affect Ads on Sidebar)'); ?></span></li>				
				<li><?php _e('Insert <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffDef--&gt;</span> to <b>disable the default positioned Ads</b>, and use <span style="font-family:Courier New,Courier,Fixed;">&lt;!--Ads1--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;">&lt;!--Ads2--&gt;</span>, etc. to insert Ads.'); ?><span class="description" style="font-style:italic"><?php _e(' (does not affect Ads on Sidebar)'); ?></span></li>								
				<li><?php _e('Insert <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffWidget--&gt;</span> to <b>disable all Ads on Sidebar</b>.'); ?><span style="color:#a00;"> <b>(New)</b></span></li>								
				<li><?php _e('Insert <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffBegin--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffMiddle--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffEnd--&gt;</span> to <b>disable Ads at Beginning, Middle</b> or <b>End of Post</b>.'); ?><span style="color:#a00;"> <b>(New)</b></span></li>								
				<li><?php _e('Insert <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffAfMore--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffBfLastPara--&gt;</span> to <b>disable Ads right after the <span style="font-family:Courier New,Courier,Fixed;">&lt;!--more--&gt;</span> tag</b>, or <b>right before the last Paragraph</b>.'); ?><span style="color:#a00;"> <b>(New)</b></span></li>												
				</ol>
				[ <input type="checkbox" id="QckTags" name="QckTags" value="true" <?php if($aqt){echo('checked');} ?> /> <?php _e('Show Quicktag Buttons on the HTML Edit Post SubPanel'); ?> ]<br/>
				[ <input type="checkbox" id="QckRnds" name="QckRnds" value="true" <?php if($aqr){echo('checked');} ?> /> <?php _e('Hide <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--RndAds--&gt;</span> from Quicktag Buttons'); ?> ]<br/>	
				[ <input type="checkbox" id="QckOffs" name="QckOffs" value="true" <?php if($aqf){echo('checked');} ?> /> <?php _e('Hide <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--NoAds--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffDef--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffWidget--&gt;</span> from Quicktag Buttons'); ?> ]<br/>								
				[ <input type="checkbox" id="QckOfPs" name="QckOfPs" value="true" <?php if($aqp){echo('checked');} ?> /> <?php _e('Hide <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffBegin--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffMiddle--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffEnd--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffAfMore--&gt;</span>, <span style="font-family:Courier New,Courier,Fixed;color:#050">&lt;!--OffBfLastPara--&gt;</span> from Quicktag Buttons'); ?> ]<br/>								
				<span class="description" style="display:block;font-style:italic;padding-top:10px"><?php _e('Tags can be inserted into a post via the additional Quicktag Buttons at the HTML Edit Post SubPanel.'); ?></span><br/>
		</td>
	</tr>	
	<tr valign="top">
		<td style="width:110px"><?php _e('Infomation :'); ?></td>
		<td>
			<span><?php echo(__(
				'A link from your blog to <a href="http://quicksense.net" target="_blank">http://quicksense.net</a> would be appreciated.'
			)); ?></span>
		</td>	
	</tr>	
	
	</table>

	<p style="margin-top:20px">( <a href="javascript:defaultoptions()"><?php _e('Load Default Setting') ?></a> )<br/><br/></p>
	
	<h3 style="font-size:120%;margin-bottom:5px"><?php _e('Adsense Codes'); ?></h3>
	<p style="margin-top:0px"><span class="description"><?php _e('Paste up to <b>'.$QData['Ads'].' Ads codes</b> on Post Body as assigned above, and up to <b>'.$QData['AdsWid'].' Ads codes</b> on Sidebar Widget. Ads codes provided must <b>not</b> be identical, repeated codes may result the Ads not being display correctly. Ads will never displays more than once in a page.') ?></span></p>	
	
	<h4><?php _e('Ads on Post Body :'); ?></h4>		
	<table border="0" cellspacing="0" cellpadding="0">
	<?php for ($i=1;$i<=$QData['Ads'];$i++) { 
		$cod = htmlentities(get_option('AdsCode'.$i)); 
		$agn = get_option('AdsAlign'.$i);
		$mar = get_option('AdsMargin'.$i);
		$optionsupdate .= ',AdsCode'.$i.',AdsAlign'.$i.',AdsMargin'.$i;
	?>	
	<tr valign="top">
		<td align="left" style="width:110px">Ads<?php echo $i; ?> :</td>
		<td align="left"><textarea style="margin:0 5px 3px 0" id="AdsCode<?php echo $i; ?>" name="AdsCode<?php echo $i; ?>" rows="3" cols="50"><?php echo $cod; ?></textarea></td>
		<td align="left">
			<select name="AdsAlign<?php echo $i; ?>">
			<option id="OptAgn<?php echo $i; ?>1" value="1" <?php if($agn=="1"){echo('selected');} ?>><?php _e('Left') ; ?></option>
			<option id="OptAgn<?php echo $i; ?>2" value="2" <?php if($agn=="2"){echo('selected');} ?>><?php _e('Center') ; ?></option>
			<option id="OptAgn<?php echo $i; ?>3" value="3" <?php if($agn=="3"){echo('selected');} ?>><?php _e('Right') ; ?></option>
			<option id="OptAgn<?php echo $i; ?>4" value="4" <?php if($agn=="4"){echo('selected');} ?>><?php _e('None') ; ?></option></select> <?php _e('alignment'); ?><br/>
			<input style="width:35px;text-align:right;" id="AdsMargin<?php echo $i; ?>" name="AdsMargin<?php echo $i; ?>" value="<?php echo stripslashes(htmlspecialchars($mar)); ?>" />px <?php _e('margin'); ?><br/>						
		</td>
	</tr>
	<?php } ?>	
	</table>

	<h4><?php _e('Ads on Sidebar Widget '); ?><span style="font-weight:normal">(<a href="widgets.php"><?php _e('Drag to Sidebar'); ?></a>)</span> :</h4>	
	<table border="0" cellspacing="0" cellpadding="0">
	<?php for ($i=1;$i<=$QData['AdsWid'];$i++) { 
		$cod = htmlentities(get_option('WidCode'.$i)); 
		$optionsupdate .= ',WidCode'.$i;
	?>	
	<tr valign="top">
		<td align="left" style="width:110px">AdsWidget<?php echo $i; ?> :</td>
		<td align="left"><textarea style="margin:0 5px 3px 0" id="WidCode<?php echo $i; ?>" name="WidCode<?php echo $i; ?>" rows="3" cols="50"><?php echo $cod; ?></textarea></td>
	</tr>
	<?php } ?>	
	</table>

	<input type="hidden" name="action" value="update" />
	<?php /* <input type="hidden" name="page_options" value="<?php echo $optionsupdate; ?>" /> */ ?>
	<?php settings_fields('qa-options'); ?>
	<div style="width:580px">

	<p class="submit">
		<input type="submit" value="<?php _e('Save Changes') ?>" />
	</p>
	</div>
	
</form>

</div> 


