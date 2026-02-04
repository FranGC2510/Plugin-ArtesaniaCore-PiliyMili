<?php
declare(strict_types=1);

namespace Artesania\Core\Product;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class AvailabilityManager
 *
 * Gestiona mensajes de stock (Slow Design) usando textos personalizados de la BD.
 *
 * @package Artesania\Core\Product
 * @version 2.4.0
 */
class AvailabilityManager {

    public function __construct() {
        add_filter( 'woocommerce_get_availability_text', [ $this, 'custom_backorder_text' ], 10, 2 );
    }

    /**
     * Modifica el texto de disponibilidad/reserva.
     *
     * @param string      $text    Texto original.
     * @param \WC_Product $product Producto.
     * @return string Texto sanitizado.
     */
    public function custom_backorder_text( $text, $product ): string {
        if ( $product->managing_stock() && $product->is_on_backorder( 1 ) ) {
            $custom_texts = get_option( 'artesania_custom_texts', [] );
            $default_msg  = __( 'Se fabrica bajo pedido. Producto hecho a mano con mucho amor.', 'artesania-core' );

            $final_msg = $custom_texts['stock_msg'] ?? $default_msg;

            return esc_html( $final_msg );
        }

        return $text;
    }
}