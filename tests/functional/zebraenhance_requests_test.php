<?php
/**
*
* Zebra Enhance extension for phpBB.
*
* @copyright (c) 2014 Stanislav Atanasov
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\tests\functional;

/**
* @group functional
*/
class zebraenhance_requests_test extends zebraenhance_base
{
	public function test_request_lifecycle_uses_stable_post_actions()
	{
		$username = "ze'user";
		$this->create_user($username);

		$this->send_request_as_admin($username);
		$crawler = $this->open_friends_as('admin');
		$this->assertSame(1, $crawler->filter('link[href*="zebraenhance.css"]')->count());
		$this->assertSame(1, $crawler->filter('script[src*="zebraenhance.js"]')->count());
		$cancel = $crawler->filter('#ze-outgoing-requests .js-ze-request')->first();
		$this->assertStringContainsString('/cancel', $cancel->attr('data-url'));
		$this->post_action($cancel->attr('data-url'), array(), 403);
		$response = $this->post_action($cancel->attr('data-url'), $this->form_token($crawler));
		$this->assertSame('cancelled', $response['action']);
		$this->logout();

		$this->send_request_as_admin($username);
		$crawler = $this->open_friends_as($username);
		$accept = $crawler->filter('#ze-incoming-requests .js-ze-request')->first();
		$this->assertStringContainsString('/accept', $accept->attr('data-url'));
		$this->assertStringNotContainsString(rawurlencode($username), $accept->attr('data-url'));
		$response = $this->post_action($accept->attr('data-url'), $this->form_token($crawler));
		$this->assertSame('accepted', $response['action']);

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertStringContainsString('admin', $crawler->filter('#ze-friends')->text());
		$remove = $crawler->filter('#ze-friends a[data-ajax=true]')->first()->link()->getUri();
		$crawler = self::request('GET', $this->local_path($remove));
		$form = $crawler->selectButton($this->lang('YES'))->form();
		self::submit($form);
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertSame(0, $crawler->filter('#ze-friends .ze-list-row')->count());
		$this->logout();

		$crawler = $this->open_friends_as('admin');
		$this->assertSame(0, $crawler->filter('#ze-friends .ze-list-row')->count());
		$this->logout();
	}

	public function test_recipient_can_decline_request()
	{
		$username = 'zedecline';
		$this->create_user($username);
		$this->send_request_as_admin($username);

		$crawler = $this->open_friends_as($username);
		$decline = $crawler->filter('#ze-incoming-requests .js-ze-request')->eq(1);
		$this->assertStringContainsString('/decline', $decline->attr('data-url'));
		$response = $this->post_action($decline->attr('data-url'), $this->form_token($crawler));
		$this->assertSame('declined', $response['action']);

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertSame(0, $crawler->filter('#ze-incoming-requests')->count());
		$this->logout();
	}

	public function test_recipient_can_decline_and_block_requester()
	{
		$requester = 'zedeclineblock';
		$requester_id = $this->create_user($requester);
		$this->login($requester);
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$create = $crawler->filter('#ze-friend-controls .js-ze-request')->first();
		$response = $this->post_action($create->attr('data-url'), $this->form_token($crawler));
		$this->assertSame('created', $response['action']);
		$this->logout();

		$crawler = $this->open_friends_as('admin');
		$block = $crawler->filter('#ze-incoming-requests .js-ze-decline-block')->first();
		$this->assertStringContainsString('/decline_block', $block->attr('data-url'));
		$this->post_action($block->attr('data-url'), array(), 403);
		$response = $this->post_action($block->attr('data-url'), $this->form_token($crawler));
		$this->assertSame('blocked', $response['action']);
		$db = $this->get_db();
		$result = $db->sql_query('SELECT COUNT(*) AS total FROM phpbb_zebra
			WHERE user_id = 2 AND zebra_id = ' . (int) $requester_id . ' AND friend = 0 AND foe = 1');
		$this->assertSame(1, (int) $db->sql_fetchfield('total'));
		$db->sql_freeresult($result);
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertStringNotContainsString($requester, $crawler->filter('html')->text());
		$this->logout();

		$this->login($requester);
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$create = $crawler->filter('#ze-friend-controls .js-ze-request')->first();
		$response = $this->post_action($create->attr('data-url'), $this->form_token($crawler), 409);
		$this->assertFalse($response['success']);
		$this->logout();
	}

	public function test_staff_requester_cannot_be_blocked()
	{
		$recipient = 'zenostaffblock';
		$recipient_id = $this->create_user($recipient);
		$this->send_request_as_admin($recipient);

		$crawler = $this->open_friends_as($recipient);
		$block = $crawler->filter('#ze-incoming-requests .js-ze-decline-block')->first();
		$response = $this->post_action($block->attr('data-url'), $this->form_token($crawler), 409);
		$this->assertFalse($response['success']);
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertStringContainsString('admin', $crawler->filter('#ze-incoming-requests')->text());
		$db = $this->get_db();
		$result = $db->sql_query('SELECT COUNT(*) AS total FROM phpbb_zebra
			WHERE user_id = ' . (int) $recipient_id . ' AND zebra_id = 2 AND foe = 1');
		$this->assertSame(0, (int) $db->sql_fetchfield('total'));
		$db->sql_freeresult($result);
		$this->logout();
	}

	public function test_profile_friend_control_covers_request_states()
	{
		$username = "ze'profile";
		$message = 'We met at the phpBB meetup. <b>Hello!</b>';
		$user_id = $this->create_user($username);

		$this->login();
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u={$user_id}&sid={$this->sid}");
		$create = $crawler->filter('#ze-friend-controls .js-ze-request')->first();
		$this->assertSame(1, $crawler->filter('#ze-request-message[maxlength="255"]')->count());
		$this->assertStringContainsString("/friend/{$user_id}/request", $create->attr('data-url'));
		$this->assertStringNotContainsString(rawurlencode($username), $create->attr('data-url'));
		$this->post_action($create->attr('data-url'), array(), 403);
		$data = $this->form_token($crawler);
		$data['message'] = $message;
		$response = $this->post_action($create->attr('data-url'), $data);
		$this->assertSame('created', $response['action']);

		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u={$user_id}&sid={$this->sid}");
		$cancel = $crawler->filter('#ze-friend-controls .js-ze-request')->first();
		$this->assertStringContainsString('/cancel', $cancel->attr('data-url'));
		$this->logout();

		$this->login($username);
		$this->add_lang('memberlist');
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$this->assertStringContainsString($message, $crawler->filter('#ze-friend-controls .ze-request-message')->text());
		$this->assertStringNotContainsString('<b>', $crawler->filter('#ze-friend-controls .ze-request-message')->html());
		$block = $crawler->filter('#ze-friend-controls .js-ze-decline-block')->first();
		$this->assertStringContainsString('/decline_block', $block->attr('data-url'));
		$accept = $crawler->filter('#ze-friend-controls .js-ze-request')->first();
		$this->assertStringContainsString('/accept', $accept->attr('data-url'));
		$response = $this->post_action($accept->attr('data-url'), $this->form_token($crawler));
		$this->assertSame('accepted', $response['action']);

		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$this->assertSame(0, $crawler->filter('#ze-friend-controls')->count());
		$this->assertStringContainsString($this->lang('REMOVE_FRIEND'), $crawler->filter('.zebra')->text());
		$this->logout();
	}

	public function test_close_friend_and_profile_visibility()
	{
		$close_friend = 'zeclose';
		$registered = 'zeregistered';
		$this->create_user($close_friend);
		$this->create_user($registered);
		$this->send_request_as_admin($close_friend);

		$crawler = $this->open_friends_as($close_friend);
		$accept = $crawler->filter('#ze-incoming-requests .js-ze-request')->first();
		$this->post_action($accept->attr('data-url'), $this->form_token($crawler));
		$this->logout();

		$crawler = $this->open_friends_as('admin');
		$close = $crawler->filter('#ze-friends .js-ze-close-friend')->first();
		$response = $this->post_action($close->attr('data-url'), $this->form_token($crawler));
		$this->assertTrue($response['is_close']);

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['zebra_profile_acl'] = 4;
		self::submit($form);
		$this->logout();

		$this->login($close_friend);
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$this->assertSame(1, $crawler->filter('link[href*="zebraenhance.css"]')->count());
		$this->assertStringContainsString($close_friend, $crawler->filter('#ze-friend-list')->text());
		$this->logout();

		$this->login($registered);
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$this->assertStringContainsString($this->lang('FRIENDLIST_ERROR_ACCESS'), $crawler->filter('#ze-friend-list')->text());
		$this->logout();
	}

	public function test_custom_circle_can_be_created_assigned_renamed_and_deleted()
	{
		$username = 'zecircle';
		$this->create_user($username);
		$this->send_request_as_admin($username);

		$crawler = $this->open_friends_as($username);
		$accept = $crawler->filter('#ze-incoming-requests .js-ze-request')->first();
		$this->post_action($accept->attr('data-url'), $this->form_token($crawler));
		$this->logout();

		$crawler = $this->open_friends_as('admin');
		$create = $crawler->filter('#ze-circles .js-ze-circle')->first();
		$this->post_action($create->attr('data-url'), array(), 403);
		$data = $this->form_token($crawler);
		$data['name'] = 'Gaming';
		$response = $this->post_action($create->attr('data-url'), $data);
		$this->assertSame('Gaming', $response['circle']['name']);
		$circle_id = (int) $response['circle']['id'];

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$circle_row = $crawler->filter("#ze-circles [data-circle-id='{$circle_id}']");
		$this->assertSame(1, $circle_row->count());
		$friend_row = $crawler->filter('#ze-friends .ze-friend-row')->reduce(function ($node) use ($username)
		{
			return strpos($node->text(), $username) !== false;
		})->first();
		$save = $friend_row->filter('.js-ze-save-circles');
		$data = $this->form_token($crawler);
		$data['circle_ids'] = array($circle_id);
		$response = $this->post_action($save->attr('data-url'), $data);
		$this->assertTrue($response['success']);

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$friend_row = $crawler->filter('#ze-friends .ze-friend-row')->reduce(function ($node) use ($username)
		{
			return strpos($node->text(), $username) !== false;
		})->first();
		$this->assertSame('selected', $friend_row->filter("option[value='{$circle_id}']")->attr('selected'));

		$circle_row = $crawler->filter("#ze-circles [data-circle-id='{$circle_id}']");
		$rename = $circle_row->filter('.js-ze-circle')->first();
		$data = $this->form_token($crawler);
		$data['name'] = 'Game night';
		$this->post_action($rename->attr('data-url'), $data);
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertSame('Game night', $crawler->filter("#ze-circle-name-{$circle_id}")->attr('value'));

		$delete = $crawler->filter("#ze-circles [data-circle-id='{$circle_id}'] .js-ze-circle")->last();
		$this->post_action($delete->attr('data-url'), $this->form_token($crawler));
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertSame(0, $crawler->filter("#ze-circles [data-circle-id='{$circle_id}']")->count());
		$this->logout();
	}

	public function test_mutual_friends_follow_profile_friend_list_privacy()
	{
		$viewer = 'zemutualviewer';
		$mutual = 'zemutualfriend';
		$viewer_id = $this->create_user($viewer);
		$mutual_id = $this->create_user($mutual);
		$db = $this->get_db();
		$db->sql_multi_insert('phpbb_zebra', array(
			array('user_id' => 2, 'zebra_id' => $viewer_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $viewer_id, 'zebra_id' => 2, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => 2, 'zebra_id' => $mutual_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $mutual_id, 'zebra_id' => 2, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $viewer_id, 'zebra_id' => $mutual_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $mutual_id, 'zebra_id' => $viewer_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
		));
		$db->sql_query('UPDATE phpbb_users SET profile_friend_show = 3 WHERE user_id = 2');

		$this->login($viewer);
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$this->assertSame(1, $crawler->filter('#ze-mutual-friends')->count());
		$this->assertStringContainsString($mutual, $crawler->filter('#ze-mutual-friends')->text());

		$db->sql_query('UPDATE phpbb_users SET profile_friend_show = 5 WHERE user_id = 2');
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$this->assertSame(0, $crawler->filter('#ze-mutual-friends')->count());
		$this->assertStringContainsString($this->lang('FRIENDLIST_ERROR_ACCESS'), $crawler->filter('#ze-friend-list')->text());
		$db->sql_query('UPDATE phpbb_users SET profile_friend_show = 0 WHERE user_id = 2');
		$this->logout();
	}

	public function test_friend_suggestions_can_create_a_request_without_leaking_private_candidates()
	{
		$viewer = 'zesuggestviewer';
		$shared = 'zesuggestshared';
		$suggested = 'zesuggested';
		$private = 'zesuggestprivate';
		$viewer_id = $this->create_user($viewer);
		$shared_id = $this->create_user($shared);
		$suggested_id = $this->create_user($suggested);
		$private_id = $this->create_user($private);
		$db = $this->get_db();
		$db->sql_multi_insert('phpbb_zebra', array(
			array('user_id' => $viewer_id, 'zebra_id' => $shared_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $shared_id, 'zebra_id' => $viewer_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $shared_id, 'zebra_id' => $suggested_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $suggested_id, 'zebra_id' => $shared_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $shared_id, 'zebra_id' => $private_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $private_id, 'zebra_id' => $shared_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
		));
		$db->sql_query('UPDATE phpbb_users SET profile_friend_show = 0 WHERE user_id = ' . (int) $suggested_id);
		$db->sql_query('UPDATE phpbb_users SET profile_friend_show = 5 WHERE user_id = ' . (int) $private_id);

		$crawler = $this->open_friends_as($viewer);
		$this->assertStringContainsString($suggested, $crawler->filter('#ze-friend-suggestions')->text());
		$this->assertStringNotContainsString($private, $crawler->filter('#ze-friend-suggestions')->text());
		$this->assertStringContainsString('1 mutual friend', $crawler->filter('#ze-friend-suggestions')->text());
		$suggestion = $crawler->filter('#ze-friend-suggestions .ze-list-row')->reduce(function ($node) use ($suggested)
		{
			return strpos($node->text(), $suggested) !== false;
		})->first();
		$response = $this->post_action(
			$suggestion->filter('.js-ze-request')->attr('data-url'),
			$this->form_token($crawler)
		);
		$this->assertSame('created', $response['action']);

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertSame(0, $crawler->filter('#ze-friend-suggestions')->count());
		$this->assertStringContainsString($suggested, $crawler->filter('#ze-outgoing-requests')->text());
		$cancel = $crawler->filter('#ze-outgoing-requests .ze-list-row')->reduce(function ($node) use ($suggested)
		{
			return strpos($node->text(), $suggested) !== false;
		})->first();
		$this->post_action($cancel->filter('.js-ze-request')->attr('data-url'), $this->form_token($crawler));
		$this->logout();
	}

	public function test_selected_outgoing_requests_can_be_cancelled_in_bulk()
	{
		$first = 'zebulkfirst';
		$second = 'zebulksecond';
		$this->create_user($first);
		$this->create_user($second);
		$this->send_request_as_admin($first);
		$this->send_request_as_admin($second);

		$crawler = $this->open_friends_as('admin');
		$request_ids = array();
		foreach (array($first, $second) as $username)
		{
			$row = $crawler->filter('#ze-outgoing-requests .ze-list-row')->reduce(function ($node) use ($username)
			{
				return strpos($node->text(), $username) !== false;
			})->first();
			$request_ids[] = (int) $row->filter('.js-ze-request-select')->attr('value');
		}
		$bulk = $crawler->filter('#ze-outgoing-requests .js-ze-bulk-requests');
		$this->post_action($bulk->attr('data-url'), array(), 403);
		$empty_response = $this->post_action($bulk->attr('data-url'), $this->form_token($crawler), 400);
		$this->assertFalse($empty_response['success']);
		$data = $this->form_token($crawler);
		$data['request_ids'] = $request_ids;
		$response = $this->post_action($bulk->attr('data-url'), $data);
		$this->assertSame(2, $response['completed']);
		$this->assertSame(0, $response['skipped']);

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertStringNotContainsString($first, $crawler->filter('html')->text());
		$this->assertStringNotContainsString($second, $crawler->filter('html')->text());
		$this->logout();
	}

	public function test_friend_search_filters_the_ucp_list()
	{
		$match = 'zefindneedle';
		$other = 'zefindother';
		$match_id = $this->create_user($match);
		$other_id = $this->create_user($other);
		$db = $this->get_db();
		$db->sql_multi_insert('phpbb_zebra', array(
			array('user_id' => 2, 'zebra_id' => $match_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $match_id, 'zebra_id' => 2, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => 2, 'zebra_id' => $other_id, 'friend' => 1, 'foe' => 0, 'bff' => 0),
			array('user_id' => $other_id, 'zebra_id' => 2, 'friend' => 1, 'foe' => 0, 'bff' => 0),
		));

		$this->login();
		$this->add_lang('ucp');
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&ze_friend_q=needle&sid={$this->sid}");
		$this->assertSame('needle', $crawler->filter('#ze-friend-search')->attr('value'));
		$this->assertStringContainsString($match, $crawler->filter('#ze-friends')->text());
		$this->assertStringNotContainsString($other, $crawler->filter('#ze-friends')->text());
		$this->logout();
	}

	public function test_user_can_restrict_new_friend_requests_in_ucp()
	{
		$requester = 'zepolicy';
		$this->create_user($requester);

		$crawler = $this->open_friends_as('admin');
		$this->assertSame(1, $crawler->filter('select[name=zebra_request_policy]')->count());
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['zebra_request_policy'] = 2;
		self::submit($form);
		$this->logout();

		$this->login($requester);
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$create = $crawler->filter('#ze-friend-controls .js-ze-request')->first();
		$response = $this->post_action($create->attr('data-url'), $this->form_token($crawler), 409);
		$this->assertFalse($response['success']);
		$this->assertSame($this->lang('ZE_FRIEND_REQUEST_UNCHANGED'), $response['message']);
		$this->logout();

		$crawler = $this->open_friends_as('admin');
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['zebra_request_policy'] = 0;
		self::submit($form);
		$this->logout();
	}

	public function test_acp_request_limits_can_be_saved()
	{
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('anavaro/zebraenhance', 'info_acp_zebraenhance');
		$url = 'adm/index.php?i=%5Canavaro%5Czebraenhance%5Cacp%5Csettings_module&mode=settings&sid=' . $this->sid;
		$crawler = self::request('GET', $url);
		$this->assertSame(1, $crawler->filter('input[name=ze_max_pending_requests]')->count());
		$this->assertSame(1, $crawler->filter('input[name=ze_decline_cooldown_days]')->count());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['ze_max_pending_requests'] = 12;
		$form['ze_decline_cooldown_days'] = 3;
		$crawler = self::submit($form);
		$this->assertStringContainsString($this->lang('ACP_ZEBRA_ENHANCE_SAVED'), $crawler->filter('#main')->text());

		$crawler = self::request('GET', $url);
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['ze_max_pending_requests'] = 100;
		$form['ze_decline_cooldown_days'] = 7;
		self::submit($form);
		$this->logout();
	}

	public function test_acp_pending_request_report_is_read_only_and_escapes_messages()
	{
		$recipient = 'zeacpreport';
		$this->create_user($recipient);
		$this->send_request_as_admin($recipient);
		$db = $this->get_db();
		$message = '<script>alert("report")</script>';
		$db->sql_query("UPDATE phpbb_zebra_requests
			SET request_message = '" . $db->sql_escape($message) . "'");

		$this->login();
		$this->admin_login();
		$this->add_lang_ext('anavaro/zebraenhance', 'info_acp_zebraenhance');
		$url = 'adm/index.php?i=%5Canavaro%5Czebraenhance%5Cacp%5Creport_module&mode=report&sid=' . $this->sid;
		$crawler = self::request('GET', $url);
		$this->assertStringContainsString($this->lang('ACP_ZEBRA_ENHANCE_REPORT'), $crawler->filter('#main')->text());
		$row = $crawler->filter('table.zebra-table tbody tr')->first();
		$this->assertSame(1, $row->count());
		$this->assertStringContainsString('admin', $row->text());
		$this->assertStringContainsString($recipient, $row->text());
		$this->assertStringContainsString($message, $row->text());
		$this->assertSame(0, $row->filter('script')->count());
		$this->assertSame(0, $row->filter('input, button')->count());
		$this->logout();
	}

	public function test_email_is_an_opt_in_notification_method()
	{
		$this->login();
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "ucp.php?i=ucp_notifications&mode=notification_options&sid={$this->sid}");

		foreach (array('NOTIFICATION_TYPE_ZEBRA_ADD', 'NOTIFICATION_TYPE_ZEBRA_CONFIRM') as $language_key)
		{
			$label = $this->lang($language_key);
			$row = $crawler->filterXPath('//tr[contains(normalize-space(.), "' . $label . '")]');
			$this->assertSame(1, $row->count());
			$this->assertGreaterThanOrEqual(2, $row->filter('input:not([disabled])')->count());
			$this->assertSame(1, $row->filter('input:checked')->count());
		}

		$this->logout();
	}

	protected function send_request_as_admin($username)
	{
		$this->login();
		$this->add_lang('ucp');
		$crawler = self::request('GET', 'ucp.php?i=zebra&add=' . rawurlencode($username) . "&sid={$this->sid}");
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		$this->assertStringContainsString($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());
		$this->logout();
	}

	protected function open_friends_as($username)
	{
		$username === 'admin' ? $this->login() : $this->login($username);
		$this->add_lang('ucp');
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');

		return self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
	}

	protected function form_token($crawler)
	{
		return array(
			'creation_time' => $crawler->filter('#ze-form-token input[name=creation_time]')->attr('value'),
			'form_token'    => $crawler->filter('#ze-form-token input[name=form_token]')->attr('value'),
		);
	}

	protected function post_action($url, array $data, $expected_status = 200)
	{
		self::request('POST', $this->local_path($url), $data, false);
		$this->assertSame($expected_status, self::$client->getResponse()->getStatus());

		return json_decode(self::get_content(), true);
	}

	protected function local_path($url)
	{
		$parts = parse_url($url);
		return ltrim($parts['path'], '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
	}
}
