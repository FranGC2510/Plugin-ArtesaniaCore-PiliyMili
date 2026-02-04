<?php
defined( 'ABSPATH' ) || exit;
/**
 * Vista de la Pestaña Fiscal.
 * @var array $limits Array de límites guardados.
 * @var string $show_widget Estado del widget ('yes'/'no').
 * @var array $gateways Objetos de pasarelas de pago.
 * @var string $option_limits Nombre de la opción en BD.
 * @var string $option_show Nombre de la opción del widget en BD.
 */
?>

<h3>Visualización</h3>
<table class="form-table">
    <tr>
        <th scope="row">Widget Escritorio</th>
        <td>
            <label>
                <input type="checkbox" name="<?php echo esc_attr( $option_show ); ?>" value="yes" <?php checked( 'yes', $show_widget ); ?>>
                Mostrar widget "Estado Pili & Mili" en el Escritorio.
            </label>
        </td>
    </tr>
</table>

<hr>

<h3>Límites Anuales</h3>
<p class="description">Si se activa el bloqueo, al superar estos umbrales, el método de pago se desactivará automáticamente hasta el próximo ejercicio.</p>

<table class="widefat fixed" style="max-width: 900px; margin-top: 15px;">
    <thead>
    <tr>
        <th style="width: 250px;">Método de Pago</th>
        <th>Límite Importe (€) / Año</th>
        <th>Límite Volumen / Año</th>
        <th>Estado</th>
    </tr>
    </thead>
    <tbody>
    <?php if ( empty( $gateways ) ) : ?>
        <tr><td colspan="4">No se han detectado pasarelas de pago activas en WooCommerce.</td></tr>
    <?php else : ?>
        <?php foreach ( $gateways as $id => $gateway ) : ?>
            <?php
            $amt = $limits[ $id ]['amount'] ?? '';
            $ord = $limits[ $id ]['orders'] ?? '';
            $act = isset( $limits[ $id ]['active'] ) && 'yes' === $limits[ $id ]['active'];
            ?>
            <tr>
                <td>
                    <strong><?php echo esc_html( $gateway->get_title() ); ?></strong><br>
                    <small style="color: #666;">ID: <?php echo esc_html( $id ); ?></small>
                </td>

                <td>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <input type="number"
                               name="<?php echo esc_attr( $option_limits ); ?>[<?php echo esc_attr( $id ); ?>][amount]"
                               value="<?php echo esc_attr( $amt ); ?>"
                               step="0.01"
                               min="0"
                               class="regular-text"
                               style="width: 120px;"
                               placeholder="Ej: 30000">
                        <span>€</span>
                    </div>
                </td>

                <td>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <input type="number"
                               name="<?php echo esc_attr( $option_limits ); ?>[<?php echo esc_attr( $id ); ?>][orders]"
                               value="<?php echo esc_attr( $ord ); ?>"
                               step="1"
                               min="0"
                               class="regular-text"
                               style="width: 120px;"
                               placeholder="Ej: 500">
                        <span>pedidos</span>
                    </div>
                </td>

                <td>
                    <label>
                        <input type="checkbox"
                               name="<?php echo esc_attr( $option_limits ); ?>[<?php echo esc_attr( $id ); ?>][active]"
                               value="yes"
                            <?php checked( $act, true ); ?>>
                        Activar Bloqueo
                    </label>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
