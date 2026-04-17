(function($) {
  'use strict';

  $(document).ready(function() {
    // Initialize stock functionality
    pn_personal_finance_manager_init_stocks();
  });

  function pn_personal_finance_manager_init_stocks() {
    // Initialize stock symbol search
    pn_personal_finance_manager_init_stock_search();

    // Handle asset type change to show/hide stock fields
    $(document).on('change', '#pn_personal_finance_manager_asset_type', function() {
      const assetType = $(this).val();

      if (assetType === 'stocks') {
        pn_personal_finance_manager_load_stock_symbols();
      } else if (assetType === 'cryptocurrencies') {
        pn_personal_finance_manager_load_crypto_symbols();
      }
    });

    // Initialize on page load
    const assetType = $('#pn_personal_finance_manager_asset_type').val();
    if (assetType === 'stocks') {
      pn_personal_finance_manager_load_stock_symbols();
    } else if (assetType === 'cryptocurrencies') {
      pn_personal_finance_manager_load_crypto_symbols();
    }
  }

  function pn_personal_finance_manager_init_stock_search() {
    // Toggle search block when clicking the search link
    $(document).on('click', '.pn-personal-finance-manager-stock-search-toggle', function(e) {
      e.preventDefault();
      var searchBlock = $(this).siblings('.pn-personal-finance-manager-stock-search-block');
      searchBlock.toggle();
      searchBlock.find('.pn-personal-finance-manager-stock-search-input').val('').focus();
      searchBlock.find('.pn-personal-finance-manager-stock-search-result').html('');
    });

    // Handle search button click
    $(document).on('click', '.pn-personal-finance-manager-stock-search-btn', function() {
      var wrapper = $(this).closest('.pn-personal-finance-manager-stock-search-block');
      var input = wrapper.find('.pn-personal-finance-manager-stock-search-input');
      var resultArea = wrapper.find('.pn-personal-finance-manager-stock-search-result');
      var symbol = input.val().trim().toUpperCase();

      if (!symbol) {
        resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + pn_personal_finance_manager_i18n.enter_symbol + '</span>');
        return;
      }

      resultArea.html('<span class="pn-personal-finance-manager-stock-search-info">' + pn_personal_finance_manager_i18n.searching_symbol + '</span>');
      $(this).prop('disabled', true);

      var btn = $(this);
      $.ajax({
        url: pn_personal_finance_manager_ajax.ajax_url,
        type: 'POST',
        data: {
          action: 'pn_personal_finance_manager_ajax',
          pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_search_stock_symbol',
          symbol: symbol,
          pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce
        },
        success: function(response) {
          if (typeof response === 'string') {
            try { response = JSON.parse(response); } catch (e) {
              resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + pn_personal_finance_manager_i18n.symbol_not_found + '</span>');
              btn.prop('disabled', false);
              return;
            }
          }

          if (response.success && response.data) {
            // Cached symbol — auto-add directly
            if (response.data.symbol && response.data.name && !response.data.matches) {
              pn_personal_finance_manager_add_stock_to_select(response.data.symbol, response.data.name, wrapper, resultArea);
            }
            // Multiple matches — show selectable list
            else if (response.data.matches && response.data.matches.length > 1) {
              var html = '<div class="pn-personal-finance-manager-stock-search-results">';
              html += '<p class="pn-personal-finance-manager-stock-search-info">' + pn_personal_finance_manager_i18n.select_correct_match + '</p>';
              response.data.matches.forEach(function(match) {
                var meta = [match.exchange, match.country, match.instrument_type].filter(Boolean).join(' · ');
                html += '<div class="pn-personal-finance-manager-stock-search-result-item"' +
                  ' data-symbol="' + match.symbol + '"' +
                  ' data-name="' + match.name.replace(/"/g, '&quot;') + '"' +
                  ' data-exchange="' + match.exchange.replace(/"/g, '&quot;') + '">';
                html += '<strong>' + match.symbol + '</strong> — ' + match.name;
                if (meta) {
                  html += '<span class="pn-personal-finance-manager-stock-search-result-meta">' + meta + '</span>';
                }
                html += '</div>';
              });
              html += '</div>';
              resultArea.html(html);
            }
            // Single match — auto-confirm
            else if (response.data.matches && response.data.matches.length === 1) {
              var m = response.data.matches[0];
              pn_personal_finance_manager_confirm_stock_symbol(m.symbol, m.name, m.exchange, wrapper, resultArea);
            }
            else {
              resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + pn_personal_finance_manager_i18n.symbol_not_found + '</span>');
            }
          } else {
            var msg = (response.data && response.data.error_content) ? response.data.error_content : pn_personal_finance_manager_i18n.symbol_not_found;
            resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + msg + '</span>');
          }
          btn.prop('disabled', false);
        },
        error: function() {
          resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + pn_personal_finance_manager_i18n.symbol_not_found + '</span>');
          btn.prop('disabled', false);
        }
      });
    });

    // Allow Enter key in search input
    $(document).on('keyup', '.pn-personal-finance-manager-stock-search-input', function(e) {
      if (e.keyCode === 13) {
        $(this).siblings('.pn-personal-finance-manager-stock-search-btn').click();
      }
    });

    // Handle click on a search result item
    $(document).on('click', '.pn-personal-finance-manager-stock-search-result-item', function() {
      var item = $(this);
      var wrapper = item.closest('.pn-personal-finance-manager-stock-search-block');
      var resultArea = wrapper.find('.pn-personal-finance-manager-stock-search-result');
      var symbol = item.data('symbol');
      var name = item.data('name');
      var exchange = item.data('exchange');

      // Highlight selected item
      item.siblings().css('opacity', '0.5');
      item.css({'border-color': 'var(--color-main, #007bff)', 'background-color': 'var(--color-main-grey, #f0f7ff)'});

      pn_personal_finance_manager_confirm_stock_symbol(symbol, name, exchange, wrapper, resultArea);
    });
  }

  // Confirm a stock symbol selection via AJAX and add to select
  function pn_personal_finance_manager_confirm_stock_symbol(symbol, name, exchange, wrapper, resultArea) {
    $.ajax({
      url: pn_personal_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_personal_finance_manager_ajax',
        pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_confirm_stock_symbol',
        symbol: symbol,
        name: name,
        exchange: exchange,
        pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce
      },
      success: function(response) {
        if (typeof response === 'string') {
          try { response = JSON.parse(response); } catch (e) {
            resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + pn_personal_finance_manager_i18n.symbol_not_found + '</span>');
            return;
          }
        }
        if (response.success && response.data) {
          pn_personal_finance_manager_add_stock_to_select(response.data.symbol, response.data.name, wrapper, resultArea);
        } else {
          resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + pn_personal_finance_manager_i18n.symbol_not_found + '</span>');
        }
      },
      error: function() {
        resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + pn_personal_finance_manager_i18n.symbol_not_found + '</span>');
      }
    });
  }

  // Add a confirmed stock symbol to the select dropdown
  function pn_personal_finance_manager_add_stock_to_select(symbol, name, wrapper, resultArea) {
    var sel = $('#pn_personal_finance_manager_stock_symbol');
    if (sel.find('option[value="' + symbol + '"]').length === 0) {
      sel.append('<option value="' + symbol + '">' + symbol + ' - ' + name + '</option>');
    }
    sel.val(symbol).trigger('change');
    resultArea.html('<span class="pn-personal-finance-manager-stock-search-success">' + pn_personal_finance_manager_i18n.symbol_found + '</span>');
    setTimeout(function() {
      wrapper.hide();
    }, 1200);
  }

  function pn_personal_finance_manager_inject_stock_search_ui() {
    var symbolSelect = $('#pn_personal_finance_manager_stock_symbol');
    if (!symbolSelect.length) return;

    var wrapper = symbolSelect.closest('.pn-personal-finance-manager-input-wrapper');
    if (!wrapper.length) return;

    // Don't inject if already present
    if (wrapper.find('.pn-personal-finance-manager-stock-search-toggle').length) return;

    var searchHtml = '<a href="#" class="pn-personal-finance-manager-stock-search-toggle pn-personal-finance-manager-font-size-small">' + pn_personal_finance_manager_i18n.search_new_symbol + '</a>' +
      '<div class="pn-personal-finance-manager-stock-search-block" style="display:none; margin-top:8px;">' +
        '<div style="display:flex; gap:6px; align-items:center;">' +
          '<input type="text" class="pn-personal-finance-manager-stock-search-input pn-personal-finance-manager-input" placeholder="' + pn_personal_finance_manager_i18n.enter_symbol + '" style="flex:1;" />' +
          '<button type="button" class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini pn-personal-finance-manager-stock-search-btn">' + pn_personal_finance_manager_i18n.search + '</button>' +
        '</div>' +
        '<div class="pn-personal-finance-manager-stock-search-result" style="margin-top:4px;"></div>' +
      '</div>';

    wrapper.append(searchHtml);
  }

  function pn_personal_finance_manager_load_stock_symbols() {
    const symbolSelect = $('#pn_personal_finance_manager_stock_symbol');

    // Save the currently selected value before replacing options
    const savedValue = symbolSelect.val();

    // Show loading state
    symbolSelect.html('<option value="">' + pn_personal_finance_manager_i18n.loading_stock_symbols + '</option>');

    $.ajax({
      url: pn_personal_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_personal_finance_manager_ajax',
        pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_get_stock_symbols',
        pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce
      },
      success: function(response) {
        // Parse JSON response if it's a string
        if (typeof response === 'string') {
          try {
            response = JSON.parse(response);
          } catch (e) {
            symbolSelect.html('<option value="">' + pn_personal_finance_manager_i18n.error_loading_symbols + '</option>');
            return;
          }
        }

        if (response.success && response.data) {
          symbolSelect.html('<option value="">' + pn_personal_finance_manager_i18n.select_stock_symbol + '</option>');
          response.data.forEach(function(stock) {
            symbolSelect.append('<option value="' + stock.symbol + '">' + stock.symbol + ' - ' + stock.name + '</option>');
          });
          // Restore the saved value if it exists
          if (savedValue) {
            symbolSelect.val(savedValue);
          }
          // Inject search UI after symbols are loaded
          pn_personal_finance_manager_inject_stock_search_ui();
        } else {
          symbolSelect.html('<option value="">' + pn_personal_finance_manager_i18n.no_stock_symbols + '</option>');
        }
      },
      error: function(xhr, status, error) {
        symbolSelect.html('<option value="">' + pn_personal_finance_manager_i18n.error_loading_symbols + '</option>');
      }
    });
  }

  // =========================================================================
  // CRYPTOCURRENCY FUNCTIONS
  // =========================================================================

  function pn_personal_finance_manager_load_crypto_symbols() {
    const symbolSelect = $('#pn_personal_finance_manager_crypto_symbol');
    if (!symbolSelect.length) return;

    const savedValue = symbolSelect.val();

    symbolSelect.html('<option value="">' + (pn_personal_finance_manager_i18n.loading_crypto_symbols || 'Loading cryptocurrencies...') + '</option>');

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
          try { response = JSON.parse(response); } catch (e) {
            symbolSelect.html('<option value="">' + (pn_personal_finance_manager_i18n.no_crypto_symbols || 'Error loading cryptocurrencies') + '</option>');
            return;
          }
        }

        if (response.success && response.data) {
          symbolSelect.html('<option value="">' + (pn_personal_finance_manager_i18n.select_crypto_symbol || 'Select a cryptocurrency') + '</option>');
          response.data.forEach(function(coin) {
            symbolSelect.append('<option value="' + coin.symbol + '">' + coin.name + '</option>');
          });
          if (savedValue) {
            symbolSelect.val(savedValue);
          }
          pn_personal_finance_manager_inject_crypto_search_ui();
        } else {
          symbolSelect.html('<option value="">' + (pn_personal_finance_manager_i18n.no_crypto_symbols || 'No cryptocurrencies available') + '</option>');
        }
      },
      error: function() {
        symbolSelect.html('<option value="">' + (pn_personal_finance_manager_i18n.no_crypto_symbols || 'Error loading cryptocurrencies') + '</option>');
      }
    });
  }

  function pn_personal_finance_manager_inject_crypto_search_ui() {
    var symbolSelect = $('#pn_personal_finance_manager_crypto_symbol');
    if (!symbolSelect.length) return;

    var wrapper = symbolSelect.closest('.pn-personal-finance-manager-input-wrapper');
    if (!wrapper.length) return;

    if (wrapper.find('.pn-personal-finance-manager-crypto-search-toggle').length) return;

    var searchHtml = '<a href="#" class="pn-personal-finance-manager-crypto-search-toggle pn-personal-finance-manager-font-size-small">' + (pn_personal_finance_manager_i18n.search_new_crypto || 'Search cryptocurrency') + '</a>' +
      '<div class="pn-personal-finance-manager-crypto-search-block" style="display:none; margin-top:8px;">' +
        '<div style="display:flex; gap:6px; align-items:center;">' +
          '<input type="text" class="pn-personal-finance-manager-crypto-search-input" placeholder="' + (pn_personal_finance_manager_i18n.enter_crypto_name || 'Enter cryptocurrency name (e.g. Solana)') + '" style="flex:1;" />' +
          '<button type="button" class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini pn-personal-finance-manager-crypto-search-btn">' + (pn_personal_finance_manager_i18n.search || 'Search') + '</button>' +
        '</div>' +
        '<div class="pn-personal-finance-manager-crypto-search-result" style="margin-top:4px;"></div>' +
      '</div>';

    wrapper.append(searchHtml);
  }

  // Toggle crypto search block
  $(document).on('click', '.pn-personal-finance-manager-crypto-search-toggle', function(e) {
    e.preventDefault();
    var searchBlock = $(this).siblings('.pn-personal-finance-manager-crypto-search-block');
    searchBlock.toggle();
    searchBlock.find('.pn-personal-finance-manager-crypto-search-input').val('').focus();
    searchBlock.find('.pn-personal-finance-manager-crypto-search-result').html('');
  });

  // Handle crypto search button click
  $(document).on('click', '.pn-personal-finance-manager-crypto-search-btn', function() {
    var wrapper = $(this).closest('.pn-personal-finance-manager-crypto-search-block');
    var input = wrapper.find('.pn-personal-finance-manager-crypto-search-input');
    var resultArea = wrapper.find('.pn-personal-finance-manager-crypto-search-result');
    var query = input.val().trim();

    if (!query) {
      resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + (pn_personal_finance_manager_i18n.enter_crypto_name || 'Enter a cryptocurrency name') + '</span>');
      return;
    }

    resultArea.html('<span class="pn-personal-finance-manager-stock-search-info">' + (pn_personal_finance_manager_i18n.searching_crypto || 'Searching...') + '</span>');
    $(this).prop('disabled', true);

    var btn = $(this);
    $.ajax({
      url: pn_personal_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_personal_finance_manager_ajax',
        pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_search_crypto_symbol',
        query: query,
        pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce
      },
      success: function(response) {
        if (typeof response === 'string') {
          try { response = JSON.parse(response); } catch (e) {
            resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + (pn_personal_finance_manager_i18n.crypto_not_found || 'Cryptocurrency not found') + '</span>');
            btn.prop('disabled', false);
            return;
          }
        }

        if (response.success && response.data) {
          var sel = $('#pn_personal_finance_manager_crypto_symbol');
          if (sel.find('option[value="' + response.data.symbol + '"]').length === 0) {
            sel.append('<option value="' + response.data.symbol + '">' + response.data.name + '</option>');
          }
          sel.val(response.data.symbol).trigger('change');
          resultArea.html('<span class="pn-personal-finance-manager-stock-search-success">' + (pn_personal_finance_manager_i18n.crypto_found || 'Cryptocurrency found and added!') + '</span>');
          setTimeout(function() { wrapper.hide(); }, 1200);
        } else {
          var msg = (response.data && response.data.error_content) ? response.data.error_content : (pn_personal_finance_manager_i18n.crypto_not_found || 'Cryptocurrency not found');
          resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + msg + '</span>');
        }
        btn.prop('disabled', false);
      },
      error: function() {
        resultArea.html('<span class="pn-personal-finance-manager-stock-search-error">' + (pn_personal_finance_manager_i18n.crypto_not_found || 'Cryptocurrency not found') + '</span>');
        btn.prop('disabled', false);
      }
    });
  });

  // Allow Enter key in crypto search input
  $(document).on('keyup', '.pn-personal-finance-manager-crypto-search-input', function(e) {
    if (e.keyCode === 13) {
      $(this).siblings('.pn-personal-finance-manager-crypto-search-btn').click();
    }
  });

  // Fetch crypto purchase price button
  $(document).on('click', '.pn-personal-finance-manager-fetch-crypto-purchase-price', function(e) {
    e.stopPropagation();
    var btn = $(this);
    var assetId = btn.data('asset-id');
    var originalHtml = btn.html();

    btn.prop('disabled', true).html('<i class="material-icons-outlined">hourglass_empty</i> ' + (pn_personal_finance_manager_i18n.fetching_price || 'Fetching...'));

    $.ajax({
      url: pn_personal_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_personal_finance_manager_ajax',
        pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_fetch_crypto_purchase_price',
        asset_id: assetId,
        pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce
      },
      success: function(response) {
        if (typeof response === 'string') {
          try { response = JSON.parse(response); } catch (e) {
            btn.prop('disabled', false).html(originalHtml);
            return;
          }
        }
        if (response.success) {
          location.reload();
        } else {
          var errorMsg = (response.error) ? response.error : (pn_personal_finance_manager_i18n.price_fetch_error || 'Could not fetch price. Try again later.');
          btn.prop('disabled', false).html('<i class="material-icons-outlined">error_outline</i> ' + errorMsg);
          setTimeout(function() { btn.html(originalHtml); }, 3000);
        }
      },
      error: function() {
        btn.prop('disabled', false).html(originalHtml);
      }
    });
  });

  // Chart.js is loaded via wp_enqueue_script dependency — no CDN fallback needed
  function pn_personal_finance_manager_load_chartjs() {
  }

  // Initialize charts for user assets portfolio
  function pn_personal_finance_manager_init_portfolio_charts() {
    if (typeof Chart === 'undefined') {
      // Wait for Chart.js to load
      setTimeout(pn_personal_finance_manager_init_portfolio_charts, 100);
      return;
    }

    $('.pn-personal-finance-manager-stock-chart canvas').each(function() {
      const canvas = this;
      const chartId = canvas.id;
      const assetId = chartId.replace('chart-', '');
      
      // Get chart data from the page
      const chartData = window.pn_personal_finance_manager_chart_data && window.pn_personal_finance_manager_chart_data[assetId];
      
      if (chartData && chartData.length > 0) {
        const labels = chartData.map(function(item) {
          return item.recorded_date;
        });
        
        const prices = chartData.map(function(item) {
          return parseFloat(item.price);
        });
        
        const symbol = chartData[0].symbol || 'Stock';
        
        new Chart(canvas.getContext('2d'), {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: symbol + ' Price',
              data: prices,
              borderColor: '#007bff',
              backgroundColor: 'rgba(0, 123, 255, 0.1)',
              borderWidth: 2,
              fill: true,
              tension: 0.1,
              pointRadius: 3,
              pointHoverRadius: 5
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
              intersect: false,
              mode: 'index'
            },
            scales: {
              x: {
                display: true,
                title: {
                  display: true,
                  text: 'Date'
                }
              },
              y: {
                display: true,
                title: {
                  display: true,
                  text: 'Price'
                },
                beginAtZero: false
              }
            },
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return 'Price: ' + context.parsed.y.toFixed(2);
                  }
                }
              }
            }
          }
        });
      }
    });
  }

  // Initialize portfolio charts when document is ready
  $(document).ready(function() {
    pn_personal_finance_manager_load_chartjs();
    
    // Initialize portfolio charts after a short delay to ensure Chart.js is loaded
    setTimeout(function() {
      pn_personal_finance_manager_init_portfolio_charts();
    }, 500);
  });

  // Function to refresh portfolio data
  window.pn_personal_finance_manager_refresh_portfolio = function() {
    location.reload();
  };

  // Toggle stock/crypto card body on header click
  $(document).on('click', '.pn-personal-finance-manager-stock-toggle', function() {
    var card = $(this).closest('.pn-personal-finance-manager-stock-card');
    var body = card.find('.pn-personal-finance-manager-stock-body');
    var icon = $(this).find('.pn-personal-finance-manager-stock-toggle-icon');
    body.slideToggle(200, function() {
      if (body.is(':visible')) {
        icon.text('expand_less');
        // Render charts once body is visible
        body.find('canvas[data-chart-data]').each(function() {
          pn_personal_finance_manager_render_stock_popup_chart($(this));
        });
      } else {
        icon.text('expand_more');
      }
    });
  });

  // Fullscreen toggle for chart blocks
  $(document).on('click', '.pn-personal-finance-manager-chart-fullscreen-btn', function(e) {
    e.stopPropagation();
    var chartWrapper = $(this).closest('.pn-personal-finance-manager-stock-evolution-chart');
    var card = chartWrapper.closest('.pn-personal-finance-manager-stock-card');
    var icon = $(this).find('i');
    chartWrapper.toggleClass('pn-personal-finance-manager-chart-fullscreen');
    card.toggleClass('pn-personal-finance-manager-chart-fullscreen-active');
    if (chartWrapper.hasClass('pn-personal-finance-manager-chart-fullscreen')) {
      icon.text('fullscreen_exit');
      $('body').addClass('pn-personal-finance-manager-no-scroll');
    } else {
      icon.text('fullscreen');
      $('body').removeClass('pn-personal-finance-manager-no-scroll');
    }
    // Let Chart.js recalculate dimensions
    var canvas = chartWrapper.find('canvas');
    var instance = canvas.data('chartInstance');
    if (instance) {
      setTimeout(function() { instance.resize(); }, 50);
    }
  });

  // Close fullscreen chart on ESC
  $(document).on('keyup', function(e) {
    if (e.keyCode === 27) {
      var fs = $('.pn-personal-finance-manager-chart-fullscreen');
      if (fs.length) {
        fs.find('.pn-personal-finance-manager-chart-fullscreen-btn i').text('fullscreen');
        fs.removeClass('pn-personal-finance-manager-chart-fullscreen');
        fs.closest('.pn-personal-finance-manager-stock-card').removeClass('pn-personal-finance-manager-chart-fullscreen-active');
        $('body').removeClass('pn-personal-finance-manager-no-scroll');
        var canvas = fs.find('canvas');
        var instance = canvas.data('chartInstance');
        if (instance) {
          setTimeout(function() { instance.resize(); }, 50);
        }
      }
    }
  });

  // Render chart inside popup from data attributes
  function pn_personal_finance_manager_render_stock_popup_chart(canvas) {
    if (typeof Chart === 'undefined') return;
    if (canvas.data('chartInstance')) return; // Already rendered

    var chartData = canvas.data('chartData');
    var chartSymbol = canvas.data('chartSymbol') || '';
    var chartLabel = canvas.data('chartLabel') || 'Price';

    if (!chartData || chartData.length === 0) return;

    var labels = chartData.map(function(d) { return d.date; });
    var prices = chartData.map(function(d) { return d.price; });

    var instance = new Chart(canvas[0].getContext('2d'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: chartLabel,
          data: prices,
          borderColor: '#007bff',
          backgroundColor: 'rgba(0, 123, 255, 0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.1,
          pointRadius: 2,
          pointHoverRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { intersect: false, mode: 'index' },
        scales: {
          x: { display: true, ticks: { maxTicksLimit: 6, maxRotation: 45 } },
          y: { display: true, beginAtZero: false, ticks: { callback: function(v) { return chartSymbol + v.toFixed(2); } } }
        },
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: function(ctx) { return chartSymbol + ctx.parsed.y.toFixed(2); } } }
        }
      }
    });
    canvas.data('chartInstance', instance);
  }

  // Expose chart helpers for use by other scripts (e.g. watchlist)
  window.pn_personal_finance_manager_render_chart = pn_personal_finance_manager_render_stock_popup_chart;
  window.pn_personal_finance_manager_load_chartjs = pn_personal_finance_manager_load_chartjs;

  /**
   * Initialize charts inside watchlist items.
   * Called on page load and after AJAX operations that re-render the list.
   */
  window.pn_personal_finance_manager_init_watchlist_charts = function() {
    if (typeof Chart === 'undefined') {
      pn_personal_finance_manager_load_chartjs();
      setTimeout(window.pn_personal_finance_manager_init_watchlist_charts, 300);
      return;
    }
    $('.pn-personal-finance-manager-watchlist-chart-section canvas[data-chart-data]').each(function() {
      pn_personal_finance_manager_render_stock_popup_chart($(this));
    });
  };

  // Fetch purchase price button
  $(document).on('click', '.pn-personal-finance-manager-fetch-purchase-price', function(e) {
    e.stopPropagation();
    var btn = $(this);
    var assetId = btn.data('asset-id');
    var originalHtml = btn.html();

    btn.prop('disabled', true).html('<i class="material-icons-outlined">hourglass_empty</i> ' + (pn_personal_finance_manager_i18n.fetching_price || 'Fetching...'));

    $.ajax({
      url: pn_personal_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_personal_finance_manager_ajax',
        pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_fetch_purchase_price',
        asset_id: assetId,
        pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce
      },
      success: function(response) {
        if (typeof response === 'string') {
          try { response = JSON.parse(response); } catch (e) {
            btn.prop('disabled', false).html(originalHtml);
            return;
          }
        }
        if (response.success) {
          location.reload();
        } else {
          var errorMsg = (response.error) ? response.error : (pn_personal_finance_manager_i18n.price_fetch_error || 'Could not fetch price. Try again later.');
          btn.prop('disabled', false).html('<i class="material-icons-outlined">error_outline</i> ' + errorMsg);
          setTimeout(function() {
            btn.html(originalHtml);
          }, 3000);
        }
      },
      error: function() {
        btn.prop('disabled', false).html(originalHtml);
      }
    });
  });

  // Sort bar handlers
  $(document).on('change', '.pn-personal-finance-manager-sort-select', function() {
    pn_personal_finance_manager_sort_grid($(this).closest('.pn-personal-finance-manager-asset-category'));
  });

  $(document).on('click', '.pn-personal-finance-manager-sort-dir-btn', function() {
    var dir = $(this).data('dir') === 'asc' ? 'desc' : 'asc';
    $(this).data('dir', dir);
    $(this).find('i').text(dir === 'asc' ? 'arrow_upward' : 'arrow_downward');
    $(this).attr('title', dir === 'asc'
      ? pn_personal_finance_manager_i18n.sort_ascending
      : pn_personal_finance_manager_i18n.sort_descending);
    pn_personal_finance_manager_sort_grid($(this).closest('.pn-personal-finance-manager-asset-category'));
  });

  function pn_personal_finance_manager_sort_grid($category) {
    var sortKey = $category.find('.pn-personal-finance-manager-sort-select').val();
    var dir = $category.find('.pn-personal-finance-manager-sort-dir-btn').data('dir');
    var $grid = $category.find(
      '.pn-personal-finance-manager-stocks-grid, .pn-personal-finance-manager-other-assets'
    );
    if (!$grid.length) return;

    var $cards = $grid.children().toArray();
    $cards.sort(function(a, b) {
      var dataAttr = 'sort-' + sortKey;
      var aVal = $(a).data(dataAttr);
      var bVal = $(b).data(dataAttr);

      if (sortKey === 'name') {
        aVal = (aVal || '').toString().toLowerCase();
        bVal = (bVal || '').toString().toLowerCase();
        return dir === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
      } else if (sortKey === 'date') {
        aVal = aVal || '';
        bVal = bVal || '';
        return dir === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
      } else {
        aVal = parseFloat(aVal) || 0;
        bVal = parseFloat(bVal) || 0;
        return dir === 'asc' ? aVal - bVal : bVal - aVal;
      }
    });
    $.each($cards, function(_, card) { $grid.append(card); });
  }

  // Auto-refresh portfolio every 5 minutes if user is on portfolio page
  if ($('.pn-personal-finance-manager-user-assets-portfolio').length > 0) {
    setInterval(function() {
      pn_personal_finance_manager_refresh_portfolio();
    }, 300000); // 5 minutes
  }

  // Initialize performance chart for stock performance shortcode
  window.pn_personal_finance_manager_init_performance_chart = function(assetId, chartData) {
    const canvas = document.getElementById('pn-personal-finance-manager-chart-' + assetId);
    console.log('Llamada a pn_personal_finance_manager_init_performance_chart', assetId, chartData, canvas);
    if (typeof Chart === 'undefined') {
      return;
    }
    if (!canvas) {
      return;
    }
    if (!chartData || chartData.length === 0) {
      return;
    }
    // Obtener número de acciones del usuario
    var shares = (window.pn_personal_finance_manager_shares && window.pn_personal_finance_manager_shares[assetId]) ? parseFloat(window.pn_personal_finance_manager_shares[assetId]) : 1;
    // Prepara los datos para la gráfica
    const labels = chartData.map(function(item) {
      return item.recorded_date;
    });
    // Calcula el valor total de la posición para cada fecha
    const positionValues = chartData.map(function(item) {
      return shares * parseFloat(item.price);
    });
    // Dataset para el valor total de la posición
    const datasets = [{
      label: 'Position Value',
      data: positionValues,
      borderColor: '#007bff',
      backgroundColor: 'rgba(0, 123, 255, 0.1)',
      borderWidth: 2,
      fill: true,
      tension: 0.1,
      pointRadius: 3,
      pointHoverRadius: 5
    }];
    // Crear el gráfico
    new Chart(canvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: datasets
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          intersect: false,
          mode: 'index'
        },
        scales: {
          x: {
            display: true,
            title: {
              display: true,
              text: 'Date'
            },
            ticks: {
              maxTicksLimit: 10,
              maxRotation: 45
            }
          },
          y: {
            display: true,
            title: {
              display: true,
              text: 'Position Value'
            },
            beginAtZero: false,
            ticks: {
              callback: function(value) {
                return value.toFixed(2);
              }
            }
          }
        },
        plugins: {
          legend: {
            display: true,
            position: 'top'
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return context.dataset.label + ': ' + context.parsed.y.toFixed(2);
              }
            }
          }
        }
      }
    });
  };

  // Comparison period selector
  $(document).on('change', '.pn-personal-finance-manager-comparison-period-select', function() {
    var period = $(this).val();
    var $select = $(this);
    $select.prop('disabled', true);

    $.ajax({
      url: pn_personal_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_personal_finance_manager_ajax',
        pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
        pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_save_comparison_period',
        comparison_period: period
      },
      success: function(response) {
        var data = typeof response === 'string' ? JSON.parse(response) : response;
        if (data.success) {
          location.reload();
        } else {
          $select.prop('disabled', false);
        }
      },
      error: function() {
        $select.prop('disabled', false);
      }
    });
  });

  // Load asset price history
  $(document).on('click', '.pn-personal-finance-manager-load-asset-history-btn', function(e) {
    e.preventDefault();
    var $btn = $(this);
    var assetId = $btn.data('asset-id');
    var symbol = $btn.data('symbol');
    var assetType = $btn.data('asset-type');
    var originalHtml = $btn.html();

    $btn.prop('disabled', true);
    $btn.html('<span class="pn-personal-finance-manager-waiting"><span class="pn-personal-finance-manager-loader-circle-waiting"><div></div><div></div><div></div><div></div></span></span>');

    $.ajax({
      url: pn_personal_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_personal_finance_manager_ajax',
        pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
        pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_load_asset_history',
        asset_id: assetId,
        symbol: symbol,
        asset_type: assetType
      },
      success: function(response) {
        var data = typeof response === 'string' ? JSON.parse(response) : response;
        if (data.success) {
          location.reload();
        } else {
          $btn.prop('disabled', false);
          $btn.html(originalHtml);
        }
      },
      error: function() {
        $btn.prop('disabled', false);
        $btn.html(originalHtml);
      }
    });
  });

  // Amortization table popup
  $(document).on('click', '.pn-personal-finance-manager-amortization-btn', function(e) {
    e.preventDefault();
    var $btn = $(this);
    var liabilityId = $btn.data('liability-id');

    $btn.prop('disabled', true);

    $.ajax({
      url: pn_personal_finance_manager_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'pn_personal_finance_manager_ajax',
        pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
        pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_amortization_table',
        pn_personal_finance_manager_liability_id: liabilityId
      },
      success: function(response) {
        var data = typeof response === 'string' ? JSON.parse(response) : response;
        $btn.prop('disabled', false);

        if (data.html) {
          // Create popup overlay
          var $overlay = $('<div class="pn-personal-finance-manager-amortization-overlay"></div>');
          var $popup = $('<div class="pn-personal-finance-manager-amortization-popup">' +
            '<div class="pn-personal-finance-manager-amortization-popup-header">' +
            '<h3>' + (pn_personal_finance_manager_i18n.amortization_schedule || 'Amortization Schedule') + '</h3>' +
            '<button type="button" class="pn-personal-finance-manager-amortization-close"><i class="material-icons-outlined">close</i></button>' +
            '</div>' +
            '<div class="pn-personal-finance-manager-amortization-popup-body">' + data.html + '</div>' +
            '</div>');

          $overlay.appendTo('body');
          $popup.appendTo('body');

          // Close handlers
          $overlay.on('click', function() {
            $overlay.remove();
            $popup.remove();
          });
          $popup.find('.pn-personal-finance-manager-amortization-close').on('click', function() {
            $overlay.remove();
            $popup.remove();
          });
        }
      },
      error: function() {
        $btn.prop('disabled', false);
      }
    });
  });

  // Section toggle (collapse/expand)
  $(document).on('click', '.pn-personal-finance-manager-section-toggle', function(e) {
    // Don't toggle if clicking a link or button inside the header
    if ($(e.target).closest('a, button').length) return;
    var $toggle = $(this);
    var $body = $toggle.next('.pn-personal-finance-manager-section-body');
    if (!$body.length) {
      // For h2 inside section-header, look for sibling after the parent
      $body = $toggle.closest('.pn-personal-finance-manager-section-header, .pn-personal-finance-manager-portfolio-summary').next('.pn-personal-finance-manager-section-body');
    }
    if (!$body.length) {
      // Fallback: find the section-body as next sibling of the header's container
      $body = $toggle.parent().find('> .pn-personal-finance-manager-section-body').first();
    }
    if ($body.length) {
      $toggle.toggleClass('collapsed');
      $body.slideToggle(250);
    }
  });

})(jQuery);