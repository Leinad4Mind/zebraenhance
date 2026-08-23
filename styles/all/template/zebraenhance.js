/**
 * @copyright (c) 2013-2026 Stanislav Atanasov
 * @copyright (c) 2026 Leinad4Mind
 * @license GNU General Public License, version 2 (GPL-2.0-only)
 */

(function ($) {
	'use strict';

	$(document).on('click', '.js-ze-close-friend', function () {
		var $button = $(this);
		var $form = $button.closest('form');
		var payload = {
			creation_time: $form.find('input[name=creation_time]').val(),
			form_token: $form.find('input[name=form_token]').val()
		};

		$button.prop('disabled', true);
		$.ajax({
			url: $button.attr('data-url'),
			method: 'POST',
			data: payload,
			dataType: 'json'
		}).done(function (response) {
			if (!response.success) {
				phpbb.alert('', response.message || 'Request failed.');
				return;
			}

			$button
				.attr('aria-pressed', response.is_close ? 'true' : 'false')
				.attr('title', response.label)
				.attr('aria-label', response.label)
				.attr('data-url', $button.attr(response.is_close ? 'data-remove-url' : 'data-add-url'));
			$button.find('.icon')
				.toggleClass('fa-star', response.is_close)
				.toggleClass('fa-star-o', !response.is_close);
		}).fail(function (xhr) {
			var response = xhr.responseJSON || {};
			phpbb.alert('', response.message || 'Request failed.');
		}).always(function () {
			$button.prop('disabled', false);
		});
	});
}(jQuery));
