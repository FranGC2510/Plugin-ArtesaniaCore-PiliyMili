<?php
declare(strict_types=1);

namespace Artesania\Core\Front;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class AssetsManager
 * Responsable de encolar assets (CSS/JS) de forma organizada.
 * WPO: Los estilos ahora se cargan desde un archivo externo estático.
 * @package Artesania\Core\Front
 */
class AssetsManager {

    public function __construct() {
        // 1. Cargar fuentes de Google
        add_action( 'wp_enqueue_scripts', [ $this, 'load_google_fonts' ] );

        // 2. Encolar Hoja de Estilos Principal (Eliminado CSS Inline antiguo)
        // Antes llamaba a 'render_global_css', ahora llamamos a 'enqueue_styles'
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    }

    /**
     * Carga las tipografías desde Google Fonts.
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
     * CONEXIÓN CRÍTICA: Carga el archivo artesania-style.css
     */
    public function enqueue_styles() {
        // Ruta relativa al archivo CSS físico que creaste
        $css_path = 'src/Front/assets/css/artesania-style.css';

        // Calculamos la URL base del plugin correctamente
        // dirname( dirname( __DIR__ ) ) nos lleva a la raíz del plugin
        $plugin_url = plugin_dir_url( dirname( dirname( __DIR__ ) ) . '/artesania-core.php' );

        wp_enqueue_style(
            'artesania-core-style', // ID único del estilo
            $plugin_url . $css_path, // URL completa al archivo
            [],
            '2.2.0' // Versión para forzar la recarga del caché
        );
    }
}