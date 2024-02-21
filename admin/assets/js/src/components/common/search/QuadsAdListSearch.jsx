import React, { Component, Fragment } from 'react';
import queryString from 'query-string'
import InputLabel from '@material-ui/core/InputLabel';
import MenuItem from '@material-ui/core/MenuItem';
import FormControl from '@material-ui/core/FormControl';
import Select from '@material-ui/core/Select';

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
{page.path === undefined?  <div className='quads-select-div'>
    <FormControl style={{minWidth:'200px',paddingRight:'10px'}}>
        <InputLabel id="quads_bulk_actions_lid">{__('Bulk Actions', 'quick-adsense-reloaded')}</InputLabel>
        <Select
          labelId="quads_bulk_actions_lid"
          className="quads_bulk_actions"
          onChange={this.props.handleBulkActions}
        >
          <MenuItem value={'draft'}>{__('Set to Draft', 'quick-adsense-reloaded')}</MenuItem>
          <MenuItem value={'publish'}>{__('Set to Publish', 'quick-adsense-reloaded')}</MenuItem>
          <MenuItem value={'delete'}>{__('Delete', 'quick-adsense-reloaded')}</MenuItem>
        </Select>
      </FormControl>

      <FormControl style={{minWidth:'200px',paddingRight:'10px'}}>
        <InputLabel id="quads_sort_lid">{__('Sort By', 'quick-adsense-reloaded')}</InputLabel>
        <Select
          labelId="quads_sort_lid"
          onChange={this.props.handleSortBy}
        >
          <MenuItem value={'impression'}>{__('Sort by Impression', 'quick-adsense-reloaded')}</MenuItem>
          <MenuItem value={'click'}>{__('Sort by Clicks', 'quick-adsense-reloaded')}</MenuItem>
        </Select>
      </FormControl>

      <FormControl style={{minWidth:'200px'}}>
        <InputLabel id="quads_filter_lid">{__('Filter By', 'quick-adsense-reloaded')}</InputLabel>
        <Select
          labelId="quads_filter_lid"
          onChange={this.props.handleFilterBy}
        >
        <MenuItem value={'adsense'}>{__('Adsense', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'plain_text'}>{__('Custom Code', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'rotator_ads'}>{__('Rotator Ads', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'random_ads'}>{__('Random Ads', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'popup_ads'}>{__('Popup Ads', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'video_ads'}>{__('Video Ads', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'double_click'}>{__('Double Click', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'yandex'}>{__('Yandex', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'mgid'}>{__('mgid', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'propeller'}>{__('Propeller', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'ad_image'}>{__('Banner Image', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'ad_blindness'}>{__('ad_blindness', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'ab_testing'}>{__('A/B Testing', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'taboola'}>{__('Taboola', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'media_net'}>{__('Media.net', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'mediavine'}>{__('Mediavine', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'outbrain'}>{__('Outbrain', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'infolinks'}>{__('Infolinks', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'background_ad'}>{__('Background Ad', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'group_insertion'}>{__('Group Insertion', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'skip_ads'}>{__('Skip Ads', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'loop_ads'}>{__('Loop Ads', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'carousel_ads'}>{__('Carousel Ads', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'parallax_ads'}>{__('Parallax Ads', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'half_page_ads'}>{__('Half Page Ads', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'sticky_scroll'}>{__('Sticky Scroll', 'quick-adsense-reloaded')}</MenuItem>
        <MenuItem value={'floating_cubes'}>{__('Floating Cubes', 'quick-adsense-reloaded')}</MenuItem> 
        </Select>
      </FormControl>
</div>:''}
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