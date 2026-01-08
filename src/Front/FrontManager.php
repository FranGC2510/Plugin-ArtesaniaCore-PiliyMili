<?php
declare(strict_types=1);

namespace Artesania\Core\Front;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class FrontManager
 * * Gestiona la lógica de presentación del frontend:
 * - Footer personalizado.
 * - Estructura de cabeceras en Tienda y Categorías.
 * - Shortcodes de secciones (Ofertas y Novedades).
 * * @package Artesania\Core\Front
 */
class FrontManager {

    /**
     * Estilos CSS inline para los títulos H2 generados dinámicamente.
     */
    private const STYLE_TITLE_TOP = 'text-align:center; text-transform:none; font-weight:300; margin-top:0px; margin-bottom:30px; font-size:34px; color:#000000; line-height:1.2;';
    private const STYLE_TITLE_BOTTOM = 'text-align:center; text-transform:none; font-weight:300; margin-top:60px; margin-bottom:20px; font-size:34px; border-top:1px solid #e6e6e6; padding-top:50px; color:#000000; line-height:1.2;';

    public function __construct() {
        // 1. Footer
        add_action( 'init', [ $this, 'customize_footer' ] );

        // 2. Cabeceras Inteligentes (Tienda/Categorías)
        add_action( 'woocommerce_before_shop_loop', [ $this, 'render_shop_headers' ], 5 );

        // 3. Shortcodes
        add_shortcode( 'seccion_ofertas', [ $this, 'render_offers_section' ] );
        add_shortcode( 'seccion_novedades', [ $this, 'render_news_section' ] );
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
        <div class="site-info" style="text-align:center; padding: 2em 0; font-size: 0.9em; clear:both; border-top: 1px solid #f0f0f0; margin-top: 2em;">
            &copy; <?php echo esc_html( $year ); ?> <strong>PiliYMili</strong>
            <span style="margin: 0 10px; color: #ccc;">|</span>
            <a href="/aviso-legal-y-condiciones-de-uso" style="color:inherit; text-decoration:none;">Aviso Legal</a>
            &nbsp;•&nbsp;
            <a href="/politica-privacidad" style="color:inherit; text-decoration:none;">Privacidad</a>
            &nbsp;•&nbsp;
            <a href="/terminos-y-condiciones" style="color:inherit; text-decoration:none;">Términos</a>
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
            echo '<h2 class="wp-block-heading" style="' . self::STYLE_TITLE_TOP . '">Colecciones</h2>';
            echo do_shortcode('[product_categories limit="6" columns="3" parent="0"]');
            echo '<h2 class="wp-block-heading" style="' . self::STYLE_TITLE_BOTTOM . '">Todos los productos</h2>';
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
                echo '<h2 class="wp-block-heading" style="' . self::STYLE_TITLE_TOP . '">Explora ' . esc_html( $obj->name ) . '</h2>';
                echo do_shortcode('[product_categories limit="6" columns="3" parent="' . esc_attr( (string)$obj->term_id ) . '"]');
                echo '<h2 class="wp-block-heading" style="' . self::STYLE_TITLE_BOTTOM . '">Productos</h2>';
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

        $html  = '<h2 class="wp-block-heading" style="' . self::STYLE_TITLE_TOP . ' margin-bottom:20px;">Productos rebajados</h2>';
        $html .= do_shortcode('[sale_products limit="4" columns="4"]');

        return '<div style="max-width: 1000px; margin: 0 auto;">' . $html . '</div>';
    }

    /**
     * Shortcode: [seccion_novedades]
     */
    public function render_news_section(): string {
        // Ajustamos el margen superior para novedades
        $style = str_replace( 'margin-top:0px', 'margin-top:60px', self::STYLE_TITLE_TOP );

        $html  = '<h2 class="wp-block-heading" style="' . $style . ' margin-bottom:20px;">Novedades</h2>';
        $html .= do_shortcode('[products limit="4" columns="4" orderby="date" order="DESC"]');

        return '<div style="max-width: 1000px; margin: 0 auto; margin-bottom: 50px;">' . $html . '</div>';
    }
}