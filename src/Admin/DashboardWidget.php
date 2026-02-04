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
 * Muestra el estado fiscal y respeta la configuración de visibilidad.
 *
 * @package Artesania\Core\Admin
 * @version 2.4.0
 */
class DashboardWidget {

    public function __construct() {
        add_action( 'wp_dashboard_setup', [ $this, 'register_widget' ] );
    }

    public function register_widget(): void {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        // Verificar preferencia de visibilidad
        if ( 'yes' !== get_option( 'artesania_show_dashboard_widget', 'yes' ) ) {
            return;
        }

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

        echo '<div class="artesania-dashboard-widget">';

        // Resumen Global
        echo '<div class="artesania-summary-box">';
        echo '<h3 class="artesania-summary-title">TOTAL ' . date('Y') . '</h3>';
        echo '<p class="artesania-total-amount">' . wc_price( $annual_stats['total'] ) . '</p>';
        echo '<span class="artesania-total-count">' . esc_html( $annual_stats['count'] ) . ' pedidos</span>';
        echo '</div>';

        // Tabla de Desglose
        echo '<table class="artesania-table">';
        echo '<thead><tr><th>Método</th><th>Actual</th><th>Límite</th><th>Estado</th></tr></thead><tbody>';

        if ( empty( $gateways ) ) {
            echo '<tr><td colspan="4">Sin métodos activos.</td></tr>';
        } else {
            foreach ( $gateways as $id => $gateway ) {
                $stats     = $calculator->get_annual_stats( $id );
                $limit_amt = (float) ( $limits[ $id ]['amount'] ?? 0 );
                $limit_ord = (int) ( $limits[ $id ]['orders'] ?? 0 );
                $active    = isset( $limits[ $id ]['active'] ) && 'yes' === $limits[ $id ]['active'];

                // Determinar estado
                $is_blocked_amt = $limit_amt > 0 && $stats['total'] >= $limit_amt;
                $is_blocked_ord = $limit_ord > 0 && $stats['count'] >= $limit_ord;

                $css_class = 'artesania-status-ok';
                $status_txt = 'OK';

                if ( $is_blocked_amt || $is_blocked_ord ) {
                    $css_class = 'artesania-status-error';
                    $status_txt = 'Límite';
                } elseif ( ! $active ) {
                    $css_class = 'artesania-status-info';
                    $status_txt = 'Info';
                }

                // Renderizado fila
                echo '<tr>';
                echo '<td><strong>' . esc_html( $gateway->get_title() ) . '</strong></td>';
                echo '<td>' . wc_price( $stats['total'] ) . '</td>';

                $display_limits = [];
                if ( $limit_amt > 0 ) $display_limits[] = wc_price( $limit_amt );
                if ( $limit_ord > 0 ) $display_limits[] = $limit_ord . ' ped.';
                echo '<td class="artesania-status-info">' . ( empty( $display_limits ) ? '∞' : implode( '<br>', $display_limits ) ) . '</td>';

                echo '<td><span class="' . esc_attr( $css_class ) . '">' . esc_html( $status_txt ) . '</span></td>';
                echo '</tr>';
            }
        }
        echo '</tbody></table>';

        echo '<div class="artesania-footer-link"><a href="' . admin_url('options-general.php?page=artesania-control&tab=fiscal') . '">Configurar</a></div>';
        echo '</div>';
    }
}