<?php
/**
 * Provide common popups for the plugin
 *
 * This file is used to markup the common popups of the plugin.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 *
 * @package    pn-personal-finance-manager
 * @subpackage pn-personal-finance-manager/common/templates
 */

  if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>
<div class="pn-personal-finance-manager-popup-overlay pn-personal-finance-manager-display-none-soft"></div>
<div class="pn-personal-finance-manager-menu-more-overlay pn-personal-finance-manager-display-none-soft"></div>

<?php foreach (PN_PERSONAL_FINANCE_MANAGER_CPTS as $cpt => $cpt_name) : ?>
  <?php $cpt_full = str_replace('pnpfm_', 'pn_personal_finance_manager_', $cpt); ?>
  <div id="pn-personal-finance-manager-popup-<?php echo esc_attr($cpt_full); ?>-add" class="pn-personal-finance-manager-popup pn-personal-finance-manager-popup-size-medium pn-personal-finance-manager-display-none-soft" data-pn-personal-finance-manager-no-dismiss="true">
    <button type="button" class="pn-personal-finance-manager-popup-close-fixed"><i class="material-icons-outlined">close</i></button>
    <?php PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_popup_loader(); ?>
  </div>

  <div id="pn-personal-finance-manager-popup-<?php echo esc_attr($cpt_full); ?>-view" class="pn-personal-finance-manager-popup pn-personal-finance-manager-popup-size-medium pn-personal-finance-manager-display-none-soft" data-pn-personal-finance-manager-no-dismiss="true">
    <button type="button" class="pn-personal-finance-manager-popup-close-fixed"><i class="material-icons-outlined">close</i></button>
    <?php PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_popup_loader(); ?>
  </div>

  <div id="pn-personal-finance-manager-popup-<?php echo esc_attr($cpt_full); ?>-edit" class="pn-personal-finance-manager-popup pn-personal-finance-manager-popup-size-medium pn-personal-finance-manager-display-none-soft" data-pn-personal-finance-manager-no-dismiss="true">
    <button type="button" class="pn-personal-finance-manager-popup-close-fixed"><i class="material-icons-outlined">close</i></button>
    <?php PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_popup_loader(); ?>
  </div>

  <div id="pn-personal-finance-manager-popup-<?php echo esc_attr($cpt_full); ?>-remove" class="pn-personal-finance-manager-popup pn-personal-finance-manager-popup-size-medium pn-personal-finance-manager-display-none-soft">
    <div class="pn-personal-finance-manager-popup-content">
      <div class="pn-personal-finance-manager-p-30">
        <h3 class="pn-personal-finance-manager-text-align-center"><?php echo esc_html($cpt_name); ?> <?php esc_html_e('removal', 'pn-personal-finance-manager'); ?></h3>
        <p class="pn-personal-finance-manager-text-align-center"><?php echo esc_html($cpt_name); ?> <?php esc_html_e('will be completely deleted. This process cannot be reversed and cannot be recovered.', 'pn-personal-finance-manager'); ?></p>

        <div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
          <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-50-percent pn-personal-finance-manager-text-align-center">
            <a href="#" class="pn-personal-finance-manager-popup-close pn-personal-finance-manager-text-decoration-none pn-personal-finance-manager-font-size-small"><?php esc_html_e('Cancel', 'pn-personal-finance-manager'); ?></a>
          </div>
          <div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-50-percent pn-personal-finance-manager-text-align-center">
            <a href="#" class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini pn-personal-finance-manager-<?php echo esc_attr($cpt); ?>-remove" data-pn-personal-finance-manager-post-type="<?php echo esc_attr($cpt_full); ?>"><?php esc_html_e('Remove', 'pn-personal-finance-manager'); ?> <?php echo esc_html($cpt_name); ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>