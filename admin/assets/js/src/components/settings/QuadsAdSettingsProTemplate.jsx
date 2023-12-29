import React, { Component, Fragment } from 'react';
import { Alert } from '@material-ui/lab';
import Icon from '@material-ui/core/Icon';
class QuadsAdSettingsProTemplate extends Component {
  constructor(props) {

    super(props);
    this.state = {

    };
  }

  render() {

    const { __ } = wp.i18n;
    const data = this.props.item;
    const quads_pro_list_selected = this.props.quads_pro_list_selected;
    
    let settings= this.props.settings;

    var load_symbol = ''
    if (quads_localize_data.is_pro) {
      if(data.id == "skippable_ads"){
        load_symbol = 'lazy_loader_s'
      }
      if(data.id == "blindness_settings"){
          load_symbol = 'lazy_loader_bl'
        }
        if(data.id == "ab_testing_settings"){
          load_symbol = 'lazy_loader_ab'
        }
        if(data.id == "optimize_core_vitals"){
          load_symbol = 'lazy_loader_o'
        }
        if(data.id == "hide_quads_markup"){
          load_symbol = 'lazy_loader_h'
        }
        if(data.id == "global_excluder"){
          load_symbol = 'lazy_loader_ge'
          return ('')
        }
        if(data.id == "ad_log"){
          load_symbol = 'lazy_loader_al'
        }
        if(data.id == "delay_ad_sec"){
          load_symbol = 'lazy_loader_das'
        }
        if(data.id == "reports_settings"){
          load_symbol = 'lazy_loader_rs'
        }

      return (<tr><th><label htmlFor={data.id}>{data.title} </label></th>
          <td>          
          {this.props.selectedBtnOpt == data.id ?
            <div className="quads-spin-cntr">
              <div className="quads-set-spin"></div>
            </div> :
            <label className="quads-switch">
              <input type="checkbox" name={data.id} id={data.id} onChange={this.props.formChangeHandler} checked={settings[data.id]} value={data.id} />
              <span id={data.id+'_'} className="quads-slider"></span>
              <div className={load_symbol}></div>
            </label>
          }  
           {data.url?
           <a className="quads-general-helper quads-general-helper-new" target="_blank" href={data.url}></a>:''} 
          </td>
        </tr>
      );
    } else {
      return (<tr><th><label htmlFor={data.id}>{data.title} <span className={'getprocheckbox'}><a className={'quads_pro_link'} target="blank" href="https://wpquads.com/#buy-wpquads">PRO</a></span></label></th>
        <td>
          <label className="quads-switch">
            <input type="checkbox" name={data.id} onChange={this.props.display_pro_alert_fun} value={data.id} />
            <span className="quads-slider"></span>
          </label>
          <a className="quads-general-helper quads-general-helper-new" target="_blank" href={data.url}></a>
          {quads_pro_list_selected.includes(data.id) ? <Alert severity="error" action={<Icon onClick={this.props.display_pro_alert_fun}>close</Icon>}><div className={'alert_get_pro'}> {__('This feature is available in PRO version', 'quick-adsense-reloaded')} <a
            className="quads-got_pro premium_features_btn"
            href="https://wpquads.com/#buy-wpquads" target="_blank">{__('Unlock this feature', 'quick-adsense-reloaded')}</a></div></Alert> : null}
        </td>
      </tr>
   );
    }
  }
}

export default QuadsAdSettingsProTemplate;