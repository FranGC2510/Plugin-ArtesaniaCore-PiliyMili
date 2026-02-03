<?php
namespace Artesania\Core\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class AdminAssetsManager
 *
 * Gestiona la carga de estilos (CSS) y scripts (JS) para el área de administración.
 * Asegura que los estilos propios del plugin se carguen correctamente.
 *
 * @package Artesania\Core\Admin
 * @author  Fco Javier García Cañero
 * @version 2.3.0
 */
class AdminAssetsManager {

    /**
     * Inicializa el hook de carga de scripts administrativos.
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    }

    /**
     * Encola la hoja de estilos principal del panel de administración.
     * Utiliza control de versiones para caché basado en la versión del plugin.
     *
     * @return void
     */
    public function enqueue_styles(): void {
        $css_path   = 'assets/css/admin.css';

        // Calculamos la URL raíz del plugin
        $plugin_url = plugin_dir_url( dirname( dirname( __DIR__ ) ) . '/artesania-core.php' );

        wp_enqueue_style(
            'artesania-admin-css',
            $plugin_url . $css_path,
            [],
            '2.3.1'
        );
    }
}