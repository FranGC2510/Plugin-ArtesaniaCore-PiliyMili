<?php
declare(strict_types=1);

namespace Artesania\Core\Admin;

use Artesania\Core\Sales\SalesCalculator;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class DashboardWidget
 *
 * Widget informativo para el Escritorio de WordPress.
 * Implementación MVC.
 *
 * @package Artesania\Core\Admin
 * @version 2.4.0
 */
class DashboardWidget {

    public function __construct() {
        add_action( 'wp_dashboard_setup', [ $this, 'register_widget' ] );
    }

    public function register_widget(): void {
        if ( ! current_user_can( 'manage_woocommerce' ) ) return;
        if ( 'yes' !== get_option( 'artesania_show_dashboard_widget', 'yes' ) ) return;

        wp_add_dashboard_widget(
            'artesania_sales_dashboard',
            'Estado Pili & Mili (Anual)',
            [ $this, 'render_content' ]
        );
    }

    public function render_content(): void {
        $calculator    = new SalesCalculator();
        $annual_stats  = $calculator->get_annual_stats();
        $gateways      = \WC()->payment_gateways->get_available_payment_gateways();
        $limits        = get_option( 'artesania_sales_limits', [] );

        $rows_data = [];

        if ( ! empty( $gateways ) ) {
            foreach ( $gateways as $id => $gateway ) {
                $stats     = $calculator->get_annual_stats( $id );
                $limit_amt = (float) ( $limits[ $id ]['amount'] ?? 0 );
                $limit_ord = (int) ( $limits[ $id ]['orders'] ?? 0 );
                $active    = isset( $limits[ $id ]['active'] ) && 'yes' === $limits[ $id ]['active'];

                $blocked_by_amt = $limit_amt > 0 && $stats['total'] >= $limit_amt;
                $blocked_by_ord = $limit_ord > 0 && $stats['count'] >= $limit_ord;

                $status_class = 'artesania-status-ok';
                $status_text  = 'OK';

                if ( $blocked_by_amt || $blocked_by_ord ) {
                    $status_class = 'artesania-status-error';
                    $status_text  = 'Límite';
                } elseif ( ! $active ) {
                    $status_class = 'artesania-status-info';
                    $status_text  = 'Info';
                }

                $rows_data[] = [
                    'title'          => $gateway->get_title(),
                    'current_total'  => $stats['total'],
                    'current_count'  => $stats['count'],
                    'limit_amt'      => $limit_amt,
                    'limit_ord'      => $limit_ord,
                    'status_class'   => $status_class,
                    'status_text'    => $status_text
                ];
            }
        }

        $this->load_view( 'dashboard-widget', [
            'annual_stats' => $annual_stats,
            'rows_data'    => $rows_data
        ] );
    }

    private function load_view( string $view_name, array $args = [] ): void {
        if ( ! empty( $args ) ) extract( $args );
        $file_path = ARTESANIA_CORE_PATH . 'templates/admin/' . $view_name . '.php';
        if ( file_exists( $file_path ) ) include $file_path;
    }
}