<?php
/**
 * Admin tab: shortcode & usage guide.
 */

defined( 'ABSPATH' ) || exit;

function cbv_tab_usage_assets(): void {
	wp_enqueue_script(
		'cbv-admin-copy',
		CBV_URL . 'assets/js/admin/copy.js',
		array(),
		CBV_VERSION,
		true
	);
}

function cbv_tab_usage(): void {
	?>
	<section class="cbv-guide-hero">
		<h2>پرو مجازی را به صفحه محصولت اضافه کن ✨</h2>
		<p>
			ابتدا ظاهر ویجت را تنظیم کن، سپس شورت‌کد را در صفحه محصول قرار بده
			تا مشتری‌ها بتوانند تجربه پرو مجازی را در فروشگاهت امتحان کنند.
		</p>
	</section>

	<div class="cbv-guide-grid" aria-label="مراحل راه‌اندازی">

		<article class="cbv-guide-step">
			<div class="cbv-guide-step__icon" aria-hidden="true">🎨</div>
			<span class="cbv-guide-step__number">مرحله 1</span>
			<h3>ظاهر ویجت را شخصی‌سازی کن</h3>
			<p>
				از تب «ظاهر و متن‌ها»، رنگ‌ها، نوشته‌ها، شکل دکمه‌ها و تنظیمات
				فرم را مطابق طراحی فروشگاهت تغییر بده. پیش‌نمایش زنده کمک می‌کند
				قبل از ذخیره، نتیجه را ببینی.
			</p>
		</article>

		<article class="cbv-guide-step">
			<div class="cbv-guide-step__icon" aria-hidden="true">📋</div>
			<span class="cbv-guide-step__number">مرحله 2</span>
			<h3>شورت‌کد را کپی کن</h3>
			<p>
				از بخش شورت‌کد، کد آماده را کپی کن. نیازی به نوشتن کد PHP
				یا تغییر فایل‌های قالب سایت نیست.
			</p>
		</article>

		<article class="cbv-guide-step">
			<div class="cbv-guide-step__icon" aria-hidden="true">🛍️</div>
			<span class="cbv-guide-step__number">مرحله 3</span>
			<h3>در صفحه محصول قرار بده</h3>
			<p>
				کد را در بلوک «شورت‌کد» وردپرس یا ویجت «Shortcode» المنتور
				در محل موردنظرت قرار بده و تغییرات را ذخیره کن.
			</p>
		</article>

	</div>

	<section class="cbv-card cbv-guide-section">
		<h2>شورت‌کد اصلی</h2>
		<p class="cbv-muted">
			این کد را در قالب تک‌محصول المنتور قرار بده. تصاویر محصول
			برای استفاده در پرو مجازی به‌صورت خودکار دریافت می‌شوند.
		</p>

		<div class="cbv-copybox">
			<code id="cbv-shortcode" class="cbv-ltr">[cbv_try_on]</code>
			<button
				type="button"
				class="button button-primary cbv-copy"
				data-target="#cbv-shortcode"
			>کپی شورت‌کد</button>
		</div>

		<h3>نمایش برای یک محصول مشخص</h3>
		<p class="cbv-muted">
			اگر شورت‌کد را در صفحه‌ای غیر از صفحه همان محصول قرار می‌دهی،
			شناسه محصول را مشخص کن. عدد 123 در نمونه زیر باید با شناسه واقعی
			محصول جایگزین شود.
		</p>

		<div class="cbv-copybox">
			<code id="cbv-shortcode-id" class="cbv-ltr">[cbv_try_on id="123"]</code>
			<button
				type="button"
				class="button cbv-copy"
				data-target="#cbv-shortcode-id"
			>کپی کد نمونه</button>
		</div>
	</section>

	<section class="cbv-card cbv-guide-section">
		<h2>چطور شورت‌کد را در سایت قرار بدهم؟</h2>

		<ol class="cbv-steps">
			<li>
				در پیشخوان وردپرس، محصول موردنظر یا قالب تک‌محصول المنتور را باز کن.
			</li>
			<li>
				در محل دلخواه، بلوک «شورت‌کد» وردپرس یا ویجت «Shortcode» المنتور
				را اضافه کن.
			</li>
			<li>
				شورت‌کد را داخل آن قرار بده و تغییرات را ذخیره یا منتشر کن.
			</li>
			<li>
				صفحه محصول را در سایت باز کن و نمایش دکمه و بازشدن فرم را بررسی کن.
			</li>
		</ol>

		<p class="cbv-note">
			با کلیک روی دکمه پرو مجازی، فرم استفاده باز می‌شود و مشتری می‌تواند
			تصویر موردنظر را از گالری محصول انتخاب و عکس خودش را بارگذاری کند.
		</p>
	</section>

	<div class="cbv-guide-alert" role="note">
		<span class="cbv-guide-alert__icon" aria-hidden="true">⚠️</span>
		<div>
			<strong>سهمیه پلن را مرتب بررسی کن</strong><br>
			هر بار که مشتری از پرو مجازی استفاده می‌کند، یک واحد از سهمیه پلن
			شما کم می‌شود. میزان سهمیه باقی‌مانده را در حساب کاربری سرویس بررسی
			کن تا پیش از اتمام آن بتوانی برای تمدید یا تغییر پلن اقدام کنی.
		</div>
	</div>
	<?php
}
