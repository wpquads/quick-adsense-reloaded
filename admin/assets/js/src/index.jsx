import React, { Component, Fragment } from 'react';
import ReactDOM from "react-dom";
import { BrowserRouter as Router, Switch, Route, Link } from 'react-router-dom';
import queryString from 'query-string'
import QuadsAdCreateRouter from './components/ads/ad-create-router/QuadsAdCreateRouter'
import './style/common.scss';
import QuadsAdmin from './components/admin/QuadsAdmin'

class QuadsAdRootComponent extends Component {
    
  render() {
            return (
                    <Fragment>  
                    <Router> 
                        <div>                                                   
                        <div className="quads-segment"> 
                        {quads_localize_data.demo_test}               
                        <Switch>
                            <Route render={props => {        

                                const page = queryString.parse(window.location.search); 
                                
                                if(typeof(page.path)  != 'undefined' ) {                           
                                    
                                        if(page.path.includes('settings') || page.path.includes('reports') || page.path.includes('ad_logging') || page.path.includes('view_report') || page.path.includes('adsell')){

                                            return <QuadsAdmin {...props}/>;

                                        }else if(page.path.includes('wizard')){

                                            return <QuadsAdCreateRouter {...props}/>;

                                        }
                                        else{
                                            return 'Page not found';
                                        }

                                }else{
                                    return <QuadsAdmin {...props}/>;  
                                }
                                }}/>            
                    </Switch> 
                    </div>
                    </div>
                   </Router>                             
                    </Fragment>                                                                               
            );
    }
}

ReactDOM.render(<QuadsAdRootComponent />, document.getElementById('quads-ad-content'));