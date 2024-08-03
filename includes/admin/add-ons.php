<?php
/**
 * Admin Add-ons
 *
 * @package     QUADS
 * @subpackage  Admin/Add-ons
 * @copyright   Copyright (c) 2015, Rene Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('admin_head', 'quads_admin_inline_css');

/**
 * Create admin inline css to bypass adblock plugin which is blocking wp quads css ressources
 */
function quads_admin_inline_css() {
    if (!quads_is_addon_page()){
        return false;
    }
    echo '<style>
.quads-button.green {
    display: inline-block;
    background-color: #83c11f;
    padding: 10px;
    min-width: 170px;
    color: white;
    font-size: 16px;
    text-decoration: none;
    text-align: center;
    margin-top: 20px;
}
#quads-add-ons li {
    font-size: 18px;
    line-height: 29px;
    position: relative;
    padding-left: 23px;
    list-style: none!important;
}
.quads-heading-pro {
    color: #83c11f;
    font-weight: bold;
}
.quads-h2 {
    margin-top: 0px;
    margin-bottom: 1.2rem;
    font-size: 30px;
    line-height: 2.5rem;
}
#quads-add-ons li:before {
    width: 1em;
    height: 100%;
    background: url(data:image/svg+xml;charset=utf8,%3Csvg%20width%3D%221792%22%20height%3D%221792%22%20viewBox%3D%220%200%201792%201792%22%20xmlns%3D%22http%3A%2F%2Fwww%2Ew3%2Eorg%2F2000%2Fsvg%22%3E%3Cpath%20fill%3D%22%2377B227%22%20d%3D%22M1671%20566q0%2040%2D28%2068l%2D724%20724%2D136%20136q%2D28%2028%2D68%2028t%2D68%2D28l%2D136%2D136%2D362%2D362q%2D28%2D28%2D28%2D68t28%2D68l136%2D136q28%2D28%2068%2D28t68%2028l294%20295%20656%2D657q28%2D28%2068%2D28t68%2028l136%20136q28%2028%2028%2068z%22%2F%3E%3C%2Fsvg%3E) left .4em no-repeat;
    background-size: contain;
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    color: #77b227;
}
.quads-h1 {
    font-size: 2.75em;
    margin-bottom: 1.35rem;
    font-size: 2.5em;
    line-height: 3.68rem;
    letter-spacing: normal;
}
#quads-add-ons h2 {
    margin: 0 0 15px;
}
#quads-add-ons .quads-footer {
    clear: both;
    margin-top: 20px;
    font-style: italic;
}
  </style>
  <style>
.fp-wr{
    width:95%;
    margin:0 auto;
}
.fp-img{
    width:100%;
    margin:0 auto;
    text-align: center;
    position: relative;
    line-height: 0;
}
.fp-img img{
    position: relative;
}
.ov{
    background: #000;
    opacity: 0.8;
    bottom: 0;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    border-radius: 10px;
}
.fp-cnt{
height: 250px;
    bottom: 0;
    left: 40px;
    right: 40px;
    margin: 0 auto;
    text-align: center;
        background: #000;
}
.fp-cnt h1{
    font-size: 45px;
    color: #fff;
    font-weight: 600;
        padding-top: 58px;
}
.fp-cnt h2{
    font-size: 25px;
    color: #fff;
    font-weight: 600;
}
.fp-cnt p{
    margin-top: 5px;
    color: #fff;
    font-size:20px;
    padding: 0 100px;
     line-height: 1.4;
}
.fp-cnt .buy{
  text-decoration: none;
    color: #fff;
     padding: 10px 35px;
    border-radius: 40px;
    font-size: 20px;: 24px;
    line-height: 1.4;
    display: inline-block;
    background: #56ab2f;
    background: -webkit-linear-gradient(to right, #a8e063, #56ab2f);
    background: linear-gradient(to right, #a8e063, #56ab2f);
    font-weight: 700;
    text-shadow: 1px 1px 1px #27a52c;
}
.pvf{
    position: relative;
    top: -16px;
    border: 1px solid #eee;
    padding-bottom: 40px;
	background: white;
}
.ext{
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    background: #f9f9f9;
    padding:45px 0px 45px 25px;
}
.ex-1{
    width:250px;
}
.ex-1 h4{
    margin: 15px 0px 12px 0px;
    font-size: 18px;
    color: #222;font-weight: 500
}
.ex-1 p{
    font-size: 14px;
    color: #555;
    font-weight: 400;
    margin:0;
} 
.e-1 img{
    width:65px !important;
}
.e-2 img{
    width:45px !important;
}
.e-3 img{
    width:49px !important;
}
.pvf-cnt{
    width:100%;
    display:inline-block;
}
.pvf-tlt{
    text-align: center;
    width:100%;
    margin:70px 0px 60px 0px;
}
.pvf-tlt h2{
    font-size: 36px;
    line-height: 1.4;
    color: #000;
    font-weight: 500;
    margin: 0;
}
.pvf-tlt span{
    font-size: 16px;
    color: #000;
    margin-top: 15px;
    display: inline-block;
    position: relative;
    top: 4px;
}
.pvf-cmp{
    display: grid;
    grid-template-columns: 1fr 2fr;
}
.fr{border-right:1px solid #eee;}
.fr h1, .pr h1{
    font-size: 36px;
    font-weight: bold;
    line-height: 1.5;
    border-bottom: 1px solid #efefef;
    padding: 0px 0px 20px 35px;
}
.pr h1{
    padding-left: 50px;
}
.fr-fe{
    color:#222;
    padding-top:10px;
}
.fe-1{
    padding:22px 35px 35px 35px;
}
.fe-1 h4{
    margin: 0px 0px 10px 0px;
    font-size: 20px;
    line-height: 1.4;
    font-weight: normal;
    color: #000;
}
.fe-1 p{
    font-size:15px;
    line-height: 1.4;
    margin:0;color: #333
}
.pr-fe{
    padding:34px 35px 35px 35px;
}
.pr-fe span{
    font-family: georgia;
    font-size: 16px;
    font-weight: bold;
    color: #000;
    font-style: italic;
    line-height: 1.3;
}
.fet{
    width: 100%;
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: 25px;
    margin-top: 40px;
}
.fe-2{
    color:#222;
}
.fe-t img{
    width:22px !important;
    display:inline-block;
    vertical-align: middle;
}
.fe-t h4{
    margin: 0;
    display: inline-block;
    vertical-align: middle;
    font-size: 19px;
    color: #000;
    font-weight: normal;
    line-height: 1.4;
    padding-left: 8px;

}
.fe-2 p{
  font-size: 15px;
    line-height: 1.4;
    margin: 0;
    color: #555;
    padding-top: 8px;
}
.pr-btn{
    width:100%;
    display:inline-block;
    text-align: center;
    margin:50px 0px 25px 0px;
}
.pr-btn a {
    text-decoration: none;
    color: #fff;
    padding: 12px 35px 17px 35px;
    display: inline-block;
    border-radius: 5px;
    font-size: 28px;
    font-weight: 500;
    line-height: 1.2;
    background: -webkit-linear-gradient(to right,#eb3349,#f45c43);
    font-weight: 600;
    background: #eb3349;
    background: linear-gradient(to right,#eb3349,#f45c43);
    margin-top: 0px;
    box-shadow: 0 0.15em 0.65em 0 rgba(0,0,0,0.25);
}
/** AMP Upgrade CSS **/
.amp-upg{
    background:#f5f5f5;
    padding:60px 10px 0px 10px;
}
.upg-t{
    text-align: center;
    color: #222;
}
.upg-t h2{
    margin: 0;
    font-size: 35px;
    color: #060606;
    line-height: 1.3;
    font-weight: 500;
}
.upg-t > span{
    font-size: 14px;
    line-height: 1.2;
    margin-top: 15px;
    display: inline-block;
    color: #666666;
}
.pri-lst{
    width: 100%;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    margin-top:70px;
    grid-gap:1px;
    box-shadow: 0px 10px 15px 1px #ddd;
}
.pri-tb{
    background: #fff;
    text-align: center;
    border: 1px solid #f9f9f9;
    position: relative;
}
.pri-tb:hover{
    border:1px solid #489bff;
}
.pri-tb a:hover .pri-by{
    background:#1e8fff;
}
.pri-tb a{ display: inline-block;
    text-decoration: none;
     color:#222;
    padding: 20px 12px;
}
.pri-tb h5{
    margin:0px 0px 20px 0px;
     font-size:13px;
    line-height: 1.2;
    letter-spacing: 2px;
    font-weight: 400;
    color: #000;
}
.pri-tb span{
    display:block;
}
.pri-tb .amt{
    font-size: 40px;
    color: #1e8fff;
    font-weight: 500;
    margin-bottom:20px;
    display: block;
}
.pri-tb .d-amt{
    font-size: 24px;
    color: #666;
    font-weight: 500;
    margin-bottom: 15px;
    display: none;
    text-decoration: line-through;
}
.d-amt sup{
    line-height: 0;
    position: relative;
    top: 7px;
}
.pri-tb .s-amt{
    font-size: 13px;
    color: #4caf50;
    font-weight: 500;
    margin-bottom:10px;
    display: block;
}
.pri-tb .amt sup{
    font-size:22px;
    padding: 0px 4px 0px 0px;
    position: relative;
    top: 7px;
}
.pri-tb .bil{
    color:#aaa;
    font-size: 12px;
    margin-bottom: 20px;
}
.pri-tb .s, .pri-tb .e, .pri-tb .f{
    font-size: 14px;
    margin-bottom: 15px;
    color: #3B4750;
}
.pri-tb .sv{
    font-size: 12px;
    color: #fff;
    background: #4CAF50;
    margin: 0 auto;
    padding: 1px 7px 2px 7px;
    border-radius: 45px;
    display: none;
}
.pri-by{
    font-size: 15px;
    line-height: 1.2;
    background: #333;
    border-radius: 2px;
    padding: 9px 18px 10px 18px;
    display: inline-block;
    color: #fff;
    margin-top: 29px;
    font-weight: 500;
}
.pri-lst .rec{
    box-shadow: 0px 1px 40px 0px #ccc;
    background: #ffffff;
    z-index: 9;
    margin-top: -20px;
    position: relative;
}
.pri-lst .rec:hover .rcm{
    background: #489bff;
    color: #fff;    
}
.pri-lst .rec .pri-by{
    background: #1e8fff;
}
.rcm{
    background: #dedede;
    color: #888;
    position: absolute;
    top: -20px;
    left: 0;
    right: -1px;
    bottom: auto;
    padding: 2px 0px;
    font-size: 11px;
    letter-spacing: 2px;
}
.tru-us{
    text-align: center;
    padding: 60px 0px;
    margin:0 auto;
    font-size: 16px;
    color: #222;
}
.tru-us h2{
    margin:20px 0px 0px 0px;
    font-size: 28px;
    font-weight: 500;
}
.tru-us p{
    font-size: 17px;
    margin: 19px 15% 18px 15%;
    color: #666666;
    line-height: 29px;
}
.tru-us a{
      font-size: 18px;
    color: #489bff;
    text-decoration: none;
    font-weight: 400;}
/** F A Q CSS **/
.ampfaq{
    width:100%;
    margin:25px 0px;
}
.ampfaq h4{
    margin: 0;
    text-align: center;
    font-size: 20px;
    font-weight: 500;
    color: #333;
}
.faq-lst{
    margin-top:50px;
    display: grid;
    grid-template-columns:1fr 1fr;
}
.lt{
    padding-left:50px;
}
.rt, .lt{
    width:70%;
}
.lt ul, .rt ul{
    margin:0;
}
.lt ul li, .rt ul li{
    color: #222;
    margin-bottom: 30px !important;
}
.lt span, .rt span{
      font-size: 17px;
    font-weight: 500;
    margin-bottom: 6px;
    display: inline-block;}
.lt p, .rt p{
    font-size: 15px;
    margin: 0;
}
.f-cnt{
    text-align: center;
    margin-top: 20px;
    color: #222;
}
.f-cnt span{
    font-size: 17px;
    margin:8px 0px;
    font-weight: 500;
}
.f-cnt p{
    font-size: 15px;
    margin:6px 0
}
.f-cnt a{
    background: #333;
    color: #fff;
    padding: 15px 30px;
    text-decoration: none;
    font-size: 18px;
    font-weight: 500;
    display: inline-block;
    margin-top: 15px;
}

.beta_tester {
    float: left;
    margin-top: 12px;
    margin-left: 15px
}
.beta_tester a{
    text-decoration: none;
background: #fff;
    color: #4452a7;
    padding: 6px 10px;
    border-radius: 20px;
    border: 1px solid #ddd;
}
.beta_tester a:hover{
    text-decoration: underline;
}
@media(max-width:1366px){
    .amp-upg{padding: 60px 0px 0px 0px}
    .fp-cnt p{line-height: 29px;font-size: 20px}
}
@media(max-width:1280px){
    .fp-cnt {
        top: 1%;
    }

}
@media(max-width:768px){
    .ext{
        grid-template-columns: 1fr;
        grid-gap: 30px 0px;
        padding: 30px;
    }
    .pvf-tlt h2 {
        font-size: 26px;
    }
    .pvf-cmp {
        grid-template-columns: 1fr;
    }
    .pr-btn a {
        font-size: 22px;
    }
    .pri-lst {
        grid-template-columns: 1fr 1fr 1fr;
    }
    .fp-cnt p {
        line-height: 1.5;
        font-size: 16px;
        margin-top: 15px;
        padding: 0 20px;
    }
    .fp-cnt .buy{
        font-size:16px;
        padding: 8px 30px;
    }
    .fp-cnt {
        top: 15px;
    }
    .fp-cnt h1 {
        font-size: 30px;
    }
    .ex-1 {
        width: 100%;
    }
    .faq-lst {
        grid-template-columns: 1fr;
    }
    .rt{
        padding-left:50px;
    }
   
}

#redux_builder_amp-menu-type .redux-image-select li, #redux_builder_amp-single-design-type .redux-image-select li{
    width:40%;
    padding-right:30px;
}
/*** Menu Types CSS ***/
#redux_builder_amp-menu-type ul.redux-image-select li{
    width:42% ;
    padding:0px 15px;
}
#redux_builder_amp-menu-type .redux-image-select li, #redux_builder_amp-single-design-type .redux-image-select li{
    width:40%;
    padding-right:30px;
}
/*** Related Post Desings ***/
#redux_builder_amp-rp_design_type ul{
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-gap: 30px;
}
#redux_builder_amp-ampforwp-gallery-design-type ul{
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-gap: 30px;
}
.redux-image-select-selected{
    position: relative;
}
.redux-image-select-selected:before{
    content: " ";
    display: block;
    border: solid 16px rgb(19, 163, 21);
    border-radius: 50px;
    height: 0;
    width: 0;
    position: absolute;
    left: 0;
    top: 0px;
}
.redux-image-select-selected:after{
    content: " ";
    display: block;
    width: 0.3em;
    height: 0.6em;
    border: solid white;
    border-width: 0 0.2em 0.2em 0;
    position: absolute;
    left: 11px;
    top: 6px;
    -webkit-transform: rotate(45deg);
    -moz-transform: rotate(45deg);
    -o-transform: rotate(45deg);
    transform: rotate(45deg);
    font-size: 20px
}
.fp-cnt h1 span{
    text-decoration: underline;
}
i.el.el-gift:after {
    content: "offer";
    font-size: 12px;
    position: absolute;
    bottom: -17px;
    left: -4px;
    color: #333;
}
i.el.el-gift:before{
    color: #333;    
}
.redux-sidebar .redux-group-menu li a:hover i.el.el-gift:before,
.redux-sidebar .redux-group-menu li a:hover i.el.el-gift:after{
    color: #fff;
}
.redux-sidebar .redux-group-menu li.active a:hover i.el.el-gift:before,
.redux-sidebar .redux-group-menu li.active a:hover i.el.el-gift:after{
    color: #333;
}
a.bfcm {
    text-align: center;
    display: block;
}
#info-info_normal_amp_popup.redux-notice-field{
    padding: 0;
    box-shadow: none;
    border: none;
} 
fieldset#redux_builder_amp-AMPforWP_cache_mode .description.field-desc {
    margin: 20px 0px;
}
.opt-go-amp-cache span.description {
    margin-left: 13px;
}
.ampforwp-ux-right-bottom{
    margin-top:55%;
}
.privacy-settings label{
    float: right;
    top: -40px;
}
.redux-sidebar .redux-group-menu .ampforwp-setup-not-tt + li a{
    padding: 15px 8px 15px 10px;
}
#wpcontent {
    padding-left: 0 !important;
}
.quads-btn{border:none;color:white;padding:9px 18px;text-align:center;text-decoration:none;display:inline-block;font-size:16px;margin:4px 2px;cursor:pointer;}
.quads-btn:hover{color:#fff;}
.quads-btn-primary{background-color:#005aef;font-size:16px;border-radius:4px;padding:10px 20px;}
a.quads-nav-link-active{border-bottom:3px solid #005af0!important;position:relative;padding:19px 20px;color:#005af0!important;}
.quads-logo{height:42px;}
.quads-ad-header{display:flex;padding:17px 0% 17px 2%;align-items:center;height:35px;background:#fff;align-items:center;justify-content:space-between;border-bottom:1px solid #D8D8D8;box-shadow:rgba(0, 0, 0, 0.04) 0px 3px 6px 0px;}
.quads-ad-menu{display:flex;}
.quads-ad-tab ul li{font-size:22px;font-weight:400;}
.quads-ad-tab ul{margin:0;}
.quads-ad-tab ul li a{color:#111111;text-decoration:none;padding:20px 30px;}
.quads-ad-tab ul li a:focus{box-shadow:none;}
.quads-ad-tab ul li{display:inline-block;margin-bottom:0;}
.quads-ad-tab ul li a.quads-btn{border:1px solid #1A73E8;background:none;padding:11px 16px 11px 43px;border-radius:50px;font-size:18px;position:relative;margin:0px 30px 0px 15px;position:relative;top:0px;}
.quads-ad-tab ul li a.quads-btn span{font-size:32px;color:#1A73E8;position:absolute;top:4px;left:5px;}
.quads-ad-tab ul li a.quads-btn:hover{background:#1873e8;color:#fff;}
.quads-ad-tab ul li a.quads-btn:hover span{color:#fff;}
.quads-got_pro{font-size:13px!important;padding:6px 12px;border-radius:60px;background:-webkit-linear-gradient(to right, #eb3349, #f45c43);background:linear-gradient(to right, #eb3349, #f45c43);color:#fff!important;box-shadow:0em 0.15em 0.65em 0em rgba(0, 0, 0, 0.25);margin:0 auto 0 23px;text-decoration:none;font-weight:500;}
.quads-ad-tab .quads-nav-link{color:#1A73E8;border-bottom:3px solid transparent;}
a.quads-nav-link-active{border-bottom:3px solid #005af0!important;position:relative;padding:19px 20px;color:#005af0!important;}
a.quads-nav-link:focus{box-shadow:none;}
.quads-nav-link{display:block;font-size:12px;top:10px;position:relative;}
.quads-ad-tab-wrapper .quads-nav-link{top:0;}
.quads-ad-tab-wrapper .quads-nav-link{font-size:22px;}
.quads-ad-header{height:27px;}
}

</style>
  ';
}

/**
 * Add-ons
 *
 * Renders the add-ons content.
 *
 * @since 1.1.8
 * @return void
 */
function quads_add_ons_page() {
  
    echo '<div class="quads-ad-header">
    <div class="quads-logo"><img height="42" width="175" src="' . esc_url(QUADS_PLUGIN_URL . 'admin/assets/js/src/images/quads-v2-logo.png') . '"></div>
    <a class="quads-got_pro premium_features_btn" href="' . esc_url(admin_url('admin.php?page=quads-addons#upgrade_to_premium')) . '">' . esc_html__('Upgrade to Premium', 'quick-adsense-reloaded') . '</a>
    <div class="quads-ad-menu">
            <div class="quads-ad-tab-wrapper">
                <div class="quads-ad-tab">
                    <ul>
                        <li><a class="quads-nav-link" href="' . esc_url(admin_url('admin.php?page=quads-settings')) . '">' . esc_html__('Ads', 'quick-adsense-reloaded') . '</a></li>
                        <li><a class="quads-nav-link" href="' . esc_url(admin_url('admin.php?page=quads-settings&path=settings')) . '">' . esc_html__('Settings', 'quick-adsense-reloaded') . '</a></li>
                        <li class="current"><a class="quads-nav-link" href="' . esc_url(admin_url('admin.php?page=quads-settings&path=reports')) . '">' . esc_html__('Reports', 'quick-adsense-reloaded') . '</a></li>
                    </ul>
                </div>
            </div>
    </div>
    </div>
    <div id="quads_freevspro">
    <div class="fp-wr">
        <div class="fp-cnt">
            <h1>' . esc_html__('Upgrade to Pro', 'quick-adsense-reloaded') . '</h1>
            <p>' . esc_html__('Take your Quads to the next level Save time & earn more with next level AdSense integration!', 'quick-adsense-reloaded') . '</p>
            <a class="buy" href="#upgrade">' . esc_html__('BUY NOW', 'quick-adsense-reloaded') . '</a>
        </div>
        <div class="pvf">
            <div class="ext">
                <div class="ex-1 e-1">
                    <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/ex-1.png') . '" />
                    <h4>' . esc_html__('Features', 'quick-adsense-reloaded') . '</h4>
                    <p>' . esc_html__('Includes a suite of advanced features like Ad Rotator, Group Insertion, GEO Location 10+ premium features.', 'quick-adsense-reloaded') . '</p>
                </div>
                <div class="ex-1 e-2">
                    <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/ex-2.png') . '" />
                    <h4>' . esc_html__('Simple Setup', 'quick-adsense-reloaded') . '</h4>
                    <p>' . esc_html__('We focus on important stuff and keep it clean and simple. WP QUADS Pro makes it extremely easy to deliver well converting ads to your audience.', 'quick-adsense-reloaded') . '</p>
                </div>
                <div class="ex-1 e-3">
                    <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/ex-3.png') . '" />
                    <h4>' . esc_html__('Dedicated Support', 'quick-adsense-reloaded') . '</h4>
                    <p>' . esc_html__('Get private ticketing help from our full-time staff who helps you with the technical issues.', 'quick-adsense-reloaded') . '</p>
                </div>
            </div><!-- /. ext -->
            <div class="pvf-cnt">
                <div class="pvf-tlt">
                    <h2 id="upgrade_to_premium">' . esc_html__('Compare Pro vs. Free Version', 'quick-adsense-reloaded') . '</h2>
                    <span>' . esc_html__("See what you'll get with the WPQuads Premium Version", 'quick-adsense-reloaded') . '</span>
                </div>
                <div class="pvf-cmp">
                    <div class="fr">
                        <h1>' . esc_html__('FREE', 'quick-adsense-reloaded') . '</h1>
                        <div class="fr-fe">
                            <div class="fe-1">
                                <h4>' . esc_html__('Continuous Development', 'quick-adsense-reloaded') . '</h4>
                                <p>' . esc_html__('We take bug reports and feature requests seriously. We’re Continuously developing & improve this product for last 4 years with passion and love.', 'quick-adsense-reloaded') . '</p>
                            </div>
                            <div class="fe-1">
                                <h4>' . esc_html__('10+ Features', 'quick-adsense-reloaded') . '</h4>
                                <p>' . esc_html__("We're constantly expanding the plugin and make it more useful. We have wide variety of features which will fit any use-case.", 'quick-adsense-reloaded') . '</p>
                            </div>
                            <div class="fe-1">
                                <h4>' . esc_html__('Design', 'quick-adsense-reloaded') . '</h4>
                                <p>' . esc_html__('We focus on important stuff and keep it clean and simple. WP QUADS Pro makes it extremely easy to deliver well converting ads to your audience.', 'quick-adsense-reloaded') . '</p>
                            </div>
                            <div class="fe-1">
                                <h4>' . esc_html__('Technical Support', 'quick-adsense-reloaded') . '</h4>
                                <p>' . esc_html__('We have a full time team which helps you with each and every issue regarding Quads.', 'quick-adsense-reloaded') . '</p>
                            </div>
                        </div><!-- /. fr-fe -->
                    </div><!-- /. fr -->
                    <div class="pr">
                        <h1>' . esc_html__('PRO', 'quick-adsense-reloaded') . '</h1>
                        <div class="pr-fe">
                            <span>' . esc_html__('Everything in Free, and:', 'quick-adsense-reloaded') . '</span>
                            <div class="fet">
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/tick.png') . '" />
                                        <h4>' . esc_html__('GEO Location', 'quick-adsense-reloaded') . '</h4>
                                    </div>
                                    <p>' . esc_html__('Target the ads by Country & City.', 'quick-adsense-reloaded') . '</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/tick.png') . '" />
                                        <h4>' . esc_html__('Ad Rotator', 'quick-adsense-reloaded') . '</h4>
                                    </div>
                                    <p>' . esc_html__('with on-reload or auto-refresh functionality.', 'quick-adsense-reloaded') . '</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/tick.png') . '" />
                                        <h4>' . esc_html__('Group Insertion', 'quick-adsense-reloaded') . '</h4>
                                    </div>
                                    <p>' . esc_html__('Insert multiple ads with one-go.', 'quick-adsense-reloaded') . '</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/tick.png') . '" />
                                        <h4>' . esc_html__('AMP Support', 'quick-adsense-reloaded') . '</h4>
                                    </div>
                                    <p>' . esc_html__('Add ads on your AMP page.', 'quick-adsense-reloaded') . '</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/tick.png') . '" />
                                        <h4>' . esc_html__('Global Excluder', 'quick-adsense-reloaded') . '</h4>
                                    </div>
                                    <p>' . esc_html__('Exclude the ads based on the tags.', 'quick-adsense-reloaded') . '</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/tick.png') . '" />
                                        <h4>' . esc_html__('Google Analytics', 'quick-adsense-reloaded') . '</h4>
                                    </div>
                                    <p>' . esc_html__('Check how many visitors are using ad blockers.', 'quick-adsense-reloaded') . '</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/tick.png') . '" />
                                        <h4>' . esc_html__('Private Support', 'quick-adsense-reloaded') . '</h4>
                                    </div>
                                    <p>' . esc_html__('Get help from our team of experts.', 'quick-adsense-reloaded') . '</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="' . esc_url(QUADS_PLUGIN_URL . 'assets/images/tick.png') . '" />
                                        <h4>' . esc_html__('And much more', 'quick-adsense-reloaded') . '</h4>
                                    </div>
                                    <p>' . esc_html__('With upcoming feature updates.', 'quick-adsense-reloaded') . '</p>
                                </div>
                            </div><!-- /. fet -->
                        </div><!-- /. pr-fe -->
                    </div><!-- /. pr -->
                </div><!-- /. pvf-cmp -->
            </div><!-- /. pvf-cnt -->
        </div><!-- /. pvf -->
    </div><!-- /. fp-wr -->
    </div><!-- /#quads_freevspro -->';


}