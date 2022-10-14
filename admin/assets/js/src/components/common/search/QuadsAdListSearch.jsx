import React, { Component, Fragment } from 'react';
import ReactDOM from "react-dom";
import queryString from 'query-string'

import './QuadsAdListSearch.scss';

class QuadsAdListSearch extends Component {
    
  constructor(props) {  
      
    super(props);
    this.state = {      
      posts_found:0   
    };       
  }
   
  static getDerivedStateFromProps(props, state) {   
    return {
        posts_found : props.ad_list.posts_found
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
   }}
      onChange={this.props.triggerSearch} value={page.ad_id} placeholder={__('Search by ad unit, id, format, etc', 'quick-adsense-reloaded')} type="text"/>
:


<input
     style = {{ backgroundImage: `url(${searchIcon})`,          
     backgroundRepeat: 'no-repeat',
     visibility:(this.state.posts_found>0)?'visible':'hidden'
   }}
      onChange={this.props.triggerSearch} placeholder={__('Search by ad unit, id, format, etc', 'quick-adsense-reloaded')} type="text"/>
  }


     </div>
    );

  }
  
}

export default QuadsAdListSearch;