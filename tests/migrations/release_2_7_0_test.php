<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\tests\migrations;

class release_2_7_0_test extends \phpbb_test_case
{
	protected function migration(array $config = array(), $db = null)
	{
		return new \anavaro\zebraenhance\migrations\v27x\release_2_7_0(
			new \phpbb\config\config(array_merge(array('zebra_enhance_version' => '2.6.0'), $config)),
			$db ?: $this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\db\tools\tools_interface')->getMock(),
			'./',
			'php',
			'phpbb_'
		);
	}

	/**
	 * A database whose acl_options table holds the given permissions.
	 */
	protected function db_with_permissions(array $auth_options)
	{
		$db = $this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock();
		$db->method('sql_escape')->willReturnArgument(0);
		$last_sql = '';
		$db->method('sql_query_limit')->willReturnCallback(function ($sql) use (&$last_sql)
		{
			$last_sql = $sql;
			return true;
		});
		$db->method('sql_fetchfield')->willReturnCallback(function () use (&$last_sql, $auth_options)
		{
			foreach ($auth_options as $auth_option)
			{
				if (strpos($last_sql, "'" . $auth_option . "'") !== false)
				{
					return 1;
				}
			}
			return false;
		});

		return $db;
	}

	public function test_schema_adds_public_by_default_birthday_visibility()
	{
		$schema = $this->migration()->update_schema();
		$this->assertSame(array('TINT:1', 0), $schema['add_columns']['phpbb_users']['zebra_birthday_show']);

		$revert = $this->migration()->revert_schema();
		$this->assertSame(array('zebra_birthday_show'), $revert['drop_columns']['phpbb_users']);
	}

	public function test_fresh_boards_only_enable_friend_posts_and_record_the_version()
	{
		$migration = $this->migration();
		$data = $migration->update_data();

		$conditional = array_filter($data, function ($step)
		{
			return $step[0] === 'if';
		});
		foreach ($conditional as $step)
		{
			$this->assertFalse($step[1][0], 'No rename applies to a board without the old names.');
		}
		$this->assertSame(array(
			array('custom', array(array($migration, 'rename_module_auth'))),
			array('config.add', array('zebraenhance_friend_posts', 1)),
			array('config.update', array('zebra_enhance_version', '2.7.0')),
		), array_slice($data, -3));
		$this->assertSame(
			array('\anavaro\zebraenhance\migrations\v26x\release_2_6_0'),
			\anavaro\zebraenhance\migrations\v27x\release_2_7_0::depends_on()
		);
	}

	public function test_boards_from_2_4_0_get_the_renamed_permissions_and_settings()
	{
		$migration = $this->migration(
			array('ze_max_pending_requests' => 40, 'ze_decline_cooldown_days' => 3),
			$this->db_with_permissions(array('u_ze_use', 'u_ze_close_friends', 'm_ze_view_private_friendlists'))
		);
		$applied = array();
		foreach ($migration->update_data() as $step)
		{
			if ($step[0] === 'if' && $step[1][0])
			{
				$applied[] = $step[1][1];
			}
		}

		$this->assertSame(array(
			array('permission.add', array('u_zebraenhance_use_friend_requests', true, 'u_ze_use')),
			array('permission.remove', array('u_ze_use')),
			array('permission.add', array('u_zebraenhance_manage_close_friends', true, 'u_ze_close_friends')),
			array('permission.remove', array('u_ze_close_friends')),
			array('permission.add', array('m_zebraenhance_view_private_friendlists', true, 'm_ze_view_private_friendlists')),
			array('permission.remove', array('m_ze_view_private_friendlists')),
			array('config.add', array('zebraenhance_max_pending_requests', 40, false)),
			array('config.remove', array('ze_max_pending_requests')),
			array('config.add', array('zebraenhance_decline_cooldown_days', 3, false)),
			array('config.remove', array('ze_decline_cooldown_days')),
		), $applied);
	}

	public function test_old_names_are_only_removed_when_the_new_ones_exist()
	{
		$migration = $this->migration(
			array('ze_foe_pm' => 0, 'zebraenhance_foe_pm' => 1),
			$this->db_with_permissions(array('u_ze_use', 'u_zebraenhance_use_friend_requests'))
		);
		$applied = array();
		foreach ($migration->update_data() as $step)
		{
			if ($step[0] === 'if' && $step[1][0])
			{
				$applied[] = $step[1][1];
			}
		}

		$this->assertSame(array(
			array('permission.remove', array('u_ze_use')),
			array('config.remove', array('ze_foe_pm')),
		), $applied);
	}

	public function test_step_count_does_not_depend_on_the_board()
	{
		$fresh = $this->migration()->update_data();
		$legacy = $this->migration(
			array('ze_max_pending_requests' => 40),
			$this->db_with_permissions(array('u_ze_use'))
		)->update_data();

		$this->assertCount(count($fresh), $legacy);
	}

	public function test_module_auth_uses_the_renamed_identifiers()
	{
		$db = $this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock();
		$db->method('get_any_char')->willReturn('%');
		$db->method('sql_like_expression')->willReturnCallback(function ($expression)
		{
			return "LIKE '" . $expression . "'";
		});
		$db->method('sql_escape')->willReturnArgument(0);
		$db->method('sql_fetchrow')->willReturnOnConsecutiveCalls(
			array('module_id' => 7, 'module_basename' => '\anavaro\zebraenhance\acp\report_module', 'module_auth' => 'ext_anavaro/zebraenhance && acl_m_ze_view_private_friendlists'),
			array('module_id' => 8, 'module_basename' => '\anavaro\zebraenhance\ucp\foes_module', 'module_auth' => 'ext_anavaro/zebraenhance && cfg_ze_foes_enhancement && acl_u_ze_use'),
			array('module_id' => 9, 'module_basename' => '\other\ext\acp\main_module', 'module_auth' => 'acl_u_ze_use'),
			false
		);
		$queries = array();
		$db->method('sql_query')->willReturnCallback(function ($sql) use (&$queries)
		{
			$queries[] = preg_replace('/\s+/', ' ', $sql);
			return true;
		});

		$this->migration(array(), $db)->rename_module_auth();

		$this->assertSame(array(
			"UPDATE phpbb_modules SET module_auth = 'ext_anavaro/zebraenhance && acl_m_zebraenhance_view_private_friendlists' WHERE module_id = 7",
			"UPDATE phpbb_modules SET module_auth = 'ext_anavaro/zebraenhance && cfg_zebraenhance_foes_enhancement && acl_u_zebraenhance_use_friend_requests' WHERE module_id = 8",
		), array_slice($queries, 1));
	}

	public function test_effectively_installed_follows_the_version()
	{
		$this->assertFalse($this->migration(array('zebra_enhance_version' => '2.6.0'))->effectively_installed());
		$this->assertTrue($this->migration(array('zebra_enhance_version' => '2.7.0'))->effectively_installed());
	}
}
