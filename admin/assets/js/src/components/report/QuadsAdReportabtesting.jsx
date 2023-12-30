import React, {Component, Fragment} from 'react';
import '../ads/create/QuadsAdListCreate.scss';
import '../../components/report/QuadsAdReport.scss'
import {Chart} from 'react-charts'
import DatePicker from "react-datepicker";

import "react-datepicker/dist/react-datepicker.css";

class QuadsAdReportabtesting extends Component {

    constructor(props) {
        super(props);
        this.state = {
            redirect:false,
            adsense_modal :false,
            current_page : 'report',
            isLoading : false,
            cust_fromdate:new Date(),
            cust_todate:new Date(),
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
                response => {
                    if (  true == response.adsense.status && response.adsense.account_id.displayName ) {
                        const {report} = this.state;
                        report['adsense_code_view'] =  true;
                        this.setState({ report,adsense_pub_id:response.adsense.account_id.displayName  });
                    }
                });
    }
    
    closeModal =() =>{
        this.setState({adsense_modal:false,current_page:'report'});
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
              
                
                
                {this.state.adsense_modal ?
                    <>
                        <div className="quads-large-popup-bglayout">  </div>
                        <div className="quads-large-popup">
                            <div className="quads-large-popup-content">
                                <span className="quads-large-close" onClick={this.closeModal}>&times;</span>
                                <div className="quads-large-popup-title">
                                    <h1>{__('Please enter the confirmation code.','quick-adsense-reloaded')}
                                    </h1>
                                </div>
                                <div className="quads-large-description"></div>

                                <div className="quads-large-content">
                                      
                                    <a className="quads-btn quads-btn-primary quads-large-btn" onClick={this.adsense_submit}>{__('Submit code','quick-adsense-reloaded')}</a>
                                </div>
                            </div>
                        </div> </>: null
                }
            </>

        );
    }
}

export default QuadsAdReportabtesting;
