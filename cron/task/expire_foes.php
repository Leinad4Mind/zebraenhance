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

namespace anavaro\zebraenhance\cron\task;

class expire_foes extends \phpbb\cron\task\base
{
	/** @var \anavaro\zebraenhance\service\relationship_manager */
	protected $relationships;

	/** @var \phpbb\config\config */
	protected $config;

	public function __construct(
		\anavaro\zebraenhance\service\relationship_manager $relationships,
		\phpbb\config\config $config
	)
	{
		$this->relationships = $relationships;
		$this->config = $config;
	}

	public function run()
	{
		if (empty($this->config['ze_foes_enhancement']) || empty($this->config['ze_foe_temporary']))
		{
			return;
		}
		$this->relationships->expire_foes();
		$this->config->set('ze_foe_expiry_last_gc', time(), true);
	}

	public function should_run()
	{
		if (empty($this->config['ze_foes_enhancement']) || empty($this->config['ze_foe_temporary']))
		{
			return false;
		}
		$last_run = isset($this->config['ze_foe_expiry_last_gc']) ? (int) $this->config['ze_foe_expiry_last_gc'] : 0;
		$interval = isset($this->config['ze_foe_expiry_gc']) ? max(300, (int) $this->config['ze_foe_expiry_gc']) : 3600;

		return $last_run < time() - $interval;
	}
}
