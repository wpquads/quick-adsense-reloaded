<div id="quick-adsense-reloaded-feedback-overlay" style="display: none;">
    <div id="quick-adsense-reloaded-feedback-content">
	<form action="" method="post">
	    <h3><strong><?php _e('If you have a moment, please let us know why you are deactivating:', 'quick-adsense-reloaded'); ?></strong></h3>
	    <ul>
		<li><label><input type="radio" name="quads_disable_reason" value="temporary"/><?php _e('It is only temporary', 'quick-adsense-reloaded'); ?></label></li>
		<li><label><input type="radio" name="quads_disable_reason" value="stopped showing ads"/><?php _e('I stopped showing ads on my site', 'quick-adsense-reloaded'); ?></label></li>
		<li><label><input type="radio" name="quads_disable_reason" value="missing feature"/><?php _e('I miss a feature', 'quick-adsense-reloaded'); ?></label></li>
		<li><input type="text" name="quads_disable_text[]" value="" placeholder="Which one?"/></li>
		<li><label><input type="radio" name="quads_disable_reason" value="technical issue"/><?php _e('I have a technical issue', 'quick-adsense-reloaded'); ?></label></li>
		<li><textarea name="quads_disable_text[]" placeholder="<?php _e('Can we help? Please let us know how', 'quick-adsense-reloaded'); ?>"></textarea></li>
		<li><label><input type="radio" name="quads_disable_reason" value="other plugin"/><?php _e('I switched to another plugin', 'quick-adsense-reloaded'); ?></label></li>
		<li><input type="text" name="quads_disable_text[]" value="" placeholder="Which one?"/></li>
		<li><label><input type="radio" name="quads_disable_reason" value="other"/><?php _e('other reason', 'quick-adsense-reloaded'); ?></label></li>
		<li><textarea name="quads_disable_text[]" placeholder="<?php _e('Please specify, if possible', 'quick-adsense-reloaded'); ?>"></textarea></li>
	    </ul>
	    <?php if ($email) : ?>
    	    <input type="hidden" name="quads_disable_from" value="<?php echo $email; ?>"/>
	    <?php endif; ?>
	    <input id="quick-adsense-reloaded-feedback-submit" class="button button-primary" type="submit" name="quads_disable_submit" value="<?php _e('Submit & Deactivate', 'quick-adsense-reloaded'); ?>"/>
	    <a class="button"><?php _e('Only Deactivate', 'quick-adsense-reloaded'); ?></a>
	    <a class="quick-adsense-reloaded-feedback-not-deactivate" href="#"><?php _e('Don\'t deactivate', 'quick-adsense-reloaded'); ?></a>
	</form>
    </div>
</div>