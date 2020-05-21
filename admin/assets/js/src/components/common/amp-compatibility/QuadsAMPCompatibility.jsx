import React, { Component, Fragment } from 'react';

import './QuadsAMPCompatibility.scss';

class QuadsAMPCompatibility extends Component {
  constructor(props) {
    super(props);    
    this.state = {               
    };       
  } 
  render() {     
    const {__} = wp.i18n;
    const {quads_post_meta} = this.props.parentState;   
     return (
       <div>
      <div className='amp_configuration'><img height="20" width="20" src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/amp_logo.png'}/> {__('AMP Configuration', 'quick-adsense-reloaded')}</div>
       <div className="quads-panel">
         <div className="quads-panel-body">
          <table>
            <tbody>
              <tr>
                <td><label htmlFor="enabled_on_amp">{__('AMP Compatibility', 'quick-adsense-reloaded')}</label></td>
                <td>
                <input id="enabled_on_amp" checked={quads_post_meta.enabled_on_amp} name="enabled_on_amp" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                </td>                
                </tr>
                {quads_post_meta.enabled_on_amp && quads_post_meta.ad_type=='yandex' ?
                <tr><td><label>{__('Size', 'quick-adsense-reloaded')}</label></td><td>
                      <div>                      
                      <div className="quads-adsense-width-heigth">
                        
                        <div className="quads-adsense-width">
                          <label>{__('Width', 'quick-adsense-reloaded')}
                          <input value={quads_post_meta.g_data_ad_width ? quads_post_meta.g_data_ad_width:'300'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_width" name="g_data_ad_width" /> 
                          </label>
                        </div>
                        <div className="quads-adsense-height">
                          <label>{__('Height', 'quick-adsense-reloaded')}
                          <input value={quads_post_meta.g_data_ad_height  ? quads_post_meta.g_data_ad_height:'250'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_height" name="g_data_ad_height" />  
                          </label>
                        </div>
                      </div>

                      </div>
                      </td></tr>
                      : null }
            </tbody>
          </table>
         </div>
       </div>
       </div>
     )     
  }
}

export default QuadsAMPCompatibility;