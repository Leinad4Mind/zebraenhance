<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2013-2026 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance;

class ext extends \phpbb\extension\base
{
	public function is_enableable()
	{
		return version_compare(PHP_VERSION, '7.4.0', '>=')
			&& defined('PHPBB_VERSION')
			&& version_compare(PHPBB_VERSION, '3.3.17', '>=')
			&& version_compare(PHPBB_VERSION, '4.0.0', '<');
	}

	public function enable_step($old_state)
	{
		if ($old_state === '')
		{
			$notifications = $this->container->get('notification_manager');
			$notifications->enable_notifications('anavaro.zebraenhance.notification.zebraadd');
			$notifications->enable_notifications('anavaro.zebraenhance.notification.zebraconfirm');

			return 'notifications';
		}

		return parent::enable_step($old_state);
	}

	public function disable_step($old_state)
	{
		if ($old_state === '')
		{
			$notifications = $this->container->get('notification_manager');
			$notifications->disable_notifications('anavaro.zebraenhance.notification.zebraadd');
			$notifications->disable_notifications('anavaro.zebraenhance.notification.zebraconfirm');

			return 'notifications';
		}

		return parent::disable_step($old_state);
	}

	public function purge_step($old_state)
	{
		if ($old_state === '')
		{
			$notifications = $this->container->get('notification_manager');
			$notifications->purge_notifications('anavaro.zebraenhance.notification.zebraadd');
			$notifications->purge_notifications('anavaro.zebraenhance.notification.zebraconfirm');

			return 'notifications';
		}

		return parent::purge_step($old_state);
	}
}
