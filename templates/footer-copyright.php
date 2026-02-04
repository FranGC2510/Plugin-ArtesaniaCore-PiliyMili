<?php
/**
 * Vista: Footer Copyright
 * @version 2.4.0
 */
defined( 'ABSPATH' ) || exit;

// Asegurar variable para evitar warnings en editores estrictos
$texto_final = $footer_content ?? '';
?>

<div class="site-info artesania-site-info">
    <?php echo wp_kses_post( $texto_final ); ?>

    <span class="artesania-separator">|</span>

    <a href="/aviso-legal-y-condiciones-de-uso" class="artesania-legal-link">Aviso Legal</a>
    &nbsp;•&nbsp;
    <a href="/politica-privacidad" class="artesania-legal-link">Privacidad</a>
    &nbsp;•&nbsp;
    <a href="/terminos-y-condiciones" class="artesania-legal-link">Términos</a>
</div>