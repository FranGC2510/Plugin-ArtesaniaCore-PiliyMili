<?php
declare(strict_types=1);

namespace Artesania\Core\Front;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class FrontManager
 * Gestiona la lógica de presentación (Controlador).
 * Separa la lógica PHP de las Vistas HTML.
 * @package Artesania\Core\Front
 */
class FrontManager {

    public function __construct() {
        // 1. Footer
        add_action( 'init', [ $this, 'customize_footer' ] );

        // 2. Cabeceras Inteligentes
        add_action( 'woocommerce_before_shop_loop', [ $this, 'render_shop_headers' ], 5 );

        // 3. Shortcodes
        add_shortcode( 'seccion_ofertas', [ $this, 'render_offers_section' ] );
        add_shortcode( 'seccion_novedades', [ $this, 'render_news_section' ] );

        // 4. Texto Botones
        add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'simplify_button_text' ] );

        // 5. Etiqueta Oferta (Ficha Producto)
        add_action( 'wp', [ $this, 'manage_single_sale_badge' ] );
        add_filter( 'woocommerce_get_price_html', [ $this, 'inject_offer_badge_in_price' ], 10, 2 );
    }

    /**
     * Helper para cargar vistas de forma segura.
     */
    private function load_view( string $view_name, array $args = [] ) {
        // Extraemos las variables para que estén disponibles en la vista (ej: $year, $term_id)
        if ( ! empty( $args ) ) {
            extract( $args );
        }

        $file_path = plugin_dir_path( __FILE__ ) . 'views/' . $view_name . '.php';

        if ( file_exists( $file_path ) ) {
            include $file_path;
        }
    }

    /* --- MÉTODOS DE LÓGICA --- */

    public function simplify_button_text( $text ) {
        if ( 'Seleccionar opciones' === $text ) return __( 'Ver opciones', 'artesania-core' );
        return $text;
    }

    public function manage_single_sale_badge() {
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

    /* --- MÉTODOS DE RENDERIZADO (Conectan con Vistas) --- */

    public function render_custom_copyright(): void {
        // Preparamos datos
        $year = date( 'Y' );
        // Cargamos vista
        $this->load_view( 'footer-copyright', [ 'year' => $year ] );
    }

    public function render_shop_headers(): void {
        // CASO 1: Tienda Principal
        if ( is_shop() ) {
            $this->load_view( 'shop-header' );
            return;
        }

        // CASO 2: Categoría
        if ( is_product_category() ) {
            $obj = get_queried_object();
            // Lógica de negocio: ¿Tiene hijos?
            $children = get_terms( 'product_cat', [ 'parent' => $obj->term_id, 'hide_empty' => false ]);

            if ( ! empty( $children ) ) {
                // Pasamos datos limpios a la vista
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

        // Shortcode requiere return string, capturamos el include
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