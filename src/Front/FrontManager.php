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
 * Gestiona footer, cabeceras, shortcodes y el badge flotante de WhatsApp.
 *
 * @package Artesania\Core\Front
 * @version 2.6.1
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
        add_action( 'wp_footer', [ $this, 'render_whatsapp_badge' ] );
        add_shortcode( 'redes_sociales', [ $this, 'render_social_shortcode' ] );
        add_action( 'wp_head', [ $this, 'permanently_hide_sticky_bar_css' ], 9999 );
        add_action( 'template_redirect', [ $this, 'remove_sticky_add_to_cart_action' ] );
    }

    private function load_view( string $view_name, array $args = [] ): void {
        if ( ! empty( $args ) ) extract( $args );
        $file_path = ARTESANIA_CORE_PATH . 'templates/' . $view_name . '.php';
        if ( file_exists( $file_path ) ) include $file_path;
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
        $this->load_view( 'footer-copyright', [
            'footer_content' => $custom_texts['footer_text'] ?? $default_footer,
            'social_html'    => $this->get_social_icons_html()
        ] );
    }

    /**
     * Shortcode [redes_sociales titulo="Tu frase aquí"]
     * Permite incluir un título opcional antes de los iconos.
     */
    public function render_social_shortcode( $atts ): string {
        // Configuramos los atributos por defecto
        $args = shortcode_atts( [
            'titulo' => '',
        ], $atts );

        $html = '<div class="artesania-social-shortcode">';

        if ( ! empty( $args['titulo'] ) ) {
            $html .= '<h3 class="artesania-social-title">' . esc_html( $args['titulo'] ) . '</h3>';
        }

        $html .= $this->get_social_icons_html();
        $html .= '</div>';

        return $html;
    }

    /**
     * Genera el HTML de los iconos de redes sociales.
     */
    private function get_social_icons_html(): string {
        $texts = get_option( 'artesania_custom_texts', [] );
        $insta = $texts['instagram_url'] ?? '';
        $fb    = $texts['facebook_url'] ?? '';

        if ( empty( $insta ) && empty( $fb ) ) return '';

        $html = '<div class="artesania-social-icons">';

        if ( ! empty( $insta ) ) {
            // Icono Instagram SVG
            $html .= '<a href="' . esc_url( $insta ) . '" target="_blank" class="artesania-social-link instagram" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>';
        }

        if ( ! empty( $fb ) ) {
            // Icono Facebook SVG
            $html .= '<a href="' . esc_url( $fb ) . '" target="_blank" class="artesania-social-link facebook" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg></a>';
        }

        $html .= '</div>';
        return $html;
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

    /**
     * Inyecta CSS de máxima prioridad para asegurar que la barra sticky no se vea nunca.
     * * @return void
     */
    public function permanently_hide_sticky_bar_css(): void {
        echo '<style id="artesania-core-global-ui-fix">
            .storefront-sticky-add-to-cart, 
            #storefront-sticky-add-to-cart,
            .storefront-sticky-add-to-cart--visible { 
                display: none !important; 
                visibility: hidden !important; 
                transform: none !important;
                opacity: 0 !important;
                pointer-events: none !important;
            }
        </style>';
    }

    /**
     * Remueve la acción de Storefront desde el servidor para optimizar la carga.
     * * @return void
     */
    public function remove_sticky_add_to_cart_action(): void {
        remove_action( 'storefront_after_footer', 'storefront_sticky_add_to_cart', 999 );
    }

    /**
     * Renderiza el botón flotante de WhatsApp.
     * Genera un mensaje automático si se visita un producto.
     */
    public function render_whatsapp_badge(): void {
        $texts = get_option( 'artesania_custom_texts', [] );
        $phone = $texts['whatsapp_number'] ?? '';

        if ( empty( $phone ) ) return;

        $message = __( 'Hola, tengo una duda.', 'artesania-core' );

        if ( is_product() ) {
            global $product;
            if ( $product ) {
                $message = sprintf( __( 'Hola, tengo una duda sobre el producto "%s".', 'artesania-core' ), $product->get_name() );
            }
        }

        $url = 'https://wa.me/' . esc_attr( $phone ) . '?text=' . urlencode( $message );

        $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="32px" height="32px"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>';

        echo '<a href="' . esc_url( $url ) . '" class="artesania-whatsapp-badge" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr__( 'Contactar por WhatsApp', 'artesania-core' ) . '">';
        echo $icon;
        echo '</a>';
    }
}