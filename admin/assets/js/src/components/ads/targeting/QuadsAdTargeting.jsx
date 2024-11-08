import React, { Component, Fragment } from 'react';
import Select from '@material-ui/core/Select';
import MenuItem from '@material-ui/core/MenuItem';

import './QuadsAdTargeting.scss';
import QuadsUserTargeting from  '../../common/user-targeting/QuadsUserTargeting'
import QuadsVisibility from  '../../common/visibility/QuadsVisibility'
import QuadsAdvancePosition from  '../../common/advance-position/QuadsAdvancePosition'
import queryString from 'query-string'
import QuadsAdvancePositionMuti from  '../../common/advance-position/QuadsAdvancePositionMuti'
import QuadsAdvancePositionMutiabtesting from  '../../common/advance-position-abtesting/QuadsAdvancePositionMutiabtesting'
import QuadsAdvancePositionn from  '../../common/advance-position-abtesting/QuadsAdvancePositionn'


class QuadsAdTargeting extends Component {

  constructor(props) {
    super(props);    
    this.state = {               
      file_uploaded :false,
      settings_saved :false,
      settings_error :'',       
      backup_file   : null,       
      old_settings  : '',        
      settings  :{
         grid_ad_style : '',
         g_data_ad_width  : '1',
         g_data_ad_height  : '2',
         checked: false,
        },
    checked: false
    };       
  } 
 
  componentDidMount(){
    this.getSettings();
  }

  saveSettings = () => {
    const formData = new FormData();
    formData.append("file", this.state.backup_file);
    formData.append("settings", JSON.stringify(this.state.settings));
    formData.append("requestfrom",'wpquads2');
    let url = quads_localize_data.rest_url + 'quads-route/update-settings';
    
    fetch(url,{
      method: "post",
      headers: {
        'Accept': 'application/json',
        'X-WP-Nonce': quads_localize_data.nonce,
      },
      body: formData
    })
    .then(res => res.json())
    .then(
      (result) => {
        const currentpage = queryString.parse(window.location.search);
          if(result.status === 't'){
            if(result.file_status === 't'){
              this.setState({file_uploaded:true,button_spinner_toggle:false});
              this.setState({settings_saved:true});
            }else{
              this.setState({settings_saved:true, button_spinner_toggle:false});
            }
          }else{
            this.setState({settings_error:result.msg, button_spinner_toggle:false});
          }
      },
      (error) => {
      let settings = this.state.settings;
      let old_settings = this.state.old_settings;
      let difference ={};
      const settingskeys = Object.keys(settings);
      const settingsValues = Object.values(settings);
      const old_settingsKeys = Object.keys(old_settings);
      const old_settingsValues = Object.values(old_settings);

      for ( let i = 0; i < settingskeys.length; i++ ) {
        for ( let j = 0; j < old_settingsKeys.length; j++ ) {
          if ( settingskeys[i] === old_settingsKeys[j]) {
            if ( settingsValues[i] !== old_settingsValues[j]) {
                difference[settingskeys[i]] = settingsValues[i];
            }
          }
        }
      }

    const formData = new FormData();
    formData.append("file", this.state.backup_file);
    formData.append("settings", JSON.stringify(difference));
    formData.append("requestfrom",'wpquads2');
    let url = quads_localize_data.rest_url + 'quads-route/update-settings';
    fetch(url,{
      method: "post",
      headers: {
        'Accept': 'application/json',
        'X-WP-Nonce': quads_localize_data.nonce,
      },
      body: formData
    })
    .then(res => res.json())
    .then(
      (result) => {
      const currentpage = queryString.parse(window.location.search);
       
          if(result.status === 't'){
            if(result.file_status === 't'){
              this.setState({file_uploaded:true,button_spinner_toggle:false});
              this.setState({settings_saved:true});
            }else{
              this.setState({settings_saved:true, button_spinner_toggle:false});
            }
          }else{
            this.setState({settings_error:result.msg, button_spinner_toggle:false});
          }
      },
      (error) => {
      }
    );
      }
    );
  }


  getSettings = () => {
    let url = quads_localize_data.rest_url + 'quads-route/get-settings';
    fetch(url,{
      headers: {
        'X-WP-Nonce': quads_localize_data.nonce,
      }
    })
    .then(res => res.json())
    .then(
      (result) => {
        const { settings } = { ...this.state };        
              Object.entries(result).map(([meta_key, meta_val]) => {                 
                    settings[meta_key] =    meta_val;                 
              })
          const  old_settings = {...settings};
          this.setState({settings:old_settings});
          this.setState({ isLoading: false });

      },
      (error) => {
        this.setState({ isLoading: false });

      }
    );
  }

  gridformChangeHandler = (event) => {
    let name  = event.target.name;
    let value = '';
    if(event.target.type === 'file'){
       value = event.target.files[0];
       this.setState({backup_file:value});
    }else{
      if(event.target.type === 'checkbox'){
        value = event.target.checked;
      }else{
        value = event.target.value
      }
        const { settings } = this.state;
        settings[name] = value;
        this.setState(settings);
    }

    if(name == 'grid_ad_style' ){
      this.saveSettings();
    }
    
  }
  
  render() {

    const {__} = wp.i18n;    
    const page = queryString.parse(window.location.search); 
    const post_meta = this.props.parentState.quads_post_meta;
    if((post_meta.ad_type == "group_insertion" || post_meta.ad_type == "sticky_scroll") && post_meta.position == "beginning_of_post") {
        this.props.adFormChangeHandler({target: {name: 'position', value: 'after_paragraph'}});
    }
    if(post_meta.ad_type == "loop_ads")
    {
      post_meta.position="amp_ads_in_loops";
    }
          return (
                <div>
              { post_meta.adsense_ad_type !="adsense_auto_ads" &&
                <div className="quads-settings-group">
              { (post_meta.ad_type == 'plain_text' || 
                (post_meta.ad_type == 'adsense' && post_meta.adsense_ad_type != 'adsense_sticky_ads' ) ||
                post_meta.ad_type == 'random_ads' ||
                post_meta.ad_type == 'rotator_ads' ||
                post_meta.ad_type == 'popup_ads' ||
                post_meta.ad_type == 'group_insertion' ||
                post_meta.ad_type == 'double_click' ||
                post_meta.ad_type == 'yandex' ||
                post_meta.ad_type == 'mgid' ||
                post_meta.ad_type == 'ad_image' ||
                post_meta.ad_type == 'video_ads' ||
                post_meta.ad_type == 'propeller' ||
                post_meta.ad_type == 'taboola' ||
                post_meta.ad_type == 'media_net' ||
                (post_meta.ad_type == 'adpushup' && quads_localize_data.is_amp_enable && post_meta.enabled_on_amp == true) ||
                post_meta.ad_type == 'mediavine' ||
                post_meta.ad_type == 'outbrain' ||
                post_meta.ad_type == 'infolinks' ||
                post_meta.ad_type == 'skip_ads' ||
                post_meta.ad_type == 'loop_ads' ||
                post_meta.ad_type == 'parallax_ads' ||
                post_meta.ad_type == 'half_page_ads' ||
                post_meta.ad_type == 'carousel_ads' ||
                post_meta.ad_type == 'ads_space'

              ) ?
              <>
                <div>{__('Position', 'quick-adsense-reloaded')}</div>  
                <div className="quads-panel">
                <div className="quads-panel-body"> 
                <table>
                  <tbody>
                  { post_meta.ad_type == "popup_ads" || post_meta.ad_type == "video_ads" || post_meta.ad_type == "parallax_ads" || post_meta.ad_type == "half_page_ads" || post_meta.ad_type == "loop_ads" ?  "" : 
                    <tr className="quads-tr-position">
                    <td><label>{__('Where will the AD appear?', 'quick-adsense-reloaded')}</label></td>
                    { post_meta.ad_type == "popup_ads" || post_meta.ad_type == "video_ads" || post_meta.ad_type == "parallax_ads" || post_meta.ad_type == "half_page_ads" ?  "" : 
                        <td>{post_meta.ad_type != "group_insertion" || post_meta.ad_type != "sticky_scroll" ? (<QuadsAdvancePosition parentState={this.props.parentState} adFormChangeHandler = {this.props.adFormChangeHandler}/>
                        ):<div><Select  style={{minWidth:'250px',marginTop:'20px'}} value={post_meta.position} name="position" onChange={this.props.adFormChangeHandler} >
                            <MenuItem value="after_paragraph">{__('After Paragraph', 'quick-adsense-reloaded')}</MenuItem>
                        </Select>
                            <div>
                                <div>
                                    <label > {__('Insert After Every '+post_meta.insert_after+' Paragraph', 'quick-adsense-reloaded')}</label>
                                    <input min="1" onChange={this.props.adFormChangeHandler} name="insert_after" value={post_meta.insert_after}  type="number" />
                                </div>
                                <div>
                                    <label > {__('Limit The Insertion Till Nth Ad', 'quick-adsense-reloaded')}</label>
                                    <input min="1" onChange={this.props.adFormChangeHandler} name="paragraph_limit" value={post_meta.paragraph_limit}  type="number" />
                                </div>
                            </div>
                        </div>
                            }</td>
                          }
                    </tr>
                        }
                      {post_meta.position == 'ad_after_id' ? (
                      <tr>
                        <td><label>{__('Enter Id Name', 'quick-adsense-reloaded')}</label></td>
                        <td><input  onChange={this.props.adFormChangeHandler} name="after_id_name" value={post_meta.after_id_name}  type="text" placeholder="Id name" /></td>
                      </tr>)
                      : null}
                      {post_meta.position == 'ad_after_class' ? (
                        <>
                      <tr>
                        <td><label>{__('Enter Class Name', 'quick-adsense-reloaded')}</label></td>
                        <td><input  onChange={this.props.adFormChangeHandler} name="after_class_name" value={post_meta.after_class_name}  type="text" placeholder="Class name" /></td>
                      </tr>
                    </>)
                      : null}    
                      {post_meta.position == 'ad_after_customq' ? (
                      <tr>
                        <td><label>{__('Enter Selector', 'quick-adsense-reloaded')}</label></td>
                        <td><input  onChange={this.props.adFormChangeHandler} name="after_customq_name" value={post_meta.after_customq_name}  type="text" placeholder="[@class='classname']//child::*[2]" /><div style={{fontSize: "12px",marginTop: "12px"}}>Eg: [@class='classname']//child::*[1] or [@id='idname']//child::*[1]</div></td>
                      </tr>)
                      : null}
                    {post_meta.position == 'ad_after_html_tag' ? (
                      <>
                    <tr>
                    <td><label>{__('Count As Per The', 'quick-adsense-reloaded')}</label></td>
                      <td><Select style={{minWidth:'250px',marginTop:'20px'}} value={post_meta.count_as_per} name="count_as_per" onChange={this.props.adFormChangeHandler} >
                      <MenuItem value="p_tag">{__('p (default)', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="div_tag">{__('div', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="img_tag">{__('img', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h1">{__('H1', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h2">{__('H2', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h3">{__('H3', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h4">{__('H4', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h5">{__('H5', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h6">{__('H6', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="custom_tag">{__('Custom', 'quick-adsense-reloaded')}</MenuItem>
                      </Select></td>
                    </tr>
                     {post_meta.count_as_per == 'custom_tag' ? 
                    <tr>
                    <td><label>{__('Enter Your Tag', 'quick-adsense-reloaded')}</label></td>
                      <td><input  onChange={this.props.adFormChangeHandler} name="enter_your_tag" value={post_meta.enter_your_tag}  type="text" placeholder="div" /></td>
                    </tr>
                    : null}
                    <tr>
                      <td><label>{__('Display After', 'quick-adsense-reloaded')}</label></td>
                      <td><input min="1" onChange={this.props.adFormChangeHandler} name="paragraph_number" value={post_meta.paragraph_number}  type="number" />
                      <input id='repeat_paragraph' checked={post_meta.repeat_paragraph} name="repeat_paragraph" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                      <label htmlFor="repeat_paragraph"> {__('Display After Every ', 'quick-adsense-reloaded')}{post_meta.paragraph_number} </label></td>
                    </tr>
                  </>)
                    : null}

                   { post_meta.position == 'ad_sticky_ad' ? 
                    <>
                   <tr>
                   <td><label>{__('Postion', 'quick-adsense-reloaded')}</label></td>
                     <td><Select style={{minWidth:'250px',marginTop:'20px'}} value={post_meta.sticky_slide_ad} name="sticky_slide_ad" onChange={this.props.adFormChangeHandler}>
                     <MenuItem value="sticky_ad_bot">{__('Bottom (default)', 'quick-adsense-reloaded')}</MenuItem>
                     <MenuItem value="sticky_ad_top">{__('Top', 'quick-adsense-reloaded')}</MenuItem>
                     </Select></td>
                   </tr>
                  <tr>
                  <td> <label htmlFor="cls_btn"> {__('Add close Button? ', 'quick-adsense-reloaded')}{post_meta.add_close_btn}</label> </td>
                    <td><input id='cls_btn' checked={post_meta.cls_btn} name="cls_btn" onChange={this.props.adFormChangeHandler} type="checkbox"/></td>
                  </tr>
                   <tr>
                   <td> <label htmlFor="sticky_ad_anim"> {__('Animation? ', 'quick-adsense-reloaded')}{post_meta.add_sticky_anim}</label> </td>
                     <td><input id='sticky_ad_anim' checked={post_meta.sticky_ad_anim} name="sticky_ad_anim" onChange={this.props.adFormChangeHandler} type="checkbox"/></td>
                   </tr>
                    { post_meta.sticky_ad_anim == 1 ?
                    <tr>
                    <td> <label htmlFor="sticky_ad_anim_txt"> {__('Anim Delay', 'quick-adsense-reloaded')}{post_meta.add_sticky_ad_anim_txt}</label> </td>
                       <td><input name="sticky_ad_anim_txt" value={post_meta.sticky_ad_anim_txt} onChange={this.props.adFormChangeHandler} type="number" placeholder={__('1000', 'quick-adsense-reloaded') }/>milliseconds</td>
                    </tr>
                   : '' }
                   <tr>
                   <td> <label htmlFor="sticky_ad_show_hide"> {__('Add Button to Show Ad? ', 'quick-adsense-reloaded')}{post_meta.add_sticky_show_hide}</label> </td>
                     <td><input id='sticky_ad_show_hide' checked={post_meta.sticky_ad_show_hide} name="sticky_ad_show_hide" onChange={this.props.adFormChangeHandler} type="checkbox"/></td>
                   </tr>
                   { post_meta.sticky_ad_show_hide == 1 ?
                   <tr>
                   <td> <label htmlFor="sticky_show_hide_txt"> {__('Button Text', 'quick-adsense-reloaded')}{post_meta.add_sticky_show_hide_txt}</label> </td>
                      <td><input name="sticky_show_hide_txt" value={post_meta.sticky_show_hide_txt} onChange={this.props.adFormChangeHandler} type="text" placeholder={__('Slide Up', 'quick-adsense-reloaded') }/></td>
                   </tr>
                  : '' }

                </>
                  : '' }

                    {post_meta.position == 'ad_before_html_tag' ? (
                      <>
                    <tr>
                    <td><label>{__('Count As Per The', 'quick-adsense-reloaded')}</label></td>
                      <td><Select style={{minWidth:'250px',marginTop:'20px'}} value={post_meta.count_as_per} name="count_as_per" onChange={this.props.adFormChangeHandler} >
                      <MenuItem value="p_tag">{__('p (default)', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="div_tag">{__('div', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="img_tag">{__('img', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h1">{__('H1', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h2">{__('H2', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h3">{__('H3', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h4">{__('H4', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h5">{__('H5', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="h6">{__('H6', 'quick-adsense-reloaded')}</MenuItem>
                      <MenuItem value="custom_tag">{__('Custom', 'quick-adsense-reloaded')}</MenuItem>
                      </Select></td>
                    </tr>
                     {post_meta.count_as_per == 'custom_tag' ? 
                    <tr>
                    <td><label>{__('Enter Your Tag', 'quick-adsense-reloaded')}</label></td>
                      <td><input  onChange={this.props.adFormChangeHandler} name="enter_your_tag" value={post_meta.enter_your_tag}  type="text" placeholder="div" /></td>
                    </tr>
                    : null}
                    <tr>
                      <td><label>{__('Display Before', 'quick-adsense-reloaded')}</label></td>
                      <td><input min="1" onChange={this.props.adFormChangeHandler} name="paragraph_number" value={post_meta.paragraph_number}  type="number" />
                      <input id='repeat_paragraph' checked={post_meta.repeat_paragraph} name="repeat_paragraph" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                      <label htmlFor="repeat_paragraph"> {__('Display Before Every ', 'quick-adsense-reloaded')}{post_meta.paragraph_number} </label></td>
                    </tr>
                  </>)
                    : null}
                  {post_meta.position == 'amp_ads_in_loops' ? 
                     <tr>
                      <td><label>{__('Display After', 'quick-adsense-reloaded')}</label></td>
                      <td><input min="1" onChange={this.props.adFormChangeHandler} name="ads_loop_number" value={post_meta.ads_loop_number} placeholder="Position" type="number" />
                      <input id='display_after_every' checked={post_meta.display_after_every} name="display_after_every" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                      <label htmlFor="display_after_every"> {__('Display After Every ', 'quick-adsense-reloaded')}{post_meta.ads_loop_number} </label></td>
                    </tr>
                   : null}
                    {post_meta.ad_type == 'rotator_ads' ?
                        <tr className='rotation_table'>
                            <td><label>{__('Rotation Type', 'quick-adsense-reloaded')}</label></td>
                            <td><Select style={{minWidth:'250px',marginTop:'20px'}}  value={post_meta.refresh_type} name="refresh_type" onChange={this.props.adFormChangeHandler} >
                                <MenuItem value="on_load">On Reload</MenuItem>
                                <MenuItem value="on_interval">Auto Rotate</MenuItem>
                                </Select></td>
                                {post_meta.ad_type == 'rotator_ads' && post_meta.refresh_type == 'on_interval' ?
                                <td><a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-auto-rotation-how-to-set-it-up/"></a></td> : '' }
                                </tr>
                        : null}

                  {post_meta.ad_type == 'rotator_ads' ?
                        <tr className='rotation_table'>
                            <td><label>{__('Sorting', 'quick-adsense-reloaded')}</label></td>
                            <td><Select style={{minWidth:'250px',marginTop:'20px'}}  value={post_meta.sort_type} name="sort_type" onChange={this.props.adFormChangeHandler} >
                                <MenuItem value="random">{__('Random ads', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="ordered">{__('Ordered ads', 'quick-adsense-reloaded')}</MenuItem>
                                </Select></td>
                                </tr>
                        : null}
                    {post_meta.ad_type == 'rotator_ads' && post_meta.refresh_type == 'on_interval' ?
                    <tr>
                    
                            <td></td>
                            <td>
                                <input id={'refresh_type_interval_sec'}
                                       name={'refresh_type_interval_sec'} type="number"
                                       value={post_meta.refresh_type_interval_sec} onChange={this.props.adFormChangeHandler}  /> {__('milliseconds', 'quick-adsense-reloaded') }
                                       { quads_localize_data.is_amp_enable && post_meta.enabled_on_amp == true ? <p className="description">{__('On AMP, Ads will be shown only on reload', 'quick-adsense-reloaded') }.</p> : '' }
                            </td>
                        </tr>
                        :null}
                    
                        {post_meta.ad_type == 'rotator_ads' && post_meta.refresh_type == 'on_interval' ?
                        <tr>

                        <td className='grid_style_ad'><label htmlFor="grid_ad_style"> {__('Grid', 'quick-adsense-reloaded')}</label>
                        <label className="quads-switch">
                            <input id="grid_ad_style" type="checkbox" name="grid_ad_style" onChange={this.gridformChangeHandler} checked={this.state.settings.grid_ad_style} />
                            <span className="quads-slider"></span>
                          </label>
                        </td>                          
                          { this.state.settings.grid_ad_style ?                         
                            <td>
                            <input id={'grid_data_ad_column'} placeholder='Column'
                              name={'grid_data_ad_column'} type="number"
                              value={post_meta.grid_data_ad_column }  onChange={this.props.adFormChangeHandler}    />X
                              <input id={'grid_data_ad_row'} placeholder='Row'
                              name={'grid_data_ad_row'} type="number"
                              value={post_meta.grid_data_ad_row }  onChange={this.props.adFormChangeHandler}    />
                              <a className="quads-general-helper quads-general-helper-new ad_rotator_grid" target="_blank" href="https://wpquads.com/documentation/how-to-setup-grid-in-ad-rotator/"></a>
                            </td>
                              : ''}
                            </tr>
                            
                        :null}
                        {post_meta.ad_type == 'rotator_ads' && post_meta.refresh_type == 'on_interval' ?
                        <tr>
                        <td className='grid_style_ad_num_ads_t_s'><label htmlFor="grid_ad_style_n"> {__('Number Of Ads to Show', 'quick-adsense-reloaded')}</label>
                        </td>
                        
                        <td>
                        <input id={'grid_data_ad_row'} name="num_ads_t_s" onChange={this.props.adFormChangeHandler} type="number" value={post_meta.num_ads_t_s} />
                        </td>
                        </tr>
                            
                        :null}
                        {post_meta.ad_type == 'rotator_ads' && post_meta.refresh_type == 'on_interval' ?
                        <tr>
                        <td className='repeat_impression'><label htmlFor="repeat_impressions"> {__('Repeat Impressions', 'quick-adsense-reloaded')}</label>
                        <label className="quads-switch">
                            <input id="repeat_impressions" type="checkbox" name="repeat_impressions" onChange={this.props.adFormChangeHandler} checked={post_meta.repeat_impressions} />
                            <span className="quads-slider"></span>
                          </label>
                        </td> 
                        <td>{__('Count impression of rotation ads on every rotation', 'quick-adsense-reloaded')}</td>                         
                        </tr>
                        :null}
                        {post_meta.ad_type == 'popup_ads' ?
                        <tr className='popup_table'>
                            <td><label>{__('Popup Type', 'quick-adsense-reloaded')}</label></td>
                            <td><Select style={{minWidth:'250px',marginTop:'20px'}} value={post_meta.popup_type} name="popup_type" onChange={this.props.adFormChangeHandler} >
                                <MenuItem value="everytime_popup">{__('Load instantly', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="specific_time_popup">{__('After Specific Time', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="on_scroll_popup">{__('On Scroll', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="load_on_top">{__('Load on Top', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="load_on_bottom">{__('Load on Bottom', 'quick-adsense-reloaded')}</MenuItem>
                                </Select></td>
                                 
                                </tr>
                        : null}
                    {post_meta.ad_type == 'popup_ads' && post_meta.popup_type == 'specific_time_popup'  ?
                    <tr>
                    
                            <td></td>
                            <td>
                                <input id={'specific_time_interval_sec'}
                                       name={'specific_time_interval_sec'} type="number"
                                       value={post_meta.specific_time_interval_sec} onChange={this.props.adFormChangeHandler}  /> {__('milliseconds', 'quick-adsense-reloaded') }
                            </td>
                        </tr>
                        :null}
                          {post_meta.ad_type == 'popup_ads' && (post_meta.popup_type == 'load_on_top'|| post_meta.popup_type == 'load_on_bottom')  ?
                    <tr>
                    
                            <td><label>{__('Popup delay', 'quick-adsense-reloaded')}</label></td>
                            <td>
                                <input id={'specific_time_interval_sec'}
                                       name={'specific_time_interval_sec'} type="number"
                                       value={post_meta.specific_time_interval_sec} onChange={this.props.adFormChangeHandler}  /> {__('milliseconds', 'quick-adsense-reloaded') }
                            </td>
                        </tr>
                        :null}
                    {post_meta.ad_type == 'popup_ads' && post_meta.popup_type == 'on_scroll_popup' ?
                      <tr>
                    
                            <td></td>
                            <td>
                                <input id={'on_scroll_popup_percentage'}
                                       name={'on_scroll_popup_percentage'} type="number"
                                       value={post_meta.on_scroll_popup_percentage} onChange={this.props.adFormChangeHandler}  /> {__('percentage', 'quick-adsense-reloaded') }
                            </td>
                        </tr>
                        :null}
                        { post_meta.ad_type == 'video_ads' ?
                        <tr className='video_ad_table'>
                            <td><label>{__('Video Type', 'quick-adsense-reloaded')}</label></td>
                            <td><Select style={{minWidth:'250px',marginTop:'20px'}} value={post_meta.video_ad_type} name="video_ad_type" onChange={this.props.adFormChangeHandler} >
                                <MenuItem value="select">{__('Select', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="specific_time_video">{__('After Specific Time', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="after_scroll_video">{__('On Scroll', 'quick-adsense-reloaded')}</MenuItem>
                                </Select></td>                                 
                                </tr>
                         : ''
                        }
                        {post_meta.ad_type == 'video_ads' && post_meta.video_ad_type == 'specific_time_video' ?
                          <tr>
                          <td></td>
                            <td>
                                <input id={'specific_time_interval_sec_video'}
                                       name={'specific_time_interval_sec_video'} type="number"
                                       value={post_meta.specific_time_interval_sec_video} onChange={this.props.adFormChangeHandler}  /> {__('milliseconds', 'quick-adsense-reloaded')}
                            </td>
                        </tr>
                        :null}
                        {post_meta.ad_type == 'video_ads' && post_meta.video_ad_type == 'after_scroll_video' ?
                          <tr>
                            <td></td>
                            <td>
                                <input id={'on_scroll_video_percentage'}
                                       name={'on_scroll_video_percentage'} type="number"
                                       value={post_meta.on_scroll_video_percentage} onChange={this.props.adFormChangeHandler}  /> {__('Scroll percentage', 'quick-adsense-reloaded')}
                            </td>
                        </tr>
                        :null}
                        
                        { post_meta.ad_type == 'video_ads' ?
                        <tr className='video_ad_positiontable'>
                            <td><label>{__('Position', 'quick-adsense-reloaded')}</label></td>
                            <td><Select style={{minWidth:'250px',marginTop:'20px'}} value={post_meta.video_ad_type_position} name="video_ad_type_position" onChange={this.props.adFormChangeHandler} >
                                <MenuItem value="select">{__('Select', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="v_left">{__('Left', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="v_right">{__('Right', 'quick-adsense-reloaded')}</MenuItem>
                                </Select></td>                                 
                                </tr>
                         : ''
                        }
                        { post_meta.ad_type == 'parallax_ads' ?
                        <tr className='parallax_ads_table'>
                            <td><label>{__('Parallax Type', 'quick-adsense-reloaded')}</label></td>
                            <td><Select style={{minWidth:'250px',marginTop:'20px'}} value={post_meta.parallax_ads_type} name="parallax_ads_type" onChange={this.props.adFormChangeHandler} >
                                <MenuItem value="after_scroll_parallax_ads">{__('On Scroll', 'quick-adsense-reloaded')}</MenuItem>
                                </Select></td>                                 
                                </tr>
                         : ''
                        }
                        {post_meta.ad_type == 'parallax_ads' ?
                          <tr>
                            <td></td>
                            <td>
                                <input id={'on_scroll_parallax_ads_percentage'}
                                       name={'on_scroll_parallax_ads_percentage'} type="number"
                                       value={post_meta.on_scroll_parallax_ads_percentage} onChange={this.props.adFormChangeHandler}  /> {__('Scroll percentage', 'quick-adsense-reloaded')}
                            </td>
                        </tr>
                        :null}

                        {post_meta.ad_type == 'half_page_ads' ?
                          <tr>
                          <td><label>{__('Open/Close Text', 'quick-adsense-reloaded')}</label></td>
                            <td>
                              <input id={'half_page_ads_page_vertical_text'} name={'half_page_ads_page_vertical_text'} type="text" value={post_meta.half_page_ads_page_vertical_text ? post_meta.half_page_ads_page_vertical_text : 'Click Here To Open/Close'} onChange={this.props.adFormChangeHandler}  /> 
                            </td>
                        </tr>
                      :null}

                      { post_meta.ad_type == 'half_page_ads' ?
                        <tr className='half_page_ads_table_position'>
                          <td><label>{__('Where Will The AD Appear?', 'quick-adsense-reloaded')}</label></td>
                          <td><Select style={{minWidth:'250px',marginTop:'20px'}} value={post_meta.half_page_ads_type_position} name="half_page_ads_type_position" onChange={this.props.adFormChangeHandler} >
                              <MenuItem value="select">{__('Select', 'quick-adsense-reloaded')}</MenuItem>
                              <MenuItem value="half_page_ads_type_position_left">{__('Left', 'quick-adsense-reloaded')}</MenuItem>
                              <MenuItem value="half_page_ads_type_position_right">{__('Right', 'quick-adsense-reloaded')}</MenuItem>
                              </Select></td>                                 
                              </tr>
                        : ''
                      }

                      { post_meta.ad_type == 'half_page_ads' ?
                        <tr className='half_page_ads_table'>
                            <td><label>{__('Ad Close Type', 'quick-adsense-reloaded')}</label></td>
                            <td><Select style={{minWidth:'250px',marginTop:'20px'}} value={post_meta.half_page_ads_type} name="half_page_ads_type" onChange={this.props.adFormChangeHandler} >
                                <MenuItem value="select">{__('Select', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="half_page_ads_type_specific_time_sec">{__('After Specific Time', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="half_page_ads_type_on_click">{__('On Click', 'quick-adsense-reloaded')}</MenuItem>
                                </Select></td>                                 
                        </tr>
                         : ''
                      }

                      {post_meta.ad_type == 'half_page_ads' && post_meta.half_page_ads_type == 'half_page_ads_type_specific_time_sec' ?
                        <tr>
                          <td></td>
                            <td>
                                <input id={'half_page_ads_type_specific_time_num'}
                                       name={'half_page_ads_type_specific_time_num'} type="number"
                                       value={post_meta.half_page_ads_type_specific_time_num ? post_meta.half_page_ads_type_specific_time_num : 5000} onChange={this.props.adFormChangeHandler}  /> {__('milliseconds', 'quick-adsense-reloaded')}
                            </td>
                        </tr>
                      :null}
                  </tbody>
                </table>                                 
                </div>  
                </div> 
                </>
                : ''}
     {post_meta.ad_type == "ad_blindness" ? <QuadsAdvancePositionMuti parentState={this.props.parentState} adFormChangeHandler={this.props.adFormChangeHandler} />  
        : ''}
     {post_meta.ad_type == "ab_testing" ? <QuadsAdvancePositionMutiabtesting parentState={this.props.parentState} adFormChangeHandler={this.props.adFormChangeHandler} />  
        : ''}

                </div> 
              }
                {post_meta.position != 'ad_shortcode' && post_meta.position != 'amp_story_ads' ?
                  <QuadsVisibility 
                    parentState                  ={this.props.parentState} 
                    updateVisibility             ={this.props.updateVisibility}
                  />
                 : ''}
                    {post_meta.position != 'amp_story_ads' ?
               <QuadsUserTargeting 
                  parentState                  ={this.props.parentState} 
                  updateVisitorTarget          ={this.props.updateVisitorTarget}
                />
                        : ''}
              <div className="quads-btn-navigate">
                <div className="quads-next"><a onClick={this.props.publish} className="quads-btn quads-btn-primary">{__(page.action == 'edit' ? __('Update', 'quick-adsense-reloaded'): __('Publish', 'quick-adsense-reloaded'))}</a></div>
                <div ><a onClick={this.props.movePrev} className="quads-btn quads-btn-primary">{__('Prev', 'quick-adsense-reloaded')}</a></div>
                </div>
              </div>
            );
  }
}

export default QuadsAdTargeting;