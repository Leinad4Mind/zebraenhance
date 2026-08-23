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
		if ((int) $this->user->data['user_id'] === ANONYMOUS
			|| !$this->auth->acl_get('u_ze_use')
			|| !$this->auth->acl_get('u_ze_close_friends'))
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

	/**
	 * Accept, decline, decline and block, or cancel a request using its stable ID.
	 */
	public function manage_request($requestid, $action)
	{
		$this->language->add_lang('zebra_enchance', 'anavaro/zebraenhance');
		if ((int) $this->user->data['user_id'] === ANONYMOUS || !$this->auth->acl_get('u_ze_use'))
		{
			return $this->error('ZE_AJAX_NOT_AUTHORIZED', 403);
		}

		if (!check_form_key('anavaro_zebraenhance'))
		{
			return $this->error('FORM_INVALID', 403);
		}

		$result = $this->relationships->manage_request(
			(int) $requestid,
			(int) $this->user->data['user_id'],
			(string) $action
		);
		if ($result === false)
		{
			return $this->error('ZE_AJAX_REQUEST_NOT_FOUND', 404);
		}
		if ($result === 'not_blockable')
		{
			return $this->error('ZE_REQUESTER_CANNOT_BE_BLOCKED', 409);
		}

		$messages = array(
			'accepted'  => 'ZE_REQUEST_ACCEPTED',
			'declined'  => 'ZE_REQUEST_DECLINED',
			'blocked'   => 'ZE_REQUEST_DECLINED_BLOCKED',
			'cancelled' => 'ZE_REQUEST_CANCELLED',
		);
		return new JsonResponse(array(
			'success' => true,
			'action'  => $result,
			'message' => $this->language->lang($messages[$result]),
		));
	}

	/**
	 * Accept, decline, or cancel selected requests with per-ID ownership checks.
	 */
	public function manage_requests($action)
	{
		if ($error = $this->authorize_relationship_change())
		{
			return $error;
		}
		$request_ids = array_values(array_filter(array_map('intval', $this->request->variable('request_ids', array(0)))));
		if (!$request_ids)
		{
			return $this->error('ZE_SELECT_REQUEST', 400);
		}
		$summary = $this->relationships->manage_requests(
			$request_ids,
			(int) $this->user->data['user_id'],
			(string) $action
		);
		if (!$summary['completed'])
		{
			return $this->error('ZE_AJAX_REQUEST_NOT_FOUND', 404);
		}

		return new JsonResponse(array(
			'success'   => true,
			'completed' => (int) $summary['completed'],
			'skipped'   => (int) $summary['skipped'],
			'message'   => $this->language->lang('ZE_BULK_REQUESTS_COMPLETED', (int) $summary['completed']),
		));
	}

	/**
	 * Create a friend request from a numeric profile user ID.
	 */
	public function create_request($userid)
	{
		$this->language->add_lang('zebra_enchance', 'anavaro/zebraenhance');
		if ((int) $this->user->data['user_id'] === ANONYMOUS || !$this->auth->acl_get('u_ze_use'))
		{
			return $this->error('ZE_AJAX_NOT_AUTHORIZED', 403);
		}

		if (!check_form_key('anavaro_zebraenhance'))
		{
			return $this->error('FORM_INVALID', 403);
		}

		$result = $this->relationships->request_friendship(
			(int) $this->user->data['user_id'],
			(int) $userid,
			$this->request->variable('message', '', true)
		);
		if (!in_array($result, array('created', 'accepted'), true))
		{
			return $this->error('ZE_FRIEND_REQUEST_UNCHANGED', 409);
		}

		return new JsonResponse(array(
			'success' => true,
			'action'  => $result,
			'message' => $this->language->lang($result === 'accepted' ? 'ZE_REQUEST_ACCEPTED' : 'ZE_FRIEND_REQUEST_CREATED'),
		));
	}

	public function create_circle()
	{
		if ($error = $this->authorize_relationship_change())
		{
			return $error;
		}
		$result = $this->relationships->create_circle(
			(int) $this->user->data['user_id'],
			$this->request->variable('name', '', true)
		);
		if (!is_array($result))
		{
			return $this->circle_error($result);
		}

		return new JsonResponse(array(
			'success' => true,
			'circle'  => array(
				'id'   => (int) $result['circle_id'],
				'name' => (string) $result['circle_name'],
			),
			'message' => $this->language->lang('ZE_CIRCLE_CREATED'),
		));
	}

	public function manage_circle($circleid, $action)
	{
		if ($error = $this->authorize_relationship_change())
		{
			return $error;
		}
		$owner_id = (int) $this->user->data['user_id'];
		if ($action === 'delete')
		{
			if (!$this->relationships->delete_circle($owner_id, (int) $circleid))
			{
				return $this->error('ZE_CIRCLE_NOT_FOUND', 404);
			}
			return new JsonResponse(array(
				'success' => true,
				'message' => $this->language->lang('ZE_CIRCLE_DELETED'),
			));
		}

		$result = $this->relationships->rename_circle(
			$owner_id,
			(int) $circleid,
			$this->request->variable('name', '', true)
		);
		if (!is_array($result))
		{
			return $this->circle_error($result);
		}
		return new JsonResponse(array(
			'success' => true,
			'message' => $this->language->lang('ZE_CIRCLE_RENAMED'),
		));
	}

	public function set_friend_circles($userid)
	{
		if ($error = $this->authorize_relationship_change())
		{
			return $error;
		}
		if (!$this->relationships->set_friend_circles(
			(int) $this->user->data['user_id'],
			(int) $userid,
			$this->request->variable('circle_ids', array(0))
		))
		{
			return $this->error('ZE_AJAX_NOT_FRIEND', 409);
		}

		return new JsonResponse(array(
			'success' => true,
			'message' => $this->language->lang('ZE_CIRCLES_SAVED'),
		));
	}

	protected function authorize_relationship_change()
	{
		$this->language->add_lang('zebra_enchance', 'anavaro/zebraenhance');
		if ((int) $this->user->data['user_id'] === ANONYMOUS || !$this->auth->acl_get('u_ze_use'))
		{
			return $this->error('ZE_AJAX_NOT_AUTHORIZED', 403);
		}
		if (!check_form_key('anavaro_zebraenhance'))
		{
			return $this->error('FORM_INVALID', 403);
		}

		return false;
	}

	protected function circle_error($result)
	{
		$errors = array(
			'invalid'   => array('ZE_CIRCLE_INVALID', 400),
			'duplicate' => array('ZE_CIRCLE_DUPLICATE', 409),
			'limit'     => array('ZE_CIRCLE_LIMIT', 409),
			'not_found' => array('ZE_CIRCLE_NOT_FOUND', 404),
		);
		$error = isset($errors[$result]) ? $errors[$result] : array('ZE_REQUEST_FAILED', 409);
		return $this->error($error[0], $error[1]);
	}

	protected function error($language_key, $status_code)
	{
		return new JsonResponse(array(
			'success' => false,
			'message' => $this->language->lang($language_key),
		), (int) $status_code);
	}
}
