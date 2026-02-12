<?php
declare(strict_types=1);

namespace Artesania\Core\Product;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CatalogManager
 *
 * Gestiona la funcionalidad de "Modo Catálogo" del plugin.
 *
 * Responsabilidades:
 * 1. Eliminar botones de compra en listados y ficha de producto.
 * 2. Ocultar elementos del carrito en la interfaz de escritorio.
 * 3. Restringir el acceso directo a las páginas de carrito y checkout.
 * 4. Mantener la integridad visual del tema Storefront en dispositivos móviles.
 *
 * @package Artesania\Core\Product
 * @version 1.0.0
 */
class CatalogManager {

    /**
     * Inicializa los hooks necesarios para el modo catálogo.
     */
    public function __construct() {
        add_action( 'wp', [ $this, 'remove_purchase_buttons' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_catalog_styles' ], 999 );
        add_action( 'template_redirect', [ $this, 'protect_cart_pages' ] );
        add_action( 'init', [ $this, 'remove_storefront_header_cart' ] );
    }

    /**
     * Elimina los botones de "Añadir al carrito" en los loops de tienda y fichas de producto.
     *
     * @return void
     */
    public function remove_purchase_buttons(): void {
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    }

    /**
     * Elimina el carrito de la cabecera principal de Storefront (Desktop).
     * Nota: No afecta a la barra inferior en móviles para preservar la estabilidad del diseño.
     *
     * @return void
     */
    public function remove_storefront_header_cart(): void {
        remove_action( 'storefront_header', 'storefront_header_cart', 60 );
    }

    /**
     * Inyecta CSS para ocultar elementos visuales del carrito en escritorio y widgets.
     *
     * @return void
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
     * Redirige a la tienda si el usuario intenta acceder manualmente al carrito o checkout.
     * Implementa una capa de seguridad para evitar compras no autorizadas.
     *
     * @return void
     */
    public function protect_cart_pages(): void {
        if ( is_cart() || is_checkout() ) {
            wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
            exit;
        }
    }
}