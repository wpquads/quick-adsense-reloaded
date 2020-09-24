import React, { Component, Fragment } from 'react';
import { BrowserRouter as Router, Switch, Route, Link } from 'react-router-dom';
import queryString from 'query-string'
import Icon from '@material-ui/core/Icon';
import { Alert } from '@material-ui/lab';
import Select from "react-select";
//import "react-select/dist/react-select.css";
import './QuadsAdListSettings.scss';
import QuadsAdSettingsNavLink from './QuadsAdSettingsNavLink';
import copy from 'copy-to-clipboard';
import { SketchPicker } from 'react-color';
import reactCSS from 'reactcss'
class QuadsAdListSettings extends Component {
  constructor(props) {      
    super(props);
    this.state = {            
            notice_txt_color_picker: false,
            notice_bg_color_picker : false,
            notice_btn_txt_color_picker : false,
            notice_btn_bg_color_picker : false,
            button_spinner_toggle: false,   
            multiUserOptions: [], 
            multiTagsOptions: [],
            multiPluginsOptions: [],           
            file_uploaded :false,
            settings_saved :false,
            settings_error :'',
            adtxt_modal :false,
            global_excluder_modal :false,
            customer_querey_error: '',
            customer_querey_success: '',
            customer_query_type: '',
            customer_query_message: '',  
            customer_query_email : '',          
            backup_file   : null,
            textToCopy    : '',
            copied: false,
            ad_blocker_support_popup:false,
            click_fraud_protection_popup:false,
            old_settings  : '',
            settings      :{
                notice_txt_color : '#ffffff',
                ad_blocker_support :false,
                click_fraud_protection : false,
                notice_bg_color : '#1e73be',
                allowed_click   : 3,
                click_limit     : 3,
                ban_duration    : 7,
                notice_btn_txt_color : '#ffffff',
                notice_btn_bg_color : '#f44336',
                uninstall_on_delete: '',
                adtxt_errors       :[],
                maxads             :"100",
                hide_ajax          :false,
                QckTags            :false,
                adsTxtEnabled      :false,
                adsforwp_quads_shortcode :false,
                adsforwp_quads_gutenberg :false,
                lazy_load_global      :false,
                global_excluder_enabled: false,  
                adsTxtText         :'',                
                debug_mode         : '',
                ip_geolocation_api : '', 
                ad_blocker_message : false,
                analytics          : false,
                multiUserValue     : [],
                multiTagsValue     : [],
                multiPluginsValue  : [], 
                notice_type        : 'ad_blocker_message', 
                notice_behaviour   : 2, 
                notice_description : 'Our website is made possible by displaying online advertisements to our visitors. Please consider supporting us by whitelisting our website.', 
                notice_title       : 'Adblock Detected!',
                notice_close_btn   : '',
                btn_txt            : 'X',                                                                 
                },
            quads_wp_quads_pro_license_key : '', 
            importampforwpmsg : "", 
            importampforwpmsgprocessing : "",
            importadsforwpmsg : "", 
            importadsforwpmsgprocessing : "",  
            importquadsclassicmsgprocessing : "",
            page_redirect_options   : []          
        };     
  }   
  handleClick_notice_txt_color = () => {
    this.setState({ notice_txt_color_picker: !this.state.notice_txt_color_picker })
  };
    handleClick_notice_bg_color = () => {
    this.setState({ notice_bg_color_picker: !this.state.notice_bg_color_picker })
  };
  handleClick_notice_btn_txt_color = () => {
    this.setState({ notice_btn_txt_color_picker: !this.state.notice_btn_txt_color_picker })
  };
    handleClick_notice_btn_bg_color = () => {
    this.setState({ notice_btn_bg_color_picker: !this.state.notice_btn_bg_color_picker })
  };
  notice_txt_color = (color) => {
  const { settings } = this.state;
  settings.notice_txt_color = color.hex;     
  this.setState(settings);
    this.setState({ notice_txt_color: color.hex })
  };
notice_bg_color = (color) => {
   const { settings } = this.state;
  settings.notice_bg_color = color.hex;     
  this.setState(settings);
  };
  notice_btn_txt_color = (color) => {
    const { settings } = this.state;
  settings.notice_btn_txt_color = color.hex;     
  this.setState(settings);
  };
    notice_btn_bg_color = (color) => {
    const { settings } = this.state;
  settings.notice_btn_bg_color = color.hex;     
  this.setState(settings);
  };
  handleClose = () => {
    this.setState({ notice_txt_color_picker: false,notice_bg_color_picker: false,notice_btn_txt_color_picker: false,notice_btn_bg_color_picker: false })
  };
  handleCopy = () => {
    copy(this.state.textToCopy);
    this.setState({ copied: true });
  }
    quads_classic_ads = () => {
    if(this.state.importquadsclassicmsgprocessing !=''){
      return;
    }
    this.setState({importquadsclassicmsgprocessing: 'Importing Ads'});
    let formData = new FormData();
    formData.append('action', 'quads_sync_ads_in_new_design');
    formData.append('nonce', quads.nonce);
    fetch(ajaxurl,{
      method: "post",
      body: formData              
    })
    .then(res => res.json())
    .then(
      (result) => {         
              this.setState({importquadsclassicmsg: 'Ads have been successfully imported',importquadsclassicmsgprocessing:''});                             
      },        
      (error) => {
      }
    );  
  }
importampforwpdata = () => {
if(this.state.importampforwpmsgprocessing !=''){
  return;
}
      this.setState({importampforwpmsgprocessing: 'Importing Ads'});
    const url = quads_localize_data.rest_url + 'quads-route/import-ampforwp-ads';    
    fetch(url,{
      method: "post",
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-WP-Nonce': quads_localize_data.nonce,
      }              
    })
    .then(res => res.json())
    .then(
      (result) => {
            if(result.status === 't'){              
              this.setState({importampforwpmsg: result.data,importampforwpmsgprocessing:''});
            }                              
      },        
      (error) => {
      }
    );  
  }
  importadsforwpdata = () => {
if(this.state.importadsforwpmsgprocessing !=''){
  return;
}
      this.setState({importadsforwpmsgprocessing: 'Importing Ads'});
    const url = quads_localize_data.rest_url + 'quads-route/import-adsforwp-ads';    
    fetch(url,{
      method: "post",
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-WP-Nonce': quads_localize_data.nonce,
      }              
    })
    .then(res => res.json())
    .then(
      (result) => {
            if(result.status === 't'){              
              this.setState({importadsforwpmsg: result.data,importadsforwpmsgprocessing:''});
            }                              
      },        
      (error) => {
      }
    );  
  }
  open_global_excluder = () => {
    this.setState({global_excluder_modal:true});
  }
  ad_blocker_support = () => {
    this.setState({ad_blocker_support_popup:true});
  }
    click_fraud_protection_popup = () => {
    this.setState({click_fraud_protection_popup:true});
  }
  getPlugins = (search) => {
    let url = quads_localize_data.rest_url + 'quads-route/get-plugins?search='+search;
    if(quads_localize_data.rest_url.includes('?')){
        url = quads_localize_data.rest_url + 'quads-route/get-plugins&search='+search;  
    }    
    fetch(url,{
      method: "get",
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-WP-Nonce': quads_localize_data.nonce,
      }              
    })
    .then(res => res.json())
    .then(
      (result) => {
            if(result.status === 't'){              
              this.setState({multiPluginsOptions: result.data});
            }                              
      },        
      (error) => {
      }
    );  
  }
  getTags = (search) => {
    let url = quads_localize_data.rest_url + 'quads-route/get-tags?search='+search;    
    if(quads_localize_data.rest_url.includes('?')){
        url = quads_localize_data.rest_url + 'quads-route/get-tags&search='+search;  
     }
    fetch(url,{
      method: "get",
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-WP-Nonce': quads_localize_data.nonce,
      }              
    })
    .then(res => res.json())
    .then(
      (result) => {
            if(result.status === 't'){
              this.setState({multiTagsOptions: result.data});
            }                  
      },        
      (error) => {
      }
    );  
  }
  getUserRole = () => {
    const url = quads_localize_data.rest_url + 'quads-route/get-user-role';    
    fetch(url,{
      method: "get",
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-WP-Nonce': quads_localize_data.nonce,
      }              
    })
    .then(res => res.json())
    .then(
      (result) => {
            if(result.status === 't'){
              this.setState({multiUserOptions: result.data});
            }                  
      },        
      (error) => {
      }
    );  
  }
  handleMultiPluginsSearch = (q) => {    
    if(q !== ''){
        this.getPlugins(q);
    } 
}
  handleMultiTagsSearch = (q) => {    
    if(q !== ''){
        this.getTags(q);
    } 
}
handleMultiPluginsChange = (option) => {    
  const { settings } = this.state;
  settings.multiPluginsValue = option;     
  this.setState(settings);
}
  handleMultiTagsChange = (option) => {    
    const { settings } = this.state;
    settings.multiTagsValue = option;     
    this.setState(settings);
}
  handleMultiUsersChange = (option) => {    
        const { settings } = this.state;
        settings.multiUserValue = option;     
        this.setState(settings);
  }  
    page_redirect_select_fun = (option) => {    
        const { settings } = this.state;
        settings.page_redirect_path = option;     
        this.setState(settings);
  }
  sendCustomerMessage = () => {       
    const json_data = {
                  email  : this.state.customer_query_email,
                  message: this.state.customer_query_message,
                  type   : this.state.customer_query_type,
    };
    const url = quads_localize_data.rest_url + 'quads-route/send-customer-query';
    fetch(url,{
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
        if(result.status === 't'){
          this.setState({customer_querey_success: 'Thank you for contacting us. We soon will get in touch with you'});
          this.setState({customer_querey_error: ''});  
        }else{
          this.setState({customer_querey_success: ''});
          this.setState({customer_querey_error: 'Something went wrong. Please check your internet connection'});  
        }         
      },        
      (error) => {
      }
    ); 
  }
   add_license_key = (e) => {
     const { settings } = this.state;
  settings.quads_wp_quads_pro_license_key = e.target.value;     
  this.setState(settings);
  }
    activeLicense = () => {       
    const json_data = {
                  license_key  : this.state.license_key,
    };
    const url = quads_localize_data.rest_url + 'quads-route/send-license-key';
    fetch(url,{
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
    location.reload();      
      },        
      (error) => {
      }
    ); 
  }
  closeQuerySuccess = (e) => {
    this.setState({customer_querey_success : '',importampforwpmsg : '',importadsforwpmsg : '',importquadsclassicmsg : ''});   
  }
  closeQueryError = (e) => {
    this.setState({customer_querey_error: ''});   
  }
  addCustomerQueryEmail = (e) => {
    let value = e.target.value;
    this.setState({customer_query_email: value}); 
  }
  addCustomerQueryType = (e) => {
    let value = e.target.value;
    this.setState({customer_query_type: value});
  }
  addCustomerMessage = (e) => {
    let value = e.target.value;
    this.setState({customer_query_message: value});
  }
  addauto_ad_code = (e) => {
    let value = e.target.value;
    const { settings } = this.state;
    settings.auto_ad_code = value;     
    this.setState(settings);
  }
    addauto_ads_pos = (e) => {
    let value = e.target.value;
    const { settings } = this.state;
    settings.auto_ads_pos = value;     
    this.setState(settings);
  }
    addautoads_post_types = (e) => {
      var options = e.target.options;
      var value = [];
      for (var i = 0, l = options.length; i < l; i++) {
        if (options[i].selected) {
          value.push(options[i].value);
        }
      }         
      const { settings } = this.state;
      settings.autoads_post_types = value;     
      this.setState(settings);
    }
    addautoads_extra_pages = (e) => {
    var options = e.target.options;
      var value = [];
      for (var i = 0, l = options.length; i < l; i++) {
        if (options[i].selected) {
          value.push(options[i].value);
        }
      }         
      const { settings } = this.state;
      settings.autoads_extra_pages = value;     
      this.setState(settings);
  }
  addautoads_user_roles = (e) => {
   var options = e.target.options;
      var value = [];
      for (var i = 0, l = options.length; i < l; i++) {
        if (options[i].selected) {
          value.push(options[i].value);
        }
      }         
      const { settings } = this.state;
      settings.autoads_user_roles = value;     
      this.setState(settings);
  }
  sendCustomerQuery = (e) => {
    e.preventDefault();
    let customer_type    = this.state.customer_query_type;
    let customer_email   = this.state.customer_query_email;
    let customer_message = this.state.customer_query_message;
    if(customer_type == '' || customer_email == '' || customer_message == ''){
      if(customer_type == ''){
        this.setState({customer_querey_error: 'Please select customer type'});
      }
      if(customer_email == ''){
        this.setState({customer_querey_error: 'Please provide a email'});
      }
      if(customer_message == ''){
        this.setState({customer_querey_error: 'Please write your query'});
      }
    }else{
      if(this.validateEmail(customer_email) == true){
        this.sendCustomerMessage();        
      }else{
        this.setState({customer_querey_error: 'Email is not valid. Please provide valid email'});
      }
    }
  }
  validateEmail = (email) => {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }
  getQuadsInfo = () => {
    let url = quads_localize_data.rest_url + 'quads-route/get-quads-info';
    fetch(url,{
      headers: {                    
        'X-WP-Nonce': quads_localize_data.nonce,
      }
    })
    .then(res => res.json())
    .then(
      (result) => {                  
          if(result.info){
            this.setState({textToCopy:result.info});
          }
      },        
      (error) => {
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
        let old_settings = '';
              Object.entries(result).map(([meta_key, meta_val]) => {                
                  if(meta_val){
                    settings[meta_key] =    meta_val;   
                  }                   
              })
          old_settings = {...settings};
          this.setState({settings:settings,old_settings:old_settings});                 
      },        
      (error) => {
      }
    );  
  }
  componentDidMount(){    
    this.getSettings();
    this.getUserRole();
    this.getTags('');
    this.getPlugins('');
    this.getQuadsInfo();
    this.getPageDataMeta('page');
    if(quads_localize_data.licenses == '' && typeof this.state.licensemsg === 'undefined'){
        this.setState({ licensemsg: 'not activated' });
    }else  if(quads_localize_data.licenses.license == 'valid'){
    if(quads_localize_data.licenses.expires == "lifetime"){
        this.setState({ licensemsg: 'License key never expire' });
      }
    } 
  }
  componentDidUpdate(){    
    if(this.state.file_uploaded){
      this.getSettings();
    }    
  }
  limitOptions =() => {
    let rows = [];
    for (let i = 1; i <=20; i++) {
      rows.push(<option key={i} value={i}>{i}</option>);
    }
    rows.push(<option key={100} value="100">Unlimited</option>);
    return rows;
  }
  saveGlobalExcluder = (e) => {
    e.preventDefault();
    this.saveSettings();
    this.setState({global_excluder_modal:false});
  }
  saveAdBlockSuport = (e) => {
    e.preventDefault();
    this.saveSettings();
    this.setState({ad_blocker_support_popup:false});
  }
   saveClickFraud = (e) => {
    e.preventDefault();
    this.saveSettings();
    this.setState({click_fraud_protection_popup:false});
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
        if(this.state.licensemsg == "not activated" && currentpage.path =="settings_licenses"){
          location.reload();
        }
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
        if(this.state.licensemsg == "not activated" && currentpage.path =="settings_licenses"){
          location.reload();
        }
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
  licensesaveSettings = (status) => {                 
      const formData = new FormData();
      formData.append("file", this.state.backup_file);
      formData.append("settings", JSON.stringify(this.state.settings));
      formData.append("requestfrom", 'wpquads2');
      if(status == 'deactivate'){
        formData.append("quads_wp_quads_pro_license_key_deactivate", 'Deactivate License');
        formData.append("quads_settings[quads_wp_quads_pro_license_key]", this.state.quads_wp_quads_pro_license_key);
        formData.append("quads_settings[quads_wp_quads_pro_license_key-nonce]", quads_localize_data.licenses_nonce);
      }
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
          location.reload();                              
        },        
        (error) => {
         location.reload();  
        }
      ); 
    }
    saveSettingsHandler = (e) => {
      e.preventDefault();  
      this.setState({button_spinner_toggle:true});
      this.saveSettings();
    }
    pro_license_key_deactivate = (e) =>{
      e.preventDefault();  
      this.setState({button_spinner_toggle:true,quads_wp_quads_pro_license_key_deactivate:'Deactivate License'});
      this.licensesaveSettings('deactivate');
    }
    validateAdstxt = (e) => {
      e.preventDefault(); 
      const { settings } = { ...this.state };
      let url =  quads_localize_data.rest_url + 'quads-route/validate-ads-txt';
      fetch(url,{
        method: "post",
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-WP-Nonce': quads_localize_data.nonce,
        },        
        body: JSON.stringify(settings.adsTxtText)
      })
      .then(res => res.json())
      .then(
        (result) => {       
          const { settings } = this.state;    
            if(result.errors){             
              settings.adtxt_errors = result.errors;
              this.setState(settings);              
            }else if(result.valid){
              settings.adtxt_errors = [];
              this.setState({adtxt_modal:false});
            }      
        },        
        (error) => {
        }
      ); 
    }  
  formChangeHandler = (event) => {
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
    if(name == 'adsTxtEnabled'){
     this.saveSettings();
    }
    if(name == 'lazy_load_global'){
     this.saveSettings();
    }
    if(name == 'ip_geolocation_api'){
     this.saveSettings();
    }
    if(name == 'adsforwp_quads_shortcode'){
     this.saveSettings();
    }
    if(name == 'adsforwp_quads_gutenberg'){
     this.saveSettings();
    }
  }
  open_ad_text_modal = () =>{        
    this.setState({adtxt_modal:true});
  }
  adsforwp_to_quads_model = () =>{        
    this.setState({adsforwp_to_quads_model:true});
  }
  closeModal = () =>{
    this.setState({adtxt_modal:false, global_excluder_modal:false, ad_blocker_support_popup:false,click_fraud_protection_popup:false,adsforwp_to_quads_model:false});
  } 
  getErrorMessage =(type) => {
    const {__} = wp.i18n;
    let message = '';
    switch (type) {
      case 'invalid_variable':
        message = __( 'Unrecognized variable' );
        break;
      case 'invalid_record':
        message = __( 'Invalid record' , 'quick-adsense-reloaded');
        break;
      case 'invalid_account_type':
        message =  __( 'Third field should be RESELLER or DIRECT', 'quick-adsense-reloaded' );
        break;
      case 'invalid_subdomain':
        message = __( '%s does not appear to be a valid subdomain', 'quick-adsense-reloaded' );
        break;
      case 'invalid_exchange':
        message = __( '%s does not appear to be a valid exchange domain', 'quick-adsense-reloaded' );
        break;
      case 'invalid_tagid':
        message = __( '%s does not appear to be a valid TAG-ID', 'quick-adsense-reloaded' );
        break;  
      default:
        break;
    }
    return message;
  }
    getPageDataMeta = (condition_type) => {
    let url = quads_localize_data.rest_url +"quads-route/get-condition-list?condition="+condition_type;
    if(quads_localize_data.rest_url.includes('?')){
      url = quads_localize_data.rest_url +"quads-route/get-condition-list&condition="+condition_type; 
    }
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': quads_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {      
            this.setState({page_redirect_options:result});          
        },        
        (error) => {
          this.setState({
            quads_is_error: false,           
          });
        }
      );          
  }
  render() {    
   const styles = reactCSS({
      'default': {
        notice_txt_color: {
          background: this.state.notice_txt_color ,
        },
        'notice_bg_color': {
          background: this.state.notice_bg_color ,
        },
        'notice_btn_txt_color': {
          background: this.state.notice_btn_txt_color ,
        },
        'notice_btn_bg_color': {
          background: this.state.notice_btn_bg_color ,
        },
      },
    });    
          const { textToCopy, btnText } = this.state;    
          const {__} = wp.i18n; 
          const {settings} = this.state;
          const page = queryString.parse(window.location.search); 
          let auto_ads_get_post_types = [];
          if(this.state.auto_ads_get_post_types){
            Object.entries(this.state.auto_ads_get_post_types).map(([meta_key, meta_val]) => {   
            auto_ads_get_post_types.push(<option value={meta_key}>{meta_val}</option>);  
            })
            }
          let autoads_excl_user_roles = [];
          if(this.state.autoads_excl_user_roles){
            Object.entries(this.state.autoads_excl_user_roles).map(([meta_key, meta_val]) => {   
            autoads_excl_user_roles.push(<option value={meta_key}>{meta_val}</option>);  
            })
            }
          return (
          <div>         
          <div className="quads-hidden-elements">
            {/* add txt modal */}
           {this.state.adtxt_modal ? 
           <>
           <div className="quads-large-popup-bglayout">  </div> 
           <div className="quads-large-popup">            
            <div className="quads-large-popup-content">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
              <div className="quads-large-popup-title">
             <h1>Ad Txt</h1>
             </div>
             <div className="quads-large-description"></div>
            
             <div className="quads-large-content">
               <textarea cols="80" rows="15" name="adsTxtText" onChange={this.formChangeHandler} value={settings.adsTxtText} />
               To know more about ads.txt  you can <a  target="_blank" href="https://wpquads.com/documentation/what-is-ads-txt-and-how-to-use-it/">view this tutorial</a>
               {settings.adtxt_errors ?
             <div className="quads-modal-error">
               <ul>
              {settings.adtxt_errors.map((error, key) => (            
                <li key={key}>             
                 {error.line} : {this.getErrorMessage(error.type)} 
                </li>
              ))} 
              </ul> 
             </div> 
            :null}
               <a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.validateAdstxt}>Validate</a>
             </div>             
             </div>       
            </div> </>: null
            } 
             {this.state.adsforwp_to_quads_model ? 
           <div className="quads-modal-popup">            
            <div className="quads-modal-popup-content">
             <span className="quads-modal-close" onClick={this.closeModal}>&times;</span>
             <h3>Ads For wp Setting</h3>
             <div className="quads-modal-description"></div>
             <div className="quads-modal-content adsforwp-quads-popup">
             <div className="quads-modal">
             Change adsforwp Short code to quads 
              <label className="quads-switch">
                         <input id="adsforwp_quads_shortcode" type="checkbox" name="adsforwp_quads_shortcode" onChange={this.formChangeHandler} checked={settings.adsforwp_quads_shortcode} />
                         <span className="quads-slider"></span>
                       </label>
            </div>
            <div className="quads-modal">
             Change adsforwp Gutenberg to quads 
              <label className="quads-switch">
                         <input id="adsforwp_quads_gutenberg" type="checkbox" name="adsforwp_quads_gutenberg" onChange={this.formChangeHandler} checked={settings.adsforwp_quads_gutenberg} />
                         <span className="quads-slider"></span>
                       </label>
            </div>
             </div>             
             </div>        
            </div> : null
            } 
            {/* global excluder modal */}
            {this.state.global_excluder_modal ? 
           <div className="quads-modal-popup">            
            <div className="quads-modal-popup-content">
             <span className="quads-modal-close" onClick={this.closeModal}>&times;</span>
             <h3>Global Excluder</h3>                         
             <div className="quads-modal-content">
             <table className="form-table" role="presentation"><tbody><tr>
                                  <th>Hide Ads for User Roles</th>
                                  <td>
                                  <Select
                                    isMulti
                                    name="hide_ads_for_users"
                                    placeholder="Choose Users"
                                    value={settings.multiUserValue}
                                    options={this.state.multiUserOptions}
                                    onChange={this.handleMultiUsersChange}                                    
                                  />
                                  </td>
                                </tr>
                                <tr>
                                  <th>Hide Ads for Tags</th>
                                  <td>
                                  <Select
                                    isMulti
                                    isSearchable 
                                    name="hide_ads_for_tags"
                                    placeholder="Choose Tags"
                                    value={settings.multiTagsValue}
                                    options={this.state.multiTagsOptions}
                                    onChange={this.handleMultiTagsChange}
                                    onInputChange={this.handleMultiTagsSearch}                                    
                                  />
                                  </td>
                                </tr>
                                <tr>
                                  <th>Hide Ads for Plugins</th>
                                  <td>
                                  <Select
                                    isMulti
                                    isSearchable 
                                    name="hide_ads_for_plugin"
                                    placeholder="Choose Plugins"
                                    value={settings.multiPluginsValue}
                                    options={this.state.multiPluginsOptions}
                                    onChange={this.handleMultiPluginsChange}
                                    onInputChange={this.handleMultiPluginsSearch}                                    
                                  />
                                  </td>
                                </tr></tbody></table>
                            <a className="quads-btn quads-btn-primary" onClick={this.saveGlobalExcluder}>OK</a>
             </div>             
             </div>        
            </div> : null
            } 
             {/* Ad Blocker Support */}
            {this.state.ad_blocker_support_popup ? 
            <>
              <div className="quads-large-popup-bglayout">  </div> 
           <div className="quads-large-popup">  
            <div className="quads-large-popup-content">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
            <div className="quads-large-popup-title">
              <h1>Notice For Ad Blocker</h1>   
            </div>                      
             <div className="quads-large-content">
             <table className="form-table" role="presentation"><tbody><tr>
                                  <th>Notice Type</th>
                                  <td className="notice_type">
                                  <span>
                                   <input id="bar" type="radio" value="bar" checked={settings.notice_type =='bar'} name="notice_type" onChange={this.formChangeHandler} />
                                    <label htmlFor="bar"> {__('Bar', 'quick-adsense-reloaded')} </label>
                                    </span><span>
                                   <input id="popup" type="radio" value="popup" checked={settings.notice_type =='popup'}  name="notice_type" onChange={this.formChangeHandler} />
                                   <label htmlFor="popup"> {__('Popup', 'quick-adsense-reloaded')} </label>
                                    </span><span>
                                   <input id="page_redirect" type="radio" checked={settings.notice_type =='page_redirect'}  value="page_redirect" name="notice_type" onChange={this.formChangeHandler}  />
                                    <label htmlFor="page_redirect"> {__('Page Redirection', 'quick-adsense-reloaded')} </label>
                                  </span><span>
                                   <input id="ad_blocker_message" type="radio" checked={settings.notice_type =='ad_blocker_message'} name="notice_type" value="ad_blocker_message"  onChange={this.formChangeHandler} /> 
                                    <label htmlFor="ad_blocker_message"> {__('Block Message ', 'quick-adsense-reloaded')} </label>
                                   </span>
                                   <div className="quads-message bottom">If visitor is using an ad blocker he will see a message instead of an ad, asking him to deactivate the ad blocker. <a href="http://wpquads.com/docs/customize-ad-blocker-notice/" target="_blank">Read here</a> how to customize colors and text.</div>
                                  </td>
                                </tr>
                                <tr>
                                  <th>Notice Behaviour</th>
                                  <td>
                                   <select value={settings.notice_behaviour} onChange={this.formChangeHandler} name="notice_behaviour" id="notice_behaviour">
                                    <option value="2">{__('Show on Every Visit', 'quick-adsense-reloaded')}</option>
                                    <option value="1">{__('Show Only Once', 'quick-adsense-reloaded')}</option> 
                                  </select>
                                  </td>
                                </tr>
                            {settings.notice_type == 'bar' || settings.notice_type == 'popup' ? 
                            <>
                              <tr>
                                <th><b>Notice Content</b></th>
                                <td>
                                </td>
                              </tr>
                              {settings.notice_type == 'popup' ? (
                              <tr>
                                <th>Title</th>
                                <td><input value={settings.notice_title} onChange={this.formChangeHandler} name="notice_title" type="text" placeholder="Adblock Detected!" className="quads-premium-cus" />
                                </td>
                              </tr>
                            ) : null    }
                              <tr>
                                <th>Description</th>
                                <td>
                                  <textarea  name="notice_description" value={settings.notice_description } onChange={this.formChangeHandler}  cols="60" rows="5" className="quads-premium-cus" />
                                </td>
                              </tr>
                              <tr>
                              <th>
                              Close Button
                              </th>
                              <td><input id="notice_close_btn" type="checkbox" name="notice_close_btn" onChange={this.formChangeHandler} checked={settings.notice_close_btn} /></td>
                              </tr>
                              <tr>
                              <th>Button Text</th>
                              <td><input value={settings.btn_txt} onChange={this.formChangeHandler} name="btn_txt" type="text" placeholder="Email" className="quads-premium-cus" /></td>
                              </tr>
                               <tr>
                                <th><b>Notice Design</b></th>
                                <td>
                                </td>
                              </tr>
                              <tr>
                              <th>Content Color</th>
                              <td>
                                 <div>
                                    <div className="color-pick-swatch" onClick={ this.handleClick_notice_txt_color }>
                                      <div >
                                        <div style={ styles.notice_txt_color } className="color-pick-color" /></div>
                                        <span className="wp-color-result-text">Select Color</span>
                                      </div>
                                    { this.state.notice_txt_color_picker ? <div className="color-pick-popover">
                                      <div className="color-pick-cover" onClick={ this.handleClose }/>
                                      <SketchPicker color={ this.state.notice_txt_color } onChange={ this.notice_txt_color } />
                                    </div> : null }
                                  </div>
                                </td>
                              </tr>
                              <tr>
                              <th>Background Color</th>
                              <td>
                                 <div>
                                    <div className="color-pick-swatch" onClick={ this.handleClick_notice_bg_color }>
                                      <div >
                                        <div style={ styles.notice_bg_color } className="color-pick-color" /></div>
                                        <span className="wp-color-result-text">Select Color</span>
                                      </div>
                                    { this.state.notice_bg_color_picker ? <div className="color-pick-popover">
                                      <div className="color-pick-cover" onClick={ this.handleClose }/>
                                      <SketchPicker color={ this.state.notice_bg_color } onChange={ this.notice_bg_color } />
                                    </div> : null }
                                  </div>
                                </td>
                              </tr>
                              <tr>
                              <th>Button Text Color</th>
                              <td>
                                 <div>
                                    <div className="color-pick-swatch" onClick={ this.handleClick_notice_btn_txt_color }>
                                      <div >
                                        <div style={ styles.notice_btn_txt_color } className="color-pick-color" /></div>
                                        <span className="wp-color-result-text">Select Color</span>
                                      </div>
                                    { this.state.notice_btn_txt_color_picker ? <div className="color-pick-popover">
                                      <div className="color-pick-cover" onClick={ this.handleClose }/>
                                      <SketchPicker color={ settings.notice_btn_txt_color } onChange={ this.notice_btn_txt_color } />
                                    </div> : null }
                                  </div>
                                </td>
                              </tr>
                              <tr>
                              <th>Button Background Color</th>
                              <td>
                                 <div>
                                    <div className="color-pick-swatch" onClick={ this.handleClick_notice_btn_bg_color }>
                                      <div >
                                        <div style={ styles.notice_btn_bg_color } className="color-pick-color" /></div>
                                        <span className="wp-color-result-text">Select Color</span>
                                      </div>
                                    { this.state.notice_btn_bg_color_picker ? <div className="color-pick-popover">
                                      <div className="color-pick-cover" onClick={ this.handleClose }/>
                                      <SketchPicker color={ this.state.notice_btn_bg_color } onChange={ this.notice_btn_bg_color } />
                                    </div> : null }
                                  </div>
                                </td>
                              </tr>
                              </>
                                : null }
                            {settings.notice_type == 'page_redirect' ? 
                                 <tr>
                                  <th>Target Page</th>
                                  <td>
                                  <Select
                                    name="page_redirect_path"
                                    placeholder="Choose Page"
                                    value={settings.page_redirect_path}
                                    options={this.state.page_redirect_options}
                                    onChange={this.page_redirect_select_fun}                                    
                                  />
                                  </td>
                                </tr>: null}</tbody></table><div className="quads-save-close">
  To know more about Ad Blocker you can <a  target="_blank" href="https://wpquads.com/documentation/how-to-use-ad-blocker-support-in-wp-quads/">view this </a>
                            <a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.saveAdBlockSuport}>Save Changes</a>
                            </div>
             </div>             
             </div>        
            </div> </>: null
            }
                        {this.state.click_fraud_protection_popup ? 
            <>
              <div className="quads-large-popup-bglayout">  </div> 
           <div className="quads-large-popup">  
            <div className="quads-large-popup-content">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
            <div className="quads-large-popup-title">
              <h1>Click Fraud Protection</h1>   
            </div>                      
             <div className="quads-large-content">
             <table className="form-table" role="presentation"><tbody>
                                  <tr>
                              <th>Allowed clicks</th>
                              <td><input value={settings.allowed_click} onChange={this.formChangeHandler} name="allowed_click" type="text" placeholder="3" className="quads-premium-cus" /></td>
                              </tr>
                               <tr>
                              <th>Click limit (in hours)</th>
                              <td><input value={settings.click_limit} onChange={this.formChangeHandler} name="click_limit" type="text" placeholder="3" className="quads-premium-cus" /></td>
                              </tr>
                               <tr>
                              <th>Ban duration (in days)</th>
                              <td><input value={settings.ban_duration} onChange={this.formChangeHandler} name="ban_duration" type="text" placeholder="3" className="quads-premium-cus" /></td>
                              </tr></tbody></table>
                              <div className="quads-save-close">
                            <a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.saveAdBlockSuport}>Save Changes</a>
                            </div>
             </div>             
             </div>        
            </div> </>: null
            }
          </div>            
          <div className="quads-settings-main">  
          <QuadsAdSettingsNavLink/>
          <div className="quads-settings-content">  
          <form encType="multipart/form-data" method="post" id="quads_settings">      
          {(() => {
            switch (page.path) {
              case "settings":   return(
                <div className="quads-settings-tab-container">
                 <table className="form-table" role="presentation"><tbody><tr>
                     <th><label htmlFor="adsTxtEnabled">ads.txt - {__('Automatic Creation', 'quick-adsense-reloaded')}</label></th> 
                     <td>
                       <label className="quads-switch">
                         <input id="adsTxtEnabled" type="checkbox" name="adsTxtEnabled" onChange={this.formChangeHandler} checked={settings.adsTxtEnabled} />
                         <span className="quads-slider"></span>
                       </label>
                       {this.state.adsTxtEnabled ? <span onClick={this.open_ad_text_modal} className="quads-generic-icon dashicons dashicons-admin-generic"></span> : ''} 
                       <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-ads-txt-and-how-to-use-it/"></a>
                     </td>
                     </tr>
                    {
                      quads_localize_data.is_pro ? 
                      <tr>
                     <th><label htmlFor="global_excluder_enabled">{__('Global Excluder', 'quick-adsense-reloaded')}</label></th> 
                     <td>
                       <label className="quads-switch">
                         <input id="global_excluder_enabled" type="checkbox" name="global_excluder_enabled" onChange={this.formChangeHandler} checked={settings.global_excluder_enabled} />
                         <span className="quads-slider"></span>
                       </label>                       
                       {this.state.global_excluder_enabled ? <span onClick={this.open_global_excluder} className="quads-generic-icon dashicons dashicons-admin-generic"></span> : null}
                     </td>
                     </tr>:null}     
                    <tr>
                   <th><label htmlFor="lazy_load_global">{__('Lazy Loading for Adsense', 'quick-adsense-reloaded')}</label></th>
                    <td>
                        <label className="quads-switch">
                         <input id="lazy_load_global" type="checkbox" name="lazy_load_global" onChange={this.formChangeHandler} checked={settings.lazy_load_global} />
                         <span className="quads-slider"></span>
                       </label>
                       <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-lazy-loading-for-adsense-and-how-to-use-it/"></a>
                      </td>
                      </tr>   
                       <tr>
                     <th><label htmlFor="ad_blocker_support">{__('Ad Blocker Support', 'quick-adsense-reloaded')}</label></th> 
                     <td>
                       <label className="quads-switch">
                         <input id="ad_blocker_support" type="checkbox" name="ad_blocker_support" onChange={this.formChangeHandler} checked={settings.ad_blocker_support} />
                         <span className="quads-slider"></span>
                       </label>                       
                       {this.state.ad_blocker_support ? <span onClick={this.ad_blocker_support} className="quads-generic-icon dashicons dashicons-admin-generic"></span> : null}
                        <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/how-to-use-ad-blocker-support-in-wp-quads/"></a>
                     </td>
                     </tr>
                     <tr>
                     <th><label htmlFor="click_fraud_protection">{__('Click Fraud Protection', 'quick-adsense-reloaded')}</label></th> 
                     <td>
                       <label className="quads-switch">
                         <input id="click_fraud_protection" type="checkbox" name="click_fraud_protection" onChange={this.formChangeHandler} checked={settings.click_fraud_protection} />
                         <span className="quads-slider"></span>
                       </label>                       
                       {this.state.click_fraud_protection ? <span onClick={this.click_fraud_protection_popup} className="quads-generic-icon dashicons dashicons-admin-generic"></span> : null}
                     </td>
                     </tr>
                     </tbody></table>  
                </div>
               );   
              case "settings_tools": return(
                <div className="quads-settings-tab-container">                  
                  <table className="form-table" role="presentation">
                    <tbody>{quads_localize_data.is_pro ?<tr>
                       <th><label   htmlFor="analytics">{__('Google Analytics Integration', 'quick-adsense-reloaded')}</label></th>
                        <td><input  id="analytics" type="checkbox" onChange={this.formChangeHandler} name="analytics" checked={settings.analytics} />
                        <a className="quads-general-helper quads-general-helper-new" href="#"></a><div className="quads-message bottom" >Check how many visitors are using ad blockers in your Google Analytics account from the event tracking in <i>Google Analytics-&gt;Behavior-&gt;Events</i>. This only works if your visitors are using regular ad blockers like 'adBlock'. There are browser plugins which block all external requests like the  software uBlock origin. This also block google analytics and as a result you do get any analytics data at all.</div></td>
                      </tr>
                       :null}<tr>
                       <th><label htmlFor="uninstall_on_delete">{__('Delete Data on Uninstall?', 'quick-adsense-reloaded')}</label></th>
                        <td><input id="uninstall_on_delete" type="checkbox" onChange={this.formChangeHandler} name="uninstall_on_delete" checked={settings.uninstall_on_delete} />
                        <a className="quads-general-helper quads-general-helper-new" href="#"></a><div className="quads-message bottom" >Check this box if you would like <strong>Settings-&gt;WPQUADS</strong> to completely remove all of its data when the plugin is deleted.</div>
                        </td>
                      </tr>
                      <tr>
                       <th><label htmlFor="debug_mode">{__('Debug Mode', 'quick-adsense-reloaded')}</label></th>
                        <td><input id="debug_mode" type="checkbox" onChange={this.formChangeHandler} name="debug_mode" checked={settings.debug_mode} /></td>
                      </tr>
                      <tr>
                       <th><label htmlFor="copy_system_info">{__('Copy System info', 'quick-adsense-reloaded')}</label></th>
                       <td>
                         <a className="quads-btn quads-btn-primary" id="copy_system_info" onClick={this.handleCopy}>{__('Copy System Info', 'quick-adsense-reloaded')}</a>
                         <div>{this.state.copied ? <span>{__('System info copied to clipboard', 'quick-adsense-reloaded')}</span> : null}</div>
                       </td>
                      </tr>
                      <tr>
                        <th><label>{__('Export', 'quick-adsense-reloaded')}</label></th>
                        <td>
                          <a href={`${quads_localize_data.rest_url}quads-route/export-settings`} className="quads-btn quads-btn-primary">Export</a>
                          <p>{__('Export the Quick AdSense Reloaded settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'quick-adsense-reloaded')}</p>
                        </td>
                      </tr></tbody></table>
                </div>
               );  
               case "settings_importer": return(
                               <div className="quads-settings-tab-container">                  
                  <table className="form-table" role="presentation"><tbody>{quads.quads_get_active_ads !== "0" ?
                       <tr>
                        <th><label>{__('Quads Classic view Ads', 'quick-adsense-reloaded')}</label></th>
                        <td>
                          <a className="quads-btn quads-btn-primary" id="quads_import_classic_ads_popup" onClick={this.quads_classic_ads}>{__('Import', 'quick-adsense-reloaded')}</a>
                            {this.state.importquadsclassicmsg  ? <Alert severity="success" action={<Icon onClick={this.closeQuerySuccess}>close</Icon>}>{this.state.importquadsclassicmsg}</Alert> : null }
                            {this.state.importquadsclassicmsgprocessing ? <div className='updating-message importquadsclassicmsgprocessing'><p>Importing Ads</p></div>:null}
                        </td>
                      </tr>  
                      : null}<tr>
                        <th><label>{__('AMP for WP Ads', 'quick-adsense-reloaded')}</label></th>
                        <td>
                          <a className="quads-btn quads-btn-primary" id="import_amp_for_wp" onClick={this.importampforwpdata}>{__('Import', 'quick-adsense-reloaded')}</a>
                            {this.state.importampforwpmsg  ? <Alert severity="success" action={<Icon onClick={this.closeQuerySuccess}>close</Icon>}>{this.state.importampforwpmsg}</Alert> :null}
                            {this.state.importampforwpmsgprocessing ? <div className='updating-message importampforwpmsgprocessing'><p>Importing Ads</p></div>:null}
                        </td>
                      </tr> 
                        <tr>
                        <th><label>{__('ADS for WP Ads', 'quick-adsense-reloaded')}</label></th>
                        <td>
                          <a className="quads-btn quads-btn-primary" id="import_ads_for_wp" onClick={this.importadsforwpdata}>{__('Import', 'quick-adsense-reloaded')}</a>
                          {settings.adsforwp_to_quads == 'imported' ? <span onClick={this.adsforwp_to_quads_model} className="quads-generic-icon import_ads_for_wp dashicons dashicons-admin-generic"></span> : ''} 
                            {this.state.importadsforwpmsg  ? <Alert severity="success" action={<Icon onClick={this.closeQuerySuccess}>close</Icon>}>{this.state.importadsforwpmsg}</Alert> : null}
                            {this.state.importadsforwpmsgprocessing ? <div className='updating-message importadsforwpmsgprocessing'><p>Importing Ads</p></div>: ''}
                        </td>
                      </tr></tbody></table>
                </div>
               );
                case "settings_google_autoads":  return(
                <div className="quads-settings-tab-container">
                <div className="quads-help-support">
                    <div>
                      <h3>{__('Google Auto Ads', 'quick-adsense-reloaded')}</h3>
                    </div>
                    <div>
                   <p> <a href="https://wpquads.com/docs/add-google-auto-ads-wordpress/" target="_blank">Read this</a> to learn how to create Google auto ads and to learn more about this new ad type. After activation, Google detects on his own where to place ads on your website. If you want to place ads manually leave auto ads empty and use the <a href="#quads_settingsadsense_header">regular ad codes</a> instead.</p> <p> Any code that you place into this field will be added to the head of your website.</p> 
                    </div>
                    <div>
                      {__('Enter Google Auto Ads code below', 'quick-adsense-reloaded')}
                    </div>
                    <div><textarea  name="auto_ad_code" value={this.state.auto_ad_code} onChange={this.addauto_ad_code}  cols="60" rows="5" className="quads-premium-cus" /></div>
                     <div>
                      {__('Status', 'quick-adsense-reloaded')}
                    </div>
                    <div>  <select name="customer_query_type" value={this.state.auto_ads_pos} onChange={this.addauto_ads_pos} className="quads-premium-cus"> m
                        <option value="disabled">{__('Auto Ads Disabled', 'quick-adsense-reloaded')}</option>
                        <option value="enabled">{__('Auto Ads Enabled', 'quick-adsense-reloaded')}</option>
                      </select></div>
                       <div>
                      {__('Exclude Auto Ads From Post Types', 'quick-adsense-reloaded')}
                    </div>
                    <div>  <select multiple={true} name="autoads_post_types" value={this.state.autoads_post_types} onChange={this.addautoads_post_types} className="quads-premium-cus">
                       {auto_ads_get_post_types}
                      </select></div>
                       <div>
                      {__('Exclude Auto Ads From Extra pages', 'quick-adsense-reloaded')}
                    </div>
                    <div>  <select multiple={true} name="autoads_extra_pages" value={this.state.autoads_extra_pages}  onChange={this.addautoads_extra_pages} className="quads-premium-cus">
                        <option value="none">{__('Exclude nothing', 'quick-adsense-reloaded')}</option>
                        <option value="homepage">{__('homepage', 'quick-adsense-reloaded')}</option>
                      </select></div>
                         <div>
                      {__('Exclude Auto Ads From User Roles', 'quick-adsense-reloaded')}
                    </div>
                    <div>  <select multiple={true} name="autoads_user_roles" value={this.state.autoads_user_roles}  onChange={this.addautoads_user_roles} className="quads-premium-cus">
                      {autoads_excl_user_roles}
                      </select></div>
                </div>
                </div>
               );
              case "settings_legacy":  return(
                <div className="quads-settings-tab-container">
                 <table className="form-table" role="presentation"><tbody><tr>
                    <th scope="row"><label>{__('Limit Amount of ads', 'quick-adsense-reloaded')}</label></th>
                    <td>
                      <select  name="maxads" value={settings.maxads} onChange={this.formChangeHandler}>
                      {this.limitOptions()}
                      </select> {__('ads on a page', 'quick-adsense-reloaded')}
                      <p><a target="_blank" href="https://wpquads.com/google-adsense-allowed-number-ads/">{__('Read here', 'quick-adsense-reloaded')}</a> {__('to learn how many AdSense ads are allowed. If you are unsure set the value to unlimited.', 'quick-adsense-reloaded')}</p>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label htmlFor="hide_ajax">{__('Hide Ads From Ajax Requests', 'quick-adsense-reloaded')}</label></th>
                    <td>
                      <input id="hide_ajax" type="checkbox" name="hide_ajax" checked={settings.hide_ajax} onChange={this.formChangeHandler} />
                      <p>{__('If your site is using ajax based infinite loading it might happen that ads are loaded without any further post content. Disable this here.', 'quick-adsense-reloaded')}</p>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label htmlFor="QckTags">{__('Quicktags', 'quick-adsense-reloaded')}</label></th>
                    <td>
                      <input id="QckTags" type="checkbox" name="QckTags" checked={settings.QckTags} onChange={this.formChangeHandler} />{__('Show Quicktag Buttons on the HTML Post Editor', 'quick-adsense-reloaded')}
                      <p>{__('Tags can be inserted into a post via the additional Quicktag Buttons at the HTML Edit Post SubPanel.', 'quick-adsense-reloaded')}</p>
                      <p><strong>Optional:</strong>{__('Insert Ads into a post, on-the-fly using below tags', 'quick-adsense-reloaded')}</p>                                    
                      <p>{__('1. Insert', 'quick-adsense-reloaded')} &lt;!--Ads1--&gt;, &lt;!--Ads2--&gt;, {__('etc. into a post to show the Particular Ads at specific location.', 'quick-adsense-reloaded')}</p>
                      <p>{__('2. Insert', 'quick-adsense-reloaded')} &lt;!--RndAds--&gt; {__('into a post to show the Random Ads at specific location', 'quick-adsense-reloaded')}</p>
                    </td>
                  </tr></tbody></table>
                </div>
               );  
              case "settings_support":  return(
                <div className="quads-settings-tab-container">
                <div className="quads-hs">
                <div className="quads-docm">
                  <a className="quads-doc-link" target="_blank" href="https://wpquads.com/documentation/">
                  <img height="121" width="121" src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/docs-img.png'} />
                    <h4>Knowledge Base</h4>
                    <p>Read our documentation & find what you're looking for</p>
                    <span className="quads-lm">View Docs</span>
                  </a>
                </div>
                <div className="quads-help-support">
                    <div>
                      <h3>{__('Ask for technical Support', 'quick-adsense-reloaded')}</h3>
                      <p>{__('We are always available to help you with anything related to ads', 'quick-adsense-reloaded')}</p>
                    </div>
                    <div className="quads-pre-cu">
                      <span>
                        {__('Are you existing Premium Customer?', 'quick-adsense-reloaded')}
                      </span>
                    <div>
                      <select name="customer_query_type" value={this.state.customer_query_type} onChange={this.addCustomerQueryType} className="quads-premium-cus">
                        <option value="">{__('Select', 'quick-adsense-reloaded')}</option>
                        <option value="yes">{__('Yes', 'quick-adsense-reloaded')}</option>
                        <option value="no">{__('No', 'quick-adsense-reloaded')}</option>
                      </select>
                    </div>
                    </div>
                    <div><input value={this.state.customer_query_email} onChange={this.addCustomerQueryEmail} name="customer_query_email"type="text" placeholder="Email" className="quads-premium-cus" /></div>
                    <div>
                    <textarea  name="customer_query_message" value={this.state.customer_query_message} onChange={this.addCustomerMessage} placeholder="Write your query here" cols="60" rows="5" className="quads-premium-cus" />
                    </div>
                    <div>
                      <a className="button quads-premium-cus" onClick={this.sendCustomerQuery}>{__('Send', 'quick-adsense-reloaded')}</a>
                    </div>
                    {this.state.customer_querey_error ? <Alert severity="error" action={<Icon onClick={this.closeQueryError}>close</Icon>}>{this.state.customer_querey_error}</Alert> : null}
                    {this.state.customer_querey_success ? <Alert severity="success" action={<Icon onClick={this.closeQuerySuccess}>close</Icon>}>{this.state.customer_querey_success}</Alert> : null}
                  {/* <div>
                    <h3>System Info</h3>
                    <textarea className="quads-system-info" readOnly={true} value={this.state.textToCopy}/>
                  </div> */}
                </div>
                </div>
                </div>
               );
               case "settings_licenses":  return(
                <div className="quads-settings-tab-container">
                <div className="quads-help-support">
                    <div>
                      <h3>{__('Activate Your License', 'quick-adsense-reloaded')}</h3>
                    </div>
                    <div>
                      {__('WP QUADS PRO License Key', 'quick-adsense-reloaded')}
                    </div>
                   <div><input value={this.state.quads_wp_quads_pro_license_key} onChange={this.add_license_key} name="quads_wp_quads_pro_license_key" type="text" placeholder="License Key" className="quads-premium-cus" />
                      {quads_localize_data.licenses.license == "valid" ? <a onClick={this.pro_license_key_deactivate} className="quads-btn quads-btn-primary">
            Deactivate License</a>: null}    </div> 
            {this.state.licensemsg ?
            <div id="quads_licensemsg">{this.state.licensemsg}</div> : null}
                  {/* <div>
                    <h3>System Info</h3>
                    <textarea className="quads-system-info" readOnly={true} value={this.state.textToCopy}/>
                  </div> */}
                </div>
                </div>
               );
            }
          })()}
          {page.path == 'settings_support' || page.path == 'settings_importer' ? '' : (
            <div className="quads-save-settings">
            {this.state.button_spinner_toggle ?
            <a className="quads-btn quads-btn-primary">
            <span className="quads-btn-spinner"></span>Saving...
            </a> :           
            <a onClick={this.saveSettingsHandler} className="quads-btn quads-btn-primary">
            Save Settings              
            </a>          
          }            
          </div>   
          )           }
          </form>
          <div className="quads-bnr">
            <a href="http://wpquads.com/?utm_source=wpquads&utm_medium=banner&utm_term=click-quads&utm_campaign=wpquads" target="_blank">
              <img  src={quads_localize_data.quads_plugin_url+'assets/images/quads_banner_250x521_buy.png'} />
           </a>
          </div>
          </div>   
          </div>          
          </div>
        );
  }
}
export default QuadsAdListSettings;