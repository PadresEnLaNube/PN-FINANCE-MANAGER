(function($) {
  'use strict';

  $(document).ready(function() {
    pn_personal_finance_manager_watchlist_init();
  });

  function pn_personal_finance_manager_watchlist_init() {
    // Initialize range displays on page load
    pn_personal_finance_manager_watchlist_init_range_displays();

    // Initialize price history charts
    if (typeof window.pn_personal_finance_manager_init_watchlist_charts === 'function') {
      window.pn_personal_finance_manager_init_watchlist_charts();
    }

    // Load symbols when type is selected (visibility handled by Forms parent system)
    $(document).on('change', '#pn_personal_finance_manager_watchlist_type', function() {
      var type = $(this).val();
      if (type === 'stock') {
        pn_personal_finance_manager_watchlist_load_stock_symbols();
      } else if (type === 'crypto') {
        pn_personal_finance_manager_watchlist_load_crypto_symbols();
      }
    });

    // Threshold slider value display (add form)
    $(document).on('input', '#pn_personal_finance_manager_watchlist_threshold', function() {
      $(this).siblings('.pn-personal-finance-manager-input-range-output').text($(this).val() + '%');
    });

    // Edit button toggle alert controls
    $(document).on('click', '.pn-personal-finance-manager-watchlist-edit-btn', function() {
      var $item = $(this).closest('.pn-personal-finance-manager-watchlist-item');
      $(this).toggleClass('active');
      $item.find('.pn-personal-finance-manager-watchlist-alert-controls').toggleClass('pn-personal-finance-manager-display-none-soft');
    });

    // Alert checkbox toggles threshold visibility (list items)
    $(document).on('change', '.pn-personal-finance-manager-watchlist-alert-toggle', function() {
      var $item = $(this).closest('.pn-personal-finance-manager-watchlist-item');
      var $threshold = $item.find('.pn-personal-finance-manager-watchlist-threshold-field-item');
      if ($(this).is(':checked')) {
        $threshold.removeClass('pn-personal-finance-manager-display-none-soft');
      } else {
        $threshold.addClass('pn-personal-finance-manager-display-none-soft');
      }
    });

    // Alert checkbox toggles threshold visibility (add form)
    $(document).on('change', '#pn_personal_finance_manager_watchlist_alert_enabled', function() {
      var $threshold = $('.pn-personal-finance-manager-watchlist-threshold-field');
      if ($(this).is(':checked')) {
        $threshold.removeClass('pn-personal-finance-manager-display-none-soft');
      } else {
        $threshold.addClass('pn-personal-finance-manager-display-none-soft');
      }
    });

    // Add to watchlist
    $(document).on('click', '.pn-personal-finance-manager-watchlist-add-btn', function() {
      var $btn = $(this);
      var type = $('#pn_personal_finance_manager_watchlist_type').val();
      var symbol = '';

      if (type === 'stock') {
        symbol = $('#pn_personal_finance_manager_watchlist_stock_symbol').val();
      } else if (type === 'crypto') {
        symbol = $('#pn_personal_finance_manager_watchlist_crypto_symbol').val();
      }

      if (!type || !symbol) {
        return;
      }

      var alertEnabled = $('#pn_personal_finance_manager_watchlist_alert_enabled').is(':checked') ? 1 : 0;
      var alertThreshold = $('#pn_personal_finance_manager_watchlist_threshold').val();

      $btn.prop('disabled', true);

      $.ajax({
        url: pn_personal_finance_manager_ajax.ajax_url,
        type: 'POST',
        data: {
          action: 'pn_personal_finance_manager_ajax',
          pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_watchlist_add',
          pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
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
            $('.pn-personal-finance-manager-watchlist-items').html(response.html);
            pn_personal_finance_manager_watchlist_init_range_displays();
            if (typeof window.pn_personal_finance_manager_init_watchlist_charts === 'function') {
              window.pn_personal_finance_manager_init_watchlist_charts();
            }
            // Reset form
            $('#pn_personal_finance_manager_watchlist_type').val('').trigger('change');
            $('#pn_personal_finance_manager_watchlist_alert_enabled').prop('checked', false);
            $('.pn-personal-finance-manager-watchlist-threshold-field').addClass('pn-personal-finance-manager-display-none-soft');
            $('#pn_personal_finance_manager_watchlist_threshold').val(5);
            $('.pn-personal-finance-manager-watchlist-add-form .pn-personal-finance-manager-input-range-output').text('5%');
          }
          $btn.prop('disabled', false);
        },
        error: function() {
          $btn.prop('disabled', false);
        }
      });
    });

    // Remove from watchlist
    $(document).on('click', '.pn-personal-finance-manager-watchlist-remove-btn', function() {
      if (!confirm(pn_personal_finance_manager_i18n.confirm_remove_watchlist)) {
        return;
      }

      var itemId = $(this).data('pn-personal-finance-manager-item-id');

      $.ajax({
        url: pn_personal_finance_manager_ajax.ajax_url,
        type: 'POST',
        data: {
          action: 'pn_personal_finance_manager_ajax',
          pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_watchlist_remove',
          pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
          watchlist_item_id: itemId
        },
        success: function(response) {
          if (typeof response === 'string') {
            try { response = JSON.parse(response); } catch (e) { return; }
          }
          if (response.success) {
            $('.pn-personal-finance-manager-watchlist-items').html(response.html);
            pn_personal_finance_manager_watchlist_init_range_displays();
            if (typeof window.pn_personal_finance_manager_init_watchlist_charts === 'function') {
              window.pn_personal_finance_manager_init_watchlist_charts();
            }
          }
        }
      });
    });

    // Threshold slider value display (item cards)
    $(document).on('input', '.pn-personal-finance-manager-watchlist-threshold', function() {
      $(this).siblings('.pn-personal-finance-manager-input-range-output').text($(this).val() + '%');
    });

    // Save alerts button
    $(document).on('click', '.pn-personal-finance-manager-watchlist-save-btn', function() {
      var $btn = $(this);
      var $item = $btn.closest('.pn-personal-finance-manager-watchlist-item');
      var itemId = $item.data('pn-personal-finance-manager-watchlist-item-id');
      var alertEnabled = $item.find('.pn-personal-finance-manager-watchlist-alert-toggle').is(':checked') ? 1 : 0;
      var alertThreshold = $item.find('.pn-personal-finance-manager-watchlist-threshold').val();
      var originalLabel = $btn.val();

      $btn.prop('disabled', true);

      $.ajax({
        url: pn_personal_finance_manager_ajax.ajax_url,
        type: 'POST',
        data: {
          action: 'pn_personal_finance_manager_ajax',
          pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_watchlist_update',
          pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
          watchlist_item_id: itemId,
          watchlist_alert_enabled: alertEnabled,
          watchlist_alert_threshold: alertThreshold
        },
        success: function(response) {
          if (typeof response === 'string') {
            try { response = JSON.parse(response); } catch (e) { return; }
          }
          if (response.success) {
            $btn.val(pn_personal_finance_manager_i18n.alerts_saved || 'Saved!');
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
    $(document).on('click', '.pn-personal-finance-manager-watchlist-refresh-btn', function() {
      var $btn = $(this);
      $btn.prop('disabled', true);
      $btn.find('i').addClass('pn-personal-finance-manager-spin');

      $.ajax({
        url: pn_personal_finance_manager_ajax.ajax_url,
        type: 'POST',
        data: {
          action: 'pn_personal_finance_manager_ajax',
          pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_watchlist_refresh',
          pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce
        },
        success: function(response) {
          if (typeof response === 'string') {
            try { response = JSON.parse(response); } catch (e) { return; }
          }
          if (response.success && response.html) {
            $('.pn-personal-finance-manager-watchlist-items').html(response.html);
            pn_personal_finance_manager_watchlist_init_range_displays();
            if (typeof window.pn_personal_finance_manager_init_watchlist_charts === 'function') {
              window.pn_personal_finance_manager_init_watchlist_charts();
            }
          }
          $btn.prop('disabled', false);
          $btn.find('i').removeClass('pn-personal-finance-manager-spin');
        },
        error: function() {
          $btn.prop('disabled', false);
          $btn.find('i').removeClass('pn-personal-finance-manager-spin');
        }
      });
    });

    // Load 30-day price history for a watchlist item
    $(document).on('click', '.pn-personal-finance-manager-watchlist-load-history-btn', function() {
      var $btn = $(this);
      var itemId = $btn.data('pn-personal-finance-manager-item-id');
      var $icon = $btn.find('i');
      var originalIcon = $icon.text();

      $btn.prop('disabled', true);
      $icon.text('hourglass_empty');
      $icon.addClass('pn-personal-finance-manager-spin');

      $.ajax({
        url: pn_personal_finance_manager_ajax.ajax_url,
        type: 'POST',
        data: {
          action: 'pn_personal_finance_manager_ajax',
          pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_watchlist_load_history',
          pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
          watchlist_item_id: itemId
        },
        success: function(response) {
          if (typeof response === 'string') {
            try { response = JSON.parse(response); } catch (e) { return; }
          }
          if (response.success && response.html) {
            $('.pn-personal-finance-manager-watchlist-items').html(response.html);
            pn_personal_finance_manager_watchlist_init_range_displays();
            if (typeof window.pn_personal_finance_manager_init_watchlist_charts === 'function') {
              window.pn_personal_finance_manager_init_watchlist_charts();
            }
          } else {
            $btn.prop('disabled', false);
            $icon.removeClass('pn-personal-finance-manager-spin');
            $icon.text(originalIcon);
          }
        },
        error: function() {
          $btn.prop('disabled', false);
          $icon.removeClass('pn-personal-finance-manager-spin');
          $icon.text(originalIcon);
        }
      });
    });
  }

  /**
   * Initialize range output displays with current value and % suffix.
   */
  function pn_personal_finance_manager_watchlist_init_range_displays() {
    $('.pn-personal-finance-manager-watchlist-container .pn-personal-finance-manager-input-range-output').each(function() {
      var $range = $(this).siblings('.pn-personal-finance-manager-input-range');
      if ($range.length) {
        $(this).text($range.val() + '%');
      }
    });
  }

  /**
   * Load stock symbols into the watchlist stock select.
   */
  function pn_personal_finance_manager_watchlist_load_stock_symbols() {
    var $select = $('#pn_personal_finance_manager_watchlist_stock_symbol');

    if ($select.find('option').length > 1) {
      return; // Already loaded
    }

    $select.html('<option value="">' + pn_personal_finance_manager_i18n.loading_stock_symbols + '</option>');

    $.ajax({
      url: pn_personal_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_personal_finance_manager_ajax',
        pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_get_stock_symbols',
        pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce
      },
      success: function(response) {
        if (typeof response === 'string') {
          try { response = JSON.parse(response); } catch (e) { return; }
        }

        $select.html('<option value="">' + pn_personal_finance_manager_i18n.select_stock_symbol + '</option>');

        if (response.success && response.data && response.data.length > 0) {
          $.each(response.data, function(i, item) {
            $select.append('<option value="' + item.symbol + '">' + item.symbol + ' - ' + item.name + '</option>');
          });
        }
      },
      error: function() {
        $select.html('<option value="">' + pn_personal_finance_manager_i18n.error_loading_symbols + '</option>');
      }
    });
  }

  /**
   * Load crypto symbols into the watchlist crypto select.
   */
  function pn_personal_finance_manager_watchlist_load_crypto_symbols() {
    var $select = $('#pn_personal_finance_manager_watchlist_crypto_symbol');

    if ($select.find('option').length > 1) {
      return; // Already loaded
    }

    $select.html('<option value="">' + pn_personal_finance_manager_i18n.loading_crypto_symbols + '</option>');

    $.ajax({
      url: pn_personal_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_personal_finance_manager_ajax',
        pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_get_crypto_symbols',
        pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce
      },
      success: function(response) {
        if (typeof response === 'string') {
          try { response = JSON.parse(response); } catch (e) { return; }
        }

        $select.html('<option value="">' + pn_personal_finance_manager_i18n.select_crypto_symbol + '</option>');

        if (response.success && response.data && response.data.length > 0) {
          $.each(response.data, function(i, item) {
            $select.append('<option value="' + item.symbol + '">' + item.name + '</option>');
          });
        }
      },
      error: function() {
        $select.html('<option value="">' + pn_personal_finance_manager_i18n.error_loading_symbols + '</option>');
      }
    });
  }

})(jQuery);
