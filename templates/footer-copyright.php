<?php
/**
 * Vista: Footer Copyright
 * @version 2.5.2
 */
defined( 'ABSPATH' ) || exit;

// Asegurar variable para evitar warnings en editores estrictos
$texto_final = $footer_content ?? '';
?>

<div class="site-info artesania-site-info">
    <?php if ( ! empty( $social_html ) ) : ?>
        <div class="artesania-footer-socials">
            <?php echo $social_html; ?>
        </div>
    <?php endif; ?>

    <div class="artesania-copyright-text">
        <?php echo wp_kses_post( $footer_content ); ?>
    </div>

    <a href="/aviso-legal-y-condiciones-de-uso" class="artesania-legal-link">Aviso Legal</a>
    &nbsp;•&nbsp;
    <a href="/politica-privacidad" class="artesania-legal-link">Privacidad</a>
    &nbsp;•&nbsp;
    <a href="/terminos-y-condiciones" class="artesania-legal-link">Términos</a>
</div>