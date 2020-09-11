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
           All_ad_network: [
                    {ad_type:'adsense',ad_type_name:'AdSense'},
                    {ad_type:'double_click',ad_type_name:'Google Ad Manager'},
                    {ad_type:'yandex',ad_type_name:'Yandex'},  
                    {ad_type:'mgid',ad_type_name:'MGID'}, 
                    {ad_type:'taboola',ad_type_name:'Taboola'}, 
                    {ad_type:'media_net',ad_type_name:'Media.net'},
                    {ad_type:'mediavine',ad_type_name:'Mediavine'},
                    {ad_type:'outbrain',ad_type_name:'Outbrain'},  
                    {ad_type:'plain_text',ad_type_name:'Plain Text / HTML / JS'},
                    {ad_type:'ad_image',ad_type_name:'Banner Ad'},
                    {ad_type:'background_ad',ad_type_name:'Background ad'},    
                    {ad_type:'random_ads',ad_type_name:'Random Ads'},          
                    ]
        };            
    }
          getImageByAdType = (type, index,return_type='') =>{
        let type_img = [];
        let img_url  = '';
    
          switch (type) {
            case 'adsense':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/add_adsense_logo.png';
              break;
    
            case 'plain_text':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/custom_code.png';
              break;
              case 'random_ads':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/random_ads.png';
              break;
              case 'double_click':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/double_click.png';
              break;
               case 'yandex':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/yandex.png';
              break;
              case 'mgid':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/mgid.png';
              break;
              case 'ad_image':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/banner_ad.png';
              break;
              case 'taboola':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/taboola.png';
              break;
              case 'media_net':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/medianet.png';
              break;
              case 'mediavine':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/mediavine.png';
              break;
              case 'outbrain':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/outbrain.png';
              break;
              case 'background_ad':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/bg_ad.png';
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
    }
    componentDidMount(){ 
        this.state.All_ad_network.map((item, index ) => {
            var link = document.createElement('link');
              link.rel = "preload";
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
                <div className="material-icons quads-close-create-page"><a onClick={this.hideAddTypeSelector} className="quads-full-page-modal-close">close</a></div>
                <h3>{__('AD Integrations', 'quick-adsense-reloaded')}</h3>
                 <div>                 
                  <AdTypeSelectorNavLink 
                  All_ad_network            =   {this.state.All_ad_network}
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
                <li><div className="quads-add-btn"><a className="quads-btn quads-btn-primary" onClick={this.showAddTypeSelector}><Icon>add_circle</Icon>Create Ad</a></div></li>                
            </ul>   
        </div> 
        </div>
        );
    }
}

export default QuadsAdListNavLink;