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
    ob_start();
    $freepro_listing = '
<div id="quads_freevspro">
    <div class="fp-wr">
        <div class="fp-cnt">
            <h1>Upgrade to Pro</h1>
            <p>Take your Quads to the next level Save time & earn more with next level AdSense integration!</p>
            <a class="buy" href="#upgrade">BUY NOW</a>
        </div>
        <div class="pvf">
            <div class="ext">
                <div class="ex-1 e-1">
                    <img src="'.QUADS_PLUGIN_URL . 'assets/images/ex-1.png" />
                    <h4>Features</h4>
                    <p>Includes a suite of advanced features like Ad Rotator, Group Insertion, GEO Location 10+ premium features.</p>
                </div>
                <div class="ex-1 e-2">
                    <img src="'.QUADS_PLUGIN_URL . 'assets/images/ex-2.png" />
                    <h4>Simple Setup</h4>
                    <p>We focus on important stuff and keep it clean and simple. WP QUADS Pro makes it extremely easy to deliver well converting ads to your audience.</p>
                </div>
                <div class="ex-1 e-3">
                    <img src="'.QUADS_PLUGIN_URL . 'assets/images/ex-3.png" />
                    <h4>Dedicated Support</h4>
                    <p>Get private ticketing help from our full-time staff who helps you with the technical issues.</p>
                </div>
            </div><!-- /. ext -->
            <div class="pvf-cnt">
                <div class="pvf-tlt">
                    <h2 id="upgrade_to_premium">Compare Pro vs. Free Version</h2>
                    <span>See what you\'ll get with the WPQuads Premium Version</span>
                </div>
                <div class="pvf-cmp">
                    <div class="fr">
                        <h1>FREE</h1>
                        <div class="fr-fe">
                            <div class="fe-1">
                                <h4>Continuous Development</h4>
                                <p>We take bug reports and feature requests seriously. We’re Continuously developing & improve this product for last 4 years with passion and love.</p>
                            </div>
                            <div class="fe-1">
                                <h4>10+ Features</h4>
                                <p>We\'re constantly expanding the plugin and make it more useful. We have wide variety of features which will fit any use-case.</p>
                            </div>
                            <div class="fe-1">
                                <h4>Design</h4>
                                <p>We focus on important stuff and keep it clean and simple. WP QUADS Pro makes it extremely easy to deliver well converting ads to your audience.</p>
                            </div>
                            <div class="fe-1">
                                <h4>Technical Support</h4>
                                <p>We have a full time team which helps you with each and every issue regarding Quads.</p>
                            </div>
                        </div><!-- /. fr-fe -->
                    </div><!-- /. fr -->
                    <div class="pr">
                        <h1>PRO</h1>
                        <div class="pr-fe">
                            <span>Everything in Free, and:</span>
                            <div class="fet">
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>GEO Location</h4>
                                    </div>
                                    <p>Target the ads by Country & City.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>Ad Rotator</h4>
                                    </div>
                                    <p>with on-reload or auto-refresh functionality.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>Group Insertion</h4>
                                    </div>
                                    <p>Insert multiple ads with one-go.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>AMP Support</h4>
                                    </div>
                                    <p>Add ads on your AMP page.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>Global Excluder</h4>
                                    </div>
                                    <p>Exclude the ads based on the tags.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>Google Analytics</h4>
                                    </div>
                                    <p>Check how many visitors are using ad blockers.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>Google Auto Ads</h4>
                                    </div>
                                    <p>Dedicated Google Auto Ads tag.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>Dedicated Support</h4>
                                    </div>
                                    <p>With a Dedicated person helping you with the extension setup and questions.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>Continious Updates</h4>
                                    </div>
                                    <p>We\'re continiously updating our premium features and releasing them.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>Innovation</h4>
                                    </div>
                                    <p>Be the first one to get the innovative features that we build in the future.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>Reports</h4>
                                    </div>
                                    <p>Reports feature we can display the day-wise earing and forecast.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>Hide Quads Markup</h4>
                                    </div>
                                    <p>Hide Quads Markup from sourcecode.</p>
                                </div>
                                <div class="fe-2">
                                    <div class="fe-t">
                                        <img src="'.QUADS_PLUGIN_URL . 'assets/images/tick.png" />
                                        <h4>50+ Plugin Support</h4>
                                    </div>
                                    <p>Works smoothly almost all the plugin.</p>
                                </div>
                            </div><!-- /. fet -->
                            <div class="pr-btn">
                                <a href="#upgrade">Upgrade to Pro</a>
                            </div><!-- /. pr-btn -->
                        </div><!-- /. pr-fe -->
                    </div><!-- /.pr -->
                </div><!-- /. pvf-cmp -->
            </div><!-- /. pvf-cnt -->
            <div id="upgrade" class="amp-upg">
                <div class="upg-t">
                    <h2>Let\'s Upgrade Your Ads Revenue</h2>
                    <span>Choose your plan and upgrade in minutes!</span>
                </div>
                <div class="pri-lst">
                    <div class="pri-tb">
                        <a href="https://wpquads.com/checkout?edd_action=add_to_cart&amp;download_id=11&amp;edd_options[price_id]=1">
                            <h5>PERSONAL</h5>
                            <span class="d-amt"><sup>$</sup>89</span>
                            <span class="amt"><sup>$</sup>89</span>
                            <span class="s-amt">(Save $59)</span>
                            <span class="bil">Billed Annually</span>
                            <span class="s">1 Site License</span>
                            <span class="e">E-mail support</span>
                            <span class="f">Pro Features</span>
                            <span class="sv">Save $800+</span>
                            <span class="pri-by">Buy Now</span>
                        </a>
                    </div>
                    <div class="pri-tb rec">
                        <a href="https://wpquads.com/checkout?edd_action=add_to_cart&amp;download_id=11&amp;edd_options[price_id]=2">
                            <h5>MULTIPLE</h5>
                            <span class="d-amt"><sup>$</sup>139</span>
                            <span class="amt"><sup>$</sup>139</span>
                            <span class="s-amt">(Save $79)</span>
                            <span class="bil">Billed Annually</span>
                            <span class="s">5 Site License</span>
                            <span class="e">E-mail support</span>
                            <span class="f">Pro Features</span>
                            <span class="sv">Save 55%</span>
                            <span class="pri-by">Buy Now</span>
                         <span class="rcm">RECOMMENDED</span>
                        </a>
                    </div>
                    <div class="pri-tb ">
                        <a href="https://wpquads.com/checkout?edd_action=add_to_cart&amp;download_id=11&amp;edd_options[price_id]=3">
                            <h5>WEBMASTER</h5>
                            <span class="d-amt"><sup>$</sup>199</span>
                            <span class="amt"><sup>$</sup>199</span>
                            <span class="s-amt">(Save $99)</span>
                            <span class="bil">Billed Annually</span>
                            <span class="s">Unlimited Site License</span>
                            <span class="e">E-mail support</span>
                            <span class="f">Pro Features</span>
                            <span class="sv">Save 83%</span>
                            <span class="pri-by">Buy Now</span>

                        </a>
                    </div>
                    <div class="pri-tb">
                        <a href="https://wpquads.com/checkout?edd_action=add_to_cart&amp;download_id=11&amp;edd_options[price_id]=4">
                            <h5>FREELANCER</h5>
                            <span class="d-amt"><sup>$</sup>449</span>
                            <span class="amt"><sup>$</sup>449</span>
                            <span class="s-amt">(Save $119)</span>
                            <span class="bil">Lifetime Support</span>
                            <span class="s">Unlimited Site License</span>
                            <span class="e">E-mail support</span>
                            <span class="f">Pro Features</span>
                            <span class="sv">Save 90%</span>
                            <span class="pri-by">Buy Now</span>
                        </a>
                    </div>
           
                </div><!-- /.pri-lst -->
                <div class="tru-us">
                    <img src="'.QUADS_PLUGIN_URL . 'assets/images/rating.png" />
                    <h2>Trusted by more that 60,000+ Users!</h2>
                    <p>More than 60k Websites, Blogs & E-Commerce website are powered by our Quads making it the #1 Rated Quads plugin in WordPress Community.</p>
                    <a href="https://wordpress.org/support/plugin/quick-adsense-reloaded/reviews/?filter=5" target="_blank">Read The Reviews</a>
                </div>
            </div><!--/ .amp-upg -->
            <div class="ampfaq">
                <h4>Frequently Asked Questions</h4>
                <div class="faq-lst">
                    <div class="lt">
                        <ul>
                            <li>
                                <span>Is there a setup fee?</span>
                                <p>No. There are no setup fees on any of our plans</p>
                            </li>
                            <li>
                                <span>What\'s the time span for your contracts?</span>
                                <p>All the plans are year-to-year which are subscribed annually.</p>
                            </li>
                            <li>
                                <span>What payment methods are accepted?</span>
                                <p>We accepts PayPal and Credit Card payments.</p>
                            </li>
                            <li>
                                <span>Do you offer support if I need help?</span>
                                <p>Yes! Top-notch customer support for our paid customers is key for a quality product, so we’ll do our very best to resolve any issues you encounter via our support page.</p>
                            </li>
                            <li>
                                <span>Can I use the plugins after my subscription is expired?</span>
                                <p>Yes, you can use the plugins but you will not get future updates for those plugins.</p>
                            </li>
                        </ul>
                    </div>
                    <div class="rt">
                        <ul>
                            <li>
                                <span>Can I cancel my membership at any time?</span>
                                <p>Yes. You can cancel your membership by contacting us.</p>
                            </li>
                            <li>
                                <span>Can I change my plan later on?</span>
                                <p>Yes. You can upgrade or downgrade your plan by contacting us.</p>
                            </li>
                            <li>
                                <span>Do you offer refunds?</span>
                                <p>You are fully protected by our 100% Money Back Guarantee Unconditional. If during the next 14 days you experience an issue that makes the plugin unusable and we are unable to resolve it, we’ll happily offer a full refund.</p>
                            </li>
                            <li>
                                <span>Do I get updates for the premium plugin?</span>
                                <p>Yes, you will get updates for all the premium plugins until your subscription is active.</p>
                            </li>
                        </ul>
                    </div>
                </div><!-- /.faq-lst -->
                <div class="f-cnt">
                    <span>I have other pre-sale questions, can you help?</span>
                    <p>All the plans are year-to-year which are subscribed annually.</p>
                    <a href="https://wpquads.com/support/">Contact a Human</a>
                </div><!-- /.f-cnt -->
            </div><!-- /.faq -->
        </div><!-- /. pvf -->
    </div><!-- /. fp-wr --></div>';

    echo $freepro_listing;

    echo ob_get_clean();
}
