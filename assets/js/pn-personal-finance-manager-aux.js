(function($) {
	'use strict';

  $(document).ready(function() {
    if (window.PN_PERSONAL_FINANCE_MANAGER_Tooltips) {
      PN_PERSONAL_FINANCE_MANAGER_Tooltips.init();
    }

    if ($('.pn-personal-finance-manager-select').length && $.fn.PN_PERSONAL_FINANCE_MANAGER_Selector) {
      $('.pn-personal-finance-manager-select').each(function(index) {
        if ($(this).attr('multiple') == 'true') {
          // For a multiple select
          $(this).PN_PERSONAL_FINANCE_MANAGER_Selector({
            multiple: true,
            searchable: true,
            placeholder: typeof pn_personal_finance_manager_i18n !== 'undefined' ? pn_personal_finance_manager_i18n.select_options : '',
          });
        } else {
          // For a single select
          $(this).PN_PERSONAL_FINANCE_MANAGER_Selector();
        }
      });
    }

    if ($.trumbowyg && typeof pn_personal_finance_manager_trumbowyg !== 'undefined' && $('.pn-personal-finance-manager-wysiwyg').length) {
      $.trumbowyg.svgPath = pn_personal_finance_manager_trumbowyg.path;
      $('.pn-personal-finance-manager-wysiwyg').each(function(index, element) {
        $(this).trumbowyg();
      });
    }
  });
})(jQuery);
