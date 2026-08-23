<?php
/**
* Zebra Enhance [Persian]
*
* @copyright (c) 2013-2026 Stanislav Atanasov
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}
$lang = array_merge($lang, array(
	'ACP_ZEBRA_ENHANCE_TITLE' => 'Zebra Enhance',
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'تنظیمات درخواست دوستی',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'محدودیت‌های عمومی درخواست دوستی را تنظیم کنید. هر کاربر می‌تواند در کنترل پنل محدودیت بیشتری انتخاب کند.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'محدودیت درخواست‌ها',
	'ACP_ZE_MAX_PENDING_REQUESTS' => 'حداکثر درخواست‌های در انتظار',
	'ACP_ZE_MAX_PENDING_REQUESTS_EXPLAIN' => 'حداکثر مجموع درخواست‌های ورودی و خروجی هر کاربر. مقدار ۰ یعنی بدون محدودیت.',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS' => 'فاصله پس از رد درخواست',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'تعداد روز تا امکان ارسال دوباره درخواست توسط فرد ردشده. مقدار ۰ این گزینه را غیرفعال می‌کند.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'تنظیمات Zebra Enhance به‌روز شد.',
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>تنظیمات Zebra Enhance تغییر کرد</strong><br>» حداکثر در انتظار: %1$d؛ فاصله رد: %2$d روز',
));
