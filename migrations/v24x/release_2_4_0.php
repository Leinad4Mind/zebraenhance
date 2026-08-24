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

namespace anavaro\zebraenhance\migrations\v24x;

class release_2_4_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '2.4.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\anavaro\zebraenhance\migrations\v23x\release_2_3_0',
		);
	}

	public function update_data()
	{
		return array(
			array('permission.permission_set', array('ADMINISTRATORS', 'm_ze_view_private_friendlists', 'group')),
			array('module.add', array(
				'acp',
				'ACP_ZEBRA_ENHANCE_TITLE',
				array(
					'module_basename' => '\anavaro\zebraenhance\acp\report_module',
					'modes' => array('report'),
				),
			)),
			array('config.update', array('zebra_enhance_version', '2.4.0')),
		);
	}
}
