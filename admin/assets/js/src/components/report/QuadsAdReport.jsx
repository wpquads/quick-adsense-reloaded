import React, {Component, Fragment} from 'react';
import Icon from '@material-ui/core/Icon';
import Select from "react-select";
import '../ads/create/QuadsAdListCreate.scss';
import '../../components/report/QuadsAdReport.scss'
import {Chart} from 'react-charts'
import DatePicker from "react-datepicker";
import queryString from 'query-string'

import "react-datepicker/dist/react-datepicker.css";

class QuadsAdReport extends Component {

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
            report_url:quads_localize_data.get_admin_url+'?page=quads-settings&path=reports',
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

    display_report_stats_main_report = (response) => {

        let imp_report = response.impressions
        let click_report = response.clicks
        let ad_day = response.ad_day
        let day_ranges_imp = response.individual_impr_day_counts
        let day_ranges_click = response.individual_click_day_counts
        let ad_imp_individual_dates = response.ad_imp_individual_dates

        if( ad_day === "last_7_days" ){
            let tot = document.getElementById("quads_report_table_total")
                tot.style.display = "none";

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
                      borderColor: 'rgb(63, 0, 15)',
                      tension: 0.1
                    }
                  ]

                  };
                }
        if( ad_day === "this_month" ){
            let tot = document.getElementById("quads_report_table_total")
                tot.style.display = "block";

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
                      borderColor: 'rgb(63, 0, 15)',
                      tension: 0.1
                    }
                  ]

                  };
                }
        if( ad_day === "last_month" ){
            let tot = document.getElementById("quads_report_table_total")
                tot.style.display = "none";

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
                      borderColor: 'rgb(63, 0, 15)',
                      tension: 0.1
                    }
                  ]

                  };
                }
                
        if( ad_day === "this_year" ){
            let tot = document.getElementById("quads_report_table_total")
                tot.style.display = "none";
                
            var imp_day_ranges_to_num = day_ranges_imp.map(Number)
            var click_day_ranges_to_num = day_ranges_click.map(Number)

            var data = {
                    labels: ['January','February','March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
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
                      borderColor: 'rgb(63, 0, 15)',
                      tension: 0.1
                    }
                  ]

                  };
            }

        if( ad_day === "all_time" ){
            let tot = document.getElementById("quads_report_table_total")
                tot.style.display = "none";
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
                      borderColor: 'rgb(63, 0, 15)',
                      tension: 0.1
                    }
                  ]

                  };
                }
        if( ad_day === 'custom' ){

            let tot = document.getElementById("quads_report_table_total")
                tot.style.display = "none";

            var imp_day_ranges_to_num = day_ranges_imp.map(Number)
            var click_day_ranges_to_num = day_ranges_click.map(Number)
            
            var size = Object.keys(imp_day_ranges_to_num).length;
            if(size == 1){
                imp_day_ranges_to_num[1] = imp_day_ranges_to_num[0];
            }else{
                imp_day_ranges_to_num = imp_day_ranges_to_num
            }

            var size2 = Object.keys(ad_imp_individual_dates).length;
            if(size2 == 1){
                ad_imp_individual_dates[1] = ad_imp_individual_dates[0];
            }
            else{
                ad_imp_individual_dates = ad_imp_individual_dates
            }

            var click_day_ranges_to_num = day_ranges_click.map(Number)
            var size3 = Object.keys(click_day_ranges_to_num).length;
            if(size3 == 1){
                click_day_ranges_to_num[1] = click_day_ranges_to_num[0];
            }else{
                click_day_ranges_to_num = click_day_ranges_to_num
            }

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
                      borderColor: 'rgb(63, 0, 15)',
                      tension: 0.1
                    }
                  ]

                  };
                }
            if( ad_day == "today" ){
                let tot = document.getElementById("quads_report_table_total")
                tot.style.display = "none";
                
                var data = {
                labels: [ad_imp_individual_dates,ad_imp_individual_dates],
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
                      borderColor: 'rgb(63, 0, 15)',
                      tension: 0.1
                    }
                  ]
                  }
                }
            if( ad_day == "yesterday" ){
                let tot = document.getElementById("quads_report_table_total")
                tot.style.display = "none";

                    var data = {
                        labels: [ad_imp_individual_dates,ad_imp_individual_dates],
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
                      borderColor: 'rgb(63, 0, 15)',
                      tension: 0.1
                    }
                  ]
                  }
                }
            const config = {
                type: 'line',
                data: data,
                options: {
                legend: {
                    position: 'bottom',
                }
            }
              };
        drawChart(config);
    
    }

    view_report_fromdate_main_report = (eve) => {
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
    
    view_report_todate_main_report = (event) => {
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

    get_data_dates_main_report = (eve) => {
        let date = ''
        let newdate = ''
        let day_val = ''

        let id_ = document.getElementById('view_stats_report').value
        newdate = document.getElementById('report_period').value
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
                    var render_data_total
                    var get_table = document.getElementById("quads_report_table")
                    var get_table_tot = document.getElementById("quads_report_table_total")
                    if(response.clicks == null || response.impressions == null ){
                        get_table.innerHTML = 'No data Found'
                    }
                    else{
                        
                        console.log(response);
                        this.display_report_stats_main_report(response)
                        var ad_day = response.ad_day

                        if( ad_day == "custom" ){
                            let pass_var
                            let pass_Date
                            if( ad_day == "all_time" || ad_day == "this_year" ){
                                pass_var = ['January','February','March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                                pass_Date = 'Month'
                            }
                            else{
                                pass_var = response.ad_imp_individual_dates
                                pass_Date = 'Date'
                            }

        render_data = `<table>
                        <tbody>
                        <tr>
                        <td><b>${pass_Date}</b></td>
                        </tr>
                        ${ pass_var.map( (item) =>  {
                            return `
                            <tr><td>${item}</td></tr>`
                        }).join('')
                        }
                        <tr><td>Total</td></tr>
                        </tbody>
                        </table>

                        <table>
                        <tbody>
                        <tr>
                        <td><b>Impressions</b></td>
                        </tr>

                        ${ response.individual_impr_day_counts.map( (item3, index3) =>  {
                            return `
                            <tr><td>${item3}</td></tr>`
                        } ).join('')
                        }
                        <tr><td>${response.impressions}</td></tr>
                        </tbody>
                        </table>
                        
                        <table>
                        <tbody>
                        <tr>
                        <td><b>Clicks</b></td>
                        </tr>

                        ${ response.individual_click_day_counts.map( (item2, index2) =>  {
                            return `
                            <tr><td>${item2}</td></tr>`
                        } ).join('')
                        }
                        <tr><td>${response.clicks}</td></tr>
                        </tbody>
                        </table>`;

                    }else{
                            render_data = "<table><tbody><tr><td><b>Impressions</b></td><td><b>Clicks</b></td></tr><tr><td>"+response.impressions+"</td><td>"+response.clicks+"</td></tr></tbody></table>"
                        }
                        get_table.innerHTML = render_data

                    }
            }
            } )

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
             
    display_report_stats = (response) => {

        var data_length = response.length;
        var dates_array = [];
        var data = [];
        // var report_view_type = document.getElementById('report_view_type').value;
        var New_date_formate = '';
        var week_total = 0;
        var weekname_flag = '';
        var flag = 0;
        var view_count = []; 
        var datasets = []; 
     

    datasets = [{
        label: '',
        backgroundColor: '',
        borderColor: '',
        display: 'none',
        data: data,
        fill: false,
    }];
    var config = {
        type: 'line',
        data: {
            labels: dates_array,
            datasets: datasets
        },
        options: {
            legend: {
                position: 'bottom',
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            responsive: true,
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label:function(tooltipItem, data){
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += '$'+tooltipItem.yLabel;
                        return label ;
                    }
                },
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Chart.js Line Chart'
                },

            },
            scales: {
                xAxes: {
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Month'
                    }
                },
                yAxes: {
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    }
                }
            }
        }
    };
drawChart(config);
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
                img_url = quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/adsense_logo_.png';
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
    view_stats_report_handler = () => {
        this.setState({
            current_page:'view_reports_stats'
        })
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

    view_report_stats_form_ChangeHandler_main_report_tab = (eve) => {
        let date = ''
        let newdate = ''
        let day_val = ''

        let id = document.getElementById('view_stats_report').value
        newdate = document.getElementById('report_period').value
        day_val = document.getElementById('report_period').value
        let qrt = document.getElementById("quads_report_table")
        let qrtt = document.getElementById("quads_report_table_total")
        let qrc = document.getElementById("quads_reports_canvas")
        if( day_val=='select_duration' || id=='select' ){
            qrt.style.display = "none";
            qrtt.style.display = "none";
            qrc.style.display = "none";
        }else{
            qrt.style.display = "flex";
            qrtt.style.display = "flex";
            qrc.style.display = "flex";
        }
       if( day_val!='custom' && day_val!='select_duration' && id!='select' ){
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
                    var render_data_total
                    var get_table = document.getElementById("quads_report_table")
                    var get_table_tot = document.getElementById("quads_report_table_total")
                    if(response.clicks == null || response.impressions == null ){
                        get_table.innerHTML = 'No data Found'
                    }
                    if( quads_localize_data.is_pro == undefined && response.ad_day == "this_year_free" || response.ad_day == "all_time_free" || response.ad_day == "custom_free" ){
                        let pro_notify_main = '<div id="pro_" class="pro_"><div id="quads_get_pro" style="font-size: 15px;color: #000;line-height: 50px;padding-left: 14px;">This feature is available in PRO version <a class="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">Upgrade to PRO to unlock the data in the Reports</a></div></div>'
                        let canva = document.getElementById('quads_reports_pro_notify_main')
                        get_table.innerHTML = ''
                        canva.innerHTML = pro_notify_main
                        let q_rc = document.getElementById("quads_reports_canvas")
                        q_rc.innerHTML = ''
                        let q_rtt = document.getElementById("quads_report_table_total")
                        q_rtt.innerHTML = ''
                    }
                    else{
                        console.log(response);
                        let pro_not = document.getElementById('quads_reports_pro_notify_main')
                        if(pro_not){
                            pro_not.innerHTML = ''
                        }
                        this.display_report_stats_main_report(response)
                        var ad_day = response.ad_day
                        let ad_imp_individual_dates = response.ad_imp_individual_dates

                        if( ad_day == "last_7_days" || ad_day == "last_month" || ad_day == "all_time" || ad_day == "this_year" ){
                            let pass_var
                            let pass_Date
                            if( ad_day == "this_year" ){
                                ad_imp_individual_dates = ['January','February','March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                                pass_Date = 'Month'
                            }
                            else if( ad_day == "all_time" ){ pass_Date = 'Year' }
                            else{
                                ad_imp_individual_dates = response.ad_imp_individual_dates
                                pass_Date = 'Date'
                            }

        render_data = `<table>
                        <tbody>
                        <tr>
                        <td><b>${pass_Date}</b></td>
                        </tr>
                        ${ ad_imp_individual_dates.map( (item) =>  {
                            return `
                            <tr><td>${item}</td></tr>`
                        }).join('')
                        }
                        <tr><td>Total</td></tr>
                        </tbody>
                        </table>

                        <table>
                        <tbody>
                        <tr>
                        <td><b>Impressions</b></td>
                        </tr>

                        ${ response.individual_impr_day_counts.map( (item3, index3) =>  {
                            return `
                            <tr><td>${item3}</td></tr>`
                        } ).join('')
                        }
                        <tr><td>${response.impressions}</td></tr>
                        </tbody>
                        </table>
                        
                        <table>
                        <tbody>
                        <tr>
                        <td><b>Clicks</b></td>
                        </tr>

                        ${ response.individual_click_day_counts.map( (item2, index2) =>  {
                            return `
                            <tr><td>${item2}</td></tr>`
                        } ).join('')
                        }
                        <tr><td>${response.clicks}</td></tr>
                        </tbody>
                        </table>`;
                    }
                    else if( ad_day == "this_month" ){
                        let pass_Date_ = 'Date'
                        render_data = `<table>
                        <tbody>
                        <tr>
                        <td><b>${pass_Date_}</b></td>
                        </tr>
                        ${ ad_imp_individual_dates.map( (item) =>  {
                            return `
                            <tr><td>${item}</td></tr>`
                        }).join('')
                        }

                        </tbody>
                        </table>

                        <table>
                        <tbody>
                        <tr>
                        <td><b>Impressions</b></td>
                        </tr>

                        ${ response.individual_impr_day_counts.map( (item3, index3) =>  {
                            return `
                            <tr><td>${item3}</td></tr>`
                        } ).join('')
                        }

                        </tbody>
                        </table>
                        
                        <table>
                        <tbody>
                        <tr>
                        <td><b>Clicks</b></td>
                        </tr>

                        ${ response.individual_click_day_counts.map( (item2, index2) =>  {
                            return `
                            <tr><td>${item2}</td></tr>`
                        } ).join('')
                        }

                        </tbody>
                        </table>`;

                        render_data_total = "<table><tbody><tr><td>Total</td><td>"+response.impressions+"</td><td>"+response.clicks+"</td></tr></tbody></table>"
                        get_table_tot.innerHTML = render_data_total
                    }
                    else{
                            render_data = "<table><tbody><tr><td><b>Impressions</b></td><td><b>Clicks</b></td></tr><tr><td>"+response.impressions+"</td><td>"+response.clicks+"</td></tr></tbody></table>"
                        }
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
                        this.display_report_stats_main_report(response)
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
                        <div>
                            <div className="quads-ad-networks-reports">
                                <ul key={'quads-ad-networks'}>
                                {quads_localize_data_is_pro ? this.state.All_report_list.map(item => (
                                    <li key={item.id} data-adtype={item.ad_type} id={item.id}><a className="quads-nav-link-reports" onClick={() => this.quads_adsense_report(this.state.adsense_pub_id)} >
                                            {this.getImageByAdType(item.ad_type)}
                                            {item.ad_type=='adsense' ? <div style={{color: "rgb(0, 90, 240)"}}>
                                            <p style={{ fontSize: "16px",fontWeight: "700",marginBottom: "11px" }}>Google Adsense</p>
                                            <p>{report.adsense_code_view ? 'View Report': 'Connect' }</p></div> : '' }
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

                                    
                                )) : ''}
                                { quads_localize_data_is_pro ? 
                                    <li data-adtype="abtesting" id="quads-adsense-abtesting" onClick={ () =>{
                                        this.abtesting_handler()
                                    } }  >
                                    <a className="quads-nav-linkforabtesting" >
                                    <img style={{marginTop: "20px"}} src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/ab.png'}/>
                                    </a>
                                    <div id="view_report_abtesting" style={{color: "#005af0"}} onClick={ () =>{
                                        this.abtesting_handler()
                                    } }>
                                    <p style={{ fontSize: "16px",fontWeight: "700",marginBottom: "11px" }}>A/B Testing</p>
                                    <p style={{ margin: "0",padding: "0" }}>View Report</p></div>
                                    </li>
                                    : '' }

                                    <li data-adtype="view_stats_report" id="quads-adsense-view_stats_report" onClick={ () =>{
                                        this.view_stats_report_handler()
                                    } }  >
                                    <a class="quads-nav-linkforview_stats_report">
                                    <img style={{marginTop: "20px"}} src={quads_localize_data.quads_plugin_url+'admin/assets/js/src/images/view_stats.png'}/>
                                    </a>
                                    <div id="view_report_view_stats_report" style={{color: "#005af0"}} onClick={ () =>{
                                        this.view_stats_report_handler()
                                    } }>
                                    <p style={{ fontSize: "16px",fontWeight: "700",marginBottom: "11px" }}>Ad Impressions/Count</p>
                                    <p>View Report</p>
                                    </div>
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
                            <li className="breadcrumb-item"><a style={{textDecoration: "unset"}} href={this.state.report_url}> Report</a></li>
                            <li className="breadcrumb-item active" aria-current="page">A/B Testing Report</li>
                        </ol>
                    </nav>
                    <div className="quads-report-networks">
                        <div className={'quads-select-menu'} >
                        <div className={'quads-select'} onClick={this.adsToggle_list}>
                        <select name="abtesting_report" id={'abtesting_report'} 
                        onChange={this.ab_testing_report_formChangeHandler} placeholder="Select Ads">
                        <option value="">Select Ad</option>
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
                
                { this.state.current_page =='view_reports_stats' ?
                    

                    <Fragment>
                    <div>
                        <nav aria-label="breadcrumb">
                        <ol className="breadcrumb">
                        
                            <li className="breadcrumb-item"><a style={{textDecoration: "unset"}}  href={this.state.report_url}> Report</a></li>
                            <li className="breadcrumb-item active" aria-current="page">Stats Report</li>
                        </ol>
                    </nav>
                    <div className="quads-report-networks">
                    <div className={'quads-select-menu'} >
                    <div className={'quads-select view_statsreport'} onClick={this.adsToggle_list}>
                    
                    <select name="view_stats_report" onChange={this.view_report_stats_form_ChangeHandler_main_report_tab} id={'view_stats_report'} placeholder="Select Ads">
                        <option value="select">Select Ad</option>
                        {this.state.getallads_data_temp ? this.state.getallads_data_temp.map( item => (
                            <option key={item.value} value={item.value}>{item.label}</option>
                        ) )
                        : 'No Options' }
                        </select>
                    <select name="report_period" id={'report_period'} onChange={this.view_report_stats_form_ChangeHandler_main_report_tab}>
                    <option value="select_duration">Select Duration</option>
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="last_7_days">Last Seven Days</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value={ quads_localize_data_is_pro ? "this_year" : "this_year_free"}>This Year</option>
                    <option value={ quads_localize_data_is_pro ? "all_time" : "all_time_free"}>All Time</option>
                    <option value={ quads_localize_data_is_pro ? "custom" : "custom_free"}>Custom</option>
                </select>
                
                { this.state.custom_period == true  ? <>
                    <DatePicker maxDate={(new Date())} selected={this.state.cust_fromdate} id={"cust_fromdate"} placeholderText="Start Date" dateFormat="dd/MM/yyyy" onChange={this.view_report_fromdate_main_report} />
                    <DatePicker maxDate={(new Date())} selected={this.state.cust_todate} id={"cust_todate"} placeholderText="End Date" dateFormat="dd/MM/yyyy" onChange={this.view_report_todate_main_report} />
                    <button className="show_btn" onClick={this.get_data_dates_main_report}>Show data</button>
                </> : '' 

                }
                    </div>
                    </div>
                    <div id='quads_reports_pro_notify_main' class='quads_reports_pro_notify_main' style={{marginTop: "20px"}}  ></div>
                    <div id='quads_reports_canvas' class='report_single' ></div>
                    <div id={'quads_report_table'}></div>
                    <div id={'quads_report_table_total'}
                    style={{ display: this.state.custom_period ? 'block' : ''}} >
                    </div>
                    </div>

                        </div>
                        </Fragment> : '' }
                        
                {this.state.current_page =='adsense_report_page' ?
                    <Fragment>
                        <div >
                            <nav aria-label="breadcrumb">
                                <ol className="breadcrumb">
                                    <li className="breadcrumb-item" onClick={() => this.change_page('report') }><a style={{textDecoration: "unset"}} href={this.state.report_url}>Report</a></li>
                                    <li className="breadcrumb-item active" aria-current="page">Adsense Report</li>
                                </ol>
                            </nav>
                            <div className="quads-report-networks">

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
                                            <select style={{marginTop: "20px"}} name="report_period" id={'report_period'} value={report.report_period} onChange={this.report_formChangeHandler}>
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
                                                <div>  revenue prediction based on<select name="report_period" style={{marginTop: "20px"}} id={'report_period'} value={report.report_period} onChange={this.report_formChangeHandler}>
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
