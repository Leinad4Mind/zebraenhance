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

namespace anavaro\zebraenhance\tests\notification;

class notification_types_test extends \phpbb_test_case
{
	public function notification_types()
	{
		return array(
			array('\anavaro\zebraenhance\notification\zebraadd'),
			array('\anavaro\zebraenhance\notification\zebraconfirm'),
		);
	}

	/**
	 * @dataProvider notification_types
	 */
	public function test_availability_requires_extension_permission($class)
	{
		$auth = $this->getMockBuilder('\phpbb\auth\auth')->disableOriginalConstructor()->getMock();
		$auth->expects($this->once())->method('acl_get')->with('u_ze_use')->willReturn(false);
		$notification = $this->notification($class, $auth);

		$this->assertFalse($notification->is_available());
	}

	/**
	 * @dataProvider notification_types
	 */
	public function test_missing_legacy_requester_is_safe($class)
	{
		$auth = $this->getMockBuilder('\phpbb\auth\auth')->disableOriginalConstructor()->getMock();
		$notification = $this->notification($class, $auth);
		$user_loader = $this->getMockBuilder('\phpbb\user_loader')->disableOriginalConstructor()->getMock();
		$user_loader->expects($this->never())->method('get_avatar');
		$notification->set_user_loader($user_loader);

		$this->assertSame(array(), $notification->users_to_query());
		$this->assertSame('', $notification->get_avatar());
		$this->assertSame(0, $class::get_item_id(array()));
		$this->assertSame(0, $class::get_item_parent_id(array()));
	}

	public function test_email_templates_are_available()
	{
		$auth = $this->getMockBuilder('\phpbb\auth\auth')->disableOriginalConstructor()->getMock();

		$this->assertSame(
			'zebraenhance_friend_request',
			$this->notification('\anavaro\zebraenhance\notification\zebraadd', $auth)->get_email_template()
		);
		$this->assertSame(
			'zebraenhance_friend_confirmed',
			$this->notification('\anavaro\zebraenhance\notification\zebraconfirm', $auth)->get_email_template()
		);
	}

	public function test_request_message_is_stored_with_notification()
	{
		$auth = $this->getMockBuilder('\phpbb\auth\auth')->disableOriginalConstructor()->getMock();
		$notification = $this->notification('\anavaro\zebraenhance\notification\zebraadd', $auth);
		$notification->create_insert_array(array(
			'request_id' => 42,
			'requester_id' => 3,
			'request_message' => 'Hello there',
		));
		$insert = $notification->get_insert_array();
		$data = unserialize($insert['notification_data']);

		$this->assertSame('Hello there', $data['request_message']);
		$this->assertSame('Hello there', $notification->get_reference());
	}

	protected function notification($class, $auth)
	{
		return new $class(
			$this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock(),
			$auth,
			'./',
			'php',
			'phpbb_user_notifications'
		);
	}
}
