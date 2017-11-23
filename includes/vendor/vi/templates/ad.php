<?php
/**
 * vi ad template
 */
?>

<!-- WP QUADS v. <?php echo QUADS_VERSION; ?>  automatic embeded vi ad -->
<div class="quads-location quads-vi-ad<?php echo $adId; ?>" id="quads-vi-ad<?php echo $adId; ?>" style="min-width:336px;<?php echo $style; ?>">
    <script>
    <?php echo do_shortcode($adCode); ?>
    </script>
</div>