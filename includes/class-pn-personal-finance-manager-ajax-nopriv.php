<?php
/**
 * Load the plugin no private Ajax functions.
 *
 * Load the plugin no private Ajax functions to be executed in background.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Ajax_Nopriv {
  /**
   * Load the plugin templates.
   *
   * @since    1.0.0
   */
  public function pn_personal_finance_manager_ajax_nopriv_server() {
    if (array_key_exists('pn_personal_finance_manager_ajax_nopriv_type', $_POST)) {
      if (!array_key_exists('pn_personal_finance_manager_ajax_nopriv_nonce', $_POST)) {
        echo wp_json_encode([
          'error_key' => 'pn_personal_finance_manager_nonce_ajax_nopriv_error_required',
          'error_content' => esc_html(__('Security check failed: Nonce is required.', 'pn-personal-finance-manager')),
        ]);

        exit;
      }

      if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_personal_finance_manager_ajax_nopriv_nonce'])), 'pn-personal-finance-manager-nonce')) {
        echo wp_json_encode([
          'error_key' => 'pn_personal_finance_manager_nonce_ajax_nopriv_error_invalid',
          'error_content' => esc_html(__('Security check failed: Invalid nonce.', 'pn-personal-finance-manager')),
        ]);

        exit;
      }

      $pn_personal_finance_manager_ajax_nopriv_type = PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['pn_personal_finance_manager_ajax_nopriv_type']));
      
      $pn_personal_finance_manager_ajax_keys = !empty($_POST['pn_personal_finance_manager_ajax_keys']) ? array_map(function($key) {
        $sanitized_key = wp_unslash($key);
        return array(
          'id' => sanitize_key($sanitized_key['id']),
          'node' => sanitize_key($sanitized_key['node']),
          'type' => sanitize_key($sanitized_key['type']),
          'multiple' => sanitize_key($sanitized_key['multiple'])
        );
      }, wp_unslash($_POST['pn_personal_finance_manager_ajax_keys'])) : [];

      $pn_personal_finance_manager_key_value = [];

      if (!empty($pn_personal_finance_manager_ajax_keys)) {
        foreach ($pn_personal_finance_manager_ajax_keys as $pn_personal_finance_manager_key) {
          if ($pn_personal_finance_manager_key['multiple'] == 'true') {
            $pn_personal_finance_manager_clear_key = str_replace('[]', '', $pn_personal_finance_manager_key['id']);
            ${$pn_personal_finance_manager_clear_key} = $pn_personal_finance_manager_key_value[$pn_personal_finance_manager_clear_key] = [];

            if (!empty($_POST[$pn_personal_finance_manager_clear_key])) {
              $unslashed_array = wp_unslash($_POST[$pn_personal_finance_manager_clear_key]);
              
              if (!is_array($unslashed_array)) {
                $unslashed_array = array($unslashed_array);
              }

              $sanitized_array = array_map(function($value) use ($pn_personal_finance_manager_key) {
                return PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(
                  $value,
                  $pn_personal_finance_manager_key['node'],
                  $pn_personal_finance_manager_key['type'],
                  $pn_personal_finance_manager_key['field_config'] ?? [],
                );
              }, $unslashed_array);
              
              foreach ($sanitized_array as $multi_key => $multi_value) {
                $final_value = !empty($multi_value) ? $multi_value : '';
                ${$pn_personal_finance_manager_clear_key}[$multi_key] = $pn_personal_finance_manager_key_value[$pn_personal_finance_manager_clear_key][$multi_key] = $final_value;
              }
            } else {
              ${$pn_personal_finance_manager_clear_key} = '';
              $pn_personal_finance_manager_key_value[$pn_personal_finance_manager_clear_key][$multi_key] = '';
            }
          } else {
            $sanitized_key = sanitize_key($pn_personal_finance_manager_key['id']);
            $unslashed_value = !empty($_POST[$sanitized_key]) ? wp_unslash($_POST[$sanitized_key]) : '';
            
            $pn_personal_finance_manager_key_id = !empty($unslashed_value) ? 
              PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(
                $unslashed_value, 
                $pn_personal_finance_manager_key['node'], 
                $pn_personal_finance_manager_key['type'],
                $pn_personal_finance_manager_key['field_config'] ?? [],
              ) : '';
            
              ${$pn_personal_finance_manager_key['id']} = $pn_personal_finance_manager_key_value[$pn_personal_finance_manager_key['id']] = $pn_personal_finance_manager_key_id;
          }
        }
      }

      switch ($pn_personal_finance_manager_ajax_nopriv_type) {
        case 'pn_personal_finance_manager_form_save':
          $pn_personal_finance_manager_form_type = !empty($_POST['pn_personal_finance_manager_form_type']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['pn_personal_finance_manager_form_type'])) : '';

          if (!empty($pn_personal_finance_manager_key_value) && !empty($pn_personal_finance_manager_form_type)) {
            $pn_personal_finance_manager_form_id = !empty($_POST['pn_personal_finance_manager_form_id']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['pn_personal_finance_manager_form_id'])) : 0;
            $pn_personal_finance_manager_form_subtype = !empty($_POST['pn_personal_finance_manager_form_subtype']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['pn_personal_finance_manager_form_subtype'])) : '';
            $user_id = !empty($_POST['pn_personal_finance_manager_form_user_id']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['pn_personal_finance_manager_form_user_id'])) : 0;
            $post_id = !empty($_POST['pn_personal_finance_manager_form_post_id']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['pn_personal_finance_manager_form_post_id'])) : 0;
            $post_type = !empty($_POST['pn_personal_finance_manager_form_post_type']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['pn_personal_finance_manager_form_post_type'])) : '';

            if (($pn_personal_finance_manager_form_type == 'user' && empty($user_id) && !in_array($pn_personal_finance_manager_form_subtype, ['user_alt_new'])) || ($pn_personal_finance_manager_form_type == 'post' && (empty($post_id) && !(!empty($pn_personal_finance_manager_form_subtype) && in_array($pn_personal_finance_manager_form_subtype, ['post_new', 'post_edit'])))) || ($pn_personal_finance_manager_form_type == 'option' && !is_user_logged_in())) {
              session_start();

              $_SESSION['pn_personal_finance_manager_form'] = [];
              $_SESSION['pn_personal_finance_manager_form'][$pn_personal_finance_manager_form_id] = [];
              $_SESSION['pn_personal_finance_manager_form'][$pn_personal_finance_manager_form_id]['form_type'] = $pn_personal_finance_manager_form_type;
              $_SESSION['pn_personal_finance_manager_form'][$pn_personal_finance_manager_form_id]['values'] = $pn_personal_finance_manager_key_value;

              if (!empty($post_id)) {
                $_SESSION['pn_personal_finance_manager_form'][$pn_personal_finance_manager_form_id]['post_id'] = $post_id;
              }

              echo wp_json_encode(['error_key' => 'pn_personal_finance_manager_form_save_error_unlogged', ]);exit;
            }else{
              switch ($pn_personal_finance_manager_form_type) {
                case 'user':
                  if (!in_array($pn_personal_finance_manager_form_subtype, ['user_alt_new'])) {
                    if (empty($user_id)) {
                      if (PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_user_is_admin(get_current_user_id())) {
                        $user_login = !empty($_POST['user_login']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['user_login'])) : 0;
                        $user_password = !empty($_POST['user_password']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['user_password'])) : 0;
                        $user_email = !empty($_POST['user_email']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['user_email'])) : 0;

                        $user_id = PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_user_insert($user_login, $user_password, $user_email);
                      }
                    }

                    if (!empty($user_id)) {
                      // Authorization: only the account owner or an admin may update user meta
                      if (!is_user_logged_in() || (intval($user_id) !== get_current_user_id() && !PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_user_is_admin(get_current_user_id()))) {
                        echo wp_json_encode(['error_key' => 'pn_personal_finance_manager_form_save_error_unauthorized', 'error_content' => esc_html(__('You are not authorized to perform this action.', 'pn-personal-finance-manager'))]);
                        exit;
                      }

                      foreach ($pn_personal_finance_manager_key_value as $pn_personal_finance_manager_key => $pn_personal_finance_manager_value) {
                        // Skip action and ajax type keys
                        if (in_array($pn_personal_finance_manager_key, ['action', 'pn_personal_finance_manager_ajax_nopriv_type'])) {
                          continue;
                        }

                        // Ensure option name is prefixed with pn_personal_finance_manager_
                        if (strpos($pn_personal_finance_manager_key, 'pn_personal_finance_manager_') !== 0) {
                          $pn_personal_finance_manager_key = 'pn_personal_finance_manager_' . $pn_personal_finance_manager_key;
                        }

                        update_user_meta($user_id, $pn_personal_finance_manager_key, $pn_personal_finance_manager_value);
                      }
                    }
                  }

                  do_action('pn_personal_finance_manager_form_save', $user_id, $pn_personal_finance_manager_key_value, $pn_personal_finance_manager_form_type, $pn_personal_finance_manager_form_subtype, '');
                  break;
                case 'post':
                  if (empty($pn_personal_finance_manager_form_subtype) || in_array($pn_personal_finance_manager_form_subtype, ['post_new', 'post_edit'])) {
                    // Skip generic post creation for asset/liability — their type-specific hooks handle it
                    $pn_personal_finance_manager_types_with_own_handlers = ['pnpfm_asset', 'pnpfm_liability'];
                    if (empty($post_id) && !in_array($post_type, $pn_personal_finance_manager_types_with_own_handlers)) {
                      if (PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_user_is_admin(get_current_user_id())) {
                        $post_functions = new PN_PERSONAL_FINANCE_MANAGER_Functions_Post();
                        $title = !empty($_POST[$post_type . '_title']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST[$post_type . '_title'])) : '';
                        $description = !empty($_POST[$post_type . '_description']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST[$post_type . '_description'])) : '';

                        if (empty($title)) {
                          $auto_type_label = __('Item', 'pn-personal-finance-manager');
                          $title = $auto_type_label . ' - ' . gmdate('Y-m-d H:i');
                        }

                        $post_id = $post_functions->pn_personal_finance_manager_insert_post($title, $description, '', sanitize_title($title), $post_type, 'publish', get_current_user_id());
                      }
                    }

                    if (!empty($post_id)) {
                      // Authorization: only post owner or admin may update post meta
                      if (!is_user_logged_in()) {
                        echo wp_json_encode(['error_key' => 'pn_personal_finance_manager_form_save_error_unauthorized', 'error_content' => esc_html(__('You are not authorized to perform this action.', 'pn-personal-finance-manager'))]);
                        exit;
                      }
                      $post_author_id = intval(get_post_field('post_author', $post_id));
                      if (get_current_user_id() !== $post_author_id && !PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_user_is_admin(get_current_user_id())) {
                        echo wp_json_encode(['error_key' => 'pn_personal_finance_manager_form_save_error_unauthorized', 'error_content' => esc_html(__('You are not authorized to perform this action.', 'pn-personal-finance-manager'))]);
                        exit;
                      }

                      foreach ($pn_personal_finance_manager_key_value as $pn_personal_finance_manager_key => $pn_personal_finance_manager_value) {
                        if ($pn_personal_finance_manager_key == $post_type . '_title') {
                          wp_update_post([
                            'ID' => $post_id,
                            'post_title' => esc_html($pn_personal_finance_manager_value),
                          ]);
                        }

                        if ($pn_personal_finance_manager_key == $post_type . '_description') {
                          wp_update_post([
                            'ID' => $post_id,
                            'post_content' => esc_html($pn_personal_finance_manager_value),
                          ]);
                        }

                        // Skip action and ajax type keys
                        if (in_array($pn_personal_finance_manager_key, ['action', 'pn_personal_finance_manager_ajax_nopriv_type'])) {
                          continue;
                        }

                        // Ensure option name is prefixed with pn_personal_finance_manager_
                        if (strpos($pn_personal_finance_manager_key, 'pn_personal_finance_manager_') !== 0) {
                          $pn_personal_finance_manager_key = 'pn_personal_finance_manager_' . $pn_personal_finance_manager_key;
                        }

                        update_post_meta($post_id, $pn_personal_finance_manager_key, $pn_personal_finance_manager_value);
                      }
                    }
                  }

                  if ($post_type === 'pnpfm_asset') {
                    do_action('pn_personal_finance_manager_asset_form_save', $post_id, $pn_personal_finance_manager_key_value, $pn_personal_finance_manager_form_type, $pn_personal_finance_manager_form_subtype, $post_type);
                  } elseif ($post_type === 'pnpfm_liability') {
                    do_action('pn_personal_finance_manager_liability_form_save', $post_id, $pn_personal_finance_manager_key_value, $pn_personal_finance_manager_form_type, $pn_personal_finance_manager_form_subtype, $post_type);
                  }

                  do_action('pn_personal_finance_manager_form_save', $post_id, $pn_personal_finance_manager_key_value, $pn_personal_finance_manager_form_type, $pn_personal_finance_manager_form_subtype, $post_type);
                  break;
                case 'option':
                  if (PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_user_is_admin(get_current_user_id())) {
                    $pn_personal_finance_manager_settings = new PN_PERSONAL_FINANCE_MANAGER_Settings();
                    $pn_personal_finance_manager_options = $pn_personal_finance_manager_settings->pn_personal_finance_manager_get_options();
                    $pn_personal_finance_manager_allowed_options = array_keys($pn_personal_finance_manager_options);
                    update_user_meta(get_current_user_id(), 'pn_personal_finance_manager_debug_pn_personal_finance_manager_allowed_options', $pn_personal_finance_manager_allowed_options);
                    
                    foreach ($pn_personal_finance_manager_key_value as $pn_personal_finance_manager_key => $pn_personal_finance_manager_value) {
                      // Skip action and ajax type keys
                      if (in_array($pn_personal_finance_manager_key, ['action', 'pn_personal_finance_manager_ajax_nopriv_type'])) {
                        continue;
                      }

                      // Ensure option name is prefixed with pn_personal_finance_manager_
                      if (strpos($pn_personal_finance_manager_key, 'pn_personal_finance_manager_') !== 0) {
                        $pn_personal_finance_manager_key = 'pn_personal_finance_manager_' . $pn_personal_finance_manager_key;
                      }

                      // Only update if option is in allowed options list
                      if (in_array($pn_personal_finance_manager_key, $pn_personal_finance_manager_allowed_options)) {
                        update_option($pn_personal_finance_manager_key, $pn_personal_finance_manager_value);
                      }
                    }
                  }

                  do_action('pn_personal_finance_manager_form_save', 0, $pn_personal_finance_manager_key_value, $pn_personal_finance_manager_form_type, $pn_personal_finance_manager_form_subtype, '');
                  break;
              }

              $popup_close = in_array($pn_personal_finance_manager_form_subtype, ['post_new', 'post_edit', 'user_alt_new']) ? true : '';
              $update_list = in_array($pn_personal_finance_manager_form_subtype, ['post_new', 'post_edit', 'user_alt_new']) ? true : '';
              
              $update_html = '';
              $statistics_html = '';
              if ($update_list && !empty($post_type)) {
                switch ($post_type) {
                  case 'pnpfm_asset':
                    $plugin_post_type_asset = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset();
                    $update_html = $plugin_post_type_asset->pn_personal_finance_manager_asset_list();
                    $statistics_html = PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset::pn_personal_finance_manager_asset_statistics();
                    break;
                  case 'pnpfm_liability':
                    $plugin_post_type_liability = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability();
                    $update_html = $plugin_post_type_liability->pn_personal_finance_manager_liability_list();
                    $statistics_html = PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability::pn_personal_finance_manager_liability_statistics();
                    break;
                }
              }

              echo wp_json_encode(['error_key' => '', 'popup_close' => $popup_close, 'update_list' => $update_list, 'update_html' => $update_html, 'statistics_html' => $statistics_html]);exit;
            }
          }else{
            echo wp_json_encode(['error_key' => 'pn_personal_finance_manager_form_save_error', ]);exit;
          }
          break;
      }

      echo wp_json_encode(['error_key' => '', ]);exit;
    }
  }
}