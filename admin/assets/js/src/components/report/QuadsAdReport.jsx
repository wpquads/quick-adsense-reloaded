import React, {Component, Fragment} from 'react';
import Icon from '@material-ui/core/Icon';
import Select from "react-select";
import '../ads/create/QuadsAdListCreate.scss';
import '../../components/report/QuadsAdReport.scss'
import {Chart} from 'react-charts'
import DatePicker from "react-datepicker";

import "react-datepicker/dist/react-datepicker.css";

class QuadsAdReport extends Component {

    constructor(props) {
        super(props);
        this.state = {
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
           Object.entries(result.posts_data).map(([key, value]) => {
           if(value.post_meta['ad_type'] != "random_ads" && value.post_meta['ad_type'] != "rotator_ads" && value.post_meta['ad_type'] != "group_insertion" && value.post['post_status'] != "draft" && value.post_meta['ad_type'] == "ab_testing" )
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
    getAccountDetails = (tokenData) => {
        const body_json = this.state;
        let data =tokenData;
        // data['token_data'] = tokenData;
        let url = quads_localize_data.rest_url + 'quads-adsense/quads_adsense_get_details';
        fetch(url,{
            method: "POST",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                "Access-Control-Allow-Origin": "*",
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            //make sure to serialize your JSON body
            body: JSON.stringify(data)
        })
        .then(response => response.json())
            .then(
                (response) => {
                    if ( response.status && true === response.status ) {
                        if ( response['adsense_id'] ) {
                            this.closeModal();
                            window.location.reload();
                            const {report} = this.state;
                            report['adsense_pub_id'] =   response['adsense_id'];
                            report['adsense_code_view'] =  true;

                            this.setState({ report });
                        } else {
                            const {report} = this.state;
                            report['adsense_report_errors'] =   JSON.stringify( response );
                            this.setState({ report });
                        }
                    }
                     else {
                        if ( response['raw']['errors'][0]['message'] ) {
                            const {report} = this.state;
                            report['adsense_report_errors'] =  response['raw']['errors'][0]['message'] ;
                            this.setState({ report });

                        } else if ( response['raw']['message'] ) {
                            const {report} = this.state;
                            report['adsense_report_errors'] =  response['raw']['message'] ;
                            this.setState({ report });
                        }
                    }

                });
    }
    adsense_submit = () => {
        const body_json = this.state;
        let url = quads_localize_data.rest_url + 'quads-adsense/quads_confirm_code';
        fetch(url,{
            method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            //make sure to serialize your JSON body
            body: JSON.stringify(body_json)
        })
            .then(res => res.json())
            .then(
                (response) => {
                    if ( response.status && true === response.status && response['token_data'] ) {
                        this.getAccountDetails( response['token_data'] );
                    }else{
                        const {report} = this.state;
                        report['adsense_report_errors'] =   response['response_body'];
                        this.setState({ report });
                    }
                });
    }

    componentDidMount(){
        this.get_report_status();
        this.getallads(); 
    }

    get_report_status = () => {
        let url = quads_localize_data.rest_url + 'quads-adsense/get_report_status';
        fetch(url,{
            method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            //make sure to serialize your JSON body
        })
            .then(res => res.json())
            .then(
                response => {
                    if (  true == response.adsense.status && response.adsense.account_id.displayName ) {
                        const {report} = this.state;
                        report['adsense_code_view'] =  true;
                        this.setState({ report,adsense_pub_id:response.adsense.account_id.displayName  });
                    }
                });
    }
    getImageByAdType = (type) =>{
        let type_img = [];
        let img_url  = '';

        switch (type) {
            case 'adsense':
                img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/add_adsense_logo.png';
                break;
            case 'abtesting':
                img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ab.png';
                break;
            default:
                break;
        }

        type_img.push(<img key={type}  src={img_url} />);

        return type_img;
    }
    openAdsenseAuth =() =>{
        this.setState({adsense_modal:true});
    }
    closeModal =() =>{
        this.setState({adsense_modal:false,current_page:'report'});
    }
    change_page =(newPageName) =>{
        this.setState({current_page:newPageName});
    }

    abtesting_handler = () =>{
        this.setState({current_page:'adsense_report_page_abtesting'});
    }
    quads_adsense_report  =(pub_id='') =>{
        const {report} = this.state;
        if(pub_id == ''){
            pub_id =this.state.adsense_pub_id;
        }else{
            this.setState({ adsense_pub_id: pub_id,current_page:'adsense_report_page'});
        }
        if(!report.adsense_code_view) {
            this.inputElement.click();
        }
    }

    ab_testing_report_formChangeHandler = (event) =>{
        const {report} = this.state;
        let name  = event.target.name;
        let value = '';
        if( name =='abtesting_report' ){
            let url = quads_localize_data.rest_url + 'quads-adsense/get_report_abtesting';
            fetch(url,{
            method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            body: JSON.stringify({ title: 'Get positions data' })
        })
        .then(res => res.json())
            .then(
                (response) => {
                    var newNode = document.createElement("span");
                    newNode.innerHTML = response.success_msg;
                    newNode.setAttribute("id", "table_main");
                    var referenceNode = document.querySelector('#abtesting_report');
                    referenceNode.after(newNode);
                }).catch((error) => {
                    console.log(error)
                  });

        }

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

    revoke_adsense_link = () =>{
        let url = quads_localize_data.rest_url + 'quads-adsense/revoke_adsense_link';
        let  pub_id =this.state.adsense_pub_id;
        fetch(url,{
            method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            body: JSON.stringify({'account_id':pub_id})

            //make sure to serialize your JSON body
        })
            .then(res => res.json())
            .then(
                (response) => {
                    if ( true == response.adsense.status && response.adsense.account_id.displayName ) {
                        const {report} = this.state;
                        report['adsense_code_view'] =  true;
                        this.setState({ report,adsense_pub_id:response.adsense.account_id.displayName  });
                    }
                });
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

        return (
            <>
                {this.state.isLoading ? <div className="quads-cover-spin"></div>
                    : null}
                { this.state.current_page == 'report' ?
                    <Fragment>
                        <div>  <h3>{__('Reports', 'quick-adsense-reloaded')}</h3>
                            <div className="quads-ad-networks">
                                <ul key={'quads-ad-networks'}>
                                    {this.state.All_report_list.map(item => (
                                        <li key={item.id} data-adtype={item.ad_type} id={item.id}><a className="quads-nav-link" onClick={() => this.quads_adsense_report(this.state.adsense_pub_id)} >
                                                {this.getImageByAdType(item.ad_type)}
                                                {item.ad_type=='adsense' ? <div><strong>{report.adsense_code_view ?'View Report': 'Connect' }</strong></div> : '' }
                                                </a>
                                                {item.ad_type=='adsense' ?
                                            <div className={'view_report'} >
                                                <label htmlFor="quads-connect-adsense">{report.adsense_code_view ?'Connected': 'Disconnected' }</label>

                                                <label className="quads-switch">
                                                    <input type={'hidden'} id={'pub_id'} value={this.state.adsense_pub_id} />

                                                    <input id="quads-connect-adsense" ref={input => this.inputElement = input} className={report.adsense_code_view ?'disabled_adsense_link': '' } type="checkbox" name="adsense_code_view" onChange={this.report_formChangeHandler} checked={report.adsense_code_view} />
                                                    <span className="quads-slider"></span>
                                                </label>
                                            </div>
                                            : '' }
                                        </li>

                                        
                                    ))}
                                    <li data-adtype="abtesting" id="quads-adsense-abtesting">
                                    <a class="quads-nav-linkforabtesting" onClick={ () =>{
                                        this.abtesting_handler()
                                    } }  >
                                    <img src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ab.png'}/>
                                    </a>
                                    <div id="view_report_abtesting" style={{marginTop: "40px",color: "#005af0"}} onClick={ () =>{
                                        this.abtesting_handler()
                                    } }><strong>View Report</strong></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </Fragment>
                    : ''
                }
                {this.state.current_page =='adsense_report_page_abtesting' ?
                    <Fragment>
                        <div >
                        <nav aria-label="breadcrumb">
                        <ol className="breadcrumb">
                            <li className="breadcrumb-item"><a> Report</a></li>
                            <li className="breadcrumb-item active" aria-current="page">A/B Testing Report</li>
                        </ol>
                    </nav>
                    <div className="quads-report-networks">
                        <div>
                        <h1>A/B Testing Reports</h1>
                        </div>
                        <div className={'quads-select-menu'} >
                        <div className={'quads-select'} onClick={this.adsToggle_list}>
                        <select name="abtesting_report" id={'abtesting_report'} 
                        onChange={this.ab_testing_report_formChangeHandler} placeholder="Select Ads">
                        <option value="">Select report</option>
                        {this.state.getallads_data_temp ? this.state.getallads_data_temp.map( item => (
                            <option key={item.value} value={item.value}>{item.label}</option>
                        ) )
                        : 'No Options' }
                        </select>
                        </div>
                        </div>
                        </div>
                        </div>
                        </Fragment> : ''}
                {this.state.current_page =='adsense_report_page' ?
                    <Fragment>
                        <div >
                            <nav aria-label="breadcrumb">
                                <ol className="breadcrumb">
                                    <li className="breadcrumb-item" onClick={() => this.change_page('report') }><a >Report</a></li>
                                    <li className="breadcrumb-item active" aria-current="page">Adsense Report</li>
                                </ol>
                            </nav>
                            <div className="quads-report-networks">

                                <div>
                                    <h1>Adsense Reports
                                    </h1>
                                </div>
                                <div className={'quads-select-menu'} >
                                    <input type={'hidden'} id={'pub_id'} value={this.state.adsense_pub_id} />
                                    <select  name="report_type" id={'report_type'} onChange={this.report_formChangeHandler} >
                                        <option value="">Select Report</option>
                                        <option value="earning">Earnings</option>
                                        <option value="earning_forcast">Earnings Forcast</option>
                                        <option value="top_device_type">Top Earning Device type</option>
                                    </select>
                                    {this.state.report.report_type != 'earning_forcast' ?
                                        <>
                                            <select name="report_period" id={'report_period'} value={report.report_period} onChange={this.report_formChangeHandler}>
                                                <option value="">Select Duration</option>
                                                <option value="last_7_days">Last 7 days</option>
                                                <option value="last_15_days">Last 15 days</option>
                                                <option value="last_30_days">Last 30 days</option>
                                                <option value="last_6_months">Last 6 months</option>
                                                <option value="last_1_year">Last 1 year</option>
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
                                        </>
                                        : [(
                                            quads_localize_data_is_pro ?
                                                <div>  revenue prediction based on<select name="report_period" id={'report_period'} value={report.report_period} onChange={this.report_formChangeHandler}>
                                                    <option value="">Select Duration</option>
                                                    <option value="last_7_days">Last 7 days</option>
                                                    <option value="last_15_days">Last 15 days</option>
                                                    <option value="last_30_days">Last 30 days</option>
                                                    <option value="last_6_months">Last 6 months</option>
                                                    <option value="last_1_year">Last 1 year</option>
                                                    <option value="all_time">All Time</option>
                                                    <option value="custom">Custom</option>
                                                </select> {report.report_period == 'custom' ?
                                                    <>
                                                        <DatePicker  minDate={(new Date())} selected={this.state.cust_fromdate} id={"cust_fromdate"} placeholderText="Start Date" dateFormat="dd/MM/yyyy"  onChange={date => this.setState({cust_fromdate:date})} />
                                                        <DatePicker minDate={(new Date())} selected={this.state.cust_todate} id={"cust_todate"} placeholderText="End Date" dateFormat="dd/MM/yyyy"  onChange={date => this.setState({cust_todate:date})} />
                                                    </>
                                                    : null} for
                                                    <select name="input_based" id={'input_based'} onChange={this.report_formChangeHandler}>
                                                        <option value="next_7days">Next 7days</option>
                                                        <option value="next_15days">Next 15days</option>
                                                        <option value="next_30days">Next 30days</option>
                                                        <option value="next_6months">Next 6months</option>
                                                        <option value="next_1year">Next 1year</option>
                                                    </select>
                                                </div>
                                                : null
                                        )]}
                                </div>
                                <div >
                                    {this.state.report.report_type != 'top_device_type' && report.report_period !='' && report.report_period != '' ?
                                        <select name="report_view_type" id={'report_view_type'}>
                                            <option value="">View Type</option>
                                            <option value="day">Day</option>
                                            <option value="week">Week</option>
                                            <option value="month">Month</option>
                                            <option value="year">year</option>
                                        </select>
                                        :null}


                                    {!quads_localize_data_is_pro && this.state.report.report_type != 'earning'?
                                        <div id='quads_reports_canvas' className={'canvas_get_pro'}>
                                            <h5> Please select Report type and Duration</h5>
                                            <div id={'quads_get_pro'}>This feature is available in PRO version <a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">Unlock this feature</a>
                                            </div>
                                        </div>
                                        :<div id='quads_reports_canvas'>
                                            <h5> Please select Report type and Duration</h5>
                                        </div>}
                                    <div id={'quads_report_table'}></div>
                                </div>
                            </div>
                        </div>
                    </Fragment>: null
                }
                {this.state.adsense_modal ?
                    <>
                        <div className="quads-large-popup-bglayout">  </div>
                        <div className="quads-large-popup">
                            <div className="quads-large-popup-content">
                                <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
                                <div className="quads-large-popup-title">
                                    <h1>Please enter the confirmation code.
                                    </h1>
                                </div>
                                <div className="quads-large-description"></div>

                                <div className="quads-large-content">
                                    <textarea name="adsense_code" onChange={this.report_formChangeHandler} value={report.adsense_code} />
                                    {report.adsense_report_errors ?
                                        <div className="quads-modal-error">
                                            {report.adsense_report_errors}
                                        </div>
                                        :null}
                                    <a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.adsense_submit}>Submit code</a>
                                </div>
                            </div>
                        </div> </>: null
                }
            </>

        );
    }
}

export default QuadsAdReport;
