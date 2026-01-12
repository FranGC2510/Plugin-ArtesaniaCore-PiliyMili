<?php
namespace Artesania\Core\Product;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class AvailabilityManager
 *
 * Gestiona la mensajería de stock y disponibilidad.
 * Adapta el lenguaje técnico de WooCommerce ("Reserva") a la filosofía "Slow Design"
 * ("Fabricación bajo pedido").
 *
 * @package Artesania\Core\Product
 */
class AvailabilityManager {

    public function __construct() {
        // Filtrar el texto de disponibilidad
        add_filter( 'woocommerce_get_availability_text', [ $this, 'custom_backorder_text' ], 10, 2 );
    }

    /**
     * Filtra y modifica el texto de disponibilidad mostrado al usuario.
     *
     * @param string      $text    Texto original de disponibilidad.
     * @param \WC_Product $product Objeto del producto.
     * @return string Texto modificado y localizado.
     */
    public function custom_backorder_text( $text, $product ) {
        // Verificar si el producto gestiona inventario y admite reservas
        if ( $product->managing_stock() && $product->is_on_backorder( 1 ) ) {
            // Aquí definimos el mensaje "Hecho a mano"
            $text = __( 'Se fabrica bajo pedido. Producto hecho a mano con mucho amor.', 'artesania-core' );
        }

        return $text;
    }
}