<?php
/**
 * Service post type and its meta fields.
 *
 * @package RopstamServices
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the service post type.
 */
function ropstam_services_register_post_type() {
	register_post_type(
		'service',
		array(
			'labels'       => array(
				'name'          => __( 'Services', 'ropstam-services' ),
				'singular_name' => __( 'Service', 'ropstam-services' ),
				'add_new_item'  => __( 'Add New Service', 'ropstam-services' ),
				'edit_item'     => __( 'Edit Service', 'ropstam-services' ),
				'search_items'  => __( 'Search Services', 'ropstam-services' ),
				'not_found'     => __( 'No services found.', 'ropstam-services' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-hammer',
			'rewrite'      => array( 'slug' => 'services' ),
			// custom-fields is needed for the meta to appear in the REST API.
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'ropstam_services_register_post_type' );

/**
 * Cleans a price before it is saved.
 *
 * @param mixed $value Submitted value.
 * @return float
 */
function ropstam_services_sanitize_price( $value ) {
	return max( 0, round( (float) $value, 2 ) );
}

/**
 * Registers base_price and delivery_time_days.
 */
function ropstam_services_register_meta() {
	$can_edit = static function ( $allowed, $meta_key, $post_id ) {
		return current_user_can( 'edit_post', $post_id );
	};

	register_post_meta(
		'service',
		'base_price',
		array(
			'type'              => 'number',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'ropstam_services_sanitize_price',
			'auth_callback'     => $can_edit,
		)
	);

	register_post_meta(
		'service',
		'delivery_time_days',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => $can_edit,
		)
	);
}
add_action( 'init', 'ropstam_services_register_meta' );
