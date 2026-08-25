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

			$max_pending = max(0, min(1000, $request->variable('zebraenhance_max_pending_requests', 100)));
			$cooldown_days = max(0, min(3650, $request->variable('zebraenhance_decline_cooldown_days', 7)));
			$config->set('zebraenhance_max_pending_requests', $max_pending);
			$config->set('zebraenhance_decline_cooldown_days', $cooldown_days);
			$foe_settings = array(
				'zebraenhance_foes_enhancement',
				'zebraenhance_foe_pm',
				'zebraenhance_foe_content',
				'zebraenhance_foe_notifications',
				'zebraenhance_foe_temporary',
				'zebraenhance_foe_notes',
				'zebraenhance_foe_exceptions',
			);
			foreach ($foe_settings as $setting)
			{
				$config->set($setting, (int) $request->variable($setting, 0));
			}
			$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_ZEBRA_ENHANCE_SETTINGS', false, array(
				$max_pending,
				$cooldown_days,
				$user->lang(!empty($config['zebraenhance_foes_enhancement']) ? 'ENABLED' : 'DISABLED'),
			));

			trigger_error($user->lang('ACP_ZEBRA_ENHANCE_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'ZEBRAENHANCE_MAX_PENDING_REQUESTS' => isset($config['zebraenhance_max_pending_requests']) ? (int) $config['zebraenhance_max_pending_requests'] : 100,
			'ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS' => isset($config['zebraenhance_decline_cooldown_days']) ? (int) $config['zebraenhance_decline_cooldown_days'] : 7,
			'S_ZEBRAENHANCE_FOES_ENHANCEMENT' => !empty($config['zebraenhance_foes_enhancement']),
			'S_ZEBRAENHANCE_FOE_PM' => !empty($config['zebraenhance_foe_pm']),
			'S_ZEBRAENHANCE_FOE_CONTENT' => !empty($config['zebraenhance_foe_content']),
			'S_ZEBRAENHANCE_FOE_NOTIFICATIONS' => !empty($config['zebraenhance_foe_notifications']),
			'S_ZEBRAENHANCE_FOE_TEMPORARY' => !empty($config['zebraenhance_foe_temporary']),
			'S_ZEBRAENHANCE_FOE_NOTES' => !empty($config['zebraenhance_foe_notes']),
			'S_ZEBRAENHANCE_FOE_EXCEPTIONS' => !empty($config['zebraenhance_foe_exceptions']),
			'U_ACTION' => $this->u_action,
		));
	}
}
