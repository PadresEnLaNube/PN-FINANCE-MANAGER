<?php
/**
 * Provide a common footer area view for the plugin
 *
 * This file is used to markup the common footer facing aspects of the plugin.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 *
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/common/templates
 */

  if (!defined('ABSPATH')) exit; // Exit if accessed directly

  $pn_personal_finance_manager_data = $GLOBALS['pn_personal_finance_manager_data'];
?>

<div id="pn-personal-finance-manager-main-message" class="pn-personal-finance-manager-main-message pn-personal-finance-manager-display-none-soft pn-personal-finance-manager-z-index-top" style="display:none;" data-user-id="<?php echo esc_attr($pn_personal_finance_manager_data['user_id']); ?>" data-post-id="<?php echo esc_attr($pn_personal_finance_manager_data['post_id']); ?>">
  <span id="pn-personal-finance-manager-main-message-span"></span><i class="material-icons-outlined pn-personal-finance-manager-vertical-align-bottom pn-personal-finance-manager-ml-20 pn-personal-finance-manager-cursor-pointer pn-personal-finance-manager-color-white pn-personal-finance-manager-close-icon">close</i>

  <div id="pn-personal-finance-manager-bar-wrapper">
  	<div id="pn-personal-finance-manager-bar"></div>
  </div>
</div>
