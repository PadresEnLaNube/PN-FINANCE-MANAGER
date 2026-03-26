(function($) {
  'use strict';

  $(document).ready(function() {
    pn_finance_manager_watchlist_init();
  });

  function pn_finance_manager_watchlist_init() {
    // Initialize range displays on page load
    pn_finance_manager_watchlist_init_range_displays();

    // Load symbols when type is selected (visibility handled by Forms parent system)
    $(document).on('change', '#pn_finance_manager_watchlist_type', function() {
      var type = $(this).val();
      if (type === 'stock') {
        pn_finance_manager_watchlist_load_stock_symbols();
      } else if (type === 'crypto') {
        pn_finance_manager_watchlist_load_crypto_symbols();
      }
    });

    // Threshold slider value display (add form)
    $(document).on('input', '#pn_finance_manager_watchlist_threshold', function() {
      $(this).siblings('.pn-finance-manager-input-range-output').text($(this).val() + '%');
    });

    // Edit button toggle alert controls
    $(document).on('click', '.pn-finance-manager-watchlist-edit-btn', function() {
      var $item = $(this).closest('.pn-finance-manager-watchlist-item');
      $(this).toggleClass('active');
      $item.find('.pn-finance-manager-watchlist-alert-controls').toggleClass('pn-finance-manager-display-none-soft');
    });

    // Alert checkbox toggles threshold visibility (list items)
    $(document).on('change', '.pn-finance-manager-watchlist-alert-toggle', function() {
      var $item = $(this).closest('.pn-finance-manager-watchlist-item');
      var $threshold = $item.find('.pn-finance-manager-watchlist-threshold-field-item');
      if ($(this).is(':checked')) {
        $threshold.removeClass('pn-finance-manager-display-none-soft');
      } else {
        $threshold.addClass('pn-finance-manager-display-none-soft');
      }
    });

    // Alert checkbox toggles threshold visibility (add form)
    $(document).on('change', '#pn_finance_manager_watchlist_alert_enabled', function() {
      var $threshold = $('.pn-finance-manager-watchlist-threshold-field');
      if ($(this).is(':checked')) {
        $threshold.removeClass('pn-finance-manager-display-none-soft');
      } else {
        $threshold.addClass('pn-finance-manager-display-none-soft');
      }
    });

    // Add to watchlist
    $(document).on('click', '.pn-finance-manager-watchlist-add-btn', function() {
      var $btn = $(this);
      var type = $('#pn_finance_manager_watchlist_type').val();
      var symbol = '';

      if (type === 'stock') {
        symbol = $('#pn_finance_manager_watchlist_stock_symbol').val();
      } else if (type === 'crypto') {
        symbol = $('#pn_finance_manager_watchlist_crypto_symbol').val();
      }

      if (!type || !symbol) {
        return;
      }

      var alertEnabled = $('#pn_finance_manager_watchlist_alert_enabled').is(':checked') ? 1 : 0;
      var alertThreshold = $('#pn_finance_manager_watchlist_threshold').val();

      $btn.prop('disabled', true);

      $.ajax({
        url: pn_finance_manager_ajax.ajax_url,
        type: 'POST',
        data: {
          action: 'pn_finance_manager_ajax',
          pn_finance_manager_ajax_type: 'pn_finance_manager_watchlist_add',
          pn_finance_manager_ajax_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce,
          watchlist_type: type,
          watchlist_symbol: symbol,
          watchlist_alert_enabled: alertEnabled,
          watchlist_alert_threshold: alertThreshold
        },
        success: function(response) {
          if (typeof response === 'string') {
            try { response = JSON.parse(response); } catch (e) { return; }
          }
          if (response.success && response.html) {
            $('.pn-finance-manager-watchlist-items').html(response.html);
            pn_finance_manager_watchlist_init_range_displays();
            // Reset form
            $('#pn_finance_manager_watchlist_type').val('').trigger('change');
            $('#pn_finance_manager_watchlist_alert_enabled').prop('checked', false);
            $('.pn-finance-manager-watchlist-threshold-field').addClass('pn-finance-manager-display-none-soft');
            $('#pn_finance_manager_watchlist_threshold').val(5);
            $('.pn-finance-manager-watchlist-add-form .pn-finance-manager-input-range-output').text('5%');
          }
          $btn.prop('disabled', false);
        },
        error: function() {
          $btn.prop('disabled', false);
        }
      });
    });

    // Remove from watchlist
    $(document).on('click', '.pn-finance-manager-watchlist-remove-btn', function() {
      if (!confirm(pn_finance_manager_i18n.confirm_remove_watchlist)) {
        return;
      }

      var itemId = $(this).data('pn-finance-manager-item-id');

      $.ajax({
        url: pn_finance_manager_ajax.ajax_url,
        type: 'POST',
        data: {
          action: 'pn_finance_manager_ajax',
          pn_finance_manager_ajax_type: 'pn_finance_manager_watchlist_remove',
          pn_finance_manager_ajax_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce,
          watchlist_item_id: itemId
        },
        success: function(response) {
          if (typeof response === 'string') {
            try { response = JSON.parse(response); } catch (e) { return; }
          }
          if (response.success) {
            $('.pn-finance-manager-watchlist-items').html(response.html);
            pn_finance_manager_watchlist_init_range_displays();
          }
        }
      });
    });

    // Threshold slider value display (item cards)
    $(document).on('input', '.pn-finance-manager-watchlist-threshold', function() {
      $(this).siblings('.pn-finance-manager-input-range-output').text($(this).val() + '%');
    });

    // Save alerts button
    $(document).on('click', '.pn-finance-manager-watchlist-save-btn', function() {
      var $btn = $(this);
      var $item = $btn.closest('.pn-finance-manager-watchlist-item');
      var itemId = $item.data('pn-finance-manager-watchlist-item-id');
      var alertEnabled = $item.find('.pn-finance-manager-watchlist-alert-toggle').is(':checked') ? 1 : 0;
      var alertThreshold = $item.find('.pn-finance-manager-watchlist-threshold').val();
      var originalLabel = $btn.val();

      $btn.prop('disabled', true);

      $.ajax({
        url: pn_finance_manager_ajax.ajax_url,
        type: 'POST',
        data: {
          action: 'pn_finance_manager_ajax',
          pn_finance_manager_ajax_type: 'pn_finance_manager_watchlist_update',
          pn_finance_manager_ajax_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce,
          watchlist_item_id: itemId,
          watchlist_alert_enabled: alertEnabled,
          watchlist_alert_threshold: alertThreshold
        },
        success: function(response) {
          if (typeof response === 'string') {
            try { response = JSON.parse(response); } catch (e) { return; }
          }
          if (response.success) {
            $btn.val(pn_finance_manager_i18n.alerts_saved || 'Saved!');
            setTimeout(function() {
              $btn.val(originalLabel);
              $btn.prop('disabled', false);
            }, 1500);
          } else {
            $btn.prop('disabled', false);
          }
        },
        error: function() {
          $btn.prop('disabled', false);
        }
      });
    });

    // Refresh button
    $(document).on('click', '.pn-finance-manager-watchlist-refresh-btn', function() {
      var $btn = $(this);
      $btn.prop('disabled', true);
      $btn.find('i').addClass('pn-finance-manager-spin');

      $.ajax({
        url: pn_finance_manager_ajax.ajax_url,
        type: 'POST',
        data: {
          action: 'pn_finance_manager_ajax',
          pn_finance_manager_ajax_type: 'pn_finance_manager_watchlist_refresh',
          pn_finance_manager_ajax_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce
        },
        success: function(response) {
          if (typeof response === 'string') {
            try { response = JSON.parse(response); } catch (e) { return; }
          }
          if (response.success && response.html) {
            $('.pn-finance-manager-watchlist-items').html(response.html);
            pn_finance_manager_watchlist_init_range_displays();
          }
          $btn.prop('disabled', false);
          $btn.find('i').removeClass('pn-finance-manager-spin');
        },
        error: function() {
          $btn.prop('disabled', false);
          $btn.find('i').removeClass('pn-finance-manager-spin');
        }
      });
    });
  }

  /**
   * Initialize range output displays with current value and % suffix.
   */
  function pn_finance_manager_watchlist_init_range_displays() {
    $('.pn-finance-manager-watchlist-container .pn-finance-manager-input-range-output').each(function() {
      var $range = $(this).siblings('.pn-finance-manager-input-range');
      if ($range.length) {
        $(this).text($range.val() + '%');
      }
    });
  }

  /**
   * Load stock symbols into the watchlist stock select.
   */
  function pn_finance_manager_watchlist_load_stock_symbols() {
    var $select = $('#pn_finance_manager_watchlist_stock_symbol');

    if ($select.find('option').length > 1) {
      return; // Already loaded
    }

    $select.html('<option value="">' + pn_finance_manager_i18n.loading_stock_symbols + '</option>');

    $.ajax({
      url: pn_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_finance_manager_ajax',
        pn_finance_manager_ajax_type: 'pn_finance_manager_get_stock_symbols',
        pn_finance_manager_ajax_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce
      },
      success: function(response) {
        if (typeof response === 'string') {
          try { response = JSON.parse(response); } catch (e) { return; }
        }

        $select.html('<option value="">' + pn_finance_manager_i18n.select_stock_symbol + '</option>');

        if (response.success && response.data && response.data.length > 0) {
          $.each(response.data, function(i, item) {
            $select.append('<option value="' + item.symbol + '">' + item.symbol + ' - ' + item.name + '</option>');
          });
        }
      },
      error: function() {
        $select.html('<option value="">' + pn_finance_manager_i18n.error_loading_symbols + '</option>');
      }
    });
  }

  /**
   * Load crypto symbols into the watchlist crypto select.
   */
  function pn_finance_manager_watchlist_load_crypto_symbols() {
    var $select = $('#pn_finance_manager_watchlist_crypto_symbol');

    if ($select.find('option').length > 1) {
      return; // Already loaded
    }

    $select.html('<option value="">' + pn_finance_manager_i18n.loading_crypto_symbols + '</option>');

    $.ajax({
      url: pn_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_finance_manager_ajax',
        pn_finance_manager_ajax_type: 'pn_finance_manager_get_crypto_symbols',
        pn_finance_manager_ajax_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce
      },
      success: function(response) {
        if (typeof response === 'string') {
          try { response = JSON.parse(response); } catch (e) { return; }
        }

        $select.html('<option value="">' + pn_finance_manager_i18n.select_crypto_symbol + '</option>');

        if (response.success && response.data && response.data.length > 0) {
          $.each(response.data, function(i, item) {
            $select.append('<option value="' + item.symbol + '">' + item.name + '</option>');
          });
        }
      },
      error: function() {
        $select.html('<option value="">' + pn_finance_manager_i18n.error_loading_symbols + '</option>');
      }
    });
  }

})(jQuery);
