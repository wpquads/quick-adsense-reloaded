import React, { Component, Fragment } from 'react';
import { BrowserRouter as Router, Switch, Route, Link } from 'react-router-dom';
import ReactDOM from 'react-dom';
import './QuadsAdCreateRouter.scss';

import QuadsAdConfig from '../config/QuadsAdConfig';
import QuadsAdTargeting from '../targeting/QuadsAdTargeting';
import QuadsAdPublish from '../publish/QuadsAdPublish';
import queryString from 'query-string'
import Icon from '@material-ui/core/Icon';

class QuadsAdCreateRouter extends Component {
    
    constructor(props) {  
      let visibility_include_def_val = [{type :
                    {
                      label : "Post Type",
                      value: "post_type"
                    },value :
                    {
                      label : "post",
                      value: "post"
                    }
                  }]
     super(props);
        this.state = {   
          show_form_error       :  false,       
          quads_include_toggle  :  false,
          Quads_confirm_box     :  false,
          quads_exclude_toggle  :  false,
          quads_include_placeholder: 'Search for post types',
          quads_exclude_placeholder: 'Search for post types',
          quads_include_current_type  : 'post_type',
          quads_exclude_current_type  : 'post_type',
          quads_include_input_text : '',
          quads_exclude_input_text : '',
          quads_include_input_id : '',
          quads_exclude_input_id : '',                           
          quads_is_reload       :  true,
          quads_modal_value     : '',
          quads_modal_error     : '',
          quads_modal_open      :  false,
          quads_is_loaded       :  true,
          quads_is_visibility   :  true,                                     
          quads_state_changed   :  false,
          quads_is_saved        :  false,
          quads_ad_status       :  '',
          quads_is_error        :  [],
          quads_post            :  {},          
          quads_include_meta     : {},                   
          quads_exclude_meta     : {},                   
          quads_post_meta        :  {        
            check_plugin_exist   : true,
            visibility_include   : visibility_include_def_val,
            ad_blindness         : [],
            ab_testing         : [],
            popup_ads         : [],
            video_ads         : [],
            visibility_exclude   : [],
            targeting_include         : [],              
            targeting_exclude        : [],              
            ad_id                : '',
            ad_type              : '',
            label                : '',
            adsense_ad_type      : 'display_ads',
            data_layout_key      : '',
            g_data_ad_slot       : '',
            g_data_ad_client     : '',
            adsense_type         : 'normal',
            g_data_ad_width      : '',
            grid_data_ad_column  : '0',
            g_data_ad_height     : '',   
            grid_data_ad_row     : '0',   
            network_code         : '',
            skip_ads_type        : 'image_banner',
            freq_page_view       : '5',
            ad_wt_time           : '5',
            ad_unit_name         : '',      
            code           : '',
            align             : 3,
            ad_label_check    : false,
            mobile_image_check    : false,
            adlabel           : '',
            ad_label_text  : 'Advertisements',
            margin            : 0,
            padding            : 0,
            position          : 'beginning_of_post',
            paragraph_number  : 1,
            count_as_per      : 'p_tag',
            word_count_number : 100,
            image_number      : 1,
            enabled_on_amp        : true,
            enable_on_end_of_post : false,
            repeat_paragraph      : false,
            repeat_impressions    : false,
            cls_btn      : false,
            after_the_percentage_value: 50,
            ads_loop_number: 1,
            paragraph_limit: '',
            insert_after: 1,
            image_caption : false,
            include_dropdown           : false,
            exclude_dropdown           : false,  
            random_ads_list            : [], 
            ads_list                   : [],
            image_src                  : '',
            image_width                : '',
            image_height               : '',
            image_mobile_src           : '',
            image_src_id               : '' ,     
            image_redirect_url         : '' ,  
            taboola_publisher_id       : '' ,   
            data_cid                   : '' , 
            data_crid                  : '' , 
            mediavine_site_id          : '' ,
            outbrain_widget_ids        : '' , 
            infolinks_pid              : '' ,
            infolinks_wsid             : '' ,
            data_container             : '' ,    
            data_js_src                : '' ,
            propeller_js                : '' ,
            adpushup_site_id           : '',
            adpushup_slot_id           : '',            
            refresh_type               : 'on_load',
            sort_type               : 'random',
            num_ads_t_s               : '1',
            loop_add_title            :'',
            loop_add_description      :'',
            loop_add_link             :'',
            carousel_ads              :[],
            carousel_type             :'slider',
            carousel_width            :450,
            carousel_height           :350,
            carousel_speed            :4,
            carousel_arrows           :false,
            sticky_scroll_height      :350,
            sticky_scroll_text        :'',
            carousel_slides           :2,
            floating_slides           :[],
            floating_position         :'bottom-right',
            floating_cubes_size       :'200',
            floating_cubes_type       :'flip',
            video_autoplay            : false,
            video_height              : 450,
            video_width               : 350,
            parallax_btn_url          : '',
            block_id                  : '',
            banner_ad_width           : '',
            banner_ad_height          : '',
            exp_date_from             : '',
            exp_date_to               : '',
            set_spec_day              : [],
            check_exp_date            :false,
            check_spec_day            :false,
            check_lazy_load           :false,
            check_lazy_load_delay     :3,
            add_url_nofollow          : true,
            delay_ad_sec              :false,
            publish_date              : '',
            mob_code                  : '',
            carousel_close            : false,
            ad_cost                   : 1,
            ad_cost_type              : 'per_day',

            },
            quads_form_errors : {
              g_data_ad_slot       : '',
              g_data_ad_client     : '',
              code                 : '',
              label                : '',
              position             : '',
              visibility_include   : [],
              random_ads_list      : [],
              ads_list            : [],
              floating_slides     : []
            }                    
        };       
     this.include_timer = null;      
     this.exclude_timer = null;  

     this.includedVal = [];
     this.excludedVal = []; 
     this.includedVisibilityVal = visibility_include_def_val;
     this.excludedVisibilityVal = []; 
    }
    
    updateVisitorTarget = (include, exclude) => {            
      this.includedVal = include;
      this.excludedVal = exclude;
    }
    updateVisibility = (include, exclude) => {            
      this.includedVisibilityVal = include;
      this.excludedVisibilityVal = exclude;
    }
      updateRandomAds = (random_ads_list) => {         
      this.random_ads_list = random_ads_list;
    }
    updateAdsList = (ads_list) => {     
        this.ads_list = ads_list;
    }

    updateFloatingList = (floating_slides) => {   
      this.state.quads_post_meta.floating_slides = floating_slides;
      this.floating_slides = floating_slides;
   }
   updateSetdaysList = (set_days) => {   
    this.state.quads_post_meta.set_spec_day = set_days;
    this.set_spec_day = set_days;
 }

    getAdDataById =  (ad_id) => {

      let url = quads_localize_data.rest_url+'quads-route/get-ad-by-id?ad-id='+ad_id;   
      if(quads_localize_data.rest_url.includes('?')){
         url = quads_localize_data.rest_url+'quads-route/get-ad-by-id&ad-id='+ad_id;  
      }   
      fetch(url,{
        headers: {                    
          'X-WP-Nonce': quads_localize_data.nonce,
        }
      }
      )
      .then(res => res.json())
      .then(
        (result) => {  
          
          const { quads_post_meta } = { ...this.state };
          Object.entries(result).map(([key, value]) => {
            if(key == 'post'){
              this.setState({quads_post: result.post}); 
            }else{ 
                          
              Object.entries(value).map(([meta_key, meta_val]) => {
                 if(meta_key == 'visibility_include'){
                   this.includedVisibilityVal = meta_val;
                }else if(meta_key == 'visibility_exclude'){
                   this.excludedVisibilityVal = meta_val;
                }else if(meta_key == 'targeting_include'){
                   this.includedVal = meta_val;
                }else if(meta_key == 'targeting_exclude'){
                   this.excludedVal = meta_val;
                }
                  
                    quads_post_meta[meta_key] =    meta_val;   
                                     
              })
              
              this.setState(quads_post_meta);
            }

          })  
                    
        },        
        (error) => {
          
        }
      );  

    }

    

    removeVisibilityIncludeItem = (e) => {
      
      e.preventDefault();
      let index = e.currentTarget.dataset.index;      
      const { quads_post_meta } = { ...this.state };    
      quads_post_meta.visibility_include.splice(index,1);
      this.setState(quads_post_meta);

    }
    removeVisibilityExcludeItem = (e) => {           
      e.preventDefault();
      let index = e.currentTarget.dataset.index;      
      const { quads_post_meta } = { ...this.state };    
      quads_post_meta.visibility_exclude.splice(index,1);
      this.setState(quads_post_meta);
  }  
     
    
    onListSearchHover = () => {      
    }
    addIncludeFromSearch = (e) => {
      e.preventDefault();
      let id   = e.currentTarget.dataset.id;
      let text = e.currentTarget.dataset.text;  
      this.setState({quads_include_input_id: id, quads_include_input_text: text}); 
      
      const { quads_post_meta } = { ...this.state };
      quads_post_meta.include_dropdown   = false;
      this.setState(quads_post_meta);
      
    }
    addExcludeFromSearch = (e) => {
      e.preventDefault();      
      let id   = e.currentTarget.dataset.id;
      let text = e.currentTarget.dataset.text;        
      this.setState({quads_exclude_input_id: id, quads_exclude_input_text: text}); 
      
      const { quads_post_meta } = { ...this.state };
      quads_post_meta.exclude_dropdown   = false;
      this.setState(quads_post_meta);

    }
    onIncludeFocus = () => {
      
      const { quads_post_meta } = { ...this.state };
      quads_post_meta.include_dropdown   = true;
      this.setState(quads_post_meta);
    }    

    onExcludeFocus = () => {

      const { quads_post_meta } = { ...this.state };
      quads_post_meta.exclude_dropdown   = true;
      this.setState(quads_post_meta);
      
    }    
      
    excludeFormToggle = () => {      
      this.setState({ quads_exclude_toggle : !this.state.quads_exclude_toggle });
    }

    includeFormToggle = () => {
      this.setState({ quads_include_toggle : !this.state.quads_include_toggle });
    }    
    openModal = () =>{      
      this.setState({quads_modal_open: true});
    }
    closeModal = () => {
      this.setState({quads_modal_open: false});
      this.setState({quads_modal_error:''});
    }
    modalValue = (e) => {
      this.setState({quads_modal_value: e.target.value});      
    }

    getGoogleAdsenseAttr =(content, regex) => {
        
        const str = content;
        var m;
        var result = {};
        
        while ((m = regex.exec(str)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === regex.lastIndex) {
                regex.lastIndex++;
            }

            // The result can be accessed through the `m`-variable.
            m.forEach(function(match, index){             
                result = match;
            });
        }
        return result;

    }

    getAdsenseCode = () =>{
      
      const content = this.state.quads_modal_value;
      this.setState({quads_modal_open: true});      
      if(content){
        
        const slot_regex = /google_ad_slot\s*=\s*"(\d*)";/g;
        let ad_slot = this.getGoogleAdsenseAttr(content, slot_regex);  

        const ad_client_regex = /google_ad_client\s*=\s*"ca-pub-(\d*)";/g;
        let ad_client       = this.getGoogleAdsenseAttr(content, ad_client_regex);  

        const width_regex = /google_ad_width\s*=\s*(\d*);/g;
        let ad_width = this.getGoogleAdsenseAttr(content, width_regex);  

        const height_regex = /google_ad_height\s*=\s*(\d*);/g;
        let ad_height = this.getGoogleAdsenseAttr(content, height_regex); 

        const ad_format_regex = /data-ad-format\s*=\s*"(\w*)";/g;
        let ad_format  = this.getGoogleAdsenseAttr(content, ad_format_regex); 
        
        const adsense_type = 'normal';        
        if ((!ad_slot || 0 === ad_slot.length) && (!ad_client || 0 === ad_client.length) && (!ad_width || 0 === ad_width.length)) {
          adsense_type = 'normal';
        }
        if(ad_format == 'auto'){
          adsense_type = 'responsive';
        }
        const { quads_post_meta } = { ...this.state };
          quads_post_meta.grid_data_ad_column  = ad_column;
          quads_post_meta.grid_data_ad_row = ad_row;
          quads_post_meta.num_ads_t_s = ad_num_ads_t_s;
          this.setState(quads_post_meta);

        if ((ad_slot.length >=0 ) && (ad_client.length >= 0 )) {

          const { quads_post_meta } = { ...this.state };
          quads_post_meta.g_data_ad_slot   = ad_slot;
          quads_post_meta.g_data_ad_client = 'ca-pub-'+ad_client;
          quads_post_meta.g_data_ad_width  = ad_width;
          quads_post_meta.g_data_ad_height = ad_height;
          quads_post_meta.adsense_type     = adsense_type;
          this.setState(quads_post_meta);
          this.setState({quads_modal_open: false});
        }else{
          this.setState({quads_modal_error:__('Can not parse AdSense Code. Is the code valid?','quick-adsense-reloaded')});
          this.setState({quads_modal_open: true});
        }                
      }else{
        this.setState({quads_modal_error:__('Please provide adsense code','quick-adsense-reloaded')});
      }                        
    }    

    saveAdFormData = (ad_status) => {
      this.setState({quads_is_loaded : true});
      const body_json = this.state;
      body_json.quads_ad_status = ad_status; 
      //visitor targeting
      body_json.quads_post_meta.targeting_include = this.includedVal; 
      body_json.quads_post_meta.targeting_exclude = this.excludedVal; 

      body_json.quads_post_meta.visibility_include = this.includedVisibilityVal; 
      body_json.quads_post_meta.visibility_exclude = this.excludedVisibilityVal; 
      body_json.quads_post_meta.random_ads_list = this.random_ads_list; 
      body_json.quads_post_meta.ad_blindness =body_json['ad_blindness'];
      body_json.quads_post_meta.ab_testing =body_json['ab_testing'];
      body_json.quads_post_meta.popup_ads =body_json['popup_ads'];
      body_json.quads_post_meta.video_ads =body_json['video_ads'];
      body_json.quads_post_meta.ads_list = this.ads_list; 
      body_json.quads_post_meta.floating_slides = this.floating_slides; 
      body_json.quads_post_meta.set_spec_day = this.set_spec_day;
      body_json.mob_code = body_json.quads_post_meta['mob_code'];
           
      let url = quads_localize_data.rest_url + 'quads-route/update-ad';
      fetch(url,{
        method: "post",
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-WP-Nonce': quads_localize_data.nonce,
        },
        //make sure to serialize your JSON body
        body: JSON.stringify(body_json)
      })
      .then(res => res.json())
      .then(
        (result) => {
          
          this.setState({quads_is_loaded : false});                   
          this.setState(Object.assign(this.state.quads_post_meta,{ad_id:result.ad_id}));
          
          let path   = this.props.location.pathname;
          let search = this.props.location.search;              
          const page = queryString.parse(window.location.search);                
          let new_url =   path + search;                    
          if(page.action != 'edit'){
            new_url = this.removePartofQueryString(new_url, 'path=wizard_target')
            new_url += 'path=wizard_publish&action=edit&post='+result.ad_id;
          }else{
            new_url = this.removePartofQueryString(new_url, 'path=wizard_target')
            new_url += 'path=wizard_publish';
          }
          this.setState({quads_is_saved:true});
          this.props.history.push(new_url);          
        },        
        (error) => {
          this.setState({            
            quads_is_error: error,
            quads_is_loaded: false
          });
        }
      ); 
      
    }

    adFormChangeHandler = (event) => {
   
      const name = event.target.name;
      let value = event.target.type === 'checkbox' ?  event.target.checked : event.target.value;  
      if(name == 'position' && value == 'amp_story_ads'){
        let url = quads_localize_data.rest_url+'quads-route/check_plugin_exist?plugin_name=amp_story';   
        fetch(url,{
          headers: {                    
            'X-WP-Nonce': quads_localize_data.nonce,
          }
        }
        )
        .then(res => res.json())
        .then(
          (result) => {  
            let status =result.status;
                
            const { quads_post_meta } = { ...this.state };
        const currentState = quads_post_meta;      
        if(name){
          currentState[name] = value;    
          if(status=='f'){
            currentState['check_plugin_exist'] = false;
          }  
          this.setState({ quads_post_meta: currentState,  quads_state_changed: true });  
        }                  
        var page = queryString.parse(window.location.search);  
        
        if(!this.state.quads_ad_status){
          this.setState({quads_ad_status:'draft'});
        }                 
          },        
          (error) => {
            
          }
        );  

      }else{
        const { quads_post_meta } = { ...this.state };
        const currentState = quads_post_meta;      
        if(name){
          if(name==='image_redirect_url'){
            value = decodeURIComponent(value);
          }
          currentState[name] = value;     
            this.setState({ quads_post_meta: currentState,  quads_state_changed: true });  
        }                  
        var page = queryString.parse(window.location.search);  
        
        if(!this.state.quads_ad_status){
          this.setState({quads_ad_status:'draft'});
        } 
      }
    }
    saveAsDraft = (event) => {      
      event.preventDefault();            
      this.saveAdFormData('draft'); 
    }
    publish = (event) => { 

      event.preventDefault();     
      const {quads_post_meta} = this.state;
      let validation_flag = true;
      if(quads_post_meta.position == 'after_the_percentage'){
        if(quads_post_meta.after_the_percentage_value == '' || parseInt(quads_post_meta.after_the_percentage_value) < 10 || parseInt(quads_post_meta.after_the_percentage_value) > 101){
          validation_flag = false;
        }
      }
    
      switch (quads_post_meta.ad_type) {

        case 'plain_text':

          if(validation_flag && quads_post_meta.code  && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
            this.saveAdFormData('publish');  
          }else{
            this.setState({show_form_error:true});
          }
          
          break;

          case 'adsense':
            if(validation_flag && (quads_post_meta.adsense_ad_type == 'adsense_auto_ads' || quads_post_meta.g_data_ad_slot) && quads_post_meta.g_data_ad_client && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
            
          break;
          case 'random_ads':
            if(validation_flag && quads_post_meta.random_ads_list.length > 0 && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'rotator_ads':
            if(validation_flag && quads_post_meta.ads_list.length > 0 && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
        case 'group_insertion':
          if(validation_flag && quads_post_meta.ads_list.length > 0 && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
            this.saveAdFormData('publish');
          }else{
            this.setState({show_form_error:true});
          }
          break;
           case 'double_click':
            if(validation_flag && quads_post_meta.ad_unit_name && quads_post_meta.network_code && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
            case 'yandex':
            if(validation_flag && quads_post_meta.block_id && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
              case 'mgid':
            if(validation_flag && quads_post_meta.data_js_src && quads_post_meta.data_container && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
              case 'propeller':
            if(validation_flag && quads_post_meta.propeller_js && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;

          case 'ad_image':
            if(validation_flag && quads_post_meta.image_src && quads_post_meta.image_redirect_url && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'video_ads':
            if(validation_flag && quads_post_meta.image_src && quads_post_meta.image_redirect_url && quads_post_meta.video_width && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'ad_blindness':
            if(validation_flag && quads_post_meta.ads_list.length > 0 && quads_post_meta.ad_blindness.length > 0 && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'ab_testing':
            if(validation_flag && quads_post_meta.ads_list.length > 0 && quads_post_meta.ab_testing.length > 0 && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'popup_ads':
            this.saveAdFormData('publish');
          break;
          case 'video_ads':
            this.saveAdFormData('publish');
          break;
          case 'taboola':
            if(validation_flag && quads_post_meta.taboola_publisher_id && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
        case 'media_net':
            if(validation_flag && quads_post_meta.data_cid && quads_post_meta.data_crid
 && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;

          
          case 'mediavine':
            if(validation_flag && quads_post_meta.mediavine_site_id && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'outbrain':
            if(validation_flag && quads_post_meta.outbrain_widget_ids && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break; 
           case 'infolinks':
            if(validation_flag && quads_post_meta.infolinks_wsid && quads_post_meta.infolinks_pid && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'background_ad':
            if(validation_flag && quads_post_meta.image_src && quads_post_meta.image_redirect_url && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break; 
          case 'skip_ads':
            if(validation_flag && quads_post_meta.skip_ads_type && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'loop_ads':
            if(validation_flag  && quads_post_meta.loop_add_title && quads_post_meta.loop_add_description && quads_post_meta.loop_add_link && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'carousel_ads':
            if(validation_flag  && quads_post_meta.ads_list.length > 0 && quads_post_meta.carousel_type && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'parallax_ads':
            if(validation_flag && quads_post_meta.parallax_btn_url && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'half_page_ads':
            if(validation_flag && quads_post_meta.half_page_ads_btn_url && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');   
            }else{
              this.setState({show_form_error:true});
            }
          break;
          case 'sticky_scroll':
            if(validation_flag && quads_post_meta.ads_list.length > 0 && quads_post_meta.position && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)){
              this.saveAdFormData('publish');
            }else{
              this.setState({show_form_error:true});
            }
            break;
        case 'floating_cubes':
          if (validation_flag && quads_post_meta.floating_slides.length >= 1 && quads_post_meta.floating_cubes_type && (quads_post_meta.position == 'ad_shortcode' || quads_post_meta.visibility_include.length > 0)) {
            this.saveAdFormData('publish');
          } else {
            this.setState({ show_form_error: true });
          }
          break;
        case 'ads_space':
          if (quads_post_meta.code && quads_post_meta.ad_cost > 0 ) {
            this.saveAdFormData('publish');
          } else {
            this.setState({ show_form_error: true });
          }
          break;    
        default:
          break;
      }

    }
    componentDidUpdate(){
          

    }
    componentDidMount(){ 
      var page = queryString.parse(window.location.search);        
      
      if(this.state.quads_is_reload && page.action == 'edit'){   
      document.body.classList.add('quads_editpage');         
          this.getAdDataById(page.post);
          this.setState({            
            quads_is_reload: false
          });
      }else{
      document.body.classList.add('quads_addpage');    
      }
      
  
      
      this.setState(Object.assign(this.state.quads_post_meta,{ad_type:page.ad_type}));
      this.setState({quads_is_loaded : false});
    } 
    removePartofQueryString = (q_string, part) => {
      
      var split_arr  = q_string.split("&");
      var new_search = '';

      for(let i=0; i<split_arr.length; i++){

        if(!split_arr[i].includes(part)){

          new_search += split_arr[i] + '&';
          
        }
        
      }
      return new_search;
    }
    quadsGoBack = (e) => {

      e.preventDefault();
      let page    = queryString.parse(window.location.search);   
      let is_ad_space = page['ad_type'];
      let new_url = this.props.location.pathname + '?page=quads-settings';
      if(is_ad_space==='ads_space'){
        new_url = this.props.location.pathname + '?page=quads-settings&path=adsell';
      }
      
      if(this.state.quads_state_changed && !this.state.quads_is_saved){

        let r = confirm("Changes you made may not be saved.");

        if (r == true) {
           this.props.history.push(new_url);  

        } else {
          return false
        }

      }else{
        this.props.history.push(new_url);           
      }

    }
    moveNext =(e) => { 

      let page    = queryString.parse(window.location.search);          
      let new_url = this.props.location.pathname + this.removePartofQueryString(this.props.location.search, 'path=wizard');      
      const {quads_post_meta} = this.state;
  
      if(page.path == 'wizard'){

        new_url += 'path=wizard_target';

        switch (quads_post_meta.ad_type) {

          case 'plain_text':

            if(quads_post_meta.code){
              this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            }
            
            break;

            case 'adsense':
              if( (quads_post_meta.adsense_ad_type == 'adsense_auto_ads' || quads_post_meta.g_data_ad_slot) && quads_post_meta.g_data_ad_client){
                this.props.history.push(new_url); 
              }else{
                this.setState({show_form_error:true});
              }
              
            break;
         case 'random_ads':
          if(quads_post_meta.random_ads_list.length > 0 ){
             this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            }
            break;
          case 'rotator_ads':

          if(quads_post_meta.ads_list.length > 0 ){
             this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            }
            break;
          case 'group_insertion':

            if(quads_post_meta.ads_list.length > 0 ){
              this.props.history.push(new_url);
            }else{
              this.setState({show_form_error:true});
            }
            break;
        case 'double_click':
          if(quads_post_meta.ad_unit_name && quads_post_meta.network_code){
            this.props.history.push(new_url); 
          }else{
            this.setState({show_form_error:true});
          } 
            break;
        case 'yandex':
          if(quads_post_meta.block_id){
            this.props.history.push(new_url); 
          }else{
            this.setState({show_form_error:true});
          }
            break;
            case 'mgid':
          if(quads_post_meta.data_container && quads_post_meta.data_js_src){
            this.props.history.push(new_url); 
          }else{
            this.setState({show_form_error:true});
          } 
            break;
            case 'propeller':
          if( quads_post_meta.propeller_js ){
            this.props.history.push(new_url); 
          }else{
            this.setState({show_form_error:true});
          } 
            break;
          case 'ad_blindness':
            if(quads_post_meta.ads_list.length > 0 ){
              this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            } 
            break;
          case 'ab_testing':
            if(quads_post_meta.ads_list.length > 0 ){
              this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            } 
            break;
          case 'popup_ads':
            if(quads_post_meta.ads_list.length > 0 ){
              this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            } 
            break;
            case 'ad_image':
          if(quads_post_meta.image_src && quads_post_meta.image_redirect_url){
            this.props.history.push(new_url); 
          }else{
            this.setState({show_form_error:true});
          } 
          break;
            case 'video_ads':
           if(quads_post_meta.image_src && quads_post_meta.image_redirect_url && quads_post_meta.video_width){
            this.props.history.push(new_url); 
          }else{
            this.setState({show_form_error:true});
          } 
          break;
          case 'taboola':
            if(quads_post_meta.taboola_publisher_id){
              this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            }
            break;
          case 'media_net':
            if(quads_post_meta.data_cid && quads_post_meta.data_crid){
              this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            }
            break;

          case 'mediavine':
            if(quads_post_meta.mediavine_site_id){
              this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            }
            break;
          case 'outbrain':
            if(quads_post_meta.outbrain_widget_ids){
              this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            }
            break;
          case 'infolinks':
            if(quads_post_meta.infolinks_pid && quads_post_meta.infolinks_wsid){
              this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            }
            break;
            case 'background_ad':
          if(quads_post_meta.image_src && quads_post_meta.image_redirect_url){
            this.props.history.push(new_url); 
          }else{
            this.setState({show_form_error:true});
          } 
          break;
          case 'skip_ads':
          if( (quads_post_meta.skip_ads_type == 'image_banner' && quads_post_meta.image_src && quads_post_meta.image_redirect_url) || (quads_post_meta.skip_ads_type == 'custom_html' && quads_post_meta.code)){
            this.props.history.push(new_url); 
          }else{
            this.setState({show_form_error:true});
          } 
            break;
          case 'loop_ads':
            if(quads_post_meta.loop_add_link && quads_post_meta.loop_add_title && quads_post_meta.loop_add_description){
              this.props.history.push(new_url); 
            }else{
              this.setState({show_form_error:true});
            } 
            break;
            case 'carousel_ads':
              if(quads_post_meta.ads_list.length > 0 && quads_post_meta.carousel_type){
                this.props.history.push(new_url); 
              }else{
                this.setState({show_form_error:true});
              } 
            break;

            case 'parallax_ads':
              if(quads_post_meta.parallax_btn_url){
               
                this.props.history.push(new_url); 
              }else{
                this.setState({show_form_error:true});
              }
            break;

            case 'half_page_ads':
              if(quads_post_meta.half_page_ads_btn_url){
               
                this.props.history.push(new_url); 
              }else{
                this.setState({show_form_error:true});
              }
            break;

              case 'sticky_scroll':

                if(quads_post_meta.ads_list.length > 0 ){
                  this.props.history.push(new_url);
                }else{
                  this.setState({show_form_error:true});
                }
                break;
                case 'floating_cubes':
                if(quads_post_meta.floating_slides.length >=1  && quads_post_meta.floating_cubes_type){
                  this.props.history.push(new_url);
                }else{
                  this.setState({show_form_error:true});
                }
                break;

                case 'ads_space':
                  if (quads_post_meta.code && quads_post_meta.ad_cost > 0 ) {
                    this.props.history.push(new_url);
                  } else {
                    this.setState({ show_form_error: true });
                  }

                  break;
          default:
            break;
        }
                         
      }else if(page.path == 'wizard_target'){

        new_url += 'path=wizard_publish';
        
        if(quads_post_meta.position && visibility_include.length > 0){
          this.props.history.push(new_url); 
        }else{
          this.setState({show_form_error:true});
        }
        
      }                         
    }
    movePrev =(e) => {
      
      let page    = queryString.parse(window.location.search);          
      let new_url = this.props.location.pathname + this.removePartofQueryString(this.props.location.search, 'path=wizard');
      
      if(page.path == 'wizard_publish'){
        new_url += 'path=wizard_target';
      }else if(page.path == 'wizard_target'){
        new_url += 'path=wizard';
      }
      
      this.props.history.push(new_url);         

    }
  closeNotice = () => {
    this.setState({quads_is_saved:false});
  }

  moveTostart = () => {
    this.movePrev();
    this.movePrev();
   };

  render() {
    
      const location = this.props.location;         
      const page = queryString.parse(window.location.search); 
      const {__} = wp.i18n; 
      const post_meta = this.state.quads_post_meta;
      const show_form_error = this.state.show_form_error;
      var quads_old_id = '';
      quads_old_id = this.state.quads_post_meta.quads_ad_old_id;       
      if( quads_old_id ){
          var final_quads_old_id =  quads_old_id.substring(2);
          
      }
      if(page.path == 'wizard_target' && this.state.quads_post_meta.label == ''){

    const json_data = {
      action: 'quads_ajax_add_ads',
    } 
    const url = quads_localize_data.rest_url + "quads-route/get-add-next-id";
    fetch(url , {
      method: "post",
      headers: {    
        'Accept': 'application/json',
        'Content-Type': 'application/json',                
        'X-WP-Nonce': quads_localize_data.nonce,
      },
      body: JSON.stringify(json_data)
    })
    .then(res => res.json())
    .then(
      (result) => {   
      let titleName =result.name;
      let quads_ad_old_id ='ad'+result.id;
      if(page.ad_type == 'random_ads'){
          titleName =result.name +" (Random)";
      }else if(page.ad_type == 'rotator_ads'){
          titleName =result.name +" (Rotator)";
      }else if(page.ad_type == 'group_insertion'){
        titleName =result.name +" (Group Insertion)";
      }else if(page.ad_type == 'popup_ads'){
        titleName =result.name +" (popup)";
      }else if(page.ad_type == 'video_ads'){
        titleName =result.name +" (video)";
      }
          this.setState(Object.assign(this.state.quads_post_meta,{label:titleName,quads_ad_old_id:quads_ad_old_id}));
      },        
      (error) => {
        
      }
    );  
      }
      else if(page.path == 'wizard_target' && page.action !== 'edit' && this.state.quads_post_meta.label != '' && this.state.quads_post_meta.label != 'Ad '+final_quads_old_id+''   ){
        setTimeout(() => {
          var e = document.getElementsByName("position")[0];
          var strUser = e.options[e.selectedIndex].value;
          if( strUser == 'ad_shortcode' ){
          const location = this.props.location;         
          var location_check = location.search
          const post_meta = this.state.quads_post_meta;
        
        const json_data = {
          action: 'quads_ajax_add_ads',
        } 
        
        const url = quads_localize_data.rest_url + "quads-route/get-add-next-id";
            
        if ( !location_check.includes('edit') ) {
        fetch( url , {
          method: "post",
          headers: {    
            'Accept': 'application/json',
            'Content-Type': 'application/json',                
            'X-WP-Nonce': quads_localize_data.nonce,
          },
          body: JSON.stringify(json_data)
        })
        .then(res => res.json())
        .then(
          (result) => {
          let titleName =post_meta.label;
          let quads_ad_old_id ='ad'+result.id;
          if(page.ad_type == 'random_ads'){
              titleName =post_meta.label;
          }else if(page.ad_type == 'rotator_ads'){
              titleName =post_meta.label;
          }else if(page.ad_type == 'group_insertion'){
            titleName =post_meta.label;
          }else if(page.ad_type == 'popup_ads'){
            titleName =post_meta.label;
          }else if(page.ad_type == 'video_ads'){
            titleName =post_meta.label;
          }
              this.setState(Object.assign(this.state.quads_post_meta,{label:titleName,quads_ad_old_id:quads_ad_old_id}));
          },        
          (error) => {
            
          }
        );
      }
          }
        }, 100);
      }

    return (    
    
      <div>  
        {this.state.quads_is_loaded ? <div className="quads-cover-spin"></div> : ''}
        <form id="quads-ad-form"> 
        <div className="material-icons quads-close-create-page"><a onClick={this.quadsGoBack} >{__('close','quick-adsense-reloaded')}</a></div>         
        <div className="quads-ad-config-header"> 
                <div className="quads-ad-input">
                  <input value={this.state.quads_post_meta.label} type="text" onChange={this.adFormChangeHandler} name="label"  placeholder={__('Name your ad unit', 'quick-adsense-reloaded') }/>
                  <input type="hidden" name="ad_id" value={this.state.quads_post_meta.ad_id} />
                  <input type="hidden" name="ad_type" value={this.state.quads_post_meta.ad_type} />
                </div>
                <div className="quads-ad-config-menu">
                    <div className="quads-ad-config-tab">
                        <ul>
                            <li className={`${ (page.path =='wizard') ? 'quads-selected' : ''}`}>
                              <a className={'config-header-link'} onClick={page.path === 'wizard_target' ? this.movePrev :  ( page.path === 'wizard_publish' ? this.moveTostart : undefined )}> {__('Configuration', 'quick-adsense-reloaded') } </a>
                            </li>
                            <li className={`${ (page.path =='wizard_target') ? 'quads-selected' : ''}`}>
                            <a className={'config-header-link'} onClick={page.path === 'wizard' ? this.moveNext : ( page.path === 'wizard_publish' ? this.movePrev : undefined )} > {__('Targeting', 'quick-adsense-reloaded') } </a>
                            </li>
                            <li className={`${ (page.path =='wizard_publish') ? 'quads-selected' : ''}`}>
                            <a> {__('Publish', 'quick-adsense-reloaded') } </a>
                            </li>                            
                        </ul>   
                    </div>                    
                </div>
            </div>  
            <Route render={props => {                                                                                                                                                                                           
                         switch (page.path) {
                          case "wizard":
                              return <QuadsAdConfig  
                              {...props} 
                              moveNext={this.moveNext}
                              parentState={this.state} 
                              adFormChangeHandler={this.adFormChangeHandler} 
                              modalValue={this.modalValue}
                              getAdsenseCode={this.getAdsenseCode} 
                              openModal     = {this.openModal}
                              closeModal    = {this.closeModal}
                              updateRandomAds    = {this.updateRandomAds}  
                              updateAdsList    = {this.updateAdsList} 
                              updateFloatingList    = {this.updateFloatingList} 
                              updateSetdaysList   = {this.updateSetdaysList} 
                              />;
                          case "wizard_target":
                              return <QuadsAdTargeting  
                              {...props} 
                              parentState={this.state}                             
                              updateVisitorTarget ={this.updateVisitorTarget}
                              updateVisibility    = {this.updateVisibility}                                                                                                                
                              adFormChangeHandler={this.adFormChangeHandler}    
                              movePrev={this.movePrev}                                                        
                              publish={this.publish}                                                                                    
                              onListSearchHover          ={this.onListSearchHover}  
                              />;
                          case "wizard_publish":  
                              return <QuadsAdPublish  {...props} 
                              parentState={this.state} 
                              adFormChangeHandler={this.adFormChangeHandler}  
                              movePrev={this.movePrev}/>; 
                            default:
                              return null           
                         }                                                 
                       }}/>  
        </form>
        </div>
  );
  }
}

export default QuadsAdCreateRouter;