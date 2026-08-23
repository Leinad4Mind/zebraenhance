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
	protected $notifications;
	protected $dispatcher;
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
		$this->notifications = $this->getMockBuilder('\phpbb\notification\manager')
			->disableOriginalConstructor()
			->getMock();
		$this->dispatcher = $this->getMockBuilder('\phpbb\event\dispatcher_interface')->getMock();
		$this->relationships = new \anavaro\zebraenhance\service\relationship_manager(
			$this->db,
			$this->db_tools,
			$this->notifications,
			$this->dispatcher,
			'phpbb_zebra_requests',
			'phpbb_zebra_confirm',
			'phpbb_zebra',
			'phpbb_users',
			'phpbb_notifications',
			'phpbb_notification_emails',
			'phpbb_notification_types'
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

	public function test_friend_list_visibility_is_clamped_and_persisted()
	{
		$this->assertSame(5, $this->relationships->set_friend_list_visibility(2, 99));
		$result = $this->db->sql_query('SELECT profile_friend_show FROM phpbb_users WHERE user_id = 2');
		$this->assertSame(5, (int) $this->db->sql_fetchfield('profile_friend_show'));
		$this->db->sql_freeresult($result);
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

	public function test_pending_request_limit_prevents_unbounded_spam()
	{
		$rows = array();
		for ($i = 0; $i < \anavaro\zebraenhance\service\relationship_manager::MAX_PENDING_REQUESTS; $i++)
		{
			$other_id = 1000 + $i;
			$rows[] = array(
				'requester_id' => $other_id,
				'recipient_id' => 4,
				'user_low'     => 4,
				'user_high'    => $other_id,
				'request_time' => 200 + $i,
			);
		}
		$this->db->sql_multi_insert('phpbb_zebra_requests', $rows);
		$this->notifications->expects($this->never())->method('add_notifications');

		$this->assertSame('limited', $this->relationships->request_friendship(3, 4));
	}

	public function test_user_deletion_cleans_requests_and_all_custom_notifications()
	{
		$this->notifications->expects($this->once())->method('delete_notifications');
		if ($this->db_tools->sql_table_exists('phpbb_notification_emails'))
		{
			$this->db->sql_query('INSERT INTO phpbb_notification_emails
				(notification_type_id, item_id, item_parent_id, user_id)
				VALUES (90, 41, 2, 3)');
		}

		$this->relationships->delete_user_data(array(3));
		$this->assertSame(1, $this->count_rows('phpbb_zebra_requests'));
		$this->assertSame(0, $this->count_rows('phpbb_notifications'));
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
