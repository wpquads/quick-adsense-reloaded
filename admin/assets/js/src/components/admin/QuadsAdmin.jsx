import React, { Component, Fragment } from 'react';
import queryString from 'query-string'
import QuadsAdListSettings from './../settings/QuadsAdListSettings'
import QuadsAdListBody from './../ads/body/QuadsAdListBody'
import QuadsAdListNavLink from './../ads/nav/QuadsAdListNavLink'

class QuadsAdmin extends Component {

    constructor(props) {      
        super(props);
        this.state = {                            
                switchToOld: false,
                ad_type_toggle: false            
            };     
      }
      nodatashowAddTypeSelector = (e) => {
        e.preventDefault();
        this.setState({ad_type_toggle:true});    
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
        let pagePath = 'ads'
        if(typeof(page.path)  != 'undefined' ){
            pagePath = page.path;
        }
            return (                                        
                    <div className="quads-ad-list-wrapper">             
                        <div className="quads-ad-header">
                          <div className="quads-logo"><img height="42" width="175" src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/quads-v2-logo.png'} /></div>
                          { !quads_localize_data.is_pro ? 
                           <a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">Go PRO</a>
                          : ''}   
                         <div>                      
                         
                          </div>
                          <div className="quads-ad-menu">
                              <QuadsAdListNavLink
                              ad_type_toggle ={this.state.ad_type_toggle}
                               />                   
                          </div>
                          
                        </div>                
                        <div className="quads-segment">                
                        {(() => {
                            if(pagePath.includes('settings')){
                                return <QuadsAdListSettings/>;                                
                            }
                            if(pagePath.includes('ads')){
                                return <QuadsAdListBody
                                 nodatashowAddTypeSelector ={this.nodatashowAddTypeSelector}
                                 />;                                
                            }                                
                            })()}
                    </div>
                    </div>                                                                
            );
    }
}

export default QuadsAdmin;