(function ($) {
	'use strict';

	function toggleFoeOptions() {
		$('#zebraenhance-foe-feature-options').toggleClass('hidden', $('.js-zebraenhance-foes-master:checked').val() !== '1');
	}

	$(toggleFoeOptions);
	$(document).on('change', '.js-zebraenhance-foes-master', toggleFoeOptions);
}(jQuery));
