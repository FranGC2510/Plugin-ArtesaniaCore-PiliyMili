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
        // 1. Estilos Globales (Cargan primero - Prioridad 99)
        add_action( 'wp_head', [ $this, 'render_global_css' ], 99 );

        // 2. Estilos Móviles (Cargan después para sobrescribir - Prioridad 100)
        add_action( 'wp_head', [ $this, 'render_mobile_css' ], 100 );
    }

    /**
     * Renderiza TODO el CSS específico para móviles.
     */
    public function render_mobile_css(): void {
        ?>
        <style id="artesania-mobile-css">
            @media (max-width: 768px) {

                /* 1. PROTECCIÓN DE TEXTO */
                .col-full { padding-left: 20px !important; padding-right: 20px !important; }

                /* 2. TÉCNICA "FULL BLEED" (Para imágenes y fondos) */
                .wp-block-image,
                .wp-block-cover,
                .wp-block-media-text__media,
                figure.wp-block-image,
                ul.products,
                .home .entry-content ul.products {
                    width: 100vw !important;
                    position: relative !important;
                    left: 50% !important;
                    right: 50% !important;
                    margin-left: -50vw !important;
                    margin-right: -50vw !important;
                    max-width: 100vw !important;
                    border-radius: 0 !important;
                }

                .wp-block-media-text__media img, .wp-block-image img {
                    width: 100% !important;
                    display: block !important;
                }

                /* 3. GRID DE PRODUCTOS (Alineación Perfecta) */
                ul.products, .home .entry-content ul.products {
                    display: grid !important;
                    grid-template-columns: 1fr 1fr !important;
                    column-gap: 15px !important;
                    row-gap: 20px !important;
                    margin-bottom: 40px !important;
                    justify-content: start !important;
                    padding-left: 10px !important;
                    padding-right: 10px !important;
                    box-sizing: border-box !important;
                }

                ul.products::before, ul.products::after { content: none !important; display: none !important; }

                /* --- ESTRUCTURA DE LA TARJETA --- */
                ul.products li.product, .home .entry-content ul.products li.product {
                    width: 100% !important;
                    float: none !important;
                    margin: 0 !important;
                    clear: none !important;
                    display: flex !important;
                    flex-direction: column !important;
                    height: 100% !important;
                }

                /* --- ESTRUCTURA INTERNA DEL ENLACE (Imagen + Título + Precio) --- */
                /* Hacemos que el enlace ocupe todo el hueco hasta el botón */
                ul.products li.product a.woocommerce-LoopProduct-link {
                    display: flex !important;
                    flex-direction: column !important;
                    flex-grow: 1 !important; /* Ocupa todo el espacio vertical disponible */
                }

                ul.products li.product img {
                    width: 100% !important;
                    height: auto !important;
                    margin-bottom: 8px !important;
                    display: block !important;
                    margin-top: 0 !important;
                }

                /* TÍTULO: Empuja lo de abajo hacia el fondo */
                ul.products li.product h2.woocommerce-loop-product__title,
                ul.products li.product .woocommerce-loop-category__title {
                    font-size: 14px !important;
                    line-height: 1.3 !important;
                    padding-top: 5px !important;
                    min-height: 0 !important;
                    margin-bottom: auto !important; /* Esto empuja Precio y Oferta hacia abajo */
                }

                /* PRECIO Y OFERTA */
                ul.products li.product .onsale {
                    position: static !important;        /* Dejamos de flotarla sobre la imagen */
                    display: inline-block !important;   /* Comportamiento de caja pequeña */
                    width: auto !important;             /* Ancho automático (no estirado) */
                    align-self: center !important;      /* Centrado horizontalmente */
                    margin-bottom: 5px !important;
                    margin-top: 5px !important;

                    font-size: 10px !important;
                    padding: 3px 8px !important;
                    line-height: 1 !important;
                    min-height: 0 !important;
                    border-radius: 0 !important;
                    top: auto !important; right: auto !important; left: auto !important;
                }

                ul.products li.product .price {
                    margin-bottom: 5px !important;
                    align-self: center !important;
                }

                /* BOTÓN ALINEADO AL FONDO */
                ul.products li.product .button {
                    font-size: 11px !important;
                    padding: 8px 10px !important;
                    width: 100% !important;
                    margin-top: 0 !important;
                }

                /* 4. ARREGLO DEL MENÚ (Suave) */
                button.menu-toggle {
                    display: block !important;
                    background-color: #ffffff !important;
                    color: #000000 !important;
                    font-weight: 800 !important;
                    text-transform: uppercase !important;
                    border: none !important;
                    border-bottom: 1px solid #e6e6e6 !important;
                    text-align: center !important;
                    padding: 15px 0 !important;
                    width: calc(100% + 40px) !important;
                    margin-left: -20px !important;
                    margin-right: -20px !important;
                    margin-top: 0 !important;
                    margin-bottom: 0 !important;
                }

                .handheld-navigation {
                    background-color: #ffffff !important;
                    padding: 0 !important;
                    border-bottom: 1px solid #e6e6e6 !important;
                    width: calc(100% + 40px) !important;
                    margin-left: -20px !important;
                    margin-right: -20px !important;
                }

                .handheld-navigation ul.menu li a {
                    color: #000000 !important; padding: 15px 20px !important; border-bottom: 1px solid #f0f0f0 !important;
                    font-size: 14px !important; font-weight: 600 !important;
                }
                .storefront-handheld-footer-bar ul li.my-account { display: none !important; }
                .storefront-handheld-footer-bar ul li.search, .storefront-handheld-footer-bar ul li.cart { width: 50% !important; display: inline-block !important; float: left !important; }

                /* 5. EXTRAS */
                .post-type-archive-product .woocommerce-products-header { margin-bottom: 10px !important; padding-bottom: 0 !important; }
                .post-type-archive-product .woocommerce-products-header__title.page-title {
                    margin-bottom: 0 !important; text-align: center !important; width: 100% !important; display: block !important;
                }
                .home.page .site-content { padding-top: 10px !important; }
                .page .entry-header { display: none !important; }
                .page .site-content { padding-top: 30px !important; }

                body { overflow-x: hidden !important; }
            }
        </style>
        <?php
    }

    /**
     * Renderiza el CSS Global (Identidad de Marca y Escritorio).
     */
    public function render_global_css(): void {
        ?>
        <style id="artesania-global-css">
            /* === 1. LÍMITES Y ESTRUCTURA (ESTILO BOUTIQUE COMPACTO) === */
            .col-full, .site-content .col-full {
                max-width: 1200px !important; /* Ancho generoso */
                margin: 0 auto !important;    /* Centrado */
                padding-left: 30px !important; /* AIRE IZQUIERDA */
                padding-right: 30px !important; /* AIRE DERECHA */
            }

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

            /* === 3. ESTILO MINIATURAS PRODUCTOS === */
            .home .entry-content ul li h2, .wc-block-grid__product-title {
                font-size: 16px !important; /* Letra un pelín más pequeña para acompañar el nuevo ancho */
                color: #000000 !important; text-decoration: none !important;
                min-height: 50px !important; display: flex !important; align-items: flex-start !important;
                justify-content: center !important; margin-bottom: 10px !important; line-height: 1.2 !important;
            }
            .home .entry-content ul li a.button { color: #ffffff !important; }
            .home .entry-content ul li a:not(.button) { color: #000000 !important; text-decoration: none !important; }

            /* === 4. NOTIFICACIONES === */
            .woocommerce-message { background-color: #000000 !important; color: #ffffff !important; border-top-color: #333333 !important; }
            .woocommerce-message a, .woocommerce-message::before { color: #ffffff !important; font-weight: bold !important; }

            /* === 5. TIPOGRAFÍA Y FORMULARIOS === */
            h1.entry-title, h2.wp-block-heading {
                text-align: center !important; color: #000000 !important; font-size: 32px !important; /* Bajamos de 34 a 32 */
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

            /* === 6. FORMULARIO TETRIS (DESKTOP) === */
            @media (min-width: 768px) {
                .entry-content form { display: grid !important; grid-template-columns: 1fr 1fr !important; column-gap: 20px !important; align-items: stretch !important; max-width: 800px !important; /* Formulario más estrecho */ margin: 0 auto !important; }
                .entry-content form > p:not(:has(textarea)):not(:has(input[type="submit"])), .entry-content form > div:not(:has(textarea)):not(:has(input[type="submit"])) { grid-column: 1 !important; margin-bottom: 20px !important; }
                .entry-content form > p:has(textarea), .entry-content form > div:has(textarea) { grid-column: 2 !important; grid-row: 1 / span 3 !important; margin-bottom: 20px !important; height: auto !important; }
                .entry-content form textarea { height: 100% !important; min-height: 100% !important; }
                .entry-content form > p:has(input[type="submit"]), .entry-content form > div:has(input[type="submit"]), .entry-content input[type="submit"] { grid-column: 1 / -1 !important; grid-row: 4 !important; }
            }

            /* === 7. EXTRAS === */
            .wp-block-group:has(.woocommerce-info), .wp-block-group:not(:has(.product)) { display: none !important; }
            .home .entry-content ul.products { display: flex !important; flex-wrap: wrap !important; justify-content: center !important; }
            .home .entry-content ul.products li.product { float: none !important; margin-left: 10px !important; margin-right: 10px !important; }
        </style>
        <?php
    }
}