<?php

/**
*
* Zebra Enhance [Bulgarian]
*
* @package language
* @version $Id$
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Awaiting confirmation',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'List with requests waiting for your approval.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Pending confirmation',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'List with your requests pending approval.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'No pending requests',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'Are you sure you want to cancel the friend request?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> 'Friend request was cancelled!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'New friend request notification',
	'NOTIFICATION_ZEBRA_ADD'	=>	'%1$s sent you friend request!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Confirmation for friend request',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s confirmed your friend request!',

	'FRIENDLIST_TITLE'	=>	'Friend list',
	'FRINEDLIST_TITLE'	=>	'Friend list', // 1.x compatibility

	'NOT_ENEMY'	=>	'All except foes',
	'SPECIAL_FRIENDS'	=>	'Special friends',

	'ZE_FRIENDLIST'	=>	'Friendlist',
	'ZE_FRIENDLIST_EXPLAIN'	=>	'Who can see your friendlist?',

	'FRIENDLIST_ERROR_ACCESS'	=>	'You do not have access to see user\'s friendlist.',

	'ZE_ACCEPT_REQUEST'      => 'Accept friend request',
	'ZE_DECLINE_REQUEST'     => 'Decline friend request',
	'ZE_CANCEL_REQUEST'      => 'Cancel friend request',
	'ZE_REMOVE_FRIEND'       => 'Remove friend',
	'ZE_ADD_CLOSE_FRIEND'    => 'Add to Close Friends',
	'ZE_REMOVE_CLOSE_FRIEND' => 'Remove from Close Friends',
	'ZE_AJAX_NOT_AUTHORIZED' => 'You are not authorized to change Close Friends.',
	'ZE_AJAX_NOT_FRIEND'     => 'That user is not in your friend list.',

));
