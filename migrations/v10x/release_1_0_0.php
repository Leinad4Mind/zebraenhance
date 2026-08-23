<?php
/**
*
* @package migration
* @copyright (c) 2012 phpBB Group
* @copyright (c) 2026 Leinad4Mind
* @license http://opensource.org/licenses/gpl-license.php GNU Public License v2
*
*/

namespace anavaro\zebraenhance\migrations\v10x;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v330\v330');
	}

	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '1.0.0', '>=');
	}
	public function update_data()
	{
		return array(
			array('config.add', array('zebra_enhance_version', '1.0.0')),
			array('config.add', array('zebra_module_id', 'none')),
		);
	}
	//lets create the needed table
	public function update_schema()
	{
		return array(
			'add_tables'    => array(
				$this->table_prefix . 'zebra_confirm'		=> array(
					'COLUMNS'		=> array(
						'user_id'		=> array('ULINT', 0),
						'zebra_id'		=> array('ULINT', 0),
						'friend'		=> array('UINT:1', 0),
						'foe'			=> array('UINT:1', 0)
					),
					'PRIMARY_KEY'    => array('user_id', 'zebra_id'),
				),
			),
			'add_columns'	=> array(
				$this->table_prefix . 'zebra' 	=> array(
					'bff'	=> array('UINT', 0),
				),
				$this->table_prefix . 'users'        => array(
					'profile_friend_show'    => array('UINT', 5),
				)
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'zebra_confirm'
			),
			'drop_columns'          => array(
				$this->table_prefix . 'zebra'	=> array(
					'bff',
				),
				$this->table_prefix . 'users'        => array(
					'profile_friend_show',
				)
			),
		);
	}
}
