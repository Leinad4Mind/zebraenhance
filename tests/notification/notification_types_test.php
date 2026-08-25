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
		$auth->expects($this->once())->method('acl_get')->with('u_zebraenhance_use_friend_requests')->willReturn(false);
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
		$this->disable_word_censor_for_test();
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

	public function test_request_message_is_safe_for_html_and_decoded_for_plaintext_email()
	{
		$this->disable_word_censor_for_test();
		$auth = $this->getMockBuilder('\phpbb\auth\auth')->disableOriginalConstructor()->getMock();
		$notification = $this->notification('\anavaro\zebraenhance\notification\zebraadd', $auth);
		$user_loader = $this->getMockBuilder('\phpbb\user_loader')->disableOriginalConstructor()->getMock();
		$user_loader->expects($this->once())->method('get_user')->with(3)->willReturn(array(
			'username' => 'Requester',
		));
		$notification->set_user_loader($user_loader);
		$notification->create_insert_array(array(
			'request_id' => 42,
			'requester_id' => 3,
			'request_message' => 'Fish &amp; Chips &lt;b&gt;',
		));

		$this->assertSame('Fish &amp; Chips &lt;b&gt;', $notification->get_reference());
		$this->assertSame(
			'Fish & Chips <b>',
			$notification->get_email_template_variables()['REQUEST_MESSAGE']
		);
	}

	protected function disable_word_censor_for_test()
	{
		global $config, $user, $auth;

		$config = new \phpbb\config\config(array('allow_nocensors' => 1));
		$user = $this->getMockBuilder('\phpbb\user')->disableOriginalConstructor()->getMock();
		$user->method('optionget')->with('viewcensors')->willReturn(false);
		$auth = $this->getMockBuilder('\phpbb\auth\auth')->disableOriginalConstructor()->getMock();
		$auth->method('acl_get')->with('u_chgcensors')->willReturn(true);
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
