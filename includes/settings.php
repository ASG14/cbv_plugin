<?php
/**
 * Settings: defaults, getters, sanitizers.
 */

defined( 'ABSPATH' ) || exit;

const CHIBEPOOSHAM_OPTION         = 'chibepoosham_settings';
const CHIBEPOOSHAM_LICENSE_OPTION = 'chibepoosham_license';

/**
 * Default values for every setting.
 */
function chibepoosham_defaults(): array {
	return array(
		// Texts.
		'trigger_label'    => 'پرو آنلاین لباس',
		'title'            => 'لباس رو تن خودت ببین!',
		'description'      => 'عکست رو انتخاب کن و ظاهر لباس رو روی خودت تصور کن.',
		'drop_text'        => 'عکس خودت رو اینجا رها کن',
		'drop_hint'        => 'برای پرو لباس باید عکس خودت رو آپلود کنی. (JPG، PNG یا WEBP، حداکثر ۱۰ مگابایت)',
		'upload_label'     => 'انتخاب فایل',
		'submit_label'     => 'پرو مجازی لباس',
		'result_title'     => 'نتیجه پرو مجازی',
		'loading_text'     => 'در حال آماده‌سازی تصویر… چند لحظه صبر کن.',
		'error_text'       => 'مشکلی پیش آمد. دوباره امتحان کن.',

		// Colors.
		'primary'          => '#7a2e4d',
		'primary_text'     => '#ffffff',
		'background'       => '#ffffff',
		'surface'          => '#f8f5f7',
		'text'             => '#231f20',
		'border'           => '#e4dce1',

		// Shape.
		'radius'           => 14,
		'font'             => 'inherit',
		'button_style'     => 'solid',  // solid | outline
		'shadow'           => 0,
		'show_title'       => 0,
		'show_description' => 0,
		'max_images'       => 6,
	);
}

function chibepoosham_text_keys(): array {
	return array( 'trigger_label', 'title', 'description', 'drop_text', 'drop_hint', 'upload_label', 'submit_label', 'result_title', 'loading_text', 'error_text' );
}

function chibepoosham_color_keys(): array {
	return array( 'primary', 'primary_text', 'background', 'surface', 'text', 'border' );
}

/**
 * Saved settings merged over the defaults.
 */
function chibepoosham_get_settings(): array {
	$saved = get_option( CHIBEPOOSHAM_OPTION, array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), chibepoosham_defaults() );
}

/**
 * Sanitize the style/text settings form.
 * The callback receives already-unslashed data from options.php.
 */
function chibepoosham_sanitize_settings( $input ): array {
	$d     = chibepoosham_defaults();
	$input = is_array( $input ) ? $input : array();
	$out   = array();

	foreach ( chibepoosham_text_keys() as $key ) {
		$value       = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : '';
		$out[ $key ] = '' !== $value ? $value : $d[ $key ];
	}

	foreach ( chibepoosham_color_keys() as $key ) {
		$color       = isset( $input[ $key ] ) ? sanitize_hex_color( $input[ $key ] ) : null;
		$out[ $key ] = $color ? $color : $d[ $key ];
	}

	$out['radius'] = isset( $input['radius'] ) ? min( 32, max( 0, absint( $input['radius'] ) ) ) : $d['radius'];

	$font        = isset( $input['font'] ) ? preg_replace( '/[^\p{L}\p{N}\s,"\'\-_]/u', '', (string) $input['font'] ) : '';
	$font        = trim( (string) $font );
	$out['font'] = '' !== $font ? $font : 'inherit';

	$out['button_style'] = ( isset( $input['button_style'] ) && in_array( $input['button_style'], array( 'solid', 'outline' ), true ) ) ? $input['button_style'] : $d['button_style'];

	// Checkboxes: unchecked boxes are absent from the request.
	foreach ( array( 'shadow', 'show_title', 'show_description' ) as $key ) {
		$out[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
	}

	$out['max_images'] = isset( $input['max_images'] ) ? min( 10, max( 1, absint( $input['max_images'] ) ) ) : $d['max_images'];

	return $out;
}

/**
 * CSS custom properties for the widget wrapper (inline style value).
 */
function chibepoosham_css_vars( array $s ): string {
	$vars = array(
		'--cbp-primary'      => $s['primary'],
		'--cbp-primary-text' => $s['primary_text'],
		'--cbp-bg'           => $s['background'],
		'--cbp-surface'      => $s['surface'],
		'--cbp-text'         => $s['text'],
		'--cbp-border'       => $s['border'],
		'--cbp-radius'       => (int) $s['radius'] . 'px',
		'--cbp-font'         => $s['font'],
	);

	$css = '';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . $value . ';';
	}
	return $css;
}

/* -------------------------------------------------------------------------
 * License (UI only for now — verification will be added with the backend)
 * ---------------------------------------------------------------------- */

function chibepoosham_get_license(): array {
	$saved = get_option( CHIBEPOOSHAM_LICENSE_OPTION, array() );
	return wp_parse_args(
		is_array( $saved ) ? $saved : array(),
		array(
			'key'    => '',
			'status' => 'inactive', // inactive | pending | active
		)
	);
}

function chibepoosham_sanitize_license( $input ): array {
	$old = chibepoosham_get_license();
	$key = isset( $input['key'] ) ? preg_replace( '/[^A-Za-z0-9\-_]/', '', (string) $input['key'] ) : '';

	if ( '' === $key ) {
		$status = 'inactive';
	} elseif ( $key === $old['key'] && 'active' === $old['status'] ) {
		$status = 'active';
	} else {
		$status = 'pending';
	}

	/**
	 * Hook for the future license backend: verify the key and return the real status.
	 *
	 * @param string $status Status computed locally.
	 * @param string $key    Sanitized license key.
	 */
	$status = apply_filters( 'chibepoosham_license_status_on_save', $status, $key );

	return array(
		'key'    => $key,
		'status' => $status,
	);
}