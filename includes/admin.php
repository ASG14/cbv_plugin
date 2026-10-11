<?php
/**
 * Admin panel: license, style settings with live preview, shortcode guide.
 */

defined( 'ABSPATH' ) || exit;

const CHIBEPOOSHAM_PAGE_SLUG = 'chibepoosham';

add_action( 'admin_menu', 'chibepoosham_admin_menu' );
add_action( 'admin_init', 'chibepoosham_register_settings' );
add_action( 'admin_enqueue_scripts', 'chibepoosham_admin_assets' );
add_filter( 'plugin_action_links_' . plugin_basename( CHIBEPOOSHAM_FILE ), 'chibepoosham_action_links' );

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

function chibepoosham_register_settings(): void {
	register_setting(
		'chibepoosham_license_group',
		CHIBEPOOSHAM_LICENSE_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'chibepoosham_sanitize_license',
			'default'           => array(),
		)
	);

	register_setting(
		'chibepoosham_style_group',
		CHIBEPOOSHAM_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'chibepoosham_sanitize_settings',
			'default'           => chibepoosham_defaults(),
		)
	);
}

function chibepoosham_admin_assets( string $hook ): void {
	if ( 'toplevel_page_' . CHIBEPOOSHAM_PAGE_SLUG !== $hook ) {
		return;
	}

	// The preview reuses the real widget stylesheet.
	wp_enqueue_style( 'chibepoosham-try-on', CHIBEPOOSHAM_URL . 'assets/css/try-on.css', array(), CHIBEPOOSHAM_VERSION );
	wp_enqueue_style( 'chibepoosham-admin', CHIBEPOOSHAM_URL . 'assets/css/admin.css', array( 'wp-color-picker' ), CHIBEPOOSHAM_VERSION );
	wp_enqueue_script( 'chibepoosham-admin', CHIBEPOOSHAM_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), CHIBEPOOSHAM_VERSION, true );
}

/* -------------------------------------------------------------------------
 * Field helpers
 * ---------------------------------------------------------------------- */

function chibepoosham_field_name( string $key ): string {
	return CHIBEPOOSHAM_OPTION . '[' . $key . ']';
}

function chibepoosham_text_field( array $s, string $key, string $label ): void {
	?>
	<div class="cbp-field">
		<label for="cbp-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" id="cbp-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( chibepoosham_field_name( $key ) ); ?>" value="<?php echo esc_attr( $s[ $key ] ); ?>" data-cbp-text="<?php echo esc_attr( $key ); ?>">
	</div>
	<?php
}

function chibepoosham_color_field( array $s, string $key, string $label, string $var ): void {
	$defaults = chibepoosham_defaults();
	?>
	<div class="cbp-field cbp-field--color">
		<label for="cbp-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
		<input type="text" class="cbp-color" id="cbp-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( chibepoosham_field_name( $key ) ); ?>" value="<?php echo esc_attr( $s[ $key ] ); ?>" data-default-color="<?php echo esc_attr( $defaults[ $key ] ); ?>" data-cbp-var="<?php echo esc_attr( $var ); ?>">
	</div>
	<?php
}

function chibepoosham_checkbox_field( array $s, string $key, string $label ): void {
	?>
	<label class="cbp-check">
		<input type="checkbox" name="<?php echo esc_attr( chibepoosham_field_name( $key ) ); ?>" value="1" <?php checked( ! empty( $s[ $key ] ) ); ?> data-cbp-toggle="<?php echo esc_attr( $key ); ?>">
		<span><?php echo esc_html( $label ); ?></span>
	</label>
	<?php
}

/* -------------------------------------------------------------------------
 * Page
 * ---------------------------------------------------------------------- */

function chibepoosham_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tabs = array(
		'license' => 'لایسنس',
		'style'   => 'ظاهر و متن‌ها',
		'usage'   => 'شورت‌کد و راهنما',
	);
	$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'license'; // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'license';
	}
	?>
	<div class="wrap cbp-admin" dir="rtl">
		<h1 class="cbp-admin__title">چی بپوشم؟ <span class="cbp-admin__version">نسخه <?php echo esc_html( CHIBEPOOSHAM_VERSION ); ?></span></h1>

		<nav class="nav-tab-wrapper">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a class="nav-tab <?php echo $slug === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . CHIBEPOOSHAM_PAGE_SLUG . '&tab=' . $slug ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>

		<?php settings_errors(); ?>

		<div class="cbp-admin__body">
			<?php
			if ( 'license' === $tab ) {
				chibepoosham_tab_license();
			} elseif ( 'style' === $tab ) {
				chibepoosham_tab_style();
			} else {
				chibepoosham_tab_usage();
			}
			?>
		</div>
	</div>
	<?php
}

function chibepoosham_tab_license(): void {
	$license = chibepoosham_get_license();
	$labels  = array(
		'inactive' => 'فعال نشده',
		'pending'  => 'کلید ذخیره شد، در انتظار تأیید',
		'active'   => 'فعال',
	);
	$status  = isset( $labels[ $license['status'] ] ) ? $license['status'] : 'inactive';
	?>
	<form class="cbp-card cbp-card--narrow" method="post" action="options.php">
		<?php settings_fields( 'chibepoosham_license_group' ); ?>

		<div class="cbp-card__head">
			<h2>فعال‌سازی لایسنس</h2>
			<span class="cbp-badge cbp-badge--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $labels[ $status ] ); ?></span>
		</div>

		<p class="cbp-muted">کلید لایسنس را از حساب خود در «چی بپوشم؟» بردارید و اینجا وارد کنید. بدون لایسنس فعال، تصویر نهایی تولید نمی‌شود.</p>

		<div class="cbp-field">
			<label for="cbp-license-key">کلید لایسنس</label>
			<input type="text" id="cbp-license-key" class="cbp-ltr" name="<?php echo esc_attr( CHIBEPOOSHAM_LICENSE_OPTION ); ?>[key]" value="<?php echo esc_attr( $license['key'] ); ?>" placeholder="XXXX-XXXX-XXXX-XXXX" autocomplete="off" spellcheck="false">
		</div>

		<?php submit_button( 'ذخیره و فعال‌سازی', 'primary', 'submit', false ); ?>

		<p class="cbp-note">تأیید کلید با سرور لایسنس هنوز متصل نشده است. فعلاً کلید فقط ذخیره می‌شود.</p>
	</form>
	<?php
}

function chibepoosham_tab_style(): void {
	$s = chibepoosham_get_settings();
	?>
	<div class="cbp-layout">
		<form class="cbp-card cbp-form" id="cbp-style-form" method="post" action="options.php">
			<?php settings_fields( 'chibepoosham_style_group' ); ?>

			<fieldset class="cbp-group">
				<legend>متن‌ها</legend>
				<?php
				chibepoosham_text_field( $s, 'trigger_label', 'متن دکمه‌ی اصلی' );
				chibepoosham_text_field( $s, 'drop_text', 'متن کادر آپلود عکس' );
				chibepoosham_text_field( $s, 'drop_hint', 'توضیح زیر متن کادر آپلود' );
				chibepoosham_text_field( $s, 'upload_label', 'متن دکمه‌ی انتخاب فایل' );
				chibepoosham_text_field( $s, 'submit_label', 'متن دکمه‌ی پرو' );
				chibepoosham_text_field( $s, 'result_title', 'عنوان بخش نتیجه' );
				chibepoosham_text_field( $s, 'title', 'عنوان بالای فرم (اختیاری)' );
				chibepoosham_text_field( $s, 'description', 'توضیح بالای فرم (اختیاری)' );
				chibepoosham_text_field( $s, 'loading_text', 'پیام در حال پردازش' );
				chibepoosham_text_field( $s, 'error_text', 'پیام خطا' );
				?>
			</fieldset>

			<fieldset class="cbp-group">
				<legend>رنگ‌ها</legend>
				<?php
				chibepoosham_color_field( $s, 'primary', 'رنگ اصلی (دکمه و انتخاب‌ها)', '--cbp-primary' );
				chibepoosham_color_field( $s, 'primary_text', 'رنگ متن روی دکمه', '--cbp-primary-text' );
				chibepoosham_color_field( $s, 'background', 'پس‌زمینه‌ی فرم', '--cbp-bg' );
				chibepoosham_color_field( $s, 'surface', 'پس‌زمینه‌ی کادر آپلود', '--cbp-surface' );
				chibepoosham_color_field( $s, 'text', 'رنگ متن', '--cbp-text' );
				chibepoosham_color_field( $s, 'border', 'رنگ حاشیه‌ها', '--cbp-border' );
				?>
			</fieldset>

			<fieldset class="cbp-group">
				<legend>شکل</legend>

				<div class="cbp-field">
					<label for="cbp-radius">گردی گوشه‌ها: <output id="cbp-radius-out"><?php echo esc_html( (string) $s['radius'] ); ?></output> پیکسل</label>
					<input type="range" id="cbp-radius" min="0" max="32" step="1" name="<?php echo esc_attr( chibepoosham_field_name( 'radius' ) ); ?>" value="<?php echo esc_attr( (string) $s['radius'] ); ?>" data-cbp-radius>
				</div>

				<div class="cbp-field">
					<label for="cbp-font">فونت</label>
					<input type="text" id="cbp-font" class="cbp-ltr" name="<?php echo esc_attr( chibepoosham_field_name( 'font' ) ); ?>" value="<?php echo esc_attr( $s['font'] ); ?>" data-cbp-font>
					<p class="cbp-hint">پیش‌فرض <code>inherit</code> است و از فونت قالب سایت استفاده می‌کند. برای فونت دیگر، نام آن را بنویسید، مثل <code>Vazirmatn, Tahoma</code>.</p>
				</div>

				<div class="cbp-field">
					<label for="cbp-button-style">حالت دکمه‌ها</label>
					<select id="cbp-button-style" name="<?php echo esc_attr( chibepoosham_field_name( 'button_style' ) ); ?>" data-cbp-button>
						<option value="solid" <?php selected( $s['button_style'], 'solid' ); ?>>توپر</option>
						<option value="outline" <?php selected( $s['button_style'], 'outline' ); ?>>توخالی</option>
					</select>
				</div>

				<div class="cbp-field">
					<label for="cbp-max-images">حداکثر تعداد تصویر قابل انتخاب از گالری محصول</label>
					<input type="number" id="cbp-max-images" min="1" max="10" name="<?php echo esc_attr( chibepoosham_field_name( 'max_images' ) ); ?>" value="<?php echo esc_attr( (string) $s['max_images'] ); ?>">
				</div>

				<?php
				chibepoosham_checkbox_field( $s, 'show_title', 'نمایش عنوان بالای فرم' );
				chibepoosham_checkbox_field( $s, 'show_description', 'نمایش توضیح بالای فرم' );
				chibepoosham_checkbox_field( $s, 'shadow', 'سایه‌ی دور کادرها' );
				?>
			</fieldset>

			<div class="cbp-form__actions">
				<?php submit_button( 'ذخیره تغییرات', 'primary', 'submit', false ); ?>
			</div>
		</form>

		<aside class="cbp-preview" aria-label="پیش‌نمایش زنده">
			<div class="cbp-preview__bar">
				<strong>پیش‌نمایش زنده</strong>
				<div class="cbp-preview__states" role="radiogroup" aria-label="حالت پیش‌نمایش">
					<label class="cbp-check"><input type="radio" name="cbp-preview-state" value="trigger"><span>فقط دکمه</span></label>
					<label class="cbp-check"><input type="radio" name="cbp-preview-state" value="form" checked><span>فرم باز</span></label>
					<label class="cbp-check"><input type="radio" name="cbp-preview-state" value="result"><span>نتیجه</span></label>
				</div>
			</div>
			<div class="cbp-preview__stage" id="cbp-preview">
				<?php
				echo chibepoosham_render_widget( // phpcs:ignore WordPress.Security.EscapeOutput
					array(
						'images'   => chibepoosham_placeholder_images(),
						'preview'  => true,
						'settings' => $s,
					)
				);
				?>
			</div>
			<p class="cbp-hint">در این نمایش، عکس‌ها نمونه هستند. در صفحه‌ی محصول، تصاویر خود محصول نمایش داده می‌شود.</p>
		</aside>
	</div>
	<?php
}

function chibepoosham_tab_usage(): void {
	?>
	<div class="cbp-card cbp-card--narrow">
		<h2>شورت‌کد</h2>
		<p class="cbp-muted">این شورت‌کد را هر جای صفحه‌ی تک‌محصول که می‌خواهید دکمه‌ی پرو مجازی نمایش داده شود قرار بدهید.</p>

		<div class="cbp-copybox">
			<code id="cbp-shortcode" class="cbp-ltr">[chibepoosham_try_on]</code>
			<button type="button" class="button button-primary cbp-copy" data-target="#cbp-shortcode">کپی</button>
		</div>

		<h3>طرز استفاده</h3>
		<ol class="cbp-steps">
			<li>در وردپرس، صفحه‌ی ویرایش محصول یا قالب تک‌محصول را باز کنید.</li>
			<li>یک بلوک «شورت‌کد» (یا ویجت «Shortcode» در المنتور) اضافه کنید.</li>
			<li>شورت‌کد بالا را در آن بچسبانید و ذخیره کنید.</li>
		</ol>

		<h3>نمایش برای یک محصول مشخص</h3>
		<p class="cbp-muted">اگر شورت‌کد را بیرون از صفحه‌ی خود محصول می‌گذارید، شناسه‌ی محصول را بدهید:</p>
		<div class="cbp-copybox">
			<code id="cbp-shortcode-id" class="cbp-ltr">[chibepoosham_try_on id="123"]</code>
			<button type="button" class="button cbp-copy" data-target="#cbp-shortcode-id">کپی</button>
		</div>

		<p class="cbp-note">با کلیک روی دکمه، فرمی باز می‌شود که تصاویر اصلی و گالری محصول در آن به‌صورت خودکار فهرست شده‌اند و کاربر می‌تواند هرکدام را انتخاب کند.</p>
	</div>
	<?php
}