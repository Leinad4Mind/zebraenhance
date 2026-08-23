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

namespace anavaro\zebraenhance\tests\event;

class zebra_listener_test extends \phpbb_test_case
{
	protected $relationships;
	protected $auth;
	protected $listener;

	// phpcs:ignore PhpbbCodingStandard.NamingConventions.LowercaseUnderscoredFunctions.NotAllowed -- PHPUnit API
	public function setUp(): void
	{
		parent::setUp();

		$this->relationships = $this->getMockBuilder('\anavaro\zebraenhance\service\relationship_manager')
			->disableOriginalConstructor()
			->getMock();
		$this->auth = $this->getMockBuilder('\phpbb\auth\auth')
			->disableOriginalConstructor()
			->getMock();
		$user = $this->getMockBuilder('\phpbb\user')
			->disableOriginalConstructor()
			->getMock();
		$user->data = array('user_id' => 2, 'profile_friend_show' => 5);

		$this->listener = new \anavaro\zebraenhance\event\zebra_listener(
			$this->relationships,
			$this->getMockBuilder('\phpbb\user_loader')->disableOriginalConstructor()->getMock(),
			$this->auth,
			$this->getMockBuilder('\phpbb\db\driver\driver_interface')->getMock(),
			$this->getMockBuilder('\phpbb\request\request_interface')->getMock(),
			$this->getMockBuilder('\phpbb\template\template')->getMock(),
			$user,
			$this->getMockBuilder('\phpbb\language\language')->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder('\phpbb\controller\helper')->disableOriginalConstructor()->getMock(),
			'./',
			'php',
			'phpbb_users'
		);
	}

	public function test_subscribed_events_are_scoped()
	{
		$this->assertSame(array(
			'core.user_setup',
			'core.ucp_add_zebra',
			'core.ucp_remove_zebra',
			'core.ucp_display_module_before',
			'core.delete_user_before',
			'core.memberlist_view_profile',
		), array_keys(\anavaro\zebraenhance\event\zebra_listener::getSubscribedEvents()));
	}

	public function test_language_is_registered_through_user_setup_event()
	{
		$lang_set_ext = array();
		$event = new \phpbb\event\data(compact('lang_set_ext'));
		$this->listener->load_language_on_setup($event);

		$this->assertSame(array(array(
			'ext_name' => 'anavaro/zebraenhance',
			'lang_set' => 'zebra_enchance',
		)), $event['lang_set_ext']);
	}

	public function test_friend_addition_is_delegated_and_core_insert_is_suppressed()
	{
		$mode = 'friends';
		$sql_ary = array(array('user_id' => 2, 'zebra_id' => 3, 'friend' => 1));
		$this->auth->expects($this->once())->method('acl_get')->with('u_ze_use')->willReturn(true);
		$this->relationships->expects($this->once())
			->method('process_additions')
			->with($mode, $sql_ary)
			->willReturn(array());

		$event = new \phpbb\event\data(compact('mode', 'sql_ary'));
		$this->listener->zebra_confirm_add($event);
		$this->assertSame(array(), $event['sql_ary']);
	}

	public function test_friend_removal_is_symmetric_and_core_delete_is_suppressed()
	{
		$mode = 'friends';
		$user_ids = array(3, 4);
		$this->relationships->expects($this->exactly(2))
			->method('remove_relationship')
			->withConsecutive(array(2, 3), array(2, 4));

		$event = new \phpbb\event\data(compact('mode', 'user_ids'));
		$this->listener->zebra_confirm_remove($event);
		$this->assertSame(array(0), $event['user_ids']);
	}
}
