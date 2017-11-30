<?php
/**
 * VI Widget
 */
?>

<div id="quads-vi-ad-integration">
    <div id="quads-vi-customize-player">        

        <h4 style="font-weight: 500;font-size:18px;color:black;">vi stories: customize your video player</h4>
        <?php
        $form = new \wpquads\adSettings();

        
        //echo $form->get()->label("quads_vi_ads[ads][1][type]");
        //echo $form->get()->render("quads_vi_ads[ads][1][type]");
        //echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][keywords]");
        echo $form->get()->render("quads_vi_ads[ads][1][keywords]");
        echo $form->get()->tooltip("quads_vi_ads[ads][1][keywords]");
        echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][iab1]");
        echo $form->get()->render("quads_vi_ads[ads][1][iab1]");
        echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][iab2]");
        echo $form->get()->render("quads_vi_ads[ads][1][iab2]");
        echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][language]");
        echo $form->get()->render("quads_vi_ads[ads][1][language]");
        echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][bg_color]");
        echo $form->get()->render("quads_vi_ads[ads][1][bg_color]");
        echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][text_color]");
        echo $form->get()->render("quads_vi_ads[ads][1][text_color]");
        echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][txt_font_family]");
        echo $form->get()->render("quads_vi_ads[ads][1][txt_font_family]");
        echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][font_size]");
        echo $form->get()->render("quads_vi_ads[ads][1][font_size]");
        echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][optional1]");
        echo $form->get()->render("quads_vi_ads[ads][1][optional1]");
        echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][optional2]");
        echo $form->get()->render("quads_vi_ads[ads][1][optional2]");
        echo '<br>';
        echo $form->get()->label("quads_vi_ads[ads][1][optional3]");
        echo $form->get()->render("quads_vi_ads[ads][1][optional3]");
        echo '<br>';
        echo $form->get()->render("quads_vi_ads[ads][1][code]");
        ?>
        <div id="quads-vi-layout">
            <h3 style="margin-bottom:8px;">Layout</h3>
            <br>
            <?php echo $form->get()->render("quads_vi_ads[ads][1][align]"); ?>
            <br>
            <br>
            Margin Top: <?php echo $form->get()->render("quads_vi_ads[ads][1][marginTop]");             ?>px &nbsp;
            Right: <?php echo $form->get()->render("quads_vi_ads[ads][1][marginRight]");  ?>px &nbsp;
            Bottom: <?php echo $form->get()->render("quads_vi_ads[ads][1][marginBottom]");  ?> px &nbsp;
            Left: <?php echo $form->get()->render("quads_vi_ads[ads][1][marginLeft]");   ?> px
            <?php echo $form->get()->tooltip("quads_vi_ads[ads][1][marginLeft]"); ?>


            <br>
        </div>
        <div id="quads_vi_automatic_integration">
        <h3 style="margin-bottom:8px;">Automatic Placement</h3>
        <div style="border-top: 1px solid #c3c3c3;"></div>
        <h3>Where:</h3>

        <?php
        echo $form->get()->label("quads_vi_ads[ads][1][position]");
        echo $form->get()->render("quads_vi_ads[ads][1][position]");
        ?>
        <br>
        <h3>Exclude Ad From:</h3>
        <?php
        echo $form->get()->label("quads_vi_ads[ads][1][excludedPostTypes]");
        echo $form->get()->render("quads_vi_ads[ads][1][excludedPostTypes]");
        echo $form->get()->tooltip("quads_vi_ads[ads][1][excludedPostTypes]");
        ?>
        <br>
        <?php
        echo $form->get()->label("quads_vi_ads[ads][1][excludedExtraPages]");
        echo $form->get()->render("quads_vi_ads[ads][1][excludedExtraPages]");
        echo $form->get()->tooltip("quads_vi_ads[ads][1][excludedExtraPages]");
        ?>
        <br>
        <?php
        echo $form->get()->label("quads_vi_ads[ads][1][excludedUserRoles]");
        echo $form->get()->render("quads_vi_ads[ads][1][excludedUserRoles]");
        echo $form->get()->tooltip("quads_vi_ads[ads][1][excludedUserRoles]");
        ?>
        <br>
        <?php
        echo $form->get()->label("quads_vi_ads[ads][1][excludedPostIds]");
        echo $form->get()->render("quads_vi_ads[ads][1][excludedPostIds]");
        echo $form->get()->tooltip("quads_vi_ads[ads][1][excludedPostIds]");
        ?>
        <br>
        <br>
    </div>
        <div id="quads-vi-save-notice"><span class="quads-spinner" style="float:none;"></span></div>
        <div id="quads-vi-save-settings">
            <input type="submit" id="quads_vi_save_settings_submit" style="" class='button button-primary' value="Save Video Settings">
        </div>

    </div>
    <div id="quads-vi-shortcodes">
        <h4 style="font-weight: 500;font-size:18px;color:black;">Manual Placement</h4>
        <label>Shortcode:</label>
        <input readonly="" id="quads_shortcode_1" type="text" onclick="this.focus();
                this.select()" value="[quadsvi id=1]" title="Optional: Copy and paste the shortcode into the post editor, click below then press Ctrl + C (PC) or Cmd + C (Mac).">
        <br>
        <br>
        <label>PHP Shortcode: </label>
        <input readonly="" id="quads_php_shortcode_1" type="text" onclick="this.focus();
                this.select()" style="width:310px;" value="&lt;?php echo do_shortcode('[quadsvi id=1]'); ?&gt;" title="Optional: Copy and paste the PHP code into your theme files, click below then press Ctrl + C (PC) or Cmd + C (Mac).">        
        <p></p>
    </div>
</div>





