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
 * @package Artesania\Core\Front
 */
class AssetsManager {

    public function __construct() {
        // 1. Cargar fuentes de Google
        add_action( 'wp_enqueue_scripts', [ $this, 'load_google_fonts' ] );
        // 2. Estilos Globales (Prioridad 99)
        add_action( 'wp_head', [ $this, 'render_global_css' ], 99 );
        // 3. Estilos Móviles (Prioridad 100 para sobrescribir)
        add_action( 'wp_head', [ $this, 'render_mobile_css' ], 100 );
    }

    public function load_google_fonts() {
        wp_enqueue_style(
            'artesania-google-fonts',
            'https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Montserrat:wght@300;400;600;700&display=swap',
            [],
            '2.0.0'
        );
    }

    public function render_mobile_css(): void {
        ?>
        <style id="artesania-mobile-css">
            @media (max-width: 768px) {
                /* 1. PROTECCIÓN DE TEXTO */
                .col-full { padding-left: 20px !important; padding-right: 20px !important; }

                /* 2. FULL BLEED & GRID */
                .wp-block-image, .wp-block-cover, .wp-block-media-text__media, figure.wp-block-image,
                ul.products, .home .entry-content ul.products {
                    width: 100vw !important; position: relative !important; left: 50% !important; right: 50% !important;
                    margin-left: -50vw !important; margin-right: -50vw !important; max-width: 100vw !important; border-radius: 0 !important;
                }
                .wp-block-media-text__media img, .wp-block-image img { width: 100% !important; display: block !important; }

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

                /* 4. MENÚ MÓVIL (Hamburguesa CSS Puro) */
                button.menu-toggle {
                    position: absolute !important; top: 25px !important; right: 20px !important; left: auto !important;
                    width: 45px !important; height: 45px !important; border-radius: 50% !important;
                    background-color: #ffffff !important; box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
                    border: 1px solid rgba(0,0,0,0.05) !important; color: transparent !important; font-size: 0 !important; padding: 0 !important; z-index: 100 !important;
                }
                button.menu-toggle span, button.menu-toggle::after { display: none !important; }
                button.menu-toggle::before {
                    content: "" !important; display: block !important;
                    width: 20px !important; height: 2px !important; background-color: #000000 !important; border-radius: 1px !important;
                    position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important;
                    box-shadow: 0 -6px 0 0 #000000, 0 6px 0 0 #000000 !important; margin: 0 !important;
                }

                .handheld-navigation {
                    background-color: #ffffff !important; padding: 0 !important; border-bottom: 1px solid #e6e6e6 !important;
                    width: calc(100% + 40px) !important; margin-left: -20px !important; margin-right: -20px !important; margin-top: 20px !important;
                }
                .handheld-navigation ul.menu li a {
                    color: #000000 !important; padding: 15px 20px !important; border-bottom: 1px solid #f0f0f0 !important;
                    font-size: 14px !important; font-weight: 600 !important;
                }
                .storefront-handheld-footer-bar ul li.my-account { display: none !important; }
                .storefront-handheld-footer-bar ul li.search, .storefront-handheld-footer-bar ul li.cart { width: 50% !important; display: inline-block !important; float: left !important; }

                /* EXTRAS */
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

    public function render_global_css(): void {
        ?>
        <style id="artesania-global-css">

            /* === 0. IDENTIDAD === */
            h1, h2, h3, h4, h5, h6, .entry-title, .wp-block-heading, .wc-block-grid__product-title, .woocommerce-loop-product__title, .site-title, .page-title {
                font-family: 'Cinzel', serif !important; text-transform: none !important;
            }
            body, p, li, a, button, input, textarea, select, .site-content, .entry-content, .main-navigation, .handheld-navigation, .storefront-primary-navigation, .button, .price, .onsale, label {
                font-family: 'Montserrat', sans-serif !important;
            }

            /* === 1. ESTRUCTURA === */
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

            /* === 3. ESTILO PRODUCTOS (DESKTOP) === */
            .home .entry-content ul li h2, .wc-block-grid__product-title {
                font-size: 16px !important; color: #000000 !important; text-decoration: none !important;
                min-height: 50px !important; display: flex !important; align-items: flex-start !important; justify-content: center !important; margin-bottom: 10px !important; line-height: 1.2 !important;
            }
            .home .entry-content ul li a.button { color: #ffffff !important; }
            .home .entry-content ul li a:not(.button) { color: #000000 !important; text-decoration: none !important; }

            @media (min-width: 769px) {
                ul.products, .home .entry-content ul.products { display: flex !important; flex-wrap: wrap !important; }
                ul.products li.product, .home .entry-content ul.products li.product { display: flex !important; flex-direction: column !important; float: left !important; }
                ul.products li.product a.woocommerce-LoopProduct-link { display: flex !important; flex-direction: column !important; flex-grow: 1 !important; text-decoration: none !important; }
                ul.products li.product img { align-self: center !important; margin-bottom: 15px !important; }
                ul.products li.product h2.woocommerce-loop-product__title, .wc-block-grid__product-title, ul.products li.product .woocommerce-loop-category__title { margin-bottom: auto !important; padding-top: 5px !important; }
                ul.products li.product .onsale {
                    position: static !important; display: inline-block !important; width: auto !important; align-self: center !important;
                    margin-top: 10px !important; margin-bottom: 5px !important; font-size: 10px !important; padding: 3px 8px !important;
                    line-height: 1 !important; min-height: 0 !important; border-radius: 0 !important; background-color: #ffffff !important;
                    color: #000000 !important; border: 1px solid #000000 !important; text-transform: uppercase !important; font-weight: 600 !important;
                }
                ul.products li.product .price { align-self: center !important; margin-bottom: 10px !important; text-align: center !important; color: #000000 !important; }
                ul.products li.product .button, .home .entry-content ul li a.button { margin-top: 0 !important; align-self: center !important; width: 100% !important; text-align: center !important; }
            }

            /* === 4. NOTIFICACIONES & INPUTS === */
            .woocommerce-message { background-color: #000000 !important; color: #ffffff !important; border-top-color: #333333 !important; }
            .woocommerce-message a, .woocommerce-message::before { color: #ffffff !important; font-weight: bold !important; }
            h1.entry-title, h2.wp-block-heading { text-align: center !important; color: #000000 !important; font-size: 32px !important; font-weight: 300 !important; text-transform: none !important; line-height: 1.2 !important; margin-bottom: 30px !important; }
            h2.wp-block-heading { margin-top: 50px !important; }

            .entry-content input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), .entry-content textarea {
                background-color: #ffffff !important; border: 1px solid #000000 !important; border-radius: 0 !important;
                padding: 12px !important; color: #333333 !important; box-shadow: none !important; max-width: 100% !important;
            }
            .entry-content label { text-transform: uppercase !important; font-size: 12px !important; font-weight: bold !important; color: #000000 !important; display: block !important; }

            /* Selectores Base */
            .variations select, .artesania-custom-field input {
                border: 1px solid #000000 !important; border-radius: 0 !important; background-color: #ffffff !important;
                color: #000000 !important; padding: 20px !important; font-size: 15px !important; min-height: 52px !important;
                font-family: 'Montserrat', sans-serif !important; box-shadow: none !important; width: 100% !important; margin-bottom: 20px !important;
            }
            .variations label { font-weight: 700 !important; text-transform: uppercase !important; font-size: 12px !important; margin-bottom: 8px !important; display: block !important; }

            /* === 10. ACTION BAR (HOMOGENEIZADA) === */

            /* BASE: CANTIDAD */
            /* Forzamos que la caja de cantidad sea idéntica en altura y estilo al botón */
            .quantity {
                margin-bottom: 0 !important;
                margin-right: 0 !important;
                /* El ancho se define por media query */
            }
            .quantity .input-text.qty {
                width: 100% !important;
                margin-right: 0 !important;
                /* HOMOGENEIZACIÓN VISUAL */
                height: 52px !important; /* Altura fija para alineación pixel-perfect */
                border: 1px solid #000000 !important;
                font-weight: 600 !important;
                padding: 0 !important; /* El texto se centra solo */
            }

            /* BASE: BOTÓN */
            .single_add_to_cart_button {
                height: 52px !important; /* Misma altura exacta que la cantidad */
                line-height: 52px !important; /* Centrado vertical texto */
                padding: 0 20px !important; /* Reset de padding vertical */
                border-radius: 0 !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                font-size: 13px !important;
                letter-spacing: 1px !important;
                transition: all 0.2s ease !important;
            }

            /* --- DISPOSITIVO: MÓVIL (< 769px) --- */
            /* Apilados (Uno encima de otro) */
            @media (max-width: 768px) {
                .quantity { width: 80px !important; margin-bottom: 15px !important; }
                .single_add_to_cart_button { width: 100% !important; margin-top: 0 !important; }
            }

            /* --- DISPOSITIVO: ESCRITORIO (>= 769px) --- */
            /* Alineados (Action Bar) */
            @media (min-width: 769px) {
                /* Layout Flex para Productos Simples */
                form.cart:not(.variations_form) {
                    display: flex !important; flex-wrap: wrap !important; align-items: flex-end !important; gap: 0 !important; /* Sin gap para efecto unido, o pequeño */
                }
                .artesania-custom-field { flex: 1 0 100% !important; margin-bottom: 25px !important; }

                /* Layout Flex para Productos Variables */
                .woocommerce-variation-add-to-cart {
                    display: flex !important; flex-wrap: nowrap !important; align-items: flex-end !important; gap: 0 !important;
                }

                /* TAMAÑOS DESKTOP */
                .quantity {
                    flex: 0 0 80px !important; /* Ancho fijo de la caja numérica */
                    margin-right: 10px !important; /* Separación visual del botón */
                }
                .single_add_to_cart_button {
                    flex-grow: 1 !important; /* El botón ocupa el resto */
                }
            }

            /* === 11. ESTADOS DE BOTÓN (ELEGANCIA) === */

            /* ESTADO 1: DESHABILITADO (Cuando no has elegido opciones) */
            /* Transformamos el bloque gris feo en un botón "Fantasma" limpio */
            .single_add_to_cart_button.disabled,
            .single_add_to_cart_button:disabled,
            .single_add_to_cart_button[disabled] {
                background-color: #ffffff !important; /* Fondo blanco */
                color: #bbbbbb !important;            /* Texto gris claro */
                border: 1px solid #e0e0e0 !important; /* Borde sutil */
                opacity: 1 !important;                /* Quitamos la opacidad de defecto de WC */
                cursor: not-allowed !important;
            }
            /* Hover en deshabilitado (para que no haga nada) */
            .single_add_to_cart_button.disabled:hover,
            .single_add_to_cart_button:disabled:hover {
                background-color: #ffffff !important;
                color: #bbbbbb !important;
            }

            /* ESTADO 2: HABILITADO (Listo para comprar) */
            /* Bloque negro sólido, potente */
            .single_add_to_cart_button:not(.disabled):not(:disabled) {
                background-color: #000000 !important;
                color: #ffffff !important;
                border: 1px solid #000000 !important;
            }
            /* Hover activo */
            .single_add_to_cart_button:not(.disabled):not(:disabled):hover {
                background-color: #333333 !important;
                border-color: #333333 !important;
            }

            /* Utiles de Texto (Refactorización previa) */
            .artesania-title-top { text-align: center !important; text-transform: none !important; font-weight: 300 !important; margin-top: 0 !important; margin-bottom: 30px !important; font-size: 34px !important; color: #000000 !important; line-height: 1.2 !important; }
            .artesania-title-bottom { text-align: center !important; text-transform: none !important; font-weight: 300 !important; margin-top: 60px !important; margin-bottom: 20px !important; font-size: 34px !important; border-top: 1px solid #e6e6e6 !important; padding-top: 50px !important; color: #000000 !important; line-height: 1.2 !important; }
            .artesania-mb-20 { margin-bottom: 20px !important; } .artesania-mb-50 { margin-bottom: 50px !important; } .artesania-mt-60 { margin-top: 60px !important; }
            .artesania-section-wrapper { max-width: 1000px !important; margin: 0 auto !important; }
            .artesania-site-info { text-align: center !important; padding: 2em 0 !important; font-size: 0.9em !important; clear: both !important; border-top: 1px solid #f0f0f0 !important; margin-top: 2em !important; }
            .artesania-separator { margin: 0 10px !important; color: #ccc !important; } .artesania-legal-link { color: inherit !important; text-decoration: none !important; }
            .wp-block-group:has(.woocommerce-info), .wp-block-group:not(:has(.product)) { display: none !important; }

            /* === 12. ETIQUETA OFERTA (MODO FICHA PRODUCTO) === */
            .artesania-sale-tag {
                display: inline-block !important;
                margin-left: 15px !important;    /* Separación del precio */

                /* Tipografía */
                font-family: 'Montserrat', sans-serif !important;
                font-size: 11px !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                letter-spacing: 1px !important;

                /* Caja Minimalista */
                color: #000000 !important;
                background-color: #ffffff !important;
                border: 1px solid #000000 !important;
                padding: 4px 10px !important;    /* Un poco de aire */

                /* Alineación */
                vertical-align: middle !important;
                position: relative !important;
                top: -2px !important; /* Ajuste óptico fino para centrar con los números */
            }
        </style>
        <?php
    }
}