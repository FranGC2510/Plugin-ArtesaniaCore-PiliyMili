<?php
declare(strict_types=1);

namespace Artesania\Core\Checkout;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CheckoutManager
 *
 * Gestiona la lógica del Checkout (NIF, Facturas, JS).
 * Usa constantes globales para localizar los scripts.
 *
 * @package Artesania\Core\Checkout
 * @version 2.4.0
 */
class CheckoutManager {

    public function __construct() {
        add_filter( 'woocommerce_checkout_fields', [ $this, 'add_invoice_fields' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_checkout_script' ] );
        add_action( 'woocommerce_checkout_process', [ $this, 'validate_conditional_nif' ] );
        add_action( 'woocommerce_checkout_create_order', [ $this, 'save_invoice_data' ], 10, 2 );
        add_action( 'woocommerce_admin_order_data_after_billing_address', [ $this, 'show_nif_in_admin' ] );
        add_action( 'wpo_wcpdf_after_billing_address', [ $this, 'show_nif_in_pdf' ], 10, 2 );
    }

    /**
     * Encola el JS de checkout desde la carpeta assets/js.
     */
    public function enqueue_checkout_script(): void {
        if ( ! is_checkout() ) return;

        $js_path = 'assets/js/checkout.js';
        $js_url  = ARTESANIA_CORE_URL . $js_path;

        wp_enqueue_script(
            'artesania-checkout-js',
            $js_url,
            ['jquery'],
            '2.4.0',
            true
        );
    }

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

    /**
     * Valida que el NIF sea obligatorio solo si el usuario solicitó factura.
     * Añade un error de WooCommerce si la validación falla.
     *
     * @return void
     */
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

    /**
     * Muestra el NIF en el panel de administración del pedido.
     *
     * @param \WC_Order $order Objeto del pedido.
     * @return void
     */
    public function show_nif_in_admin( $order ) {
        $nif = $order->get_meta( '_billing_nif' );
        if ( $nif ) {
            echo '<p><strong>NIF/DNI:</strong> ' . esc_html( $nif ) . '</p>';
        }
    }

    /**
     * Inyecta el NIF en las facturas PDF generadas por 'WooCommerce PDF Invoices & Packing Slips'.
     *
     * @param string    $template_type Tipo de documento (invoice, packing-slip).
     * @param \WC_Order $order         Objeto del pedido.
     * @return void
     */
    public function show_nif_in_pdf( $template_type, $order ) {
        $nif = $order->get_meta( '_billing_nif' );
        if ( ! empty( $nif ) ) {
            echo '<div class="billing-nif">';
            echo '<strong>NIF/DNI:</strong> ' . esc_html( $nif );
            echo '</div>';
        }
    }
}