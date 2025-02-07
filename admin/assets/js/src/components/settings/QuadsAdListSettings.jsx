import React, { Component, Fragment } from 'react';
import { BrowserRouter as Router, Switch, Route, Link } from 'react-router-dom';
import queryString from 'query-string'
import Icon from '@material-ui/core/Icon';
import { Alert } from '@material-ui/lab';
import MenuItem from '@material-ui/core/MenuItem';
import FormControl from '@material-ui/core/FormControl';
import MSelect from '@material-ui/core/Select';
import Select from "react-select";
//import "react-select/dist/react-select.css";
import './QuadsAdListSettings.scss';
import QuadsAdSettingsNavLink from './QuadsAdSettingsNavLink';
import QuadsAdSettingsProTemplate from './QuadsAdSettingsProTemplate';
import copy from 'copy-to-clipboard';
import { SketchPicker } from 'react-color';
import reactCSS from 'reactcss';
import QuadsVerticalTabs from './QuadsVerticalTabs';
import QuadsDisabledAdsList from './QuadsDisabledAdsList';
const {__} = wp.i18n;
// import {saveAs} from "file-saver";
class QuadsAdListSettings extends Component {
  constructor(props) {
    super(props);
    this.state = {
            display_pro_alert_msg : false,
            notice_txt_color_picker: false,
            notice_bg_color_picker : false,
            notice_btn_txt_color_picker : false,
            notice_btn_bg_color_picker : false,
            button_spinner_toggle: false,
            selectedBtnOpt: '',
            quads_pro_list_selected:[],
            multiUserOptions: [],
            multiTagsOptions: [],
            multiPluginsOptions: [],
            file_uploaded :false,
            settings_saved :false,
            quadsIsAdmin :false,
            settings_error :'',
            adtxt_modal :false,
            sellable_ads_modal :false,
            disable_ads_modal :false,
            disable_list_modal :false,
            global_excluder_modal :false,
            revenue_sharing_modal :false,
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
                revenue_sharing_enabled : false,
                hide_quads_markup : false,
                tcf_2_integration   : false,
                rotator_ads_settings   : true,
                group_insertion_settings : true,
                blindness_settings : true,
                ab_testing_settings : true,
                skippable_ads : true,
                ad_performance_tracking : false,
                exclude_admin_tracking : false,
                sellable_ads : true,
                disableads : true,
                reports_settings : true,
                ad_logging : false,
                ad_owner_revenue_per:50,
                ad_author_revenue_per:50,
                notice_bg_color : '#1e73be',
                allowed_click   : 3,
                click_limit     : 3,
                ban_duration    : 7,
                checkbox_value    : false,
                notice_btn_txt_color : '#ffffff',
                notice_btn_bg_color : '#f44336',
                uninstall_on_delete: '',
                adtxt_errors       :[],
                maxads             :100,
                hide_ajax          :false,
                QckTags            :false,
                adsTxtEnabled      :false,
                adsforwp_quads_shortcode :false,
                adsforwp_quads_gutenberg :false,
                advance_ads_to_quads_model :false,
                advance_ads_to_quads : false,
                lazy_load_global      :false,
                global_excluder_enabled: false,
                adsTxtText         :'',
                debug_mode         : '',
                ip_geolocation_api : '',
                ad_blocker_message : false,
                report_logging     : 'combined_legacy',
                logging_toggle     : false,
                analytics          : false,
                multiUserValue     : [],
                RoleBasedAccess    : [],
                multiTagsValue     : [],
                multiPluginsValue  : [],
                notice_type        : 'ad_blocker_message',
                notice_behaviour   : 2,
                notice_bar         : 2,
                notice_bar_sticky  : '',
                notice_description : 'Our website is made possible by displaying online advertisements to our visitors. Please consider supporting us by whitelisting our website.',
                notice_title       : 'Adblock Detected!',
                notice_close_btn   : '',
                btn_txt            : 'X',
                adsforwp_to_quads  : false,
                optimize_core_vitals : false, 
                namer : '', 
                currency : 'USD',
                email_notification_adsell_expiry : true,
                paypal_email : '',
                payment_gateway : '',
                payment_page : 'buy-adspace',
                authorize_name : '',
                authorize_transactionKey : '',
                authorize_merchant_name : '',
                stripe_publishable_key : '',
                stripe_secret_key : '',
                _dacurrency : 'USD',
                _daemail_notification_adsell_expiry : true,
                _dapaypal_email : '',
                _dapayment_gateway : '',
                
                dapayment_page : 'disable-ads',
                _daauthorize_name : '',
                _daauthorize_transactionKey : '',
                _daauthorize_merchant_name : '',
                _dastripe_publishable_key : '',
                _dastripe_secret_key : '',
                _dacost : '',
                _daduration : 'monthly',
            },
            quads_wp_quads_pro_license_key : '',
            importampforwpmsg : "",
            importampforwpmsgprocessing : "",
            importadsforwpmsg : "",
            importadsforwpmsgprocessing : "",
            importadvancedadsmsg : '',
            importadvancedadsmsgprocessing : "",
            importquadsclassicmsgprocessing : "",
            page_redirect_options   : [],
            selectedFile  : '',
            isLoading : true,
            blocked_ips:'',
            q_admin_url:'',
            black: true,
            checked: false,
            role_permission_modal :false,
            pages: [],
        };
  }
  onFileChange = (event) => {
    
    // Update the state
    this.setState({ selectedFile: event.target.files[0] });
  
  };
  import_settings = () => {
    
    this.setState({ isLoading: true });
    // Create an object of formData
    const formData = new FormData();
    // Update the formData object
    formData.append(
      "myFile",
      this.state.selectedFile,
      this.state.selectedFile.name
    );
    const url = quads_localize_data.rest_url + 'quads-route/import-settings';

    fetch(url,{
      method: "post",
      body: formData,
      headers: {
          'Accept': 'application/json',
          'X-WP-Nonce': quads_localize_data.nonce,
      }
  }) .then(res => res.json())
      .then(
          (result) => {
            this.setState({ isLoading: false,selectedFile:'' });
            
          },
          (error) => {
          }
      );
  };
  
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
  export_settings = () => {
    const url = quads_localize_data.rest_url + 'quads-route/export-settings';
    fetch(url,{
        method: "post",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-WP-Nonce': quads_localize_data.nonce,
        }
    }) .then(res => res.json())
        .then(
            (result) => {
            let jsonData =result;
            let filename ='quads-settings';
                const fileData = JSON.stringify(jsonData);
                const blob = new Blob([fileData], {type: "text/plain"});
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.download = `${filename}.json`;
                link.href = url;
                link.click();
            },
            (error) => {
            }
        );
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

  importadvancedadsdata = () => {
      if(this.state.importadvancedadsmsgprocessing !=''){
        return;
      }
      this.setState({importadvancedadsmsgprocessing: 'Importing Ads'});
    const url = quads_localize_data.rest_url + 'quads-route/import-advance-ads';
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
              this.setState({importadvancedadsmsg: result.data,importadvancedadsmsgprocessing:''});
            }
      },
      (error) => {
      }
    );
  }
  open_global_excluder = () => {
    this.setState({global_excluder_modal:true});
  }
  open_revenue_sharing_excluder = () => {
    this.setState({revenue_sharing_modal:true});
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
  getCurrentUser = () => {
    const url = quads_localize_data.rest_url + 'quads-route/get-current-user';
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
              this.setState({quadsIsAdmin:true});
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
  
  handleRoleBasedAccess = (option) => {
    const { settings } = this.state; 
    if(option?.length >= 1 ){
      const rolesWithCapabilities = option.map((role, index) => {
        if (!('setting_access' in role)) {
          return { ...role, setting_access: true };
        }
        return role;
      });
    settings.RoleBasedAccess = rolesWithCapabilities;
    this.state.settings.namer = 'RoleBasedAccess';
    this.setState(settings);
    this.saveSettings();
  }else{
    settings.RoleBasedAccess = [];
    this.state.settings.namer = 'RoleBasedAccess';
    this.setState(settings);
    this.saveSettings();
  }
}

handleCapabilityChange = (event) =>{
  const { settings } = this.state;
  const tabsindex = event.target.dataset.index;
  const value = event.target.checked?true:false;
  const rolesWithCapabilities = settings.RoleBasedAccess.map((role, index) => {
    if(index == tabsindex){
      return { ...role, setting_access: value };
    }
    return role;
  });
  settings.RoleBasedAccess = rolesWithCapabilities;
  this.state.settings.namer = 'RoleBasedAccess';
  this.setState(settings);
  this.saveSettings();
  
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
          this.setState({customer_querey_success: __('Thank you for contacting us. We soon will get in touch with you', 'quick-adsense-reloaded')});
          this.setState({customer_querey_error: ''});
        }else{
          this.setState({customer_querey_success: ''});
          this.setState({customer_querey_error: __('Something went wrong. Please check your internet connection', 'quick-adsense-reloaded')});
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
    this.setState({customer_querey_success : '',importampforwpmsg : '',importadsforwpmsg : '',importadvancedadsmsg : '',importquadsclassicmsg : ''});
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
        this.setState({customer_querey_error: __('Please select customer type', 'quick-adsense-reloaded')});
      }
      if(customer_email == ''){
        this.setState({customer_querey_error: __('Please provide a email', 'quick-adsense-reloaded')});
      }
      if(customer_message == ''){
        this.setState({customer_querey_error: __('Please write your query', 'quick-adsense-reloaded')});
      }
    }else{
      if(this.validateEmail(customer_email) == true){
        this.sendCustomerMessage();
      }else{
        this.setState({customer_querey_error: __('Email is not valid. Please provide valid email', 'quick-adsense-reloaded')});
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
                 // if(meta_val != ''){
                    settings[meta_key] =    meta_val;
                 // }
              })
          old_settings = {...settings};
          this.setState({settings:settings,old_settings:old_settings});
          this.setState({ isLoading: false });

      },
      (error) => {
        this.setState({ isLoading: false });

      }
    );
  }

  get_blocked_ips () {
    var blocked_ips = quads_localize_data.quads_get_ips;
    var q_admin_url = quads_localize_data.ajax_url;

    if( blocked_ips.length > 0 ){
      this.setState({ blocked_ips: blocked_ips });
    }else{
      blocked_ips = 0;
    }
    if(q_admin_url){
      this.setState({ q_admin_url: q_admin_url });
    }else{
      q_admin_url = ''
    }
  }
  getPages = () => {
    let url = quads_localize_data.rest_url + 'quads-route/get-pages';
    fetch(url,{
      headers: {
        'X-WP-Nonce': quads_localize_data.nonce,
      }
    })
    .then(res => res.json())
    .then(
      (data) => {
        console.log(data);
          this.setState({pages:data});
      },
      (error) => {
      }
    );
  }

  
  componentDidMount(){
    this.get_blocked_ips();
    this.getSettings();
    this.getCurrentUser();
    this.getUserRole();
    this.getTags('');
    this.getPlugins('');
    this.getQuadsInfo();
    this.getPageDataMeta('page');
    this.getPages();
    if(quads_localize_data.licenses == '' && typeof this.state.licensemsg === 'undefined'){
        this.setState({ licensemsg: __('Please activate your WP QUADS PRO License Key', 'quick-adsense-reloaded') });
    }else  if(quads_localize_data.licenses.license == 'valid'){
    if(quads_localize_data.licenses.expires == "lifetime"){
        this.setState({ licensemsg: __('License key never expire', 'quick-adsense-reloaded') });
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
    rows.push(<option key={100} value="100">{__('Unlimited', 'quick-adsense-reloaded')}</option>);
    return rows;
  }
  saveGlobalExcluder = (e) => {
    e.preventDefault();
    this.saveSettings();
    this.setState({global_excluder_modal:false});
  }
  saveRevenueSharing = (e) => {
    e.preventDefault();
    this.saveSettings();
    this.setState({revenue_sharing_modal:false});
  }
  saveAdBlockSuport = (e) => {
    e.preventDefault();
    this.saveSettings();
    this.setState({ad_blocker_support_popup:false});
    this.setState({button_spinner_toggle:true});
  }
   saveClickFraud = (e) => {
    e.preventDefault();
    this.saveSettings();
    this.setState({click_fraud_protection_popup:false});
  }
  quadsUserHasSettingsAccess() {
    let roles_access =  this.state.settings;
    let user_roles = quads_localize_data.user_roles;
    roles_access = roles_access.RoleBasedAccess;
      for (let role of user_roles) {
          if(role == 'administrator' || role == 'super_admin'){
            return true;
          }
          let roleAccess = roles_access ? roles_access.find(item => item.value === role) : '';
          if (roleAccess && roleAccess.setting_access === true) {
              return true; 
          }
      }
      return false; 
  }
    saveSettings = ( reload_page = false ) => {

      if(!this.quadsUserHasSettingsAccess){
         return __('Unauthorised Action', 'quick-adsense-reloaded');
      }
      this.closeModal();
      const formData = new FormData();
      formData.append("file", this.state.backup_file);
      formData.append("settings", JSON.stringify(this.state.settings));
      formData.append("requestfrom",'wpquads2');
      let url = quads_localize_data.rest_url + 'quads-route/update-settings';
      const currentpage = queryString.parse(window.location.search);
      if(currentpage.path =="settings_licenses")
      {
        let lsc_key=document.querySelector("input[name='quads_wp_quads_pro_license_key']").value;
        if(!lsc_key)
        {
          document.getElementById('quads_licensemsg').textContent=__('Please enter valid license!', 'quick-adsense-reloaded');
          this.setState({button_spinner_toggle:false});
          return;
        }
      }

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
        if( (this.state.licensemsg == "Please activate your WP QUADS PRO License Key" && currentpage.path =="settings_licenses") ||( currentpage.path =="settings_licenses" && result.status == "license_validated" && result.license == "valid" )){
          location.reload();
        }
        if(result.status == "lic_not_valid" && result.license == "invalid" && quads_localize_data.licenses.error != "expired" ){
            setTimeout(function(){ 
            var elementsArray = document.getElementsByClassName("inv_msg");
            if(elementsArray){elementsArray[0].style.display = 'block';}  
           }, 100);
        }
        if( quads_localize_data.licenses.error == "expired" && quads_localize_data.licenses.expires < 0 ){
            setTimeout(function(){ 
            var elementsArray = document.getElementsByClassName("exp_msg");
            if(elementsArray){elementsArray[0].style.display = 'block';}
            if(elementsArray){elementsArray[0].style.color = 'red';}
           }, 100);
        }
            if(result.status === 't'){
              if(result.file_status === 't'){
                this.setState({file_uploaded:true,button_spinner_toggle:false, selectedBtnOpt:null});
                this.setState({settings_saved:true});
              }else{
                this.setState({settings_saved:true, button_spinner_toggle:false, selectedBtnOpt:null});
              }
            }else{
              var createDiv = document.createElement('div');
              createDiv.className = "quads_response-suc-wrap bottom-left";
              document.body.appendChild(createDiv);
              var quads_response_suc = document.getElementsByClassName("quads_response-suc-wrap bottom-left")[0];
    quads_response_suc.innerHTML += "<div class='quads_response-suc-single quads_response-suc-success'><span class='quads_response-suc-loader quads_response-suc-loaded'></span>Settings Saved</div>";
              setTimeout(() => {
                var quads_response_suc_ = document.getElementsByClassName("quads_response-suc-wrap bottom-left")[0];
                quads_response_suc_.remove();
              }, 1000);

              this.setState({settings_error:result.msg, button_spinner_toggle:false, selectedBtnOpt:null});
              if(reload_page){
                location.reload();
              }
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
        if(this.state.licensemsg == "Please activate your WP QUADS PRO License Key" && currentpage.path =="settings_licenses"){
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
      if(status == 'refresh'){
        formData.append("refresh_license", true);
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
    pro_license_key_refresh = (e) =>{
      e.preventDefault();
      this.setState({button_spinner_toggle:true});
      this.licensesaveSettings('refresh');
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
    selectLoggingChangeHandler = (event) => {
      let select_value  = event.target.value;
      if(select_value == 'combined_legacy'){
        this.setState({settings:{report_logging:select_value,logging_toggle:false}});
      }else{
        this.setState({settings:{report_logging:select_value,logging_toggle:true}});
      }
      const { settings } = this.state;
      settings.report_logging = select_value;
    }
  formChangeHandler = (event) => {
    let name  = event.target.name;
    console.log(name);
    
    //spin
    this.setState({selectedBtnOpt:name});
    this.state.settings.namer  = name;
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
    if(name == 'adsTxtEnabled' || name == 'global_excluder_enabled' || name == 'lazy_load_global' || name == 'ad_blocker_support' || name == 'click_fraud_protection' || name == 'revenue_sharing_enabled' || name == 'hide_quads_markup' || name == 'optimize_core_vitals'){
      this.saveSettings();
     }
     if(name == 'ip_geolocation_api'){
      this.saveSettings();
     }
     if(name == 'tcf_2_integration'){
      this.saveSettings();
     }
     if(name == 'rotator_ads_settings' || name == 'group_insertion_settings' || name == 'blindness_settings' || name == 'ab_testing_settings' || name == 'reports_settings' || name == 'ad_performance_tracking'|| name == 'report_logging' || name == 'ad_log' || name == 'global_excluder' || name == 'delay_ad_sec' || name == 'skippable_ads' || name == 'exclude_admin_tracking' || name == 'sellable_ads' || name == 'disableads'){
      
      if(name == 'sellable_ads' || name == 'disableads'){
        if(name==='sellable_ads'){
          if(value===true){
            if(document.getElementById('handleShowSellableAds')){
              document.getElementById('handleShowSellableAds').click();
            }
          }else{
            if(document.getElementById('handleHideSellableAds')){
              document.getElementById('handleHideSellableAds').click();
            }
          }
        }
        this.saveSettings();
      }else{
        this.saveSettings();
      }
    }
    if(name == 'adsforwp_quads_shortcode'|| name == 'adsforwp_quads_gutenberg' || name == 'advance_ads_to_quads'){
     this.saveSettings();
    }
     if(name == 'ad_owner_revenue_per'){
      const { settings } = this.state;
      let { value, min, max } = event.target;
      value = Math.max(Number(min), Math.min(Number(max), Number(value)));
      settings['ad_author_revenue_per'] = 100 - value;
      this.setState({ settings });

    }
    if(name == 'ad_author_revenue_per'){
     const { settings } = this.state;
      let { value, min, max } = event.target;
      value = Math.max(Number(min), Math.min(Number(max), Number(value)));
      settings['ad_owner_revenue_per'] = 100 - value;
      this.setState({ settings });
    }
  }

  formhandler = (e) => {
    if (window.confirm(__("You are about to clear Log Data, do you wish to continue?", 'quick-adsense-reloaded'))) {
    e.preventDefault();
    fetch( this.state.q_admin_url+'?action=quads_id_delete&nonce='+quads.nonce , {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
          },      
        }).then((response) => response.json())
        setTimeout(() => {
          document.getElementById("blocked_id_main").innerHTML = __('Data is cleared', 'quick-adsense-reloaded')
        document.getElementById("blocked_id_main").style.fontWeight = '500'
        }, 500);
      }
    }

      quads_open_mainblock = (event) =>{
        let name  = event.target.name;
      let value = '';
      if(event.target.type === 'checkbox'){
        value = event.target.checked;
      }else{
        value = event.target.value
      }
        const { settings } = this.state;
        settings[name] = value;
        this.setState(settings);

        if(name == 'checkbox_value'){
          this.saveSettings();
         }
         
         this.setState({
          checked: !this.state.checked
        } )
  }      
  open_ad_text_modal = () =>{
    this.setState({adtxt_modal:true});
  }
  open_sellable_ad_modal = () =>{
    this.setState({sellable_ads_modal:true});
  }
  open_disable_ad_modal = () =>{
    this.setState({disable_ads_modal:true});
  }
  open_disable_list_modal = () =>{
    this.setState({disable_list_modal:true});
  }
  open_role_permission_modal = () =>{
    this.setState({role_permission_modal:true});
  }
  adsforwp_to_quads_model = () =>{
    this.setState({adsforwp_to_quads_model:true});
  }
  advance_ads_to_quads_model = () =>{
    this.setState({advance_ads_to_quads_model:true});
  }
  closeModal = () =>{
    this.setState({adtxt_modal:false, sellable_ads_modal:false,disable_ads_modal:false,disable_list_modal:false, global_excluder_modal:false, ad_blocker_support_popup:false,click_fraud_protection_popup:false,adsforwp_to_quads_model:false,advance_ads_to_quads_model:false,revenue_sharing_modal:false,role_permission_modal:false});
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
    display_pro_alert_fun = (event) => {
        let name  = event.target.name;
        let value  = event.target.value?event.target.value:'';
        let { quads_pro_list_selected } = this.state;
        if(this.state.display_pro_alert_msg == name && name!=''){
            name ='';
        }
        if(value){
            quads_pro_list_selected.push(value);
        }
        if(!event.target.checked){
          quads_pro_list_selected = quads_pro_list_selected.filter(item => item !== value)
        }
        this.setState({display_pro_alert_msg: name,quads_pro_list_selected:quads_pro_list_selected});
    }
    quads_pro_list_selected = (event) => {
    
      let name  = event.target.name;
      let value  = event.target.value;
      const { quads_pro_list_selected } = this.state;
      quads_pro_list_selected[name] = value;
      this.setState();
  }
 
  render() {
    const quads_setting_pro_items =[
      {id:'skippable_ads',title:'Skippable Ad',url:'https://wpquads.com/documentation/how-to-ad-skippable-ads/'},
      {id:'blindness_settings',title:'Ad Blindness',url:'https://wpquads.com/documentation/how-to-enable-ad-blindness-2/'},
      {id:'ab_testing_settings',title:'AB Testing',url:'https://wpquads.com/documentation/how-to-add-ab-testing'},
      {id:'optimize_core_vitals',title:'Optimize for Core Web Vitals',url:'https://wpquads.com/documentation/how-to-optimize-core-web-vitals-with-wp-quads-plugin/'},
      {id:'hide_quads_markup',title:'Hide Quads Markup',url:'https://wpquads.com/documentation/how-to-hide-extra-quads-markup-from-ads/'},
      {id:'global_excluder',title:'Global Excluder',url:'https://wpquads.com/documentation/how-to-globally-exclude-or-hide-ads-for-user-roles-with-wp-quads-pro/'},
      {id:'ad_log',title:'AD Logging',url:'https://wpquads.com/documentation/how-to-enable-ad-logging/'},
      {id:'delay_ad_sec',title:'Lazy Load'},
     ];

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

          if(settings.auto_ads_get_post_types){
            Object.entries(settings.auto_ads_get_post_types).map(([meta_key, meta_val]) => {
            auto_ads_get_post_types.push(<option key={meta_key} value={meta_key}>{meta_val}</option>);
            })
            }
          let autoads_excl_user_roles = [];
          if(settings.autoads_excl_user_roles){
            Object.entries(settings.autoads_excl_user_roles).map(([meta_key, meta_val]) => {
            autoads_excl_user_roles.push(<option key={meta_key} value={meta_key}>{meta_val}</option>);
            })
            }
          return (
          <div>
            {this.state.isLoading ? <div className="quads-cover-spin"></div>
                    : null}
          <div className="quads-hidden-elements">
            {/* add txt modal */}
           {this.state.sellable_ads_modal ?
           <>
           <div className="quads-large-popup-bglayout">  </div>
           <div className="quads-large-popup">
            <div className="quads-large-popup-content1">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
              <div className="quads-large-popup-title">
             <h1>{__('Sellable Ads', 'quick-adsense-reloaded')}</h1>
             </div>
             <div className="quads-large-description"></div>
             <div className="quads-large-content">
                 <div className="quads-settings-tab-container">
                 <table className="form-table" role="presentation"><tbody>
                  <tr>
                  <th scope="row"><label>{__('Payment Gateway', 'quick-adsense-reloaded')}</label></th>
                  <td>
                  <select value={settings.payment_gateway} onChange={this.formChangeHandler} name="payment_gateway" id="payment_gateway">
                    <option value="">{__('Setup Payment Gateway', 'quick-adsense-reloaded')}</option>
                    <option value="paypal">{__('Paypal', 'quick-adsense-reloaded')}</option>
                    <option value="authorize">{__('Authorize.net', 'quick-adsense-reloaded')}</option>
                    <option value="stripe">{__('Stripe', 'quick-adsense-reloaded')}</option>
                  </select>
                  </td>
                  </tr>
                  {(settings.payment_gateway==='paypal') &&
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Paypal Email', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="paypal_email" style={{maxWidth:'25rem',width:'100%'}} value={settings.paypal_email} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  }
                  {(settings.payment_gateway==='authorize') &&
                  <>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Name', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="authorize_name" style={{maxWidth:'25rem',width:'100%'}} value={settings.authorize_name} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Transaction Key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="authorize_transactionKey" style={{maxWidth:'25rem',width:'100%'}} value={settings.authorize_transactionKey} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Merchant Name', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="authorize_merchant_name" style={{maxWidth:'25rem',width:'100%'}} value={settings.authorize_merchant_name} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  </>
                  }
                   {(settings.payment_gateway==='stripe') &&
                  <>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Stripe Publishable Key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="stripe_publishable_key" style={{maxWidth:'25rem',width:'100%'}} value={settings.stripe_publishable_key} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Stripe Secret key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="stripe_secret_key" style={{maxWidth:'25rem',width:'100%'}} value={settings.stripe_secret_key} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  </>
                  }
                  <tr>
                    <th scope="row"><label htmlFor="hide_ajax">{__('Currency', 'quick-adsense-reloaded')}</label></th>
                    <td>
                     <select name="currency" value={settings.currency} onChange={this.formChangeHandler}>
                      <option value="USD">USD</option>
                      <option value="EUR">EUR</option>
                      <option value="GBP">GBP</option>
                      <option value="AUD">AUD</option>
                      <option value="CAD">CAD</option>
                      <option value="JPY">JPY</option>
                      <option value="NZD">NZD</option>
                      <option value="CHF">CHF</option>
                      <option value="HKD">HKD</option>
                      <option value="SGD">SGD</option>
                      <option value="SEK">SEK</option>
                      <option value="DKK">DKK</option>
                      <option value="PLN">PLN</option>
                      <option value="NOK">NOK</option>
                      <option value="HUF">HUF</option>
                      <option value="CZK">CZK</option>
                      <option value="ILS">ILS</option>
                      <option value="MXN">MXN</option>
                      <option value="BRL">BRL</option>
                      <option value="MYR">MYR</option>
                      <option value="PHP">PHP</option>
                      <option value="TWD">TWD</option>
                      <option value="THB">THB</option>
                      <option value="TRY">TRY</option>
                      <option value="RUB">RUB</option>
                      <option value="INR">INR</option>
                      <option value="CNY">CNY</option>
                      <option value="IDR">IDR</option>
                      <option value="KRW">KRW</option>
                      <option value="ZAR">ZAR</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label htmlFor="hide_ajax">{__('Email Notification on Ad Expiry', 'quick-adsense-reloaded')}</label></th>
                    <td>
                      <label className="quads-switch" style={{height:'26px'}}><input id="email_notification_adsell_expiry" type="checkbox" name="email_notification_adsell_expiry" checked={settings.email_notification_adsell_expiry} onChange={this.formChangeHandler} /><span className="quads-slider"></span></label>
                      <p>{__('User will receive an email notification when an ad is about to expire or is expired.', 'quick-adsense-reloaded')}</p>
                    </td>
                    </tr>
                    <tr>
                    <th scope="row"><label>{__('Payment Page', 'quick-adsense-reloaded')}</label></th>
                    <td>
                     <select name="payment_page" value={settings.payment_page} onChange={this.formChangeHandler}>
                      <option value="">{__('Select Page', 'quick-adsense-reloaded')}</option>
                      {this.state.pages.map((page, index) => (
                        <option key={index} value={page['ID']}>{page['post_title']}</option>
                      ))}
                    </select>
                    <p>{__('By default we have created  a payment page named "')}<b>{__('Buy Adspace')}</b>{__('". But if you have deleted or want to modify , create a new page and  paste the shortcode ')} <code>[quads_buy_form]</code> {__(' and select that page from above . ')} <br/><br/> <b>{__('Note ')} </b> {__(': Payment page must  exists and contains the shortcode')} <code>[quads_buy_form]</code>
                    <a target="_blank" href="https://wpquads.com/documentation/how-to-set-up-sellable-ads-in-wp-quads/">{__('Learn More')}</a></p>
                    <br/>
                    <br/>
                    <p>
                      <b>{__('Member Login Form :')}</b>
                      {__(' Create a page and paste the ')} 
                      <code>[sellable_premium_member_page]</code> 
                      {__(' shortcode for Member Login . ')}
                    </p>
                    </td>
                  </tr>
                  </tbody></table>
                </div>
             </div>
             <div className="quads-save-close"><a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.saveSettingsHandler}>{__('Save Changes', 'quick-adsense-reloaded')}</a></div>
             </div>
            </div> </>: null
            }
           {this.state.disable_ads_modal ?
           <>
           <div className="quads-large-popup-bglayout">  </div>
           <div className="quads-large-popup">
            <div className="quads-large-popup-content1">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
              <div className="quads-large-popup-title">
             <h1>{__('Hide Ads for Premium Members', 'quick-adsense-reloaded')}</h1>
             </div>
             <div className="quads-large-description"></div>
             <div className="quads-large-content">
                 <div className="quads-settings-tab-container">
                 <table className="form-table" role="presentation"><tbody>
                  <tr>
                  <th scope="row"><label>{__('Payment Gateway', 'quick-adsense-reloaded')}</label></th>
                  <td>
                  <select value={settings._dapayment_gateway} onChange={this.formChangeHandler} name="_dapayment_gateway" id="_dapayment_gateway">
                    <option value="">{__('Setup Payment Gateway', 'quick-adsense-reloaded')}</option>
                    <option value="paypal">{__('Paypal', 'quick-adsense-reloaded')}</option>
                    <option value="authorize">{__('Authorize.net', 'quick-adsense-reloaded')}</option>
                    <option value="stripe">{__('Stripe', 'quick-adsense-reloaded')}</option>
                  </select>
                  </td>
                  </tr>
                  {(settings._dapayment_gateway==='paypal') &&
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Paypal Email', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_dapaypal_email" style={{maxWidth:'25rem',width:'100%'}} value={settings._dapaypal_email} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  }
                  {(settings._dapayment_gateway==='authorize') &&
                  <>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Name', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_daauthorize_name" style={{maxWidth:'25rem',width:'100%'}} value={settings._daauthorize_name} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Transaction Key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_daauthorize_transactionKey" style={{maxWidth:'25rem',width:'100%'}} value={settings._daauthorize_transactionKey} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Merchant Name', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_daauthorize_merchant_name" style={{maxWidth:'25rem',width:'100%'}} value={settings._daauthorize_merchant_name} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  </>
                  }
                   {(settings._dapayment_gateway==='stripe') &&
                  <>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Stripe Publishable Key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_dastripe_publishable_key" style={{maxWidth:'25rem',width:'100%'}} value={settings._dastripe_publishable_key} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Stripe Secret key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_dastripe_secret_key" style={{maxWidth:'25rem',width:'100%'}} value={settings._dastripe_secret_key} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  </>
                  }
                  <tr>
                    <th scope="row"><label htmlFor="hide_ajax">{__('Currency', 'quick-adsense-reloaded')}</label></th>
                    <td>
                     <select name="_dacurrency" value={settings._dacurrency} onChange={this.formChangeHandler}>
                      <option value="USD">USD</option>
                      <option value="EUR">EUR</option>
                      <option value="GBP">GBP</option>
                      <option value="AUD">AUD</option>
                      <option value="CAD">CAD</option>
                      <option value="JPY">JPY</option>
                      <option value="NZD">NZD</option>
                      <option value="CHF">CHF</option>
                      <option value="HKD">HKD</option>
                      <option value="SGD">SGD</option>
                      <option value="SEK">SEK</option>
                      <option value="DKK">DKK</option>
                      <option value="PLN">PLN</option>
                      <option value="NOK">NOK</option>
                      <option value="HUF">HUF</option>
                      <option value="CZK">CZK</option>
                      <option value="ILS">ILS</option>
                      <option value="MXN">MXN</option>
                      <option value="BRL">BRL</option>
                      <option value="MYR">MYR</option>
                      <option value="PHP">PHP</option>
                      <option value="TWD">TWD</option>
                      <option value="THB">THB</option>
                      <option value="TRY">TRY</option>
                      <option value="RUB">RUB</option>
                      <option value="INR">INR</option>
                      <option value="CNY">CNY</option>
                      <option value="IDR">IDR</option>
                      <option value="KRW">KRW</option>
                      <option value="ZAR">ZAR</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                      <th scope="row"><label>{__('Diable Ads Cost', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_dacost" style={{maxWidth:'25rem',width:'100%'}} value={settings._dacost} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  <tr>
                      <th scope="row"><label>{__('Duration', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <select value={settings._daduration} onChange={this.formChangeHandler} name="_daduration" id="_daduration">
                        <option value="monthly">{__('Monthly', 'quick-adsense-reloaded')}</option>
                        <option value="yearly">{__('Yearly', 'quick-adsense-reloaded')}</option>
                      </select>
                      </td>
                    </tr>
                 {/*  <tr>
                    <th scope="row"><label htmlFor="hide_ajax">{__('Email Notification on Ad Expiry', 'quick-adsense-reloaded')}</label></th>
                    <td>
                      <label className="quads-switch"><input id="_daemail_notification_adsell_expiry" type="checkbox" name="_daemail_notification_adsell_expiry" checked={settings._daemail_notification_adsell_expiry} onChange={this.formChangeHandler} /><span className="quads-slider"></span></label>
                      <p>{__('User will receive an email notification when an ad is about to expire or is expired.', 'quick-adsense-reloaded')}</p>
                    </td>
                    </tr> */}
                    <tr>
                    <th scope="row"><label>{__('Payment Page', 'quick-adsense-reloaded')}</label></th>
                    <td>
                     <select name="dapayment_page" value={settings.dapayment_page} onChange={this.formChangeHandler} id="dapayment_page">
                      <option value="">{__('Select Page', 'quick-adsense-reloaded')}</option>
                      {this.state.pages.map((page, index) => (
                        <option key={index} value={page['ID']}>{page['post_title']}</option>
                      ))}
                    </select>
                    <p>{__('By default we have created  a payment page named "')}<b>{__('Hide Ads for Premium Members')}</b>{__('". But if you have deleted or want to modify , create a new page and  paste the shortcode ')} <code>[quads_disable_ads_form]</code> {__(' and select that page from above . ')} <br/><br/> <b>{__('Note ')} </b> {__(': Payment page must  exists and contains the shortcode')} <code>[quads_disable_ads_form]</code>
                    <a target="_blank" href="https://wpquads.com/documentation/how-to-set-up-sellable-ads-in-wp-quads/">{__('Learn More')}</a></p>
                    </td>
                  </tr>
                  </tbody></table>
                </div>
             </div>
             <div className="quads-save-close"><a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.saveSettingsHandler}>{__('Save Changes', 'quick-adsense-reloaded')}</a></div>
             </div>
            </div> </>: null
            }
           {this.state.disable_list_modal ?
           <>
           <div className="quads-large-popup-bglayout">  </div>
           <div className="quads-large-popup">
            <div className="quads-large-popup-content1">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
              <div className="quads-large-popup-title">
             <h1>{__('Premium Members List', 'quick-adsense-reloaded')}</h1>
             </div>
             <div className="quads-large-description"></div>
              <div className="quads-large-content">
                  <div className="quads-settings-tab-container">
                  <QuadsDisabledAdsList />
                  </div>
              </div>
             </div>
            </div> </>: null
            }
           {this.state.adtxt_modal ?
           <>
           <div className="quads-large-popup-bglayout">  </div>
           <div className="quads-large-popup">
            <div className="quads-large-popup-content1">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
              <div className="quads-large-popup-title">
                 <h1>{__('Ad Txt', 'quick-adsense-reloaded')}</h1>
             </div>
             <div className="quads-large-description"></div>

             <div className="quads-large-content">
               <textarea cols="80" rows="15" name="adsTxtText" onChange={this.formChangeHandler} value={settings.adsTxtText} />
               {__('To know more about ads.txt  you can', 'quick-adsense-reloaded')} <a  target="_blank" href="https://wpquads.com/documentation/what-is-ads-txt-and-how-to-use-it/">{__('view this tutorial', 'quick-adsense-reloaded')}</a>
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
               
             </div>
             
             <div className="quads-save-close"><a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.validateAdstxt}>{__('Validate & Save', 'quick-adsense-reloaded')}</a></div>
             
             </div>
            </div> </>: null
            }
             {this.state.adsforwp_to_quads_model ?
           <div className="quads-modal-popup">
            <div className="quads-modal-popup-content">
             <span className="quads-modal-close" onClick={this.closeModal}>&times;</span>
             <h3>{__('Ads For wp Setting', 'quick-adsense-reloaded')}</h3>
             <div className="quads-modal-description"></div>
             <div className="quads-modal-content adsforwp-quads-popup">
             <div className="quads-modal">
             {__('Change adsforwp Short code to quads', 'quick-adsense-reloaded')}
              <label className="quads-switch">
                         <input id="adsforwp_quads_shortcode" type="checkbox" name="adsforwp_quads_shortcode" onChange={this.formChangeHandler} checked={settings.adsforwp_quads_shortcode} />
                         <span className="quads-slider"></span>
                       </label>
            </div>
            <div className="quads-modal">
            {__('Change adsforwp Gutenberg to quads', 'quick-adsense-reloaded')}
              <label className="quads-switch">
                         <input id="adsforwp_quads_gutenberg" type="checkbox" name="adsforwp_quads_gutenberg" onChange={this.formChangeHandler} checked={settings.adsforwp_quads_gutenberg} />
                         <span className="quads-slider"></span>
                       </label>
            </div>
             </div>
             </div>
            </div> : null
            }
             {this.state.advance_ads_to_quads_model ?
           <div className="quads-modal-popup">
            <div className="quads-modal-popup-content">
             <span className="quads-modal-close" onClick={this.closeModal}>&times;</span>
             <h3>{__('Advance Ads Setting', 'quick-adsense-reloaded')}</h3>
             <div className="quads-modal-description"></div>
             <div className="quads-modal-content adsforwp-quads-popup">
             <div className="quads-modal">
             {__('Change Advance Ads Short code to quads', 'quick-adsense-reloaded')}
              <label className="quads-switch">
                         <input id="advance_ads_to_quads" type="checkbox" name="advance_ads_to_quads" onChange={this.formChangeHandler} checked={settings.advance_ads_to_quads} />
                         <span className="quads-slider"></span>
                       </label>
            </div>
             </div>
             </div>
            </div> : null
            }
                {this.state.role_permission_modal ?
           <div className="quads-modal-popup quads-role-permission">
            <div className="quads-modal-popup-content">
            <div className="quads-modal-header"> <span className="quads-modal-close" onClick={this.closeModal}>&times;</span>
             <h3>{__('Role Capabilities', 'quick-adsense-reloaded')}</h3>
            </div>
              <div className="quads-modal-content">
              <QuadsVerticalTabs RoleBasedAccess={settings.RoleBasedAccess} handleCapabilityChange={this.handleCapabilityChange} />
              </div>
             </div>
            </div> : null
            }
            {/* global excluder modal */}
            {this.state.global_excluder_modal ?
                <>           <div className="quads-large-popup-bglayout">  </div>

           <div className="quads-large-popup">
            <div className="quads-large-popup-content1">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
                <div className="quads-large-popup-title">
                    <h1>{__('Global Excluder', 'quick-adsense-reloaded')}</h1>
                </div>
                <div className="quads-large-description"></div>

                <div className="quads-large-content">
             <table className="form-table" role="presentation"><tbody><tr>
                                  <th>{__('Hide Ads for User Roles', 'quick-adsense-reloaded')}</th>
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
                                  <th>{__('Hide Ads for Tags', 'quick-adsense-reloaded')}</th>
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
                                  <th>{__('Hide Ads for Plugins', 'quick-adsense-reloaded')}</th>
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
            </div> </>: null
            }
            {/* Revenue Sharing modal */}
            {this.state.revenue_sharing_modal ?
            <>
                <div className="quads-large-popup-bglayout">  </div>
           <div className="quads-large-popup">
            <div className="quads-large-popup-content1">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
             <div className="quads-large-popup-title">
             <h1>Revenue Sharing</h1>
             </div>
             <div className="quads-large-content">
             <table className="form-table" role="presentation"><tbody><tr>
                                  <th>{__('Administrator', 'quick-adsense-reloaded')}</th>
                                  <td>
                                     <input type="number" min={0} max={100} placeholder="Percentage"  name="ad_owner_revenue_per" value={settings.ad_owner_revenue_per}  onChange={this.formChangeHandler} />
                                  </td>
                                </tr><tr>
                                  <th>Author</th>
                                  <td>
                                     <input type="number"  min={0} max={100} placeholder="Percentage"  name="ad_author_revenue_per" value={settings.ad_author_revenue_per} onChange={this.formChangeHandler} />
                                  </td>
                                </tr>
                               </tbody></table>  
              <p>{__('Enter the percentage of revenue that you would like to share', 'quick-adsense-reloaded')}</p>                  
             </div>
             <div className="quads-save-close">
                                          
                            <a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.saveRevenueSharing}>{__('Save Changes', 'quick-adsense-reloaded')}</a>
                            </div>
             </div>
            </div> </>: null
            }
             {/* Ad Blocker Support */}
            {this.state.ad_blocker_support_popup ?
            <>
              <div className="quads-large-popup-bglayout">  </div>
           <div className="quads-large-popup">
            <div className="quads-large-popup-content1">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
            <div className="quads-large-popup-title">
              <h1>Notice For Ad Blocker</h1>
            </div>
             <div className="quads-large-content">
             <table className="form-table" role="presentation"><tbody><tr>
                                  <th>{__('Notice Type', 'quick-adsense-reloaded')}</th>
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
                                   <div className="quads-message bottom">{__('If visitor is using an ad blocker he will see a message instead of an ad, asking him to deactivate the ad blocker.', 'quick-adsense-reloaded')} <a href="http://wpquads.com/docs/customize-ad-blocker-notice/" target="_blank">{__('Read here', 'quick-adsense-reloaded')}</a> {__('how to customize colors and text.', 'quick-adsense-reloaded')}</div>
                                  </td>
                                </tr>
                                <tr>
                                  <th>{__('Notice Behaviour', 'quick-adsense-reloaded')}</th>
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
                                <th><b>{__('Notice Content', 'quick-adsense-reloaded')}</b></th>
                                <td>
                                </td>
                              </tr>
                              {settings.notice_type == 'popup' ? (
                              <tr>
                                <th>{__('Title', 'quick-adsense-reloaded')}</th>
                                <td><input value={settings.notice_title} onChange={this.formChangeHandler} name="notice_title" type="text" placeholder="Adblock Detected!" className="quads-premium-cus" />
                                </td>
                              </tr>
                            ) : null    }
                            {settings.notice_type == 'bar' ? ( 
                              <>
                                <tr>
                                  <th>{__('Notice Bar', 'quick-adsense-reloaded')}</th>
                                  <td>
                                    <select value={settings.notice_bar} onChange={this.formChangeHandler} name="notice_bar" id="notice_bar">
                                    <option value="2">{__('Top Bar', 'quick-adsense-reloaded')}</option>
                                    <option value="1">{__('Bottom Bar', 'quick-adsense-reloaded')}</option>
                                  </select>
                                  </td>
                                </tr>
                                <tr>
                                  <th>{__('Sticky Notice Bar', 'quick-adsense-reloaded')}</th>
                                  <td><input id="notice_bar_sticky" type="checkbox" name="notice_bar_sticky" onChange={this.formChangeHandler} checked={settings.notice_bar_sticky} /></td>
                                </tr>
                              </>
                            ) : null    }
                              <tr>
                                <th>{__('Description', 'quick-adsense-reloaded')}</th>
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
                              <th>{__('Button Text', 'quick-adsense-reloaded')}</th>
                              <td><input value={settings.btn_txt} onChange={this.formChangeHandler} name="btn_txt" type="text" placeholder="Email" className="quads-premium-cus" /></td>
                              </tr>
                               <tr>
                                <th><b>{__('Notice Design', 'quick-adsense-reloaded')}</b></th>
                                <td>
                                </td>
                              </tr>
                              <tr>
                              <th>{__('Content Color', 'quick-adsense-reloaded')}</th>
                              <td>
                                 <div>
                                    <div className="color-pick-swatch" onClick={ this.handleClick_notice_txt_color }>
                                      <div >
                                        <div style={ styles.notice_txt_color } className="color-pick-color" /></div>
                                        <span className="wp-color-result-text">{__('Select Color', 'quick-adsense-reloaded')}</span>
                                      </div>
                                    { this.state.notice_txt_color_picker ? <div className="color-pick-popover">
                                      <div className="color-pick-cover" onClick={ this.handleClose }/>
                                      <SketchPicker color={ this.state.notice_txt_color } onChange={ this.notice_txt_color } />
                                    </div> : null }
                                  </div>
                                </td>
                              </tr>
                              <tr>
                              <th>{__('Background Color', 'quick-adsense-reloaded')}</th>
                              <td>
                                 <div>
                                    <div className="color-pick-swatch" onClick={ this.handleClick_notice_bg_color }>
                                      <div >
                                        <div style={ styles.notice_bg_color } className="color-pick-color" /></div>
                                        <span className="wp-color-result-text">{__('Select Color', 'quick-adsense-reloaded')}</span>
                                      </div>
                                    { this.state.notice_bg_color_picker ? <div className="color-pick-popover">
                                      <div className="color-pick-cover" onClick={ this.handleClose }/>
                                      <SketchPicker color={ this.state.notice_bg_color } onChange={ this.notice_bg_color } />
                                    </div> : null }
                                  </div>
                                </td>
                              </tr>
                              <tr>
                              <th>{__('Button Text Color', 'quick-adsense-reloaded')}</th>
                              <td>
                                 <div>
                                    <div className="color-pick-swatch" onClick={ this.handleClick_notice_btn_txt_color }>
                                      <div >
                                        <div style={ styles.notice_btn_txt_color } className="color-pick-color" /></div>
                                        <span className="wp-color-result-text">{__('Select Color', 'quick-adsense-reloaded')}</span>
                                      </div>
                                    { this.state.notice_btn_txt_color_picker ? <div className="color-pick-popover">
                                      <div className="color-pick-cover" onClick={ this.handleClose }/>
                                      <SketchPicker color={ settings.notice_btn_txt_color } onChange={ this.notice_btn_txt_color } />
                                    </div> : null }
                                  </div>
                                </td>
                              </tr>
                              <tr>
                              <th>{__('Button Background Color', 'quick-adsense-reloaded')}</th>
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
                                  <th>{__('Target Page', 'quick-adsense-reloaded')}</th>
                                  <td>
                                  <Select
                                    name="page_redirect_path"
                                    placeholder="Choose Page"
                                    value={settings.page_redirect_path}
                                    options={this.state.page_redirect_options}
                                    onChange={this.page_redirect_select_fun}
                                  />
                                  </td>
                                </tr>: null}</tbody></table>
                                To know more about Ad Blocker you can <a  target="_blank" href="https://wpquads.com/documentation/how-to-use-ad-blocker-support-in-wp-quads/">view this </a>
             </div>
             <div className="quads-save-close">
  
                <a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.saveAdBlockSuport}>Save Changes</a>
                            </div>
             </div>
            </div> </>: null
            }
                        {this.state.click_fraud_protection_popup ?
            <>
              <div className="quads-large-popup-bglayout">  </div>
           <div className="quads-large-popup">
            <div className="quads-large-popup-content1">
             <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
            <div className="quads-large-popup-title">
              <h1>Click Fraud Protection</h1>
            </div>
             <div className="quads-large-content">
             <table className="form-table" role="presentation"><tbody>
                                  <tr>
                              <th>Allowed clicks</th>
                              <td><input value={settings.allowed_click} onChange={this.formChangeHandler} name="allowed_click" type="text" placeholder="3" className="quads-premium-cus" /><a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-click-fraud-protection-and-how-to-use-it/"></a></td>
                              </tr>
                               <tr>
                              <th>Click limit (in hours)</th>
                              <td><input value={settings.click_limit} onChange={this.formChangeHandler} name="click_limit" type="text" placeholder="3" className="quads-premium-cus" /><a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-click-fraud-protection-and-how-to-use-it/"></a></td>
                              </tr>
                               <tr>
                              <th>Ban duration (in days)</th>
                              <td><input value={settings.ban_duration} onChange={this.formChangeHandler} name="ban_duration" type="text" placeholder="3" className="quads-premium-cus" /><a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-click-fraud-protection-and-how-to-use-it/"></a></td>
                              </tr></tbody></table>
                              
                            <div className="blocked_main">
                            <span className="blocked_ids_in">Log IP of Blocked Users<span className="blocked_ids_inn"> <input className="quads_open_block" onClick={this.quads_open_mainblock} value={settings.checkbox_value} name="checkbox_value" type="checkbox" defaultChecked={this.state.settings.checkbox_value} ></input> </span> </span>
                            <span>
                            {(this.state.blocked_ips && this.state.blocked_ips.length > 0 ) ? 
                            <span className="blocked_ids"><form className="quads_block_ids_" method="POST" action={this.state.q_admin_url+'?action=quads_id_delete'} >
                            { (settings.checkbox_value === true ) ? <span id="input_main">
                            <input id="btn_clear_all_ips" onClick={this.formhandler} type="button" value="Clear All" /></span>
                            : ''}
                          </form></span> : '' }
                          <a className="blocked_ids_href quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-click-fraud-protection-and-how-to-use-it/"></a>
                          </span>
                          { (settings.checkbox_value === true ) ?
                            <div id="blocked_id_main">
                            <div id="table_main">
                            <table id="blocked_id_table">
                            <tbody>
                            <tr className="b_in_">
                              <td className="b_in_">ID</td>
                              <td className="b_in_">Date/Time</td>
                              <td className="b_in_">IP</td>
                            </tr>
                            </tbody>
                            { (this.state.blocked_ips && this.state.blocked_ips.length > 0 && Array.isArray(this.state.blocked_ips)) ? this.state.blocked_ips.map( (value, index) => {
                              return (
                                <tbody className="b_inspan">
                                { (value!=="") &&
                                <tr key={`${value}_${index}`} className="b_in">
                              <td className="b_in">{index+1}</td>
                              <td className="b_in">{value.time}</td>
                              <td className="b_in">{value.ip}</td>
                            </tr>
                              }</tbody> )})
                                
                              : <div className="no_id">No Data available</div> }
                              </table></div></div>
                                : ''}
                                
                              </div>
                            
             </div>
              <div className="quads-save-close">
              {this.state.button_spinner_toggle ?
                <a className="quads-btn quads-btn-primary quads-large-btn">
                <span className="quads-btn-spinner"></span>Saving...</a> :
                <a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.saveAdBlockSuport}>Save Changes</a>
              }
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
                     <th><label htmlFor="reports_settings">{__('Reports', 'quick-adsense-reloaded')}</label></th>
                     <td>
                        {this.state.selectedBtnOpt == 'reports_settings' ? 
                          <div className="quads-spin-cntr">
                             <div className="quads-set-spin"></div>
                          </div> : 
                           <label className="quads-switch">
                             <input id="reports_settings" type="checkbox" name="reports_settings" onChange={this.formChangeHandler} checked={settings.reports_settings} />
                             <span id="reports_settings_" className="quads-slider"></span>
                             <div className="lazy_loader_rs"></div>
                           </label>
                        }
                         <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/how-to-link-adsense-account-for-the-revenue-reports-feature/"></a>
                     </td>
                     </tr>
                 <tr>
                     <th><label htmlFor="adsTxtEnabled">ads.txt - {__('Automatic Creation', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'adsTxtEnabled' ?
                        <div className="quads-spin-cntr">
                           <div className="quads-set-spin"></div>
                        </div> : 
                        <label className="quads-switch">
                          <input id="adsTxtEnabled" type="checkbox" name="adsTxtEnabled" onChange={this.formChangeHandler} checked={settings.adsTxtEnabled} />
                          <span id="adsTxtEnabled_" className="quads-slider"></span>
                          <div className="lazy_loader"></div>
                        </label>
                      } 
                       {settings.adsTxtEnabled ? <><span onClick={this.open_ad_text_modal} className="quads-generic-icon dashicons dashicons-admin-generic"></span><span style={{marginLeft:'10px',cursor:'pointer'}} onClick={this.open_ad_text_modal}>View Settings</span></> : ''}
                       <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-ads-txt-and-how-to-use-it/"></a>
                     </td>
                     </tr>
                    {
                      quads_localize_data.is_pro ?
                      <tr>
                     <th><label htmlFor="global_excluder_enabled">{__('Global Excluder', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'global_excluder_enabled' ?
                       <div className="quads-spin-cntr">
                          <div className="quads-set-spin"></div>
                       </div> :
                       <label className="quads-switch">
                         <input id="global_excluder_enabled" type="checkbox" name="global_excluder_enabled" onChange={this.formChangeHandler} checked={settings.global_excluder_enabled} />
                         <span id="global_excluder_enabled_" className="quads-slider"></span>
                         <div className="lazy_loader_g"></div>
                       </label>
                       
                      } 
                         

                         {settings.global_excluder_enabled ? <><span onClick={this.open_global_excluder} className="quads-generic-icon dashicons dashicons-admin-generic"></span><span style={{marginLeft:'10px',cursor:'pointer'}}  onClick={this.open_global_excluder}>View Settings</span></> : null}
                         <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/how-to-globally-exclude-or-hide-ads-for-user-roles-with-wp-quads-pro/"></a>
                     </td>
                     </tr>:null}
                    <tr>
                   <th><label htmlFor="lazy_load_global">{__('Lazy Loading for Adsense', 'quick-adsense-reloaded')}</label></th>
                    <td>
                      {this.state.selectedBtnOpt == 'lazy_load_global' ?
                        <div className="quads-spin-cntr">
                          <div className="quads-set-spin"></div>
                        </div> :
                        <label className="quads-switch">
                         <input id="lazy_load_global" type="checkbox" name="lazy_load_global" onChange={this.formChangeHandler} checked={settings.lazy_load_global} />
                         <span id="lazy_load_global_" className="quads-slider"></span>
                         <div className="lazy_loader_l"></div>
                        </label>
                      }
                       <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-lazy-loading-for-adsense-and-how-to-use-it/"></a>
                      </td>
                      </tr>
                       <tr>
                     <th><label htmlFor="ad_blocker_support">{__('Ad Blocker Support', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'ad_blocker_support' ?
                       <div className="quads-spin-cntr">
                          <div className="quads-set-spin"></div>
                       </div> :
                       <label className="quads-switch">
                         <input id="ad_blocker_support" type="checkbox" name="ad_blocker_support" onChange={this.formChangeHandler} checked={settings.ad_blocker_support} />
                         <span id="ad_blocker_support_" className="quads-slider"></span>
                         <div className="lazy_loader_a"></div>
                       </label>
                       }
                       {settings.ad_blocker_support ? <><span onClick={this.ad_blocker_support} className="quads-generic-icon dashicons dashicons-admin-generic"></span><span style={{marginLeft:'10px',cursor:'pointer'}} onClick={this.ad_blocker_support}>View Settings</span></> : null}
                        <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/how-to-use-ad-blocker-support-in-wp-quads/"></a>
                     </td>
                     </tr>
                     <tr>
                     <th><label htmlFor="click_fraud_protection">{__('Click Fraud Protection', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'click_fraud_protection' ?
                       <div className="quads-spin-cntr">
                        <div className="quads-set-spin"></div>
                       </div> :
                       <label className="quads-switch">
                         <input id="click_fraud_protection" type="checkbox" name="click_fraud_protection" onChange={this.formChangeHandler} checked={settings.click_fraud_protection} />
                         <span id="click_fraud_protection_" className="quads-slider"></span>
                         <div className="lazy_loader_c"></div>
                        </label>
                      }
                       {settings.click_fraud_protection ? <><span onClick={this.click_fraud_protection_popup} className="quads-generic-icon dashicons dashicons-admin-generic"></span><span style={{marginLeft:'10px',cursor:'pointer'}} onClick={this.click_fraud_protection_popup}>View Settings</span></> : null}
                         <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-click-fraud-protection-and-how-to-use-it/"></a>
                     </td>
                     </tr>
                     <tr>
                     <th><label htmlFor="revenue_sharing_enabled">{__('Revenue Sharing', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'revenue_sharing_enabled' ?
                       <div className="quads-spin-cntr">
                        <div className="quads-set-spin"></div>
                       </div> :
                       <label className="quads-switch">
                         <input id="revenue_sharing_enabled" type="checkbox" name="revenue_sharing_enabled" onChange={this.formChangeHandler} checked={settings.revenue_sharing_enabled} />
                         <span id="revenue_sharing_enabled_" className="quads-slider"></span>
                         <div className="lazy_loader_r"></div>
                        </label> 
                      }
                       {settings.revenue_sharing_enabled ? <><span onClick={this.open_revenue_sharing_excluder} className="quads-generic-icon dashicons dashicons-admin-generic"></span><span style={{marginLeft:'10px',cursor:'pointer'}} onClick={this.open_revenue_sharing_excluder}>View Settings</span></> : null}
                         <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/setup-revenue-sharing-in-wordpress-and-amp/"></a>
                     </td>
                     </tr>
                     <tr>
                         <th><label htmlFor="tcf_2_integration">{__('TCF v2.0 integration ', 'quick-adsense-reloaded')}</label></th>
                         <td>
                          {this.state.selectedBtnOpt == 'tcf_2_integration' ?
                             <div className="quads-spin-cntr">
                              <div className="quads-set-spin"></div>
                             </div> :
                             <label className="quads-switch">
                                 <input id="tcf_2_integration" type="checkbox" name="tcf_2_integration" onChange={this.formChangeHandler} checked={settings.tcf_2_integration} />
                                 <span id="tcf_2_integration_" className="quads-slider"></span>
                                 <div className="lazy_loader_t"></div>
                             </label> 
                          }
                             <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/what-is-transparency-consent-framework-tcf-v2-0-and-how-to-use-it/"></a>
                         </td>
                     </tr>
                     <tr>
                     <th><label htmlFor="rotator_ads_settings">{__('Rotator Ads', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'rotator_ads_settings' ?
                       <div className="quads-spin-cntr">
                        <div className="quads-set-spin"></div>
                       </div> :
                       <label className="quads-switch">
                         <input id="rotator_ads_settings" type="checkbox" name="rotator_ads_settings" onChange={this.formChangeHandler} checked={settings.rotator_ads_settings} />
                         <span id="rotator_ads_settings_" className="quads-slider"></span>
                         <div className="lazy_loader_rr"></div>
                       </label> 
                      }
                         <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/how-to-use-ad-rotator-in-wp-quads/"></a>
                     </td>
                     </tr>
                     <tr>
                     <th><label htmlFor="group_insertion_settings">{__('Group Insertion Ads', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'group_insertion_settings' ?
                         <div className="quads-spin-cntr">
                          <div className="quads-set-spin"></div>
                         </div> :
                         <label className="quads-switch">
                             <input id="group_insertion_settings" type="checkbox" name="group_insertion_settings" onChange={this.formChangeHandler} checked={settings.group_insertion_settings} />
                             <span id="group_insertion_settings_" className="quads-slider"></span>
                             <div className="lazy_loader_gp"></div>
                         </label> 
                      }
                         <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/how-to-add-group-insertion-ads-in-wp-quads/"></a>
                     </td>
                 </tr>
             
                 <tr>
                     <th><label htmlFor="ad_performance_tracking">{__('Ad Performance Tracking', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'ad_performance_tracking' ?
                         <div className="quads-spin-cntr">
                          <div className="quads-set-spin"></div>
                         </div> :
                         <label className="quads-switch">
                             <input id="ad_performance_tracking" type="checkbox" name="ad_performance_tracking" onChange={this.formChangeHandler} checked={settings.ad_performance_tracking} />
                             <span id="ad_performance_tracking_" className="quads-slider"></span>
                             <div className="lazy_loader_ap"></div>
                         </label>
                      }
                         <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/ad-performance-tracking-in-wp-quads/"></a>
                     </td>
                 </tr>
                 <tr>
                     <th><label htmlFor="exclude_admin_tracking">{__('Exclude Admin from tracking', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'exclude_admin_tracking' ?
                         <div className="quads-spin-cntr">
                          <div className="quads-set-spin"></div>
                         </div> :
                         <label className="quads-switch">
                             <input id="exclude_admin_tracking" type="checkbox" name="exclude_admin_tracking" onChange={this.formChangeHandler} checked={settings.exclude_admin_tracking} />
                             <span id="exclude_admin_tracking_" className="quads-slider"></span>
                             <div className="lazy_loader_at"></div>
                         </label>
                      }
                     </td>
                 </tr>
                 <tr>
                     <th><label htmlFor="disableads">{__('Hide Ads for Premium Members', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'disableads' ?
                         <div className="quads-spin-cntr">
                          <div className="quads-set-spin"></div>
                         </div> :
                         <label className="quads-switch">
                             <input id="disableads" type="checkbox" name="disableads" onChange={this.formChangeHandler} checked={settings.disableads} />
                             <span id="disableads_" className="quads-slider"></span>
                             <div className="lazy_loader_sa"></div>
                         </label>
                      }
                      {settings.disableads ? <><span onClick={this.open_disable_ad_modal} className="quads-generic-icon dashicons dashicons-admin-generic"></span><span style={{marginLeft:'10px',cursor:'pointer'}} onClick={this.open_disable_ad_modal}>View Settings</span><span style={{marginLeft:'10px'}}> | </span><span onClick={this.open_disable_list_modal} className="quads-generic-icon dashicons dashicons-visibility"></span><span style={{marginLeft:'10px',cursor:'pointer'}} onClick={this.open_disable_list_modal}>View Members List</span></> : ''}
                      {/*  <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/how-to-set-up-sellable-ads-in-wp-quads/"></a> */}
                     </td>
                 </tr>
                 <tr>
                     <th><label htmlFor="sellable_ads">{__('Sellable Ads', 'quick-adsense-reloaded')}</label></th>
                     <td>
                      {this.state.selectedBtnOpt == 'sellable_ads' ?
                         <div className="quads-spin-cntr">
                          <div className="quads-set-spin"></div>
                         </div> :
                         <label className="quads-switch">
                             <input id="sellable_ads" type="checkbox" name="sellable_ads" onChange={this.formChangeHandler} checked={settings.sellable_ads} />
                             <span id="sellable_ads_" className="quads-slider"></span>
                             <div className="lazy_loader_sa"></div>
                         </label>
                         
                      }
                      {settings.sellable_ads ? <><span onClick={this.open_sellable_ad_modal} className="quads-generic-icon dashicons dashicons-admin-generic"></span><span style={{marginLeft:'10px',cursor:'pointer'}} onClick={this.open_sellable_ad_modal}>View Settings</span></> : ''}
                       <a className="quads-general-helper quads-general-helper-new" target="_blank" href="https://wpquads.com/documentation/how-to-set-up-sellable-ads-in-wp-quads/"></a>
                     </td>
                 </tr>
                 {this.state.quadsIsAdmin ?
                 <tr>
                    <th scope="row"><label htmlFor="RoleBasedAccess">{__('Role Based Access', 'quick-adsense-reloaded')}</label></th>
                    <td className='quadsRoleAccess'>
                    {this.state.selectedBtnOpt == 'RoleBasedAccess' ?
                    <div className="quads-spin-cntr">
                       <div className="quads-set-spin"></div>
                    </div> :
                    <Select
                      isMulti
                      name="RoleBasedAccess"
                      className={'RoleBasedAccess'}
                      placeholder="Role Based Access"
                      value={this.state.settings.RoleBasedAccess}
                      options={this.state.multiUserOptions}
                      onChange={this.handleRoleBasedAccess}
                    />
                    }
                    {this.state.settings.RoleBasedAccess.length > 0 ? <><span onClick={this.open_role_permission_modal} className="quads-generic-icon dashicons dashicons-admin-generic"></span><span style={{marginLeft:'10px',cursor:'pointer'}} onClick={this.open_role_permission_modal}>View Settings</span></> : ''}

                    </td>
                  </tr>:null}
                
                      {quads_setting_pro_items.map((item, index) => ( <QuadsAdSettingsProTemplate key={index} display_pro_alert_msg={this.state.display_pro_alert_msg} item={item} display_pro_alert_fun={this.display_pro_alert_fun} selectedBtnOpt={this.state.selectedBtnOpt} quads_pro_list_selected={this.state.quads_pro_list_selected} formChangeHandler={this.formChangeHandler} settings={settings} />  ) )}
                 
                     </tbody></table>
                </div>
               );
              case "settings_tools": return(
                <div className="quads-settings-tab-container">
                  <table className="form-table" role="presentation">
                    <tbody>
                    <tr>
                       <th><label htmlFor="report_logging">{__('Report Logging Method', 'quick-adsense-reloaded')}</label></th>
                        <td>
                          <FormControl style={{minWidth:'300px'}} >
                          <MSelect
                            name="report_logging" 
                            id="report_logging"
                            onChange={e =>this.selectLoggingChangeHandler(e)} 
                            value={settings.report_logging}
                          >
                            <MenuItem value="combined_legacy">{__('Combined Data (Legacy)', 'quick-adsense-reloaded')}</MenuItem>
                            <MenuItem value="improved_v2">{__('Separate Data (Improved V2)', 'quick-adsense-reloaded')}</MenuItem>
                          </MSelect>
                        </FormControl>
                          {(settings.logging_toggle != 'off')?settings.report_logging == 'improved_v2'?
                          <p>{__('You are now using new improved report tracking.', 'quick-adsense-reloaded')} </p>
                          :
                          <p>{__('You are using Legacy report tracking.Tracking will get slower as the Datebase size increases. We recommend switching to Separate Data (Improved V2) for better performance', 'quick-adsense-reloaded')}</p>
                        :''}
                        </td>
                      </tr>
                      {quads_localize_data.is_pro ?<tr>
                       <th><label   htmlFor="analytics">{__('Google Analytics Integration', 'quick-adsense-reloaded')}</label></th>
                        <td><label className="quads-switch"><input  id="analytics" type="checkbox" onChange={this.formChangeHandler} name="analytics" checked={settings.analytics} /><span className="quads-slider"></span></label>
                        <a className="quads-general-helper quads-general-helper-new" href="#"></a><div className="quads-message bottom" >Check how many visitors are using ad blockers in your Google Analytics account from the event tracking in <i>Google Analytics-&gt;Behavior-&gt;Events</i>. This only works if your visitors are using regular ad blockers like 'adBlock'. There are browser plugins which block all external requests like the  software uBlock origin. This also block google analytics and as a result you do get any analytics data at all.</div></td>
                      </tr>
                       :null}<tr>
                       <th><label htmlFor="uninstall_on_delete">{__('Delete Data on Uninstall?', 'quick-adsense-reloaded')}</label></th>
                        <td><label className="quads-switch"><input id="uninstall_on_delete" type="checkbox" onChange={this.formChangeHandler} name="uninstall_on_delete" checked={settings.uninstall_on_delete} /><span className="quads-slider"></span></label>
                        <a className="quads-general-helper quads-general-helper-new" href="#"></a><div className="quads-message bottom" >Check this box if you would like <strong>Settings-&gt;WPQUADS</strong> to completely remove all of its data when the plugin is deleted.</div>
                        </td>
                      </tr>
                      <tr>
                       <th><label htmlFor="debug_mode">{__('Debug Mode', 'quick-adsense-reloaded')}</label></th>
                        <td><label className="quads-switch"><input id="debug_mode" type="checkbox" onChange={this.formChangeHandler} name="debug_mode" checked={settings.debug_mode} /><span className="quads-slider"></span></label></td>
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
                          <a onClick={this.export_settings} className="quads-btn quads-btn-primary">Export</a>
                          <p>{__('Export the Quick AdSense Reloaded settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'quick-adsense-reloaded')}</p>
                        </td>
                      </tr>
                      <tr>
                        <th><label>{__('Import', 'quick-adsense-reloaded')}</label></th>
                        <td>    <input type="file" onChange={this.onFileChange} />
                        <a className="quads-btn quads-btn-primary" onClick={this.import_settings}>Import</a>
          
                          <p>{__('Import the Quick AdSense Reloaded settings for this site from a .json file. This allows you to easily import the configuration into another site.', 'quick-adsense-reloaded')}</p>
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
                          {settings.adsforwp_to_quads == 'imported' ? <><span onClick={this.adsforwp_to_quads_model} className="quads-generic-icon import_ads_for_wp dashicons dashicons-admin-generic"></span><span style={{marginLeft:'10px',cursor:'pointer'}} onClick={this.adsforwp_to_quads_model}>View Settings</span></> : ''}
                            {this.state.importadsforwpmsg  ? <Alert severity="success" action={<Icon onClick={this.closeQuerySuccess}>close</Icon>}>{this.state.importadsforwpmsg}</Alert> : null}
                            {this.state.importadsforwpmsgprocessing ? <div className='updating-message importadsforwpmsgprocessing'><p>Importing Ads</p></div>: ''}
                        </td>
                      </tr>
                      <tr>
                        <th><label>{__('Advanced Ads', 'quick-adsense-reloaded')}</label></th>
                        <td>
                          <a className="quads-btn quads-btn-primary" id="import_advanced_ads" onClick={this.importadvancedadsdata}>{__('Import', 'quick-adsense-reloaded')}</a>
                          {settings.advance_ads_to_quads == 'imported' ? <><span onClick={this.advance_ads_to_quads_model} className="quads-generic-icon import_advanced_ads dashicons dashicons-admin-generic"></span></> : ''} 

                            {this.state.importadvancedadsmsg  ? <Alert severity="success" action={<Icon onClick={this.closeQuerySuccess}>close</Icon>}>{this.state.importadvancedadsmsg}</Alert> : null}
                            {this.state.importadvancedadsmsgprocessing ? <div className='updating-message importadvancedadsmsgprocessing'><p>Importing Ads</p></div>: ''}
                        </td>
                      </tr>
                      </tbody></table>
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
                    <div><textarea  name="auto_ad_code" value={settings.auto_ad_code} onChange={this.addauto_ad_code}  cols="60" rows="5" className="quads-premium-cus" /></div>
                     <div>
                      {__('Status', 'quick-adsense-reloaded')}
                    </div>
                    <div>  <select name="customer_query_type" value={settings.auto_ads_pos} onChange={this.addauto_ads_pos} className="quads-premium-cus"> 
                        <option value="disabled">{__('Auto Ads Disabled', 'quick-adsense-reloaded')}</option>
                        <option value="enabled">{__('Auto Ads Enabled', 'quick-adsense-reloaded')}</option>
                      </select></div>
                       <div>
                      {__('Exclude Auto Ads From Post Types', 'quick-adsense-reloaded')}
                    </div>
                    <div>  <select multiple={true} name="autoads_post_types" value={settings.autoads_post_types} onChange={this.addautoads_post_types} className="quads-premium-cus">
                       {auto_ads_get_post_types}
                      </select></div>
                       <div>
                      {__('Exclude Auto Ads From Extra pages', 'quick-adsense-reloaded')}
                    </div>
                    <div>  <select multiple={true} name="autoads_extra_pages" value={settings.autoads_extra_pages}  onChange={this.addautoads_extra_pages} className="quads-premium-cus">
                        <option value="none">{__('Exclude nothing', 'quick-adsense-reloaded')}</option>
                        <option value="homepage">{__('homepage', 'quick-adsense-reloaded')}</option>
                      </select></div>
                         <div>
                      {__('Exclude Auto Ads From User Roles', 'quick-adsense-reloaded')}
                    </div>
                    <div>  <select multiple={true} name="autoads_user_roles" value={settings.autoads_user_roles}  onChange={this.addautoads_user_roles} className="quads-premium-cus">
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
                      <label className="quads-switch"><input id="hide_ajax" type="checkbox" name="hide_ajax" checked={settings.hide_ajax} onChange={this.formChangeHandler} /><span className="quads-slider"></span></label>
                      <p>{__('If your site is using ajax based infinite loading it might happen that ads are loaded without any further post content. Disable this here.', 'quick-adsense-reloaded')}</p>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label htmlFor="QckTags">{__('Quicktags', 'quick-adsense-reloaded')}</label></th>
                    <td>
                      <label className="quads-switch"><input id="QckTags" type="checkbox" name="QckTags" checked={settings.QckTags} onChange={this.formChangeHandler} /><span className="quads-slider"></span></label>{__('Show Quicktag Buttons on the HTML Post Editor', 'quick-adsense-reloaded')}
                      <p>{__('Tags can be inserted into a post via the additional Quicktag Buttons at the HTML Edit Post SubPanel.', 'quick-adsense-reloaded')}</p>
                      <p><strong>Optional:</strong>{__('Insert Ads into a post, on-the-fly using below tags', 'quick-adsense-reloaded')}</p>
                      <p>{__('1. Insert', 'quick-adsense-reloaded')} &lt;!--Ads1--&gt;, &lt;!--Ads2--&gt;, {__('etc. into a post to show the Particular Ads at specific location.', 'quick-adsense-reloaded')}</p>
                      <p>{__('2. Insert', 'quick-adsense-reloaded')} &lt;!--RndAds--&gt; {__('into a post to show the Random Ads at specific location', 'quick-adsense-reloaded')}</p>
                    </td>
                  </tr>
                  </tbody></table>
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
                   
                    </div>
                    <div>
                      <select name="customer_query_type" value={this.state.customer_query_type} onChange={this.addCustomerQueryType} className="quads-premium-cus">
                        <option value="">{__('Select', 'quick-adsense-reloaded')}</option>
                        <option value="yes">{__('Yes', 'quick-adsense-reloaded')}</option>
                        <option value="no">{__('No', 'quick-adsense-reloaded')}</option>
                      </select>
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
        {( quads_localize_data.licenses.license == "valid" &&quads_localize_data.licenses.price_id != 0 ) && 
        <span className="activated_messg">Congratulations!</span>
        }
        {( quads_localize_data.licenses.license == "valid" &&quads_localize_data.licenses.price_id != 0 ) && 
        <p className="activated_p"> WP QUADS PRO is now activated and working for you. This enables the Advanced Settings and High Performance for your ADS!</p>
        }
                    <div>
                    {quads_localize_data.licenses.license !== "valid" ? <h3>{__('Activate Your License', 'quick-adsense-reloaded')}</h3> : null}
                    </div>
                    <div className="Key_msg">
                      {/*__('WP QUADS PRO License Key', 'quick-adsense-reloaded')*/}
                    </div>
                   <div>
                   <input value={settings.quads_wp_quads_pro_license_key} onChange={this.add_license_key} name="quads_wp_quads_pro_license_key" type="text" placeholder="License Key" className="quads-premium-cus" />
                   {
                    quads_localize_data.licenses.license == "valid" ?  
                   <div className="">
            {this.state.button_spinner_toggle ?
            <a className="quads-btn quads-btn-primary">
            <span className="quads-btn-spinner"></span>{__('Updating...', 'quick-adsense-reloaded')}
            </a> :
            //<div style={{width: "199px";float: "left";}}>
            <div className="pro_key_btn">
            <a onClick={this.pro_license_key_deactivate} className="quads-btn quads-btn-primary">
            {__('Deactivate License', 'quick-adsense-reloaded')}</a>
            </div>
            
          }
          </div> : null}
          {quads_localize_data.licenses.license !== "valid" ?  
          <div className="">
            {this.state.button_spinner_toggle ?
            <a className="quads-btn quads-btn-primary">
            <span className="quads-btn-spinner"></span>{__('Saving...', 'quick-adsense-reloaded')}
            </a> :
            <a onClick={this.saveSettingsHandler} className="quads-btn quads-btn-primary" id="act_license" >
            {__('Activate License', 'quick-adsense-reloaded')}
            </a>
          }
          <p>{__('Please Activate Your License to get feature updates and premium support.', 'quick-adsense-reloaded')} <a href="https://wpquads.com/accounts/?account_page=downloads" target="_blank">{__('Get Your License Here', 'quick-adsense-reloaded')}</a></p>
						<p><a href="https://wpquads.com/documentation/" target="_blank">{__('Documentation', 'quick-adsense-reloaded')}</a> | <a href="https://wpquads.com/support/" target="_blank">{__('Contact Tech Support', 'quick-adsense-reloaded')}</a></p>
          </div> : null}
          {quads_localize_data.licenses.license !== "valid" ?  
          <div className="">
            {this.state.button_spinner_toggle ?
            <a className="">
            <span className=""></span>
            </a> :
            <div className="inv_msg" style={{display: "none"}}>{__('Enter a Valid License Key', 'quick-adsense-reloaded')}</div>
          }
          </div> : null}
          {
            quads_localize_data.licenses.error == "expired" ? 
            <div className='exp_msg' style={{display:'none'}}> {__('You are entering an Expired License Key', 'quick-adsense-reloaded')} </div> : ''
          }
          
          </div>
            {this.state.licensemsg ?
            <div id="quads_licensemsg"></div> : null}

                </div>
                </div>
               );
               case "settings_adsell":  return(
                <div className="quads-settings-tab-container">
                 <table className="form-table" role="presentation"><tbody>
                  <tr>
                  <th scope="row"><label>{__('Payment Gateway', 'quick-adsense-reloaded')}</label></th>
                  <td>
                  <select value={settings.payment_gateway} onChange={this.formChangeHandler} name="payment_gateway" id="payment_gateway">
                    <option value="paypal">{__('Paypal', 'quick-adsense-reloaded')}</option>
                    <option value="authorize">{__('Authorize.net', 'quick-adsense-reloaded')}</option>
                    <option value="stripe">{__('Stripe', 'quick-adsense-reloaded')}</option>
                  </select>
                  </td>
                  </tr>
                  {(settings.payment_gateway==='paypal') &&
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Paypal Email', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="paypal_email" style={{maxWidth:'25rem',width:'100%'}} value={settings.paypal_email} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  }
                  {(settings.payment_gateway==='authorize') &&
                  <>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Name', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="authorize_name" style={{maxWidth:'25rem',width:'100%'}} value={settings.authorize_name} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Transaction Key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="authorize_transactionKey" style={{maxWidth:'25rem',width:'100%'}} value={settings.authorize_transactionKey} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Merchant Name', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="authorize_merchant_name" style={{maxWidth:'25rem',width:'100%'}} value={settings.authorize_merchant_name} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  </>
                  }
                   {(settings.payment_gateway==='stripe') &&
                  <>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Stripe Publishable Key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="stripe_publishable_key" style={{maxWidth:'25rem',width:'100%'}} value={settings.stripe_publishable_key} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Stripe Secret key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="stripe_secret_key" style={{maxWidth:'25rem',width:'100%'}} value={settings.stripe_secret_key} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  </>
                  }
                  <tr>
                    <th scope="row"><label htmlFor="hide_ajax">{__('Currency', 'quick-adsense-reloaded')}</label></th>
                    <td>
                     <select name="currency" value={settings.currency} onChange={this.formChangeHandler}>
                      <option value="USD">USD</option>
                      <option value="EUR">EUR</option>
                      <option value="GBP">GBP</option>
                      <option value="AUD">AUD</option>
                      <option value="CAD">CAD</option>
                      <option value="JPY">JPY</option>
                      <option value="NZD">NZD</option>
                      <option value="CHF">CHF</option>
                      <option value="HKD">HKD</option>
                      <option value="SGD">SGD</option>
                      <option value="SEK">SEK</option>
                      <option value="DKK">DKK</option>
                      <option value="PLN">PLN</option>
                      <option value="NOK">NOK</option>
                      <option value="HUF">HUF</option>
                      <option value="CZK">CZK</option>
                      <option value="ILS">ILS</option>
                      <option value="MXN">MXN</option>
                      <option value="BRL">BRL</option>
                      <option value="MYR">MYR</option>
                      <option value="PHP">PHP</option>
                      <option value="TWD">TWD</option>
                      <option value="THB">THB</option>
                      <option value="TRY">TRY</option>
                      <option value="RUB">RUB</option>
                      <option value="INR">INR</option>
                      <option value="CNY">CNY</option>
                      <option value="IDR">IDR</option>
                      <option value="KRW">KRW</option>
                      <option value="ZAR">ZAR</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label htmlFor="hide_ajax">{__('Email Notification on Ad Expiry', 'quick-adsense-reloaded')}</label></th>
                    <td>
                      <label className="quads-switch" style={{height:'26px'}}><input id="email_notification_adsell_expiry" type="checkbox" name="email_notification_adsell_expiry" checked={settings.email_notification_adsell_expiry} onChange={this.formChangeHandler} /><span className="quads-slider"></span></label>
                      <p>{__('User will receive an email notification when an ad is about to expire or is expired.', 'quick-adsense-reloaded')}</p>
                    </td>
                    </tr>
                    <tr>
                    <th scope="row"><label>{__('Payment Page', 'quick-adsense-reloaded')}</label></th>
                    <td>
                     <select name="payment_page" value={settings.payment_page} onChange={this.formChangeHandler}>
                      <option value="">{__('Select Page', 'quick-adsense-reloaded')}</option>
                      {this.state.pages.map((page, index) => (
                        <option key={index} value={page['ID']}>{page['post_title']}</option>
                      ))}
                    </select>
                    <p>{__('By default we have created  a payment page named "')}<b>{__('Buy Adspace')}</b>{__('". But if you have deleted or want to modify , create a new page and  paste the shortcode ')} <code>[quads_buy_form]</code> {__(' and select that page from above . ')} <br/><br/> <b>{__('Note ')} </b> {__(': Payment page must  exists and contains the shortcode')} <code>[quads_buy_form]</code>
                    <a target="_blank" href="https://wpquads.com/documentation/how-to-set-up-sellable-ads-in-wp-quads/">{__('Learn More')}</a></p>
                    </td>
                  </tr>
                  </tbody></table>
                </div>
               );
               case "settings_disableads":  return(
                <div className="quads-settings-tab-container">
                 <table className="form-table" role="presentation"><tbody>
                  <tr>
                  <th scope="row"><label>{__('Payment Gateway', 'quick-adsense-reloaded')}</label></th>
                  <td>
                  <select value={settings._dapayment_gateway} onChange={this.formChangeHandler} name="_dapayment_gateway" id="_dapayment_gateway">
                    <option value="paypal">{__('Paypal', 'quick-adsense-reloaded')}</option>
                    <option value="authorize">{__('Authorize.net', 'quick-adsense-reloaded')}</option>
                    <option value="stripe">{__('Stripe', 'quick-adsense-reloaded')}</option>
                  </select>
                  </td>
                  </tr>
                  {(settings._dapayment_gateway==='paypal') &&
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Paypal Email', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_dapaypal_email" style={{maxWidth:'25rem',width:'100%'}} value={settings._dapaypal_email} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  }
                  {(settings._dapayment_gateway==='authorize') &&
                  <>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Name', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_daauthorize_name" style={{maxWidth:'25rem',width:'100%'}} value={settings._daauthorize_name} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Transaction Key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_daauthorize_transactionKey" style={{maxWidth:'25rem',width:'100%'}} value={settings._daauthorize_transactionKey} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Authorize.net Merchant Name', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_daauthorize_merchant_name" style={{maxWidth:'25rem',width:'100%'}} value={settings._daauthorize_merchant_name} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  </>
                  }
                   {(settings._dapayment_gateway==='stripe') &&
                  <>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Stripe Publishable Key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_dastripe_publishable_key" style={{maxWidth:'25rem',width:'100%'}} value={settings._dastripe_publishable_key} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                    <tr>
                      <th scope="row"><label style={{marginLeft:'30px'}}>{__('Stripe Secret key', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_dastripe_secret_key" style={{maxWidth:'25rem',width:'100%'}} value={settings._dastripe_secret_key} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  </>
                  }
                  <tr>
                    <th scope="row"><label htmlFor="hide_ajax">{__('Currency', 'quick-adsense-reloaded')}</label></th>
                    <td>
                     <select name="_dacurrency" value={settings._dacurrency} onChange={this.formChangeHandler}>
                      <option value="USD">USD</option>
                      <option value="EUR">EUR</option>
                      <option value="GBP">GBP</option>
                      <option value="AUD">AUD</option>
                      <option value="CAD">CAD</option>
                      <option value="JPY">JPY</option>
                      <option value="NZD">NZD</option>
                      <option value="CHF">CHF</option>
                      <option value="HKD">HKD</option>
                      <option value="SGD">SGD</option>
                      <option value="SEK">SEK</option>
                      <option value="DKK">DKK</option>
                      <option value="PLN">PLN</option>
                      <option value="NOK">NOK</option>
                      <option value="HUF">HUF</option>
                      <option value="CZK">CZK</option>
                      <option value="ILS">ILS</option>
                      <option value="MXN">MXN</option>
                      <option value="BRL">BRL</option>
                      <option value="MYR">MYR</option>
                      <option value="PHP">PHP</option>
                      <option value="TWD">TWD</option>
                      <option value="THB">THB</option>
                      <option value="TRY">TRY</option>
                      <option value="RUB">RUB</option>
                      <option value="INR">INR</option>
                      <option value="CNY">CNY</option>
                      <option value="IDR">IDR</option>
                      <option value="KRW">KRW</option>
                      <option value="ZAR">ZAR</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                      <th scope="row"><label>{__('Diable Ads Cost', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <input type="text" name="_dacost" style={{maxWidth:'25rem',width:'100%'}} value={settings._dacost} onChange={this.formChangeHandler} />
                      </td>
                    </tr>
                  <tr>
                      <th scope="row"><label>{__('Duration', 'quick-adsense-reloaded')}</label></th>
                      <td>
                      <select value={settings._daduration} onChange={this.formChangeHandler} name="_daduration" id="_daduration">
                        <option value="monthly">{__('Monthly', 'quick-adsense-reloaded')}</option>
                        <option value="yearly">{__('Yearly', 'quick-adsense-reloaded')}</option>
                      </select>
                      </td>
                    </tr>
                 {/*  <tr>
                    <th scope="row"><label htmlFor="hide_ajax">{__('Email Notification on Ad Expiry', 'quick-adsense-reloaded')}</label></th>
                    <td>
                      <label className="quads-switch"><input id="_daemail_notification_adsell_expiry" type="checkbox" name="_daemail_notification_adsell_expiry" checked={settings._daemail_notification_adsell_expiry} onChange={this.formChangeHandler} /><span className="quads-slider"></span></label>
                      <p>{__('User will receive an email notification when an ad is about to expire or is expired.', 'quick-adsense-reloaded')}</p>
                    </td>
                    </tr> */}
                    <tr>
                    <th scope="row"><label>{__('Payment Page', 'quick-adsense-reloaded')}</label></th>
                    <td>
                     <select name="dapayment_page" value={settings.dapayment_page} onChange={this.formChangeHandler} id="dapayment_page">
                      <option value="">{__('Select Page', 'quick-adsense-reloaded')}</option>
                      {this.state.pages.map((page, index) => (
                        <option key={index} value={page['ID']}>{page['post_title']}</option>
                      ))}
                    </select>
                    <p>{__('By default we have created  a payment page named "')}<b>{__('Hide Ads for Premium Members')}</b>{__('". But if you have deleted or want to modify , create a new page and  paste the shortcode ')} <code>[quads_disable_ads_form]</code> {__(' and select that page from above . ')} <br/><br/> <b>{__('Note ')} </b> {__(': Payment page must  exists and contains the shortcode')} <code>[quads_disable_ads_form]</code>
                    </p>
                    </td>
                  </tr>
                  </tbody></table>
                </div>
               );
               case "settings_disableadslist":  return(
                <QuadsDisabledAdsList />
               );
            
            }
          })()}

          {page.path == 'settings_support' || page.path == 'settings_importer' || page.path == 'settings_disableadslist' || page.path == 'settings' || page.path == 'settings_licenses' ? '' : (
            <div className="quads-save-settings">
            {this.state.button_spinner_toggle ?
            <a className="quads-btn quads-btn-primary">
            <span className="quads-btn-spinner"></span> {__('Saving...', 'quick-adsense-reloaded')}
            </a> :
            <a onClick={this.saveSettingsHandler} className="quads-btn quads-btn-primary">
            {__('Save Settings', 'quick-adsense-reloaded')}
            </a>
          }
          </div>
          )           }
          </form>
           
            {( quads_localize_data.licenses.license == "valid" && quads_localize_data.licenses.price_id != 0 ) &&
            <div className="quads-renew-message-main">
            { quads_localize_data.is_pro ?
          <div className="quads-Page-col-main">
          <div className="quads-Page-inner">
          <div className="quads-optionHeader">
          <h3 className="quads-title2"> {__('My Account', 'quick-adsense-reloaded')}</h3>
          </div>
          <div className="quads-field quads-field-account">
          <div className="quads-flex">
          <div className="quads-infoAccount-License">
          <span className="quads-title3">{__('License Key is', 'quick-adsense-reloaded')}</span>
          <span className="quads-infoAccountt quads-isValids" id="quads-account-data"> {__('Activated', 'quick-adsense-reloaded')}</span>          
          <p>{__('Hey! You\'re enjoying all the PRO benefits of the WP QUADS along with regular updates & Technical Support.', 'quick-adsense-reloaded')}</p>
          <p className="">
          { quads_localize_data.licenses.expires>0 ? <span className="quads-title3">{__('Your License is valid for', 'quick-adsense-reloaded')} {quads_localize_data.licenses.expires} {__('days', 'quick-adsense-reloaded')}<span className="quads-refresh quads-isValid material-icons MuiIcon-root" onClick={this.pro_license_key_refresh} id="quads-expiration-data" title="refresh">{__('refresh', 'quick-adsense-reloaded')}</span></span> : <span className="quads-title3">{__('Your', 'quick-adsense-reloaded')} <span className="lifetime">{__('License is valid for', 'quick-adsense-reloaded')} {quads_localize_data.licenses.expires}</span><span className="quads-refresh quads-isValid material-icons MuiIcon-root" onClick={this.pro_license_key_refresh} title="refresh" id="quads-expiration-data">{__('refresh', 'quick-adsense-reloaded')}</span></span> }
          </p>
          <a href="https://wpquads.com/your-account/" target="_blank" className="quads-button quads-button-btn quads-button--small ">{__('Extend License', 'quick-adsense-reloaded')} <span className="quads-user material-icons MuiIcon-root">person</span></a>
          </div>
          </div>
          </div>
          </div>
          </div>
          : <div className="quads-bnr-inv">
            <a href="http://wpquads.com/?utm_source=wpquads&utm_medium=banner&utm_term=click-quads&utm_campaign=wpquads" target="_blank">
              <img  src={quads_localize_data.quads_plugin_url+'assets/images/quads_banner_250x521_buy.png'} />
           </a>
          </div> }
          </div>
        }
        {( quads_localize_data.licenses.price_id == 0 ) &&
          <div className="quads-renew-message-main">
            { quads_localize_data.is_pro ?
          <div className="quads-Page-col-main">
          <div className="quads-Page-inner">
          <div className="quads-optionHeader">
          <h3 className="quads-title2">{__('My Account', 'quick-adsense-reloaded')}</h3>
          </div>
          <div className="quads-field quads-field-account">
          <div className="quads-flex">
          <div className="quads-infoAccount-License">
          <span className="quads-title3">{__('License Key is', 'quick-adsense-reloaded')}</span>
          <span className="quads-infoAccountt quads-isinValid" id="quads-account-data">{__('Expired', 'quick-adsense-reloaded')}</span>          
          <p>{__('Extend the License to receive the further updates & support.', 'quick-adsense-reloaded')}</p>
          <a href="https://wpquads.com/your-account/" target="_blank" className="quads-button quads-button-btn quads-button--small quads-icon-user">{__('Extend License', 'quick-adsense-reloaded')}</a>
          </div>
          </div>
          </div>
          </div>
          </div>
        : <div className="quads-bnr-inv">
            <a href="http://wpquads.com/?utm_source=wpquads&utm_medium=banner&utm_term=click-quads&utm_campaign=wpquads" target="_blank">
              <img  src={quads_localize_data.quads_plugin_url+'assets/images/quads_banner_250x521_buy.png'} />
           </a>
          </div> }
          </div>
        }
        
                   
          </div>
          </div>
          </div>
        );
  }
}
export default QuadsAdListSettings;
