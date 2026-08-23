/**
 * @copyright (c) 2013-2026 Stanislav Atanasov
 * @copyright (c) 2026 Leinad4Mind
 * @license GNU General Public License, version 2 (GPL-2.0-only)
 */

(function ($) {
	'use strict';
	var $controls = $('#ze-friend-controls');

	function tokenPayload() {
		var $token = $('#ze-form-token');

		return {
			creation_time: $token.find('input[name=creation_time]').val(),
			form_token: $token.find('input[name=form_token]').val()
		};
	}

	function showError(response) {
		phpbb.alert(
			$controls.attr('data-error-title'),
			response.message || $controls.attr('data-request-failed')
		);
	}

	$(document).on('click', '.js-ze-close-friend', function () {
		var $button = $(this);

		$button.prop('disabled', true);
		$.ajax({
			url: $button.attr('data-url'),
			method: 'POST',
			data: tokenPayload(),
			dataType: 'json'
		}).done(function (response) {
			if (!response.success) {
				showError(response);
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
			showError(xhr.responseJSON || {});
		}).always(function () {
			$button.prop('disabled', false);
		});
	});

	$(document).on('click', '.js-ze-request', function () {
		var $button = $(this);
		var submit = function () {
			var payload = tokenPayload();
			var messageInput = $button.attr('data-message-input');
			if (messageInput) {
				payload.message = $(messageInput).val();
			}
			$button.prop('disabled', true);
			$.ajax({
				url: $button.attr('data-url'),
				method: 'POST',
				data: payload,
				dataType: 'json'
			}).done(function (response) {
				if (response.success) {
					window.location.reload();
					return;
				}
				showError(response);
			}).fail(function (xhr) {
				showError(xhr.responseJSON || {});
			}).always(function () {
				$button.prop('disabled', false);
			});
		};
		var confirmation = $button.attr('data-confirm');

		if (confirmation) {
			phpbb.confirm(confirmation, function (confirmed) {
				if (confirmed) {
					submit();
				}
			});
			return;
		}

		submit();
	});
}(jQuery));
