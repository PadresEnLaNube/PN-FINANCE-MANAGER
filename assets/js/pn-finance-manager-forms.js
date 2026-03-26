(function($) {
  'use strict';

  $(document).ready(function() {
    if ($('.pn-finance-manager-password-checker').length) {
      var pass_view_state = false;

      function pn_finance_manager_pass_check_strength(pass) {
        var strength = 0;
        var password = $('.pn-finance-manager-password-strength');
        var low_upper_case = password.closest('.pn-finance-manager-password-checker').find('.low-upper-case i');
        var number = password.closest('.pn-finance-manager-password-checker').find('.one-number i');
        var special_char = password.closest('.pn-finance-manager-password-checker').find('.one-special-char i');
        var eight_chars = password.closest('.pn-finance-manager-password-checker').find('.eight-character i');

        //If pass contains both lower and uppercase characters
        if (pass.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
          strength += 1;
          low_upper_case.text('task_alt');
        } else {
          low_upper_case.text('radio_button_unchecked');
        }

        //If it has numbers and characters
        if (pass.match(/([0-9])/)) {
          strength += 1;
          number.text('task_alt');
        } else {
          number.text('radio_button_unchecked');
        }

        //If it has one special character
        if (pass.match(/([!,%,&,@,#,$,^,*,?,_,~,|,¬,+,ç,-,€])/)) {
          strength += 1;
          special_char.text('task_alt');
        } else {
          special_char.text('radio_button_unchecked');
        }

        //If pass is greater than 7
        if (pass.length > 7) {
          strength += 1;
          eight_chars.text('task_alt');
        } else {
          eight_chars.text('radio_button_unchecked');
        }

        // If value is less than 2
        if (strength < 2) {
          $('.pn-finance-manager-password-strength-bar').removeClass('pn-finance-manager-progress-bar-warning pn-finance-manager-progress-bar-success').addClass('pn-finance-manager-progress-bar-danger').css('width', '10%');
        } else if (strength == 3) {
          $('.pn-finance-manager-password-strength-bar').removeClass('pn-finance-manager-progress-bar-success pn-finance-manager-progress-bar-danger').addClass('pn-finance-manager-progress-bar-warning').css('width', '60%');
        } else if (strength == 4) {
          $('.pn-finance-manager-password-strength-bar').removeClass('pn-finance-manager-progress-bar-warning pn-finance-manager-progress-bar-danger').addClass('pn-finance-manager-progress-bar-success').css('width', '100%');
        }
      }

      $(document).on('click', '.pn-finance-manager-show-pass', function(e){
        e.preventDefault();
        var pn_finance_manager_btn = $(this);
        var password_input = pn_finance_manager_btn.siblings('.pn-finance-manager-password-strength');

        if (pass_view_state) {
          password_input.attr('type', 'password');
          pn_finance_manager_btn.find('i').text('visibility');
          pass_view_state = false;
        } else {
          password_input.attr('type', 'text');
          pn_finance_manager_btn.find('i').text('visibility_off');
          pass_view_state = true;
        }
      });

      $(document).on('keyup', ('.pn-finance-manager-password-strength'), function(e){
        pn_finance_manager_pass_check_strength($('.pn-finance-manager-password-strength').val());

        if (!$('#pn-finance-manager-popover-pass').is(':visible')) {
          $('#pn-finance-manager-popover-pass').fadeIn('slow');
        }

        if (!$('.pn-finance-manager-show-pass').is(':visible')) {
          $('.pn-finance-manager-show-pass').fadeIn('slow');
        }
      });
    }
    
    $(document).on('mouseover', '.pn-finance-manager-input-star', function(e){
      if (!$(this).closest('.pn-finance-manager-input-stars').hasClass('clicked')) {
        $(this).text('star');
        $(this).prevAll('.pn-finance-manager-input-star').text('star');
      }
    });

    $(document).on('mouseout', '.pn-finance-manager-input-stars', function(e){
      if (!$(this).hasClass('clicked')) {
        $(this).find('.pn-finance-manager-input-star').text('star_outlined');
      }
    });

    $(document).on('click', '.pn-finance-manager-input-star', function(e){
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      $(this).closest('.pn-finance-manager-input-stars').addClass('clicked');
      $(this).closest('.pn-finance-manager-input-stars').find('.pn-finance-manager-input-star').text('star_outlined');
      $(this).text('star');
      $(this).prevAll('.pn-finance-manager-input-star').text('star');
      $(this).closest('.pn-finance-manager-input-stars').siblings('.pn-finance-manager-input-hidden-stars').val($(this).prevAll('.pn-finance-manager-input-star').length + 1);
    });

    $(document).on('change', '.pn-finance-manager-input-hidden-stars', function(e){
      $(this).siblings('.pn-finance-manager-input-stars').find('.pn-finance-manager-input-star').text('star_outlined');
      $(this).siblings('.pn-finance-manager-input-stars').find('.pn-finance-manager-input-star').slice(0, $(this).val()).text('star');
    });

    if ($('.pn-finance-manager-field[data-pn-finance-manager-parent]').length) {
      pn_finance_manager_form_update();

      $(document).on('change', '.pn-finance-manager-field[data-pn-finance-manager-parent~="this"]', function(e) {
        pn_finance_manager_form_update();
      });
    }

    if ($('.pn-finance-manager-html-multi-group').length) {
      $(document).on('click', '.pn-finance-manager-html-multi-remove-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var pn_finance_manager_users_btn = $(this);

        if (pn_finance_manager_users_btn.closest('.pn-finance-manager-html-multi-wrapper').find('.pn-finance-manager-html-multi-group').length > 1) {
          $(this).closest('.pn-finance-manager-html-multi-group').remove();
        } else {
          $(this).closest('.pn-finance-manager-html-multi-group').find('input, select, textarea').val('');
        }
      });

      $(document).on('click', '.pn-finance-manager-html-multi-add-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        $(this).closest('.pn-finance-manager-html-multi-wrapper').find('.pn-finance-manager-html-multi-group:first').clone().insertAfter($(this).closest('.pn-finance-manager-html-multi-wrapper').find('.pn-finance-manager-html-multi-group:last'));
        $(this).closest('.pn-finance-manager-html-multi-wrapper').find('.pn-finance-manager-html-multi-group:last').find('input, select, textarea').val('');

        $(this).closest('.pn-finance-manager-html-multi-wrapper').find('.pn-finance-manager-input-range').each(function(index, element) {
          $(this).siblings('.pn-finance-manager-input-range-output').html($(this).val());
        });
      });

      $('.pn-finance-manager-html-multi-wrapper').sortable({handle: '.pn-finance-manager-multi-sorting'});

      $(document).on('sortstop', '.pn-finance-manager-html-multi-wrapper', function(event, ui){
        pn_finance_manager_get_main_message(pn_finance_manager_i18n.ordered_element);
      });
    }

    if ($('.pn-finance-manager-input-range').length) {
      $('.pn-finance-manager-input-range').each(function(index, element) {
        $(this).siblings('.pn-finance-manager-input-range-output').html($(this).val());
      });

      $(document).on('input', '.pn-finance-manager-input-range', function(e) {
        $(this).siblings('.pn-finance-manager-input-range-output').html($(this).val());
      });
    }

    if ($('.pn-finance-manager-image-btn').length) {
      var image_frame;

      $(document).on('click', '.pn-finance-manager-image-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (image_frame){
          image_frame.open();
          return;
        }

        var pn_finance_manager_input_btn = $(this);
        var pn_finance_manager_images_block = pn_finance_manager_input_btn.closest('.pn-finance-manager-images-block').find('.pn-finance-manager-images');
        var pn_finance_manager_images_input = pn_finance_manager_input_btn.closest('.pn-finance-manager-images-block').find('.pn-finance-manager-image-input');

        var image_frame = wp.media({
          title: (pn_finance_manager_images_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.select_images : pn_finance_manager_i18n.select_image,
          library: {
            type: 'image'
          },
          multiple: (pn_finance_manager_images_block.attr('data-pn-finance-manager-multiple') == 'true') ? 'true' : 'false',
        });

        image_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (pn_finance_manager_images_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.edit_images : pn_finance_manager_i18n.edit_image,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(image_frame.options.library),
            multiple: (pn_finance_manager_images_block.attr('data-pn-finance-manager-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        image_frame.open();

        image_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = image_frame.state().get('selection').toJSON();
          pn_finance_manager_images_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            pn_finance_manager_images_block.append('<img src="' + $(this)[0].url + '" class="">');
          });

          pn_finance_manager_input_btn.text((pn_finance_manager_images_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.select_images : pn_finance_manager_i18n.select_image);
          pn_finance_manager_images_input.val(ids);
        });
      });
    }

    if ($('.pn-finance-manager-audio-btn').length) {
      var audio_frame;

      $(document).on('click', '.pn-finance-manager-audio-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (audio_frame){
          audio_frame.open();
          return;
        }

        var pn_finance_manager_input_btn = $(this);
        var pn_finance_manager_audios_block = pn_finance_manager_input_btn.closest('.pn-finance-manager-audios-block').find('.pn-finance-manager-audios');
        var pn_finance_manager_audios_input = pn_finance_manager_input_btn.closest('.pn-finance-manager-audios-block').find('.pn-finance-manager-audio-input');

        var audio_frame = wp.media({
          title: (pn_finance_manager_audios_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.select_audios : pn_finance_manager_i18n.select_audio,
          library : {
            type : 'audio'
          },
          multiple: (pn_finance_manager_audios_block.attr('data-pn-finance-manager-multiple') == 'true') ? 'true' : 'false',
        });

        audio_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (pn_finance_manager_audios_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.select_audios : pn_finance_manager_i18n.select_audio,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(audio_frame.options.library),
            multiple: (pn_finance_manager_audios_block.attr('data-pn-finance-manager-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        audio_frame.open();

        audio_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = audio_frame.state().get('selection').toJSON();
          pn_finance_manager_audios_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            pn_finance_manager_audios_block.append('<div class="pn-finance-manager-audio pn-finance-manager-tooltip" title="' + $(this)[0].title + '"><i class="dashicons dashicons-media-audio"></i></div>');
          });

          $('.pn-finance-manager-tooltip').tooltipster({maxWidth: 300,delayTouch:[0, 4000], customClass: 'pn-finance-manager-tooltip'});
          pn_finance_manager_input_btn.text((pn_finance_manager_audios_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.select_audios : pn_finance_manager_i18n.select_audio);
          pn_finance_manager_audios_input.val(ids);
        });
      });
    }

    if ($('.pn-finance-manager-video-btn').length) {
      var video_frame;

      $(document).on('click', '.pn-finance-manager-video-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (video_frame){
          video_frame.open();
          return;
        }

        var pn_finance_manager_input_btn = $(this);
        var pn_finance_manager_videos_block = pn_finance_manager_input_btn.closest('.pn-finance-manager-videos-block').find('.pn-finance-manager-videos');
        var pn_finance_manager_videos_input = pn_finance_manager_input_btn.closest('.pn-finance-manager-videos-block').find('.pn-finance-manager-video-input');

        var video_frame = wp.media({
          title: (pn_finance_manager_videos_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.select_videos : pn_finance_manager_i18n.select_video,
          library : {
            type : 'video'
          },
          multiple: (pn_finance_manager_videos_block.attr('data-pn-finance-manager-multiple') == 'true') ? 'true' : 'false',
        });

        video_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (pn_finance_manager_videos_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.select_videos : pn_finance_manager_i18n.select_video,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(video_frame.options.library),
            multiple: (pn_finance_manager_videos_block.attr('data-pn-finance-manager-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        video_frame.open();

        video_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = video_frame.state().get('selection').toJSON();
          pn_finance_manager_videos_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            pn_finance_manager_videos_block.append('<div class="pn-finance-manager-video pn-finance-manager-tooltip" title="' + $(this)[0].title + '"><i class="dashicons dashicons-media-video"></i></div>');
          });

          $('.pn-finance-manager-tooltip').tooltipster({maxWidth: 300,delayTouch:[0, 4000], customClass: 'pn-finance-manager-tooltip'});
          pn_finance_manager_input_btn.text((pn_finance_manager_videos_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.select_videos : pn_finance_manager_i18n.select_video);
          pn_finance_manager_videos_input.val(ids);
        });
      });
    }

    if ($('.pn-finance-manager-file-btn').length) {
      var file_frame;

      $(document).on('click', '.pn-finance-manager-file-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (file_frame){
          file_frame.open();
          return;
        }

        var pn_finance_manager_input_btn = $(this);
        var pn_finance_manager_files_block = pn_finance_manager_input_btn.closest('.pn-finance-manager-files-block').find('.pn-finance-manager-files');
        var pn_finance_manager_files_input = pn_finance_manager_input_btn.closest('.pn-finance-manager-files-block').find('.pn-finance-manager-file-input');

        var file_frame = wp.media({
          title: (pn_finance_manager_files_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.select_files : pn_finance_manager_i18n.select_file,
          multiple: (pn_finance_manager_files_block.attr('data-pn-finance-manager-multiple') == 'true') ? 'true' : 'false',
        });

        file_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (pn_finance_manager_files_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.select_files : pn_finance_manager_i18n.select_file,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(file_frame.options.library),
            multiple: (pn_finance_manager_files_block.attr('data-pn-finance-manager-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        file_frame.open();

        file_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = file_frame.state().get('selection').toJSON();
          pn_finance_manager_files_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            pn_finance_manager_files_block.append('<embed src="' + $(this)[0].url + '" type="application/pdf" class="pn-finance-manager-embed-file"/>');
          });

          pn_finance_manager_input_btn.text((pn_finance_manager_files_block.attr('data-pn-finance-manager-multiple') == 'true') ? pn_finance_manager_i18n.edit_files : pn_finance_manager_i18n.edit_file);
          pn_finance_manager_files_input.val(ids);
        });
      });
    }
  });

  $(document).on('click', '.pn-finance-manager-toggle', function(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    var pn_finance_manager_toggle = $(this);

    if (pn_finance_manager_toggle.find('i').length) {
      if (pn_finance_manager_toggle.siblings('.pn-finance-manager-toggle-content').is(':visible')) {
        pn_finance_manager_toggle.find('i').text('add');
      } else {
        pn_finance_manager_toggle.find('i').text('clear');
      }
    }

    pn_finance_manager_toggle.siblings('.pn-finance-manager-toggle-content').fadeToggle();
  });

  // Role management functionality
  function pn_finance_manager_handle_role_action(action, btn) {
    var wrapper = btn.closest('.pn-finance-manager-user-role-selector-wrapper');
    var select = wrapper.find('.pn-finance-manager-user-role-select');
    var selectedUsers = select.val();
    var role = select.data('role');
    var roleLabel = select.data('role-label');
    var nonce = wrapper.find('.pn-finance-manager-role-nonce').val();
    var messageDiv = wrapper.find('.pn-finance-manager-role-message');

    if (!selectedUsers || selectedUsers.length === 0) {
      messageDiv.removeClass('pn-finance-manager-display-none-soft').html('<p class="pn-finance-manager-color-error"><i class="material-icons-outlined pn-finance-manager-vertical-align-middle">warning</i> Please select at least one user.</p>');
      return;
    }

    var confirmMsg = action === 'assign'
      ? 'Are you sure you want to assign the ' + roleLabel + ' role to ' + selectedUsers.length + ' user(s)?'
      : 'Are you sure you want to remove the ' + roleLabel + ' role from ' + selectedUsers.length + ' user(s)?';

    if (!confirm(confirmMsg)) return;

    btn.prop('disabled', true);

    $.ajax({
      url: pn_finance_manager_ajax.ajax_url,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'pn_finance_manager_ajax',
        pn_finance_manager_ajax_type: 'pn_finance_manager_update_user_role',
        pn_finance_manager_ajax_nonce: pn_finance_manager_ajax.pn_finance_manager_ajax_nonce,
        role_action: action,
        role: role,
        user_ids: selectedUsers,
        role_nonce: nonce
      },
      success: function(response) {
        if (typeof response === 'string') {
          try { response = JSON.parse(response); } catch(e) { return; }
        }
        if (response.error_key === '') {
          messageDiv.removeClass('pn-finance-manager-display-none-soft').html('<p class="pn-finance-manager-color-success"><i class="material-icons-outlined pn-finance-manager-vertical-align-middle">check_circle</i> ' + response.error_content + '</p>');
          setTimeout(function() { location.reload(); }, 1500);
        } else {
          messageDiv.removeClass('pn-finance-manager-display-none-soft').html('<p class="pn-finance-manager-color-error"><i class="material-icons-outlined pn-finance-manager-vertical-align-middle">error</i> ' + response.error_content + '</p>');
          btn.prop('disabled', false);
        }
      },
      error: function() {
        messageDiv.removeClass('pn-finance-manager-display-none-soft').html('<p class="pn-finance-manager-color-error"><i class="material-icons-outlined pn-finance-manager-vertical-align-middle">error</i> An error occurred. Please try again.</p>');
        btn.prop('disabled', false);
      }
    });
  }

  $(document).on('click', '.pn-finance-manager-assign-role-btn', function(e) {
    e.preventDefault();
    pn_finance_manager_handle_role_action('assign', $(this));
  });

  $(document).on('click', '.pn-finance-manager-remove-role-btn', function(e) {
    e.preventDefault();
    pn_finance_manager_handle_role_action('remove', $(this));
  });
})(jQuery);
