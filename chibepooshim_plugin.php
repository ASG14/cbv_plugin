<?php
/**
 * Plugin Name:       Chi Bepoosham vision (چی بپوشم ویژن)
 * Description:       لباس را قبل از خرید، واقعاً روی تن ببین
 * Version:           0.3.0
 * Author:            Chi_Bepoosham
 * Author URI:        https://chibepoosham.app/vision
 * Plugin URI:        https://github.com/ASG14/cbv_plugin
 * Requires PHP:      8.0
 * Requires at least: 6.0
 * Text Domain:       chi bepoosham vision
 */

defined( 'ABSPATH' ) || exit;

define( 'CBV_VERSION', '0.3.0' );
define( 'CBV_FILE', __FILE__ );
define( 'CBV_PATH', plugin_dir_path( __FILE__ ) );
define( 'CBV_URL', plugin_dir_url( __FILE__ ) );

// Data.
require_once CBV_PATH . 'includes/settings.php';
require_once CBV_PATH . 'includes/license.php';

// Storefront.
require_once CBV_PATH . 'includes/frontend/gallery.php';
require_once CBV_PATH . 'includes/frontend/widget.php';
require_once CBV_PATH . 'includes/frontend/shortcode.php';

// API.
require_once CBV_PATH . 'includes/rest.php';

// Admin panel.
if ( is_admin() ) {
	require_once CBV_PATH . 'includes/admin/admin.php';
}
