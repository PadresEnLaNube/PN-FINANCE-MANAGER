(function($) {
	'use strict';

  $(document).ready(function() {
    if($('.pn-finance-manager-tooltip').length && $.fn.tooltipster) {
      $('.pn-finance-manager-tooltip').tooltipster({maxWidth: 300, delayTouch:[0, 4000], customClass: 'pn-finance-manager-tooltip'});
    }

    if ($('.pn-finance-manager-select').length && $.fn.PN_FINANCE_MANAGER_Selector) {
      $('.pn-finance-manager-select').each(function(index) {
        if ($(this).attr('multiple') == 'true') {
          // For a multiple select
          $(this).PN_FINANCE_MANAGER_Selector({
            multiple: true,
            searchable: true,
            placeholder: typeof pn_finance_manager_i18n !== 'undefined' ? pn_finance_manager_i18n.select_options : '',
          });
        } else {
          // For a single select
          $(this).PN_FINANCE_MANAGER_Selector();
        }
      });
    }

    if ($.trumbowyg && typeof pn_finance_manager_trumbowyg !== 'undefined' && $('.pn-finance-manager-wysiwyg').length) {
      $.trumbowyg.svgPath = pn_finance_manager_trumbowyg.path;
      $('.pn-finance-manager-wysiwyg').each(function(index, element) {
        $(this).trumbowyg();
      });
    }
  });
})(jQuery);
