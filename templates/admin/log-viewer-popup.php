<?php
/**
 * Vista: Popup del Visor de Logs.
 * * @var string $log_content Contenido del archivo debug.log
 * @var bool   $file_exists Indica si el archivo existe en disco
 * @package Artesania\Core\Templates
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Visor de Logs - Artesanía Core</title>
        <?php
        // Cargamos mínimamente el CSS de admin para los botones estándar de WP
        do_action('admin_enqueue_scripts');
        wp_print_styles('artesania-admin-css');
        ?>
    </head>
    <body class="artesania-log-body">

        <h2 class="artesania-log-title">Visor de Errores (Artesanía Core)</h2>

        <?php if ( $file_exists ) : ?>
            <?php if ( empty( $log_content ) ) : ?>
                <div class="artesania-log-success">
                    El archivo debug.log está limpio. No hay errores registrados.
                </div>
            <?php else : ?>
                <textarea class="artesania-log-textarea" readonly><?php echo esc_textarea( $log_content ); ?></textarea>
            <?php endif; ?>
        <?php else : ?>
            <div class="artesania-log-warning">
                No existe el archivo debug.log (El sistema aún no ha registrado errores).
            </div>
        <?php endif; ?>

        <div class="artesania-log-actions">
            <a href="javascript:window.close();" class="button button-primary button-large">Cerrar Visor</a>
        </div>

    </body>
</html>
