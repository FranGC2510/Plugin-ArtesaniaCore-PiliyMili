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
        // 2. Encolar Hoja de Estilos Principal (Eliminado CSS Inline)
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
     * Encola el archivo CSS principal del plugin.
     * Utiliza filemtime para versionado automático (cache busting) en desarrollo.
     */
    public function enqueue_styles() {
        // Ruta al archivo CSS físico
        // Asume: src/Front/assets/css/artesania-style.css
        // Ajustamos la ruta relativa desde este archivo (__FILE__)
        // __FILE__ está en src/Front/
        // URL base del plugin: plugin_dir_url( dirname( dirname( __FILE__ ) ) ) si estuviéramos en root
        // Usamos una constante o cálculo relativo.

        // Calculamos la URL correcta del plugin
        // Nota: Asumo que el plugin está en 'wp-content/plugins/artesania-core/'
        // La estructura es src/Front/assets/css/artesania-style.css

        $css_path = 'src/Front/assets/css/artesania-style.css';

        // Truco para obtener la URL base del plugin desde una clase interna
        // Subimos 2 niveles desde src/Front -> src -> root
        $plugin_url = plugin_dir_url( dirname( dirname( __DIR__ ) ) . '/artesania-core.php' );

        wp_enqueue_style(
            'artesania-core-style',
            $plugin_url . $css_path,
            [],
            '2.0.0'
        );
    }
}