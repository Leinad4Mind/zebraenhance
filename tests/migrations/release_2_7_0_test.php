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
	protected function migration($version = '2.6.0')
	{
		return new \anavaro\zebraenhance\migrations\v27x\release_2_7_0(
			new \phpbb\config\config(array('zebra_enhance_version' => $version)),
			$this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\db\tools\tools_interface')->getMock(),
			'./',
			'php',
			'phpbb_'
		);
	}

	public function test_schema_adds_public_by_default_birthday_visibility()
	{
		$schema = $this->migration()->update_schema();
		$this->assertSame(array('TINT:1', 0), $schema['add_columns']['phpbb_users']['zebra_birthday_show']);

		$revert = $this->migration()->revert_schema();
		$this->assertSame(array('zebra_birthday_show'), $revert['drop_columns']['phpbb_users']);
	}

	public function test_data_enables_friend_posts_and_records_the_version()
	{
		$this->assertSame(
			array(
				array('config.add', array('zebraenhance_friend_posts', 1)),
				array('config.update', array('zebra_enhance_version', '2.7.0')),
			),
			$this->migration()->update_data()
		);
		$this->assertSame(
			array('\anavaro\zebraenhance\migrations\v26x\release_2_6_0'),
			\anavaro\zebraenhance\migrations\v27x\release_2_7_0::depends_on()
		);
	}

	public function test_effectively_installed_follows_the_version()
	{
		$this->assertFalse($this->migration('2.6.0')->effectively_installed());
		$this->assertTrue($this->migration('2.7.0')->effectively_installed());
	}
}
