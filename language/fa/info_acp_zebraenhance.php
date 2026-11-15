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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'تنظیمات Zebra Enhance',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'محدودیت‌های درخواست دوستی و جستجوی ارسال‌های دوستان را برای کل انجمن تنظیم کنید و مشخص کنید کاربران در کنترل پنل به کدام امکانات پیشرفته دشمنان دسترسی داشته باشند.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'محدودیت درخواست‌ها',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS' => 'حداکثر درخواست‌های در انتظار',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS_EXPLAIN' => 'حداکثر درخواست‌های ورودی و خروجی هر کاربر که برای هر جهت جداگانه اعمال می‌شود. مقدار ۰ یعنی بدون محدودیت.',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS' => 'فاصله پس از رد درخواست',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'تعداد روز تا امکان ارسال دوباره درخواست توسط فرد ردشده. مقدار ۰ این گزینه را غیرفعال می‌کند.',
	'ACP_ZEBRA_ENHANCE_FRIENDS' => 'دوستان',
	'ACP_ZEBRAENHANCE_FRIEND_POSTS' => 'جستجوی ارسال‌های دوستان',
	'ACP_ZEBRAENHANCE_FRIEND_POSTS_EXPLAIN' => 'گزینه «دیدن ارسال‌های دوستان شما» را به منوی پیوندهای سریع اضافه می‌کند و کنار هر دوست در کنترل پنل کاربر > دوستان پیوندی برای جستجوی ارسال‌های آن دوست قرار می‌دهد.',
	'ACP_ZEBRAENHANCE_FRIEND_HIGHLIGHT' => 'برجسته کردن محتوای دوستان',
	'ACP_ZEBRAENHANCE_FRIEND_HIGHLIGHT_EXPLAIN' => 'به کاربران اجازه می‌دهد ارسال‌ها، موضوع‌ها، نتایج جستجو و پیام‌های خصوصی دوستان خود را با رنگی جداگانه برای دوستان نزدیک برجسته کنند. هر کاربر می‌تواند آن را در کنترل پنل کاربر > دوستان خاموش کند.',
	'ACP_ZEBRAENHANCE_FRIEND_AVATARS' => 'نمایش آواتار دوستان',
	'ACP_ZEBRAENHANCE_FRIEND_AVATARS_EXPLAIN' => 'آواتار هر دوست را در کنترل پنل کاربر > دوستان نشان می‌دهد.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'تنظیمات Zebra Enhance به‌روز شد.',
	'ACP_ZEBRA_ENHANCE_REPORT' => 'درخواست‌های دوستی در انتظار',
	'ACP_ZEBRA_ENHANCE_REPORT_EXPLAIN' => 'گزارش فقط‌خواندنی از درخواست‌های دوستی کل انجمن که هنوز پذیرفته، رد یا لغو نشده‌اند.',
	'ACP_ZEBRAENHANCE_REQUEST_ID' => 'شناسه درخواست',
	'ACP_ZEBRAENHANCE_REQUESTER' => 'درخواست‌دهنده',
	'ACP_ZEBRAENHANCE_RECIPIENT' => 'گیرنده',
	'ACP_ZEBRAENHANCE_REQUEST_DATE' => 'تاریخ درخواست',
	'ACP_ZEBRAENHANCE_REQUEST_MESSAGE' => 'پیام',
	'ACP_ZEBRAENHANCE_NO_PENDING_REQUESTS' => 'هیچ درخواست دوستی در انتظاری وجود ندارد.',
	'ACP_ZEBRAENHANCE_PENDING_TOTAL' => array(1 => '%d درخواست دوستی در انتظار', 2 => '%d درخواست دوستی در انتظار'),
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>تنظیمات Zebra Enhance تغییر کرد</strong><br>» حداکثر درخواست‌های در انتظار: %1$d؛ فاصله پس از رد: %2$d روز؛ امکانات پیشرفته دشمنان: %3$s',
	'ACP_ZEBRA_ENHANCE_FOES' => 'امکانات پیشرفته دشمنان',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT' => 'فعال‌سازی امکانات پیشرفته دشمنان',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT_EXPLAIN' => 'مدیریت پیشرفته دشمنان و محافظت‌های انتخاب‌شده در زیر را در اختیار کاربران قرار می‌دهد. در صورت غیرفعال بودن، رفتار پیش‌فرض phpBB با دشمنان بدون تغییر می‌ماند.',
	'ACP_ZEBRAENHANCE_FOE_PM' => 'محافظت از پیام‌های خصوصی',
	'ACP_ZEBRAENHANCE_FOE_PM_EXPLAIN' => 'به کاربران اجازه می‌دهد انتخاب کنند چه کسانی بتوانند برای آن‌ها پیام خصوصی بفرستند: همه، همه به جز دشمنان، فقط دوستان یا فقط دوستان نزدیک.',
	'ACP_ZEBRAENHANCE_FOE_CONTENT' => 'پنهان کردن محتوا و نقل‌قول‌ها',
	'ACP_ZEBRAENHANCE_FOE_CONTENT_EXPLAIN' => 'به کاربران اجازه می‌دهد پست‌ها، موضوع‌ها، نتایج جستجو و نقل‌قول‌های قابل شناسایی دشمنان خود را پشت یک پیغام یا به‌طور کامل پنهان کنند.',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS' => 'بی‌صدا کردن اعلان‌ها',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS_EXPLAIN' => 'به کاربران اجازه می‌دهد اعلان‌های پشتیبانی‌شده‌ای را که دشمنانشان ایجاد می‌کنند متوقف کنند.',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY' => 'دشمنان موقت',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY_EXPLAIN' => 'به کاربران اجازه می‌دهد کسی را برای ۲۴ ساعت، ۷ روز یا ۳۰ روز به فهرست دشمنان اضافه کنند.',
	'ACP_ZEBRAENHANCE_FOE_NOTES' => 'یادداشت‌های خصوصی درباره دشمنان',
	'ACP_ZEBRAENHANCE_FOE_NOTES_EXPLAIN' => 'به کاربران اجازه می‌دهد برای هر دشمن یک یادداشت خصوصی ذخیره کنند.',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS' => 'استثنا برای هر دشمن',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS_EXPLAIN' => 'به کاربران اجازه می‌دهد انتخاب‌های عمومی پیام خصوصی، محتوا و اعلان را برای دشمنان جداگانه تغییر دهند.',
));
