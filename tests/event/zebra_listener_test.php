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
	protected $template;
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
		$this->template = $this->getMockBuilder('\phpbb\template\template')->getMock();

		$this->listener = new \anavaro\zebraenhance\event\zebra_listener(
			$this->relationships,
			$this->getMockBuilder('\phpbb\user_loader')->disableOriginalConstructor()->getMock(),
			$this->auth,
			$this->getMockBuilder('\phpbb\request\request_interface')->getMock(),
			$this->template,
			$this->user,
			$language,
			$this->getMockBuilder('\phpbb\controller\helper')->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder('\phpbb\pagination')->disableOriginalConstructor()->getMock(),
			new \phpbb\config\config(array('allow_birthdays' => 1)),
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
			'core.text_formatter_s9e_render_after',
			'core.viewforum_modify_topics_data',
			'core.viewforum_modify_topicrow',
			'core.search_modify_rowset',
			'core.search_modify_tpl_ary',
			'core.page_header',
			'core.notification_manager_add_notifications',
			'core.delete_user_before',
			'core.memberlist_view_profile',
			'core.memberlist_modify_view_profile_template_vars',
			'core.memberlist_prepare_profile_data',
			'core.viewtopic_cache_user_data',
			'core.viewtopic_modify_post_row',
			'core.index_modify_birthdays_sql',
			'core.index_modify_birthdays_list',
			'core.index_modify_page_title',
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

	public function test_remove_mode_drops_foe_topics_from_topic_search_results()
	{
		$this->user->data['zebra_hide_foe_content'] = 1;
		$this->relationships->method('get_foe_identities')->with(2)->willReturn(array(3 => 'user3'));
		$show_results = 'topics';
		$rowset = array(
			20 => array('topic_poster' => 3, 'poster_id' => 4),
			21 => array('topic_poster' => 4, 'poster_id' => 3),
		);
		$event = new \phpbb\event\data(compact('show_results', 'rowset'));

		$this->listener->hide_foe_search_results($event);

		$this->assertSame(array(21 => array('topic_poster' => 4, 'poster_id' => 3)), $event['rowset']);
	}

	public function test_remove_mode_drops_foe_topics_from_viewforum()
	{
		$this->user->data['zebra_hide_foe_content'] = 1;
		$this->relationships->method('get_foe_identities')->with(2)->willReturn(array(3 => 'user3'));
		$topic_list = array(30, 31, 32);
		$rowset = array(
			30 => array('topic_poster' => 4),
			31 => array('topic_poster' => 3),
			32 => array('topic_poster' => 5),
		);
		$total_topic_count = 3;
		$event = new \phpbb\event\data(compact('topic_list', 'rowset', 'total_topic_count'));

		$this->listener->hide_foe_topics($event);

		$this->assertSame(array(30, 32), $event['topic_list']);
		$this->assertSame(array(30, 32), array_keys($event['rowset']));
	}

	public function test_notice_mode_keeps_foe_posts_and_topics_for_phpbb_placeholders()
	{
		$this->user->data['zebra_hide_foe_content'] = 2;
		$this->relationships->expects($this->never())->method('get_foe_identities');
		$rowset = array(10 => array('poster_id' => 3), 11 => array('poster_id' => 4));
		$topic_list = array(10, 11);
		$show_results = 'posts';
		$post_event = new \phpbb\event\data(compact('rowset'));
		$topic_event = new \phpbb\event\data(compact('topic_list', 'rowset'));
		$search_event = new \phpbb\event\data(compact('show_results', 'rowset'));

		$this->listener->hide_foe_posts($post_event);
		$this->listener->hide_foe_topics($topic_event);
		$this->listener->hide_foe_search_results($search_event);

		$this->assertSame($rowset, $post_event['rowset']);
		$this->assertSame($rowset, $topic_event['rowset']);
		$this->assertSame($rowset, $search_event['rowset']);
	}

	public function test_notice_mode_collapses_foe_quotes_behind_a_notice()
	{
		$this->user->data['zebra_hide_foe_content'] = 2;
		$this->relationships->expects($this->once())
			->method('get_foe_identities')
			->with(2)
			->willReturn(array(3 => 'foe user'));
		$xml = '<r><QUOTE author="Foe User" user_id="3"><t>collapsed-modern</t></QUOTE>'
			. '<QUOTE author="Friend" user_id="4"><t>open-friend</t></QUOTE>'
			. '<QUOTE user_id="3"><t>collapsed-anonymous</t></QUOTE></r>';
		$event = new \phpbb\event\data(compact('xml'));

		$this->listener->hide_foe_quotes($event);

		$this->assertStringContainsString('collapsed-modern', $event['xml']);
		$this->assertStringContainsString('collapsed-anonymous', $event['xml']);
		$this->assertStringContainsString("\u{E000}", $event['xml']);

		// Stand in for the renderer: the XML text is what reaches the HTML.
		$html = $event['xml'];
		$render_event = new \phpbb\event\data(compact('html'));
		$this->listener->insert_foe_quote_notices($render_event);
		$html = $render_event['html'];

		$this->assertStringNotContainsString("\u{E000}", $html);
		$this->assertSame(2, substr_count($html, '<div class="zebraenhance-foe-quote"><details><summary>'));
		$this->assertSame(2, substr_count($html, '</details></div>'));
		$this->assertStringContainsString('<summary>ZEBRAENHANCE_FOE_QUOTE_HIDDEN<strong>Foe User</strong>', $html);
		$this->assertStringContainsString('<summary>ZEBRAENHANCE_FOE_QUOTE_HIDDEN_UNKNOWN', $html);
		$this->assertSame(1, preg_match('#<details>.*collapsed-modern.*</details></div><QUOTE author="Friend"#s', $html));
		$this->assertStringNotContainsString('</details></div>' . "\u{E000}", $html);
	}

	public function test_quote_notices_only_replace_placeholders_from_the_current_render()
	{
		$html = "<p>\u{E000}123456789.0.1\u{E001}</p>";
		$event = new \phpbb\event\data(compact('html'));

		$this->listener->insert_foe_quote_notices($event);

		$this->assertSame($html, $event['html']);
	}

	public function test_notice_assets_are_loaded_only_in_notice_mode()
	{
		$this->template->expects($this->once())
			->method('assign_var')
			->with('S_ZEBRAENHANCE_FOE_CONTENT_NOTICE', true);

		$this->user->data['zebra_hide_foe_content'] = 1;
		$this->listener->assign_foe_content_assets();
		$this->user->data['zebra_hide_foe_content'] = 2;
		$this->listener->assign_foe_content_assets();
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

	public function test_profile_friend_list_shows_the_first_page_and_a_show_more_link()
	{
		$listener = $this->profile_listener(false, 0, $assigned);
		$this->relationships->method('can_view_friend_list')->willReturn(true);
		$this->relationships->method('count_friends')->with(5)->willReturn(120);
		$this->relationships->expects($this->once())
			->method('get_friends')
			->with(5, 50)
			->willReturn($this->friend_rows(10, 50));
		$this->relationships->method('get_mutual_friends')->willReturn(array());

		$listener->prepare_friends($this->profile_event());

		$this->assertSame(120, $assigned['ZEBRAENHANCE_FRIENDS_TOTAL']);
		$this->assertSame('ZEBRAENHANCE_FRIENDS_SHOWN|120|50', $assigned['ZEBRAENHANCE_FRIENDS_SHOWN']);
		$this->assertStringContainsString('zebraenhance_friends_start=50#zebraenhance-friend-list', $assigned['U_ZEBRAENHANCE_MORE_FRIENDS']);
		$this->assertNull($listener->sent);
	}

	public function test_profile_friend_list_without_javascript_keeps_earlier_friends()
	{
		$listener = $this->profile_listener(false, 50, $assigned);
		$this->relationships->method('can_view_friend_list')->willReturn(true);
		$this->relationships->method('count_friends')->willReturn(70);
		$this->relationships->expects($this->once())
			->method('get_friends')
			->with(5, 100)
			->willReturn($this->friend_rows(10, 70));
		$this->relationships->method('get_mutual_friends')->willReturn(array());

		$listener->prepare_friends($this->profile_event());

		$this->assertSame('ZEBRAENHANCE_FRIENDS_SHOWN|70|70', $assigned['ZEBRAENHANCE_FRIENDS_SHOWN']);
		$this->assertSame('', $assigned['U_ZEBRAENHANCE_MORE_FRIENDS']);
	}

	public function test_short_profile_friend_list_has_no_counter()
	{
		$listener = $this->profile_listener(false, 0, $assigned);
		$this->relationships->method('can_view_friend_list')->willReturn(true);
		$this->relationships->method('count_friends')->willReturn(3);
		$this->relationships->method('get_friends')->willReturn($this->friend_rows(10, 3));
		$this->relationships->method('get_mutual_friends')->willReturn(array());

		$listener->prepare_friends($this->profile_event());

		$this->assertSame('', $assigned['ZEBRAENHANCE_FRIENDS_SHOWN']);
		$this->assertSame('', $assigned['U_ZEBRAENHANCE_MORE_FRIENDS']);
	}

	public function test_show_more_returns_the_next_page_as_json()
	{
		$listener = $this->profile_listener(true, 50, $assigned);
		$this->relationships->method('can_view_friend_list')->willReturn(true);
		$this->relationships->method('count_friends')->willReturn(120);
		$this->relationships->expects($this->once())
			->method('get_friends')
			->with(5, 50, 50)
			->willReturn($this->friend_rows(60, 50));
		$this->relationships->expects($this->never())->method('get_mutual_friends');
		$this->template->expects($this->once())->method('set_filenames');
		$this->template->expects($this->once())
			->method('assign_display')
			->with('zebraenhance_profile_friends')
			->willReturn('<li>friend</li>');

		$listener->prepare_friends($this->profile_event());

		$this->assertSame(array(array(
			'success'    => true,
			'html'       => '<li>friend</li>',
			'shown'      => 'ZEBRAENHANCE_FRIENDS_SHOWN|120|100',
			'next_start' => 100,
		), 200), $listener->sent);
		$this->assertSame(array(), $assigned);
	}

	public function test_show_more_reports_the_last_page()
	{
		$listener = $this->profile_listener(true, 100, $assigned);
		$this->relationships->method('can_view_friend_list')->willReturn(true);
		$this->relationships->method('count_friends')->willReturn(120);
		$this->relationships->method('get_friends')->with(5, 50, 100)->willReturn($this->friend_rows(110, 20));
		$this->template->method('assign_display')->willReturn('<li>friend</li>');

		$listener->prepare_friends($this->profile_event());

		$this->assertSame('ZEBRAENHANCE_FRIENDS_SHOWN|120|120', $listener->sent[0]['shown']);
		$this->assertSame(0, $listener->sent[0]['next_start']);
	}

	public function test_show_more_respects_friend_list_privacy()
	{
		$listener = $this->profile_listener(true, 50, $assigned);
		$this->relationships->method('can_view_friend_list')->willReturn(false);
		$this->relationships->expects($this->never())->method('get_friends');

		$listener->prepare_friends($this->profile_event());

		$this->assertSame(array(array(
			'success' => false,
			'message' => 'FRIENDLIST_ERROR_ACCESS',
		), 403), $listener->sent);
	}

	public function test_show_more_requires_the_friend_permission()
	{
		$listener = $this->profile_listener(true, 50, $assigned, false);
		$this->relationships->expects($this->never())->method('can_view_friend_list');
		$this->relationships->expects($this->never())->method('get_friends');

		$listener->prepare_friends($this->profile_event());

		$this->assertSame(403, $listener->sent[1]);
	}

	public function test_profile_age_is_hidden_when_the_birthday_is_not_visible()
	{
		$this->relationships->expects($this->once())
			->method('filter_visible_owners')
			->with(2, array(5 => 3), false, true)
			->willReturn(array());
		$data = array('user_id' => 5, 'zebra_birthday_show' => 3);
		$template_data = array('AGE' => 30, 'USERNAME' => 'user5');
		$event = new \phpbb\event\data(compact('data', 'template_data'));

		$this->listener->hide_profile_age($event);

		$this->assertSame('', $event['template_data']['AGE']);
		$this->assertSame('user5', $event['template_data']['USERNAME']);
	}

	public function test_profile_age_stays_when_the_birthday_is_visible_or_public()
	{
		$this->relationships->expects($this->once())
			->method('filter_visible_owners')
			->willReturn(array(5));
		$template_data = array('AGE' => 30);

		$data = array('user_id' => 5, 'zebra_birthday_show' => 1);
		$event = new \phpbb\event\data(compact('data', 'template_data'));
		$this->listener->hide_profile_age($event);
		$this->assertSame(30, $event['template_data']['AGE']);

		$data = array('user_id' => 6, 'zebra_birthday_show' => 0);
		$event = new \phpbb\event\data(compact('data', 'template_data'));
		$this->listener->hide_profile_age($event);
		$this->assertSame(30, $event['template_data']['AGE']);
	}

	public function test_poster_ages_are_checked_once_per_page()
	{
		foreach (array(5 => 3, 6 => 2, 7 => 0) as $poster_id => $visibility)
		{
			$row = array('zebra_birthday_show' => $visibility);
			$user_cache_data = array();
			$this->listener->collect_poster_birthday_visibility(new \phpbb\event\data(compact('user_cache_data', 'poster_id', 'row')));
		}
		$this->relationships->expects($this->once())
			->method('filter_visible_owners')
			->with(2, array(5 => 3, 6 => 2))
			->willReturn(array(6));

		$ages = array();
		foreach (array(5, 6, 7, 5) as $poster_id)
		{
			$post_row = array('POSTER_AGE' => 40);
			$event = new \phpbb\event\data(compact('poster_id', 'post_row'));
			$this->listener->hide_poster_age($event);
			$ages[] = $event['post_row']['POSTER_AGE'];
		}

		$this->assertSame(array('', 40, 40, ''), $ages);
	}

	public function test_index_birthday_list_leaves_out_hidden_birthdays()
	{
		$sql_ary = array('SELECT' => 'u.user_id, u.username, u.user_colour, u.user_birthday');
		$event = new \phpbb\event\data(compact('sql_ary'));
		$this->listener->select_birthday_visibility($event);
		$this->assertSame('u.user_id, u.username, u.user_colour, u.user_birthday, u.zebra_birthday_show', $event['sql_ary']['SELECT']);

		$this->relationships->expects($this->once())
			->method('filter_visible_owners')
			->with(2, array(4 => 3, 5 => 5))
			->willReturn(array(4));
		$rows = array(
			array('user_id' => 3, 'zebra_birthday_show' => 0),
			array('user_id' => 4, 'zebra_birthday_show' => 3),
			array('user_id' => 5, 'zebra_birthday_show' => 5),
		);
		$birthdays = array(
			array('USERNAME' => 'user3', 'AGE' => 20),
			array('USERNAME' => 'user4', 'AGE' => ''),
			array('USERNAME' => 'user5', 'AGE' => 40),
		);
		$event = new \phpbb\event\data(compact('birthdays', 'rows'));
		$this->listener->hide_index_birthdays($event);

		$this->assertSame(array(3, 4), array_column($event['rows'], 'user_id'));
		$this->assertSame(array('user3', 'user4'), array_column($event['birthdays'], 'USERNAME'));

		$this->template->expects($this->once())
			->method('assign_var')
			->with('BIRTHDAY_LIST', 'user3 (20)COMMA_SEPARATORuser4');
		$this->listener->rebuild_birthday_list();
	}

	public function test_index_birthday_list_is_untouched_without_restrictions()
	{
		$this->relationships->expects($this->never())->method('filter_visible_owners');
		$this->template->expects($this->never())->method('assign_var');
		$rows = array(array('user_id' => 3, 'zebra_birthday_show' => 0));
		$birthdays = array(array('USERNAME' => 'user3', 'AGE' => 20));
		$event = new \phpbb\event\data(compact('birthdays', 'rows'));

		$this->listener->hide_index_birthdays($event);
		$this->listener->rebuild_birthday_list();

		$this->assertSame($rows, $event['rows']);
	}

	protected function profile_listener($ajax, $start, &$assigned, $can_use = true)
	{
		global $phpbb_dispatcher;
		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();
		$assigned = array();
		$this->auth->method('acl_get')->willReturnCallback(function ($option) use ($can_use)
		{
			return $option === 'u_zebraenhance_use_friend_requests' && $can_use;
		});
		$request = $this->getMockBuilder('\phpbb\request\request_interface')->getMock();
		$request->method('is_ajax')->willReturn($ajax);
		$request->method('is_set')->willReturnCallback(function ($name) use ($ajax)
		{
			return $ajax && $name === 'zebraenhance_friends_start';
		});
		$request->method('variable')->willReturnCallback(function ($name, $default) use ($start)
		{
			return $name === 'zebraenhance_friends_start' ? $start : $default;
		});
		$language = $this->getMockBuilder('\phpbb\language\language')
			->disableOriginalConstructor()
			->getMock();
		$language->method('lang')->willReturnCallback(function ()
		{
			return implode('|', func_get_args());
		});
		$user_loader = $this->getMockBuilder('\phpbb\user_loader')->disableOriginalConstructor()->getMock();
		$user_loader->method('get_username')->willReturn('friend');
		$user_loader->method('get_avatar')->willReturn('');
		$this->template->method('assign_vars')->willReturnCallback(function ($vars) use (&$assigned)
		{
			$assigned = array_merge($assigned, $vars);
		});

		return new class(
			$this->relationships,
			$user_loader,
			$this->auth,
			$request,
			$this->template,
			$this->user,
			$language,
			$this->getMockBuilder('\phpbb\controller\helper')->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder('\phpbb\pagination')->disableOriginalConstructor()->getMock(),
			new \phpbb\config\config(array('allow_birthdays' => 1)),
			'./',
			'php'
		) extends \anavaro\zebraenhance\event\zebra_listener
		{
			public $sent;

			protected function send_json(array $data, $status_code = 200)
			{
				$this->sent = array($data, $status_code);
			}
		};
	}

	protected function profile_event()
	{
		// The viewer is already a friend, so no request controls are prepared.
		$member = array('user_id' => 5, 'profile_friend_show' => 0);
		$friend = true;
		$foe = false;

		return new \phpbb\event\data(compact('member', 'friend', 'foe'));
	}

	protected function friend_rows($first_id, $count)
	{
		$rows = array();
		for ($i = 0; $i < $count; $i++)
		{
			$rows[] = array('zebra_id' => $first_id + $i, 'bff' => 0, 'username' => 'u' . $i, 'user_colour' => '');
		}

		return $rows;
	}
}
