<?php
/**
 * Block registration, assets and the data the block needs.
 *
 * @package RopstamServices
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the block assets, then the block that uses them.
 */
function ropstam_services_register_block() {
	wp_register_script(
		'ropstam-services-editor',
		ROPSTAM_SERVICES_URL . 'assets/js/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render' ),
		ROPSTAM_SERVICES_VERSION,
		true
	);

	wp_register_script(
		'ropstam-services-calculator',
		ROPSTAM_SERVICES_URL . 'assets/js/calculator.js',
		array(),
		ROPSTAM_SERVICES_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_register_style(
		'ropstam-services-calculator',
		ROPSTAM_SERVICES_URL . 'assets/css/calculator.css',
		array(),
		ROPSTAM_SERVICES_VERSION
	);

	register_block_type( ROPSTAM_SERVICES_DIR . 'blocks/service-calculator' );
}
add_action( 'init', 'ropstam_services_register_block' );

/**
 * Returns the three most recently updated services.
 *
 * @return WP_Post[]
 */
function ropstam_services_get_recent() {
	$query = new WP_Query(
		array(
			'post_type'              => 'service',
			'post_status'            => 'publish',
			'posts_per_page'         => 3,
			'orderby'                => 'modified',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);

	return $query->posts;
}

/**
 * Returns the optional add-ons shown on every service.
 *
 * @return array[] Each item has a label and a price.
 */
function ropstam_services_get_addons() {
	$addons = array(
		array(
			'label' => __( 'Priority Turnaround', 'ropstam-services' ),
			'price' => 50,
		),
		array(
			'label' => __( 'Extra Revision Round', 'ropstam-services' ),
			'price' => 25,
		),
		array(
			'label' => __( 'Source Files', 'ropstam-services' ),
			'price' => 30,
		),
	);

	return apply_filters( 'ropstam_services_addons', $addons );
}

/**
 * Formats a price for display. Keep in sync with formatPrice() in calculator.js.
 *
 * @param float $amount Price in dollars.
 * @return string
 */
function ropstam_services_format_price( $amount ) {
	return '$' . number_format( (float) $amount, 2, '.', ',' );
}

/**
 * Converts dollars to cents so the calculator only adds whole numbers.
 *
 * @param float $amount Price in dollars.
 * @return int
 */
function ropstam_services_to_cents( $amount ) {
	return (int) round( (float) $amount * 100 );
}
