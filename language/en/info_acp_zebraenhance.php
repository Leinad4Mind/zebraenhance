<?php
/**
*
* Zebra Enhance [English]
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
	'ACP_ZEBRA_ENHANCE_TITLE' => 'Zebra Enhance',
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Friend request settings',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Configure board-wide limits for friend requests. Individual users can further restrict who may contact them in the UCP.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Request limits',
	'ACP_ZE_MAX_PENDING_REQUESTS' => 'Maximum pending requests',
	'ACP_ZE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Maximum combined incoming and outgoing pending requests per user. Set to 0 for no limit.',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS' => 'Cooldown after a decline',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Number of days before the declined requester may contact that user again. Set to 0 to disable.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Zebra Enhance settings updated.',
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Changed Zebra Enhance settings</strong><br>» Maximum pending requests: %1$d; decline cooldown: %2$d days',
));
