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
      ad_types : [],  
    };       
  }

  // create afunction to get  adtypes from the server

  componentDidMount() {
    this.getAdTypes();
  }

  getAdTypes = () => {
    let url = quads_localize_data.rest_url+'quads-route/get-ad-types';
    fetch(url,{
      headers: {
        'X-WP-Nonce': quads_localize_data.nonce,
      }
    }
    )
    .then(res => res.json())
    .then(
      (result) => {
        let adTypesArray = Array.isArray(result) ? result : Object.values(result);
        this.setState({ad_types: adTypesArray});
      },
      (error) => {

      }
    );
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
    const adOptions = [
      { value: 'adsense', label: __('Adsense', 'quick-adsense-reloaded') },
      { value: 'plain_text', label: __('Custom Code', 'quick-adsense-reloaded') },
      { value: 'rotator_ads', label: __('Rotator Ads', 'quick-adsense-reloaded') },
      { value: 'random_ads', label: __('Random Ads', 'quick-adsense-reloaded') },
      { value: 'popup_ads', label: __('Popup Ads', 'quick-adsense-reloaded') },
      { value: 'video_ads', label: __('Video Ads', 'quick-adsense-reloaded') },
      { value: 'double_click', label: __('Double Click', 'quick-adsense-reloaded') },
      { value: 'yandex', label: __('Yandex', 'quick-adsense-reloaded') },
      { value: 'mgid', label: __('mgid', 'quick-adsense-reloaded') },
      { value: 'propeller', label: __('Propeller', 'quick-adsense-reloaded') },
      { value: 'ad_image', label: __('Banner Image', 'quick-adsense-reloaded') },
      { value: 'ad_blindness', label: __('ad_blindness', 'quick-adsense-reloaded') },
      { value: 'ab_testing', label: __('A/B Testing', 'quick-adsense-reloaded') },
      { value: 'taboola', label: __('Taboola', 'quick-adsense-reloaded') },
      { value: 'media_net', label: __('Media.net', 'quick-adsense-reloaded') },
      { value: 'mediavine', label: __('Mediavine', 'quick-adsense-reloaded') },
      { value: 'outbrain', label: __('Outbrain', 'quick-adsense-reloaded') },
      { value: 'infolinks', label: __('Infolinks', 'quick-adsense-reloaded') },
      { value: 'background_ad', label: __('Background Ad', 'quick-adsense-reloaded') },
      { value: 'group_insertion', label: __('Group Insertion', 'quick-adsense-reloaded') },
      { value: 'skip_ads', label: __('Skip Ads', 'quick-adsense-reloaded') },
      { value: 'loop_ads', label: __('Loop Ads', 'quick-adsense-reloaded') },
      { value: 'carousel_ads', label: __('Carousel Ads', 'quick-adsense-reloaded') },
      { value: 'parallax_ads', label: __('Parallax Ads', 'quick-adsense-reloaded') },
      { value: 'half_page_ads', label: __('Half Page Ads', 'quick-adsense-reloaded') },
      { value: 'sticky_scroll', label: __('Sticky Scroll', 'quick-adsense-reloaded') },
      { value: 'floating_cubes', label: __('Floating Cubes', 'quick-adsense-reloaded') }
    ];
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
{(page.path === undefined || page.path === 'adsell')?  <div className='quads-select-div quads-mui-select'>
    <FormControl style={{width:'100%',paddingRight:'8px'}}>
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

      <FormControl style={{width:'100%',paddingRight:'8px'}}>
        <InputLabel id="quads_sort_lid">{__('Sort By', 'quick-adsense-reloaded')}</InputLabel>
        <Select
          labelId="quads_sort_lid"
          onChange={this.props.handleSortBy}
        >
          <MenuItem value={'impression'}>{__('Sort by Impression', 'quick-adsense-reloaded')}</MenuItem>
          <MenuItem value={'click'}>{__('Sort by Clicks', 'quick-adsense-reloaded')}</MenuItem>
        </Select>
      </FormControl>
      {(page.path === undefined ) &&
      <FormControl style={{width:'100%'}}>
        <InputLabel id="quads_filter_lid">{__('Filter By', 'quick-adsense-reloaded')}</InputLabel>
        <Select
          labelId="quads_filter_lid"
          onChange={this.props.handleFilterBy}
        >
        {adOptions
            .filter(option => this.state.ad_types.includes(option.value)) 
            .sort((a, b) => a.label.localeCompare(b.label))
            .map(option => (
              <MenuItem key={option.value} value={option.value}>
                {option.label}
              </MenuItem>
            ))
          }
        </Select>
      </FormControl>
      }
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