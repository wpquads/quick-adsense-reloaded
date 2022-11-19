import React, { Component, Fragment } from 'react';
import {Link} from 'react-router-dom';
import queryString from 'query-string'
import AdTypeSelectorNavLink from "../ad-type-selector-nav/AdTypeSelectorNavLink";
import Icon from '@material-ui/core/Icon';

class QuadsAdListNavLink extends Component {

    constructor(props) {

        super(props);
        this.state = {
            ad_type_toggle:this.props.ad_type_toggle,
            settings:this.props.settings,
            displayReports:false,
            displayad_logging:false,
           All_ad_network: [
                    {ad_type:'adsense',ad_type_name:'AdSense'},
                    {ad_type:'double_click',ad_type_name:'Double Click'},
                    // {ad_type:'adpushup',ad_type_name:'AdPushup'},
                    {ad_type:'yandex',ad_type_name:'Yandex'},
                    {ad_type:'mgid',ad_type_name:'MGID'},
                    {ad_type:'taboola',ad_type_name:'Taboola'},
                    {ad_type:'media_net',ad_type_name:'Media.net'},
                    {ad_type:'mediavine',ad_type_name:'Mediavine'},
                    {ad_type:'outbrain',ad_type_name:'Outbrain'},
                    {ad_type:'propeller',ad_type_name:'Propeller'},
                    {ad_type:'infolinks',ad_type_name:'Infolinks'},
                  ],
           All_ad_network_format: [
                    {ad_type:'background_ad',ad_type_name:'Background ad'},
                    {ad_type:'video_ads',ad_type_name:'Video Ad'},
                    {ad_type:'plain_text',ad_type_name:'Plain Text / HTML / JS'},
                    {ad_type:'ad_image',ad_type_name:'Banner Ad'},
                    {ad_type:'random_ads',ad_type_name:'Random Ads'},
                    {ad_type:'popup_ads',ad_type_name:'Popup Ads'},
                    {ad_type:'loop_ads',ad_type_name:'Loop Ads'},
                    {ad_type:'carousel_ads',ad_type_name:'Carousel Ads'},
                    {ad_type:'parallax_ads',ad_type_name:'Parallax Ads'},
                    {ad_type:'floating_cubes',ad_type_name:'Floating Ads',pro:'true'},
                    {ad_type:'rotator_ads',ad_type_name:'Rotator Ads',pro:'true'},
                    {ad_type:'group_insertion',ad_type_name:'Group Insertion',pro:'true'},
                    {ad_type:'skip_ads',ad_type_name:'Skip Ads',pro:'true'},
                    {ad_type:'ad_blindness',ad_type_name:'Ad Blindness',pro:'true'},
                    {ad_type:'ab_testing',ad_type_name:'AB Testing',pro:'true'},
                    {ad_type:'sticky_scroll',ad_type_name:'Hold on Scroll Ads',pro:'true'},
           ]
        };
        this.getSettings();
    }
    getSettings = () => {
        let url = quads_localize_data.rest_url + 'quads-route/get-settings';
        fetch(url,{
            headers: {
                'X-WP-Nonce': quads_localize_data.nonce,
            }
        })
            .then(res => res.json())
            .then(
                (result) => {
                  const { settings } = { ...this.state };
                    Object.entries(result).map(([meta_key, meta_val]) => {
                        if(meta_key=='reports_settings'){
                            this.setState({displayReports:meta_val});
                        }else if(meta_key=='ad_logging'){
                          this.setState({displayad_logging:meta_val});
                      }
                        settings[meta_key] =    meta_val;
                    })
                    this.setState({settings:settings});

                }
            );
    }
          getImageByAdType = (type, index,return_type='') =>{
        let type_img = [];
        let img_url  = '';

          switch (type) {
            case 'adsense':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/adsensev3.png';
              break;
            case 'ab_testing':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ab_icon.jpg';
              break;
            // case 'adpushup':
            //   img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/adpushup.png';
            // break;
            case 'plain_text':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/htmlcode.png';
              break;
              case 'rotator_ads':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/rotator_ads_icon.png';
              break;
              case 'random_ads':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/random_ad_icon.png';
              break;
              case 'popup_ads':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/popup_ads.png';
              break;
              case 'group_insertion':
                  img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/group_insertion_icon.png';
                  break;
              case 'double_click':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/double_click_icon.png';
              break;
               case 'yandex':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/yandex_icon.png';
              break;
              case 'mgid':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/mgid_icon.png';
              break;
              case 'propeller':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/propeller_icon.jpg';
              break;
              case 'ad_image':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/banner_ad_icon.png';
              break;
              case 'video_ads':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/video_ads_icon.jpg';
              break;
              case 'ad_blindness':
                img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ad_blindness_icon.png';
                break;
              case 'ab_testing':
                img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ab_icon.jpg';
                break;
              case 'taboola':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/taboola_icon.png';
              break;
              case 'media_net':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/medianet_icon.png';
              break;
              case 'mediavine':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/mediavine_icon.png';
              break;
              case 'outbrain':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/outbrain_icon.png';
              break;
              case 'infolinks':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/infolinks_icon.png';
              break;
              case 'background_ad':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/bg_ad_icon.png';
              break;
              case 'skip_ads':
                  img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/skip_ads_icon.png';
                  break;
              case 'loop_ads':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/loop_ads_icon.png';
              break;
              case 'carousel_ads':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/carousel_ads_icon.png';
              break;
              case 'parallax_ads':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/parallax_ads_icon.png';
              break;
              case 'sticky_scroll':
                img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/sticky_scroll_icon.png';
                break;
                case 'floating_cubes':
                  img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/floating_ads.png';
                  break;
            default:
              break;
          }
            if(return_type == 'image_url'){
            return img_url;
            }
          type_img.push(<img key={index}  src={img_url} />);

        return type_img;
      }
    showAddTypeSelector = (e) => {


        e.preventDefault();
        this.setState({ad_type_toggle:true});
        
    }
    hideAddTypeSelector = (e) => {
        e.preventDefault();
        this.setState({ad_type_toggle:false});
        this.props.setStateOfToggle(false);
    }
    componentDidMount(){

        this.state.All_ad_network.map((item, index ) => {
            var link = document.createElement('link');
              link.rel = "preload";
              link.key = index;
              link.href = this.getImageByAdType(item.ad_type, index,'image_url');
              link.as = "image";
              document.head.appendChild(link);
            })
    }
  
  
  render() {
    const {__} = wp.i18n;
    const page = queryString.parse(window.location.search);
    let current = 'ads';

    if(typeof(page.path)  != 'undefined' ) {

        if( page.path == 'settings' || page.path == 'settings_tools' || page.path == 'settings_importer' || page.path == 'settings_legacy' || page.path == 'settings_support' || page.path == 'settings_licenses' || page.path == 'settings_google_autoads')  {
            jQuery('.wp-submenu li').removeClass('current');
            jQuery('a[href$="quads-settings&path=settings"]').parent().addClass('current');
            current = 'settings';
        }else if(page.path == 'reports'){
            jQuery('.wp-submenu li').removeClass('current');
            jQuery('a[href$="quads-settings&path=reports"]').parent().addClass('current');
            current = 'reports';
        }else if(page.path == 'view_report'){
          jQuery('.wp-submenu li').removeClass('current');
          jQuery('a[href$="quads-settings&path=reports"]').parent().addClass('current');
            current = 'reports';
        }
        else if(page.path == 'ad_logging'){
          jQuery('.wp-submenu li').removeClass('current');
          jQuery('a[href$="quads-settings&path=ad_logging"]').parent().addClass('current');
          current = 'ad_logging';
      }
        }else if(page.page == 'quads-settings'){
            jQuery('.wp-submenu li').removeClass('current');
            jQuery('a[href$="quads-settings"]').parent().addClass('current');
        }
      return(
        <div className="quads-ad-tab-wrapper">
         <div className="quads-hidden-element">
         {
                this.state.ad_type_toggle || this.props.ad_type_toggle ?
                <div className="quads-full-page-modal">
                <div className="quads-full-page-modal-content">
                <h4 className='quad-ad-network-heading'>{__('Which type of AD would you like to insert?', 'quick-adsense-reloaded')}</h4>
                <div className="material-icons quads-close-create-page"><a onClick={this.hideAddTypeSelector} className="quads-full-page-modal-close">close</a></div>
                 <div>
                  <AdTypeSelectorNavLink
                  All_ad_network            =   {this.state.All_ad_network}
                  All_ad_network_format     =   {this.state.All_ad_network_format}
                  getImageByAdType          =   {this.getImageByAdType} />
                 </div>
                </div>
                </div>
                : ''
              }
         </div>
        <div className="quads-ad-tab">
            <ul>
                <li><Link to={'admin.php?page=quads-settings'} className={current == 'ads' ? 'quads-nav-link quads-nav-link-active ' : 'quads-nav-link'}>{__('Ads', 'quick-adsense-reloaded')}</Link></li>
                <li><Link to={'admin.php?page=quads-settings&path=settings'} className={current == 'settings' ? 'quads-nav-link quads-nav-link-active ' : 'quads-nav-link'}>{__('Settings', 'quick-adsense-reloaded')}</Link></li>
                {this.state.displayReports ?
                <li><Link to={'admin.php?page=quads-settings&path=reports'} className={current == 'reports' ? 'quads-nav-link quads-nav-link-active ' : 'quads-nav-link'}>{__('Reports', 'quick-adsense-reloaded')}</Link></li>
                : null }
                {quads_localize_data.is_pro && this.state.displayad_logging ?
                <li><Link to={'admin.php?page=quads-settings&path=ad_logging'} className={current == 'ad_logging' ? 'quads-nav-link quads-nav-link-active ' : 'quads-nav-link'}>{__('Log', 'quick-adsense-reloaded')}</Link></li>
                : null }
                <li><div className="quads-add-btn"><a className="quads-btn quads-btn-primary" onClick={this.showAddTypeSelector}><Icon>add_circle</Icon>Create Ad</a></div></li>
            </ul>
        </div>
        </div>
        );
    }
}

export default QuadsAdListNavLink;
