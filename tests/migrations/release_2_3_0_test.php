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

class release_2_3_0_test extends \phpbb_test_case
{
	protected function migration()
	{
		return new \anavaro\zebraenhance\migrations\v23x\release_2_3_0(
			new \phpbb\config\config(array('zebra_enhance_version' => '2.2.0')),
			$this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\db\tools\tools_interface')->getMock(),
			'./',
			'php',
			'phpbb_'
		);
	}

	public function test_schema_adds_owner_scoped_circles_and_memberships()
	{
		$schema = $this->migration()->update_schema();
		$this->assertArrayHasKey('phpbb_zebra_circles', $schema['add_tables']);
		$this->assertArrayHasKey('phpbb_zebra_circle_members', $schema['add_tables']);
		$this->assertSame(
			array('UNIQUE', array('owner_id', 'circle_name_clean')),
			$schema['add_tables']['phpbb_zebra_circles']['KEYS']['owner_name']
		);
		$this->assertSame(
			array('circle_id', 'friend_id'),
			$schema['add_tables']['phpbb_zebra_circle_members']['PRIMARY_KEY']
		);
	}

	public function test_data_updates_extension_version()
	{
		$this->assertSame(array(
			array('config.update', array('zebra_enhance_version', '2.3.0')),
		), $this->migration()->update_data());
	}
}
