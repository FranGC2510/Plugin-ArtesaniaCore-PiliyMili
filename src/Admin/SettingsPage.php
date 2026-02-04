<?php
declare(strict_types=1);

namespace Artesania\Core\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class SettingsPage
 *
 * Panel de Control Principal.
 * Gestiona configuración modular, textos personalizados, límites fiscales y debug.
 *
 * @package Artesania\Core\Admin
 * @version 2.4.0
 */
class SettingsPage {

    private const OPTION_MODULES     = 'artesania_active_modules';
    private const OPTION_TEXTS       = 'artesania_custom_texts';
    private const OPTION_LIMITS      = 'artesania_sales_limits';
    private const OPTION_DEBUG       = 'artesania_debug_mode';
    private const OPTION_SHOW_WIDGET = 'artesania_show_dashboard_widget';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function add_admin_menu(): void {
        add_options_page(
                'Control de Ventas Pili & Mili',
                'Pili & Mili Control',
                'manage_woocommerce',
                'artesania-control',
                [ $this, 'render_settings_page' ]
        );
    }

    public function register_settings(): void {
        // Grupo: Módulos y Debug (Solo Admin)
        register_setting( 'artesania_modules_group', self::OPTION_MODULES, [
                'type'              => 'array',
                'sanitize_callback' => [ $this, 'sanitize_modules_security' ]
        ] );
        register_setting( 'artesania_modules_group', self::OPTION_DEBUG, [
                'type'              => 'string',
                'sanitize_callback' => [ $this, 'sanitize_debug_security' ]
        ] );

        // Grupo: Textos (Admin y Gestor)
        register_setting( 'artesania_texts_group', self::OPTION_TEXTS, [
                'type'              => 'array',
                'sanitize_callback' => [ $this, 'sanitize_texts' ]
        ] );

        // Grupo: Fiscal y Widget (Admin y Gestor)
        register_setting( 'artesania_limits_group', self::OPTION_LIMITS );
        register_setting( 'artesania_limits_group', self::OPTION_SHOW_WIDGET );
    }

    public function render_settings_page(): void {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'modules';
        ?>
        <div class="wrap">
            <h1>🌸 Control Pili & Mili (v2.4)</h1>

            <nav class="nav-tab-wrapper">
                <a href="?page=artesania-control&tab=modules" class="nav-tab <?php echo $active_tab === 'modules' ? 'nav-tab-active' : ''; ?>">🧩 Módulos</a>
                <a href="?page=artesania-control&tab=texts" class="nav-tab <?php echo $active_tab === 'texts' ? 'nav-tab-active' : ''; ?>">✍️ Textos</a>
                <a href="?page=artesania-control&tab=fiscal" class="nav-tab <?php echo $active_tab === 'fiscal' ? 'nav-tab-active' : ''; ?>">⚖️ Fiscal</a>
            </nav>

            <div class="artesania-tab-content" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-top: 0;">
                <form method="post" action="options.php">
                    <?php
                    switch ( $active_tab ) {
                        case 'texts':
                            settings_fields( 'artesania_texts_group' );
                            $this->render_texts_tab();
                            break;
                        case 'fiscal':
                            settings_fields( 'artesania_limits_group' );
                            $this->render_fiscal_tab();
                            break;
                        case 'modules':
                        default:
                            settings_fields( 'artesania_modules_group' );
                            $this->render_modules_tab();
                            break;
                    }

                    if ( $active_tab === 'modules' && ! current_user_can( 'administrator' ) ) {
                        echo '<p class="description" style="margin-top:20px;">⚠️ Solo el Administrador puede gestionar módulos.</p>';
                    } else {
                        submit_button();
                    }
                    ?>
                </form>
            </div>
        </div>
        <?php
    }

    private function render_modules_tab(): void {
        $options      = get_option( self::OPTION_MODULES, [] );
        $debug_active = get_option( self::OPTION_DEBUG, 'no' );
        $is_admin     = current_user_can( 'administrator' );
        $disabled     = $is_admin ? '' : 'disabled="disabled"';
        $style        = $is_admin ? '' : 'opacity: 0.6; cursor: not-allowed;';

        $modules = [
                'checkout'    => [ 'title' => 'Checkout Avanzado', 'desc' => 'Habilita NIF y Facturas.' ],
                'customizer'  => [ 'title' => 'Personalización', 'desc' => 'Habilita campo de texto en producto.' ],
                'slow_design' => [ 'title' => 'Slow Design', 'desc' => 'Mensajes de stock personalizados.' ],
                'frontend'    => [ 'title' => 'Frontend', 'desc' => 'Estilos y Footer personalizados.' ],
        ];
        ?>
        <h3>🔌 Funcionalidades</h3>
        <table class="form-table">
            <?php foreach ( $modules as $key => $info ) : ?>
                <?php $checked = isset( $options[ $key ] ) ? $options[ $key ] : '0'; ?>
                <tr>
                    <th scope="row" style="<?php echo $style; ?>"><?php echo esc_html( $info['title'] ); ?></th>
                    <td>
                        <label style="<?php echo $style; ?>">
                            <input type="checkbox" name="<?php echo self::OPTION_MODULES; ?>[<?php echo $key; ?>]" value="1" <?php checked( '1', $checked ); ?> <?php echo $disabled; ?>>
                            <?php echo esc_html( $info['desc'] ); ?>
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <br><h3>🐛 Herramientas Avanzadas</h3><hr>
        <table class="form-table">
            <tr>
                <th scope="row" style="<?php echo $style; ?>">Modo Debug</th>
                <td>
                    <label style="<?php echo $style; ?>">
                        <input type="checkbox" name="<?php echo self::OPTION_DEBUG; ?>" value="yes" <?php checked( 'yes', $debug_active ); ?> <?php echo $disabled; ?>>
                        Activar Registro de Depuración (Logs)
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

    private function render_texts_tab(): void {
        $texts      = get_option( self::OPTION_TEXTS, [] );
        $val_stock  = $texts['stock_msg'] ?? 'Se fabrica bajo pedido. Producto hecho a mano con mucho amor.';
        $val_footer = $texts['footer_text'] ?? '© ' . date('Y') . ' Pili & Mili Detalles';
        ?>
        <h3>✍️ Textos Personalizados</h3>
        <table class="form-table">
            <tr>
                <th scope="row">Mensaje Stock</th>
                <td><textarea name="<?php echo self::OPTION_TEXTS; ?>[stock_msg]" rows="3" class="large-text code"><?php echo esc_textarea( $val_stock ); ?></textarea></td>
            </tr>
            <tr>
                <th scope="row">Pie de Página</th>
                <td><input type="text" name="<?php echo self::OPTION_TEXTS; ?>[footer_text]" value="<?php echo esc_attr( $val_footer ); ?>" class="regular-text"></td>
            </tr>
        </table>
        <?php
    }

    private function render_fiscal_tab(): void {
        $limits      = get_option( self::OPTION_LIMITS, [] );
        $show_widget = get_option( self::OPTION_SHOW_WIDGET, 'yes' );
        $gateways    = \WC()->payment_gateways->get_available_payment_gateways();
        ?>
        <h3>📊 Visualización</h3>
        <table class="form-table">
            <tr>
                <th scope="row">Widget Escritorio</th>
                <td>
                    <label>
                        <input type="checkbox" name="<?php echo self::OPTION_SHOW_WIDGET; ?>" value="yes" <?php checked( 'yes', $show_widget ); ?>>
                        Mostrar widget "Estado Pili & Mili"
                    </label>
                </td>
            </tr>
        </table>
        <hr>
        <h3>⚖️ Límites Anuales</h3>
        <table class="widefat fixed" style="max-width: 900px; margin-top: 15px;">
            <thead>
            <tr>
                <th>Método</th>
                <th>Límite €</th>
                <th>Límite Pedidos</th>
                <th>Estado</th>
            </tr>
            </thead>
            <tbody>
            <?php if ( empty( $gateways ) ) : ?>
                <tr><td colspan="4">No hay pasarelas activas.</td></tr>
            <?php else : ?>
                <?php foreach ( $gateways as $id => $gateway ) : ?>
                    <?php
                    $amt = $limits[ $id ]['amount'] ?? '';
                    $ord = $limits[ $id ]['orders'] ?? '';
                    $act = isset( $limits[ $id ]['active'] ) && 'yes' === $limits[ $id ]['active'];
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $gateway->get_title() ); ?></strong><br><small><?php echo esc_html( $id ); ?></small></td>
                        <td><input type="number" name="<?php echo self::OPTION_LIMITS; ?>[<?php echo $id; ?>][amount]" value="<?php echo esc_attr( $amt ); ?>" step="0.01" min="0"> €</td>
                        <td><input type="number" name="<?php echo self::OPTION_LIMITS; ?>[<?php echo $id; ?>][orders]" value="<?php echo esc_attr( $ord ); ?>" step="1" min="0"> ped.</td>
                        <td><label><input type="checkbox" name="<?php echo self::OPTION_LIMITS; ?>[<?php echo $id; ?>][active]" value="yes" <?php checked( $act, true ); ?>> Activar Bloqueo</label></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Sanitización de seguridad para Módulos (Solo Admin).
     */
    public function sanitize_modules_security( $input ): array {
        if ( ! current_user_can( 'administrator' ) ) return get_option( self::OPTION_MODULES, [] );
        $clean = [];
        if ( is_array( $input ) ) foreach ( $input as $k => $v ) $clean[ sanitize_key( $k ) ] = '1';
        return $clean;
    }

    /**
     * Sanitización de seguridad para Debug (Solo Admin).
     */
    public function sanitize_debug_security( $input ): string {
        return ( current_user_can( 'administrator' ) && $input === 'yes' ) ? 'yes' : 'no';
    }

    /**
     * Sanitización de textos (HTML permitido en footer).
     */
    public function sanitize_texts( $input ): array {
        return [
                'stock_msg'   => isset($input['stock_msg']) ? sanitize_textarea_field($input['stock_msg']) : '',
                'footer_text' => isset($input['footer_text']) ? wp_kses_post($input['footer_text']) : ''
        ];
    }
}