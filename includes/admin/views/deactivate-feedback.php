<?php 
$reasons = array(
    		1 => '<li><label><input type="radio" name="quads_disable_reason" value="temporary"/>' . __('It is only temporary', 'quick-adsense-reloaded') . '</label></li>',
		2 => '<li><label><input type="radio" name="quads_disable_reason" value="stopped showing ads"/>' . __('I stopped showing ads on my site', 'quick-adsense-reloaded') . '</label></li>',
		3 => '<li><label><input type="radio" name="quads_disable_reason" value="missing feature"/>' . __('I miss a feature', 'quick-adsense-reloaded') . '</label></li>
		<li><input type="text" name="quads_disable_text[]" value="" placeholder="Please describe the feature"/></li>',
		4 => '<li><label><input type="radio" name="quads_disable_reason" value="technical issue"/>' . __('Technical Issue', 'quick-adsense-reloaded') . '</label></li>
		<li><textarea name="quads_disable_text[]" placeholder="' . __('Can we help? Please describe your problem', 'quick-adsense-reloaded') . '"></textarea></li>',
		5 => '<li><label><input type="radio" name="quads_disable_reason" value="other plugin"/>' . __('I switched to another plugin', 'quick-adsense-reloaded') .  '</label></li>
		<li><input type="text" name="quads_disable_text[]" value="" placeholder="Name of the plugin"/></li>',
		6 => '<li><label><input type="radio" name="quads_disable_reason" value="other"/>' . __('Other reason', 'quick-adsense-reloaded') . '</label></li>
		<li><textarea name="quads_disable_text[]" placeholder="' . __('Please specify, if possible', 'quick-adsense-reloaded') . '"></textarea></li>',
    );
shuffle($reasons);
?>


<div id="quick-adsense-reloaded-feedback-overlay" style="display: none;">
    <div id="quick-adsense-reloaded-feedback-content">
	<form action="" method="post">
	    <h3><strong><?php _e('If you have a moment, please let us know why you are deactivating:', 'quick-adsense-reloaded'); ?></strong></h3>
	    <ul>
                <?php 
                foreach ($reasons as $reason){
                    echo $reason;
                }
                ?>
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