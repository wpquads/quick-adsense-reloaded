import React, {Component} from 'react';
import '../ads/create/QuadsAdListCreate.scss';
import '../../components/report/QuadsAdReport.scss'
import {Chart} from 'react-charts'

class QuadsAdReport extends Component {

    constructor(props) {
        super(props);
        this.state = {
            redirect:false,
            adsense_modal :false,
            current_page : 'report',
            isLoading : false,
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
            method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            //make sure to serialize your JSON body
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(
                (response) => {
                    if ( response.status && true === response.status ) {
                        if ( response['adsense_id'] ) {
                            this.closeModal();
                        } else {
                            const {report} = this.state;
                            report['adsense_report_errors'] =   JSON.stringify( response );
                            this.setState({ report });
                        }
                    } else {
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
                (response) => {
                    if ( true === response.adsense.status && response.adsense.account_id ) {
                        const {report} = this.state;
                        report['adsense_code_view'] =  true;
                        this.setState({ report,adsense_pub_id:response.adsense.account_id  });
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

            default:
                break;
        }

        type_img.push(<img  src={img_url} />);

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
    quads_adsense_report  =(pub_id='') =>{
        const {report} = this.state;
        if(pub_id == ''){
            pub_id =this.state.adsense_pub_id;
        }else{
            this.setState({ adsense_pub_id: pub_id,current_page:'adsense_report_page'});
        }
        // this.setState({isLoading:true});

        let report_type =report.report_type;
        let report_period =report.report_period;
        let url = quads_localize_data.rest_url + 'quads-adsense/get_report_adsense';
        // return true;
        fetch(url,{
            method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            body: JSON.stringify({'account_id':pub_id,'report_type':report_type,'report_period':report_period})
            //make sure to serialize your JSON body
        })
            .then(res => res.json())
            .then(
                (response) => {
                    const {report} = this.state;

                    let data_array_desktop =[];
                    let data_array_tablet =[];
                    let data_array_mobile = [];
                    let data_array = [];
                    let arr =[];
                    if(report_type == 'earning') {
                        response.map(item => {
                            data_array.push({primary: new Date(item[0]), secondary: item[1]});
                        });
                        arr = [{
                            label: 'Earnings',
                            data: data_array
                        }];
                        report['adsense_code_data']=arr;
                        this.setState({report:report,isLoading:false});
                    }else if(report_type == 'earning_forcast') {
                        let total_amount = 0;
                        let data_array = [];
                        response.map(item => {
                            total_amount += parseFloat(item[1]);
                        });
                        let avg = total_amount/response.length;

                        for (let i=1;i<=report.input_based;i++){
                            let date = new Date();
                            date.setDate(date.getDate() + i);
                            console.log(date);

                            data_array.push({primary: date, secondary: avg});
                        }
                        console.log(data_array);

                        arr = [{
                            label: 'Earnings',
                            data: data_array
                        }];
                        report['adsense_code_data']=arr;
                        this.setState({report:report,isLoading:false});
                    }else
                    if(report_type == 'top_device_type') {
                        response.map(item => {
                            if (item[1] == 'Desktop') {
                                data_array_desktop.push({primary: new Date(item[0]), secondary: item[2]});
                            } else if (item[1] == 'Tablet') {
                                data_array_tablet.push({primary: new Date(item[0]), secondary: item[2]});
                            } else if (item[1] == 'HighEndMobile') {
                                data_array_mobile.push({primary: new Date(item[0]), secondary: item[2]});
                            }
                        });
                        arr = [{
                            label: 'Desktop',
                            data: data_array_desktop
                        }, {
                            label: 'Tablet',
                            data: data_array_tablet
                        }, {
                            label: 'HighEndMobile',
                            data: data_array_mobile
                        }];
                        report['adsense_code_data']=arr;
                        this.setState({report:report,isLoading:false});
                    }else{
                        report['adsense_code_data']=arr;
                        this.setState({isLoading:false});
                    }

                });
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
            report['adsense_code_view'] =  value;
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
        console.log(pub_id);
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
                    if ( true === response.adsense.status && response.adsense.account_id ) {
                        const {report} = this.state;
                        report['adsense_code_view'] =  true;
                        this.setState({ report,adsense_pub_id:response.adsense.account_id  });
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

        return (
            <>
                {this.state.isLoading ? <div className="quads-cover-spin"></div>
                : null}
                { this.state.current_page == 'report' ?
                    <div className="quads-full-page-modal">
                        <div className="quads-full-page-modal-content"><h3>{__('Reports', 'quick-adsense-reloaded')}</h3>
                            <div className="quads-ad-networks">
                                <ul>
                                    {this.state.All_report_list.map(item => (
                                        <li  data-adtype={item.ad_type} key={item.ad_type}><a className="quads-nav-link" id={item.id} onClick={() => this.quads_adsense_report(this.state.adsense_pub_id)} >{this.getImageByAdType(item.ad_type)}<div><strong>{item.ad_type_name}</strong></div></a>
                                                <div className={'view_report'} >
                                                 <label htmlFor="quads-connect-adsense">{report.adsense_code_view ?'Active': 'InActive' }</label>

                                                        <label className="quads-switch">
                                                            <input id="quads-connect-adsense" className={report.adsense_code_view ?'disabled_adsense_link': '' } type="checkbox" name="adsense_code_view" onChange={this.report_formChangeHandler} checked={report.adsense_code_view} />
                                                            <span className="quads-slider"></span>
                                                        </label>
                                                </div>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    </div> : ''
                }
                {this.state.current_page =='adsense_report_page' ?
                    <div className="quads-full-page-modal">

                        <div className="quads-full-page-modal-content">
                            <nav aria-label="breadcrumb">
                                <ol className="breadcrumb">
                                    <li className="breadcrumb-item" onClick={() => this.change_page('report') }><a >Report</a></li>
                                    <li className="breadcrumb-item active" aria-current="page">Adsense Report</li>
                                </ol>
                            </nav>
                            <div className="quads-ad-networks">

                                <div className="quads-large-popup-title">
                                    <h1>Adsense Reports
                                    </h1>
                                </div>
                                {/*<div className="quads-large-description"></div>*/}

                                {/*<div className="quads-large-content">*/}
                                    <select  name="report_type" onChange={this.report_formChangeHandler} >
                                        <option value="earning">Earnings</option>
                                        <option value="earning_forcast">Earnings Forcast</option>
                                        <option value="top_adunit">Top Performs Adunits</option>
                                        <option value="top_country">Top Paying country</option>
                                        <option value="top_device_type">Top Earning Device type</option>
                                    </select>
                                {this.state.report.report_type != 'earning_forcast' ?
                                    <select name="report_period" onChange={this.report_formChangeHandler}>
                                        <option value="last_7days">Last 7days</option>
                                        <option value="last_15days">Last 15days</option>
                                        <option value="last_30days">Last 30days</option>
                                        <option value="last_6months">Last 6months</option>
                                        <option value="last_1year">Last 1year</option>
                                    </select>
                               : ''}
                                {this.state.report.report_type == 'earning_forcast' ?
                                  <div>  revenue prediction based on<select name="report_period" onChange={this.report_formChangeHandler}>
                                        <option value="last_7days">Last 7days</option>
                                        <option value="last_15days">Last 15days</option>
                                        <option value="last_30days">Last 30days</option>
                                        <option value="last_6months">Last 6months</option>
                                        <option value="last_1year">Last 1year</option>
                                    </select> for <input id={'input_based'} defaultValue={'7'} name={'input_based'} onChange={this.report_formChangeHandler} />
                                  </div>
                                    : ''}
                                    <div className={'chart_main_class'} style={{
                                        width: "400px",
                                        height: "300px"
                                    }}
                                    >
                                        <Chart data={this.state.report.adsense_code_data} axes={axes} series={series} tooltip />
                                    </div>
                                {/*</div>*/}

                            </div>
                        </div>
                    </div>: null
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
