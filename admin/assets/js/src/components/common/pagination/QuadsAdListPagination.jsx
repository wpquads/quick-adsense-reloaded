import React, { Component, Fragment } from 'react';
import ReactDOM from "react-dom";

import './QuadsAdListPagination.scss';

class QuadsAdListPagination extends Component {
    
  constructor(props) {      
    super(props);
    this.state = {         
        ad_count   :0,
        page_count :0
    };     
  }
  
  static getDerivedStateFromProps(props, state) { 
        
        let page_count = 0;        
        if(props.ad_list.posts_found > 20){
            
            page_count = Math.ceil(props.ad_list.posts_found / 20);
        }
              
        return {
            page_count:page_count,
            ad_count : props.ad_list.posts_found
        };
  }

  render() {
    
    if(this.state.ad_count > 20){   
        
        const paginate = [];

        for(var i=1; i <= this.state.page_count; i++){            
         paginate.push(<a className={this.props.ad_list.clicked_btn_id == i ? 'quads-page-active' : ''} onClick={this.props.triggerPagination} key={i} data-index={i} data-id={i} href="#">{i}</a>);                 
        }            
                
        return (
        <div className="quads-ads-pagination">
            <a className={this.props.ad_list.clicked_btn_id == 0 ? 'quads-page-active material-icons' : 'material-icons'} onClick={this.props.triggerPagination} key={0} data-index={0} data-id="1" href="#">keyboard_arrow_left</a>
            {paginate}
            <a className={(this.props.ad_list.clicked_btn_id == (this.state.page_count+1)) ? 'quads-page-active material-icons' : 'material-icons'} onClick={this.props.triggerPagination} data-index={(this.state.page_count+1)} key={(this.state.page_count+1)} data-id={this.state.page_count} href="#">keyboard_arrow_right</a>
        </div>
        
    );
            
    }else{
       
        return '';    
        
    }    
    
  }
  
}

export default QuadsAdListPagination;