import React, { Component, Fragment } from 'react';
import {Link} from 'react-router-dom';
import queryString from 'query-string'
import './AdTypeSelectorNavLink.scss';

class AdTypeSelectorNavLink extends Component {

    constructor(props) {
        super(props);    
        this.state = {
          redirect:false,  
          popular_ad_network : [
                    {ad_type:'adsense',ad_type_name:'AdSense'},
                    {ad_type:'plain_text',ad_type_name:'Plain Text / HTML / JS'},               
                    ],
           All_ad_network: [
                    {ad_type:'adsense',ad_type_name:'AdSense'},
                    {ad_type:'double_click',ad_type_name:'Google Ad Manager'},
                    {ad_type:'yandex',ad_type_name:'Yandex'},  
                    {ad_type:'mgid',ad_type_name:'MGID'}, 
                    {ad_type:'ad_image',ad_type_name:'Banner Ad'},
                    {ad_type:'plain_text',ad_type_name:'Plain Text / HTML / JS'},
                    {ad_type:'taboola',ad_type_name:'Taboola'}, 
                    {ad_type:'media_net',ad_type_name:'Media.net'},
                    {ad_type:'mediavine',ad_type_name:'Mediavine'},
                    {ad_type:'outbrain',ad_type_name:'Outbrain'},  
                     {ad_type:'background_ad',ad_type_name:'Background ad'},    
                    {ad_type:'random_ads',ad_type_name:'Random Ads'},  
                               
                    ]          
       };       
      } 
      getImageByAdType = (type, index) =>{
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
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/add_adsense_logo.png';
              break;
              case 'media_net':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/add_adsense_logo.png';
              break;
              case 'mediavine':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/add_adsense_logo.png';
              break;
              case 'outbrain':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/add_adsense_logo.png';
              break;
              case 'background_ad':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/add_adsense_logo.png';
              break;
            default:
              break;
          }
    
          type_img.push(<img key={index}  src={img_url} />);
          
        return type_img;
      }  
    
  render() {    

    const {__} = wp.i18n;    
    const page = queryString.parse(window.location.search);     
                                
    return(  

                    <div className="quads-ad-networks">                                                                                
                    <ul>
                    {this.state.All_ad_network.map((item, index ) => (
                        <li key={item.ad_type}><div className="quads-ad-type-link"><Link  to={`admin.php?page=quads-settings&path=wizard&ad_type=${item.ad_type}`} className="quads-nav-link">{this.getImageByAdType(item.ad_type, index)}</Link></div></li>  
                    ))}
                    </ul>
                    </div>                            
        );
    }
}

export default AdTypeSelectorNavLink;