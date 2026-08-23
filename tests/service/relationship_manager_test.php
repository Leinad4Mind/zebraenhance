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
		$this->ensure_legacy_columns();
		$this->notifications = $this->getMockBuilder('\phpbb\notification\manager')
			->disableOriginalConstructor()
			->getMock();
		$this->relationships = new \anavaro\zebraenhance\service\relationship_manager(
			$this->db,
			$this->db_tools,
			$this->notifications,
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
	 * phpBB's unit-test schema generator may process extension migrations
	 * before the core table definitions. Add the legacy 1.x columns here so
	 * the service tests exercise the same schema that an installed board has.
	 */
	protected function ensure_legacy_columns()
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
		$this->assertSame('ignored', $this->relationships->request_friendship(2, 3));
		$this->assertSame(2, $this->count_rows('phpbb_zebra_requests'));
	}

	public function test_new_request_has_unique_identity_and_notification()
	{
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

	public function test_friend_list_visibility_rules()
	{
		$this->assertTrue($this->relationships->can_view_friend_list(4, ANONYMOUS, 0));
		$this->assertFalse($this->relationships->can_view_friend_list(4, ANONYMOUS, 1));
		$this->assertFalse($this->relationships->can_view_friend_list(4, 99, 1, false, false));
		$this->assertFalse($this->relationships->can_view_friend_list(4, 5, 2));
		$this->assertTrue($this->relationships->can_view_friend_list(4, 3, 2));
		$this->assertTrue($this->relationships->can_view_friend_list(4, 5, 5, true));
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
}
