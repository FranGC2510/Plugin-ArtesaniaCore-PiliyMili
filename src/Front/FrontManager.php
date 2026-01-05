<?php
namespace Artesania\Core\Front;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Clase FrontManager
 * Se encarga de todo lo visual: Footer, Cabeceras de Tienda y Shortcodes.
 */
class FrontManager {

    public function __construct() {
        // 1. FOOTER PERSONALIZADO
        add_action( 'init', [ $this, 'personalizar_footer' ] );

        // 2. CABECERAS DE TIENDA (Colecciones vs Productos)
        add_action( 'woocommerce_before_shop_loop', [ $this, 'cabeceras_tienda' ], 5 );

        // 3. SHORTCODE DE OFERTAS (Portada)
        add_shortcode( 'seccion_ofertas', [ $this, 'mostrar_ofertas_inteligentes' ] );
    }

    /**
     * Lógica del Footer
     */
    public function personalizar_footer() {
        remove_action( 'storefront_footer', 'storefront_credit', 20 );
        add_action( 'storefront_footer', [ $this, 'nuevo_copyright' ], 20 );
    }

    public function nuevo_copyright() {
        ?>
        <div class="site-info" style="text-align:center; padding: 2em 0; font-size: 0.9em; clear:both; border-top: 1px solid #f0f0f0; margin-top: 2em;">
            &copy; <?php echo date( 'Y' ); ?> <strong>PiliYMili</strong>
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
     * Lógica de Cabeceras en Tienda
     */
    public function cabeceras_tienda() {
        if ( is_shop() ) {
            // A. TÍTULO Y REJILLA DE COLECCIONES
            echo '<h2 class="wp-block-heading" style="text-align:center; text-transform:uppercase; letter-spacing:3px; margin-top:0px; margin-bottom:30px; font-size:24px;">COLECCIONES</h2>';

            echo do_shortcode('[product_categories limit="6" columns="3" parent="0"]');

            // B. TÍTULO DE TODOS LOS PRODUCTOS
            echo '<h2 class="wp-block-heading" style="text-align:center; text-transform:uppercase; letter-spacing:3px; margin-top:60px; margin-bottom:20px; font-size:24px; border-top:1px solid #e6e6e6; padding-top:50px;">TODOS LOS PRODUCTOS</h2>';
        }
    }

    /**
     * Lógica de Ofertas (Portada)
     */
    public function mostrar_ofertas_inteligentes() {
        // Si WooCommerce no está activo o no hay funciones de venta, salimos
        if ( ! function_exists( 'wc_get_product_ids_on_sale' ) ) return '';

        $ofertas = wc_get_product_ids_on_sale();

        if ( ! empty( $ofertas ) ) {
            $html  = '<h2 class="wp-block-heading" style="text-align:center; text-transform:uppercase; letter-spacing:3px; margin-top:0px; margin-bottom:30px; font-size:24px;">PRODUCTOS REBAJADOS</h2>';
            $html .= do_shortcode('[sale_products limit="4" columns="4"]');

            return '<div style="max-width: 1000px; margin-left: auto; margin-right: auto;">' . $html . '</div>';
        }
        return '';
    }
}