# چی بپوشم؟ (chibepooshim_plugin)

افزونه‌ی وردپرس برای پرو مجازی لباس در فروشگاه‌های ووکامرسی. پردازش و تولید تصویر در سرویس «چی بپوشم؟» انجام می‌شود؛ این افزونه فقط پنل تنظیمات، شورت‌کد و ارتباط با سرویس را دارد.

## نصب
پوشه‌ی افزونه را در `wp-content/plugins/` بگذارید و فعالش کنید. منوی «چی بپوشم؟» در پیشخوان اضافه می‌شود.

## استفاده
1. تب «لایسنس»: کلید لایسنس را وارد کنید.
2. تب «ظاهر و متن‌ها»: متن، رنگ و چیدمان را با پیش‌نمایش زنده تنظیم کنید.
3. تب «شورت‌کد و راهنما»: `[chibepoosham_try_on]` را در صفحه‌ی تک‌محصول بگذارید.
   برای محصول مشخص: `[chibepoosham_try_on id="123"]`

کاربر می‌تواند عکس خودش را بدهد و هر تصویری از گالری محصول (تصویر اصلی + گالری) را انتخاب کند.

## ساختار
```
chibepooshim_plugin.php      بوت‌استرپ
includes/settings.php        مقادیر پیش‌فرض، sanitize، لایسنس (فقط ذخیره)
includes/widget.php          شورت‌کد و HTML ویجت (مشترک با پیش‌نمایش ادمین)
includes/rest.php            POST /wp-json/chibepoosham/v1/try-on
includes/admin.php           پنل مدیریت
assets/css/try-on.css        استایل ویجت (با CSS variables)
assets/js/try-on.js          رفتار فرانت: آپلود، انتخاب تصویر، ارسال، نتیجه
assets/css/admin.css, assets/js/admin.js
```

## وضعیت فعلی
- فرانت‌اند کامل است: پنل ادمین، پیش‌نمایش زنده، شورت‌کد، آپلود، انتخاب از گالری، نمایش نتیجه.
- لایسنس: فقط ذخیره می‌شود (وضعیت `pending`). تأیید با سرور در بک‌اند بعدی اضافه می‌شود؛ نقطه‌ی اتصال: فیلتر `chibepoosham_license_status_on_save`.
- سرویس پردازش وصل نیست؛ تا آن موقع endpoint در **حالت دمو** جواب می‌دهد (همان عکس کاربر را برمی‌گرداند).

## اتصال به سرویس try-on
فیلتر `chibepoosham_try_on_request` را پیاده‌سازی کنید:

```php
add_filter( 'chibepoosham_try_on_request', function ( $result, $payload ) {
	// $payload: person_file (مسیر temp)، person_mime، garment_file، garment_url، product_id
	$url = my_call_tryon_api( $payload ); // درخواست به سرویس شما
	return is_wp_error( $url ) ? $url : $url; // URL تصویر نتیجه یا WP_Error
}, 10, 2 );
```
محدودیت نرخ پیش‌فرض ۱۰ درخواست در ۱۰ دقیقه برای هر IP است (فیلتر `chibepoosham_rate_limit`).
