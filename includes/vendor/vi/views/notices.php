<?php
/*
 * Vi Notices
 */
?>



<div class="quads-banner-wrapper notice <?php echo $type; ?>">
  <section class="quads-banner-content">
    <div class="quads-banner-columns">
      <main class="quads-banner-main"><?php echo $message; ?></main>
      <aside class="quads-banner-sidebar-second" style="margin-right:30px;"><p style="text-align:center;"><img src="<?php echo QUADS_PLUGIN_URL; ?>assets/images/vi_quads_logo.png" width="152" height="70"></p></aside>
    </div>
    <!--<aside class="quads-banner-close"><div style="margin-top:5px;"><a href="'.admin_url().'admin.php?page=quads-settings&quads-action=close_vi_notice" class="quads-notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a></div></aside>//-->
  </section>
</div>

