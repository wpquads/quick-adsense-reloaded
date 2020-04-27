import React, { Component, Fragment } from 'react';


import './QuadsLayout.scss';

class QuadsLayout extends Component {
  
  constructor(props) {
    super(props);    
    this.state = {               
    };       
  } 

  render() {
    const {__} = wp.i18n;  
    const post_meta = this.props.parentState.quads_post_meta;  

          return (
            <div>
              <div>{__('Layout', 'quick-adsense-reloaded')}</div>
             <div className="quads-panel">
               <div className="quads-panel-body">
                <table>
                  <tbody>
                    {quads_localize_data.is_pro ? 
                    <tr><td><label>{__('Ad Label', 'quick-adsense-reloaded')}</label></td> 
                    <td>
                    <select name="adlabel" value={post_meta.adlabel} onChange={this.props.adFormChangeHandler}>
                      <option value="none">{__('No Label', 'quick-adsense-reloaded')}</option>  
                      <option value="above">{__('Above Ads', 'quick-adsense-reloaded')}</option>  
                      <option value="below">{__('Below Ads', 'quick-adsense-reloaded')}</option>                        
                    </select>  
                    </td>
                    </tr> : ''
                    }                    
                    <tr><td><label>{__('Align', 'quick-adsense-reloaded')}</label></td>
                      <td>
                        <select name="align" value={post_meta.align} onChange={this.props.adFormChangeHandler}>
                          <option value="3">{__('Default', 'quick-adsense-reloaded')}</option>
                          <option value="0">{__('Left', 'quick-adsense-reloaded')}</option>
                          <option value="1">{__('Center', 'quick-adsense-reloaded')}</option>
                          <option value="2">{__('Right', 'quick-adsense-reloaded')}</option>                          
                        </select>
                      </td>
                      </tr>
                    <tr><td><label>{__('Margin', 'quick-adsense-reloaded')}</label></td><td> <input onChange={this.props.adFormChangeHandler} type="number" step="1" max="" min="" className="small-text" id="margin" name="margin" value={post_meta.margin}/></td></tr>
                  </tbody>
                </table>
               </div>              
            </div> 
            </div>                           
            );
  }
}

export default QuadsLayout;