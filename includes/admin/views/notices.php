<?php
/*
 * WP QUADS Notices
 */
?>



<div class="quads-banner-wrapper notice <?php echo $type; ?>">
  <section class="quads-banner-content">
    <div class="quads-banner-columns">
        <main class="quads-banner-main"><p><?php echo $message; ?></p></main>
      <aside class="quads-banner-sidebar-second" style="margin-right:30px;"></p></aside>
    </div>
    <aside class="quads-banner-close"><div style="margin-top:5px;"><a href="<?php echo admin_url();?>admin.php?page=quads-settings&quads-action=<?php echo $action; ?>" class="quads-notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a></div></aside>
  </section>
</div>

