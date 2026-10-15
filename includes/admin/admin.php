<?php
/**
 * Admin shell: menu, tab navigation, per-tab dispatch.
 * Each tab lives in its own file and registers its own settings and assets.
 */

defined( 'ABSPATH' ) || exit;

const CBV_PAGE_SLUG = 'cbv';

require_once __DIR__ . '/fields.php';
require_once __DIR__ . '/tab-consumption.php';
require_once __DIR__ . '/tab-license.php';
require_once __DIR__ . '/tab-style.php';
require_once __DIR__ . '/tab-usage.php';

add_action( 'admin_menu', 'cbv_admin_menu' );
add_action( 'admin_enqueue_scripts', 'cbv_admin_assets' );
add_filter( 'plugin_action_links_' . plugin_basename( CBV_FILE ), 'cbv_action_links' );

/**
 * Tab registry. To add a tab: create tab-xxx.php and add a row here.
 * 'assets' is optional and runs only when that tab is open.
 */
function cbv_admin_tabs(): array {
	return array(
		'consumption' => array(
			'label'  => 'پلن و مصرف',
			'render' => 'cbv_tab_consumption',
		),
		'style'   => array(
			'label'  => 'ظاهر و متن‌ها',
			'render' => 'cbv_tab_style',
			'assets' => 'cbv_tab_style_assets',
		),
		'usage'   => array(
			'label'  => 'شورت‌کد و راهنما',
			'render' => 'cbv_tab_usage',
			'assets' => 'cbv_tab_usage_assets',
		),
		'license' => array(
			'label'  => 'لایسنس',
			'render' => 'cbv_tab_license',
		),
	);
}

function cbv_current_tab(): string {
	$tab = isset( $_GET['tab'] )
		? sanitize_key( wp_unslash( $_GET['tab'] ) )
		: 'consumption'; // phpcs:ignore WordPress.Security.NonceVerification

	return isset( cbv_admin_tabs()[ $tab ] )
		? $tab
		: 'consumption';
}

function cbv_admin_menu(): void {
	add_menu_page(
		'چی بپوشم؟',
		'چی بپوشم؟',
		'manage_options',
		CBV_PAGE_SLUG,
		'cbv_admin_page',
		'dashicons-visibility',
		58
	);
}

function cbv_action_links( array $links ): array {
	$url = admin_url( 'admin.php?page=' . CBV_PAGE_SLUG );
	array_unshift( $links, '<a href="' . esc_url( $url ) . '">تنظیمات</a>' );
	return $links;
}

function cbv_admin_assets( string $hook ): void {
	if ( 'toplevel_page_' . CBV_PAGE_SLUG !== $hook ) {
		return;
	}

	// One stylesheet for everything: widget (also used by the live preview) + admin.
	wp_enqueue_style( 'cbv', CBV_URL . 'assets/css/cbv.css', array(), CBV_VERSION );

	$tab = cbv_admin_tabs()[ cbv_current_tab() ];
	if ( ! empty( $tab['assets'] ) && is_callable( $tab['assets'] ) ) {
		call_user_func( $tab['assets'] );
	}
}

function cbv_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tabs    = cbv_admin_tabs();
	$current = cbv_current_tab();
	?>
	<div class="wrap cbv-admin" dir="rtl">
		<h1 class="cbv-admin__title">چی بپوشم؟ <span class="cbv-admin__version">نسخه <?php echo esc_html( CBV_VERSION ); ?></span></h1>

		<nav class="nav-tab-wrapper">
			<?php foreach ( $tabs as $slug => $tab ) : ?>
				<a class="nav-tab <?php echo $slug === $current ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . CBV_PAGE_SLUG . '&tab=' . $slug ) ); ?>"><?php echo esc_html( $tab['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<?php settings_errors(); ?>

		<div class="cbv-admin__body">
			<?php call_user_func( $tabs[ $current ]['render'] ); ?>
		</div>
	</div>
	<?php
}
