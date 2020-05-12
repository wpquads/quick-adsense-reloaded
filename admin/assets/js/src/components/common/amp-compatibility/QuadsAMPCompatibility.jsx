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
      <div className='amp_configuration'><img height="20" width="20" src="http://localhost/wordpress/wp-content/plugins/quick-adsense-reloaded/admin/assets/js/src/images/amp_logo.png"/> {__('AMP Configuration', 'quick-adsense-reloaded')}</div>
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
            </tbody>
          </table>
         </div>
       </div>
       </div>
     )     
  }
}

export default QuadsAMPCompatibility;