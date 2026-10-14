<?php
/**
 * Admin tab: license.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', 'chibepoosham_register_license_setting' );

function chibepoosham_register_license_setting(): void {
	register_setting(
		'chibepoosham_license_group',
		CHIBEPOOSHAM_LICENSE_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'chibepoosham_sanitize_license',
			'default'           => array(),
		)
	);
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
