<?php
/**
 * Plugin Name: Artesanía Core (Lógica de Negocio)
 * Description: Sistema modular de gestión para Pili & Mili. Incluye Diseño, Stock, Checkout y Personalización bajo arquitectura MVC.
 * Version: 2.4.0
 * Author: Fco Javier García Cañero
 * Package: Artesania\Core
 *
 * --- CONFIGURACIÓN GITHUB ---
 * GitHub Plugin URI: FranGC2510/Plugin-ArtesaniaCore-PiliyMili
 * Primary Branch: main
 */

declare(strict_types=1);

namespace Artesania\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Main
 *
 * Bootstrapper principal del plugin.
 * Implementa patrón Singleton para la inicialización condicional de módulos.
 *
 * @package Artesania\Core
 * @version 2.4.0
 */
final class Main {

    /** @var Main|null Instancia única de la clase. */
    private static $instance = null;

    /**
     * Obtiene la instancia única (Singleton).
     * @return Main
     */
    public static function get_instance(): Main {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_modules();
    }

    /**
     * Carga las clases PHP requeridas.
     */
    private function load_dependencies(): void {
        $base_dir = plugin_dir_path( __FILE__ );
        $modules  = [
            'src/Product/Customizer.php',
            'src/Product/AvailabilityManager.php',
            'src/Checkout/CheckoutManager.php',
            'src/Front/AssetsManager.php',
            'src/Front/FrontManager.php',
            'src/Admin/AdminAssetsManager.php',
            'src/Sales/SalesCalculator.php',
            'src/Sales/SalesLimiter.php',
            'src/Admin/SettingsPage.php',
            'src/Admin/DashboardWidget.php',
            'src/Admin/AdminManager.php',
        ];

        foreach ( $modules as $file ) {
            if ( file_exists( $base_dir . $file ) ) {
                require_once $base_dir . $file;
            }
        }
    }

    /**
     * Inicializa los módulos basándose en la configuración de la base de datos.
     */
    private function init_modules(): void {
        $active_modules = get_option( 'artesania_active_modules', [] );

        // 1. Módulos Condicionales (Configurables)
        if ( ! empty( $active_modules['customizer'] ) && class_exists( '\Artesania\Core\Product\Customizer' ) ) {
            new \Artesania\Core\Product\Customizer();
        }

        if ( ! empty( $active_modules['slow_design'] ) && class_exists( '\Artesania\Core\Product\AvailabilityManager' ) ) {
            new \Artesania\Core\Product\AvailabilityManager();
        }

        if ( ! empty( $active_modules['checkout'] ) && class_exists( '\Artesania\Core\Checkout\CheckoutManager' ) ) {
            new \Artesania\Core\Checkout\CheckoutManager();
        }

        if ( ! empty( $active_modules['frontend'] ) ) {
            if ( class_exists( '\Artesania\Core\Front\AssetsManager' ) ) {
                new \Artesania\Core\Front\AssetsManager();
            }
            if ( class_exists( '\Artesania\Core\Front\FrontManager' ) ) {
                new \Artesania\Core\Front\FrontManager();
            }
        }

        // 2. Módulos Core (Siempre activos)
        if ( class_exists( '\Artesania\Core\Sales\SalesLimiter' ) ) {
            new \Artesania\Core\Sales\SalesLimiter();
        }

        if ( is_admin() && class_exists( '\Artesania\Core\Admin\AdminManager' ) ) {
            new \Artesania\Core\Admin\AdminManager();
        }
    }
}

Main::get_instance();