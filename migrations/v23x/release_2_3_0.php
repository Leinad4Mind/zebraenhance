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

namespace anavaro\zebraenhance\migrations\v23x;

class release_2_3_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '2.3.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\anavaro\zebraenhance\migrations\v22x\release_2_2_0',
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'zebra_circles' => array(
					'COLUMNS' => array(
						'circle_id'         => array('UINT', null, 'auto_increment'),
						'owner_id'          => array('ULINT', 0),
						'circle_name'       => array('VCHAR_UNI:50', ''),
						'circle_name_clean' => array('VCHAR_UNI:50', ''),
						'created_at'        => array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY' => 'circle_id',
					'KEYS' => array(
						'owner_name' => array('UNIQUE', array('owner_id', 'circle_name_clean')),
						'owner_id'   => array('INDEX', 'owner_id'),
					),
				),
				$this->table_prefix . 'zebra_circle_members' => array(
					'COLUMNS' => array(
						'circle_id' => array('UINT', 0),
						'friend_id' => array('ULINT', 0),
					),
					'PRIMARY_KEY' => array('circle_id', 'friend_id'),
					'KEYS' => array(
						'friend_id' => array('INDEX', 'friend_id'),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'zebra_circle_members',
				$this->table_prefix . 'zebra_circles',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('zebra_enhance_version', '2.3.0')),
		);
	}
}
