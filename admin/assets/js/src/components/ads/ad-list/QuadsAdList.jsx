import React, { Component, Fragment } from 'react';
import { Redirect } from 'react-router-dom'
import { BrowserRouter as Router, Switch, Route, Link } from 'react-router-dom';
import queryString from 'query-string'
import QuadsAdCreateRouter from  '../ad-create-router/QuadsAdCreateRouter'
import Icon from '@material-ui/core/Icon';
import Tooltip from '@material-ui/core/Tooltip';
import { Alert } from '@material-ui/lab';
import QuadsCheckbox from "./QuadsCheckbox";


import './QuadsAdList.scss';

class QuadsAdList extends Component {
  

  constructor(props) {     
        super(props);
        this.state = { 
           redirect:false,
           ad_id:null,
           adListsId : null,
           toggleAdsLists : false,
           importquadsclassicmsgprocessing : "",  
           importquadsclassiccss : false,
           importquadsclassicalertcss : false,
           copiedInputId: null,
        };
        this.wrapperRef = React.createRef();
        this.handleClickOutside = this.handleClickOutside.bind(this);               
  }

  componentDidMount() {
    document.addEventListener("mousedown", this.handleClickOutside);
  }

  componentWillUnmount() {
    document.removeEventListener("mousedown", this.handleClickOutside);
  }

  handleClickOutside(event) {
    if( this.wrapperRef.current && !this.wrapperRef.current.contains(event.target) && this.props.more_box_id ) {
        this.props.hideMoreIconBox();
    }
    if( this.wrapperRef.current && !this.wrapperRef.current.contains(event.target) && this.state.adListsId ) {
        this.setState({toggleAdsLists: false});
    }
  }

  QuadsRedirectToEditAd = (e) => {
       this.setState({redirect: true, ad_id:e.currentTarget.dataset.id});
  }  
  getAmpLogoByEnabled = (enabled, index) =>{
    let type_img = [];
    let img_url  = '';

    if(enabled){
      img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/amp_logo.png';
      type_img.push(<Tooltip title='AMP' placement="right" arrow key={index}><img key={index} height="20" width="20" src={img_url} /></Tooltip>);
    }            
    return type_img;
  }
    quads_classic_ads = (status) => {
    if(status == 'no'){
    quads.quads_import_classic_ads_popup = false;
    }
        this.setState({importquadsclassicmsgprocessing: 'Importing Ads', importquadsclassiccss : true});
    if(this.state.importquadsclassicmsgprocessing !=''){
      return;
    }
   
    let formData = new FormData();
    formData.append('action', 'quads_sync_ads_in_new_design');
    formData.append('nonce', quads.nonce);
    formData.append('status', status);

    fetch(ajaxurl,{
      method: "post",
      body: formData              
    })
    .then(res => res.json())
    .then(
      (result) => {         
              this.setState({importquadsclassicmsg: 'Ads have been successfully', importquadsclassiccss : false,importquadsclassicalertcss : true});                             
      },        
      (error) => {
        
      }
    );  

  }
  getImageByAdType = (type, index) =>{
    let type_img = [];
    let img_url  = '';

      switch (type) {
        case 'adsense':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/adsensev3.png';
          break;
        case 'plain_text':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/htmlcode.png';
          type = "custom code";
          break;
          case 'rotator_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/rotator_ads_icon.png';
           break;
          type = "Rotator ads";
          break;
          case 'random_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/random_ad_icon.png';
          type = "Random Ad";
          break;
          case 'popup_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/popup_ads.png';
          type = "Popup Ad";
          break;
          case 'video_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/video_ads.png';
          type = "Video Ad";
          break;
          case 'double_click':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/double_click_icon.png';
          type = "Google Ad Manager";
          break;
          case 'yandex':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/yandex_icon.png';
          type = "Yandex";
          break;
          case 'mgid':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/mgid_icon.png';
          type = "MGID";
          break;
          case 'propeller':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/propeller_icon.png';
          type = "Propeller";
          break;
          case 'ad_image':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/banner_ad_icon.png';
          type = "Banner Ad";
          break;
          case 'video_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/video_ads.png';
          type = "Video Ad";
          break;
          case 'ad_blindness':
            img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ad_blindness_icon.png';
            type = "Ad Blindness";
            break;
          case 'ab_testing':
            img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ab_testing_img.png';
            type = "AB Testing";
            break;
          case 'taboola':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/taboola_icon.png';
          break;
          case 'media_net':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/medianet_icon.png';
          type = "Media net";
          break;
          case 'mediavine':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/mediavine_icon.png';
          break;
          case 'outbrain':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/outbrain_icon.png';
          break;
           case 'infolinks':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/infolinks.png';
          break;
          case 'background_ad':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/bg_ad_icon.png';
          type = "Background Ad";
          break;
        case 'group_insertion':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/group_insertion_icon.png';
          type = "Group Insertion";
          break;
          case 'skip_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/skip_ads_icon.png';
          type = "Skip Ad";
          break;
          case 'loop_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/loop_ads_icon.png';
          type = "Loop Ad";
          break;
          case 'carousel_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/carousel_ads_icon.png';
          type = "Carousel Ad";
          break;
          case 'parallax_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/parallax_ads_icon.png';
          type = "Parallax Ad";
          break;
          case 'half_page_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/halfpage_ads_icon.png';
          type = "Half Page Slider Ad";
          break;
          case 'sticky_scroll':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/sticky_scroll_icon.png';
          type = "Hold on Scroll  Ad";
          break;
          case 'floating_cubes':
            img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/floating_ads_icon.png';
            type = "Floating Ad";
            break;
            case 'ads_space':
              img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ads_space_icon.png';
              type = "Ads Space";
              break;
        default:
          break;
      }

      type_img.push(<Tooltip title={type} placement="left" arrow key={index}><img key={index} height="20" width="20" src={img_url} /></Tooltip>);
      
    return type_img;
  }   

  getActiveAdsLists = (lists, index) =>{
    const rows = [];
    if(typeof(lists) != 'undefined'){
      for (let i = 0; i < lists.length; i++) {
          rows.push(<span>{lists[i].label}</span>);
      }
    }
    return <span className="quads-ads-selected-lists" ref={this.wrapperRef}>{rows}</span>;
  }

  showInfoAds = (cAdId) => {
    this.setState({adListsId: cAdId, toggleAdsLists: !this.state.toggleAdsLists});
  }

   handleCopy = ( event ) => {

    const valueToCopy = event.target.value;
    event.target.select();
    navigator.clipboard.writeText(valueToCopy)
    .then(() => {
      let inputId = (valueToCopy).match(/\d+/);
        this.setState({copiedInputId: inputId});
        let  target = document.getElementById('post_shortcode_'+inputId);
        target.style.backgroundColor = 'green';
        target.style.color = 'white';
        target.style.textAlign = 'center';
        target.value = 'Copied!';

        setTimeout(() => {
          this.setState({copiedInputId: null});
          target.style.backgroundColor = '';
          target.style.color = '';
          target.textAlign = '';
          target.value = valueToCopy ;
        }, 3000);
      })
      .catch(err => {
        console.error('Failed to copy text: ', err);
      });
  };
   
  render() {    
    
    const {__} = wp.i18n;     
    const { error, isLoaded, items } = this.props.ad_list; 
    const t_main = { 
        position: "absolute",
        left: this.props.settings.ad_performance_tracking ? "83px" : "-12px",
        top: "53px",
  };
    const t_main_more = { 
        position: "absolute",
        left: this.props.settings.ad_performance_tracking ? "132px" : "36px",
        top: "53px",
  };
    const ttp_1 = { 
      opacity: "1", 
      transform: "none",
       transition: "opacity 200ms cubic-bezier(0.4, 0, 0.2, 1) 0ms, transform 133ms cubic-bezier(0.4, 0, 0.2, 1) 0ms",
  };
    const ttp_1_ = { 
      top: "2px",
      left: "-6px !important",
      margin: "-0.88em 5px 0px 18px",
    }
  
   
    if (error) {
      return <div>Error: {error.message}</div>;
    } else if (!isLoaded) {
      return <div className="quads-cover-spin"></div>;
    } else {
      return (
        <div>         
        <div>  
        <div className="quads-ad-list-table-div" onClick={this.checkClickForMenu}>  
        { items && items.length > 0 ?      
        <table className="quads-ad-table">
          <thead>
          <tr>
          <th style={{width:'60px'}}> <input id='quads_master_checkbox' name='quads_ids_' value={'all'} type='checkbox' onClick={this.props.handleMasterCheckbox}/> <span id="quads_selected_total_cnt"></span></th>
          <th>{__('Name', 'quick-adsense-reloaded')}</th>
          <th>{__('Type', 'quick-adsense-reloaded')}</th>
          <th>{__('Last Modified', 'quick-adsense-reloaded')}</th>
          <th style={{width:'140px'}}>{__('Shortcode', 'quick-adsense-reloaded')}</th>
          <th>{__('Actions', 'quick-adsense-reloaded')}</th>
          </tr>
          </thead>
          <tbody>
          {items.map((item, index) => ( item.post_meta.ad_id &&(    
                   
            <tr key={index}>
                <td><QuadsCheckbox index={index} name='quads_ids' value={item.post_meta.ad_id} onClick={this.props.handleBulkCheckbox}/></td>
                <td><Link onMouseEnter={this.props.EditHoverIn} onMouseLeave={this.props.EditHoverOut} data-index={index} data-id={item.post_meta.ad_id} to={`admin.php?page=quads-settings&path=wizard&ad_type=${item.post_meta.ad_type}&action=edit&post=${item.post.post_id}`} className="quads-edit-btn"> {item.post_meta.label} </Link> {item.post_meta.ad_type == 'rotator_ads' || item.post_meta.ad_type == 'carousel_ads' || item.post_meta.ad_type == 'group_insertion' ? <span className="quads-adlists info-wrpr"><span className="material-icons info" aria-hidden="true" onClick={() => this.showInfoAds(item.post_meta.ad_id)}>info</span>{ item.post_meta.ad_id == this.state.adListsId && this.state.toggleAdsLists ? this.getActiveAdsLists(item.post_meta.ads_list, index) : '' }</span> : ''} {item.post.post_status == 'draft' ? <span className="quads-ad-label-draft">{__('draft','quick-adsense-reloaded')}</span> : ''}</td>
                <td>{this.getImageByAdType(item.post_meta.ad_type, index)} {this.getAmpLogoByEnabled(item.post_meta.enabled_on_amp, index)}</td>
                <td>{item.post.post_modified}</td>
                <td><input name="post_shortcode" id={'post_shortcode_'+(item.post_meta.quads_ad_old_id).match(/\d+/)} data-attr={''+(item.post_meta.quads_ad_old_id).match(/\d+/)+''} type="text" defaultValue={'[quads id='+(item.post_meta.quads_ad_old_id).match(/\d+/)+']'}  onClick={this.handleCopy} style={{ cursor: 'pointer' , width: '120px'}} readOnly/>
              </td>
                <td>
                <div className="quads-action-div">
                {this.props.static_box_id ==  item.post_meta.ad_id ?
                <div className="quads-more-icon-box">
                  <div className="quads-more-icon-box-close" onClick={this.props.hideStaticIconBox}><Icon>close</Icon></div>                  
                  
                  {this.props.analytics_loader ===  false ?
                  <ul>
                <li role="presentation"><span className="static_num">{this.props.analytics_impressions? this.props.analytics_impressions : 0}</span> <span>{__('Impression ', 'quick-adsense-reloaded')}</span></li>
                    <li role="presentation"><span className="static_num">{this.props.analytics_clicks ? this.props.analytics_clicks : 0}</span> <span>{__('Clicks ', 'quick-adsense-reloaded')}</span></li>
                  {
                    quads_localize_data.is_pro ? <li><Link to={'admin.php?page=quads-settings&path=view_report&id='+item.post_meta.ad_id+'&ad='+item.post_meta.label+''} id={item.post_meta.ad_id} className='view_reports_'> {__('View Full Report', 'quick-adsense-reloaded')}</Link></li> : ''
                  }
                  </ul>
                  :<ul>
                    <li role="presentation"><div className="quads_ajax_loader"></div></li> 
                  </ul>
                }
                  </div> : ''  }
                  {this.props.settings.ad_performance_tracking ? <a className="quads-edit-btn" onMouseEnter={this.props.AdImp_Count_HoverIn} onMouseLeave={this.props.AdImp_Count_HoverOut} data-index={index} data-id={item.post_meta.ad_id} onClick={this.props.showStaticIconBox}><Icon>stacked_bar_chart</Icon></a>                
                : null }
                
                {(this.props.settings.ad_log && quads_localize_data.is_pro) ?<Link className="quads-edit-btn" onMouseEnter={this.props.AdLogHoverIn} onMouseLeave={this.props.AdLogHoverOut} data-index={index} data-id={item.post_meta.ad_id} to={'admin.php?page=quads-settings&path=ad_logging&ad_id='+item.post_meta.ad_id} ><img height="20" width="20" src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ad_log.png'} ></img></Link>              
                : null }
                { item.post_meta.ad_id && this.props.adlog_hover_id ==  item.post_meta.ad_id ?
                  <div role="tooltip" className="MuiTooltip-popper MuiTooltip-popperArrow" id="tooltip_2" x-placement="bottom"><div className="MuiTooltip-tooltip MuiTooltip-tooltipPlacementLeft MuiTooltip-tooltipArrow" style={ttp_1} >Ad log<span className="MuiTooltip-arrow" style={ttp_1_}></span></div></div> : '' }
                
                <Link onMouseEnter={this.props.EditHoverIn} onMouseLeave={this.props.EditHoverOut} data-index={index} data-id={item.post_meta.ad_id} to={`admin.php?page=quads-settings&path=wizard&ad_type=${item.post_meta.ad_type}&action=edit&post=${item.post.post_id}`} className="quads-edit-btn"><Icon>edit_icon</Icon> </Link> 
                  
                { item.post_meta.ad_id && this.props.edit_hover_id ==  item.post_meta.ad_id ?
                  
            
                  <div style={t_main} className="MuiTooltip-popper MuiTooltip-popperArrow" id="tooltip" x-placement="bottom"><div className="MuiTooltip-tooltip MuiTooltip-tooltipPlacementLeft MuiTooltip-tooltipArrow" style={ttp_1} >{__('Edit Ad', 'quick-adsense-reloaded')}<span className="MuiTooltip-arrow" style={ttp_1_}></span></div></div>
                   : '' }
                {item.post_meta.ad_id && this.props.more_box_id ==  item.post_meta.ad_id ?
                <div className="quads-more-icon-box" style={{left:this.props.settings.ad_performance_tracking ?(this.props.settings.ad_log?'10%':'-5%'):'-25%'}} ref={this.wrapperRef}>
                  <div className="quads-more-icon-box-close" onClick={this.props.hideMoreIconBox}><Icon>close</Icon></div>
                  <ul>
                    <li role="presentation"><a onClick={this.props.processAction} data-ad={item.post_meta.ad_id} data-id={item.post.post_status == 'publish' ? 'draft' : 'publish'} ><Icon>{item.post.post_status == 'publish' ? 'drafts' : 'publish'}</Icon> <span>{__(item.post.post_status == 'publish' ? __('Set to Draft', 'quick-adsense-reloaded') : __('Publish', 'quick-adsense-reloaded'))}</span></a></li>
                    <li role="presentation"><a onClick={this.props.showDeleteModal} data-ad={item.post_meta.ad_id} data-id="delete"><Icon>delete</Icon> <span>{__('Delete', 'quick-adsense-reloaded')}</span></a></li>
                    <li role="presentation"><a onClick={this.props.processAction} data-ad={item.post_meta.ad_id} data-id="duplicate"><Icon>file_copy</Icon> <span>{__('Duplicate', 'quick-adsense-reloaded')}</span></a></li>
                  </ul>
                  </div> : ''  } 
                { item.post_meta.ad_id && this.props.more_hover_box_id ==  item.post_meta.ad_id ?
                  <div role="tooltip" style={t_main_more} id="tooltip_3" className="MuiTooltip-popper MuiTooltip-popperArrow" x-placement="bottom"><div className="MuiTooltip-tooltip MuiTooltip-tooltipPlacementLeft MuiTooltip-tooltipArrow" style={ttp_1} >{__('More Options','quick-adsense-reloaded')}<span className="MuiTooltip-arrow" style={ttp_1_}></span></div></div> : '' }
                  
                <a onMouseEnter={this.props.showMoreHoverIn} onMouseLeave={this.props.showMoreHoverOut} className="quads-edit-btn" data-index={index} data-id={item.post_meta.ad_id} onClick={this.props.showMoreIconBox}><Icon>more_vert_icon</Icon></a>       
        
                </div>  
                </td>
                
            </tr>
             )   ))} 
            </tbody>
        </table> : <div className="nodatadiv"><div className="first_ad_main">
                    {(this.props.ad_type!=='ads_space') &&
                      <>
                        <h3>{__('Thank you for using WP Quads','quick-adsense-reloaded')}</h3>
                        <div className="first_ad">{__('Let\'s ', 'quick-adsense-reloaded') } <strong>{__('create our First Ad', 'quick-adsense-reloaded') }</strong> {__(' in 3 simple steps', 'quick-adsense-reloaded') }. </div>
                        <div className="quads-add-btn"><a className="quads-btn quads-btn-primary" onClick={this.props.nodatashowAddTypeSelector}><Icon>add_circle</Icon>{__('Create Ad', 'quick-adsense-reloaded') }</a></div>
                      </>
                    }
                    {(this.props.ad_type==='ads_space') &&
                      <>
                        <h3>{__('You don\'t have the ad space available to sell','quick-adsense-reloaded')}</h3>
                        <div className="first_ad">{__('You can start sellings ads by creating a space on your website from the ', 'quick-adsense-reloaded') } <strong>{__('below button', 'quick-adsense-reloaded') }</strong>. </div>
                        <div className="quads-add-btn">
                          <div class="quads-add-btn">
                            <Link class="quads-btn quads-btn-primary" to={`admin.php?page=quads-settings&path=wizard&ad_type=ads_space`}><Icon>add_circle</Icon>{__('Create Ad Space', 'quick-adsense-reloaded') }</Link>
                          </div>
                        </div>
                      </>
                    }
                  </div>
                  {quads.quads_import_classic_ads_popup && quads.quads_get_active_ads !=="0" ?
                    <div className="fakebox" >
                    <div className="fakebox_close" onClick={() => this.quads_classic_ads('no')}> </div>
                        <div><h3>{__('This is your first time on New Interface','quick-adsense-reloaded')}</h3></div>
                        <div className="text">{__('Would you like to import your ads from the classic view?','quick-adsense-reloaded')} </div>
                        {!this.state.importquadsclassicalertcss?
                        <div className="quads-add-btn"><a className="quads-btn quads-btn-primary yes" onClick={() => this.quads_classic_ads('yes')}>Yes, Import</a><a className="quads-btn quads-btn-primary no" onClick={() => this.quads_classic_ads('no')}>{__('No Thanks', 'quick-adsense-reloaded') }</a></div>
                         : ''}

                        <div style={{display: this.state.importquadsclassiccss ? 'block' : 'none' }} className='updating-message importquadsclassicmsgprocessing'>{__('Importing Ads', 'quick-adsense-reloaded') }</div>

                        <div style={{display: this.state.importquadsclassicalertcss ? 'block' : 'none' }}><Alert severity="success" action={<Icon onClick={() => this.quads_classic_ads('no')}>close</Icon>}>{this.state.importquadsclassicmsg}</Alert> </div>
                    </div>
                  : ''}

                </div>
        }
        </div>                  
          </div>          
        {(this.state.redirect && this.state.ad_id ) ? <Redirect to="admin.php?page=quads-settings&ads_page=ad-wizard&creation_type=edit&ad_setup=config" /> : ''}
       </div>
      );
    }
  }

  checkClickForMenu = (e) => {
    if(this.props.more_box_id)
    {
      this.props.hideMoreIconBox();
    }
} 
}


export default QuadsAdList;