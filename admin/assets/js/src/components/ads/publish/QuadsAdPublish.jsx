import React, { Component, Fragment } from 'react';
import { Link } from 'react-router-dom';
import './QuadsAdPublish.scss';
import queryString from 'query-string'

class QuadsAdPublish extends Component {
  constructor(props) {
    super(props);    
    this.state = {   
    title:"Live",            
    };      
    var page = queryString.parse(window.location.search);  
          this.getAdDataById(page['post']);  
  } 
 
     getAdDataById =  (ad_id) => {

      let url = quads_localize_data.rest_url+'quads-route/get-ad-by-id?ad-id='+ad_id;     
      if(quads_localize_data.rest_url.includes('?')){
         url = quads_localize_data.rest_url+'quads-route/get-ad-by-id&ad-id='+ad_id;    
      } 
      fetch(url,{
        headers: {                    
          'X-WP-Nonce': quads_localize_data.nonce,
        }
      }
      )
      .then(res => res.json())
      .then(
        (result) => {  
        if(result['post']['post_status'] =='draft')
          this.setState({title: 'Draft'});    
        },        
        (error) => {
          
        }
      );  

    }

  render() {

          const {__} = wp.i18n; 
          return (
            <div className="quads-settings-group">            
            <div className="quads-panel">
            <div className="quads-panel-body quads-live-wrapper">
              <div className="quads_live"><p>{__('Your ad is now '+this.state.title, 'quick-adsense-reloaded')}</p></div>
              <div className="live-one"><img src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/billboardicon.png'} /></div>
              <div className="live-two">
                <div className="live-two-left">
                  <a href="https://wordpress.org/support/plugin/quick-adsense-reloaded/reviews/" target="_blank">
                  <img src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/review.png'} />
                  </a>
                </div>
                <div className="live-two-right">
                  <a href="https://wordpress.org/support/plugin/quick-adsense-reloaded/reviews/" target="_blank">
                    <span>Share Your</span> Precious Feedback!
                  </a>
                </div>
                <div className="clear"></div>
              </div>
              <div className="live-three">
                <a onClick={this.props.movePrev} className="quads-btn live-three-one quads-btn-primary">{__('Prev', 'quick-adsense-reloaded')}</a>
                <Link to={`${this.props.location.pathname}?page=quads-settings`} className="quads-btn live-three-two quads-btn-primary">
                <span className="material-icons live-three-two-dashboard">speed</span>
                  {__('Return to Dashboard', 'quick-adsense-reloaded')}</Link>
              </div>
            </div>
            </div>
            </div>
            );
      }
}

export default QuadsAdPublish;