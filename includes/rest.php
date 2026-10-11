<?php
/**
 * REST endpoint used by the front-end widget.
 *
 * POST /wp-json/chibepoosham/v1/try-on
 *   product_id   int    (form field)
 *   image_id     int    (form field) attachment ID of the chosen garment image
 *   person_photo file   (JPG / PNG / WEBP, max 10 MB)
 *
 * The real try-on service is not connected yet. Until a plugin/backend
 * answers the `chibepoosham_try_on_request` filter, the endpoint replies in
 * demo mode so the whole front-end flow can be tested.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'rest_api_init',
	static function (): void {
		register_rest_route(
			'chibepoosham/v1',
			'/try-on',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'chibepoosham_rest_try_on',
				// Public on purpose: shoppers are usually logged out. A REST nonce is not
				// used because full-page caches would serve stale nonces to visitors.
				'permission_callback' => '__return_true',
			)
		);
	}
);

function chibepoosham_rest_try_on( WP_REST_Request $request ) {
	// Basic per-IP rate limit to protect the paid processing service.
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$key   = 'cbp_rl_' . md5( $ip );
	$count = (int) get_transient( $key );
	$limit = (int) apply_filters( 'chibepoosham_rate_limit', 10 ); // Requests per 10 minutes.

	if ( $count >= $limit ) {
		return new WP_Error( 'chibepoosham_rate_limited', 'تعداد درخواست‌ها زیاد بود. چند دقیقه دیگر دوباره امتحان کن.', array( 'status' => 429 ) );
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	// Product + garment image.
	$product_id = absint( $request->get_param( 'product_id' ) );
	$image_id   = absint( $request->get_param( 'image_id' ) );
	$settings   = chibepoosham_get_settings();

	if ( ! $product_id || 'publish' !== get_post_status( $product_id ) ) {
		return new WP_Error( 'chibepoosham_bad_product', 'محصول پیدا نشد.', array( 'status' => 404 ) );
	}

	$allowed = wp_list_pluck( chibepoosham_get_garment_images( $product_id, (int) $settings['max_images'] ), 'id' );
	if ( ! in_array( $image_id, $allowed, true ) ) {
		return new WP_Error( 'chibepoosham_bad_image', 'تصویر لباس نامعتبر است.', array( 'status' => 400 ) );
	}

	// Uploaded photo.
	$files = $request->get_file_params();
	$file  = $files['person_photo'] ?? null;

	if ( ! is_array( $file ) || ! isset( $file['error'], $file['tmp_name'], $file['size'] ) || UPLOAD_ERR_OK !== (int) $file['error'] ) {
		return new WP_Error( 'chibepoosham_no_file', 'عکس دریافت نشد. دوباره انتخابش کن.', array( 'status' => 400 ) );
	}

	if ( (int) $file['size'] > 10 * MB_IN_BYTES ) {
		return new WP_Error( 'chibepoosham_too_large', 'حجم عکس باید کمتر از ۱۰ مگابایت باشد.', array( 'status' => 413 ) );
	}

	$check = wp_check_filetype_and_ext(
		$file['tmp_name'],
		(string) ( $file['name'] ?? '' ),
		array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png'          => 'image/png',
			'webp'         => 'image/webp',
		)
	);

	if ( empty( $check['type'] ) || ! @getimagesize( $file['tmp_name'] ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
		return new WP_Error( 'chibepoosham_bad_type', 'فقط فایل JPG، PNG یا WEBP قابل قبول است.', array( 'status' => 415 ) );
	}

	/**
	 * Connect the try-on service here.
	 *
	 * Return one of:
	 *  - string       URL of the generated image
	 *  - array        array( 'image_url' => '...' )
	 *  - WP_Error     to report a failure to the shopper
	 *  - null         (default) demo mode
	 *
	 * @param mixed $result  Null by default.
	 * @param array $payload person_file (temp path), person_mime, garment_file (path), garment_url, product_id.
	 */
	$result = apply_filters(
		'chibepoosham_try_on_request',
		null,
		array(
			'person_file'  => $file['tmp_name'],
			'person_mime'  => $check['type'],
			'garment_file' => get_attached_file( $image_id ),
			'garment_url'  => wp_get_attachment_image_url( $image_id, 'large' ),
			'product_id'   => $product_id,
		)
	);

	if ( is_wp_error( $result ) ) {
		$result->add_data( array( 'status' => 502 ), $result->get_error_code() );
		return $result;
	}

	if ( is_array( $result ) && ! empty( $result['image_url'] ) ) {
		$result = $result['image_url'];
	}

	if ( is_string( $result ) && '' !== $result ) {
		return rest_ensure_response( array( 'image_url' => esc_url_raw( $result ) ) );
	}

	return rest_ensure_response( array( 'demo' => true ) );
}
