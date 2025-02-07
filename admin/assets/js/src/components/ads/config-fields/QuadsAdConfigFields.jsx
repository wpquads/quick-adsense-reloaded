import React, { Component, Fragment } from 'react';
import './QuadsAdConfigFields.scss';
import QuadsAdModal from '../../common/modal/QuadsAdModal';
import QuadsLargeAdModal from '../../common/modal/QuadsLargeAdModal';
import MenuItem from '@material-ui/core/MenuItem';
import MSelect from '@material-ui/core/Select';
import Icon from '@material-ui/core/Icon';
import Select from "react-select";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";

class QuadsAdConfigFields extends Component {
  constructor(props) {
    super(props);    
    this.state = { 
    adsToggle : false,    
    random_ads_list:[],  
    adsToggle_list : false,
    ads_list:[], 
    ad_blindness:[],
    ab_testing:[],
    popup_ads:[],
    getallads_data: [],
    getallads_data_temp: [],
    ad_ids_temp: [],
    floating_slides_flag :false,
    floating_slides:[{'slide':'#','link':'#'}],
    currentselectedvalue: "",
    currentselectedlabel : "",
    height:"",
    width:"",
    currency:quads_localize_data.currency,              
    };       
  }   
  adsToggle = () => {
  const get_all_data = JSON.parse(JSON.stringify(this.state.getallads_data));
  var get_all_data_count = get_all_data.length;
  var getallads_data_temp = [];
  getallads_data_temp = get_all_data;
  const random_ads_list = this.state.random_ads_list;
  var random_ads_list_count = this.state.random_ads_list.length;

      for( let i=getallads_data_temp.length - 1; i>=0; i--){
          for( let j=0; j<random_ads_list.length; j++){
              if(getallads_data_temp[i] && (getallads_data_temp[i].value === random_ads_list[j].value)){
                  getallads_data_temp.splice(i, 1);
              }
          }
      }
  this.setState({adsToggle:!this.state.adsToggle,currentselectedvalue : '',getallads_data_temp:getallads_data_temp});
}
  adsToggle_list = () => {
  const get_all_data = JSON.parse(JSON.stringify(this.state.getallads_data));
  var getallads_data_temp = [];
  getallads_data_temp = get_all_data;
  const ads_list = this.state.ads_list;

      for( let i=getallads_data_temp.length - 1; i>=0; i--){
          for( let j=0; j<ads_list.length; j++){
              if(getallads_data_temp[i] && (getallads_data_temp[i].value === ads_list[j].value)){
                  getallads_data_temp.splice(i, 1);
              }
          }
      }

  this.setState({adsToggle_list:!this.state.adsToggle_list,currentselectedvalue : '',getallads_data_temp:getallads_data_temp});
}

  static getDerivedStateFromProps(props, state) {    

       let alldata = {};
    if(!state.adsToggle){
       alldata.random_ads_list = props.parentState.quads_post_meta.random_ads_list;
    }
    if(!state.adsToggle_list){
      alldata.ads_list= props.parentState.quads_post_meta.ads_list;
    }
  
    if(!state.floating_slides_flag &&  props.parentState.quads_post_meta.floating_slides.length>0 ){
      alldata.floating_slides= props.parentState.quads_post_meta.floating_slides;
    }
    return alldata;
    
  }
    componentDidUpdate (){
    
    const random_ads_list = this.state.random_ads_list; 
    if(random_ads_list &&random_ads_list.length > 0 ){
      this.props.updateRandomAds(random_ads_list);
    }
     const ads_list = this.state.ads_list; 
    if(ads_list && ads_list.length > 0 ){
      this.props.updateAdsList(ads_list);
    }
    const floating_slides = this.state.floating_slides; 
    if(floating_slides && floating_slides.length > 0 ){
     this.props.updateFloatingList(floating_slides);
    }
    
  }
  
  handleDrop = (droppedItem) => {
    // Ignore drop outside droppable container
    if (!droppedItem.destination) return;
    var updatedList = { ...this.state };
    const [reorderedItem] = updatedList.ads_list.splice(droppedItem.source.index, 1);
    updatedList.ads_list.splice(droppedItem.destination.index, 0, reorderedItem);
    this.setState(updatedList);
  };

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
                    console.log(attachment);
                     id = attachment['id'];
                     src = attachment.attributes.sizes.full.url;
                     self.setState({height:attachment.attributes.height,width:attachment.attributes.width});
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
    selectadbannerimages  = (event) => {
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
                     self.setState({height:attachment.attributes.height,width:attachment.attributes.width});
                  });
                  self.props.adFormChangeHandler({ target : { name : 'ad_space_banner_image_src' , value : src } });                  
               });   
      image_frame.on('open',function() {
              // On open, get the id from the hidden input
              // and select the appropiate images in the media manager
              var selection =  image_frame.state().get('selection');

            });
          image_frame.open();

    }
  
    selectvideo  = (event) => {
      var video_frame;

      var self =this;
      if(video_frame){
       video_frame.open();
      }

      // Define video_frame as wp.media object
      video_frame = wp.media({
                 library : {
                      type : 'video',
                  }
             });
      video_frame.on('close',function() {
                  // On close, get selections and save to the hidden input
                  // plus other AJAX stuff to refresh the image preview
                  var selection =  video_frame.state().get('selection');
                  var id = '';
                  var src = '';
                  var video_width = '';
                  var video_height = '';
                  var my_index = 0;
                  selection.each(function(attachment) {
                    console.log(attachment);
                    id = attachment['id'];
                    src = attachment.attributes.url;
                    video_width = attachment.attributes.width;
                    video_height = attachment.attributes.height;
                  });
                  self.props.adFormChangeHandler({ target : { name : 'image_src_id' , value : id } });
                  self.props.adFormChangeHandler({ target : { name : 'image_src' , value : src } });                  
                  self.props.adFormChangeHandler({ target : { name : 'image_width' , value : video_width } });                  
                  self.props.adFormChangeHandler({ target : { name : 'image_height' , value : video_height } });                  
               });   
      video_frame.on('open',function() {
              // On open, get the id from the hidden input
              // and select the appropiate images in the media manager
              var selection =  video_frame.state().get('selection');

            });
            console.log("video_frame.open");
          video_frame.open();

    }
    selectimages_2  = (event) => {
      var image_frame_2;

      var self =this;
      if(image_frame_2){
       image_frame_2.open();
      }

      // Define image_frame_2 as wp.media object
      image_frame_2 = wp.media({
                 library : {
                      type : 'image',
                  }
             });
      image_frame_2.on('close',function() {
                  // On close, get selections and save to the hidden input
                  // plus other AJAX stuff to refresh the image preview
                  var selection =  image_frame_2.state().get('selection');
                  var id = '';
                  var src_2 = '';
                  var my_index_2 = 0;
                  selection.each(function(attachment) {
                     id = attachment['id'];
                     src_2 = attachment.attributes.sizes.full.url;
                  });
                  self.props.adFormChangeHandler({ target : { name : 'image_mobile_src' , value : src_2 } });                  
               });   
      image_frame_2.on('open',function() {
              // On open, get the id from the hidden input
              // and select the appropiate images in the media manager
              var selection =  image_frame_2.state().get('selection');

            });
          image_frame_2.open();

    }
    remove_image = (e) => {
    this.props.adFormChangeHandler({ target : { name : 'image_src_id' , value : '' } });
    this.props.adFormChangeHandler({ target : { name : 'image_src' , value : '' } });    

}
    remove_image_2 = (e) => {
    this.props.adFormChangeHandler({ target : { name : 'image_mobile_src' , value : '' } });    

}
    remove_adspace_image = (e) => {
    this.props.adFormChangeHandler({ target : { name : 'ad_space_banner_image_src' , value : '' } });    

}
removeSeleted = (e) => {
      let index = e.currentTarget.dataset.index;  
      const { random_ads_list } = { ...this.state };    
      random_ads_list.splice(index,1);
      this.setState(random_ads_list);

}
removeSeleted_list = (e) => {
      let index = e.currentTarget.dataset.index;  
      const { ads_list } = { ...this.state };    
      ads_list.splice(index,1);
      this.setState(ads_list);

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
          let ad_ids_temp =[];
          Object.entries(result.posts_data).map(([key, value]) => {
          if(value.post_meta['ad_type'] != "random_ads" && value.post_meta['ad_type'] != "rotator_ads" && value.post_meta['ad_type'] != "carousel_ads" && value.post_meta['ad_type'] != "group_insertion" && value.post['post_status'] != "draft")
            getallads_data.push({label: value.post['post_title'], value: value.post['post_id']});
          if(value.post_meta['ad_type'] != "random_ads" && value.post_meta['ad_type'] != "rotator_ads" && value.post_meta['ad_type'] != "carousel_ads" && value.post_meta['ad_type'] != "group_insertion" && value.post['post_status'] == "publish")
            ad_ids_temp.push(value.post['post_id']);
          })      
            this.setState({
            isLoaded: true,
            getallads_data: getallads_data,
            ad_ids_temp: ad_ids_temp,
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

floatingLinkChange = (event,key)=>{
  let tmp_slides=this.state.floating_slides;
tmp_slides[key].link = event.target.value;
this.setState({ floating_slides: tmp_slides,floating_slides_flag :true })
}

selectFloatingSlides = (event,key)=>{
  let tmp_slides=this.state.floating_slides;
  let self=this;
  let image_frame;
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
              selection.each(function(attachment) {
                 id = attachment['id'];
                 src = attachment.attributes.sizes.full.url;
              });

              tmp_slides[key].slide = src; 
              self.setState({ floating_slides: tmp_slides ,floating_slides_flag :true})  
                       
           });   
  image_frame.on('open',function() {
          // On open, get the id from the hidden input
          // and select the appropiate images in the media manager
          var selection =  image_frame.state().get('selection');

        });
      image_frame.open();

          
}

deleteFloatingSlide= (key)=>{
let tmp_slides=this.state.floating_slides;
let filtered = tmp_slides.filter((value,tkey) => {
  return tkey !== key;
});
this.setState({ floating_slides: filtered,floating_slides_flag :true })
}

removeSlideImage= (key)=>{
  let tmp_slides=this.state.floating_slides;
  tmp_slides[key].slide = '#';
  this.setState({ floating_slides: tmp_slides ,floating_slides_flag :true})
  }



addFloatingSlide = (type) =>{
const {__} = wp.i18n;
 let float_final_key=0;
 let tmp_slides =this.state.floating_slides;
 let slides_count =this.state.floating_slides.length;
 return (
  <table style={{width:'100%',marginTop:'10px'}}>
    <tbody>
      {
      tmp_slides.map((value,index) => {
        float_final_key++;
        var del_btn_show= ((slides_count-1)!=index) && index!=5;
        return (
        <tr key={"key_"+index}>
          <td> <label>Slide {index+1}</label></td>
          <td style={{width:'25%'}}>
         { value.slide == '#' ? <div><a className="button" data-slideid={index} onClick={(event) => {this.selectFloatingSlides(event,index)}}> {__('Upload Image', 'quick-adsense-reloaded')}</a>
         </div>
         : <div>
         <div>
         <img src={value.slide} className="banner_image" style={{width:'150px'}}/>
         <a className="button" onClick={() => {this.removeSlideImage(index)}}>{__('Upload Image', 'quick-adsense-reloaded')}</a></div>

         </div>
        }
           </td>
           <td>
          <label>Link {index+1}</label>
          <input value={value.link} onChange={(event) => {this.floatingLinkChange(event,index)}} type="text" id={"floating_link_"+index} name={"floating_link_"+index} placeholder={__('Ad Anchor link', 'quick-adsense-reloaded')} />
          {(value.link == '') ? <div className="quads_form_msg"><span className="material-icons">
          error_outline</span>Enter Ad Anchor link</div> :''}
          
            {(del_btn_show==true || index==5)? <a className="quads-btn quads-btn-primary" onClick={()=>{this.deleteFloatingSlide(index)} } >{__('Remove', 'quick-adsense-reloaded')}</a>: <a className="quads-btn quads-btn-primary add_slider" onClick={()=>{this.addFloatingSlide('add')} } >{__('Add', 'quick-adsense-reloaded')}</a>}
           </td>
           </tr>
        );
           
        
      })}     
{
  type=='add'?(float_final_key<6) ? this.setState({ floating_slides: [...this.state.floating_slides, {"slide":"#","link":"#"} ] ,floating_slides_flag :true}) : alert('You can add 6 slides only'):<tr></tr>
}
</tbody> 
</table>
  );
 
     
}
  addselected_list = (e) => {
    e.preventDefault();  
    let value  = this.state.currentselectedvalue;  
    let label  = this.state.currentselectedlabel;  
  
    if( typeof (value) !== 'undefined' && value != ''){
      const {ads_list} = this.state;
      let data    = ads_list;
      data.push({ value: value,label: label});
      let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);          
      this.setState({ads_list: newData,adsToggle_list : false});    
         
    }       

}
   componentDidMount() {  
          this.getallads(); 
  } 
    selectAdchange = (option) => {    
   
      this.setState({currentselectedlabel: option.label,currentselectedvalue: option.value});

  }
  render() {     
    const get_all_data = JSON.parse(JSON.stringify(this.state.getallads_data));
    var getallads_data_temp = [];
    getallads_data_temp = get_all_data;

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
                        <MSelect  style={{minWidth:'200px'}} value={post_meta.adsense_ad_type} onChange={this.props.adFormChangeHandler} name="adsense_ad_type" id="adsense_ad_type">
                          <MenuItem value="display_ads">{__('Display Ads', 'quick-adsense-reloaded')}</MenuItem>
                          <MenuItem value="in_feed_ads">{__('In-Feed Ads', 'quick-adsense-reloaded')}</MenuItem>
                          <MenuItem value="in_article_ads">{__('In-Article Ads', 'quick-adsense-reloaded')}</MenuItem> 
                          <MenuItem value="adsense_auto_ads">{__('Auto Ads', 'quick-adsense-reloaded')}</MenuItem>
                            {quads_localize_data.is_amp_enable || post_meta.adsense_ad_type == "adsense_sticky_ads" ?    <MenuItem value="adsense_sticky_ads">{__('Sticky (Only AMP)', 'quick-adsense-reloaded')}</MenuItem>:null}
                            <MenuItem value="matched_content">{__('Matched content', 'quick-adsense-reloaded')}</MenuItem>
                        </MSelect>
                      </div>
                    </td></tr> 
                    {post_meta.adsense_ad_type == 'in_feed_ads' ? 
                    <tr><td><label>{__('Data Layout Key', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_layout_key == '') ? 'quads_form_error' : ''} value={post_meta.data_layout_key} placeholder="-ez+4v+7r-fc+65" onChange={this.props.adFormChangeHandler} type="text" id="data_layout_key" name="data_layout_key" />
                    {(show_form_error && post_meta.data_layout_key == '') ? <div className="quads_form_msg"><span className="material-icons">
                      error_outline</span>{__('Enter Data Layout Key', 'quick-adsense-reloaded')}</div> :''} </td></tr>
                      : null }
                    <tr><td><label>{__('Data Client ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.g_data_ad_client == '') ? 'quads_form_error' : ''} value={post_meta.g_data_ad_client} placeholder="ca-pub-2005XXXXXXXXX342" onChange={this.props.adFormChangeHandler} type="text" id="g_data_ad_client" name="g_data_ad_client" />
                    {(show_form_error && post_meta.g_data_ad_client == '') ? <div className="quads_form_msg"><span className="material-icons">
                      error_outline</span>{__('Enter Data Client ID', 'quick-adsense-reloaded')}</div> :''} </td></tr>

                     {post_meta.adsense_ad_type != 'adsense_auto_ads' ? 
                    <tr><td><label>{__('Data Slot ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.g_data_ad_slot == '') ? 'quads_form_error' : ''}  value={post_meta.g_data_ad_slot} onChange={this.props.adFormChangeHandler} type="text" id="g_data_ad_slot" name="g_data_ad_slot" placeholder="70XXXXXX12" />
                    {(show_form_error && post_meta.g_data_ad_slot == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline
                    </span>{__('Enter Data Slot ID', 'quick-adsense-reloaded')}</div> :''}</td></tr>
                      : null }
                      { !post_meta.adsense_ad_type || post_meta.adsense_ad_type == 'display_ads' || post_meta.adsense_ad_type == 'matched_content' || post_meta.adsense_ad_type == 'adsense_sticky_ads' ? (
                    <tr><td><label>{__('Size', 'quick-adsense-reloaded')}</label></td><td>
                      <div>
                        <MSelect style={{minWidth:'200px'}} value={post_meta.adsense_type} onChange={this.props.adFormChangeHandler} name="adsense_type" id="adsense_type">
                        <MenuItem value="normal">{__('Fixed Size', 'quick-adsense-reloaded')}</MenuItem>
                        <MenuItem value="responsive">{__('Responsive', 'quick-adsense-reloaded')}</MenuItem> 
                      </MSelect>
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
                  {(show_form_error && post_meta.code == '') ? <div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Enter Plain Text / HTML / JS', 'quick-adsense-reloaded')}</div> : ''}</td>
                  </tr>
                  <tr>
                  <td><label className='q_img_ma_lab' htmlFor="mobile_image_check">{__('Mobile specific Ad', 'quick-adsense-reloaded')}</label></td>
                  <td>
                  <label className="quads-switch mob_ads_html">
                  <input className='mob_html_check' id="mobile_html_check" checked={post_meta.mobile_html_check} name="mobile_html_check" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                         <span className="quads-slider"></span>
                       </label>
                  </td>
                  </tr>
                   { post_meta.mobile_html_check && post_meta.mobile_html_check == 1 ? <tr><td>
                     <label>{__('Mobile Plain Text / HTML / JS ', 'quick-adsense-reloaded')}</label></td>
                     <td><textarea className={(show_form_error && post_meta.mob_code == '') ? 'quads_form_error' : ''}  cols="50" rows="5" value={post_meta.mob_code} onChange={this.props.adFormChangeHandler} id="mob_code" name="mob_code" />
                     {(show_form_error && post_meta.mob_code == '') ? <div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Enter Plain Text / HTML / JS', 'quick-adsense-reloaded')}</div> : ''}</td>
                      </tr>
                     : ''
                   }
                  </tbody></table>
                  </div>);      
              break;

              case 'parallax_ads':                
                ad_type_name = 'Parallax Ads';
                comp_html.push(<div key="parallax_ads">
                  <table><tbody>
                    <tr>
                      <td><label>{__('Upload Ad Image', 'quick-adsense-reloaded')}</label></td>
                      <td>{post_meta.image_src == '' ? <div><div><a className="button" onClick={this.selectimages}>{__(' Upload Image',   'quick-adsense-reloaded')}</a></div></div>
                      : <div><div><img src={post_meta.image_src} className="banner_image" /><a className="button" onClick={this.remove_image}>{__('Remove Image', 'quick-adsense-reloaded')}</a></div></div>
                      } 
                      {(show_form_error && post_meta.image_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                      error_outline</span>{__('Upload Ad Image', 'quick-adsense-reloaded')}</div> :''}
                      </td>
                    </tr>
                    <tr><td>
                      <label>{__('Ad Button link', 'quick-adsense-reloaded')}</label></td>
                      <td><input value={post_meta.parallax_btn_url} onChange={this.props.adFormChangeHandler} type="text" id="parallax_btn_url" name="parallax_btn_url" placeholder="Ad Button link" />
                        {(show_form_error && post_meta.parallax_btn_url == '') ? <div className="quads_form_msg"><span className="material-icons">
                        error_outline</span>{__('Enter Ad Button link', 'quick-adsense-reloaded')}</div> :''}
                      </td>
                    </tr>
                  </tbody></table>
                </div>);      
              break;

              case 'half_page_ads':                
                ad_type_name = 'Half Page Slider Ad';
                comp_html.push(<div key="half_page_ads">
                  <table><tbody>
                    <tr>
                      <td><label>{__('Upload Ad Image', 'quick-adsense-reloaded')}</label></td>
                      <td>{post_meta.image_src == '' ? <div><div><a className="button" onClick={this.selectimages}>{__(' Upload Image',   'quick-adsense-reloaded')}</a></div></div>
                      : <div><div><img src={post_meta.image_src} className="banner_image" /><a className="button" onClick={this.remove_image}>{__('Remove Image', 'quick-adsense-reloaded')}</a></div></div>
                      } 
                      {(show_form_error && post_meta.image_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                      error_outline</span>{__('Upload Ad Image', 'quick-adsense-reloaded')}</div> :''}
                      </td>
                    </tr>
                    <tr><td>
                      <label>{__('Ad Button link', 'quick-adsense-reloaded')}</label></td>
                      <td><input value={post_meta.half_page_ads_btn_url} onChange={this.props.adFormChangeHandler} type="text" id="half_page_ads_btn_url" name="half_page_ads_btn_url" placeholder="Ad Button link" />
                        {(show_form_error && post_meta.half_page_ads_btn_url == '') ? <div className="quads_form_msg"><span className="material-icons">
                        error_outline</span>{__('Enter Ad Button link', 'quick-adsense-reloaded')}</div> :''}
                      </td>
                    </tr>
                  </tbody></table>
                </div>);      
              break;

              case 'rotator_ads':
                 ad_type_name = 'Rotator Ads';
                 if(!quads_localize_data.is_pro){
                  comp_html.push(<div key="rotator_ads" className="quads-user-targeting"> 
{__('This feature is available in PRO version', 'quick-adsense-reloaded')} <a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">{__('Unlock this feature', 'quick-adsense-reloaded')}</a>
</div>);
                 break;
                 }
                comp_html.push(<div key="rotator_ads" className="quads-user-targeting"> 
       <h2>{__('Select Ads', 'quick-adsense-reloaded')}<a onClick={this.adsToggle_list}><Icon>add_circle</Icon></a>  </h2>

                
             <div className="quads-target-item-list">
              {                
              this.state.ads_list ? 
              this.state.ads_list.map( (item, index) => {
                getallads_data_temp.map( ( item_two,index_two )  => {
                  if( item.value == item_two.value ){
                    item.label = item_two.label;
                  }
                })
                if( this.state.ad_ids_temp /*&& this.state.ad_ids_temp.indexOf(item.value)>=0*/ ) {
                return <div key={index} className="quads-target-item">
                  <span className="quads-target-label xyz">{item.label}</span>
                  <span className="quads-target-icon qaz" onClick={this.removeSeleted_list} data-index={index}><Icon>close</Icon></span> 
                </div>  
                }
              })
              :''}
              <div>{ (this.state.ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Select at least one Ad', 'quick-adsense-reloaded')}</div></span> : ''}</div>
             </div>             
        

        {this.state.adsToggle_list ?
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
           <td><a onClick={this.addselected_list} className="quads-btn quads-btn-primary">{__('Add', 'quick-adsense-reloaded')}</a></td>
           </tr>
         </tbody> 
        </table>
        </div>
        : ''}
       </div>);      
              break;


              case 'group_insertion':
                  ad_type_name = 'Group Insertion';
                  if(!quads_localize_data.is_pro){
                      comp_html.push(<div key="group_insertion" className="quads-user-targeting">
                         {__('This feature is available in PRO version', 'quick-adsense-reloaded')}  <a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">{__('Unlock this feature', 'quick-adsense-reloaded')}</a>
                      </div>);
                      break;
                  }
                  comp_html.push(<div key="group_insertion" className="quads-user-targeting">
                      <h2>{__('Select Ads', 'quick-adsense-reloaded')}<a onClick={this.adsToggle_list}><Icon>add_circle</Icon></a>  </h2>


                      <div className="quads-target-item-list">
                          {
                              this.state.ads_list ?
                                  this.state.ads_list.map( (item, index) => (
                                      <div key={index} className="quads-target-item">
                                          <span className="quads-target-label">{item.label}</span>
                                          <span className="quads-target-icon" onClick={this.removeSeleted_list} data-index={index}><Icon>close</Icon></span>
                                      </div>
                                  ) )
                                  :''}
                          <div>{ (this.state.ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Select at least one Ad', 'quick-adsense-reloaded')}</div></span> : ''}</div>
                      </div>


                      {this.state.adsToggle_list ?
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
                                      <td><a onClick={this.addselected_list} className="quads-btn quads-btn-primary">{__('Add', 'quick-adsense-reloaded')}</a></td>
                                  </tr>
                                  </tbody>
                              </table>
                          </div>
                          : ''}
                  </div>);
                  break;



               case 'random_ads':
                 ad_type_name = 'Random Ads';
                comp_html.push(<div key="random_ads" className="quads-user-targeting"> 
       <h2>{__('Select Ads', 'quick-adsense-reloaded')}<a onClick={this.adsToggle}><Icon>add_circle</Icon></a>  </h2>

                
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
              <div>{ (this.state.random_ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Select at least one Ad', 'quick-adsense-reloaded')}</div></span> : ''}</div>
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
           <td><a onClick={this.addselected} className="quads-btn quads-btn-primary">{__('Add', 'quick-adsense-reloaded')}</a></td>
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
                    error_outline</span>{__('Enter Network Code', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                    <tr><td><label>{__('AD Unit Name', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.ad_unit_name == '') ? 'quads_form_error' : ''}  value={post_meta.ad_unit_name} onChange={this.props.adFormChangeHandler} type="text" placeholder="AD Unit Name" id="ad_unit_name" name="ad_unit_name" />
                    {(show_form_error && post_meta.ad_unit_name == '') ? <div className="quads_form_msg"><span className="material-icons">
error_outline
</span>{__('Enter AD Unit Name', 'quick-adsense-reloaded')}</div> :''}</td></tr>
                    <tr><td><label>{__('Size', 'quick-adsense-reloaded')}</label></td><td>
                      <div>
                      <MSelect style={{minWidth:'200px'}} value={post_meta.adsense_type} onChange={this.props.adFormChangeHandler} name="adsense_type" id="adsense_type">
                        <MenuItem value="normal">{__('Fixed Size', 'quick-adsense-reloaded')}</MenuItem>
                        <MenuItem value="responsive">{__('Responsive', 'quick-adsense-reloaded')}</MenuItem> 
                      </MSelect>
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
                    error_outline</span>{__('Enter Block Id', 'quick-adsense-reloaded')}</div> :''}
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
                    error_outline</span>{__('Enter Data Container', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                           <tr><td>
                    <label>{__('Data Js Src', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_js_src == '') ? 'quads_form_error' : ''} value={post_meta.data_js_src} onChange={this.props.adFormChangeHandler} type="text" id="data_js_src" name="data_js_src" placeholder="//jsc.mgid.com/a/m/quads.com.123645.js" />
                    {(show_form_error && post_meta.data_js_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter Data Js Src', 'quick-adsense-reloaded')}</div> :''}
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
                   {post_meta.image_src == '' ? <div><div><a className="button" onClick={this.selectimages}>{__(' Upload Banner', 'quick-adsense-reloaded')}</a>
                   </div>
                   </div>
                   : <div>
                   <div>
                   <img src={post_meta.image_src} className="banner_image" />
                   <a className="button" onClick={this.remove_image}>{__('Remove Banner', 'quick-adsense-reloaded')}</a></div>

                   </div>
                  } 
                    {(show_form_error && post_meta.image_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Upload Ad Image', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                      {post_meta.image_src == '' ? '' : <tr>
                      <td><label>{__('Size', 'quick-adsense-reloaded')}</label></td>
                      <td>
                      <div className="quads-banner-width-heigth">
                        <div className="quads-banner-width">
                          <label>{__('Width', 'quick-banner-reloaded')}
                          <input value={post_meta.banner_ad_width?post_meta.banner_ad_width:this.state.width} onChange={this.props.adFormChangeHandler} type="number" id="banner_ad_width" name="banner_ad_width" placeholder="300"/> 
                          </label>
                        </div>
                        <div className="quads-banner-height">
                          <label>{__('Height', 'quick-banner-reloaded')}
                          <input value={post_meta.banner_ad_height?post_meta.banner_ad_height:this.state.height} onChange={this.props.adFormChangeHandler} type="number" id="banner_ad_height" name="banner_ad_height" placeholder="300"/>  
                          </label>
                        </div>
                      </div>
                     </td></tr>
                     }
                     <tr>
                     <td><label className='q_img_ma_lab' htmlFor="mobile_image_check">{__('Mobile specific Banner', 'quick-adsense-reloaded')}</label></td>
                     <td>
                     <label className="quads-switch mob_ads_image">
                     <input className='mob_img_check' id="mobile_image_check" checked={post_meta.mobile_image_check} name="mobile_image_check" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                            <span className="quads-slider"></span>
                          </label>
                     </td>
                     </tr>
                     
                      { post_meta.mobile_image_check && post_meta.mobile_image_check == 1 ? <><tr><td>
                        <label>{__('Upload Mobile AD Banner ', 'quick-adsense-reloaded')}</label></td><td>
                       {post_meta.image_mobile_src == '' ? <div><a className="button" onClick={this.selectimages_2}>{__(' Upload Banner', 'quick-adsense-reloaded')}</a></div>
                       : <div>
                       <img src={post_meta.image_mobile_src} className="banner_image" />
                       <a className="button" onClick={this.remove_image_2}>{__('Remove Banner', 'quick-adsense-reloaded')}</a></div>}
                         
                          
                        {(show_form_error && post_meta.image_mobile_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                        error_outline</span>{__('Upload Mobile AD Banner', 'quick-adsense-reloaded')} </div> :''}
                         </td></tr>
                        {(post_meta.image_mobile_src != '') ?
                        <tr>
                         <td><label>{__('Mobile Size', 'quick-adsense-reloaded')}</label></td>
                         <td>
                         <div className="quads-banner-width-heigth">
                           <div className="quads-banner-width">
                             <label>{__('Width', 'quick-banner-reloaded')}
                             <input value={post_meta.mob_banner_ad_width} onChange={this.props.adFormChangeHandler} type="number" id="mob_banner_ad_width" name="mob_banner_ad_width" placeholder="300"/> 
                             </label>
                           </div>
                           <div className="quads-banner-height">
                             <label>{__('Height', 'quick-banner-reloaded')}
                             <input value={post_meta.mob_banner_ad_height} onChange={this.props.adFormChangeHandler} type="number" id="mob_banner_ad_height" name="mob_banner_ad_height" placeholder="300"/>  
                             </label>
                           </div>
                         </div>
                        </td></tr> : '' }
                        </>
                        : ''
                      }
                     <tr><td>
                    <label>{__('Ad Anchor link', 'quick-adsense-reloaded')}</label></td><td>
                    <input value={post_meta.image_redirect_url} onChange={this.props.adFormChangeHandler} type="text" id="image_redirect_url" name="image_redirect_url" placeholder="Ad Anchor link" />
                    {(show_form_error && post_meta.image_redirect_url == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter Ad Anchor link', 'quick-adsense-reloaded')}</div> :''}
                    <label className="quads-switch quads_url_nofollow">
                     <input className='add_url_nofollow' id="add_url_nofollow" checked={post_meta.add_url_nofollow} name="add_url_nofollow" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                      <span className="quads-slider"></span></label>  <b>{__('Add rel="NoFollow"', 'quick-adsense-reloaded')}</b>
                     </td></tr>
                    <tr>
                      <td><label className='q_img_ma_lab' htmlFor="parallax_ads_check">{__('Parallax Effect', 'quick-adsense-reloaded')} </label></td>
                      <td>
                        <label>
                        <input className='parallax_ads_check' id="parallax_ads_check" checked={post_meta.parallax_ads_check} name="parallax_ads_check" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                            </label>
                      </td>
                    </tr>
                    { post_meta.parallax_ads_check && post_meta.parallax_ads_check == 1 ? <tr><td>
                        <label>{__('Banner height ', 'quick-adsense-reloaded')}</label></td><td>
                        <input className='parallax_height small-text' min="1" step="1" id="parallax_height" value={post_meta.parallax_height} name="parallax_height" onChange={this.props.adFormChangeHandler} type="number"/>
                         </td></tr>
                        : ''
                    }
                  </tbody>
                </table>
                </div>);

              break;
            case 'video_ads':
             ad_type_name = 'Video';  
              comp_html.push(<div key="video_ads">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Upload A Video', 'quick-adsense-reloaded')}</label></td><td>
                   {post_meta.image_src == '' ? <div><div><a className="button" onClick={this.selectvideo}>{__(' Upload Video', 'quick-adsense-reloaded')}</a>
                   </div>
                   </div>
                   : <div>
                   <div>
                   <video src={post_meta.image_src} className="banner_image" />
                   <a className="button" onClick={this.remove_image}>{__('Remove Video', 'quick-adsense-reloaded')}</a>
                   <p className={'p_q_video'}>{__('We recommend try keeping videos to under 15 seconds as much as possible & if size\'s smaller please donot upload HD Quality', 'quick-adsense-reloaded')}</p>
                   </div>                   

                   </div>
                  }
                    {(show_form_error && post_meta.image_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Upload A Video', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                     <tr><td>
                    <label>{__('Ad Anchor link', 'quick-adsense-reloaded')}</label></td><td>
                    <input value={post_meta.image_redirect_url} onChange={this.props.adFormChangeHandler} type="text" id="image_redirect_url" name="image_redirect_url" placeholder="Ad Anchor link" />
                    {(show_form_error && post_meta.image_redirect_url == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter Ad Anchor link', 'quick-adsense-reloaded')}</div> :''}
                    <label className="quads-switch quads_url_nofollow">
                     <input className='add_url_nofollow' id="add_url_nofollow" checked={post_meta.add_url_nofollow} name="add_url_nofollow" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                            <span className="quads-slider"></span>
                           </label>  <b>{__('Add rel="NoFollow"', 'quick-adsense-reloaded')}</b>
                     </td></tr>
                     <tr><td>
                    <label>{__('Video Size', 'quick-adsense-reloaded')}</label></td><td>
                          <label>
                          <input value={post_meta.video_width} onChange={this.props.adFormChangeHandler} type="number" id="video_width" name="video_width" /> {__('Width(PX)', 'quick-adsense-reloaded')}
                          </label>
                     </td></tr>
                  </tbody>
                </table>
                </div>);
              break;
              case 'propeller':
             ad_type_name = 'Propeller';  
              comp_html.push(<div key="propeller">
                <table>
                  <tbody>
                           <tr><td>
                    <label>{__('Propeller AD Script', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.propeller_js == '') ? 'quads_form_error' : ''} value={post_meta.propeller_js} onChange={this.props.adFormChangeHandler} type="text" id="propeller_js" name="propeller_js" placeholder="" />
                    {(show_form_error && post_meta.propeller_js == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter AD Script', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);

              break;
            case 'ab_testing':
              ad_type_name = 'AB Testing';
              if(!quads_localize_data.is_pro){
                comp_html.push(<div key="ab_testing" className="quads-user-targeting"> 
{__('This feature is available in PRO version', 'quick-adsense-reloaded')} <a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">{__('Unlock this feature', 'quick-adsense-reloaded')} </a>
</div>);
               break;
               }

              comp_html.push(<div key="ab_testing" className="quads-user-targeting">
                {this.state.ads_list.length == 0  ?
                <h2>{__('Select Ads', 'quick-adsense-reloaded')}<a onClick={this.adsToggle_list}><Icon>add_circle</Icon></a>  </h2>
                :null}
                <div className="quads-target-item-list">
                  {
                    this.state.ads_list ?
                      this.state.ads_list.map((item, index) => (
                        <div key={index} className="quads-target-item">
                          <span className="quads-target-label">{item.label}</span>
                          <span className="quads-target-icon" onClick=
                          {this.removeSeleted_list} data-index={index}><Icon>close</Icon></span>
                        </div>
                      ))
                      : ''}
                  <div>{(this.state.ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Select at least one Ad', 'quick-adsense-reloaded')}</div></span> : ''}</div>
                </div>
                {this.state.adsToggle_list ?
                  <div className="quads-targeting-selection">
                    <table className="form-table">
                      <tbody>
                        <tr>
                          <td>
                            <Select
                              name="userTargetingIncludedType"
                              placeholder="Select Ads"
                              options={this.state.getallads_data_temp}
                              value={this.multiTypeLeftIncludedValue}
                              onChange={this.selectAdchange}
                            />
                          </td>
                          <td><a onClick={this.addselected_list} className="quads-btn quads-btn-primary">{__('Add', 'quick-adsense-reloaded')}</a></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  : ''}
              </div>);
              break;
            case 'popup_ads':
              ad_type_name = 'popup ads';
              comp_html.push(<div key="popup_ads" className="quads-user-targeting">
                {this.state.ads_list.length == 0  ?
                <h2>{__('Select Ads', 'quick-adsense-reloaded')}<a onClick={this.adsToggle_list}><Icon>add_circle</Icon></a>  </h2>
                :null}
                <div className="quads-target-item-list">
                  {
                    this.state.ads_list ?
                      this.state.ads_list.map((item, index) => (
                        <div key={index} className="quads-target-item">
                          <span className="quads-target-label">{item.label}</span>
                          <span className="quads-target-icon" onClick={this.removeSeleted_list} data-index={index}><Icon>close</Icon></span>
                        </div>
                      ))
                      : ''}
                  {this.state.ads_list.length == 0  ? '' :
                  <div className="quads-popup-cookie">
                    <table className="form-table">
                      <tbody>
                        <tr style={{marginBottom: 0 + 'px'}}>
                          <td><label className='popup_cookie_type' htmlFor="popup_set_cookie_type">{__('Cookie Setup', 'quick-adsense-reloaded')}</label></td>
                          <td style={{width:'150px'}}>
                             <select value={post_meta.popup_set_cookie_type} defaultValue="withcookieexp" onChange={this.props.adFormChangeHandler} name="popup_set_cookie_type" id="popup_set_cookie_type">
                              <option value="withcookieexp">{__('Expiry', 'quick-adsense-reloaded')}</option>
                              <option value="withoutcookieexp">{__('No Expiry', 'quick-adsense-reloaded')}</option> 
                            </select>
                          </td>
                        </tr>
                        { post_meta.popup_set_cookie_type && post_meta.popup_set_cookie_type == 'withoutcookieexp' ? '' :
                        <tr>
                          <td></td>
                          <td style={{width:'315px'}}>
                            <input id={'pop_set_cookie_indays'}
                                   name={'pop_set_cookie_indays'} type="number"
                                   value={post_meta.pop_set_cookie_indays} onChange={this.props.adFormChangeHandler}  /> days
                          </td>
                        </tr>
                        }
                      </tbody>
                    </table>
                  </div>}
                  <div>{(this.state.ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Select at least one Ad', 'quick-adsense-reloaded')}</div></span> : ''}</div>
                </div>
                {this.state.adsToggle_list ?
                  <div className="quads-targeting-selection">
                    <table className="form-table">
                      <tbody>
                        <tr>
                          <td>
                            <Select
                              name="userTargetingIncludedType"
                              placeholder="Select Ads"
                              options={this.state.getallads_data_temp}
                              value={this.multiTypeLeftIncludedValue}
                              onChange={this.selectAdchange}
                            />
                          </td>
                          <td><a onClick={this.addselected_list} className="quads-btn quads-btn-primary">{__('Add', 'quick-adsense-reloaded')}</a></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  : ''}
              </div>);
              break;
              case 'ad_blindness':
              ad_type_name = 'Ad Blindness';
              if(!quads_localize_data.is_pro){
                comp_html.push(<div key="ad_blindness" className="quads-user-targeting"> 
{__('This feature is available in PRO version', 'quick-adsense-reloaded')} <a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">{__('Unlock this feature', 'quick-adsense-reloaded')}</a>
</div>);
               break;
               }

              comp_html.push(<div key="ad_blindness" className="quads-user-targeting">
                {this.state.ads_list.length == 0  ?
                <h2>{__('Select Ads', 'quick-adsense-reloaded')}<a onClick={this.adsToggle_list}><Icon>add_circle</Icon></a>  </h2>
                :null}
                <div className="quads-target-item-list">
                  {
                    this.state.ads_list ?
                      this.state.ads_list.map((item, index) => (
                        <div key={index} className="quads-target-item">
                          <span className="quads-target-label">{item.label}</span>
                          <span className="quads-target-icon" onClick={this.removeSeleted_list} data-index={index}><Icon>close</Icon></span>
                        </div>
                      ))
                      : ''}
                  <div>{(this.state.ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Select at least one Ad', 'quick-adsense-reloaded')}</div></span> : ''}</div>
                </div>
                {this.state.adsToggle_list ?
                  <div className="quads-targeting-selection">
                    <table className="form-table">
                      <tbody>
                        <tr>
                          <td>
                            <Select
                              name="userTargetingIncludedType"
                              placeholder="Select Ads"
                              options={this.state.getallads_data_temp}
                              value={this.multiTypeLeftIncludedValue}
                              onChange={this.selectAdchange}
                            />
                          </td>
                          <td><a onClick={this.addselected_list} className="quads-btn quads-btn-primary">{__('Add', 'quick-adsense-reloaded')}</a></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  : ''}
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
                    error_outline</span>{__('Enter Data Publisher Id', 'quick-adsense-reloaded')}</div> :''}
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
                    error_outline</span>{__('Enter Data CID', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                     <tr><td>
                    <label>{__('Data CRID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_crid == '') ? 'quads_form_error' : ''} value={post_meta.data_crid} onChange={this.props.adFormChangeHandler} type="text" id="data_crid" name="data_crid" placeholder="1XXXXXX82" />
                    {(show_form_error && post_meta.data_crid == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter Data CRID', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                     <tr><td><label>{__('Size', 'quick-adsense-reloaded')}</label></td><td>
                      <div>
                      <MSelect style={{minWidth:'200px'}} value={post_meta.adsense_type} onChange={this.props.adFormChangeHandler} name="adsense_type" id="adsense_type">
                        <MenuItem value="normal">{__('Fixed Size', 'quick-adsense-reloaded')}</MenuItem>
                        <MenuItem value="responsive">{__('Responsive', 'quick-adsense-reloaded')}</MenuItem> 
                      </MSelect>
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

              case 'adpushup':
             ad_type_name = 'AdPushup';  
              comp_html.push(<div key="adpushup">
                <table>
                  <tbody>
                      <tr><td>
                    <label>{__('Adpushup Site ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.adpushup_site_id == '') ? 'quads_form_error' : ''} value={post_meta.adpushup_site_id} onChange={this.props.adFormChangeHandler} type="text" id="adpushup_site_id" name="adpushup_site_id" placeholder="42844" />
                    {(show_form_error && post_meta.adpushup_site_id == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter Adpushup Site ID', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                     <tr><td>
                    <label>{__('Adpushup Slot ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.adpushup_slot_id == '') ? 'quads_form_error' : ''} value={post_meta.adpushup_slot_id} onChange={this.props.adFormChangeHandler} type="text" id="adpushup_slot_id" name="adpushup_slot_id" placeholder="/103512698/AMP_COMPONENT_TEST_1" />
                    {(show_form_error && post_meta.adpushup_slot_id == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter Adpushup Slot ID', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                     <tr><td><label>{__('Size', 'quick-adsense-reloaded')}</label></td><td>
                      <div>
                      <MSelect style={{minWidth:'200px'}} value={post_meta.adsense_type} onChange={this.props.adFormChangeHandler} name="adsense_type" id="adsense_type">
                        <MenuItem value="normal">{__('Fixed Size', 'quick-adsense-reloaded')}</MenuItem>
                        <MenuItem value="responsive">{__('Responsive', 'quick-adsense-reloaded')}</MenuItem> 
                      </MSelect>
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
                    error_outline</span>{__('Enter Data Site Id', 'quick-adsense-reloaded')}</div> :''}
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
                    error_outline</span>{__('Enter Widget Id\'s', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);
              break;
             case 'infolinks':
             ad_type_name = 'Infolinks';  
              comp_html.push(<div key="infolinks">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Infolinks P ID', 'quick-adsense-reloaded')}</label></td><td>
                   <div> <input value={post_meta.infolinks_pid} onChange={this.props.adFormChangeHandler} type="text" id="infolinks_pid" name="infolinks_pid" /></div>
                    {(show_form_error && post_meta.infolinks_pid == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter Infolinks P ID', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                      <tr><td>
                    <label>{__('Infolinks W S ID', 'quick-adsense-reloaded')}</label></td><td>
                   <div> <input value={post_meta.infolinks_wsid} onChange={this.props.adFormChangeHandler} type="text" id="infolinks_wsid" name="infolinks_wsid" /></div>
                    {(show_form_error && post_meta.infolinks_wsid == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter Infolinks W S ID', 'quick-adsense-reloaded')}</div> :''}
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
                    error_outline</span>{__('Upload Ad Image', 'quick-adsense-reloaded')}</div> :''}
                     </td></tr>
                     <tr><td>
                    <label>{__('Ad Anchor link', 'quick-adsense-reloaded')} </label></td><td>
                    <input value={post_meta.image_redirect_url} onChange={this.props.adFormChangeHandler} type="text" id="image_redirect_url" name="image_redirect_url" placeholder="Ad Anchor link" />
                    {(show_form_error && post_meta.image_redirect_url == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter Ad Anchor link', 'quick-adsense-reloaded')}</div> :''}
                    <label className="quads-switch quads_url_nofollow">
                     <input className='add_url_nofollow' id="add_url_nofollow" checked={post_meta.add_url_nofollow} name="add_url_nofollow" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                            <span className="quads-slider"></span>
                            </label> <b>{__('Add rel="NoFollow"', 'quick-adsense-reloaded')}</b>
                     </td></tr>
                  </tbody>
                </table>
                </div>);
              break;
              case 'skip_ads':
                ad_type_name = 'Skip Ads';  
                 comp_html.push(<div key="skip_ads">
                   <table>
                     <tbody>
                       <tr><td>
                       <label>{__('Skip Ads Type', 'quick-adsense-reloaded')}</label></td><td>
                       <select value={post_meta.skip_ads_type} onChange={this.props.adFormChangeHandler} name="skip_ads_type" id="skip_ads_type">
                        <option value="image_banner">{__('Image Banner', 'quick-adsense-reloaded')}</option>
                        <option value="custom_html">{__('Custom HTML', 'quick-adsense-reloaded')}</option> 
                      </select>
                        </td></tr>
                        <tr><td>
                    <label>{__('Frequency by page view', 'quick-adsense-reloaded')}</label></td><td>
                    <input value={post_meta.freq_page_view} onChange={this.props.adFormChangeHandler} type="number" id="freq_page_view" name="freq_page_view" placeholder="Ad Frequency" />
                     </td></tr>
                     <tr><td>
                    <label>{__('Ad Waiting Time', 'quick-adsense-reloaded')}</label></td><td>
                    <input value={post_meta.ad_wt_time} onChange={this.props.adFormChangeHandler} type="number" id="ad_wt_time" name="ad_wt_time" placeholder="Ad Waiting Time" />
                     </td></tr>
                        {post_meta.skip_ads_type == 'image_banner' ? <>
                        <tr><td>
                    <label>{__('Upload Ad Banner', 'quick-adsense-reloaded')}</label></td><td>
                   {post_meta.image_src == '' ? <div><a className="button" onClick={this.selectimages}>{__(' Upload Banner', 'quick-adsense-reloaded')}</a></div>
                   : <div>
                   <img src={post_meta.image_src} className="banner_image" />
                   <a className="button" onClick={this.remove_image}>{__('Remove Banner', 'quick-adsense-reloaded')}</a></div>}
                     
                      
                    {(show_form_error && post_meta.image_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Upload Ad Image', 'quick-adsense-reloaded')}Upload Ad Image</div> :''}
                     </td></tr>
                     <tr><td>
                    <label>{__('Ad Anchor link', 'quick-adsense-reloaded')}</label></td><td>
                    <input value={post_meta.image_redirect_url} onChange={this.props.adFormChangeHandler} type="text" id="image_redirect_url" name="image_redirect_url" placeholder="Ad Anchor link" />
                    {(show_form_error && post_meta.image_redirect_url == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>{__('Enter Ad Anchor link', 'quick-adsense-reloaded')}</div> :''}
                    <label className="quads-switch quads_url_nofollow">
                     <input className='add_url_nofollow' id="add_url_nofollow" checked={post_meta.add_url_nofollow} name="add_url_nofollow" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                            <span className="quads-slider"></span>
                           </label>  <b>{__('Add rel="NoFollow"', 'quick-adsense-reloaded')}</b>
                     </td></tr>
                     </>
                     :<><tr>
                     <td><label>{__('Plain Text / HTML / JS', 'quick-adsense-reloaded')}</label></td> 
                     <td><textarea className={(show_form_error && post_meta.code == '') ? 'quads_form_error' : ''}  cols="50" rows="5" value={post_meta.code} onChange={this.props.adFormChangeHandler} id="code" name="code" />
                     {(show_form_error && post_meta.code == '') ? <div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Enter Plain Text / HTML / JS', 'quick-adsense-reloaded')}</div> : ''}</td>
                     </tr></>
                      }
                     
                     </tbody>
                   </table>
                   </div>);
   
                 break;
                 case 'loop_ads':
                  ad_type_name = 'Loop';
                   comp_html.push(<div key="loop_ads">
                     <table>
                       <tbody>
                         <tr><td>
                         <label>{__('Upload Ad Image', 'quick-adsense-reloaded')}</label></td><td>
                        {post_meta.image_src == '' ? <div><div><a className="button" onClick={this.selectimages}>{__(' Upload Image', 'quick-adsense-reloaded')}</a>
                        </div>
                        </div>
                        : <div>
                        <div>
                        <img src={post_meta.image_src} className="banner_image" />
                        <a className="button" onClick={this.remove_image}>{__('Remove Image', 'quick-adsense-reloaded')}</a></div>
     
                        </div>
                       }
                          </td></tr>
                          <tr><td>
                         <label>{__('Ad Title', 'quick-adsense-reloaded')}</label></td><td>
                         <input value={post_meta.loop_add_title} onChange={this.props.adFormChangeHandler} type="text" id="loop_add_title" name="loop_add_title" placeholder="Ad title" />
                         {(show_form_error && post_meta.loop_add_title == '') ? <div className="quads_form_msg"><span className="material-icons">
                         error_outline</span>{__('Enter Ad Title', 'quick-adsense-reloaded')}</div> :''}
                          </td></tr>
                          <tr><td>
                         <label>{__('Ad Short Description', 'quick-adsense-reloaded')}</label></td><td>
                         <textarea rows="5" cols="50" onChange={this.props.adFormChangeHandler}  id="loop_add_description" name="loop_add_description" placeholder="Ad Short Description" value={post_meta.loop_add_description}></textarea>
                         {(show_form_error && post_meta.loop_add_description == '') ? <div className="quads_form_msg"><span className="material-icons">
                         error_outline</span>{__('Enter Ad Description', 'quick-adsense-reloaded')}</div> :''}
                          </td></tr>
                          <tr><td>
                         <label>{__('Ad Anchor link', 'quick-adsense-reloaded')}</label></td><td>
                         <input value={post_meta.loop_add_link} onChange={this.props.adFormChangeHandler} type="text" id="loop_add_link" name="loop_add_link" placeholder="Ad Anchor link" />
                         {(show_form_error && post_meta.loop_add_link == '') ? <div className="quads_form_msg"><span className="material-icons">
                         error_outline</span>{__('Enter Ad Anchor link', 'quick-adsense-reloaded')}</div> :''}
                         <label className="quads-switch quads_url_nofollow">
                            <input className='add_url_nofollow' id="add_url_nofollow" checked={post_meta.add_url_nofollow} name="add_url_nofollow" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                            <span className="quads-slider"></span></label> <b>{__('Add rel="NoFollow"', 'quick-adsense-reloaded')}</b>
                          </td></tr>
                       </tbody>
                     </table>
                     </div>);
     
                  break;
                  case 'carousel_ads':
                  ad_type_name = 'Carousel';
                    comp_html.push(<div key="carousel_ads">
                      <table>
                        <tbody>
                        <tr><td>
                          <label>{__('Carousel Type', 'quick-adsense-reloaded')}</label></td><td>
                            <select onChange={this.props.adFormChangeHandler} value={post_meta.carousel_type} id="carousel_type" name="carousel_type" placeholder="Carousel Type">
                              <option value="slider">{__('Single Slide', 'quick-adsense-reloaded')}</option>
                            </select>
                          {(show_form_error && post_meta.carousel_type == '') ? <div className="quads_form_msg"><span className="material-icons">
                          error_outline</span>{__('Select Carousel Type', 'quick-adsense-reloaded')}</div> :''}
                          </td></tr>
                          
                         <tr><td> <label>{__('Carousel Speed', 'quick-adsense-reloaded')}</label></td><td>
                        <div className="quads-adsense-width-heigth"><label>{__('Seconds', 'quick-adsense-reloaded')}<input className='carousel_speed small-text' min="1" step="1" id="carousel_speed" value={post_meta.carousel_speed} name="carousel_speed" onChange={this.props.adFormChangeHandler} type="number"/></label></div>
                         </td></tr>
                         <tr>
                        <td><label className='carousel_arrows' htmlFor="carousel_arrows">{__('Navigation Arrows', 'quick-adsense-reloaded')}</label></td>
                        <td>
                        <label className="quads-switch exp_date">
                        <input className='exp_date_check' id="carousel_arrows" checked={post_meta.carousel_arrows} name="carousel_arrows" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                            <span className="quads-slider"></span>
                          </label>
                        </td>
                        </tr>
                        <tr>
                        <td><label className='carousel_rndms' htmlFor="carousel_rndms">{__('Show Randomly?', 'quick-adsense-reloaded')}</label></td>
                        <td>
                        <label className="quads-switch exp_date">
                        <input className='exp_date_check' id="carousel_rndms" checked={post_meta.carousel_rndms} name="carousel_rndms" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                            <span className="quads-slider"></span>
                          </label>
                        </td>
                        </tr>
                        <tr>
                        <td><label className='carousel_close' htmlFor="carousel_close">{__('Show Close Button', 'quick-adsense-reloaded')}</label></td>
                        <td>
                        <label className="quads-switch exp_date">
                        <input className='exp_date_check' id="carousel_close" checked={post_meta.carousel_close} name="carousel_close" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                            <span className="quads-slider"></span>
                          </label>
                        </td>
                        </tr>
                         <tr style={{marginBottom: 0 + 'px'}}><td><label>{__('Select Ads ', 'quick-adsense-reloaded')}</label></td><td><a onClick={this.adsToggle_list}><Icon>add_circle</Icon></a></td></tr>
                        <tr><td colSpan={'2'} style={{width:'100%'}}>
                        {this.state.ads_list ?
                        <DragDropContext onDragEnd={this.handleDrop}>
                          <Droppable droppableId="quads-target-item-list">
                              {(provided) => (
                                <div
                                  className="quads-target-item-list"
                                  {...provided.droppableProps}
                                  ref={provided.innerRef}
                                >
                                  {this.state.ads_list.map((item, index) => (
                                    <Draggable key={index} draggableId={'ad'+index} index={index}>
                                      {(provided) => (
                                        <div
                                          className="quads-target-item"
                                          ref={provided.innerRef}
                                          {...provided.dragHandleProps}
                                          {...provided.draggableProps}
                                        >
                                          <span className="quads-target-label">{item.label}</span>
                                          <span className="quads-target-icon" onClick={this.removeSeleted_list} data-index={index}><Icon>close</Icon></span>
                                        </div>
                                      )}
                                      </Draggable>
                                  ))}
                                  {provided.placeholder}
                                </div>
                              )}
                          </Droppable>
                        </DragDropContext>
                      :''}
                      <div>{ (this.state.ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Select at least one Ad', 'quick-adsense-reloaded')}</div></span> : ''}</div>
                      </td></tr>
                        </tbody>
                      </table>
            
                      {this.state.adsToggle_list ?
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
                                      <td><a onClick={this.addselected_list} className="quads-btn quads-btn-primary">{__('Add', 'quick-adsense-reloaded')}</a></td>
                                  </tr>
                                  </tbody>
                              </table>
                          </div>
                          : ''}


                      </div>);
      
                    break;
                    case 'sticky_scroll':
                  ad_type_name = 'Hold on Scroll ';
                  if(!quads_localize_data.is_pro){
                      comp_html.push(<div key="sticky_scroll" className="quads-user-targeting">
                          {__('This feature is available in PRO version', 'quick-adsense-reloaded')} <a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">{__('Unlock this feature', 'quick-adsense-reloaded')}</a>
                      </div>);
                      break;
                  }
                  comp_html.push(<div key="sticky_scroll" className="quads-user-targeting">
                    <table>
                    <tbody>
                          <tr style={{marginBottom: 0 + 'px'}}><td><label>{__('Select Ads', 'quick-adsense-reloaded')} </label></td> <td><a onClick={this.adsToggle_list}><Icon>add_circle</Icon></a> </td></tr>
                          <tr><td colSpan={'2'} style={{width:'100%'}}>
                            <div className="quads-target-item-list">
                          {
                              this.state.ads_list ?
                                  this.state.ads_list.map( (item, index) => (
                                      <div key={index} className="quads-target-item">
                                          <span className="quads-target-label">{item.label}</span>
                                          <span className="quads-target-icon" onClick={this.removeSeleted_list} data-index={index}><Icon>close</Icon></span>
                                      </div>
                                  ) )
                                  :''}
                                  
                                  </div>
                          <div>{ (this.state.ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Select at least one Ad', 'quick-adsense-reloaded')}</div></span> : ''}</div>
                          
                      </td></tr>
                      </tbody>
                      </table>
                      {this.state.adsToggle_list ?
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
                                      <td><a onClick={this.addselected_list} className="quads-btn quads-btn-primary">{__('Add', 'quick-adsense-reloaded')}</a></td>
                                  </tr>
                                  </tbody>
                              </table>
                          </div>
                          : ''}
                      
                      <table>
                        <tbody>
                      <tr>
                          <td><label>{__('Scroll Height', 'quick-adsense-reloaded')}  </label></td>
                          <td><input className='small-text' value={post_meta.sticky_scroll_height ? post_meta.sticky_scroll_height:'350'} onChange={this.props.adFormChangeHandler} type="number" id="sticky_scroll_height" name="sticky_scroll_height" />
                          </td></tr>
                          </tbody>
                      </table>
                    
                  </div>);
                  break;
                  case 'floating_cubes':
                  ad_type_name = 'Floating';
                  if(!quads_localize_data.is_pro){
                    comp_html.push(<div key="floating_cubes" className="quads-user-targeting">
                        {__('This feature is available in PRO version', 'quick-adsense-reloaded')} <a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">{__('Unlock this feature', 'quick-adsense-reloaded')}</a>
                    </div>);
                    break;
                }
                    comp_html.push(<div key="floating_cubes">
                      <table >
                        <tbody>
                          <tr><td>
                          <label>{__('Floating Position ', 'quick-adsense-reloaded')}</label></td><td>
                            <select onChange={this.props.adFormChangeHandler} value={post_meta.floating_position} id="floating_position" name="floating_position" placeholder="Floating Position">
                              <option value="top-left">{__('Top Left', 'quick-adsense-reloaded')}</option>
                              <option value="top-right">{__('Top Right', 'quick-adsense-reloaded')}</option>
                              <option value="bottom-left">{__('Bottom Left', 'quick-adsense-reloaded')}</option>
                              <option value="bottom-right">{__('Bottom Right', 'quick-adsense-reloaded')}</option>
                            </select>
                          {(show_form_error && post_meta.floating_position == '') ? <div className="quads_form_msg"><span className="material-icons">
                          error_outline</span>{__('Select Floating Position', 'quick-adsense-reloaded')}</div> :''}
                          </td></tr>
                          <tr>
                          <td><label>{__('3D Cube Size', 'quick-adsense-reloaded')}  </label></td>
                          <td><input className='small-text' value={post_meta.floating_cubes_size ? post_meta.floating_cubes_size:'200'} onChange={this.props.adFormChangeHandler} type="number" id="floating_cubes_size" name="floating_cubes_size" />{__('PX', 'quick-adsense-reloaded')}
                          </td></tr>
                        </tbody>
                      </table>
                        {  this.addFloatingSlide('init') }

                        {(show_form_error && post_meta.floating_slides.length != 6 ) ? <div className="quads_form_msg"><span className="material-icons">
                          error_outline</span>{__('Atleast two slides are  required', 'quick-adsense-reloaded')}</div> :''}
                      </div>);
      
                    break;
                    case 'ads_space':
                      ad_type_name = 'Ad Space';
                      post_meta.code = post_meta.code ? post_meta.code : __('Advertise on this Space', 'quick-adsense-reloaded');
                      comp_html.push(<div key="ad_space">
                        <table><tbody>
                          <tr>
                            <td><label>{__('Ad Space Type', 'quick-adsense-reloaded')}</label></td>
                            <td>
                              <MSelect value={(post_meta.ad_space_type!==undefined)?post_meta.ad_space_type:'text'} onChange={this.props.adFormChangeHandler} name="ad_space_type" id="ad_space_type" style={{width:'300px'}}>
                                  <MenuItem value="text">{__('Text', 'quick-adsense-reloaded')}</MenuItem>
                                  <MenuItem value="banner">{__('Banner', 'quick-adsense-reloaded')}</MenuItem> 
                              </MSelect>
                            </td>
                          </tr>
                          {(post_meta.ad_space_type!=='banner') &&
                          <tr>
                            <td><label>{__('Ad Space Text', 'quick-adsense-reloaded')}</label></td>
                            <td><textarea className={(show_form_error && post_meta.code == '') ? 'quads_form_error' : ''} cols="50" rows="5" value={post_meta.code} onChange={this.props.adFormChangeHandler} id="code" name="code" />
                              {(show_form_error && post_meta.code == '') ? <div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Enter Plain Text / HTML / JS', 'quick-adsense-reloaded')}</div> : ''}</td>
                          </tr>
                          }
                          {(post_meta.ad_space_type==='banner') &&
                          <>
                        
                          <tr>
                            <td><label>{__('Ad Space Banner', 'quick-adsense-reloaded')}</label></td>
                            <td>
                            {post_meta.ad_space_banner_image_src == '' ? <div><a className="button" onClick={this.selectadbannerimages}>{__(' Upload Banner', 'quick-adsense-reloaded')}</a></div>
                            : <div>
                            <img src={post_meta.ad_space_banner_image_src} className="banner_image" width={post_meta.banner_ad_width?post_meta.banner_ad_width:300} height={post_meta.banner_ad_height?post_meta.banner_ad_height:300} />
                            <a className="button" onClick={this.remove_adspace_image}>{__('Remove Banner', 'quick-adsense-reloaded')}</a></div>}
                            {(show_form_error && post_meta.ad_space_banner_image_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                              error_outline</span>{__('Upload Ad Space Banner', 'quick-adsense-reloaded')} </div> :''}
                              </td>
                            </tr>
                          </>
                          }
                          <tr>
                      <td><label>{__('Ad Size', 'quick-adsense-reloaded')}</label></td>
                      <td>
                      <div className="quads-banner-width-heigth">
                        <div className="quads-banner-width">
                          <label>{__('Width(px)', 'quick-banner-reloaded')}
                          <input value={post_meta.banner_ad_width?post_meta.banner_ad_width:300} onChange={this.props.adFormChangeHandler} type="number" id="banner_ad_width" name="banner_ad_width" placeholder="300"/> 
                          </label>
                        </div>
                        <div className="quads-banner-height">
                          <label>{__('Height(px)', 'quick-banner-reloaded')}
                          <input value={post_meta.banner_ad_height?post_meta.banner_ad_height:300} onChange={this.props.adFormChangeHandler} type="number" id="banner_ad_height" name="banner_ad_height" placeholder="300"/>  
                          
                          </label>
                        </div>
                      </div>
                     </td></tr>
                          <tr>
                            <td><label>{__('Ad Cost', 'quick-adsense-reloaded')} per day </label></td>
                            <td><input value={post_meta.ad_cost} onChange={this.props.adFormChangeHandler} type="number" id="ad_cost" name="ad_cost" placeholder="10" style={{width:'100px'}}/>  {this.state.currency} <br/><br/>
                            {__('Make sure you have added paypal email and Set payment currency in ', 'quick-adsense-reloaded')} <a href='?page=quads-settings&path=settings_adsell'>{__('Sellable Ads settings', 'quick-adsense-reloaded')}</a>
                            {(show_form_error && post_meta.ad_cost <= 0 ) ? <div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Ad cost must be greater than  0 ', 'quick-adsense-reloaded')}</div> : ''}
                            </td>
                          </tr> 

                          <tr>
                            <td><label>{__('Ad Minimum', 'quick-adsense-reloaded')}</label></td>
                            <td>
                              <input value={post_meta.ad_minimum_days} onChange={this.props.adFormChangeHandler} type="number" id="ad_minimum_days" name="ad_minimum_days" placeholder="10" style={{width:'100px'}}/> 
                              <MSelect style={{minWidth:'200px'}} value={(post_meta.ad_minimum_selection!==undefined)?post_meta.ad_minimum_selection:'day'} onChange={this.props.adFormChangeHandler} name="ad_minimum_selection" id="ad_minimum_selection">
                                <MenuItem value="day">{__('Days(s)', 'quick-adsense-reloaded')}</MenuItem>
                                <MenuItem value="month">{__('Month(s)', 'quick-adsense-reloaded')}</MenuItem> 
                              </MSelect><br/>
                            {(show_form_error && post_meta.ad_minimum_days <= 0 ) ? <div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Ad days must be greater than  0 ', 'quick-adsense-reloaded')}</div> : ''}
                            </td>
                          </tr>
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
                      {__('Do not enter AdSense page level ads or Auto ads! Learn how to create', 'quick-adsense-reloaded')} <a  target="_blank" href="https://wpquads.com/documentation/how-to-find-data-client-id-data-slot-id-for-adsense-integration/"> {__('AdSense ad coded', 'quick-adsense-reloaded')} </a>
                      <a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.props.getAdsenseCode}>{__('Get Code', 'quick-adsense-reloaded')}</a></div>
                    </div>
                  }/>
                </div> : ''}
                {this.props.ad_type == 'background_ad' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-background-ad-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'plain_text' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-custom-code-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'adsense' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-adsense-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'yandex' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-yandexdirect-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'mgid' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-mgid-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'taboola' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-taboola-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'media_net' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-media-net-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'outbrain' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-outbrain-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'mediavine' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-mediavine-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'rotator_ads' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-use-ad-rotator-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'group_insertion' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-group-insertion-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'infolinks' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-infolinks-ad-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'skip_ads' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/what-is-skippable-ad-and-how-to-use-it/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'propeller' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-setup-propeller-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'ab_testing' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-add-ab-testing/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                {this.props.ad_type == 'ads_space' ?  <a className="quads-docs-link" target="_blank" href="https://wpquads.com/documentation/how-to-set-up-sellable-ads-in-wp-quads/">{__('View Documentation on', 'quick-adsense-reloaded')} {ad_type_name} {__('AD', 'quick-adsense-reloaded')}</a>:''}
                
                <div className="quads-panel">
                 <div className="quads-panel-body">{comp_html}</div>
              </div>
              </div>
              );
  }
}

export default QuadsAdConfigFields;