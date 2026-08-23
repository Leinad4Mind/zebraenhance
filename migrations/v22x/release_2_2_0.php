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

namespace anavaro\zebraenhance\migrations\v22x;

class release_2_2_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '2.2.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\anavaro\zebraenhance\migrations\v21x\release_2_1_0',
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'zebra_requests' => array(
					'request_message' => array('VCHAR_UNI:255', ''),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'zebra_requests' => array(
					'request_message',
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('zebra_enhance_version', '2.2.0')),
		);
	}
}
