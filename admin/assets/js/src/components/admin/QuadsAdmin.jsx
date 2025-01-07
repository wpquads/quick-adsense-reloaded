import React, { Component, Fragment } from 'react';
import queryString from 'query-string'
import QuadsAdListSettings from './../settings/QuadsAdListSettings'
import QuadsAdListBody from './../ads/body/QuadsAdListBody'
import QuadsAdListNavLink from './../ads/nav/QuadsAdListNavLink'
import QuadsAdReport from '../report/QuadsAdReport'
import QuadsAdReportabtesting from '../report/QuadsAdReportabtesting'
import QuadsAdLogging from '../report/QuadsAdLogging'
import Quads_single_report from '../report/Quads_single_report';
import { Link } from 'react-router-dom';
import AdSellRecords from './../settings/QuadsAdSellList';

class QuadsAdmin extends Component {

    constructor(props) {      
        super(props);
        this.state = {                            
                switchToOld: false,
                ad_type_toggle: false,
                settings  : []          
            };  
           // this.quads_occasional_ads_method();   
      }
      nodatashowAddTypeSelector = (e) => {
        e.preventDefault();
        this.setState({ad_type_toggle:true});    
    }
    setStateOfToggle = (toggle) => {
      this.setState({ad_type_toggle: toggle});
    }
    switchToOld = () => {

    const json_data = {
        mode  : 'old',            
    };

    const url = quads_localize_data.rest_url + 'quads-route/change-mode';

    fetch(url,{
      method: "post",
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-WP-Nonce': quads_localize_data.nonce,
      },        
      body: JSON.stringify(json_data)
    })
    .then(res => res.json())
    .then(
      (result) => { 
        if(result.status == 't'){          
            this.setState({switchToOld:true});
            window.location.href = this.props.location.pathname+'?page=quads-settings'; 
            exit;         
        }                 
      },        
      (error) => {
       
      }
    );         
    }  

  render() {
        const {__} = wp.i18n; 
        const page = queryString.parse(window.location.search); 
        let pagePath = 'ads';
        if(typeof(page.path)  != 'undefined' ){
            pagePath = page.path;
        }
            return (                                        
                    <div className="quads-ad-list-wrapper">             
                        <div className="quads-ad-header">
                          <div className="quads-logo"><Link to={'admin.php?page=quads-settings'} ><img height="42" width="175" src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/quads-v2-logo.png'} /></Link></div>
                          { !quads_localize_data.is_pro ? 
                            <a className="quads-got_pro premium_features_btn" href={quads_localize_data.get_admin_url+'?page=quads-addons#upgrade_to_premium'} >{__('Upgrade to Premium','quick-adsense-reloaded')}</a>
                          : ''}   
                         <div>                      
                         
                          </div>
                          <div className="quads-ad-menu">
                              <QuadsAdListNavLink
                              ad_type_toggle ={this.state.ad_type_toggle}
                              settings = {this.state.settings}
                              setStateOfToggle ={this.setStateOfToggle}
                               />                   
                          </div>
                          
                        </div>
                        
                        {(quads_localize_data && quads_localize_data.is_pro == 1 && quads_localize_data.licenses== ""  ) &&
                        <div className="quads-renew-message-main">
                        <div className="quads-renewal-banner">
                          <div className="quads-renew-message">
                        <p>{__('Thank you for installing','quick-adsense-reloaded')} <a href="https://wpquads.com/" >WP QUADS PRO</a>, please <Link to={`admin.php?page=quads-settings&path=settings_licenses`} >ACTIVATE</Link> the license key to receive regular updates.</p>
                        </div>
                        </div>
                        </div>
                        }
                        {(quads_localize_data && quads_localize_data.licenses!==undefined && quads_localize_data.licenses.price_id!==undefined && quads_localize_data.licenses !== ""  &&
                            quads_localize_data.licenses.price_id > 0 && 
                         quads_localize_data.licenses.price_id <= 30 ) &&
                        <div className="quads-renew-message-main">
                        { quads_localize_data.is_pro ? <div className="quads-renewal-banner">
                        <div className="quads-renew-message">
                        <p>{__('Your WP QUADS PRO license is about to expire in','quick-adsense-reloaded')}  <span className="q-r-m">{quads_localize_data.licenses.price_id} {__('days','quick-adsense-reloaded')}</span>.</p>
                        </div>
                        <div className="quads-renew-cta-container">
                        <a href="https://wpquads.com/your-account/" className="quads-renew-cta" target="_blank" rel="noopener noreferrer">{__('Renew now','quick-adsense-reloaded')}</a>
                        </div>
                        </div>
                          : '' }
                        </div>
                        }
                        {(quads_localize_data && quads_localize_data.licenses!==undefined && quads_localize_data.licenses.price_id!==undefined && quads_localize_data.licenses !== "" && quads_localize_data.licenses.license !== "valid"  &&
                            quads_localize_data.licenses.price_id <= 0 ) &&
                        <div className="quads-renew-message-main">
                        { quads_localize_data.is_pro ?
                            <div className="quads-renewal-banner">
                        <div className="quads-renew-message">
                        <p>{__('Your WP QUADS PRO license Key is','quick-adsense-reloaded')} <span className="q-r-m-e">{__('Expired','quick-adsense-reloaded')}</span>.</p>
                        </div>
                        <div className="quads-renew-cta-container">
                        <a href="https://wpquads.com/your-account/" className="quads-renew-cta" target="_blank" rel="noopener noreferrer">{__('Renew now','quick-adsense-reloaded')}</a>
                        </div>
                        </div>                        
                          : '' }
                        </div>
                        }
                        
                        
                        <div className="quads-segment">                
                          {(() => {
                            if(pagePath.includes('settings')){
                                return <QuadsAdListSettings/>;                                
                            }
                            if(pagePath.includes('adsell')){
                              return <AdSellRecords />;
                            }
                            if(pagePath.includes('ads')){
                                return <QuadsAdListBody
                                settings = {this.state.settings}
                                 nodatashowAddTypeSelector ={this.nodatashowAddTypeSelector}
                                 setStateOfToggle ={this.setStateOfToggle}
                                 />;                                
                            }
                            if(pagePath.includes('reports')){
                                return <QuadsAdReport      />;
                                return <QuadsAdReportabtesting/>;
                            }
                            if(pagePath.includes('view_report')){
                              return <Quads_single_report/>
                            }
                            if(pagePath.includes('ad_logging')){
                              return <QuadsAdLogging      />;
                            }
                            
                            
                          })()}
                        </div>
                    </div>                                                                
            );
    }
}

export default QuadsAdmin;
