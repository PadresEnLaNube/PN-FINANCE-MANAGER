<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    pn-personal-finance-manager
 * @subpackage pn-personal-finance-manager/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Activator {
	/**
   * Plugin activation functions
   *
   * Functions to be loaded on plugin activation. This actions creates roles, options and post information attached to the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function pn_personal_finance_manager_activate() {
    require_once PN_PERSONAL_FINANCE_MANAGER_DIR . 'includes/class-pn-personal-finance-manager-functions-post.php';
    require_once PN_PERSONAL_FINANCE_MANAGER_DIR . 'includes/class-pn-personal-finance-manager-functions-attachment.php';
    require_once PN_PERSONAL_FINANCE_MANAGER_DIR . 'includes/class-pn-personal-finance-manager-stocks.php';

    $post_functions = new PN_PERSONAL_FINANCE_MANAGER_Functions_Post();
    $attachment_functions = new PN_PERSONAL_FINANCE_MANAGER_Functions_Attachment();
    $stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();

    add_role('pn_personal_finance_manager_role_manager', esc_html(__('Personal Finance Manager - PN', 'pn-personal-finance-manager')));

    $pn_personal_finance_manager_role_admin = get_role('administrator');
    $pn_personal_finance_manager_role_manager = get_role('pn_personal_finance_manager_role_manager');

    $pn_personal_finance_manager_role_manager->add_cap('upload_files'); 
    $pn_personal_finance_manager_role_manager->add_cap('read'); 

    foreach (PN_PERSONAL_FINANCE_MANAGER_CPTS as $cpt_key => $cpt_name) { 
      $pn_personal_finance_manager_role_admin->add_cap('manage_' . $cpt_key . '_options');
      $pn_personal_finance_manager_role_manager->add_cap('manage_' . $cpt_key . '_options');
    }

    // Schedule cron jobs
    if (!wp_next_scheduled('pn_personal_finance_manager_update_stock_symbols_event')) {
      wp_schedule_event(time(), 'daily', 'pn_personal_finance_manager_update_stock_symbols_event');
    }
    
    if (!wp_next_scheduled('pn_personal_finance_manager_daily_stock_price_recording_event')) {
      wp_schedule_event(time(), 'daily', 'pn_personal_finance_manager_daily_stock_price_recording_event');
    }

    // Crypto cron jobs
    if (!wp_next_scheduled('pn_personal_finance_manager_update_crypto_symbols_event')) {
      wp_schedule_event(time(), 'daily', 'pn_personal_finance_manager_update_crypto_symbols_event');
    }

    if (!wp_next_scheduled('pn_personal_finance_manager_daily_crypto_price_recording_event')) {
      wp_schedule_event(time(), 'daily', 'pn_personal_finance_manager_daily_crypto_price_recording_event');
    }

    // Watchlist cron job
    if (!wp_next_scheduled('pn_personal_finance_manager_watchlist_check_alerts_event')) {
      wp_schedule_event(time(), 'daily', 'pn_personal_finance_manager_watchlist_check_alerts_event');
    }

    if (empty(get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'pnpfm_asset', 'post_status' => 'any', ]))) {
      $pn_personal_finance_manager_title = __('Asset Test', 'pn-personal-finance-manager');
      $pn_personal_finance_manager_id = $post_functions->pn_personal_finance_manager_insert_post(esc_html($pn_personal_finance_manager_title), $pn_personal_finance_manager_post_content, '', sanitize_title(esc_html($pn_personal_finance_manager_title)), 'pnpfm_asset', 'publish', 1);

      if (class_exists('Polylang') && function_exists('pll_default_language')) {
        $language = pll_default_language();
        pll_set_post_language($pn_personal_finance_manager_id, $language);
        $locales = pll_languages_list(['hide_empty' => false]);

        if (!empty($locales)) {
          foreach ($locales as $locale) {
            if ($locale != $language) {
              $pn_personal_finance_manager_title = __('Asset Test', 'pn-personal-finance-manager') . ' ' . $locale;
              $translated_pn_personal_finance_manager_id = $post_functions->pn_personal_finance_manager_insert_post(esc_html($pn_personal_finance_manager_title), $pn_personal_finance_manager_post_content, '', sanitize_title(esc_html($pn_personal_finance_manager_title)), 'pnpfm_asset', 'publish', 1);

              pll_set_post_language($translated_pn_personal_finance_manager_id, $locale);

              pll_save_post_translations([
                $language => $pn_personal_finance_manager_id,
                $locale => $translated_pn_personal_finance_manager_id,
              ]);
            }
          }
        }
      }
    }

    // Set default values for new options
    if (!get_option('pn_personal_finance_manager_currency')) {
      update_option('pn_personal_finance_manager_currency', 'EUR');
    }
    
    if (!get_option('pn_personal_finance_manager_liability_slug')) {
      update_option('pn_personal_finance_manager_liability_slug', 'pn-personal-finance-manager-liability');
    }
    
    if (!get_option('pn_personal_finance_manager_asset_slug')) {
      update_option('pn_personal_finance_manager_asset_slug', 'pn-personal-finance-manager-asset');
    }

    update_option('pn_personal_finance_manager_options_changed', true);
  }
}