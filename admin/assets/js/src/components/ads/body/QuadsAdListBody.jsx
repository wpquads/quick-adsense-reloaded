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
      more_hover_box_id       : null,
      more_box_index    : null,
      more_box_hover_index    : null,
      edit_hover_id    : null,
      EditHoverIn_index    : null,
      adlog_hover_id    : null,
      AdlogIn_index    : null,
      adimp_count_hover_id    : null,
      AdImp_Count_index    : null,
      static_box_id       : null,
      static_box_index    : null,
      delete_modal      : false,
      delete_modal_id   : null,
      display_pagination:false,
      analytics_impressions:0,
      analytics_clicks:0, 
      analytics_loader:false,
      bulk_ads_ids : [],
      bulk_ads_index : [],
      ads_sort_by : '',
      ads_filter_by: ''          
    };                   
  }
  handleBulkCheckbox = (e) => {
    const { value, checked } = e.target;
    if (checked) {
      let arr = this.state.bulk_ads_ids;
      let arr2 =  this.state.bulk_ads_index;

      arr = arr.concat(value);
      arr2 = arr2.concat(e.target.dataset.index);
      this.setState({bulk_ads_ids:arr , bulk_ads_index:arr2});
      this.handleCheckBoxUI(arr);
      if(e.target.parentElement.parentElement.parentElement){
        e.target.parentElement.parentElement.parentElement.classList.add('quads-checked');
      }
      
    }else{
      this.state.bulk_ads_ids = this.state.bulk_ads_ids.filter((ele)=> ele !==value);
      this.state.bulk_ads_index = this.state.bulk_ads_index.filter((ele)=> ele !== e.target.dataset.index);
      this.handleCheckBoxUI(this.state.bulk_ads_ids);
      if(e.target.parentElement.parentElement.parentElement){
        e.target.parentElement.parentElement.parentElement.classList.remove('quads-checked');
      }
    }
  }

  handleCheckBoxUI = (arr) => {
    let master_check = document.querySelector('#quads_master_checkbox');
        let all_checks = document.querySelectorAll('.quads_checkbox_adlist');
        let total_cnt = document.querySelector('#quads_selected_total_cnt');
      if(arr.length){
        total_cnt.innerHTML = '('+arr.length+')';
        if(master_check && all_checks){
          if(all_checks.length>arr.length){
            master_check.classList.add("partial-checked");
            master_check.checked=false;
          }else if(all_checks.length==arr.length){
            master_check.checked=true;
            master_check.classList.remove("partial-checked");
          }
          else{
            master_check.checked= false;
            master_check.classList.remove("partial-checked");
          }
        }
      }else{
        total_cnt.innerHTML = '';
        master_check.checked= false;
        master_check.classList.remove("partial-checked");
      }
  }
  handleMasterCheckbox = (e) =>{
    const { checked } = e.target;
    const checkboxes = document.querySelectorAll('.quads_checkbox_adlist');
    let total_cnt = document.querySelector('#quads_selected_total_cnt');
    if (checked) {

      
      let arr = [];
      let arr2 = [];

      checkboxes.forEach((checkbox, index) => {
        checkbox.checked = true;
          checkbox.parentElement.parentElement.parentElement.classList.add('quads-checked');
          arr.push(checkbox.value);
          arr2.push(index);
    });

    total_cnt.innerHTML = '('+checkboxes.length+')';
      this.setState({bulk_ads_ids:arr , bulk_ads_index:arr2});
      
    }else{
      checkboxes.forEach((checkbox) => {
          checkbox.checked = false;
          checkbox.parentElement.parentElement.parentElement.classList.remove('quads-checked');
    });
      this.state.bulk_ads_ids = [];
      this.state.bulk_ads_index = [];
      total_cnt.innerHTML = '';
    }
    
    e.target.classList.remove('partial-checked');
  }
  handleBulkActions = (e) => {
    const { value } = e.target;
    const {__} = wp.i18n; 
  
    if(!this.state.bulk_ads_ids.length && value){
      e.target.value = '';
      alert(__('Please  select at least one Ad', 'quick-adsense-reloaded'));
      return;
    }
  
    if(value == 'delete'){
      this.setState({delete_modal:true, more_box_id:this.bulk_ads_index, delete_modal_id:this.state.bulk_ads_ids});
    }
    else{
      this.processStatus(value);
    }
  }
 renderCheckbox = ( ) => {
      let adlist_checkboxes = document.querySelectorAll('.quads_checkbox_adlist');
      if(adlist_checkboxes){
      for(let i=0;i<adlist_checkboxes.length;i++){
        if(adlist_checkboxes[i].checked){
          adlist_checkboxes[i].checked = false;
        }
      }
    }
    let bulk_select = document.querySelector('.quads_bulk_actions');
    if(bulk_select){
      bulk_select.value="";
    } 
  }

  handleSortBy = (e) => {
    const { value } = e.target;
      this.setState({ads_sort_by:value});
      this.mainSearchMethod(this.state.search_text, this.state.page, value , this.state.ads_filter_by); 
  }
  handleFilterBy = (e) => {
    const { value } = e.target;
    this.setState({ads_filter_by:value});
    this.mainSearchMethod(this.state.search_text, this.state.page, this.state.ads_sort_by ,value); 
  }
  showDeleteModal =(e) => {
    const ad_id = e.currentTarget.dataset.ad;
    this.setState({delete_modal:true, more_box_id:null, delete_modal_id:ad_id});
  }
  hideDeleteModal =(e) => {    
    this.setState({delete_modal:false, delete_modal_id:null});
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
  
  showMoreHoverIn = (e) => {
    e.preventDefault();        
    const id = e.currentTarget.dataset.id;  
    const index = e.currentTarget.dataset.index;        
    if(this.state.more_box_hover_index != index || this.state.more_hover_box_id == null)  
     this.setState({more_hover_box_id:id, more_box_hover_index:index});
    else   
     this.setState({more_hover_box_id:null});
  }
  showMoreHoverOut = (e) => {
    e.preventDefault();        
     this.setState({more_hover_box_id:null});
  }
  
  EditHoverIn = (e) => {
    e.preventDefault();        
    const id = e.currentTarget.dataset.id;  
    const index = e.currentTarget.dataset.index;        
    if(this.state.EditHoverIn_index != index || this.state.edit_hover_id == null)  
     this.setState({edit_hover_id:id, EditHoverIn_index:index});
    else   
     this.setState({edit_hover_id:null});
  }
  EditHoverOut = (e) => {
    e.preventDefault();        
     this.setState({edit_hover_id:null});
  }
  
  AdLogHoverIn = (e) => {
    e.preventDefault();        
    const id = e.currentTarget.dataset.id;  
    const index = e.currentTarget.dataset.index;        
    if(this.state.AdlogIn_index != index || this.state.adlog_hover_id == null)  
     this.setState({adlog_hover_id:id, AdlogIn_index:index});
    else   
     this.setState({adlog_hover_id:null});
  }
  AdLogHoverOut = (e) => {
    e.preventDefault();        
     this.setState({adlog_hover_id:null});
  }
  
  AdImp_Count_HoverIn = (e) => {
    e.preventDefault();        
    const id = e.currentTarget.dataset.id;  
    const index = e.currentTarget.dataset.index;        
    if(this.state.AdImp_Count_index != index || this.state.adimp_count_hover_id == null)  
     this.setState({adimp_count_hover_id:id, AdImp_Count_index:index});
    else   
     this.setState({adimp_count_hover_id:null});
  }
  AdImp_Count_HoverOut = (e) => {
    e.preventDefault();        
     this.setState({adimp_count_hover_id:null});
  }
  
  showStaticIconBox = (e) => {
    e.preventDefault();        
    const id = e.currentTarget.dataset.id;  
    const index = e.currentTarget.dataset.index;        
    if(this.state.static_box_index != index || this.state.static_box_id == null)  
     {
      this.setState({analytics_loader:true,analytics_impressions:0, analytics_clicks:0});
      this.getAdAnalytics(id);
      this.setState({static_box_id:id, static_box_index:index});
    }
    else
    {   
     this.setState({static_box_id:null});
    }
  }
  hideMoreIconBox = (e) => {
    if(e){e.preventDefault();}
    this.setState({more_box_id:null});
  }
  hideStaticIconBox = (e) => {
    e.preventDefault();
    this.setState({static_box_id:null});
  }

  processStatus = (status) => {
    this.setState({actionPerform:true}); 
    const action = status;
    const ad_id  = this.state.bulk_ads_ids;
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
            let items = this.state.items;
            let row_indexes = this.state.bulk_ads_index;
            if(Array.isArray(row_indexes)){
              for (let i = 0;i< row_indexes.length;i++){
                let item = { ...items[row_indexes[i]] };
                item.post.post_status = action;
                items[row_indexes[i]] = item;
              }
            }
            this.setState({ items: items, more_box_id:null,bulk_ads_ids:[],bulk_ads_index:[]});
            this.renderCheckbox();
        }
      },        
      (error) => {
        
      }
    );  

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
              location.reload();         
            } else if(action == 'delete'){
              let row_indexes = this.state.bulk_ads_index;
              const sortedNumbers = row_indexes.slice().sort((a, b) => b - a);
              if(sortedNumbers.length){
                for (let i = 0;i< sortedNumbers.length;i++){
                    items.splice(sortedNumbers[i], 1);
                }
              }else{
                items.splice(this.state.more_box_index,1);
              }              
             
            } else {
              item.post.post_status = action;
              items[this.state.more_box_index] = item;
            }
            this.setState({ items: items, more_box_id:null, delete_modal:false,bulk_ads_ids:[],bulk_ads_index:[]});
            this.renderCheckbox();
        }
      },        
      (error) => {
        
      }
    );  

  }
  mainSearchMethod = (search_text, page , sort_by = '',filter_by = '') => { 
      let filter_not_by = '';
      if(this.props.ad_type && this.props.ad_type==='ads_space') {
        filter_by = 'ads_space';
      }else if(this.props.ad_type && this.props.ad_type==='ads') {
        filter_not_by = 'ads_space';
      }
      this.setState({isLoaded:false})
      let get_eppp = quads_localize_data.num_of_ads_to_display
      let url = quads_localize_data.rest_url + "quads-route/get-ads-list?search_param="+search_text+"&posts_per_page="+get_eppp+"&pageno="+page+"&sort_by="+sort_by+"&filter_by="+filter_by+"&filter_not_by="+filter_not_by;
      if(quads_localize_data.rest_url.includes('?')){
         url = quads_localize_data.rest_url + "quads-route/get-ads-list&search_param="+search_text+"&posts_per_page="+get_eppp+"&pageno="+page+"&sort_by="+sort_by+"&filter_by="+filter_by+"&filter_not_by="+filter_not_by; 
      }
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': quads_localize_data.nonce,
          "Content-Type": "application/json"
        }
      })
      .then(res => res.json())
      .then(
        (result) => {
          let state_vars={       
            isLoaded: true,
            items: result.posts_data,
            posts_found: result?.posts_found,
          };
          if(result?.posts_found>20)
          {
            state_vars.display_pagination=true;
          }              
          this.setState(state_vars);
        },        
        (error) => {       
          this.setState({
            isLoaded: true,
           
          });
        }
      );            
  } 
  
  getAdAnalytics = (ad_id) => { 
    let url = quads_localize_data.rest_url + "quads-route/get-ads-analytics?ad_id="+ad_id;
    if(quads_localize_data.rest_url.includes('?')){
       url = quads_localize_data.rest_url + "quads-route/get-ads-analytics&ad_id="+ad_id;  
    }

    fetch(url, {
      headers: {                    
        'X-WP-Nonce': quads_localize_data.nonce,
        "Content-Type": "application/json"
      }
    })
    .then(res => res.json())
    .then(
      (result) => {
        let state_vars={      
          analytics_impressions: result.impressions, 
          analytics_clicks: result.clicks,
          analytics_loader:false
        };
                   
        this.setState(state_vars);
      },        
      (error) => {   
        console.log(error); 
        this.setState({analytics_loader:false});   
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
              <h3>{__('Are you sure you want to', 'quick-adsense-reloaded')}<span> {__(' DELETE  ', 'quick-adsense-reloaded')} </span>{Array.isArray(this.state.delete_modal_id)?__( 'selected ads?', 'quick-adsense-reloaded'):__( 'this ad?', 'quick-adsense-reloaded')}</h3> 
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
              {(this.state.items && this.state.items.length>0) &&        
              <div className="quads-search-box-panel">                
                <div className="quads-search-box">
                  <QuadsAdListSearch ad_list={this.state} triggerSearch={this.QuadsSearchAd} handleSortBy={this.handleSortBy} handleFilterBy={this.handleFilterBy}  handleBulkActions={this.handleBulkActions}/>
                </div>                
              </div>  
              }
             
              <div className="quads-list-ads">
                <QuadsAdList
                  {...this.state}
                  ad_type = {this.props.ad_type}
                  ad_list={this.state}
                  showMoreIconBox ={this.showMoreIconBox}
                  showMoreHoverIn ={this.showMoreHoverIn}
                  showMoreHoverOut ={this.showMoreHoverOut}
                  EditHoverIn ={this.EditHoverIn}
                  EditHoverOut ={this.EditHoverOut}
                  AdLogHoverIn ={this.AdLogHoverIn}
                  AdLogHoverOut ={this.AdLogHoverOut}
                  AdImp_Count_HoverIn ={this.AdImp_Count_HoverIn}
                  AdImp_Count_HoverOut ={this.AdImp_Count_HoverOut}
                  showStaticIconBox = {this.showStaticIconBox}
                  hideMoreIconBox ={this.hideMoreIconBox}
                  hideStaticIconBox = {this.hideStaticIconBox}
                  processAction   ={this.processAction}
                  showDeleteModal ={this.showDeleteModal}
                  nodatashowAddTypeSelector ={this.props.nodatashowAddTypeSelector}
                  handleBulkCheckbox = {this.handleBulkCheckbox}
                  settings = {this.props.settings}
                  handleMasterCheckbox={this.handleMasterCheckbox}
                />
              </div>            
              <div className="quads-list-pagination" style={{visibility:this.state.display_pagination?'visible':'hidden'}} >
                <QuadsAdListPagination ad_list={this.state} triggerPagination={this.QuadsPaginateAd} />
              </div>
              </div>                        
            </Fragment>  
            );
  }
}

export default QuadsAdListBody;