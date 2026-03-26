<?php
/**
 * Fired from activate() function.
 *
 * This class defines all post types necessary to run during the plugin's life cycle.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_FINANCE_MANAGER
 * @subpackage PN_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_FINANCE_MANAGER_Forms {
  /**
   * Plaform forms.
   *
   * @since    1.0.0
   */

  /**
   * Get the current value of a field based on its type and storage
   * 
   * @param string $field_id The field ID
   * @param string $pn_finance_manager_type The type of field (user, post, option)
   * @param int $pn_finance_manager_id The ID of the user/post/option
   * @param int $pn_finance_manager_meta_array Whether the field is part of a meta array
   * @param int $pn_finance_manager_array_index The index in the meta array
   * @param array $pn_finance_manager_input The input array containing field configuration
   * @return mixed The current value of the field
   */
  private static function pn_finance_manager_get_field_value($field_id, $pn_finance_manager_type, $pn_finance_manager_id = 0, $pn_finance_manager_meta_array = 0, $pn_finance_manager_array_index = 0, $pn_finance_manager_input = []) {
    $current_value = '';

    if ($pn_finance_manager_meta_array) {
      switch ($pn_finance_manager_type) {
        case 'user':
          $meta = get_user_meta($pn_finance_manager_id, $field_id, true);
          if (is_array($meta) && isset($meta[$pn_finance_manager_array_index])) {
            $current_value = $meta[$pn_finance_manager_array_index];
          }
          break;
        case 'post':
          $meta = get_post_meta($pn_finance_manager_id, $field_id, true);
          if (is_array($meta) && isset($meta[$pn_finance_manager_array_index])) {
            $current_value = $meta[$pn_finance_manager_array_index];
          }
          break;
        case 'option':
          $option = get_option($field_id);
          if (is_array($option) && isset($option[$pn_finance_manager_array_index])) {
            $current_value = $option[$pn_finance_manager_array_index];
          }
          break;
      }
    } else {
      switch ($pn_finance_manager_type) {
        case 'user':
          $current_value = get_user_meta($pn_finance_manager_id, $field_id, true);
          break;
        case 'post':
          $current_value = get_post_meta($pn_finance_manager_id, $field_id, true);
          break;
        case 'option':
          $current_value = get_option($field_id);
          break;
      }
    }

    // If no value is found and there's a default value in the input config, use it
    if (empty($current_value) && !empty($pn_finance_manager_input['value'])) {
      $current_value = $pn_finance_manager_input['value'];
    }

    return $current_value;
  }

  public static function pn_finance_manager_input_builder($pn_finance_manager_input, $pn_finance_manager_type, $pn_finance_manager_id = 0, $disabled = 0, $pn_finance_manager_meta_array = 0, $pn_finance_manager_array_index = 0) {
    // Get the current value using the new function
    $pn_finance_manager_value = self::pn_finance_manager_get_field_value($pn_finance_manager_input['id'], $pn_finance_manager_type, $pn_finance_manager_id, $pn_finance_manager_meta_array, $pn_finance_manager_array_index, $pn_finance_manager_input);

    $pn_finance_manager_parent_block = (!empty($pn_finance_manager_input['parent']) ? 'data-pn-finance-manager-parent="' . $pn_finance_manager_input['parent'] . '"' : '') . ' ' . (!empty($pn_finance_manager_input['parent_option']) ? 'data-pn-finance-manager-parent-option="' . $pn_finance_manager_input['parent_option'] . '"' : '');

    switch ($pn_finance_manager_input['input']) {
      case 'input':        
        switch ($pn_finance_manager_input['type']) {
          case 'file':
            ?>
              <?php if (empty($pn_finance_manager_value)): ?>
                <p class="pn-finance-manager-m-10"><?php esc_html_e('No file found', 'pn-finance-manager'); ?></p>
              <?php else: ?>
                <p class="pn-finance-manager-m-10">
                  <a href="<?php echo esc_url(get_post_meta($pn_finance_manager_id, $pn_finance_manager_input['id'], true)['url']); ?>" target="_blank"><?php echo esc_html(basename(get_post_meta($pn_finance_manager_id, $pn_finance_manager_input['id'], true)['url'])); ?></a>
                </p>
              <?php endif ?>
            <?php
            break;
          case 'checkbox':
            ?>
              <label class="pn-finance-manager-switch">
                <input id="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" class="<?php echo array_key_exists('class', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['class']) : ''; ?> pn-finance-manager-checkbox pn-finance-manager-checkbox-switch pn-finance-manager-field" type="<?php echo esc_attr($pn_finance_manager_input['type']); ?>" <?php echo $pn_finance_manager_value == 'on' ? 'checked="checked"' : ''; ?> <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo ((array_key_exists('required', $pn_finance_manager_input) && $pn_finance_manager_input['required'] == true) ? 'required' : ''); ?> <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>>
                <span class="pn-finance-manager-slider pn-finance-manager-round"></span>
              </label>
            <?php
            break;
          case 'radio':
            ?>
              <div class="pn-finance-manager-input-radio-wrapper">
                <?php if (!empty($pn_finance_manager_input['radio_options'])): ?>
                  <?php foreach ($pn_finance_manager_input['radio_options'] as $radio_option): ?>
                    <div class="pn-finance-manager-input-radio-item">
                      <label for="<?php echo esc_attr($radio_option['id']); ?>">
                        <?php echo wp_kses_post(wp_specialchars_decode($radio_option['label'])); ?>
                        
                        <input type="<?php echo esc_attr($pn_finance_manager_input['type']); ?>"
                          id="<?php echo esc_attr($radio_option['id']); ?>"
                          name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>"
                          value="<?php echo esc_attr($radio_option['value']); ?>"
                          <?php echo $pn_finance_manager_value == $radio_option['value'] ? 'checked="checked"' : ''; ?>
                          <?php echo ((array_key_exists('required', $pn_finance_manager_input) && $pn_finance_manager_input['required'] == 'true') ? 'required' : ''); ?>>

                        <div class="pn-finance-manager-radio-control"></div>
                      </label>
                    </div>
                  <?php endforeach ?>
                <?php endif ?>
              </div>
            <?php
            break;
          case 'range':
            ?>
              <div class="pn-finance-manager-input-range-wrapper">
                <div class="pn-finance-manager-width-100-percent">
                  <?php if (!empty($pn_finance_manager_input['pn_finance_manager_label_min'])): ?>
                    <p class="pn-finance-manager-input-range-label-min"><?php echo esc_html($pn_finance_manager_input['pn_finance_manager_label_min']); ?></p>
                  <?php endif ?>

                  <?php if (!empty($pn_finance_manager_input['pn_finance_manager_label_max'])): ?>
                    <p class="pn-finance-manager-input-range-label-max"><?php echo esc_html($pn_finance_manager_input['pn_finance_manager_label_max']); ?></p>
                  <?php endif ?>
                </div>

                <input type="<?php echo esc_attr($pn_finance_manager_input['type']); ?>" id="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" class="pn-finance-manager-input-range <?php echo array_key_exists('class', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['class']) : ''; ?>" <?php echo ((array_key_exists('required', $pn_finance_manager_input) && $pn_finance_manager_input['required'] == true) ? 'required' : ''); ?> <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo (isset($pn_finance_manager_input['pn_finance_manager_max']) ? 'max=' . esc_attr($pn_finance_manager_input['pn_finance_manager_max']) : ''); ?> <?php echo (isset($pn_finance_manager_input['pn_finance_manager_min']) ? 'min=' . esc_attr($pn_finance_manager_input['pn_finance_manager_min']) : ''); ?> <?php echo (((array_key_exists('step', $pn_finance_manager_input) && $pn_finance_manager_input['step'] != '')) ? 'step="' . esc_attr($pn_finance_manager_input['step']) . '"' : ''); ?> <?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple'] ? 'multiple' : ''); ?> value="<?php echo (!empty($pn_finance_manager_input['button_text']) ? esc_html($pn_finance_manager_input['button_text']) : esc_html($pn_finance_manager_value)); ?>"/>
                <h3 class="pn-finance-manager-input-range-output"></h3>
              </div>
            <?php
            break;
          case 'stars':
            $pn_finance_manager_stars = !empty($pn_finance_manager_input['stars_number']) ? $pn_finance_manager_input['stars_number'] : 5;
            ?>
              <div class="pn-finance-manager-input-stars-wrapper">
                <div class="pn-finance-manager-width-100-percent">
                  <?php if (!empty($pn_finance_manager_input['pn_finance_manager_label_min'])): ?>
                    <p class="pn-finance-manager-input-stars-label-min"><?php echo esc_html($pn_finance_manager_input['pn_finance_manager_label_min']); ?></p>
                  <?php endif ?>

                  <?php if (!empty($pn_finance_manager_input['pn_finance_manager_label_max'])): ?>
                    <p class="pn-finance-manager-input-stars-label-max"><?php echo esc_html($pn_finance_manager_input['pn_finance_manager_label_max']); ?></p>
                  <?php endif ?>
                </div>

                <div class="pn-finance-manager-input-stars pn-finance-manager-text-align-center pn-finance-manager-pt-20">
                  <?php foreach (range(1, $pn_finance_manager_stars) as $index => $star): ?>
                    <i class="material-icons-outlined pn-finance-manager-input-star">star_outlined</i>
                  <?php endforeach ?>
                </div>

                <input type="number" <?php echo ((array_key_exists('required', $pn_finance_manager_input) && $pn_finance_manager_input['required'] == true) ? 'required' : ''); ?> <?php echo ((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') ? 'disabled' : ''); ?> id="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" class="pn-finance-manager-input-hidden-stars <?php echo array_key_exists('class', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['class']) : ''; ?>" min="1" max="<?php echo esc_attr($pn_finance_manager_stars) ?>">
              </div>
            <?php
            break;
          case 'submit':
            ?>
              <div class="pn-finance-manager-text-align-right">
                <input type="submit" value="<?php echo esc_attr($pn_finance_manager_input['value']); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" class="pn-finance-manager-btn" data-pn-finance-manager-type="<?php echo esc_attr($pn_finance_manager_type); ?>" data-pn-finance-manager-subtype="<?php echo ((array_key_exists('subtype', $pn_finance_manager_input)) ? esc_attr($pn_finance_manager_input['subtype']) : ''); ?>" data-pn-finance-manager-user-id="<?php echo esc_attr($pn_finance_manager_id); ?>" data-pn-finance-manager-post-id="<?php echo !empty(get_the_ID()) ? esc_attr(get_the_ID()) : ''; ?>"/><?php esc_html(PN_FINANCE_MANAGER_Data::pn_finance_manager_loader()); ?>
              </div>
            <?php
            break;
          case 'button':
            ?>
              <input 
                type="button" 
                id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" 
                class="<?php echo array_key_exists('class', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['class']) : 'pn-finance-manager-btn'; ?>" 
                value="<?php echo array_key_exists('label', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['label']) : 'Button'; ?>"
                <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>
              >
            <?php
            break;
          case 'hidden':
            ?>
              <input type="hidden" id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" value="<?php echo esc_attr($pn_finance_manager_value); ?>" <?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple'] == 'true' ? 'multiple' : ''); ?>>
            <?php
            break;
          case 'nonce':
            ?>
              <input type="hidden" id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" value="<?php echo esc_attr(wp_create_nonce('pn-finance-manager-nonce')); ?>">
            <?php
            break;
          case 'password':
            ?>
              <div class="pn-finance-manager-password-checker">
                <div class="pn-finance-manager-password-input pn-finance-manager-position-relative">
                  <input id="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple'] == 'true') ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple'] == 'true') ? '[]' : ''); ?>" <?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple'] == 'true' ? 'multiple' : ''); ?> class="pn-finance-manager-field pn-finance-manager-password-strength <?php echo array_key_exists('class', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['class']) : ''; ?>" type="<?php echo esc_attr($pn_finance_manager_input['type']); ?>" <?php echo ((array_key_exists('required', $pn_finance_manager_input) && $pn_finance_manager_input['required'] == 'true') ? 'required' : ''); ?> <?php echo ((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') ? 'disabled' : ''); ?> value="<?php echo (!empty($pn_finance_manager_input['button_text']) ? esc_html($pn_finance_manager_input['button_text']) : esc_attr($pn_finance_manager_value)); ?>" placeholder="<?php echo (array_key_exists('placeholder', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['placeholder']) : ''); ?>" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>/>

                  <a href="#" class="pn-finance-manager-show-pass pn-finance-manager-cursor-pointer pn-finance-manager-display-none-soft">
                    <i class="material-icons-outlined pn-finance-manager-font-size-20">visibility</i>
                  </a>
                </div>

                <div id="pn-finance-manager-popover-pass" class="pn-finance-manager-display-none-soft">
                  <div class="pn-finance-manager-progress-bar-wrapper">
                    <div class="pn-finance-manager-password-strength-bar"></div>
                  </div>

                  <h3 class="pn-finance-manager-mt-20"><?php esc_html_e('Password strength checker', 'pn-finance-manager'); ?> <i class="material-icons-outlined pn-finance-manager-cursor-pointer pn-finance-manager-close-icon pn-finance-manager-mt-30">close</i></h3>
                  <ul class="pn-finance-manager-list-style-none">
                    <li class="low-upper-case">
                      <i class="material-icons-outlined pn-finance-manager-font-size-20 pn-finance-manager-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Lowercase & Uppercase', 'pn-finance-manager'); ?></span>
                    </li>
                    <li class="one-number">
                      <i class="material-icons-outlined pn-finance-manager-font-size-20 pn-finance-manager-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Number (0-9)', 'pn-finance-manager'); ?></span>
                    </li>
                    <li class="one-special-char">
                      <i class="material-icons-outlined pn-finance-manager-font-size-20 pn-finance-manager-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Special Character (!@#$%^&*)', 'pn-finance-manager'); ?></span>
                    </li>
                    <li class="eight-character">
                      <i class="material-icons-outlined pn-finance-manager-font-size-20 pn-finance-manager-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Atleast 8 Character', 'pn-finance-manager'); ?></span>
                    </li>
                  </ul>
                </div>
              </div>
            <?php
            break;
          case 'color':
            ?>
              <input id="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" <?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple'] ? 'multiple' : ''); ?> class="pn-finance-manager-field <?php echo array_key_exists('class', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['class']) : ''; ?>" type="<?php echo esc_attr($pn_finance_manager_input['type']); ?>" <?php echo ((array_key_exists('required', $pn_finance_manager_input) && $pn_finance_manager_input['required'] == true) ? 'required' : ''); ?> <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> value="<?php echo (!empty($pn_finance_manager_value) ? esc_attr($pn_finance_manager_value) : '#000000'); ?>" placeholder="<?php echo (array_key_exists('placeholder', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['placeholder']) : ''); ?>" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>/>
            <?php
            break;
          case 'map':
            ?>
            <div class="pn-finance-manager-map-wrapper" style="margin-bottom:20px;">
              <label for="<?php echo esc_attr($pn_finance_manager_input['id']); ?>"><?php echo esc_html($pn_finance_manager_input['label']); ?></label>
              <input type="text" id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>_search" class="pn-finance-manager-map-search pn-finance-manager-input pn-finance-manager-width-100-percent" placeholder="<?php echo esc_attr($pn_finance_manager_input['placeholder']); ?>" value="<?php echo esc_attr($pn_finance_manager_value); ?>" />
              <div id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>_map" class="pn-finance-manager-map" style="height:250px;"></div>
              <input type="hidden" id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" value="<?php echo esc_attr($pn_finance_manager_value); ?>" />
            </div>
            
            <?php
            // Incluir el script de mapa solo una vez
            if (!defined('PN_FINANCE_MANAGER_MAP_SCRIPT_INCLUDED')) {
              define('PN_FINANCE_MANAGER_MAP_SCRIPT_INCLUDED', true);
              wp_enqueue_script('pn-finance-manager-map', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-map.js', ['jquery'], PN_FINANCE_MANAGER_VERSION, true);
            }
            break;
          default:
            ?>
              <input 
                <?php /* ID and name attributes */ ?>
                id="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" 
                name="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>"
                
                <?php /* Type and styling */ ?>
                class="pn-finance-manager-field <?php echo array_key_exists('class', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['class']) : ''; ?>" 
                type="<?php echo esc_attr($pn_finance_manager_input['type']); ?>"
                
                <?php /* State attributes */ ?>
                <?php echo ((array_key_exists('required', $pn_finance_manager_input) && $pn_finance_manager_input['required'] == true) ? 'required' : ''); ?>
                <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>
                <?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple'] ? 'multiple' : ''); ?>
                
                <?php /* Validation and limits */ ?>
                <?php echo (((array_key_exists('step', $pn_finance_manager_input) && $pn_finance_manager_input['step'] != '')) ? 'step="' . esc_attr($pn_finance_manager_input['step']) . '"' : ''); ?>
                <?php echo (isset($pn_finance_manager_input['max']) ? 'max="' . esc_attr($pn_finance_manager_input['max']) . '"' : ''); ?>
                <?php echo (isset($pn_finance_manager_input['min']) ? 'min="' . esc_attr($pn_finance_manager_input['min']) . '"' : ''); ?>
                <?php echo (isset($pn_finance_manager_input['maxlength']) ? 'maxlength="' . esc_attr($pn_finance_manager_input['maxlength']) . '"' : ''); ?>
                <?php echo (isset($pn_finance_manager_input['pattern']) ? 'pattern="' . esc_attr($pn_finance_manager_input['pattern']) . '"' : ''); ?>
                
                <?php /* Content attributes */ ?>
                value="<?php echo (!empty($pn_finance_manager_input['button_text']) ? esc_html($pn_finance_manager_input['button_text']) : esc_html($pn_finance_manager_value)); ?>"
                placeholder="<?php echo (array_key_exists('placeholder', $pn_finance_manager_input) ? esc_html($pn_finance_manager_input['placeholder']) : ''); ?>"
                
                <?php /* Custom data attributes */ ?>
                <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>
              />
            <?php
            break;
        }
        break;
      case 'select':
        if (!empty($pn_finance_manager_input['options']) && is_array($pn_finance_manager_input['options'])) {
          ?>
          <select 
            id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" 
            name="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" 
            class="pn-finance-manager-field <?php echo array_key_exists('class', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['class']) : ''; ?>"
            <?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? 'multiple' : ''; ?>
            <?php echo ((array_key_exists('required', $pn_finance_manager_input) && $pn_finance_manager_input['required'] == true) ? 'required' : ''); ?>
            <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>
            <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>
          >
            <?php if (array_key_exists('placeholder', $pn_finance_manager_input) && !empty($pn_finance_manager_input['placeholder'])): ?>
              <option value=""><?php echo esc_html($pn_finance_manager_input['placeholder']); ?></option>
            <?php endif; ?>
            
            <?php 
            $selected_values = array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple'] ? 
              (is_array($pn_finance_manager_value) ? $pn_finance_manager_value : array()) : 
              array($pn_finance_manager_value);
            
            foreach ($pn_finance_manager_input['options'] as $value => $label): 
              $is_selected = in_array($value, $selected_values);
            ?>
              <option 
                value="<?php echo esc_attr($value); ?>"
                <?php echo $is_selected ? 'selected="selected"' : ''; ?>
              >
                <?php echo esc_html($label); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php
        }
        break;
      case 'textarea':
        ?>
          <textarea id="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']) . ((array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? '[]' : ''); ?>" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?> class="pn-finance-manager-field <?php echo array_key_exists('class', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['class']) : ''; ?>" <?php echo ((array_key_exists('required', $pn_finance_manager_input) && $pn_finance_manager_input['required'] == true) ? 'required' : ''); ?> <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple'] ? 'multiple' : ''); ?> placeholder="<?php echo (array_key_exists('placeholder', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['placeholder']) : ''); ?>"><?php echo esc_html($pn_finance_manager_value); ?></textarea>
        <?php
        break;
      case 'image':
        ?>
          <div class="pn-finance-manager-field pn-finance-manager-images-block" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?> data-pn-finance-manager-multiple="<?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? 'true' : 'false'; ?>">
            <?php if (!empty($pn_finance_manager_value)): ?>
              <div class="pn-finance-manager-images">
                <?php foreach (explode(',', $pn_finance_manager_value) as $pn_finance_manager_image): ?>
                  <?php echo wp_get_attachment_image($pn_finance_manager_image, 'medium'); ?>
                <?php endforeach ?>
              </div>

              <div class="pn-finance-manager-text-align-center pn-finance-manager-position-relative"><a href="#" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-image-btn"><?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? esc_html(__('Edit images', 'pn-finance-manager')) : esc_html(__('Edit image', 'pn-finance-manager')); ?></a></div>
            <?php else: ?>
              <div class="pn-finance-manager-images"></div>

              <div class="pn-finance-manager-text-align-center pn-finance-manager-position-relative"><a href="#" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-image-btn"><?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? esc_html(__('Add images', 'pn-finance-manager')) : esc_html(__('Add image', 'pn-finance-manager')); ?></a></div>
            <?php endif ?>

            <input id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" class="pn-finance-manager-display-none pn-finance-manager-image-input" type="text" value="<?php echo esc_attr($pn_finance_manager_value); ?>"/>
          </div>
        <?php
        break;
      case 'video':
        ?>
        <div class="pn-finance-manager-field pn-finance-manager-videos-block" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>>
            <?php if (!empty($pn_finance_manager_value)): ?>
              <div class="pn-finance-manager-videos">
                <?php foreach (explode(',', $pn_finance_manager_value) as $pn_finance_manager_video): ?>
                  <div class="pn-finance-manager-video pn-finance-manager-tooltip" title="<?php echo esc_html(get_the_title($pn_finance_manager_video)); ?>"><i class="dashicons dashicons-media-video"></i></div>
                <?php endforeach ?>
              </div>

              <div class="pn-finance-manager-text-align-center pn-finance-manager-position-relative"><a href="#" class="pn-finance-manager-btn pn-finance-manager-video-btn"><?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? esc_html(__('Edit videos', 'pn-finance-manager')) : esc_html(__('Edit video', 'pn-finance-manager')); ?></a></div>
            <?php else: ?>
              <div class="pn-finance-manager-videos"></div>

              <div class="pn-finance-manager-text-align-center pn-finance-manager-position-relative"><a href="#" class="pn-finance-manager-btn pn-finance-manager-video-btn"><?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? esc_html(__('Add videos', 'pn-finance-manager')) : esc_html(__('Add video', 'pn-finance-manager')); ?></a></div>
            <?php endif ?>

            <input id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" class="pn-finance-manager-display-none pn-finance-manager-video-input" type="text" value="<?php echo esc_attr($pn_finance_manager_value); ?>"/>
          </div>
        <?php
        break;
      case 'audio':
        ?>
          <div class="pn-finance-manager-field pn-finance-manager-audios-block" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>>
            <?php if (!empty($pn_finance_manager_value)): ?>
              <div class="pn-finance-manager-audios">
                <?php foreach (explode(',', $pn_finance_manager_value) as $pn_finance_manager_audio): ?>
                  <div class="pn-finance-manager-audio pn-finance-manager-tooltip" title="<?php echo esc_html(get_the_title($pn_finance_manager_audio)); ?>"><i class="dashicons dashicons-media-audio"></i></div>
                <?php endforeach ?>
              </div>

              <div class="pn-finance-manager-text-align-center pn-finance-manager-position-relative"><a href="#" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-audio-btn"><?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? esc_html(__('Edit audios', 'pn-finance-manager')) : esc_html(__('Edit audio', 'pn-finance-manager')); ?></a></div>
            <?php else: ?>
              <div class="pn-finance-manager-audios"></div>

              <div class="pn-finance-manager-text-align-center pn-finance-manager-position-relative"><a href="#" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-audio-btn"><?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? esc_html(__('Add audios', 'pn-finance-manager')) : esc_html(__('Add audio', 'pn-finance-manager')); ?></a></div>
            <?php endif ?>

            <input id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" class="pn-finance-manager-display-none pn-finance-manager-audio-input" type="text" value="<?php echo esc_attr($pn_finance_manager_value); ?>"/>
          </div>
        <?php
        break;
      case 'file':
        ?>
          <div class="pn-finance-manager-field pn-finance-manager-files-block" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>>
            <?php if (!empty($pn_finance_manager_value)): ?>
              <div class="pn-finance-manager-files pn-finance-manager-text-align-center">
                <?php foreach (explode(',', $pn_finance_manager_value) as $pn_finance_manager_file): ?>
                  <embed src="<?php echo esc_url(wp_get_attachment_url($pn_finance_manager_file)); ?>" type="application/pdf" class="pn-finance-manager-embed-file"/>
                <?php endforeach ?>
              </div>

              <div class="pn-finance-manager-text-align-center pn-finance-manager-position-relative"><a href="#" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-file-btn"><?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? esc_html(__('Edit files', 'pn-finance-manager')) : esc_html(__('Edit file', 'pn-finance-manager')); ?></a></div>
            <?php else: ?>
              <div class="pn-finance-manager-files"></div>

              <div class="pn-finance-manager-text-align-center pn-finance-manager-position-relative"><a href="#" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-btn-mini pn-finance-manager-file-btn"><?php echo (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) ? esc_html(__('Add files', 'pn-finance-manager')) : esc_html(__('Add file', 'pn-finance-manager')); ?></a></div>
            <?php endif ?>

            <input id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" class="pn-finance-manager-display-none pn-finance-manager-file-input pn-finance-manager-btn-mini" type="text" value="<?php echo esc_attr($pn_finance_manager_value); ?>"/>
          </div>
        <?php
        break;
      case 'editor':
        ?>
          <div class="pn-finance-manager-field" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>>
            <textarea id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" class="pn-finance-manager-input pn-finance-manager-width-100-percent pn-finance-manager-wysiwyg"><?php echo ((empty($pn_finance_manager_value)) ? (array_key_exists('placeholder', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['placeholder']) : '') : esc_html($pn_finance_manager_value)); ?></textarea>
          </div>
        <?php
        break;
      case 'html':
        ?>
          <div class="pn-finance-manager-field" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>>
            <?php echo !empty($pn_finance_manager_input['html_content']) ? wp_kses(do_shortcode($pn_finance_manager_input['html_content']), PN_FINANCE_MANAGER_KSES) : ''; ?>
          </div>
        <?php
        break;
      case 'html_multi':
        switch ($pn_finance_manager_type) {
          case 'user':
            $html_multi_fields_length = !empty(get_user_meta($pn_finance_manager_id, $pn_finance_manager_input['html_multi_fields'][0]['id'], true)) ? count(get_user_meta($pn_finance_manager_id, $pn_finance_manager_input['html_multi_fields'][0]['id'], true)) : 0;
            break;
          case 'post':
            $html_multi_fields_length = !empty(get_post_meta($pn_finance_manager_id, $pn_finance_manager_input['html_multi_fields'][0]['id'], true)) ? count(get_post_meta($pn_finance_manager_id, $pn_finance_manager_input['html_multi_fields'][0]['id'], true)) : 0;
            break;
          case 'option':
            $html_multi_fields_length = !empty(get_option($pn_finance_manager_input['html_multi_fields'][0]['id'])) ? count(get_option($pn_finance_manager_input['html_multi_fields'][0]['id'])) : 0;
        }

        ?>
          <div class="pn-finance-manager-field pn-finance-manager-html-multi-wrapper pn-finance-manager-mb-50" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>>
            <?php if ($html_multi_fields_length): ?>
              <?php foreach (range(0, ($html_multi_fields_length - 1)) as $length_index): ?>
                <div class="pn-finance-manager-html-multi-group pn-finance-manager-display-table pn-finance-manager-width-100-percent">
                  <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-90-percent">
                    <?php foreach ($pn_finance_manager_input['html_multi_fields'] as $index => $html_multi_field): ?>
                      <label><?php echo esc_html($html_multi_field['label']); ?></label>

                      <?php self::pn_finance_manager_input_builder($html_multi_field, $pn_finance_manager_type, $pn_finance_manager_id, false, true, $length_index); ?>
                    <?php endforeach ?>
                  </div>
                  <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-10-percent pn-finance-manager-text-align-center">
                    <i class="material-icons-outlined pn-finance-manager-cursor-move pn-finance-manager-multi-sorting pn-finance-manager-vertical-align-super pn-finance-manager-tooltip" title="<?php esc_html_e('Order element', 'pn-finance-manager'); ?>">drag_handle</i>
                  </div>

                  <div class="pn-finance-manager-text-align-right">
                    <a href="#" class="pn-finance-manager-html-multi-remove-btn"><i class="material-icons-outlined pn-finance-manager-cursor-pointer pn-finance-manager-tooltip" title="<?php esc_html_e('Remove element', 'pn-finance-manager'); ?>">remove</i></a>
                  </div>
                </div>
              <?php endforeach ?>
            <?php else: ?>
              <div class="pn-finance-manager-html-multi-group pn-finance-manager-mb-50">
                <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-90-percent">
                  <?php foreach ($pn_finance_manager_input['html_multi_fields'] as $html_multi_field): ?>
                    <?php self::pn_finance_manager_input_builder($html_multi_field, $pn_finance_manager_type); ?>
                  <?php endforeach ?>
                </div>
                <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-10-percent pn-finance-manager-text-align-center">
                  <i class="material-icons-outlined pn-finance-manager-cursor-move pn-finance-manager-multi-sorting pn-finance-manager-vertical-align-super pn-finance-manager-tooltip" title="<?php esc_html_e('Order element', 'pn-finance-manager'); ?>">drag_handle</i>
                </div>

                <div class="pn-finance-manager-text-align-right">
                  <a href="#" class="pn-finance-manager-html-multi-remove-btn pn-finance-manager-tooltip" title="<?php esc_html_e('Remove element', 'pn-finance-manager'); ?>"><i class="material-icons-outlined pn-finance-manager-cursor-pointer">remove</i></a>
                </div>
              </div>
            <?php endif ?>

            <div class="pn-finance-manager-text-align-right">
              <a href="#" class="pn-finance-manager-html-multi-add-btn pn-finance-manager-tooltip" title="<?php esc_html_e('Add element', 'pn-finance-manager'); ?>"><i class="material-icons-outlined pn-finance-manager-cursor-pointer pn-finance-manager-font-size-40">add</i></a>
            </div>
          </div>
        <?php
        break;
      case 'audio_recorder':
        // Enqueue CSS and JS files for audio recorder
        wp_enqueue_style('pn-finance-manager-audio-recorder', PN_FINANCE_MANAGER_URL . 'assets/css/pn-finance-manager-audio-recorder.css', array(), '1.0.0');
        wp_enqueue_script('pn-finance-manager-audio-recorder', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-audio-recorder.js', array('jquery'), '1.0.0', true);
        
        // Localize script with AJAX data
        wp_localize_script('pn-finance-manager-audio-recorder', 'pn_finance_manager_audio_recorder_vars', array(
          'ajax_url' => admin_url('admin-ajax.php'),
          'ajax_nonce' => wp_create_nonce('pn_finance_manager_audio_nonce'),
        ));
        
        ?>
          <div class="pn-finance-manager-audio-recorder-status pn-finance-manager-display-none-soft">
            <p class="pn-finance-manager-recording-status"><?php esc_html_e('Ready to record', 'pn-finance-manager'); ?></p>
          </div>
          
          <div class="pn-finance-manager-audio-recorder-wrapper">
            <div class="pn-finance-manager-audio-recorder-controls">
              <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
                <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-text-align-center pn-finance-manager-mb-20">
                  <button type="button" class="pn-finance-manager-btn pn-finance-manager-btn-primary pn-finance-manager-start-recording" <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                    <i class="material-icons-outlined pn-finance-manager-vertical-align-middle">mic</i>
                    <?php esc_html_e('Start recording', 'pn-finance-manager'); ?>
                  </button>
                </div>

                <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-text-align-center pn-finance-manager-mb-20">
                  <button type="button" class="pn-finance-manager-btn pn-finance-manager-btn-secondary pn-finance-manager-stop-recording" style="display: none;" <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                    <i class="material-icons-outlined pn-finance-manager-vertical-align-middle">stop</i>
                    <?php esc_html_e('Stop recording', 'pn-finance-manager'); ?>
                  </button>
                </div>
              </div>

              <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
                <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-text-align-center pn-finance-manager-mb-20">
                  <button type="button" class="pn-finance-manager-btn pn-finance-manager-btn-secondary pn-finance-manager-play-audio" style="display: none;" <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                    <i class="material-icons-outlined pn-finance-manager-vertical-align-middle">play_arrow</i>
                    <?php esc_html_e('Play audio', 'pn-finance-manager'); ?>
                  </button>
                </div>

                <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-text-align-center pn-finance-manager-mb-20">
                  <button type="button" class="pn-finance-manager-btn pn-finance-manager-btn-secondary pn-finance-manager-stop-audio" style="display: none;" <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                    <i class="material-icons-outlined pn-finance-manager-vertical-align-middle">stop</i>
                    <?php esc_html_e('Stop audio', 'pn-finance-manager'); ?>
                  </button>
                </div>
              </div>
            </div>

            <div class="pn-finance-manager-audio-recorder-visualizer" style="display: none;">
              <canvas class="pn-finance-manager-audio-canvas" width="300" height="60"></canvas>
            </div>

            <div class="pn-finance-manager-audio-recorder-timer" style="display: none;">
              <span class="pn-finance-manager-recording-time">00:00</span>
            </div>

            <div class="pn-finance-manager-audio-transcription-controls pn-finance-manager-display-none-soft pn-finance-manager-display-table pn-finance-manager-width-100-percent pn-finance-manager-mb-20">
              <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-text-align-center">
                <button type="button" class="pn-finance-manager-btn pn-finance-manager-btn-primary pn-finance-manager-transcribe-audio" <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                  <i class="material-icons-outlined pn-finance-manager-vertical-align-middle">translate</i>
                  <?php esc_html_e('Transcribe Audio', 'pn-finance-manager'); ?>
                </button>
              </div>

              <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-text-align-center">
                <button type="button" class="pn-finance-manager-btn pn-finance-manager-btn-secondary pn-finance-manager-clear-transcription" <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                  <i class="material-icons-outlined pn-finance-manager-vertical-align-middle">clear</i>
                  <?php esc_html_e('Clear', 'pn-finance-manager'); ?>
                </button>
              </div>
            </div>

            <div class="pn-finance-manager-audio-transcription-loading">
              <?php echo esc_html(PN_FINANCE_MANAGER_Data::pn_finance_manager_loader()); ?>
            </div>

            <div class="pn-finance-manager-audio-transcription-result">
              <textarea 
                id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" 
                name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>" 
                class="pn-finance-manager-field pn-finance-manager-transcription-textarea <?php echo array_key_exists('class', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['class']) : ''; ?>" 
                placeholder="<?php echo (array_key_exists('placeholder', $pn_finance_manager_input) ? esc_attr($pn_finance_manager_input['placeholder']) : esc_attr__('Transcribed text will appear here...', 'pn-finance-manager')); ?>"
                <?php echo ((array_key_exists('required', $pn_finance_manager_input) && $pn_finance_manager_input['required'] == true) ? 'required' : ''); ?>
                <?php echo (((array_key_exists('disabled', $pn_finance_manager_input) && $pn_finance_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>
                <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>
                rows="4"
                style="width: 100%; margin-top: 10px;"
              ><?php echo esc_textarea($pn_finance_manager_value); ?></textarea>
            </div>

            <div class="pn-finance-manager-audio-transcription-error pn-finance-manager-display-none-soft">
              <p class="pn-finance-manager-error-message"></p>
            </div>

            <div class="pn-finance-manager-audio-transcription-success pn-finance-manager-display-none-soft">
              <p class="pn-finance-manager-success-message"></p>
            </div>

            <!-- Hidden input to store audio data -->
            <input type="hidden" 
                  id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>_audio_data" 
                  name="<?php echo esc_attr($pn_finance_manager_input['id']); ?>_audio_data" 
                  value="" />
          </div>
        <?php
        break;
      case 'user_role_selector':
        if (!current_user_can('manage_options')) {
          ?>
          <div class="pn-finance-manager-field"><p class="pn-finance-manager-color-error"><?php esc_html_e('You do not have permission to manage user roles.', 'pn-finance-manager'); ?></p></div>
          <?php
          break;
        }
        $users = get_users(['orderby' => 'display_name', 'order' => 'ASC']);
        $target_role = isset($pn_finance_manager_input['role']) ? $pn_finance_manager_input['role'] : 'pn_finance_manager_role_manager';
        $role_label = isset($pn_finance_manager_input['role_label']) ? $pn_finance_manager_input['role_label'] : __('Finance Manager - PN', 'pn-finance-manager');
        $users_with_role = array_filter($users, function ($user) use ($target_role) {
          return in_array($target_role, (array) $user->roles);
        });
        ?>
        <div class="pn-finance-manager-user-role-selector-wrapper" <?php echo wp_kses_post($pn_finance_manager_parent_block); ?>>
          <?php if (!empty($users_with_role)): ?>
            <div class="pn-finance-manager-mb-20 pn-finance-manager-p-15 pn-finance-manager-users-with-role-box">
              <?php /* translators: %s: role name */ ?>
              <h4 class="pn-finance-manager-mb-10"><?php echo esc_html(sprintf(__('Users with %s Role', 'pn-finance-manager'), $role_label)); ?> <span class="pn-finance-manager-role-badge"><?php echo count($users_with_role); ?></span></h4>
              <div class="pn-finance-manager-users-with-role-list">
                <?php foreach ($users_with_role as $user): ?>
                  <div class="pn-finance-manager-user-role-item"><i class="material-icons-outlined">person</i> <strong><?php echo esc_html($user->display_name); ?></strong> <span class="pn-finance-manager-color-gray">(<?php echo esc_html($user->user_email); ?>)</span></div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php else: ?>
            <?php /* translators: %s: role name */ ?>
            <div class="pn-finance-manager-mb-20 pn-finance-manager-p-15 pn-finance-manager-alert-warning"><p><i class="material-icons-outlined pn-finance-manager-vertical-align-middle">info</i> <?php echo esc_html(sprintf(__('No users currently have the %s role.', 'pn-finance-manager'), $role_label)); ?></p></div>
          <?php endif; ?>
          <div class="pn-finance-manager-mb-20">
            <label for="pn_finance_manager_user_select_<?php echo esc_attr($pn_finance_manager_input['id']); ?>" class="pn-finance-manager-mb-10 pn-finance-manager-display-block"><?php esc_html_e('Select Users', 'pn-finance-manager'); ?></label>
            <select id="pn_finance_manager_user_select_<?php echo esc_attr($pn_finance_manager_input['id']); ?>" class="pn-finance-manager-select pn-finance-manager-width-100-percent pn-finance-manager-user-role-select" multiple size="10" data-role="<?php echo esc_attr($target_role); ?>" data-role-label="<?php echo esc_attr($role_label); ?>">
              <?php foreach ($users as $user): $has_role = in_array($target_role, (array) $user->roles); ?>
                <option value="<?php echo esc_attr($user->ID); ?>" <?php echo $has_role ? 'data-has-role="true"' : ''; ?>><?php echo esc_html($user->display_name . ' (' . $user->user_email . ')'); ?><?php if ($has_role): ?> ✓<?php endif; ?></option>
              <?php endforeach; ?>
            </select>
            <p class="pn-finance-manager-font-size-small pn-finance-manager-color-gray pn-finance-manager-mt-5"><?php esc_html_e('Hold Ctrl (Windows) or Cmd (Mac) to select multiple users. Users with ✓ already have this role.', 'pn-finance-manager'); ?></p>
          </div>
          <div class="pn-finance-manager-role-actions pn-finance-manager-mb-20">
            <input type="hidden" class="pn-finance-manager-role-nonce" value="<?php echo esc_attr(wp_create_nonce('pn-finance-manager-role-assignment')); ?>">
            <?php /* translators: %s: role name */ ?>
            <div class="pn-finance-manager-display-inline-block pn-finance-manager-mr-10"><button type="button" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-assign-role-btn" data-input-id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>"><i class="material-icons-outlined pn-finance-manager-vertical-align-middle">person_add</i> <?php echo esc_html(sprintf(__('Assign %s Role', 'pn-finance-manager'), $role_label)); ?></button></div>
            <?php /* translators: %s: role name */ ?>
            <div class="pn-finance-manager-display-inline-block"><button type="button" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-remove-role-btn" data-input-id="<?php echo esc_attr($pn_finance_manager_input['id']); ?>"><i class="material-icons-outlined pn-finance-manager-vertical-align-middle">person_remove</i> <?php echo esc_html(sprintf(__('Remove %s Role', 'pn-finance-manager'), $role_label)); ?></button></div>
          </div>
          <div class="pn-finance-manager-role-message pn-finance-manager-mt-20 pn-finance-manager-display-none-soft"></div>
        </div>
        <?php
        break;
    }
  }

  public static function pn_finance_manager_input_wrapper_builder($input_array, $type, $pn_finance_manager_id = 0, $disabled = 0, $pn_finance_manager_format = 'half'){
    ?>
      <?php if (array_key_exists('section', $input_array) && !empty($input_array['section'])): ?>
        <?php if ($input_array['section'] == 'start'): ?>
          <div class="pn-finance-manager-toggle-wrapper pn-finance-manager-section-wrapper pn-finance-manager-position-relative <?php echo array_key_exists('class', $input_array) ? esc_attr($input_array['class']) : ''; ?>" id="<?php echo array_key_exists('id', $input_array) ? esc_attr($input_array['id']) : ''; ?>">
            <a href="#" class="pn-finance-manager-toggle pn-finance-manager-width-100-percent pn-finance-manager-text-decoration-none">
              <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent pn-finance-manager-mb-20">
                <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-90-percent">
                  <label class="pn-finance-manager-cursor-pointer pn-finance-manager-mb-20 pn-finance-manager-color-main-0"><?php echo wp_kses_post($input_array['label']); ?></label>
                </div>
                <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-10-percent pn-finance-manager-text-align-right">
                  <i class="material-icons-outlined pn-finance-manager-cursor-pointer pn-finance-manager-color-main-0">add</i>
                </div>
              </div>
            </a>

            <div class="pn-finance-manager-content pn-finance-manager-pl-10 pn-finance-manager-toggle-content pn-finance-manager-mb-20 pn-finance-manager-display-none-soft">
              <?php if (array_key_exists('description', $input_array) && !empty($input_array['description'])): ?>
                <div class="pn-finance-manager-section-info-block pn-finance-manager-mb-20">
                  <i class="material-icons-outlined pn-finance-manager-section-info-icon">info_outline</i>
                  <small><?php echo wp_kses_post($input_array['description']); ?></small>
                </div>
              <?php endif ?>
        <?php elseif ($input_array['section'] == 'end'): ?>
            </div>
          </div>
        <?php endif ?>
      <?php else: ?>
        <div class="pn-finance-manager-input-wrapper <?php echo esc_attr($input_array['id']); ?> <?php echo !empty($input_array['tabs']) ? 'pn-finance-manager-input-tabbed' : ''; ?> pn-finance-manager-input-field-<?php echo esc_attr($input_array['input']); ?> <?php echo (!empty($input_array['required']) && $input_array['required'] == true) ? 'pn-finance-manager-input-field-required' : ''; ?> <?php echo ($disabled) ? 'pn-finance-manager-input-field-disabled' : ''; ?>">
          <?php if (array_key_exists('label', $input_array) && !empty($input_array['label'])): ?>
            <div class="pn-finance-manager-display-inline-table <?php echo (($pn_finance_manager_format == 'half' && !(array_key_exists('type', $input_array) && $input_array['type'] == 'submit')) ? 'pn-finance-manager-width-40-percent' : 'pn-finance-manager-width-100-percent'); ?> pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-vertical-align-top">
              <div class="pn-finance-manager-p-10 <?php echo (array_key_exists('parent', $input_array) && !empty($input_array['parent']) && $input_array['parent'] != 'this') ? 'pn-finance-manager-pl-30' : ''; ?>">
                <label class="pn-finance-manager-vertical-align-middle pn-finance-manager-display-block <?php echo (array_key_exists('description', $input_array) && !empty($input_array['description'])) ? 'pn-finance-manager-toggle' : ''; ?>" for="<?php echo esc_attr($input_array['id']); ?>"><?php echo esc_attr($input_array['label']); ?> <?php echo (array_key_exists('required', $input_array) && !empty($input_array['required']) && $input_array['required'] == true) ? '<span class="pn-finance-manager-tooltip" title="' . esc_html(__('Required field', 'pn-finance-manager')) . '">*</span>' : ''; ?><?php echo (array_key_exists('description', $input_array) && !empty($input_array['description'])) ? '<i class="material-icons-outlined pn-finance-manager-cursor-pointer pn-finance-manager-float-right">add</i>' : ''; ?></label>

                <?php if (array_key_exists('description', $input_array) && !empty($input_array['description'])): ?>
                  <div class="pn-finance-manager-toggle-content pn-finance-manager-display-none-soft">
                    <small><?php echo wp_kses_post(wp_specialchars_decode($input_array['description'])); ?></small>
                  </div>
                <?php endif ?>
              </div>
            </div>
          <?php endif ?>

          <div class="pn-finance-manager-display-inline-table <?php echo ((array_key_exists('label', $input_array) && empty($input_array['label'])) ? 'pn-finance-manager-width-100-percent' : (($pn_finance_manager_format == 'half' && !(array_key_exists('type', $input_array) && $input_array['type'] == 'submit')) ? 'pn-finance-manager-width-60-percent' : 'pn-finance-manager-width-100-percent')); ?> pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-vertical-align-top">
            <div class="pn-finance-manager-p-10 <?php echo (array_key_exists('parent', $input_array) && !empty($input_array['parent']) && $input_array['parent'] != 'this') ? 'pn-finance-manager-pl-30' : ''; ?>">
              <div class="pn-finance-manager-input-field"><?php self::pn_finance_manager_input_builder($input_array, $type, $pn_finance_manager_id, $disabled); ?></div>
            </div>
          </div>
        </div>
      <?php endif ?>
    <?php
  }

  /**
   * Display wrapper for field values with format control
   * 
   * @param array $input_array The input array containing field configuration
   * @param string $type The type of field (user, post, option)
   * @param int $pn_finance_manager_id The ID of the user/post/option
   * @param int $pn_finance_manager_meta_array Whether the field is part of a meta array
   * @param int $pn_finance_manager_array_index The index in the meta array
   * @param string $pn_finance_manager_format The display format ('half' or 'full')
   * @return string Formatted HTML output
   */
  public static function pn_finance_manager_input_display_wrapper($input_array, $type, $pn_finance_manager_id = 0, $pn_finance_manager_meta_array = 0, $pn_finance_manager_array_index = 0, $pn_finance_manager_format = 'half') {
    ob_start();
    ?>
    <?php if (array_key_exists('section', $input_array) && !empty($input_array['section'])): ?>
      <?php if ($input_array['section'] == 'start'): ?>
        <div class="pn-finance-manager-toggle-wrapper pn-finance-manager-section-wrapper pn-finance-manager-position-relative <?php echo array_key_exists('class', $input_array) ? esc_attr($input_array['class']) : ''; ?>" id="<?php echo array_key_exists('id', $input_array) ? esc_attr($input_array['id']) : ''; ?>">
          <a href="#" class="pn-finance-manager-toggle pn-finance-manager-width-100-percent pn-finance-manager-text-decoration-none">
            <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent pn-finance-manager-mb-20">
              <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-90-percent">
                <label class="pn-finance-manager-cursor-pointer pn-finance-manager-mb-20 pn-finance-manager-color-main-0"><?php echo wp_kses_post($input_array['label']); ?></label>
              </div>
              <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-10-percent pn-finance-manager-text-align-right">
                <i class="material-icons-outlined pn-finance-manager-cursor-pointer pn-finance-manager-color-main-0">add</i>
              </div>
            </div>
          </a>

          <div class="pn-finance-manager-content pn-finance-manager-pl-10 pn-finance-manager-toggle-content pn-finance-manager-mb-20 pn-finance-manager-display-none-soft">
            <?php if (array_key_exists('description', $input_array) && !empty($input_array['description'])): ?>
              <div class="pn-finance-manager-section-info-block pn-finance-manager-mb-20">
                <i class="material-icons-outlined pn-finance-manager-section-info-icon">info_outline</i>
                <small><?php echo wp_kses_post($input_array['description']); ?></small>
              </div>
            <?php endif ?>
      <?php elseif ($input_array['section'] == 'end'): ?>
            </div>
          </div>
        <?php endif ?>
      <?php else: ?>
        <div class="pn-finance-manager-input-wrapper <?php echo esc_attr($input_array['id']); ?> pn-finance-manager-input-display-<?php echo esc_attr($input_array['input']); ?> <?php echo (!empty($input_array['required']) && $input_array['required'] == true) ? 'pn-finance-manager-input-field-required' : ''; ?>">
          <?php if (array_key_exists('label', $input_array) && !empty($input_array['label'])): ?>
            <div class="pn-finance-manager-display-inline-table <?php echo ($pn_finance_manager_format == 'half' ? 'pn-finance-manager-width-40-percent' : 'pn-finance-manager-width-100-percent'); ?> pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-vertical-align-top">
              <div class="pn-finance-manager-p-10 <?php echo (array_key_exists('parent', $input_array) && !empty($input_array['parent']) && $input_array['parent'] != 'this') ? 'pn-finance-manager-pl-30' : ''; ?>">
                <label class="pn-finance-manager-vertical-align-middle pn-finance-manager-display-block <?php echo (array_key_exists('description', $input_array) && !empty($input_array['description'])) ? 'pn-finance-manager-toggle' : ''; ?>" for="<?php echo esc_attr($input_array['id']); ?>">
                  <?php echo esc_html($input_array['label']); ?>
                  <?php echo (array_key_exists('required', $input_array) && !empty($input_array['required']) && $input_array['required'] == true) ? '<span class="pn-finance-manager-tooltip" title="' . esc_html(__('Required field', 'pn-finance-manager')) . '">*</span>' : ''; ?>
                  <?php echo (array_key_exists('description', $input_array) && !empty($input_array['description'])) ? '<i class="material-icons-outlined pn-finance-manager-cursor-pointer pn-finance-manager-float-right">add</i>' : ''; ?>
                </label>

                <?php if (array_key_exists('description', $input_array) && !empty($input_array['description'])): ?>
                  <div class="pn-finance-manager-toggle-content pn-finance-manager-display-none-soft">
                    <small><?php echo wp_kses_post(wp_specialchars_decode($input_array['description'])); ?></small>
                  </div>
                <?php endif ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="pn-finance-manager-display-inline-table <?php echo ((array_key_exists('label', $input_array) && empty($input_array['label'])) ? 'pn-finance-manager-width-100-percent' : ($pn_finance_manager_format == 'half' ? 'pn-finance-manager-width-60-percent' : 'pn-finance-manager-width-100-percent')); ?> pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-vertical-align-top">
            <div class="pn-finance-manager-p-10 <?php echo (array_key_exists('parent', $input_array) && !empty($input_array['parent']) && $input_array['parent'] != 'this') ? 'pn-finance-manager-pl-30' : ''; ?>">
              <div class="pn-finance-manager-input-field">
                <?php self::pn_finance_manager_input_display($input_array, $type, $pn_finance_manager_id, $pn_finance_manager_meta_array, $pn_finance_manager_array_index); ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    <?php
    return ob_get_clean();
  }

  /**
   * Display formatted values of pn_finance_manager_input_builder fields in frontend
   * 
   * @param array $pn_finance_manager_input The input array containing field configuration
   * @param string $pn_finance_manager_type The type of field (user, post, option)
   * @param int $pn_finance_manager_id The ID of the user/post/option
   * @param int $pn_finance_manager_meta_array Whether the field is part of a meta array
   * @param int $pn_finance_manager_array_index The index in the meta array
   * @return string Formatted HTML output of field values
   */
  public static function pn_finance_manager_input_display($pn_finance_manager_input, $pn_finance_manager_type, $pn_finance_manager_id = 0, $pn_finance_manager_meta_array = 0, $pn_finance_manager_array_index = 0) {
    // Get the current value using the new function
    $current_value = self::pn_finance_manager_get_field_value($pn_finance_manager_input['id'], $pn_finance_manager_type, $pn_finance_manager_id, $pn_finance_manager_meta_array, $pn_finance_manager_array_index, $pn_finance_manager_input);

    // Start the field value display
    ?>
      <div class="pn-finance-manager-field-value">
        <?php
        switch ($pn_finance_manager_input['input']) {
          case 'input':
            switch ($pn_finance_manager_input['type']) {
              case 'hidden':
                break;
              case 'nonce':
                break;
              case 'file':
                if (!empty($current_value)) {
                  $file_url = wp_get_attachment_url($current_value);
                  ?>
                    <div class="pn-finance-manager-file-display">
                      <a href="<?php echo esc_url($file_url); ?>" target="_blank" class="pn-finance-manager-file-link">
                        <?php echo esc_html(basename($file_url)); ?>
                      </a>
                    </div>
                  <?php
                } else {
                  echo '<span class="pn-finance-manager-no-file">' . esc_html__('No file uploaded', 'pn-finance-manager') . '</span>';
                }
                break;

              case 'checkbox':
                ?>
                  <div class="pn-finance-manager-checkbox-display">
                    <span class="pn-finance-manager-checkbox-status <?php echo $current_value === 'on' ? 'checked' : 'unchecked'; ?>">
                      <?php echo $current_value === 'on' ? esc_html__('Yes', 'pn-finance-manager') : esc_html__('No', 'pn-finance-manager'); ?>
                    </span>
                  </div>
                <?php
                break;

              case 'radio':
                if (!empty($pn_finance_manager_input['radio_options'])) {
                  foreach ($pn_finance_manager_input['radio_options'] as $option) {
                    if ($current_value === $option['value']) {
                      ?>
                        <span class="pn-finance-manager-radio-selected"><?php echo esc_html($option['label']); ?></span>
                      <?php
                    }
                  }
                }
                break;

              case 'color':
                ?>
                  <div class="pn-finance-manager-color-display">
                    <span class="pn-finance-manager-color-preview" style="background-color: <?php echo esc_attr($current_value); ?>"></span>
                    <span class="pn-finance-manager-color-value"><?php echo esc_html($current_value); ?></span>
                  </div>
                <?php
                break;

              default:
                // Special handling for stock total amount field
                if ($pn_finance_manager_input['id'] === 'pn_finance_manager_stock_total_amount' && !empty($current_value)) {
                  ?>
                    <div class="pn-finance-manager-stock-amount-value">
                      <span class="pn-finance-manager-stock-amount-label"><?php esc_html_e('Shares:', 'pn-finance-manager'); ?></span>
                      <span class="pn-finance-manager-stock-amount-number"><?php echo esc_html($current_value); ?></span>
                    </div>
                  <?php
                } else {
                  ?>
                    <span class="pn-finance-manager-text-value"><?php echo esc_html($current_value); ?></span>
                  <?php
                }
                break;
            }
            break;

          case 'select':
            if (!empty($pn_finance_manager_input['options']) && is_array($pn_finance_manager_input['options'])) {
              if (array_key_exists('multiple', $pn_finance_manager_input) && $pn_finance_manager_input['multiple']) {
                // Handle multiple select
                $selected_values = is_array($current_value) ? $current_value : array();
                if (!empty($selected_values)) {
                  ?>
                  <div class="pn-finance-manager-select-values pn-finance-manager-select-values-column">
                    <?php foreach ($selected_values as $value): ?>
                      <?php if (isset($pn_finance_manager_input['options'][$value])): ?>
                        <div class="pn-finance-manager-select-value-item"><?php echo esc_html($pn_finance_manager_input['options'][$value]); ?></div>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                  <?php
                }
              } else {
                // Handle single select
                $current_value = is_scalar($current_value) ? (string)$current_value : '';
                if (isset($pn_finance_manager_input['options'][$current_value])) {
                  ?>
                  <span class="pn-finance-manager-select-value"><?php echo esc_html($pn_finance_manager_input['options'][$current_value]); ?></span>
                  <?php
                }
              }
            }
            break;

          case 'textarea':
            ?>
              <div class="pn-finance-manager-textarea-value"><?php echo wp_kses_post(nl2br($current_value)); ?></div>
            <?php
            break;
          case 'image':
            if (!empty($current_value)) {
              $image_ids = is_array($current_value) ? $current_value : explode(',', $current_value);
              ?>
                <div class="pn-finance-manager-image-gallery">
                  <?php foreach ($image_ids as $image_id): ?>
                    <div class="pn-finance-manager-image-item">
                      <?php echo wp_get_attachment_image($image_id, 'medium'); ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php
            } else {
              ?>
                <span class="pn-finance-manager-no-image"><?php esc_html_e('No images uploaded', 'pn-finance-manager'); ?></span>
              <?php
            }
            break;
          case 'editor':
            ?>
              <div class="pn-finance-manager-editor-content"><?php echo wp_kses_post($current_value); ?></div>
            <?php
            break;
          case 'html':
            if (!empty($pn_finance_manager_input['html_content'])) {
              ?>
                <div class="pn-finance-manager-html-content"><?php echo wp_kses_post(do_shortcode($pn_finance_manager_input['html_content'])); ?></div>
              <?php
            }
            break;
          case 'html_multi':
            switch ($pn_finance_manager_type) {
              case 'user':
                $html_multi_fields_length = !empty(get_user_meta($pn_finance_manager_id, $pn_finance_manager_input['html_multi_fields'][0]['id'], true)) ? count(get_user_meta($pn_finance_manager_id, $pn_finance_manager_input['html_multi_fields'][0]['id'], true)) : 0;
                break;
              case 'post':
                $html_multi_fields_length = !empty(get_post_meta($pn_finance_manager_id, $pn_finance_manager_input['html_multi_fields'][0]['id'], true)) ? count(get_post_meta($pn_finance_manager_id, $pn_finance_manager_input['html_multi_fields'][0]['id'], true)) : 0;
                break;
              case 'option':
                $html_multi_fields_length = !empty(get_option($pn_finance_manager_input['html_multi_fields'][0]['id'])) ? count(get_option($pn_finance_manager_input['html_multi_fields'][0]['id'])) : 0;
            }

            ?>
              <div class="pn-finance-manager-html-multi-content">
                <?php if ($html_multi_fields_length): ?>
                  <?php foreach (range(0, ($html_multi_fields_length - 1)) as $length_index): ?>
                    <div class="pn-finance-manager-html-multi-group pn-finance-manager-display-table pn-finance-manager-width-100-percent">
                      <?php foreach ($pn_finance_manager_input['html_multi_fields'] as $index => $html_multi_field): ?>
                          <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-60-percent">
                            <label><?php echo esc_html($html_multi_field['label']); ?></label>
                          </div>

                          <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-40-percent">
                            <?php self::pn_finance_manager_input_display($html_multi_field, $pn_finance_manager_type, $pn_finance_manager_id, 1, $length_index); ?>
                          </div>
                      <?php endforeach ?>
                    </div>
                  <?php endforeach ?>
                <?php endif; ?>
              </div>
            <?php
            break;
        }
        ?>
      </div>
    <?php
  }

  public static function pn_finance_manager_sanitizer($value, $node = '', $type = '', $field_config = []) {
    // Use the new validation system
    $result = PN_FINANCE_MANAGER_Validation::pn_finance_manager_validate_and_sanitize($value, $node, $type, $field_config);
    
    // If validation failed, return empty value and log the error
    if (is_wp_error($result)) {
        return '';
    }
    
    return $result;
  }
}