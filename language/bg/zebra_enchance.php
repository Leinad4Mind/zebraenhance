<?php

/**
*
* Zebra Enhance [Bulgarian]
*
* @package language
* @version $Id$
* @copyright (c) 2026 Leinad4Mind
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Очакващи потвърждение',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'Списък на очакващите потвърждение заявки за приятелство.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Изчакващи потвърждение',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'Списък на изчкаващите вашето потвърждение заявки за приятелство.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'Нямате изчакващи заявки',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'Сигурни ли сте, че искате да отхвърлите предложението за приятелство?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> 'Предложението за приятелство е отхвърлено!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Сигнал за получаване на заявка за приятелство',
	'NOTIFICATION_ZEBRA_ADD'	=>	'%1$s Ви изпрати покана за приятелство!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Сигнал за потвърждаване на приятелство',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s потвърди приятелството!',

	'FRINEDLIST_TITLE'	=>	'Приятели',

	'NOT_ENEMY'	=>	'Всички без врагове',
	'SPECIAL_FRIENDS'	=>	'Специални приятели',

	'ZE_FRIENDLIST'	=>	'Листа с приятели',
	'ZE_FRIENDLIST_EXPLAIN'	=>	'Кой може да вижда приятелите ви в профила ви?',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Нямате права да виждате приятелите на този потребител',
	'FRIENDLIST_TITLE'       => 'Friend list',
	'ZE_ACCEPT_REQUEST'      => 'Accept friend request',
	'ZE_DECLINE_REQUEST'     => 'Decline friend request',
	'ZE_CANCEL_REQUEST'      => 'Cancel friend request',
	'ZE_REMOVE_FRIEND'       => 'Remove friend',
	'ZE_ADD_CLOSE_FRIEND'    => 'Add to Close Friends',
	'ZE_REMOVE_CLOSE_FRIEND' => 'Remove from Close Friends',
	'ZE_AJAX_NOT_AUTHORIZED' => 'You are not authorized to change Close Friends.',
	'ZE_AJAX_NOT_FRIEND'     => 'That user is not in your friend list.',

));
