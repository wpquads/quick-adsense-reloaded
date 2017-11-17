<?php
/**
 * VI Widget
 */

?>
    
    <div id="quads-vi-welcome">
        <p>
            Advertisers pay more for video advertising when it's matched with video content. This new video player will insert both on your page. It increases time on site, and commands a higher CPM than display advertising.
        </p>
        <p>
            You'll see video content that is matched to your sites keywords straight away. A few days after activation you'll begin to receive revenue from advertising served before this video content.
        </p>
        <ul>
            <li> The set up takes only a few minutes</li>
            <li> Up to 10x higher CPM than traditional display advertising</li>
            <li> Users spend longer on your site thanks to professional video content</li>
            <li> The video player is customizable to match your site</li>
        </ul>
        <p>
            Watch a <a href="<?php echo isset($demoPageURL) ? $demoPageURL : ''; ?>" rel="external nofollow" target="_blank">demo</a> of how vi stories works.
        <p>
            By clicking sign up you agree to send your current domain, email and affiliate ID to video intelligence & WP QUADS.
        </p>
        <div class="quads-widget-buttons">
            <a href="<?php echo $loginAPI; ?>" class="button button-secondary" id="quads_vi_login_submit"> Login </a> 
            <a href="<?php echo $signupURL; ?>?email=<?php echo bloginfo('admin_email'); ?>&domain=<?php echo get_site_url(); ?>" class="button button-primary" id="quads-vi-signup"> Signup </a> 
        </div>
    </div>
    
    <div id="quads-vi-signup-fullscreen">
        <div id="quads-vi-signup-container">
            <div id="quads-vi-close"></div>
            <iframe src="<?php echo $signupURL; ?>" scrolling="no"></iframe>
        </div>

    </div>
    
    <div id="quads-vi-login">
        <form action="<?php echo admin_url() . '?quads_action=vi_login' ?>">
            <div class="quads-spinner" id="quads_vi_loading"></div>
            <div id="quads_add_err"></div>
            <div class="container">
                <label><b>E-Mail</b></label>
                <input type="text" placeholder="Enter Username" name="email" id="email" required>

                <label><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="password" id="password" required>

                <button type="submit" id="quads_vi_login_submit" style="display:none;">Login</button>
            </div>
        </form>
    </div>



