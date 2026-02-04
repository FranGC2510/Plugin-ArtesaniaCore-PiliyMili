<?php defined( 'ABSPATH' ) || exit; ?>
<div class="artesania-dashboard-widget">

    <div class="artesania-summary-box">
        <h3 class="artesania-summary-title">TOTAL <?php echo date('Y'); ?></h3>
        <p class="artesania-total-amount"><?php echo wc_price( $annual_stats['total'] ); ?></p>
        <span class="artesania-total-count"><?php echo esc_html( $annual_stats['count'] ); ?> pedidos</span>
    </div>

    <table class="artesania-table">
        <thead><tr><th>Método</th><th>Actual</th><th>Límite</th><th>Estado</th></tr></thead>
        <tbody>
        <?php if ( empty( $rows_data ) ) : ?>
            <tr><td colspan="4">Sin métodos activos.</td></tr>
        <?php else : ?>
            <?php foreach ( $rows_data as $row ) : ?>
                <tr>
                    <td><strong><?php echo esc_html( $row['title'] ); ?></strong></td>
                    <td><?php echo wc_price( $row['current_total'] ); ?></td>

                    <td class="artesania-status-info">
                        <?php
                        $limits = [];
                        if ( $row['limit_amt'] > 0 ) $limits[] = wc_price( $row['limit_amt'] );
                        if ( $row['limit_ord'] > 0 ) $limits[] = $row['limit_ord'] . ' ped.';
                        echo empty( $limits ) ? '∞' : implode( '<br>', $limits );
                        ?>
                    </td>

                    <td><span class="<?php echo esc_attr( $row['status_class'] ); ?>"><?php echo esc_html( $row['status_text'] ); ?></span></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="artesania-footer-link">
        <a href="<?php echo admin_url('options-general.php?page=artesania-control&tab=fiscal'); ?>">Configurar</a>
    </div>
</div>
