(function($) {
    'use strict';

    window.PN_FINANCE_MANAGER_Popups = {
      open: function(popup, options = {}) {
        var popupElement = typeof popup === 'string' ? $('#' + popup) : popup;
        
        if (!popupElement.length) {
          return;
        }
  
        if (typeof options.beforeShow === 'function') {
          options.beforeShow();
        }
  
        // Show overlay - Remove any inline styles and add active class
        $('.pn-finance-manager-popup-overlay').removeClass('pn-finance-manager-display-none-soft').addClass('pn-finance-manager-popup-overlay-active').css('display', '');
  
        // Show popup - Remove any inline styles and add active class
        popupElement.removeClass('pn-finance-manager-display-none-soft').addClass('pn-finance-manager-popup-active').css('display', '');
  
        // Focus first input if exists
        popupElement.find('input, textarea, select').first().focus();
  
        if (typeof options.afterShow === 'function') {
          options.afterShow();
        }
      },
  
      close: function() {
        // Hide all popups - Remove classes and set inline display:none
        $('.pn-finance-manager-popup').each(function() {
          $(this).removeClass('pn-finance-manager-popup-active').addClass('pn-finance-manager-display-none-soft').css('display', 'none');
        });
  
        // Hide overlay - Remove classes and set inline display:none
        $('.pn-finance-manager-popup-overlay').removeClass('pn-finance-manager-popup-overlay-active').addClass('pn-finance-manager-display-none-soft').css('display', 'none');
  
        // Call afterClose callback if exists
        $('.pn-finance-manager-popup').each(function() {
          const afterClose = $(this).data('afterClose');
          if (typeof afterClose === 'function') {
            afterClose();
            $(this).removeData('afterClose');
          }
        });

        document.body.classList.remove('pn-finance-manager-popup-open');
      }
    };
  
    // Initialize popup functionality
    $(document).ready(function() {
      // Close popup when clicking overlay
      $(document).on('click', '.pn-finance-manager-popup-overlay', function(e) {
        // Only close if the click was directly on the overlay
        if (e.target === this) {
          var activePopup = $('.pn-finance-manager-popup-active');
          if (activePopup.length && activePopup.attr('data-pn-finance-manager-no-dismiss') === 'true') {
            return;
          }
          PN_FINANCE_MANAGER_Popups.close();
        }
      });
  
      // Prevent clicks inside popup from bubbling up to the overlay
      $(document).on('click', '.pn-finance-manager-popup', function(e) {
        e.stopPropagation();
      });
  
      // Close popup when pressing ESC key
      $(document).on('keyup', function(e) {
        if (e.keyCode === 27) { // ESC key
          var activePopup = $('.pn-finance-manager-popup-active');
          if (activePopup.length && activePopup.attr('data-pn-finance-manager-no-dismiss') === 'true') {
            return;
          }
          PN_FINANCE_MANAGER_Popups.close();
        }
      });
  
      // Close popup when clicking close button
      $(document).on('click', '.pn-finance-manager-popup-close, .pn-finance-manager-popup-close-fixed', function(e) {
        e.preventDefault();
        PN_FINANCE_MANAGER_Popups.close();
      });
    });
  })(jQuery); 