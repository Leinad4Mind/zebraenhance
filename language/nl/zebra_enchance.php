<?php

/**
*
* Zebraenhance [Dutch] Translated by Dutch Translators (https://github.com/dutch-translators)
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Wachten op goedkeuring',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'Lijst met vriendschapsverzoeken wachtend op jouw goedkeuring.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Wachten op goedkeuring',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'Lijst met jou vriendschapsverzoeken wachtend goedkeuring.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'Geen wachtende vriendschapsverzoeken',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'Weet je zeker dat je deze vriendschapsverzoek wilt wijgeren?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> 'Vriendschapsverzoek is geweigerd!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Nieuwe vriendschapsverzoek notificatie',
	'NOTIFICATION_ZEBRA_ADD'	=>	'%1$s heeft je een vriendsschapsverzoek gestuurd!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Bevestiging van je vriendschapsverzoek',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s heeft je vriendschapsverzoek goedgekeurd!',

	'FRINEDLIST_TITLE'	=>	'Vriendenlijst',

	'NOT_ENEMY'	=>	'Alle behalve vijanden',
	'SPECIAL_FRIENDS'	=>	'Speciale Vrienden',

	'ZE_FRIENDLIST'	=>	'Vriendenlijst',
	'ZE_FRIENDLIST_EXPLAIN'	=>	'Wie kan je vriendenlijst zien?',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Je hebt geen toegang tot de vriendenlijst van deze gebruiker.',

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
	'ZE_REQUEST_POLICY'                    => 'Wie mag je vriendschapsverzoeken sturen?',
	'ZE_REQUEST_POLICY_EXPLAIN'            => 'Dit geldt alleen voor nieuwe verzoeken. Verzoeken die je al hebt gestuurd kunnen nog worden geaccepteerd.',
	'ZE_REQUEST_POLICY_EVERYONE'           => 'Iedereen',
	'ZE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Vrienden van vrienden',
	'ZE_REQUEST_POLICY_NOBODY'             => 'Niemand',

));
