<?php
/**
 * Meta box for entering the price and delivery time.
 *
 * @package RopstamServices
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds the meta box to the service edit screen.
 */
function ropstam_services_add_meta_box() {
	add_meta_box(
		'ropstam-service-details',
		__( 'Service Details', 'ropstam-services' ),
		'ropstam_services_render_meta_box',
		'service',
		'side'
	);
}
add_action( 'add_meta_boxes_service', 'ropstam_services_add_meta_box' );

/**
 * Prints the meta box fields.
 *
 * @param WP_Post $post Current service.
 */
function ropstam_services_render_meta_box( $post ) {
	wp_nonce_field( 'ropstam_save_service', 'ropstam_service_nonce' );
	?>
	<p>
		<label for="ropstam-base-price"><?php esc_html_e( 'Base price ($)', 'ropstam-services' ); ?></label>
		<input type="number" id="ropstam-base-price" name="base_price" class="widefat" min="0" step="0.01"
			value="<?php echo esc_attr( get_post_meta( $post->ID, 'base_price', true ) ); ?>">
	</p>
	<p>
		<label for="ropstam-delivery-days"><?php esc_html_e( 'Delivery time (days)', 'ropstam-services' ); ?></label>
		<input type="number" id="ropstam-delivery-days" name="delivery_time_days" class="widefat" min="0" step="1"
			value="<?php echo esc_attr( get_post_meta( $post->ID, 'delivery_time_days', true ) ); ?>">
	</p>
	<?php
}

/**
 * Saves the meta box fields.
 *
 * @param int $post_id Service ID.
 */
function ropstam_services_save_meta_box( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$nonce = isset( $_POST['ropstam_service_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['ropstam_service_nonce'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'ropstam_save_service' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array( 'base_price', 'delivery_time_days' ) as $key ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';

		// An empty field means "not set", which is different from a price of 0.
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post_service', 'ropstam_services_save_meta_box' );
