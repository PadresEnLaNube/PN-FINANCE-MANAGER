(function($) {
	'use strict';

	function pn_personal_finance_manager_timer(step) {
		var step_timer = $('.pn-personal-finance-manager-player-step[data-pn-personal-finance-manager-step="' + step + '"] .pn-personal-finance-manager-player-timer');
		var step_icon = $('.pn-personal-finance-manager-player-step[data-pn-personal-finance-manager-step="' + step + '"] .pn-personal-finance-manager-player-timer-icon');
		
		if (!step_timer.hasClass('timing')) {
			step_timer.addClass('timing');

      setInterval(function() {
      	step_icon.fadeOut('fast').fadeIn('slow').fadeOut('fast').fadeIn('slow');
      }, 5000);

      setInterval(function() {
      	step_timer.text(Math.max(0, parseInt(step_timer.text()) - 1)).fadeOut('fast').fadeIn('slow').fadeOut('fast').fadeIn('slow');
      }, 60000);
		}
	}

	$(document).on('click', '.pn-personal-finance-manager-popup-player-btn', function(e){
  	pn_personal_finance_manager_timer(1);
	});

  $(document).on('click', '.pn-personal-finance-manager-steps-prev', function(e){
    e.preventDefault();

    var steps_count = $('#pn-personal-finance-manager-recipe-wrapper').attr('data-pn-personal-finance-manager-steps-count');
    var current_step = $('#pn-personal-finance-manager-popup-steps').attr('data-pn-personal-finance-manager-current-step');
    var next_step = Math.max(0, (parseInt(current_step) - 1));
    
    $('.pn-personal-finance-manager-player-step').addClass('pn-personal-finance-manager-display-none-soft');
    $('#pn-personal-finance-manager-popup-steps').attr('data-pn-personal-finance-manager-current-step', next_step);
    $('.pn-personal-finance-manager-player-step[data-pn-personal-finance-manager-step=' + next_step + ']').removeClass('pn-personal-finance-manager-display-none-soft');

    if (current_step <= steps_count) {
    	$('.pn-personal-finance-manager-steps-next').removeClass('pn-personal-finance-manager-display-none');
    }

    if (current_step <= 2) {
    	$(this).addClass('pn-personal-finance-manager-display-none');
    }

    pn_personal_finance_manager_timer(next_step);
	});

	$(document).on('click', '.pn-personal-finance-manager-steps-next', function(e){
    e.preventDefault();

    var steps_count = $('#pn-personal-finance-manager-recipe-wrapper').attr('data-pn-personal-finance-manager-steps-count');
    var current_step = $('#pn-personal-finance-manager-popup-steps').attr('data-pn-personal-finance-manager-current-step');
    var next_step = Math.min(steps_count, (parseInt(current_step) + 1));

    $('.pn-personal-finance-manager-player-step').addClass('pn-personal-finance-manager-display-none-soft');
    $('#pn-personal-finance-manager-popup-steps').attr('data-pn-personal-finance-manager-current-step', next_step);
    $('.pn-personal-finance-manager-player-step[data-pn-personal-finance-manager-step=' + next_step + ']').removeClass('pn-personal-finance-manager-display-none-soft');

    if (current_step >= 1) {
    	$('.pn-personal-finance-manager-steps-prev').removeClass('pn-personal-finance-manager-display-none');
    }

    if (current_step >= (steps_count - 1)) {
    	$(this).addClass('pn-personal-finance-manager-display-none');
    }

    pn_personal_finance_manager_timer(next_step);
	});

})(jQuery);
