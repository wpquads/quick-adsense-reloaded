<?php
/**
 * VI Widget
 */
?>

<div id="quads-vi-ad-integration">
    <div id="quads-vi-customize-player">        
        <?php
        //settings_fields('quads_vi_ads');
        ?>
        <h4 style="font-weight: 500;font-size:18px;color:black;">vi stories: customize your video player</h4>
        <?php
        $form = new wpquads\adSettings();

        echo $form->get()->label("quads_vi_ads[ads][1][type]");
        echo $form->get()->render("quads_vi_ads[ads][1][type]");
        echo '<br>';    
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
        ?>


        <!--            <label>Ad Unit*</label>
                    <select class="quads-select-Type quads-large-size" id='quads_vi_ads[ads][1][type]' name="quads_vi_ads[ads][1][type]">
                        <option value='vi_stories' name='vi_stories'>vi stories / default</option>
                        <option value='outstream' name='outstream'>Outstream</option>
                    </select>-->
        <!--        <br>
                <label>Keywords</label><input type="text" name="quads_vi_ads[ads][1][keywords]">
                <br>
                <label>IAB Category*</label> 
                <select class="quads-select-Type quads-large-size" id='quads_vi_ads[ads][1][iab1]' name="quads_vi_ads[ads][1][iab1]"> -->
        <?php
//foreach ($iab as $key => $value) {
//    echo "<option value='{$value['val0']}' name='{$value['val0']}'>{$value['val1']}</option>";
//}
        ?>
        <!--        </select>
                <br>
                <label>IAB Category tier 2</label> 
                <select class="quads-select-Type quads-large-size" id='quads_vi_ads[ads][1][iab2]' name="quads_vi_ads[ads][1][iab2]"> -->
        <?php
//            foreach ($iab as $key => $value) {
//                echo "<option value='{$value['val0']}' name='{$value['val0']}'>{$value['val1']}</option>";
//            }
        ?>
        <!--        </select>
                <br>
                <label>Language*</label><input type="text" name="quads_vi_ads[ads][1][language]">
                <br>
                <label>Native Background Color</label><input type="text" name="quads_vi_ads[ads][1][bg_color]">
                <br>
                <label>Native Text Color</label><input type="text" name="quads_vi_ads[ads][1][text_color]">
                <br>
                <label>Native Text Font Family</label><input type="text" name="quads_vi_ads[ads][1][txt_font_family]">
                <br>
                <label>Native Text Font Size</label><input type="text" name="quads_vi_ads[ads][1][txt_font_size]">
                <br>
                <label>Optional 1</label><input type="text" name="quads_vi_ads[ads][1][optional1]">
                <br>
                <label>Optional 2</label><input type="text" name="quads_vi_ads[ads][1][optional2]">
                <br>
                <label>Optional 3</label><input type="text" name="quads_vi_ads[ads][1][optional3]">
                <br>-->
        <?php //echo $jsTag;  ?>

        <!--        <h4>Automatic Integration</h4>
                Where:<br>
                <label>Post types:</label>
                <label>Paragraph:</label>
                <label>Place:</label>
                When:-->
        <!--        <label></label>-->
        <p>
            <!--            vi Ad Changes might take some time to take into effect.-->
        </p>
        <div id="quads-vi-save-notice"><span class="quads-spinner" style="float:none;"></span></div>
        <div id="quads-vi-save-settings">
            <input type="submit" id="quads_vi_save_settings_submit" style="" class='button button-primary' value="Save Video Settings">
        </div>

    </div>
    <div id="quads-vi-shortcodes">
        <h4 style="font-weight: 500;font-size:18px;color:black;">Manual Integration</h4>
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





