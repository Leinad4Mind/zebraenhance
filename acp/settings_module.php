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

namespace anavaro\zebraenhance\acp;

class settings_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $config, $phpbb_log, $request, $template, $user;

		$user->add_lang_ext('anavaro/zebraenhance', 'info_acp_zebraenhance');
		$this->tpl_name = 'acp_zebraenhance_settings';
		$this->page_title = 'ACP_ZEBRA_ENHANCE_SETTINGS';
		$form_key = 'anavaro_zebraenhance_acp';
		add_form_key($form_key);

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}

			$max_pending = max(0, min(1000, $request->variable('ze_max_pending_requests', 100)));
			$cooldown_days = max(0, min(3650, $request->variable('ze_decline_cooldown_days', 7)));
			$config->set('ze_max_pending_requests', $max_pending);
			$config->set('ze_decline_cooldown_days', $cooldown_days);
			$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_ZEBRA_ENHANCE_SETTINGS', false, array($max_pending, $cooldown_days));

			trigger_error($user->lang('ACP_ZEBRA_ENHANCE_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'ZE_MAX_PENDING_REQUESTS' => isset($config['ze_max_pending_requests']) ? (int) $config['ze_max_pending_requests'] : 100,
			'ZE_DECLINE_COOLDOWN_DAYS' => isset($config['ze_decline_cooldown_days']) ? (int) $config['ze_decline_cooldown_days'] : 7,
			'U_ACTION' => $this->u_action,
		));
	}
}
