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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    echo '<link rel="stylesheet" href="' . esc_url( includes_url( 'css/buttons.min.css' ) ) . '">';

    echo '<link rel="stylesheet" href="' . esc_url( ARTESANIA_CORE_URL . 'assets/css/admin.css' ) . '?ver=' . esc_attr( ARTESANIA_CORE_VERSION ) . '">';
    ?>

    <style>
        /* Pequeño ajuste para que el botón se vea bien sin cargar todo el core de WP */
        body.artesania-log-body { margin: 0; }
        .button-large { line-height: 30px; height: 32px; padding: 0 12px; }
    </style>
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
    <a href="#" onclick="window.close(); return false;" class="button button-primary button-large">Cerrar Visor</a>
</div>

</body>
</html>
