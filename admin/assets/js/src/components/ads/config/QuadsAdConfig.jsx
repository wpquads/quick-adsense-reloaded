import React, { Component, Fragment } from 'react';

import './QuadsAdConfig.scss';
import QuadsAdConfigFields from  '../config-fields/QuadsAdConfigFields';
import QuadsAMPCompatibility from  '../../common/amp-compatibility/QuadsAMPCompatibility';
import QuadsAdsAdvancedSettings from  '../../common/advanced/QuadsAdsAdvancedSettings';
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
    }else if(typeof(page.action) != "undefined" && (this.props.parentState.ad_id != page.post)) {
      return <div className="quads-cover-spin"></div>;
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
              {quads_localize_data.is_amp_enable && this.props.parentState.ad_type != 'skip_ads'  ? 
              <div className="quads-settings-group">
              <QuadsAMPCompatibility 
              ad_type={page.ad_type} 
              parentState={this.props.parentState} 
              adFormChangeHandler={this.props.adFormChangeHandler} 
              />
              </div> 
              :''}   
              {quads_localize_data.is_pro ?
              <div className="quads-settings-group">
              <QuadsAdsAdvancedSettings 
              ad_type={page.ad_type} 
              parentState={this.props.parentState} 
              adFormChangeHandler={this.props.adFormChangeHandler} 
              updateSetdaysList   = {this.props.updateSetdaysList} 
              
              />
              </div>
              :''}
              {this.props.parentState.ad_type !="random_ads" && this.props.parentState.ad_type !="rotator_ads" && this.props.parentState.ad_type != 'background_ad' ?
              <div className="quads-settings-group">
              <QuadsLayout 
              ad_type={page.ad_type} 
              parentState={this.props.parentState} 
              adFormChangeHandler={this.props.adFormChangeHandler} 
              />
              </div>
              : ""}
              <div className="quads-btn-navigate">
              <div className="quads-next" onClick={this.props.moveNext}>
              {(this.props.parentState.show_form_error) ? <span className="quads_form_msg"><span className="material-icons">
                      error_outline</span>{__('Please fill all required fields', 'quick-adsense-reloaded')}</span> :''} &nbsp;
                      <a className="quads-btn quads-btn-primary">{__('Next', 'quick-adsense-reloaded')}</a></div>
              </div>
            </Fragment>
           );
    }
          
  }
}

export default QuadsAdConfig;