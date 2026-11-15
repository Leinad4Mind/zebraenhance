<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\migrations\v27x;

class release_2_7_0 extends \phpbb\db\migration\migration
{
	/**
	 * Permissions released in 2.4.0 under short names, renamed before 2.7.0.
	 * The earlier migrations now create the long names, so boards that ran
	 * them under the old names are converted here.
	 */
	const RENAMED_PERMISSIONS = array(
		'u_ze_use'                      => 'u_zebraenhance_use_friend_requests',
		'u_ze_close_friends'            => 'u_zebraenhance_manage_close_friends',
		'm_ze_view_private_friendlists' => 'm_zebraenhance_view_private_friendlists',
	);

	/** Configuration names renamed the same way: old name => array(new name, dynamic) */
	const RENAMED_CONFIGS = array(
		'ze_max_pending_requests'  => array('zebraenhance_max_pending_requests', false),
		'ze_decline_cooldown_days' => array('zebraenhance_decline_cooldown_days', false),
		'ze_foes_enhancement'      => array('zebraenhance_foes_enhancement', false),
		'ze_foe_pm'                => array('zebraenhance_foe_pm', false),
		'ze_foe_content'           => array('zebraenhance_foe_content', false),
		'ze_foe_notifications'     => array('zebraenhance_foe_notifications', false),
		'ze_foe_temporary'         => array('zebraenhance_foe_temporary', false),
		'ze_foe_notes'             => array('zebraenhance_foe_notes', false),
		'ze_foe_exceptions'        => array('zebraenhance_foe_exceptions', false),
		'ze_foe_expiry_last_gc'    => array('zebraenhance_foe_expiry_last_gc', true),
		'ze_foe_expiry_gc'         => array('zebraenhance_foe_expiry_gc', false),
	);

	/** Old identifiers that module auth strings stored in the database may still use */
	const RENAMED_MODULE_AUTH = array(
		'acl_m_ze_view_private_friendlists' => 'acl_m_zebraenhance_view_private_friendlists',
		'acl_u_ze_close_friends'            => 'acl_u_zebraenhance_manage_close_friends',
		'acl_u_ze_use'                      => 'acl_u_zebraenhance_use_friend_requests',
		'cfg_ze_foes_enhancement'           => 'cfg_zebraenhance_foes_enhancement',
	);

	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '2.7.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\anavaro\zebraenhance\migrations\v26x\release_2_6_0',
		);
	}

	/**
	 * Birthdays stay visible to everyone (0) until a user restricts them.
	 *
	 * The PM choice reuses zebra_block_foe_pm: its 0 and 1 already mean
	 * "everyone" and "everyone except foes", so existing users keep their setting.
	 */
	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_birthday_show' => array('TINT:1', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_birthday_show',
				),
			),
		);
	}

	/**
	 * phpBB may call this again while resuming the migration, so every rename
	 * is an "if" step: the list keeps the same length and order on each call.
	 */
	public function update_data()
	{
		$data = array();
		foreach (self::RENAMED_PERMISSIONS as $old_name => $new_name)
		{
			$old_exists = $this->permission_exists($old_name);
			// Copying keeps the group, user and role settings of the old permission.
			$data[] = array('if', array(
				$old_exists && !$this->permission_exists($new_name),
				array('permission.add', array($new_name, true, $old_name)),
			));
			$data[] = array('if', array(
				$old_exists,
				array('permission.remove', array($old_name)),
			));
		}
		foreach (self::RENAMED_CONFIGS as $old_name => $new_config)
		{
			list($new_name, $is_dynamic) = $new_config;
			$old_exists = isset($this->config[$old_name]);
			$data[] = array('if', array(
				$old_exists && !isset($this->config[$new_name]),
				array('config.add', array($new_name, $old_exists ? $this->config[$old_name] : '', $is_dynamic)),
			));
			$data[] = array('if', array(
				$old_exists,
				array('config.remove', array($old_name)),
			));
		}

		$data[] = array('custom', array(array($this, 'rename_module_auth')));
		$data[] = array('config.add', array('zebraenhance_friend_posts', 1));
		$data[] = array('config.update', array('zebra_enhance_version', '2.7.0'));

		return $data;
	}

	/**
	 * Point the extension's modules at the renamed permission and configuration.
	 */
	public function rename_module_auth()
	{
		$modules_table = $this->table_prefix . 'modules';
		$sql = 'SELECT module_id, module_basename, module_auth
			FROM ' . $modules_table . '
			WHERE module_auth ' . $this->db->sql_like_expression($this->db->get_any_char() . '_ze_' . $this->db->get_any_char());
		$result = $this->db->sql_query($sql);
		$updates = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			if (strpos($row['module_basename'], 'zebraenhance') === false)
			{
				continue;
			}
			$module_auth = strtr($row['module_auth'], self::RENAMED_MODULE_AUTH);
			if ($module_auth !== $row['module_auth'])
			{
				$updates[(int) $row['module_id']] = $module_auth;
			}
		}
		$this->db->sql_freeresult($result);

		foreach ($updates as $module_id => $module_auth)
		{
			$this->db->sql_query('UPDATE ' . $modules_table . "
				SET module_auth = '" . $this->db->sql_escape($module_auth) . "'
				WHERE module_id = " . $module_id);
		}
	}

	protected function permission_exists($auth_option)
	{
		$sql = 'SELECT auth_option_id
			FROM ' . $this->table_prefix . "acl_options
			WHERE auth_option = '" . $this->db->sql_escape($auth_option) . "'";
		$result = $this->db->sql_query_limit($sql, 1);
		$exists = (bool) $this->db->sql_fetchfield('auth_option_id');
		$this->db->sql_freeresult($result);

		return $exists;
	}
}
