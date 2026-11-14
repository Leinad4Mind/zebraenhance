<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2013-2026 Stanislav Atanasov
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
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
	'ACL_U_ZEBRAENHANCE_USE_FRIEND_REQUESTS'       => 'می‌تواند از درخواست دوستی و فهرست دوستان پیشرفته استفاده کند',
	'ACL_U_ZEBRAENHANCE_MANAGE_CLOSE_FRIENDS'      => 'می‌تواند دوستان نزدیک را مدیریت کند',
	'ACL_M_ZEBRAENHANCE_VIEW_PRIVATE_FRIENDLISTS'  => 'می‌تواند فهرست‌های دوستان خصوصی را ببیند',
));
