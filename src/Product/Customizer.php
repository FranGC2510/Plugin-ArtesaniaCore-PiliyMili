<?php
namespace Artesania\Core\Product;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Customizer
 *
 * Gestiona la personalización de productos (campo de texto "Escribe tu frase").
 * Funciona bajo un modelo de activación condicional por producto (Backend -> Frontend).
 * Maneja el flujo completo: Configuración -> Input Cliente -> Carrito -> Pedido.
 *
 * @package Artesania\Core\Product
 */
class Customizer {

    public function __construct() {
            // --- BACKEND (Panel de Control) ---
            // 1. Añadir checkbox en la configuración del producto
            add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_custom_settings_field' ] );
            // 2. Guardar la configuración del checkbox
            add_action( 'woocommerce_process_product_meta', [ $this, 'save_custom_settings_field' ] );

            // --- FRONTEND (Tienda) ---
            // 3. Mostrar el campo SOLO si está activado
            add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'render_input_field' ] );

            // 4. Lógica de Carrito y Pedido (Igual que antes)
            add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_custom_data_to_cart' ], 10, 3 );
            add_filter( 'woocommerce_get_item_data', [ $this, 'display_custom_data_cart' ], 10, 2 );
            add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'save_custom_data_order' ], 10, 4 );
        }

    /**
     * BACKEND: Añade un checkbox de configuración en la pestaña "General" del producto.
     * Permite activar/desactivar la personalización por producto.
     *
     * @return void
     */
    public function add_custom_settings_field() {
        echo '<div class="options_group">';

        woocommerce_wp_checkbox( array(
            'id'            => '_artesania_allow_text',
            'label'         => __( 'Permitir Personalización', 'artesania-core' ),
            'description'   => __( 'Activa esto para que el cliente pueda escribir una frase o nombre.', 'artesania-core' ),
            'desc_tip'      => true,
            ) );

        echo '</div>';
    }

    /**
     * BACKEND: Guarda el estado de la configuración de personalización.
     *
     * @param int $post_id ID del producto.
     * @return void
     */
     public function save_custom_settings_field( $post_id ) {
        $checkbox_value = isset( $_POST['_artesania_allow_text'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_artesania_allow_text', $checkbox_value );
     }

    /**
     * FRONTEND: Renderiza el campo de texto en la ficha de producto.
     * Verifica si la opción está activa antes de mostrar el HTML.
     *
     * @return void
     */
     public function render_input_field() {
        global $product;

        // VERIFICACIÓN: ¿Tiene este producto activada la opción?
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
     * Procesa y añade el texto personalizado al objeto del carrito.
     * Aplica sanitización (sanitize_text_field) para seguridad.
     *
     * @param array $cart_item_data Datos actuales del item en carrito.
     * @param int   $product_id     ID del producto.
     * @param int   $variation_id   ID de la variación.
     * @return array Datos modificados.
     */
     public function add_custom_data_to_cart( $cart_item_data, $product_id, $variation_id ) {
        if ( ! empty( $_POST['artesania_custom_text'] ) ) {
            $clean_text = sanitize_text_field( $_POST['artesania_custom_text'] );
            $cart_item_data['artesania_custom_text'] = $clean_text;
        }
        return $cart_item_data;
     }

    /**
     * Muestra el dato personalizado en la tabla del carrito y checkout.
     *
     * @param array $item_data Array de datos clave/valor a mostrar.
     * @param array $cart_item Datos del item del carrito.
     * @return array Datos modificados.
     */
     public function display_custom_data_cart( $item_data, $cart_item ) {
        if ( isset( $cart_item['artesania_custom_text'] ) ) {
            $item_data[] = array(
                'key'   => esc_html__( 'Personalización', 'artesania-core' ),
                'value' => esc_html( $cart_item['artesania_custom_text'] ),
            );
        }
        return $item_data;
     }

    /**
     * Guarda el dato personalizado como meta-data en la línea del pedido final.
     * Esto asegura que la información persista en el historial y emails.
     *
     * @param \WC_Order_Item_Product $item          Objeto del item del pedido.
     * @param string                 $cart_item_key Key del item en carrito.
     * @param array                  $values        Valores del item.
     * @param \WC_Order              $order         Objeto del pedido.
     * @return void
     */
     public function save_custom_data_order( $item, $cart_item_key, $values, $order ) {
        if ( isset( $values['artesania_custom_text'] ) ) {
            $item->add_meta_data(
                esc_html__( 'Personalización', 'artesania-core' ),
                $values['artesania_custom_text']
            );
        }
     }
}