<?php
/**
 * Admin tab: appearance & texts, with live preview.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', 'cbv_register_style_setting' );

function cbv_register_style_setting(): void {
	register_setting(
		'cbv_style_group',
		CBV_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'cbv_sanitize_settings',
			'default'           => cbv_defaults(),
		)
	);
}

function cbv_tab_style_assets(): void {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'cbv-admin-preview', CBV_URL . 'assets/js/admin/preview.js', array( 'jquery', 'wp-color-picker' ), CBV_VERSION, true );
}

function cbv_tab_style(): void {
	$s = cbv_get_settings();
	?>
	<div class="cbv-layout">
		<form class="cbv-card cbv-form" id="cbv-style-form" method="post" action="options.php">
			<?php settings_fields( 'cbv_style_group' ); ?>

			<fieldset class="cbv-group">
				<legend>متن‌ها</legend>
				<?php
				cbv_text_field( $s, 'trigger_label', 'متن دکمه‌ی اصلی' );
				cbv_text_field( $s, 'drop_text', 'متن کادر آپلود عکس' );
				cbv_text_field( $s, 'drop_hint', 'توضیح زیر متن کادر آپلود' );
				cbv_text_field( $s, 'upload_label', 'متن دکمه‌ی انتخاب فایل' );
				cbv_text_field( $s, 'submit_label', 'متن دکمه‌ی پرو' );
				cbv_text_field( $s, 'result_title', 'عنوان بخش نتیجه' );
				cbv_text_field( $s, 'title', 'عنوان بالای فرم (اختیاری)' );
				cbv_text_field( $s, 'description', 'توضیح بالای فرم (اختیاری)' );
				cbv_text_field( $s, 'loading_text', 'پیام در حال پردازش' );
				cbv_text_field( $s, 'error_text', 'پیام خطا' );
				?>
			</fieldset>

			<fieldset class="cbv-group">
				<legend>رنگ‌ها</legend>
				<?php
				cbv_color_field( $s, 'primary', 'رنگ اصلی (دکمه و انتخاب‌ها)', '--cbv-primary' );
				cbv_color_field( $s, 'primary_text', 'رنگ متن روی دکمه', '--cbv-primary-text' );
				cbv_color_field( $s, 'background', 'پس‌زمینه‌ی فرم', '--cbv-bg' );
				cbv_color_field( $s, 'surface', 'پس‌زمینه‌ی کادر آپلود', '--cbv-surface' );
				cbv_color_field( $s, 'text', 'رنگ متن', '--cbv-text' );
				cbv_color_field( $s, 'border', 'رنگ حاشیه‌ها', '--cbv-border' );
				?>
			</fieldset>

			<fieldset class="cbv-group">
				<legend>شکل</legend>

				<div class="cbv-field">
					<label for="cbv-radius">گردی گوشه‌ها: <output id="cbv-radius-out"><?php echo esc_html( (string) $s['radius'] ); ?></output> پیکسل</label>
					<input type="range" id="cbv-radius" min="0" max="32" step="1" name="<?php echo esc_attr( cbv_field_name( 'radius' ) ); ?>" value="<?php echo esc_attr( (string) $s['radius'] ); ?>" data-cbv-radius>
				</div>

				<div class="cbv-field">
					<label for="cbv-font">فونت</label>
					<input type="text" id="cbv-font" class="cbv-ltr" name="<?php echo esc_attr( cbv_field_name( 'font' ) ); ?>" value="<?php echo esc_attr( $s['font'] ); ?>" data-cbv-font>
					<p class="cbv-hint">پیش‌فرض <code>inherit</code> است و از فونت قالب سایت استفاده می‌کند. برای فونت دیگر، نام آن را بنویسید، مثل <code>Vazirmatn, Tahoma</code>.</p>
				</div>

				<div class="cbv-field">
					<label for="cbv-button-style">حالت دکمه‌ها</label>
					<select id="cbv-button-style" name="<?php echo esc_attr( cbv_field_name( 'button_style' ) ); ?>" data-cbv-button>
						<option value="solid" <?php selected( $s['button_style'], 'solid' ); ?>>توپر</option>
						<option value="outline" <?php selected( $s['button_style'], 'outline' ); ?>>توخالی</option>
					</select>
				</div>

				<div class="cbv-field">
					<label for="cbv-max-images">حداکثر تعداد تصویر قابل انتخاب از گالری محصول</label>
					<input type="number" id="cbv-max-images" min="1" max="10" name="<?php echo esc_attr( cbv_field_name( 'max_images' ) ); ?>" value="<?php echo esc_attr( (string) $s['max_images'] ); ?>">
				</div>

				<?php
				cbv_checkbox_field( $s, 'show_title', 'نمایش عنوان بالای فرم' );
				cbv_checkbox_field( $s, 'show_description', 'نمایش توضیح بالای فرم' );
				cbv_checkbox_field( $s, 'shadow', 'سایه‌ی دور کادرها' );
				?>
			</fieldset>

			<div class="cbv-form__actions">
				<?php submit_button( 'ذخیره تغییرات', 'primary', 'submit', false ); ?>
			</div>
		</form>

		<aside class="cbv-preview" aria-label="پیش‌نمایش زنده">
			<div class="cbv-preview__bar">
				<strong>پیش‌نمایش زنده</strong>
				<div class="cbv-preview__states" role="radiogroup" aria-label="حالت پیش‌نمایش">
					<label class="cbv-check"><input type="radio" name="cbv-preview-state" value="trigger"><span>فقط دکمه</span></label>
					<label class="cbv-check"><input type="radio" name="cbv-preview-state" value="form" checked><span>فرم باز</span></label>
					<label class="cbv-check"><input type="radio" name="cbv-preview-state" value="result"><span>نتیجه</span></label>
				</div>
			</div>
			<div class="cbv-preview__stage" id="cbv-preview">
				<?php
				echo cbv_render_widget( // phpcs:ignore WordPress.Security.EscapeOutput
					array(
						'images'   => cbv_placeholder_images(),
						'preview'  => true,
						'settings' => $s,
					)
				);
				?>
			</div>
			<p class="cbv-hint">در این نمایش، عکس‌ها نمونه هستند. در صفحه‌ی محصول، تصاویر خود محصول نمایش داده می‌شود.</p>
		</aside>
	</div>
	<?php
}
