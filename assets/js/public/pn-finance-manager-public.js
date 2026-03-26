(function($) {
	'use strict';

	function pn_finance_manager_timer(step) {
		var step_timer = $('.pn-finance-manager-player-step[data-pn-finance-manager-step="' + step + '"] .pn-finance-manager-player-timer');
		var step_icon = $('.pn-finance-manager-player-step[data-pn-finance-manager-step="' + step + '"] .pn-finance-manager-player-timer-icon');
		
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

	$(document).on('click', '.pn-finance-manager-popup-player-btn', function(e){
  	pn_finance_manager_timer(1);
	});

  $(document).on('click', '.pn-finance-manager-steps-prev', function(e){
    e.preventDefault();

    var steps_count = $('#pn-finance-manager-recipe-wrapper').attr('data-pn-finance-manager-steps-count');
    var current_step = $('#pn-finance-manager-popup-steps').attr('data-pn-finance-manager-current-step');
    var next_step = Math.max(0, (parseInt(current_step) - 1));
    
    $('.pn-finance-manager-player-step').addClass('pn-finance-manager-display-none-soft');
    $('#pn-finance-manager-popup-steps').attr('data-pn-finance-manager-current-step', next_step);
    $('.pn-finance-manager-player-step[data-pn-finance-manager-step=' + next_step + ']').removeClass('pn-finance-manager-display-none-soft');

    if (current_step <= steps_count) {
    	$('.pn-finance-manager-steps-next').removeClass('pn-finance-manager-display-none');
    }

    if (current_step <= 2) {
    	$(this).addClass('pn-finance-manager-display-none');
    }

    pn_finance_manager_timer(next_step);
	});

	$(document).on('click', '.pn-finance-manager-steps-next', function(e){
    e.preventDefault();

    var steps_count = $('#pn-finance-manager-recipe-wrapper').attr('data-pn-finance-manager-steps-count');
    var current_step = $('#pn-finance-manager-popup-steps').attr('data-pn-finance-manager-current-step');
    var next_step = Math.min(steps_count, (parseInt(current_step) + 1));

    $('.pn-finance-manager-player-step').addClass('pn-finance-manager-display-none-soft');
    $('#pn-finance-manager-popup-steps').attr('data-pn-finance-manager-current-step', next_step);
    $('.pn-finance-manager-player-step[data-pn-finance-manager-step=' + next_step + ']').removeClass('pn-finance-manager-display-none-soft');

    if (current_step >= 1) {
    	$('.pn-finance-manager-steps-prev').removeClass('pn-finance-manager-display-none');
    }

    if (current_step >= (steps_count - 1)) {
    	$(this).addClass('pn-finance-manager-display-none');
    }

    pn_finance_manager_timer(next_step);
	});

	$('.pn-finance-manager-carousel-main-images .owl-carousel').owlCarousel({
    margin: 10,
    center: true,
    nav: false, 
    autoplay: true, 
    autoplayTimeout: 5000, 
    autoplaySpeed: 2000, 
    pagination: true, 
    responsive:{
      0:{
        items: 2,
      },
      600:{
        items: 3,
      },
      1000:{
        items: 4,
      }
    }, 
  });
})(jQuery);
