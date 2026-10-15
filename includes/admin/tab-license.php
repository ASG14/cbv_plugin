<?php
/**
 * Admin tab: license.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', 'cbv_register_license_setting' );

function cbv_register_license_setting(): void {
	register_setting(
		'cbv_license_group',
		CBV_LICENSE_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'cbv_sanitize_license',
			'default'           => array(),
		)
	);
}

function cbv_tab_license(): void {
	$license = cbv_get_license();

	$labels = array(
		'inactive' => 'فعال نشده',
		'pending'  => 'کلید ذخیره شده؛ در انتظار تأیید',
		'active'   => 'فعال',
	);

	$status = isset( $labels[ $license['status'] ] )
		? $license['status']
		: 'inactive';
	?>

	<section class="cbv-guide-hero">
		<h2>فروشگاهت را به چی بپوشم؟ متصل کن 🔐</h2>
		<p>
			برای استفاده از سرویس پرو مجازی، ابتدا حساب کاربری بساز،
			فروشگاهت را ثبت کن و پلن مناسب را بخر. سپس کلید لایسنس را
			در همین صفحه وارد کن.
		</p>
	</section>

	<div class="cbv-card cbv-card--narrow">

		<div class="cbv-card__head">
			<h2>وضعیت کلید لایسنس</h2>

			<span class="cbv-badge cbv-badge--<?php echo esc_attr( $status ); ?>">
				<?php echo esc_html( $labels[ $status ] ); ?>
			</span>
		</div>

		<div class="cbv-license-steps">

			<div class="cbv-license-step">
				<div class="cbv-license-step__icon" aria-hidden="true">👤</div>
				<div>
					<h3>۱. حساب کاربری بساز</h3>
					<p>
						وارد وب‌سایت چی بپوشم؟ شو و یک حساب کاربری ایجاد کن.
					</p>
				</div>
			</div>

			<div class="cbv-license-step">
				<div class="cbv-license-step__icon" aria-hidden="true">🏪</div>
				<div>
					<h3>۲. فروشگاهت را ثبت کن</h3>
					<p>
						در حساب کاربری، فروشگاهی را که می‌خواهی از سرویس
						پرو مجازی استفاده کند ثبت کن.
					</p>
				</div>
			</div>

			<div class="cbv-license-step">
				<div class="cbv-license-step__icon" aria-hidden="true">💳</div>
				<div>
					<h3>۳. پلن موردنظرت را بخر</h3>
					<p>
						با توجه به نیاز فروشگاهت، پلن مناسب را انتخاب و خریداری کن.
					</p>
				</div>
			</div>

			<div class="cbv-license-step">
				<div class="cbv-license-step__icon" aria-hidden="true">🔑</div>
				<div>
					<h3>۴. کلید لایسنس را کپی کن</h3>
					<p>
						پس از خرید، کلید لایسنس را از حساب کاربری‌ات بردار
						و در کادر زیر وارد کن.
					</p>
				</div>
			</div>

		</div>

		<p>
			<a
				class="button button-primary"
				href="https://chibepoosham.app/"
				target="_blank"
				rel="noopener noreferrer"
			>ورود به وب‌سایت چی بپوشم؟ ↗</a>
		</p>

		<hr>

		<h2>ورود کلید لایسنس</h2>

		<p class="cbv-muted">
			کلید خریداری‌شده را در کادر زیر وارد کن و روی ذخیره کلید بزن.
		</p>

		<form method="post" action="options.php">
			<?php settings_fields( 'cbv_license_group' ); ?>

			<div class="cbv-field">
				<label for="cbv-license-key">کلید لایسنس</label>

				<input
					type="text"
					id="cbv-license-key"
					class="cbv-ltr"
					name="<?php echo esc_attr( CBV_LICENSE_OPTION ); ?>[key]"
					value="<?php echo esc_attr( $license['key'] ); ?>"
					placeholder="XXXX-XXXX-XXXX-XXXX"
					autocomplete="off"
					spellcheck="false"
				>

				<p class="cbv-hint">
					کلید را دقیقاً مطابق مقدار نمایش‌داده‌شده در حساب کاربری وارد کن.
				</p>
			</div>

			<?php submit_button( 'ذخیره کلید لایسنس', 'primary', 'submit', false ); ?>
		</form>

		<div class="cbv-guide-alert" role="note">
			<span class="cbv-guide-alert__icon" aria-hidden="true">ℹ️</span>
			<div>
				<strong>نکته مهم درباره فعال‌سازی</strong><br>
				در نسخه فعلی افزونه، کلید لایسنس در تنظیمات وردپرس ذخیره می‌شود؛
				اتصال تأیید لایسنس به سرور هنوز پیاده‌سازی نشده است.
				بنابراین ذخیره کلید به‌تنهایی به معنی تأیید یا فعال‌شدن واقعی
				لایسنس نیست.
			</div>
		</div>

	</div>

	<?php
}
