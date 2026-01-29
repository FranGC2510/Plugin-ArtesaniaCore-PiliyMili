<?php
/**
 * Plugin Name: Artesanía Core (Lógica de Negocio)
 * Description: Sistema modular de gestión para Pili & Mili. Incluye Diseño, Stock, Checkout y Personalización bajo arquitectura MVC y POO.
 * Version: 2.2.4
 * Author: Fco Javier García Cañero
 * Package: Artesania\Core
 *
 * --- CONFIGURACIÓN GITHUB ---
 * GitHub Plugin URI: FranGC2510/Plugin-ArtesaniaCore-PiliyMili
 * Primary Branch: main
 */

namespace Artesania\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Main
 *
 * Clase principal del plugin (Bootstrapper).
 * Implementa el patrón de diseño Singleton para asegurar una única instancia de ejecución.
 * Se encarga de la inyección de dependencias y la inicialización de módulos.
 *
 * @package Artesania\Core
 * @version 2.2.1
 * @since   1.0.0
 */
final class Main {

    /**
     * @var Main|null Instancia única de la clase.
     */
    private static $instance = null;

    /**
     * Obtiene la instancia única de la clase (Singleton).
     *
     * @return Main La instancia principal.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor privado para prevenir instanciación externa.
     * Carga dependencias e inicializa módulos.
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_modules();
    }

    /**
     * Carga los archivos de clases requeridos.
     * Utiliza rutas relativas basadas en la constante __FILE__.
     *
     * @return void
     */
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

    /**
     * Inicializa las instancias de los módulos (Controladores).
     * Verifica la existencia de las clases antes de instanciarlas para evitar errores fatales.
     *
     * @return void
     */
    private function init_modules() {
        // 1. Módulos de Producto
        if ( class_exists( '\Artesania\Core\Product\Customizer' ) ) {
            new \Artesania\Core\Product\Customizer();
        }
        if ( class_exists( '\Artesania\Core\Product\AvailabilityManager' ) ) {
            new \Artesania\Core\Product\AvailabilityManager();
        }

        // 2. Módulo Checkout / Facturas
        /*
        if ( class_exists( '\Artesania\Core\Checkout\CheckoutManager' ) ) {
            new \Artesania\Core\Checkout\CheckoutManager();
        }*/

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