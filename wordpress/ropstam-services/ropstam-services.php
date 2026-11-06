<?php
/**
 * Plugin Name: Ropstam Services
 * Description: Services post type with a block that lists the latest services and a live price calculator.
 * Version:     1.0.1
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * License:     GPL-2.0-or-later
 * Text Domain: ropstam-services
 *
 * @package RopstamServices
 */

defined( 'ABSPATH' ) || exit;

define( 'ROPSTAM_SERVICES_VERSION', '1.0.1' );
define( 'ROPSTAM_SERVICES_FILE', __FILE__ );
define( 'ROPSTAM_SERVICES_DIR', plugin_dir_path( __FILE__ ) );
define( 'ROPSTAM_SERVICES_URL', plugin_dir_url( __FILE__ ) );

require_once ROPSTAM_SERVICES_DIR . 'includes/post-type.php';
require_once ROPSTAM_SERVICES_DIR . 'includes/meta-box.php';
require_once ROPSTAM_SERVICES_DIR . 'includes/block.php';

/**
 * Registers the post type before flushing so the /services/ URLs exist straight away.
 */
function ropstam_services_activate() {
	ropstam_services_register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ropstam_services_activate' );

/**
 * Removes the /services/ URLs when the plugin is turned off.
 */
function ropstam_services_deactivate() {
	unregister_post_type( 'service' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ropstam_services_deactivate' );
