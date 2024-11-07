import React, { Component, Fragment } from 'react';
import './QuadsAdvancePosition.scss';
import { Alert } from '@material-ui/lab';
import Icon from '@material-ui/core/Icon';

class QuadsAdvancePosition extends Component {

    constructor(props) {
        super(props);
        this.state = {
            ad_extra_position :[]

        }
        this.quads_register_ad();
    };
check_plugin_exist = (event) => {
    const { quads_post_meta } = { ...this.state };
    this.setState({ quads_post_meta: true});  
    }
    quads_register_ad = () => {
        const json_data = {
            action: 'quads_ajax_add_ads',
        }
        const url = quads_localize_data.rest_url + "quads-route/quads_register_ad";
        fetch(url , {
            method: "post",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': quads_localize_data.nonce,
            },
            body: JSON.stringify(json_data)
        })
            .then(res => res.json())
            .then(
                (result) => {
                    let ad_extra_position =  Object.entries(result).map(([key, value]) => {
                        return (
                            <option key={key} value={'api-'+value.location}>{value.description}</option>
                        )
                    });
                    this.setState({ad_extra_position:ad_extra_position});
                },
                (error) => {
                }
            );
    }
    render() {

        const {__} = wp.i18n;
        const post_meta = this.props.parentState.quads_post_meta;
        const show_form_error = this.props.parentState.show_form_error;
        if(post_meta.ad_type == 'ads_space'){
            post_meta.position = 'ad_shortcode';
        }
        return (
            <div>
                <div className="quads-position-dropdown">
                    <div>
                        {quads_localize_data.is_amp_enable &&  post_meta.enabled_on_amp ?
                        <>
                            <select style={{minWidth:'250px'}} className={(show_form_error && post_meta.position == '') ? 'quads_form_error' : ''} value={post_meta.position} name="position" onChange={this.props.adFormChangeHandler} disabled={post_meta.ad_type == 'ads_space'} >
                               <>
                               {post_meta.ad_type != "adpushup" ?                                
                                <>
                                <optgroup label='Full Support ( AMP &amp; NON AMP )'>
                                    <option value="select_position">{__('Select Position', 'quick-adsense-reloaded')}</option>
                                    <option value="random_ad_placement">{__('Random Placement', 'quick-adsense-reloaded')}</option>
                                    <option value="beginning_of_post">{__('Beginning of Post', 'quick-adsense-reloaded')}</option>
                                    <option value="middle_of_post">{__('Middle of Post', 'quick-adsense-reloaded')}</option>
                                    <option value="end_of_post">{__('End of Post', 'quick-adsense-reloaded')}</option>
                                    <option value="after_more_tag">{__('Right after the', 'quick-adsense-reloaded')} &lt;!--more--&gt; {__('tag', 'quick-adsense-reloaded')}</option>
                                    <option value="before_last_paragraph">{__('Right before the last Paragraph', 'quick-adsense-reloaded')}</option>
                                    <option value="after_paragraph">{__('After Paragraph', 'quick-adsense-reloaded')}</option>
                                    <option value="after_image">{__('After Image', 'quick-adsense-reloaded')}</option>
                                    <option value="before_image">{__('Before Image', 'quick-adsense-reloaded')}</option>
                                    <option value="after_word_count">{__('By Word Count', 'quick-adsense-reloaded')}</option>
                                    <option value="after_the_percentage">{__('After the Percentage', 'quick-adsense-reloaded')}</option>
                                    <option value="ad_after_id">{__('After Id', 'quick-adsense-reloaded')}</option>
                                    <option value="ad_after_class">{__('After Class', 'quick-adsense-reloaded')}</option>
                                    <option value="ad_after_customq">{__('After Advance Selector', 'quick-adsense-reloaded')}</option>
                                    <option value="ad_after_html_tag">{__('After HTML Tag', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_ads_in_loops">{__('Inbetween Loop', 'quick-adsense-reloaded')}</option>
                                    <option value="ad_shortcode">{__('Shortcode (Manual)', 'quick-adsense-reloaded')}</option>
                                    <option value="ad_sticky_ad">{__('Sticky (NON AMP ONLY)', 'quick-adsense-reloaded')}</option>
                                    {this.state.ad_extra_position}
                                    </optgroup>
                                {quads_localize_data.is_newsPapertheme_exist ?
                                     <option value="before_header">{__('Before the Header (Newspaper Theme)', 'quick-adsense-reloaded')}</option>
                                     
                                : ''}
                                {quads_localize_data.is_newsPapertheme_exist ?
                                     <option value="after_header">{__('After the Header (Newspaper Theme)', 'quick-adsense-reloaded')}</option>
                                : ''}
                                </>
                                : ''}  
                               </> 
                               <optgroup label='Partial Support ( AMP Only )'>
                                    <option value="amp_after_featured_image">{__('After Featured Image', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_below_the_header">{__('Below the Header (SiteWide)', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_below_the_footer">{__('Below the Footer (SiteWide)', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_above_the_footer">{__('Above the Footer (SiteWide)', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_above_the_post_content">{__('Above the Post Content (Single Post)', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_below_the_post_content">{__('Below the Post Content (Single Post)', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_below_the_title">{__('Below the Title (Single Post)', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_above_related_post">{__('Above Related Posts (Single Post)', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_below_author_box">{__('Below the Author Box (Single Post)', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_after_paragraph">{__('After Paragraph (Single Post)', 'quick-adsense-reloaded')}</option>
                                    <option value="amp_doubleclick_sticky_ad">{__('Sticky (AMP)', 'quick-adsense-reloaded')} </option>
                                    {post_meta.ad_type =='adsense' || post_meta.ad_type =='double_click' ?  <option value="amp_story_ads">{__('AMP Story', 'quick-adsense-reloaded')}</option> : null }
                                    </optgroup>
                            </select>
                            {post_meta.ad_type == "adpushup" ? <p>{__('This selection is just for AMP', 'quick-adsense-reloaded')}</p> : ''}
                            </>                            
                            : <select style={{minWidth:'250px'}} disabled={post_meta.ad_type == 'ads_space'} className={(show_form_error && post_meta.position == '') ? 'quads_form_error' : ''} value={post_meta.position} name="position" onChange={this.props.adFormChangeHandler} >
                                <option value="select_position">{__('Select Position', 'quick-adsense-reloaded')}</option>
                                <option value="random_ad_placement">{__('Random Placement', 'quick-adsense-reloaded')}</option>
                                <option value="beginning_of_post">{__('Beginning of Post', 'quick-adsense-reloaded')}</option>
                                <option value="middle_of_post">{__('Middle of Post', 'quick-adsense-reloaded')}</option>
                                <option value="end_of_post">{__('End of Post', 'quick-adsense-reloaded')}</option>
                                <option value="after_more_tag">{__('Right after the', 'quick-adsense-reloaded')} &lt;!--more--&gt; {__('tag', 'quick-adsense-reloaded')}</option>
                                <option value="before_last_paragraph">{__('Right before the last Paragraph', 'quick-adsense-reloaded')}</option>
                                <option value="after_paragraph">{__('After Paragraph', 'quick-adsense-reloaded')}</option>
                                <option value="after_image">{__('After Image', 'quick-adsense-reloaded')}</option>
                                <option value="before_image">{__('Before Image', 'quick-adsense-reloaded')}</option>
                                <option value="after_word_count">{__('By Word Count', 'quick-adsense-reloaded')}</option>
                                <option value="after_the_percentage">{__('After the Percentage', 'quick-adsense-reloaded')}</option>
                                <option value="ad_after_id">{__('After Id', 'quick-adsense-reloaded')}</option>
                                <option value="ad_after_class">{__('After Class', 'quick-adsense-reloaded')}</option>
                                <option value="ad_after_customq">{__('After Advance Selector', 'quick-adsense-reloaded')}</option>
                                <option value="ad_after_html_tag">{__('After HTML Tag', 'quick-adsense-reloaded')}</option>
                                <option value="ad_before_html_tag">{__('Before HTML Tag', 'quick-adsense-reloaded')}</option>
                                <option value="ad_sticky_ad">{__('Sticky', 'quick-adsense-reloaded')}</option>
                                <option value="amp_ads_in_loops">{__('Inbetween Loop', 'quick-adsense-reloaded')}</option>
                                {quads_localize_data.is_bbpress_exist ?
                                    <>
                                        <option value="bbpress_before_ad">{__('BBpress Before Ad', 'quick-adsense-reloaded')}</option>
                                        <option value="bbpress_after_ad">{__('BBpress After Ad', 'quick-adsense-reloaded')}</option>
                                        <option value="bbpress_before_reply">{__('BBpress Before Reply', 'quick-adsense-reloaded')}</option>
                                        <option value="bbpress_after_reply">{__('BBpress After Reply', 'quick-adsense-reloaded')}</option>
                                    </>
                                    :null}
                                    
                                {this.state.ad_extra_position}
                                <option value="ad_shortcode">{__('Shortcode (Manual)', 'quick-adsense-reloaded')}</option>
                                {quads_localize_data.is_newsPapertheme_exist ?
                                <optgroup label="Newspaper Theme support">
                                <option value="before_header">{__('Before the Header', 'quick-adsense-reloaded')}</option>
                                <option value="after_header">{__('After the Header', 'quick-adsense-reloaded')}</option>
                                    </optgroup>
                                : ''}    
                            </select>  }
                        <div>{ (show_form_error && post_meta.position == '')  ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>{__('Select Where Will The AD Appear', 'quick-adsense-reloaded')}</div></span> : ''}</div>
                    </div>
                    <div className='position_content'>
                        <div>
                            {post_meta.position == 'after_image' ? <input min="1" onChange={this.props.adFormChangeHandler} name="image_number" value={post_meta.image_number}  type="number" /> : ''}
                       
                        
                        </div>
                        <div>
                            {post_meta.position == 'before_image' ? <input min="1" onChange={this.props.adFormChangeHandler} name="image_number" value={post_meta.image_number}  type="number" /> : ''}
                       
                        
                        </div>
                        <div>
                            {post_meta.position == 'bbpress_before_reply' || post_meta.position == 'bbpress_after_reply' ?
                                <div>
                                    <label> {__('Inject at', 'quick-adsense-reloaded')} 
                                        <input min="1" onChange={this.props.adFormChangeHandler} name="paragraph_number" value={post_meta.paragraph_number}  type="number" />
                                    </label>
                                    <input id='repeat_paragraph' checked={post_meta.repeat_paragraph} name="repeat_paragraph" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                                    <label htmlFor="repeat_paragraph"> {__('Display After Every ', 'quick-adsense-reloaded')}{post_meta.paragraph_number}</label>
                                </div> : ''}

                            {post_meta.position == 'after_paragraph' ?
                                <div>
                                    <div>
                                        <label>
                                            {post_meta.position == 'after_paragraph' ? <input min="1" onChange={this.props.adFormChangeHandler} name="paragraph_number" value={post_meta.paragraph_number}  type="number" /> : ''}


                                        </label>
                                        <label htmlFor="enable_on_end_of_post">
                                            <input id='enable_on_end_of_post' checked={post_meta.enable_on_end_of_post} name="enable_on_end_of_post" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                                            {__('to', 'quick-adsense-reloaded')} <strong>{__('End of Post', 'quick-adsense-reloaded')}</strong> {__('if fewer paragraphs', 'quick-adsense-reloaded')}</label>
                                    </div>
                                    <div>
                                        <input id='repeat_paragraph' checked={post_meta.repeat_paragraph} name="repeat_paragraph" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                                        <label htmlFor="repeat_paragraph"> {__('Display After Every ', 'quick-adsense-reloaded')}{post_meta.paragraph_number}</label>
                                    </div>


                                </div> : ''}

                                {
                                    post_meta.position == "amp_after_paragraph" ? 
                                    <div>
                                    <div>
                                        <label>
                                            {post_meta.position == 'amp_after_paragraph' ? <input min="1" onChange={this.props.adFormChangeHandler} name="paragraph_number" value={post_meta.paragraph_number}  type="number" /> : ''}


                                        </label>
                                        <label htmlFor="enable_on_end_of_post">
                                            <input id='enable_on_end_of_post' checked={post_meta.enable_on_end_of_post} name="enable_on_end_of_post" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                                            {__('to', 'quick-adsense-reloaded')} <strong>{__('End of Post', 'quick-adsense-reloaded')}</strong> {__('if fewer paragraphs', 'quick-adsense-reloaded')}</label>
                                    </div>
                                    <div>
                                        <input id='repeat_paragraph' checked={post_meta.repeat_paragraph} name="repeat_paragraph" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                                        <label htmlFor="repeat_paragraph"> {__('Display After Every ', 'quick-adsense-reloaded')}{post_meta.paragraph_number}</label>
                                    </div>


                                </div> : ''

                                }

                                {!post_meta.check_plugin_exist?<Alert severity="error" className={'check_plugin_exist'}><div > {__('AMP stories plugin not exist', 'quick-adsense-reloaded')} </div></Alert>:null}

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
error_outline</span>{__('Percentage should be between 10 to 100 ', 'quick-adsense-reloaded')}</div> :''}

                            {post_meta.position == 'after_image' ?
                                <label>
                                    <input checked={post_meta.image_caption} name="image_caption" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                                    {__('after', 'quick-adsense-reloaded')} <strong>{__('Image\'s outer', 'quick-adsense-reloaded')} &lt;div&gt; wp-caption</strong> {__('if any.', 'quick-adsense-reloaded')}
                                </label> : ''}

                            {post_meta.position == 'before_image' ?
                                <label>
                                    <input checked={post_meta.image_caption} name="image_caption" onChange={this.props.adFormChangeHandler} type="checkbox"/>
                                    {__('before', 'quick-adsense-reloaded')} <strong>{__('Image\'s outer', 'quick-adsense-reloaded')} &lt;div&gt; wp-caption</strong> {__('if any.', 'quick-adsense-reloaded')}
                                </label> : ''}

                            {post_meta.position == 'ad_shortcode' &&  post_meta.quads_ad_old_id ?

                                <label>

{__('Post Shortcode:', 'quick-adsense-reloaded')} <input name="post_shortcode" id="post_shortcode" data-attr={''+(post_meta.quads_ad_old_id).match(/\d+/)+''} type="text" defaultValue={'[quads id='+(post_meta.quads_ad_old_id).match(/\d+/)+']'}  readOnly/>
{__('PHP:', 'quick-adsense-reloaded')}<input name="php_shortcode" id="post_shortcode_php"  type="text" defaultValue={"<?php echo do_shortcode('[quads id="+(post_meta.quads_ad_old_id).match(/\d+/)+"]'); ?>"} readOnly/>
                                </label> : ''}
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default QuadsAdvancePosition;
