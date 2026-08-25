<?php

/**
*
* Zebra Enhance [German]
*
* @package language
* @version $Id$
* @copyright (c) 2026 Leinad4Mind
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* German translation by franki (http://dieahnen.de/ahnenforum/)
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
	'UCP_ZEBRA_PENDING_IN'					=> 'Warten auf Bestätigung',
	'UCP_ZEBRA_PENDING_IN_EXP'				=> 'Liste mit Anfragen warten auf Deine Zustimmung.',

	'UCP_ZEBRA_PENDING_OUT'					=> 'Noch zu bestätigen',
	'UCP_ZEBRA_PENDING_OUT_EXP'				=> 'Liste mit Deinen Anfragen warten auf Bestätigung.',

	'UCP_ZEBRA_PENDING_NONE'				=> 'Keine anstehenden Anfragen',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=> 'Bist du sicher, dass Du die Freundschaftsanfrage abbrechen möchtest?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'		=> 'Freundschaftsanfrage wurde abgelehnt!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'			=> 'Benachrichtigung bei neuen Freundschaftsanfragen',
	'NOTIFICATION_ZEBRA_ADD'				=> '%1$s schickt dir eine Freundschaftsanfrage!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'		=> 'Bestätigung für Freundschaftsanfrage',
	'NOTIFICATION_ZEBRA_CONFIRM'			=> '%1$s hat deine Freundschaftsanfrage bestätigt!',

	'FRINEDLIST_TITLE'						=> 'Freundesliste',

	'NOT_ENEMY'								=> 'Alle außer Feinde',
	'SPECIAL_FRIENDS'						=> 'Besondere Freunde',

	'ZEBRAENHANCE_FRIENDLIST'							=> 'Freundesliste',
	'ZEBRAENHANCE_FRIENDLIST_EXPLAIN'					=> 'Wer kann deine Freundeliste sehen?',

	'FRIENDLIST_ERROR_ACCESS'				=> 'Du hast keine Berechtigung die Benutzer-Freundesliste zu sehen.',

	'FRIENDLIST_TITLE'       => 'Freundesliste',
	'ZEBRAENHANCE_ACCEPT_REQUEST'      => 'Accept friend request',
	'ZEBRAENHANCE_DECLINE_REQUEST'     => 'Decline friend request',
	'ZEBRAENHANCE_DECLINE_BLOCK_REQUEST' => 'Decline and block requester',
	'ZEBRAENHANCE_CANCEL_REQUEST'      => 'Cancel friend request',
	'ZEBRAENHANCE_REMOVE_FRIEND'       => 'Remove friend',
	'ZEBRAENHANCE_ADD_CLOSE_FRIEND'    => 'Add to Close Friends',
	'ZEBRAENHANCE_REMOVE_CLOSE_FRIEND' => 'Remove from Close Friends',
	'ZEBRAENHANCE_AJAX_NOT_AUTHORIZED' => 'You are not authorized to manage enhanced friend relationships.',
	'ZEBRAENHANCE_AJAX_NOT_FRIEND'     => 'That user is not in your friend list.',
	'ZEBRAENHANCE_AJAX_REQUEST_NOT_FOUND' => 'That friend request no longer exists or does not belong to you.',
	'ZEBRAENHANCE_REQUESTER_CANNOT_BE_BLOCKED' => 'Administrators and moderators cannot be blocked.',
	'ZEBRAENHANCE_REQUEST_ACCEPTED'       => 'Friend request accepted.',
	'ZEBRAENHANCE_FRIEND_REQUEST_CREATED' => 'Friend request sent.',
	'ZEBRAENHANCE_REQUEST_DECLINED'       => 'Friend request declined.',
	'ZEBRAENHANCE_REQUEST_DECLINED_BLOCKED' => 'Friend request declined and requester blocked.',
	'ZEBRAENHANCE_REQUEST_CANCELLED'      => 'Friend request cancelled.',
	'ZEBRAENHANCE_CONFIRM_DECLINE_REQUEST' => 'Are you sure you want to decline this friend request?',
	'ZEBRAENHANCE_CONFIRM_DECLINE_BLOCK_REQUEST' => 'Decline this friend request and block the requester?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_REQUEST'  => 'Are you sure you want to cancel this friend request?',
	'ZEBRAENHANCE_ERROR_TITLE'              => 'Friend request error',
	'ZEBRAENHANCE_REQUEST_FAILED'           => 'The request could not be completed.',
	'ZEBRAENHANCE_NO_FRIENDS'               => 'No friends currently defined.',
	'ZEBRAENHANCE_VIS_EVERYONE'             => 'Everyone',
	'ZEBRAENHANCE_VIS_REGISTERED'           => 'Registered users',
	'ZEBRAENHANCE_VIS_NON_FOES'             => 'Everyone except foes',
	'ZEBRAENHANCE_VIS_FRIENDS'              => 'Friends',
	'ZEBRAENHANCE_VIS_CLOSE_FRIENDS'        => 'Close Friends',
	'ZEBRAENHANCE_VIS_NOBODY'               => 'Nobody',
	'ZEBRAENHANCE_FRIEND_REQUEST_NOT_AUTHORIZED' => 'You are not authorized to send friend requests.',
	'ZEBRAENHANCE_FRIEND_REQUEST_UNCHANGED'      => 'No friend request was changed.',
	'ZEBRAENHANCE_REQUEST_POLICY'                    => 'Wer darf dir Freundschaftsanfragen senden?',
	'ZEBRAENHANCE_REQUEST_POLICY_EXPLAIN'            => 'Dies gilt nur für neue Anfragen. Bereits gesendete Anfragen können weiterhin angenommen werden.',
	'ZEBRAENHANCE_REQUEST_POLICY_EVERYONE'           => 'Jeder',
	'ZEBRAENHANCE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Freunde von Freunden',
	'ZEBRAENHANCE_REQUEST_POLICY_NOBODY'             => 'Niemand',
	'ZEBRAENHANCE_REQUEST_MESSAGE'                   => 'Optionale Nachricht',
	'ZEBRAENHANCE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Kurze persönliche Nachricht hinzufügen (optional)',

	'ZEBRAENHANCE_MUTUAL_FRIENDS'        => 'Mutual friends',
	'ZEBRAENHANCE_BLOCK_FOE_PM'                      => 'Block private messages from foes',
	'ZEBRAENHANCE_BLOCK_FOE_PM_EXPLAIN'              => 'When enabled, users on your foe list cannot send you private messages. A group PM from a foe that includes you is rejected for the whole group.',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT'                  => 'Completely hide content from foes',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT_EXPLAIN'          => 'Removes foe-authored posts instead of showing a placeholder and hides quotes that can be identified as coming from foes. Older or manually attributed quotes may not always be identifiable.',
	'ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED'             => 'One or more recipients do not accept private messages from you.',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS'         => 'People you may know',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS_EXPLAIN' => 'Friends of friends whose privacy and request settings allow a suggestion.',
	'ZEBRAENHANCE_SEND_FRIEND_REQUEST'        => 'Send friend request',
	'ZEBRAENHANCE_MUTUAL_FRIEND_COUNT'        => array(
		1 => '%d mutual friend',
		2 => '%d mutual friends',
	),
	'ZEBRAENHANCE_SEARCH_FRIENDS'             => 'Search friends',
	'ZEBRAENHANCE_SELECT_REQUEST'             => 'Select at least one friend request.',
	'ZEBRAENHANCE_SELECT_REQUEST_ITEM'        => 'Select friend request involving %s',
	'ZEBRAENHANCE_SELECT_ALL'                 => 'Select all',
	'ZEBRAENHANCE_ACCEPT_SELECTED'            => 'Accept selected',
	'ZEBRAENHANCE_DECLINE_SELECTED'           => 'Decline selected',
	'ZEBRAENHANCE_CANCEL_SELECTED'            => 'Cancel selected',
	'ZEBRAENHANCE_CONFIRM_DECLINE_SELECTED'   => 'Decline the selected friend requests?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_SELECTED'    => 'Cancel the selected friend requests?',
	'ZEBRAENHANCE_BULK_REQUESTS_COMPLETED'    => array(
		1 => '%d friend request processed.',
		2 => '%d friend requests processed.',
	),
	'ZEBRAENHANCE_CIRCLES'               => 'Friend circles',
	'ZEBRAENHANCE_CIRCLES_EXPLAIN'       => 'Create private groups and choose which accepted friends belong to each one.',
	'ZEBRAENHANCE_CIRCLE_NAME'           => 'Circle name',
	'ZEBRAENHANCE_CREATE_CIRCLE'         => 'Create circle',
	'ZEBRAENHANCE_RENAME_CIRCLE'         => 'Rename',
	'ZEBRAENHANCE_DELETE_CIRCLE'         => 'Delete',
	'ZEBRAENHANCE_SAVE_CIRCLES'          => 'Save circles',
	'ZEBRAENHANCE_MEMBERS'               => 'members',
	'ZEBRAENHANCE_CONFIRM_DELETE_CIRCLE' => 'Delete this circle? Friendships will not be removed.',
	'ZEBRAENHANCE_CIRCLE_CREATED'        => 'Friend circle created.',
	'ZEBRAENHANCE_CIRCLE_RENAMED'        => 'Friend circle renamed.',
	'ZEBRAENHANCE_CIRCLE_DELETED'        => 'Friend circle deleted.',
	'ZEBRAENHANCE_CIRCLES_SAVED'         => 'Friend circles saved.',
	'ZEBRAENHANCE_CIRCLE_INVALID'        => 'Enter a valid circle name of up to 50 characters.',
	'ZEBRAENHANCE_CIRCLE_DUPLICATE'      => 'You already have a circle with that name.',
	'ZEBRAENHANCE_CIRCLE_LIMIT'          => 'You can create up to 20 friend circles.',
	'ZEBRAENHANCE_CIRCLE_NOT_FOUND'      => 'That friend circle no longer exists or does not belong to you.',

	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS'         => 'Mute notifications from foes',
	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS_EXPLAIN' => 'Suppresses quote, mention, reply, topic, and bookmark notifications created by users on your foe list.',
	'ZEBRAENHANCE_UCP_FOE_MANAGER'                => 'Enhanced foe management',
	'ZEBRAENHANCE_UCP_FOE_MANAGER_EXPLAIN'        => 'Search your foes, keep private notes, set temporary blocks, and override each global protection for individual users.',
	'ZEBRAENHANCE_SEARCH_FOES'                    => 'Search foes',
	'ZEBRAENHANCE_ADD_FOES'                       => 'Add foes',
	'ZEBRAENHANCE_GLOBAL_DEFAULTS'                => 'Global defaults',
	'ZEBRAENHANCE_PROTECTION_PM'                  => 'Private messages',
	'ZEBRAENHANCE_PROTECTION_CONTENT'             => 'Content',
	'ZEBRAENHANCE_PROTECTION_NOTIFICATIONS'       => 'Notifications',
	'ZEBRAENHANCE_FOE_ADDED'                      => 'Recorded',
	'ZEBRAENHANCE_FOE_EXPIRES'                    => 'Expires',
	'ZEBRAENHANCE_FOE_DATE_UNKNOWN'               => 'Unknown',
	'ZEBRAENHANCE_FOE_PERMANENT'                  => 'Permanent',
	'ZEBRAENHANCE_FOE_NOTE'                       => 'Private note',
	'ZEBRAENHANCE_FOE_NOTE_PLACEHOLDER'           => 'Optional note visible only to you',
	'ZEBRAENHANCE_FOE_DURATION'                   => 'Duration',
	'ZEBRAENHANCE_FOE_DURATION_KEEP'              => 'Keep current expiry',
	'ZEBRAENHANCE_FOE_24_HOURS'                   => '24 hours',
	'ZEBRAENHANCE_FOE_7_DAYS'                     => '7 days',
	'ZEBRAENHANCE_FOE_30_DAYS'                    => '30 days',
	'ZEBRAENHANCE_POLICY_INHERIT'                 => 'Use global default',
	'ZEBRAENHANCE_POLICY_ALLOW'                   => 'Allow',
	'ZEBRAENHANCE_POLICY_BLOCK'                   => 'Block',
	'ZEBRAENHANCE_SELECT_FOE'                     => 'Select at least one foe.',
	'ZEBRAENHANCE_SELECT_FOE_ITEM'                => 'Select foe %s',
	'ZEBRAENHANCE_REMOVE_SELECTED_FOES'           => 'Remove selected',
	'ZEBRAENHANCE_CONFIRM_REMOVE_FOES'            => 'Remove the selected users from your foe list?',
	'ZEBRAENHANCE_NO_FOES'                        => 'No foes currently defined.',
	'ZEBRAENHANCE_NO_MATCHING_FOES'               => 'No foes match your search.',
	'ZEBRAENHANCE_FOE_SAVED'                      => 'Foe settings saved.',
	'ZEBRAENHANCE_FOE_NOT_FOUND'                  => 'That user is no longer on your foe list.',
	'ZEBRAENHANCE_FOE_ERROR_TITLE'                => 'Foe management error',
	'ZEBRAENHANCE_FOES_REMOVED'                   => array(
		1 => '%d foe removed.',
		2 => '%d foes removed.',
	),
));
