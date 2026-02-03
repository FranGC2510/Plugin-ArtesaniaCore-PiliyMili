<?php
declare(strict_types=1);

namespace Artesania\Core\Front;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class AssetsManager
 *
 * Responsable de la gestión y encolado de recursos estáticos (CSS, JS, Fuentes).
 * Prioriza el rendimiento (WPO) evitando la carga de estilos inline y utilizando
 * el sistema de dependencias de WordPress.
 *
 * @package Artesania\Core\Front
 */
class AssetsManager {

    /**
     * Inicializa los hooks para encolar scripts y estilos.
     * Usa 'wp_enqueue_scripts' para el frontend.
     */
    public function __construct() {
        // 1. Cargar fuentes de Google
        add_action( 'wp_enqueue_scripts', [ $this, 'load_google_fonts' ] );

        // 2. Encolar Hoja de Estilos Principal (Eliminado CSS Inline antiguo)
        // Antes llamaba a 'render_global_css', ahora llamamos a 'enqueue_styles'
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    }

    /**
     * Carga las tipografías corporativas desde Google Fonts.
     * Optimizado para carga asíncrona y compatibilidad con caché.
     *
     * Fuentes: Cinzel (Títulos), Montserrat (Cuerpo).
     *
     * @return void
     */
    public function load_google_fonts() {
        wp_enqueue_style(
            'artesania-google-fonts',
            'https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Montserrat:wght@300;400;600;700&display=swap',
            [],
            '2.0.0'
        );
    }

    /**
     * Encola la hoja de estilos principal del plugin.
     * Calcula la URL absoluta del archivo CSS basándose en la estructura del directorio.
     * Incluye versionado para control de caché del navegador.
     *
     * @return void
     */
    public function enqueue_styles() {
        // Ruta relativa al archivo CSS físico que creaste
        $css_path = 'assets/css/front.css';

        // URL raíz del plugin
        $plugin_url = plugin_dir_url( dirname( dirname( __DIR__ ) ) . '/artesania-core.php' );

        wp_enqueue_style(
            'artesania-core-style',
            $plugin_url . $css_path,
            [],
            '2.3.1'
        );
    }
}