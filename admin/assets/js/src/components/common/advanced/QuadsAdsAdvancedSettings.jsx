import React, { Component, Fragment } from 'react';

class QuadsAdsAdvancedSettings extends Component {
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
      <div className='exp_configuration'>{__('Advanced Configuration', 'quick-adsense-reloaded')}</div>
       <div className="quads-panel">
         <div className="quads-panel-body">
          <table>
            <tbody>
              <tr>
              <td><label className='q_exp_date' htmlFor="check_exp_date">{__('Set Expire Date', 'quick-adsense-reloaded')}</label></td>
              <td>
              <label className="quads-switch exp_date">
              <input className='exp_date_check' id="check_exp_date" checked={post_meta.check_exp_date} name="check_exp_date" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                  <span className="quads-slider"></span>
                </label>
              </td>
              </tr>
              { post_meta.check_exp_date && post_meta.check_exp_date == 1 ? <tr>
              <td><label className='q_exp_date' htmlFor="exp_date_from">{__('From ', 'quick-adsense-reloaded')}</label></td>
              <td><input className='exp_date_from' id="exp_date_from" name="exp_date_from" onChange={this.props.adFormChangeHandler} type="date" value={post_meta.exp_date_from}/></td>
              </tr>
              : ''
              }
              { post_meta.check_exp_date && post_meta.check_exp_date == 1 ? <tr>
              <td><label className='q_exp_date' htmlFor="check_exp_to">{__('To ', 'quick-adsense-reloaded')}</label></td>
              <td><input className='exp_date_to' id="exp_date_to" name="exp_date_to" onChange={this.props.adFormChangeHandler} type="date" value={post_meta.exp_date_to}/></td>
              </tr>
              : ''
              }
              <tr>
              <td><label className='q_spec_day' htmlFor="check_spec_day">{__('Set Specific Day', 'quick-adsense-reloaded')}</label></td>
              <td>
              <label className="quads-switch spec_day">
              <input className='spec_day_check' id="check_spec_day" checked={post_meta.check_spec_day} name="check_spec_day" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                  <span className="quads-slider"></span>
                </label>
              </td>
              </tr>
              { post_meta.check_spec_day && post_meta.check_spec_day == 1 ? <tr>
              <td><label>{__('Days ', 'quick-adsense-reloaded')}</label></td>
              <td>
                <select value={post_meta.set_spec_day} onChange={this.props.adFormChangeHandler} name="set_spec_day" id="set_spec_day">
                  <option value="mon">{__('Monday', 'quick-adsense-reloaded')}</option>
                  <option value="tue">{__('Tuesday', 'quick-adsense-reloaded')}</option> 
                  <option value="wed">{__('Wednesday', 'quick-adsense-reloaded')}</option> 
                  <option value="thu">{__('Thursday', 'quick-adsense-reloaded')}</option> 
                  <option value="fri">{__('Friday', 'quick-adsense-reloaded')}</option> 
                  <option value="sat">{__('Saturday', 'quick-adsense-reloaded')}</option> 
                  <option value="sun">{__('Sunday', 'quick-adsense-reloaded')}</option> 
                </select>
              </td>
              </tr>
              : ''
              }
            </tbody>
          </table>
         </div>
       </div>
       </div>
     )     
  }
}

export default QuadsAdsAdvancedSettings;