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

class release_2_2_0_test extends \phpbb_test_case
{
	protected function migration()
	{
		return new \anavaro\zebraenhance\migrations\v22x\release_2_2_0(
			new \phpbb\config\config(array('zebra_enhance_version' => '2.1.0')),
			$this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\db\tools\tools_interface')->getMock(),
			'./',
			'php',
			'phpbb_'
		);
	}

	public function test_schema_adds_optional_request_message()
	{
		$schema = $this->migration()->update_schema();

		$this->assertSame(
			array('VCHAR_UNI:255', ''),
			$schema['add_columns']['phpbb_zebra_requests']['request_message']
		);
	}

	public function test_data_updates_extension_version()
	{
		$this->assertSame(array(
			array('config.update', array('zebra_enhance_version', '2.2.0')),
		), $this->migration()->update_data());
	}
}
