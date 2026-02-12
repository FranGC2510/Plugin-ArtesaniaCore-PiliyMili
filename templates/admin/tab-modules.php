<?php
/**
 * Vista: Pestaña de Módulos
 * Actualizada para incluir confirmación de seguridad al desactivar.
 */
defined( 'ABSPATH' ) || exit;

$disabled = $is_admin ? '' : 'disabled="disabled"';
$style    = $is_admin ? '' : 'opacity: 0.6; cursor: not-allowed;';

$modules_list = [
        'checkout'    => [ 'title' => 'Checkout Avanzado', 'desc' => 'Habilita NIF y Facturas.' ],
        'customizer'  => [ 'title' => 'Personalización', 'desc' => 'Habilita campo de texto en producto.' ],
        'slow_design' => [ 'title' => 'Slow Design', 'desc' => 'Mensajes de stock personalizados.' ],
        'frontend'    => [ 'title' => 'Frontend', 'desc' => 'Estilos y Footer personalizados.' ],
        'catalog_mode'=> [ 'title' => 'Modo Catálogo', 'desc' => 'Oculta botones de compra y carrito. Solo muestra productos.' ],
];
?>
<h3>Funcionalidades</h3>
<table class="form-table">
    <?php foreach ( $modules_list as $key => $info ) : ?>
        <?php $checked = isset( $options[ $key ] ) ? $options[ $key ] : '0'; ?>
        <tr>
            <th scope="row" style="<?php echo $style; ?>"><?php echo esc_html( $info['title'] ); ?></th>
            <td>
                <label style="<?php echo $style; ?>">
                    <input type="checkbox"
                           name="<?php echo $option_modules; ?>[<?php echo $key; ?>]"
                           value="1"
                            <?php checked( '1', $checked ); ?>
                            <?php echo $disabled; ?>
                           onclick="if (!this.checked && !confirm('¿Estás seguro de que deseas desactivar el módulo &quot;<?php echo esc_js( $info['title'] ); ?>&quot;?\n\nEsto podría ocultar funcionalidades visibles en tu tienda.')) return false;">

                    <?php echo esc_html( $info['desc'] ); ?>
                </label>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br><h3>Herramientas Avanzadas</h3><hr>
<table class="form-table">
    <tr>
        <th scope="row" style="<?php echo $style; ?>">Modo Debug</th>
        <td>
            <label style="<?php echo $style; ?>">
                <input type="checkbox" name="<?php echo $option_debug; ?>" value="yes" <?php checked( 'yes', $debug_active ); ?> <?php echo $disabled; ?>>
                Activar Registro de Depuración (Logs)
            </label>
            <p class="description">Guarda errores en <code>debug.log</code> en lugar de mostrarlos en pantalla.</p>
        </td>
    </tr>
    <tr>
        <th scope="row" style="<?php echo $style; ?>">Historial de Errores</th>
        <td>
            <?php
            $view_url  = admin_url('options-general.php?page=artesania-control&tab=modules&artesania_action=view_log');
            $clean_url = wp_nonce_url( admin_url('options-general.php?page=artesania-control&tab=modules&artesania_action=clear_log'), 'artesania_clean_log_nonce' );
            ?>

            <a href="<?php echo esc_url( $view_url ); ?>"
               class="button button-secondary"
               target="_blank"
                    <?php echo $disabled; ?>>
                Ver Log Completo
            </a>

            <a href="<?php echo esc_url( $clean_url ); ?>"
               class="button button-link-delete artesania-btn-danger"
               onclick="return confirm('¿Estás seguro de que quieres borrar todo el historial de errores?');"
                    <?php echo $disabled; ?>>
                Limpiar Archivo
            </a>

            <p class="description">Abre una ventana emergente para inspeccionar el historial de errores.</p>
        </td>
    </tr>
</table>
