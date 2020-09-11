import React, { Component, Fragment } from 'react';
import './QuadsAdConfigFields.scss';
import QuadsAdModal from '../../common/modal/QuadsAdModal';
import QuadsLargeAdModal from '../../common/modal/QuadsLargeAdModal';

import Icon from '@material-ui/core/Icon';
import Select from "react-select";

class QuadsAdConfigFields extends Component {
  constructor(props) {
    super(props);    
    this.state = { 
    adsToggle : false,    
    random_ads_list:[],  
    getallads_data: [],
    getallads_data_temp: [],
    currentselectedvalue: "",
    currentselectedlabel : "",              
    };       
  }   
  adsToggle = () => {
  const get_all_data = JSON.parse(JSON.stringify(this.state.getallads_data));
  var get_all_data_count = get_all_data.length;
  var getallads_data_temp = [];
  getallads_data_temp = get_all_data;
  const random_ads_list = this.state.random_ads_list;
  var random_ads_list_count = this.state.random_ads_list.length;

    for(var i = 0;i < get_all_data_count;i++){
      for(var j = 0;j < random_ads_list_count;j++){
        if(typeof random_ads_list[j] !== "undefined" && typeof get_all_data[i] !== "undefined" && get_all_data[i].value == random_ads_list[j].value){
          getallads_data_temp.splice(i,1);
        }
      }
    }
 
  this.setState({adsToggle:!this.state.adsToggle,currentselectedvalue : '',getallads_data_temp:getallads_data_temp});
}
addIncluded = (e) => {

    e.preventDefault();  

    let type  = this.state.multiTypeLeftIncludedValue;
    let value = this.state.multiTypeRightIncludedValue;
  
    if( typeof (value.value) !== 'undefined'){
      const {random_ads_list} = this.state;
      let data    = random_ads_list;
      data.push({type: type, value: value});
      let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);          
      this.setState({random_ads_list: newData});       
    }        
  
}

  static getDerivedStateFromProps(props, state) {    

    if(!state.adsToggle){
      return {
        random_ads_list: props.parentState.quads_post_meta.random_ads_list, 
      };
    }else{
      return null;
    }
    
  }
    componentDidUpdate (){
    
    const random_ads_list = this.state.random_ads_list; 
    if(random_ads_list &&random_ads_list.length > 0 ){
      this.props.updateRandomAds(random_ads_list);
    }
    
  }
  
  selectimages  = (event) => {
      var image_frame;

      var self =this;
      if(image_frame){
       image_frame.open();
      }

      // Define image_frame as wp.media object
      image_frame = wp.media({
                 library : {
                      type : 'image',
                  }
             });
      image_frame.on('close',function() {
                  // On close, get selections and save to the hidden input
                  // plus other AJAX stuff to refresh the image preview
                  var selection =  image_frame.state().get('selection');
                  var id = '';
                  var src = '';
                  var my_index = 0;
                  selection.each(function(attachment) {
                     id = attachment['id'];
                     src = attachment.attributes.sizes.full.url;
                  });
                  self.props.adFormChangeHandler({ target : { name : 'image_src_id' , value : id } });
                  self.props.adFormChangeHandler({ target : { name : 'image_src' , value : src } });                  
               });   
      image_frame.on('open',function() {
              // On open, get the id from the hidden input
              // and select the appropiate images in the media manager
              var selection =  image_frame.state().get('selection');

            });
          image_frame.open();

    }
    remove_image = (e) => {
    this.props.adFormChangeHandler({ target : { name : 'image_src_id' , value : '' } });
    this.props.adFormChangeHandler({ target : { name : 'image_src' , value : '' } });    

}
removeSeleted = (e) => {
      let index = e.currentTarget.dataset.index;  
      const { random_ads_list } = { ...this.state };    
      random_ads_list.splice(index,1);
      this.setState(random_ads_list);

}
  getallads = (search_text = '',page = '') => {
       let url = quads_localize_data.rest_url + "quads-route/get-ads-list?posts_per_page=100&pageno="+page;
  if(quads_localize_data.rest_url.includes('?')){
        url = quads_localize_data.rest_url + "quads-route/get-ads-list&posts_per_page=100&pageno="+page;
  }
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': quads_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {      
          let getallads_data =[];
          Object.entries(result.posts_data).map(([key, value]) => {
          if(value.post_meta['ad_type'] != "random_ads" && value.post['post_status'] != "draft")
            getallads_data.push({label: value.post['post_title'], value: value.post['post_id']});
          })      
            this.setState({
            isLoaded: true,
            getallads_data: getallads_data,
          });
          
        },        
        (error) => {
          this.setState({
             isLoaded: true,         
          });
        }
      );          
  }

  addselected = (e) => {

    e.preventDefault();  

    let value  = this.state.currentselectedvalue;  
    let label  = this.state.currentselectedlabel;  
  
    if( typeof (value) !== 'undefined' && value != ''){
      const {random_ads_list} = this.state;
      let data    = random_ads_list;
      data.push({ value: value,label: label});
      let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);          
      this.setState({random_ads_list: newData,adsToggle : false});    
         
    }        
  
}
   componentDidMount() {  
          this.getallads(); 
  } 
    selectAdchange = (option) => {    
   
      this.setState({currentselectedlabel: option.label,currentselectedvalue: option.value});

  }
  render() {     

          const {__} = wp.i18n;
          const post_meta = this.props.parentState.quads_post_meta;
          const show_form_error = this.props.parentState.show_form_error;
          const comp_html = [];   
          let ad_type_name = '';     

          switch (this.props.ad_type) {

            case 'adsense':
             ad_type_name = 'AdSense';  
              comp_html.push(<div key="adsense">
                <table>
                  <tbody>
                    <tr><td><label>{__('AdSense Type', 'quick-adsense-reloaded')}</label></td>
                    <td>
                      <div>
                        <select value={post_meta.adsense_ad_type} onChange={this.props.adFormChangeHandler} name="adsense_ad_type" id="adsense_ad_type">
                          <option value="display_ads">{__('Display Ads', 'quick-adsense-reloaded')}</option>
                          <option value="in_feed_ads">{__('In-Feel Ads', 'quick-adsense-reloaded')}</option> 
                          <option value="in_article_ads">{__('In-Article Ads', 'quick-adsense-reloaded')}</option> 
                          <option value="adsense_auto_ads">{__('Auto Ads', 'quick-adsense-reloaded')}</option> 
                          <option value="matched_content">{__('Matched content', 'quick-adsense-reloaded')}</option> 
                        </select>
                      </div>
                    </td></tr> 
                    {post_meta.adsense_ad_type == 'in_feed_ads' ? 
                    <tr><td><label>{__('Data Layout Key', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_layout_key == '') ? 'quads_form_error' : ''} value={post_meta.data_layout_key} placeholder="-ez+4v+7r-fc+65" onChange={this.props.adFormChangeHandler} type="text" id="data_layout_key" name="data_layout_key" />
                    {(show_form_error && post_meta.data_layout_key == '') ? <div className="quads_form_msg"><span className="material-icons">
                      error_outline</span>Enter Data Layout Key</div> :''} </td></tr>
                      : null }
                    <tr><td><label>{__('Data Client ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.g_data_ad_client == '') ? 'quads_form_error' : ''} value={post_meta.g_data_ad_client} placeholder="ca-pub-2005XXXXXXXXX342" onChange={this.props.adFormChangeHandler} type="text" id="g_data_ad_client" name="g_data_ad_client" />
                    {(show_form_error && post_meta.g_data_ad_client == '') ? <div className="quads_form_msg"><span className="material-icons">
                      error_outline</span>Enter Data Client ID</div> :''} </td></tr>

                     {post_meta.adsense_ad_type != 'adsense_auto_ads' ? 
                    <tr><td><label>{__('Data Slot ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.g_data_ad_slot == '') ? 'quads_form_error' : ''}  value={post_meta.g_data_ad_slot} onChange={this.props.adFormChangeHandler} type="text" id="g_data_ad_slot" name="g_data_ad_slot" placeholder="70XXXXXX12" />
                    {(show_form_error && post_meta.g_data_ad_slot == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline
                    </span>Enter Data Slot ID</div> :''}</td></tr>
                      : null }
                      { !post_meta.adsense_ad_type || post_meta.adsense_ad_type == 'display_ads' || post_meta.adsense_ad_type == 'matched_content' ? (
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
                      ) : null }
                  </tbody>
                </table>
                </div>);

              break;
          
              case 'plain_text':                
                ad_type_name = 'Plain Text / HTML / JS';
                comp_html.push(<div key="plain_text">
                  <table><tbody>
                  <tr>
                  <td><label>{__('Plain Text / HTML / JS', 'quick-adsense-reloaded')}</label></td> 
                  <td><textarea className={(show_form_error && post_meta.code == '') ? 'quads_form_error' : ''}  cols="50" rows="5" value={post_meta.code} onChange={this.props.adFormChangeHandler} id="code" name="code" />
                  {(show_form_error && post_meta.code == '') ? <div className="quads_form_msg"><span className="material-icons">error_outline</span>Enter Plain Text / HTML / JS</div> : ''}</td>
                  </tr>
                  </tbody></table>
                  </div>);      
              break; 
               case 'random_ads':                
                 ad_type_name = 'Random Ads';
                comp_html.push(<div key="random_ads" className="quads-user-targeting"> 
       <h2>Select Ads<a onClick={this.adsToggle}><Icon>add_circle</Icon></a>  </h2>

                
             <div className="quads-target-item-list">
              {                
              this.state.random_ads_list ? 
              this.state.random_ads_list.map( (item, index) => (
                <div key={index} className="quads-target-item">
                  <span className="quads-target-label">{item.label}</span>
                  <span className="quads-target-icon" onClick={this.removeSeleted} data-index={index}><Icon>close</Icon></span> 
                </div>
               ) )
              :''}
              <div>{ (this.state.random_ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>Select at least one Ad</div></span> : ''}</div>
             </div>             
        

        {this.state.adsToggle ?
        <div className="quads-targeting-selection">
        <table className="form-table">
         <tbody>
           <tr>             
           <td>
            <Select              
              name="userTargetingIncludedType"
              placeholder="Select Ads"              
              options= {this.state.getallads_data_temp}
              value  = {this.multiTypeLeftIncludedValue}
              onChange={this.selectAdchange}                                                 
            />             
           </td>
           <td><a onClick={this.addselected} className="quads-btn quads-btn-primary">Add</a></td>
           </tr>
         </tbody> 
        </table>
        </div>
        : ''}
       </div>);      
              break; 
            case 'double_click':
             ad_type_name = 'Google AD Manager (DFP)';  
              comp_html.push(<div key="double_click">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Network Code', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.network_code == '') ? 'quads_form_error' : ''} value={post_meta.network_code} onChange={this.props.adFormChangeHandler} type="text" id="network_code" name="network_code" placeholder="Network Code" />
                    {(show_form_error && post_meta.network_code == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Network Code</div> :''}
                     </td></tr>
                    <tr><td><label>{__('AD Unit Name', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.ad_unit_name == '') ? 'quads_form_error' : ''}  value={post_meta.ad_unit_name} onChange={this.props.adFormChangeHandler} type="text" placeholder="AD Unit Name" id="ad_unit_name" name="ad_unit_name" />
                    {(show_form_error && post_meta.ad_unit_name == '') ? <div className="quads_form_msg"><span className="material-icons">
error_outline
</span>Enter AD Unit Name</div> :''}</td></tr>
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
                  </tbody>
                </table>
                </div>);

              break;
            case 'yandex':
             ad_type_name = 'Yandex';  
              comp_html.push(<div key="yandex">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Block Id', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.block_id == '') ? 'quads_form_error' : ''} value={post_meta.block_id} onChange={this.props.adFormChangeHandler} type="text" id="block_id" name="block_id" placeholder="Block Id" />
                    {(show_form_error && post_meta.block_id == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Block Id</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);

              break;
            case 'mgid':
             ad_type_name = 'MGID';  
              comp_html.push(<div key="mgid">
                <table>
                  <tbody>
                       <tr><td>
                    <label>{__('Data Container', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_container == '') ? 'quads_form_error' : ''} value={post_meta.data_container} onChange={this.props.adFormChangeHandler} type="text" id="data_container" name="data_container" placeholder="M87ScriptRootC123645" />
                    {(show_form_error && post_meta.data_container == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Data Container</div> :''}
                     </td></tr>
                           <tr><td>
                    <label>{__('Data Js Src', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_js_src == '') ? 'quads_form_error' : ''} value={post_meta.data_js_src} onChange={this.props.adFormChangeHandler} type="text" id="data_js_src" name="data_js_src" placeholder="//jsc.mgid.com/a/m/quads.com.123645.js" />
                    {(show_form_error && post_meta.data_js_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Data Js Src</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);

              break;
            case 'ad_image':
             ad_type_name = 'Banner';  
              comp_html.push(<div key="ad_image">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Upload Ad Banner', 'quick-adsense-reloaded')}</label></td><td>
                   {post_meta.image_src == '' ? <div><a className="button" onClick={this.selectimages}>{__(' Upload Banner', 'quick-adsense-reloaded')}</a></div>
                   : <div>
                   <img src={post_meta.image_src} className="banner_image" />
                   <a className="button" onClick={this.remove_image}>{__('Remove Banner', 'quick-adsense-reloaded')}</a></div>}
                     
                      
                    {(show_form_error && post_meta.image_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Upload Ad Image</div> :''}
                     </td></tr>
                     <tr><td>
                    <label>{__('Ad Anchor link', 'quick-adsense-reloaded')}</label></td><td>
                    <input value={post_meta.image_redirect_url} onChange={this.props.adFormChangeHandler} type="text" id="image_redirect_url" name="image_redirect_url" placeholder="Ad Anchor link" />
                    {(show_form_error && post_meta.image_redirect_url == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Ad Anchor link</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);

              break;
            case 'taboola':
             ad_type_name = 'Taboola';  
              comp_html.push(<div key="taboola">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Data Publisher Id', 'quick-adsense-reloaded')}</label></td><td>
                   <div> <input value={post_meta.taboola_publisher_id} onChange={this.props.adFormChangeHandler} type="text" id="taboola_publisher_id" name="taboola_publisher_id" placeholder="123456" /></div>
                                   
                    {(show_form_error && post_meta.taboola_publisher_id == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Data Publisher Id</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);

              break;
              case 'media_net':
             ad_type_name = 'Media.net';  
              comp_html.push(<div key="media_net">
                <table>
                  <tbody>
                      <tr><td>
                    <label>{__('Data CID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_cid == '') ? 'quads_form_error' : ''} value={post_meta.data_cid} onChange={this.props.adFormChangeHandler} type="text" id="data_cid" name="data_cid" placeholder="8XXXXX74" />
                    {(show_form_error && post_meta.data_cid == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Data CID</div> :''}
                     </td></tr>
                     <tr><td>
                    <label>{__('Data CRID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_crid == '') ? 'quads_form_error' : ''} value={post_meta.data_crid} onChange={this.props.adFormChangeHandler} type="text" id="data_crid" name="data_crid" placeholder="1XXXXXX82" />
                    {(show_form_error && post_meta.data_crid == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Data CRID</div> :''}
                     </td></tr>
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
                  </tbody>
                </table>
                </div>);

              break;
              case 'mediavine':
             ad_type_name = 'MediaVine';  
              comp_html.push(<div key="mediavine">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Data Site Id', 'quick-adsense-reloaded')}</label></td><td>
                   <div> <input value={post_meta.mediavine_site_id} onChange={this.props.adFormChangeHandler} type="text" id="mediavine_site_id" name="mediavine_site_id" placeholder="123456" /></div>
                                   
                    {(show_form_error && post_meta.mediavine_site_id == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Data Site Id</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);
              break;
            case 'outbrain':
             ad_type_name = 'Outbrain';  
              comp_html.push(<div key="outbrain">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Widget Id\'s', 'quick-adsense-reloaded')}</label></td><td>
                   <div> <input value={post_meta.outbrain_widget_ids} onChange={this.props.adFormChangeHandler} type="text" id="outbrain_widget_ids" name="outbrain_widget_ids" placeholder="widget_1,widget_2" /></div>
                                   
                    {(show_form_error && post_meta.outbrain_widget_ids == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Widget Id's</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);
              break;
            case 'background_ad':
             ad_type_name = 'Background';  
              comp_html.push(<div key="background_ad">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Upload Ad Banner', 'quick-adsense-reloaded')}</label></td><td>
                   {post_meta.image_src == '' ? <div><a className="button" onClick={this.selectimages}>{__(' Upload Banner', 'quick-adsense-reloaded')}</a></div>
                   : <div>
                   <img src={post_meta.image_src} className="banner_image" />
                   <a className="button" onClick={this.remove_image}>{__('Remove Banner', 'quick-adsense-reloaded')}</a></div>}                      
                    {(show_form_error && post_meta.image_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Upload Ad Image</div> :''}
                     </td></tr>
                     <tr><td>
                    <label>{__('Ad Anchor link', 'quick-adsense-reloaded')}</label></td><td>
                    <input value={post_meta.image_redirect_url} onChange={this.props.adFormChangeHandler} type="text" id="image_redirect_url" name="image_redirect_url" placeholder="Ad Anchor link" />
                    {(show_form_error && post_meta.image_redirect_url == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Ad Anchor link</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);
              break;
            default:
              comp_html.push(<div key="noads" >{__('Ad not found', 'quick-adsense-reloaded')}</div>);
              break;
          }
              return(
                <div>{ad_type_name} {__('Ad Configuration', 'quick-adsense-reloaded')}
                {this.props.ad_type == 'adsense' ? 
                <div className="quads-autofill-div"><a className="quads-autofill" onClick={this.props.openModal}>{__('Autofill', 'quick-adsense-reloaded')}</a>
                <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/how-to-add-adsense-ads-in-wp-quads/"></a>
                <QuadsLargeAdModal 
                 closeModal    = {this.props.closeModal}
                 parentState={this.props.parentState} 
                 title={__('Enter AdSense text and display ad code here', 'quick-adsense-reloaded')}
                  content={
                    <div>
                      <div><textarea className="quads-auto-fill-textarea" cols="80" rows="15" onChange={this.props.modalValue} value={this.props.quads_modal_value}/></div>
                      <div>
Do not enter AdSense page level ads or Auto ads! Learn how to create <a  target="_blank" href="https://wpquads.com/documentation/how-to-find-data-client-id-data-slot-id-for-adsense-integration/"> AdSense ad code </a>
                      <a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.props.getAdsenseCode}>{__('Get Code', 'quick-adsense-reloaded')}</a></div>
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