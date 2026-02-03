<?php
namespace Artesania\Core\Checkout;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CheckoutManager
 *
 * Gestiona la lógica extendida del proceso de compra (Checkout).
 * Responsabilidades:
 * 1. Campos condicionales de facturación (NIF/DNI).
 * 2. Carga optimizada de JavaScript para interacción de usuario.
 * 3. Validación de datos en el servidor.
 * 4. Persistencia de metadatos en el pedido.
 * 5. Integración con plugins de facturas PDF.
 *
 * @package Artesania\Core\Checkout
 */
class CheckoutManager {

    /**
     * Inicializa los hooks de formulario, validación, guardado y visualización.
     */
    public function __construct() {
        // --- FORMULARIO (INPUT) ---
        add_filter( 'woocommerce_checkout_fields', [ $this, 'add_invoice_fields' ] );

        // CARGA OPTIMIZADA DE JS (Solo encolamos el archivo, nada de inline)
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_checkout_script' ] );

        add_action( 'woocommerce_checkout_process', [ $this, 'validate_conditional_nif' ] );
        add_action( 'woocommerce_checkout_create_order', [ $this, 'save_invoice_data' ], 10, 2 );

        // --- VISUALIZACIÓN (OUTPUT) ---
        add_action( 'woocommerce_admin_order_data_after_billing_address', [ $this, 'show_nif_in_admin' ] );

        // --- CONEXIÓN CON PDF INVOICES ---
        add_action( 'wpo_wcpdf_after_billing_address', [ $this, 'show_nif_in_pdf' ], 10, 2 );
    }

    /**
     * Encola el script JS específico para el checkout.
     * Carga condicional: Solo se ejecuta en la página is_checkout() para optimizar recursos.
     *
     * @return void
     */
    public function enqueue_checkout_script() {
        if ( ! is_checkout() ) return;

        // Calculamos la ruta relativa a la raíz del plugin
        $plugin_url = plugin_dir_url( dirname( dirname( __DIR__ ) ) . '/artesania-core.php' );
        $js_path    = 'assets/js/checkout.js';

        wp_enqueue_script(
            'artesania-checkout-js',
            $plugin_url . $js_path,
            ['jquery'], // Dependencia vital
            '2.3.1',
            true // Cargar en el footer
        );
    }

    /**
     * Añade campos personalizados al formulario de facturación.
     * - Checkbox: "¿Deseo factura?"
     * - Input: "NIF/DNI"
     *
     * @param array $fields Array de campos de WooCommerce.
     * @return array Campos modificados.
     */
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

    /**
     * Guarda los metadatos personalizados (NIF, Checkbox) en el pedido.
     * Utiliza sanitización estricta antes de guardar en base de datos.
     *
     * @param int   $order_id ID del pedido creado.
     * @param array $data     Datos del POST procesados.
     * @return void
     */
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