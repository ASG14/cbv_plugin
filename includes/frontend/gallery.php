<?php
/**
 * Product images the shopper can try on (also used to validate REST requests).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main image first, then the WooCommerce gallery.
 *
 * @return array<int, array{id:int, url:string, thumb:string, alt:string}>
 */
function chibepoosham_get_garment_images( int $product_id, int $max = 6 ): array {
	$ids = array();

	if ( function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			$main = (int) $product->get_image_id();
			if ( $main ) {
				$ids[] = $main;
			}
			$ids = array_merge( $ids, array_map( 'intval', $product->get_gallery_image_ids() ) );
		}
	}

	if ( ! $ids && $product_id && has_post_thumbnail( $product_id ) ) {
		$ids[] = (int) get_post_thumbnail_id( $product_id );
	}

	$ids    = array_slice( array_values( array_unique( array_filter( $ids ) ) ), 0, max( 1, $max ) );
	$images = array();

	foreach ( $ids as $id ) {
		$url = wp_get_attachment_image_url( $id, 'large' );
		if ( ! $url ) {
			continue;
		}
		$alt      = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		$images[] = array(
			'id'    => $id,
			'url'   => $url,
			'thumb' => wp_get_attachment_image_url( $id, 'thumbnail' ) ?: $url,
			'alt'   => '' !== $alt ? $alt : get_the_title( $product_id ),
		);
	}

	return $images;
}

/**
 * Inline SVG placeholders used by the admin live preview.
 */
function chibepoosham_placeholder_images(): array {
	$colors = array( '#7a2e4d', '#2f5d62', '#b98a1f' );
	$images = array();

	foreach ( $colors as $i => $color ) {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400"><rect width="300" height="400" fill="#efeaed"/>'
			. '<path d="M105 70 60 92 28 150 70 172 92 148V330H208V148L230 172 272 150 240 92 195 70C185 100 165 108 150 108S115 100 105 70Z" fill="' . $color . '"/></svg>';
		$uri      = 'data:image/svg+xml;charset=utf-8,' . rawurlencode( $svg );

		$images[] = array(
			'id'    => $i + 1,
			'url'   => $uri,
			'thumb' => $uri,
			'alt'   => 'نمونه محصول',
		);
	}

	return $images;
}
