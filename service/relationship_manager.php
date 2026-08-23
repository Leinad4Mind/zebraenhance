<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2013-2026 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\service;

class relationship_manager
{
	const REQUEST_NOTIFICATION = 'anavaro.zebraenhance.notification.zebraadd';
	const CONFIRM_NOTIFICATION = 'anavaro.zebraenhance.notification.zebraconfirm';

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

	/** @var \phpbb\db\tools\tools_interface */
	protected $db_tools;

	/** @var string */
	protected $requests_table;

	/** @var string */
	protected $legacy_requests_table;

	/** @var string */
	protected $zebra_table;

	/** @var string */
	protected $users_table;

	/** @var string */
	protected $notifications_table;

	/** @var string */
	protected $notification_emails_table;

	/** @var string */
	protected $notification_types_table;

	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\db\tools\tools_interface $db_tools,
		\phpbb\notification\manager $notification_manager,
		$requests_table,
		$legacy_requests_table,
		$zebra_table,
		$users_table,
		$notifications_table,
		$notification_emails_table,
		$notification_types_table
	)
	{
		$this->db = $db;
		$this->db_tools = $db_tools;
		$this->notification_manager = $notification_manager;
		$this->requests_table = $requests_table;
		$this->legacy_requests_table = $legacy_requests_table;
		$this->zebra_table = $zebra_table;
		$this->users_table = $users_table;
		$this->notifications_table = $notifications_table;
		$this->notification_emails_table = $notification_emails_table;
		$this->notification_types_table = $notification_types_table;
	}

	/**
	 * Intercept rows prepared by phpBB's UCP Zebra module.
	 *
	 * Friend rows are converted into requests. Foe rows are returned to phpBB
	 * after pending requests between the users have been cancelled.
	 *
	 * @param string $mode
	 * @param array  $rows
	 * @return array Rows phpBB should continue inserting
	 */
	public function process_additions($mode, array $rows)
	{
		if ($mode === 'friends')
		{
			foreach ($rows as $row)
			{
				$this->request_friendship((int) $row['user_id'], (int) $row['zebra_id']);
			}

			return array();
		}

		if ($mode === 'foes')
		{
			foreach ($rows as $row)
			{
				$this->cancel_requests_between((int) $row['user_id'], (int) $row['zebra_id']);
			}
		}

		return $rows;
	}

	/**
	 * Create a request or accept the reverse request.
	 *
	 * @return string created, accepted, ignored, or blocked
	 */
	public function request_friendship($requester_id, $recipient_id)
	{
		$requester_id = (int) $requester_id;
		$recipient_id = (int) $recipient_id;
		if (!$requester_id || !$recipient_id || $requester_id === $recipient_id)
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
		if ($this->is_foe($recipient_id, $requester_id))
		{
			return 'blocked';
		}

		$sql_ary = array(
			'requester_id' => $requester_id,
			'recipient_id' => $recipient_id,
			'user_low'     => min($requester_id, $recipient_id),
			'user_high'    => max($requester_id, $recipient_id),
			'request_time' => time(),
		);
		$this->db->sql_return_on_error(true);
		$result = $this->db->sql_query('INSERT INTO ' . $this->requests_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
		$this->db->sql_return_on_error(false);

		// A concurrent reverse request may have won the unique user-pair key.
		if ($result === false)
		{
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
			'user_id'      => array($recipient_id => 'notification.method.board'),
		));

		return 'created';
	}

	/**
	 * Remove a friendship or pending request in both directions.
	 */
	public function remove_relationship($user_id, $zebra_id)
	{
		$user_id = (int) $user_id;
		$zebra_id = (int) $zebra_id;
		if (!$user_id || !$zebra_id || $user_id === $zebra_id)
		{
			return;
		}

		$requests = $this->get_requests_between($user_id, $zebra_id);
		$this->db->sql_transaction('begin');
		try
		{
			$this->delete_zebra_between($user_id, $zebra_id);
			$this->delete_request_rows($requests);
			$this->delete_legacy_between($user_id, $zebra_id);
			$this->mark_changed(array($user_id, $zebra_id));
			$this->db->sql_transaction('commit');
		}
		catch (\Throwable $e)
		{
			$this->db->sql_transaction('rollback');
			throw $e;
		}

		$this->delete_request_notifications($requests);
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

		$sql = 'UPDATE ' . $this->zebra_table . '
			SET bff = ' . ((bool) $is_close ? 1 : 0) . '
			WHERE user_id = ' . (int) $owner_id . '
				AND zebra_id = ' . (int) $friend_id . '
				AND friend = 1
				AND foe = 0';
		$this->db->sql_query($sql);
		if (!$this->db->sql_affectedrows())
		{
			return false;
		}

		$this->mark_changed(array($owner_id));
		return true;
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
	public function get_friends($owner_id, $limit = 0)
	{
		$sql = 'SELECT z.zebra_id, z.bff, u.username, u.user_colour
			FROM ' . $this->zebra_table . ' z
			INNER JOIN ' . $this->users_table . ' u
				ON u.user_id = z.zebra_id
			WHERE z.user_id = ' . (int) $owner_id . '
				AND z.friend = 1
				AND z.foe = 0
			ORDER BY u.username_clean ASC';
		$result = $limit ? $this->db->sql_query_limit($sql, (int) $limit) : $this->db->sql_query($sql);
		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rows;
	}

	/**
	 * Return requests with the other user's public identity fields.
	 */
	public function get_requests($user_id, $incoming)
	{
		$user_column = $incoming ? 'r.requester_id' : 'r.recipient_id';
		$match_column = $incoming ? 'r.recipient_id' : 'r.requester_id';
		$sql = 'SELECT r.request_id, r.requester_id, r.recipient_id,
				u.username, u.user_colour
			FROM ' . $this->requests_table . ' r
			INNER JOIN ' . $this->users_table . ' u
				ON u.user_id = ' . $user_column . '
			WHERE ' . $match_column . ' = ' . (int) $user_id . '
			ORDER BY r.request_time ASC, r.request_id ASC';
		$result = $this->db->sql_query($sql);
		$rows = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rows;
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
		$sql = 'SELECT request_id, recipient_id
			FROM ' . $this->requests_table . '
			WHERE ' . $sql_in . ' OR ' . $sql_out;
		$result = $this->db->sql_query($sql);
		$requests = array();
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
		if ($this->is_foe($requester_id, $acceptor_id) || $this->is_foe($acceptor_id, $requester_id))
		{
			$this->cancel_requests_between($requester_id, $acceptor_id);
			return false;
		}

		$this->db->sql_transaction('begin');
		try
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
			$this->db->sql_multi_insert($this->zebra_table, array(
				array('user_id' => $requester_id, 'zebra_id' => $acceptor_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
				array('user_id' => $acceptor_id, 'zebra_id' => $requester_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			));
			$this->mark_changed(array($requester_id, $acceptor_id));
			$this->db->sql_transaction('commit');
		}
		catch (\Throwable $e)
		{
			$this->db->sql_transaction('rollback');
			throw $e;
		}

		$this->notification_manager->delete_notifications(self::REQUEST_NOTIFICATION, $request_id, false, $acceptor_id);
		$this->notification_manager->add_notifications(self::CONFIRM_NOTIFICATION, array(
			'request_id'  => $request_id,
			'requester_id' => $acceptor_id,
			'user_id'      => array($requester_id => 'notification.method.board'),
		));

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

	protected function get_requests_between($user_id, $zebra_id)
	{
		$request = $this->get_request_between($user_id, $zebra_id);
		return $request ? array($request) : array();
	}

	protected function cancel_requests_between($user_id, $zebra_id)
	{
		$requests = $this->get_requests_between($user_id, $zebra_id);
		$this->delete_request_rows($requests);
		$this->delete_legacy_between($user_id, $zebra_id);
		$this->delete_request_notifications($requests);
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
}
