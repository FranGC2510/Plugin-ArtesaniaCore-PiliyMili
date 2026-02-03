<?php
namespace Artesania\Core\Admin;

use Artesania\Core\Sales\SalesCalculator;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class DashboardWidget
 *
 * Widget informativo para el Escritorio de WordPress.
 * Versión optimizada con clases CSS externas.
 *
 * @package Artesania\Core\Admin
 * @author  Fco Javier García Cañero
 * @version 2.3.1
 */
class DashboardWidget {

    public function __construct() {
        add_action( 'wp_dashboard_setup', [ $this, 'register_widget' ] );
    }

    public function register_widget(): void {
        if ( ! current_user_can( 'manage_options' ) ) return;

        wp_add_dashboard_widget(
            'artesania_sales_dashboard',
            'Estado Pili & Mili (Anual)',
            [ $this, 'render_content' ]
        );
    }

    public function render_content(): void {
        $calculator    = new SalesCalculator();
        $annual_global = $calculator->get_annual_stats();
        $gateways      = WC()->payment_gateways->get_available_payment_gateways();
        $limits        = get_option( 'artesania_sales_limits', [] );

        echo '<div class="artesania-dashboard-widget">';

        // --- Resumen Global ---
        echo '<div class="artesania-summary-box">';
        echo '<h3 class="artesania-summary-title">TOTAL AÑO ' . date('Y') . '</h3>';
        echo '<p class="artesania-total-amount">' . wc_price( $annual_global['total'] ) . '</p>';
        echo '<span class="artesania-total-count">' . esc_html( $annual_global['count'] ) . ' pedidos totales</span>';
        echo '</div>';

        // --- Tabla de Desglose ---
        echo '<h4>Desglose por Método de Pago (Acumulado Anual)</h4>';
        echo '<table class="artesania-table">';
        echo '<thead><tr>
                <th>Método</th>
                <th>Llevamos (Año)</th>
                <th>Límite Config.</th>
                <th>Estado</th>
              </tr></thead>';
        echo '<tbody>';

        if ( empty( $gateways ) ) {
            echo '<tr><td colspan="4">No hay métodos activos.</td></tr>';
        } else {
            foreach ( $gateways as $id => $gateway ) {
                $stats = $calculator->get_annual_stats( $id );

                $limit_amount = isset( $limits[ $id ]['amount'] ) ? (float) $limits[ $id ]['amount'] : 0.0;
                $limit_orders = isset( $limits[ $id ]['orders'] ) ? (int) $limits[ $id ]['orders'] : 0;
                $is_active    = isset( $limits[ $id ]['active'] ) && 'yes' === $limits[ $id ]['active'];

                // Formateo de límites
                $display_limits = [];
                if ( $limit_amount > 0 ) $display_limits[] = wc_price( $limit_amount );
                if ( $limit_orders > 0 ) $display_limits[] = $limit_orders . ' ped.';

                // Lógica de Clases CSS para Estados
                $css_class = 'artesania-status-ok';
                $icon      = 'Listo';
                $reasons   = [];

                if ( $limit_amount > 0 && $stats['total'] >= $limit_amount ) $reasons[] = 'Dinero';
                if ( $limit_orders > 0 && $stats['count'] >= $limit_orders ) $reasons[] = 'Pedidos';

                if ( ! empty( $reasons ) ) {
                    $css_class = 'artesania-status-error';
                    $icon      = 'Límite alcanzado';
                } elseif ( ! $is_active ) {
                    $css_class = 'artesania-status-info';
                    $icon      = '(Solo info)';
                }

                $reason_html = ! empty( $reasons )
                    ? '<span class="artesania-status-reason">(' . implode( ' + ', $reasons ) . ')</span>'
                    : '';

                echo '<tr>';
                // Columna Nombre
                echo '<td>
                        <span class="artesania-gateway-name">' . esc_html( $gateway->get_title() ) . '</span>
                      </td>';

                // Columna Realidad
                echo '<td>' . wc_price( $stats['total'] ) . '<br><small>' . $stats['count'] . ' ped.</small></td>';

                // Columna Límite
                echo '<td class="artesania-status-info">' . ( empty( $display_limits ) ? '∞' : implode( '<br>', $display_limits ) ) . '</td>';

                // Columna Estado (Usando clases)
                echo '<td>
                        <div class="artesania-status ' . esc_attr( $css_class ) . '">
                            ' . $icon . '
                        </div>
                        ' . $reason_html . '
                      </td>';
                echo '</tr>';
            }
        }

        echo '</tbody></table>';

        echo '<div class="artesania-footer-link">';
        echo '<a href="' . admin_url('options-general.php?page=artesania-control') . '">Configurar Límites</a>';
        echo '</div>';
        echo '</div>'; // Fin widget
    }
}