<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2013-2026 Stanislav Atanasov
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\service;

class relationship_manager
{
	const REQUEST_NOTIFICATION = 'anavaro.zebraenhance.notification.zebraadd';
	const CONFIRM_NOTIFICATION = 'anavaro.zebraenhance.notification.zebraconfirm';
	const EVENT_REQUEST_CREATED = 'anavaro.zebraenhance.friend_request_created';
	const EVENT_REQUEST_ACCEPTED = 'anavaro.zebraenhance.friend_request_accepted';
	const EVENT_REQUEST_DECLINED = 'anavaro.zebraenhance.friend_request_declined';
	const EVENT_REQUEST_CANCELLED = 'anavaro.zebraenhance.friend_request_cancelled';
	const EVENT_FRIENDSHIP_REMOVED = 'anavaro.zebraenhance.friendship_removed';
	const EVENT_CLOSE_FRIEND_CHANGED = 'anavaro.zebraenhance.close_friend_changed';
	const EVENT_VISIBILITY_CHANGED = 'anavaro.zebraenhance.friend_list_visibility_changed';
	const EVENT_CIRCLE_CREATED = 'anavaro.zebraenhance.circle_created';
	const EVENT_CIRCLE_RENAMED = 'anavaro.zebraenhance.circle_renamed';
	const EVENT_CIRCLE_DELETED = 'anavaro.zebraenhance.circle_deleted';
	const EVENT_FRIEND_CIRCLES_CHANGED = 'anavaro.zebraenhance.friend_circles_changed';
	const PAGE_SIZE = 25;
	const MAX_CIRCLES = 20;
	const MAX_CIRCLE_NAME_LENGTH = 50;
	const MAX_BULK_REQUESTS = 100;
	const MAX_FRIEND_SEARCH_LENGTH = 100;
	const ACP_REPORT_PAGE_SIZE = 50;
	const DEFAULT_MAX_PENDING_REQUESTS = 100;
	const REQUEST_POLICY_EVERYONE = 0;
	const REQUEST_POLICY_FRIENDS_OF_FRIENDS = 1;
	const REQUEST_POLICY_NOBODY = 2;
	const POLICY_INHERIT = 0;
	const POLICY_ALLOW = 1;
	const POLICY_BLOCK = 2;
	const FOE_DURATION_KEEP = -1;
	const FOE_DURATION_PERMANENT = 0;
	const MAX_FOE_NOTE_LENGTH = 255;
	const MAX_FOE_SEARCH_LENGTH = 100;
	const MAX_BULK_FOES = 100;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\tools\tools_interface */
	protected $db_tools;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var string */
	protected $requests_table;

	/** @var string */
	protected $cooldowns_table;

	/** @var string */
	protected $legacy_requests_table;

	/** @var string */
	protected $zebra_table;

	/** @var string */
	protected $circles_table;

	/** @var string */
	protected $circle_members_table;

	/** @var string */
	protected $users_table;

	/** @var string */
	protected $user_group_table;

	/** @var string */
	protected $notifications_table;

	/** @var string */
	protected $notification_emails_table;

	/** @var string */
	protected $notification_types_table;

	/** @var string */
	protected $foe_settings_table;

	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\db\tools\tools_interface $db_tools,
		\phpbb\auth\auth $auth,
		\phpbb\notification\manager $notification_manager,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\config\config $config,
		$requests_table,
		$cooldowns_table,
		$legacy_requests_table,
		$zebra_table,
		$circles_table,
		$circle_members_table,
		$users_table,
		$user_group_table,
		$notifications_table,
		$notification_emails_table,
		$notification_types_table,
		$foe_settings_table
	)
	{
		$this->db = $db;
		$this->db_tools = $db_tools;
		$this->auth = $auth;
		$this->notification_manager = $notification_manager;
		$this->dispatcher = $dispatcher;
		$this->config = $config;
		$this->requests_table = $requests_table;
		$this->cooldowns_table = $cooldowns_table;
		$this->legacy_requests_table = $legacy_requests_table;
		$this->zebra_table = $zebra_table;
		$this->circles_table = $circles_table;
		$this->circle_members_table = $circle_members_table;
		$this->users_table = $users_table;
		$this->user_group_table = $user_group_table;
		$this->notifications_table = $notifications_table;
		$this->notification_emails_table = $notification_emails_table;
		$this->notification_types_table = $notification_types_table;
		$this->foe_settings_table = $foe_settings_table;
	}

	/**
	 * Check whether the administrator made an enhanced foe capability available.
	 *
	 * @param string $feature Empty for the master switch
	 */
	public function foe_feature_enabled($feature = '')
	{
		if (empty($this->config['ze_foes_enhancement']))
		{
			return false;
		}
		if ($feature === '')
		{
			return true;
		}

		$features = array(
			'pm'            => 'ze_foe_pm',
			'content'       => 'ze_foe_content',
			'notifications' => 'ze_foe_notifications',
			'temporary'     => 'ze_foe_temporary',
			'notes'         => 'ze_foe_notes',
			'exceptions'    => 'ze_foe_exceptions',
		);

		return isset($features[$feature]) && !empty($this->config[$features[$feature]]);
	}

	/**
	 * Intercept rows prepared by phpBB's UCP Zebra module.
	 *
	 * Friend rows are converted into requests. Foe rows are returned to phpBB
	 * after pending requests between the users have been cancelled.
	 *
	 * @param string $mode
	 * @param array  $rows
	 * @param array  $results Outcomes for intercepted friend requests
	 * @return array Rows phpBB should continue inserting
	 */
	public function process_additions($mode, array $rows, ?array &$results = null, $request_message = '')
	{
		$results = array();
		if ($mode === 'friends')
		{
			foreach ($rows as $row)
			{
				$results[] = $this->request_friendship((int) $row['user_id'], (int) $row['zebra_id'], $request_message);
			}

			return array();
		}

		if ($mode === 'foes')
		{
			$relationships = array();
			foreach ($rows as $row)
			{
				$relationships[(int) $row['user_id']][] = (int) $row['zebra_id'];
			}
			foreach ($relationships as $user_id => $zebra_ids)
			{
				$this->remove_relationships_with_reason($user_id, $zebra_ids, 'foe');
				$this->register_foe_settings($user_id, $zebra_ids);
			}
		}

		return $rows;
	}

	/**
	 * Create a request or accept the reverse request.
	 *
	 * @return string created, accepted, ignored, blocked, restricted, cooldown, or limited
	 */
	public function request_friendship($requester_id, $recipient_id, $request_message = '')
	{
		$requester_id = (int) $requester_id;
		$recipient_id = (int) $recipient_id;
		$request_message = $this->normalize_request_message($request_message);
		if (!$requester_id || !$recipient_id || $requester_id === $recipient_id)
		{
			return 'ignored';
		}
		if (!$this->can_receive_friend_request($recipient_id))
		{
			return 'ignored';
		}

		if ($this->are_friends($requester_id, $recipient_id))
		{
			return 'ignored';
		}

		$request = $this->get_request_between($requester_id, $recipient_id);
		if ($request)
		{
			if ((int) $request['requester_id'] === $requester_id)
			{
				return 'ignored';
			}

			return $this->accept_request($request, $requester_id) ? 'accepted' : 'ignored';
		}

		// Do not reveal that the recipient has blocked the requester.
		if ($this->is_foe($requester_id, $recipient_id) || $this->is_foe($recipient_id, $requester_id))
		{
			return 'blocked';
		}
		if (!$this->request_policy_allows($requester_id, $recipient_id))
		{
			return 'restricted';
		}
		if ($this->is_request_on_cooldown($requester_id, $recipient_id))
		{
			return 'cooldown';
		}
		$max_pending = $this->max_pending_requests();
		if ($max_pending > 0 && ($this->count_pending_requests($requester_id, false) >= $max_pending
			|| $this->count_pending_requests($recipient_id, true) >= $max_pending))
		{
			return 'limited';
		}

		$sql_ary = array(
			'requester_id' => $requester_id,
			'recipient_id' => $recipient_id,
			'user_low'     => min($requester_id, $recipient_id),
			'user_high'    => max($requester_id, $recipient_id),
			'request_time' => time(),
			'request_message' => $request_message,
		);
		$this->db->sql_return_on_error(true);
		$result = $this->db->sql_query('INSERT INTO ' . $this->requests_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
		$sql_error = $result === false ? $this->db->get_sql_error_returned() : array();
		$this->db->sql_return_on_error(false);

		// A concurrent reverse request may have won the unique user-pair key.
		if ($result === false)
		{
			if (!$this->is_duplicate_key_error($sql_error))
			{
				throw new \RuntimeException('Unable to create the friend request.');
			}

			$request = $this->get_request_between($requester_id, $recipient_id);
			if ($request && (int) $request['requester_id'] === $recipient_id)
			{
				return $this->accept_request($request, $requester_id) ? 'accepted' : 'ignored';
			}

			return 'ignored';
		}

		$request_id = (int) $this->db->sql_nextid();
		$this->notification_manager->add_notifications(self::REQUEST_NOTIFICATION, array(
			'request_id'  => $request_id,
			'requester_id' => $requester_id,
			'request_message' => $request_message,
			'user_id'      => array($recipient_id => 'notification.method.board'),
		));
		$request = $sql_ary;
		$request['request_id'] = $request_id;
		$this->dispatch_request_event(self::EVENT_REQUEST_CREATED, $request, $requester_id);

		return 'created';
	}

	protected function is_duplicate_key_error(array $error)
	{
		$code = isset($error['code']) ? (string) $error['code'] : '';
		if (in_array($code, array('19', '1062', '23000', '23505', '2601', '2627'), true))
		{
			return true;
		}

		$message = isset($error['message']) ? $error['message'] : '';
		return (bool) preg_match('#(?:duplicate|unique constraint|unique violation)#i', $message);
	}

	/**
	 * Remove a friendship or pending request in both directions.
	 */
	public function remove_relationship($user_id, $zebra_id)
	{
		$this->remove_relationships($user_id, array($zebra_id));
	}

	/**
	 * Remove friendships and requests for several users in one transaction.
	 */
	public function remove_relationships($user_id, array $zebra_ids)
	{
		$this->remove_relationships_with_reason($user_id, $zebra_ids, 'relationship_removed');
	}

	protected function remove_relationships_with_reason($user_id, array $zebra_ids, $reason)
	{
		$user_id = (int) $user_id;
		$zebra_ids = array_values(array_unique(array_filter(array_map('intval', $zebra_ids), function ($zebra_id) use ($user_id)
		{
			return $zebra_id && $zebra_id !== $user_id;
		})));
		if (!$user_id || !$zebra_ids)
		{
			return;
		}

		$this->db->sql_transaction('begin');
		try
		{
			$sql = 'SELECT request_id, requester_id, recipient_id, request_time, request_message
				FROM ' . $this->requests_table . '
				WHERE (requester_id = ' . (int) $user_id . '
						AND ' . $this->db->sql_in_set('recipient_id', $zebra_ids) . ')
					OR (recipient_id = ' . (int) $user_id . '
						AND ' . $this->db->sql_in_set('requester_id', $zebra_ids) . ')';
			$result = $this->db->sql_query($sql);
			$requests = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$requests[] = $row;
			}
			$this->db->sql_freeresult($result);

			$sql = 'SELECT user_id, zebra_id
				FROM ' . $this->zebra_table . '
				WHERE friend = 1
					AND foe = 0
					AND ((user_id = ' . (int) $user_id . '
							AND ' . $this->db->sql_in_set('zebra_id', $zebra_ids) . ')
						OR (zebra_id = ' . (int) $user_id . '
							AND ' . $this->db->sql_in_set('user_id', $zebra_ids) . '))';
			$result = $this->db->sql_query($sql);
			$removed_friend_ids = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$friend_id = (int) $row['user_id'] === $user_id ? (int) $row['zebra_id'] : (int) $row['user_id'];
				$removed_friend_ids[$friend_id] = true;
			}
			$this->db->sql_freeresult($result);

			$sql = 'DELETE FROM ' . $this->zebra_table . '
				WHERE friend = 1
					AND foe = 0
					AND ((user_id = ' . (int) $user_id . '
							AND ' . $this->db->sql_in_set('zebra_id', $zebra_ids) . ')
						OR (zebra_id = ' . (int) $user_id . '
							AND ' . $this->db->sql_in_set('user_id', $zebra_ids) . '))';
			$this->db->sql_query($sql);
			foreach (array_keys($removed_friend_ids) as $friend_id)
			{
				$this->delete_circle_membership_between($user_id, (int) $friend_id);
			}
			$this->delete_request_rows($requests);
			$sql = 'DELETE FROM ' . $this->legacy_requests_table . '
				WHERE (user_id = ' . (int) $user_id . '
						AND ' . $this->db->sql_in_set('zebra_id', $zebra_ids) . ')
					OR (zebra_id = ' . (int) $user_id . '
						AND ' . $this->db->sql_in_set('user_id', $zebra_ids) . ')';
			$this->db->sql_query($sql);
			$this->mark_changed(array_merge(array($user_id), $zebra_ids));
			$this->db->sql_transaction('commit');
		}
		catch (\Throwable $e)
		{
			$this->db->sql_transaction('rollback');
			throw $e;
		}

		$this->delete_request_notifications($requests);
		foreach ($requests as $request)
		{
			$event_name = (int) $request['requester_id'] === $user_id
				? self::EVENT_REQUEST_CANCELLED
				: self::EVENT_REQUEST_DECLINED;
			$this->dispatch_request_event($event_name, $request, $user_id, $reason);
		}
		foreach (array_keys($removed_friend_ids) as $friend_id)
		{
			$this->dispatch_event(self::EVENT_FRIENDSHIP_REMOVED, array(
				'user_id'   => $user_id,
				'friend_id' => (int) $friend_id,
				'reason'    => (string) $reason,
			));
		}
	}

	/**
	 * Accept, decline, decline and block, or cancel a request using its stable request ID.
	 *
	 * @return string|false The completed action, not_blockable for protected
	 *                      staff, or false when it is not owned by the actor
	 */
	public function manage_request($request_id, $actor_id, $action)
	{
		$request_id = (int) $request_id;
		$actor_id = (int) $actor_id;
		if (!$request_id || !$actor_id || !in_array($action, array('accept', 'decline', 'decline_block', 'cancel'), true))
		{
			return false;
		}

		$request = $this->get_request_by_id($request_id);
		if (!$request)
		{
			return false;
		}

		if ($action === 'accept')
		{
			return (int) $request['recipient_id'] === $actor_id && $this->accept_request($request, $actor_id)
				? 'accepted'
				: false;
		}
		if ($action === 'decline_block')
		{
			if ((int) $request['recipient_id'] !== $actor_id)
			{
				return false;
			}
			if (!$this->can_be_foe((int) $request['requester_id']))
			{
				return 'not_blockable';
			}

			return $this->decline_and_block_request($request, $actor_id);
		}

		$owner_column = $action === 'decline' ? 'recipient_id' : 'requester_id';
		if ((int) $request[$owner_column] !== $actor_id)
		{
			return false;
		}

		$this->db->sql_transaction('begin');
		try
		{
			$this->delete_request_rows(array($request));
			$this->delete_legacy_between((int) $request['requester_id'], (int) $request['recipient_id']);
			if ($action === 'decline')
			{
				$this->replace_request_cooldown((int) $request['requester_id'], (int) $request['recipient_id']);
			}
			$this->db->sql_transaction('commit');
		}
		catch (\Throwable $e)
		{
			$this->db->sql_transaction('rollback');
			throw $e;
		}

		$this->delete_request_notifications(array($request));
		$event_name = $action === 'decline' ? self::EVENT_REQUEST_DECLINED : self::EVENT_REQUEST_CANCELLED;
		$this->dispatch_request_event($event_name, $request, $actor_id, 'user');
		return $action === 'decline' ? 'declined' : 'cancelled';
	}

	/**
	 * Decline an owned request and create a directional phpBB foe relation.
	 */
	protected function decline_and_block_request(array $request, $actor_id)
	{
		$actor_id = (int) $actor_id;
		$requester_id = (int) $request['requester_id'];
		$had_friendship = false;
		$this->db->sql_transaction('begin');
		try
		{
			$sql = 'SELECT 1 AS is_friend
				FROM ' . $this->zebra_table . '
				WHERE friend = 1
					AND foe = 0
					AND ((user_id = ' . (int) $actor_id . ' AND zebra_id = ' . (int) $requester_id . ')
						OR (user_id = ' . (int) $requester_id . ' AND zebra_id = ' . (int) $actor_id . '))';
			$result = $this->db->sql_query_limit($sql, 1);
			$had_friendship = (bool) $this->db->sql_fetchfield('is_friend');
			$this->db->sql_freeresult($result);

			$this->db->sql_query('DELETE FROM ' . $this->zebra_table . '
				WHERE friend = 1
					AND foe = 0
					AND ((user_id = ' . (int) $actor_id . ' AND zebra_id = ' . (int) $requester_id . ')
						OR (user_id = ' . (int) $requester_id . ' AND zebra_id = ' . (int) $actor_id . '))');
			if ($had_friendship)
			{
				$this->delete_circle_membership_between($actor_id, $requester_id);
			}

			// Replace only the actor-owned row; an existing foe owned by the requester is private state.
			$this->db->sql_query('DELETE FROM ' . $this->zebra_table . '
				WHERE user_id = ' . (int) $actor_id . '
					AND zebra_id = ' . (int) $requester_id);
			$this->db->sql_query('INSERT INTO ' . $this->zebra_table . ' ' . $this->db->sql_build_array('INSERT', array(
				'user_id'  => $actor_id,
				'zebra_id' => $requester_id,
				'friend'   => 0,
				'foe'      => 1,
				'bff'      => 0,
			)));
			$this->register_foe_settings($actor_id, array($requester_id));
			$this->delete_request_rows(array($request));
			$this->delete_legacy_between($requester_id, $actor_id);
			$this->delete_request_cooldown($requester_id, $actor_id, true);
			$this->mark_changed(array($actor_id, $requester_id));
			$this->db->sql_transaction('commit');
		}
		catch (\Throwable $e)
		{
			$this->db->sql_transaction('rollback');
			throw $e;
		}

		$this->delete_request_notifications(array($request));
		$this->dispatch_request_event(self::EVENT_REQUEST_DECLINED, $request, $actor_id, 'foe');
		if ($had_friendship)
		{
			$this->dispatch_event(self::EVENT_FRIENDSHIP_REMOVED, array(
				'user_id'   => $actor_id,
				'friend_id' => $requester_id,
				'reason'    => 'foe',
			));
		}

		return 'blocked';
	}

	/**
	 * Match phpBB Zebra's rule that administrators and moderators cannot be foes.
	 */
	protected function can_be_foe($user_id)
	{
		$user_id = (int) $user_id;
		foreach ((array) $this->auth->acl_get_list(array($user_id), array('a_', 'm_')) as $forum_permissions)
		{
			foreach ((array) $forum_permissions as $permission_users)
			{
				if (in_array($user_id, array_map('intval', (array) $permission_users), true))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Apply the normal ownership-checked request action to a bounded ID list.
	 */
	public function manage_requests(array $request_ids, $actor_id, $action)
	{
		$actor_id = (int) $actor_id;
		$request_ids = array_values(array_unique(array_filter(array_map('intval', $request_ids))));
		$total_ids = count($request_ids);
		$request_ids = array_slice($request_ids, 0, self::MAX_BULK_REQUESTS);
		$summary = array(
			'completed' => 0,
			'skipped'   => $total_ids - count($request_ids),
			'results'   => array(),
		);
		if (!$actor_id || !in_array($action, array('accept', 'decline', 'cancel'), true))
		{
			$summary['skipped'] += count($request_ids);
			return $summary;
		}

		foreach ($request_ids as $request_id)
		{
			$result = $this->manage_request($request_id, $actor_id, $action);
			if ($result === false)
			{
				$summary['skipped']++;
				continue;
			}
			$summary['completed']++;
			$summary['results'][$request_id] = $result;
		}

		return $summary;
	}

	public function set_friend_list_visibility($user_id, $visibility)
	{
		$user_id = (int) $user_id;
		$visibility = max(0, min(5, (int) $visibility));
		$sql = 'SELECT profile_friend_show
			FROM ' . $this->users_table . '
			WHERE user_id = ' . (int) $user_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$previous_visibility = $this->db->sql_fetchfield('profile_friend_show');
		$this->db->sql_freeresult($result);
		if ($previous_visibility === false || (int) $previous_visibility === $visibility)
		{
			return $visibility;
		}

		$this->db->sql_query('UPDATE ' . $this->users_table . '
			SET profile_friend_show = ' . (int) $visibility . '
			WHERE user_id = ' . (int) $user_id);
		$this->dispatch_event(self::EVENT_VISIBILITY_CHANGED, array(
			'user_id'        => $user_id,
			'old_visibility' => (int) $previous_visibility,
			'new_visibility' => $visibility,
		));

		return $visibility;
	}

	public function set_request_policy($user_id, $policy)
	{
		$user_id = (int) $user_id;
		$policy = max(self::REQUEST_POLICY_EVERYONE, min(self::REQUEST_POLICY_NOBODY, (int) $policy));
		if ($user_id)
		{
			$this->db->sql_query('UPDATE ' . $this->users_table . '
				SET zebra_request_policy = ' . (int) $policy . '
				WHERE user_id = ' . (int) $user_id);
		}

		return $policy;
	}

	public function set_block_foe_pm($user_id, $enabled)
	{
		$user_id = (int) $user_id;
		$enabled = (bool) $enabled;
		if ($user_id)
		{
			$this->db->sql_query('UPDATE ' . $this->users_table . '
				SET zebra_block_foe_pm = ' . (int) $enabled . '
				WHERE user_id = ' . (int) $user_id);
		}

		return $enabled;
	}

	public function set_hide_foe_content($user_id, $enabled)
	{
		$user_id = (int) $user_id;
		$enabled = (bool) $enabled;
		if ($user_id)
		{
			$this->db->sql_query('UPDATE ' . $this->users_table . '
				SET zebra_hide_foe_content = ' . (int) $enabled . '
				WHERE user_id = ' . (int) $user_id);
		}

		return $enabled;
	}

	public function set_mute_foe_notifications($user_id, $enabled)
	{
		$user_id = (int) $user_id;
		$enabled = (bool) $enabled;
		if ($user_id)
		{
			$this->db->sql_query('UPDATE ' . $this->users_table . '
				SET zebra_mute_foe_notifications = ' . (int) $enabled . '
				WHERE user_id = ' . $user_id);
		}

		return $enabled;
	}

	/**
	 * Record metadata for newly added foes without overwriting existing notes.
	 */
	public function register_foe_settings($owner_id, array $foe_ids, $added_at = null)
	{
		$owner_id = (int) $owner_id;
		$foe_ids = array_values(array_unique(array_filter(array_map('intval', $foe_ids))));
		if (!$owner_id || !$foe_ids)
		{
			return;
		}

		$result = $this->db->sql_query('SELECT foe_id
			FROM ' . $this->foe_settings_table . '
			WHERE owner_id = ' . $owner_id . '
				AND ' . $this->db->sql_in_set('foe_id', $foe_ids));
		$existing = array();
		while (($foe_id = $this->db->sql_fetchfield('foe_id')) !== false)
		{
			$existing[(int) $foe_id] = true;
		}
		$this->db->sql_freeresult($result);

		$rows = array();
		foreach ($foe_ids as $foe_id)
		{
			if (!isset($existing[$foe_id]))
			{
				$rows[] = array(
					'owner_id'   => $owner_id,
					'foe_id'     => $foe_id,
					'added_at'   => $added_at === null ? time() : max(0, (int) $added_at),
					'expires_at' => 0,
				);
			}
		}
		if ($rows)
		{
			$this->db->sql_multi_insert($this->foe_settings_table, $rows);
		}
	}

	public function count_foes($owner_id, $search = '')
	{
		$sql = 'SELECT COUNT(*) AS total
			FROM ' . $this->zebra_table . ' z
			INNER JOIN ' . $this->users_table . ' u ON u.user_id = z.zebra_id
			WHERE z.user_id = ' . (int) $owner_id . '
				AND z.foe = 1' . $this->foe_search_sql($search, 'u');
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		return $count;
	}

	public function get_foes($owner_id, $limit = self::PAGE_SIZE, $offset = 0, $search = '')
	{
		$sql = 'SELECT z.zebra_id, u.username, u.username_clean, u.user_colour,
				fs.added_at, fs.expires_at, fs.foe_note, fs.pm_policy,
				fs.content_policy, fs.notification_policy
			FROM ' . $this->zebra_table . ' z
			INNER JOIN ' . $this->users_table . ' u ON u.user_id = z.zebra_id
			LEFT JOIN ' . $this->foe_settings_table . ' fs
				ON fs.owner_id = z.user_id AND fs.foe_id = z.zebra_id
			WHERE z.user_id = ' . (int) $owner_id . '
				AND z.foe = 1' . $this->foe_search_sql($search, 'u') . '
			ORDER BY u.username_clean ASC, z.zebra_id ASC';
		$result = $this->db->sql_query_limit($sql, max(1, (int) $limit), max(0, (int) $offset));
		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$row['added_at'] = isset($row['added_at']) ? (int) $row['added_at'] : 0;
			$row['expires_at'] = isset($row['expires_at']) ? (int) $row['expires_at'] : 0;
			$row['foe_note'] = isset($row['foe_note']) ? (string) $row['foe_note'] : '';
			foreach (array('pm_policy', 'content_policy', 'notification_policy') as $policy)
			{
				$row[$policy] = isset($row[$policy]) ? (int) $row[$policy] : self::POLICY_INHERIT;
			}
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rows;
	}

	/**
	 * Update a foe's duration, note, and privacy exceptions.
	 */
	public function update_foe($owner_id, $foe_id, $duration, $note, $pm_policy, $content_policy, $notification_policy)
	{
		$owner_id = (int) $owner_id;
		$foe_id = (int) $foe_id;
		if (!$this->foe_feature_enabled() || !$this->is_foe($owner_id, $foe_id))
		{
			return false;
		}
		$this->register_foe_settings($owner_id, array($foe_id));
		$set = array();
		if ($this->foe_feature_enabled('notes'))
		{
			$set['foe_note'] = utf8_substr(trim((string) $note), 0, self::MAX_FOE_NOTE_LENGTH);
		}
		if ($this->foe_feature_enabled('exceptions'))
		{
			if ($this->foe_feature_enabled('pm'))
			{
				$set['pm_policy'] = $this->normalize_foe_policy($pm_policy);
			}
			if ($this->foe_feature_enabled('content'))
			{
				$set['content_policy'] = $this->normalize_foe_policy($content_policy);
			}
			if ($this->foe_feature_enabled('notifications'))
			{
				$set['notification_policy'] = $this->normalize_foe_policy($notification_policy);
			}
		}
		if ($this->foe_feature_enabled('temporary'))
		{
			$duration = (int) $duration;
			$valid_durations = array(self::FOE_DURATION_KEEP, 0, 86400, 604800, 2592000);
			if (!in_array($duration, $valid_durations, true))
			{
				$duration = self::FOE_DURATION_KEEP;
			}
			if ($duration !== self::FOE_DURATION_KEEP)
			{
				$set['expires_at'] = $duration ? time() + $duration : 0;
			}
		}
		if (!$set)
		{
			return true;
		}
		$this->db->sql_query('UPDATE ' . $this->foe_settings_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $set) . '
			WHERE owner_id = ' . $owner_id . '
				AND foe_id = ' . $foe_id);

		return true;
	}

	public function remove_foes($owner_id, array $foe_ids)
	{
		$owner_id = (int) $owner_id;
		$foe_ids = array_slice(array_values(array_unique(array_filter(array_map('intval', $foe_ids)))), 0, self::MAX_BULK_FOES);
		if (!$owner_id || !$foe_ids)
		{
			return 0;
		}
		$sql_ids = $this->db->sql_in_set('zebra_id', $foe_ids);
		$this->db->sql_transaction('begin');
		try
		{
			$this->db->sql_query('UPDATE ' . $this->zebra_table . '
				SET foe = 0
				WHERE user_id = ' . $owner_id . ' AND foe = 1 AND ' . $sql_ids);
			$removed = (int) $this->db->sql_affectedrows();
			$this->db->sql_query('DELETE FROM ' . $this->zebra_table . '
				WHERE user_id = ' . $owner_id . ' AND friend = 0 AND foe = 0 AND ' . $sql_ids);
			$this->db->sql_query('DELETE FROM ' . $this->foe_settings_table . '
				WHERE owner_id = ' . $owner_id . ' AND ' . $this->db->sql_in_set('foe_id', $foe_ids));
			$this->db->sql_transaction('commit');
		}
		catch (\Throwable $e)
		{
			$this->db->sql_transaction('rollback');
			throw $e;
		}

		return $removed;
	}

	public function expire_foes($now = null, $owner_id = 0)
	{
		if (!$this->foe_feature_enabled('temporary'))
		{
			return 0;
		}
		$now = $now === null ? time() : (int) $now;
		$owner_id = (int) $owner_id;
		$sql = 'SELECT owner_id, foe_id
			FROM ' . $this->foe_settings_table . '
			WHERE expires_at > 0 AND expires_at <= ' . $now;
		if ($owner_id)
		{
			$sql .= ' AND owner_id = ' . $owner_id;
		}
		$result = $this->db->sql_query($sql);
		$owners = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$owners[(int) $row['owner_id']][] = (int) $row['foe_id'];
		}
		$this->db->sql_freeresult($result);
		$removed = 0;
		foreach ($owners as $expired_owner_id => $foe_ids)
		{
			foreach (array_chunk($foe_ids, self::MAX_BULK_FOES) as $foe_batch)
			{
				$removed += $this->remove_foes($expired_owner_id, $foe_batch);
			}
		}

		return $removed;
	}

	/**
	 * Return foe IDs for which a protection is currently effective.
	 */
	public function get_effective_foe_ids($owner_id, $protection)
	{
		$columns = $this->foe_policy_columns($protection);
		if (!$columns || !(int) $owner_id || !$this->foe_feature_enabled($protection))
		{
			return array();
		}
		$now = time();
		$sql = 'SELECT z.zebra_id
			FROM ' . $this->zebra_table . ' z
			INNER JOIN ' . $this->users_table . ' u ON u.user_id = z.user_id
			LEFT JOIN ' . $this->foe_settings_table . ' fs
				ON fs.owner_id = z.user_id AND fs.foe_id = z.zebra_id
			WHERE z.user_id = ' . (int) $owner_id . '
				AND z.foe = 1
				' . $this->active_foe_sql('fs', $now) . '
				AND ' . $this->effective_policy_sql('fs.' . $columns[0], 'u.' . $columns[1]);
		$result = $this->db->sql_query($sql);
		$ids = array();
		while (($foe_id = $this->db->sql_fetchfield('zebra_id')) !== false)
		{
			$ids[] = (int) $foe_id;
		}
		$this->db->sql_freeresult($result);

		return $ids;
	}

	public function filter_foe_notification_recipients($actor_id, array $notify_users)
	{
		if (!$this->foe_feature_enabled('notifications'))
		{
			return $notify_users;
		}
		$actor_id = (int) $actor_id;
		$recipient_ids = array_values(array_filter(array_map('intval', array_keys($notify_users))));
		if (!$actor_id || !$recipient_ids)
		{
			return $notify_users;
		}
		$now = time();
		$sql = 'SELECT z.user_id
			FROM ' . $this->zebra_table . ' z
			INNER JOIN ' . $this->users_table . ' u ON u.user_id = z.user_id
			LEFT JOIN ' . $this->foe_settings_table . ' fs
				ON fs.owner_id = z.user_id AND fs.foe_id = z.zebra_id
			WHERE z.zebra_id = ' . $actor_id . '
				AND z.foe = 1
				AND ' . $this->db->sql_in_set('z.user_id', $recipient_ids) . '
				' . $this->active_foe_sql('fs', $now) . '
				AND ' . $this->effective_policy_sql('fs.notification_policy', 'u.zebra_mute_foe_notifications');
		$result = $this->db->sql_query($sql);
		while (($recipient_id = $this->db->sql_fetchfield('user_id')) !== false)
		{
			unset($notify_users[(int) $recipient_id]);
		}
		$this->db->sql_freeresult($result);

		return $notify_users;
	}

	/**
	 * Return foe IDs and their current normalized usernames for quote matching.
	 */
	public function get_foe_identities($owner_id)
	{
		$owner_id = (int) $owner_id;
		if (!$owner_id)
		{
			return array();
		}

		$identities = array();
		$foe_ids = $this->get_effective_foe_ids($owner_id, 'content');
		if (!$foe_ids)
		{
			return array();
		}
		$sql = 'SELECT z.zebra_id, u.username_clean
			FROM ' . $this->zebra_table . ' z
			INNER JOIN ' . $this->users_table . ' u
				ON u.user_id = z.zebra_id
			WHERE z.user_id = ' . $owner_id . '
				AND z.foe = 1
				AND ' . $this->db->sql_in_set('z.zebra_id', $foe_ids);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$identities[(int) $row['zebra_id']] = (string) $row['username_clean'];
		}
		$this->db->sql_freeresult($result);

		return $identities;
	}

	/**
	 * Remove users who opted to reject PMs from foes from a PM address list.
	 *
	 * A group is removed as a whole when any active member would block the
	 * sender. phpBB expands group recipients only after its last filter event,
	 * so partial group delivery would otherwise bypass the preference.
	 */
	public function filter_pm_address_list($sender_id, array $address_list)
	{
		if (!$this->foe_feature_enabled('pm'))
		{
			return $address_list;
		}
		$sender_id = (int) $sender_id;
		if (!$sender_id)
		{
			return $address_list;
		}

		$user_ids = !empty($address_list['u'])
			? array_values(array_filter(array_map('intval', array_keys($address_list['u']))))
			: array();
		if ($user_ids)
		{
			$now = time();
			$sql = 'SELECT z.user_id AS recipient_id
				FROM ' . $this->zebra_table . ' z
				INNER JOIN ' . $this->users_table . ' u
					ON u.user_id = z.user_id
				LEFT JOIN ' . $this->foe_settings_table . ' fs
					ON fs.owner_id = z.user_id AND fs.foe_id = z.zebra_id
				WHERE z.zebra_id = ' . $sender_id . '
					AND z.foe = 1
					' . $this->active_foe_sql('fs', $now) . '
					AND ' . $this->effective_policy_sql('fs.pm_policy', 'u.zebra_block_foe_pm') . '
					AND ' . $this->db->sql_in_set('z.user_id', $user_ids);
			$result = $this->db->sql_query($sql);
			while (($recipient_id = $this->db->sql_fetchfield('recipient_id')) !== false)
			{
				unset($address_list['u'][(int) $recipient_id]);
			}
			$this->db->sql_freeresult($result);
			if (empty($address_list['u']))
			{
				unset($address_list['u']);
			}
		}

		$group_ids = !empty($address_list['g'])
			? array_values(array_filter(array_map('intval', array_keys($address_list['g']))))
			: array();
		if ($group_ids)
		{
			$sql_allow_pm = (!$this->auth->acl_gets('a_', 'm_') && !$this->auth->acl_getf_global('m_'))
				? ' AND u.user_allow_pm = 1'
				: '';
			$sql = 'SELECT DISTINCT ug.group_id AS recipient_group_id
				FROM ' . $this->user_group_table . ' ug
				INNER JOIN ' . $this->zebra_table . ' z
					ON z.user_id = ug.user_id
					AND z.zebra_id = ' . $sender_id . '
					AND z.foe = 1
				INNER JOIN ' . $this->users_table . ' u
					ON u.user_id = ug.user_id
				LEFT JOIN ' . $this->foe_settings_table . ' fs
					ON fs.owner_id = z.user_id AND fs.foe_id = z.zebra_id
				WHERE ug.user_pending = 0
					AND u.user_type IN (' . USER_NORMAL . ', ' . USER_FOUNDER . ')' .
					$sql_allow_pm . '
					' . $this->active_foe_sql('fs', time()) . '
					AND ' . $this->effective_policy_sql('fs.pm_policy', 'u.zebra_block_foe_pm') . '
					AND ' . $this->db->sql_in_set('ug.group_id', $group_ids);
			$result = $this->db->sql_query($sql);
			while (($group_id = $this->db->sql_fetchfield('recipient_group_id')) !== false)
			{
				unset($address_list['g'][(int) $group_id]);
			}
			$this->db->sql_freeresult($result);
			if (empty($address_list['g']))
			{
				unset($address_list['g']);
			}
		}

		return $address_list;
	}

	/**
	 * Set or clear the owner's close-friend flag.
	 *
	 * @return bool False when the target is not currently a friend
	 */
	public function set_close_friend($owner_id, $friend_id, $is_close)
	{
		$owner_id = (int) $owner_id;
		$friend_id = (int) $friend_id;
		if (!$owner_id || !$friend_id || $owner_id === $friend_id)
		{
			return false;
		}
		$is_close = (bool) $is_close;
		$sql = 'SELECT bff
			FROM ' . $this->zebra_table . '
			WHERE user_id = ' . (int) $owner_id . '
				AND zebra_id = ' . (int) $friend_id . '
				AND friend = 1
				AND foe = 0';
		$result = $this->db->sql_query_limit($sql, 1);
		$old_state = $this->db->sql_fetchfield('bff');
		$this->db->sql_freeresult($result);
		if ($old_state === false)
		{
			return false;
		}
		$old_state = (bool) $old_state;
		if ($old_state === $is_close)
		{
			return true;
		}

		$sql = 'UPDATE ' . $this->zebra_table . '
			SET bff = ' . ($is_close ? 1 : 0) . '
			WHERE user_id = ' . (int) $owner_id . '
				AND zebra_id = ' . (int) $friend_id . '
				AND friend = 1
				AND foe = 0';
		$this->db->sql_query($sql);
		$this->mark_changed(array($owner_id));
		$this->dispatch_event(self::EVENT_CLOSE_FRIEND_CHANGED, array(
			'owner_id'  => $owner_id,
			'friend_id' => $friend_id,
			'old_state' => $old_state,
			'new_state' => $is_close,
		));
		return true;
	}

	/**
	 * Create a private friend circle owned by a user.
	 *
	 * @return array|string The created row, or invalid, duplicate, or limit
	 */
	public function create_circle($owner_id, $circle_name)
	{
		$owner_id = (int) $owner_id;
		$names = $this->normalize_circle_name($circle_name);
		if (!$owner_id || !$names)
		{
			return 'invalid';
		}
		if (count($this->get_circles($owner_id)) >= self::MAX_CIRCLES)
		{
			return 'limit';
		}
		if ($this->get_circle_by_clean_name($owner_id, $names['clean']))
		{
			return 'duplicate';
		}

		$sql_ary = array(
			'owner_id'          => $owner_id,
			'circle_name'       => $names['display'],
			'circle_name_clean' => $names['clean'],
			'created_at'        => time(),
		);
		$this->db->sql_return_on_error(true);
		$result = $this->db->sql_query('INSERT INTO ' . $this->circles_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
		$sql_error = $result === false ? $this->db->get_sql_error_returned() : array();
		$this->db->sql_return_on_error(false);
		if ($result === false)
		{
			if ($this->is_duplicate_key_error($sql_error))
			{
				return 'duplicate';
			}
			throw new \RuntimeException('Unable to create the friend circle.');
		}

		$circle = $sql_ary;
		$circle['circle_id'] = (int) $this->db->sql_nextid();
		$circle['member_count'] = 0;
		$this->dispatch_event(self::EVENT_CIRCLE_CREATED, array(
			'owner_id'    => $owner_id,
			'circle_id'   => $circle['circle_id'],
			'circle_name' => $circle['circle_name'],
		));

		return $circle;
	}

	/**
	 * Rename a circle owned by the acting user.
	 *
	 * @return array|string The renamed row, or invalid, duplicate, or not_found
	 */
	public function rename_circle($owner_id, $circle_id, $circle_name)
	{
		$owner_id = (int) $owner_id;
		$circle_id = (int) $circle_id;
		$names = $this->normalize_circle_name($circle_name);
		if (!$owner_id || !$circle_id || !$names)
		{
			return 'invalid';
		}
		$circle = $this->get_owned_circle($owner_id, $circle_id);
		if (!$circle)
		{
			return 'not_found';
		}
		if ((string) $circle['circle_name_clean'] === $names['clean']
			&& (string) $circle['circle_name'] === $names['display'])
		{
			return $circle;
		}
		if ((string) $circle['circle_name_clean'] !== $names['clean']
			&& $this->get_circle_by_clean_name($owner_id, $names['clean']))
		{
			return 'duplicate';
		}

		$this->db->sql_return_on_error(true);
		$result = $this->db->sql_query('UPDATE ' . $this->circles_table . '
			SET ' . $this->db->sql_build_array('UPDATE', array(
				'circle_name'       => $names['display'],
				'circle_name_clean' => $names['clean'],
			)) . '
			WHERE circle_id = ' . (int) $circle_id . '
				AND owner_id = ' . (int) $owner_id);
		$sql_error = $result === false ? $this->db->get_sql_error_returned() : array();
		$this->db->sql_return_on_error(false);
		if ($result === false)
		{
			if ($this->is_duplicate_key_error($sql_error))
			{
				return 'duplicate';
			}
			throw new \RuntimeException('Unable to rename the friend circle.');
		}

		$old_name = (string) $circle['circle_name'];
		$circle['circle_name'] = $names['display'];
		$circle['circle_name_clean'] = $names['clean'];
		$this->dispatch_event(self::EVENT_CIRCLE_RENAMED, array(
			'owner_id' => $owner_id,
			'circle_id' => $circle_id,
			'old_name' => $old_name,
			'new_name' => $names['display'],
		));

		return $circle;
	}

	public function delete_circle($owner_id, $circle_id)
	{
		$owner_id = (int) $owner_id;
		$circle_id = (int) $circle_id;
		$circle = $this->get_owned_circle($owner_id, $circle_id);
		if (!$circle)
		{
			return false;
		}

		$this->db->sql_transaction('begin');
		try
		{
			$this->db->sql_query('DELETE FROM ' . $this->circle_members_table . '
				WHERE circle_id = ' . (int) $circle_id);
			$this->db->sql_query('DELETE FROM ' . $this->circles_table . '
				WHERE circle_id = ' . (int) $circle_id . '
					AND owner_id = ' . (int) $owner_id);
			$this->db->sql_transaction('commit');
		}
		catch (\Throwable $e)
		{
			$this->db->sql_transaction('rollback');
			throw $e;
		}

		$this->dispatch_event(self::EVENT_CIRCLE_DELETED, array(
			'owner_id'    => $owner_id,
			'circle_id'   => $circle_id,
			'circle_name' => (string) $circle['circle_name'],
		));

		return true;
	}

	public function get_circles($owner_id)
	{
		$sql = 'SELECT c.circle_id, c.owner_id, c.circle_name, c.circle_name_clean,
				c.created_at, COUNT(m.friend_id) AS member_count
			FROM ' . $this->circles_table . ' c
			LEFT JOIN ' . $this->circle_members_table . ' m
				ON m.circle_id = c.circle_id
			WHERE c.owner_id = ' . (int) $owner_id . '
			GROUP BY c.circle_id, c.owner_id, c.circle_name, c.circle_name_clean, c.created_at
			ORDER BY c.circle_name_clean ASC';
		$result = $this->db->sql_query($sql);
		$circles = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$row['circle_id'] = (int) $row['circle_id'];
			$row['owner_id'] = (int) $row['owner_id'];
			$row['created_at'] = (int) $row['created_at'];
			$row['member_count'] = (int) $row['member_count'];
			$circles[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $circles;
	}

	public function get_friend_circle_ids($owner_id, $friend_id)
	{
		$memberships = $this->get_circle_memberships($owner_id, array($friend_id));
		return isset($memberships[(int) $friend_id]) ? $memberships[(int) $friend_id] : array();
	}

	public function get_circle_memberships($owner_id, array $friend_ids)
	{
		$friend_ids = array_values(array_unique(array_filter(array_map('intval', $friend_ids))));
		if (!$friend_ids)
		{
			return array();
		}

		$sql = 'SELECT m.circle_id, m.friend_id
			FROM ' . $this->circle_members_table . ' m
			INNER JOIN ' . $this->circles_table . ' c
				ON c.circle_id = m.circle_id
			WHERE c.owner_id = ' . (int) $owner_id . '
				AND ' . $this->db->sql_in_set('m.friend_id', $friend_ids) . '
			ORDER BY m.circle_id ASC';
		$result = $this->db->sql_query($sql);
		$memberships = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$memberships[(int) $row['friend_id']][] = (int) $row['circle_id'];
		}
		$this->db->sql_freeresult($result);

		return $memberships;
	}

	public function set_friend_circles($owner_id, $friend_id, array $circle_ids)
	{
		$owner_id = (int) $owner_id;
		$friend_id = (int) $friend_id;
		$circle_ids = array_values(array_unique(array_filter(array_map('intval', $circle_ids))));
		sort($circle_ids);
		if (!$owner_id || !$friend_id || $owner_id === $friend_id || !$this->are_friends($owner_id, $friend_id))
		{
			return false;
		}

		$owned_ids = $this->get_owned_circle_ids($owner_id);
		if (array_diff($circle_ids, $owned_ids))
		{
			return false;
		}
		$old_ids = $this->get_friend_circle_ids($owner_id, $friend_id);
		sort($old_ids);
		if ($old_ids === $circle_ids)
		{
			return true;
		}

		$this->db->sql_transaction('begin');
		try
		{
			if ($owned_ids)
			{
				$this->db->sql_query('DELETE FROM ' . $this->circle_members_table . '
					WHERE friend_id = ' . (int) $friend_id . '
						AND ' . $this->db->sql_in_set('circle_id', $owned_ids));
			}
			if ($circle_ids)
			{
				$rows = array();
				foreach ($circle_ids as $circle_id)
				{
					$rows[] = array('circle_id' => $circle_id, 'friend_id' => $friend_id);
				}
				$this->db->sql_multi_insert($this->circle_members_table, $rows);
			}
			$this->db->sql_transaction('commit');
		}
		catch (\Throwable $e)
		{
			$this->db->sql_transaction('rollback');
			throw $e;
		}

		$this->dispatch_event(self::EVENT_FRIEND_CIRCLES_CHANGED, array(
			'owner_id'       => $owner_id,
			'friend_id'      => $friend_id,
			'old_circle_ids' => $old_ids,
			'new_circle_ids' => $circle_ids,
		));

		return true;
	}

	public function is_friend_in_circle($owner_id, $friend_id, $circle_id)
	{
		return in_array((int) $circle_id, $this->get_friend_circle_ids($owner_id, $friend_id), true);
	}

	public function get_circle_friend_ids($owner_id, $circle_id)
	{
		$circle = $this->get_owned_circle($owner_id, $circle_id);
		if (!$circle)
		{
			return array();
		}

		$result = $this->db->sql_query('SELECT friend_id
			FROM ' . $this->circle_members_table . '
			WHERE circle_id = ' . (int) $circle_id . '
			ORDER BY friend_id ASC');
		$friend_ids = array();
		while ($friend_id = $this->db->sql_fetchfield('friend_id'))
		{
			$friend_ids[] = (int) $friend_id;
		}
		$this->db->sql_freeresult($result);

		return $friend_ids;
	}

	public function are_friends($user_id, $zebra_id)
	{
		$sql = 'SELECT 1 AS is_friend
			FROM ' . $this->zebra_table . '
			WHERE user_id = ' . (int) $user_id . '
				AND zebra_id = ' . (int) $zebra_id . '
				AND friend = 1
				AND foe = 0';
		$result = $this->db->sql_query_limit($sql, 1);
		$is_friend = (bool) $this->db->sql_fetchfield('is_friend');
		$this->db->sql_freeresult($result);

		return $is_friend;
	}

	public function is_foe($owner_id, $other_id)
	{
		$sql = 'SELECT 1 AS is_foe
			FROM ' . $this->zebra_table . '
			WHERE user_id = ' . (int) $owner_id . '
				AND zebra_id = ' . (int) $other_id . '
				AND foe = 1';
		$result = $this->db->sql_query_limit($sql, 1);
		$is_foe = (bool) $this->db->sql_fetchfield('is_foe');
		$this->db->sql_freeresult($result);

		return $is_foe;
	}

	public function is_close_friend($owner_id, $other_id)
	{
		$sql = 'SELECT 1 AS is_close
			FROM ' . $this->zebra_table . '
			WHERE user_id = ' . (int) $owner_id . '
				AND zebra_id = ' . (int) $other_id . '
				AND friend = 1
				AND foe = 0
				AND bff = 1';
		$result = $this->db->sql_query_limit($sql, 1);
		$is_close = (bool) $this->db->sql_fetchfield('is_close');
		$this->db->sql_freeresult($result);

		return $is_close;
	}

	/**
	 * Evaluate a profile friend-list visibility value (0 through 5).
	 */
	public function can_view_friend_list($owner_id, $viewer_id, $visibility, $override = false, $viewer_registered = null)
	{
		$owner_id = (int) $owner_id;
		$viewer_id = (int) $viewer_id;
		$visibility = max(0, min(5, (int) $visibility));
		if ($override || ($viewer_id && $owner_id === $viewer_id))
		{
			return true;
		}

		if ($visibility === 0)
		{
			return true;
		}
		$is_registered = $viewer_registered === null
			? ($viewer_id && $viewer_id !== ANONYMOUS)
			: (bool) $viewer_registered;
		if (!$is_registered || $visibility === 5)
		{
			return false;
		}
		if ($visibility === 1)
		{
			return true;
		}
		if ($visibility === 2)
		{
			return !$this->is_foe($owner_id, $viewer_id);
		}
		if ($visibility === 3)
		{
			return $this->are_friends($owner_id, $viewer_id);
		}

		return $this->is_close_friend($owner_id, $viewer_id);
	}

	/**
	 * Return friend rows for UCP or profile display.
	 */
	public function get_friends($owner_id, $limit = 0, $offset = 0, $search = '')
	{
		$sql = 'SELECT z.zebra_id, z.bff, u.username, u.user_colour
			FROM ' . $this->zebra_table . ' z
			INNER JOIN ' . $this->users_table . ' u
				ON u.user_id = z.zebra_id
			WHERE z.user_id = ' . (int) $owner_id . '
				AND z.friend = 1
				AND z.foe = 0';
		$sql .= $this->friend_search_sql($search, 'u');
		$sql .= ' ORDER BY u.username_clean ASC';
		$result = $limit ? $this->db->sql_query_limit($sql, (int) $limit, max(0, (int) $offset)) : $this->db->sql_query($sql);
		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rows;
	}

	/**
	 * Return accepted friends present in both users' directional lists.
	 */
	public function get_mutual_friends($first_user_id, $second_user_id, $limit = 12)
	{
		$first_user_id = (int) $first_user_id;
		$second_user_id = (int) $second_user_id;
		$limit = max(1, min(100, (int) $limit));
		if (!$first_user_id || !$second_user_id || $first_user_id === $second_user_id)
		{
			return array();
		}

		$sql = 'SELECT first_friend.zebra_id, u.username, u.user_colour
			FROM ' . $this->zebra_table . ' first_friend
			INNER JOIN ' . $this->zebra_table . ' second_friend
				ON second_friend.zebra_id = first_friend.zebra_id
			INNER JOIN ' . $this->users_table . ' u
				ON u.user_id = first_friend.zebra_id
			WHERE first_friend.user_id = ' . (int) $first_user_id . '
				AND second_friend.user_id = ' . (int) $second_user_id . '
				AND first_friend.zebra_id <> ' . (int) $first_user_id . '
				AND first_friend.zebra_id <> ' . (int) $second_user_id . '
				AND first_friend.friend = 1
				AND first_friend.foe = 0
				AND second_friend.friend = 1
				AND second_friend.foe = 0
			ORDER BY u.username_clean ASC';
		$result = $this->db->sql_query_limit($sql, $limit);
		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rows;
	}

	/**
	 * Suggest visible friends-of-friends who can currently receive a request.
	 */
	public function get_friend_suggestions($viewer_id, $limit = 8)
	{
		$viewer_id = (int) $viewer_id;
		$limit = max(1, min(50, (int) $limit));
		if (!$viewer_id || $viewer_id === ANONYMOUS)
		{
			return array();
		}
		$max_pending = $this->max_pending_requests();
		if ($max_pending > 0 && $this->count_pending_requests($viewer_id, false) >= $max_pending)
		{
			return array();
		}
		$candidate_limit_sql = $max_pending > 0
			? ' AND (SELECT COUNT(*) FROM ' . $this->requests_table . ' candidate_pending
				WHERE candidate_pending.recipient_id = candidate.user_id) < ' . (int) $max_pending
			: '';

		$sql = 'SELECT candidate.user_id, candidate.username, candidate.user_colour,
				candidate.profile_friend_show, candidate.zebra_request_policy,
				COUNT(DISTINCT viewer_friend.zebra_id) AS mutual_count
			FROM ' . $this->zebra_table . ' viewer_friend
			INNER JOIN ' . $this->zebra_table . ' friend_candidate
				ON friend_candidate.user_id = viewer_friend.zebra_id
					AND friend_candidate.friend = 1
					AND friend_candidate.foe = 0
			INNER JOIN ' . $this->users_table . ' candidate
				ON candidate.user_id = friend_candidate.zebra_id
			LEFT JOIN ' . $this->zebra_table . ' viewer_relationship
				ON viewer_relationship.user_id = ' . (int) $viewer_id . '
					AND viewer_relationship.zebra_id = candidate.user_id
			LEFT JOIN ' . $this->zebra_table . ' candidate_relationship
				ON candidate_relationship.user_id = candidate.user_id
					AND candidate_relationship.zebra_id = ' . (int) $viewer_id . '
			LEFT JOIN ' . $this->requests_table . ' pending
				ON (pending.requester_id = ' . (int) $viewer_id . ' AND pending.recipient_id = candidate.user_id)
					OR (pending.recipient_id = ' . (int) $viewer_id . ' AND pending.requester_id = candidate.user_id)
			LEFT JOIN ' . $this->cooldowns_table . ' active_cooldown
				ON active_cooldown.requester_id = ' . (int) $viewer_id . '
					AND active_cooldown.recipient_id = candidate.user_id
					AND active_cooldown.expires_at > ' . time() . '
			WHERE viewer_friend.user_id = ' . (int) $viewer_id . '
				AND viewer_friend.friend = 1
				AND viewer_friend.foe = 0
				AND candidate.user_id <> ' . (int) $viewer_id . '
				AND ' . $this->db->sql_in_set('candidate.user_type', array(USER_NORMAL, USER_FOUNDER)) . '
				AND candidate.zebra_request_policy <> ' . self::REQUEST_POLICY_NOBODY . '
				AND ' . $this->db->sql_in_set('candidate.profile_friend_show', array(0, 1, 2)) . '
				AND viewer_relationship.user_id IS NULL
				AND candidate_relationship.user_id IS NULL
				AND pending.request_id IS NULL
				AND active_cooldown.requester_id IS NULL' . $candidate_limit_sql . '
			GROUP BY candidate.user_id, candidate.username, candidate.user_colour,
				candidate.profile_friend_show, candidate.zebra_request_policy
			ORDER BY mutual_count DESC, candidate.username ASC';
		$result = $this->db->sql_query_limit($sql, max(50, $limit * 5));
		$suggestions = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$candidate_id = (int) $row['user_id'];
			$row['user_id'] = $candidate_id;
			$row['mutual_count'] = (int) $row['mutual_count'];
			$suggestions[] = $row;
			if (count($suggestions) >= $limit)
			{
				break;
			}
		}
		$this->db->sql_freeresult($result);

		return $suggestions;
	}

	public function count_friends($owner_id, $search = '')
	{
		$sql = 'SELECT COUNT(*) AS total
			FROM ' . $this->zebra_table . ' z
			INNER JOIN ' . $this->users_table . ' u
				ON u.user_id = z.zebra_id
			WHERE z.user_id = ' . (int) $owner_id . '
				AND z.friend = 1
				AND z.foe = 0';
		$sql .= $this->friend_search_sql($search, 'u');
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		return $count;
	}

	protected function friend_search_sql($search, $user_alias)
	{
		$search = utf8_clean_string(utf8_substr(trim((string) $search), 0, self::MAX_FRIEND_SEARCH_LENGTH));
		if ($search === '')
		{
			return '';
		}

		return ' AND ' . $user_alias . '.username_clean ' . $this->db->sql_like_expression(
			$this->db->get_any_char() . $search . $this->db->get_any_char()
		);
	}

	protected function foe_search_sql($search, $user_alias)
	{
		$search = utf8_clean_string(utf8_substr(trim((string) $search), 0, self::MAX_FOE_SEARCH_LENGTH));
		if ($search === '')
		{
			return '';
		}

		return ' AND ' . $user_alias . '.username_clean ' . $this->db->sql_like_expression(
			$this->db->get_any_char() . $search . $this->db->get_any_char()
		);
	}

	protected function normalize_foe_policy($policy)
	{
		$policy = (int) $policy;
		return in_array($policy, array(self::POLICY_INHERIT, self::POLICY_ALLOW, self::POLICY_BLOCK), true)
			? $policy
			: self::POLICY_INHERIT;
	}

	protected function foe_policy_columns($protection)
	{
		$columns = array(
			'pm'            => array('pm_policy', 'zebra_block_foe_pm'),
			'content'       => array('content_policy', 'zebra_hide_foe_content'),
			'notifications' => array('notification_policy', 'zebra_mute_foe_notifications'),
		);

		return isset($columns[$protection]) ? $columns[$protection] : false;
	}

	protected function effective_policy_sql($policy_column, $global_column)
	{
		if (!$this->foe_feature_enabled('exceptions'))
		{
			return $global_column . ' = 1';
		}
		return '((' . $policy_column . ' = ' . self::POLICY_BLOCK . ')
			OR ((' . $policy_column . ' IS NULL OR ' . $policy_column . ' = ' . self::POLICY_INHERIT . ')
				AND ' . $global_column . ' = 1))';
	}

	protected function active_foe_sql($settings_alias, $now)
	{
		if (!$this->foe_feature_enabled('temporary'))
		{
			return '';
		}

		return 'AND (' . $settings_alias . '.foe_id IS NULL OR ' . $settings_alias . '.expires_at = 0
			OR ' . $settings_alias . '.expires_at > ' . (int) $now . ')';
	}

	/**
	 * Return requests with the other user's public identity fields.
	 */
	public function get_requests($user_id, $incoming, $limit = 0, $offset = 0)
	{
		$user_column = $incoming ? 'r.requester_id' : 'r.recipient_id';
		$match_column = $incoming ? 'r.recipient_id' : 'r.requester_id';
		$sql = 'SELECT r.request_id, r.requester_id, r.recipient_id, r.request_message,
				u.username, u.user_colour
			FROM ' . $this->requests_table . ' r
			INNER JOIN ' . $this->users_table . ' u
				ON u.user_id = ' . $user_column . '
			WHERE ' . $match_column . ' = ' . (int) $user_id . '
			ORDER BY r.request_time ASC, r.request_id ASC';
		$result = $limit ? $this->db->sql_query_limit($sql, (int) $limit, max(0, (int) $offset)) : $this->db->sql_query($sql);
		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rows;
	}

	public function count_requests($user_id, $incoming)
	{
		$user_column = $incoming ? 'r.requester_id' : 'r.recipient_id';
		$match_column = $incoming ? 'r.recipient_id' : 'r.requester_id';
		$sql = 'SELECT COUNT(*) AS total
			FROM ' . $this->requests_table . ' r
			INNER JOIN ' . $this->users_table . ' u
				ON u.user_id = ' . $user_column . '
			WHERE ' . $match_column . ' = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		return $count;
	}

	/**
	 * Return a board-wide, newest-first pending request report for the ACP.
	 */
	public function get_pending_request_report($limit = self::ACP_REPORT_PAGE_SIZE, $offset = 0)
	{
		$sql = 'SELECT r.request_id, r.requester_id, r.recipient_id,
				r.request_time, r.request_message,
				requester.username AS requester_username,
				requester.user_colour AS requester_colour,
				recipient.username AS recipient_username,
				recipient.user_colour AS recipient_colour
			FROM ' . $this->requests_table . ' r
			INNER JOIN ' . $this->users_table . ' requester
				ON requester.user_id = r.requester_id
			INNER JOIN ' . $this->users_table . ' recipient
				ON recipient.user_id = r.recipient_id
			ORDER BY r.request_time DESC, r.request_id DESC';
		$result = $this->db->sql_query_limit($sql, max(1, (int) $limit), max(0, (int) $offset));
		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rows;
	}

	public function count_pending_request_report()
	{
		$sql = 'SELECT COUNT(*) AS total
			FROM ' . $this->requests_table . ' r
			INNER JOIN ' . $this->users_table . ' requester
				ON requester.user_id = r.requester_id
			INNER JOIN ' . $this->users_table . ' recipient
				ON recipient.user_id = r.recipient_id';
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		return $count;
	}

	/**
	 * Return the stable pending request between two profile users.
	 */
	public function get_pending_request_between($user_id, $zebra_id)
	{
		$request = $this->get_request_between($user_id, $zebra_id);
		if (!$request)
		{
			return false;
		}

		return array(
			'request_id'   => (int) $request['request_id'],
			'requester_id' => (int) $request['requester_id'],
			'recipient_id' => (int) $request['recipient_id'],
			'request_time' => (int) $request['request_time'],
			'request_message' => isset($request['request_message']) ? (string) $request['request_message'] : '',
		);
	}

	protected function normalize_request_message($request_message)
	{
		$request_message = trim((string) $request_message);
		return utf8_substr($request_message, 0, 255);
	}

	protected function normalize_circle_name($circle_name)
	{
		$circle_name = trim(utf8_substr(trim((string) $circle_name), 0, self::MAX_CIRCLE_NAME_LENGTH));
		$circle_name_clean = utf8_substr(utf8_clean_string($circle_name), 0, self::MAX_CIRCLE_NAME_LENGTH);
		if ($circle_name === '' || $circle_name_clean === '')
		{
			return false;
		}

		return array(
			'display' => $circle_name,
			'clean'   => $circle_name_clean,
		);
	}

	protected function get_circle_by_clean_name($owner_id, $circle_name_clean)
	{
		$sql = 'SELECT circle_id, owner_id, circle_name, circle_name_clean, created_at
			FROM ' . $this->circles_table . '
			WHERE owner_id = ' . (int) $owner_id . "
				AND circle_name_clean = '" . $this->db->sql_escape($circle_name_clean) . "'";
		$result = $this->db->sql_query_limit($sql, 1);
		$circle = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $circle ?: false;
	}

	protected function get_owned_circle($owner_id, $circle_id)
	{
		$sql = 'SELECT circle_id, owner_id, circle_name, circle_name_clean, created_at
			FROM ' . $this->circles_table . '
			WHERE circle_id = ' . (int) $circle_id . '
				AND owner_id = ' . (int) $owner_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$circle = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $circle ?: false;
	}

	protected function get_owned_circle_ids($owner_id)
	{
		$result = $this->db->sql_query('SELECT circle_id
			FROM ' . $this->circles_table . '
			WHERE owner_id = ' . (int) $owner_id);
		$circle_ids = array();
		while (($circle_id = $this->db->sql_fetchfield('circle_id')) !== false)
		{
			$circle_ids[] = (int) $circle_id;
		}
		$this->db->sql_freeresult($result);

		return $circle_ids;
	}

	protected function delete_circle_membership_between($first_user_id, $second_user_id)
	{
		$first_circle_ids = $this->get_owned_circle_ids($first_user_id);
		if ($first_circle_ids)
		{
			$this->db->sql_query('DELETE FROM ' . $this->circle_members_table . '
				WHERE friend_id = ' . (int) $second_user_id . '
					AND ' . $this->db->sql_in_set('circle_id', $first_circle_ids));
		}
		$second_circle_ids = $this->get_owned_circle_ids($second_user_id);
		if ($second_circle_ids)
		{
			$this->db->sql_query('DELETE FROM ' . $this->circle_members_table . '
				WHERE friend_id = ' . (int) $first_user_id . '
					AND ' . $this->db->sql_in_set('circle_id', $second_circle_ids));
		}
	}

	protected function count_pending_requests($user_id, $incoming)
	{
		$column = $incoming ? 'recipient_id' : 'requester_id';
		$sql = 'SELECT COUNT(*) AS total
			FROM ' . $this->requests_table . '
			WHERE ' . $column . ' = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		return $count;
	}

	protected function max_pending_requests()
	{
		if (!isset($this->config['ze_max_pending_requests']))
		{
			return self::DEFAULT_MAX_PENDING_REQUESTS;
		}

		return max(0, (int) $this->config['ze_max_pending_requests']);
	}

	protected function request_policy_allows($requester_id, $recipient_id)
	{
		$sql = 'SELECT zebra_request_policy
			FROM ' . $this->users_table . '
			WHERE user_id = ' . (int) $recipient_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$policy = $this->db->sql_fetchfield('zebra_request_policy');
		$this->db->sql_freeresult($result);
		$policy = $policy === false ? self::REQUEST_POLICY_NOBODY : (int) $policy;
		if ($policy === self::REQUEST_POLICY_NOBODY)
		{
			return false;
		}
		if ($policy !== self::REQUEST_POLICY_FRIENDS_OF_FRIENDS)
		{
			return true;
		}

		$sql = 'SELECT 1 AS is_mutual
			FROM ' . $this->zebra_table . ' requester_friend
			INNER JOIN ' . $this->zebra_table . ' recipient_friend
				ON recipient_friend.zebra_id = requester_friend.zebra_id
			WHERE requester_friend.user_id = ' . (int) $requester_id . '
				AND recipient_friend.user_id = ' . (int) $recipient_id . '
				AND requester_friend.friend = 1
				AND requester_friend.foe = 0
				AND recipient_friend.friend = 1
				AND recipient_friend.foe = 0';
		$result = $this->db->sql_query_limit($sql, 1);
		$allowed = (bool) $this->db->sql_fetchfield('is_mutual');
		$this->db->sql_freeresult($result);

		return $allowed;
	}

	protected function is_request_on_cooldown($requester_id, $recipient_id)
	{
		$sql = 'SELECT expires_at
			FROM ' . $this->cooldowns_table . '
			WHERE requester_id = ' . (int) $requester_id . '
				AND recipient_id = ' . (int) $recipient_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$expires_at = $this->db->sql_fetchfield('expires_at');
		$this->db->sql_freeresult($result);
		if ($expires_at === false)
		{
			return false;
		}
		if ((int) $expires_at > time())
		{
			return true;
		}

		$this->delete_request_cooldown($requester_id, $recipient_id);
		return false;
	}

	protected function replace_request_cooldown($requester_id, $recipient_id)
	{
		$this->delete_request_cooldown($requester_id, $recipient_id);
		$days = isset($this->config['ze_decline_cooldown_days'])
			? max(0, (int) $this->config['ze_decline_cooldown_days'])
			: 7;
		if (!$days)
		{
			return;
		}

		$this->db->sql_query('INSERT INTO ' . $this->cooldowns_table . ' ' . $this->db->sql_build_array('INSERT', array(
			'requester_id' => (int) $requester_id,
			'recipient_id' => (int) $recipient_id,
			'expires_at'   => time() + $days * 86400,
		)));
	}

	protected function delete_request_cooldown($requester_id, $recipient_id, $both_directions = false)
	{
		$sql = 'DELETE FROM ' . $this->cooldowns_table . '
			WHERE (requester_id = ' . (int) $requester_id . '
				AND recipient_id = ' . (int) $recipient_id . ')';
		if ($both_directions)
		{
			$sql .= '
				OR (requester_id = ' . (int) $recipient_id . '
					AND recipient_id = ' . (int) $requester_id . ')';
		}
		$this->db->sql_query($sql);
	}

	protected function can_receive_friend_request($user_id)
	{
		$user_id = (int) $user_id;
		if (!$user_id || $user_id === ANONYMOUS)
		{
			return false;
		}

		$sql = 'SELECT user_type
			FROM ' . $this->users_table . '
			WHERE user_id = ' . (int) $user_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$user_type = $this->db->sql_fetchfield('user_type');
		$this->db->sql_freeresult($result);

		return $user_type !== false
			&& (int) $user_type !== USER_INACTIVE
			&& (int) $user_type !== USER_IGNORE;
	}

	/**
	 * Remove extension-owned rows when phpBB deletes users.
	 */
	public function delete_user_data(array $user_ids)
	{
		$user_ids = array_values(array_unique(array_filter(array_map('intval', $user_ids))));
		if (!$user_ids)
		{
			return;
		}

		$sql_in = $this->db->sql_in_set('requester_id', $user_ids);
		$sql_out = $this->db->sql_in_set('recipient_id', $user_ids);
		$requests = array();
		$this->db->sql_transaction('begin');
		try
		{
			$sql = 'SELECT request_id, recipient_id
				FROM ' . $this->requests_table . '
				WHERE ' . $sql_in . ' OR ' . $sql_out;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$requests[] = $row;
			}
			$this->db->sql_freeresult($result);

			$this->db->sql_query('DELETE FROM ' . $this->requests_table . '
				WHERE ' . $sql_in . ' OR ' . $sql_out);
			$this->db->sql_query('DELETE FROM ' . $this->legacy_requests_table . '
				WHERE ' . $this->db->sql_in_set('user_id', $user_ids) . '
					OR ' . $this->db->sql_in_set('zebra_id', $user_ids));
			$this->db->sql_query('DELETE FROM ' . $this->cooldowns_table . '
				WHERE ' . $this->db->sql_in_set('requester_id', $user_ids) . '
					OR ' . $this->db->sql_in_set('recipient_id', $user_ids));
			$this->db->sql_query('DELETE FROM ' . $this->foe_settings_table . '
				WHERE ' . $this->db->sql_in_set('owner_id', $user_ids) . '
					OR ' . $this->db->sql_in_set('foe_id', $user_ids));

			$owned_circle_ids = array();
			$result = $this->db->sql_query('SELECT circle_id
				FROM ' . $this->circles_table . '
				WHERE ' . $this->db->sql_in_set('owner_id', $user_ids));
			while (($circle_id = $this->db->sql_fetchfield('circle_id')) !== false)
			{
				$owned_circle_ids[] = (int) $circle_id;
			}
			$this->db->sql_freeresult($result);
			if ($owned_circle_ids)
			{
				$this->db->sql_query('DELETE FROM ' . $this->circle_members_table . '
					WHERE ' . $this->db->sql_in_set('circle_id', $owned_circle_ids));
			}
			$this->db->sql_query('DELETE FROM ' . $this->circle_members_table . '
				WHERE ' . $this->db->sql_in_set('friend_id', $user_ids));
			$this->db->sql_query('DELETE FROM ' . $this->circles_table . '
				WHERE ' . $this->db->sql_in_set('owner_id', $user_ids));
			$this->db->sql_transaction('commit');
		}
		catch (\Throwable $e)
		{
			$this->db->sql_transaction('rollback');
			throw $e;
		}

		$this->delete_request_notifications($requests);
		$this->delete_user_notifications($user_ids);
	}

	protected function delete_user_notifications(array $user_ids)
	{
		$types = array(self::REQUEST_NOTIFICATION, self::CONFIRM_NOTIFICATION);
		$sql = 'SELECT notification_type_id
			FROM ' . $this->notification_types_table . '
			WHERE ' . $this->db->sql_in_set('notification_type_name', $types);
		$result = $this->db->sql_query($sql);
		$type_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$type_ids[] = (int) $row['notification_type_id'];
		}
		$this->db->sql_freeresult($result);

		$type_ids = array_values(array_unique($type_ids));
		if (!$type_ids)
		{
			return;
		}

		foreach (array($this->notifications_table, $this->notification_emails_table) as $table)
		{
			if (!$this->db_tools->sql_table_exists($table))
			{
				continue;
			}

			$sql = 'DELETE FROM ' . $table . '
				WHERE ' . $this->db->sql_in_set('notification_type_id', $type_ids) . '
					AND (' . $this->db->sql_in_set('user_id', $user_ids) . '
						OR ' . $this->db->sql_in_set('item_parent_id', $user_ids) . ')';
			$this->db->sql_query($sql);
		}
	}

	protected function accept_request(array $request, $acceptor_id)
	{
		$request_id = (int) $request['request_id'];
		$requester_id = (int) $request['requester_id'];
		$acceptor_id = (int) $acceptor_id;
		$blocked_requests = array();
		$this->db->sql_transaction('begin');
		try
		{
			if ($this->is_foe($requester_id, $acceptor_id) || $this->is_foe($acceptor_id, $requester_id))
			{
				$blocked_requests = $this->get_requests_between($requester_id, $acceptor_id);
				$this->delete_request_rows($blocked_requests);
				$this->delete_legacy_between($requester_id, $acceptor_id);
				$this->db->sql_transaction('commit');
			}
			else
			{
				$sql = 'DELETE FROM ' . $this->requests_table . '
				WHERE request_id = ' . (int) $request_id . '
					AND requester_id = ' . (int) $requester_id . '
					AND recipient_id = ' . (int) $acceptor_id;
				$this->db->sql_query($sql);
				if ((int) $this->db->sql_affectedrows() !== 1)
				{
					$this->db->sql_transaction('commit');
					return false;
				}

				$this->delete_zebra_between($requester_id, $acceptor_id);
				$this->delete_legacy_between($requester_id, $acceptor_id);
				$this->delete_request_cooldown($requester_id, $acceptor_id, true);
				$this->db->sql_multi_insert($this->zebra_table, array(
					array('user_id' => $requester_id, 'zebra_id' => $acceptor_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
					array('user_id' => $acceptor_id, 'zebra_id' => $requester_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
				));
				$this->mark_changed(array($requester_id, $acceptor_id));
				$this->db->sql_transaction('commit');
			}
		}
		catch (\Throwable $e)
		{
			$this->db->sql_transaction('rollback');
			throw $e;
		}
		if ($blocked_requests)
		{
			$this->delete_request_notifications($blocked_requests);
			foreach ($blocked_requests as $blocked_request)
			{
				$this->dispatch_request_event(self::EVENT_REQUEST_DECLINED, $blocked_request, $acceptor_id, 'foe');
			}
			return false;
		}

		$this->notification_manager->delete_notifications(self::REQUEST_NOTIFICATION, $request_id, false, $acceptor_id);
		$this->notification_manager->add_notifications(self::CONFIRM_NOTIFICATION, array(
			'request_id'  => $request_id,
			'requester_id' => $acceptor_id,
			'user_id'      => array($requester_id => 'notification.method.board'),
		));
		$this->dispatch_request_event(self::EVENT_REQUEST_ACCEPTED, $request, $acceptor_id);

		return true;
	}

	protected function get_request_between($user_id, $zebra_id)
	{
		$sql = 'SELECT *
			FROM ' . $this->requests_table . '
			WHERE user_low = ' . min((int) $user_id, (int) $zebra_id) . '
				AND user_high = ' . max((int) $user_id, (int) $zebra_id);
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row ?: false;
	}

	protected function get_request_by_id($request_id)
	{
		$sql = 'SELECT *
			FROM ' . $this->requests_table . '
			WHERE request_id = ' . (int) $request_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row ?: false;
	}

	protected function get_requests_between($user_id, $zebra_id)
	{
		$request = $this->get_request_between($user_id, $zebra_id);
		return $request ? array($request) : array();
	}

	protected function delete_request_rows(array $requests)
	{
		if (!$requests)
		{
			return;
		}

		$request_ids = array_map(function ($request)
		{
			return (int) $request['request_id'];
		}, $requests);
		$this->db->sql_query('DELETE FROM ' . $this->requests_table . '
			WHERE ' . $this->db->sql_in_set('request_id', $request_ids));
	}

	protected function delete_request_notifications(array $requests)
	{
		foreach ($requests as $request)
		{
			$this->notification_manager->delete_notifications(
				self::REQUEST_NOTIFICATION,
				(int) $request['request_id'],
				false,
				isset($request['recipient_id']) ? (int) $request['recipient_id'] : false
			);
		}
	}

	protected function delete_zebra_between($user_id, $zebra_id)
	{
		$sql = 'DELETE FROM ' . $this->zebra_table . '
			WHERE (user_id = ' . (int) $user_id . ' AND zebra_id = ' . (int) $zebra_id . ')
				OR (user_id = ' . (int) $zebra_id . ' AND zebra_id = ' . (int) $user_id . ')';
		$this->db->sql_query($sql);
	}

	protected function delete_legacy_between($user_id, $zebra_id)
	{
		$sql = 'DELETE FROM ' . $this->legacy_requests_table . '
			WHERE (user_id = ' . (int) $user_id . ' AND zebra_id = ' . (int) $zebra_id . ')
				OR (user_id = ' . (int) $zebra_id . ' AND zebra_id = ' . (int) $user_id . ')';
		$this->db->sql_query($sql);
	}

	protected function mark_changed(array $user_ids)
	{
		$user_ids = array_values(array_unique(array_filter(array_map('intval', $user_ids))));
		if ($user_ids)
		{
			$this->db->sql_query('UPDATE ' . $this->users_table . '
				SET zebra_changed = 1
				WHERE ' . $this->db->sql_in_set('user_id', $user_ids));
		}
	}

	protected function dispatch_request_event($event_name, array $request, $actor_id, $reason = null)
	{
		$data = array(
			'request_id'   => (int) $request['request_id'],
			'requester_id' => (int) $request['requester_id'],
			'recipient_id' => (int) $request['recipient_id'],
			'actor_id'     => (int) $actor_id,
			'request_time' => isset($request['request_time']) ? (int) $request['request_time'] : 0,
		);
		if ($reason !== null)
		{
			$data['reason'] = (string) $reason;
		}
		if (!empty($request['request_message']))
		{
			$data['message'] = (string) $request['request_message'];
		}

		$this->dispatch_event($event_name, $data);
	}

	protected function dispatch_event($event_name, array $data)
	{
		$this->dispatcher->trigger_event($event_name, $data);
	}
}
