<?php
namespace Artesania\Core\Sales;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class SalesLimiter
 *
 * Gestiona la disponibilidad de las pasarelas de pago.
 * Intercepta el flujo de checkout para deshabilitar métodos de pago
 * que hayan superado los límites fiscales o de producción configurados.
 *
 * @package Artesania\Core\Sales
 * @author  Fco Javier García Cañero
 * @version 2.3.0
 */
class SalesLimiter {

    /**
     * Inicializa el filtro de pasarelas de WooCommerce.
     */
    public function __construct() {
        add_filter( 'woocommerce_available_payment_gateways', [ $this, 'filter_gateways' ] );
    }

    /**
     * Filtra las pasarelas disponibles basándose en los límites anuales.
     *
     * @param array $available_gateways Lista de pasarelas activas.
     * @return array Pasarelas filtradas.
     */
    public function filter_gateways( array $available_gateways ): array {
        // Permitir siempre el acceso a administradores (excepto en peticiones AJAX del checkout)
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return $available_gateways;
        }

        $calculator = new SalesCalculator();
        $limits     = get_option( 'artesania_sales_limits', [] );

        foreach ( $available_gateways as $id => $gateway ) {
            // Verificar si el bloqueo está activo para esta pasarela
            if ( empty( $limits[ $id ]['active'] ) || 'yes' !== $limits[ $id ]['active'] ) {
                continue;
            }

            $limit_amount = isset( $limits[ $id ]['amount'] ) ? (float) $limits[ $id ]['amount'] : 0.0;
            $limit_orders = isset( $limits[ $id ]['orders'] ) ? (int) $limits[ $id ]['orders'] : 0;

            // Si no hay límites definidos, continuar
            if ( $limit_amount <= 0 && $limit_orders <= 0 ) {
                continue;
            }

            $stats = $calculator->get_annual_stats( $id );
            $should_block = false;

            // Verificación de límite monetario
            if ( $limit_amount > 0 && $stats['total'] >= $limit_amount ) {
                $should_block = true;
            }

            // Verificación de límite de volumen (pedidos)
            if ( $limit_orders > 0 && $stats['count'] >= $limit_orders ) {
                $should_block = true;
            }

            if ( $should_block ) {
                unset( $available_gateways[ $id ] );
            }
        }

        return $available_gateways;
    }
}