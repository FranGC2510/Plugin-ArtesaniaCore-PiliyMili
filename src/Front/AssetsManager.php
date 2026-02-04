<?php
declare(strict_types=1);

namespace Artesania\Core\Front;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class AssetsManager
 *
 * Responsable de la gestión y encolado de recursos estáticos del Frontend.
 * Optimizado con uso de constantes globales.
 *
 * @package Artesania\Core\Front
 * @version 2.4.0
 */
class AssetsManager {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'load_google_fonts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    }

    public function load_google_fonts(): void {
        wp_enqueue_style(
            'artesania-google-fonts',
            'https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Montserrat:wght@300;400;600;700&display=swap',
            [],
            '2.0.0'
        );
    }

    /**
     * Encola la hoja de estilos principal (front.css).
     */
    public function enqueue_styles(): void {
        $css_path = 'assets/css/front.css';
        $css_url = ARTESANIA_CORE_URL . $css_path;

        wp_enqueue_style(
            'artesania-core-style',
            $css_url,
            [],
            ARTESANIA_CORE_VERSION
        );
    }
}