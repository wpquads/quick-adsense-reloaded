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
      </div>


      <div className="quads-ad-networks quad-ad-network-list" style={{float:"left"}}>
      <p className='ad_format'>{__('AD Format', 'quick-adsense-reloaded')}</p>
        <ul>
          {this.props.All_ad_network_format.map((item, index) =>
            <li title={item.ad_type} className={!quads_localize_data.is_pro && (item.ad_type == 'group_insertion' || item.ad_type == 'skip_ads' || item.ad_type == 'ad_blindness' || item.ad_type == 'ab_testing'  || item.ad_type == 'rotator_ads' || item.ad_type == 'sticky_scroll' || item.ad_type == 'floating_cubes') ?'quads_ad_pro':''} key={item.ad_type} style={(item.ad_type == 'skip_ads' && !this.state.skippable_ads) || (item.ad_type == 'ad_blindness' && !this.state.blindness_settings) || (item.ad_type == 'ab_testing' && !this.state.ab_testing_settings) || (item.ad_type == 'rotator_ads' && !this.state.rotator_ads_status) ? ({ display: 'none' }) : {}}> {!item.pro || quads_localize_data.is_pro ?<Link to={`admin.php?page=quads-settings&path=wizard&ad_type=${item.ad_type}`} className="quads-nav-link">{this.props.getImageByAdType(item.ad_type, index)}<span className="ad_type_name_ part1">{item.ad_type_name}</span></Link> :<div onClick={() => this.changepopupState(item.ad_type_name)}>  {this.props.getImageByAdType(item.ad_type, index)}<span className="ad_type_name_ part2">{item.ad_type_name}</span> </div>}</li>)}
        </ul>
        {this.state.showGoProPopup ?
          <>

<div className="gopropopup quads-modal-popup">            
            <div className="quads-modal-popup-content">   
            <span className="quads-large-close" onClick={this.changepopupState}>&times;</span>

              <div className="quads-modal-popup-txt">      
              <div className="quads-modal-popup-heading"> {this.state.feature_name} is a PRO Feature</div>    
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