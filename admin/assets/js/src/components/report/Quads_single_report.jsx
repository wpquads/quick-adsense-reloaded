import React, {Component, Fragment} from 'react';
import Icon from '@material-ui/core/Icon';
import Select from "react-select";
import '../ads/create/QuadsAdListCreate.scss';
import '../../components/report/QuadsAdReport.scss'
import {Chart} from 'react-charts'
import DatePicker from "react-datepicker";
import queryString from 'query-string'

import "react-datepicker/dist/react-datepicker.css";

class Quads_single_report extends Component {
    
    

    constructor(props) {
        super(props);
        this.state = {
            custom_period: false,
            redirect:false,
            adsense_modal :false,
            current_page : 'report',
            isLoading : false,
            cust_fromdate:new Date(),
            cust_todate:new Date(),
            ads_list:[],
            adsToggle : false,    
            adsToggle_list : false,
            ab_testing:[],
            getallads_data_temp: [],
            getallads_data: [],
            ad_ids_temp: [],
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
        this.QuadsRedirectToWizard = this.QuadsRedirectToWizard.bind(this);
    }
     display_report_stats = (response) => {

        let imp_report = response.impressions
        let click_report = response.clicks
        let ad_day = response.ad_day
        let day_ranges_imp = response.individual_impr_day_counts
        let day_ranges_click = response.individual_click_day_counts
        let ad_imp_individual_dates = response.ad_imp_individual_dates
        
        if( ad_day === "last_7_days" ){

            var imp_day_ranges_to_num = day_ranges_imp.map(Number)
                var click_day_ranges_to_num = day_ranges_click.map(Number)
                var data = {
                    labels: ad_imp_individual_dates,
                    datasets: [
                      {
                      label: 'Impressions',
                      data: imp_day_ranges_to_num,
                      fill: false,
                      borderColor: 'rgb(75, 192, 192)',
                      tension: 0.1
                    },
                    {
                      label: 'Clicks',
                      data: click_day_ranges_to_num,
                      fill: false,
                      borderColor: 'rgb(75, 192, 192)',
                      tension: 0.1
                    }
                  ]

                  };
                }
            if( ad_day == "today" ){
                
                var data = {
                        labels: ['ad report','ad report'],
                    datasets: [
                      {
                      label: 'Impressions',
                      data: [imp_report,imp_report],
                      fill: false,
                      borderColor: 'rgb(75, 192, 192)',
                      tension: 0.1
                    },
                    {
                      label: 'Clicks',
                      data: [click_report,click_report],
                      fill: false,
                      borderColor: 'rgb(75, 192, 192)',
                      tension: 0.1
                    }
                  ]
                  }
                }
            if( ad_day == "yesterday" ){

                    var data = {
                        labels: ['ad report','ad report'],
                    datasets: [
                      {
                      label: 'Impressions',
                      data: [imp_report,imp_report],
                      fill: false,
                      borderColor: 'rgb(75, 192, 192)',
                      tension: 0.1
                    },
                    {
                      label: 'Clicks',
                      data: [click_report,click_report],
                      fill: false,
                      borderColor: 'rgb(75, 192, 192)',
                      tension: 0.1
                    }
                  ]
                  }
                }
            const config = {
                type: 'line',
                data: data,
              };
        drawChart(config);
    
    }
    
    drawChart = (config) => {

        if(document.getElementById("quads_canvas"))
        document.getElementById("quads_canvas").outerHTML = "";
        var new_canvas = "<canvas id='quads_canvas'>" + " <canvas>";
        document.getElementById('quads_reports_canvas').innerHTML = new_canvas;
        if(window.myPieChart ) {
            window.myPieChart.update();
        }
        // Get the context of the canvas element we want to select
        var ctx = document.getElementById('quads_canvas');
        window.myPieChart = new Chart(ctx, config);

    }

    getallads = (search_text = '',page = '') => {
        let url = quads_localize_data.rest_url + "quads-route/get-ads-list?posts_per_page=100&pageno="+page;
        if(quads_localize_data.rest_url.includes('?')){
         url = quads_localize_data.rest_url + "quads-route/get-ads-list&posts_per_page=100&pageno="+page;
      }
       
       fetch(url, {
         headers: {                    
           'X-WP-Nonce': quads_localize_data.nonce,
         }
       })
       .then(res => res.json())
       .then(
         (result) => {      
           let getallads_data =[];
           let ad_ids_temp =[];
           Object.entries(result.posts_data).map(([key, value]) => {
           if(value.post_meta['ad_type'] != "random_ads" && value.post_meta['ad_type'] != "rotator_ads" && value.post_meta['ad_type'] != "group_insertion" && value.post['post_status'] != "draft")
             getallads_data.push({label: value.post['post_title'], value: value.post['post_id']});
           if(value.post_meta['ad_type'] != "random_ads" && value.post_meta['ad_type'] != "rotator_ads" && value.post_meta['ad_type'] != "group_insertion" && value.post['post_status'] == "publish")
             ad_ids_temp.push(value.post['post_id']);
           })      
             this.setState({
             isLoaded: true,
             getallads_data: getallads_data,
             ad_ids_temp: ad_ids_temp,
           });
           
         },        
         (error) => {
           this.setState({
              isLoaded: true,         
           });
         }
       );          
      }

      view_reports_data = (eve_) => {
        let date = ''
        let newdate = ''
        let day_val = ''
        const {report} = this.state

        const current_page = queryString.parse(window.location.search);
        let id_ = current_page.id
        newdate = document.getElementById('report_period').value
        day_val = document.getElementById('report_period').value
        
        var url =  quads_localize_data.rest_url + 'quads-adsense/get_report_stats?id='+id_+'&date='+newdate+'&day='+day_val;

            fetch(url,{
                method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            } )
            .then(res => res.json())
            .then( (response) => {
                if(response!=null){
                    var render_data
                    var get_table = document.getElementById("quads_report_table")
                    if(response.clicks == null || response.impressions == null ){
                        get_table.innerHTML = 'No data Found'
                    }
                    else{
                        console.log(response);
                        this.display_report_stats(response)
                        render_data = "<table><tbody><tr><td><b>Impressions</b></td><td><b>Clicks</b></td></tr><tr><td>"+response.impressions+"</td><td>"+response.clicks+"</td></tr></tbody></table>"
                        get_table.innerHTML = render_data

                    }
            }
            } )    

    }
    view_report_stats_form_ChangeHandler = (eve) => {
        let date = ''
        let newdate = ''
        let day_val = ''

        let id = document.getElementById('view_stats_report').value
        newdate = document.getElementById('report_period').value
        day_val = document.getElementById('report_period').value

       if( day_val!='custom' ){
        var url =  quads_localize_data.rest_url + 'quads-adsense/get_report_stats?id='+id+'&date='+newdate+'&day='+day_val;

            fetch(url,{
                method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            } )
            .then(res => res.json())
            .then( (response) => {
                if(response!=null){
                    var render_data
                    var get_table = document.getElementById("quads_report_table")
                    if(response.clicks == null || response.impressions == null ){
                        get_table.innerHTML = 'No data Found'
                    }
                    else{
                        console.log(response);
                        this.display_report_stats(response)
                        render_data = "<table><tbody><tr><td><b>Impressions</b></td><td><b>Clicks</b></td></tr><tr><td>"+response.impressions+"</td><td>"+response.clicks+"</td></tr></tbody></table>"
                        get_table.innerHTML = render_data

                    }
            }
            } )
        }
        
        if( day_val == 'custom' ){
            var newfromdate;
            var newtodate;
            if(eve.target===undefined){
                this.setState({cust_fromdate:eve}) ;
           }
            if(eve.target===undefined){
                this.setState({cust_todate:eve}) ;
           }
           newfromdate = new Date(this.state.cust_fromdate).toISOString()
           newtodate = new Date(this.state.cust_todate).toISOString()
            this.setState( { custom_period: true } )
            var url =  quads_localize_data.rest_url + 'quads-adsense/get_report_stats?id='+id+'&fromdate='+newfromdate+'&todate='+newtodate+'&day='+day_val;

            fetch(url,{
                method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            } )
            .then(res => res.json())
            .then( (response) => {
                if(response!=null){
                    var render_data
                    var get_table = document.getElementById("quads_report_table")
                    if(response.clicks == null || response.impressions == null ){
                        get_table.innerHTML = 'No data Found'
                    }
                    else{
                        console.log(response);
                        this.display_report_stats(response)
                        render_data = "<table><tbody><tr><td><b>Impressions</b></td><td><b>Clicks</b></td></tr><tr><td>"+response.impressions+"</td><td>"+response.clicks+"</td></tr></tbody></table>"
                        get_table.innerHTML = render_data

                    }
            }
            } )
        }
        else{
            this.setState( { custom_period: false } )
        }

    }
    
    view_report_fromdate = (eve) => {
        let date = ''
        let newdate = ''
        let day_val = ''

        let id = document.getElementById('view_stats_report').value
        newdate = document.getElementById('report_period').value
        day_val = document.getElementById('report_period').value
        
        if( day_val == 'custom' ){
            var newfromdate;
            var newtodate;
            if(eve.target===undefined){
                this.setState({cust_fromdate:eve}) ;
            }
           newfromdate = new Date(this.state.cust_fromdate).toISOString()
        } 

    }
    
    view_report_todate = (event) => {
        let date = ''
        let newdate = ''
        let day_val = ''
        const {report} = this.state
        if(event.target===undefined){
             this.setState({cust_todate:event}) ;
        }
        let id = document.getElementById('view_stats_report').value
        newdate = document.getElementById('report_period').value
        day_val = document.getElementById('report_period').value
        
        if( day_val == 'custom' ){
            var newtodate;
            if(event.target===undefined){
                this.setState({cust_todate:event}) ;
           }
           newtodate = new Date(this.state.cust_todate).toISOString()
        } 

    }

    get_data_dates = (eve) => {
        let day_val = ''
        const current_page = queryString.parse(window.location.search);
        let id_ = current_page.id
        day_val = document.getElementById('report_period').value
        let from_date = new Date(this.state.cust_fromdate).toISOString()
        let to_date = new Date(this.state.cust_todate).toISOString()
        var url =  quads_localize_data.rest_url + 'quads-adsense/get_report_stats?id='+id_+'&fromdate='+from_date+'&todate='+to_date+'&day='+day_val;
        fetch(url,{
                method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            } )
            .then(res => res.json())
            .then( (response) => {
                if(response!=null){
                    var render_data
                    var get_table = document.getElementById("quads_report_table")
                    if(response.clicks == null || response.impressions == null ){
                        get_table.innerHTML = 'No data Found'
                    }
                    else{
                        console.log(response);
                        this.display_report_stats(response)
                        render_data = "<table><tbody><tr><td><b>Impressions</b></td><td><b>Clicks</b></td></tr><tr><td>"+response.impressions+"</td><td>"+response.clicks+"</td></tr></tbody></table>"
                        get_table.innerHTML = render_data

                    }
            }
            } )

    }

    
    adsToggle_list = () => {
      const get_all_data = JSON.parse(JSON.stringify(this.state.getallads_data));
      var getallads_data_temp = [];
      getallads_data_temp = get_all_data;
      const ads_list = this.state.ads_list;
      this.setState({getallads_data_temp:getallads_data_temp});
    }
    
    QuadsRedirectToWizard(e){

        this.setState({
            redirect: true
        })

        const ad_type = e.currentTarget.dataset.adtype;

        const location = this.props.location;
        const pathname = location.pathname;

        let url = `${pathname}?page=quads-settings&path=wizard&ad_type=${ad_type}`;
        //this.props.history.push(url);
        window.location.href = url;

    }  

    componentDidMount(){
        // this.get_report_status();
        this.getallads(); 
        this.view_reports_data(); 
        setTimeout( () => {
            var view_stat = document.getElementsByClassName("view_statsreport")[0]
            view_stat.click()
        }, 500)
    }
  
     
    
    view_stats_report_handler = () => {
        this.setState({
            current_page:'view_reports_stats'
        })
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
        if(name == 'adsense_code'){
            report['adsense_code'] =  value;
            this.setState({ report });
        }else if(name == 'report_type'){
            report['report_type'] =  value;
            this.setState({ report });
            this.quads_adsense_report();
        }else if(name == 'report_period'){
            report['report_period'] =  value;
            this.setState({ report });
            this.quads_adsense_report();
        }else if(name == 'input_based'){
            report['input_based'] =  value;
            this.setState({ report });
            this.quads_adsense_report();
        }else if(name == 'adsense_code_view'){
            if(this.state.adsense_pub_id ) {
                report['adsense_code_view'] =  value;
            }
            this.setState({ report });
            if(value) {
                this.openAdsenseAuth();
            }else{
                this.revoke_adsense_link();
            }
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

        let params = new URLSearchParams(location.search);
        let q_id = params.get('id')
        let q_ad = params.get('ad')

        return (
            <>
                {this.state.isLoading ? <div className="quads-cover-spin"></div>
                    : null}

                        <div>  <h3>{__('Reports', 'quick-adsense-reloaded')}</h3>
                        </div>
                
                <Fragment>
                    <div>
                        <nav aria-label="breadcrumb">
                        <ol className="breadcrumb">
                            <li className="breadcrumb-item"><a> Report</a></li>
                            <li className="breadcrumb-item active" aria-current="page">Stats Report</li>
                        </ol>
                    </nav>
                    <div className="quads-report-networks">
                        <div className={'stats_rep'}>
                        <h1>Stats Report</h1>
                        </div>
                        <div className={'quads-select-menu'} >
                        <div className={'quads-select view_statsreport'} onClick={this.adsToggle_list}>
                        <select name="view_stats_report" onChange={this.view_report_stats_form_ChangeHandler} id={'view_stats_report'} placeholder="Select Ads">
                        {this.state.getallads_data_temp ? this.state.getallads_data_temp.map( item => {
                            const sel = item.value                    
                            return (
                                <option data-attr={q_id} data-selected={item.value == q_id ? "selected" : ''} selected={sel == q_id} key={item.value} value={item.value}>{item.label}</option>
                                )
                         } )
                        : 'No Options' }
                        </select>
                        <select name="report_period" id={'report_period'} onChange={this.view_report_stats_form_ChangeHandler}>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="last_7_days">Last 7 days</option>
                        <option value="last_15_days">Last 15 days</option>
                        <option value="custom">Custom</option>
                    </select>
                    
                    { this.state.custom_period == true  ? <>
                        <DatePicker maxDate={(new Date())} selected={this.state.cust_fromdate} id={"cust_fromdate"} placeholderText="Start Date" dateFormat="dd/MM/yyyy" onChange={this.view_report_fromdate} />
                        <DatePicker maxDate={(new Date())} selected={this.state.cust_todate} id={"cust_todate"} placeholderText="End Date" dateFormat="dd/MM/yyyy" onChange={this.view_report_todate} />
                        <button className="show_btn" onClick={this.get_data_dates}>Show data</button>
                    </> : '' 

                    }
                        </div>
                        </div>
                        <div id='quads_reports_canvas' class='report_single' ></div>
                        <div id={'quads_report_table'}></div>
                        </div>
                        </div>
                        </Fragment>
            </>

        );
    }
}

export default Quads_single_report;
