import React, { Component, Fragment } from 'react';

import './QuadsAdConfigFields.scss';



class QuadsAdFieldsGenerator extends Component {

    constructor(props) {
        super(props);    
        this.state = {         
          fields : []
        };       
      } 
 
  render() {
      
    const metafields = this.props.metafields;     
    if(metafields){

        metafields.map(item =>{

            var ad_field = [];
            switch (item.type) {

                case 'checkbox':
                
                    break;
                case 'radio':
            
                    break; 
                case 'select':
                                      
                    var option = Object.keys(item.options).map(function(key, index) {
                        return <option key={index} value={key}>{item.options[key]}</option>
                    });

                    ad_field.push( 
                    <div className="quads-ad-field" key={item.id}>
                     <label>{item.label}</label> 
                     <select id={item.id} name={item.id}>{option}</select>  
                    </div>                                             
                    ); 
                    break; 
                case 'textarea':
    
                    break;  
                case 'media':

                    break;                    
            
                default:
                    ad_field.push(<div key={item.id} className="quads-ad-field"><label>{item.label}</label><input type="text" id={item.id} name={item.id} /></div>);                    
                    break;
            }

            this.state.fields.push(
                ad_field
            );
        })

        return(<div>{this.state.fields}</div>);
    }else{
        return '';
    }
    
  }
}

export default QuadsAdFieldsGenerator;