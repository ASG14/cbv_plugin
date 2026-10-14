<?php
/**
 * Widget markup (shared by the shortcode and the admin live preview).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Structure: trigger button -> collapsible form (gallery row, drop zone,
 * file button, submit) -> result (replaces the form after a successful try-on).
 *
 * @param array $args images (required), product_id, preview (bool), settings (array|null).
 */
function cbv_render_widget( array $args ): string {
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

	$s       = is_array( $args['settings'] ) ? $args['settings'] : cbv_get_settings();
	$preview = (bool) $args['preview'];

	static $counter = 0;
	++$counter;
	$uid = $preview ? 'cbv-preview' : 'cbv-' . $counter;

	$classes = array( 'cbv', 'cbv--' . $s['button_style'] );
	if ( ! empty( $s['shadow'] ) ) {
		$classes[] = 'cbv--shadow';
	}

	// Preview images are data: URIs, which esc_url() would strip.
	$src  = static fn( string $url ): string => $preview ? esc_attr( $url ) : esc_url( $url );
	$bind = static fn( string $key ): string => $preview ? ' data-cbv-bind="' . esc_attr( $key ) . '"' : '';

	$first = $images[0];

	ob_start();
	?>
	<section
		class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
		dir="rtl"
		style="<?php echo esc_attr( cbv_css_vars( $s ) ); ?>"
		data-product-id="<?php echo esc_attr( (string) (int) $args['product_id'] ); ?>"
		<?php echo $preview ? 'data-preview="1"' : ''; ?>
	>
		<button
			class="cbv__trigger"
			type="button"
			aria-expanded="<?php echo $preview ? 'true' : 'false'; ?>"
			aria-controls="<?php echo esc_attr( $uid ); ?>-form"
			<?php echo $bind( 'trigger_label' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		><?php echo esc_html( $s['trigger_label'] ); ?></button>

		<div class="cbv__form" id="<?php echo esc_attr( $uid ); ?>-form" <?php echo $preview ? '' : 'hidden'; ?>>
			<header class="cbv__header">
				<h2 class="cbv__title" <?php echo empty( $s['show_title'] ) ? 'hidden' : ''; ?><?php echo $bind( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['title'] ); ?></h2>
				<p class="cbv__description" <?php echo empty( $s['show_description'] ) ? 'hidden' : ''; ?><?php echo $bind( 'description' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['description'] ); ?></p>
			</header>

			<!-- 1. Gallery row -->
			<div class="cbv__thumbs" role="radiogroup" aria-label="انتخاب تصویر لباس">
				<?php foreach ( $images as $i => $image ) : ?>
					<label class="cbv__thumb">
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
				class="cbv__file"
				id="<?php echo esc_attr( $uid ); ?>-photo"
				type="file"
				accept="image/jpeg,image/png,image/webp"
				<?php echo $preview ? 'disabled' : ''; ?>
			>
			<div class="cbv__drop">
				<div class="cbv__drop-intro">
					<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
					<p class="cbv__drop-text"<?php echo $bind( 'drop_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['drop_text'] ); ?></p>
					<p class="cbv__drop-hint"<?php echo $bind( 'drop_hint' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['drop_hint'] ); ?></p>
				</div>
				<img class="cbv__drop-preview" alt="پیش‌نمایش عکس انتخاب‌شده" hidden>
			</div>

			<!-- 3. Choose-file button -->
			<label class="cbv__btn cbv__choose" for="<?php echo esc_attr( $uid ); ?>-photo"<?php echo $bind( 'upload_label' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['upload_label'] ); ?></label>

			<!-- Appears once a photo is chosen -->
			<button class="cbv__submit" type="button" <?php echo $preview ? '' : 'hidden'; ?><?php echo $bind( 'submit_label' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['submit_label'] ); ?></button>

			<p class="cbv__status" role="status" aria-live="polite"></p>
		</div>

		<!-- Result: replaces the form -->
		<div class="cbv__result" hidden>
			<h3 class="cbv__result-title"<?php echo $bind( 'result_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['result_title'] ); ?></h3>
			<img class="cbv__result-image" alt="نتیجه پرو مجازی" <?php echo $preview ? 'src="' . $src( $first['url'] ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<p class="cbv__note" hidden></p>
			<div class="cbv__actions">
				<a class="cbv__btn cbv__download" href="#" download="cbv-try-on.jpg">دانلود تصویر</a>
				<button class="cbv__btn cbv__again" type="button">امتحان مجدد</button>
			</div>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}
