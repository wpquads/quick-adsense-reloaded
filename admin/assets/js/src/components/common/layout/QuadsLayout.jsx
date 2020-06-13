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
                      <tr>
                        <td><label>{__('Margin', 'quick-adsense-reloaded')}</label></td><td> <input onChange={this.props.adFormChangeHandler} type="number" step="1" max="" min="" className="small-text" id="margin" name="margin" value={post_meta.margin}/></td>
                      </tr>
                      <tr>
                        <td><label htmlFor="ad_label_check">{__('Ad label', 'quick-adsense-reloaded')}</label></td>
                        <td>
                        <input id="ad_label_check" checked={post_meta.ad_label_check} name="ad_label_check" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                        </td>                
                      </tr>
                      {post_meta.ad_label_check ?
                      <tr>
                        <td><label>{__('Ad Label Text', 'quick-adsense-reloaded')}</label></td> 
                        <td>
                        <input onChange={this.props.adFormChangeHandler} type="text" className="small-text" id="ad_label_text" name="ad_label_text" value={post_meta.ad_label_text}/>  
                        </td>
                      </tr>
                      : null }
                      {post_meta.ad_label_check ?
                      <tr>
                        <td><label>{__('Ad Label Position', 'quick-adsense-reloaded')}</label></td> 
                        <td>
                        <select name="adlabel" value={post_meta.adlabel} onChange={this.props.adFormChangeHandler}>
                        <option value="above">{__('Above Ad', 'quick-adsense-reloaded')}</option>  
                        <option value="below">{__('Below Ad', 'quick-adsense-reloaded')}</option>                        
                        </select>  
                        </td>
                      </tr> 
                        : null }
                       
                  </tbody>
                </table>
               </div>              
            </div> 
            </div>                           
            );
  }
}

export default QuadsLayout;