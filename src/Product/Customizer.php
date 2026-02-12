<?php
declare(strict_types=1);

namespace Artesania\Core\Product;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Customizer
 *
 * Gestiona la personalización de productos (campo de texto "Escribe tu frase").
 * Funciona bajo un modelo de activación condicional por producto.
 * Implementa seguridad mediante Nonces para evitar ataques CSRF en la configuración.
 *
 * @package Artesania\Core\Product
 * @version 2.6.2
 */
class Customizer {

    public function __construct() {
        add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_custom_settings_field' ] );
        add_action( 'woocommerce_process_product_meta', [ $this, 'save_custom_settings_field' ] );

        add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'render_input_field' ] );
        add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_custom_data_to_cart' ], 10, 3 );
        add_filter( 'woocommerce_get_item_data', [ $this, 'display_custom_data_cart' ], 10, 2 );
        add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'save_custom_data_order' ], 10, 4 );
    }

    /**
     * BACKEND: Añade checkbox en la pestaña "General" del producto.
     * Incluye campo nonce de seguridad.
     */
    public function add_custom_settings_field(): void {
        echo '<div class="options_group">';

        woocommerce_wp_checkbox( [
            'id'          => '_artesania_allow_text',
            'label'       => __( 'Permitir Personalización', 'artesania-core' ),
            'description' => __( 'Activa esto para que el cliente pueda escribir una frase o nombre.', 'artesania-core' ),
            'desc_tip'    => true,
        ] );

        wp_nonce_field( 'artesania_save_customizer_data', 'artesania_customizer_nonce' );

        echo '</div>';
    }

    /**
     * BACKEND: Guarda la configuración del producto.
     * Verifica permisos y nonce antes de guardar.
     *
     * @param int $post_id ID del producto.
     */
    public function save_custom_settings_field( int $post_id ): void {
        if ( ! isset( $_POST['artesania_customizer_nonce'] ) ||
            ! wp_verify_nonce( $_POST['artesania_customizer_nonce'], 'artesania_save_customizer_data' )
        ) {
            \Artesania\Core\Main::log( "Fallo de seguridad (Nonce) al guardar personalización en producto ID: $post_id", 'ERROR' );
            return;
        }

        if ( ! current_user_can( 'edit_product', $post_id ) ) {
            return;
        }

        $checkbox_value = isset( $_POST['_artesania_allow_text'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_artesania_allow_text', $checkbox_value );
    }

    /**
     * FRONTEND: Renderiza el input en la ficha de producto.
     */
    public function render_input_field(): void {
        global $product;

        if ( ! $product ) return;

        $is_active = get_post_meta( $product->get_id(), '_artesania_allow_text', true );

        if ( 'yes' === $is_active ) {
            echo '<div class="artesania-custom-field-wrapper">';
            echo '<label for="artesania_custom_text" class="artesania-custom-label">';
            echo esc_html__( 'Escribe tu frase o nombre:', 'artesania-core' );
            echo '</label>';

            echo '<input type="text" id="artesania_custom_text" name="artesania_custom_text" class="input-text" placeholder="' . esc_attr__( 'Personalización...', 'artesania-core' ) . '" maxlength="50">';
            echo '</div>';
        }
    }

    /**
     * Procesa y añade el texto personalizado al carrito.
     */
    public function add_custom_data_to_cart( array $cart_item_data, int $product_id, int $variation_id ): array {
        if ( ! empty( $_POST['artesania_custom_text'] ) ) {
            $clean_text = sanitize_text_field( $_POST['artesania_custom_text'] );
            $cart_item_data['artesania_custom_text'] = $clean_text;
        }
        return $cart_item_data;
    }

    /**
     * Muestra el dato personalizado en carrito y checkout.
     */
    public function display_custom_data_cart( array $item_data, array $cart_item ): array {
        if ( isset( $cart_item['artesania_custom_text'] ) ) {
            $item_data[] = [
                'key'   => esc_html__( 'Personalización', 'artesania-core' ),
                'value' => esc_html( $cart_item['artesania_custom_text'] ),
            ];
        }
        return $item_data;
    }

    /**
     * Guarda el dato como meta en la línea del pedido.
     */
    public function save_custom_data_order( \WC_Order_Item_Product $item, string $cart_item_key, array $values, \WC_Order $order ): void {
        if ( isset( $values['artesania_custom_text'] ) ) {
            $item->add_meta_data(
                esc_html__( 'Personalización', 'artesania-core' ),
                $values['artesania_custom_text']
            );
        }
    }
}