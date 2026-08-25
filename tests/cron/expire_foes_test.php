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

namespace anavaro\zebraenhance\tests\cron;

class expire_foes_test extends \phpbb_test_case
{
	public function test_run_expires_foes_and_records_last_run()
	{
		$relationships = $this->getMockBuilder('\anavaro\zebraenhance\service\relationship_manager')
			->disableOriginalConstructor()
			->getMock();
		$relationships->expects($this->once())->method('expire_foes');
		$config = new \phpbb\config\config(array(
			'zebraenhance_foes_enhancement' => 1,
			'zebraenhance_foe_temporary'    => 1,
			'zebraenhance_foe_expiry_last_gc' => 0,
			'zebraenhance_foe_expiry_gc'      => 3600,
		));
		$task = new \anavaro\zebraenhance\cron\task\expire_foes($relationships, $config);

		$this->assertTrue($task->should_run());
		$before = time();
		$task->run();
		$this->assertGreaterThanOrEqual($before, (int) $config['zebraenhance_foe_expiry_last_gc']);
		$this->assertFalse($task->should_run());
	}

	public function test_task_is_disabled_by_acp_settings()
	{
		$relationships = $this->getMockBuilder('\anavaro\zebraenhance\service\relationship_manager')
			->disableOriginalConstructor()
			->getMock();
		$relationships->expects($this->never())->method('expire_foes');
		$config = new \phpbb\config\config(array(
			'zebraenhance_foes_enhancement' => 0,
			'zebraenhance_foe_temporary'    => 1,
		));
		$task = new \anavaro\zebraenhance\cron\task\expire_foes($relationships, $config);

		$this->assertFalse($task->should_run());
		$task->run();
	}
}
