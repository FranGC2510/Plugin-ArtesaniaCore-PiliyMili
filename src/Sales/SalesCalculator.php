<?php
declare(strict_types=1);

namespace Artesania\Core\Sales;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class SalesCalculator
 *
 * Motor de cálculo financiero.
 * Gestiona consultas a pedidos con sistema de CACHÉ (Transients) para optimizar rendimiento.
 *
 * @package Artesania\Core\Sales
 * @version 2.6.2
 */
class SalesCalculator {
    /**
     * Duración de la caché en segundos (1 hora por defecto, aunque se limpia por eventos).
     */
    private const CACHE_EXPIRATION = 3600;

    /**
     * Prefijo para las claves de los transients.
     */
    private const CACHE_PREFIX = 'artesania_stats_';

    /**
     * Obtiene las estadísticas acumuladas del año en curso.
     * Utiliza caché para evitar sobrecarga en la base de datos.
     *
     * @param string|null $payment_method_id ID de la pasarela de pago (opcional).
     * @return array { count: int, total: float }
     */
    public function get_annual_stats( ?string $payment_method_id = null ): array {
        $year = date( 'Y' );
        $key_suffix = $payment_method_id ? "_{$payment_method_id}" : '_global';
        $cache_key  = self::CACHE_PREFIX . $year . $key_suffix;

        $cached_stats = get_transient( $cache_key );
        if ( false !== $cached_stats ) {
            return $cached_stats;
        }

        $start_date = date( 'Y-01-01 00:00:00' );
        $end_date   = date( 'Y-12-31 23:59:59' );

        $stats = $this->query_orders( $start_date, $end_date, $payment_method_id );

        if ( $stats['count'] === 0 && $stats['total'] === 0.0 ) {
            \Artesania\Core\Main::log( "Cálculo de ventas anuales devolvió 0. ¿Es correcto o hay un fallo en las fechas? ($year)", 'INFO' );
        }

        set_transient( $cache_key, $stats, self::CACHE_EXPIRATION );

        return $stats;
    }

    /**
     * Obtiene las estadísticas del mes en curso (Sin caché estricta por ahora, o compartida).
     *
     * @param string|null $payment_method_id
     * @return array
     */
    public function get_monthly_stats( ?string $payment_method_id = null ): array {
        $start_date = date( 'Y-m-01 00:00:00' );
        $end_date   = date( 'Y-m-t 23:59:59' );

        return $this->query_orders( $start_date, $end_date, $payment_method_id );
    }

    /**
     * Borra la caché de estadísticas.
     * Debe llamarse cuando cambia el estado de un pedido.
     *
     * @return void
     */
    public static function delete_stats_cache(): void {
        global $wpdb;

        $prefix = '_transient_' . self::CACHE_PREFIX . '%';
        $sql    = $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", $prefix );

        $wpdb->query( $sql );

        $prefix_timeout = '_transient_timeout_' . self::CACHE_PREFIX . '%';
        $sql_timeout    = $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", $prefix_timeout );

        $wpdb->query( $sql_timeout );
    }

    /**
     * Ejecuta la consulta de pedidos.
     * Optimizado para reducir llamadas a la BD (return objects).
     *
     * @param string      $start_date
     * @param string      $end_date
     * @param string|null $payment_method
     * @return array
     */
    private function query_orders( string $start_date, string $end_date, ?string $payment_method = null ): array {
        $args = [
            'limit'        => -1,
            'status'       => [ 'processing', 'completed' ],
            'date_created' => $start_date . '...' . $end_date,
            'return'       => 'objects',
            'type'         => 'shop_order',
        ];

        if ( ! empty( $payment_method ) ) {
            $args['payment_method'] = $payment_method;
        }

        $orders = wc_get_orders( $args );

        $total_amount = 0.0;
        $count        = count( $orders );

        foreach ( $orders as $order ) {
            if ( $order instanceof \WC_Order ) {
                $total_amount += (float) $order->get_total();
            }
        }

        return [
            'count' => $count,
            'total' => $total_amount,
        ];
    }
}