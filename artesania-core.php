<?php
/**
 * Plugin Name: Artesanía Core (Lógica de Negocio)
 * Description: Plugin estructural: Personalización, Stock, Checkout y Diseño.
 * Version: 1.5.0
 * Author: WP E-Comm Architect
 */

namespace Artesania\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ==========================================
// 1. PERSONALIZACIÓN DEL FOOTER (PIE DE PÁGINA)
// ==========================================

// Enganchamos nuestra función de limpieza al inicio de WordPress
// Usamos __NAMESPACE__ para que encuentre la función correctamente
add_action( 'init', __NAMESPACE__ . '\\artesania_personalizar_footer' );

function artesania_personalizar_footer() {
    // Quitamos los créditos originales de Storefront
    remove_action( 'storefront_footer', 'storefront_credit', 20 );

    // Añadimos nuestro propio copyright
    add_action( 'storefront_footer', __NAMESPACE__ . '\\artesania_nuevo_copyright', 20 );
}

function artesania_nuevo_copyright() {
    ?>
    <div class="site-info" style="text-align:center; padding: 2em 0; font-size: 0.9em; clear:both; border-top: 1px solid #f0f0f0; margin-top: 2em;">
        &copy; <?php echo date( 'Y' ); ?> <strong>PiliYMili</strong>

        <span style="margin: 0 10px; color: #ccc;">|</span>

        <a href="/aviso-legal-y-condiciones-de-uso" style="color:inherit; text-decoration:none;">Aviso Legal</a>
        &nbsp;•&nbsp;
        <a href="/politica-privacidad" style="color:inherit; text-decoration:none;">Privacidad</a>
        &nbsp;•&nbsp;
        <a href="/terminos-y-condiciones" style="color:inherit; text-decoration:none;">Términos</a>
    </div>
    <?php
}

// --- SEPARAR COLECCIONES Y PRODUCTOS EN LA TIENDA ---

add_action( 'woocommerce_before_shop_loop', __NAMESPACE__ . '\\artesania_cabeceras_tienda', 5 );

function artesania_cabeceras_tienda() {
    // Solo actuamos en la página principal de la Tienda
    if ( is_shop() ) {

        // 1. TÍTULO Y REJILLA DE COLECCIONES
        echo '<h2 class="wp-block-heading" style="text-align:center; text-transform:uppercase; letter-spacing:3px; margin-top:10px; margin-bottom:30px; font-size:24px;">COLECCIONES</h2>';

        // Mostramos las categorías (ids: parent="0" muestra solo las principales)
        echo do_shortcode('[product_categories limit="6" columns="3" parent="0"]');

        // 2. TÍTULO DE TODOS LOS PRODUCTOS
        // Esto aparecerá justo antes de la lista normal de productos y los filtros
        echo '<h2 class="wp-block-heading" style="text-align:center; text-transform:uppercase; letter-spacing:3px; margin-top:60px; margin-bottom:20px; font-size:24px; border-top:1px solid #e6e6e6; padding-top:50px;">TODOS LOS PRODUCTOS</h2>';
    }
}

// ==========================================
// 2. CLASE PRINCIPAL (CARGA DE MÓDULOS)
// ==========================================

final class Main {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    private function load_dependencies() {
        $base_dir = plugin_dir_path( __FILE__ );

        // Cargar módulos (si existen)
        if ( file_exists( $base_dir . 'src/Product/Customizer.php' ) )
            require_once $base_dir . 'src/Product/Customizer.php';

        if ( file_exists( $base_dir . 'src/Product/AvailabilityManager.php' ) )
            require_once $base_dir . 'src/Product/AvailabilityManager.php';

        if ( file_exists( $base_dir . 'src/Checkout/CheckoutManager.php' ) )
            require_once $base_dir . 'src/Checkout/CheckoutManager.php';
    }

    private function init_hooks() {
        // Iniciar módulos activos
        if ( class_exists( '\Artesania\Core\Product\Customizer' ) )
            new \Artesania\Core\Product\Customizer();

        if ( class_exists( '\Artesania\Core\Product\AvailabilityManager' ) )
            new \Artesania\Core\Product\AvailabilityManager();

        // MÓDULO DE FACTURACIÓN (Desactivado temporalmente con //)
        // if ( class_exists( '\Artesania\Core\Checkout\CheckoutManager' ) )
        //    new \Artesania\Core\Checkout\CheckoutManager();
    }
}

Main::get_instance();