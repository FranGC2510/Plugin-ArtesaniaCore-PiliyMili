<?php
/**
 * Plugin Name: Artesanía Core (Lógica de Negocio)
 * Description: Sistema modular de gestión para Pili & Mili. Incluye Diseño, Stock, Checkout y Personalización.
 * Version: 2.0.0
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
        $this->init_modules();
    }

    private function load_dependencies() {
        $base_dir = plugin_dir_path( __FILE__ );

        // Lista de módulos a cargar
        $modules = [
            'src/Product/Customizer.php',
            'src/Product/AvailabilityManager.php',
            'src/Checkout/CheckoutManager.php',
            'src/Front/AssetsManager.php', // ¡NUEVO!
            'src/Front/FrontManager.php',
        ];

        foreach ( $modules as $file ) {
            if ( file_exists( $base_dir . $file ) ) {
                require_once $base_dir . $file;
            }
        }
    }

    private function init_modules() {
        // Inicializamos las clases si existen
        if ( class_exists( '\Artesania\Core\Product\Customizer' ) ) {
            new \Artesania\Core\Product\Customizer();
        }
        if ( class_exists( '\Artesania\Core\Product\AvailabilityManager' ) ) {
            new \Artesania\Core\Product\AvailabilityManager();
        }
        if ( class_exists( '\Artesania\Core\Checkout\CheckoutManager' ) ) {
            new \Artesania\Core\Checkout\CheckoutManager();
        }

        // Módulos Visuales
        if ( class_exists( '\Artesania\Core\Front\AssetsManager' ) ) {
            new \Artesania\Core\Front\AssetsManager();
        }
        if ( class_exists( '\Artesania\Core\Front\FrontManager' ) ) {
            new \Artesania\Core\Front\FrontManager();
        }
    }
}

// Arrancar el plugin
Main::get_instance();