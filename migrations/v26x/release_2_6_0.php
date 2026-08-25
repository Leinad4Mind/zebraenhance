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

namespace anavaro\zebraenhance\migrations\v26x;

class release_2_6_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '2.6.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\anavaro\zebraenhance\migrations\v25x\release_2_5_0',
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'zebra_foe_settings' => array(
					'COLUMNS' => array(
						'owner_id'            => array('ULINT', 0),
						'foe_id'              => array('ULINT', 0),
						'added_at'            => array('TIMESTAMP', 0),
						'expires_at'          => array('TIMESTAMP', 0),
						'foe_note'            => array('VCHAR_UNI:255', ''),
						'pm_policy'           => array('TINT:1', 0),
						'content_policy'      => array('TINT:1', 0),
						'notification_policy' => array('TINT:1', 0),
					),
					'PRIMARY_KEY' => array('owner_id', 'foe_id'),
					'KEYS' => array(
						'foe_id'     => array('INDEX', 'foe_id'),
						'expires_at' => array('INDEX', 'expires_at'),
					),
				),
			),
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_mute_foe_notifications' => array('BOOL', 0),
				),
			),
			'drop_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_changed',
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_changed' => array('UINT', 0),
				),
			),
			'drop_tables' => array(
				$this->table_prefix . 'zebra_foe_settings',
			),
			'drop_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_mute_foe_notifications',
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('zebraenhance_foes_enhancement', 0)),
			array('config.add', array('zebraenhance_foe_pm', 1)),
			array('config.add', array('zebraenhance_foe_content', 1)),
			array('config.add', array('zebraenhance_foe_notifications', 1)),
			array('config.add', array('zebraenhance_foe_temporary', 1)),
			array('config.add', array('zebraenhance_foe_notes', 1)),
			array('config.add', array('zebraenhance_foe_exceptions', 1)),
			array('config.add', array('zebraenhance_foe_expiry_last_gc', 0, true)),
			array('config.add', array('zebraenhance_foe_expiry_gc', 3600)),
			array('custom', array(array($this, 'migrate_existing_foes'))),
			array('module.add', array(
				'ucp',
				'UCP_ZEBRA',
				array(
					'module_basename' => '\anavaro\zebraenhance\ucp\foes_module',
					'modes' => array('manage'),
				),
			)),
			array('config.update', array('zebra_enhance_version', '2.6.0')),
		);
	}

	public function migrate_existing_foes($state = 0)
	{
		$settings_table = $this->table_prefix . 'zebra_foe_settings';
		$owner_id = is_array($state) && isset($state['owner_id']) ? (int) $state['owner_id'] : 0;
		$foe_id = is_array($state) && isset($state['foe_id']) ? (int) $state['foe_id'] : 0;
		$rows = array();
		$sql = 'SELECT z.user_id, z.zebra_id
			FROM ' . $this->table_prefix . 'zebra z
			LEFT JOIN ' . $settings_table . ' fs
				ON fs.owner_id = z.user_id AND fs.foe_id = z.zebra_id
			WHERE z.foe = 1
				AND fs.owner_id IS NULL';
		if ($owner_id)
		{
			$sql .= ' AND (z.user_id > ' . $owner_id . '
				OR (z.user_id = ' . $owner_id . ' AND z.zebra_id > ' . $foe_id . '))';
		}
		$sql .= ' ORDER BY z.user_id ASC, z.zebra_id ASC';
		$result = $this->db->sql_query_limit($sql, 500);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = array(
				'owner_id'   => (int) $row['user_id'],
				'foe_id'     => (int) $row['zebra_id'],
				'added_at'   => 0,
				'expires_at' => 0,
			);
		}
		$this->db->sql_freeresult($result);

		foreach (array_chunk($rows, 500) as $batch)
		{
			$this->db->sql_multi_insert($settings_table, $batch);
		}

		if (count($rows) === 500)
		{
			$last = end($rows);
			return array(
				'owner_id' => (int) $last['owner_id'],
				'foe_id'   => (int) $last['foe_id'],
			);
		}

		return null;
	}
}
