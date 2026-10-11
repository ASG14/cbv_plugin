<?php
/**
 * Front-end: shortcode + widget markup (shared with the admin live preview).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product images the shopper can try on: main image first, then the gallery.
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

/**
 * Enqueue front-end assets (only when the widget is actually rendered).
 */
function chibepoosham_enqueue_front_assets(): void {
	wp_enqueue_style( 'chibepoosham-try-on', CHIBEPOOSHAM_URL . 'assets/css/try-on.css', array(), CHIBEPOOSHAM_VERSION );
	wp_enqueue_script( 'chibepoosham-try-on', CHIBEPOOSHAM_URL . 'assets/js/try-on.js', array(), CHIBEPOOSHAM_VERSION, true );

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
 * Widget markup.
 *
 * Structure: trigger button -> collapsible form (gallery row, drop zone,
 * file button, submit) -> result (replaces the form after a successful try-on).
 *
 * @param array $args images (required), product_id, preview (bool), settings (array|null).
 */
function chibepoosham_render_widget( array $args ): string {
	$args = wp_parse_args(
		$args,
		array(
			'images'     => array(),
			'product_id' => 0,
			'preview'    => false,
			'settings'   => null,
		)
	);

	$images = $args['images'];
	if ( ! $images ) {
		return '';
	}

	$s       = is_array( $args['settings'] ) ? $args['settings'] : chibepoosham_get_settings();
	$preview = (bool) $args['preview'];

	static $counter = 0;
	++$counter;
	$uid = $preview ? 'cbp-preview' : 'cbp-' . $counter;

	$classes = array( 'chibepoosham', 'chibepoosham--' . $s['button_style'] );
	if ( ! empty( $s['shadow'] ) ) {
		$classes[] = 'chibepoosham--shadow';
	}

	// Preview images are data: URIs, which esc_url() would strip.
	$src  = static fn( string $url ): string => $preview ? esc_attr( $url ) : esc_url( $url );
	$bind = static fn( string $key ): string => $preview ? ' data-cbp-bind="' . esc_attr( $key ) . '"' : '';

	$first = $images[0];

	ob_start();
	?>
	<section
		class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
		dir="rtl"
		style="<?php echo esc_attr( chibepoosham_css_vars( $s ) ); ?>"
		data-product-id="<?php echo esc_attr( (string) (int) $args['product_id'] ); ?>"
		<?php echo $preview ? 'data-preview="1"' : ''; ?>
	>
		<button
			class="chibepoosham__trigger"
			type="button"
			aria-expanded="<?php echo $preview ? 'true' : 'false'; ?>"
			aria-controls="<?php echo esc_attr( $uid ); ?>-form"
			<?php echo $bind( 'trigger_label' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		><?php echo esc_html( $s['trigger_label'] ); ?></button>

		<div class="chibepoosham__form" id="<?php echo esc_attr( $uid ); ?>-form" <?php echo $preview ? '' : 'hidden'; ?>>
			<header class="chibepoosham__header">
				<h2 class="chibepoosham__title" <?php echo empty( $s['show_title'] ) ? 'hidden' : ''; ?><?php echo $bind( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['title'] ); ?></h2>
				<p class="chibepoosham__description" <?php echo empty( $s['show_description'] ) ? 'hidden' : ''; ?><?php echo $bind( 'description' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['description'] ); ?></p>
			</header>

			<!-- 1. Gallery row -->
			<div class="chibepoosham__thumbs" role="radiogroup" aria-label="انتخاب تصویر لباس">
				<?php foreach ( $images as $i => $image ) : ?>
					<label class="chibepoosham__thumb">
						<input
							type="radio"
							name="<?php echo esc_attr( $uid ); ?>-garment"
							value="<?php echo esc_attr( (string) $image['id'] ); ?>"
							<?php checked( 0, $i ); ?>
						>
						<img src="<?php echo $src( $image['thumb'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy">
					</label>
				<?php endforeach; ?>
			</div>

			<!-- 2. Drop zone -->
			<input
				class="chibepoosham__file"
				id="<?php echo esc_attr( $uid ); ?>-photo"
				type="file"
				accept="image/jpeg,image/png,image/webp"
				<?php echo $preview ? 'disabled' : ''; ?>
			>
			<div class="chibepoosham__drop">
				<div class="chibepoosham__drop-intro">
					<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
					<p class="chibepoosham__drop-text"<?php echo $bind( 'drop_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['drop_text'] ); ?></p>
					<p class="chibepoosham__drop-hint"<?php echo $bind( 'drop_hint' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['drop_hint'] ); ?></p>
				</div>
				<img class="chibepoosham__drop-preview" alt="پیش‌نمایش عکس انتخاب‌شده" hidden>
			</div>

			<!-- 3. Choose-file button -->
			<label class="chibepoosham__btn chibepoosham__choose" for="<?php echo esc_attr( $uid ); ?>-photo"<?php echo $bind( 'upload_label' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['upload_label'] ); ?></label>

			<!-- Appears once a photo is chosen -->
			<button class="chibepoosham__submit" type="button" <?php echo $preview ? '' : 'hidden'; ?><?php echo $bind( 'submit_label' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['submit_label'] ); ?></button>

			<p class="chibepoosham__status" role="status" aria-live="polite"></p>
		</div>

		<!-- Result: replaces the form -->
		<div class="chibepoosham__result" hidden>
			<h3 class="chibepoosham__result-title"<?php echo $bind( 'result_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['result_title'] ); ?></h3>
			<img class="chibepoosham__result-image" alt="نتیجه پرو مجازی" <?php echo $preview ? 'src="' . $src( $first['url'] ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<p class="chibepoosham__note" hidden></p>
			<div class="chibepoosham__actions">
				<a class="chibepoosham__btn chibepoosham__download" href="#" download="chibepoosham-try-on.jpg">دانلود تصویر</a>
				<button class="chibepoosham__btn chibepoosham__again" type="button">امتحان مجدد</button>
			</div>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
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