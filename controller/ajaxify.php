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
			|| !$this->auth->acl_get('u_zebraenhance_use_friend_requests')
			|| !$this->auth->acl_get('u_zebraenhance_manage_close_friends'))
		{
			return $this->error('ZEBRAENHANCE_AJAX_NOT_AUTHORIZED', 403);
		}

		if (!check_form_key('anavaro_zebraenhance'))
		{
			return $this->error('FORM_INVALID', 403);
		}

		$is_close = (bool) (int) $state;
		if (!$this->relationships->set_close_friend((int) $this->user->data['user_id'], (int) $userid, $is_close))
		{
			return $this->error('ZEBRAENHANCE_AJAX_NOT_FRIEND', 409);
		}

		return new JsonResponse(array(
			'success'  => true,
			'user_id'  => (int) $userid,
			'is_close' => $is_close,
			'label'    => $this->language->lang($is_close ? 'ZEBRAENHANCE_REMOVE_CLOSE_FRIEND' : 'ZEBRAENHANCE_ADD_CLOSE_FRIEND'),
		));
	}

	/**
	 * Accept, decline, decline and block, or cancel a request using its stable ID.
	 */
	public function manage_request($requestid, $action)
	{
		$this->language->add_lang('zebra_enchance', 'anavaro/zebraenhance');
		if ((int) $this->user->data['user_id'] === ANONYMOUS || !$this->auth->acl_get('u_zebraenhance_use_friend_requests'))
		{
			return $this->error('ZEBRAENHANCE_AJAX_NOT_AUTHORIZED', 403);
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
			return $this->error('ZEBRAENHANCE_AJAX_REQUEST_NOT_FOUND', 404);
		}
		if ($result === 'not_blockable')
		{
			return $this->error('ZEBRAENHANCE_REQUESTER_CANNOT_BE_BLOCKED', 409);
		}

		$messages = array(
			'accepted'  => 'ZEBRAENHANCE_REQUEST_ACCEPTED',
			'declined'  => 'ZEBRAENHANCE_REQUEST_DECLINED',
			'blocked'   => 'ZEBRAENHANCE_REQUEST_DECLINED_BLOCKED',
			'cancelled' => 'ZEBRAENHANCE_REQUEST_CANCELLED',
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
		if ($error = $this->authorizebraenhance_relationship_change())
		{
			return $error;
		}
		$request_ids = array_values(array_filter(array_map('intval', $this->request->variable('request_ids', array(0)))));
		if (!$request_ids)
		{
			return $this->error('ZEBRAENHANCE_SELECT_REQUEST', 400);
		}
		$summary = $this->relationships->manage_requests(
			$request_ids,
			(int) $this->user->data['user_id'],
			(string) $action
		);
		if (!$summary['completed'])
		{
			return $this->error('ZEBRAENHANCE_AJAX_REQUEST_NOT_FOUND', 404);
		}

		return new JsonResponse(array(
			'success'   => true,
			'completed' => (int) $summary['completed'],
			'skipped'   => (int) $summary['skipped'],
			'message'   => $this->language->lang('ZEBRAENHANCE_BULK_REQUESTS_COMPLETED', (int) $summary['completed']),
		));
	}

	/**
	 * Create a friend request from a numeric profile user ID.
	 */
	public function create_request($userid)
	{
		$this->language->add_lang('zebra_enchance', 'anavaro/zebraenhance');
		if ((int) $this->user->data['user_id'] === ANONYMOUS || !$this->auth->acl_get('u_zebraenhance_use_friend_requests'))
		{
			return $this->error('ZEBRAENHANCE_AJAX_NOT_AUTHORIZED', 403);
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
			return $this->error('ZEBRAENHANCE_FRIEND_REQUEST_UNCHANGED', 409);
		}

		return new JsonResponse(array(
			'success' => true,
			'action'  => $result,
			'message' => $this->language->lang($result === 'accepted' ? 'ZEBRAENHANCE_REQUEST_ACCEPTED' : 'ZEBRAENHANCE_FRIEND_REQUEST_CREATED'),
		));
	}

	public function create_circle()
	{
		if ($error = $this->authorizebraenhance_relationship_change())
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
			'message' => $this->language->lang('ZEBRAENHANCE_CIRCLE_CREATED'),
		));
	}

	public function manage_circle($circleid, $action)
	{
		if ($error = $this->authorizebraenhance_relationship_change())
		{
			return $error;
		}
		$owner_id = (int) $this->user->data['user_id'];
		if ($action === 'delete')
		{
			if (!$this->relationships->delete_circle($owner_id, (int) $circleid))
			{
				return $this->error('ZEBRAENHANCE_CIRCLE_NOT_FOUND', 404);
			}
			return new JsonResponse(array(
				'success' => true,
				'message' => $this->language->lang('ZEBRAENHANCE_CIRCLE_DELETED'),
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
			'message' => $this->language->lang('ZEBRAENHANCE_CIRCLE_RENAMED'),
		));
	}

	public function set_friend_circles($userid)
	{
		if ($error = $this->authorizebraenhance_relationship_change())
		{
			return $error;
		}
		if (!$this->relationships->set_friend_circles(
			(int) $this->user->data['user_id'],
			(int) $userid,
			$this->request->variable('circle_ids', array(0))
		))
		{
			return $this->error('ZEBRAENHANCE_AJAX_NOT_FRIEND', 409);
		}

		return new JsonResponse(array(
			'success' => true,
			'message' => $this->language->lang('ZEBRAENHANCE_CIRCLES_SAVED'),
		));
	}

	public function update_foe($userid)
	{
		if ($error = $this->authorizebraenhance_relationship_change(true))
		{
			return $error;
		}
		if (!$this->relationships->update_foe(
			(int) $this->user->data['user_id'],
			(int) $userid,
			$this->request->variable('duration', -1),
			$this->request->variable('note', '', true),
			$this->request->variable('pm_policy', 0),
			$this->request->variable('content_policy', 0),
			$this->request->variable('notification_policy', 0)
		))
		{
			return $this->error('ZEBRAENHANCE_FOE_NOT_FOUND', 404);
		}

		return new JsonResponse(array(
			'success' => true,
			'message' => $this->language->lang('ZEBRAENHANCE_FOE_SAVED'),
		));
	}

	public function remove_foes()
	{
		if ($error = $this->authorizebraenhance_relationship_change(true))
		{
			return $error;
		}
		$foe_ids = array_values(array_filter(array_map('intval', $this->request->variable('foe_ids', array(0)))));
		if (!$foe_ids)
		{
			return $this->error('ZEBRAENHANCE_SELECT_FOE', 400);
		}
		$removed = $this->relationships->remove_foes((int) $this->user->data['user_id'], $foe_ids);
		if (!$removed)
		{
			return $this->error('ZEBRAENHANCE_FOE_NOT_FOUND', 404);
		}

		return new JsonResponse(array(
			'success' => true,
			'removed' => $removed,
			'message' => $this->language->lang('ZEBRAENHANCE_FOES_REMOVED', $removed),
		));
	}

	protected function authorizebraenhance_relationship_change($foe_enhancement = false)
	{
		$this->language->add_lang('zebra_enchance', 'anavaro/zebraenhance');
		if ((int) $this->user->data['user_id'] === ANONYMOUS || !$this->auth->acl_get('u_zebraenhance_use_friend_requests'))
		{
			return $this->error('ZEBRAENHANCE_AJAX_NOT_AUTHORIZED', 403);
		}
		if ($foe_enhancement && !$this->relationships->foe_feature_enabled())
		{
			return $this->error('ZEBRAENHANCE_AJAX_NOT_AUTHORIZED', 403);
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
			'invalid'   => array('ZEBRAENHANCE_CIRCLE_INVALID', 400),
			'duplicate' => array('ZEBRAENHANCE_CIRCLE_DUPLICATE', 409),
			'limit'     => array('ZEBRAENHANCE_CIRCLE_LIMIT', 409),
			'not_found' => array('ZEBRAENHANCE_CIRCLE_NOT_FOUND', 404),
		);
		$error = isset($errors[$result]) ? $errors[$result] : array('ZEBRAENHANCE_REQUEST_FAILED', 409);
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
