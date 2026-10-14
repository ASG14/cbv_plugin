<?php
/**
 * License data: storage and sanitizing.
 *
 * Verification against the Chi Bepoosham server is not connected yet;
 * for now the key is only stored (status "pending").
 */

defined( 'ABSPATH' ) || exit;

const CHIBEPOOSHAM_LICENSE_OPTION = 'chibepoosham_license';

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
	 * Hook for the license backend: verify the key and return the real status.
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
