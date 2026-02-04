<?php defined( 'ABSPATH' ) || exit; ?>
<h3>Textos Personalizados</h3>
<table class="form-table">
    <tr>
        <th scope="row">Mensaje Stock</th>
        <td>
            <textarea name="<?php echo $option_texts; ?>[stock_msg]" rows="3" class="large-text code"><?php echo esc_textarea( $val_stock ); ?></textarea>
        </td>
    </tr>
    <tr>
        <th scope="row">Pie de Página</th>
        <td>
            <input type="text" name="<?php echo $option_texts; ?>[footer_text]" value="<?php echo esc_attr( $val_footer ); ?>" class="regular-text">
        </td>
    </tr>
</table>
