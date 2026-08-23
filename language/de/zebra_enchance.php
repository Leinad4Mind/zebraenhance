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

	'ZE_FRIENDLIST'							=> 'Freundesliste',
	'ZE_FRIENDLIST_EXPLAIN'					=> 'Wer kann deine Freundeliste sehen?',

	'FRIENDLIST_ERROR_ACCESS'				=> 'Du hast keine Berechtigung die Benutzer-Freundesliste zu sehen.',

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
	'ZE_REQUEST_POLICY'                    => 'Wer darf dir Freundschaftsanfragen senden?',
	'ZE_REQUEST_POLICY_EXPLAIN'            => 'Dies gilt nur für neue Anfragen. Bereits gesendete Anfragen können weiterhin angenommen werden.',
	'ZE_REQUEST_POLICY_EVERYONE'           => 'Jeder',
	'ZE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Freunde von Freunden',
	'ZE_REQUEST_POLICY_NOBODY'             => 'Niemand',
	'ZE_REQUEST_MESSAGE'                   => 'Optionale Nachricht',
	'ZE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Kurze persönliche Nachricht hinzufügen (optional)',

));
