<?php
/**
* Zebra Enhance [Bulgarian]
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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Настройки за покани за приятелство',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Настройте общите ограничения за поканите. Потребителите могат да задават допълнителни ограничения в UCP.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Ограничения за покани',
	'ACP_ZE_MAX_PENDING_REQUESTS' => 'Максимален брой чакащи покани',
	'ACP_ZE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Общ максимален брой входящи и изходящи покани за потребител. 0 означава без ограничение.',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS' => 'Изчакване след отказ',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Дни преди отказан потребител да може да изпрати нова покана. 0 изключва ограничението.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Настройките на Zebra Enhance са обновени.',
	'ACP_ZEBRA_ENHANCE_REPORT' => 'Pending friend requests',
	'ACP_ZEBRA_ENHANCE_REPORT_EXPLAIN' => 'Read-only board-wide report of friend requests that have not yet been accepted, declined, or cancelled.',
	'ACP_ZE_REQUEST_ID' => 'Request ID',
	'ACP_ZE_REQUESTER' => 'Requester',
	'ACP_ZE_RECIPIENT' => 'Recipient',
	'ACP_ZE_REQUEST_DATE' => 'Requested',
	'ACP_ZE_REQUEST_MESSAGE' => 'Message',
	'ACP_ZE_NO_PENDING_REQUESTS' => 'There are no pending friend requests.',
	'ACP_ZE_PENDING_TOTAL' => array(1 => '%d pending friend request', 2 => '%d pending friend requests'),
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Променени настройки на Zebra Enhance</strong><br>» Максимум чакащи: %1$d; изчакване: %2$d дни',
));
