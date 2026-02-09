<?php
defined( 'ABSPATH' ) || exit;
/**
 * Vista de la Pestaña Textos.
 * Permite editar mensajes de stock, footer y configuración de WhatsApp.
 */
?>
<h3>Textos Personalizados</h3>
<table class="form-table">
    <tr>
        <th scope="row">Mensaje Stock</th>
        <td>
            <textarea name="<?php echo $option_texts; ?>[stock_msg]" rows="3" class="large-text code"><?php echo esc_textarea( $val_stock ); ?></textarea>
            <p class="description">Mensaje que aparece bajo el botón de compra en productos sin stock físico.</p>
        </td>
    </tr>
    <tr>
        <th scope="row">Pie de Página</th>
        <td>
            <input type="text" name="<?php echo $option_texts; ?>[footer_text]" value="<?php echo esc_attr( $val_footer ); ?>" class="regular-text">
            <p class="description">Texto o HTML del copyright en el footer.</p>
        </td>
    </tr>
    <tr>
        <th scope="row">Instagram (URL)</th>
        <td>
            <input type="url" name="<?php echo $option_texts; ?>[instagram_url]" value="<?php echo esc_attr( $val_instagram ); ?>" class="regular-text" placeholder="https://instagram.com/...">
        </td>
    </tr>
    <tr>
        <th scope="row">Facebook (URL)</th>
        <td>
            <input type="url" name="<?php echo $option_texts; ?>[facebook_url]" value="<?php echo esc_attr( $val_facebook ); ?>" class="regular-text" placeholder="https://facebook.com/...">
        </td>
    </tr>
    <tr>
        <th scope="row">WhatsApp (Móvil)</th>
        <td>
            <input type="text"
                   name="<?php echo $option_texts; ?>[whatsapp_number]"
                   value="<?php echo esc_attr( $val_whatsapp ); ?>"
                   class="regular-text"
                   placeholder="Ej: 34600000000">
            <p class="description">Escribe el número con el código de país (sin el +). Ej: <b>34</b> para España seguido del móvil. Si lo dejas vacío, el botón no aparecerá.</p>
        </td>
    </tr>
</table>
