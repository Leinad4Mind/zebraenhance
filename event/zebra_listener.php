<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2013-2026 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class zebra_listener implements EventSubscriberInterface
{
	/** @var \anavaro\zebraenhance\service\relationship_manager */
	protected $relationships;

	/** @var \phpbb\user_loader */
	protected $user_loader;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\controller\helper */
	protected $controller_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $users_table;

	public function __construct(
		\anavaro\zebraenhance\service\relationship_manager $relationships,
		\phpbb\user_loader $user_loader,
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request_interface $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\language\language $language,
		\phpbb\controller\helper $controller_helper,
		$root_path,
		$php_ext,
		$users_table
	)
	{
		$this->relationships = $relationships;
		$this->user_loader = $user_loader;
		$this->auth = $auth;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->language = $language;
		$this->controller_helper = $controller_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->users_table = $users_table;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'                => 'load_language_on_setup',
			'core.ucp_add_zebra'             => 'zebra_confirm_add',
			'core.ucp_remove_zebra'          => 'zebra_confirm_remove',
			'core.ucp_display_module_before' => 'module_display',
			'core.delete_user_before'        => 'delete_users',
			'core.memberlist_view_profile'   => 'prepare_friends',
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'anavaro/zebraenhance',
			'lang_set' => 'zebra_enchance',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function zebra_confirm_add($event)
	{
		$mode = $event['mode'];
		$sql_ary = $event['sql_ary'];
		if ($mode === 'friends' && !$this->auth->acl_get('u_ze_use'))
		{
			$event['sql_ary'] = array();
			return;
		}

		$event['sql_ary'] = $this->relationships->process_additions($mode, $sql_ary);
	}

	public function zebra_confirm_remove($event)
	{
		if ($event['mode'] !== 'friends')
		{
			return;
		}

		foreach ($event['user_ids'] as $zebra_id)
		{
			$this->relationships->remove_relationship((int) $this->user->data['user_id'], (int) $zebra_id);
		}

		// The service removed both directions, so keep phpBB's trailing DELETE inert.
		$event['user_ids'] = array(0);
	}

	public function module_display($event)
	{
		if (!$this->is_zebra_friends_module($event) || !$this->auth->acl_get('u_ze_use'))
		{
			return;
		}

		add_form_key('anavaro_zebraenhance');
		$user_id = (int) $this->user->data['user_id'];
		if ($this->request->is_set_post('zebra_profile_acl'))
		{
			if (!check_form_key('anavaro_zebraenhance'))
			{
				trigger_error('FORM_INVALID');
			}

			$visibility = max(0, min(5, $this->request->variable('zebra_profile_acl', 5)));
			$sql = 'UPDATE ' . $this->users_table . '
				SET profile_friend_show = ' . (int) $visibility . '
				WHERE user_id = ' . (int) $user_id;
			$this->db->sql_query($sql);
			$this->user->data['profile_friend_show'] = $visibility;
		}

		$this->template->assign_vars(array(
			'IS_ZEBRA'           => true,
			'ZEBRA_ACL'          => (int) $this->user->data['profile_friend_show'],
			'S_CAN_CLOSE_FRIENDS' => $this->auth->acl_get('u_ze_close_friends'),
		));

		foreach ($this->relationships->get_requests($user_id, true) as $row)
		{
			$requester_id = (int) $row['requester_id'];
			$this->template->assign_block_vars('pending_requests', array(
				'USER_ID'     => $requester_id,
				'USERNAME'    => $row['username'],
				'USER_COLOUR' => $row['user_colour'],
				'U_PROFILE'   => $this->profile_url($requester_id),
				'U_CONFIRM'   => $this->ucp_friend_url('add=' . urlencode(html_entity_decode($row['username'], ENT_COMPAT))),
				'U_CANCEL'    => $this->ucp_friend_url('remove=1&usernames[]=' . $requester_id),
			));
		}

		foreach ($this->relationships->get_requests($user_id, false) as $row)
		{
			$recipient_id = (int) $row['recipient_id'];
			$this->template->assign_block_vars('pending_awaits', array(
				'USER_ID'     => $recipient_id,
				'USERNAME'    => $row['username'],
				'USER_COLOUR' => $row['user_colour'],
				'U_PROFILE'   => $this->profile_url($recipient_id),
				'U_CANCEL'    => $this->ucp_friend_url('remove=1&usernames[]=' . $recipient_id),
			));
		}

		foreach ($this->relationships->get_friends($user_id) as $row)
		{
			$friend_id = (int) $row['zebra_id'];
			$is_close = (bool) $row['bff'];
			$this->template->assign_block_vars('pretty_zebra', array(
				'USER_ID'       => $friend_id,
				'USERNAME'      => $row['username'],
				'USER_COLOUR'   => $row['user_colour'],
				'U_PROFILE'     => $this->profile_url($friend_id),
				'U_CANCEL'      => $this->ucp_friend_url('remove=1&usernames[]=' . $friend_id),
				'U_CLOSE_ADD'   => $this->controller_helper->route('anavaro_zebraenhance_close_friend', array(
					'userid' => $friend_id,
					'state'  => 1,
				)),
				'U_CLOSE_REMOVE' => $this->controller_helper->route('anavaro_zebraenhance_close_friend', array(
					'userid' => $friend_id,
					'state'  => 0,
				)),
				'S_CLOSE'       => $is_close,
				'L_CLOSE_ACTION' => $this->language->lang($is_close ? 'ZE_REMOVE_CLOSE_FRIEND' : 'ZE_ADD_CLOSE_FRIEND'),
			));
		}
	}

	public function delete_users($event)
	{
		$this->relationships->delete_user_data($event['user_ids']);
	}

	public function prepare_friends($event)
	{
		$member = $event['member'];
		$owner_id = (int) $member['user_id'];
		$viewer_id = (int) $this->user->data['user_id'];
		$override = $this->auth->acl_get('a_user') || $this->auth->acl_get('m_ze_view_private_friendlists');
		$can_view = $this->relationships->can_view_friend_list(
			$owner_id,
			$viewer_id,
			(int) $member['profile_friend_show'],
			$override,
			(bool) $this->user->data['is_registered']
		);

		$this->template->assign_vars(array(
			'FRIENDLIST'              => true,
			'FRIENDLIST_ERROR_ACCESS' => !$can_view,
		));
		if (!$can_view)
		{
			return;
		}

		$friends = $this->relationships->get_friends($owner_id, 100);
		if (!$friends)
		{
			return;
		}

		$user_ids = array_map(function ($row)
		{
			return (int) $row['zebra_id'];
		}, $friends);
		$this->user_loader->load_users($user_ids);
		foreach ($friends as $row)
		{
			$friend_id = (int) $row['zebra_id'];
			$this->template->assign_block_vars('zebra_friendslist', array(
				'USER_ID'     => $friend_id,
				'USERNAME'    => $row['username'],
				'USER_COLOUR' => $row['user_colour'],
				'U_PROFILE'   => $this->profile_url($friend_id),
				'USER_AVATAR' => $this->user_loader->get_avatar($friend_id, false, true),
			));
		}
	}

	protected function is_zebra_friends_module($event)
	{
		if ($event['mode'] !== 'friends')
		{
			return false;
		}

		$id = $event['id'];
		$normalized_id = is_numeric($id) ? (int) $id : 'ucp_' . preg_replace('#^ucp_#', '', (string) $id);
		foreach ($event['module']->module_ary as $module)
		{
			if ($module['name'] !== 'ucp_zebra' || $module['mode'] !== 'friends')
			{
				continue;
			}

			return is_int($normalized_id)
				? (int) $module['id'] === $normalized_id
				: $module['name'] === $normalized_id;
		}

		return false;
	}

	protected function profile_url($user_id)
	{
		return append_sid($this->root_path . 'memberlist.' . $this->php_ext, 'mode=viewprofile&u=' . (int) $user_id);
	}

	protected function ucp_friend_url($parameters)
	{
		return append_sid($this->root_path . 'ucp.' . $this->php_ext, 'i=ucp_zebra&mode=friends&' . $parameters);
	}
}
