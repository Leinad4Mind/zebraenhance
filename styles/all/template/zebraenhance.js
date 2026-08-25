/**
 * @copyright (c) 2013-2026 Stanislav Atanasov
 * @copyright (c) 2026 Leinad4Mind
 * @license GNU General Public License, version 2 (GPL-2.0-only)
 */

(function ($) {
	'use strict';
	var $controls = $('#ze-friend-controls, #ze-foe-controls').first();

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

	function postAndReload($button, payload) {
		$button.prop('disabled', true);
		$.ajax({
			url: $button.attr('data-url'),
			method: 'POST',
			data: $.extend(tokenPayload(), payload || {}),
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
	}

	$(document).on('click', '.js-ze-circle', function () {
		var $button = $(this);
		var submit = function () {
			var input = $button.attr('data-name-input');
			postAndReload($button, input ? {name: $(input).val()} : {});
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

	$(document).on('click', '.js-ze-save-circles', function () {
		var $button = $(this);
		var circleIds = $($button.attr('data-select')).val() || [];
		postAndReload($button, {circle_ids: circleIds});
	});

	$(document).on('change', '.js-ze-check-all', function () {
		$($(this).attr('data-target')).prop('checked', this.checked);
	});

	$(document).on('click', '.js-ze-bulk-requests', function () {
		var $button = $(this);
		var requestIds = $($button.attr('data-target') + ':checked').map(function () {
			return $(this).val();
		}).get();
		var submit = function () {
			postAndReload($button, {request_ids: requestIds});
		};
		var confirmation = $button.attr('data-confirm');

		if (!requestIds.length) {
			showError({message: $controls.attr('data-select-request')});
			return;
		}
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

	$(document).on('click', '.js-ze-search-friends', function (event) {
		event.preventDefault();
		var $button = $(this);
		var value = $($button.attr('data-input')).val();
		var separator = $button.attr('data-url').indexOf('?') === -1 ? '?' : '&';
		window.location.href = $button.attr('data-url') + separator + 'ze_friend_q=' + encodeURIComponent(value);
	});

	$(document).on('keydown', '#ze-friend-search', function (event) {
		if (event.key === 'Enter') {
			event.preventDefault();
			$('.js-ze-search-friends').trigger('click');
		}
	});

	$(document).on('click', '.js-ze-search-foes', function (event) {
		event.preventDefault();
		var $button = $(this);
		var value = $('#ze-foe-search').val();
		var separator = $button.attr('data-url').indexOf('?') === -1 ? '?' : '&';
		window.location.href = $button.attr('data-url') + separator + 'ze_foe_q=' + encodeURIComponent(value);
	});

	$(document).on('keydown', '#ze-foe-search', function (event) {
		if (event.key === 'Enter') {
			event.preventDefault();
			$('.js-ze-search-foes').trigger('click');
		}
	});

	$(document).on('click', '.js-ze-save-foe', function () {
		var $button = $(this);
		var $row = $button.closest('.ze-foe-row');
		$button.attr('data-url', $row.attr('data-save-url'));
		postAndReload($button, {
			note: $row.find('.js-ze-foe-note').val(),
			duration: $row.find('.js-ze-foe-duration').val(),
			pm_policy: $row.find('.js-ze-foe-pm-policy').val(),
			content_policy: $row.find('.js-ze-foe-content-policy').val(),
			notification_policy: $row.find('.js-ze-foe-notification-policy').val()
		});
	});

	$(document).on('click', '.js-ze-remove-foes', function () {
		var $button = $(this);
		var foeIds = $('.js-ze-foe-select:checked').map(function () {
			return $(this).val();
		}).get();
		if (!foeIds.length) {
			showError({message: $controls.attr('data-select-foe')});
			return;
		}
		phpbb.confirm($button.attr('data-confirm'), function (confirmed) {
			if (confirmed) {
				postAndReload($button, {foe_ids: foeIds});
			}
		});
	});

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
