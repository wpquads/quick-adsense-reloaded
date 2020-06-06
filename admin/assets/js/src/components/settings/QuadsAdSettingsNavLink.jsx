import React, { Component, Fragment } from 'react';
import {Link} from 'react-router-dom';
import queryString from 'query-string'

class QuadsAdSettingsNavLink extends Component {

    constructor(props) {  
      
        super(props);
        this.state = {          
          
        };            
    }
    
    
  render() {    

    const {__} = wp.i18n;    
    const page = queryString.parse(window.location.search); 
    let current = 'settings';
    
    if(typeof(page.path)  != 'undefined' ) { 
      current = page.path;
    }                            
    return(                             
      <div className="quads-settings-tab">
      <ul>
          <li><h2><Link to={'admin.php?page=quads-settings&path=settings'} className={current == 'settings' ? 'quads-nav-link quads-nav-link-active' : 'quads-nav-link'}>{__('Features', 'quick-adsense-reloaded')}</Link></h2></li>
          {/* <li><h2><Link to={'admin.php?page=quads-settings&path=main_settings_importer'} className="quads-nav-link">{__('Migrator', 'quick-adsense-reloaded')}</Link></h2></li> */}
          <li><h2><Link to={'admin.php?page=quads-settings&path=settings_tools'} className={current == 'settings_tools' ? 'quads-nav-link quads-nav-link-active' : 'quads-nav-link'}>{__('Tools', 'quick-adsense-reloaded')}</Link></h2></li>
           <li><h2><Link to={'admin.php?page=quads-settings&path=settings_importer'} className={current == 'settings_importer' ? 'quads-nav-link quads-nav-link-active' : 'quads-nav-link'}>{__('Importer', 'quick-adsense-reloaded')}</Link></h2></li>
          <li><h2><Link to={'admin.php?page=quads-settings&path=settings_legacy'} className={current == 'settings_legacy' ? 'quads-nav-link quads-nav-link-active' : 'quads-nav-link'}>{__('Legacy', 'quick-adsense-reloaded')}</Link></h2></li>
          <li><h2><Link to={'admin.php?page=quads-settings&path=settings_support'} className={current == 'settings_support' ? 'quads-nav-link quads-nav-link-active' : 'quads-nav-link'}>{__('Help & Support', 'quick-adsense-reloaded')}</Link></h2></li>    
          {quads_localize_data.is_pro ? 
           <li><h2><Link to={'admin.php?page=quads-settings&path=settings_google_autoads'} className={current == 'settings_google_autoads' ? 'quads-nav-link quads-nav-link-active' : 'quads-nav-link'}>{__('Google Auto Ads', 'quick-adsense-reloaded')}</Link></h2></li>     :''} 
          {quads_localize_data.is_pro ? 
           <li><h2><Link to={'admin.php?page=quads-settings&path=settings_licenses'} className={current == 'settings_licenses' ? 'quads-nav-link quads-nav-link-active' : 'quads-nav-link'}>{__('Licenses', 'quick-adsense-reloaded')}</Link></h2></li>     :''}                                                  
        </ul>                          
        </div>        
        );
    }
}

export default QuadsAdSettingsNavLink;