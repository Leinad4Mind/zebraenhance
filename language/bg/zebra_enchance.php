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
	'FRIENDLIST_TITLE'       => 'Приятели',
	'ZE_ACCEPT_REQUEST'      => 'Accept friend request',
	'ZE_DECLINE_REQUEST'     => 'Decline friend request',
	'ZE_DECLINE_BLOCK_REQUEST' => 'Decline and block requester',
	'ZE_CANCEL_REQUEST'      => 'Cancel friend request',
	'ZE_REMOVE_FRIEND'       => 'Remove friend',
	'ZE_ADD_CLOSE_FRIEND'    => 'Add to Close Friends',
	'ZE_REMOVE_CLOSE_FRIEND' => 'Remove from Close Friends',
	'ZE_AJAX_NOT_AUTHORIZED' => 'You are not authorized to manage enhanced friend relationships.',
	'ZE_AJAX_NOT_FRIEND'     => 'That user is not in your friend list.',
	'ZE_AJAX_REQUEST_NOT_FOUND' => 'That friend request no longer exists or does not belong to you.',
	'ZE_REQUESTER_CANNOT_BE_BLOCKED' => 'Administrators and moderators cannot be blocked.',
	'ZE_REQUEST_ACCEPTED'       => 'Friend request accepted.',
	'ZE_FRIEND_REQUEST_CREATED' => 'Friend request sent.',
	'ZE_REQUEST_DECLINED'       => 'Friend request declined.',
	'ZE_REQUEST_DECLINED_BLOCKED' => 'Friend request declined and requester blocked.',
	'ZE_REQUEST_CANCELLED'      => 'Friend request cancelled.',
	'ZE_CONFIRM_DECLINE_REQUEST' => 'Are you sure you want to decline this friend request?',
	'ZE_CONFIRM_DECLINE_BLOCK_REQUEST' => 'Decline this friend request and block the requester?',
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
	'ZE_REQUEST_POLICY'                    => 'Кой може да ви изпраща покани за приятелство?',
	'ZE_REQUEST_POLICY_EXPLAIN'            => 'Това важи само за нови покани. Поканите, които вече сте изпратили, все още могат да бъдат приети.',
	'ZE_REQUEST_POLICY_EVERYONE'           => 'Всички',
	'ZE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Приятели на приятели',
	'ZE_REQUEST_POLICY_NOBODY'             => 'Никой',
	'ZE_REQUEST_MESSAGE'                   => 'Незадължително съобщение',
	'ZE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Добавете кратко лично съобщение (незадължително)',

	'ZE_MUTUAL_FRIENDS'        => 'Mutual friends',
	'ZE_BLOCK_FOE_PM'                      => 'Block private messages from foes',
	'ZE_BLOCK_FOE_PM_EXPLAIN'              => 'When enabled, users on your foe list cannot send you private messages. A group PM from a foe that includes you is rejected for the whole group.',
	'ZE_HIDE_FOE_CONTENT'                  => 'Completely hide content from foes',
	'ZE_HIDE_FOE_CONTENT_EXPLAIN'          => 'Removes foe-authored posts instead of showing a placeholder and hides quotes that can be identified as coming from foes. Older or manually attributed quotes may not always be identifiable.',
	'ZE_PM_RECIPIENTS_BLOCKED'             => 'One or more recipients do not accept private messages from you.',
	'ZE_FRIEND_SUGGESTIONS'         => 'People you may know',
	'ZE_FRIEND_SUGGESTIONS_EXPLAIN' => 'Friends of friends whose privacy and request settings allow a suggestion.',
	'ZE_SEND_FRIEND_REQUEST'        => 'Send friend request',
	'ZE_MUTUAL_FRIEND_COUNT'        => array(
		1 => '%d mutual friend',
		2 => '%d mutual friends',
	),
	'ZE_SEARCH_FRIENDS'             => 'Search friends',
	'ZE_SELECT_REQUEST'             => 'Select at least one friend request.',
	'ZE_SELECT_REQUEST_ITEM'        => 'Select this friend request',
	'ZE_SELECT_ALL'                 => 'Select all',
	'ZE_ACCEPT_SELECTED'            => 'Accept selected',
	'ZE_DECLINE_SELECTED'           => 'Decline selected',
	'ZE_CANCEL_SELECTED'            => 'Cancel selected',
	'ZE_CONFIRM_DECLINE_SELECTED'   => 'Decline the selected friend requests?',
	'ZE_CONFIRM_CANCEL_SELECTED'    => 'Cancel the selected friend requests?',
	'ZE_BULK_REQUESTS_COMPLETED'    => array(
		1 => '%d friend request processed.',
		2 => '%d friend requests processed.',
	),
	'ZE_CIRCLES'               => 'Friend circles',
	'ZE_CIRCLES_EXPLAIN'       => 'Create private groups and choose which accepted friends belong to each one.',
	'ZE_CIRCLE_NAME'           => 'Circle name',
	'ZE_CREATE_CIRCLE'         => 'Create circle',
	'ZE_RENAME_CIRCLE'         => 'Rename',
	'ZE_DELETE_CIRCLE'         => 'Delete',
	'ZE_SAVE_CIRCLES'          => 'Save circles',
	'ZE_MEMBERS'               => 'members',
	'ZE_CONFIRM_DELETE_CIRCLE' => 'Delete this circle? Friendships will not be removed.',
	'ZE_CIRCLE_CREATED'        => 'Friend circle created.',
	'ZE_CIRCLE_RENAMED'        => 'Friend circle renamed.',
	'ZE_CIRCLE_DELETED'        => 'Friend circle deleted.',
	'ZE_CIRCLES_SAVED'         => 'Friend circles saved.',
	'ZE_CIRCLE_INVALID'        => 'Enter a valid circle name of up to 50 characters.',
	'ZE_CIRCLE_DUPLICATE'      => 'You already have a circle with that name.',
	'ZE_CIRCLE_LIMIT'          => 'You can create up to 20 friend circles.',
	'ZE_CIRCLE_NOT_FOUND'      => 'That friend circle no longer exists or does not belong to you.',

	'ZE_MUTE_FOE_NOTIFICATIONS'         => 'Mute notifications from foes',
	'ZE_MUTE_FOE_NOTIFICATIONS_EXPLAIN' => 'Suppresses quote, mention, reply, topic, and bookmark notifications created by users on your foe list.',
	'ZE_UCP_FOE_MANAGER'                => 'Enhanced foe management',
	'ZE_UCP_FOE_MANAGER_EXPLAIN'        => 'Search your foes, keep private notes, set temporary blocks, and override each global protection for individual users.',
	'ZE_SEARCH_FOES'                    => 'Search foes',
	'ZE_ADD_FOES'                       => 'Add foes',
	'ZE_GLOBAL_DEFAULTS'                => 'Global defaults',
	'ZE_PROTECTION_PM'                  => 'Private messages',
	'ZE_PROTECTION_CONTENT'             => 'Content',
	'ZE_PROTECTION_NOTIFICATIONS'       => 'Notifications',
	'ZE_FOE_ADDED'                      => 'Recorded',
	'ZE_FOE_EXPIRES'                    => 'Expires',
	'ZE_FOE_DATE_UNKNOWN'               => 'Unknown',
	'ZE_FOE_PERMANENT'                  => 'Permanent',
	'ZE_FOE_NOTE'                       => 'Private note',
	'ZE_FOE_NOTE_PLACEHOLDER'           => 'Optional note visible only to you',
	'ZE_FOE_DURATION'                   => 'Duration',
	'ZE_FOE_DURATION_KEEP'              => 'Keep current expiry',
	'ZE_FOE_24_HOURS'                   => '24 hours',
	'ZE_FOE_7_DAYS'                     => '7 days',
	'ZE_FOE_30_DAYS'                    => '30 days',
	'ZE_POLICY_INHERIT'                 => 'Use global default',
	'ZE_POLICY_ALLOW'                   => 'Allow',
	'ZE_POLICY_BLOCK'                   => 'Block',
	'ZE_SELECT_FOE'                     => 'Select at least one foe.',
	'ZE_SELECT_FOE_ITEM'                => 'Select this foe',
	'ZE_REMOVE_SELECTED_FOES'           => 'Remove selected',
	'ZE_CONFIRM_REMOVE_FOES'            => 'Remove the selected users from your foe list?',
	'ZE_NO_FOES'                        => 'No foes currently defined.',
	'ZE_NO_MATCHING_FOES'               => 'No foes match your search.',
	'ZE_FOE_SAVED'                      => 'Foe settings saved.',
	'ZE_FOE_NOT_FOUND'                  => 'That user is no longer on your foe list.',
	'ZE_FOE_ERROR_TITLE'                => 'Foe management error',
	'ZE_FOES_REMOVED'                   => array(
		1 => '%d foe removed.',
		2 => '%d foes removed.',
	),
));
