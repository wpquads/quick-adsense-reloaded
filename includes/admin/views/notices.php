<?php
/*
 * WP QUADS Notices
 */
?>
<div class="quads-banner-wrapper notice <?php echo esc_attr($type); ?>">
  <section class="quads-banner-content">
    <div class="quads-banner-columns">
        <main class="quads-banner-main"><p><?php echo wp_kses_post($message); ?></p></main>
      <aside class="quads-banner-sidebar-second" style="margin-right:30px;"></p></aside>
    </div>
    <aside class="quads-banner-close"><div style="margin-top:5px;"><a href="<?php echo esc_url(admin_url('admin.php?page=quads-settings&quads-action='.$action)) ;?>" class="quads-notice-dismiss"><span class="screen-reader-text"><?php echo esc_html('Dismiss this notice.','quick-adsense-reloaded')?></span></a></div></aside>
  </section>
</div>

