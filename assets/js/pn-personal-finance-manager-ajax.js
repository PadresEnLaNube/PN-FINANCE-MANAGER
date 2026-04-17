(function($) {
  'use strict';

  $(document).ready(function() {
    $(document).on('submit', '.pn-personal-finance-manager-form', function(e){
      var pn_personal_finance_manager_form = $(this);
      var pn_personal_finance_manager_btn = pn_personal_finance_manager_form.find('input[type="submit"]');
      pn_personal_finance_manager_btn.addClass('pn-personal-finance-manager-link-disabled').siblings('.pn-personal-finance-manager-waiting').removeClass('pn-personal-finance-manager-display-none');

      var ajax_url = pn_personal_finance_manager_ajax.ajax_url;
      var data = {
        action: 'pn_personal_finance_manager_ajax_nopriv',
        pn_personal_finance_manager_ajax_nopriv_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
        pn_personal_finance_manager_get_nonce: pn_personal_finance_manager_action.pn_personal_finance_manager_get_nonce,
        pn_personal_finance_manager_ajax_nopriv_type: 'pn_personal_finance_manager_form_save',
        pn_personal_finance_manager_form_id: pn_personal_finance_manager_form.attr('id'),
        pn_personal_finance_manager_form_type: pn_personal_finance_manager_btn.attr('data-pn-personal-finance-manager-type'),
        pn_personal_finance_manager_form_subtype: pn_personal_finance_manager_btn.attr('data-pn-personal-finance-manager-subtype'),
        pn_personal_finance_manager_form_user_id: pn_personal_finance_manager_btn.attr('data-pn-personal-finance-manager-user-id'),
        pn_personal_finance_manager_form_post_id: pn_personal_finance_manager_btn.attr('data-pn-personal-finance-manager-post-id'),
        pn_personal_finance_manager_form_post_type: pn_personal_finance_manager_btn.attr('data-pn-personal-finance-manager-post-type'),
        pn_personal_finance_manager_ajax_keys: [],
      };
      console.log('data');console.log(data);

      if (!(typeof window['pn_personal_finance_manager_window_vars'] !== 'undefined')) {
        window['pn_personal_finance_manager_window_vars'] = [];
      }

      $(pn_personal_finance_manager_form.find('input:not([type="submit"]), select, textarea')).each(function(index, element) {
        var is_multiple = $(this).attr('multiple');
        
        if (is_multiple) {
          if (!(typeof window['pn_personal_finance_manager_window_vars']['form_field_' + element.name] !== 'undefined')) {
            window['pn_personal_finance_manager_window_vars']['form_field_' + element.name] = [];
          }

          window['pn_personal_finance_manager_window_vars']['form_field_' + element.name].push($(element).val());

          data[element.name] = window['pn_personal_finance_manager_window_vars']['form_field_' + element.name];
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

        data.pn_personal_finance_manager_ajax_keys.push({
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

        if (response_json['error_key'] == 'pn_personal_finance_manager_form_save_error_unlogged') {
          pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_i18n.user_unlogged);

          if (!$('.userspn-profile-wrapper .user-unlogged').length) {
            $('.userspn-profile-wrapper').prepend('<div class="userspn-alert userspn-alert-warning user-unlogged">' + pn_personal_finance_manager_i18n.user_unlogged + '</div>');
          }

          PN_PERSONAL_FINANCE_MANAGER_Popups.open($('#userspn-profile-popup'));
          $('#userspn-login input#user_login').focus();
        }else if (response_json['error_key'] != '') {
          pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_i18n.an_error_has_occurred);
        }else {
          pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_i18n.saved_successfully);
        }

        if (response_json['update_list']) {
          $('.pn-personal-finance-manager-' + data.pn_personal_finance_manager_form_post_type + '-list').html(response_json['update_html']);
        }

        if (response_json['statistics_html']) {
          $('.pn-personal-finance-manager-' + data.pn_personal_finance_manager_form_post_type + '-statistics').html(response_json['statistics_html']);
          if (typeof pnFinanceManagerInitCharts === 'function') {
            pnFinanceManagerInitCharts();
          }
        }

        if (response_json['popup_close']) {
          PN_PERSONAL_FINANCE_MANAGER_Popups.close();
          $('.pn-personal-finance-manager-menu-more-overlay').fadeOut('fast');
        }

        pn_personal_finance_manager_btn.removeClass('pn-personal-finance-manager-link-disabled').siblings('.pn-personal-finance-manager-waiting').addClass('pn-personal-finance-manager-display-none')
      });

      delete window['pn_personal_finance_manager_window_vars'];
      return false;
    });

    $(document).on('click', '.pn-personal-finance-manager-popup-open-ajax', function(e) {
      e.preventDefault();

      var pn_personal_finance_manager_btn = $(this);
      var pn_personal_finance_manager_ajax_type = pn_personal_finance_manager_btn.attr('data-pn-personal-finance-manager-ajax-type');
      var pn_personal_finance_manager_asset_id = pn_personal_finance_manager_btn.closest('.pn-personal-finance-manager-asset').attr('data-pn_personal_finance_manager_asset-id');
      var pn_personal_finance_manager_liability_id = pn_personal_finance_manager_btn.closest('.pn-personal-finance-manager-liability').attr('data-pn_personal_finance_manager_liability-id');
      var pn_personal_finance_manager_popup_element = $('#' + pn_personal_finance_manager_btn.attr('data-pn-personal-finance-manager-popup-id'));

      PN_PERSONAL_FINANCE_MANAGER_Popups.open(pn_personal_finance_manager_popup_element, {
        beforeShow: function(instance, popup) {
          var ajax_url = pn_personal_finance_manager_ajax.ajax_url;
          var data = {
            action: 'pn_personal_finance_manager_ajax',
            pn_personal_finance_manager_ajax_type: pn_personal_finance_manager_ajax_type,
            pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
            pn_personal_finance_manager_get_nonce: pn_personal_finance_manager_action.pn_personal_finance_manager_get_nonce,
            pn_personal_finance_manager_asset_id: pn_personal_finance_manager_asset_id ? pn_personal_finance_manager_asset_id : '',
            pn_personal_finance_manager_liability_id: pn_personal_finance_manager_liability_id ? pn_personal_finance_manager_liability_id : '',
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
                    pn_personal_finance_manager_popup_element.find('.pn-personal-finance-manager-popup-content').html(response);

                    // Initialize media uploaders if function exists
                    if (typeof initMediaUpload === 'function') {
                      $('.pn-personal-finance-manager-image-upload-wrapper').each(function() {
                        initMediaUpload($(this), 'image');
                      });
                      $('.pn-personal-finance-manager-audio-upload-wrapper').each(function() {
                        initMediaUpload($(this), 'audio');
                      });
                      $('.pn-personal-finance-manager-video-upload-wrapper').each(function() {
                        initMediaUpload($(this), 'video');
                      });
                    }
                    return;
                  }
                }

                // Handle JSON response
                if (response_json.error_key) {
                  var errorMessage = response_json.error_message || pn_personal_finance_manager_i18n.an_error_has_occurred;
                  pn_personal_finance_manager_get_main_message(errorMessage);
                  return;
                }

                // Handle successful JSON response with HTML content
                if (response_json.html) {
                  pn_personal_finance_manager_popup_element.find('.pn-personal-finance-manager-popup-content').html(response_json.html);

                  // Initialize media uploaders if function exists
                  if (typeof initMediaUpload === 'function') {
                    $('.pn-personal-finance-manager-image-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'image');
                    });
                    $('.pn-personal-finance-manager-audio-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'audio');
                    });
                    $('.pn-personal-finance-manager-video-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'video');
                    });
                  }
                } else {
                  pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_i18n.an_error_has_occurred);
                }
              } catch (e) {
                pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_i18n.an_error_has_occurred);
              }
            },
            error: function(xhr, status, error) {
              pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_i18n.an_error_has_occurred);
            }
          });
        },
        afterClose: function() {
          pn_personal_finance_manager_popup_element.find('.pn-personal-finance-manager-popup-content').html('<div class="pn-personal-finance-manager-loader-circle-wrapper"><div class="pn-personal-finance-manager-text-align-center"><div class="pn-personal-finance-manager-loader-circle"><div></div><div></div><div></div><div></div></div></div></div>');
        },
      });
    });

    // Event listener for simple popups (non-AJAX)
    $(document).on('click', '.pn-personal-finance-manager-popup-open', function(e) {
      e.preventDefault();

      var pn_personal_finance_manager_btn = $(this);
      var pn_personal_finance_manager_popup_element = $('#' + pn_personal_finance_manager_btn.attr('data-pn-personal-finance-manager-popup-id'));

      if (pn_personal_finance_manager_popup_element.length) {
        // Store the item ID on the popup for remove handlers
        Object.keys(pn_personal_finance_manager_cpts).forEach(function(cpt) {
          var cpt_full = cpt.replace('pnpfm_', 'pn_personal_finance_manager_');
          var cpt_base = cpt.replace('pnpfm_', '');
          var item = pn_personal_finance_manager_btn.closest('.pn-personal-finance-manager-' + cpt_base);
          if (item.length) {
            pn_personal_finance_manager_popup_element.attr('data-pn-personal-finance-manager-remove-id', item.attr('data-' + cpt_full + '-id'));
          }
        });

        PN_PERSONAL_FINANCE_MANAGER_Popups.open(pn_personal_finance_manager_popup_element);
      }
    });

    // Generate event listeners for duplicate and remove functions based on CPTs
    var pn_personal_finance_manager_cpts_mapping = {
      'pnpfm_asset': 'assets',
      'pnpfm_liability': 'liabilities'
    };

    // Loop through CPTs to create duplicate event listeners
    Object.keys(pn_personal_finance_manager_cpts).forEach(function(cpt) {
      // cpt = 'pnpfm_asset' → cpt_full = 'pn_personal_finance_manager_asset', cpt_base = 'asset'
      var cpt_full = cpt.replace('pnpfm_', 'pn_personal_finance_manager_');
      var cpt_base = cpt.replace('pnpfm_', '');
      var container_class = '.pn-personal-finance-manager-' + pn_personal_finance_manager_cpts_mapping[cpt];

      // Duplicate event listener
      $(document).on('click', '.pn-personal-finance-manager-' + cpt_full + '-duplicate-post', function(e) {
        e.preventDefault();

        $(container_class).fadeOut('fast');
        var pn_personal_finance_manager_btn = $(this);
        var pn_personal_finance_manager_id = pn_personal_finance_manager_btn.closest('.pn-personal-finance-manager-' + cpt_base).attr('data-' + cpt_full + '-id');

        var ajax_url = pn_personal_finance_manager_ajax.ajax_url;
        var data = {
          action: 'pn_personal_finance_manager_ajax',
          pn_personal_finance_manager_ajax_type: cpt_full + '_duplicate',
          [cpt_full + '_id']: pn_personal_finance_manager_id,
          pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
        };

        $.post(ajax_url, data, function(response) {
          var response_json = $.parseJSON(response);

          if (response_json['error_key'] != '') {
            pn_personal_finance_manager_get_main_message(response_json['error_content']);
          }else{
            $(container_class).html(response_json['html']);
          }

          if (response_json['statistics_html']) {
            $('.pn-personal-finance-manager-' + cpt + '-statistics').html(response_json['statistics_html']);
            if (typeof pnFinanceManagerInitCharts === 'function') {
              pnFinanceManagerInitCharts();
            }
          }

          $(container_class).fadeIn('slow');
          $('.pn-personal-finance-manager-menu-more-overlay').fadeOut('fast');
        });
      });

      // Remove event listener (for popup button)
      $(document).on('click', '.pn-personal-finance-manager-' + cpt + '-remove', function(e) {
        e.preventDefault();

        $(container_class).fadeOut('fast');
        // Get the item ID from the popup (stored when popup was opened), fallback to menu-more
        var removePopup = $(this).closest('.pn-personal-finance-manager-popup');
        var pn_personal_finance_manager_id = removePopup.attr('data-pn-personal-finance-manager-remove-id');
        if (!pn_personal_finance_manager_id) {
          pn_personal_finance_manager_id = $('.pn-personal-finance-manager-menu-more.pn-personal-finance-manager-active').closest('.pn-personal-finance-manager-' + cpt_base).attr('data-' + cpt_full + '-id');
        }

        var ajax_url = pn_personal_finance_manager_ajax.ajax_url;
        var data = {
          action: 'pn_personal_finance_manager_ajax',
          pn_personal_finance_manager_ajax_type: cpt_full + '_remove',
          [cpt_full + '_id']: pn_personal_finance_manager_id,
          pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
        };

        $.post(ajax_url, data, function(response) {
          var response_json = $.parseJSON(response);

          if (response_json['error_key'] != '') {
            pn_personal_finance_manager_get_main_message(response_json['error_content']);
          }else{
            $(container_class).html(response_json['html']);
            pn_personal_finance_manager_get_main_message(pn_personal_finance_manager_i18n.removed_successfully);
          }

          if (response_json['statistics_html']) {
            $('.pn-personal-finance-manager-' + cpt + '-statistics').html(response_json['statistics_html']);
            if (typeof pnFinanceManagerInitCharts === 'function') {
              pnFinanceManagerInitCharts();
            }
          }

          $(container_class).fadeIn('slow');
          $('.pn-personal-finance-manager-menu-more-overlay').fadeOut('fast');

          PN_PERSONAL_FINANCE_MANAGER_Popups.close();
        });
      });
    });
  });
})(jQuery);
