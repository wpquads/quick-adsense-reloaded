import React, { Component, Fragment } from 'react';
import './QuadsAdvancePosition.scss';
import { Alert } from '@material-ui/lab';
import Icon from '@material-ui/core/Icon';
import Select from "react-select";
class QuadsAdvancePositionMutiabtesting extends Component {

    constructor(props) {
        super(props);
        this.state = {
            ad_extra_position: [],
            positionSelected: [],
            positionToggle: false,
            position_data: this.props.parentState.quads_post_meta,
            check_plugin_exist: true,
            ab_testing_temp: {},
            position_list: [
                { label: 'Beginning of Post', value: 'beginning_of_post' },
                { label: 'middle_of_post', value: 'Middle of Post' },
                { label: 'end_of_post', value: 'End of Post' },
                { label: 'after_more_tag', value: 'Right after the <!--more--> tag' },
                { label: 'before_last_paragraph', value: 'Right before the last Paragraph' },
                { label: 'after_paragraph', value: 'After Paragraph' },
                { label: 'after_image', value: 'After Image' },
                { label: 'before_image', value: 'Before Image' },
                { label: 'after_word_count', value: 'By Word Count' },
                { label: 'after_the_percentage', value: 'After the Percentage' },
                { label: 'ad_after_id', value: 'After Id' },
                { label: 'ad_after_class', value: 'After Class' },
                { label: 'ad_after_customq', value: 'After Advance Selector' },
                { label: 'ad_after_html_tag', value: 'After HTML Tag' },
                { label: 'amp_ads_in_loops', value: 'Inbetween Loop' },
                { label: 'ad_shortcode', value: 'Shortcode (Manual)' },
            ]
        }
    };

    addposition = (e) => {

        e.preventDefault();

        let values = this.state.ab_testing_temp;

        let { position_data, ab_testing_temp } = { ...this.state };
        let data = position_data['ab_testing'];
        data.push(ab_testing_temp);
        this.props.adFormChangeHandler({ target: { name: 'ab_testing' , value: data } });
        this.setState({ positionToggle: false, ab_testing_temp: {} });

    }
    positionToggle = () => {

        this.setState({ positionToggle: !this.state.positionToggle });
    }
    adFormChangeHandlerstate = (event) => {

        const name = event.target.name;
        const value = event.target.type === 'checkbox' ? event.target.checked : event.target.value;
        const { ab_testing_temp } = { ...this.state };

        if (name) {
            ab_testing_temp[name] = value;
            this.setState({ ab_testing_temp: ab_testing_temp });
        }
    }
    // check_plugin_exist = (event) => {
    //     const { quads_post_meta } = { ...this.state };
    //     this.setState({ quads_post_meta: true });
    // }
    removeSeleted_list = (e) => {
        let index = e.currentTarget.dataset.index;
        const position_data = this.props.parentState.quads_post_meta.ab_testing;
        position_data.splice(index, 1);
        this.props.adFormChangeHandler({ target: { name: 'ab_testing', value: position_data } });
        this.setState({ ab_testing_temp: {} });
    }

    render() {

        const { __ } = wp.i18n;
        const show_form_error = this.props.parentState.show_form_error;

        const { ab_testing_temp } = { ...this.state };
        const post_meta = ab_testing_temp
        const position_data = this.props.parentState.quads_post_meta;
        return (
            <div>
                <div>{__('Position', 'quick-adsense-reloaded')}</div>
                <div className="quads-panel">
                    <div className="quads-panel-body"> <h2> Select Position<a onClick={this.positionToggle}><Icon>add_circle</Icon></a> </h2>
                        {this.state.positionToggle ?
                            <div>
                                <div className="quads-position-dropdown">
                                    <div>
                                        {quads_localize_data.is_amp_enable && position_data.enabled_on_amp ?
                                            <select className={(show_form_error && position_data.ab_testing.length <= 0) ? 'quads_form_error' : ''} value={post_meta.position} name="position" onChange={this.adFormChangeHandlerstate} >
                                                <optgroup label="Full Support ( AMP &amp; NON AMP )">
                                                    <option value="">{__('Select Position', 'quick-adsense-reloaded')}</option>
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
                                                    <option value="ad_after_id">{__('Ad After Id', 'quick-adsense-reloaded')}</option>
                                                    <option value="ad_after_class">{__('Ad After Class', 'quick-adsense-reloaded')}</option>
                                                    <option value="ad_after_customq">{__('Ad After Advance Selector', 'quick-adsense-reloaded')}</option>
                                                    <option value="ad_after_html_tag">{__('Ad After HTML Tag', 'quick-adsense-reloaded')}</option>
                                                    <option value="amp_ads_in_loops">Ads Inbetween Loop</option>
                                                    <option value="ad_shortcode">{__('Shortcode (Manual)', 'quick-adsense-reloaded')}</option>
                                                    {this.state.ad_extra_position}
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
                                                    {post_meta.ad_type == 'adsense' || post_meta.ad_type == 'double_click' ? <option value="amp_story_ads">AMP Story Ad</option> : null}
                                                </optgroup>

                                            </select>
                                            : <select className={(show_form_error && position_data.ab_testing.length <= 0) ? 'quads_form_error' : ''} value={post_meta.position} name="position" onChange={this.adFormChangeHandlerstate} >
                                                <option value="">{__('Select Position', 'quick-adsense-reloaded')}</option>
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
                                                <option value="ad_after_id">{__('Ad After Id', 'quick-adsense-reloaded')}</option>
                                                <option value="ad_after_class">{__('Ad After Class', 'quick-adsense-reloaded')}</option>
                                                <option value="ad_after_customq">{__('Ad After Advance Selector', 'quick-adsense-reloaded')}</option>
                                                <option value="ad_after_html_tag">{__('Ad After HTML Tag', 'quick-adsense-reloaded')}</option>
                                                <option value="amp_ads_in_loops">Ads Inbetween Loop</option>
                                                {quads_localize_data.is_bbpress_exist ?
                                                    <>
                                                        <option value="bbpress_before_ad">BBpress Before Ad</option>
                                                        <option value="bbpress_after_ad">BBpress After Ad</option>
                                                        <option value="bbpress_before_reply">BBpress Before Reply</option>
                                                        <option value="bbpress_after_reply">BBpress After Reply</option>
                                                    </>
                                                    : null}

                                                {this.state.ad_extra_position}
                                                <option value="ad_shortcode">{__('Shortcode (Manual)', 'quick-adsense-reloaded')}</option>
                                            </select>}
                                    </div>
                                    <div className='position_content'>
                                        <div>
                                            {post_meta.position == 'after_image' ? <input min="1" onChange={this.adFormChangeHandlerstate} name="image_number" value={post_meta.image_number} type="number" /> : ''}
                                        </div>
                                        <div>
                                            {post_meta.position == 'before_image' ? <input min="1" onChange={this.adFormChangeHandlerstate} name="image_number" value={post_meta.image_number} type="number" /> : ''}
                                        </div>
                                        <div>
                                            {post_meta.position == 'bbpress_before_reply' || post_meta.position == 'bbpress_after_reply' ?
                                                <div>
                                                    <label>  Inject at
                                                        <input min="1" onChange={this.adFormChangeHandlerstate} name="paragraph_number" value={post_meta.paragraph_number} type="number" />
                                                    </label>
                                                    <input id='repeat_paragraph' checked={post_meta.repeat_paragraph} name="repeat_paragraph" onChange={this.adFormChangeHandlerstate} type="checkbox" />
                                                    <label htmlFor="repeat_paragraph"> {__('Display After Every ', 'quick-adsense-reloaded')}{post_meta.paragraph_number}</label>
                                                </div> : ''}

                                            {post_meta.position == 'after_paragraph' ?
                                                <div>
                                                    <div>
                                                        <label>
                                                            {post_meta.position == 'after_paragraph' ? <input min="1" onChange={this.adFormChangeHandlerstate} name="paragraph_number" value={post_meta.paragraph_number} type="number" /> : ''}


                                                        </label>
                                                        <label htmlFor="enable_on_end_of_post">
                                                            <input id='enable_on_end_of_post' checked={post_meta.enable_on_end_of_post} name="enable_on_end_of_post" onChange={this.adFormChangeHandlerstate} type="checkbox" />
                                                            {__('to', 'quick-adsense-reloaded')} <strong>{__('End of Post', 'quick-adsense-reloaded')}</strong> {__('if fewer paragraphs', 'quick-adsense-reloaded')}</label>
                                                    </div>
                                                    <div>
                                                        <input id='repeat_paragraph' checked={post_meta.repeat_paragraph} name="repeat_paragraph" onChange={this.adFormChangeHandlerstate} type="checkbox" />
                                                        <label htmlFor="repeat_paragraph"> {__('Display After Every ', 'quick-adsense-reloaded')}{post_meta.paragraph_number}</label>
                                                    </div>


                                                </div> : ''}


                                            {!position_data.check_plugin_exist ? <Alert severity="error" className={'check_plugin_exist'}><div > AMP stories plugin not exist </div></Alert> : null}

                                            {post_meta.position == 'after_word_count' ?
                                                <div>
                                                    <label>
                                                        <input min="1" onChange={this.adFormChangeHandlerstate} name="word_count_number" value={post_meta.word_count_number} type="number" />
                                                    </label>
                                                </div> : ''}
                                            {post_meta.position == 'after_the_percentage' ?
                                                <div>
                                                    <label>
                                                        <input min="1" onChange={this.adFormChangeHandlerstate} name="after_the_percentage_value" value={post_meta.after_the_percentage_value} type="number" /> %
                                                    </label>
                                                </div> : ''}
                                            {(show_form_error && post_meta.position == 'after_the_percentage' && (post_meta.g_data_ad_client == '' || parseInt(quads_post_meta.after_the_percentage_value) < 10 || parseInt(quads_post_meta.after_the_percentage_value) > 101)) ? <div className="quads_form_msg"><span className="material-icons">
                                                error_outline</span>Percentage should be
                                                between 10 to 100</div> : ''}

                                            {post_meta.position == 'after_image' ?
                                                <label>
                                                    <input checked={post_meta.image_caption} name="image_caption" onChange={this.adFormChangeHandlerstate} type="checkbox" />
                                                    {__('after', 'quick-adsense-reloaded')} <strong>{__('Image\'s outer', 'quick-adsense-reloaded')} &lt;div&gt; wp-caption</strong> {__('if any.', 'quick-adsense-reloaded')}
                                                </label> : ''}

                                            {post_meta.position == 'before_image' ?
                                                <label>
                                                    <input checked={post_meta.image_caption} name="image_caption" onChange={this.adFormChangeHandlerstate} type="checkbox" />
                                                    {__('before', 'quick-adsense-reloaded')} <strong>{__('Image\'s outer', 'quick-adsense-reloaded')} &lt;div&gt; wp-caption</strong> {__('if any.', 'quick-adsense-reloaded')}
                                                </label> : ''}

                                            {post_meta.position == 'ad_shortcode' && post_meta.quads_ad_old_id ?

                                                <label>

                                                    Post Shortcode: <input name="post_shortcode" id="post_shortcode" type="text" defaultValue={'[quads id=' + (post_meta.quads_ad_old_id).match(/\d+/) + ']'} readOnly />
                                                    PHP:<input name="php_shortcode" id="post_shortcode_php" type="text" defaultValue={"<?php echo do_shortcode('[quads id=" + (post_meta.quads_ad_old_id).match(/\d+/) + "]'); ?>"} readOnly />
                                                </label> : ''}
                                        </div>
                                    </div>
                                </div><a onClick={this.addposition} className="quads-btn quads-btn-primary">Add</a>
                            </div> : ''}
                        <div>{(show_form_error && position_data.ab_testing.length <= 0) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>Select Where Will The AD Appear</div></span> : ''}</div>

                        <div className="quads-target-item-list">
                            {
                                position_data.ab_testing ?
                                    position_data.ab_testing.map((item, index) => (
                                        <div key={index} className="quads-target-item">
                                            <span className="quads-target-label">{item.position}</span>
                                            <span className="quads-target-icon" onClick={this.removeSeleted_list} data-index={index}><Icon>close</Icon></span>
                                        </div>
                                    ))
                                    : ''}
                            {/* <div>{ (post_meta.ab_testing.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>Select at least one Ad</div></span> : ''}</div> */}
                        </div>
                    </div></div>
            </div>
        );
    }
}

export default QuadsAdvancePositionMutiabtesting;
