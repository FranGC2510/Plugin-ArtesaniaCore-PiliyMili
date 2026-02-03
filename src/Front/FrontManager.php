<?php
declare(strict_types=1);

namespace Artesania\Core\Front;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class FrontManager
 *
 * Controlador de Presentación (Frontend Controller).
 * Implementa una arquitectura MVC simplificada.
 * Su responsabilidad es preparar los datos y delegar el renderizado a las Vistas (Templates).
 * Gestiona Shortcodes, Hooks de WooCommerce y modificaciones del DOM.
 *
 * @package Artesania\Core\Front
 */
class FrontManager {

    /**
     * Registra los hooks de acción y filtros para modificar el frontend.
     * Define shortcodes para secciones personalizadas.
     */
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
     * Helper privado para cargar vistas (Templates).
     * Localiza e incluye archivos PHP desde la carpeta 'views/'.
     *
     * @param string $view_name Nombre del archivo de vista (sin extensión .php).
     * @param array  $args      Array asociativo de datos a pasar a la vista.
     * @return void
     */
    private function load_view( string $view_name, array $args = [] ) {
        if ( ! empty( $args ) ) {
            extract( $args );
        }

        // Ruta absoluta al archivo de vista
        $file_path = dirname( dirname( __DIR__ ) ) . '/templates/' . $view_name . '.php';

        if ( file_exists( $file_path ) ) {
            include $file_path;
        } else {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( "ArtesaniaCore: No se encuentra la vista $view_name en $file_path" );
            }
        }
    }

    /**
     * Filtra el texto del botón de "Añadir al carrito" en productos variables.
     * Cambia "Seleccionar opciones" por "Ver opciones" para consistencia visual.
     *
     * @param string $text Texto original.
     * @return string Texto modificado y traducible.
     */
    public function simplify_button_text( $text ) {
        if ( 'Seleccionar opciones' === $text ) return __( 'Ver opciones', 'artesania-core' );
        return $text;
    }

    /**
     * Gestiona la visibilidad de la etiqueta de oferta en la ficha de producto.
     * Elimina la acción por defecto para permitir una reubicación personalizada.
     *
     * @return void
     */
    public function manage_single_sale_badge() {
        if ( is_product() ) {
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
        }
    }

    /**
     * Inyecta la etiqueta de oferta junto al precio del producto.
     *
     * @param string     $price   HTML del precio original.
     * @param \WC_Product $product Objeto del producto actual.
     * @return string HTML modificado con la etiqueta de oferta.
     */
    public function inject_offer_badge_in_price( $price, $product ) {
        if ( is_product() && $product->is_on_sale() ) {
            $badge = '<span class="artesania-sale-tag">' . esc_html__( '¡OFERTA!', 'artesania-core' ) . '</span>';
            return $price . $badge;
        }
        return $price;
    }

    /**
     * Reemplaza el footer por defecto de Storefront por uno personalizado.
     *
     * @return void
     */
    public function customize_footer(): void {
        remove_action( 'storefront_footer', 'storefront_credit', 20 );
        add_action( 'storefront_footer', [ $this, 'render_custom_copyright' ], 20 );
    }

    /**
     * Renderiza el copyright y enlaces legales cargando la vista correspondiente.
     *
     * @return void
     */
    public function render_custom_copyright(): void {
        $this->load_view( 'footer-copyright', [ 'year' => date( 'Y' ) ] );
    }

    /**
     * Renderiza cabeceras condicionales en la tienda y categorías.
     * Determina si mostrar "Colecciones" o subcategorías "Explora" basándose en el contexto actual.
     *
     * @return void
     */
    public function render_shop_headers(): void {
        if ( is_shop() ) {
            $this->load_view( 'shop-header' );
            return;
        }

        if ( is_product_category() ) {
            $obj = get_queried_object();
            // Comprobamos si tiene hijos para decidir si mostrar "Explora"
            $children = get_terms( 'product_cat', [ 'parent' => $obj->term_id, 'hide_empty' => false ]);

            if ( ! empty( $children ) ) {
                $this->load_view( 'category-header', [
                    'category_name' => $obj->name,
                    'term_id'       => $obj->term_id
                ]);
            }
        }
    }

    /**
     * Shortcode [seccion_ofertas].
     * Renderiza un grid de productos en oferta.
     *
     * @return string HTML de la sección (output buffered).
     */
    public function render_offers_section(): string {
        if ( ! function_exists( 'wc_get_product_ids_on_sale' ) ) return '';

        $sale_ids = wc_get_product_ids_on_sale();
        if ( empty( $sale_ids ) ) return '';

        ob_start();
        $this->load_view( 'section-offers' );
        return ob_get_clean();
    }

    /**
     * Shortcode [seccion_novedades].
     * Renderiza un grid de los productos más recientes.
     *
     * @return string HTML de la sección (output buffered).
     */
    public function render_news_section(): string {
        ob_start();
        $this->load_view( 'section-news' );
        return ob_get_clean();
    }
}