<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\migrations\v27x;

class release_2_7_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '2.7.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\anavaro\zebraenhance\migrations\v26x\release_2_6_0',
		);
	}

	/**
	 * Birthdays stay visible to everyone (0) until a user restricts them.
	 *
	 * The PM choice reuses zebra_block_foe_pm: its 0 and 1 already mean
	 * "everyone" and "everyone except foes", so existing users keep their setting.
	 */
	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_birthday_show' => array('TINT:1', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_birthday_show',
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('zebra_enhance_version', '2.7.0')),
		);
	}
}
