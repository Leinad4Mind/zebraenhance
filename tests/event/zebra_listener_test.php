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
	protected $user;
	protected $listener;

	// phpcs:ignore PhpbbCodingStandard.NamingConventions.LowercaseUnderscoredFunctions.NotAllowed -- PHPUnit API
	public function setUp(): void
	{
		parent::setUp();

		$this->relationships = $this->getMockBuilder('\anavaro\zebraenhance\service\relationship_manager')
			->disableOriginalConstructor()
			->getMock();
		$this->relationships->method('foe_feature_enabled')->willReturn(true);
		$this->auth = $this->getMockBuilder('\phpbb\auth\auth')
			->disableOriginalConstructor()
			->getMock();
		$this->user = $this->getMockBuilder('\phpbb\user')
			->disableOriginalConstructor()
			->getMock();
		$this->user->data = array(
			'user_id' => 2,
			'profile_friend_show' => 5,
			'is_registered' => true,
			'user_type' => USER_NORMAL,
			'zebra_hide_foe_content' => false,
			'zebra_mute_foe_notifications' => false,
		);
		$language = $this->getMockBuilder('\phpbb\language\language')
			->disableOriginalConstructor()
			->getMock();
		$language->method('lang')->willReturnArgument(0);

		$this->listener = new \anavaro\zebraenhance\event\zebra_listener(
			$this->relationships,
			$this->getMockBuilder('\phpbb\user_loader')->disableOriginalConstructor()->getMock(),
			$this->auth,
			$this->getMockBuilder('\phpbb\request\request_interface')->getMock(),
			$this->getMockBuilder('\phpbb\template\template')->getMock(),
			$this->user,
			$language,
			$this->getMockBuilder('\phpbb\controller\helper')->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder('\phpbb\pagination')->disableOriginalConstructor()->getMock(),
			'./',
			'php'
		);
	}

	public function test_subscribed_events_are_scoped()
	{
		$this->assertSame(array(
			'core.user_setup',
			'core.ucp_add_zebra',
			'core.ucp_remove_zebra',
			'core.ucp_display_module_before',
			'core.message_list_actions',
			'core.submit_pm_before',
			'core.viewtopic_modify_post_data',
			'core.topic_review_modify_post_list',
			'core.text_formatter_s9e_render_before',
			'core.search_modify_rowset',
			'core.notification_manager_add_notifications',
			'core.delete_user_before',
			'core.memberlist_view_profile',
			'core.memberlist_modify_view_profile_template_vars',
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
		$this->auth->expects($this->once())->method('acl_get')->with('u_zebraenhance_use_friend_requests')->willReturn(true);
		$this->relationships->expects($this->once())
			->method('process_additions')
			->willReturnCallback(function ($actual_mode, $actual_rows, &$results) use ($mode, $sql_ary)
			{
				$this->assertSame($mode, $actual_mode);
				$this->assertSame($sql_ary, $actual_rows);
				$results = array('created');
				return array();
			});

		$event = new \phpbb\event\data(compact('mode', 'sql_ary'));
		$this->listener->zebra_confirm_add($event);
		$this->assertSame(array(), $event['sql_ary']);
	}

	public function test_friend_removal_is_symmetric_and_core_delete_is_suppressed()
	{
		$mode = 'friends';
		$user_ids = array(3, 4);
		$this->relationships->expects($this->once())
			->method('remove_relationships')
			->with(2, array(3, 4));

		$event = new \phpbb\event\data(compact('mode', 'user_ids'));
		$this->listener->zebra_confirm_remove($event);
		$this->assertSame(array(0), $event['user_ids']);
	}

	public function test_foe_removal_cleans_metadata_and_suppresses_core_delete()
	{
		$mode = 'foes';
		$user_ids = array(3, 4);
		$this->relationships->expects($this->once())
			->method('remove_foes')
			->with(2, $user_ids);

		$event = new \phpbb\event\data(compact('mode', 'user_ids'));
		$this->listener->zebra_confirm_remove($event);
		$this->assertSame(array(0), $event['user_ids']);
	}

	public function test_blocked_pm_recipients_are_removed_with_generic_error()
	{
		$address_list = array('u' => array(3 => 'to', 4 => 'bcc'));
		$error = array();
		$this->relationships->expects($this->once())
			->method('filter_pm_address_list')
			->with(2, $address_list)
			->willReturn(array('u' => array(4 => 'bcc')));

		$event = new \phpbb\event\data(compact('address_list', 'error'));
		$this->listener->block_foe_pm_recipients($event);

		$this->assertSame(array('u' => array(4 => 'bcc')), $event['address_list']);
		$this->assertSame(array('ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED'), $event['error']);
	}

	public function test_pm_submission_guard_ignores_message_edits()
	{
		$this->relationships->expects($this->never())->method('filter_pm_address_list');
		$mode = 'edit';
		$data = array('address_list' => array('u' => array(3 => 'to')));

		$this->listener->guard_foe_pm_submission(new \phpbb\event\data(compact('mode', 'data')));
	}

	public function test_pm_submission_guard_rejects_a_bypassed_blocked_recipient()
	{
		$address_list = array('u' => array(3 => 'to'));
		$this->relationships->expects($this->once())
			->method('filter_pm_address_list')
			->with(2, $address_list)
			->willReturn(array());
		$mode = 'post';
		$data = compact('address_list');
		$error_message = null;
		set_error_handler(function ($error_number, $message) use (&$error_message)
		{
			$error_message = $message;
			return true;
		});

		try
		{
			$this->listener->guard_foe_pm_submission(new \phpbb\event\data(compact('mode', 'data')));
		}
		finally
		{
			restore_error_handler();
		}

		$this->assertSame('ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED', $error_message);
	}

	public function test_opted_in_user_does_not_receive_foe_post_rows()
	{
		$this->user->data['zebra_hide_foe_content'] = true;
		$this->relationships->expects($this->once())
			->method('get_foe_identities')
			->with(2)
			->willReturn(array(3 => 'user3'));
		$rowset = array(
			10 => array('poster_id' => 3, 'foe' => 1),
			11 => array('poster_id' => 4, 'foe' => 0),
		);
		$event = new \phpbb\event\data(compact('rowset'));

		$this->listener->hide_foe_posts($event);

		$this->assertSame(array(11 => array('poster_id' => 4, 'foe' => 0)), $event['rowset']);
	}

	public function test_opted_in_user_does_not_receive_identifiable_foe_quotes()
	{
		$this->user->data['zebra_hide_foe_content'] = true;
		$this->relationships->expects($this->once())
			->method('get_foe_identities')
			->with(2)
			->willReturn(array(3 => 'foe user'));
		$xml = '<r><QUOTE author="Foe User" user_id="3"><t>hidden-modern</t></QUOTE>'
			. '<QUOTE author="Friend" user_id="4"><t>visible</t><QUOTE author="Foe User"><t>hidden-legacy</t></QUOTE></QUOTE>'
			. '<QUOTE author="Foe User" user_id="4"><t>kept-mismatched-id</t></QUOTE></r>';
		$event = new \phpbb\event\data(compact('xml'));

		$this->listener->hide_foe_quotes($event);

		$this->assertStringNotContainsString('hidden-modern', $event['xml']);
		$this->assertStringNotContainsString('hidden-legacy', $event['xml']);
		$this->assertStringContainsString('visible', $event['xml']);
		$this->assertStringContainsString('kept-mismatched-id', $event['xml']);
	}

	public function test_foe_quotes_are_unchanged_without_opt_in()
	{
		$this->relationships->expects($this->once())->method('get_foe_identities')->with(2)->willReturn(array());
		$xml = '<r><QUOTE author="Foe User" user_id="3"><t>visible-by-default</t></QUOTE></r>';
		$event = new \phpbb\event\data(compact('xml'));

		$this->listener->hide_foe_quotes($event);

		$this->assertSame($xml, $event['xml']);
	}

	public function test_opted_in_user_does_not_receive_foe_post_search_results()
	{
		$this->user->data['zebra_hide_foe_content'] = true;
		$this->relationships->expects($this->once())
			->method('get_foe_identities')
			->with(2)
			->willReturn(array(3 => 'user3'));
		$show_results = 'posts';
		$zebra = array('foe' => array(3));
		$rowset = array(
			10 => array('poster_id' => 3, 'post_text' => 'hidden'),
			11 => array('poster_id' => 4, 'post_text' => 'visible'),
		);
		$event = new \phpbb\event\data(compact('show_results', 'zebra', 'rowset'));

		$this->listener->hide_foe_search_results($event);

		$this->assertSame(array(
			11 => array('poster_id' => 4, 'post_text' => 'visible'),
		), $event['rowset']);
	}

	public function test_user_content_notifications_are_filtered_by_foe_policy()
	{
		$notification_type_name = 'notification.type.quote';
		$data = array('poster_id' => 3);
		$notify_users = array(2 => array('notification.method.board'), 4 => array('notification.method.board'));
		$this->relationships->expects($this->once())
			->method('filter_foe_notification_recipients')
			->with(3, $notify_users)
			->willReturn(array(4 => array('notification.method.board')));

		$event = new \phpbb\event\data(compact('notification_type_name', 'data', 'notify_users'));
		$this->listener->mute_foe_notifications($event);
		$this->assertSame(array(4 => array('notification.method.board')), $event['notify_users']);
	}

	public function test_administrative_notifications_are_not_filtered()
	{
		$this->relationships->expects($this->never())->method('filter_foe_notification_recipients');
		$notification_type_name = 'notification.type.report_post';
		$data = array('poster_id' => 3);
		$notify_users = array(2 => array('notification.method.board'));

		$this->listener->mute_foe_notifications(new \phpbb\event\data(compact('notification_type_name', 'data', 'notify_users')));
	}

	public function test_profile_context_can_hide_native_add_friend_link()
	{
		foreach (array('profile_context_ready', 'profile_hide_native_add') as $property_name)
		{
			$property = new \ReflectionProperty($this->listener, $property_name);
			$property->setAccessible(true);
			$property->setValue($this->listener, true);
		}

		$template_ary = array('U_ADD_FRIEND' => './ucp.php?add=user3', 'U_ADD_FOE' => './ucp.php?mode=foes');
		$event = new \phpbb\event\data(compact('template_ary'));
		$this->listener->modify_profile_template_vars($event);

		$this->assertSame('', $event['template_ary']['U_ADD_FRIEND']);
		$this->assertSame('./ucp.php?mode=foes', $event['template_ary']['U_ADD_FOE']);
	}
}
