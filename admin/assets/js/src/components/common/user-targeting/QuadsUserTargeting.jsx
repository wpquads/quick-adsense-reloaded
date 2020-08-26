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
      includedRightTextPlaceholder: 'Enter Targeting Data',
      excludedRightTextPlaceholder: 'Enter Targeting Data',
      
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
        geo_location_country:[
          {value :'AF' , label : 'Afghanistan'},
          {value :'AL' , label : 'Albania'},
          {value :'DZ' , label : 'Algeria'},
          {value :'AS' , label : 'American Samoa'},
          {value :'AD' , label : 'Andorra'},
          {value :'AO' , label : 'Angola'},
          {value :'AI' , label : 'Anguilla'},
          {value :'AQ' , label : 'Antarctica'},
          {value :'AG' , label : 'Antigua and Barbuda'},
          {value :'AR' , label : 'Argentina'},
          {value :'AM' , label : 'Armenia'},
          {value :'AW' , label : 'Aruba'},
          {value :'AU' , label : 'Australia'},
          {value :'AT' , label : 'Austria'},
          {value :'AZ' , label : 'Azerbaijan'},
          {value :'BS' , label : 'Bahamas'},
          {value :'BH' , label : 'Bahrain'},
          {value :'BD' , label : 'Bangladesh'},
          {value :'BB' , label : 'Barbados'},
          {value :'BY' , label : 'Belarus'},
          {value :'BE' , label : 'Belgium'},
          {value :'BZ' , label : 'Belize'},
          {value :'BJ' , label : 'Benin'},
          {value :'BM' , label : 'Bermuda'},
          {value :'BT' , label : 'Bhutan'},
          {value :'BO' , label : 'Bolivia'},
          {value :'BA' , label : 'Bosnia and Herzegovina'},
          {value :'BW' , label : 'Botswana'},
          {value :'BV' , label : 'Bouvet Island'},
          {value :'BR' , label : 'Brazil'},
          {value :'IO' , label : 'British Indian Ocean Territory'},
          {value :'BN' , label : 'Brunei Darussalam'},
          {value :'BG' , label : 'Bulgaria'},
          {value :'BF' , label : 'Burkina Faso'},
          {value :'BI' , label : 'Burundi'},
          {value :'KH' , label : 'Cambodia'},
          {value :'CM' , label : 'Cameroon'},
          {value :'CA' , label : 'Canada'},
          {value :'CV' , label : 'Cape Verde'},
          {value :'KY' , label : 'Cayman Islands'},
          {value :'CF' , label : 'Central African Republic'},
          {value :'TD' , label : 'Chad'},
          {value :'CL' , label : 'Chile'},
          {value :'CN' , label : 'China'},
          {value :'CX' , label : 'Christmas Island'},
          {value :'CC' , label : 'Cocos (Keeling) Islands'},
          {value :'CO' , label : 'Colombia'},
          {value :'KM' , label : 'Comoros'},
          {value :'CG' , label : 'Congo'},
          {value :'CD' , label : 'Congo, the Democratic Republic of the'},
          {value :'CK' , label : 'Cook Islands'},
          {value :'CR' , label : 'Costa Rica'},
          {value :'CI' , label : 'Cote D\'Ivoire'},
          {value :'HR' , label : 'Croatia'},
          {value :'CU' , label : 'Cuba'},
          {value :'CY' , label : 'Cyprus'},
          {value :'CZ' , label : 'Czech Republic'},
          {value :'DK' , label : 'Denmark'},
          {value :'DJ' , label : 'Djibouti'},
          {value :'DM' , label : 'Dominica'},
          {value :'DO' , label : 'Dominican Republic'},
          {value :'EC' , label : 'Ecuador'},
          {value :'EG' , label : 'Egypt'},
          {value :'SV' , label : 'El Salvador'},
          {value :'GQ' , label : 'Equatorial Guinea'},
          {value :'ER' , label : 'Eritrea'},
          {value :'EE' , label : 'Estonia'},
          {value :'ET' , label : 'Ethiopia'},
          {value :'FK' , label : 'Falkland Islands (Malvinas)'},
          {value :'FO' , label : 'Faroe Islands'},
          {value :'FJ' , label : 'Fiji'},
          {value :'FI' , label : 'Finland'},
          {value :'FR' , label : 'France'},
          {value :'GF' , label : 'French Guiana'},
          {value :'PF' , label : 'French Polynesia'},
          {value :'TF' , label : 'French Southern Territories'},
          {value :'GA' , label : 'Gabon'},
          {value :'GM' , label : 'Gambia'},
          {value :'GE' , label : 'Georgia'},
          {value :'DE' , label : 'Germany'},
          {value :'GH' , label : 'Ghana'},
          {value :'GI' , label : 'Gibraltar'},
          {value :'GR' , label : 'Greece'},
          {value :'GL' , label : 'Greenland'},
          {value :'GD' , label : 'Grenada'},
          {value :'GP' , label : 'Guadeloupe'},
          {value :'GU' , label : 'Guam'},
          {value :'GT' , label : 'Guatemala'},
          {value :'GN' , label : 'Guinea'},
          {value :'GW' , label : 'Guinea-Bissau'},
          {value :'GY' , label : 'Guyana'},
          {value :'HT' , label : 'Haiti'},
          {value :'HM' , label : 'Heard Island and Mcdonald Islands'},
          {value :'VA' , label : 'Holy See (Vatican City State)'},
          {value :'HN' , label : 'Honduras'},
          {value :'HK' , label : 'Hong Kong'},
          {value :'HU' , label : 'Hungary'},
          {value :'IS' , label : 'Iceland'},
          {value :'IN' , label : 'India'},
          {value :'ID' , label : 'Indonesia'},
          {value :'IR' , label : 'Iran, Islamic Republic of'},
          {value :'IQ' , label : 'Iraq'},
          {value :'IE' , label : 'Ireland'},
          {value :'IL' , label : 'Israel'},
          {value :'IT' , label : 'Italy'},
          {value :'JM' , label : 'Jamaica'},
          {value :'JP' , label : 'Japan'},
          {value :'JO' , label : 'Jordan'},
          {value :'KZ' , label : 'Kazakhstan'},
          {value :'KE' , label : 'Kenya'},
          {value :'KI' , label : 'Kiribati'},
          {value :'KP' , label : 'Korea, Democratic People\'s Republic of'},
          {value :'KR' , label : 'Korea, Republic of'},
          {value :'KW' , label : 'Kuwait'},
          {value :'KG' , label : 'Kyrgyzstan'},
          {value :'LA' , label : 'Lao People\'s Democratic Republic'},
          {value :'LV' , label : 'Latvia'},
          {value :'LB' , label : 'Lebanon'},
          {value :'LS' , label : 'Lesotho'},
          {value :'LR' , label : 'Liberia'},
          {value :'LY' , label : 'Libyan Arab Jamahiriya'},
          {value :'LI' , label : 'Liechtenstein'},
          {value :'LT' , label : 'Lithuania'},
          {value :'LU' , label : 'Luxembourg'},
          {value :'MO' , label : 'Macao'},
          {value :'MK' , label : 'Macedonia, the Former Yugoslav Republic of'},
          {value :'MG' , label : 'Madagascar'},
          {value :'MW' , label : 'Malawi'},
          {value :'MY' , label : 'Malaysia'},
          {value :'MV' , label : 'Maldives'},
          {value :'ML' , label : 'Mali'},
          {value :'MT' , label : 'Malta'},
          {value :'MH' , label : 'Marshall Islands'},
          {value :'MQ' , label : 'Martinique'},
          {value :'MR' , label : 'Mauritania'},
          {value :'MU' , label : 'Mauritius'},
          {value :'YT' , label : 'Mayotte'},
          {value :'MX' , label : 'Mexico'},
          {value :'FM' , label : 'Micronesia, Federated States of'},
          {value :'MD' , label : 'Moldova, Republic of'},
          {value :'MC' , label : 'Monaco'},
          {value :'MN' , label : 'Mongolia'},
          {value :'MS' , label : 'Montserrat'},
          {value :'MA' , label : 'Morocco'},
          {value :'MZ' , label : 'Mozambique'},
          {value :'MM' , label : 'Myanmar'},
          {value :'NA' , label : 'Namibia'},
          {value :'NR' , label : 'Nauru'},
          {value :'NP' , label : 'Nepal'},
          {value :'NL' , label : 'Netherlands'},
          {value :'AN' , label : 'Netherlands Antilles'},
          {value :'NC' , label : 'New Caledonia'},
          {value :'NZ' , label : 'New Zealand'},
          {value :'NI' , label : 'Nicaragua'},
          {value :'NE' , label : 'Niger'},
          {value :'NG' , label : 'Nigeria'},
          {value :'NU' , label : 'Niue'},
          {value :'NF' , label : 'Norfolk Island'},
          {value :'MP' , label : 'Northern Mariana Islands'},
          {value :'NO' , label : 'Norway'},
          {value :'OM' , label : 'Oman'},
          {value :'PK' , label : 'Pakistan'},
          {value :'PW' , label : 'Palau'},
          {value :'PS' , label : 'Palestinian Territory, Occupied'},
          {value :'PA' , label : 'Panama'},
          {value :'PG' , label : 'Papua New Guinea'},
          {value :'PY' , label : 'Paraguay'},
          {value :'PE' , label : 'Peru'},
          {value :'PH' , label : 'Philippines'},
          {value :'PN' , label : 'Pitcairn'},
          {value :'PL' , label : 'Poland'},
          {value :'PT' , label : 'Portugal'},
          {value :'PR' , label : 'Puerto Rico'},
          {value :'QA' , label : 'Qatar'},
          {value :'RE' , label : 'Reunion'},
          {value :'RO' , label : 'Romania'},
          {value :'RU' , label : 'Russian Federation'},
          {value :'RW' , label : 'Rwanda'},
          {value :'SH' , label : 'Saint Helena'},
          {value :'KN' , label : 'Saint Kitts and Nevis'},
          {value :'LC' , label : 'Saint Lucia'},
          {value :'PM' , label : 'Saint Pierre and Miquelon'},
          {value :'VC' , label : 'Saint Vincent and the Grenadines'},
          {value :'WS' , label : 'Samoa'},
          {value :'SM' , label : 'San Marino'},
          {value :'ST' , label : 'Sao Tome and Principe'},
          {value :'SA' , label : 'Saudi Arabia'},
          {value :'SN' , label : 'Senegal'},
          {value :'CS' , label : 'Serbia and Montenegro'},
          {value :'SC' , label : 'Seychelles'},
          {value :'SL' , label : 'Sierra Leone'},
          {value :'SG' , label : 'Singapore'},
          {value :'SK' , label : 'Slovakia'},
          {value :'SI' , label : 'Slovenia'},
          {value :'SB' , label : 'Solomon Islands'},
          {value :'SO' , label : 'Somalia'},
          {value :'ZA' , label : 'South Africa'},
          {value :'GS' , label : 'South Georgia and the South Sandwich Islands'},
          {value :'ES' , label : 'Spain'},
          {value :'LK' , label : 'Sri Lanka'},
          {value :'SD' , label : 'Sudan'},
          {value :'SR' , label : 'Suriname'},
          {value :'SJ' , label : 'Svalbard and Jan Mayen'},
          {value :'SZ' , label : 'Swaziland'},
          {value :'SE' , label : 'Sweden'},
          {value :'CH' , label : 'Switzerland'},
          {value :'SY' , label : 'Syrian Arab Republic'},
          {value :'TW' , label : 'Taiwan, Province of China'},
          {value :'TJ' , label : 'Tajikistan'},
          {value :'TZ' , label : 'Tanzania, United Republic of'},
          {value :'TH' , label : 'Thailand'},
          {value :'TL' , label : 'Timor-Leste'},
          {value :'TG' , label : 'Togo'},
          {value :'TK' , label : 'Tokelau'},
          {value :'TO' , label : 'Tonga'},
          {value :'TT' , label : 'Trinidad and Tobago'},
          {value :'TN' , label : 'Tunisia'},
          {value :'TR' , label : 'Turkey'},
          {value :'TM' , label : 'Turkmenistan'},
          {value :'TC' , label : 'Turks and Caicos Islands'},
          {value :'TV' , label : 'Tuvalu'},
          {value :'UG' , label : 'Uganda'},
          {value :'UA' , label : 'Ukraine'},
          {value :'AE' , label : 'United Arab Emirates'},
          {value :'GB' , label : 'United Kingdom'},
          {value :'US' , label : 'United States'},
          {value :'UM' , label : 'United States Minor Outlying Islands'},
          {value :'UY' , label : 'Uruguay'},
          {value :'UZ' , label : 'Uzbekistan'},
          {value :'VU' , label : 'Vanuatu'},
          {value :'VE' , label : 'Venezuela'},
          {value :'VN' , label : 'Viet Nam'},
          {value :'VG' , label : 'Virgin Islands, British'},
          {value :'VI' , label : 'Virgin Islands, U.s.'},
          {value :'WF' , label : 'Wallis and Futuna'},
          {value :'EH' , label : 'Western Sahara'},
          {value :'YE' , label : 'Yemen'},
          {value :'ZM' , label : 'Zambia'},
          {value :'ZW' , label : 'Zimbabwe'}
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
        if( !quads_localize_data.is_pro && (option.value==='geo_location_country' || option.value==='geo_location_city')){
         this.setState({includedMainToggle:false});
         return;
        }else{
        this.setState({includedMainToggle:true});
        } 
      var placeholder = 'Search for ' + option.label;
    
      if(option.value==='cookie' || option.value==='url_parameter' || option.value==='referrer_url' || option.value==='geo_location_city'){
        placeholder = 'Enter your ' + option.label;
        this.setState({includedTextToggle:false});
        this.setState({multiTypeLeftIncludedValue:option, includedDynamicOptions:type, textTypeRightIncludedValue:'', includedRightPlaceholder:placeholder});
      }else{
        if(option.value=='browser_width'){
          this.setState({is_amp_endpoint_inc:true});
        }else{
          this.setState({is_amp_endpoint_inc:false});
        }
        this.setState({includedTextToggle:true});
        this.setState({multiTypeLeftIncludedValue:option, includedDynamicOptions:type, multiTypeRightIncludedValue:[], includedRightPlaceholder:placeholder});
      }
     
  }
  handleMultiExcludedLeftChange = (option) => {    
    let type = this.state.multiTypeTargetOption[option.value];      
    if( !quads_localize_data.is_pro && (option.value==='geo_location_country' || option.value==='geo_location_city')){
     this.setState({excludedMainToggle:false});
    return;
    }else{
      this.setState({excludedMainToggle:true});
    } 
     var placeholder = 'Search for ' + option.label;
      if(option.value==='cookie' || option.value==='url_parameter' || option.value==='referrer_url' || option.value==='geo_location_city'){
         placeholder = 'Enter your ' + option.label;
         this.setState({excludedTextToggle:false});
         this.setState({multiTypeLeftExcludedValue:option, excludedDynamicOptions:type, textTypeRightExcludedValue:'', excludedRightPlaceholder:placeholder});
      }else{
        if(option.value=='browser_width'){
          this.setState({is_amp_endpoint_exc:true});
        }else{
          this.setState({is_amp_endpoint_exc:false});
        }
        this.setState({excludedTextToggle:true});
        this.setState({multiTypeLeftExcludedValue:option, excludedDynamicOptions:type, multiTypeRightExcludedValue:[], excludedRightPlaceholder:placeholder});
      }
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
    if(type.value==='cookie' || type.value==='url_parameter' || type.value==='referrer_url'  || type.value==='geo_location_city'){
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
  if(type.value=='cookie' || type.value==='url_parameter' || type.value==='referrer_url'|| type.value==='geo_location_city'){
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
      data.push({type: type, value: value});
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
    data.push({type: type, value: value});
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
    return (
      <div className="quads-settings-group quads-targeting">
      <div className="quads-title">{__('Targeting', 'quick-adsense-reloaded')}</div>  
      <div className="quads-panel">
      <div className="quads-panel-body">                 
      <div className="quads-user-targeting-label">
        <b>When</b>  {__(' should the ad display?', 'quick-adsense-reloaded')}                     
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
            {this.state.is_amp_endpoint_inc?
              <span className="amp-support">AMP does not support Browser Width Targeting</span>
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
              </>) :<><td className="targeting_get_pro">This feature is available in PRO version </td><td><a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">Unlock this feature</a> </td></>}
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
            {this.state.is_amp_endpoint_exc?
              <span className="amp-support">AMP does not support Browser Width Targeting</span>
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
               </>) : <><td className="targeting_get_pro">This feature is available in PRO version</td><td><a className="quads-got_pro premium_features_btn" href="https://wpquads.com/#buy-wpquads" target="_blank">Unlock this feature</a> </td></>}
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