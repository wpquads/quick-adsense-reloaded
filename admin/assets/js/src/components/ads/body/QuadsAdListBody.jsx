import React, { Component, Fragment } from 'react';
import QuadsAdListSearch from "../../common/search/QuadsAdListSearch";
import QuadsAdList from "../ad-list/QuadsAdList";
import QuadsAdListPagination from "../../common/pagination/QuadsAdListPagination";
import Icon from '@material-ui/core/Icon';

import './QuadsAdListBody.scss';

class QuadsAdListBody extends Component {
    
  constructor(props) {
    super(props);    
    this.state = {
      ad_type_toggle    : false,
      error             : null,
      actionPerform     : false,
      isLoaded          : false,
      search_text       : '',
      clicked_btn_id    : 1,
      page              : 1,
      timeout           : 0,
      items             : [],
      posts_found       : 0,
      more_box_id       : null,
      more_box_index    : null,
      delete_modal      : false,
      delete_modal_id   : null,            
    };                   
  }
  showDeleteModal =(e) => {
    const ad_id = e.currentTarget.dataset.ad;
    this.setState({delete_modal:true, more_box_id:null, delete_modal_id:ad_id});
  }
  hideDeleteModal =(e) => {    
    this.setState({delete_modal:false});
  }  
  showMoreIconBox = (e) => {
    e.preventDefault();        
    const id = e.currentTarget.dataset.id;  
    const index = e.currentTarget.dataset.index;        
    if(this.state.more_box_index != index || this.state.more_box_id == null)  
     this.setState({more_box_id:id, more_box_index:index});
    else   
     this.setState({more_box_id:null});
  }
  hideMoreIconBox = (e) => {
    e.preventDefault();
    this.setState({more_box_id:null});
  }
  processAction = (e) => {

    e.preventDefault();   
    this.setState({actionPerform:true}); 
    const action = e.currentTarget.dataset.id;
    const ad_id  = e.currentTarget.dataset.ad;
    const json_data = {
      ad_id : ad_id,
      action: action,
    }        
    const url = quads_localize_data.rest_url + "quads-route/ad-more-action";
    
    fetch(url, {
      method: "post",
      headers: {    
        'Accept': 'application/json',
        'Content-Type': 'application/json',                
        'X-WP-Nonce': quads_localize_data.nonce,
      },
      body: JSON.stringify(json_data)
    })
    .then(res => res.json())
    .then(
      (result) => {   
        this.setState({actionPerform:false});
        if(result.status){
            
            let items = [...this.state.items];
            let item = { ...items[this.state.more_box_index] };
                                
            if(action == 'duplicate'){              
              item.post.post_id = result.data.post.ID;
              item.post_meta.ad_id = result.data.post.ID;
              items.splice(this.state.more_box_index, 0, item);              
            } else if(action == 'delete'){              
              items.splice(this.state.more_box_index,1);
            } else {
              item.post.post_status = action;
              items[this.state.more_box_index] = item;
            }
            this.setState({ items: items, more_box_id:null, delete_modal:false});
        }
      },        
      (error) => {
        
      }
    );  

  }

  mainSearchMethod = (search_text, page) => { 
      this.setState({isLoaded:false})
      let url = quads_localize_data.rest_url + "quads-route/get-ads-list?search_param="+search_text+"&posts_per_page=20&pageno="+page;
      if(quads_localize_data.rest_url.includes('?')){
         url = quads_localize_data.rest_url + "quads-route/get-ads-list&search_param="+search_text+"&posts_per_page=20&pageno="+page;  
      }
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': quads_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {                
          this.setState({
            isLoaded: true,
            items: result.posts_data,
            posts_found: result.posts_found
          });
        },        
        (error) => {
          this.setState({
            isLoaded: true,
           
          });
        }
      );            
  }  

  componentDidMount() {    
          this.mainSearchMethod(this.state.search_text, this.state.page); 
  }  
  timer = null;
  QuadsSearchAd =(e) => {                         
          
          let search_val = e.target.value;

          this.setState({
            search_text: search_val,     
          });

          clearTimeout(this.timer);

          this.timer = setTimeout(() => {
            this.mainSearchMethod(search_val, this.state.page);
          }, 300);

  }
  QuadsPaginateAd =(e) => { 
    e.preventDefault();                     
    this.mainSearchMethod(this.state.search_text, e.currentTarget.dataset.id);
    this.setState({
       page: e.currentTarget.dataset.id,
       clicked_btn_id: e.currentTarget.dataset.index
     });
}  
        
  render() {
    
          const {__} = wp.i18n; 
          return (
            <Fragment>  
              <div>    
              {this.state.actionPerform ? <div className="quads-cover-spin"></div> : ''}
              <div className="quads-hidden-elements">                            
              {this.state.delete_modal ? 
              <div className="quads-modal-popup">            
            <div className="quads-modal-popup-content">   
              <div className="quads-modal-popup-txt">          
              <h3>{__('Are you sure you want to', 'quick-adsense-reloaded')}<span> {__(' DELETE  ', 'quick-adsense-reloaded')} </span>{__( 'this ad?', 'quick-adsense-reloaded')}</h3> 
              <p>{__('It will permenently removed and you won\'t be able to see the ad again. You cannot undo this action.', 'quick-adsense-reloaded')}</p>
              </div>           
             <div className="quads-modal-content">
             <a className="quads-btn quads-btn-cancel" onClick={this.hideDeleteModal}>{__('Cancel', 'quick-adsense-reloaded')}</a>
              <a data-id="delete" data-ad={this.state.delete_modal_id} className="quads-btn quads-btn-delete" onClick={this.processAction}>{__('Delete', 'quick-adsense-reloaded')}</a>
             </div>             
             </div>        
            </div>
              : ''}  
              </div>         
              <div className="quads-search-box-panel">                
                <div className="quads-search-box"><QuadsAdListSearch triggerSearch={this.QuadsSearchAd} /></div>                
              </div>              
              <div className="quads-list-ads">
                <QuadsAdList 
                  {...this.state}
                  ad_list={this.state}
                  showMoreIconBox ={this.showMoreIconBox}
                  hideMoreIconBox ={this.hideMoreIconBox}
                  processAction   ={this.processAction}
                  showDeleteModal ={this.showDeleteModal}
                  nodatashowAddTypeSelector ={this.props.nodatashowAddTypeSelector}
                />
              </div>            
              <div className="quads-list-pagination">
                <QuadsAdListPagination ad_list={this.state} triggerPagination={this.QuadsPaginateAd} />
              </div>
              </div>                        
            </Fragment>  
            );
  }
}

export default QuadsAdListBody;