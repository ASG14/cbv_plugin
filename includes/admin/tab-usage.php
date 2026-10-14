<?php
/**
 * Admin tab: shortcode & usage guide.
 */

defined( 'ABSPATH' ) || exit;

function chibepoosham_tab_usage_assets(): void {
	wp_enqueue_script(
		'chibepoosham-admin-copy',
		CHIBEPOOSHAM_URL . 'assets/js/admin/copy.js',
		array(),
		CHIBEPOOSHAM_VERSION,
		true
	);
}

function chibepoosham_tab_usage(): void {
	?>
	<section class="cbp-guide-hero">
		<h2>پرو مجازی را به صفحه محصولت اضافه کن ✨</h2>
		<p>
			ابتدا ظاهر ویجت را تنظیم کن، سپس شورت‌کد را در صفحه محصول قرار بده
			تا مشتری‌ها بتوانند تجربه پرو مجازی را در فروشگاهت امتحان کنند.
		</p>
	</section>

	<div class="cbp-guide-grid" aria-label="مراحل راه‌اندازی">

		<article class="cbp-guide-step">
			<div class="cbp-guide-step__icon" aria-hidden="true">🎨</div>
			<span class="cbp-guide-step__number">مرحله ۰۱</span>
			<h3>ظاهر ویجت را شخصی‌سازی کن</h3>
			<p>
				از تب «ظاهر و متن‌ها»، رنگ‌ها، نوشته‌ها، شکل دکمه‌ها و تنظیمات
				فرم را مطابق طراحی فروشگاهت تغییر بده. پیش‌نمایش زنده کمک می‌کند
				قبل از ذخیره، نتیجه را ببینی.
			</p>
		</article>

		<article class="cbp-guide-step">
			<div class="cbp-guide-step__icon" aria-hidden="true">📋</div>
			<span class="cbp-guide-step__number">مرحله ۰۲</span>
			<h3>شورت‌کد را کپی کن</h3>
			<p>
				از بخش شورت‌کد، کد آماده را کپی کن. نیازی به نوشتن کد PHP
				یا تغییر فایل‌های قالب سایت نیست.
			</p>
		</article>

		<article class="cbp-guide-step">
			<div class="cbp-guide-step__icon" aria-hidden="true">🛍️</div>
			<span class="cbp-guide-step__number">مرحله ۰۳</span>
			<h3>در صفحه محصول قرار بده</h3>
			<p>
				کد را در بلوک «شورت‌کد» وردپرس یا ویجت «Shortcode» المنتور
				در محل موردنظرت قرار بده و تغییرات را ذخیره کن.
			</p>
		</article>

	</div>

	<section class="cbp-card cbp-guide-section">
		<h2>شورت‌کد اصلی</h2>
		<p class="cbp-muted">
			این کد را در صفحه تک‌محصول ووکامرس قرار بده. تصاویر محصول
			برای استفاده در پرو مجازی به‌صورت خودکار دریافت می‌شوند.
		</p>

		<div class="cbp-copybox">
			<code id="cbp-shortcode" class="cbp-ltr">[chibepoosham_try_on]</code>
			<button
				type="button"
				class="button button-primary cbp-copy"
				data-target="#cbp-shortcode"
			>کپی شورت‌کد</button>
		</div>

		<h3>نمایش برای یک محصول مشخص</h3>
		<p class="cbp-muted">
			اگر شورت‌کد را در صفحه‌ای غیر از صفحه همان محصول قرار می‌دهی،
			شناسه محصول را مشخص کن. عدد ۱۲۳ در نمونه زیر باید با شناسه واقعی
			محصول جایگزین شود.
		</p>

		<div class="cbp-copybox">
			<code id="cbp-shortcode-id" class="cbp-ltr">[chibepoosham_try_on id="123"]</code>
			<button
				type="button"
				class="button cbp-copy"
				data-target="#cbp-shortcode-id"
			>کپی کد نمونه</button>
		</div>
	</section>

	<section class="cbp-card cbp-guide-section">
		<h2>چطور شورت‌کد را در سایت قرار بدهم؟</h2>

		<ol class="cbp-steps">
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

		<p class="cbp-note">
			با کلیک روی دکمه پرو مجازی، فرم استفاده باز می‌شود و مشتری می‌تواند
			تصویر موردنظر را از گالری محصول انتخاب و عکس خودش را بارگذاری کند.
		</p>
	</section>

	<div class="cbp-guide-alert" role="note">
		<span class="cbp-guide-alert__icon" aria-hidden="true">⚠️</span>
		<div>
			<strong>سهمیه پلن را مرتب بررسی کن</strong><br>
			هر بار که مشتری از پرو مجازی استفاده می‌کند، یک واحد از سهمیه پلن
			شما کم می‌شود. میزان سهمیه باقی‌مانده را در حساب کاربری سرویس بررسی
			کن تا پیش از اتمام آن بتوانی برای تمدید یا تغییر پلن اقدام کنی.
		</div>
	</div>
	<?php
}
