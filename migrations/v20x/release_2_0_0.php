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

namespace anavaro\zebraenhance\migrations\v20x;

class release_2_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '2.0.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\anavaro\zebraenhance\migrations\v10x\release_1_0_1',
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'zebra_requests' => array(
					'COLUMNS' => array(
						'request_id'   => array('UINT', null, 'auto_increment'),
						'requester_id' => array('ULINT', 0),
						'recipient_id' => array('ULINT', 0),
						'user_low'     => array('ULINT', 0),
						'user_high'    => array('ULINT', 0),
						'request_time' => array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY' => 'request_id',
					'KEYS' => array(
						'user_pair' => array('UNIQUE', array('user_low', 'user_high')),
						'requester' => array('INDEX', 'requester_id'),
						'recipient' => array('INDEX', 'recipient_id'),
					),
				),
			),
			// Repair incomplete 1.x installations. The database tools skip
			// columns that already exist on correctly upgraded boards.
			'add_columns' => array(
				$this->table_prefix . 'zebra' => array(
					'bff' => array('UINT', 0),
				),
				$this->table_prefix . 'users' => array(
					'profile_friend_show' => array('UINT', 5),
					'zebra_changed'       => array('UINT', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'zebra_requests',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('permission.add', array('u_ze_use')),
			array('permission.add', array('u_ze_close_friends')),
			array('permission.add', array('m_ze_view_private_friendlists', true)),
			array('permission.permission_set', array('REGISTERED', 'u_ze_use', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_ze_close_friends', 'group')),
			array('permission.permission_set', array('REGISTERED_COPPA', 'u_ze_use', 'group')),
			array('permission.permission_set', array('REGISTERED_COPPA', 'u_ze_close_friends', 'group')),
			array('permission.permission_set', array('GLOBAL_MODERATORS', 'm_ze_view_private_friendlists', 'group')),
			array('custom', array(array($this, 'migrate_legacy_requests'))),
			array('custom', array(array($this, 'purge_legacy_notifications'))),
			array('config.remove', array('zebra_module_id')),
			array('config.update', array('zebra_enhance_version', '2.0.0')),
		);
	}

	/**
	 * Remove 1.x notifications whose item IDs were recipient user IDs rather
	 * than stable request IDs. Keeping them would cause collisions with 2.x
	 * notifications and their requester identity was only in serialized data.
	 */
	public function purge_legacy_notifications()
	{
		$notification_types_table = $this->table_prefix . 'notification_types';
		$types = array(
			'anavaro.zebraenhance.notification.zebraadd',
			'anavaro.zebraenhance.notification.zebraconfirm',
		);
		$sql = 'SELECT notification_type_id
			FROM ' . $notification_types_table . '
			WHERE ' . $this->db->sql_in_set('notification_type_name', $types);
		$result = $this->db->sql_query($sql);
		$type_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$type_ids[] = (int) $row['notification_type_id'];
		}
		$this->db->sql_freeresult($result);

		if (!$type_ids)
		{
			return;
		}

		foreach (array('notifications', 'notification_emails') as $table_name)
		{
			$table = $this->table_prefix . $table_name;
			if (!$this->db_tools->sql_table_exists($table))
			{
				continue;
			}

			$this->db->sql_query('DELETE FROM ' . $table . '
				WHERE ' . $this->db->sql_in_set('notification_type_id', $type_ids));
		}
	}

	/**
	 * Copy valid 1.x requests into the directional, uniquely keyed 2.0 table.
	 *
	 * The old table is deliberately retained until purge so an interrupted
	 * downgrade does not destroy user data.
	 */
	public function migrate_legacy_requests()
	{
		$legacy_table = $this->table_prefix . 'zebra_confirm';
		$request_table = $this->table_prefix . 'zebra_requests';

		$sql = 'SELECT zc.user_id, zc.zebra_id
			FROM ' . $legacy_table . ' zc
			INNER JOIN ' . $this->table_prefix . 'users requester
				ON requester.user_id = zc.user_id
			INNER JOIN ' . $this->table_prefix . 'users recipient
				ON recipient.user_id = zc.zebra_id
			WHERE zc.friend = 1
			ORDER BY zc.user_id, zc.zebra_id';
		$result = $this->db->sql_query($sql);

		$requests = array();
		$seen_pairs = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$requester_id = (int) $row['user_id'];
			$recipient_id = (int) $row['zebra_id'];
			if (!$requester_id || !$recipient_id || $requester_id === $recipient_id)
			{
				continue;
			}

			$user_low = min($requester_id, $recipient_id);
			$user_high = max($requester_id, $recipient_id);
			$pair_key = $user_low . ':' . $user_high;
			if (isset($seen_pairs[$pair_key]))
			{
				continue;
			}

			$seen_pairs[$pair_key] = true;
			$requests[] = array(
				'requester_id' => $requester_id,
				'recipient_id' => $recipient_id,
				'user_low'     => $user_low,
				'user_high'    => $user_high,
				'request_time' => time(),
			);
		}
		$this->db->sql_freeresult($result);

		if ($requests)
		{
			$this->db->sql_multi_insert($request_table, $requests);
		}
	}
}
