<?php
namespace Artesania\Core\Product;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Clase AvailabilityManager
 * Gestiona los mensajes de stock y disponibilidad del producto.
 */
class AvailabilityManager {

    public function __construct() {
        // Filtrar el texto de disponibilidad
        add_filter( 'woocommerce_get_availability_text', [ $this, 'custom_backorder_text' ], 10, 2 );
    }

    /**
     * Cambia el texto "Disponible para reserva" por algo más artesanal.
     * * @param string $text El texto original de WooCommerce.
     * @param \WC_Product $product El objeto producto actual.
     * @return string El texto modificado.
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