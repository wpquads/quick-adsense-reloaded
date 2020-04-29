import React, { Component, Fragment } from 'react';
import {Link} from 'react-router-dom';
import queryString from 'query-string'
import AdTypeSelectorNavLink from "../ad-type-selector-nav/AdTypeSelectorNavLink";
import Icon from '@material-ui/core/Icon';

class QuadsAdListNavLink extends Component {

    constructor(props) {  
      
        super(props);
        this.state = {          
            ad_type_toggle:this.props.ad_type_toggle
        };            
    }
    showAddTypeSelector = (e) => {
        e.preventDefault();
        this.setState({ad_type_toggle:true});    
    }
    hideAddTypeSelector = (e) => {
        e.preventDefault();
        this.setState({ad_type_toggle:false});    
    }
    
  render() {    
    const {__} = wp.i18n;    
    const page = queryString.parse(window.location.search); 
    let current = 'ads';
    
    if(typeof(page.path)  != 'undefined' ) { 

        if( page.path == 'settings' || page.path == 'settings_tools' || page.path == 'settings_importer' || page.path == 'settings_legacy' || page.path == 'settings_support' || page.path == 'settings_licenses' || page.path == 'settings_google_autoads')  {
            jQuery('.wp-submenu li').removeClass('current');
            jQuery('a[href$="quads-settings&path=settings"]').parent().addClass('current');
            current = 'settings';
        }
        }else if(page.page == 'quads-settings'){
            jQuery('.wp-submenu li').removeClass('current');
            jQuery('a[href$="quads-settings"]').parent().addClass('current');
        }                           
    return(                             
        <div className="quads-ad-tab-wrapper">
         <div className="quads-hidden-element">
         {
                this.state.ad_type_toggle || this.props.ad_type_toggle ? 
                <div className="quads-full-page-modal">                           
                <div className="quads-full-page-modal-content">             
                <div className="material-icons quads-close-create-page"><a onClick={this.hideAddTypeSelector} className="quads-full-page-modal-close">close</a></div>
                <h3>{__('AD Integrations', 'quick-adsense-reloaded')}</h3>
                 <div>                 
                  <AdTypeSelectorNavLink />                  
                 </div>
                </div>        
                </div>
                : ''
              }
         </div>   
        <div className="quads-ad-tab">
            <ul>
                <li><Link to={'admin.php?page=quads-settings'} className={current == 'ads' ? 'quads-nav-link quads-nav-link-active ' : 'quads-nav-link'}>{__('Ads', 'quick-adsense-reloaded')}</Link></li>                
                <li><Link to={'admin.php?page=quads-settings&path=settings'} className={current == 'settings' ? 'quads-nav-link quads-nav-link-active ' : 'quads-nav-link'}>{__('Settings', 'quick-adsense-reloaded')}</Link></li>                
                <li><div className="quads-add-btn"><a className="quads-btn quads-btn-primary" onClick={this.showAddTypeSelector}><Icon>add_circle</Icon>Create Ad</a></div></li>                
            </ul>   
        </div> 
        </div>
        );
    }
}

export default QuadsAdListNavLink;