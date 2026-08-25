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

namespace anavaro\zebraenhance\tests\migrations;

class release_2_6_0_data_test extends \phpbb_database_test_case
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

	public function test_existing_foes_are_migrated_in_resumable_idempotent_batches()
	{
		global $phpbb_root_path;

		$db = $this->new_dbal();
		$db->sql_query('DELETE FROM phpbb_zebra_foe_settings');
		$db->sql_query('DELETE FROM phpbb_zebra WHERE foe = 1');
		$zebra_rows = array();
		for ($foe_id = 20000; $foe_id < 20501; $foe_id++)
		{
			$zebra_rows[] = array(
				'user_id'  => 9000,
				'zebra_id' => $foe_id,
				'friend'   => 0,
				'foe'      => 1,
				'bff'      => 0,
			);
		}
		$db->sql_multi_insert('phpbb_zebra', $zebra_rows);

		$factory = new \phpbb\db\tools\factory();
		$migration = new \anavaro\zebraenhance\migrations\v26x\release_2_6_0(
			new \phpbb\config\config(array('zebra_enhance_version' => '2.5.0')),
			$db,
			$factory->get($db),
			$phpbb_root_path,
			'php',
			'phpbb_'
		);

		$state = $migration->migrate_existing_foes();
		$this->assertSame(array('owner_id' => 9000, 'foe_id' => 20499), $state);
		$this->assertSame(500, $this->count_migrated_foes($db));

		$this->assertNull($migration->migrate_existing_foes($state));
		$this->assertSame(501, $this->count_migrated_foes($db));
		$this->assertNull($migration->migrate_existing_foes());
		$this->assertSame(501, $this->count_migrated_foes($db));

		$result = $db->sql_query('SELECT COUNT(*) AS total
			FROM phpbb_zebra_foe_settings
			WHERE owner_id = 9000 AND added_at = 0 AND expires_at = 0');
		$this->assertSame(501, (int) $db->sql_fetchfield('total'));
		$db->sql_freeresult($result);
	}

	protected function count_migrated_foes($db)
	{
		$result = $db->sql_query('SELECT COUNT(*) AS total
			FROM phpbb_zebra_foe_settings WHERE owner_id = 9000');
		$count = (int) $db->sql_fetchfield('total');
		$db->sql_freeresult($result);

		return $count;
	}
}
