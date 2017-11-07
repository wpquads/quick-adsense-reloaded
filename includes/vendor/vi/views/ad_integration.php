<?php
/**
 * VI Widget
 */
?>

<div id="quads-vi-ad-integration">
    <div>
        <h4>vi stories: customize your video player</h4>
        Input fields for customization here
        <?php //echo $jsTag; ?>
        
<!--        <h4>Automatic Integration</h4>
        Where:<br>
        <label>Post types:</label>
        <label>Paragraph:</label>
        <label>Place:</label>
        When:-->
<!--        <label></label>-->
        <div id="quads-vi-save-settings">
            <button type="submit" id="quads_vi_login_submit" style="" class='button button-primary'>Save</button>
        </div>
    </div>
    <div>
        <h4>Manual Integration</h4>
        <label>Shortcode:
            <input readonly="" id="quads_shortcode_1" type="text" onclick="this.focus();
                    this.select()" value="[quadsvi id=1]" title="Optional: Copy and paste the shortcode into the post editor, click below then press Ctrl + C (PC) or Cmd + C (Mac).">
        </label>
        <br>
        <br>
        <label>PHP Shortcode: 
            <input readonly="" id="quads_php_shortcode_1" type="text" onclick="this.focus();
                    this.select()" style="width:290px;" value="&lt;?php echo do_shortcode('[quadsvi id=1]'); ?&gt;" title="Optional: Copy and paste the PHP code into your theme files, click below then press Ctrl + C (PC) or Cmd + C (Mac).">        
        </label>
        <p></p>
    </div>
</div>





