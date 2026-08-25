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

class release_2_4_0_test extends \phpbb_test_case
{
	protected function migration()
	{
		return new \anavaro\zebraenhance\migrations\v24x\release_2_4_0(
			new \phpbb\config\config(array('zebra_enhance_version' => '2.3.0')),
			$this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\db\tools\tools_interface')->getMock(),
			'./',
			'php',
			'phpbb_'
		);
	}

	public function test_report_module_and_permission_are_added()
	{
		$data = $this->migration()->update_data();
		$this->assertSame(
			array('permission.permission_set', array('ADMINISTRATORS', 'm_zebraenhance_view_private_friendlists', 'group')),
			$data[0]
		);
		$this->assertSame('module.add', $data[1][0]);
		$this->assertSame('\anavaro\zebraenhance\acp\report_module', $data[1][1][2]['module_basename']);
		$this->assertSame(array('report'), $data[1][1][2]['modes']);
		$this->assertSame(array('config.update', array('zebra_enhance_version', '2.4.0')), $data[2]);
	}
}
