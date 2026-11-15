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
	'ACL_U_ZEBRAENHANCE_USE_FRIEND_REQUESTS'       => 'Pode usar solicitações de amizade e listas de amigos avançadas',
	'ACL_U_ZEBRAENHANCE_MANAGE_CLOSE_FRIENDS'      => 'Pode gerenciar Amigos próximos',
	'ACL_M_ZEBRAENHANCE_VIEW_PRIVATE_FRIENDLISTS'  => 'Pode ver listas de amigos, aniversários e idades privados',
));
