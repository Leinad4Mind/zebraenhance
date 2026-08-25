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

	public function test_acp_controls_foe_options_exposed_in_ucp()
	{
		$db = $this->get_db();
		$db->sql_query("UPDATE phpbb_config SET config_value = '0', is_dynamic = 1
			WHERE config_name = 'ze_foes_enhancement'");
		$crawler = $this->open_friends_as('admin');
		$this->assertSame(0, $crawler->filter('input[name=zebra_block_foe_pm]')->count());
		$this->assertSame(0, $crawler->filter('input[name=zebra_hide_foe_content]')->count());
		$this->assertSame(0, $crawler->filter('input[name=zebra_mute_foe_notifications]')->count());
		$this->assertStringNotContainsString($this->lang('ZE_UCP_FOE_MANAGER'), $crawler->filter('html')->text());
		$this->logout();

		$this->enable_foe_enhancements();
		$db->sql_query("UPDATE phpbb_config SET config_value = '0', is_dynamic = 1
			WHERE config_name = 'ze_foe_content'");
		$crawler = $this->open_friends_as('admin');
		$this->assertSame(2, $crawler->filter('input[name=zebra_block_foe_pm]')->count());
		$this->assertSame(0, $crawler->filter('input[name=zebra_hide_foe_content]')->count());
		$this->assertSame(2, $crawler->filter('input[name=zebra_mute_foe_notifications]')->count());
		$this->assertStringContainsString($this->lang('ZE_UCP_FOE_MANAGER'), $crawler->filter('html')->text());
		$this->logout();
	}

	public function test_user_can_toggle_foe_privacy_settings_in_ucp()
	{
		$this->enable_foe_enhancements();
		$db = $this->get_db();
		$db->sql_query('UPDATE phpbb_users
			SET zebra_block_foe_pm = 0, zebra_hide_foe_content = 0, zebra_mute_foe_notifications = 0
			WHERE user_id = 2');
		$crawler = $this->open_friends_as('admin');
		$this->assertSame(2, $crawler->filter('input[name=zebra_block_foe_pm]')->count());
		$this->assertSame('0', $crawler->filter('input[name=zebra_block_foe_pm]:checked')->attr('value'));
		$this->assertSame(2, $crawler->filter('input[name=zebra_hide_foe_content]')->count());
		$this->assertSame('0', $crawler->filter('input[name=zebra_hide_foe_content]:checked')->attr('value'));
		$this->assertSame(2, $crawler->filter('input[name=zebra_mute_foe_notifications]')->count());
		$this->assertSame('0', $crawler->filter('input[name=zebra_mute_foe_notifications]:checked')->attr('value'));

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['zebra_block_foe_pm'] = 1;
		$form['zebra_hide_foe_content'] = 1;
		$form['zebra_mute_foe_notifications'] = 1;
		self::submit($form);

		$result = $db->sql_query('SELECT zebra_block_foe_pm, zebra_hide_foe_content, zebra_mute_foe_notifications
			FROM phpbb_users WHERE user_id = 2');
		$row = $db->sql_fetchrow($result);
		$this->assertSame(1, (int) $row['zebra_block_foe_pm']);
		$this->assertSame(1, (int) $row['zebra_hide_foe_content']);
		$this->assertSame(1, (int) $row['zebra_mute_foe_notifications']);
		$db->sql_freeresult($result);

		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertSame('1', $crawler->filter('input[name=zebra_block_foe_pm]:checked')->attr('value'));
		$this->assertSame('1', $crawler->filter('input[name=zebra_hide_foe_content]:checked')->attr('value'));
		$this->assertSame('1', $crawler->filter('input[name=zebra_mute_foe_notifications]:checked')->attr('value'));
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['zebra_block_foe_pm'] = 0;
		$form['zebra_hide_foe_content'] = 0;
		$form['zebra_mute_foe_notifications'] = 0;
		self::submit($form);
		$this->logout();
	}

	public function test_enhanced_foe_manager_saves_exceptions_and_bulk_removes()
	{
		$this->enable_foe_enhancements();
		$username = 'zemanagedfoe';
		$foe_id = $this->create_user($username);
		$db = $this->get_db();
		$db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (2, ' . (int) $foe_id . ', 0, 1, 0)');
		$db->sql_query('INSERT INTO phpbb_zebra_foe_settings
			(owner_id, foe_id, added_at, expires_at, foe_note, pm_policy, content_policy, notification_policy)
			VALUES (2, ' . (int) $foe_id . ", 1234, 0, '', 0, 0, 0)");

		$this->login();
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$result = $db->sql_query("SELECT module_id, parent_id FROM phpbb_modules
			WHERE module_basename = '\\anavaro\\zebraenhance\\ucp\\foes_module'
				AND module_mode = 'manage'");
		$module = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		$module_id = (int) $module['module_id'];
		$this->assertGreaterThan(0, $module_id);
		$navigation = self::request('GET', 'ucp.php?i=' . (int) $module['parent_id'] . '&sid=' . $this->sid);
		$manager_link = $navigation->selectLink($this->lang('ZE_UCP_FOE_MANAGER'))->link()->getUri();
		$url = $this->local_path($manager_link);
		$url .= (strpos($url, '?') === false ? '?' : '&') . 'ze_foe_q=' . rawurlencode($username);
		$crawler = self::request('GET', $url);
		$this->assertStringContainsString($this->lang('ZE_UCP_FOE_MANAGER'), $crawler->filter('html')->text());
		$this->assertSame(1, $crawler->filter('.ze-foe-row')->count());
		$this->assertStringContainsString($username, $crawler->filter('.ze-foe-heading')->text());

		$save_url = $crawler->filter('.ze-foe-row')->attr('data-save-url');
		$response = $this->post_action($save_url, array_merge($this->form_token($crawler), array(
			'note'                => 'Private context',
			'duration'            => 86400,
			'pm_policy'           => 1,
			'content_policy'      => 2,
			'notification_policy' => 2,
		)));
		$this->assertTrue($response['success']);

		$result = $db->sql_query('SELECT expires_at, foe_note, pm_policy, content_policy, notification_policy
			FROM phpbb_zebra_foe_settings
			WHERE owner_id = 2 AND foe_id = ' . (int) $foe_id);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		$this->assertGreaterThan(time(), (int) $row['expires_at']);
		$this->assertSame('Private context', $row['foe_note']);
		$this->assertSame(1, (int) $row['pm_policy']);
		$this->assertSame(2, (int) $row['content_policy']);
		$this->assertSame(2, (int) $row['notification_policy']);

		$crawler = self::request('GET', $url);
		$remove_url = $crawler->filter('.js-ze-remove-foes')->attr('data-url');
		$response = $this->post_action($remove_url, array_merge($this->form_token($crawler), array(
			'foe_ids' => array($foe_id),
		)));
		$this->assertTrue($response['success']);
		$this->assertSame(1, (int) $response['removed']);

		$result = $db->sql_query('SELECT COUNT(*) AS total FROM phpbb_zebra
			WHERE user_id = 2 AND zebra_id = ' . (int) $foe_id . ' AND foe = 1');
		$this->assertSame(0, (int) $db->sql_fetchfield('total'));
		$db->sql_freeresult($result);
		$this->logout();
	}

	public function test_opted_in_user_rejects_private_messages_from_foes()
	{
		$this->enable_foe_enhancements();
		$sender = 'zepmsender';
		$recipient = 'zepmrecipient';
		$sender_id = $this->create_user($sender);
		$recipient_id = $this->create_user($recipient);
		$db = $this->get_db();
		$db->sql_query('UPDATE phpbb_users
			SET zebra_block_foe_pm = 1
			WHERE user_id = ' . (int) $recipient_id);
		$db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (' . (int) $recipient_id . ', ' . (int) $sender_id . ', 0, 1, 0)');

		$this->login($sender);
		$this->add_lang('ucp');
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "ucp.php?i=pm&mode=compose&u={$recipient_id}&sid={$this->sid}");

		$this->assertStringContainsString(
			$this->lang('ZE_PM_RECIPIENTS_BLOCKED'),
			$crawler->filter('html')->text()
		);
		$this->assertSame(0, $crawler->filter('input[name^="address_list[u]"]')->count());
		$this->logout();
	}

	public function test_opted_in_user_does_not_see_foe_posts_or_quotes()
	{
		$this->enable_foe_enhancements();
		$foe = 'zehiddenfoe';
		$viewer = 'zecontentviewer';
		$foe_id = $this->create_user($foe);
		$viewer_id = $this->create_user($viewer);

		$this->login($foe);
		$topic = $this->create_topic(2, 'ZE foe content topic', 'ze-foe-original-body');
		$this->logout();

		$this->login();
		$quote = '[quote=' . $foe . ' post_id=' . (int) $topic['post_id'] . ' user_id=' . (int) $foe_id . ']'
			. 'ze-foe-quoted-body[/quote] ze-visible-reply-body';
		$this->create_post(2, $topic['topic_id'], 'Re: ZE foe content topic', $quote);
		$this->logout();

		$db = $this->get_db();
		$db->sql_query('UPDATE phpbb_users
			SET zebra_hide_foe_content = 1
			WHERE user_id = ' . (int) $viewer_id);
		$db->sql_query('INSERT INTO phpbb_zebra
			(user_id, zebra_id, friend, foe, bff)
			VALUES (' . (int) $viewer_id . ', ' . (int) $foe_id . ', 0, 1, 0)');

		$this->login($viewer);
		$crawler = self::request('GET', 'viewtopic.php?t=' . (int) $topic['topic_id'] . "&sid={$this->sid}");
		$page_text = $crawler->filter('html')->text();
		$this->assertStringNotContainsString('ze-foe-original-body', $page_text);
		$this->assertStringNotContainsString('ze-foe-quoted-body', $page_text);
		$this->assertStringContainsString('ze-visible-reply-body', $page_text);
		$this->assertSame(0, $crawler->filter('#p' . (int) $topic['post_id'])->count());

		$crawler = self::request('GET', 'search.php?author_id=' . (int) $foe_id . '&sr=posts&sid=' . $this->sid);
		$this->assertSame(0, $crawler->filter('div.search.post')->count());
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
		$this->assertSame(2, $crawler->filter('input[name=ze_foes_enhancement]')->count());
		$this->assertSame(1, $crawler->filter('#ze-foe-feature-options')->count());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['ze_max_pending_requests'] = 12;
		$form['ze_decline_cooldown_days'] = 3;
		$form['ze_foes_enhancement'] = 1;
		$form['ze_foe_pm'] = 1;
		$form['ze_foe_content'] = 0;
		$form['ze_foe_notifications'] = 1;
		$form['ze_foe_temporary'] = 0;
		$form['ze_foe_notes'] = 1;
		$form['ze_foe_exceptions'] = 0;
		$crawler = self::submit($form);
		$this->assertStringContainsString($this->lang('ACP_ZEBRA_ENHANCE_SAVED'), $crawler->filter('#main')->text());

		$crawler = self::request('GET', $url);
		$this->assertSame('1', $crawler->filter('input[name=ze_foes_enhancement]:checked')->attr('value'));
		$this->assertSame('0', $crawler->filter('input[name=ze_foe_content]:checked')->attr('value'));
		$this->assertSame('0', $crawler->filter('input[name=ze_foe_temporary]:checked')->attr('value'));
		$this->assertSame('0', $crawler->filter('input[name=ze_foe_exceptions]:checked')->attr('value'));
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['ze_max_pending_requests'] = 100;
		$form['ze_decline_cooldown_days'] = 7;
		$form['ze_foes_enhancement'] = 0;
		$form['ze_foe_pm'] = 1;
		$form['ze_foe_content'] = 1;
		$form['ze_foe_notifications'] = 1;
		$form['ze_foe_temporary'] = 1;
		$form['ze_foe_notes'] = 1;
		$form['ze_foe_exceptions'] = 1;
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

	protected function enable_foe_enhancements()
	{
		$db = $this->get_db();
		$db->sql_query("UPDATE phpbb_config
			SET config_value = '1', is_dynamic = 1
			WHERE " . $db->sql_in_set('config_name', array(
				'ze_foes_enhancement',
				'ze_foe_pm',
				'ze_foe_content',
				'ze_foe_notifications',
				'ze_foe_temporary',
				'ze_foe_notes',
				'ze_foe_exceptions',
			)));
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
