<?php
/**
 * Plugin Name:       Chi Bepoosham (چی بپوشم؟)
 * Description:       پرو مجازی لباس با افزونه «چی بپوشم؟» برای فروشگاه‌های ووکامرسی پوشاک
 * Version:           0.3.0
 * Author:            Chi_Bepoosham
 * Author URI:        https://chibepoosham.app/
 * Plugin URI:        https://github.com/ASG14/chibepooshim_plugin
 * Requires PHP:      8.0
 * Requires at least: 6.0
 * Text Domain:       chibepoosham
 */

defined( 'ABSPATH' ) || exit;

define( 'CHIBEPOOSHAM_VERSION', '0.3.0' );
define( 'CHIBEPOOSHAM_FILE', __FILE__ );
define( 'CHIBEPOOSHAM_PATH', plugin_dir_path( __FILE__ ) );
define( 'CHIBEPOOSHAM_URL', plugin_dir_url( __FILE__ ) );

// Data.
require_once CHIBEPOOSHAM_PATH . 'includes/settings.php';
require_once CHIBEPOOSHAM_PATH . 'includes/license.php';

// Storefront.
require_once CHIBEPOOSHAM_PATH . 'includes/frontend/gallery.php';
require_once CHIBEPOOSHAM_PATH . 'includes/frontend/widget.php';
require_once CHIBEPOOSHAM_PATH . 'includes/frontend/shortcode.php';

// API.
require_once CHIBEPOOSHAM_PATH . 'includes/rest.php';

// Admin panel.
if ( is_admin() ) {
	require_once CHIBEPOOSHAM_PATH . 'includes/admin/admin.php';
}
