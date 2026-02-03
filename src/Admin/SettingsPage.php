<?php
namespace Artesania\Core\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class SettingsPage
 *
 * Renderiza la página de ajustes del plugin en el panel de control.
 * Permite la configuración dinámica de límites basada en las pasarelas activas.
 *
 * @package Artesania\Core\Admin
 * @author  Fco Javier García Cañero
 * @version 2.3.0
 */
class SettingsPage {

    /**
     * @var string Nombre de la opción en wp_options.
     */
    private $option_name = 'artesania_sales_limits';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Registra la página en el menú de ajustes.
     */
    public function add_admin_menu(): void {
        add_options_page(
            'Control de Ventas Pili & Mili',
            'Pili & Mili Control',
            'manage_options',
            'artesania-control',
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Registra el grupo de configuraciones.
     */
    public function register_settings(): void {
        register_setting( 'artesania_control_group', $this->option_name );
    }

    /**
     * Renderiza la interfaz de usuario.
     */
    public function render_settings_page(): void {
        $limits   = get_option( $this->option_name, [] );
        $gateways = WC()->payment_gateways->get_available_payment_gateways();
        ?>
        <div class="wrap">
            <h1>Control de Ventas Pili & Mili (Fiscal)</h1>
            <p>Configuración de <strong>Límites Anuales</strong>. Al superar estos umbrales, el método de pago se desactivará automáticamente hasta el próximo ejercicio fiscal.</p>

            <form method="post" action="options.php">
                <?php settings_fields( 'artesania_control_group' ); ?>

                <table class="widefat fixed" style="max-width: 800px; margin-top: 20px;">
                    <thead>
                    <tr>
                        <th>Método de Pago</th>
                        <th>Límite Importe (€) / Año</th>
                        <th>Límite Volumen / Año</th>
                        <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ( empty( $gateways ) ) : ?>
                        <tr><td colspan="4">No se han detectado pasarelas de pago activas.</td></tr>
                    <?php else : ?>
                        <?php foreach ( $gateways as $id => $gateway ) : ?>
                            <?php
                            $amount_limit = isset( $limits[ $id ]['amount'] ) ? $limits[ $id ]['amount'] : '';
                            $order_limit  = isset( $limits[ $id ]['orders'] ) ? $limits[ $id ]['orders'] : '';
                            $is_active    = isset( $limits[ $id ]['active'] ) && 'yes' === $limits[ $id ]['active'];
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html( $gateway->get_title() ); ?></strong><br>
                                    <small class="description">ID: <?php echo esc_html( $id ); ?></small>
                                </td>
                                <td>
                                    <input type="number"
                                           name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>][amount]"
                                           value="<?php echo esc_attr( $amount_limit ); ?>"
                                           step="0.01" min="0" placeholder="Ej: 30000"> €
                                </td>
                                <td>
                                    <input type="number"
                                           name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>][orders]"
                                           value="<?php echo esc_attr( $order_limit ); ?>"
                                           step="1" min="0" placeholder="Ej: 500"> pedidos
                                </td>
                                <td>
                                    <label>
                                        <input type="checkbox"
                                               name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $id ); ?>][active]"
                                               value="yes" <?php checked( $is_active, true ); ?>>
                                        Activar Bloqueo
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>

                <p class="description" style="margin-top: 20px;">
                    * <strong>Nota:</strong> El contador se reinicia automáticamente el 1 de Enero de cada año.
                </p>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}