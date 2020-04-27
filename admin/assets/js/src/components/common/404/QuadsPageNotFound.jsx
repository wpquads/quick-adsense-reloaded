import React, { Component, Fragment } from 'react';


import './QuadsPageNotFound.scss';

class QuadsPageNotFound extends Component {
  
  constructor(props) {
    super(props);    
    this.state = {               
    };       
  } 

  render() {
          const {__} = wp.i18n; 
          return (
            __('Page not found. Redirect to home', 'quick-adsense-reloaded')   
            );
  }
}

export default QuadsPageNotFound;