<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2013-2026 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\tests\migrations;

class release_2_0_0_test extends \phpbb_database_test_case
{
	static protected function setup_extensions()
	{
		return array('anavaro/zebraenhance');
	}

	// phpcs:ignore PhpbbCodingStandard.NamingConventions.LowercaseUnderscoredFunctions.NotAllowed -- PHPUnit API
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/../event/fixtures/fixture.xml');
	}

	public function test_valid_legacy_requests_are_preserved_and_deduplicated()
	{
		global $phpbb_root_path;

		$db = $this->new_dbal();
		$db->sql_query('DELETE FROM phpbb_zebra_requests');
		$db->sql_query('INSERT INTO phpbb_zebra_confirm (user_id, zebra_id, friend, foe)
			VALUES (3, 2, 1, 0)');
		$db->sql_query('INSERT INTO phpbb_zebra_confirm (user_id, zebra_id, friend, foe)
			VALUES (99, 2, 1, 0)');
		$db->sql_query('INSERT INTO phpbb_zebra_confirm (user_id, zebra_id, friend, foe)
			VALUES (2, 2, 1, 0)');

		$factory = new \phpbb\db\tools\factory();
		$migration = new \anavaro\zebraenhance\migrations\v20x\release_2_0_0(
			new \phpbb\config\config(array('zebra_enhance_version' => '1.0.4')),
			$db,
			$factory->get($db),
			$phpbb_root_path,
			'php',
			'phpbb_'
		);
		$migration->migrate_legacy_requests();

		$result = $db->sql_query('SELECT requester_id, recipient_id, user_low, user_high
			FROM phpbb_zebra_requests ORDER BY user_high');
		$this->assertEquals(array(
			array('requester_id' => '2', 'recipient_id' => '3', 'user_low' => '2', 'user_high' => '3'),
			array('requester_id' => '2', 'recipient_id' => '52', 'user_low' => '2', 'user_high' => '52'),
		), $db->sql_fetchrowset($result));
		$db->sql_freeresult($result);
	}

	public function test_legacy_notifications_are_purged_during_upgrade()
	{
		global $phpbb_root_path;

		$db = $this->new_dbal();
		$factory = new \phpbb\db\tools\factory();
		$db_tools = $factory->get($db);
		if ($db_tools->sql_table_exists('phpbb_notification_emails'))
		{
			$db->sql_query('INSERT INTO phpbb_notification_emails
				(notification_type_id, item_id, item_parent_id, user_id)
				VALUES (90, 41, 2, 3)');
		}
		$migration = new \anavaro\zebraenhance\migrations\v20x\release_2_0_0(
			new \phpbb\config\config(array('zebra_enhance_version' => '1.0.4')),
			$db,
			$db_tools,
			$phpbb_root_path,
			'php',
			'phpbb_'
		);
		$migration->purge_legacy_notifications();

		$result = $db->sql_query('SELECT COUNT(*) AS total FROM phpbb_notifications');
		$this->assertSame(0, (int) $db->sql_fetchfield('total'));
		$db->sql_freeresult($result);
		if ($db_tools->sql_table_exists('phpbb_notification_emails'))
		{
			$result = $db->sql_query('SELECT COUNT(*) AS total FROM phpbb_notification_emails');
			$this->assertSame(0, (int) $db->sql_fetchfield('total'));
			$db->sql_freeresult($result);
		}
	}
}
