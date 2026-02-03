<?php defined( 'ABSPATH' ) || exit; ?>

<h2 class="wp-block-heading artesania-title-top">
    Explora <?php echo esc_html( $category_name ); ?>
</h2>

<?php
// Renderizamos las subcategorías usando el ID que nos pasa el Manager
echo do_shortcode('[product_categories limit="6" columns="6" parent="' . esc_attr( (string)$term_id ) . '"]');
?>

<h2 class="wp-block-heading artesania-title-bottom">Productos</h2>