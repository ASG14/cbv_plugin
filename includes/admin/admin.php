<?php
/**
 * Admin shell: menu, tab navigation, per-tab dispatch.
 * Each tab lives in its own file and registers its own settings and assets.
 */

defined( 'ABSPATH' ) || exit;

const CHIBEPOOSHAM_PAGE_SLUG = 'chibepoosham';

require_once __DIR__ . '/fields.php';
require_once __DIR__ . '/tab-license.php';
require_once __DIR__ . '/tab-style.php';
require_once __DIR__ . '/tab-usage.php';

add_action( 'admin_menu', 'chibepoosham_admin_menu' );
add_action( 'admin_enqueue_scripts', 'chibepoosham_admin_assets' );
add_filter( 'plugin_action_links_' . plugin_basename( CHIBEPOOSHAM_FILE ), 'chibepoosham_action_links' );

/**
 * Tab registry. To add a tab: create tab-xxx.php and add a row here.
 * 'assets' is optional and runs only when that tab is open.
 */
function chibepoosham_admin_tabs(): array {
	return array(
		'license' => array(
			'label'  => 'لایسنس',
			'render' => 'chibepoosham_tab_license',
		),
		'style'   => array(
			'label'  => 'ظاهر و متن‌ها',
			'render' => 'chibepoosham_tab_style',
			'assets' => 'chibepoosham_tab_style_assets',
		),
		'usage'   => array(
			'label'  => 'شورت‌کد و راهنما',
			'render' => 'chibepoosham_tab_usage',
			'assets' => 'chibepoosham_tab_usage_assets',
		),
	);
}

function chibepoosham_current_tab(): string {
	$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'license'; // phpcs:ignore WordPress.Security.NonceVerification
	return isset( chibepoosham_admin_tabs()[ $tab ] ) ? $tab : 'license';
}

function chibepoosham_admin_menu(): void {
	add_menu_page(
		'چی بپوشم؟',
		'چی بپوشم؟',
		'manage_options',
		CHIBEPOOSHAM_PAGE_SLUG,
		'chibepoosham_admin_page',
		'dashicons-visibility',
		58
	);
}

function chibepoosham_action_links( array $links ): array {
	$url = admin_url( 'admin.php?page=' . CHIBEPOOSHAM_PAGE_SLUG );
	array_unshift( $links, '<a href="' . esc_url( $url ) . '">تنظیمات</a>' );
	return $links;
}

function chibepoosham_admin_assets( string $hook ): void {
	if ( 'toplevel_page_' . CHIBEPOOSHAM_PAGE_SLUG !== $hook ) {
		return;
	}

	// One stylesheet for everything: widget (also used by the live preview) + admin.
	wp_enqueue_style( 'chibepoosham', CHIBEPOOSHAM_URL . 'assets/css/chibepoosham.css', array(), CHIBEPOOSHAM_VERSION );

	$tab = chibepoosham_admin_tabs()[ chibepoosham_current_tab() ];
	if ( ! empty( $tab['assets'] ) && is_callable( $tab['assets'] ) ) {
		call_user_func( $tab['assets'] );
	}
}

function chibepoosham_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tabs    = chibepoosham_admin_tabs();
	$current = chibepoosham_current_tab();
	?>
	<div class="wrap cbp-admin" dir="rtl">
		<h1 class="cbp-admin__title">چی بپوشم؟ <span class="cbp-admin__version">نسخه <?php echo esc_html( CHIBEPOOSHAM_VERSION ); ?></span></h1>

		<nav class="nav-tab-wrapper">
			<?php foreach ( $tabs as $slug => $tab ) : ?>
				<a class="nav-tab <?php echo $slug === $current ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . CHIBEPOOSHAM_PAGE_SLUG . '&tab=' . $slug ) ); ?>"><?php echo esc_html( $tab['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<?php settings_errors(); ?>

		<div class="cbp-admin__body">
			<?php call_user_func( $tabs[ $current ]['render'] ); ?>
		</div>
	</div>
	<?php
}
