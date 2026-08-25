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

class release_2_1_0_test extends \phpbb_test_case
{
	protected function migration()
	{
		return new \anavaro\zebraenhance\migrations\v21x\release_2_1_0(
			new \phpbb\config\config(array('zebra_enhance_version' => '2.0.0')),
			$this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\db\tools\tools_interface')->getMock(),
			'./',
			'php',
			'phpbb_'
		);
	}

	public function test_schema_adds_policy_and_directional_cooldowns()
	{
		$schema = $this->migration()->update_schema();

		$this->assertArrayHasKey('zebra_request_policy', $schema['add_columns']['phpbb_users']);
		$this->assertArrayHasKey('phpbb_zebra_request_cooldowns', $schema['add_tables']);
		$this->assertSame(array('requester_id', 'recipient_id'), $schema['add_tables']['phpbb_zebra_request_cooldowns']['PRIMARY_KEY']);
	}

	public function test_data_adds_defaults_acp_module_and_version()
	{
		$data = $this->migration()->update_data();

		$this->assertContains(array('config.add', array('zebraenhance_max_pending_requests', 100)), $data);
		$this->assertContains(array('config.add', array('zebraenhance_decline_cooldown_days', 7)), $data);
		$this->assertSame('module.add', $data[2][0]);
		$this->assertSame('module.add', $data[3][0]);
		$this->assertContains(array('config.update', array('zebra_enhance_version', '2.1.0')), $data);
	}
}
