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

class report_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $auth, $phpbb_container, $request, $template, $user;

		$user->add_lang_ext('anavaro/zebraenhance', 'info_acp_zebraenhance');
		if (!$auth->acl_get('m_ze_view_private_friendlists'))
		{
			trigger_error('NO_AUTH_OPERATION', E_USER_WARNING);
		}

		$this->tpl_name = 'acp_zebraenhance_report';
		$this->page_title = 'ACP_ZEBRA_ENHANCE_REPORT';
		$start = max(0, $request->variable('start', 0));
		$relationships = $phpbb_container->get('anavaro.zebraenhance.relationships');
		$total = $relationships->count_pending_request_report();
		$rows = $relationships->get_pending_request_report(
			\anavaro\zebraenhance\service\relationship_manager::ACP_REPORT_PAGE_SIZE,
			$start
		);

		foreach ($rows as $row)
		{
			$message = html_entity_decode((string) $row['request_message'], ENT_QUOTES, 'UTF-8');
			$message = utf8_htmlspecialchars(censor_text($message));
			$template->assign_block_vars('ze_requests', array(
				'REQUEST_ID' => (int) $row['request_id'],
				'REQUESTER'  => get_username_string('full', (int) $row['requester_id'], (string) $row['requester_username'], (string) $row['requester_colour']),
				'RECIPIENT'  => get_username_string('full', (int) $row['recipient_id'], (string) $row['recipient_username'], (string) $row['recipient_colour']),
				'DATE'       => $user->format_date((int) $row['request_time']),
				'MESSAGE'    => $message,
			));
		}

		$phpbb_container->get('pagination')->generate_template_pagination(
			$this->u_action,
			'pagination',
			'start',
			$total,
			\anavaro\zebraenhance\service\relationship_manager::ACP_REPORT_PAGE_SIZE,
			$start
		);
		$template->assign_vars(array(
			'TOTAL_REQUESTS' => $user->lang('ACP_ZE_PENDING_TOTAL', $total),
		));
	}
}
