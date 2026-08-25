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

class report_info
{
	public function module()
	{
		return array(
			'filename' => '\anavaro\zebraenhance\acp\report_module',
			'title'    => 'ACP_ZEBRA_ENHANCE_TITLE',
			'modes'    => array(
				'report' => array(
					'title' => 'ACP_ZEBRA_ENHANCE_REPORT',
					'auth'  => 'ext_anavaro/zebraenhance && acl_m_zebraenhance_view_private_friendlists',
					'cat'   => array('ACP_ZEBRA_ENHANCE_TITLE'),
				),
			),
		);
	}
}
