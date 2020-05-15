import React, { Component, Fragment } from 'react';
import { Redirect } from 'react-router-dom'
import { BrowserRouter as Router, Switch, Route, Link } from 'react-router-dom';
import queryString from 'query-string'
import QuadsAdCreateRouter from  '../ad-create-router/QuadsAdCreateRouter'
import Icon from '@material-ui/core/Icon';
import Tooltip from '@material-ui/core/Tooltip';


import './QuadsAdList.scss';

class QuadsAdList extends Component {

  constructor(props) {     
        super(props);
        this.state = { 
           redirect:false,
           ad_id:null 
        };        
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
          case 'random_ads':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/random_ad_icon.png';
          type = "random ads";
          break;
          case 'double_click':
          img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/double_click_icon.png';
          type = "random ads";
          break;
      
        default:
          break;
      }

      type_img.push(<Tooltip title={type} placement="left" arrow key={index}><img key={index} height="20" width="20" src={img_url} /></Tooltip>);
      
    return type_img;
  }    
  render() {    
    
    const {__} = wp.i18n;     
    const { error, isLoaded, items } = this.props.ad_list;             
    if (error) {
      return <div>Error: {error.message}</div>;
    } else if (!isLoaded) {
      return <div className="quads-cover-spin"></div>;
    } else {
      return (
        <div>         
        <div>  
        <div className="quads-ad-list-table-div">  
        { items ?      
        <table className="quads-ad-table">
          <thead>
          <tr>
          <th>{__('Name', 'quick-adsense-reloaded')}</th>
          <th>{__('Type', 'quick-adsense-reloaded')}</th>
          <th>{__('Last Modified', 'quick-adsense-reloaded')}</th>
          {/* <th>{__('Status', 'quick-adsense-reloaded')}</th> */}
          <th></th>
          </tr>
          </thead>
          <tbody>
          {items.map((item, index) => (     
                   
            <tr key={index}>
                <td>{item.post_meta.label} {item.post.post_status == 'draft' ? <span className="quads-ad-label-draft">draft</span> : ''}</td>
                <td>{this.getImageByAdType(item.post_meta.ad_type, index)} {this.getAmpLogoByEnabled(item.post_meta.enabled_on_amp, index)}</td>
                <td>{item.post.post_modified}</td>
                {/* <td>{item.post.post_status}</td> */}
                <td>
                <div className="quads-action-div">

                {this.props.more_box_id ==  item.post_meta.ad_id ?
                <div className="quads-more-icon-box">
                  <div className="quads-more-icon-box-close" onClick={this.props.hideMoreIconBox}><Icon>close</Icon></div>
                  <ul>
                    <li role="presentation"><a onClick={this.props.processAction} data-ad={item.post_meta.ad_id} data-id={item.post.post_status == 'publish' ? 'draft' : 'publish'} ><Icon>{item.post.post_status == 'publish' ? 'drafts' : 'publish'}</Icon> <span>{__(item.post.post_status == 'publish' ? 'Set to Draft' : 'Publish', 'quick-adsense-reloaded')}</span></a></li>
                    <li role="presentation"><a onClick={this.props.showDeleteModal} data-ad={item.post_meta.ad_id} data-id="delete"><Icon>delete</Icon> <span>{__('Delete', 'quick-adsense-reloaded')}</span></a></li>
                    <li role="presentation"><a onClick={this.props.processAction} data-ad={item.post_meta.ad_id} data-id="duplicate"><Icon>file_copy</Icon> <span>{__('Duplicate', 'quick-adsense-reloaded')}</span></a></li>
                  </ul>
                  </div> : ''  }

                <Link to={`admin.php?page=quads-settings&path=wizard&ad_type=${item.post_meta.ad_type}&action=edit&post=${item.post.post_id}`} className="quads-edit-btn"><Icon>edit_icon</Icon> </Link>                                                
                  <a className="quads-edit-btn" data-index={index} data-id={item.post_meta.ad_id} onClick={this.props.showMoreIconBox}><Icon>more_vert_icon</Icon></a>                
                </div>  
                </td>
            </tr>
          ))} 
            </tbody>
        </table> : <table className="quads-ad-table nodatatable">
<thead>
         <tr><td> Let's create our First Ad, in 3 simple steps. <div className="quads-add-btn"><a className="quads-btn quads-btn-primary" onClick={this.props.nodatashowAddTypeSelector}><Icon>add_circle</Icon>Create Ad</a></div></td></tr></thead></table> 
        }
        </div>                  
          </div>          
        {(this.state.redirect && this.state.ad_id ) ? <Redirect to="admin.php?page=quads-settings&ads_page=ad-wizard&creation_type=edit&ad_setup=config" /> : ''}
       </div>
      );
    }
  }
}


export default QuadsAdList;