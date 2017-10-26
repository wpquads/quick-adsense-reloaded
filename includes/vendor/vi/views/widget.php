<?php
/**
 * VI Widget
 */
?>

<div id="quads-vi-widget">
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
        Watch a <a href="<?php echo $demoPageURL; ?>" rel="external nofollow" target="_blank">demo</a> of how vi stories works.
    <p>
        By clicking sign up you agree to send your current domain, email and affiliate ID to video intelligence & WP QUADS.
    </p>
    <div class="quads-widget-buttons">
        <a href="<?php echo $loginAPI; ?>" class="button button-secondary" id="quads-vi-login"> Login </a> <a href="<?php echo $signupURL; ?>" class="button button-primary" id="quads-vi-signup"> Signup </a> 
    </div>
    <?php
//    $signup = new \wpquads\template('includes/vendor/vi/views/signup', $settings);
//    echo $signup->render();
    ?>
    <div id="quads-vi-signup-fullscreen">
        <div id="quads-vi-signup-container">
            <div id="quads-vi-close"></div>
            <iframe src="<?php echo $signupURL; ?>"></iframe>
        </div>

    </div>
</div>



