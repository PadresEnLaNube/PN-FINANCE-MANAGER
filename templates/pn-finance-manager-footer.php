<?php
/**
 * Provide a common footer area view for the plugin
 *
 * This file is used to markup the common footer facing aspects of the plugin.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 *
 * @package    PN_FINANCE_MANAGER
 * @subpackage PN_FINANCE_MANAGER/common/templates
 */

  if (!defined('ABSPATH')) exit; // Exit if accessed directly

  $pn_finance_manager_data = $GLOBALS['pn_finance_manager_data'];
?>

<div id="pn-finance-manager-main-message" class="pn-finance-manager-main-message pn-finance-manager-display-none-soft pn-finance-manager-z-index-top" style="display:none;" data-user-id="<?php echo esc_attr($pn_finance_manager_data['user_id']); ?>" data-post-id="<?php echo esc_attr($pn_finance_manager_data['post_id']); ?>">
  <span id="pn-finance-manager-main-message-span"></span><i class="material-icons-outlined pn-finance-manager-vertical-align-bottom pn-finance-manager-ml-20 pn-finance-manager-cursor-pointer pn-finance-manager-color-white pn-finance-manager-close-icon">close</i>

  <div id="pn-finance-manager-bar-wrapper">
  	<div id="pn-finance-manager-bar"></div>
  </div>
</div>
