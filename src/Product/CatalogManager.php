<?php
declare(strict_types=1);

namespace Artesania\Core\Product;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CatalogManager
 *
 * Gestiona exclusivamente la lógica del Modo Catálogo.
 * Incluye la modificación de etiquetas "Tienda" por "Catálogo".
 *
 * @package Artesania\Core\Product
 * @version 1.3.0
 */
class CatalogManager {

    /**
     * Inicializa los hooks del Modo Catálogo.
     */
    public function __construct() {
        add_action( 'wp', [ $this, 'remove_purchase_buttons' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_catalog_styles' ], 999 );
        add_action( 'template_redirect', [ $this, 'protect_cart_pages' ] );
        add_action( 'init', [ $this, 'remove_storefront_header_cart' ] );
        add_filter( 'woocommerce_page_title', [ $this, 'change_shop_archive_title' ] );
        add_filter( 'woocommerce_get_breadcrumb_prepend', [ $this, 'change_shop_breadcrumb_text' ], 20 );
        add_filter( 'wp_nav_menu_objects', [ $this, 'change_menu_item_title' ] );
    }

    /**
     * Cambia el título de la página de archivo de productos.
     */
    public function change_shop_archive_title( $page_title ): string {
        if ( is_shop() ) {
            return 'Catálogo';
        }
        return $page_title;
    }

    /**
     * Cambia el texto "Tienda" en las migas de pan de WooCommerce.
     */
    public function change_shop_breadcrumb_text( $crumbs ) {
        foreach ( $crumbs as $key => $crumb ) {
            if ( 'Tienda' === $crumb[0] ) {
                $crumbs[$key][0] = 'Catálogo';
            }
        }
        return $crumbs;
    }

    /**
     * Cambia dinámicamente el nombre del enlace en el menú de navegación.
     */
    public function change_menu_item_title( $items ) {
        foreach ( $items as $item ) {
            if ( 'page' === $item->object && (int) $item->object_id === (int) wc_get_page_id( 'shop' ) ) {
                $item->title = 'Catálogo';
            }
        }
        return $items;
    }

    /**
     * Elimina los botones de "Añadir al carrito".
     */
    public function remove_purchase_buttons(): void {
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    }

    /**
     * Elimina el carrito del header (Desktop).
     */
    public function remove_storefront_header_cart(): void {
        remove_action( 'storefront_header', 'storefront_header_cart', 60 );
    }

    /**
     * Inyecta CSS específico del Modo Catálogo.
     */
    public function enqueue_catalog_styles(): void {
        $css = "
            .site-header-cart, 
            .widget_shopping_cart, 
            a.cart-contents,
            .woocommerce-mini-cart { display: none !important; }
        ";
        wp_add_inline_style( 'artesania-front-css', $css );
    }

    /**
     * Bloquea el acceso a páginas de compra.
     */
    public function protect_cart_pages(): void {
        if ( is_cart() || is_checkout() ) {
            wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
            exit;
        }
    }
}