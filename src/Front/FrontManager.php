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

        // 4. SHORTCODE DE NOVEDADES (Portada)
        add_shortcode( 'seccion_novedades', [ $this, 'mostrar_novedades_estilizadas' ] );
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
         * Lógica de Cabeceras en Tienda y Categorías
         */
        public function cabeceras_tienda() {

            // ESTILO COMÚN PARA LOS TÍTULOS (Copiado de tus preferencias anteriores)
            $estilo_titulo_arriba = 'text-align:center; text-transform:none; font-weight:300; margin-top:0px; margin-bottom:30px; font-size:34px; color:#000000; line-height:1.2;';
            $estilo_titulo_abajo  = 'text-align:center; text-transform:none; font-weight:300; margin-top:60px; margin-bottom:20px; font-size:34px; border-top:1px solid #e6e6e6; padding-top:50px; color:#000000; line-height:1.2;';

            // --- CASO 1: TIENDA PRINCIPAL ---
            if ( is_shop() ) {
                // A. TÍTULO COLECCIONES
                echo '<h2 class="wp-block-heading" style="' . $estilo_titulo_arriba . '">Colecciones</h2>';

                // Muestra solo las categorías PADRE (parent="0")
                echo do_shortcode('[product_categories limit="6" columns="3" parent="0"]');

                // B. TÍTULO TODOS LOS PRODUCTOS
                echo '<h2 class="wp-block-heading" style="' . $estilo_titulo_abajo . '">Todos los productos</h2>';
            }

            // --- CASO 2: DENTRO DE UNA CATEGORÍA (Ej: Eventos, Hogar...) ---
            elseif ( is_product_category() ) {

                // 1. Obtenemos la categoría actual
                $objeto_actual = get_queried_object();
                $id_actual     = $objeto_actual->term_id;
                $nombre_cat    = $objeto_actual->name;

                // 2. Comprobamos si tiene subcategorías hijas
                $hijas = get_terms( 'product_cat', array(
                    'parent'     => $id_actual,
                    'hide_empty' => false
                ) );

                // 3. SI TIENE HIJAS -> Las mostramos arriba
                if ( ! empty( $hijas ) ) {
                    // Título dinámico: "EXPLORA [NOMBRE CATEGORÍA]"
                    echo '<h2 class="wp-block-heading" style="' . $estilo_titulo_arriba . '">Explora ' . esc_html( $nombre_cat ) . '</h2>';

                    // Shortcode mágico: muestra las categorías hijas de la actual
                    echo do_shortcode('[product_categories limit="6" columns="3" parent="' . $id_actual . '"]');

                    // Título separador para los productos
                    echo '<h2 class="wp-block-heading" style="' . $estilo_titulo_abajo . '">Productos</h2>';
                }
            }
        }

    /**
     * Lógica de Ofertas (Portada)
     */
    public function mostrar_ofertas_inteligentes() {
        if ( ! function_exists( 'wc_get_product_ids_on_sale' ) ) return '';

        $ofertas = wc_get_product_ids_on_sale();

        if ( ! empty( $ofertas ) ) {
            $estilo_titulo = 'text-align:center; text-transform:none; font-weight:300; margin-top:0px; margin-bottom:20px; font-size:34px; color:#000000; line-height: 1.2;';

            $html  = '<h2 class="wp-block-heading" style="' . $estilo_titulo . '">Productos rebajados</h2>';
            $html .= do_shortcode('[sale_products limit="4" columns="4"]');

            return '<div style="max-width: 1000px; margin-left: auto; margin-right: auto;">' . $html . '</div>';
        }
        return '';
    }

    /**
     * Lógica de Novedades (Para que sea gemela a Ofertas)
     * Shortcode: [seccion_novedades]
     */
    public function mostrar_novedades_estilizadas() {
            // Usamos la misma variable de estilo para que sean gemelos
            $estilo_titulo = 'text-align:center; text-transform:none; font-weight:300; margin-top:60px; margin-bottom:20px; font-size:34px; color:#000000; line-height: 1.2;';

            $html  = '<h2 class="wp-block-heading" style="' . $estilo_titulo . '">Novedades</h2>';
            $html .= do_shortcode('[products limit="4" columns="4" orderby="date" order="DESC"]');

            // Margen inferior para separar del pie de página
            return '<div style="max-width: 1000px; margin-left: auto; margin-right: auto; margin-bottom: 50px;">' . $html . '</div>';
       }
}