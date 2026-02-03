<?php defined( 'ABSPATH' ) || exit; ?>

<div class="artesania-section-wrapper artesania-mb-50">
    <h2 class="wp-block-heading artesania-title-top artesania-mt-60 artesania-mb-20">Novedades</h2>
    <?php echo do_shortcode('[products limit="5" columns="5" orderby="date" order="DESC"]'); ?>
</div>