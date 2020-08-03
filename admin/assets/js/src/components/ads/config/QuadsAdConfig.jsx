import React, { Component, Fragment } from 'react';

import './QuadsAdConfig.scss';
import QuadsAdConfigFields from  '../config-fields/QuadsAdConfigFields';
import QuadsAMPCompatibility from  '../../common/amp-compatibility/QuadsAMPCompatibility';
import queryString from 'query-string';
import QuadsPageNotFound from  '../../common/404/QuadsPageNotFound'
import QuadsLayout from  '../../common/layout/QuadsLayout'

class QuadsAdConfig extends Component {
 
  
  constructor(props) {
    super(props);    
    this.state = {               
    };       
  } 

  render() {

    const {__} = wp.i18n; 
    const page = queryString.parse(window.location.search);   

    if(typeof(page.ad_type) === 'undefined'){
      return ( 
           <QuadsPageNotFound />
       );   
    }else{
           return ( 
             <Fragment>
              <div className="quads-settings-group">
              
              <QuadsAdConfigFields 
              ad_type={page.ad_type} 
              {...this.props} 
              parentState={this.props.parentState} 
              adFormChangeHandler={this.props.adFormChangeHandler}
              modalValue={this.props.modalValue}
              getAdsenseCode={this.props.getAdsenseCode} 
              openModal     = {this.props.openModal} 
              closeModal    = {this.props.closeModal}
              />
              </div> 
              {quads_localize_data.is_amp_enable ? 
              <div className="quads-settings-group">
              <QuadsAMPCompatibility 
              ad_type={page.ad_type} 
              parentState={this.props.parentState} 
              adFormChangeHandler={this.props.adFormChangeHandler} 
              />
              </div> 
              :''}   
              {this.props.parentState.ad_type !="random_ads" && this.props.parentState.ad_type != 'background_ad' ?
              <div className="quads-settings-group">
              <QuadsLayout 
              ad_type={page.ad_type} 
              parentState={this.props.parentState} 
              adFormChangeHandler={this.props.adFormChangeHandler} 
              />
              </div>
              : ""}
              <div className="quads-btn-navigate">
              <div className="quads-next" onClick={this.props.moveNext}><a className="quads-btn quads-btn-primary">{__('Next', 'quick-adsense-reloaded')}</a></div>
              </div>
            </Fragment>
           );
    }
          
  }
}

export default QuadsAdConfig;