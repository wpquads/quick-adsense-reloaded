import React, { Component, Fragment } from 'react';
import './QuadsAdListCreate.scss';
import { Redirect } from 'react-router-dom'

import { BrowserRouter as Router, Switch, Route, Link } from 'react-router-dom';
import queryString from 'query-string'

class QuadsAdListCreate extends Component {
    
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
                {ad_type:'plain_text',ad_type_name:'Plain Text / HTML / JS'},               
                ]          
   };
   this.QuadsRedirectToWizard = this.QuadsRedirectToWizard.bind(this);
  } 
  QuadsRedirectToWizard(e){ 

      this.setState({
        redirect: true
      })

    const ad_type = e.currentTarget.dataset.adtype;    

    const location = this.props.location;
    const pathname = location.pathname;

    let url = `${pathname}?page=quads-settings&path=wizard&ad_type=${ad_type}`;    
    //this.props.history.push(url);
    window.location.href = url;     
    
  }

  ad_modal_hide = () => {
    this.props.history.goBack();
  }

  getImageByAdType = (type) =>{
    let type_img = [];
    let img_url  = '';

      switch (type) {
        case 'adsense':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/adsense_logo.png';
          break;

        case 'plain_text':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/plain_text_logo.png';
          break;
      
        default:
          break;
      }

      type_img.push(<img height="80" width="80" src={img_url} />);
      
    return type_img;
  }

  render() {
          const {__} = wp.i18n; 
          return (                                                   
                   <div className="quads-ad-networks">
                     <div className="quads-close-ad-modal material-icons" onClick={this.ad_modal_hide}><a>{__('close', 'quick-adsense-reloaded')}</a></div>
                    <div className="quads-popular-network">
                    <h3>{__('Popular Integration', 'quick-adsense-reloaded')}</h3>
                        <div>
                        <ul>
                    {this.state.popular_ad_network.map(item => (
                    <li data-adtype={item.ad_type} onClick={this.QuadsRedirectToWizard} key={item.ad_type}><a className="quads-nav-link">{this.getImageByAdType(item.ad_type)}<div><strong>{__(item.ad_type_name,'quick-adsense-reloaded')}</strong></div></a></li>  
                    ))}
                    </ul>
                        </div>
                    </div>
                    <div className="quads-all-network">
                    <h3>{__('AD Integrations', 'quick-adsense-reloaded')}</h3>
                    <ul>
                    {this.state.All_ad_network.map(item => (
                        <li data-adtype={item.ad_type} onClick={this.QuadsRedirectToWizard} key={item.ad_type}><a className="quads-nav-link">{this.getImageByAdType(item.ad_type)}<div><strong>{__(item.ad_type_name,'quick-adsense-reloaded')}</strong></div></a></li>  
                    ))}
                    </ul>
                    </div>
                    </div>
                        
            );
      }
}

export default QuadsAdListCreate;