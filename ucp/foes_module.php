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

namespace anavaro\zebraenhance\ucp;

class foes_module
{
	public $u_action;
	public $page_title;
	public $tpl_name;

	public function main($id, $mode)
	{
		global $phpbb_container, $template, $user, $request, $phpbb_root_path;

		$relationships = $phpbb_container->get('anavaro.zebraenhance.relationships');
		if (!$relationships->foe_feature_enabled())
		{
			trigger_error('NO_AUTH_OPERATION');
		}
		$controller_helper = $phpbb_container->get('controller.helper');
		$pagination = $phpbb_container->get('pagination');
		$php_ext = $phpbb_container->getParameter('core.php_ext');
		$user_id = (int) $user->data['user_id'];
		$relationships->expire_foes(time(), $user_id);
		$search = utf8_substr(
			trim($request->variable('ze_foe_q', '', true)),
			0,
			\anavaro\zebraenhance\service\relationship_manager::MAX_FOE_SEARCH_LENGTH
		);
		$start = max(0, $request->variable('start', 0));
		$page_size = \anavaro\zebraenhance\service\relationship_manager::PAGE_SIZE;
		$total = $relationships->count_foes($user_id, $search);
		$foes = $relationships->get_foes($user_id, $page_size, $start, $search);

		add_form_key('anavaro_zebraenhance', '_ZE');
		$this->tpl_name = 'ucp_foes';
		$this->page_title = 'ZE_UCP_FOE_MANAGER';
		$base_url = $this->u_action;
		if ($search !== '')
		{
			$base_url .= (strpos($base_url, '?') === false ? '?' : '&') . 'ze_foe_q=' . rawurlencode($search);
		}
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total, $page_size, $start);

		$template->assign_vars(array(
			'U_FOE_SEARCH'   => $this->u_action,
			'U_ADD_FOES'     => append_sid($phpbb_root_path . 'ucp.' . $php_ext, 'i=ucp_zebra&mode=foes'),
			'U_REMOVE_FOES'  => $controller_helper->route('anavaro_zebraenhance_remove_foes'),
			'FOE_SEARCH'     => $search,
			'TOTAL_FOES'     => $total,
			'S_GLOBAL_PM'    => !empty($user->data['zebra_block_foe_pm']),
			'S_GLOBAL_CONTENT' => !empty($user->data['zebra_hide_foe_content']),
			'S_GLOBAL_NOTIFICATIONS' => !empty($user->data['zebra_mute_foe_notifications']),
			'S_FOE_PM_AVAILABLE' => $relationships->foe_feature_enabled('pm'),
			'S_FOE_CONTENT_AVAILABLE' => $relationships->foe_feature_enabled('content'),
			'S_FOE_NOTIFICATIONS_AVAILABLE' => $relationships->foe_feature_enabled('notifications'),
			'S_FOE_TEMPORARY_AVAILABLE' => $relationships->foe_feature_enabled('temporary'),
			'S_FOE_NOTES_AVAILABLE' => $relationships->foe_feature_enabled('notes'),
			'S_FOE_EXCEPTIONS_AVAILABLE' => $relationships->foe_feature_enabled('exceptions'),
			'S_FOE_CAN_SAVE' => $relationships->foe_feature_enabled('notes')
				|| $relationships->foe_feature_enabled('temporary')
				|| ($relationships->foe_feature_enabled('exceptions')
					&& ($relationships->foe_feature_enabled('pm')
						|| $relationships->foe_feature_enabled('content')
						|| $relationships->foe_feature_enabled('notifications'))),
		));

		foreach ($foes as $foe)
		{
			$foe_id = (int) $foe['zebra_id'];
			$expires_at = (int) $foe['expires_at'];
			$template->assign_block_vars('ze_foes', array(
				'USER_ID'        => $foe_id,
				'USERNAME_FULL'  => get_username_string('full', $foe_id, $foe['username'], $foe['user_colour']),
				'ADDED'          => (int) $foe['added_at'] ? $user->format_date((int) $foe['added_at']) : $user->lang('ZE_FOE_DATE_UNKNOWN'),
				'EXPIRES'        => $expires_at ? $user->format_date($expires_at) : $user->lang('ZE_FOE_PERMANENT'),
				'NOTE'           => (string) $foe['foe_note'],
				'PM_POLICY'      => (int) $foe['pm_policy'],
				'CONTENT_POLICY' => (int) $foe['content_policy'],
				'NOTIFICATION_POLICY' => (int) $foe['notification_policy'],
				'S_PM_ACTIVE'    => $relationships->foe_feature_enabled('pm') && $this->effective((int) $foe['pm_policy'], !empty($user->data['zebra_block_foe_pm']), $relationships->foe_feature_enabled('exceptions')),
				'S_CONTENT_ACTIVE' => $relationships->foe_feature_enabled('content') && $this->effective((int) $foe['content_policy'], !empty($user->data['zebra_hide_foe_content']), $relationships->foe_feature_enabled('exceptions')),
				'S_NOTIFICATIONS_ACTIVE' => $relationships->foe_feature_enabled('notifications') && $this->effective((int) $foe['notification_policy'], !empty($user->data['zebra_mute_foe_notifications']), $relationships->foe_feature_enabled('exceptions')),
				'U_SAVE' => $controller_helper->route('anavaro_zebraenhance_update_foe', array('userid' => $foe_id)),
			));
		}
	}

	protected function effective($policy, $global, $exceptions)
	{
		if (!$exceptions)
		{
			return (bool) $global;
		}
		if ($policy === \anavaro\zebraenhance\service\relationship_manager::POLICY_ALLOW)
		{
			return false;
		}
		return $policy === \anavaro\zebraenhance\service\relationship_manager::POLICY_BLOCK || (bool) $global;
	}
}
