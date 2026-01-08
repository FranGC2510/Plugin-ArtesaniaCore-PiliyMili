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
     * Agrupa: Grid de productos, Menú Storefront, Cabeceras y Ajustes.
     */
    public function render_mobile_css(): void {
        ?>
        <style id="artesania-mobile-css">
            @media (max-width: 768px) {
                /* --- A. REJILLA DE PRODUCTOS (GRID 2 COLUMNAS) --- */
                /* 1. Reset de estructura Storefront (Eliminar huecos fantasma) */
                ul.products::before, ul.products::after { content: none !important; display: none !important; }

                /* 2. Grid System */
                ul.products, .home .entry-content ul.products {
                    display: grid !important;
                    grid-template-columns: 1fr 1fr !important;
                    column-gap: 15px !important;
                    row-gap: 20px !important;
                    margin-bottom: 40px !important;
                    justify-content: start !important;
                }

                /* 3. Reset de Items */
                ul.products li.product, .home .entry-content ul.products li.product {
                    width: 100% !important; float: none !important; margin: 0 !important; clear: none !important;
                }

                /* 4. Tipografía Productos */
                ul.products li.product h2.woocommerce-loop-product__title,
                ul.products li.product .woocommerce-loop-category__title {
                    font-size: 14px !important; line-height: 1.3 !important; padding-top: 5px !important; min-height: 0 !important;
                }

                /* 5. Imágenes y Botones */
                ul.products li.product img { width: 100% !important; height: auto !important; margin-bottom: 8px !important; display: block !important; }
                ul.products li.product .button { font-size: 11px !important; padding: 8px 10px !important; width: 100% !important; }

                /* --- B. NAVEGACIÓN Y MENÚ (STOREFRONT MÓVIL) --- */
                button.menu-toggle {
                    display: block !important; width: 100% !important; background-color: #ffffff !important;
                    color: #000000 !important; font-weight: 800 !important; text-transform: uppercase !important;
                    border: none !important; border-bottom: 1px solid #e6e6e6 !important; text-align: center !important;
                    padding: 15px 0 !important; margin: 0 !important;
                }
                .handheld-navigation { background-color: #ffffff !important; padding: 0 !important; border-bottom: 1px solid #e6e6e6 !important; }
                .handheld-navigation ul.menu li a {
                    color: #000000 !important; padding: 15px 20px !important; border-bottom: 1px solid #f0f0f0 !important;
                    font-size: 14px !important; font-weight: 600 !important;
                }

                /* Footer Móvil (Barra inferior) */
                .storefront-handheld-footer-bar ul li.my-account { display: none !important; }
                .storefront-handheld-footer-bar ul li.search, .storefront-handheld-footer-bar ul li.cart { width: 50% !important; display: inline-block !important; float: left !important; }

                /* --- C. AJUSTES GENERALES DE PÁGINA --- */
                /* Cabecera Tienda */
                .post-type-archive-product .woocommerce-products-header { margin-bottom: 10px !important; padding-bottom: 0 !important; }
                .post-type-archive-product .woocommerce-products-header__title.page-title {
                    margin-bottom: 0 !important; text-align: center !important; width: 100% !important; display: block !important;
                }

                /* Márgenes */
                .home.page .site-content { padding-top: 10px !important; }
                .page .entry-header { display: none !important; } /* Ocultar título automático */
                .page .site-content { padding-top: 30px !important; }
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
            /* === 1. ESTRUCTURA Y LIMPIEZA === */
            .storefront-breadcrumb { display: none; }
            .site-content { padding-top: 60px !important; }
            a.added_to_cart { display: none !important; }
            .site-content a, .entry-content a, .wc-block-grid__product-title, .woocommerce-loop-product__title { text-decoration: none !important; box-shadow: none !important; }

            /* === 2. MENÚ DE NAVEGACIÓN (DESKTOP) === */
            .storefront-primary-navigation { background-color: #ffffff !important; border-bottom: 1px solid #e6e6e6 !important; z-index: 9999 !important; }
            .storefront-primary-navigation .col-full { display: flex !important; flex-wrap: nowrap !important; align-items: center !important; justify-content: space-between !important; width: 100% !important; }
            .storefront-primary-navigation .col-full::before, .storefront-primary-navigation .col-full::after { display: none !important; content: none !important; }
            /* Fix Desplegables */
            .storefront-primary-navigation, .main-navigation { overflow: visible !important; }
            .main-navigation ul.menu ul.sub-menu { background-color: #ffffff !important; border: 1px solid #e6e6e6 !important; width: 220px !important; z-index: 99999 !important; }

            /* === 3. ESTILO PRODUCTOS === */
            .home .entry-content ul li h2, .wc-block-grid__product-title {
                font-size: 20px !important; color: #000000 !important; text-decoration: none !important;
                min-height: 75px !important; display: flex !important; align-items: flex-start !important;
                justify-content: center !important; margin-bottom: 10px !important; line-height: 1.2 !important;
            }
            .home .entry-content ul li a.button { color: #ffffff !important; }
            .home .entry-content ul li a:not(.button) { color: #000000 !important; text-decoration: none !important; }

            /* === 4. NOTIFICACIONES === */
            .woocommerce-message { background-color: #000000 !important; color: #ffffff !important; border-top-color: #333333 !important; }
            .woocommerce-message a, .woocommerce-message::before { color: #ffffff !important; font-weight: bold !important; }

            /* === 5. TIPOGRAFÍA Y FORMULARIOS (Slow Design) === */
            h1.entry-title, h2.wp-block-heading {
                text-align: center !important; color: #000000 !important; font-size: 34px !important;
                font-weight: 300 !important; text-transform: none !important; line-height: 1.2 !important;
                margin-bottom: 30px !important;
            }
            h2.wp-block-heading { margin-top: 50px !important; }
            /* Excepción para títulos pegados arriba */
            h2.wp-block-heading[style*="margin-top:0px"], h2.wp-block-heading[style*="margin-top: 0px"] { margin-top: 0 !important; }

            /* Inputs */
            .entry-content input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), .entry-content textarea {
                background-color: #ffffff !important; border: 1px solid #000000 !important; border-radius: 0 !important;
                padding: 12px !important; color: #333333 !important; box-shadow: none !important; max-width: 100% !important;
            }
            .entry-content label { text-transform: uppercase !important; font-size: 12px !important; font-weight: bold !important; color: #000000 !important; display: block !important; }

            /* Botones */
            .entry-content input[type="submit"], .wpcf7-submit, button[type="submit"] {
                background-color: #000000 !important; color: #ffffff !important; border: none !important; border-radius: 0 !important;
                text-transform: uppercase !important; font-weight: bold !important; padding: 15px 30px !important; width: 100% !important; cursor: pointer !important; margin-top: 10px !important;
            }
            .entry-content input[type="submit"]:hover, .wpcf7-submit:hover { background-color: #333333 !important; }

            /* === 6. FORMULARIO TETRIS (DESKTOP ONLY) === */
            @media (min-width: 768px) {
                .entry-content form { display: grid !important; grid-template-columns: 1fr 1fr !important; column-gap: 20px !important; align-items: stretch !important; max-width: 900px !important; margin: 0 auto !important; }
                .entry-content form > p:not(:has(textarea)):not(:has(input[type="submit"])), .entry-content form > div:not(:has(textarea)):not(:has(input[type="submit"])) { grid-column: 1 !important; margin-bottom: 20px !important; }
                .entry-content form > p:has(textarea), .entry-content form > div:has(textarea) { grid-column: 2 !important; grid-row: 1 / span 3 !important; margin-bottom: 20px !important; height: auto !important; }
                .entry-content form textarea { height: 100% !important; min-height: 100% !important; }
                .entry-content form > p:has(input[type="submit"]), .entry-content form > div:has(input[type="submit"]), .entry-content input[type="submit"] { grid-column: 1 / -1 !important; grid-row: 4 !important; }
            }

            /* === 7. EXTRAS GLOBALES === */
            .wp-block-group:has(.woocommerce-info), .wp-block-group:not(:has(.product)) { display: none !important; }
            /* Centrado Portada (Desktop Flex) - En móvil gana el Grid de arriba */
            .home .entry-content ul.products { display: flex !important; flex-wrap: wrap !important; justify-content: center !important; }
            .home .entry-content ul.products li.product { float: none !important; margin-left: 10px !important; margin-right: 10px !important; }
        </style>
        <?php
    }
}