import React, { Component, Fragment } from 'react';
import './QuadsAdConfigFields.scss';
import QuadsAdModal from '../../common/modal/QuadsAdModal';
import Icon from '@material-ui/core/Icon';
import Select from "react-select";

class QuadsAdConfigFields extends Component {
  constructor(props) {
    super(props);    
    this.state = { 
    adsToggle : false,    
    random_ads_list:[],  
    getallads_data: [],
    currentselectedvalue: "",
    currentselectedlabel : "",              
    };       
  }   
  adsToggle = () => {
  
  this.setState({adsToggle:!this.state.adsToggle,currentselectedvalue : ''});
}
addIncluded = (e) => {

    e.preventDefault();  

    let type  = this.state.multiTypeLeftIncludedValue;
    let value = this.state.multiTypeRightIncludedValue;
  
    if( typeof (value.value) !== 'undefined'){
      const {random_ads_list} = this.state;
      let data    = random_ads_list;
      data.push({type: type, value: value});
      let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);          
      this.setState({random_ads_list: newData});       
    }        
  
}
  static getDerivedStateFromProps(props, state) {    

    if(!state.adsToggle){
      return {
        random_ads_list: props.parentState.quads_post_meta.random_ads_list, 
      };
    }else{
      return null;
    }
    
  }
    componentDidUpdate (){
    
    const random_ads_list = this.state.random_ads_list; 
    if(random_ads_list &&random_ads_list.length > 0 ){
      this.props.updateRandomAds(random_ads_list);
    }
    
  }
removeSeleted = (e) => {
      let index = e.currentTarget.dataset.index;  
      const { random_ads_list } = { ...this.state };    
      random_ads_list.splice(index,1);
      this.setState(random_ads_list);

}
  getallads = (search_text = '',page = '') => {
   let url = quads_localize_data.rest_url + "quads-route/get-ads-list?posts_per_page=100&page="+page;
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': quads_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {      
          let getallads_data =[];
          Object.entries(result.posts_data).map(([key, value]) => {
          if(value.post_meta['ad_type'] != "random_ads" && value.post['post_status'] != "draft")
            getallads_data.push({label: value.post['post_title'], value: value.post['post_id']});
          })      
            this.setState({
            isLoaded: true,
            getallads_data: getallads_data,
          });
          
        },        
        (error) => {
          this.setState({
             isLoaded: true,         
          });
        }
      );          
  }

  addselected = (e) => {

    e.preventDefault();  

    let value  = this.state.currentselectedvalue;  
    let label  = this.state.currentselectedlabel;  
  
    if( typeof (value) !== 'undefined' && value != ''){
      const {random_ads_list} = this.state;
      let data    = random_ads_list;
      data.push({ value: value,label: label});
      let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);          
      this.setState({random_ads_list: newData,adsToggle : false});    
         
    }        
  
}
   componentDidMount() {  
          this.getallads(); 
  } 
    selectAdchange = (option) => {    
   
      this.setState({currentselectedlabel: option.label,currentselectedvalue: option.value});

  }
  render() {     

          const {__} = wp.i18n;
          const post_meta = this.props.parentState.quads_post_meta;
          const show_form_error = this.props.parentState.show_form_error;
          const comp_html = [];   
          let ad_type_name = '';     

          switch (this.props.ad_type) {

            case 'adsense':
             ad_type_name = 'AdSense';  
              comp_html.push(<div key="adsense">
                <table>
                  <tbody>
                    <tr><td><label>{__('Data Client ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.g_data_ad_client == '') ? 'quads_form_error' : ''} value={post_meta.g_data_ad_client} onChange={this.props.adFormChangeHandler} type="text" id="g_data_ad_client" name="g_data_ad_client" />
                    {(show_form_error && post_meta.g_data_ad_client == '') ? <div className="quads_form_msg"><span className="material-icons">
error_outline</span>Enter Data Client ID</div> :''} </td></tr>
                    <tr><td><label>{__('Data Slot ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.g_data_ad_slot == '') ? 'quads_form_error' : ''}  value={post_meta.g_data_ad_slot} onChange={this.props.adFormChangeHandler} type="text" id="g_data_ad_slot" name="g_data_ad_slot" />
                    {(show_form_error && post_meta.g_data_ad_slot == '') ? <div className="quads_form_msg"><span className="material-icons">
error_outline
</span>Enter Data Slot ID</div> :''}</td></tr>
                    <tr><td><label>{__('Size', 'quick-adsense-reloaded')}</label></td><td>
                      <div>
                        <select value={post_meta.adsense_type} onChange={this.props.adFormChangeHandler} name="adsense_type" id="adsense_type">
                        <option value="normal">{__('Fixed Size', 'quick-adsense-reloaded')}</option>
                        <option value="responsive">{__('Responsive', 'quick-adsense-reloaded')}</option> 
                      </select>
                      {
                        post_meta.adsense_type !== 'responsive' ?                        
                      <div className="quads-adsense-width-heigth">
                        
                        <div className="quads-adsense-width">
                          <label>{__('Width', 'quick-adsense-reloaded')}
                          <input value={post_meta.g_data_ad_width ? post_meta.g_data_ad_width:'300'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_width" name="g_data_ad_width" /> 
                          </label>
                        </div>
                        <div className="quads-adsense-height">
                          <label>{__('Height', 'quick-adsense-reloaded')}
                          <input value={post_meta.g_data_ad_height  ? post_meta.g_data_ad_height:'250'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_height" name="g_data_ad_height" />  
                          </label>
                        </div>
                      </div>
                      : ''
                      }
                      </div>
                      </td></tr>
                  </tbody>
                </table>
                </div>);

              break;
          
              case 'plain_text':                
                ad_type_name = 'Plain Text / HTML / JS';
                comp_html.push(<div key="plain_text">
                  <table><tbody>
                  <tr>
                  <td><label>{__('Plain Text / HTML / JS', 'quick-adsense-reloaded')}</label></td> 
                  <td><textarea className={(show_form_error && post_meta.code == '') ? 'quads_form_error' : ''}  cols="50" rows="5" value={post_meta.code} onChange={this.props.adFormChangeHandler} id="code" name="code" />
                  {(show_form_error && post_meta.code == '') ? <div className="quads_form_msg"><span className="material-icons">error_outline</span>Enter Plain Text / HTML / JS</div> : ''}</td>
                  </tr>
                  </tbody></table>
                  </div>);      
              break; 
               case 'random_ads':                
                 ad_type_name = 'Random Ads';
                comp_html.push(       <div className="quads-user-targeting"> 
       <h2>Select Ads<a onClick={this.adsToggle}><Icon>add_circle</Icon></a>  </h2>

                
             <div className="quads-target-item-list">
              {                
              this.state.random_ads_list ? 
              this.state.random_ads_list.map( (item, index) => (
                <div key={index} className="quads-target-item">
                  <span className="quads-target-label">{item.label}</span>
                  <span className="quads-target-icon" onClick={this.removeSeleted} data-index={index}><Icon>close</Icon></span> 
                </div>
               ) )
              :''}
              <div>{ (this.state.random_ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>Select at least one Ad</div></span> : ''}</div>
             </div>             
        

        {this.state.adsToggle ?
        <div className="quads-targeting-selection">
        <table className="form-table">
         <tbody>
           <tr>             
           <td>
            <Select              
              name="userTargetingIncludedType"
              placeholder="Select Ads"              
              options= {this.state.getallads_data}
              value  = {this.multiTypeLeftIncludedValue}
              onChange={this.selectAdchange}                                                 
            />             
           </td>
           <td><a onClick={this.addselected} className="quads-btn quads-btn-primary">Add</a></td>
           </tr>
         </tbody> 
        </table>
        </div>
        : ''}
       </div>);      
              break; 

            default:
              comp_html.push(<div>{__('Ad not found', 'quick-adsense-reloaded')}</div>);
              break;
          }
              return(
                <div>{ad_type_name} {__('Ad Configuration', 'quick-adsense-reloaded')}
                {this.props.ad_type == 'adsense' ? 
                <div className="quads-autofill-div"><a className="quads-autofill" onClick={this.props.openModal}>{__('Autofill', 'quick-adsense-reloaded')}</a>
                <QuadsAdModal 
                 closeModal    = {this.props.closeModal}
                 parentState={this.props.parentState} 
                 title={__('Enter AdSense text and display ad code here', 'quick-adsense-reloaded')}
                 description={__('Do not enter AdSense page level ads or Auto ads! Learn how to create AdSense ad code', 'quick-adsense-reloaded')}  
                  content={
                    <div>
                      <div><textarea className="quads-auto-fill-textarea" cols="80" rows="15" onChange={this.props.modalValue} value={this.props.quads_modal_value}/></div>
                      <div><a className="button" onClick={this.props.getAdsenseCode}>{__('Get Code', 'quick-adsense-reloaded')}</a></div>
                    </div>
                  }/>
                </div> : ''}
                <div className="quads-panel">
                 <div className="quads-panel-body">{comp_html}</div>
              </div>
              </div>
              );
  }
}

export default QuadsAdConfigFields;