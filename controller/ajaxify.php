<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2013-2026 Stanislav Atanasov
* @copyright (c) 2013 Lucifer
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class ajaxify
{
	/** @var \anavaro\zebraenhance\service\relationship_manager */
	protected $relationships;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $language;

	public function __construct(
		\anavaro\zebraenhance\service\relationship_manager $relationships,
		\phpbb\auth\auth $auth,
		\phpbb\request\request_interface $request,
		\phpbb\user $user,
		\phpbb\language\language $language
	)
	{
		$this->relationships = $relationships;
		$this->auth = $auth;
		$this->request = $request;
		$this->user = $user;
		$this->language = $language;
	}

	/**
	 * Set an existing friend as close (or ordinary) using a CSRF-protected POST.
	 */
	public function set_close_friend($userid, $state)
	{
		$this->language->add_lang('zebra_enchance', 'anavaro/zebraenhance');
		if ((int) $this->user->data['user_id'] === ANONYMOUS || !$this->auth->acl_get('u_ze_close_friends'))
		{
			return $this->error('ZE_AJAX_NOT_AUTHORIZED', 403);
		}

		if (!check_form_key('anavaro_zebraenhance'))
		{
			return $this->error('FORM_INVALID', 403);
		}

		$is_close = (bool) (int) $state;
		if (!$this->relationships->set_close_friend((int) $this->user->data['user_id'], (int) $userid, $is_close))
		{
			return $this->error('ZE_AJAX_NOT_FRIEND', 409);
		}

		return new JsonResponse(array(
			'success'  => true,
			'user_id'  => (int) $userid,
			'is_close' => $is_close,
			'label'    => $this->language->lang($is_close ? 'ZE_REMOVE_CLOSE_FRIEND' : 'ZE_ADD_CLOSE_FRIEND'),
		));
	}

	protected function error($language_key, $status_code)
	{
		return new JsonResponse(array(
			'success' => false,
			'message' => $this->language->lang($language_key),
		), (int) $status_code);
	}
}
