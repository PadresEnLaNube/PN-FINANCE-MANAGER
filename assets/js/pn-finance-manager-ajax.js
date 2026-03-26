(function($) {
  'use strict';

  $(document).ready(function() {
    $(document).on('submit', '.pn-finance-manager-form', function(e){
      var pn_finance_manager_form = $(this);
      var pn_finance_manager_btn = pn_finance_manager_form.find('input[type="submit"]');
      pn_finance_manager_btn.addClass('pn-finance-manager-link-disabled').siblings('.pn-finance-manager-waiting').removeClass('pn-finance-manager-display-none');

      var ajax_url = pn_finance_manager_ajax.ajax_url;
      var data = {
        action: 'pn_finance_manager_ajax_nopriv',
        pn_finance_manager_ajax_nopriv_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce,
        pn_finance_manager_get_nonce: pn_finance_manager_action.pn_finance_manager_get_nonce,
        pn_finance_manager_ajax_nopriv_type: 'pn_finance_manager_form_save',
        pn_finance_manager_form_id: pn_finance_manager_form.attr('id'),
        pn_finance_manager_form_type: pn_finance_manager_btn.attr('data-pn-finance-manager-type'),
        pn_finance_manager_form_subtype: pn_finance_manager_btn.attr('data-pn-finance-manager-subtype'),
        pn_finance_manager_form_user_id: pn_finance_manager_btn.attr('data-pn-finance-manager-user-id'),
        pn_finance_manager_form_post_id: pn_finance_manager_btn.attr('data-pn-finance-manager-post-id'),
        pn_finance_manager_form_post_type: pn_finance_manager_btn.attr('data-pn-finance-manager-post-type'),
        pn_finance_manager_ajax_keys: [],
      };
      console.log('data');console.log(data);

      if (!(typeof window['pn_finance_manager_window_vars'] !== 'undefined')) {
        window['pn_finance_manager_window_vars'] = [];
      }

      $(pn_finance_manager_form.find('input:not([type="submit"]), select, textarea')).each(function(index, element) {
        var is_multiple = $(this).attr('multiple');
        
        if (is_multiple) {
          if (!(typeof window['pn_finance_manager_window_vars']['form_field_' + element.name] !== 'undefined')) {
            window['pn_finance_manager_window_vars']['form_field_' + element.name] = [];
          }

          window['pn_finance_manager_window_vars']['form_field_' + element.name].push($(element).val());

          data[element.name] = window['pn_finance_manager_window_vars']['form_field_' + element.name];
        }else{
          if ($(this).is(':checkbox')) {
            if ($(this).is(':checked')) {
              data[element.name] = $(element).val();
            }else{
              data[element.name] = '';
            }
          }else if ($(this).is(':radio')) {
            if ($(this).is(':checked')) {
              data[element.name] = $(element).val();
            }
          }else{
            data[element.name] = $(element).val();
          }
        }

        data.pn_finance_manager_ajax_keys.push({
          id: element.name,
          node: element.nodeName,
          type: element.type,
          multiple: (is_multiple == 'multiple' ? true : false),
        });
      });

      $.post(ajax_url, data, function(response) {
        console.log('data');console.log(data);
        console.log('response');console.log(response);

        var response_json = $.parseJSON(response);

        if (response_json['error_key'] == 'pn_finance_manager_form_save_error_unlogged') {
          pn_finance_manager_get_main_message(pn_finance_manager_i18n.user_unlogged);

          if (!$('.userspn-profile-wrapper .user-unlogged').length) {
            $('.userspn-profile-wrapper').prepend('<div class="userspn-alert userspn-alert-warning user-unlogged">' + pn_finance_manager_i18n.user_unlogged + '</div>');
          }

          PN_FINANCE_MANAGER_Popups.open($('#userspn-profile-popup'));
          $('#userspn-login input#user_login').focus();
        }else if (response_json['error_key'] != '') {
          pn_finance_manager_get_main_message(pn_finance_manager_i18n.an_error_has_occurred);
        }else {
          pn_finance_manager_get_main_message(pn_finance_manager_i18n.saved_successfully);
        }

        if (response_json['update_list']) {
          $('.pn-finance-manager-' + data.pn_finance_manager_form_post_type + '-list').html(response_json['update_html']);
        }

        if (response_json['statistics_html']) {
          $('.pn-finance-manager-' + data.pn_finance_manager_form_post_type + '-statistics').html(response_json['statistics_html']);
          if (typeof pnFinanceManagerInitCharts === 'function') {
            pnFinanceManagerInitCharts();
          }
        }

        if (response_json['popup_close']) {
          PN_FINANCE_MANAGER_Popups.close();
          $('.pn-finance-manager-menu-more-overlay').fadeOut('fast');
        }

        pn_finance_manager_btn.removeClass('pn-finance-manager-link-disabled').siblings('.pn-finance-manager-waiting').addClass('pn-finance-manager-display-none')
      });

      delete window['pn_finance_manager_window_vars'];
      return false;
    });

    $(document).on('click', '.pn-finance-manager-popup-open-ajax', function(e) {
      e.preventDefault();

      var pn_finance_manager_btn = $(this);
      var pn_finance_manager_ajax_type = pn_finance_manager_btn.attr('data-pn-finance-manager-ajax-type');
      var pn_finance_manager_asset_id = pn_finance_manager_btn.closest('.pn-finance-manager-asset').attr('data-pn_finance_manager_asset-id');
      var pn_finance_manager_liability_id = pn_finance_manager_btn.closest('.pn-finance-manager-liability').attr('data-pn_finance_manager_liability-id');
      var pn_finance_manager_popup_element = $('#' + pn_finance_manager_btn.attr('data-pn-finance-manager-popup-id'));

      PN_FINANCE_MANAGER_Popups.open(pn_finance_manager_popup_element, {
        beforeShow: function(instance, popup) {
          var ajax_url = pn_finance_manager_ajax.ajax_url;
          var data = {
            action: 'pn_finance_manager_ajax',
            pn_finance_manager_ajax_type: pn_finance_manager_ajax_type,
            pn_finance_manager_ajax_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce,
            pn_finance_manager_get_nonce: pn_finance_manager_action.pn_finance_manager_get_nonce,
            pn_finance_manager_asset_id: pn_finance_manager_asset_id ? pn_finance_manager_asset_id : '',
            pn_finance_manager_liability_id: pn_finance_manager_liability_id ? pn_finance_manager_liability_id : '',
          };

          // Log the data being sent
          $.ajax({
            url: ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
              try {               
                // Check if response is already an object (parsed JSON)
                var response_json = typeof response === 'object' ? response : null;
                
                // If not an object, try to parse as JSON
                if (!response_json) {
                  try {
                    response_json = JSON.parse(response);
                  } catch (parseError) {
                    // If parsing fails, assume it's HTML content
                    pn_finance_manager_popup_element.find('.pn-finance-manager-popup-content').html(response);

                    // Initialize media uploaders if function exists
                    if (typeof initMediaUpload === 'function') {
                      $('.pn-finance-manager-image-upload-wrapper').each(function() {
                        initMediaUpload($(this), 'image');
                      });
                      $('.pn-finance-manager-audio-upload-wrapper').each(function() {
                        initMediaUpload($(this), 'audio');
                      });
                      $('.pn-finance-manager-video-upload-wrapper').each(function() {
                        initMediaUpload($(this), 'video');
                      });
                    }
                    return;
                  }
                }

                // Handle JSON response
                if (response_json.error_key) {
                  var errorMessage = response_json.error_message || pn_finance_manager_i18n.an_error_has_occurred;
                  pn_finance_manager_get_main_message(errorMessage);
                  return;
                }

                // Handle successful JSON response with HTML content
                if (response_json.html) {
                  pn_finance_manager_popup_element.find('.pn-finance-manager-popup-content').html(response_json.html);

                  // Initialize media uploaders if function exists
                  if (typeof initMediaUpload === 'function') {
                    $('.pn-finance-manager-image-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'image');
                    });
                    $('.pn-finance-manager-audio-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'audio');
                    });
                    $('.pn-finance-manager-video-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'video');
                    });
                  }
                } else {
                  pn_finance_manager_get_main_message(pn_finance_manager_i18n.an_error_has_occurred);
                }
              } catch (e) {
                pn_finance_manager_get_main_message(pn_finance_manager_i18n.an_error_has_occurred);
              }
            },
            error: function(xhr, status, error) {
              pn_finance_manager_get_main_message(pn_finance_manager_i18n.an_error_has_occurred);
            }
          });
        },
        afterClose: function() {
          pn_finance_manager_popup_element.find('.pn-finance-manager-popup-content').html('<div class="pn-finance-manager-loader-circle-wrapper"><div class="pn-finance-manager-text-align-center"><div class="pn-finance-manager-loader-circle"><div></div><div></div><div></div><div></div></div></div></div>');
        },
      });
    });

    // Event listener for simple popups (non-AJAX)
    $(document).on('click', '.pn-finance-manager-popup-open', function(e) {
      e.preventDefault();

      var pn_finance_manager_btn = $(this);
      var pn_finance_manager_popup_element = $('#' + pn_finance_manager_btn.attr('data-pn-finance-manager-popup-id'));

      if (pn_finance_manager_popup_element.length) {
        // Store the item ID on the popup for remove handlers
        Object.keys(pn_finance_manager_cpts).forEach(function(cpt) {
          var cpt_full = cpt.replace('pnfm_', 'pn_finance_manager_');
          var cpt_base = cpt.replace('pnfm_', '');
          var item = pn_finance_manager_btn.closest('.pn-finance-manager-' + cpt_base);
          if (item.length) {
            pn_finance_manager_popup_element.attr('data-pn-finance-manager-remove-id', item.attr('data-' + cpt_full + '-id'));
          }
        });

        PN_FINANCE_MANAGER_Popups.open(pn_finance_manager_popup_element);
      }
    });

    // Generate event listeners for duplicate and remove functions based on CPTs
    var pn_finance_manager_cpts_mapping = {
      'pnfm_asset': 'assets',
      'pnfm_liability': 'liabilities'
    };

    // Loop through CPTs to create duplicate event listeners
    Object.keys(pn_finance_manager_cpts).forEach(function(cpt) {
      // cpt = 'pnfm_asset' → cpt_full = 'pn_finance_manager_asset', cpt_base = 'asset'
      var cpt_full = cpt.replace('pnfm_', 'pn_finance_manager_');
      var cpt_base = cpt.replace('pnfm_', '');
      var container_class = '.pn-finance-manager-' + pn_finance_manager_cpts_mapping[cpt];

      // Duplicate event listener
      $(document).on('click', '.pn-finance-manager-' + cpt_full + '-duplicate-post', function(e) {
        e.preventDefault();

        $(container_class).fadeOut('fast');
        var pn_finance_manager_btn = $(this);
        var pn_finance_manager_id = pn_finance_manager_btn.closest('.pn-finance-manager-' + cpt_base).attr('data-' + cpt_full + '-id');

        var ajax_url = pn_finance_manager_ajax.ajax_url;
        var data = {
          action: 'pn_finance_manager_ajax',
          pn_finance_manager_ajax_type: cpt_full + '_duplicate',
          [cpt_full + '_id']: pn_finance_manager_id,
          pn_finance_manager_ajax_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce,
        };

        $.post(ajax_url, data, function(response) {
          var response_json = $.parseJSON(response);

          if (response_json['error_key'] != '') {
            pn_finance_manager_get_main_message(response_json['error_content']);
          }else{
            $(container_class).html(response_json['html']);
          }

          if (response_json['statistics_html']) {
            $('.pn-finance-manager-' + cpt + '-statistics').html(response_json['statistics_html']);
            if (typeof pnFinanceManagerInitCharts === 'function') {
              pnFinanceManagerInitCharts();
            }
          }

          $(container_class).fadeIn('slow');
          $('.pn-finance-manager-menu-more-overlay').fadeOut('fast');
        });
      });

      // Remove event listener (for popup button)
      $(document).on('click', '.pn-finance-manager-' + cpt + '-remove', function(e) {
        e.preventDefault();

        $(container_class).fadeOut('fast');
        // Get the item ID from the popup (stored when popup was opened), fallback to menu-more
        var removePopup = $(this).closest('.pn-finance-manager-popup');
        var pn_finance_manager_id = removePopup.attr('data-pn-finance-manager-remove-id');
        if (!pn_finance_manager_id) {
          pn_finance_manager_id = $('.pn-finance-manager-menu-more.pn-finance-manager-active').closest('.pn-finance-manager-' + cpt_base).attr('data-' + cpt_full + '-id');
        }

        var ajax_url = pn_finance_manager_ajax.ajax_url;
        var data = {
          action: 'pn_finance_manager_ajax',
          pn_finance_manager_ajax_type: cpt_full + '_remove',
          [cpt_full + '_id']: pn_finance_manager_id,
          pn_finance_manager_ajax_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce,
        };

        $.post(ajax_url, data, function(response) {
          var response_json = $.parseJSON(response);

          if (response_json['error_key'] != '') {
            pn_finance_manager_get_main_message(response_json['error_content']);
          }else{
            $(container_class).html(response_json['html']);
            pn_finance_manager_get_main_message(pn_finance_manager_i18n.removed_successfully);
          }

          if (response_json['statistics_html']) {
            $('.pn-finance-manager-' + cpt + '-statistics').html(response_json['statistics_html']);
            if (typeof pnFinanceManagerInitCharts === 'function') {
              pnFinanceManagerInitCharts();
            }
          }

          $(container_class).fadeIn('slow');
          $('.pn-finance-manager-menu-more-overlay').fadeOut('fast');

          PN_FINANCE_MANAGER_Popups.close();
        });
      });
    });
  });
})(jQuery);
