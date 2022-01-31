import React, { Component, Fragment } from 'react';
import {Link} from 'react-router-dom';
import queryString from 'query-string'
import './AdTypeSelectorNavLink.scss';

class AdTypeSelectorNavLinkR extends Component {

    constructor(props) {
        super(props);    
        this.state = {
          redirect:false, 
          rotator_ads_status:true,
            group_insertion_status :true,
          popular_ad_network : [
                    {ad_type:'adsense',ad_type_name:'AdSense'},
                    {ad_type:'plain_text',ad_type_name:'Plain Text / HTML / JS'},               
                    ],
       };       
      } 
      componentDidMount(){ 
        this.getSettings_data();
    }
      getSettings_data = () => {
    let url = quads_localize_data.rest_url + 'quads-route/get-settings';
    fetch(url,{
      headers: {                    
        'X-WP-Nonce': quads_localize_data.nonce,
      }
    })
    .then(res => res.json())
    .then(
      (result) => {                  
      
              Object.entries(result).map(([meta_key, meta_val]) => {      
              if(meta_key =="rotator_ads_settings"){
              this.setState({rotator_ads_status: meta_val});
              }else if(meta_key =="group_insertion_settings"){
                  this.setState({group_insertion_status: meta_val});
              }
              })
      },        
      (error) => {
      }
    );  
  }
  render() {    

    const {__} = wp.i18n;    
    const page = queryString.parse(window.location.search);     
    return(  

                    <div className="quads-ad-networks">                                                                                
                    <ul>
                    {this.props.All_ad_network.map((item, index ) => 
                    <li key={item.ad_type}  style={(item.ad_type ==  'blindness_settings' && !this.state.blindness_settings) || (item.ad_type ==  'ab_testing_settings' && !this.state.ab_testing_settings) || (item.ad_type ==  'rotator_ads' && !this.state.rotator_ads_status) || (item.ad_type ==  'group_insertion' && !this.state.group_insertion_settings) ?  ({ display: 'none' }) :{}}><div className="quads-ad-type-link"><Link  to={`admin.php?page=quads-settings&path=wizard&ad_type=${item.ad_type}`} className="quads-nav-link">{this.props.getImageByAdType(item.ad_type, index)}</Link></div></li>    )}
                    </ul>
                    </div>                            
        );
    }
}

export default AdTypeSelectorNavLinkR;