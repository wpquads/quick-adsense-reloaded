import React, { Component, Fragment } from 'react';
import { Link } from 'react-router-dom';
import queryString from 'query-string'
import './AdTypeSelectorNavLink.scss';

class AdTypeSelectorNavLink extends Component {

  constructor(props) {
    super(props);
    this.state = {
      redirect: false,
      showGoProPopup: false,
      rotator_ads_status: true,
      blindness_settings: true,
      ab_testing_settings: true,
      skippable_ads: true,
      feature_name:'',
      popular_ad_network: [
        { ad_type: 'adsense', ad_type_name: 'AdSense' },
        { ad_type: 'plain_text', ad_type_name: 'Plain Text / HTML / JS' },
      ],
    };
  }
  componentDidMount() {
    this.getSettings_data();
  }
  changepopupState = (feature_name='') =>{
    this.setState({showGoProPopup:!this.state.showGoProPopup,feature_name:feature_name});
  }
  getSettings_data = () => {
    let url = quads_localize_data.rest_url + 'quads-route/get-settings';
    fetch(url, {
      headers: {
        'X-WP-Nonce': quads_localize_data.nonce,
      }
    })
      .then(res => res.json())
      .then(
        (result) => {

          Object.entries(result).map(([meta_key, meta_val]) => {
            if (meta_key == "rotator_ads_settings") {
              this.setState({ rotator_ads_status: meta_val });
            }
            if (meta_key == "blindness_settings") {
              this.setState({ blindness_settings: meta_val });
            }
            if (meta_key == "ab_testing_settings") {
              this.setState({ ab_testing_settings: meta_val });
            }
            if (meta_key == "skippable_ads") {
              this.setState({ skippable_ads: meta_val });
            }
          })
        },
        (error) => {
        }
      );
  }

  getImageUrlByAdType = (type, index) =>{
    let img_url  = '';
      switch (type) {
          case 'plain_text':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/html_ad_prv.jpg';
          break;
          case 'popup_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/popup_ad_prv.jpg';
          break;
          case 'ad_image':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/banner_ad_prv.jpg';
          break;
          case 'video_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/video_ad_prv.jpg';
          break;
          case 'background_ad':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/background_ad_prv.png';
          break;
          case 'skip_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/skip_ad_prv.jpg';
          break;
          case 'carousel_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/carousel_ad_prv.jpg';
          break;
          case 'parallax_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/parallax_ad_prv.jpg';
          break;
          case 'half_page_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/half_page_slider_ad_prv.jpg';
          break;
          case 'floating_cubes':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/floating_ad_prv.gif';
          break;
          case 'ad_blindness':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ad_blindnes_prv.gif';
          break;
          case 'group_insertion':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/group_insertion_prv.gif';
          break;
          case 'rotator_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/rotator_ad_prv.gif';
          break;
          case 'loop_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/loop_ad_preview.gif';
          break;
          case 'sticky_scroll':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/hold_on_scroll_preview.png';
          break;
          case 'ab_testing':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ab_testing_preview.gif';
          break;
        default:
          break;
      }    
    return img_url;
  }

  render() {

    const { __ } = wp.i18n;
    const page = queryString.parse(window.location.search);
    return (
      <div>
      <div className="quads-ad-networks quad-ad-network-list" style={{float:"left"}}>
      <p className='ad_vendor'>{__('AD Vendors', 'quick-adsense-reloaded')}</p>
      <ul>
          {this.props.All_ad_network.map((item, index) =>
             <li title={item.ad_type} key={item.ad_type} ><Link to={`admin.php?page=quads-settings&path=wizard&ad_type=${item.ad_type}`} className="quads-nav-link" >{this.props.getImageByAdType(item.ad_type, index)}<span className="ad_type_name_">{item.ad_type_name}</span></Link></li>  )}
        </ul>
       {quads_localize_data.sellable_ads == 1 ?<p className='quads-ad-selling'>{__('Sellable Ads', 'quick-adsense-reloaded')}</p>:''}  
       {quads_localize_data.sellable_ads == 1 ?
       <ul>
       <li title="Ads Space" key="ads-space" ><Link to={`admin.php?page=quads-settings&path=wizard&ad_type=ads_space`} className="quads-nav-link" >{this.props.getImageByAdType('ads_space', 'ads_space')}<span className="ad_type_name_">Ads Space</span></Link></li>
     </ul>
     :''} 
      
      </div>

     


      <div className="quads-ad-networks quad-ad-network-list" style={{float:"left"}}>
      <p className='ad_format'>{__('AD Format', 'quick-adsense-reloaded')}</p>
        <ul>
          {this.props.All_ad_network_format.map((item, index) =>
            <li title={'Preview '+item.ad_type_name} className={!quads_localize_data.is_pro && (item.ad_type == 'group_insertion' || item.ad_type == 'skip_ads' || item.ad_type == 'ad_blindness' || item.ad_type == 'ab_testing'  || item.ad_type == 'rotator_ads' || item.ad_type == 'sticky_scroll' || item.ad_type == 'floating_cubes') ?'quads_ad_pro':''} key={item.ad_type} style={(item.ad_type == 'skip_ads' && !this.state.skippable_ads) || (item.ad_type == 'ad_blindness' && !this.state.blindness_settings) || (item.ad_type == 'ab_testing' && !this.state.ab_testing_settings) || (item.ad_type == 'rotator_ads' && !this.state.rotator_ads_status) ? ({ display: 'none' }) : {}}> {!item.pro || quads_localize_data.is_pro ?<Link to={`admin.php?page=quads-settings&path=wizard&ad_type=${item.ad_type}`} className="quads-nav-link w-prv">{this.props.getImageByAdType(item.ad_type, index)}<span className="ad_type_name_ part1">{item.ad_type_name}</span></Link> :<div onClick={() => this.changepopupState(item.ad_type_name)}>  {this.props.getImageByAdType(item.ad_type, index)}<span className="ad_type_name_ part2">{item.ad_type_name}</span> </div>} <a href={this.getImageUrlByAdType(item.ad_type, index)} className="material-icons quads-prv-img-wrpr" target="_blank"><span className="quads-prv-ad">remove_red_eye </span></a></li>)}
        </ul>
        {this.state.showGoProPopup ?
          <>

<div className="gopropopup quads-modal-popup">            
            <div className="quads-modal-popup-content">   
            <span className="quads-large-close" onClick={this.changepopupState}>&times;</span>

              <div className="quads-modal-popup-txt">      
              <div className="quads-modal-popup-heading"> {this.state.feature_name} {__('is a PRO Feature', 'quick-adsense-reloaded') }</div>    
              <p>{__("We're sorry, the "+this.state.feature_name+" is not available on your plan. Please upgrade to the PRO plan to unlock all these awesome features.", 'quick-adsense-reloaded')}</p>
              </div>           
             <div className="quads-modal-content">
              <a href={'https://wpquads.com/#buy-wpquads'} className={'quads-got_pro premium_features_btn'} >{__('Go PRO', 'quick-adsense-reloaded')}</a>
             </div>             
             </div>        
            </div>
 </> : null
        }
      </div>
      </div>

    );
  }
}

export default AdTypeSelectorNavLink;