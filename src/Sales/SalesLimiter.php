<?php
declare(strict_types=1);

namespace Artesania\Core\Sales;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class SalesLimiter
 *
 * Gestiona la disponibilidad de las pasarelas de pago y la INVALIDACIÓN DE CACHÉ.
 * Actúa como "Observador" de los pedidos para mantener las estadísticas actualizadas.
 *
 * @package Artesania\Core\Sales
 * @version 2.4.0
 */
class SalesLimiter {

    /**
     * Inicializa los filtros y hooks de observación.
     */
    public function __construct() {
        add_filter( 'woocommerce_available_payment_gateways', [ $this, 'filter_gateways' ] );

        $cache_clearing_hooks = [
            'woocommerce_new_order',
            'woocommerce_order_status_completed',
            'woocommerce_order_status_processing',
            'woocommerce_order_status_on-hold',
            'woocommerce_order_status_cancelled',
            'woocommerce_order_status_refunded',
            'woocommerce_order_status_failed'
        ];

        foreach ( $cache_clearing_hooks as $hook ) {
            add_action( $hook, [ __CLASS__, 'clear_stats_cache' ] );
        }
    }

    /**
     * Callback estático para limpiar la caché de la calculadora.
     * Llama al método de la clase SalesCalculator.
     */
    public static function clear_stats_cache(): void {
        if ( class_exists( '\Artesania\Core\Sales\SalesCalculator' ) ) {
            \Artesania\Core\Sales\SalesCalculator::delete_stats_cache();
        }
    }

    /**
     * Filtra las pasarelas disponibles basándose en los límites anuales.
     *
     * @param array $available_gateways Lista de pasarelas activas.
     * @return array Pasarelas filtradas.
     */
    public function filter_gateways( array $available_gateways ): array {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return $available_gateways;
        }

        $calculator = new SalesCalculator();
        $limits     = get_option( 'artesania_sales_limits', [] );

        foreach ( $available_gateways as $id => $gateway ) {
            if ( empty( $limits[ $id ]['active'] ) || 'yes' !== $limits[ $id ]['active'] ) {
                continue;
            }

            $limit_amount = (float) ( $limits[ $id ]['amount'] ?? 0 );
            $limit_orders = (int) ( $limits[ $id ]['orders'] ?? 0 );

            if ( $limit_amount <= 0 && $limit_orders <= 0 ) {
                continue;
            }

            $stats = $calculator->get_annual_stats( $id );
            $should_block = false;

            if ( $limit_amount > 0 && $stats['total'] >= $limit_amount ) {
                $should_block = true;
            }

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