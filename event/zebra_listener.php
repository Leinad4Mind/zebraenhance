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

namespace anavaro\zebraenhance\event;

use anavaro\zebraenhance\service\relationship_manager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class zebra_listener implements EventSubscriberInterface
{
	/** Friends shown on a profile at once, and loaded by each "Show more" */
	const PROFILE_FRIENDS_PAGE_SIZE = 50;

	/** @var \anavaro\zebraenhance\service\relationship_manager */
	protected $relationships;

	/** @var \phpbb\user_loader */
	protected $user_loader;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\controller\helper */
	protected $controller_helper;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var bool */
	protected $profile_context_ready = false;

	/** @var bool */
	protected $profile_hide_native_add = false;

	/** @var array|null */
	protected $foe_identities;

	/** @var array Poster user ID => birthday visibility, for posters who restrict it */
	protected $poster_birthday_visibility = array();

	/** @var array|null Poster user IDs whose age the viewer may see, as keys */
	protected $visible_poster_birthdays;

	/** @var array|null The index birthday list after hidden birthdays were removed */
	protected $index_birthdays;

	/** @var array Placeholder tokens of the text being rendered, mapped to their HTML */
	protected $foe_quote_replacements = array();

	/** @var string */
	protected $foe_quote_nonce = '';

	public function __construct(
		\anavaro\zebraenhance\service\relationship_manager $relationships,
		\phpbb\user_loader $user_loader,
		\phpbb\auth\auth $auth,
		\phpbb\request\request_interface $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\language\language $language,
		\phpbb\controller\helper $controller_helper,
		\phpbb\pagination $pagination,
		\phpbb\config\config $config,
		$root_path,
		$php_ext
	)
	{
		$this->relationships = $relationships;
		$this->user_loader = $user_loader;
		$this->auth = $auth;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->language = $language;
		$this->controller_helper = $controller_helper;
		$this->pagination = $pagination;
		$this->config = $config;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'                => 'load_language_on_setup',
			'core.ucp_add_zebra'             => 'zebra_confirm_add',
			'core.ucp_remove_zebra'          => 'zebra_confirm_remove',
			'core.ucp_display_module_before' => 'module_display',
			'core.message_list_actions'       => 'block_foe_pm_recipients',
			'core.submit_pm_before'           => 'guard_foe_pm_submission',
			'core.viewtopic_modify_post_data' => 'hide_foe_posts',
			'core.topic_review_modify_post_list' => 'hide_foe_posts',
			'core.text_formatter_s9e_render_before' => 'hide_foe_quotes',
			'core.text_formatter_s9e_render_after' => 'insert_foe_quote_notices',
			'core.viewforum_modify_topics_data' => 'hide_foe_topics',
			'core.viewforum_modify_topicrow'  => 'mark_foe_topic_row',
			'core.search_modify_rowset'       => 'hide_foe_search_results',
			'core.search_modify_tpl_ary'      => 'mark_foe_search_topic_row',
			'core.page_header'                => 'assign_foe_content_assets',
			'core.notification_manager_add_notifications' => 'mute_foe_notifications',
			'core.delete_user_before'        => 'delete_users',
			'core.memberlist_view_profile'   => 'prepare_friends',
			'core.memberlist_modify_view_profile_template_vars' => 'modify_profile_template_vars',
			'core.memberlist_prepare_profile_data' => 'hide_profile_age',
			'core.viewtopic_cache_user_data'  => 'collect_poster_birthday_visibility',
			'core.viewtopic_modify_post_row'  => 'hide_poster_age',
			'core.index_modify_birthdays_sql' => 'select_birthday_visibility',
			'core.index_modify_birthdays_list' => 'hide_index_birthdays',
			'core.index_modify_page_title'    => 'rebuild_birthday_list',
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'anavaro/zebraenhance',
			'lang_set' => 'zebra_enchance',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function zebra_confirm_add($event)
	{
		$mode = $event['mode'];
		$sql_ary = $event['sql_ary'];
		if ($mode === 'friends' && !$this->auth->acl_get('u_zebraenhance_use_friend_requests'))
		{
			$event['sql_ary'] = array();
			trigger_error('ZEBRAENHANCE_FRIEND_REQUEST_NOT_AUTHORIZED');
			return;
		}

		$results = array();
		$event['sql_ary'] = $this->relationships->process_additions(
			$mode,
			$sql_ary,
			$results,
			$this->request->variable('zebraenhance_request_message', '', true)
		);
		if ($mode === 'friends' && !array_intersect($results, array('created', 'accepted')))
		{
			trigger_error('ZEBRAENHANCE_FRIEND_REQUEST_UNCHANGED');
		}
	}

	public function zebra_confirm_remove($event)
	{
		if ($event['mode'] === 'foes')
		{
			$this->relationships->remove_foes((int) $this->user->data['user_id'], $event['user_ids']);
			$event['user_ids'] = array(0);
			return;
		}
		if ($event['mode'] !== 'friends')
		{
			return;
		}

		$this->relationships->remove_relationships((int) $this->user->data['user_id'], $event['user_ids']);

		// The service removed both directions, so keep phpBB's trailing DELETE inert.
		$event['user_ids'] = array(0);
	}

	public function module_display($event)
	{
		if (!$this->is_zebra_friends_module($event) || !$this->auth->acl_get('u_zebraenhance_use_friend_requests'))
		{
			return;
		}

		add_form_key('anavaro_zebraenhance', '_ZEBRAENHANCE');
		$user_id = (int) $this->user->data['user_id'];
		$friend_search = utf8_substr(
			trim($this->request->variable('zebraenhance_friend_q', '', true)),
			0,
			\anavaro\zebraenhance\service\relationship_manager::MAX_FRIEND_SEARCH_LENGTH
		);
		if ($this->request->is_set_post('zebra_profile_acl')
			|| $this->request->is_set_post('zebra_request_policy')
			|| $this->request->is_set_post('zebra_pm_policy')
			|| $this->request->is_set_post('zebra_birthday_show')
			|| $this->request->is_set_post('zebra_hide_foe_content')
			|| $this->request->is_set_post('zebra_mute_foe_notifications'))
		{
			if (!check_form_key('anavaro_zebraenhance'))
			{
				trigger_error('FORM_INVALID');
			}

			if ($this->request->is_set_post('zebra_profile_acl'))
			{
				$visibility = $this->relationships->set_friend_list_visibility(
					$user_id,
					$this->request->variable('zebra_profile_acl', 5)
				);
				$this->user->data['profile_friend_show'] = $visibility;
			}
			if ($this->request->is_set_post('zebra_request_policy'))
			{
				$policy = $this->relationships->set_request_policy(
					$user_id,
					$this->request->variable('zebra_request_policy', 0)
				);
				$this->user->data['zebra_request_policy'] = $policy;
			}
			if ($this->relationships->foe_feature_enabled('pm')
				&& $this->request->is_set_post('zebra_pm_policy'))
			{
				$this->user->data['zebra_block_foe_pm'] = $this->relationships->set_pm_policy(
					$user_id,
					$this->request->variable('zebra_pm_policy', 0)
				);
			}
			if (!empty($this->config['allow_birthdays'])
				&& $this->request->is_set_post('zebra_birthday_show'))
			{
				$this->user->data['zebra_birthday_show'] = $this->relationships->set_birthday_visibility(
					$user_id,
					$this->request->variable('zebra_birthday_show', 0)
				);
			}
			if ($this->relationships->foe_feature_enabled('content')
				&& $this->request->is_set_post('zebra_hide_foe_content'))
			{
				$hide_foe_content = $this->relationships->set_hide_foe_content(
					$user_id,
					$this->request->variable('zebra_hide_foe_content', 0)
				);
				$this->user->data['zebra_hide_foe_content'] = $hide_foe_content;
			}
			if ($this->relationships->foe_feature_enabled('notifications')
				&& $this->request->is_set_post('zebra_mute_foe_notifications'))
			{
				$mute_foe_notifications = $this->relationships->set_mute_foe_notifications(
					$user_id,
					$this->request->variable('zebra_mute_foe_notifications', 0)
				);
				$this->user->data['zebra_mute_foe_notifications'] = $mute_foe_notifications;
			}
		}

		$this->template->assign_vars(array(
			'IS_ZEBRA'           => true,
			'ZEBRA_ACL'          => (int) $this->user->data['profile_friend_show'],
			'ZEBRA_REQUEST_POLICY' => isset($this->user->data['zebra_request_policy']) ? (int) $this->user->data['zebra_request_policy'] : 0,
			'ZEBRA_PM_POLICY'    => relationship_manager::normalize_pm_policy(
				isset($this->user->data['zebra_block_foe_pm']) ? $this->user->data['zebra_block_foe_pm'] : 0
			),
			'ZEBRA_BIRTHDAY_SHOW' => isset($this->user->data['zebra_birthday_show']) ? (int) $this->user->data['zebra_birthday_show'] : 0,
			'S_ZEBRAENHANCE_BIRTHDAY_PRIVACY_AVAILABLE' => !empty($this->config['allow_birthdays']),
			'ZEBRA_FOE_CONTENT_MODE' => relationship_manager::normalize_foe_content_mode(
				isset($this->user->data['zebra_hide_foe_content']) ? $this->user->data['zebra_hide_foe_content'] : 0
			),
			'S_ZEBRA_MUTE_FOE_NOTIFICATIONS' => !empty($this->user->data['zebra_mute_foe_notifications']),
			'S_ZEBRAENHANCE_FOE_PM_AVAILABLE' => $this->relationships->foe_feature_enabled('pm'),
			'S_ZEBRAENHANCE_FOE_CONTENT_AVAILABLE' => $this->relationships->foe_feature_enabled('content'),
			'S_ZEBRAENHANCE_FOE_NOTIFICATIONS_AVAILABLE' => $this->relationships->foe_feature_enabled('notifications'),
			'S_CAN_CLOSE_FRIENDS' => $this->auth->acl_get('u_zebraenhance_manage_close_friends'),
			'FRIEND_SEARCH'       => $friend_search,
			'U_FRIEND_SEARCH'     => $this->ucp_friend_url(''),
			'U_CREATE_CIRCLE'     => $this->controller_helper->route('anavaro_zebraenhance_create_circle'),
			'U_BULK_ACCEPT'       => $this->controller_helper->route('anavaro_zebraenhance_manage_requests', array('action' => 'accept')),
			'U_BULK_DECLINE'      => $this->controller_helper->route('anavaro_zebraenhance_manage_requests', array('action' => 'decline')),
			'U_BULK_CANCEL'       => $this->controller_helper->route('anavaro_zebraenhance_manage_requests', array('action' => 'cancel')),
		));

		$page_size = \anavaro\zebraenhance\service\relationship_manager::PAGE_SIZE;
		$incoming_start = max(0, $this->request->variable('zebraenhance_in_start', 0));
		$outgoing_start = max(0, $this->request->variable('zebraenhance_out_start', 0));
		$friends_start = max(0, $this->request->variable('zebraenhance_friend_start', 0));
		$incoming_count = $this->relationships->count_requests($user_id, true);
		$outgoing_count = $this->relationships->count_requests($user_id, false);
		$friends_count = $this->relationships->count_friends($user_id, $friend_search);
		$incoming = $this->relationships->get_requests($user_id, true, $page_size, $incoming_start);
		$outgoing = $this->relationships->get_requests($user_id, false, $page_size, $outgoing_start);
		$friends = $this->relationships->get_friends($user_id, $page_size, $friends_start, $friend_search);
		$suggestions = $this->relationships->get_friend_suggestions($user_id);
		$circles = $this->relationships->get_circles($user_id);
		foreach ($circles as $circle)
		{
			$this->template->assign_block_vars('zebraenhance_circles', array(
				'ID'           => (int) $circle['circle_id'],
				'NAME'         => (string) $circle['circle_name'],
				'MEMBER_COUNT' => (int) $circle['member_count'],
				'U_RENAME'     => $this->controller_helper->route('anavaro_zebraenhance_manage_circle', array(
					'circleid' => (int) $circle['circle_id'],
					'action'   => 'rename',
				)),
				'U_DELETE'     => $this->controller_helper->route('anavaro_zebraenhance_manage_circle', array(
					'circleid' => (int) $circle['circle_id'],
					'action'   => 'delete',
				)),
			));
		}
		$base_url = $this->ucp_friend_url($friend_search !== '' ? 'zebraenhance_friend_q=' . rawurlencode($friend_search) : '');
		$this->pagination->generate_template_pagination($base_url, 'zebraenhance_in_pagination', 'zebraenhance_in_start', $incoming_count, $page_size, $incoming_start);
		$this->pagination->generate_template_pagination($base_url, 'zebraenhance_out_pagination', 'zebraenhance_out_start', $outgoing_count, $page_size, $outgoing_start);
		$this->pagination->generate_template_pagination($base_url, 'zebraenhance_friend_pagination', 'zebraenhance_friend_start', $friends_count, $page_size, $friends_start);
		$identity_ids = array();
		foreach (array_merge($incoming, $outgoing) as $row)
		{
			$identity_ids[] = (int) ((int) $row['requester_id'] === $user_id ? $row['recipient_id'] : $row['requester_id']);
		}
		foreach ($friends as $row)
		{
			$identity_ids[] = (int) $row['zebra_id'];
		}
		foreach ($suggestions as $row)
		{
			$identity_ids[] = (int) $row['user_id'];
		}
		$identity_ids = array_values(array_unique(array_filter($identity_ids)));
		if ($identity_ids)
		{
			$this->user_loader->load_users($identity_ids);
		}
		$friend_ids = array_map(function ($row)
		{
			return (int) $row['zebra_id'];
		}, $friends);
		$circle_memberships = $this->relationships->get_circle_memberships($user_id, $friend_ids);

		foreach ($incoming as $row)
		{
			$requester_id = (int) $row['requester_id'];
			$this->template->assign_block_vars('pending_requests', array(
				'REQUEST_ID'    => (int) $row['request_id'],
				'USER_ID'       => $requester_id,
				'USERNAME'      => (string) $row['username'],
				'USERNAME_FULL' => $this->user_loader->get_username($requester_id, 'full'),
				'MESSAGE'       => $this->display_request_message($row['request_message']),
				'U_ACCEPT'      => $this->request_action_url((int) $row['request_id'], 'accept'),
				'U_DECLINE'     => $this->request_action_url((int) $row['request_id'], 'decline'),
				'U_DECLINE_BLOCK' => $this->request_action_url((int) $row['request_id'], 'decline_block'),
			));
		}

		foreach ($outgoing as $row)
		{
			$recipient_id = (int) $row['recipient_id'];
			$this->template->assign_block_vars('pending_awaits', array(
				'REQUEST_ID'    => (int) $row['request_id'],
				'USER_ID'       => $recipient_id,
				'USERNAME'      => (string) $row['username'],
				'USERNAME_FULL' => $this->user_loader->get_username($recipient_id, 'full'),
				'MESSAGE'       => $this->display_request_message($row['request_message']),
				'U_CANCEL'      => $this->request_action_url((int) $row['request_id'], 'cancel'),
			));
		}

		foreach ($suggestions as $row)
		{
			$suggested_user_id = (int) $row['user_id'];
			$this->template->assign_block_vars('zebraenhance_friend_suggestions', array(
				'USER_ID'       => $suggested_user_id,
				'USERNAME_FULL' => $this->user_loader->get_username($suggested_user_id, 'full'),
				'MUTUAL_COUNT'  => (int) $row['mutual_count'],
				'U_REQUEST'     => $this->controller_helper->route('anavaro_zebraenhance_create_request', array(
					'userid' => $suggested_user_id,
				)),
			));
		}

		foreach ($friends as $row)
		{
			$friend_id = (int) $row['zebra_id'];
			$is_close = (bool) $row['bff'];
			$this->template->assign_block_vars('pretty_zebra', array(
				'USER_ID'       => $friend_id,
				'USERNAME_FULL' => $this->user_loader->get_username($friend_id, 'full'),
				'U_CANCEL'      => $this->ucp_friend_url('remove=1&usernames[]=' . $friend_id),
				'U_CLOSE_ADD'   => $this->controller_helper->route('anavaro_zebraenhance_close_friend', array(
					'userid' => $friend_id,
					'state'  => 1,
				)),
				'U_CLOSE_REMOVE' => $this->controller_helper->route('anavaro_zebraenhance_close_friend', array(
					'userid' => $friend_id,
					'state'  => 0,
				)),
				'S_CLOSE'       => $is_close,
				'L_CLOSE_ACTION' => $this->language->lang($is_close ? 'ZEBRAENHANCE_REMOVE_CLOSE_FRIEND' : 'ZEBRAENHANCE_ADD_CLOSE_FRIEND'),
				'U_CIRCLES'      => $this->controller_helper->route('anavaro_zebraenhance_friend_circles', array(
					'userid' => $friend_id,
				)),
			));
			$selected_circle_ids = isset($circle_memberships[$friend_id]) ? $circle_memberships[$friend_id] : array();
			foreach ($circles as $circle)
			{
				$this->template->assign_block_vars('pretty_zebra.circles', array(
					'ID'         => (int) $circle['circle_id'],
					'NAME'       => (string) $circle['circle_name'],
					'S_SELECTED' => in_array((int) $circle['circle_id'], $selected_circle_ids, true),
				));
			}
		}
	}

	public function block_foe_pm_recipients($event)
	{
		$address_list = $event['address_list'];
		$filtered = $this->relationships->filter_pm_address_list(
			(int) $this->user->data['user_id'],
			$address_list
		);
		if ($filtered === $address_list)
		{
			return;
		}

		$error = $event['error'];
		$error[] = $this->language->lang('ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED');
		$event['address_list'] = $filtered;
		$event['error'] = $error;
	}

	public function guard_foe_pm_submission($event)
	{
		if ($event['mode'] === 'edit')
		{
			return;
		}

		$data = $event['data'];
		$address_list = isset($data['address_list']) && is_array($data['address_list'])
			? $data['address_list']
			: array();
		if ($this->relationships->filter_pm_address_list((int) $this->user->data['user_id'], $address_list) !== $address_list)
		{
			trigger_error('ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED');
		}
	}

	public function hide_foe_posts($event)
	{
		// In the notice mode phpBB's own "post by a foe" placeholder is kept.
		if ($this->foe_content_mode() !== relationship_manager::FOE_CONTENT_REMOVE)
		{
			return;
		}

		$foe_ids = array_fill_keys(array_keys($this->get_foe_identities()), true);
		if (!$foe_ids)
		{
			return;
		}
		$rowset = $event['rowset'];
		foreach ($rowset as $post_id => $row)
		{
			$poster_id = isset($row['poster_id']) ? (int) $row['poster_id'] : (isset($row['user_id']) ? (int) $row['user_id'] : 0);
			if (isset($foe_ids[$poster_id]))
			{
				unset($rowset[$post_id]);
			}
		}
		$event['rowset'] = $rowset;
	}

	public function hide_foe_quotes($event)
	{
		$this->foe_quote_replacements = array();
		$mode = $this->foe_content_mode();
		if ($mode === relationship_manager::FOE_CONTENT_SHOW || !class_exists('\\DOMDocument'))
		{
			return;
		}

		$xml = $event['xml'];
		if (strpos($xml, '<QUOTE') === false)
		{
			return;
		}

		$foe_identities = $this->get_foe_identities();
		if (!$foe_identities)
		{
			return;
		}
		$foe_names = array_fill_keys(array_values($foe_identities), true);

		$document = new \DOMDocument();
		$previous_errors = libxml_use_internal_errors(true);
		$loaded = $document->loadXML($xml, LIBXML_NONET);
		libxml_clear_errors();
		libxml_use_internal_errors($previous_errors);
		if (!$loaded || !$document->documentElement)
		{
			return;
		}

		$foe_quotes = array();
		foreach ($document->getElementsByTagName('QUOTE') as $quote)
		{
			$quote_user_id = (int) $quote->getAttribute('user_id');
			$hide_quote = $quote_user_id
				? isset($foe_identities[$quote_user_id])
				: isset($foe_names[utf8_clean_string($quote->getAttribute('author'))]);
			if ($hide_quote)
			{
				$foe_quotes[] = $quote;
			}
		}
		if (!$foe_quotes)
		{
			return;
		}

		if ($mode === relationship_manager::FOE_CONTENT_NOTICE)
		{
			foreach ($foe_quotes as $index => $quote)
			{
				$this->wrap_foe_quote($document, $quote, $index);
			}
		}
		else
		{
			for ($i = count($foe_quotes) - 1; $i >= 0; $i--)
			{
				$quote = $foe_quotes[$i];
				if ($quote->parentNode)
				{
					$quote->parentNode->removeChild($quote);
				}
			}
		}
		$event['xml'] = $document->saveXML($document->documentElement);
	}

	/**
	 * Turn the placeholders left by hide_foe_quotes() into the collapsible notice.
	 */
	public function insert_foe_quote_notices($event)
	{
		if (!$this->foe_quote_replacements)
		{
			return;
		}

		$event['html'] = strtr($event['html'], $this->foe_quote_replacements);
		$this->foe_quote_replacements = array();
	}

	public function hide_foe_search_results($event)
	{
		if ($this->foe_content_mode() !== relationship_manager::FOE_CONTENT_REMOVE
			|| !in_array($event['show_results'], array('posts', 'topics'), true))
		{
			return;
		}

		$foe_ids = array_fill_keys(array_keys($this->get_foe_identities()), true);
		if (!$foe_ids)
		{
			return;
		}
		$author_key = $event['show_results'] === 'topics' ? 'topic_poster' : 'poster_id';
		$rowset = $event['rowset'];
		foreach ($rowset as $result_id => $row)
		{
			if (isset($row[$author_key], $foe_ids[(int) $row[$author_key]]))
			{
				unset($rowset[$result_id]);
			}
		}
		$event['rowset'] = $rowset;
	}

	public function hide_foe_topics($event)
	{
		if ($this->foe_content_mode() !== relationship_manager::FOE_CONTENT_REMOVE)
		{
			return;
		}

		$foe_ids = array_fill_keys(array_keys($this->get_foe_identities()), true);
		if (!$foe_ids)
		{
			return;
		}
		$rowset = $event['rowset'];
		$removed = array();
		foreach ($rowset as $topic_id => $row)
		{
			if (isset($row['topic_poster'], $foe_ids[(int) $row['topic_poster']]))
			{
				unset($rowset[$topic_id]);
				$removed[(int) $topic_id] = true;
			}
		}
		if (!$removed)
		{
			return;
		}

		$topic_list = array();
		foreach ($event['topic_list'] as $topic_id)
		{
			if (!isset($removed[(int) $topic_id]))
			{
				$topic_list[] = $topic_id;
			}
		}
		$event['rowset'] = $rowset;
		$event['topic_list'] = $topic_list;
	}

	public function mark_foe_topic_row($event)
	{
		$event['topic_row'] = $this->add_foe_topic_notice($event['row'], $event['topic_row']);
	}

	public function mark_foe_search_topic_row($event)
	{
		if ($event['show_results'] === 'topics')
		{
			$event['tpl_ary'] = $this->add_foe_topic_notice($event['row'], $event['tpl_ary']);
		}
	}

	/**
	 * Load the notice styles and script only for users who can see a notice.
	 */
	public function assign_foe_content_assets()
	{
		if ($this->foe_content_mode() === relationship_manager::FOE_CONTENT_NOTICE)
		{
			$this->template->assign_var('S_ZEBRAENHANCE_FOE_CONTENT_NOTICE', true);
		}
	}

	public function mute_foe_notifications($event)
	{
		$notification_types = array(
			'notification.type.quote',
			'notification.type.post',
			'notification.type.topic',
			'notification.type.bookmark',
			'notification.type.mention',
		);
		if (!in_array($event['notification_type_name'], $notification_types, true))
		{
			return;
		}
		$data = $event['data'];
		$actor_id = isset($data['poster_id']) ? (int) $data['poster_id'] : 0;
		if (!$actor_id)
		{
			return;
		}
		$event['notify_users'] = $this->relationships->filter_foe_notification_recipients(
			$actor_id,
			$event['notify_users']
		);
	}

	public function delete_users($event)
	{
		$this->relationships->delete_user_data($event['user_ids']);
	}

	public function prepare_friends($event)
	{
		$member = $event['member'];
		$owner_id = (int) $member['user_id'];
		$viewer_id = (int) $this->user->data['user_id'];
		$viewer_registered = $this->viewer_is_registered();
		$can_use = $this->auth->acl_get('u_zebraenhance_use_friend_requests');
		$friends_page_request = $this->request->is_ajax() && $this->request->is_set('zebraenhance_friends_start');
		$this->profile_context_ready = true;
		$this->profile_hide_native_add = !$can_use || !$viewer_registered;

		if (!$friends_page_request && $can_use && $viewer_registered && $viewer_id !== $owner_id)
		{
			$request = $this->relationships->get_pending_request_between($viewer_id, $owner_id);
			$friend = isset($event['friend']) && (bool) $event['friend'];
			$foe = isset($event['foe']) && (bool) $event['foe'];
			if ($request || (!$friend && !$foe))
			{
				add_form_key('anavaro_zebraenhance', '_ZEBRAENHANCE');
				$is_incoming = $request && (int) $request['recipient_id'] === $viewer_id;
				$this->profile_hide_native_add = true;
				$this->template->assign_vars(array(
					'S_ZEBRAENHANCE_PROFILE_FRIEND_CONTROL' => true,
					'S_ZEBRAENHANCE_PROFILE_CAN_CREATE'     => !$request,
					'S_ZEBRAENHANCE_PROFILE_INCOMING'       => $is_incoming,
					'S_ZEBRAENHANCE_PROFILE_OUTGOING'       => $request && !$is_incoming,
					'U_ZEBRAENHANCE_PROFILE_CREATE'         => !$request ? $this->controller_helper->route('anavaro_zebraenhance_create_request', array(
						'userid' => $owner_id,
					)) : '',
					'U_ZEBRAENHANCE_PROFILE_ACCEPT'         => $is_incoming ? $this->request_action_url((int) $request['request_id'], 'accept') : '',
					'U_ZEBRAENHANCE_PROFILE_DECLINE'        => $is_incoming ? $this->request_action_url((int) $request['request_id'], 'decline') : '',
					'U_ZEBRAENHANCE_PROFILE_DECLINE_BLOCK'  => $is_incoming ? $this->request_action_url((int) $request['request_id'], 'decline_block') : '',
					'U_ZEBRAENHANCE_PROFILE_CANCEL'         => $request && !$is_incoming ? $this->request_action_url((int) $request['request_id'], 'cancel') : '',
					'ZEBRAENHANCE_PROFILE_REQUEST_MESSAGE'  => $request ? $this->display_request_message($request['request_message']) : '',
				));
			}
		}

		if (!$can_use)
		{
			if ($friends_page_request)
			{
				$this->send_friends_page_error('FRIENDLIST_ERROR_ACCESS', 403);
			}
			return;
		}

		$can_view = $this->relationships->can_view_friend_list(
			$owner_id,
			$viewer_id,
			(int) $member['profile_friend_show'],
			$this->can_see_private_details(),
			$viewer_registered
		);

		if ($friends_page_request)
		{
			if (!$can_view)
			{
				$this->send_friends_page_error('FRIENDLIST_ERROR_ACCESS', 403);
				return;
			}
			$this->send_friends_page($owner_id, max(0, (int) $this->request->variable('zebraenhance_friends_start', 0)));
			return;
		}

		$this->template->assign_vars(array(
			'FRIENDLIST'              => true,
			'FRIENDLIST_ERROR_ACCESS' => !$can_view,
		));
		if (!$can_view)
		{
			return;
		}

		$total = $this->relationships->count_friends($owner_id);
		if (!$total)
		{
			return;
		}

		// Without JavaScript, "Show more" reloads the profile with every friend up to the next page.
		$start = min(max(0, (int) $this->request->variable('zebraenhance_friends_start', 0)), $total);
		$friends = $this->relationships->get_friends($owner_id, $start + self::PROFILE_FRIENDS_PAGE_SIZE);
		$mutual_friends = $viewer_registered && $viewer_id !== $owner_id
			? $this->relationships->get_mutual_friends($owner_id, $viewer_id)
			: array();

		$this->load_profile_friends(array_merge($friends, $mutual_friends));
		foreach ($mutual_friends as $row)
		{
			$this->template->assign_block_vars('zebra_mutual_friends', $this->profile_friend_row((int) $row['zebra_id']));
		}
		foreach ($friends as $row)
		{
			$this->template->assign_block_vars('zebra_friendslist', $this->profile_friend_row((int) $row['zebra_id']));
		}

		$shown = count($friends);
		$this->template->assign_vars(array(
			'ZEBRAENHANCE_FRIENDS_TOTAL' => $total,
			'ZEBRAENHANCE_FRIENDS_SHOWN' => $total > self::PROFILE_FRIENDS_PAGE_SIZE ? $this->language->lang('ZEBRAENHANCE_FRIENDS_SHOWN', $total, $shown) : '',
			'U_ZEBRAENHANCE_MORE_FRIENDS' => $shown < $total ? $this->more_friends_url($owner_id, $shown) : '',
		));
	}

	/**
	 * Hide the age on profiles and in the member list from viewers the user does not allow.
	 */
	public function hide_profile_age($event)
	{
		$data = $event['data'];
		$template_data = $event['template_data'];
		if (!isset($template_data['AGE']) || $template_data['AGE'] === '' || empty($data['zebra_birthday_show']))
		{
			return;
		}

		$owner_id = (int) $data['user_id'];
		if (!$this->visible_birthday_owners(array($owner_id => (int) $data['zebra_birthday_show'])))
		{
			$template_data['AGE'] = '';
			$event['template_data'] = $template_data;
		}
	}

	public function collect_poster_birthday_visibility($event)
	{
		$row = $event['row'];
		if (!empty($row['zebra_birthday_show']))
		{
			$this->poster_birthday_visibility[(int) $event['poster_id']] = (int) $row['zebra_birthday_show'];
			$this->visible_poster_birthdays = null;
		}
	}

	/**
	 * Hide the age next to posts, checking every poster on the page at once.
	 */
	public function hide_poster_age($event)
	{
		$poster_id = (int) $event['poster_id'];
		if (!isset($this->poster_birthday_visibility[$poster_id]))
		{
			return;
		}
		if ($this->visible_poster_birthdays === null)
		{
			$this->visible_poster_birthdays = array_flip($this->visible_birthday_owners($this->poster_birthday_visibility));
		}
		if (isset($this->visible_poster_birthdays[$poster_id]))
		{
			return;
		}

		$post_row = $event['post_row'];
		$post_row['POSTER_AGE'] = '';
		$event['post_row'] = $post_row;
	}

	public function select_birthday_visibility($event)
	{
		$sql_ary = $event['sql_ary'];
		$sql_ary['SELECT'] .= ', u.zebra_birthday_show';
		$event['sql_ary'] = $sql_ary;
	}

	/**
	 * Leave users out of the index birthday list when they hide their birthday from the viewer.
	 */
	public function hide_index_birthdays($event)
	{
		$rows = $event['rows'];
		$visibilities = array();
		foreach ($rows as $row)
		{
			if (!empty($row['zebra_birthday_show']))
			{
				$visibilities[(int) $row['user_id']] = (int) $row['zebra_birthday_show'];
			}
		}
		if (!$visibilities)
		{
			return;
		}

		$visible = array_flip($this->visible_birthday_owners($visibilities));
		$birthdays = $event['birthdays'];
		$kept_rows = $kept_birthdays = array();
		foreach (array_values($rows) as $index => $row)
		{
			$user_id = (int) $row['user_id'];
			if (isset($visibilities[$user_id]) && !isset($visible[$user_id]))
			{
				continue;
			}
			$kept_rows[] = $row;
			if (isset($birthdays[$index]))
			{
				$kept_birthdays[] = $birthdays[$index];
			}
		}
		if (count($kept_rows) === count($rows))
		{
			return;
		}

		$event['rows'] = $kept_rows;
		$event['birthdays'] = $kept_birthdays;
		$this->index_birthdays = $kept_birthdays;
	}

	/**
	 * phpBB builds the old BIRTHDAY_LIST variable before the birthday list event
	 * can change it, so rebuild it from the filtered list.
	 */
	public function rebuild_birthday_list()
	{
		if ($this->index_birthdays === null)
		{
			return;
		}

		$list = array();
		foreach ($this->index_birthdays as $birthday)
		{
			$list[] = $birthday['USERNAME'] . (!empty($birthday['AGE']) ? ' (' . $birthday['AGE'] . ')' : '');
		}
		$this->template->assign_var('BIRTHDAY_LIST', implode($this->language->lang('COMMA_SEPARATOR'), $list));
	}

	public function modify_profile_template_vars($event)
	{
		if (!$this->profile_context_ready || !$this->profile_hide_native_add)
		{
			return;
		}

		$template_ary = $event['template_ary'];
		$template_ary['U_ADD_FRIEND'] = '';
		$event['template_ary'] = $template_ary;
	}

	protected function is_zebra_friends_module($event)
	{
		if ($event['mode'] !== 'friends')
		{
			return false;
		}

		if (!isset($event['module']->module_ary) || !is_array($event['module']->module_ary))
		{
			return false;
		}

		$id = $event['id'];
		$normalized_id = is_numeric($id) ? (int) $id : 'ucp_' . preg_replace('#^ucp_#', '', (string) $id);
		foreach ($event['module']->module_ary as $module)
		{
			if ($module['name'] !== 'ucp_zebra' || $module['mode'] !== 'friends')
			{
				continue;
			}

			return is_int($normalized_id) ? (int) $module['id'] === $normalized_id : $normalized_id === 'ucp_zebra';
		}

		return false;
	}

	protected function hide_foe_content_enabled()
	{
		return !empty($this->user->data['is_registered'])
			&& $this->relationships->foe_feature_enabled('content');
	}

	/**
	 * How content from the viewer's protected foes is presented.
	 *
	 * Which foes are protected is decided by get_foe_identities(). A per-foe
	 * block applies even when the global choice is to show content, and it then
	 * removes the content as it did before the notice mode existed.
	 *
	 * @return int One of the relationship_manager::FOE_CONTENT_* constants
	 */
	protected function foe_content_mode()
	{
		if (!$this->hide_foe_content_enabled())
		{
			return relationship_manager::FOE_CONTENT_SHOW;
		}
		$mode = isset($this->user->data['zebra_hide_foe_content']) ? (int) $this->user->data['zebra_hide_foe_content'] : 0;

		return $mode === relationship_manager::FOE_CONTENT_NOTICE
			? relationship_manager::FOE_CONTENT_NOTICE
			: relationship_manager::FOE_CONTENT_REMOVE;
	}

	/**
	 * Surround a foe quote with placeholders that insert_foe_quote_notices()
	 * replaces with a collapsed <details> element after rendering.
	 *
	 * The author is left as post text between two placeholders, so phpBB
	 * escapes and censors it like the rest of the post.
	 */
	protected function wrap_foe_quote(\DOMDocument $document, \DOMElement $quote, $index)
	{
		$parent = $quote->parentNode;
		if (!$parent)
		{
			return;
		}

		$open = '<div class="zebraenhance-foe-quote"><details><summary>';
		$toggle = ' <span class="zebraenhance-foe-quote-show">' . $this->language->lang('ZEBRAENHANCE_FOE_QUOTE_SHOW') . '</span>'
			. '<span class="zebraenhance-foe-quote-hide" hidden>' . $this->language->lang('ZEBRAENHANCE_FOE_QUOTE_HIDE') . '</span></summary>';
		$close_token = $this->foe_quote_token($index, 3);
		$this->foe_quote_replacements[$close_token] = '</details></div>';

		$author = trim($quote->getAttribute('author'));
		if ($author === '')
		{
			$open_token = $this->foe_quote_token($index, 1);
			$this->foe_quote_replacements[$open_token] = $open . $this->language->lang('ZEBRAENHANCE_FOE_QUOTE_HIDDEN_UNKNOWN') . $toggle;
			$parent->insertBefore($document->createTextNode($open_token), $quote);
		}
		else
		{
			// Split the translated sentence around the author so word order stays translatable.
			$marker = $this->foe_quote_token($index, 0);
			$notice = explode($marker, $this->language->lang('ZEBRAENHANCE_FOE_QUOTE_HIDDEN', $marker), 2);
			$open_token = $this->foe_quote_token($index, 1);
			$author_token = $this->foe_quote_token($index, 2);
			$this->foe_quote_replacements[$open_token] = $open . $notice[0] . '<strong>';
			$this->foe_quote_replacements[$author_token] = '</strong>' . (isset($notice[1]) ? $notice[1] : '') . $toggle;
			$parent->insertBefore($document->createTextNode($open_token), $quote);
			$parent->insertBefore($document->createTextNode($author), $quote);
			$parent->insertBefore($document->createTextNode($author_token), $quote);
		}

		if ($quote->nextSibling)
		{
			$parent->insertBefore($document->createTextNode($close_token), $quote->nextSibling);
		}
		else
		{
			$parent->appendChild($document->createTextNode($close_token));
		}
	}

	/**
	 * A placeholder made of private-use delimiters and digits, which the
	 * renderer and the word censor leave untouched. The per-request nonce
	 * keeps post text from forging one.
	 */
	protected function foe_quote_token($index, $part)
	{
		if ($this->foe_quote_nonce === '')
		{
			$this->foe_quote_nonce = (string) random_int(100000000, 999999999);
		}

		return "\u{E000}" . $this->foe_quote_nonce . '.' . (int) $index . '.' . (int) $part . "\u{E001}";
	}

	/**
	 * Flag a topic row started by a protected foe when the notice mode is used.
	 */
	protected function add_foe_topic_notice(array $row, array $template_row)
	{
		if ($this->foe_content_mode() !== relationship_manager::FOE_CONTENT_NOTICE || empty($row['topic_poster']))
		{
			return $template_row;
		}
		$foe_identities = $this->get_foe_identities();
		if (!isset($foe_identities[(int) $row['topic_poster']]))
		{
			return $template_row;
		}

		$template_row['S_ZEBRAENHANCE_FOE_TOPIC'] = true;
		$template_row['ZEBRAENHANCE_FOE_TOPIC_NOTICE'] = $this->language->lang(
			'ZEBRAENHANCE_FOE_TOPIC_HIDDEN',
			get_username_string(
				'full',
				(int) $row['topic_poster'],
				isset($row['topic_first_poster_name']) ? $row['topic_first_poster_name'] : '',
				isset($row['topic_first_poster_colour']) ? $row['topic_first_poster_colour'] : ''
			)
		);

		return $template_row;
	}

	protected function get_foe_identities()
	{
		if ($this->foe_identities === null)
		{
			$this->foe_identities = $this->relationships->get_foe_identities(
				(int) $this->user->data['user_id']
			);
		}

		return $this->foe_identities;
	}

	/**
	 * Answer a "Show more" request with the next page of the profile friend list, then exit.
	 */
	protected function send_friends_page($owner_id, $start)
	{
		$total = $this->relationships->count_friends($owner_id);
		$friends = $start < $total
			? $this->relationships->get_friends($owner_id, self::PROFILE_FRIENDS_PAGE_SIZE, $start)
			: array();

		$this->load_profile_friends($friends);
		foreach ($friends as $row)
		{
			$this->template->assign_block_vars('zebra_friendslist', $this->profile_friend_row((int) $row['zebra_id']));
		}
		$this->template->set_filenames(array(
			'zebraenhance_profile_friends' => '@anavaro_zebraenhance/zebraenhance_profile_friends.html',
		));

		$shown = min($total, $start + count($friends));
		$this->send_json(array(
			'success'    => true,
			'html'       => $friends ? $this->template->assign_display('zebraenhance_profile_friends') : '',
			'shown'      => $this->language->lang('ZEBRAENHANCE_FRIENDS_SHOWN', $total, $shown),
			'next_start' => $friends && $shown < $total ? $shown : 0,
		));
	}

	protected function send_friends_page_error($language_key, $status_code)
	{
		$this->send_json(array(
			'success' => false,
			'message' => $this->language->lang($language_key),
		), $status_code);
	}

	/**
	 * Send a JSON response and end the request (overridden in tests).
	 */
	protected function send_json(array $data, $status_code = 200)
	{
		if ($status_code !== 200)
		{
			send_status_line($status_code, $status_code === 403 ? 'Forbidden' : 'Bad Request');
		}
		$response = new \phpbb\json_response();
		$response->send($data);
	}

	protected function load_profile_friends(array $rows)
	{
		$user_ids = array_values(array_unique(array_map(function ($row)
		{
			return (int) $row['zebra_id'];
		}, $rows)));
		if ($user_ids)
		{
			$this->user_loader->load_users($user_ids);
		}
	}

	protected function profile_friend_row($friend_id)
	{
		return array(
			'USER_ID'       => $friend_id,
			'USERNAME_FULL' => $this->user_loader->get_username($friend_id, 'full'),
			'U_PROFILE'     => $this->profile_url($friend_id),
			'USER_AVATAR'   => $this->user_loader->get_avatar($friend_id, false, true),
		);
	}

	protected function more_friends_url($owner_id, $start)
	{
		return append_sid(
			$this->root_path . 'memberlist.' . $this->php_ext,
			'mode=viewprofile&u=' . (int) $owner_id . '&zebraenhance_friends_start=' . (int) $start
		) . '#zebraenhance-friend-list';
	}

	/**
	 * @param array $visibilities Owner user ID => birthday visibility
	 * @return array Owner user IDs whose birthday the viewer may see
	 */
	protected function visible_birthday_owners(array $visibilities)
	{
		return $this->relationships->filter_visible_owners(
			(int) $this->user->data['user_id'],
			$visibilities,
			$this->can_see_private_details(),
			$this->viewer_is_registered()
		);
	}

	/**
	 * Administrators and moderators who may see private friend lists may also see hidden birthdays.
	 */
	protected function can_see_private_details()
	{
		return $this->auth->acl_get('a_user') || $this->auth->acl_get('m_zebraenhance_view_private_friendlists');
	}

	protected function viewer_is_registered()
	{
		return !empty($this->user->data['is_registered'])
			&& (!isset($this->user->data['user_type']) || (int) $this->user->data['user_type'] !== USER_IGNORE);
	}

	protected function profile_url($user_id)
	{
		return append_sid($this->root_path . 'memberlist.' . $this->php_ext, 'mode=viewprofile&u=' . (int) $user_id);
	}

	protected function display_request_message($message)
	{
		$message = html_entity_decode((string) $message, ENT_QUOTES, 'UTF-8');
		return utf8_htmlspecialchars(censor_text($message));
	}

	protected function ucp_friend_url($parameters)
	{
		$query = 'i=ucp_zebra&mode=friends';
		if ($parameters !== '')
		{
			$query .= '&' . $parameters;
		}

		return append_sid($this->root_path . 'ucp.' . $this->php_ext, $query);
	}

	protected function request_action_url($request_id, $action)
	{
		return $this->controller_helper->route('anavaro_zebraenhance_manage_request', array(
			'requestid' => (int) $request_id,
			'action'    => $action,
		));
	}
}
