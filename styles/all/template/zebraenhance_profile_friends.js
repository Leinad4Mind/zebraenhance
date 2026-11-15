/**
 * @copyright (c) 2026 Leinad4Mind
 * @license GNU General Public License, version 2 (GPL-2.0-only)
 */

(function ($) {
	'use strict';

	$(document).on('click', '.js-zebraenhance-more-friends', function (event) {
		event.preventDefault();
		var $button = $(this);
		var $more = $button.closest('.zebraenhance-friends-more');
		var showError = function (response) {
			phpbb.alert($more.attr('data-error-title'), response.message || $more.attr('data-request-failed'));
		};

		if ($button.attr('aria-disabled') === 'true') {
			return;
		}
		$button.attr('aria-disabled', 'true').addClass('disabled');

		$.ajax({
			url: this.href.split('#')[0],
			method: 'GET',
			dataType: 'json'
		}).done(function (response) {
			if (!response.success) {
				showError(response);
				return;
			}
			$('#zebraenhance-friend-list .js-zebraenhance-friends').append(response.html);
			$more.find('.js-zebraenhance-friends-shown').text(response.shown);
			if (response.next_start) {
				$button.attr('href', $button.attr('href').replace(
					/([?&]zebraenhance_friends_start=)\d+/,
					'$1' + response.next_start
				));
			} else {
				$button.remove();
			}
		}).fail(function (xhr) {
			showError(xhr.responseJSON || {});
		}).always(function () {
			$button.attr('aria-disabled', 'false').removeClass('disabled');
		});
	});
}(jQuery));
