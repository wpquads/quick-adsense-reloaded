import React, { Component, Fragment } from 'react';
import Select from 'react-select';

class QuadsAdsAdvancedSettings extends Component {
  constructor(props) {
    super(props);    
    this.state = {
      selectedValue : this.props.parentState.quads_post_meta.set_spec_day         
    };
       
    this.multiSelectChangeHandler = this.multiSelectChangeHandler.bind(this) 
  }
  multiSelectChangeHandler(e){
    this.setState({selectedValue:Array.isArray(e) ? e.map(x => x.value) : []});  
  } 

  componentDidUpdate (){
    
    const updateSetdaysArr = this.state.selectedValue; 
    console.log(updateSetdaysArr);
    if(updateSetdaysArr && updateSetdaysArr.length > 0 ){
     this.props.updateSetdaysList(updateSetdaysArr);
    }
    
  }
  render() {     
    const {__} = wp.i18n;
    const post_meta = this.props.parentState.quads_post_meta;
    const current_date_obj = new Date();
    const current_date = `${current_date_obj.getFullYear()}-${String(current_date_obj.getMonth()+1).padStart(2,"0")}-${String(current_date_obj.getDate()).padStart(2,"0")}`;
    const days_options = [
      { value: 'mon', label: __('Monday', 'quick-adsense-reloaded') },
      { value: 'tue', label: __('Tuesday', 'quick-adsense-reloaded')  },
      { value: 'wed', label: __('Webnesday', 'quick-adsense-reloaded')  },
      { value: 'thu', label: __('Thursday', 'quick-adsense-reloaded')  },
      { value: 'fri', label: __('Friday', 'quick-adsense-reloaded')  },
      { value: 'sat', label: __('Saturday', 'quick-adsense-reloaded')  },
      { value: 'sun', label: __('Sunday', 'quick-adsense-reloaded')  }
    ]
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
              <td><input className='exp_date_from' id="exp_date_from" name="exp_date_from" min={current_date} onChange={this.props.adFormChangeHandler} type="date" value={post_meta.exp_date_from}/></td>
              </tr>
              : ''
              }
              { post_meta.check_exp_date && post_meta.check_exp_date == 1 ? <tr>
              <td><label className='q_exp_date' htmlFor="check_exp_to">{__('To ', 'quick-adsense-reloaded')}</label></td>
              <td><input className='exp_date_to' id="exp_date_to" name="exp_date_to" onChange={this.props.adFormChangeHandler} min={post_meta.exp_date_from} type="date" value={post_meta.exp_date_to}/></td>
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
              <td style={{width:'300px'}}>
                <Select
                  value={days_options.filter(obj => this.state.selectedValue.includes(obj.value))}
                  isMulti
                  onChange={this.multiSelectChangeHandler}
                  name="set_spec_day_select"
                  options={days_options}
                  className="basic-multi-select"
                  classNamePrefix="select"
                />
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