<?php
/**
 * Plugin Name: Artesanía Core (Lógica de Negocio)
 * Description: Sistema modular de gestión para Pili & Mili. Incluye Diseño, Stock, Checkout, Personalización, WhatsApp y Modo Catálogo bajo arquitectura MVC.
 * Version: 2.6.1
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

// === CONSTANTES GLOBALES DE RUTAS ===
if ( ! defined( 'ARTESANIA_CORE_PATH' ) ) {
    define( 'ARTESANIA_CORE_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ARTESANIA_CORE_URL' ) ) {
    define( 'ARTESANIA_CORE_URL', plugin_dir_url( __FILE__ ) );
}

// Versión Global para Cache Busting
if ( ! defined( 'ARTESANIA_CORE_VERSION' ) ) {
    define( 'ARTESANIA_CORE_VERSION', '2.6.1' );
}

/**
 * Class Main
 *
 * Bootstrapper principal del plugin.
 * Implementa patrón Singleton.
 * Gestiona dependencias, i18n, HPOS y la inicialización de módulos.
 *
 * @package Artesania\Core
 * @version 2.6.1
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
        $this->init_hooks();
        $this->init_modules();
    }

    /**
     * Inicializa los hooks principales del core (i18n, HPOS).
     */
    private function init_hooks(): void {
        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
        add_action( 'before_woocommerce_init', [ $this, 'declare_hpos_compatibility' ] );
    }

    /**
     * Carga el Text Domain para traducciones.
     */
    public function load_textdomain(): void {
        load_plugin_textdomain(
            'artesania-core',
            false,
            dirname( plugin_basename( __FILE__ ) ) . '/languages'
        );
    }

    /**
     * Declara compatibilidad con High Performance Order Storage (HPOS).
     */
    public function declare_hpos_compatibility(): void {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }

    /**
     * Carga las clases PHP requeridas.
     */
    private function load_dependencies(): void {
        $base_dir = ARTESANIA_CORE_PATH;

        $modules  = [
            'src/Product/Customizer.php',
            'src/Product/AvailabilityManager.php',
            'src/Product/CatalogManager.php',
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

        // Verificamos si el Modo Catálogo está activo
        $is_catalog_mode = ! empty( $active_modules['catalog_mode'] );

        // 1. Módulos Condicionales

        // MODO CATÁLOGO: Si está activo, cargamos el Manager y BLOQUEAMOS Customizer y Checkout
        if ( $is_catalog_mode ) {
            if ( class_exists( '\Artesania\Core\Product\CatalogManager' ) ) {
                new \Artesania\Core\Product\CatalogManager();
            }
        }
        // Si NO está en modo catálogo, cargamos la tienda normal (Customizer y Checkout)
        else {
            if ( ! empty( $active_modules['customizer'] ) && class_exists( '\Artesania\Core\Product\Customizer' ) ) {
                new \Artesania\Core\Product\Customizer();
            }
            if ( ! empty( $active_modules['checkout'] ) && class_exists( '\Artesania\Core\Checkout\CheckoutManager' ) ) {
                new \Artesania\Core\Checkout\CheckoutManager();
            }
        }

        // Slow Design (Stock) - Esto lo dejamos activo en ambos modos por si acaso
        if ( ! empty( $active_modules['slow_design'] ) && class_exists( '\Artesania\Core\Product\AvailabilityManager' ) ) {
            new \Artesania\Core\Product\AvailabilityManager();
        }

        // Frontend (Diseño)
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