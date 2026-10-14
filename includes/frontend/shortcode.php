<?php
/**
 * Shortcode + front-end assets.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue front-end assets (only when the widget is actually rendered).
 */
function chibepoosham_enqueue_front_assets(): void {
	wp_enqueue_style( 'chibepoosham', CHIBEPOOSHAM_URL . 'assets/css/chibepoosham.css', array(), CHIBEPOOSHAM_VERSION );
	wp_enqueue_script( 'chibepoosham-try-on', CHIBEPOOSHAM_URL . 'assets/js/front/try-on.js', array(), CHIBEPOOSHAM_VERSION, true );

	$s = chibepoosham_get_settings();

	wp_localize_script(
		'chibepoosham-try-on',
		'ChibepooshamTryOn',
		array(
			'endpoint' => esc_url_raw( rest_url( 'chibepoosham/v1/try-on' ) ),
			'maxBytes' => 10 * MB_IN_BYTES,
			'texts'    => array(
				'loading'     => $s['loading_text'],
				'error'       => $s['error_text'],
				'change'      => 'تغییر فایل',
				'invalidType' => 'فقط فایل JPG، PNG یا WEBP قابل قبول است.',
				'tooLarge'    => 'حجم عکس باید کمتر از ۱۰ مگابایت باشد.',
				'demo'        => 'نمایش آزمایشی: اتصال به سرویس پردازش هنوز فعال نشده است.',
			),
		)
	);
}

/**
 * Shortcode: [chibepoosham_try_on] or [chibepoosham_try_on id="123"]
 */
function chibepoosham_shortcode( $atts ): string {
	$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'chibepoosham_try_on' );

	$product_id = absint( $atts['id'] );

	if ( ! $product_id ) {
		global $product;
		if ( $product instanceof WC_Product ) {
			$product_id = $product->get_id();
		}
	}

	if ( ! $product_id ) {
		$product_id = (int) get_queried_object_id();
	}

	$settings = chibepoosham_get_settings();
	$images   = $product_id ? chibepoosham_get_garment_images( $product_id, (int) $settings['max_images'] ) : array();

	if ( ! $images ) {
		// Only admins see why nothing is rendered.
		return current_user_can( 'manage_options' )
			? '<p class="chibepoosham__notice">شورت‌کد «چی بپوشم؟» تصویری برای این صفحه پیدا نکرد. آن را در صفحه‌ی یک محصول که تصویر دارد قرار بدهید. (این پیام فقط برای مدیر سایت نمایش داده می‌شود.)</p>'
			: '';
	}

	chibepoosham_enqueue_front_assets();

	return chibepoosham_render_widget(
		array(
			'images'     => $images,
			'product_id' => $product_id,
			'settings'   => $settings,
		)
	);
}
add_shortcode( 'chibepoosham_try_on', 'chibepoosham_shortcode' );
