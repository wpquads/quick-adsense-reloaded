import React, { Component, Fragment } from 'react';
import './QuadsAdvancePosition.scss';

class QuadsAdvancePosition extends Component {
  
  constructor(props) {
    super(props);
    this.state = {       

      }
    };   
      
  render() {

    const {__} = wp.i18n; 
    const post_meta = this.props.parentState.quads_post_meta;
    const show_form_error = this.props.parentState.show_form_error;
    
    return (
    <div>
      <div className="quads-position-dropdown">
        <div>
        <select className={(show_form_error && post_meta.position == '') ? 'quads_form_error' : ''} value={post_meta.position} name="position" onChange={this.props.adFormChangeHandler} >
          <option value="">{__('Select Position', 'quick-adsense-reloaded')}</option>
          <option value="beginning_of_post">{__('Beginning of Post', 'quick-adsense-reloaded')}</option>
          <option value="middle_of_post">{__('Middle of Post', 'quick-adsense-reloaded')}</option>
          <option value="end_of_post">{__('End of Post', 'quick-adsense-reloaded')}</option>
          <option value="after_more_tag">{__('Right after the', 'quick-adsense-reloaded')} &lt;!--more--&gt; {__('tag', 'quick-adsense-reloaded')}</option>
          <option value="before_last_paragraph">{__('Right before the last Paragraph', 'quick-adsense-reloaded')}</option>
          <option value="after_paragraph">{__('After Paragraph', 'quick-adsense-reloaded')}</option>
          <option value="after_image">{__('After Image', 'quick-adsense-reloaded')}</option>          
        </select> 
        </div> 

          <div>
          {post_meta.position == 'after_paragraph' ? <input min="1" onChange={this.props.adFormChangeHandler} name="paragraph_number" value={post_meta.paragraph_number}  type="number" /> : ''}         
          {post_meta.position == 'after_image' ? <input min="1" onChange={this.props.adFormChangeHandler} name="image_number" value={post_meta.image_number}  type="number" /> : ''}         
          </div>
          <div>

          {post_meta.position == 'after_paragraph' ? 
          <label>  
          <input checked={post_meta.enable_on_end_of_post} name="enable_on_end_of_post" onChange={this.props.adFormChangeHandler} type="checkbox"/>
           {__('to', 'quick-adsense-reloaded')} <strong>{__('End of Post', 'quick-adsense-reloaded')}</strong> {__('if fewer paragraphs', 'quick-adsense-reloaded')}
          </label> : ''}

          {post_meta.position == 'after_image' ? 
          <label>  
          <input checked={post_meta.image_caption} name="image_caption" onChange={this.props.adFormChangeHandler} type="checkbox"/>
          {__('after', 'quick-adsense-reloaded')} <strong>{__('Image\'s outer', 'quick-adsense-reloaded')} &lt;div&gt; wp-caption</strong> {__('if any.', 'quick-adsense-reloaded')}
          </label> : ''}
           
          </div>      
      </div>  
    </div> 
    );
  }
}

export default QuadsAdvancePosition;