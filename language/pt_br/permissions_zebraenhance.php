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
	'ACL_U_ZE_USE'                      => 'Can use friend requests and enhanced friend lists',
	'ACL_U_ZE_CLOSE_FRIENDS'            => 'Can manage Close Friends',
	'ACL_M_ZE_VIEW_PRIVATE_FRIENDLISTS' => 'Can view private friend lists',
));
