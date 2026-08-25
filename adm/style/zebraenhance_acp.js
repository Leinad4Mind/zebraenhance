(function ($) {
	'use strict';

	function toggleFoeOptions() {
		$('#ze-foe-feature-options').toggleClass('hidden', $('.js-ze-foes-master:checked').val() !== '1');
	}

	$(toggleFoeOptions);
	$(document).on('change', '.js-ze-foes-master', toggleFoeOptions);
}(jQuery));
