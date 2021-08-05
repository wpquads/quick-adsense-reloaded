import React, {Component, Fragment} from 'react';
import '../ads/create/QuadsAdListCreate.scss';
import '../../components/report/QuadsAdReport.scss'
import QuadsAdListSearch from "../common/search/QuadsAdListSearch";
import QuadsAdListPagination from "../common/pagination/QuadsAdListPagination";
import { BrowserRouter as Router, Switch, Route, Link } from 'react-router-dom';
import Tooltip from '@material-ui/core/Tooltip';

import {Chart} from 'react-charts'
import DatePicker from "react-datepicker";
import queryString from 'query-string'
import "react-datepicker/dist/react-datepicker.css";

class QuadsAdLogging extends Component {

    constructor(props) {
        super(props);
        this.state = {
            redirect:false,
            adsense_modal :false,
            current_page : 'ad_logging',
            isLoading : true,
            cust_fromdate:new Date(),
            cust_todate:new Date(),
            search_text       : '',
            clicked_btn_id    : 1,
            page              : 1,
            items : [],
            posts_found:'',
            report : {
                adsense_code: '',
                adsense_code_data :[
                    {
                        label: 'Series 1',
                        data: []
                    }
                ],
                adsense_pub_id : '',
                adsense_code_view: false,
                adsense_report_errors: '',
                report_type: 'earning',
                input_based: 7,
                report_period:'',
            },
            All_report_list: [
                {ad_type:'adsense',ad_type_name:'AdSense',id:'quads-adsense'},
            ]
        };
        const page = queryString.parse(window.location.search);
        {page.ad_id ? this.logMainSearchMethod('last_7days',page.ad_id) :this.logMainSearchMethod('last_7days') }

        // this.QuadsRedirectToWizard = this.QuadsRedirectToWizard.bind(this);
    }
     timeConverter = (UNIX_timestamp) => {
        var a = new Date(UNIX_timestamp * 1000);
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var year = a.getFullYear();
        var month = months[a.getMonth()];
        var date = a.getDate();
        // var hour = a.getHours();
        // var min = a.getMinutes();
        // var sec = a.getSeconds();
        var time = date + '/ ' + month + '/ ' + year + ' ' +  a.toLocaleString('en-US', { hour: 'numeric', hour12: true }) ;
        return time;
      }
  report_formChangeHandler = (event) => {

        const {report} = this.state;
        let name  = event.target.name;
        let value = '';
        if(event.target.type === 'file'){
            value = event.target.files[0];
            this.setState({backup_file:value});
        }else {
            if (event.target.type === 'checkbox') {
                value = event.target.checked;
            } else {
                value = event.target.value
            }
        }
        if(name == 'report_period'){
            report['report_period'] =  value;
            this.logMainSearchMethod(value);
            this.setState({ report });
        }
    }
    timer = null;
  QuadslogSearchAd =(e) => {                         
          
          let search_val = e.target.value;

          this.setState({
            search_text: search_val,     
          });

          clearTimeout(this.timer);

          this.timer = setTimeout(() => {
            this.logMainSearchMethod('',search_val, this.state.page);
          }, 300);

  }
  QuadsLogPaginateAd =(e) => { 
    e.preventDefault();                     
    this.logMainSearchMethod('','', e.currentTarget.dataset.id);
    this.setState({
       page: e.currentTarget.dataset.id,
       clicked_btn_id: e.currentTarget.dataset.index,
       isLoading:true
     });
}
open_ad = (ad_id) => {
    console.log(ad_id);
    
        getAdDataById =  (ad_id) => {

      let url = quads_localize_data.rest_url+'quads-route/get-ad-by-id?ad-id='+ad_id;   
      if(quads_localize_data.rest_url.includes('?')){
         url = quads_localize_data.rest_url+'quads-route/get-ad-by-id&ad-id='+ad_id;  
      }   
      fetch(url,{
        headers: {                    
          'X-WP-Nonce': quads_localize_data.nonce,
        }
      }
      )
      .then(res => res.json())
      .then(
        (result) => {  
          
          const { quads_post_meta } = { ...this.state };
          Object.entries(result).map(([key, value]) => {
            if(key == 'post'){
              this.setState({quads_post: result.post}); 
            }else{ 
                          
              Object.entries(value).map(([meta_key, meta_val]) => {
                 if(meta_key == 'visibility_include'){
                   this.includedVisibilityVal = meta_val;
                }else if(meta_key == 'visibility_exclude'){
                   this.excludedVisibilityVal = meta_val;
                }else if(meta_key == 'targeting_include'){
                   this.includedVal = meta_val;
                }else if(meta_key == 'targeting_exclude'){
                   this.excludedVal = meta_val;
                }
                  if(meta_val){
                    quads_post_meta[meta_key] =    meta_val;   
                  }                   
              })
              
              this.setState(quads_post_meta);
            }

          })  
                    
        },        
        (error) => {
          
        }
      );  

    }
}
  logMainSearchMethod = (report_period='',search_text='', page=1) => { 
    let url = quads_localize_data.rest_url + "quads-route/getAdloggingData";
    let cust_fromdate =this.state.cust_fromdate;
    let cust_todate =this.state.cust_todate;
    const {report} = this.state;
    if(report_period == ''){
        report_period = report.report_period;
    }
    if(search_text == ''){
        search_text = this.state.search_text;
    }
   

    fetch(url,{
        method: "post",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-WP-Nonce': quads_localize_data.nonce,
        },
        body: JSON.stringify({'report_period':report_period,'cust_fromdate':cust_fromdate,'cust_todate':cust_todate,'search_param':search_text,'page':page})
    })
    .then(res => res.json())
    .then(
      (result) => { 
        this.setState({
            isLoading: false,
          items: result.posts_data,
          posts_found: result.posts_found,

        });
      },        
      (error) => {
        this.setState({
            isLoading: true,
         
        });
      }
    );            
}  
renderSwitch(param='') {
    const {report} = this.state;

    switch(report.report_period) {
    case 'last_15days':
        return 'from Last 15 days';
    case 'last_30days':
        return 'from Last 30 days';
    case 'last_6months':
        return 'from Last 6 months';
    case 'last_1year':
        return 'from Last 1 Year';
    case 'all_time':
        return '';
    case 'custom':
        let cust_fromdate = new Date(this.state.cust_fromdate);
        cust_fromdate = cust_fromdate.getDate()+"/"+cust_fromdate.getMonth()+"/"+cust_fromdate.getFullYear();
        let cust_todate = new Date(this.state.cust_todate);
        cust_todate = cust_todate.getDate()+"/"+cust_todate.getMonth()+"/"+cust_todate.getFullYear();
 
        
        return 'from '+cust_fromdate+' to '+ cust_todate;
      default:
        return 'from Last 7 days';
    }
  }
    render() {
        
        const {__} = wp.i18n;
        const {report} = this.state;
        const axes = [
            { primary: true, type: 'time', position: 'bottom' },
            { position: 'left', type: 'linear'  }
        ];
        const series = [
            {showPoints: 'false'}
        ];

        let quads_localize_data_is_pro =quads_localize_data.is_pro;
        let items = this.state.items;


        
        return (
            <>
                {this.state.isLoading ? <div className="quads-cover-spin"></div>
                    : null} 
                { this.state.current_page == 'ad_logging' ?
                    <Fragment>
                        <div>  <h1>{__('AD Logging', 'quick-adsense-reloaded')}</h1>
                        <h3>{__('Here you can see the total performance and behaviour  of your ads', 'quick-adsense-reloaded')}</h3>
                        <p> Showing all the recent data  {this.renderSwitch()}</p>
                            <div className="quads-ad-networks ad_logging">
                            <div className="quads-search-box-panel">                
                            <div className="quads-search-box"><QuadsAdListSearch triggerSearch={this.QuadslogSearchAd} /></div>                

                            <div className={'quads-select-menu'} >
                        <select name="report_period" id={'report_period'} value={report.report_period} onChange={this.report_formChangeHandler}>
                                                <option value="last_7days">Last 7 days</option>
                                                <option value="last_15days">Last 15 days</option>
                                                <option value="last_30days">Last 30 days</option>
                                                <option value="last_6months">Last 6 months</option>
                                                <option value="last_1year">Last 1 year</option>
                                                <option value="all_time">All Time</option>
                                                <option value="custom">Custom</option>
                                            </select>
                                            {report.report_period == 'custom' ?[(
                                                quads_localize_data_is_pro ?
                                                    <>
                                                        <DatePicker maxDate={(new Date())} selected={this.state.cust_fromdate} id={"cust_fromdate"} placeholderText="Start Date" dateFormat="dd/MM/yyyy"  onChange={date => this.setState({cust_fromdate:date})} />
                                                        <DatePicker maxDate={(new Date())} selected={this.state.cust_todate} id={"cust_todate"} placeholderText="End Date" dateFormat="dd/MM/yyyy"  onChange={date => this.setState({cust_todate:date})} />
                                                    </>
                                                    :  null
                                            )] : null}
                        </div>
              </div> 
                            { items && items.length > 0 ?      
        <table className="quads-ad-table">
          <thead>
          <tr>
          <th>{__('Ad id', 'quick-adsense-reloaded')}</th>
          <th>{__('Time Stamp', 'quick-adsense-reloaded')}</th>
          <th>{__('URL', 'quick-adsense-reloaded')}</th>
          <th>{__('Browser', 'quick-adsense-reloaded')}</th>
          <th>{__('Referrer', 'quick-adsense-reloaded')}</th>
          <th>{__('IP Address', 'quick-adsense-reloaded')}</th>
          <th></th>
          </tr>
          </thead>
          <tbody>
          {items.map((item, index) => (     
                   
            <tr key={index}>
                <td> <Link to={`admin.php?page=quads-settings&path=wizard&ad_type=${item.ad_type}&action=edit&post=${item.ad_id}`} target="_blank"  className="quads-edit-btn"><Tooltip title={item.label} placement="right" arrow ><span>{item.ad_id}</span></Tooltip> </Link></td>
                <td>{this.timeConverter(item.ad_thetime)}</td>
                <td className={'quads_no_transform'}> <a target="_blank" href={item.url}  target="_blank"  className="quads-edit-btn"> {item.url}</a></td>
                <td>{item.browser}</td>
                <td className={'quads_no_transform'}>{item.referrer}</td>
                <td>{item.ip_address}</td>
                </tr>
                 ))} 
                </tbody>
               
                </table> : <div> No Data </div> }
                </div>  
                <div className="quads-list-pagination">
                <QuadsAdListPagination ad_list={this.state} triggerPagination={this.QuadsLogPaginateAd} search_text={this.state.search_text} />
              </div>
               </div>
                    </Fragment>
                    : ''
                }
            
               
            </>

        );
    }
}

export default QuadsAdLogging;
