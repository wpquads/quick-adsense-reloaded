import React, { Component, Fragment } from 'react';
import Select from 'react-select';

class QuadsAdsAdvancedSettings extends Component {
  constructor(props) {
    super(props);    
    this.state = {
      selectedValue : this.props.parentState.quads_post_meta.set_spec_day,
      delay_ad_sec :false,         
    };
       
    this.multiSelectChangeHandler = this.multiSelectChangeHandler.bind(this) 
    this.getGlobalLasyStatus()
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
  getGlobalLasyStatus(){
    let url = quads_localize_data.rest_url + 'quads-route/get-settings';
    fetch(url,{
        headers: {
            'X-WP-Nonce': quads_localize_data.nonce,
        }
    })
        .then(res => res.json())
        .then(
            (result) => {
                Object.entries(result).map(([meta_key, meta_val]) => {
                    if(meta_key=='delay_ad_sec'){
                        this.setState({delay_ad_sec:meta_val});
                    }
                })
            }
        );
  }
  render() {     
    const {__} = wp.i18n;
    const post_meta = this.props.parentState.quads_post_meta;
    console.log(this.props.parentState);
    const current_date_obj = new Date();
    const current_date = `${current_date_obj.getFullYear()}-${String(current_date_obj.getMonth()+1).padStart(2,"0")}-${String(current_date_obj.getDate()).padStart(2,"0")}`;
    const next_schedule_date = `${current_date_obj.getFullYear()}-${String(current_date_obj.getMonth()+1).padStart(2,"0")}-${String(current_date_obj.getDate()+1).padStart(2,"0")}`;
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
              { post_meta.ad_type != 'ads_space' ? 
            <tr>
              <td><label className='q_exp_date' htmlFor="publish_date">{__('Schedule Ad', 'quick-adsense-reloaded')}</label></td>
              <td><input className='publish_date' id="publish_date" name="publish_date" min={next_schedule_date} onChange={this.props.adFormChangeHandler} type="date" value={post_meta.publish_date}/></td>
              </tr> :''}
              { post_meta.ad_type != 'ads_space' ? <tr>
              <td><label className='q_exp_date' htmlFor="check_exp_date">{__('Set Expire Date', 'quick-adsense-reloaded')}</label></td>
              <td>
              <label className="quads-switch exp_date">
              <input className='exp_date_check' id="check_exp_date" checked={post_meta.check_exp_date} name="check_exp_date" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                  <span className="quads-slider"></span>
                </label>
              </td>
              </tr> :''}
              { ( post_meta.check_exp_date && post_meta.check_exp_date == 1) ? <tr>
              <td><label className='q_exp_date' htmlFor="exp_date_from"> {__('From ', 'quick-adsense-reloaded')}</label></td>
              <td><input className='exp_date_from' id="exp_date_from" name="exp_date_from" min={current_date} onChange={this.props.adFormChangeHandler} type="date" value={post_meta.exp_date_from}/>
              </td>
              </tr>
              : ''
              }
              { ( post_meta.check_exp_date && post_meta.check_exp_date == 1) ? <tr>
              <td><label className='q_exp_date' htmlFor="check_exp_to">{__('To ', 'quick-adsense-reloaded')}</label></td>
              <td><input className='exp_date_to' id="exp_date_to" name="exp_date_to" onChange={this.props.adFormChangeHandler} min={post_meta.exp_date_from} type="date" value={post_meta.exp_date_to}/></td>
              </tr>
              : ''
              }
              { post_meta.ad_type != 'ads_space' ? <tr>
              <td><label className='q_spec_day' htmlFor="check_spec_day">{__('Set Specific Day', 'quick-adsense-reloaded')}</label></td>
              <td>
              <label className="quads-switch spec_day">
              <input className='spec_day_check' id="check_spec_day" checked={post_meta.check_spec_day} name="check_spec_day" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                  <span className="quads-slider"></span>
                </label>
              </td>
              </tr> :''}
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
              {(post_meta.ad_type!='adsense' && post_meta.ad_type!='double_click')?
               <tr>
              <td><label className='q_lazy_load' htmlFor="check_lazy_load">{__('Lazy Load', 'quick-adsense-reloaded')}</label></td>
              <td>
              <label className="quads-switch lazy_load">
              
              <input className='lazy_load_check' id="check_lazy_load" checked={post_meta.check_lazy_load} name="check_lazy_load" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                  <span className="quads-slider"></span>
                </label>
              </td>
              </tr>:''}
              { (post_meta.check_lazy_load) && (post_meta.ad_type!='adsense' && post_meta.ad_type!='double_click') ? <tr>
              <td><label>{__('Lazy Load Delay ', 'quick-adsense-reloaded')}</label></td>
              <td>
              <input onChange={this.props.adFormChangeHandler} type="number" step="1" max="" min="1" className="small-text" id="check_lazy_load_delay" name="check_lazy_load_delay" placeholder="" value={post_meta.check_lazy_load_delay}/> <small>{__('seconds','quick-adsense-reloaded')}</small>
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