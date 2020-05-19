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
         <optgroup label="Full Support ( AMP &amp; NON AMP )"> 
          <option value="">{__('Select Position', 'quick-adsense-reloaded')}</option>
          <option value="beginning_of_post">{__('Beginning of Post', 'quick-adsense-reloaded')}</option>
          <option value="middle_of_post">{__('Middle of Post', 'quick-adsense-reloaded')}</option>
          <option value="end_of_post">{__('End of Post', 'quick-adsense-reloaded')}</option>
          <option value="after_more_tag">{__('Right after the', 'quick-adsense-reloaded')} &lt;!--more--&gt; {__('tag', 'quick-adsense-reloaded')}</option>
          <option value="before_last_paragraph">{__('Right before the last Paragraph', 'quick-adsense-reloaded')}</option>
          <option value="after_paragraph">{__('After Paragraph', 'quick-adsense-reloaded')}</option>
          <option value="after_image">{__('After Image', 'quick-adsense-reloaded')}</option>    
          <option value="ad_shortcode">{__('Shortcode (Manual)', 'quick-adsense-reloaded')}</option>  
          </optgroup>  
          {quads_localize_data.is_amp_enable ? 
          <optgroup label="Partial Support ( AMP Only )">
            <option value="amp_after_featured_image">Ad after Featured Image</option>
            <option value="amp_below_the_header">Below the Header (SiteWide)</option>
            <option value="amp_below_the_footer">Below the Footer (SiteWide)</option>
            <option value="amp_above_the_footer">Above the Footer (SiteWide)</option>
            <option value="amp_above_the_post_content">Above the Post Content (Single Post)</option>
            <option value="amp_below_the_post_content">Below the Post Content (Single Post)</option>
            <option value="amp_below_the_title">Below the Title (Single Post)</option>
            <option value="amp_above_related_post">Above Related Posts (Single Post)</option>
            <option value="amp_below_author_box">Below the Author Box (Single Post)</option>
            <option value="amp_ads_in_loops">Ads Inbetween Loop</option>
            </optgroup> 
            : null }
        </select> 
           <div>{ (show_form_error && post_meta.position == '')  ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>Select Where Will The AD Appear</div></span> : ''}</div>
        </div> 
<div className='position_content'>
          <div>
          {post_meta.position == 'amp_ads_in_loops' ? <input min="1" onChange={this.props.ads_loop_number} name="paragraph_number" value={post_meta.ads_loop_number} placeholder="Position" type="number" /> : ''}         
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
          {post_meta.position == 'ad_shortcode' &&  post_meta.quads_ad_old_id ?   

          <label>   

          Post Shortcode: <input name="post_shortcode" id="post_shortcode" type="text" value={'[quads id='+(post_meta.quads_ad_old_id).match(/\d+/)+']'} readonly=""/>  
          PHP:<input name="php_shortcode" id="post_shortcode_php"  type="text" value={"<?php echo do_shortcode('[quads id="+(post_meta.quads_ad_old_id).match(/\d+/)+"]'); ?>"} readonly=""/> 
          </label> : ''}
            </div> 
          </div>      
      </div>  
    </div> 
    );
  }
}

export default QuadsAdvancePosition;