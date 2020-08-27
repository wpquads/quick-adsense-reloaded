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
         {quads_localize_data.is_amp_enable &&  post_meta.enabled_on_amp ? 
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
          <option value="after_word_count">{__('By Word Count', 'quick-adsense-reloaded')}</option>
          <option value="after_the_percentage">{__('After the Percentage', 'quick-adsense-reloaded')}</option>
          <option value="ad_after_html_tag">{__('Ad After HTML Tag', 'quick-adsense-reloaded')}</option>
          <option value="amp_ads_in_loops">Ads Inbetween Loop</option>
          <option value="ad_shortcode">{__('Shortcode (Manual)', 'quick-adsense-reloaded')}</option> 
          </optgroup>  
         
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
            </optgroup> 
           
        </select> 
         : <select className={(show_form_error && post_meta.position == '') ? 'quads_form_error' : ''} value={post_meta.position} name="position" onChange={this.props.adFormChangeHandler} >
          <option value="">{__('Select Position', 'quick-adsense-reloaded')}</option>
          <option value="beginning_of_post">{__('Beginning of Post', 'quick-adsense-reloaded')}</option>
          <option value="middle_of_post">{__('Middle of Post', 'quick-adsense-reloaded')}</option>
          <option value="end_of_post">{__('End of Post', 'quick-adsense-reloaded')}</option>
          <option value="after_more_tag">{__('Right after the', 'quick-adsense-reloaded')} &lt;!--more--&gt; {__('tag', 'quick-adsense-reloaded')}</option>
          <option value="before_last_paragraph">{__('Right before the last Paragraph', 'quick-adsense-reloaded')}</option>
          <option value="after_paragraph">{__('After Paragraph', 'quick-adsense-reloaded')}</option>
          <option value="after_image">{__('After Image', 'quick-adsense-reloaded')}</option>    
          <option value="after_word_count">{__('By Word Count', 'quick-adsense-reloaded')}</option>
           <option value="after_the_percentage">{__('After the Percentage', 'quick-adsense-reloaded')}</option>
           <option value="ad_after_html_tag">{__('Ad After HTML Tag', 'quick-adsense-reloaded')}</option>
          <option value="amp_ads_in_loops">Ads Inbetween Loop</option>
          <option value="ad_shortcode">{__('Shortcode (Manual)', 'quick-adsense-reloaded')}</option>
          </select>  }
           <div>{ (show_form_error && post_meta.position == '')  ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>Select Where Will The AD Appear</div></span> : ''}</div>
        </div> 
<div className='position_content'>
          <div>       
          {post_meta.position == 'after_image' ? <input min="1" onChange={this.props.adFormChangeHandler} name="image_number" value={post_meta.image_number}  type="number" /> : ''}         
          </div>
          <div>


          {post_meta.position == 'after_paragraph' ? 
          <div>
          <div>
          <label>  
          {post_meta.position == 'after_paragraph' ? <input min="1" onChange={this.props.adFormChangeHandler} name="paragraph_number" value={post_meta.paragraph_number}  type="number" /> : ''} 

          
          </label>
           <label htmlFor="enable_on_end_of_post">
           <input id='enable_on_end_of_post' checked={post_meta.enable_on_end_of_post} name="enable_on_end_of_post" onChange={this.props.adFormChangeHandler} type="checkbox"/>
           {__('to', 'quick-adsense-reloaded')} <strong>{__('End of Post', 'quick-adsense-reloaded')}</strong> {__('if fewer paragraphs', 'quick-adsense-reloaded')}</label>
</div><div>
             <input id='repeat_paragraph' checked={post_meta.repeat_paragraph} name="repeat_paragraph" onChange={this.props.adFormChangeHandler} type="checkbox"/>
              <label htmlFor="repeat_paragraph"> {__('Display After Every ', 'quick-adsense-reloaded')}{post_meta.paragraph_number}</label>
           
           </div></div> : ''}

          {post_meta.position == 'after_word_count' ? 
          <div>
          <label>  
          <input min="1" onChange={this.props.adFormChangeHandler} name="word_count_number" value={post_meta.word_count_number}  type="number" /> 
          </label>
           </div> : ''}
           {post_meta.position == 'after_the_percentage' ? 
          <div>
          <label> 
          <input min="1" onChange={this.props.adFormChangeHandler} name="after_the_percentage_value" value={post_meta.after_the_percentage_value}  type="number" /> % 
          </label>
           </div> : ''}
            {(show_form_error && post_meta.position == 'after_the_percentage' && (post_meta.g_data_ad_client == '' || parseInt(quads_post_meta.after_the_percentage_value) < 10 || parseInt(quads_post_meta.after_the_percentage_value) > 101)) ? <div className="quads_form_msg"><span className="material-icons">
error_outline</span>Percentage should be
 between 10 to 100</div> :''} 

          {post_meta.position == 'after_image' ? 
          <label>  
          <input checked={post_meta.image_caption} name="image_caption" onChange={this.props.adFormChangeHandler} type="checkbox"/>
          {__('after', 'quick-adsense-reloaded')} <strong>{__('Image\'s outer', 'quick-adsense-reloaded')} &lt;div&gt; wp-caption</strong> {__('if any.', 'quick-adsense-reloaded')}
          </label> : ''}
          
          {post_meta.position == 'ad_shortcode' &&  post_meta.quads_ad_old_id ?   

          <label>   

          Post Shortcode: <input name="post_shortcode" id="post_shortcode" type="text" defaultValue={'[quads id='+(post_meta.quads_ad_old_id).match(/\d+/)+']'}  readOnly/>  
          PHP:<input name="php_shortcode" id="post_shortcode_php"  type="text" defaultValue={"<?php echo do_shortcode('[quads id="+(post_meta.quads_ad_old_id).match(/\d+/)+"]'); ?>"} readOnly/> 
          </label> : ''}
            </div> 
          </div>      
      </div>  
    </div> 
    );
  }
}

export default QuadsAdvancePosition;