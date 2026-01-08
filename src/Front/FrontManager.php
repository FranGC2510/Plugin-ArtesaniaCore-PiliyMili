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

        // 5. Enganchamos nuestros estilos al cabezal de la web
        add_action( 'wp_head', [ $this, 'imprimir_estilos_movil' ] );

        // 6. ESTILOS GLOBALES (Marca, Menú, Formularios...)
        add_action( 'wp_head', [ $this, 'imprimir_estilos_globales' ] );
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

/**
     * Inyecta CSS para forzar 2 columnas en móvil
     * Se carga en el <head> de la web automáticamente.
     */
    public function imprimir_estilos_movil() {
        ?>
        <style>
            /* === MODO MOSAICO (2 COLUMNAS) EN MÓVIL === */
            @media (max-width: 768px) {

                /* 1. ELIMINAR EL HUECO FANTASMA (Vital para Storefront) */
                ul.products::before,
                ul.products::after {
                    content: none !important;
                    display: none !important;
                }

                /* 2. FORZAR GRID (Incluimos .home para ganar al centrado anterior) */
                ul.products,
                .home .entry-content ul.products {
                    display: grid !important;
                    grid-template-columns: 1fr 1fr !important; /* 50% - 50% */
                    column-gap: 15px !important;
                    row-gap: 20px !important;
                    margin-bottom: 40px !important;
                    justify-content: start !important; /* Anulamos el centrado flex */
                }

                /* 3. RESETEAR ESTILOS DE LISTA */
                ul.products li.product,
                .home .entry-content ul.products li.product {
                    width: 100% !important;
                    float: none !important;
                    margin: 0 !important;
                    clear: none !important;
                }

                /* 4. TEXTOS Y TÍTULOS MÁS PEQUEÑOS */
                ul.products li.product h2.woocommerce-loop-product__title,
                ul.products li.product .woocommerce-loop-category__title {
                    font-size: 14px !important;
                    line-height: 1.3 !important;
                    padding-top: 5px !important;
                    min-height: 0 !important;
                }

                /* 5. IMÁGENES AL 100% */
                ul.products li.product img {
                    width: 100% !important;
                    height: auto !important;
                    margin-bottom: 8px !important;
                    display: block !important;
                }

                /* 6. BOTÓN */
                ul.products li.product .button {
                    font-size: 11px !important;
                    padding: 8px 10px !important;
                    width: 100% !important;
                }
            }
        </style>
        <?php
    }

/**
     * Inyecta el CSS Global de la Marca (Tipografías, Menú, Botones...)
     * Esto sustituye al "CSS Adicional" del personalizador.
     */
    public function imprimir_estilos_globales() {
        ?>
        <style>
            /* =========================================
               1. ESTRUCTURA Y LIMPIEZA GENERAL
               ========================================= */
            .storefront-breadcrumb { display: none; }
            .site-content { padding-top: 60px !important; }
            a.added_to_cart { display: none !important; }

            /* Quitar subrayados */
            .site-content a, .entry-content a, .wc-block-grid__product-title,
            .wc-block-grid__product-link, .woocommerce-loop-product__title {
                text-decoration: none !important;
                box-shadow: none !important;
            }

            /* =========================================
               2. MENÚ DE NAVEGACIÓN
               ========================================= */
            .storefront-primary-navigation {
                background-color: #ffffff !important;
                border-bottom: 1px solid #e6e6e6 !important;
                z-index: 9999 !important;
            }
            .storefront-primary-navigation .col-full {
                display: flex !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                justify-content: space-between !important;
                width: 100% !important;
            }
            .storefront-primary-navigation .col-full::before,
            .storefront-primary-navigation .col-full::after {
                display: none !important;
                content: none !important;
            }

            /* Corrección para desplegables en Desktop */
            .storefront-primary-navigation, .main-navigation { overflow: visible !important; }
            .main-navigation ul.menu ul.sub-menu {
                background-color: #ffffff !important;
                border: 1px solid #e6e6e6 !important;
                width: 220px !important;
            }

            /* =========================================
               3. ESTILO DE PRODUCTOS
               ========================================= */
            .home .entry-content ul li h2,
            .wc-block-grid__product-title,
            .wc-block-components-product-title {
                font-size: 20px !important;
                color: #000000 !important;
                text-decoration: none !important;
                min-height: 75px !important;
                display: flex !important;
                align-items: flex-start !important;
                justify-content: center !important;
                margin-bottom: 10px !important;
                line-height: 1.2 !important;
            }
            /* Botones y Enlaces */
            .home .entry-content ul li a.button,
            .wc-block-grid__product-add-to-cart .wp-block-button__link { color: #ffffff !important; }
            .home .entry-content ul li a:not(.button):not(.wp-block-button__link) { color: #000000 !important; text-decoration: none !important; }

            /* =========================================
               4. NOTIFICACIONES (Negro)
               ========================================= */
            .woocommerce-message { background-color: #000000 !important; color: #ffffff !important; border-top-color: #333333 !important; }
            .woocommerce-message a { color: #ffffff !important; font-weight: bold !important; }
            .woocommerce-message::before { color: #ffffff !important; }

            /* =========================================
               5. TÍTULOS Y FORMULARIOS (ESTILO SLOW)
               ========================================= */
            h1.entry-title, h2.wp-block-heading {
                text-align: center !important;
                color: #000000 !important;
                font-size: 34px !important;
                font-weight: 300 !important;
                text-transform: none !important;
                letter-spacing: normal !important;
                line-height: 1.2 !important;
                margin-bottom: 30px !important;
            }
            /* Ajuste margen superior para textos de sección */
            h2.wp-block-heading { margin-top: 50px !important; }
            /* Excepción para títulos de ofertas (sin margen arriba) */
            h2.wp-block-heading[style*="margin-top:0px"],
            h2.wp-block-heading[style*="margin-top: 0px"] { margin-top: 0 !important; }

            /* Campos de formulario */
            .entry-content input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]),
            .entry-content textarea, .wpcf7-form-control-wrap input, .wpcf7-form-control-wrap textarea {
                background-color: #ffffff !important;
                border: 1px solid #000000 !important;
                border-radius: 0 !important;
                padding: 12px !important;
                color: #333333 !important;
                box-shadow: none !important;
                max-width: 100% !important;
            }
            .entry-content label {
                text-transform: uppercase !important;
                font-size: 12px !important;
                font-weight: bold !important;
                color: #000000 !important;
                margin-bottom: 5px !important;
                display: block !important;
            }
            /* Botón Enviar */
            .entry-content input[type="submit"], .wpcf7-submit, .wp-block-button__link, button[type="submit"] {
                background-color: #000000 !important;
                color: #ffffff !important;
                border: none !important;
                border-radius: 0 !important;
                text-transform: uppercase !important;
                font-weight: bold !important;
                padding: 15px 30px !important;
                width: 100% !important;
                cursor: pointer !important;
                margin-top: 10px !important;
            }
            .entry-content input[type="submit"]:hover, .wpcf7-submit:hover { background-color: #333333 !important; }

            /* =========================================
               6. FORMULARIO TETRIS (Desktop)
               ========================================= */
            @media (min-width: 768px) {
                .entry-content form {
                    display: grid !important;
                    grid-template-columns: 1fr 1fr !important;
                    column-gap: 20px !important;
                    align-items: stretch !important;
                    max-width: 900px !important;
                    margin: 0 auto !important;
                }
                .entry-content form > p:not(:has(textarea)):not(:has(input[type="submit"])),
                .entry-content form > div:not(:has(textarea)):not(:has(input[type="submit"])) { grid-column: 1 !important; margin-bottom: 20px !important; }

                .entry-content form > p:has(textarea),
                .entry-content form > div:has(textarea) { grid-column: 2 !important; grid-row: 1 / span 3 !important; margin-bottom: 20px !important; height: auto !important; }
                .entry-content form textarea { height: 100% !important; min-height: 100% !important; }

                .entry-content form > p:has(input[type="submit"]),
                .entry-content form > div:has(input[type="submit"]),
                .entry-content input[type="submit"] { grid-column: 1 / -1 !important; grid-row: 4 !important; }
            }

            /* =========================================
               7. AJUSTES MÓVILES Y EXTRAS
               ========================================= */
            .wp-block-group:has(.woocommerce-info), .wp-block-group:not(:has(.product)) { display: none !important; }

            /* Centrado de productos en portada (Desktop/Flex) */
            .home .entry-content ul.products { display: flex !important; flex-wrap: wrap !important; justify-content: center !important; }
            .home .entry-content ul.products li.product { float: none !important; margin-left: 10px !important; margin-right: 10px !important; }

            /* Menú Móvil Storefront */
            @media (max-width: 768px) {
                button.menu-toggle {
                    display: block !important; width: 100% !important; background-color: #ffffff !important;
                    color: #000000 !important; font-weight: 800 !important; text-transform: uppercase !important;
                    border: none !important; border-bottom: 1px solid #e6e6e6 !important; text-align: center !important;
                    padding: 15px 0 !important; margin: 0 !important;
                }
                .handheld-navigation { background-color: #ffffff !important; padding: 0 !important; border-bottom: 1px solid #e6e6e6 !important; }
                .handheld-navigation ul.menu li a { color: #000000 !important; padding: 15px 20px !important; border-bottom: 1px solid #f0f0f0 !important; font-size: 14px !important; }
                .storefront-handheld-footer-bar ul li.my-account { display: none !important; }
                .storefront-handheld-footer-bar ul li.search, .storefront-handheld-footer-bar ul li.cart { width: 50% !important; display: inline-block !important; float: left !important; }

                /* Ajustes de espaciado móvil */
                .post-type-archive-product .woocommerce-products-header { margin-bottom: 10px !important; padding-bottom: 0 !important; }
                .post-type-archive-product .woocommerce-products-header__title.page-title { margin-bottom: 0 !important; text-align: center !important; width: 100% !important; display: block !important; }
                .home.page .site-content { padding-top: 10px !important; }
                .page .entry-header { display: none !important; }
                .page .site-content { padding-top: 30px !important; }
            }
        </style>
        <?php
    }
}