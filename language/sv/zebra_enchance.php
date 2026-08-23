<?php

/**
*
* Zebra Enhance [Swedish]
*
* @package language
* @version $Id$
* @copyright (c) 2026 Leinad4Mind
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* Swedish translation by Holger (http://www.maskinisten.net)
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Väntar på bekräftelse',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'En lista med förfrågningar som väntar på att bli bekräftade av dig.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Dina öppna förfrågningar',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'En lista med dina förfrågningar som måste godkännas av andra.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'Inga öppna förfrågningar',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'Är du säker på att du vill avbryta förfrågningen?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> 'Förfrågningen avbröts!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Nytt meddelande om vänförfrågning',
	'NOTIFICATION_ZEBRA_ADD'	=>	'%1$s skickade en vänförfrågning!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Bekräftelse av vänförfrågning',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s bekräftade din vänförfrågning!',

	'FRINEDLIST_TITLE'	=>	'Vänlista',

	'NOT_ENEMY'	=>	'Alla utom ignorerade',
	'SPECIAL_FRIENDS'	=>	'Speciella vänner',

	'ZE_FRIENDLIST'	=>	'Vänlista',
	'ZE_FRIENDLIST_EXPLAIN'	=>	'Vem kan se din vänlista?',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Du är ej behörig att se denna vänlista.',

	'FRIENDLIST_TITLE'       => 'Friend list',
	'ZE_ACCEPT_REQUEST'      => 'Accept friend request',
	'ZE_DECLINE_REQUEST'     => 'Decline friend request',
	'ZE_CANCEL_REQUEST'      => 'Cancel friend request',
	'ZE_REMOVE_FRIEND'       => 'Remove friend',
	'ZE_ADD_CLOSE_FRIEND'    => 'Add to Close Friends',
	'ZE_REMOVE_CLOSE_FRIEND' => 'Remove from Close Friends',
	'ZE_AJAX_NOT_AUTHORIZED' => 'You are not authorized to manage enhanced friend relationships.',
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
	'ZE_REQUEST_POLICY'                    => 'Vem får skicka vänförfrågningar till dig?',
	'ZE_REQUEST_POLICY_EXPLAIN'            => 'Detta gäller bara nya förfrågningar. Förfrågningar som du redan har skickat kan fortfarande accepteras.',
	'ZE_REQUEST_POLICY_EVERYONE'           => 'Alla',
	'ZE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Vänners vänner',
	'ZE_REQUEST_POLICY_NOBODY'             => 'Ingen',
	'ZE_REQUEST_MESSAGE'                   => 'Valfritt meddelande',
	'ZE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Lägg till ett kort personligt meddelande (valfritt)',

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

));
