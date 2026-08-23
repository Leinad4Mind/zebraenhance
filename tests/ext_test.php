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

namespace anavaro\zebraenhance\tests;

class ext_test extends \phpbb_test_case
{
	public function test_current_environment_is_enableable()
	{
		$notifications = $this->getMockBuilder('\phpbb\notification\manager')
			->disableOriginalConstructor()
			->getMock();
		$this->assertTrue($this->extension($notifications, false)->is_enableable());
	}

	public function test_enable_notifications_run_on_initial_false_state()
	{
		$notifications = $this->notification_manager('enable_notifications');
		$extension = $this->extension($notifications);

		$this->assertSame('notifications', $extension->enable_step(false));
	}

	public function test_disable_notifications_run_on_initial_false_state()
	{
		$notifications = $this->notification_manager('disable_notifications');
		$extension = $this->extension($notifications);

		$this->assertSame('notifications', $extension->disable_step(false));
	}

	public function test_purge_notifications_run_on_initial_false_state()
	{
		$notifications = $this->notification_manager('purge_notifications');
		$extension = $this->extension($notifications);

		$this->assertSame('notifications', $extension->purge_step(false));
	}

	protected function notification_manager($method)
	{
		$notifications = $this->getMockBuilder('\phpbb\notification\manager')
			->disableOriginalConstructor()
			->getMock();
		$notifications->expects($this->exactly(2))
			->method($method)
			->withConsecutive(
				array('anavaro.zebraenhance.notification.zebraadd'),
				array('anavaro.zebraenhance.notification.zebraconfirm')
			);

		return $notifications;
	}

	protected function extension($notifications, $expect_manager = true)
	{
		$container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')->getMock();
		$container->expects($expect_manager ? $this->once() : $this->never())
			->method('get')
			->with('notification_manager')
			->willReturn($notifications);

		return new \anavaro\zebraenhance\ext(
			$container,
			$this->getMockBuilder('\phpbb\finder')->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder('\phpbb\db\migrator')->disableOriginalConstructor()->getMock(),
			'anavaro/zebraenhance',
			'ext/anavaro/zebraenhance/'
		);
	}
}
