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

class release_2_5_0_test extends \phpbb_test_case
{
	protected function migration()
	{
		return new \anavaro\zebraenhance\migrations\v25x\release_2_5_0(
			new \phpbb\config\config(array('zebra_enhance_version' => '2.4.0')),
			$this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\db\tools\tools_interface')->getMock(),
			'./',
			'php',
			'phpbb_'
		);
	}

	public function test_schema_adds_foe_privacy_preferences()
	{
		$schema = $this->migration()->update_schema();

		$this->assertSame(
			array('BOOL', 0),
			$schema['add_columns']['phpbb_users']['zebra_block_foe_pm']
		);
		$this->assertSame(
			array('BOOL', 0),
			$schema['add_columns']['phpbb_users']['zebra_hide_foe_content']
		);
		$this->assertContains(
			'zebra_block_foe_pm',
			$this->migration()->revert_schema()['drop_columns']['phpbb_users']
		);
		$this->assertContains(
			'zebra_hide_foe_content',
			$this->migration()->revert_schema()['drop_columns']['phpbb_users']
		);
	}

	public function test_data_updates_extension_version()
	{
		$this->assertSame(array(
			array('config.update', array('zebra_enhance_version', '2.5.0')),
		), $this->migration()->update_data());
	}
}
