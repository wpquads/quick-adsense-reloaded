import React, { Component, Fragment } from 'react';

import './QuadsAdTargeting.scss';
import QuadsUserTargeting from  '../../common/user-targeting/QuadsUserTargeting'
import QuadsVisibility from  '../../common/visibility/QuadsVisibility'
import QuadsAdvancePosition from  '../../common/advance-position/QuadsAdvancePosition'
import queryString from 'query-string'


class QuadsAdTargeting extends Component {

  constructor(props) {
    super(props);    
    this.state = {               
    };       
  } 
 
  render() {

    const {__} = wp.i18n;    
    const page = queryString.parse(window.location.search); 
    const post_meta = this.props.parentState.quads_post_meta;
    if(post_meta.ad_type == "group_insertion" && post_meta.position == "beginning_of_post") {
        this.props.adFormChangeHandler({target: {name: 'position', value: 'after_paragraph'}});
    }
          return (
                <div>
                <div className="quads-settings-group">
              {post_meta.ad_type != "background_ad" && post_meta.adsense_ad_type != 'adsense_sticky_ads' ?
              <>
                <div>{__('Position', 'quick-adsense-reloaded')}</div>  
                <div className="quads-panel">
                <div className="quads-panel-body"> 
                <table>
                  <tbody>
                    <tr className="quads-tr-position">
                    <td><label>{__('Where will the AD appear?', 'quick-adsense-reloaded')}</label></td>
                        <td>{post_meta.ad_type != "group_insertion" ? (<QuadsAdvancePosition parentState={this.props.parentState} adFormChangeHandler = {this.props.adFormChangeHandler}/>
                        ):<div><select  value={post_meta.position} name="position" onChange={this.props.adFormChangeHandler} >
                            <option value="after_paragraph">{__('After Paragraph', 'quick-adsense-reloaded')}</option>
                        </select>
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
                    </tr>
                    {post_meta.position == 'ad_after_html_tag' ? (
                      <>
                    <tr>
                    <td><label>{__('Count As Per The', 'quick-adsense-reloaded')}</label></td>
                      <td><select value={post_meta.count_as_per} name="count_as_per" onChange={this.props.adFormChangeHandler} >
                      <option value="p_tag">p (default)</option>
                      <option value="div_tag">div</option>
                      <option value="img_tag">img</option>
                      <option value="h1">H1</option>
                      <option value="h2">H2</option>
                      <option value="h3">H3</option>
                      <option value="h4">H4</option>
                      <option value="h5">H5</option>
                      <option value="h6">H6</option>
                      <option value="custom_tag">{__('Custom', 'quick-adsense-reloaded')}</option>
                      </select></td>
                    </tr>
                     {post_meta.count_as_per == 'custom_tag' ? 
                    <tr>
                    <td><label>{__('Enter Your Tag', 'quick-adsense-reloaded')}</label></td>
                      <td><input  onChange={this.props.adFormChangeHandler} name="enter_your_tag" value={post_meta.enter_your_tag}  type="text" placeholder='"div"' /></td>
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

                  {post_meta.position == 'amp_ads_in_loops' ? 
                     <tr>
                      <td><label>{__('Display After', 'quick-adsense-reloaded')}</label></td>
                      <td><input min="1" onChange={this.props.adFormChangeHandler} name="ads_loop_number" value={post_meta.ads_loop_number} placeholder="Position" type="number" />
                      <input id='display_after_every' checked={post_meta.display_after_every} name="display_after_every" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                      <label htmlFor="display_after_every"> {__('Display After Every ', 'quick-adsense-reloaded')}{post_meta.ads_loop_number} </label></td>
                    </tr>
                   : null}
                    {post_meta.ad_type == 'rotator_ads' ?
                        <tr>
                            <td><label>{__('Refresh Type', 'quick-adsense-reloaded')}</label></td>
                            <td><select value={post_meta.refresh_type} name="refresh_type" onChange={this.props.adFormChangeHandler} >
                                <option value="on_load">On Reload</option>
                                <option value="on_interval">Auto Refresh</option>
                            </select></td>
                        </tr>
                        : null}
                    {post_meta.ad_type == 'rotator_ads' && post_meta.refresh_type == 'on_interval' ?
                        <tr>
                            <td></td>
                            <td>
                                <input id={'refresh_type_interval_sec'}
                                       name={'refresh_type_interval_sec'} type="number"
                                       value={post_meta.refresh_type_interval_sec} onChange={this.props.adFormChangeHandler}  /> milliseconds<p className="description">Refresh ads on the
                                    same spot</p><p className="description">On AMP ads will be shown only on reload.</p>
                            </td>
                        </tr>
                        :null}
                  </tbody>
                </table>                                 
                </div>  
                </div> 
                </>
                : ''}
                </div> 
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
                <div className="quads-next"><a onClick={this.props.publish} className="quads-btn quads-btn-primary">{page.action == 'edit' ? 'Update' : 'Publish'}</a></div>
                <div ><a onClick={this.props.movePrev} className="quads-btn quads-btn-primary">{__('Prev', 'quick-adsense-reloaded')}</a></div>
                </div>
              </div>
            );
  }
}

export default QuadsAdTargeting;