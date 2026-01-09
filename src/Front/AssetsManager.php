<?php
declare(strict_types=1);

namespace Artesania\Core\Front;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class AssetsManager
 * Responsable de inyectar estilos (CSS) de forma organizada.
 * Separa estrictamente la lógica Global de la Lógica Móvil.
 * * @package Artesania\Core\Front
 */
class AssetsManager {

    public function __construct() {
        // 1. Cargar fuentes de Google de forma óptima
        add_action( 'wp_enqueue_scripts', [ $this, 'load_google_fonts' ] );
        // 1. Estilos Globales (Prioridad 99)
        add_action( 'wp_head', [ $this, 'render_global_css' ], 99 );
        // 2. Estilos Móviles (Prioridad 100 para sobrescribir)
        add_action( 'wp_head', [ $this, 'render_mobile_css' ], 100 );
    }

    /**
     * Carga las tipografías desde Google Fonts.
     * Esta es la forma estándar de WordPress: no bloquea la carga y es compatible con caché.
     */
    public function load_google_fonts() {
        wp_enqueue_style(
            'artesania-google-fonts',
            'https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Montserrat:wght@300;400;600;700&display=swap',
            [],
            '2.0.0'
        );
    }

    /**
     * Renderiza CSS Móvil (TU VERSIÓN CORRECTA - NO LA TOCAMOS)
     */
    public function render_mobile_css(): void {
        ?>
        <style id="artesania-mobile-css">
            @media (max-width: 768px) {
                /* 1. PROTECCIÓN DE TEXTO */
                .col-full { padding-left: 20px !important; padding-right: 20px !important; }

                /* 2. TÉCNICA "FULL BLEED" */
                .wp-block-image, .wp-block-cover, .wp-block-media-text__media, figure.wp-block-image,
                ul.products, .home .entry-content ul.products {
                    width: 100vw !important; position: relative !important; left: 50% !important; right: 50% !important;
                    margin-left: -50vw !important; margin-right: -50vw !important; max-width: 100vw !important; border-radius: 0 !important;
                }
                .wp-block-media-text__media img, .wp-block-image img { width: 100% !important; display: block !important; }

                /* 3. GRID MÓVIL */
                ul.products, .home .entry-content ul.products {
                    display: grid !important;
                    grid-template-columns: 1fr 1fr !important;
                    column-gap: 15px !important; row-gap: 20px !important;
                    margin-bottom: 40px !important; justify-content: start !important;
                    padding-left: 10px !important; padding-right: 10px !important; box-sizing: border-box !important;
                }
                ul.products::before, ul.products::after { content: none !important; display: none !important; }

                /* Tarjeta Móvil */
                ul.products li.product, .home .entry-content ul.products li.product {
                    width: 100% !important; float: none !important; margin: 0 !important; clear: none !important;
                    display: flex !important; flex-direction: column !important; height: 100% !important;
                }
                ul.products li.product a.woocommerce-LoopProduct-link { display: flex !important; flex-direction: column !important; flex-grow: 1 !important; }
                ul.products li.product h2.woocommerce-loop-product__title, ul.products li.product .woocommerce-loop-category__title {
                    font-size: 14px !important; line-height: 1.3 !important; padding-top: 5px !important; min-height: 0 !important; margin-bottom: auto !important;
                }
                ul.products li.product img { width: 100% !important; height: auto !important; margin-bottom: 8px !important; display: block !important; margin-top: 0 !important; }

                ul.products li.product .onsale {
                    position: static !important; display: inline-block !important; width: auto !important; align-self: center !important;
                    margin-bottom: 5px !important; margin-top: 5px !important; font-size: 10px !important; padding: 3px 8px !important;
                    line-height: 1 !important; min-height: 0 !important; border-radius: 0 !important; top: auto !important; right: auto !important; left: auto !important;
                }
                ul.products li.product .price { margin-bottom: 5px !important; align-self: center !important; }
                ul.products li.product .button { font-size: 11px !important; padding: 8px 10px !important; width: 100% !important; margin-top: 0 !important; }

                /* 4. MENÚ MÓVIL */
                button.menu-toggle {
                    position: absolute !important;
                    top: 25px !important;       /* Alineado verticalmente */
                    right: 20px !important;     /* A la derecha */
                    left: auto !important;

                    /* FORMA DE CÍRCULO PERFECTO */
                    width: 45px !important;
                    height: 45px !important;
                    border-radius: 50% !important;

                    /* ESTILO CONTENEDOR (Para que resalte al bajar) */
                    background-color: #ffffff !important;        /* Fondo blanco */
                    box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important; /* Sombra suave (elevación) */
                    border: 1px solid rgba(0,0,0,0.05) !important;      /* Borde muy sutil */
                    color: #000000 !important;

                    /* Limpieza de texto antiguo */
                    color: transparent !important; /* Texto invisible */
                    font-size: 0 !important;
                    padding: 0 !important;
                    margin: 0 !important;
                    z-index: 100 !important;
                }

                /* ¡IMPORTANTE! Ocultamos cualquier contenido interno que ponga el tema */
                button.menu-toggle span,
                button.menu-toggle::after {
                    display: none !important;
                }

                /* DIBUJO DE LAS 3 RAYAS (HAMBURGUESA) */
                button.menu-toggle::before {
                    content: "" !important;
                    display: block !important;

                    /* La línea central */
                    width: 20px !important;  /* Ancho de la raya */
                    height: 2px !important;  /* Grosor de la raya */
                    background-color: #000000 !important;
                    border-radius: 1px !important;

                    /* Posicionamiento Absoluto al Centro */
                    position: absolute !important;
                    top: 50% !important;
                    left: 50% !important;
                    transform: translate(-50%, -50%) !important;

                    /* Las líneas de arriba y abajo (usando sombras sólidas) */
                    box-shadow:
                        0 -6px 0 0 #000000, /* Raya de arriba */
                        0 6px 0 0 #000000 !important; /* Raya de abajo */

                    margin: 0 !important;
                }

                .handheld-navigation {
                    background-color: #ffffff !important; padding: 0 !important; border-bottom: 1px solid #e6e6e6 !important;
                    width: calc(100% + 40px) !important; margin-left: -20px !important; margin-right: -20px !important;
                    margin-top: 20px !important;
                }

                .handheld-navigation ul.menu li a {
                    color: #000000 !important; padding: 15px 20px !important; border-bottom: 1px solid #f0f0f0 !important;
                    font-size: 14px !important; font-weight: 600 !important;
                }
                .storefront-handheld-footer-bar ul li.my-account { display: none !important; }
                .storefront-handheld-footer-bar ul li.search, .storefront-handheld-footer-bar ul li.cart { width: 50% !important; display: inline-block !important; float: left !important; }

                /* 5. EXTRAS */
                .post-type-archive-product .woocommerce-products-header { margin-bottom: 10px !important; padding-bottom: 0 !important; }
                .post-type-archive-product .woocommerce-products-header__title.page-title { margin-bottom: 0 !important; text-align: center !important; width: 100% !important; display: block !important; }
                .home.page .site-content { padding-top: 10px !important; }
                .page .entry-header { display: none !important; }
                .page .site-content { padding-top: 30px !important; }
                body { overflow-x: hidden !important; }
            }
        </style>
        <?php
    }

    /**
     * Renderiza CSS Global (Escritorio + Base).
     */
    public function render_global_css(): void {
        ?>
        <style id="artesania-global-css">

            /* === 0. IDENTIDAD TIPOGRÁFICA === */
            /* Títulos y Cabeceras: CINZEL */
            h1, h2, h3, h4, h5, h6,
            .entry-title,
            .wp-block-heading,
            .wc-block-grid__product-title,
            .woocommerce-loop-product__title,
            .site-title,
            .page-title {
                font-family: 'Cinzel', serif !important;
                text-transform: none !important;
            }

            /* Cuerpo, Botones y Menús: MONTSERRAT */
            body, p, li, a, button, input, textarea, select,
            .site-content, .entry-content, .main-navigation, .handheld-navigation,
            .storefront-primary-navigation, .button, .price, .onsale, label {
                font-family: 'Montserrat', sans-serif !important;
            }
            /* === 1. LÍMITES Y ESTRUCTURA === */
            .col-full, .site-content .col-full { max-width: 1200px !important; margin: 0 auto !important; padding-left: 30px !important; padding-right: 30px !important; }
            .storefront-breadcrumb { display: none; }
            .site-content { padding-top: 60px !important; }
            a.added_to_cart { display: none !important; }
            .site-content a, .entry-content a, .wc-block-grid__product-title, .woocommerce-loop-product__title { text-decoration: none !important; box-shadow: none !important; }

            /* === 2. MENÚ DESKTOP === */
            .storefront-primary-navigation { background-color: #ffffff !important; border-bottom: 1px solid #e6e6e6 !important; z-index: 9999 !important; }
            .storefront-primary-navigation .col-full { display: flex !important; flex-wrap: nowrap !important; align-items: center !important; justify-content: space-between !important; width: 100% !important; }
            .storefront-primary-navigation .col-full::before, .storefront-primary-navigation .col-full::after { display: none !important; content: none !important; }
            .storefront-primary-navigation, .main-navigation { overflow: visible !important; }
            .main-navigation ul.menu ul.sub-menu { background-color: #ffffff !important; border: 1px solid #e6e6e6 !important; width: 220px !important; z-index: 99999 !important; }

            /* === 3. ESTILO PRODUCTOS & IGUALACIÓN DE ALTURAS (DESKTOP) === */

            /* A. ESTILO BASE TÍTULOS Y BOTONES (Igual que tenías) */
            .home .entry-content ul li h2, .wc-block-grid__product-title {
                font-size: 16px !important; color: #000000 !important; text-decoration: none !important;
                min-height: 50px !important; display: flex !important; align-items: flex-start !important;
                justify-content: center !important; margin-bottom: 10px !important; line-height: 1.2 !important;
            }
            .home .entry-content ul li a.button { color: #ffffff !important; }
            .home .entry-content ul li a:not(.button) { color: #000000 !important; text-decoration: none !important; }

            /* B. LÓGICA DE ALTURA IGUALADA (Solo escritorio) */
            /* NO tocamos width ni grid, solo activamos Flexbox para igualar altos */
            @media (min-width: 769px) {

                /* El contenedor de productos se convierte en Flex Wrap */
                ul.products, .home .entry-content ul.products {
                    display: flex !important;
                    flex-wrap: wrap !important;
                    /* NOTA: Storefront ya pone width: 22% a los hijos, Flex lo respetará */
                }

                /* La tarjeta del producto: Columna flexible */
                ul.products li.product, .home .entry-content ul.products li.product {
                    display: flex !important;
                    flex-direction: column !important;
                    float: left !important; /* Mantenemos float si Storefront lo necesita, aunque Flex suele ignorarlo */
                    /* IMPORTANTE: No ponemos width aquí para que use el del tema (3 cols, 4 cols...) */
                }

                /* Enlace (Imagen + Título) ocupa lo que sobre */
                ul.products li.product a.woocommerce-LoopProduct-link {
                    display: flex !important;
                    flex-direction: column !important;
                    flex-grow: 1 !important; /* Empuja el botón al fondo */
                    text-decoration: none !important;
                }

                /* Imagen centrada */
                ul.products li.product img {
                    align-self: center !important;
                    margin-bottom: 15px !important;
                }

                /* Título "Muelle": Empuja precio y botón al fondo */
                ul.products li.product h2.woocommerce-loop-product__title,
                .wc-block-grid__product-title,
                ul.products li.product .woocommerce-loop-category__title {
                    margin-bottom: auto !important; /* LA CLAVE */
                    padding-top: 5px !important;
                }

                ul.products li.product .onsale {
                    position: static !important;
                    display: inline-block !important;
                    width: auto !important;
                    align-self: center !important;
                    margin-top: 10px !important;
                    margin-bottom: 5px !important;

                    font-size: 10px !important;
                    padding: 3px 8px !important;
                    line-height: 1 !important;
                    min-height: 0 !important;
                    border-radius: 0 !important;
                    background-color: #ffffff !important;
                    color: #000000 !important;
                    border: 1px solid #000000 !important;
                    text-transform: uppercase !important;
                    font-weight: 600 !important;
                }

                ul.products li.product .price {
                    align-self: center !important;
                    margin-bottom: 10px !important;
                    text-align: center !important;
                    color: #000000 !important;
                }

                /* Botón al fondo */
                ul.products li.product .button, .home .entry-content ul li a.button {
                    margin-top: 0 !important;
                    align-self: center !important;
                    width: 100% !important;
                    text-align: center !important;
                }
            }

            /* === 4. NOTIFICACIONES === */
            .woocommerce-message { background-color: #000000 !important; color: #ffffff !important; border-top-color: #333333 !important; }
            .woocommerce-message a, .woocommerce-message::before { color: #ffffff !important; font-weight: bold !important; }

            /* === 5. TIPOGRAFÍA Y FORMULARIOS === */
            h1.entry-title, h2.wp-block-heading {
                text-align: center !important; color: #000000 !important; font-size: 32px !important;
                font-weight: 300 !important; text-transform: none !important; line-height: 1.2 !important;
                margin-bottom: 30px !important;
            }
            h2.wp-block-heading { margin-top: 50px !important; }
            h2.wp-block-heading[style*="margin-top:0px"], h2.wp-block-heading[style*="margin-top: 0px"] { margin-top: 0 !important; }

            .entry-content input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), .entry-content textarea {
                background-color: #ffffff !important; border: 1px solid #000000 !important; border-radius: 0 !important;
                padding: 12px !important; color: #333333 !important; box-shadow: none !important; max-width: 100% !important;
            }
            .entry-content label { text-transform: uppercase !important; font-size: 12px !important; font-weight: bold !important; color: #000000 !important; display: block !important; }
            .entry-content input[type="submit"], .wpcf7-submit, button[type="submit"] {
                background-color: #000000 !important; color: #ffffff !important; border: none !important; border-radius: 0 !important;
                text-transform: uppercase !important; font-weight: bold !important; padding: 15px 30px !important; width: 100% !important; cursor: pointer !important; margin-top: 10px !important;
            }
            .entry-content input[type="submit"]:hover, .wpcf7-submit:hover { background-color: #333333 !important; }

            /* === 6. FORMULARIO TETRIS === */
            @media (min-width: 768px) {
                .entry-content form { display: grid !important; grid-template-columns: 1fr 1fr !important; column-gap: 20px !important; align-items: stretch !important; max-width: 800px !important; margin: 0 auto !important; }
                .entry-content form > p:not(:has(textarea)):not(:has(input[type="submit"])), .entry-content form > div:not(:has(textarea)):not(:has(input[type="submit"])) { grid-column: 1 !important; margin-bottom: 20px !important; }
                .entry-content form > p:has(textarea), .entry-content form > div:has(textarea) { grid-column: 2 !important; grid-row: 1 / span 3 !important; margin-bottom: 20px !important; height: auto !important; }
                .entry-content form textarea { height: 100% !important; min-height: 100% !important; }
                .entry-content form > p:has(input[type="submit"]), .entry-content form > div:has(input[type="submit"]), .entry-content input[type="submit"] { grid-column: 1 / -1 !important; grid-row: 4 !important; }
            }

            /* === 7. FORMULARIOS Y DESPLEGABLES === */
            /* Base común: Selects, Cantidad y Campo Personalizado */
            .variations select,
            .quantity .input-text.qty,
            .artesania-custom-field input {
                border: 1px solid #000000 !important;
                border-radius: 0 !important;
                background-color: #ffffff !important;
                color: #000000 !important;

                /* MÁS PRESENCIA */
                padding: 20px !important;       /* Aumentamos relleno */
                font-size: 15px !important;     /* Aumentamos letra */
                min-height: 60px !important;    /* Aumentamos altura */

                font-family: 'Montserrat', sans-serif !important;
                box-shadow: none !important;
            }

            /* REGLA DE ORO: Selects y Custom Field ocupan el 100% */
            .variations select,
            .artesania-custom-field input {
                width: 100% !important;
                margin-bottom: 20px !important; /* Separación vertical */
            }

            /* La caja de cantidad (número) NO debe ser 100% */
            .quantity .input-text.qty {
                width: 80px !important;
                margin-right: 10px !important;
            }

            /* Etiquetas */
            .variations label {
                font-weight: 700 !important;
                text-transform: uppercase !important;
                font-size: 12px !important;
                margin-bottom: 8px !important; /* Un poco más de aire */
                display: block !important;
            }
            /* === 8. EXTRAS === */
            .wp-block-group:has(.woocommerce-info), .wp-block-group:not(:has(.product)) { display: none !important; }
            .home .entry-content ul.products { display: flex !important; flex-wrap: wrap !important; justify-content: center !important; }
            .home .entry-content ul.products li.product { float: none !important; margin-left: 10px !important; margin-right: 10px !important; }
        </style>
        <?php
    }
}