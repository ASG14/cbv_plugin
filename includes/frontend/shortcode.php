<?php
/**
 * Shortcode + front-end assets.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue front-end assets (only when the widget is actually rendered).
 */
function cbv_enqueue_front_assets(): void {
	wp_enqueue_style( 'cbv', CBV_URL . 'assets/css/cbv.css', array(), CBV_VERSION );
	wp_enqueue_script( 'cbv-try-on', CBV_URL . 'assets/js/front/try-on.js', array(), CBV_VERSION, true );

	$s = cbv_get_settings();

	wp_localize_script(
		'cbv-try-on',
		'cbvTryOn',
		array(
			'endpoint' => esc_url_raw( rest_url( 'cbv/v1/try-on' ) ),
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
 * Shortcode: [cbv_try_on] or [cbv_try_on id="123"]
 */
function cbv_shortcode( $atts ): string {
	$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'cbv_try_on' );

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

	$settings = cbv_get_settings();
	$images   = $product_id ? cbv_get_garment_images( $product_id, (int) $settings['max_images'] ) : array();

	if ( ! $images ) {
		// Only admins see why nothing is rendered.
		return current_user_can( 'manage_options' )
			? '<p class="cbv__notice">شورت‌کد «چی بپوشم؟» تصویری برای این صفحه پیدا نکرد. آن را در صفحه‌ی یک محصول که تصویر دارد قرار بدهید. (این پیام فقط برای مدیر سایت نمایش داده می‌شود.)</p>'
			: '';
	}

	cbv_enqueue_front_assets();

	return cbv_render_widget(
		array(
			'images'     => $images,
			'product_id' => $product_id,
			'settings'   => $settings,
		)
	);
}
add_shortcode( 'cbv_try_on', 'cbv_shortcode' );
