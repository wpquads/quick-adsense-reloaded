<?php 
$reasons = array(
    		1 => '<li><label><input type="radio" name="quads_disable_reason" value="temporary"/>' . esc_html__('It is only temporary', 'quick-adsense-reloaded') . '</label></li>',
		2 => '<li><label><input type="radio" name="quads_disable_reason" value="stopped showing ads"/>' . esc_html__('I stopped showing ads on my site', 'quick-adsense-reloaded') . '</label></li>',
		3 => '<li><label><input type="radio" name="quads_disable_reason" value="missing feature"/>' . esc_html__('I miss a feature', 'quick-adsense-reloaded') . '</label></li>
		<li><input type="text" name="quads_disable_text[]" value="" placeholder="Please describe the feature"/></li>',
		4 => '<li><label><input type="radio" name="quads_disable_reason" value="technical issue"/>' . esc_html__('Technical Issue', 'quick-adsense-reloaded') . '</label></li>
		<li><textarea name="quads_disable_text[]" placeholder="' . esc_html__('Can we help? Please describe your problem', 'quick-adsense-reloaded') . '"></textarea></li>',
		5 => '<li><label><input type="radio" name="quads_disable_reason" value="other plugin"/>' . esc_html__('I switched to another plugin', 'quick-adsense-reloaded') .  '</label></li>
		<li><input type="text" name="quads_disable_text[]" value="" placeholder="Name of the plugin"/></li>',
		6 => '<li><label><input type="radio" name="quads_disable_reason" value="other"/>' . esc_html__('Other reason', 'quick-adsense-reloaded') . '</label></li>
		<li><textarea name="quads_disable_text[]" placeholder="' . esc_html__('Please specify, if possible', 'quick-adsense-reloaded') . '"></textarea></li>',
    );
shuffle($reasons);
?>
<div id="quick-adsense-reloaded-feedback-overlay" style="display: none;">
    <div id="quick-adsense-reloaded-feedback-content">
	<form action="" method="post">
	<input type="hidden" name="quads_feedback_nonce" value="<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ echo wp_create_nonce( 'quads_feedback_nonce');?>">
	    <h3><strong><?php esc_html_e('If you have a moment, please let us know why you are deactivating:', 'quick-adsense-reloaded'); ?></strong></h3>
	    <ul>
                <?php 
                foreach ($reasons as $reason){
                    //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason: Escaping is done above
                    echo $reason;
                }
                ?>
	    </ul>
	    <?php if ($email) : ?>
    	    <input type="hidden" name="quads_disable_from" value="<?php echo esc_attr( $email ); ?>"/>
	    <?php endif; ?>
	    <input id="quick-adsense-reloaded-feedback-submit" class="button button-primary" type="submit" name="quads_disable_submit" value="<?php esc_html_e('Submit & Deactivate', 'quick-adsense-reloaded'); ?>"/>
	    <a class="button"><?php esc_html_e('Only Deactivate', 'quick-adsense-reloaded'); ?></a>
	    <a class="quick-adsense-reloaded-feedback-not-deactivate" href="#"><?php esc_html_e('Don\'t deactivate', 'quick-adsense-reloaded'); ?></a>
	</form>
    </div>
</div>