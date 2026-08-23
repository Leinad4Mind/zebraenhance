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
	'ZE_AJAX_REQUEST_NOT_FOUND' => 'That friend request no longer exists or does not belong to you.',
	'ZE_REQUEST_ACCEPTED'       => 'Friend request accepted.',
	'ZE_FRIEND_REQUEST_CREATED' => 'Friend request sent.',
	'ZE_REQUEST_DECLINED'       => 'Friend request declined.',
	'ZE_REQUEST_CANCELLED'      => 'Friend request cancelled.',
	'ZE_CONFIRM_DECLINE_REQUEST' => 'Are you sure you want to decline this friend request?',
	'ZE_CONFIRM_CANCEL_REQUEST'  => 'Are you sure you want to cancel this friend request?',
	'ZE_ERROR_TITLE'              => 'Friend request error',
	'ZE_REQUEST_FAILED'           => 'The request could not be completed.',
	'ZE_NO_FRIENDS'               => 'No friends currently defined.',
	'ZE_VIS_EVERYONE'             => 'Everyone',
	'ZE_VIS_REGISTERED'           => 'Registered users',
	'ZE_VIS_NON_FOES'             => 'Everyone except foes',
	'ZE_VIS_FRIENDS'              => 'Friends',
	'ZE_VIS_CLOSE_FRIENDS'        => 'Close Friends',
	'ZE_VIS_NOBODY'               => 'Nobody',
	'ZE_FRIEND_REQUEST_NOT_AUTHORIZED' => 'You are not authorized to send friend requests.',
	'ZE_FRIEND_REQUEST_UNCHANGED'      => 'No friend request was changed.',

));
