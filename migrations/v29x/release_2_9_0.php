<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\migrations\v29x;

class release_2_9_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '2.9.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\anavaro\zebraenhance\migrations\v28x\release_2_8_0',
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('zebraenhance_friend_avatars', 1)),
			array('config.update', array('zebra_enhance_version', '2.9.0')),
		);
	}
}
