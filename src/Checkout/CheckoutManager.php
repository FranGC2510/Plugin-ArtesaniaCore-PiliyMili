<?php
namespace Artesania\Core\Checkout;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Clase CheckoutManager
 * Gestiona NIF, Checkbox y Conexión con Facturas PDF.
 */
class CheckoutManager {

    public function __construct() {
        add_filter( 'woocommerce_checkout_fields', [ $this, 'add_invoice_fields' ] );

        // CAMBIO: Usamos wp_enqueue_scripts en lugar de wp_footer para carga profesional
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_checkout_script' ] );

        add_action( 'woocommerce_checkout_process', [ $this, 'validate_conditional_nif' ] );
        add_action( 'woocommerce_checkout_create_order', [ $this, 'save_invoice_data' ], 10, 2 );
        add_action( 'woocommerce_admin_order_data_after_billing_address', [ $this, 'show_nif_in_admin' ] );
        add_action( 'wpo_wcpdf_after_billing_address', [ $this, 'show_nif_in_pdf' ], 10, 2 );
    }

    /**
     * Carga condicional del JS de checkout[cite: 91].
     * Solo se carga en la página de checkout para mejorar el rendimiento (WPO).
     */
    public function enqueue_checkout_script() {
        if ( ! is_checkout() ) return;

        // Calculamos la URL del JS
        $plugin_url = plugin_dir_url( dirname( dirname( __DIR__ ) ) . '/artesania-core.php' );
        $js_path    = 'src/Front/assets/js/artesania-checkout.js';

        wp_enqueue_script(
            'artesania-checkout-js',
            $plugin_url . $js_path,
            ['jquery'], // Dependencia
            '2.0.0',
            true // Cargar en el footer
        );
    }

    // ... (Los métodos add_invoice_fields, add_conditional_js_script, validate y save SE MANTIENEN IGUAL) ...
    // Copia aquí los métodos que ya tenías: add_invoice_fields, add_conditional_js_script,
    // validate_conditional_nif, save_invoice_data, show_nif_in_admin.
    // Solo añado el método nuevo abajo:

    public function add_invoice_fields( $fields ) {
        $fields['billing']['billing_wants_invoice'] = array(
            'type'      => 'checkbox',
            'label'     => __( 'Deseo recibir factura con NIF/DNI', 'artesania-core' ),
            'class'     => array( 'form-row-wide' ),
            'clear'     => true,
            'priority'  => 24,
        );
        $fields['billing']['billing_nif'] = array(
            'label'       => __( 'NIF/DNI / CIF', 'artesania-core' ),
            'placeholder' => _x( 'Introduce tu documento', 'placeholder', 'artesania-core' ),
            'required'    => false,
            'class'       => array( 'form-row-wide', 'billing-nif-field' ),
            'clear'       => true,
            'priority'    => 25,
        );
        return $fields;
    }

    public function add_conditional_js_script() {
        if ( ! is_checkout() ) return;
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                var nifField = $('.billing-nif-field'); // Selector por clase más robusto
                var checkbox = $('#billing_wants_invoice');
                if ( ! checkbox.is(':checked') ) nifField.hide();
                checkbox.change(function(){
                    if ( $(this).is(':checked') ) {
                        nifField.slideDown();
                    } else {
                        nifField.slideUp();
                        $('#billing_nif').val('');
                    }
                });
            });
        </script>
        <?php
    }

    public function validate_conditional_nif() {
        if ( ! empty( $_POST['billing_wants_invoice'] ) && empty( $_POST['billing_nif'] ) ) {
            wc_add_notice( __( 'Has solicitado factura, por favor introduce tu NIF/DNI.', 'artesania-core' ), 'error' );
        }
    }

    public function save_invoice_data( $order, $data ) {
        if ( ! empty( $_POST['billing_wants_invoice'] ) ) {
            $order->update_meta_data( '_billing_wants_invoice', 'Sí' );
            if ( ! empty( $_POST['billing_nif'] ) ) {
                $order->update_meta_data( '_billing_nif', sanitize_text_field( $_POST['billing_nif'] ) );
            }
        }
    }

    public function show_nif_in_admin( $order ) {
        $nif = $order->get_meta( '_billing_nif' );
        if ( $nif ) {
            echo '<p><strong>NIF/DNI:</strong> ' . esc_html( $nif ) . '</p>';
        }
    }

    /**
     * NUEVO: Inyectar el NIF en el PDF automáticamente
     */
    public function show_nif_in_pdf( $template_type, $order ) {
        // Solo queremos que salga en la Factura, no en el albarán (opcional)
        // if ( 'invoice' !== $template_type ) return;

        $nif = $order->get_meta( '_billing_nif' );

        if ( ! empty( $nif ) ) {
            echo '<div class="billing-nif">';
            echo '<strong>NIF/DNI:</strong> ' . esc_html( $nif );
            echo '</div>';
        }
    }
}