import React, { Component, Fragment } from 'react';
import './QuadsAdConfigFields.scss';
import QuadsAdModal from '../../common/modal/QuadsAdModal';

class QuadsAdConfigFields extends Component {
  constructor(props) {
    super(props);    
    this.state = {                     
    };       
  }   
  render() {     

          const {__} = wp.i18n;
          const post_meta = this.props.parentState.quads_post_meta;
          const show_form_error = this.props.parentState.show_form_error;
          const comp_html = [];        

          switch (this.props.ad_type) {

            case 'adsense':

              comp_html.push(<div key="adsense">
                <table>
                  <tbody>
                    <tr><td><label>{__('Data Client ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.g_data_ad_client == '') ? 'quads_form_error' : ''} value={post_meta.g_data_ad_client} onChange={this.props.adFormChangeHandler} type="text" id="g_data_ad_client" name="g_data_ad_client" />
                    {(show_form_error && post_meta.g_data_ad_client == '') ? <div className="quads_form_msg"><span class="material-icons">
error_outline</span>Enter Data Client ID</div> :''} </td></tr>
                    <tr><td><label>{__('Data Slot ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.g_data_ad_slot == '') ? 'quads_form_error' : ''}  value={post_meta.g_data_ad_slot} onChange={this.props.adFormChangeHandler} type="text" id="g_data_ad_slot" name="g_data_ad_slot" />
                    {(show_form_error && post_meta.g_data_ad_slot == '') ? <div className="quads_form_msg"><span class="material-icons">
error_outline
</span>Enter Data Slot ID</div> :''}</td></tr>
                    <tr><td><label>{__('Size', 'quick-adsense-reloaded')}</label></td><td>
                      <div>
                        <select value={post_meta.adsense_type} onChange={this.props.adFormChangeHandler} name="adsense_type" id="adsense_type">
                        <option value="normal">{__('Fixed Size', 'quick-adsense-reloaded')}</option>
                        <option value="responsive">{__('Responsive', 'quick-adsense-reloaded')}</option> 
                      </select>
                      {
                        post_meta.adsense_type !== 'responsive' ?                        
                      <div className="quads-adsense-width-heigth">
                        
                        <div className="quads-adsense-width">
                          <label>{__('Width', 'quick-adsense-reloaded')}
                          <input value={post_meta.g_data_ad_width ? post_meta.g_data_ad_width:'300'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_width" name="g_data_ad_width" /> 
                          </label>
                        </div>
                        <div className="quads-adsense-height">
                          <label>{__('Height', 'quick-adsense-reloaded')}
                          <input value={post_meta.g_data_ad_height  ? post_meta.g_data_ad_height:'250'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_height" name="g_data_ad_height" />  
                          </label>
                        </div>
                      </div>
                      : ''
                      }
                      </div>
                      </td></tr>
                       <tr><td><label>{__('Lazy Loading', 'quick-adsense-reloaded')}</label></td><td>
                        <div>
                          <select value={post_meta.lazy_load_ads} onChange={this.props.adFormChangeHandler} name="lazy_load_ads" id="lazy_load_ads">
                          <option value="inherit">{__('Inherit from settings', 'quick-adsense-reloaded')}</option>
                          <option value="yes">{__('Yes, Enable for this ad', 'quick-adsense-reloaded')}</option>
                          <option value="no">{__("No, Disable for this ad", 'quick-adsense-reloaded')}</option> 
                        </select>
                        </div>
                      </td></tr> 
                  </tbody>
                </table>
                </div>);

              break;
          
              case 'plain_text':                
                
                comp_html.push(<div key="plain_text">
                  <table><tbody>
                  <tr>
                  <td><label>{__('Plain Text / HTML / JS', 'quick-adsense-reloaded')}</label></td> 
                  <td><textarea className={(show_form_error && post_meta.code == '') ? 'quads_form_error' : ''}  cols="50" rows="5" value={post_meta.code} onChange={this.props.adFormChangeHandler} id="code" name="code" />
                  {(show_form_error && post_meta.code == '') ? <div class="quads_form_msg"><span class="material-icons">error_outline</span>Enter Plain Text / HTML / JS</div> : ''}</td>
                  </tr>
                  </tbody></table>
                  </div>);      
              break;  

            default:
              comp_html.push(<div>{__('Ad not found', 'quick-adsense-reloaded')}</div>);
              break;
          }
              return(
                <div>{this.props.ad_type} {__('Ad Configuration', 'quick-adsense-reloaded')}
                {this.props.ad_type == 'adsense' ? 
                <div className="quads-autofill-div"><a className="quads-autofill" onClick={this.props.openModal}>{__('Autofill', 'quick-adsense-reloaded')}</a>
                <QuadsAdModal 
                 closeModal    = {this.props.closeModal}
                 parentState={this.props.parentState} 
                 title={__('Enter AdSense text and display ad code here', 'quick-adsense-reloaded')}
                 description={__('Do not enter AdSense page level ads or Auto ads! Learn how to create AdSense ad code', 'quick-adsense-reloaded')}  
                  content={
                    <div>
                      <div><textarea className="quads-auto-fill-textarea" cols="80" rows="15" onChange={this.props.modalValue} value={this.props.quads_modal_value}/></div>
                      <div><a className="button" onClick={this.props.getAdsenseCode}>{__('Get Code', 'quick-adsense-reloaded')}</a></div>
                    </div>
                  }/>
                </div> : ''}
                <div className="quads-panel">
                 <div className="quads-panel-body">{comp_html}</div>
              </div>
              </div>
              );
  }
}

export default QuadsAdConfigFields;