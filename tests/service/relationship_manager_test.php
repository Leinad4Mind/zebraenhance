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

namespace anavaro\zebraenhance\tests\service;

class relationship_manager_test extends \phpbb_database_test_case
{
	protected $db;
	protected $db_tools;
	protected $auth;
	protected $notifications;
	protected $dispatcher;
	protected $config;
	protected $relationships;

	static protected function setup_extensions()
	{
		return array('anavaro/zebraenhance');
	}

	// phpcs:ignore PhpbbCodingStandard.NamingConventions.LowercaseUnderscoredFunctions.NotAllowed -- PHPUnit API
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/../event/fixtures/fixture.xml');
	}

	// phpcs:ignore PhpbbCodingStandard.NamingConventions.LowercaseUnderscoredFunctions.NotAllowed -- PHPUnit API
	public function setUp(): void
	{
		parent::setUp();
		$this->db = $this->new_dbal();
		$factory = new \phpbb\db\tools\factory();
		$this->db_tools = $factory->get($this->db);
		$this->ensure_extension_columns();
		$this->db->sql_query('DELETE FROM phpbb_zebra_foe_settings');
		$this->auth = $this->getMockBuilder('\phpbb\auth\auth')
			->disableOriginalConstructor()
			->getMock();
		$this->notifications = $this->getMockBuilder('\phpbb\notification\manager')
			->disableOriginalConstructor()
			->getMock();
		$this->dispatcher = $this->getMockBuilder('\phpbb\event\dispatcher_interface')->getMock();
		$this->config = new \phpbb\config\config(array(
			'ze_max_pending_requests' => 100,
			'ze_decline_cooldown_days' => 7,
			'ze_foes_enhancement' => 1,
			'ze_foe_pm' => 1,
			'ze_foe_content' => 1,
			'ze_foe_notifications' => 1,
			'ze_foe_temporary' => 1,
			'ze_foe_notes' => 1,
			'ze_foe_exceptions' => 1,
		));
		$this->relationships = new \anavaro\zebraenhance\service\relationship_manager(
			$this->db,
			$this->db_tools,
			$this->auth,
			$this->notifications,
			$this->dispatcher,
			$this->config,
			'phpbb_zebra_requests',
			'phpbb_zebra_request_cooldowns',
			'phpbb_zebra_confirm',
			'phpbb_zebra',
			'phpbb_zebra_circles',
			'phpbb_zebra_circle_members',
			'phpbb_users',
			'phpbb_user_group',
			'phpbb_notifications',
			'phpbb_notification_emails',
			'phpbb_notification_types',
			'phpbb_zebra_foe_settings'
		);
	}

	/**
	 * phpBB's database-test schema builder creates core tables after it reads
	 * extension schemas, so add_columns cannot target them in this test harness.
	 */
	protected function ensure_extension_columns()
	{
		if (!$this->db_tools->sql_column_exists('phpbb_zebra', 'bff'))
		{
			$this->db_tools->sql_column_add('phpbb_zebra', 'bff', array('UINT', 0));
		}
		if (!$this->db_tools->sql_column_exists('phpbb_users', 'profile_friend_show'))
		{
			$this->db_tools->sql_column_add('phpbb_users', 'profile_friend_show', array('UINT', 5));
		}
		if (!$this->db_tools->sql_column_exists('phpbb_users', 'zebra_changed'))
		{
			$this->db_tools->sql_column_add('phpbb_users', 'zebra_changed', array('UINT', 0));
		}
		if (!$this->db_tools->sql_column_exists('phpbb_users', 'zebra_request_policy'))
		{
			$this->db_tools->sql_column_add('phpbb_users', 'zebra_request_policy', array('UINT', 0));
		}
		if (!$this->db_tools->sql_column_exists('phpbb_users', 'zebra_block_foe_pm'))
		{
			$this->db_tools->sql_column_add('phpbb_users', 'zebra_block_foe_pm', array('BOOL', 0));
		}
		if (!$this->db_tools->sql_column_exists('phpbb_users', 'zebra_hide_foe_content'))
		{
			$this->db_tools->sql_column_add('phpbb_users', 'zebra_hide_foe_content', array('BOOL', 0));
		}
		if (!$this->db_tools->sql_column_exists('phpbb_users', 'zebra_mute_foe_notifications'))
		{
			$this->db_tools->sql_column_add('phpbb_users', 'zebra_mute_foe_notifications', array('BOOL', 0));
		}
	}

	public function test_same_direction_request_is_idempotent()
	{
		$this->notifications->expects($this->never())->method('add_notifications');
		$this->dispatcher->expects($this->never())->method('trigger_event');
		$this->assertSame('ignored', $this->relationships->request_friendship(2, 3));
		$this->assertSame(2, $this->count_rows('phpbb_zebra_requests'));
	}

	public function test_new_request_has_unique_identity_and_notification()
	{
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_CREATED,
				$this->callback(function ($data)
				{
					return $data['request_id'] > 2
						&& $data['requester_id'] === 3
						&& $data['recipient_id'] === 4
						&& $data['actor_id'] === 3
						&& $data['request_time'] > 0
						&& !isset($data['reason']);
				})
			);
		$this->notifications->expects($this->once())
			->method('add_notifications')
			->with(
				'anavaro.zebraenhance.notification.zebraadd',
				$this->callback(function ($data)
				{
					return $data['request_id'] > 2
						&& $data['requester_id'] === 3
						&& isset($data['user_id'][4]);
				})
			);

		$this->assertSame('created', $this->relationships->request_friendship(3, 4));
		$this->assertSame(3, $this->count_rows('phpbb_zebra_requests'));
	}

	public function test_request_message_is_normalized_persisted_and_dispatched()
	{
		$message = "  Let's connect!\nWe share interests.  ";
		$normalized = "Let's connect!\nWe share interests.";
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_CREATED,
				$this->callback(function ($data) use ($normalized)
				{
					return isset($data['message']) && $data['message'] === $normalized;
				})
			);
		$this->notifications->expects($this->once())
			->method('add_notifications')
			->with(
				'anavaro.zebraenhance.notification.zebraadd',
				$this->callback(function ($data) use ($normalized)
				{
					return $data['request_message'] === $normalized;
				})
			);

		$this->assertSame('created', $this->relationships->request_friendship(3, 4, $message));
		$result = $this->db->sql_query('SELECT request_message FROM phpbb_zebra_requests
			WHERE requester_id = 3 AND recipient_id = 4');
		$this->assertSame($normalized, $this->db->sql_fetchfield('request_message'));
		$this->db->sql_freeresult($result);
	}

	public function test_blocked_request_is_silently_discarded()
	{
		$this->notifications->expects($this->never())->method('add_notifications');
		$this->dispatcher->expects($this->never())->method('trigger_event');
		$this->assertSame('blocked', $this->relationships->request_friendship(5, 4));
		$this->assertSame(2, $this->count_rows('phpbb_zebra_requests'));
	}

	public function test_reverse_request_atomically_accepts_friendship()
	{
		$this->notifications->expects($this->once())
			->method('delete_notifications')
			->with('anavaro.zebraenhance.notification.zebraadd', 1, false, 3);
		$this->notifications->expects($this->once())
			->method('add_notifications')
			->with(
				'anavaro.zebraenhance.notification.zebraconfirm',
				$this->callback(function ($data)
				{
					return $data['request_id'] === 1
						&& $data['requester_id'] === 3
						&& isset($data['user_id'][2]);
				})
			);

		$this->assertSame('accepted', $this->relationships->request_friendship(3, 2));
		$this->assertSame(1, $this->count_rows('phpbb_zebra_requests'));
		$this->assertSame(2, $this->count_friend_rows(2, 3));
		$this->assertTrue($this->relationships->set_close_friend(2, 3, true));
		$this->assertTrue($this->relationships->is_close_friend(2, 3));
		$this->assertFalse($this->relationships->set_close_friend(2, 99, true));
	}

	public function test_removal_cleans_new_and_legacy_pending_rows()
	{
		$this->db->sql_query("UPDATE phpbb_zebra_requests
			SET request_message = 'Still interested'
			WHERE request_id = 2");
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_CANCELLED,
				array(
					'request_id' => 2,
					'requester_id' => 2,
					'recipient_id' => 52,
					'actor_id' => 2,
					'request_time' => 101,
					'reason' => 'relationship_removed',
					'message' => 'Still interested',
				)
			);
		$this->notifications->expects($this->once())
			->method('delete_notifications')
			->with('anavaro.zebraenhance.notification.zebraadd', 2, false, 52);

		$this->relationships->remove_relationship(2, 52);
		$this->assertSame(1, $this->count_rows('phpbb_zebra_requests'));

		$sql = 'SELECT COUNT(*) AS total FROM phpbb_zebra_confirm
			WHERE (user_id = 2 AND zebra_id = 52)
				OR (user_id = 52 AND zebra_id = 2)';
		$result = $this->db->sql_query($sql);
		$this->assertSame(0, (int) $this->db->sql_fetchfield('total'));
		$this->db->sql_freeresult($result);
	}

	public function test_batch_removal_cleans_all_selected_relationships()
	{
		$this->notifications->expects($this->exactly(2))->method('delete_notifications');

		$this->relationships->remove_relationships(2, array(3, 52, 52, 2, 0));

		$this->assertSame(0, $this->count_rows('phpbb_zebra_requests'));
		$this->assertSame(0, $this->count_rows('phpbb_zebra_confirm'));
	}

	public function test_removal_preserves_foe_owned_by_other_user()
	{
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_FRIENDSHIP_REMOVED,
				array('user_id' => 3, 'friend_id' => 2, 'reason' => 'relationship_removed')
			);
		$this->db->sql_query('DELETE FROM phpbb_zebra_requests WHERE user_low = 2 AND user_high = 3');
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (3, 2, 1, 0, 1)');
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (2, 3, 0, 1, 0)');

		$this->relationships->remove_relationship(3, 2);

		$this->assertSame(0, $this->count_zebra_rows(3, 2, 1, 0));
		$this->assertSame(1, $this->count_zebra_rows(2, 3, 0, 1));
	}

	public function test_foe_addition_removes_friendship_but_preserves_existing_foes()
	{
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_FRIENDSHIP_REMOVED,
				array('user_id' => 3, 'friend_id' => 5, 'reason' => 'foe')
			);
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (3, 5, 1, 0, 1)');
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (5, 3, 1, 0, 0)');
		$rows = array(array('user_id' => 3, 'zebra_id' => 5, 'foe' => 1));

		$this->assertSame($rows, $this->relationships->process_additions('foes', $rows));
		$this->assertSame(0, $this->count_zebra_rows(3, 5, 1, 0));
		$this->assertSame(0, $this->count_zebra_rows(5, 3, 1, 0));
		$this->assertSame(1, $this->count_zebra_rows(4, 5, 0, 1));
	}

	public function test_friend_addition_reports_request_outcomes()
	{
		$results = null;
		$rows = array(array('user_id' => 2, 'zebra_id' => 3, 'friend' => 1));

		$this->assertSame(array(), $this->relationships->process_additions('friends', $rows, $results));
		$this->assertSame(array('ignored'), $results);
	}

	public function test_profile_request_context_uses_stable_numeric_identity()
	{
		$this->assertSame(array(
			'request_id' => 1,
			'requester_id' => 2,
			'recipient_id' => 3,
			'request_time' => 100,
			'request_message' => '',
		), $this->relationships->get_pending_request_between(3, 2));
		$this->assertFalse($this->relationships->get_pending_request_between(3, 4));
	}

	public function test_inactive_and_bot_recipients_cannot_receive_direct_requests()
	{
		$this->db->sql_query('UPDATE phpbb_users SET user_type = ' . USER_INACTIVE . ' WHERE user_id = 4');
		$this->dispatcher->expects($this->never())->method('trigger_event');
		$this->notifications->expects($this->never())->method('add_notifications');

		$this->assertSame('ignored', $this->relationships->request_friendship(3, 4));
		$this->db->sql_query('UPDATE phpbb_users SET user_type = ' . USER_IGNORE . ' WHERE user_id = 4');
		$this->assertSame('ignored', $this->relationships->request_friendship(3, 4));
	}

	public function test_requester_cannot_request_a_user_on_their_foe_list()
	{
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (3, 4, 0, 1, 0)');
		$this->dispatcher->expects($this->never())->method('trigger_event');
		$this->notifications->expects($this->never())->method('add_notifications');

		$this->assertSame('blocked', $this->relationships->request_friendship(3, 4));
	}

	public function test_recipient_can_restrict_new_requests_to_nobody()
	{
		$this->db->sql_query('UPDATE phpbb_users SET zebra_request_policy = 2 WHERE user_id = 4');
		$this->notifications->expects($this->never())->method('add_notifications');
		$this->dispatcher->expects($this->never())->method('trigger_event');

		$this->assertSame('restricted', $this->relationships->request_friendship(3, 4));
	}

	public function test_friends_of_friends_policy_requires_a_mutual_friend()
	{
		$this->db->sql_query('UPDATE phpbb_users SET zebra_request_policy = 1 WHERE user_id = 4');
		$this->assertSame('restricted', $this->relationships->request_friendship(3, 4));
		$this->db->sql_query('INSERT INTO phpbb_zebra (user_id, zebra_id, friend, foe, bff)
			VALUES (3, 52, 1, 0, 0)');
		$this->db->sql_query('INSERT INTO phpbb_zebra (user_id, zebra_id, friend, foe, bff)
			VALUES (4, 52, 1, 0, 0)');

		$this->assertSame('created', $this->relationships->request_friendship(3, 4));
	}

	public function test_reverse_request_can_be_accepted_despite_recipient_policy()
	{
		$this->db->sql_query('UPDATE phpbb_users SET zebra_request_policy = 2 WHERE user_id = 2');

		$this->assertSame('accepted', $this->relationships->request_friendship(3, 2));
		$this->assertSame(2, $this->count_friend_rows(2, 3));
	}

	public function test_only_unique_constraint_errors_are_treated_as_request_races()
	{
		$method = new \ReflectionMethod($this->relationships, 'is_duplicate_key_error');
		$method->setAccessible(true);

		$this->assertTrue($method->invoke($this->relationships, array('code' => 1062, 'message' => 'duplicate')));
		$this->assertTrue($method->invoke($this->relationships, array('code' => '23505', 'message' => '')));
		$this->assertTrue($method->invoke($this->relationships, array('code' => 0, 'message' => 'UNIQUE constraint failed')));
		$this->assertFalse($method->invoke($this->relationships, array('code' => 5, 'message' => 'database is locked')));
	}

	public function test_request_is_accepted_by_stable_id_and_recipient()
	{
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_ACCEPTED,
				array(
					'request_id' => 1,
					'requester_id' => 2,
					'recipient_id' => 3,
					'actor_id' => 3,
					'request_time' => 100,
				)
			);
		$this->notifications->expects($this->once())
			->method('delete_notifications')
			->with('anavaro.zebraenhance.notification.zebraadd', 1, false, 3);
		$this->notifications->expects($this->once())
			->method('add_notifications')
			->with(
				'anavaro.zebraenhance.notification.zebraconfirm',
				$this->callback(function ($data)
				{
					return $data['request_id'] === 1
						&& $data['requester_id'] === 3
						&& isset($data['user_id'][2]);
				})
			);

		$this->assertSame('accepted', $this->relationships->manage_request(1, 3, 'accept'));
		$this->assertSame(2, $this->count_friend_rows(2, 3));
		$this->assertSame(1, $this->count_rows('phpbb_zebra_requests'));
	}

	public function test_request_actions_enforce_directional_ownership()
	{
		$this->assertFalse($this->relationships->manage_request(1, 2, 'accept'));
		$this->assertFalse($this->relationships->manage_request(1, 2, 'decline'));
		$this->assertFalse($this->relationships->manage_request(1, 2, 'decline_block'));
		$this->assertFalse($this->relationships->manage_request(1, 3, 'cancel'));
		$this->assertFalse($this->relationships->manage_request(999, 3, 'accept'));
		$this->assertSame(2, $this->count_rows('phpbb_zebra_requests'));
	}

	public function test_foe_prevents_acceptance_and_dispatches_decline()
	{
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (3, 2, 0, 1, 0)');
		$this->notifications->expects($this->once())
			->method('delete_notifications')
			->with('anavaro.zebraenhance.notification.zebraadd', 1, false, 3);
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_DECLINED,
				array(
					'request_id' => 1,
					'requester_id' => 2,
					'recipient_id' => 3,
					'actor_id' => 3,
					'request_time' => 100,
					'reason' => 'foe',
				)
			);

		$this->assertFalse($this->relationships->manage_request(1, 3, 'accept'));
		$this->assertSame(1, $this->count_rows('phpbb_zebra_requests'));
	}

	public function test_recipient_can_decline_request_by_id()
	{
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_DECLINED,
				array(
					'request_id' => 1,
					'requester_id' => 2,
					'recipient_id' => 3,
					'actor_id' => 3,
					'request_time' => 100,
					'reason' => 'user',
				)
			);
		$this->notifications->expects($this->once())
			->method('delete_notifications')
			->with('anavaro.zebraenhance.notification.zebraadd', 1, false, 3);

		$this->assertSame('declined', $this->relationships->manage_request(1, 3, 'decline'));
		$this->assertSame(1, $this->count_rows('phpbb_zebra_requests'));
		$this->assertSame(1, $this->count_rows('phpbb_zebra_request_cooldowns'));
		$this->assertSame('cooldown', $this->relationships->request_friendship(2, 3));
	}

	public function test_recipient_can_decline_and_block_without_removing_requester_foe_state()
	{
		$this->db->sql_query('INSERT INTO phpbb_zebra_requests
			(request_id, requester_id, recipient_id, user_low, user_high, request_time)
			VALUES (3, 4, 3, 3, 4, 102)');
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (4, 3, 0, 1, 0)');
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_DECLINED,
				array(
					'request_id' => 3,
					'requester_id' => 4,
					'recipient_id' => 3,
					'actor_id' => 3,
					'request_time' => 102,
					'reason' => 'foe',
				)
			);
		$this->notifications->expects($this->once())
			->method('delete_notifications')
			->with('anavaro.zebraenhance.notification.zebraadd', 3, false, 3);

		$this->assertSame('blocked', $this->relationships->manage_request(3, 3, 'decline_block'));
		$this->assertSame(2, $this->count_rows('phpbb_zebra_requests'));
		$result = $this->db->sql_query('SELECT COUNT(*) AS total FROM phpbb_zebra_request_cooldowns
			WHERE (requester_id = 4 AND recipient_id = 3)
				OR (requester_id = 3 AND recipient_id = 4)');
		$this->assertSame(0, (int) $this->db->sql_fetchfield('total'));
		$this->db->sql_freeresult($result);
		$this->assertSame(1, $this->count_zebra_rows(3, 4, 0, 1));
		$this->assertSame(1, $this->count_zebra_rows(4, 3, 0, 1));
		$this->assertSame('blocked', $this->relationships->request_friendship(4, 3));
	}

	public function test_administrators_and_moderators_cannot_be_blocked()
	{
		$this->auth->expects($this->once())
			->method('acl_get_list')
			->with(array(2), array('a_', 'm_'))
			->willReturn(array(0 => array('a_' => array(2))));
		$this->notifications->expects($this->never())->method('delete_notifications');
		$this->dispatcher->expects($this->never())->method('trigger_event');

		$this->assertSame('not_blockable', $this->relationships->manage_request(1, 3, 'decline_block'));
		$this->assertSame(2, $this->count_rows('phpbb_zebra_requests'));
		$this->assertSame(0, $this->count_zebra_rows(3, 2, 0, 1));
	}

	public function test_zero_cooldown_allows_a_new_request_after_decline()
	{
		$this->config->set('ze_decline_cooldown_days', 0);
		$this->assertSame('declined', $this->relationships->manage_request(1, 3, 'decline'));
		$this->assertSame(0, $this->count_rows('phpbb_zebra_request_cooldowns'));
		$this->assertSame('created', $this->relationships->request_friendship(2, 3));
	}

	public function test_expired_cooldown_is_removed_opportunistically()
	{
		$this->db->sql_query('INSERT INTO phpbb_zebra_request_cooldowns
			(requester_id, recipient_id, expires_at) VALUES (3, 4, ' . (time() - 1) . ')');

		$this->assertSame('created', $this->relationships->request_friendship(3, 4));
		$this->assertSame(0, $this->count_rows('phpbb_zebra_request_cooldowns'));
	}

	public function test_requester_can_cancel_request_by_id()
	{
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_CANCELLED,
				array(
					'request_id' => 2,
					'requester_id' => 2,
					'recipient_id' => 52,
					'actor_id' => 2,
					'request_time' => 101,
					'reason' => 'user',
				)
			);
		$this->notifications->expects($this->once())
			->method('delete_notifications')
			->with('anavaro.zebraenhance.notification.zebraadd', 2, false, 52);

		$this->assertSame('cancelled', $this->relationships->manage_request(2, 2, 'cancel'));
		$this->assertSame(1, $this->count_rows('phpbb_zebra_requests'));
	}

	public function test_bulk_request_actions_are_bounded_deduplicated_and_owner_checked()
	{
		$summary = $this->relationships->manage_requests(array(1, 2, 2, 999), 2, 'cancel');

		$this->assertSame(2, $summary['completed']);
		$this->assertSame(1, $summary['skipped']);
		$this->assertSame(array(1 => 'cancelled', 2 => 'cancelled'), $summary['results']);
		$this->assertSame(0, $this->relationships->count_requests(2, false));
	}

	public function test_acp_pending_request_report_is_counted_paginated_and_newest_first()
	{
		$this->assertSame(2, $this->relationships->count_pending_request_report());
		$first_page = $this->relationships->get_pending_request_report(1, 0);
		$this->assertCount(1, $first_page);
		$this->assertSame(2, (int) $first_page[0]['request_id']);
		$this->assertSame('admin', $first_page[0]['requester_username']);
		$this->assertSame('user52', $first_page[0]['recipient_username']);
		$second_page = $this->relationships->get_pending_request_report(1, 1);
		$this->assertCount(1, $second_page);
		$this->assertSame(1, (int) $second_page[0]['request_id']);
	}

	public function test_friend_list_visibility_is_clamped_and_persisted()
	{
		$this->assertSame(5, $this->relationships->set_friend_list_visibility(2, 99));
		$result = $this->db->sql_query('SELECT profile_friend_show FROM phpbb_users WHERE user_id = 2');
		$this->assertSame(5, (int) $this->db->sql_fetchfield('profile_friend_show'));
		$this->db->sql_freeresult($result);
	}

	public function test_request_policy_is_clamped_and_persisted()
	{
		$this->assertSame(2, $this->relationships->set_request_policy(2, 99));
		$result = $this->db->sql_query('SELECT zebra_request_policy FROM phpbb_users WHERE user_id = 2');
		$this->assertSame(2, (int) $this->db->sql_fetchfield('zebra_request_policy'));
		$this->db->sql_freeresult($result);
	}

	public function test_block_foe_pm_preference_is_persisted()
	{
		$this->assertTrue($this->relationships->set_block_foe_pm(2, true));
		$result = $this->db->sql_query('SELECT zebra_block_foe_pm FROM phpbb_users WHERE user_id = 2');
		$this->assertSame(1, (int) $this->db->sql_fetchfield('zebra_block_foe_pm'));
		$this->db->sql_freeresult($result);

		$this->assertFalse($this->relationships->set_block_foe_pm(2, false));
	}

	public function test_hide_foe_content_preference_is_persisted()
	{
		$this->assertTrue($this->relationships->set_hide_foe_content(2, true));
		$result = $this->db->sql_query('SELECT zebra_hide_foe_content FROM phpbb_users WHERE user_id = 2');
		$this->assertSame(1, (int) $this->db->sql_fetchfield('zebra_hide_foe_content'));
		$this->db->sql_freeresult($result);

		$this->assertFalse($this->relationships->set_hide_foe_content(2, false));
	}

	public function test_mute_foe_notifications_preference_is_persisted()
	{
		$this->assertTrue($this->relationships->set_mute_foe_notifications(2, true));
		$result = $this->db->sql_query('SELECT zebra_mute_foe_notifications FROM phpbb_users WHERE user_id = 2');
		$this->assertSame(1, (int) $this->db->sql_fetchfield('zebra_mute_foe_notifications'));
		$this->db->sql_freeresult($result);

		$this->assertFalse($this->relationships->set_mute_foe_notifications(2, false));
	}

	public function test_master_switch_bypasses_all_enhanced_foe_behaviour()
	{
		$this->config->set('ze_foes_enhancement', 0);
		$this->db->sql_query('UPDATE phpbb_users
			SET zebra_block_foe_pm = 1, zebra_hide_foe_content = 1, zebra_mute_foe_notifications = 1
			WHERE user_id = 4');
		$address_list = array('u' => array(4 => 'to'));
		$recipients = array(4 => array('notification.method.board'));

		$this->assertFalse($this->relationships->foe_feature_enabled());
		$this->assertSame($address_list, $this->relationships->filter_pm_address_list(5, $address_list));
		$this->assertSame(array(), $this->relationships->get_foe_identities(4));
		$this->assertSame($recipients, $this->relationships->filter_foe_notification_recipients(5, $recipients));
		$this->assertSame(0, $this->relationships->expire_foes(time() + 90000, 4));
		$this->assertFalse($this->relationships->update_foe(4, 5, 86400, 'ignored', 2, 2, 2));
	}

	public function test_disabled_exceptions_use_only_the_global_preference()
	{
		$this->config->set('ze_foe_exceptions', 0);
		$this->relationships->register_foe_settings(4, array(5));
		$this->db->sql_query('UPDATE phpbb_zebra_foe_settings
			SET content_policy = 2
			WHERE owner_id = 4 AND foe_id = 5');

		$this->assertSame(array(), $this->relationships->get_effective_foe_ids(4, 'content'));
		$this->db->sql_query('UPDATE phpbb_users SET zebra_hide_foe_content = 1 WHERE user_id = 4');
		$this->db->sql_query('UPDATE phpbb_zebra_foe_settings
			SET content_policy = 1
			WHERE owner_id = 4 AND foe_id = 5');
		$this->assertSame(array(5), $this->relationships->get_effective_foe_ids(4, 'content'));
	}

	public function test_foe_identities_include_normalized_usernames()
	{
		$this->db->sql_query('UPDATE phpbb_users SET zebra_hide_foe_content = 1 WHERE user_id = 4');
		$this->assertSame(array(5 => 'user5'), $this->relationships->get_foe_identities(4));
		$this->assertSame(array(), $this->relationships->get_foe_identities(0));
	}

	public function test_foe_manager_search_notes_and_individual_policies()
	{
		$this->relationships->register_foe_settings(4, array(5), 1234);
		$this->assertTrue($this->relationships->update_foe(4, 5, -1, '  Temporary conflict  ', 1, 2, 2));

		$this->assertSame(1, $this->relationships->count_foes(4, 'USER5'));
		$foes = $this->relationships->get_foes(4, 25, 0, 'user5');
		$this->assertCount(1, $foes);
		$this->assertSame(1234, (int) $foes[0]['added_at']);
		$this->assertSame('Temporary conflict', $foes[0]['foe_note']);
		$this->assertSame(1, (int) $foes[0]['pm_policy']);
		$this->assertSame(2, (int) $foes[0]['content_policy']);
		$this->assertSame(2, (int) $foes[0]['notification_policy']);
		$this->assertSame(array(5), $this->relationships->get_effective_foe_ids(4, 'content'));
	}

	public function test_notification_policy_can_inherit_allow_or_block()
	{
		$this->relationships->register_foe_settings(4, array(5));
		$recipients = array(4 => array('notification.method.board'), 3 => array('notification.method.board'));

		$this->db->sql_query('UPDATE phpbb_users SET zebra_mute_foe_notifications = 1 WHERE user_id = 4');
		$this->assertSame(
			array(3 => array('notification.method.board')),
			$this->relationships->filter_foe_notification_recipients(5, $recipients)
		);

		$this->relationships->update_foe(4, 5, -1, '', 0, 0, 1);
		$this->assertSame($recipients, $this->relationships->filter_foe_notification_recipients(5, $recipients));

		$this->db->sql_query('UPDATE phpbb_users SET zebra_mute_foe_notifications = 0 WHERE user_id = 4');
		$this->relationships->update_foe(4, 5, -1, '', 0, 0, 2);
		$this->assertSame(
			array(3 => array('notification.method.board')),
			$this->relationships->filter_foe_notification_recipients(5, $recipients)
		);
	}

	public function test_temporary_foes_expire_and_are_removed()
	{
		$this->relationships->register_foe_settings(4, array(5));
		$this->assertTrue($this->relationships->update_foe(4, 5, 86400, '', 0, 0, 0));
		$this->assertSame(1, $this->relationships->expire_foes(time() + 90000, 4));
		$this->assertSame(0, $this->relationships->count_foes(4));
		$result = $this->db->sql_query('SELECT COUNT(*) AS total FROM phpbb_zebra_foe_settings
			WHERE owner_id = 4 AND foe_id = 5');
		$this->assertSame(0, (int) $this->db->sql_fetchfield('total'));
		$this->db->sql_freeresult($result);
	}

	public function test_pm_filter_removes_opted_in_foes_and_affected_groups()
	{
		$this->db->sql_query('UPDATE phpbb_users SET zebra_block_foe_pm = 1 WHERE user_id = 4');
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (4, 3, 0, 1, 0)');
		$this->db->sql_query('INSERT INTO phpbb_user_group
			(group_id, user_id, group_leader, user_pending)
			VALUES (2, 4, 0, 0)');
		$address_list = array(
			'u' => array(4 => 'to', 5 => 'bcc'),
			'g' => array(2 => 'to', 5 => 'bcc'),
		);

		$this->assertSame(array(
			'u' => array(5 => 'bcc'),
			'g' => array(5 => 'bcc'),
		), $this->relationships->filter_pm_address_list(3, $address_list));
	}

	public function test_pm_filter_does_not_apply_without_recipient_opt_in()
	{
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (4, 3, 0, 1, 0)');
		$address_list = array('u' => array(4 => 'to'));

		$this->assertSame($address_list, $this->relationships->filter_pm_address_list(3, $address_list));
	}

	public function test_friend_list_visibility_change_dispatches_event()
	{
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_VISIBILITY_CHANGED,
				array('user_id' => 2, 'old_visibility' => 5, 'new_visibility' => 3)
			);

		$this->assertSame(3, $this->relationships->set_friend_list_visibility(2, 3));
	}

	public function test_close_friend_change_dispatches_directional_event()
	{
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (2, 3, 1, 0, 0)');
		$this->dispatcher->expects($this->once())
			->method('trigger_event')
			->with(
				\anavaro\zebraenhance\service\relationship_manager::EVENT_CLOSE_FRIEND_CHANGED,
				array('owner_id' => 2, 'friend_id' => 3, 'old_state' => false, 'new_state' => true)
			);

		$this->assertTrue($this->relationships->set_close_friend(2, 3, true));
		$this->assertTrue($this->relationships->set_close_friend(2, 3, true));
	}

	public function test_circle_lifecycle_is_owner_scoped_and_dispatches_events()
	{
		$this->dispatcher->expects($this->exactly(4))
			->method('trigger_event')
			->withConsecutive(
				array(
					\anavaro\zebraenhance\service\relationship_manager::EVENT_CIRCLE_CREATED,
					$this->callback(function ($data)
					{
						return $data['owner_id'] === 2 && $data['circle_name'] === 'Gaming';
					}),
				),
				array(
					\anavaro\zebraenhance\service\relationship_manager::EVENT_CIRCLE_RENAMED,
					$this->callback(function ($data)
					{
						return $data['owner_id'] === 2
							&& $data['old_name'] === 'Gaming'
							&& $data['new_name'] === 'GAMING';
					}),
				),
				array(
					\anavaro\zebraenhance\service\relationship_manager::EVENT_CIRCLE_RENAMED,
					$this->callback(function ($data)
					{
						return $data['owner_id'] === 2
							&& $data['old_name'] === 'GAMING'
							&& $data['new_name'] === 'Gaming friends';
					}),
				),
				array(
					\anavaro\zebraenhance\service\relationship_manager::EVENT_CIRCLE_DELETED,
					$this->callback(function ($data)
					{
						return $data['owner_id'] === 2 && $data['circle_name'] === 'Gaming friends';
					}),
				)
			);

		$circle = $this->relationships->create_circle(2, '  Gaming  ');
		$this->assertIsArray($circle);
		$this->assertSame('duplicate', $this->relationships->create_circle(2, 'gaming'));
		$this->assertSame('invalid', $this->relationships->create_circle(2, '   '));
		$this->assertSame('not_found', $this->relationships->rename_circle(3, $circle['circle_id'], 'Other'));
		$renamed = $this->relationships->rename_circle(2, $circle['circle_id'], 'GAMING');
		$this->assertSame('GAMING', $renamed['circle_name']);
		$renamed = $this->relationships->rename_circle(2, $circle['circle_id'], 'Gaming friends');
		$this->assertSame('Gaming friends', $renamed['circle_name']);
		$this->assertFalse($this->relationships->delete_circle(3, $circle['circle_id']));
		$this->assertTrue($this->relationships->delete_circle(2, $circle['circle_id']));
		$this->assertSame(array(), $this->relationships->get_circles(2));
	}

	public function test_circle_count_is_limited_per_owner()
	{
		for ($index = 1; $index <= \anavaro\zebraenhance\service\relationship_manager::MAX_CIRCLES; $index++)
		{
			$this->assertIsArray($this->relationships->create_circle(2, 'Circle ' . $index));
		}
		$this->assertSame('limit', $this->relationships->create_circle(2, 'One too many'));
		$this->assertIsArray($this->relationships->create_circle(3, 'Another owner'));
	}

	public function test_circle_memberships_require_an_accepted_friendship()
	{
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (2, 3, 1, 0, 0)');
		$circle = $this->relationships->create_circle(2, 'Local friends');
		$foreign_circle = $this->relationships->create_circle(3, 'Work');

		$this->assertFalse($this->relationships->set_friend_circles(2, 4, array($circle['circle_id'])));
		$this->assertFalse($this->relationships->set_friend_circles(2, 3, array($foreign_circle['circle_id'])));
		$this->assertTrue($this->relationships->set_friend_circles(2, 3, array($circle['circle_id'])));
		$this->assertTrue($this->relationships->is_friend_in_circle(2, 3, $circle['circle_id']));
		$this->assertSame(array(3), $this->relationships->get_circle_friend_ids(2, $circle['circle_id']));
		$this->assertSame(array($circle['circle_id']), $this->relationships->get_friend_circle_ids(2, 3));
	}

	public function test_relationship_removal_cleans_circle_memberships_in_both_directions()
	{
		$this->db->sql_multi_insert('phpbb_zebra', array(
			array('user_id' => 2, 'zebra_id' => 3, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => 3, 'zebra_id' => 2, 'friend' => 1, 'foe' => 0, 'bff' => 0),
		));
		$first = $this->relationships->create_circle(2, 'First');
		$second = $this->relationships->create_circle(3, 'Second');
		$this->assertTrue($this->relationships->set_friend_circles(2, 3, array($first['circle_id'])));
		$this->assertTrue($this->relationships->set_friend_circles(3, 2, array($second['circle_id'])));

		$this->relationships->remove_relationship(2, 3);

		$this->assertSame(0, $this->count_rows('phpbb_zebra_circle_members'));
	}

	public function test_integration_event_names_are_vendor_prefixed()
	{
		$events = array(
			\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_CREATED,
			\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_ACCEPTED,
			\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_DECLINED,
			\anavaro\zebraenhance\service\relationship_manager::EVENT_REQUEST_CANCELLED,
			\anavaro\zebraenhance\service\relationship_manager::EVENT_FRIENDSHIP_REMOVED,
			\anavaro\zebraenhance\service\relationship_manager::EVENT_CLOSE_FRIEND_CHANGED,
			\anavaro\zebraenhance\service\relationship_manager::EVENT_VISIBILITY_CHANGED,
			\anavaro\zebraenhance\service\relationship_manager::EVENT_CIRCLE_CREATED,
			\anavaro\zebraenhance\service\relationship_manager::EVENT_CIRCLE_RENAMED,
			\anavaro\zebraenhance\service\relationship_manager::EVENT_CIRCLE_DELETED,
			\anavaro\zebraenhance\service\relationship_manager::EVENT_FRIEND_CIRCLES_CHANGED,
		);
		foreach ($events as $event_name)
		{
			$this->assertStringStartsWith('anavaro.zebraenhance.', $event_name);
		}
	}

	public function test_friend_list_visibility_rules()
	{
		$this->assertTrue($this->relationships->can_view_friend_list(4, ANONYMOUS, 0));
		$this->assertFalse($this->relationships->can_view_friend_list(4, ANONYMOUS, 1));
		$this->assertFalse($this->relationships->can_view_friend_list(4, 99, 1, false, false));
		$this->assertFalse($this->relationships->can_view_friend_list(4, 5, 2));
		$this->assertTrue($this->relationships->can_view_friend_list(4, 3, 2));
		$this->assertTrue($this->relationships->can_view_friend_list(4, 5, 5, true));
	}

	public function test_relationship_lists_support_counts_limits_and_offsets()
	{
		$this->assertSame(2, $this->relationships->count_requests(2, false));
		$this->assertSame(0, $this->relationships->count_requests(2, true));
		$this->assertSame(1, count($this->relationships->get_requests(2, false, 1, 1)));

		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (2, 3, 1, 0, 0)');
		$this->db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (2, 4, 1, 0, 0)');
		$this->assertSame(2, $this->relationships->count_friends(2));
		$this->assertSame(1, count($this->relationships->get_friends(2, 1, 1)));
	}

	public function test_friend_list_search_filters_counts_and_rows_case_insensitively()
	{
		$this->db->sql_multi_insert('phpbb_zebra', array(
			array('user_id' => 2, 'zebra_id' => 3, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => 2, 'zebra_id' => 4, 'friend' => 1, 'foe' => 0, 'bff' => 0),
		));

		$this->assertSame(1, $this->relationships->count_friends(2, 'USER3'));
		$friends = $this->relationships->get_friends(2, 25, 0, 'USER3');
		$this->assertCount(1, $friends);
		$this->assertSame(3, (int) $friends[0]['zebra_id']);
	}

	public function test_mutual_friends_require_valid_friend_rows_for_both_users()
	{
		$this->db->sql_multi_insert('phpbb_zebra', array(
			array('user_id' => 2, 'zebra_id' => 3, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => 4, 'zebra_id' => 3, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => 2, 'zebra_id' => 5, 'friend' => 1, 'foe' => 0, 'bff' => 0),
		));

		$mutual_friends = $this->relationships->get_mutual_friends(2, 4);
		$this->assertCount(1, $mutual_friends);
		$this->assertSame(3, (int) $mutual_friends[0]['zebra_id']);
		$this->assertSame(array(), $this->relationships->get_mutual_friends(2, 2));
	}

	public function test_friend_suggestions_respect_privacy_policy_and_relationship_state()
	{
		$this->db->sql_multi_insert('phpbb_users', array(
			array('user_id' => 6, 'user_type' => USER_NORMAL, 'group_id' => 2, 'username' => 'suggested', 'username_clean' => 'suggested', 'profile_friend_show' => 0, 'zebra_request_policy' => 0),
			array('user_id' => 7, 'user_type' => USER_NORMAL, 'group_id' => 2, 'username' => 'private', 'username_clean' => 'private', 'profile_friend_show' => 5, 'zebra_request_policy' => 0),
			array('user_id' => 8, 'user_type' => USER_NORMAL, 'group_id' => 2, 'username' => 'restricted', 'username_clean' => 'restricted', 'profile_friend_show' => 0, 'zebra_request_policy' => 2),
		));
		$this->db->sql_multi_insert('phpbb_zebra', array(
			array('user_id' => 2, 'zebra_id' => 4, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => 4, 'zebra_id' => 6, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => 4, 'zebra_id' => 7, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => 4, 'zebra_id' => 8, 'friend' => 1, 'foe' => 0, 'bff' => 0),
		));

		$suggestions = $this->relationships->get_friend_suggestions(2);
		$this->assertCount(1, $suggestions);
		$this->assertSame(6, $suggestions[0]['user_id']);
		$this->assertSame(1, $suggestions[0]['mutual_count']);

		$this->db->sql_query('INSERT INTO phpbb_zebra_request_cooldowns
			(requester_id, recipient_id, expires_at)
			VALUES (2, 6, ' . (time() + 3600) . ')');
		$this->assertSame(array(), $this->relationships->get_friend_suggestions(2));
		$this->db->sql_query('DELETE FROM phpbb_zebra_request_cooldowns
			WHERE requester_id = 2 AND recipient_id = 6');
		$this->db->sql_query('INSERT INTO phpbb_zebra_requests
			(requester_id, recipient_id, user_low, user_high, request_time, request_message)
			VALUES (2, 6, 2, 6, ' . time() . ", '')");
		$this->assertSame(array(), $this->relationships->get_friend_suggestions(2));
	}

	public function test_pending_request_limit_prevents_unbounded_spam()
	{
		$this->config->set('ze_max_pending_requests', 1);
		$this->notifications->expects($this->never())->method('add_notifications');

		$this->assertSame('limited', $this->relationships->request_friendship(3, 4));
	}

	public function test_zero_pending_request_limit_is_unlimited()
	{
		$this->config->set('ze_max_pending_requests', 0);
		$this->assertSame('created', $this->relationships->request_friendship(3, 4));
	}

	public function test_user_deletion_cleans_requests_and_all_custom_notifications()
	{
		$this->notifications->expects($this->once())->method('delete_notifications');
		$this->db->sql_query('INSERT INTO phpbb_zebra_request_cooldowns
			(requester_id, recipient_id, expires_at) VALUES (3, 4, ' . (time() + 1000) . ')');
		$this->db->sql_query('INSERT INTO phpbb_zebra_request_cooldowns
			(requester_id, recipient_id, expires_at) VALUES (4, 5, ' . (time() + 1000) . ')');
		if ($this->db_tools->sql_table_exists('phpbb_notification_emails'))
		{
			$this->db->sql_query('INSERT INTO phpbb_notification_emails
				(notification_type_id, item_id, item_parent_id, user_id)
				VALUES (90, 41, 2, 3)');
		}
		$owned = $this->relationships->create_circle(3, 'Owned');
		$other = $this->relationships->create_circle(2, 'Other');
		$this->db->sql_multi_insert('phpbb_zebra_circle_members', array(
			array('circle_id' => $owned['circle_id'], 'friend_id' => 2),
			array('circle_id' => $other['circle_id'], 'friend_id' => 3),
		));

		$this->relationships->delete_user_data(array(3));
		$this->assertSame(1, $this->count_rows('phpbb_zebra_requests'));
		$this->assertSame(0, $this->count_rows('phpbb_notifications'));
		$this->assertSame(1, $this->count_rows('phpbb_zebra_request_cooldowns'));
		$this->assertSame(1, $this->count_rows('phpbb_zebra_circles'));
		$this->assertSame(0, $this->count_rows('phpbb_zebra_circle_members'));
		if ($this->db_tools->sql_table_exists('phpbb_notification_emails'))
		{
			$this->assertSame(0, $this->count_rows('phpbb_notification_emails'));
		}

		$sql = 'SELECT COUNT(*) AS total FROM phpbb_zebra_confirm
			WHERE user_id = 3 OR zebra_id = 3';
		$result = $this->db->sql_query($sql);
		$this->assertSame(0, (int) $this->db->sql_fetchfield('total'));
		$this->db->sql_freeresult($result);
	}

	public function test_notification_item_ids_are_request_ids()
	{
		$data = array('request_id' => 42, 'user_id' => array(3 => ''));
		$this->assertSame(42, \anavaro\zebraenhance\notification\zebraadd::get_item_id($data));
		$this->assertSame(42, \anavaro\zebraenhance\notification\zebraconfirm::get_item_id($data));
		$this->assertSame(42, \anavaro\zebraenhance\notification\zebraadd::get_item_parent_id(array('requester_id' => 42)));
		$this->assertSame(42, \anavaro\zebraenhance\notification\zebraconfirm::get_item_parent_id(array('requester_id' => 42)));
	}

	protected function count_rows($table)
	{
		$result = $this->db->sql_query('SELECT COUNT(*) AS total FROM ' . $table);
		$count = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);
		return $count;
	}

	protected function count_friend_rows($first_id, $second_id)
	{
		$sql = 'SELECT COUNT(*) AS total FROM phpbb_zebra
			WHERE friend = 1 AND (
				(user_id = ' . (int) $first_id . ' AND zebra_id = ' . (int) $second_id . ')
				OR (user_id = ' . (int) $second_id . ' AND zebra_id = ' . (int) $first_id . ')
			)';
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);
		return $count;
	}

	protected function count_zebra_rows($user_id, $zebra_id, $friend, $foe)
	{
		$sql = 'SELECT COUNT(*) AS total FROM phpbb_zebra
			WHERE user_id = ' . (int) $user_id . '
				AND zebra_id = ' . (int) $zebra_id . '
				AND friend = ' . (int) $friend . '
				AND foe = ' . (int) $foe;
		$result = $this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);
		return $count;
	}
}
