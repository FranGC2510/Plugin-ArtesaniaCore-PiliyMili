<?php
namespace Artesania\Core\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class AdminManager
 *
 * Orquestador del área de administración.
 * Responsable de inicializar las páginas de configuración, widgets y ASSETS.
 *
 * @package Artesania\Core\Admin
 * @author  Fco Javier García Cañero
 * @version 2.3.0
 */
class AdminManager {

    /**
     * Constructor. Inicializa los componentes de administración.
     */
    public function __construct() {
        // 1. Cargar Estilos y Scripts (NUEVO)
        if ( class_exists( '\Artesania\Core\Admin\AdminAssetsManager' ) ) {
            new AdminAssetsManager();
        }

        // 2. Cargar Página de Ajustes
        if ( class_exists( '\Artesania\Core\Admin\SettingsPage' ) ) {
            new SettingsPage();
        }

        // 3. Cargar Widget
        if ( class_exists( '\Artesania\Core\Admin\DashboardWidget' ) ) {
            new DashboardWidget();
        }
    }
}