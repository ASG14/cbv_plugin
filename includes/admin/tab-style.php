<?php
/**
 * Admin tab: appearance & texts, with live preview.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', 'chibepoosham_register_style_setting' );

function chibepoosham_register_style_setting(): void {
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

function chibepoosham_tab_style_assets(): void {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'chibepoosham-admin-preview', CHIBEPOOSHAM_URL . 'assets/js/admin/preview.js', array( 'jquery', 'wp-color-picker' ), CHIBEPOOSHAM_VERSION, true );
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
