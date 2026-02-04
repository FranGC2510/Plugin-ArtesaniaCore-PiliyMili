<?php
declare(strict_types=1);

namespace Artesania\Core\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class AdminAssetsManager
 *
 * Gestiona la carga de estilos (CSS) y scripts (JS) para el área de administración.
 * Actualizado para usar constantes globales de URL.
 *
 * @package Artesania\Core\Admin
 * @version 2.4.0
 */
class AdminAssetsManager {

    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
    }

    /**
     * Encola la hoja de estilos principal del panel de administración.
     */
    public function enqueue_admin_styles(): void {
        $css_url = ARTESANIA_CORE_URL . 'assets/css/admin.css';

        wp_enqueue_style(
            'artesania-admin-css',
            $css_url,
            [],
            ARTESANIA_CORE_VERSION
        );
    }
}