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
