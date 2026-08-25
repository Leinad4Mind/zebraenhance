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

class foes_info
{
	public function module()
	{
		return array(
			'filename' => '\anavaro\zebraenhance\ucp\foes_module',
			'title'    => 'ZEBRAENHANCE_UCP_FOE_MANAGER',
			'version'  => '2.6.0',
			'modes'    => array(
				'manage' => array(
					'title' => 'ZEBRAENHANCE_UCP_FOE_MANAGER',
					'auth'  => 'ext_anavaro/zebraenhance && cfg_zebraenhance_foes_enhancement && acl_u_zebraenhance_use_friend_requests',
					'cat'   => array('UCP_ZEBRA'),
				),
			),
		);
	}
}
