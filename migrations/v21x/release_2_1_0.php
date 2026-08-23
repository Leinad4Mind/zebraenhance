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

namespace anavaro\zebraenhance\migrations\v21x;

class release_2_1_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['zebra_enhance_version']) && version_compare($this->config['zebra_enhance_version'], '2.1.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\anavaro\zebraenhance\migrations\v20x\release_2_0_0',
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'zebra_request_cooldowns' => array(
					'COLUMNS' => array(
						'requester_id' => array('ULINT', 0),
						'recipient_id' => array('ULINT', 0),
						'expires_at'   => array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY' => array('requester_id', 'recipient_id'),
					'KEYS' => array(
						'expires_at' => array('INDEX', 'expires_at'),
					),
				),
			),
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_request_policy' => array('UINT', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'users' => array(
					'zebra_request_policy',
				),
			),
			'drop_tables' => array(
				$this->table_prefix . 'zebra_request_cooldowns',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('ze_max_pending_requests', 100)),
			array('config.add', array('ze_decline_cooldown_days', 7)),
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_ZEBRA_ENHANCE_TITLE')),
			array('module.add', array(
				'acp',
				'ACP_ZEBRA_ENHANCE_TITLE',
				array(
					'module_basename' => '\anavaro\zebraenhance\acp\settings_module',
					'modes' => array('settings'),
				),
			)),
			array('config.update', array('zebra_enhance_version', '2.1.0')),
		);
	}
}
