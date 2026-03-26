<?php
/**
 * Provide common popups for the plugin
 *
 * This file is used to markup the common popups of the plugin.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 *
 * @package    pn-finance-manager
 * @subpackage pn-finance-manager/common/templates
 */

  if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>
<div class="pn-finance-manager-popup-overlay pn-finance-manager-display-none-soft"></div>
<div class="pn-finance-manager-menu-more-overlay pn-finance-manager-display-none-soft"></div>

<?php foreach (PN_FINANCE_MANAGER_CPTS as $cpt => $cpt_name) : ?>
  <?php $cpt_full = str_replace('pnfm_', 'pn_finance_manager_', $cpt); ?>
  <div id="pn-finance-manager-popup-<?php echo esc_attr($cpt_full); ?>-add" class="pn-finance-manager-popup pn-finance-manager-popup-size-medium pn-finance-manager-display-none-soft" data-pn-finance-manager-no-dismiss="true">
    <button type="button" class="pn-finance-manager-popup-close-fixed"><i class="material-icons-outlined">close</i></button>
    <?php PN_FINANCE_MANAGER_Data::pn_finance_manager_popup_loader(); ?>
  </div>

  <div id="pn-finance-manager-popup-<?php echo esc_attr($cpt_full); ?>-view" class="pn-finance-manager-popup pn-finance-manager-popup-size-medium pn-finance-manager-display-none-soft" data-pn-finance-manager-no-dismiss="true">
    <button type="button" class="pn-finance-manager-popup-close-fixed"><i class="material-icons-outlined">close</i></button>
    <?php PN_FINANCE_MANAGER_Data::pn_finance_manager_popup_loader(); ?>
  </div>

  <div id="pn-finance-manager-popup-<?php echo esc_attr($cpt_full); ?>-edit" class="pn-finance-manager-popup pn-finance-manager-popup-size-medium pn-finance-manager-display-none-soft" data-pn-finance-manager-no-dismiss="true">
    <button type="button" class="pn-finance-manager-popup-close-fixed"><i class="material-icons-outlined">close</i></button>
    <?php PN_FINANCE_MANAGER_Data::pn_finance_manager_popup_loader(); ?>
  </div>

  <div id="pn-finance-manager-popup-<?php echo esc_attr($cpt_full); ?>-remove" class="pn-finance-manager-popup pn-finance-manager-popup-size-medium pn-finance-manager-display-none-soft">
    <div class="pn-finance-manager-popup-content">
      <div class="pn-finance-manager-p-30">
        <h3 class="pn-finance-manager-text-align-center"><?php echo esc_html($cpt_name); ?> <?php esc_html_e('removal', 'pn-finance-manager'); ?></h3>
        <p class="pn-finance-manager-text-align-center"><?php echo esc_html($cpt_name); ?> <?php esc_html_e('will be completely deleted. This process cannot be reversed and cannot be recovered.', 'pn-finance-manager'); ?></p>

        <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
          <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-text-align-center">
            <a href="#" class="pn-finance-manager-popup-close pn-finance-manager-text-decoration-none pn-finance-manager-font-size-small"><?php esc_html_e('Cancel', 'pn-finance-manager'); ?></a>
          </div>
          <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-text-align-center">
            <a href="#" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-<?php echo esc_attr($cpt); ?>-remove" data-pn-finance-manager-post-type="<?php echo esc_attr($cpt_full); ?>"><?php esc_html_e('Remove', 'pn-finance-manager'); ?> <?php echo esc_html($cpt_name); ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>