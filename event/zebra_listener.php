<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2013-2026 Stanislav Atanasov
* @copyright (c) 2013 Lucifer
* @copyright (c) 2026 Leinad4Mind
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

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var bool */
	protected $profile_context_ready = false;

	/** @var bool */
	protected $profile_hide_native_add = false;

	public function __construct(
		\anavaro\zebraenhance\service\relationship_manager $relationships,
		\phpbb\user_loader $user_loader,
		\phpbb\auth\auth $auth,
		\phpbb\request\request_interface $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\language\language $language,
		\phpbb\controller\helper $controller_helper,
		\phpbb\pagination $pagination,
		$root_path,
		$php_ext
	)
	{
		$this->relationships = $relationships;
		$this->user_loader = $user_loader;
		$this->auth = $auth;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->language = $language;
		$this->controller_helper = $controller_helper;
		$this->pagination = $pagination;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
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
			'core.memberlist_modify_view_profile_template_vars' => 'modify_profile_template_vars',
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
			trigger_error('ZE_FRIEND_REQUEST_NOT_AUTHORIZED');
			return;
		}

		$results = array();
		$event['sql_ary'] = $this->relationships->process_additions($mode, $sql_ary, $results);
		if ($mode === 'friends' && !array_intersect($results, array('created', 'accepted')))
		{
			trigger_error('ZE_FRIEND_REQUEST_UNCHANGED');
		}
	}

	public function zebra_confirm_remove($event)
	{
		if ($event['mode'] !== 'friends')
		{
			return;
		}

		$this->relationships->remove_relationships((int) $this->user->data['user_id'], $event['user_ids']);

		// The service removed both directions, so keep phpBB's trailing DELETE inert.
		$event['user_ids'] = array(0);
	}

	public function module_display($event)
	{
		if (!$this->is_zebra_friends_module($event) || !$this->auth->acl_get('u_ze_use'))
		{
			return;
		}

		add_form_key('anavaro_zebraenhance', '_ZE');
		$user_id = (int) $this->user->data['user_id'];
		if ($this->request->is_set_post('zebra_profile_acl') || $this->request->is_set_post('zebra_request_policy'))
		{
			if (!check_form_key('anavaro_zebraenhance'))
			{
				trigger_error('FORM_INVALID');
			}

			if ($this->request->is_set_post('zebra_profile_acl'))
			{
				$visibility = $this->relationships->set_friend_list_visibility(
					$user_id,
					$this->request->variable('zebra_profile_acl', 5)
				);
				$this->user->data['profile_friend_show'] = $visibility;
			}
			if ($this->request->is_set_post('zebra_request_policy'))
			{
				$policy = $this->relationships->set_request_policy(
					$user_id,
					$this->request->variable('zebra_request_policy', 0)
				);
				$this->user->data['zebra_request_policy'] = $policy;
			}
		}

		$this->template->assign_vars(array(
			'IS_ZEBRA'           => true,
			'ZEBRA_ACL'          => (int) $this->user->data['profile_friend_show'],
			'ZEBRA_REQUEST_POLICY' => isset($this->user->data['zebra_request_policy']) ? (int) $this->user->data['zebra_request_policy'] : 0,
			'S_CAN_CLOSE_FRIENDS' => $this->auth->acl_get('u_ze_close_friends'),
		));

		$page_size = \anavaro\zebraenhance\service\relationship_manager::PAGE_SIZE;
		$incoming_start = max(0, $this->request->variable('ze_in_start', 0));
		$outgoing_start = max(0, $this->request->variable('ze_out_start', 0));
		$friends_start = max(0, $this->request->variable('ze_friend_start', 0));
		$incoming_count = $this->relationships->count_requests($user_id, true);
		$outgoing_count = $this->relationships->count_requests($user_id, false);
		$friends_count = $this->relationships->count_friends($user_id);
		$incoming = $this->relationships->get_requests($user_id, true, $page_size, $incoming_start);
		$outgoing = $this->relationships->get_requests($user_id, false, $page_size, $outgoing_start);
		$friends = $this->relationships->get_friends($user_id, $page_size, $friends_start);
		$base_url = $this->ucp_friend_url('');
		$this->pagination->generate_template_pagination($base_url, 'ze_in_pagination', 'ze_in_start', $incoming_count, $page_size, $incoming_start);
		$this->pagination->generate_template_pagination($base_url, 'ze_out_pagination', 'ze_out_start', $outgoing_count, $page_size, $outgoing_start);
		$this->pagination->generate_template_pagination($base_url, 'ze_friend_pagination', 'ze_friend_start', $friends_count, $page_size, $friends_start);
		$identity_ids = array();
		foreach (array_merge($incoming, $outgoing) as $row)
		{
			$identity_ids[] = (int) ((int) $row['requester_id'] === $user_id ? $row['recipient_id'] : $row['requester_id']);
		}
		foreach ($friends as $row)
		{
			$identity_ids[] = (int) $row['zebra_id'];
		}
		$identity_ids = array_values(array_unique(array_filter($identity_ids)));
		if ($identity_ids)
		{
			$this->user_loader->load_users($identity_ids);
		}

		foreach ($incoming as $row)
		{
			$requester_id = (int) $row['requester_id'];
			$this->template->assign_block_vars('pending_requests', array(
				'USER_ID'       => $requester_id,
				'USERNAME_FULL' => $this->user_loader->get_username($requester_id, 'full'),
				'U_ACCEPT'      => $this->request_action_url((int) $row['request_id'], 'accept'),
				'U_DECLINE'     => $this->request_action_url((int) $row['request_id'], 'decline'),
			));
		}

		foreach ($outgoing as $row)
		{
			$recipient_id = (int) $row['recipient_id'];
			$this->template->assign_block_vars('pending_awaits', array(
				'USER_ID'       => $recipient_id,
				'USERNAME_FULL' => $this->user_loader->get_username($recipient_id, 'full'),
				'U_CANCEL'      => $this->request_action_url((int) $row['request_id'], 'cancel'),
			));
		}

		foreach ($friends as $row)
		{
			$friend_id = (int) $row['zebra_id'];
			$is_close = (bool) $row['bff'];
			$this->template->assign_block_vars('pretty_zebra', array(
				'USER_ID'       => $friend_id,
				'USERNAME_FULL' => $this->user_loader->get_username($friend_id, 'full'),
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
		$viewer_registered = (bool) $this->user->data['is_registered']
			&& (!isset($this->user->data['user_type']) || (int) $this->user->data['user_type'] !== USER_IGNORE);
		$can_use = $this->auth->acl_get('u_ze_use');
		$this->profile_context_ready = true;
		$this->profile_hide_native_add = !$can_use || !$viewer_registered;

		if ($can_use && $viewer_registered && $viewer_id !== $owner_id)
		{
			$request = $this->relationships->get_pending_request_between($viewer_id, $owner_id);
			$friend = isset($event['friend']) && (bool) $event['friend'];
			$foe = isset($event['foe']) && (bool) $event['foe'];
			if ($request || (!$friend && !$foe))
			{
				add_form_key('anavaro_zebraenhance', '_ZE');
				$is_incoming = $request && (int) $request['recipient_id'] === $viewer_id;
				$this->profile_hide_native_add = true;
				$this->template->assign_vars(array(
					'S_ZE_PROFILE_FRIEND_CONTROL' => true,
					'S_ZE_PROFILE_CAN_CREATE'     => !$request,
					'S_ZE_PROFILE_INCOMING'       => $is_incoming,
					'S_ZE_PROFILE_OUTGOING'       => $request && !$is_incoming,
					'U_ZE_PROFILE_CREATE'         => !$request ? $this->controller_helper->route('anavaro_zebraenhance_create_request', array(
						'userid' => $owner_id,
					)) : '',
					'U_ZE_PROFILE_ACCEPT'         => $is_incoming ? $this->request_action_url((int) $request['request_id'], 'accept') : '',
					'U_ZE_PROFILE_DECLINE'        => $is_incoming ? $this->request_action_url((int) $request['request_id'], 'decline') : '',
					'U_ZE_PROFILE_CANCEL'         => $request && !$is_incoming ? $this->request_action_url((int) $request['request_id'], 'cancel') : '',
				));
			}
		}

		if (!$can_use)
		{
			return;
		}

		$override = $this->auth->acl_get('a_user') || $this->auth->acl_get('m_ze_view_private_friendlists');
		$can_view = $this->relationships->can_view_friend_list(
			$owner_id,
			$viewer_id,
			(int) $member['profile_friend_show'],
			$override,
			$viewer_registered
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
				'USER_ID'       => $friend_id,
				'USERNAME_FULL' => $this->user_loader->get_username($friend_id, 'full'),
				'U_PROFILE'     => $this->profile_url($friend_id),
				'USER_AVATAR'   => $this->user_loader->get_avatar($friend_id, false, true),
			));
		}
	}

	public function modify_profile_template_vars($event)
	{
		if (!$this->profile_context_ready || !$this->profile_hide_native_add)
		{
			return;
		}

		$template_ary = $event['template_ary'];
		$template_ary['U_ADD_FRIEND'] = '';
		$event['template_ary'] = $template_ary;
	}

	protected function is_zebra_friends_module($event)
	{
		if ($event['mode'] !== 'friends')
		{
			return false;
		}

		if (!isset($event['module']->module_ary) || !is_array($event['module']->module_ary))
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

			return is_int($normalized_id) ? (int) $module['id'] === $normalized_id : $normalized_id === 'ucp_zebra';
		}

		return false;
	}

	protected function profile_url($user_id)
	{
		return append_sid($this->root_path . 'memberlist.' . $this->php_ext, 'mode=viewprofile&u=' . (int) $user_id);
	}

	protected function ucp_friend_url($parameters)
	{
		$query = 'i=ucp_zebra&mode=friends';
		if ($parameters !== '')
		{
			$query .= '&' . $parameters;
		}

		return append_sid($this->root_path . 'ucp.' . $this->php_ext, $query);
	}

	protected function request_action_url($request_id, $action)
	{
		return $this->controller_helper->route('anavaro_zebraenhance_manage_request', array(
			'requestid' => (int) $request_id,
			'action'    => $action,
		));
	}
}
