<?php
declare(strict_types=1);

namespace Artesania\Core\Front;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class FrontManager
 *
 * Controlador de Presentación.
 * Busca las vistas en la carpeta /templates/ usando rutas absolutas constantes.
 *
 * @package Artesania\Core\Front
 * @version 2.4.0
 */
class FrontManager {

    public function __construct() {
        add_action( 'init', [ $this, 'customize_footer' ] );
        add_action( 'woocommerce_before_shop_loop', [ $this, 'render_shop_headers' ], 5 );
        add_shortcode( 'seccion_ofertas', [ $this, 'render_offers_section' ] );
        add_shortcode( 'seccion_novedades', [ $this, 'render_news_section' ] );
        add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'simplify_button_text' ] );
        add_action( 'wp', [ $this, 'manage_single_sale_badge' ] );
        add_filter( 'woocommerce_get_price_html', [ $this, 'inject_offer_badge_in_price' ], 10, 2 );
    }

    /**
     * Carga una vista desde la carpeta templates/ en la raíz del plugin.
     *
     * @param string $view_name Nombre del archivo (sin .php).
     * @param array  $args      Datos a pasar a la vista.
     */
    private function load_view( string $view_name, array $args = [] ): void {
        if ( ! empty( $args ) ) {
            extract( $args );
        }

        $file_path = ARTESANIA_CORE_PATH . 'templates/' . $view_name . '.php';

        if ( file_exists( $file_path ) ) {
            include $file_path;
        } else {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( "ArtesaniaCore: No se encuentra la vista $view_name en $file_path" );
            }
        }
    }

    public function simplify_button_text( $text ) {
        return ( 'Seleccionar opciones' === $text ) ? __( 'Ver opciones', 'artesania-core' ) : $text;
    }

    public function manage_single_sale_badge(): void {
        if ( is_product() ) {
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
        }
    }

    public function inject_offer_badge_in_price( $price, $product ) {
        if ( is_product() && $product->is_on_sale() ) {
            $badge = '<span class="artesania-sale-tag">' . esc_html__( '¡OFERTA!', 'artesania-core' ) . '</span>';
            return $price . $badge;
        }
        return $price;
    }

    public function customize_footer(): void {
        remove_action( 'storefront_footer', 'storefront_credit', 20 );
        add_action( 'storefront_footer', [ $this, 'render_custom_copyright' ], 20 );
    }

    public function render_custom_copyright(): void {
        $custom_texts   = get_option( 'artesania_custom_texts', [] );
        $default_footer = '&copy; ' . date( 'Y' ) . ' <strong>PiliYMili</strong>';

        $footer_content = $custom_texts['footer_text'] ?? $default_footer;

        $this->load_view( 'footer-copyright', [ 'footer_content' => $footer_content ] );
    }

    public function render_shop_headers(): void {
        if ( is_shop() ) {
            $this->load_view( 'shop-header' );
            return;
        }

        if ( is_product_category() ) {
            $obj = get_queried_object();
            $children = get_terms( 'product_cat', [ 'parent' => $obj->term_id, 'hide_empty' => false ]);

            if ( ! empty( $children ) ) {
                $this->load_view( 'category-header', [
                    'category_name' => $obj->name,
                    'term_id'       => $obj->term_id
                ]);
            }
        }
    }

    public function render_offers_section(): string {
        if ( ! function_exists( 'wc_get_product_ids_on_sale' ) ) return '';

        $sale_ids = wc_get_product_ids_on_sale();
        if ( empty( $sale_ids ) ) return '';

        ob_start();
        $this->load_view( 'section-offers' );
        return ob_get_clean();
    }

    public function render_news_section(): string {
        ob_start();
        $this->load_view( 'section-news' );
        return ob_get_clean();
    }
}