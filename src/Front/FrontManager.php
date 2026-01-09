<?php
declare(strict_types=1);

namespace Artesania\Core\Front;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class FrontManager
 * Gestiona la lógica de presentación del frontend:
 * - Footer personalizado.
 * - Estructura de cabeceras en Tienda y Categorías.
 * - Shortcodes de secciones (Ofertas y Novedades).
 * @package Artesania\Core\Front
 */
class FrontManager {

    /* CONSTANTES DE ESTILO ELIMINADAS EN REFACTORIZACIÓN - AHORA EN CSS */

    public function __construct() {
        // 1. Footer
        add_action( 'init', [ $this, 'customize_footer' ] );

        // 2. Cabeceras Inteligentes (Tienda/Categorías)
        add_action( 'woocommerce_before_shop_loop', [ $this, 'render_shop_headers' ], 5 );

        // 3. Shortcodes
        add_shortcode( 'seccion_ofertas', [ $this, 'render_offers_section' ] );
        add_shortcode( 'seccion_novedades', [ $this, 'render_news_section' ] );

        // 4. AJUSTE DE TEXTO DE BOTONES
        add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'simplify_button_text' ] );
    }

    /**
     * Filtro para cambiar "Seleccionar opciones" por "Ver opciones".
     * Esto iguala el tamaño visual de los botones variable vs simple.
     */
    public function simplify_button_text( $text ) {
        // Si el texto es exactamente el estándar largo...
        if ( 'Seleccionar opciones' === $text ) {
            // ...lo cambiamos por uno más corto y directo.
            return __( 'Ver opciones', 'artesania-core' );
        }
        return $text;
    }

    /**
     * Reemplaza el footer por defecto de Storefront.
     */
    public function customize_footer(): void {
        remove_action( 'storefront_footer', 'storefront_credit', 20 );
        add_action( 'storefront_footer', [ $this, 'render_custom_copyright' ], 20 );
    }

    /**
     * Renderiza el HTML del nuevo copyright.
     */
    public function render_custom_copyright(): void {
        $year = date( 'Y' );
        ?>
        <div class="site-info artesania-site-info">
            &copy; <?php echo esc_html( $year ); ?> <strong>PiliYMili</strong>
            <span class="artesania-separator">|</span>
            <a href="/aviso-legal-y-condiciones-de-uso" class="artesania-legal-link">Aviso Legal</a>
            &nbsp;•&nbsp;
            <a href="/politica-privacidad" class="artesania-legal-link">Privacidad</a>
            &nbsp;•&nbsp;
            <a href="/terminos-y-condiciones" class="artesania-legal-link">Términos</a>
        </div>
        <?php
    }

    /**
     * Lógica condicional para mostrar Colecciones vs Productos
     * dependiendo de si estamos en la tienda principal o en una categoría.
     */
    public function render_shop_headers(): void {

        // CASO 1: Página Principal de Tienda
        if ( is_shop() ) {
            echo '<h2 class="wp-block-heading artesania-title-top">Colecciones</h2>';
            echo do_shortcode('[product_categories limit="6" columns="6" parent="0"]');
            echo '<h2 class="wp-block-heading artesania-title-bottom">Todos los productos</h2>';
            return;
        }

        // CASO 2: Dentro de una Categoría
        if ( is_product_category() ) {
            $obj = get_queried_object();

            // Verificamos si esta categoría tiene subcategorías (hijas)
            $children = get_terms( 'product_cat', [
                'parent'     => $obj->term_id,
                'hide_empty' => false
            ]);

            // Solo mostramos el bloque "Explora" si hay subcategorías
            if ( ! empty( $children ) ) {
                echo '<h2 class="wp-block-heading artesania-title-top">Explora ' . esc_html( $obj->name ) . '</h2>';
                echo do_shortcode('[product_categories limit="6" columns="6" parent="' . esc_attr( (string)$obj->term_id ) . '"]');
                echo '<h2 class="wp-block-heading artesania-title-bottom">Productos</h2>';
            }
            // Si es categoría final, no mostramos nada extra (para evitar redundancia con el título H1)
        }
    }

    /**
     * Shortcode: [seccion_ofertas]
     */
    public function render_offers_section(): string {
        if ( ! function_exists( 'wc_get_product_ids_on_sale' ) ) return '';

        $sale_ids = wc_get_product_ids_on_sale();
        if ( empty( $sale_ids ) ) return '';

        // Usamos clases CSS para margin-bottom:20px
        $html  = '<h2 class="wp-block-heading artesania-title-top artesania-mb-20">Productos rebajados</h2>';
        $html .= do_shortcode('[sale_products limit="5" columns="5"]');

        return '<div class="artesania-section-wrapper">' . $html . '</div>';
    }

    /**
     * Shortcode: [seccion_novedades]
     */
    public function render_news_section(): string {
        // Usamos clases auxiliares: artesania-mt-60 (para reemplazar margin-top del title-top)
        $html  = '<h2 class="wp-block-heading artesania-title-top artesania-mt-60 artesania-mb-20">Novedades</h2>';
        $html .= do_shortcode('[products limit="5" columns="5" orderby="date" order="DESC"]');

        // wrapper con mb-50
        return '<div class="artesania-section-wrapper artesania-mb-50">' . $html . '</div>';
    }
}