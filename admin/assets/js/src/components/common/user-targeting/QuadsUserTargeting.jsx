import React, { Component, Fragment } from 'react';
import Icon from '@material-ui/core/Icon';
import Select from "react-select";
import './QuadsUserTargeting.scss';

class QuadsUserTargeting extends Component {
  
  constructor(props) {
    super(props);

    this.state = {  
      includedToggle : false,
      includedTextToggle : true,
      excludedTextToggle : true,
      excludedToggle  : false,      
      includedRightPlaceholder: 'Select Targeting Data',
      excludedRightPlaceholder: 'Select Targeting Data',
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
        {label:'User Type', value:'user_type'},                
        {label:'Geo Location', value:'geo_location'},
        {label:'Cookie', value:'cookie'},
        {label:'URL Parameter ', value:'url_parameter'},
        {label:'Referring URL ', value:'referrer_url'},      
      ],
      multiTypeTargetOption : {
        device_type:[
          {label:'Desktop', value:'desktop'},
          {label:'Mobile', value:'mobile'},
          {label:'Tablet', value:'tablet'}
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
        geo_location:[
          {value :'AFG' , label : 'Afghanistan'},
          {value :'ALB' , label : 'Albania'},
          {value :'DZA' , label : 'Algeria'},
          {value :'ASM' , label : 'American Samoa'},
          {value :'AND' , label : 'Andorra'},
          {value :'AGO' , label : 'Angola'},
          {value :'AIA' , label : 'Anguilla'},
          {value :'ATA' , label : 'Antarctica'},
          {value :'ATG' , label : 'Antigua and Barbuda'},
          {value :'ARG' , label : 'Argentina'},
          {value :'ARM' , label : 'Armenia'},
          {value :'ABW' , label : 'Aruba'},
          {value :'AUS' , label : 'Australia'},
          {value :'AUT' , label : 'Austria'},
          {value :'AZE' , label : 'Azerbaijan'},
          {value :'BHS' , label : 'Bahamas'},
          {value :'BHR' , label : 'Bahrain'},
          {value :'BGD' , label : 'Bangladesh'},
          {value :'BRB' , label : 'Barbados'},
          {value :'BLR' , label : 'Belarus'},
          {value :'BEL' , label : 'Belgium'},
          {value :'BLZ' , label : 'Belize'},
          {value :'BEN' , label : 'Benin'},
          {value :'BMU' , label : 'Bermuda'},
          {value :'BTN' , label : 'Bhutan'},
          {value :'BOL' , label : 'Bolivia'},
          {value :'BIH' , label : 'Bosnia and Herzegovina'},
          {value :'BWA' , label : 'Botswana'},
          {value :'BRA' , label : 'Brazil'},
          {value :'IOT' , label : 'British Indian Ocean Territory'},
          {value :'VGB' , label : 'British Virgin Islands'},
          {value :'BRN' , label : 'Brunei'},
          {value :'BGR' , label : 'Bulgaria'},
          {value :'BFA' , label : 'Burkina Faso'},
          {value :'BDI' , label : 'Burundi'},
          {value :'KHM' , label : 'Cambodia'},
          {value :'CMR' , label : 'Cameroon'},
          {value :'CAN' , label : 'Canada'},
          {value :'CPV' , label : 'Cape Verde'},
          {value :'CYM' , label : 'Cayman Islands'},
          {value :'CAF' , label : 'Central African Republic'},
          {value :'TCD' , label : 'Chad'},
          {value :'CHL' , label : 'Chile'},
          {value :'CHN' , label : 'China'},
          {value :'CXR' , label : 'Christmas Island'},
          {value :'CCK' , label : 'Cocos Islands'},
          {value :'COL' , label : 'Colombia'},
          {value :'COM' , label : 'Comoros'},
          {value :'COK' , label : 'Cook Islands'},
          {value :'CRI' , label : 'Costa Rica'},
          {value :'HRV' , label : 'Croatia'},
          {value :'CUB' , label : 'Cuba'},
          {value :'CUW' , label : 'Curacao'},
          {value :'CYP' , label : 'Cyprus'},
          {value :'CZE' , label : 'Czech Republic'},
          {value :'COD' , label : 'Democratic Republic of the Congo'},
          {value :'DNK' , label : 'Denmark'},
          {value :'DJI' , label : 'Djibouti'},
          {value :'DMA' , label : 'Dominica'},
          {value :'DOM' , label : 'Dominican Republic'},
          {value :'TLS' , label : 'East Timor'},
          {value :'ECU' , label : 'Ecuador'},
          {value :'EGY' , label : 'Egypt'},
          {value :'SLV' , label : 'El Salvador'},
          {value :'GNQ' , label : 'Equatorial Guinea'},
          {value :'ERI' , label : 'Eritrea'},
          {value :'EST' , label : 'Estonia'},
          {value :'ETH' , label : 'Ethiopia'},
          {value :'FLK' , label : 'Falkland Islands'},
          {value :'FRO' , label : 'Faroe Islands'},
          {value :'FJI' , label : 'Fiji'},
          {value :'FIN' , label : 'Finland'},
          {value :'FRA' , label : 'France'},
          {value :'PYF' , label : 'French Polynesia'},
          {value :'GAB' , label : 'Gabon'},
          {value :'GMB' , label : 'Gambia'},
          {value :'GEO' , label : 'Georgia'},
          {value :'DEU' , label : 'Germany'},
          {value :'GHA' , label : 'Ghana'},
          {value :'GIB' , label : 'Gibraltar'},
          {value :'GRC' , label : 'Greece'},
          {value :'GRL' , label : 'Greenland'},
          {value :'GRD' , label : 'Grenada'},
          {value :'GUM' , label : 'Guam'},
          {value :'GTM' , label : 'Guatemala'},
          {value :'GGY' , label : 'Guernsey'},
          {value :'GIN' , label : 'Guinea'},
          {value :'GNB' , label : 'Guinea-Bissau'},
          {value :'GUY' , label : 'Guyana'},
          {value :'HTI' , label : 'Haiti'},
          {value :'HND' , label : 'Honduras'},
          {value :'HKG' , label : 'Hong Kong'},
          {value :'HUN' , label : 'Hungary'},
          {value :'ISL' , label : 'Iceland'},
          {value :'IND' , label : 'India'},
          {value :'IDN' , label : 'Indonesia'},
          {value :'IRN' , label : 'Iran'},
          {value :'IRQ' , label : 'Iraq'},
          {value :'IRL' , label : 'Ireland'},
          {value :'IMN' , label : 'Isle of Man'},
          {value :'ISR' , label : 'Israel'},
          {value :'ITA' , label : 'Italy'},
          {value :'CIV' , label : 'Ivory Coast'},
          {value :'JAM' , label : 'Jamaica'},
          {value :'JPN' , label : 'Japan'},
          {value :'JEY' , label : 'Jersey'},
          {value :'JOR' , label : 'Jordan'},
          {value :'KAZ' , label : 'Kazakhstan'},
          {value :'KEN' , label : 'Kenya'},
          {value :'KIR' , label : 'Kiribati'},
          {value :'XKX' , label : 'Kosovo'},
          {value :'KWT' , label : 'Kuwait'},
          {value :'KGZ' , label : 'Kyrgyzstan'},
          {value :'LAO' , label : 'Laos'},
          {value :'LVA' , label : 'Latvia'},
          {value :'LBN' , label : 'Lebanon'},
          {value :'LSO' , label : 'Lesotho'},
          {value :'LBR' , label : 'Liberia'},
          {value :'LBY' , label : 'Libya'},
          {value :'LIE' , label : 'Liechtenstein'},
          {value :'LTU' , label : 'Lithuania'},
          {value :'LUX' , label : 'Luxembourg'},
          {value :'MAC' , label : 'Macau'},
          {value :'MKD' , label : 'Macedonia'},
          {value :'MDG' , label : 'Madagascar'},
          {value :'MWI' , label : 'Malawi'},
          {value :'MYS' , label : 'Malaysia'},
          {value :'MDV' , label : 'Maldives'},
          {value :'MLI' , label : 'Mali'},
          {value :'MLT' , label : 'Malta'},
          {value :'MHL' , label : 'Marshall Islands'},
          {value :'MRT' , label : 'Mauritania'},
          {value :'MUS' , label : 'Mauritius'},
          {value :'MYT' , label : 'Mayotte'},
          {value :'MEX' , label : 'Mexico'},
          {value :'FSM' , label : 'Micronesia'},
          {value :'MDA' , label : 'Moldova'},
          {value :'MCO' , label : 'Monaco'},
          {value :'MNG' , label : 'Mongolia'},
          {value :'MNE' , label : 'Montenegro'},
          {value :'MSR' , label : 'Montserrat'},
          {value :'MAR' , label : 'Morocco'},
          {value :'MOZ' , label : 'Mozambique'},
          {value :'MMR' , label : 'Myanmar'},
          {value :'NAM' , label : 'Namibia'},
          {value :'NRU' , label : 'Nauru'},
          {value :'NPL' , label : 'Nepal'},
          {value :'NLD' , label : 'Netherlands'},
          {value :'ANT' , label : 'Netherlands Antilles'},
          {value :'NCL' , label : 'New Caledonia'},
          {value :'NZL' , label : 'New Zealand'},
          {value :'NIC' , label : 'Nicaragua'},
          {value :'NER' , label : 'Niger'},
          {value :'NGA' , label : 'Nigeria'},
          {value :'NIU' , label : 'Niue'},
          {value :'PRK' , label : 'North Korea'},
          {value :'MNP' , label : 'Northern Mariana Islands'},
          {value :'NOR' , label : 'Norway'},
          {value :'OMN' , label : 'Oman'},
          {value :'PAK' , label : 'Pakistan'},
          {value :'PLW' , label : 'Palau'},
          {value :'PSE' , label : 'Palestine'},
          {value :'PAN' , label : 'Panama'},
          {value :'PNG' , label : 'Papua New Guinea'},
          {value :'PRY' , label : 'Paraguay'},
          {value :'PER' , label : 'Peru'},
          {value :'PHL' , label : 'Philippines'},
          {value :'PCN' , label : 'Pitcairn'},
          {value :'POL' , label : 'Poland'},
          {value :'PRT' , label : 'Portugal'},
          {value :'PRI' , label : 'Puerto Rico'},
          {value :'QAT' , label : 'Qatar'},
          {value :'COG' , label : 'Republic of the Congo'},
          {value :'REU' , label : 'Reunion'},
          {value :'ROU' , label : 'Romania'},
          {value :'RUS' , label : 'Russia'},
          {value :'RWA' , label : 'Rwanda'},
          {value :'BLM' , label : 'Saint Barthelemy'},
          {value :'SHN' , label : 'Saint Helena'},
          {value :'KNA' , label : 'Saint Kitts and Nevis'},
          {value :'LCA' , label : 'Saint Lucia'},
          {value :'MAF' , label : 'Saint Martin'},
          {value :'SPM' , label : 'Saint Pierre and Miquelon'},
          {value :'VCT' , label : 'Saint Vincent and the Grenadines'},
          {value :'WSM' , label : 'Samoa'},
          {value :'SMR' , label : 'San Marino'},
          {value :'STP' , label : 'Sao Tome and Principe'},
          {value :'SAU' , label : 'Saudi Arabia'},
          {value :'SEN' , label : 'Senegal'},
          {value :'SRB' , label : 'Serbia'},
          {value :'SYC' , label : 'Seychelles'},
          {value :'SLE' , label : 'Sierra Leone'},
          {value :'SGP' , label : 'Singapore'},
          {value :'SXM' , label : 'Sint Maarten'},
          {value :'SVK' , label : 'Slovakia'},
          {value :'SVN' , label : 'Slovenia'},
          {value :'SLB' , label : 'Solomon Islands'},
          {value :'SOM' , label : 'Somalia'},
          {value :'ZAF' , label : 'South Africa'},
          {value :'KOR' , label : 'South Korea'},
          {value :'SSD' , label : 'South Sudan'},
          {value :'ESP' , label : 'Spain'},
          {value :'LKA' , label : 'Sri Lanka'},
          {value :'SDN' , label : 'Sudan'},
          {value :'SUR' , label : 'Suriname'},
          {value :'SJM' , label : 'Svalbard and Jan Mayen'},
          {value :'SWZ' , label : 'Swaziland'},
          {value :'SWE' , label : 'Sweden'},
          {value :'CHE' , label : 'Switzerland'},
          {value :'SYR' , label : 'Syria'},
          {value :'TWN' , label : 'Taiwan'},
          {value :'TJK' , label : 'Tajikistan'},
          {value :'TZA' , label : 'Tanzania'},
          {value :'THA' , label : 'Thailand'},
          {value :'TGO' , label : 'Togo'},
          {value :'TKL' , label : 'Tokelau'},
          {value :'TON' , label : 'Tonga'},
          {value :'TTO' , label : 'Trinidad and Tobago'},
          {value :'TUN' , label : 'Tunisia'},
          {value :'TUR' , label : 'Turkey'},
          {value :'TKM' , label : 'Turkmenistan'},
          {value :'TCA' , label : 'Turks and Caicos Islands'},
          {value :'TUV' , label : 'Tuvalu'},
          {value :'VIR' , label : 'U.S. Virgin Islands'},
          {value :'UGA' , label : 'Uganda'},
          {value :'UKR' , label : 'Ukraine'},
          {value :'ARE' , label : 'United Arab Emirates'},
          {value :'GBR' , label : 'United Kingdom'},
          {value :'USA' , label : 'United States'},
          {value :'URY' , label : 'Uruguay'},
          {value :'UZB' , label : 'Uzbekistan'},
          {value :'VUT' , label : 'Vanuatu'},
          {value :'VAT' , label : 'Vatican'},
          {value :'VEN' , label : 'Venezuela'},
          {value :'VNM' , label : 'Vietnam'},
          {value :'WLF' , label : 'Wallis and Futuna'},
          {value :'ESH' , label : 'Western Sahara'},
          {value :'YEM' , label : 'Yemen'},
          {value :'ZMB' , label : 'Zambia'},
          {value :'ZWE' , label : 'Zimbabwe'}
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
      var placeholder = 'Search for ' + option.label;
    
      if(option.value==='cookie' || option.value==='url_parameter' || option.value==='referrer_url'){
        placeholder = 'Enter your ' + option.label +' here';
        this.setState({includedTextToggle:false});
        this.setState({multiTypeLeftIncludedValue:option, includedDynamicOptions:type, textTypeRightIncludedValue:'', includedRightPlaceholder:placeholder});
      }else{
        this.setState({includedTextToggle:true});
        this.setState({multiTypeLeftIncludedValue:option, includedDynamicOptions:type, multiTypeRightIncludedValue:[], includedRightPlaceholder:placeholder});
      }
  }
  handleMultiExcludedLeftChange = (option) => {    
    let type = this.state.multiTypeTargetOption[option.value];         
     var placeholder = 'Search for ' + option.label;
      if(option.value==='cookie' || option.value==='url_parameter' || option.value==='referrer_url'){
         placeholder = 'Enter your ' + option.label +' here';
         this.setState({excludedTextToggle:false});
         this.setState({multiTypeLeftExcludedValue:option, excludedDynamicOptions:type, textTypeRightExcludedValue:'', excludedRightPlaceholder:placeholder});
      }else{
        this.setState({excludedTextToggle:true});
        this.setState({multiTypeLeftExcludedValue:option, excludedDynamicOptions:type, multiTypeRightExcludedValue:[], excludedRightPlaceholder:placeholder});
      }
}
handleMultiIncludedRightChange = (option) => {    
    let type  = this.state.multiTypeLeftIncludedValue;
    if(type.value=='cookie' || type.value==='url_parameter' || type.value==='referrer_url'){
      this.setState({textTypeRightIncludedValue:option.target.value});
    }else{
      this.setState({multiTypeRightIncludedValue:option});
    }
}
handleMultiExcludedRightChange = (option) => {    
  let type  = this.state.multiTypeLeftExcludedValue;
  if(type.value=='cookie' || type.value==='url_parameter' || type.value==='referrer_url'){
    this.setState({textTypeRightExcludedValue:option.target.value});
  }else{ 
    this.setState({multiTypeRightExcludedValue:option});
  }
}
addIncluded = (e) => {
    e.preventDefault();  
    let type  = this.state.multiTypeLeftIncludedValue;
    var value = this.state.multiTypeRightIncludedValue;
    if(value==''){
       var text_data = this.state.textTypeRightIncludedValue;
       value = {value:text_data,label:text_data};
    }
    if( typeof (value.value) !== 'undefined'){
      const {multiTypeIncludedValue} = this.state;
      let data    = multiTypeIncludedValue;
      data.push({type: type, value: value});
      let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);          
      this.setState({multiTypeIncludedValue: newData});       
    }        
  
}
addExcluded = (e) => {

  e.preventDefault();  

  let type  = this.state.multiTypeLeftExcludedValue;
  var value = this.state.multiTypeRightExcludedValue;
  if(value==''){
     var text_data = this.state.textTypeRightExcludedValue;
     value = {value:text_data,label:text_data};
  } 
  if( typeof (value.value) !== 'undefined'){
    const {multiTypeExcludedValue} = this.state;
    let data    = multiTypeExcludedValue;
    data.push({type: type, value: value});
    let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);    
    this.setState({multiTypeExcludedValue: newData});       
  }        

}
removeIncluded = (e) => {
      let index = e.currentTarget.dataset.index;  
      const { multiTypeIncludedValue } = { ...this.state };    
      multiTypeIncludedValue.splice(index,1);
      this.setState(multiTypeIncludedValue);

}
removeExcluded = (e) => {
  let index = e.currentTarget.dataset.index;  
  const { multiTypeExcludedValue } = { ...this.state };    
  multiTypeExcludedValue.splice(index,1);
  this.setState(multiTypeExcludedValue);

}
includedToggle = () => {
  
  this.setState({includedToggle:!this.state.includedToggle});
}
excludedToggle = () => {
  this.setState({excludedToggle:!this.state.excludedToggle});
}
  componentDidUpdate (){
    
    const include = this.state.multiTypeIncludedValue; 
    const exclude = this.state.multiTypeExcludedValue
    if(include.length > 0 || exclude.length > 0){
      this.props.updateVisitorTarget(include, exclude);
    }
    
  }
    

  render() {

    const {__} = wp.i18n; 
        
    return (
      <div className="quads-settings-group">
      <div>{__('Targeting', 'quick-adsense-reloaded')}</div>  
      <div className="quads-panel">
      <div className="quads-panel-body">                 
      <div class="quads-user-targeting-label">
          {__('When should the ad display?', 'quick-adsense-reloaded')}                     
      </div>

       <div className="quads-user-targeting"> 
       <h2>Included On <a onClick={this.includedToggle}><Icon>add_circle</Icon></a>  </h2>

                
             <div className="quads-target-item-list">
              {                
              this.state.multiTypeIncludedValue ? 
              this.state.multiTypeIncludedValue.map( (item, index) => (
                <div key={index} className="quads-target-item">
                  <span className="quads-target-label">{item.type.label} - {item.value.label}</span>
                  <span className="quads-target-icon" onClick={this.removeIncluded} data-index={index}><Icon>close</Icon></span> 
                </div>
               ) )
              :''}
             </div>             
        

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
            />             
           </td>
           <td>
           {this.state.includedTextToggle?
            <Select       
              Clearable ={true}      
              name="userTargetingIncludedData"
              placeholder={this.state.includedRightPlaceholder}
              value={this.state.multiTypeRightIncludedValue}
              options={this.state.includedDynamicOptions}
              onChange={this.handleMultiIncludedRightChange}                                    
            /> 
            :<input type="text"
              name="userTargetingIncludedData" 
              placeholder={this.state.includedRightPlaceholder}
              value={this.state.textTypeRightIncludedValue}
              onChange={this.handleMultiIncludedRightChange}  />  
            }          
           </td>
           <td><a onClick={this.addIncluded} className="quads-btn quads-btn-primary">Add</a></td>
           </tr>
         </tbody> 
        </table>
        </div>
        : ''}
       </div>
       <div className="quads-user-targeting"> 
       <h2>Excluded From <a onClick={this.excludedToggle}><Icon>remove_circle</Icon></a>  </h2>
       <div className="quads-target-item-list">
              {                
              this.state.multiTypeExcludedValue ? 
              this.state.multiTypeExcludedValue.map( (item, index) => (
                <div key={index} className="quads-target-item">
                  <span className="quads-target-label">{item.type.label} - {item.value.label}</span>
                  <span className="quads-target-icon" onClick={this.removeExcluded} data-index={index}><Icon>close</Icon></span> 
                </div>
               ) )
              :''}
             </div>
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
            />             
           </td>
           <td>
           {this.state.excludedTextToggle?
            <Select       
              Clearable ={true}      
              name="userTargetingExcludedData"
              placeholder={this.state.excludedRightPlaceholder}
              value={this.state.multiTypeRightExcludedValue}
              options={this.state.excludedDynamicOptions}
              onChange={this.handleMultiExcludedRightChange}                                    
            />   
            :<input type="text"
              Clearable ={true}  
              name="userTargetingExcludedData" 
              placeholder={this.state.excludedRightPlaceholder}
              value={this.state.textTypeRightExcludedValue}
              onChange={this.handleMultiExcludedRightChange}  />  
            }            
           </td>
           <td><a onClick={this.addExcluded} className="quads-btn quads-btn-primary">Add</a></td>
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