/**
 * @copyright (c) 2026 Leinad4Mind
 * @license GNU General Public License, version 2 (GPL-2.0-only)
 */

(function ($) {
	'use strict';

	$(document).on('click', '.js-zebraenhance-foe-topic-toggle', function () {
		var $button = $(this);
		var $notice = $button.closest('.zebraenhance-foe-topic-notice');
		var open = !$notice.hasClass('is-open');

		$notice.toggleClass('is-open', open);
		$button.attr('aria-expanded', open ? 'true' : 'false');
		$button.text($button.data(open ? 'hide-label' : 'show-label'));
	});
})(jQuery);
