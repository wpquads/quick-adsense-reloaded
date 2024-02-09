import React, { Component, Fragment } from 'react';
import ReactDOM from "react-dom";
import queryString from 'query-string'

import './QuadsAdListSearch.scss';

class QuadsAdListSearch extends Component {
    
  constructor(props) {  
      
    super(props);
    this.state = {      
      posts_found:0,  
    };       
  }
   
  static getDerivedStateFromProps(props, state) {   
    return {
        posts_found : props.ad_list?.posts_found,
    };
}
  render() {
    const page = queryString.parse(window.location.search);
    const {__} = wp.i18n;      
    let searchIcon = quads_localize_data.quads_plugin_url+'admin/assets/img/quads-search.png'; 
    return (
     <div className="quads-ad-search-box">
       {typeof this.props.search_text === 'undefined'  && typeof page.ad_id !== 'undefined' ?
     <input
     style = {{ backgroundImage: `url(${searchIcon})`,          
     backgroundRepeat: 'no-repeat',
     visibility:'hidden'
   }}
      onChange={e =>this.props.triggerSearch(e)} value={page.ad_id} placeholder={__('Search by ad unit, id, format, etc', 'quick-adsense-reloaded')} type="text"/>
:
<div>
  <div className='quads-select-div'>
<label>{__('Bulk Actions', 'quick-adsense-reloaded')}<select className='quads_bulk_actions' onChange={this.props.handleBulkActions}><option value={''}>{__('-- Select --', 'quick-adsense-reloaded')}</option><option value={'draft'}>{__('Set to Draft', 'quick-adsense-reloaded')}</option><option value={'publish'}>{__('Set to Publish', 'quick-adsense-reloaded')}</option><option value={'delete'}>{__('Delete', 'quick-adsense-reloaded')}</option></select></label>
<label>{__('Sort By', 'quick-adsense-reloaded')} <select  onChange={this.props.handleSortBy}><option value={''}>{__('-- Select --', 'quick-adsense-reloaded')}</option><option value={'impression'}>{__('Sort by Impression', 'quick-adsense-reloaded')}</option><option value={'click'}>{__('Sort by Clicks', 'quick-adsense-reloaded')}</option></select></label>
<label>{__('Filter By', 'quick-adsense-reloaded')} <select  onChange={this.props.handleFilterBy}>
    <option value={''}>{__('-- Select --', 'quick-adsense-reloaded')}</option>
    <option value={'adsense'}>{__('Adsense', 'quick-adsense-reloaded')}</option>
    <option value={'plain_text'}>{__('Custom Code', 'quick-adsense-reloaded')}</option>
    <option value={'rotator_ads'}>{__('Rotator Ads', 'quick-adsense-reloaded')}</option>
    <option value={'random_ads'}>{__('Random Ads', 'quick-adsense-reloaded')}</option>
    <option value={'popup_ads'}>{__('Popup Ads', 'quick-adsense-reloaded')}</option>
    <option value={'video_ads'}>{__('Video Ads', 'quick-adsense-reloaded')}</option>
    <option value={'double_click'}>{__('Double Click', 'quick-adsense-reloaded')}</option>
    <option value={'yandex'}>{__('Yandex', 'quick-adsense-reloaded')}</option>
    <option value={'mgid'}>{__('mgid', 'quick-adsense-reloaded')}</option>
    <option value={'propeller'}>{__('Propeller', 'quick-adsense-reloaded')}</option>
    <option value={'ad_image'}>{__('Banner Image', 'quick-adsense-reloaded')}</option>
    <option value={'ad_blindness'}>{__('ad_blindness', 'quick-adsense-reloaded')}</option>
    <option value={'ab_testing'}>{__('A/B Testing', 'quick-adsense-reloaded')}</option>
    <option value={'taboola'}>{__('Taboola', 'quick-adsense-reloaded')}</option>
    <option value={'media_net'}>{__('Media.net', 'quick-adsense-reloaded')}</option>
    <option value={'mediavine'}>{__('Mediavine', 'quick-adsense-reloaded')}</option>
    <option value={'outbrain'}>{__('Outbrain', 'quick-adsense-reloaded')}</option>
    <option value={'infolinks'}>{__('Infolinks', 'quick-adsense-reloaded')}</option>
    <option value={'background_ad'}>{__('Background Ad', 'quick-adsense-reloaded')}</option>
    <option value={'group_insertion'}>{__('Group Insertion', 'quick-adsense-reloaded')}</option>
    <option value={'skip_ads'}>{__('Skip Ads', 'quick-adsense-reloaded')}</option>
    <option value={'loop_ads'}>{__('Loop Ads', 'quick-adsense-reloaded')}</option>
    <option value={'carousel_ads'}>{__('Carousel Ads', 'quick-adsense-reloaded')}</option>
    <option value={'parallax_ads'}>{__('Parallax Ads', 'quick-adsense-reloaded')}</option>
    <option value={'half_page_ads'}>{__('Half Page Ads', 'quick-adsense-reloaded')}</option>
    <option value={'sticky_scroll'}>{__('Sticky Scroll', 'quick-adsense-reloaded')}</option>
    <option value={'floating_cubes'}>{__('Floating Cubes', 'quick-adsense-reloaded')}</option>    
</select></label>
</div>
<input
     style = {{ backgroundImage: `url(${searchIcon})`,          
     backgroundRepeat: 'no-repeat',
     visibility:'visible'
   }}
      onChange={this.props.triggerSearch} placeholder={__('Search by ad unit, id, format, etc', 'quick-adsense-reloaded')} type="text"/>
      </div>
  }


     </div>
    );

  }
  
}

export default QuadsAdListSearch;