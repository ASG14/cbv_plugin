<?php
/**
 * Admin tab: shortcode & usage guide.
 */

defined( 'ABSPATH' ) || exit;

function chibepoosham_tab_usage_assets(): void {
	wp_enqueue_script( 'chibepoosham-admin-copy', CHIBEPOOSHAM_URL . 'assets/js/admin/copy.js', array(), CHIBEPOOSHAM_VERSION, true );
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
