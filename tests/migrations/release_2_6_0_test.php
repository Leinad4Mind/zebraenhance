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

namespace anavaro\zebraenhance\tests\migrations;

class release_2_6_0_test extends \phpbb_test_case
{
	protected function migration()
	{
		return new \anavaro\zebraenhance\migrations\v26x\release_2_6_0(
			new \phpbb\config\config(array('zebra_enhance_version' => '2.5.0')),
			$this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\db\tools\tools_interface')->getMock(),
			'./',
			'php',
			'phpbb_'
		);
	}

	public function test_schema_adds_foe_settings_and_notification_preference()
	{
		$schema = $this->migration()->update_schema();
		$table = $schema['add_tables']['phpbb_zebra_foe_settings'];

		$this->assertSame(array('owner_id', 'foe_id'), $table['PRIMARY_KEY']);
		$this->assertArrayHasKey('expires_at', $table['COLUMNS']);
		$this->assertArrayHasKey('foe_note', $table['COLUMNS']);
		$this->assertArrayHasKey('notification_policy', $table['COLUMNS']);
		$this->assertSame(
			array('BOOL', 0),
			$schema['add_columns']['phpbb_users']['zebra_mute_foe_notifications']
		);
	}

	public function test_data_registers_cron_module_and_version()
	{
		$migration = $this->migration();
		$data = $migration->update_data();
		$this->assertContains(
			array('permission.add', array('u_zebraenhance_use_friend_requests', true, 'u_ze_use')),
			$data
		);
		$this->assertContains(
			array('permission.add', array('u_zebraenhance_manage_close_friends', true, 'u_ze_close_friends')),
			$data
		);
		$this->assertContains(
			array('permission.add', array('m_zebraenhance_view_private_friendlists', true, 'm_ze_view_private_friendlists')),
			$data
		);
		$this->assertContains(array('permission.remove', array('u_ze_use')), $data);
		$this->assertContains(array('permission.remove', array('u_ze_close_friends')), $data);
		$this->assertContains(array('permission.remove', array('m_ze_view_private_friendlists')), $data);
		$this->assertContains(array('config.add', array('ze_foes_enhancement', 0)), $data);
		$this->assertContains(array('config.add', array('ze_foe_pm', 1)), $data);
		$this->assertContains(array('config.add', array('ze_foe_content', 1)), $data);
		$this->assertContains(array('config.add', array('ze_foe_notifications', 1)), $data);
		$this->assertContains(array('config.add', array('ze_foe_temporary', 1)), $data);
		$this->assertContains(array('config.add', array('ze_foe_notes', 1)), $data);
		$this->assertContains(array('config.add', array('ze_foe_exceptions', 1)), $data);
		$this->assertContains(array('config.add', array('ze_foe_expiry_gc', 3600)), $data);
		$this->assertContains(array('custom', array(array($migration, 'migrate_existing_foes'))), $data);
		$this->assertContains(array('config.update', array('zebra_enhance_version', '2.6.0')), $data);
		$modules = array_values(array_filter($data, function ($operation)
		{
			return $operation[0] === 'module.add';
		}));
		$this->assertCount(1, $modules);
		$this->assertSame('ucp', $modules[0][1][0]);
	}
}
