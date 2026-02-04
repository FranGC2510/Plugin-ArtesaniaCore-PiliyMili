<?php
defined( 'ABSPATH' ) || exit;
// Variables recibidas: $options, $debug_active, $is_admin, $option_modules, $option_debug

$disabled = $is_admin ? '' : 'disabled="disabled"';
$style    = $is_admin ? '' : 'opacity: 0.6; cursor: not-allowed;';

$modules_list = [
    'checkout'    => [ 'title' => 'Checkout Avanzado', 'desc' => 'Habilita NIF y Facturas.' ],
    'customizer'  => [ 'title' => 'Personalización', 'desc' => 'Habilita campo de texto en producto.' ],
    'slow_design' => [ 'title' => 'Slow Design', 'desc' => 'Mensajes de stock personalizados.' ],
    'frontend'    => [ 'title' => 'Frontend', 'desc' => 'Estilos y Footer personalizados.' ],
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
                    <input type="checkbox" name="<?php echo $option_modules; ?>[<?php echo $key; ?>]" value="1" <?php checked( '1', $checked ); ?> <?php echo $disabled; ?>>
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
        </td>
    </tr>
</table>
