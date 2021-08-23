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

    if (quads_localize_data.is_pro) {
      return (
        <tr>  <th><label htmlFor={data.id}>{data.title} </label></th>
          <td>
            <label className="quads-switch">
              <input type="checkbox" name={data.id} onChange={this.props.formChangeHandler} checked={settings[data.id]} value={data.id} />
              <span className="quads-slider"></span>
            </label>
            <a className="quads-general-helper quads-general-helper-new" target="_blank" href={data.url}></a>
          </td>
        </tr>
      );
    } else {
      return (
      <tr>  <th><label htmlFor={data.id}>{data.title} <span className={'getprocheckbox'}>PRO</span></label></th>
        <td>
          <label className="quads-switch">
            <input type="checkbox" name={data.id} onChange={this.props.display_pro_alert_fun} value={data.id} />
            <span className="quads-slider"></span>
          </label>
          <a className="quads-general-helper quads-general-helper-new" target="_blank" href={data.url}></a>
          {quads_pro_list_selected.includes(data.id) ? <Alert severity="error" action={<Icon onClick={this.props.display_pro_alert_fun}>close</Icon>}><div className={'alert_get_pro'}> This feature is available in PRO version <a
            className="quads-got_pro premium_features_btn"
            href="https://wpquads.com/#buy-wpquads" target="_blank">Unlock this feature</a></div></Alert> : null}
        </td>
      </tr>
   );
    }
  }
}

export default QuadsAdSettingsProTemplate;