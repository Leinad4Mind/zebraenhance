<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\migrations\v28x;

class release_2_8_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '2.8.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\anavaro\zebraenhance\migrations\v27x\release_2_7_0',
		);
	}

	/**
	 * Friends' content is highlighted for every user until they switch it off.
	 */
	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_highlight_friends' => array('BOOL', 1),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_highlight_friends',
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('zebraenhance_friend_highlight', 1)),
			array('config.update', array('zebra_enhance_version', '2.8.0')),
		);
	}
}
