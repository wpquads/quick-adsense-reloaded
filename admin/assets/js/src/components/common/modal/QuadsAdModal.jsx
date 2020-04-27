import React, { Component, Fragment } from 'react';
import ReactDOM from "react-dom";

import './QuadsAdModal.scss';

class QuadsAdModal extends Component {
    
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
      <div className="quads-modal-popup">            
        <div className="quads-modal-popup-content">
          <span className="quads-modal-close" onClick={this.props.closeModal}>&times;</span>
          <h3>{this.state.title}</h3>
          <div className="quads-modal-description">{this.state.description}</div>
          <div className="quads-modal-content">{this.state.content}</div>
          <div className="quads-modal-error">{this.state.error}</div>      
        </div>        
      </div> : ''
    );
  }
  
}

export default QuadsAdModal;