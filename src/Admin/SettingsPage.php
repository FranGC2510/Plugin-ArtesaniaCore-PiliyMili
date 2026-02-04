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
 * Implementación MVC: Separa la lógica de presentación usando vistas en /templates/admin/.
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

        add_filter( 'option_page_capability_artesania_texts_group', [ $this, 'set_group_capability' ] );
        add_filter( 'option_page_capability_artesania_limits_group', [ $this, 'set_group_capability' ] );
    }

    public function set_group_capability() {
        return 'manage_woocommerce';
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
        register_setting( 'artesania_modules_group', self::OPTION_MODULES, [
                'type' => 'array', 'sanitize_callback' => [ $this, 'sanitize_modules_security' ]
        ] );
        register_setting( 'artesania_modules_group', self::OPTION_DEBUG, [
                'type' => 'string', 'sanitize_callback' => [ $this, 'sanitize_debug_security' ]
        ] );
        register_setting( 'artesania_texts_group', self::OPTION_TEXTS, [
                'type' => 'array', 'sanitize_callback' => [ $this, 'sanitize_texts' ]
        ] );
        register_setting( 'artesania_limits_group', self::OPTION_LIMITS );
        register_setting( 'artesania_limits_group', self::OPTION_SHOW_WIDGET );
    }

    /**
     * Carga una vista de administración.
     */
    private function load_view( string $view_name, array $args = [] ): void {
        if ( ! empty( $args ) ) extract( $args );
        $file_path = ARTESANIA_CORE_PATH . 'templates/admin/' . $view_name . '.php';
        if ( file_exists( $file_path ) ) include $file_path;
    }

    /**
     * Renderiza el contenedor principal de la página de ajustes.
     */
    public function render_settings_page(): void {
        if ( ! current_user_can( 'manage_woocommerce' ) ) return;

        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'modules';

        ob_start();
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
        $tab_content = ob_get_clean();

        $this->load_view( 'settings-wrapper', [
                'active_tab'  => $active_tab,
                'tab_content' => $tab_content
        ] );
    }

    private function render_modules_tab(): void {
        $this->load_view( 'tab-modules', [
                'options'       => get_option( self::OPTION_MODULES, [] ),
                'debug_active'  => get_option( self::OPTION_DEBUG, 'no' ),
                'is_admin'      => current_user_can( 'administrator' ),
                'option_modules'=> self::OPTION_MODULES,
                'option_debug'  => self::OPTION_DEBUG
        ] );
    }

    private function render_texts_tab(): void {
        $texts = get_option( self::OPTION_TEXTS, [] );
        $this->load_view( 'tab-texts', [
                'val_stock'     => $texts['stock_msg'] ?? 'Se fabrica bajo pedido. Producto hecho a mano con mucho amor.',
                'val_footer'    => $texts['footer_text'] ?? '© ' . date('Y') . ' Pili & Mili Detalles',
                'option_texts'  => self::OPTION_TEXTS
        ] );
    }

    private function render_fiscal_tab(): void {
        $this->load_view( 'tab-fiscal', [
                'limits'        => get_option( self::OPTION_LIMITS, [] ),
                'show_widget'   => get_option( self::OPTION_SHOW_WIDGET, 'yes' ),
                'gateways'      => \WC()->payment_gateways->get_available_payment_gateways(),
                'option_limits' => self::OPTION_LIMITS,
                'option_show'   => self::OPTION_SHOW_WIDGET
        ] );
    }

    // Callbacks de seguridad
    public function sanitize_modules_security( $input ): array {
        if ( ! current_user_can( 'administrator' ) ) return get_option( self::OPTION_MODULES, [] );
        $clean = [];
        if ( is_array( $input ) ) foreach ( $input as $k => $v ) $clean[ sanitize_key( $k ) ] = '1';
        return $clean;
    }
    public function sanitize_debug_security( $input ): string {
        return ( current_user_can( 'administrator' ) && $input === 'yes' ) ? 'yes' : 'no';
    }
    public function sanitize_texts( $input ): array {
        return [
                'stock_msg'   => isset($input['stock_msg']) ? sanitize_textarea_field($input['stock_msg']) : '',
                'footer_text' => isset($input['footer_text']) ? wp_kses_post($input['footer_text']) : ''
        ];
    }
}