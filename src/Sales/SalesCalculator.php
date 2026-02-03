<?php
namespace Artesania\Core\Sales;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class SalesCalculator
 *
 * Motor de cálculo financiero.
 * Gestiona las consultas a la base de datos de WooCommerce para obtener
 * métricas de facturación y volumen de pedidos filtrados por fecha y pasarela.
 *
 * @package Artesania\Core\Sales
 * @author  Fco Javier García Cañero
 * @version 2.3.0
 */
class SalesCalculator {

    /**
     * Obtiene las estadísticas acumuladas del año en curso.
     *
     * @param string|null $payment_method_id ID de la pasarela de pago (opcional).
     * @return array { count: int, total: float }
     */
    public function get_annual_stats( $payment_method_id = null ): array {
        $start_date = date( 'Y-01-01 00:00:00' );
        $end_date   = date( 'Y-12-31 23:59:59' );

        return $this->query_orders( $start_date, $end_date, $payment_method_id );
    }

    /**
     * Obtiene las estadísticas del mes en curso.
     *
     * @param string|null $payment_method_id ID de la pasarela de pago (opcional).
     * @return array { count: int, total: float }
     */
    public function get_monthly_stats( $payment_method_id = null ): array {
        $start_date = date( 'Y-m-01 00:00:00' );
        $end_date   = date( 'Y-m-t 23:59:59' );

        return $this->query_orders( $start_date, $end_date, $payment_method_id );
    }

    /**
     * Ejecuta la consulta de pedidos a través de la capa de abstracción de WooCommerce.
     *
     * @param string      $start_date     Fecha de inicio (Y-m-d H:i:s).
     * @param string      $end_date       Fecha de fin (Y-m-d H:i:s).
     * @param string|null $payment_method ID del método de pago.
     * @return array Datos agregados (total y conteo).
     */
    private function query_orders( string $start_date, string $end_date, $payment_method = null ): array {
        $args = [
            'limit'        => -1,
            'status'       => [ 'processing', 'completed' ],
            'date_created' => $start_date . '...' . $end_date,
            'return'       => 'ids',
        ];

        if ( ! empty( $payment_method ) ) {
            $args['payment_method'] = $payment_method;
        }

        $order_ids = wc_get_orders( $args );

        $total_amount = 0.0;
        $count        = count( $order_ids );

        foreach ( $order_ids as $order_id ) {
            $order = wc_get_order( $order_id );
            if ( $order ) {
                $total_amount += $order->get_total();
            }
        }

        return [
            'count' => $count,
            'total' => $total_amount,
        ];
    }
}