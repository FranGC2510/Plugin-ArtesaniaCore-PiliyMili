<?php
/**
 * Plugin Name: Artesanía Core (Lógica de Negocio)
 * Description: Plugin estructural modular. Gestiona Personalización, Stock, Checkout y Diseño.
 * Version: 1.6.0
 * Author: Fco Javier García Cañero
 */

namespace Artesania\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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

        // 1. Módulo de Productos (Personalización y Stock)
        if ( file_exists( $base_dir . 'src/Product/Customizer.php' ) )
            require_once $base_dir . 'src/Product/Customizer.php';

        if ( file_exists( $base_dir . 'src/Product/AvailabilityManager.php' ) )
            require_once $base_dir . 'src/Product/AvailabilityManager.php';

        // 2. Módulo de Checkout (Facturas y NIF)
        if ( file_exists( $base_dir . 'src/Checkout/CheckoutManager.php' ) )
            require_once $base_dir . 'src/Checkout/CheckoutManager.php';

        // 3. Módulo Visual (Footer, Tienda y Ofertas) -> ¡NUEVO!
        if ( file_exists( $base_dir . 'src/Front/FrontManager.php' ) )
            require_once $base_dir . 'src/Front/FrontManager.php';
    }

    private function init_hooks() {
        // Instanciamos las clases. Al hacerlo, se ejecutan sus __construct y se activan los hooks.

        if ( class_exists( '\Artesania\Core\Product\Customizer' ) )
            new \Artesania\Core\Product\Customizer();

        if ( class_exists( '\Artesania\Core\Product\AvailabilityManager' ) )
            new \Artesania\Core\Product\AvailabilityManager();

        // ¡IMPORTANTE! He descomentado esto porque en tu código anterior estaba desactivado con //
        if ( class_exists( '\Artesania\Core\Checkout\CheckoutManager' ) )
            new \Artesania\Core\Checkout\CheckoutManager();

        // Instanciamos el nuevo gestor visual
        if ( class_exists( '\Artesania\Core\Front\FrontManager' ) )
            new \Artesania\Core\Front\FrontManager();
    }
}

// Arrancamos el motor
Main::get_instance();