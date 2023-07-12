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
    
    // quads_occasional_ads_method() {
    //   function quads_set_admin_occasional_ads_pop_up_cookie(){
    //     var o = new Date;
    //     o.setFullYear(o.getFullYear() + 1), document.cookie = "quads_hide_admin_occasional_ads_pop_up_cookie_feedback=1; expires=" + o.toUTCString() + "; path=/"
    //     }
        
    //     function quads_delete_admin_occasional_ads_pop_up_cookie() {
    //         document.cookie = "quads_hide_admin_occasional_ads_pop_up_cookie_feedback=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;"
    //     }
        
    //     function quads_get_admin_occasional_ads_pop_up_cookie() {
    //         for (var o = "quads_hide_admin_occasional_ads_pop_up_cookie_feedback=", a = decodeURIComponent(document.cookie).split(";"), e = 0; e < a.length; e++) {
    //             for (var c = a[e];
    //                 " " == c.charAt(0);) c = c.substring(1);
    //             if (0 == c.indexOf(o)) return c.substring(o.length, c.length)
    //         }
    //         return ""
    //     }
    //   jQuery(function(o) {
    //       var a = quads_get_admin_occasional_ads_pop_up_cookie();
    //       void 0 !== a && "" !== a && o("details#quads-ocassional-pop-up-container").attr("open", !1), o("details#quads-ocassional-pop-up-container span.quads-promotion-close-btn").click(function(a) {
    //           o("details#quads-ocassional-pop-up-container summary").click()
    //       }), o("details#quads-ocassional-pop-up-container summary").click(function(a) {
    //           var e = o(this).parents("details#quads-ocassional-pop-up-container"),
    //               c = o(e).attr("open");
    //           void 0 !== c && !1 !== c ? quads_set_admin_occasional_ads_pop_up_cookie() : quads_delete_admin_occasional_ads_pop_up_cookie()
    //       })
    //   });
    // }

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
                          <div className="quads-logo"><img height="42" width="175" src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/quads-v2-logo.png'} /></div>
                          { !quads_localize_data.is_pro ? 
                            <a className="quads-got_pro premium_features_btn" href={quads_localize_data.get_admin_url+'?page=quads-addons#upgrade_to_premium'} >Upgrade to Premium</a>
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
                        {/*<p>After installing <a href="https://wpquads.com/" >WP Quads Pro</a>, you need to activate your license. Please add the License key.</p>*/}
                        <p>Thank you for installing <a href="https://wpquads.com/" >WP QUADS PRO</a>, please <Link to={`admin.php?page=quads-settings&path=settings_licenses`} >ACTIVATE</Link> the license key to receive regular updates.</p>
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
                        <p>Your WP QUADS PRO license is about to expire in <span className="q-r-m">{quads_localize_data.licenses.price_id} days</span>.</p>
                        </div>
                        <div className="quads-renew-cta-container">
                        <a href="https://wpquads.com/your-account/" className="quads-renew-cta" target="_blank" rel="noopener noreferrer">Renew now</a>
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
                        <p>Your WP QUADS PRO license Key is <span className="q-r-m-e">Expired</span>.</p>
                        </div>
                        <div className="quads-renew-cta-container">
                        <a href="https://wpquads.com/your-account/" className="quads-renew-cta" target="_blank" rel="noopener noreferrer">Renew now</a>
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
                        {/* { !quads_localize_data.is_pro ? 
                         ( <details id="quads-ocassional-pop-up-container" open>
                            <summary className="quads-ocassional-pop-up-open-close-button">40% OFF - Limited Time Only <img height="25" width="25" src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/sale.png'} /></summary>
                            <span className="quads-promotion-close-btn">  &times;  </span>
                            <div className="quads-ocassional-pop-up-contents">
                        
                                <img src="https://cdn-icons-png.flaticon.com/512/2349/2349820.png" className="quads-promotion-surprise-icon" />
                                <p className="quads-ocassional-pop-up-headline">40% OFF on <span>WP QUADS PRO</span></p>
                                <p className="quads-ocassional-pop-up-second-headline">Upgrade the PRO version during this festive season and get our biggest discount of all time on New Purchases, Renewals &amp; Upgrades</p>
                                <a className="quads-ocassional-pop-up-offer-btn" href="https://wpquads.com/november-deal/" target="_blank">Get This Offer Now</a>
                                <p className="quads-ocassional-pop-up-last-line">Black Friday, Cyber Monday, Christmas &amp; New year are the only times we offer discounts this big.</p>
                            </div>
                        </details>)
                      : ''} */}
                    </div>                                                                
            );
    }
}

export default QuadsAdmin;
