import React, { Component, Fragment } from 'react';
import ReactDOM from "react-dom";

import './QuadsLargeAdModal.scss';

class QuadsLargeAdModal extends Component {
    
  constructor(props) {  
      
    super(props);
    this.state = { 
      open       : false,
      title      :'Demo Modal',
      description:'This is the modal description',        
      content    :'This is the content of a modal',
      error      :'',                    
    };       
  }
  static getDerivedStateFromProps(props, state) {
    return {
      title: props.title, 
      description: props.description, 
      content: props.content, 
      open:props.parentState.quads_modal_open,
      error:props.parentState.quads_modal_error
    };
  }  
  render() {      
    return (   
      this.state.open ?     
      <div className="quads-large-popup">            
        <div className="quads-large-popup-content">
          <span className="quads-large-close" onClick={this.props.closeModal}>&times;</span>
          <div className="quads-large-popup-title">
            <h1>{this.state.title}</h1>
          </div>
          <div className="quads-large-description">{this.state.description}</div>
          <div className="quads-large-content">{this.state.content}</div>
          <div className="quads-large-error">{this.state.error}</div>      
        </div>        
      </div> : ''
    );
  }
  
}

export default QuadsLargeAdModal;