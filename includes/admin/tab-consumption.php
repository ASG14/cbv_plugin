<?php
/**
 * Admin tab: plan and usage.
 *
 * NOTE:
 * Values in this file are temporary demo data.
 * Replace them with the server response when the API is ready.
 */

defined( 'ABSPATH' ) || exit;

function chibepoosham_tab_consumption(): void {

	/*
	 * Temporary demo data.
	 * These values must eventually come from the backend API.
	 */
	$plan_name      = 'حرفه‌ای';
	$quota_total    = 1000;
	$quota_used     = 200;
	$quota_remaining = max( 0, $quota_total - $quota_used );
	$expires_at     = '۱۴۰۵/۰۸/۳۰';

	$usage_percent = $quota_total > 0
		? min( 100, round( ( $quota_used / $quota_total ) * 100 ) )
		: 0;

	$remaining_percent = $quota_total > 0
		? min( 100, round( ( $quota_remaining / $quota_total ) * 100 ) )
		: 0;

	$circumference = 289.03;
	$dash_offset   = $circumference * ( 1 - $remaining_percent / 100 );

	?>
	<div class="cbv-usage">

		<!-- Hero -->
		<section class="cbv-usage-hero">

			<div class="cbv-usage-hero__content">
				<span class="cbv-usage-eyebrow">
					پنل مدیریت اشتراک
				</span>

				<h2>وضعیت پلن و مصرف شما</h2>

				<p>
					در یک نگاه، وضعیت اشتراک و سهمیه باقی‌مانده فروشگاهت
					را بررسی کن.
				</p>
			</div>

			<div class="cbv-usage-hero__icon" aria-hidden="true">
				<svg viewBox="0 0 64 64" fill="none">
					<rect x="8" y="8" width="48" height="48" rx="16"
						stroke="currentColor" stroke-width="2" />
					<path d="M20 38L28 30L34 36L44 24"
						stroke="currentColor" stroke-width="3"
						stroke-linecap="round" stroke-linejoin="round" />
					<path d="M37 24H44V31"
						stroke="currentColor" stroke-width="3"
						stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</div>

		</section>

		<!-- Main grid -->
		<div class="cbv-usage-grid">

			<!-- Remaining quota card -->
			<section class="cbv-usage-card cbv-usage-card--quota">

				<div class="cbv-usage-card__heading">
					<div>
						<span class="cbv-usage-label">سهمیه باقی‌مانده</span>
						<h3>مصرف سرویس</h3>
					</div>

					<span class="cbv-usage-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none">
							<circle cx="12" cy="12" r="9"
								stroke="currentColor" stroke-width="1.8" />
							<path d="M12 7V12L15.5 14"
								stroke="currentColor" stroke-width="1.8"
								stroke-linecap="round" />
						</svg>
					</span>
				</div>

				<div class="cbv-usage-chart-wrap">

					<svg
						class="cbv-usage-chart"
						viewBox="0 0 120 120"
						role="img"
						aria-label="<?php
							echo esc_attr(
								sprintf(
									'%s درصد سهمیه باقی‌مانده',
									$remaining_percent
								)
							);
						?>"
					>
						<circle
							class="cbv-usage-chart__track"
							cx="60"
							cy="60"
							r="46"
						/>

						<circle
							class="cbv-usage-chart__progress"
							cx="60"
							cy="60"
							r="46"
							stroke-dasharray="<?php echo esc_attr( (string) $circumference ); ?>"
							stroke-dashoffset="<?php echo esc_attr( (string) $dash_offset ); ?>"
						/>

						<text
							class="cbv-usage-chart__number"
							x="60"
							y="57"
							text-anchor="middle"
						><?php echo esc_html( (string) $remaining_percent ); ?>٪</text>

						<text
							class="cbv-usage-chart__caption"
							x="60"
							y="75"
							text-anchor="middle"
						>باقی‌مانده</text>
					</svg>

				</div>

				<div class="cbv-usage-quota-number">
					<strong>
						<?php echo esc_html( number_format_i18n( $quota_remaining ) ); ?>
					</strong>

					<span>درخواست باقی‌مانده</span>
				</div>

				<div class="cbv-usage-progress">
					<div class="cbv-usage-progress__labels">
						<span>مصرف‌شده</span>
						<strong><?php echo esc_html( (string) $usage_percent ); ?>٪</strong>
					</div>

					<div
						class="cbv-usage-progress__track"
						role="progressbar"
						aria-label="درصد سهمیه مصرف‌شده"
						aria-valuemin="0"
						aria-valuemax="100"
						aria-valuenow="<?php echo esc_attr( (string) $usage_percent ); ?>"
					>
						<span
							class="cbv-usage-progress__bar"
							style="width: <?php echo esc_attr( (string) $usage_percent ); ?>%;"
						></span>
					</div>
				</div>

				<div class="cbv-usage-card__footer">
					<span>کل سهمیه پلن</span>
					<strong>
						<?php echo esc_html( number_format_i18n( $quota_total ) ); ?>
						درخواست
					</strong>
				</div>

			</section>

			<!-- Plan card -->
			<section class="cbv-usage-card cbv-usage-card--plan">

				<div class="cbv-usage-card__heading">
					<div>
						<span class="cbv-usage-label">اشتراک شما</span>
						<h3>پلن فعلی</h3>
					</div>

					<span class="cbv-usage-plan-badge">
						فعال
					</span>
				</div>

				<div class="cbv-usage-plan-illustration" aria-hidden="true">
					<svg viewBox="0 0 120 100" fill="none">
						<rect x="20" y="20" width="80" height="60" rx="12"
							stroke="currentColor" stroke-width="2" />
						<path d="M36 40H84M36 52H68"
							stroke="currentColor" stroke-width="3"
							stroke-linecap="round" />
						<circle cx="83" cy="67" r="8"
							fill="currentColor" fill-opacity=".14"
							stroke="currentColor" stroke-width="2" />
						<path d="M79 67L82 70L87 64"
							stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</div>

				<div class="cbv-usage-plan-name">
					<h3><?php echo esc_html( $plan_name ); ?></h3>
					<p>پلن فعال فروشگاه شما</p>
				</div>

				<div class="cbv-usage-plan-details">

					<div>
						<span>تعداد مصرف‌شده</span>
						<strong>
							<?php echo esc_html( number_format_i18n( $quota_used ) ); ?>
							درخواست
						</strong>
					</div>

					<div>
						<span>پایان دوره</span>
						<strong><?php echo esc_html( $expires_at ); ?></strong>
					</div>

				</div>

				<a
					class="cbv-usage-website-button"
					href="https://chibepoosham.app/"
					target="_blank"
					rel="noopener noreferrer"
				>
					<span>مدیریت اشتراک در سایت</span>

					<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
						<path d="M7 17L17 7M8 7H17V16"
							stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</a>

			</section>

		</div>

		<!-- Informational notice -->
		<section class="cbv-usage-notice">

			<span class="cbv-usage-notice__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" fill="none">
					<circle cx="12" cy="12" r="9"
						stroke="currentColor" stroke-width="1.8" />
					<path d="M12 11V16M12 8H12.01"
						stroke="currentColor" stroke-width="2"
						stroke-linecap="round" />
				</svg>
			</span>

			<div>
				<strong>اطلاعات اشتراک</strong>
				<p>
					میزان مصرف و سهمیه نمایش‌داده‌شده در این نسخه آزمایشی است.
					پس از اتصال به سرور، اطلاعات واقعی اشتراک و مصرف در این بخش
					نمایش داده خواهد شد.
				</p>
			</div>

		</section>

	</div>
	<?php
}