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

class release_2_9_0_test extends \phpbb_test_case
{
	protected function migration($version = '2.8.0')
	{
		return new \anavaro\zebraenhance\migrations\v29x\release_2_9_0(
			new \phpbb\config\config(array('zebra_enhance_version' => $version)),
			$this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\db\tools\tools_interface')->getMock(),
			'./',
			'php',
			'phpbb_'
		);
	}

	public function test_data_enables_friend_avatars_and_records_the_version()
	{
		$this->assertSame(array(
			array('config.add', array('zebraenhance_friend_avatars', 1)),
			array('config.update', array('zebra_enhance_version', '2.9.0')),
		), $this->migration()->update_data());
		$this->assertSame(
			array('\anavaro\zebraenhance\migrations\v28x\release_2_8_0'),
			\anavaro\zebraenhance\migrations\v29x\release_2_9_0::depends_on()
		);
	}

	public function test_effectively_installed_follows_the_version()
	{
		$this->assertFalse($this->migration('2.8.0')->effectively_installed());
		$this->assertTrue($this->migration('2.9.0')->effectively_installed());
	}
}
