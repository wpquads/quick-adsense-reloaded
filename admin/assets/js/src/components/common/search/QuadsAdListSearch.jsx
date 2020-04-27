import React, { Component, Fragment } from 'react';
import ReactDOM from "react-dom";

import './QuadsAdListSearch.scss';

class QuadsAdListSearch extends Component {
    
  constructor(props) {  
      
    super(props);
    this.state = {         
    };       
  }
  
  render() {

    const {__} = wp.i18n;      
    let searchIcon = quads_localize_data.quads_plugin_url+'admin/assets/img/quads-search.png'; 
    return (
     <div className="quads-ad-search-box">
     <input
     style = {{ backgroundImage: `url(${searchIcon})`,          
     backgroundRepeat: 'no-repeat',
   }}
      onChange={this.props.triggerSearch} placeholder={__('Search by ad unit, id, format, etc', 'quick-adsense-reloaded')} type="text"/>
     </div>
    );

  }
  
}

export default QuadsAdListSearch;