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
		$this->assertStringContainsString($close_friend, $crawler->filter('#ze-friend-list')->text());
		$this->logout();

		$this->login($registered);
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "memberlist.php?mode=viewprofile&u=2&sid={$this->sid}");
		$this->assertStringContainsString($this->lang('FRIENDLIST_ERROR_ACCESS'), $crawler->filter('#ze-friend-list')->text());
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
