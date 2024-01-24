import React, { Component, Fragment } from 'react';
import Icon from '@material-ui/core/Icon';
import Select from "react-select";
import './QuadsUserTargeting.scss';

class QuadsUserTargeting extends Component {

    constructor(props) {
        super(props);
        this.state = {
            is_amp_endpoint_inc : false,
            is_amp_endpoint_exc : false,
            includedToggle : false,
            includedTextToggle : true,
            includedMainToggle : true,
            includedCustomTextToggle : false,
            excludedCustomTextToggle : false,
            excludedTextToggle : true,
            excludedMainToggle :true,
            excludedToggle  : false,
            includedRightPlaceholder: 'Select Targeting Data',
            excludedRightPlaceholder: 'Select Targeting Data',
            includedRightTextPlaceholder: 'Select Targeting Data',
            excludedRightTextPlaceholder: 'Select Targeting Data',
            TargetingConditionIncluded: "AND",
            multiTypeIncludedValue:[],
            multiTypeExcludedValue:[],

            multiTypeLeftIncludedValue:[],
            multiTypeRightIncludedValue:[],
            textTypeRightIncludedValue:'',
            textTypeRightExcludedValue:'',

            multiTypeLeftExcludedValue:[],
            multiTypeRightExcludedValue:[],

            includedDynamicOptions:[],
            excludedDynamicOptions:[],

            multiTypeOptions : [
                {label:'Device Type', value:'device_type'},
                {label:'Browser Language', value:'browser_language'},
                {label:'Logged In', value:'logged_in'},
                {label:'User Agent', value:'user_agent'},
                {label:'User Role', value:'user_type'},
                {label:'Country', value:'geo_location_country'},
                {label:'City', value:'geo_location_city'},
                {label:'State', value:'geo_location_state'},
                {label:'Cookie', value:'cookie'},
                {label:'URL Parameter ', value:'url_parameter'},
                {label:'Referring URL ', value:'referrer_url'},
                {label:'Browser Width', value:'browser_width'},
            ],
            multiTypeTargetOption : {
                device_type:[
                    {label:'Desktop', value:'desktop'},
                    {label:'Mobile', value:'mobile'},
                    {label:'Tablet', value:'tablet'}
                ],
                browser_width:[
                    {label:'Extra Small Devices (320px)', value:'320'},
                    {label:'Small Devices (600px)', value:'600'},
                    {label:'Medium Devices (768px)', value:'768'},
                    {label:'Large Devices (992px)', value:'992'},
                    {label:'Extra Large Devices (1200px)', value:'1200'},
                    {label:'Custom Width', value:'browser_width_custom'},
                ],
                browser_language:[
                    { value:'af', label: 'Afrikanns'}  ,
                    { value:'sq', label: 'Albanian'}  ,
                    { value:'ar', label: 'Arabic'}  ,
                    { value:'hy', label: 'Armenian'}  ,
                    { value:'eu', label: 'Basque'}  ,
                    { value:'bn', label: 'Bengali'}  ,
                    { value:'bg', label: 'Bulgarian'}  ,
                    { value:'ca', label: 'Catalan'}  ,
                    { value:'km', label: 'Cambodian'}  ,
                    { value:'zh', label: 'Chinese (Mandarin)'}  ,
                    { value:'hr', label: 'Croation'}  ,
                    { value:'cs', label: 'Czech'}  ,
                    { value:'da', label: 'Danish'}  ,
                    { value:'nl', label: 'Dutch'}  ,
                    { value:'en', label: 'English'}  ,
                    { value:'et', label: 'Estonian'}  ,
                    { value:'fj', label: 'Fiji'}  ,
                    { value:'fi', label: 'Finnish'}  ,
                    { value:'fr', label: 'French'}  ,
                    { value:'ka', label: 'Georgian'}  ,
                    { value:'de', label: 'German'}  ,
                    { value:'el', label: 'Greek'}  ,
                    { value:'gu', label: 'Gujarati'}  ,
                    { value:'he', label: 'Hebrew'}  ,
                    { value:'hi', label: 'Hindi'}  ,
                    { value:'hu', label: 'Hungarian'}  ,
                    { value:'is', label: 'Icelandic'}  ,
                    { value:'id', label: 'Indonesian'}  ,
                    { value:'ga', label: 'Irish'}  ,
                    { value:'it', label: 'Italian'}  ,
                    { value:'ja', label: 'Japanese'}  ,
                    { value:'jw', label: 'Javanese'}  ,
                    { value:'ko', label: 'Korean'}  ,
                    { value:'la', label: 'Latin'}  ,
                    { value:'lv', label: 'Latvian'}  ,
                    { value:'lt', label: 'Lithuanian'}  ,
                    { value:'mk', label: 'Macedonian'}  ,
                    { value:'ms', label: 'Malay'}  ,
                    { value:'ml', label: 'Malayalam'}  ,
                    { value:'mt', label: 'Maltese'}  ,
                    { value:'mi', label: 'Maori'}  ,
                    { value:'mr', label: 'Marathi'}  ,
                    { value:'mn', label: 'Mongolian'}  ,
                    { value:'ne', label: 'Nepali'}  ,
                    { value:'no', label: 'Norwegian'}  ,
                    { value:'fa', label: 'Persian'}  ,
                    { value:'pl', label: 'Polish'}  ,
                    { value:'pt', label: 'Portuguese'}  ,
                    { value:'pa', label: 'Punjabi'}  ,
                    { value:'qu', label: 'Quechua'}  ,
                    { value:'ro', label: 'Romanian'}  ,
                    { value:'ru', label: 'Russian'}  ,
                    { value:'sm', label: 'Samoan'}  ,
                    { value:'sr', label: 'Serbian'}  ,
                    { value:'sk', label: 'Slovak'}  ,
                    { value:'sl', label: 'Slovenian'}  ,
                    { value:'es', label: 'Spanish'}  ,
                    { value:'sw', label: 'Swahili'}  ,
                    { value:'sv', label: 'Swedish '}  ,
                    { value:'ta', label: 'Tamil'}  ,
                    { value:'tt', label: 'Tatar'}  ,
                    { value:'te', label: 'Telugu'}  ,
                    { value:'th', label: 'Thai'}  ,
                    { value:'bo', label: 'Tibetan'}  ,
                    { value:'to', label: 'Tonga'}  ,
                    { value:'tr', label: 'Turkish'}  ,
                    { value:'uk', label: 'Ukranian'}  ,
                    { value:'ur', label: 'Urdu'}  ,
                    { value:'uz', label: 'Uzbek'}  ,
                    { value:'vi', label: 'Vietnamese'}  ,
                    { value:'cy', label: 'Welsh'},
                    { value:'xh', label: 'Xhosa'}
                ],
                user_agent:[
                    { value:'opera', label: 'Opera'},
                    { value:'edge', label: 'Edge'},
                    { value:'chrome', label: 'Chrome'},
                    { value:'safari', label: 'Safari'},
                    { value:'firefox', label: 'Firefox'},
                    { value:'msie', label: 'MSIE'},
                    { value:'android', label: 'Android'},
                    { value:'iphone', label: 'iPhone'},
                    { value:'ipad', label: 'iPad'},
                    { value:'ipod', label: 'iPod'},
                ],
                multilingual_language:[
                    { value:'af', label: 'Afrikanns'}  ,
                    { value:'sq', label: 'Albanian'}  ,
                    { value:'ar', label: 'Arabic'}  ,
                    { value:'hy', label: 'Armenian'}  ,
                    { value:'eu', label: 'Basque'}  ,
                    { value:'bn', label: 'Bengali'}  ,
                    { value:'bg', label: 'Bulgarian'}  ,
                    { value:'ca', label: 'Catalan'}  ,
                    { value:'km', label: 'Cambodian'}  ,
                    { value:'zh', label: 'Chinese (Mandarin)'}  ,
                    { value:'hr', label: 'Croation'}  ,
                    { value:'cs', label: 'Czech'}  ,
                    { value:'da', label: 'Danish'}  ,
                    { value:'nl', label: 'Dutch'}  ,
                    { value:'en', label: 'English'}  ,
                    { value:'et', label: 'Estonian'}  ,
                    { value:'fj', label: 'Fiji'}  ,
                    { value:'fi', label: 'Finnish'}  ,
                    { value:'fr', label: 'French'}  ,
                    { value:'ka', label: 'Georgian'}  ,
                    { value:'de', label: 'German'}  ,
                    { value:'el', label: 'Greek'}  ,
                    { value:'gu', label: 'Gujarati'}  ,
                    { value:'he', label: 'Hebrew'}  ,
                    { value:'hi', label: 'Hindi'}  ,
                    { value:'hu', label: 'Hungarian'}  ,
                    { value:'is', label: 'Icelandic'}  ,
                    { value:'id', label: 'Indonesian'}  ,
                    { value:'ga', label: 'Irish'}  ,
                    { value:'it', label: 'Italian'}  ,
                    { value:'ja', label: 'Japanese'}  ,
                    { value:'jw', label: 'Javanese'}  ,
                    { value:'ko', label: 'Korean'}  ,
                    { value:'la', label: 'Latin'}  ,
                    { value:'lv', label: 'Latvian'}  ,
                    { value:'lt', label: 'Lithuanian'}  ,
                    { value:'mk', label: 'Macedonian'}  ,
                    { value:'ms', label: 'Malay'}  ,
                    { value:'ml', label: 'Malayalam'}  ,
                    { value:'mt', label: 'Maltese'}  ,
                    { value:'mi', label: 'Maori'}  ,
                    { value:'mr', label: 'Marathi'}  ,
                    { value:'mn', label: 'Mongolian'}  ,
                    { value:'ne', label: 'Nepali'}  ,
                    { value:'no', label: 'Norwegian'}  ,
                    { value:'fa', label: 'Persian'}  ,
                    { value:'pl', label: 'Polish'}  ,
                    { value:'pt', label: 'Portuguese'}  ,
                    { value:'pa', label: 'Punjabi'}  ,
                    { value:'qu', label: 'Quechua'}  ,
                    { value:'ro', label: 'Romanian'}  ,
                    { value:'ru', label: 'Russian'}  ,
                    { value:'sm', label: 'Samoan'}  ,
                    { value:'sr', label: 'Serbian'}  ,
                    { value:'sk', label: 'Slovak'}  ,
                    { value:'sl', label: 'Slovenian'}  ,
                    { value:'es', label: 'Spanish'}  ,
                    { value:'sw', label: 'Swahili'}  ,
                    { value:'sv', label: 'Swedish '}  ,
                    { value:'ta', label: 'Tamil'}  ,
                    { value:'tt', label: 'Tatar'}  ,
                    { value:'te', label: 'Telugu'}  ,
                    { value:'th', label: 'Thai'}  ,
                    { value:'bo', label: 'Tibetan'}  ,
                    { value:'to', label: 'Tonga'}  ,
                    { value:'tr', label: 'Turkish'}  ,
                    { value:'uk', label: 'Ukranian'}  ,
                    { value:'ur', label: 'Urdu'}  ,
                    { value:'uz', label: 'Uzbek'}  ,
                    { value:'vi', label: 'Vietnamese'}  ,
                    { value:'cy', label: 'Welsh'},
                    { value:'xh', label: 'Xhosa'}
                ],
                user_type:[
                    { value:'administrator', label: 'Administrator'},
                    { value:'editor', label: 'Editor'},
                    { value:'author', label: 'Author'},
                    { value:'contributor', label: 'Contributor'},
                    { value:'subscriber', label: 'Subscriber'}

                ],
                logged_in:[
                    { value:'true', label: 'True'},
                    { value:'false', label: 'False'},
                ],

            },
        };
    }
    static getDerivedStateFromProps(props, state) {

        if(!state.includedToggle && !state.excludedToggle){
            return {
                multiTypeIncludedValue: props.parentState.quads_post_meta.targeting_include,
                multiTypeExcludedValue: props.parentState.quads_post_meta.targeting_exclude,
            };
        }else{
            return null;
        }

    }

    handleMultiIncludedLeftChange = (option) => {
        let type = this.state.multiTypeTargetOption[option.value];
        let self =this;
        if( !quads_localize_data.is_pro && (option.value==='geo_location_country' || option.value==='geo_location_city' ||option.value==='geo_location_state')){
            this.setState({includedMainToggle:false});
            return;
        }else{
            this.setState({includedMainToggle:true});
        }
        var placeholder = 'Search for ' + option.label;

        if(option.value==='cookie' || option.value==='url_parameter' || option.value==='referrer_url' || option.value==='geo_location_city'|| option.value==='geo_location_state'){
            placeholder = 'Enter your ' + option.label;
            if(option.value==='geo_location_city'){
                placeholder = 'Add City name';
            }
            if(option.value==='geo_location_state'){
                placeholder = 'Add State name';
            }
            this.setState({includedTextToggle:false});
            this.setState({multiTypeLeftIncludedValue:option, includedDynamicOptions:type, textTypeRightIncludedValue:'', includedRightPlaceholder:placeholder});
        }else{
            if(option.value=='browser_width'){
                this.setState({is_amp_endpoint_inc:true});
            }else{
                this.setState({is_amp_endpoint_inc:false});
            }
            if(option.value==='geo_location_country'){

                const response =  fetch(
                    quads_localize_data.quads_pro_plugin_url+'includes/admin/geo_location_country_code.json', {headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': quads_localize_data.nonce,
                        }}
                )  .then(res => res.json())    .then(function(result) {

                    type =  result.geo_location_country;

                    self.setState({includedTextToggle:true});
                    self.setState({multiTypeLeftIncludedValue:option, includedDynamicOptions:type, multiTypeRightIncludedValue:[], includedRightPlaceholder:placeholder});
                });

            }else{
                this.setState({includedTextToggle:true});
                this.setState({multiTypeLeftIncludedValue:option, includedDynamicOptions:type, multiTypeRightIncludedValue:[], includedRightPlaceholder:placeholder});
            }

        }

    }
    handleMultiExcludedLeftChange = (option) => {
        let type = this.state.multiTypeTargetOption[option.value];
        let self =this;
        if( !quads_localize_data.is_pro && (option.value==='geo_location_country' || option.value==='geo_location_city' ||option.value==='geo_location_state')){
            this.setState({excludedMainToggle:false});
            return;
        }else{
            this.setState({excludedMainToggle:true});
        }
        var placeholder = 'Search for ' + option.label;
        if(option.value==='cookie' || option.value==='url_parameter' || option.value==='referrer_url' || option.value==='geo_location_city' ||option.value==='geo_location_state'){
            placeholder = 'Enter your ' + option.label;
            if(option.value==='geo_location_city'){
                placeholder = 'Add City name';
            }
            if(option.value==='geo_location_state'){
                placeholder = 'Add State name';
            }
            this.setState({excludedTextToggle:false});
            this.setState({multiTypeLeftExcludedValue:option, excludedDynamicOptions:type, textTypeRightExcludedValue:'', excludedRightPlaceholder:placeholder});
        }else{
            if(option.value=='browser_width'){
                this.setState({is_amp_endpoint_exc:true});
            }else{
                this.setState({is_amp_endpoint_exc:false});
            }if(option.value==='geo_location_country'){

                const response =  fetch(
                    quads_localize_data.quads_pro_plugin_url+'includes/admin/geo_location_country_code.json', {headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': quads_localize_data.nonce,
                        }}
                )  .then(res => res.json())    .then(function(result) {

                    type =  result.geo_location_country;

                    self.setState({excludedTextToggle:true});
                    self.setState({multiTypeLeftExcludedValue:option, excludedDynamicOptions:type, multiTypeRightExcludedValue:[], excludedRightPlaceholder:placeholder});
                });

            }else{
                this.setState({excludedTextToggle:true});
                this.setState({multiTypeLeftExcludedValue:option, excludedDynamicOptions:type, multiTypeRightExcludedValue:[], excludedRightPlaceholder:placeholder});
            }

        }
    }
// new condition
    removeExcluded_con = (e) => {
        let index = e.currentTarget.dataset.index;
        const {multiTypeExcludedValue} = this.state;
        let data = multiTypeExcludedValue;
        let lastArray = multiTypeExcludedValue[multiTypeExcludedValue.length - 1];
        multiTypeExcludedValue.splice(-1, 1);
        lastArray['condition'] = '';
        data.push(lastArray);
        let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);
        this.setState({multiTypeExcludedValue: newData});
    }
    removeIncluded_con = (e) => {
        let index = e.currentTarget.dataset.index;
        const {multiTypeIncludedValue} = this.state;
        let data = multiTypeIncludedValue;
        let lastArray = multiTypeIncludedValue[multiTypeIncludedValue.length - 1];
        multiTypeIncludedValue.splice(-1, 1);
        lastArray['condition'] = '';
        data.push(lastArray);
        let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);
        this.setState({multiTypeIncludedValue: newData});
    }
    TargetingConditionIncluded = (option) => {
        this.setState({TargetingConditionIncluded: option.value});
    }
    TargetingConditionExcluded = (option) => {
        this.setState({TargetingConditionExcluded: option.value});
    }
    addIncluded_condition = (e) => {
        e.preventDefault();
        let value = this.state.TargetingConditionIncluded;
        if (typeof (value) !== 'undefined') {
            const {multiTypeIncludedValue} = this.state;
            let data = multiTypeIncludedValue;
            let lastArray = multiTypeIncludedValue[multiTypeIncludedValue.length - 1];
            multiTypeIncludedValue.splice(-1, 1);
            lastArray['condition'] = value;
            data.push(lastArray);
            let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);
            this.setState({multiTypeIncludedValue: newData, includedToggleCondition: false});
        }
    }
    addExcluded_condition = (e) => {
        e.preventDefault();
        let value = this.state.TargetingConditionExcluded;
        if (typeof (value) !== 'undefined') {
            const {multiTypeExcludedValue} = this.state;
            let data = multiTypeExcludedValue;
            let lastArray = multiTypeExcludedValue[multiTypeExcludedValue.length - 1];
            multiTypeExcludedValue.splice(-1, 1);
            lastArray['condition'] = value;
            data.push(lastArray);
            let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);
            this.setState({multiTypeExcludedValue: newData, excludeToggleCondition: false});
        }
    }
    removeExcluded = (e) => {
        let index = e.currentTarget.dataset.index;
        const {multiTypeExcludedValue} = {...this.state};
        multiTypeExcludedValue.splice(index, 1);
        this.setState(multiTypeExcludedValue);
    }

    handleCustomIncludedRightChange = (option) =>{
        this.setState({includedCustomTextToggle:true});
        this.setState({textTypeRightIncludedValue:option.target.value});
    }
    handleCustomExcludedRightChange = (option) =>{
        this.setState({excludedCustomTextToggle:true});
        this.setState({textTypeRightExcludedValue:option.target.value});
    }
    handleMultiIncludedRightChange = (option) => {

        this.setState({multiTypeRightIncludedValue:option});
    }
    handleMultiIncludedRightChange = (option) => {
        let type  = this.state.multiTypeLeftIncludedValue;
        if(type.value==='cookie' || type.value==='url_parameter' || type.value==='referrer_url'  || type.value==='geo_location_city' || type.value==='geo_location_state' ){
            this.setState({textTypeRightIncludedValue:option.target.value});
        }else{
            this.setState({multiTypeRightIncludedValue:option});
            if(option.value=='browser_width_custom'){
                let placeholder = 'Enter your ' + option.label;
                this.setState({includedRightTextPlaceholder:placeholder});
                this.setState({includedCustomTextToggle:true});
            }else{
                this.setState({includedCustomTextToggle:false});
            }
        }
    }
    handleMultiExcludedRightChange = (option) => {
        let type  = this.state.multiTypeLeftExcludedValue;
        if(type.value=='cookie' || type.value==='url_parameter' || type.value==='referrer_url'|| type.value==='geo_location_city' || type.value==='geo_location_state'){
            this.setState({textTypeRightExcludedValue:option.target.value});
        }else{
            this.setState({multiTypeRightExcludedValue:option});
            if(option.value=='browser_width_custom'){
                let placeholder = 'Enter your ' + option.label;
                this.setState({excludedRightTextPlaceholder:placeholder});
                this.setState({excludedCustomTextToggle:true});
            }else{
                this.setState({excludedCustomTextToggle:false});
            }
        }
    }
    addIncluded = (e) => {

        e.preventDefault();

        let type  = this.state.multiTypeLeftIncludedValue;
        var value = this.state.multiTypeRightIncludedValue;
        if((type.value==='cookie' || type.value==='url_parameter' || type.value==='referrer_url') ||(value=='' || (typeof (value.value) !== 'undefined' && value.value=="browser_width_custom"))){
            var text_data = this.state.textTypeRightIncludedValue;
            value = {value:text_data,label:text_data};
        }
        if( typeof (value.value) !== 'undefined'){
            const {multiTypeIncludedValue} = this.state;
            let data    = multiTypeIncludedValue;
            data.push({type: type, value: value, condition: ''});
            let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);
            this.setState({multiTypeIncludedValue: newData,includedToggle : false,textTypeRightIncludedValue:'',multiTypeRightIncludedValue:[],includedCustomTextToggle:false,includedTextToggle:true, includedRightPlaceholder: 'Select Targeting Data',is_amp_endpoint_inc:false});
        }

    }
    addExcluded = (e) => {

        e.preventDefault();

        let type  = this.state.multiTypeLeftExcludedValue;
        var value = this.state.multiTypeRightExcludedValue;
        if((type.value==='cookie' || type.value==='url_parameter' || type.value==='referrer_url') ||(value=='' || (typeof (value.value) !== 'undefined' && value.value=="browser_width_custom"))){
            var text_data = this.state.textTypeRightExcludedValue;
            value = {value:text_data,label:text_data};
        }
        if( typeof (value.value) !== 'undefined'){
            const {multiTypeExcludedValue} = this.state;
            let data    = multiTypeExcludedValue;
            data.push({type: type, value: value, condition: ''});
            let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);
            this.setState({multiTypeExcludedValue: newData,excludedToggle : false,textTypeRightExcludedValue:'',multiTypeRightExcludedValue:[],excludedCustomTextToggle:false,excludedTextToggle:true,excludedRightPlaceholder: 'Select Targeting Data',is_amp_endpoint_inc:false});
        }

    }
    removeIncluded = (e) => {
        let index = e.currentTarget.dataset.index;
        const { multiTypeIncludedValue } = { ...this.state };
        multiTypeIncludedValue.splice(index,1);
        this.setState(multiTypeIncludedValue);

    }
    includedToggle = () => {
        const {multiTypeIncludedValue} = this.state;
        let data = multiTypeIncludedValue;
        let lastArray = multiTypeIncludedValue[multiTypeIncludedValue.length - 1];
        if (typeof (lastArray) !== 'undefined' && (typeof (lastArray['condition']) === 'undefined' || lastArray['condition'] == '')) {
            this.setState({includedToggleCondition: !this.state.includedToggle});
        } else {
            this.setState({includedToggle: !this.state.includedToggle});
        }
    }

    excludedToggle = () => {
        const {multiTypeExcludedValue} = this.state;
        let data = multiTypeExcludedValue;
        let lastArray = multiTypeExcludedValue[multiTypeExcludedValue.length - 1];
        if (typeof (lastArray) !== 'undefined' && (typeof (lastArray['condition']) === 'undefined' || lastArray['condition'] == '')) {
            this.setState({excludeToggleCondition: !this.state.excludedToggle});
        } else {
            this.setState({excludedToggle: !this.state.excludedToggle});
        }
    }

    componentDidMount(){
        const wpml_activation = quads_localize_data.wpml_activation
        const multiTypeOptions = this.state.multiTypeOptions

        if( wpml_activation == 1 ){
            multiTypeOptions.push( { label:'Language', value:'multilingual_language' } )
            this.setState({
                multiTypeOptions:multiTypeOptions
            })
        }
    }

    componentDidUpdate (){
        const include = this.state.multiTypeIncludedValue;
        const exclude = this.state.multiTypeExcludedValue;
        if(include.length > 0 || exclude.length > 0){
            this.props.updateVisitorTarget(include, exclude);
        }

    }


    render() {

        const {__} = wp.i18n;
        const show_form_error = this.props.parentState.show_form_error;
        let  validation_flag = false;
        if(!quads_localize_data.is_pro ){
            this.state.multiTypeIncludedValue.map( (item, index) => {
                if(item.type.value == "geo_location_country"){
                    validation_flag = true;
                }
            } )
            this.state.multiTypeExcludedValue.map( (item, index) => {
                if(item.type.value == "geo_location_country"){
                    validation_flag = true;
                }
            } )
        }
        const colorStyles = {
            placeholder: defaultStyles => {
                return {
                    ...defaultStyles,
                    color: "#333"
                };
            }
        };
        return (
            <div className="quads-settings-group quads-targeting">
                <div className="quads-title">{__('Targeting', 'quick-adsense-reloaded')}</div>
                <div className="quads-panel">
                    <div className="quads-panel-body">
                        <div className="quads-user-targeting-label">
                            <b>{__('When','quick-adsense-reloaded')}</b>  {__(' should the ad display?', 'quick-adsense-reloaded')}
                        </div>

                        <div className="quads-user-targeting">
                            <h2> {__('Included On','quick-adsense-reloaded')} <a onClick={this.includedToggle}><Icon>add_circle</Icon></a>  </h2>


                            <div className="quads-target-item-list">
                                {
                                    this.state.multiTypeIncludedValue ?
                                        this.state.multiTypeIncludedValue.map( (item, index,arr) => (
                                            <>
                                                <div key={index} className="quads-target-item">
                                                    <span className="quads-target-label">{item.type.label} - {item.value.label}</span>
                                                    <span className="quads-target-icon" onClick={this.removeIncluded} data-index={index}><Icon>close</Icon></span>
                                                </div>
                                                {item.condition && item.condition != '' ?
                                                    <div key={index + 'condition'} className="quads-target-item">
                                                        <span className="quads-target-label">{item.condition}</span>
                                                        {arr.length - 1 === index ?
                                                            <span className="quads-target-icon"
                                                                  onClick={this.removeIncluded_con}
                                                                  data-index={index}><Icon>close</Icon></span> : null}
                                                    </div> : null}
                                            </>
                                        ) )
                                        :''}
                            </div>

                            {this.state.includedToggleCondition ?
                                <div className="quads-targeting-selection">
                                    <table className="form-table">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <Select
                                                    name="TargetingConditionIncluded"
                                                    placeholder="Select Condition"
                                                    onChange={this.TargetingConditionIncluded}
                                                    options={[
                                                        {label: 'AND', value: 'AND'},
                                                        {label: 'OR', value: 'OR'},
                                                    ]}
                                                    styles={colorStyles}
                                                />
                                            </td>
                                            <td><a onClick={this.addIncluded_condition}
                                                   className="quads-btn quads-btn-primary">{__('Add','quick-adsense-reloaded')}</a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                : ''}
                            {this.state.includedToggle ?
                                <div className="quads-targeting-selection">
                                    <table className="form-table">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <Select
                                                    name="userTargetingIncludedType"
                                                    placeholder="Select Targeting Type"
                                                    options= {this.state.multiTypeOptions}
                                                    value  = {this.multiTypeLeftIncludedValue}
                                                    onChange={this.handleMultiIncludedLeftChange}
                                                    styles={colorStyles}
                                                />
                                                {this.state.is_amp_endpoint_inc?
                                                    <span className="amp-support">{__('AMP does not support Browser Width Targeting','quick-adsense-reloaded')}</span>
                                                    :''}
                                            </td>
                                            {this.state.includedMainToggle ? (
                                                <>
                                                    <td>
                                                        {this.state.includedTextToggle?
                                                            <Select
                                                                Clearable ={true}
                                                                name="userTargetingIncludedData"
                                                                placeholder={this.state.includedRightPlaceholder}
                                                                value={this.state.multiTypeRightIncludedValue}
                                                                options={this.state.includedDynamicOptions}
                                                                onChange={this.handleMultiIncludedRightChange}
                                                                styles={colorStyles}
                                                            />
                                                            :<input type="text"
                                                                    name="userTargetingIncludedData"
                                                                    placeholder={this.state.includedRightPlaceholder}
                                                                    value={this.state.textTypeRightIncludedValue}
                                                                    onChange={this.handleMultiIncludedRightChange}  />
                                                        }
                                                        {this.state.includedCustomTextToggle?
                                                            <input type="text"
                                                                   name="userTargetingIncludedData"
                                                                   placeholder={this.state.includedRightTextPlaceholder}
                                                                   value={this.state.textTypeRightIncludedValue}
                                                                   onChange={this.handleCustomIncludedRightChange}
                                                            />
                                                            :''}
                                                    </td>
                                                    <td><a onClick={this.addIncluded} className="quads-btn quads-btn-primary">Add</a></td>
                                                </>) :<><td className="targeting_get_pro">{__('This feature is available in PRO version','quick-adsense-reloaded')} </td><td><a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">{__('Unlock this feature','quick-adsense-reloaded')}</a> </td></>}
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                : ''}
                        </div>
                        <div className="quads-user-targeting">
                            <h2>Excluded On <a onClick={this.excludedToggle}><Icon>remove_circle</Icon></a>  </h2>
                            <div className="quads-target-item-list">
                                {
                                    this.state.multiTypeExcludedValue ?
                                        this.state.multiTypeExcludedValue.map( (item, index,arr) => (
                                            <>
                                                <div key={index} className="quads-target-item">
                                                    <span className="quads-target-label">{item.type.label} - {item.value.label}</span>
                                                    <span className="quads-target-icon" onClick={this.removeExcluded} data-index={index}><Icon>close</Icon></span>
                                                </div>
                                                {item.condition && item.condition != '' ?
                                                    <div key={index + 'condition'} className="quads-target-item">
                                                        <span className="quads-target-label">{item.condition}</span>
                                                        {arr.length - 1 === index ?
                                                            <span className="quads-target-icon"
                                                                  onClick={this.removeExcluded_con}
                                                                  data-index={index}><Icon>close</Icon></span> : null}
                                                    </div> : null}
                                            </>
                                        ) )
                                        :''}
                            </div>
                            {this.state.excludeToggleCondition ?
                                <div className="quads-targeting-selection">
                                    <table className="form-table">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <Select
                                                    name="TargetingConditionIncluded"
                                                    placeholder="Select Condition"
                                                    onChange={this.TargetingConditionExcluded}
                                                    options={[
                                                        {label: 'AND', value: 'AND'},
                                                        {label: 'OR', value: 'OR'},
                                                    ]}
                                                    styles={colorStyles}
                                                />
                                            </td>
                                            <td><a onClick={this.addExcluded_condition}
                                                   className="quads-btn quads-btn-primary">{__('Add','quick-adsense-reloaded')}</a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                : ''}
                            {this.state.excludedToggle ?
                                <div className="quads-targeting-selection">
                                    <table className="form-table">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <Select
                                                    name="userTargetingExcludedType"
                                                    placeholder="Select Targeting Type"
                                                    options= {this.state.multiTypeOptions}
                                                    value  = {this.multiTypeLeftExcludedValue}
                                                    onChange={this.handleMultiExcludedLeftChange}
                                                    styles={colorStyles}
                                                />
                                                {this.state.is_amp_endpoint_exc?
                                                    <span className="amp-support">{__('AMP does not support Browser Width Targeting','quick-adsense-reloaded')}</span>
                                                    :''}
                                            </td>
                                            {this.state.excludedMainToggle ? (
                                                <>
                                                    <td>

                                                        {this.state.excludedTextToggle ?
                                                            <Select
                                                                Clearable ={true}
                                                                name="userTargetingExcludedData"
                                                                placeholder={this.state.excludedRightPlaceholder}
                                                                value={this.state.multiTypeRightExcludedValue}
                                                                options={this.state.excludedDynamicOptions}
                                                                onChange={this.handleMultiExcludedRightChange}
                                                                styles={colorStyles}
                                                            />
                                                            :<input type="text"
                                                                    Clearable ={true}
                                                                    name="userTargetingExcludedData"
                                                                    placeholder={this.state.excludedRightPlaceholder}
                                                                    value={this.state.textTypeRightExcludedValue}
                                                                    onChange={this.handleMultiExcludedRightChange}  />
                                                        }

                                                        {this.state.excludedCustomTextToggle?
                                                            <input type="text"
                                                                   name="userTargetingIncludedData"
                                                                   placeholder={this.state.excludedRightTextPlaceholder}
                                                                   value={this.state.textTypeRightExcludedValue}
                                                                   onChange={this.handleCustomExcludedRightChange}
                                                            />
                                                            :''}
                                                    </td>
                                                    <td><a onClick={this.addExcluded} className="quads-btn quads-btn-primary">Add</a></td>
                                                </>) : <><td className="targeting_get_pro">{__('This feature is available in PRO version','quick-adsense-reloaded')}</td><td><a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">{__('Unlock this feature','quick-adsense-reloaded')}</a> </td></>}
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                : ''}
                        </div>

                    </div>
                </div>
            </div>
        );
    }
}

export default QuadsUserTargeting;