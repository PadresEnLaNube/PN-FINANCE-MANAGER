<?php

/**
 * Fired during plugin deactivation
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 *
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Deactivator {

	/**
	 * Plugin deactivation functions
	 *
	 * Functions to be loaded on plugin deactivation. This actions remove roles, options and post information attached to the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function pn_personal_finance_manager_deactivate() {
		$plugin_post = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset();
		
		// Clear scheduled cron jobs
		wp_clear_scheduled_hook('pn_personal_finance_manager_update_stock_symbols_event');
		wp_clear_scheduled_hook('pn_personal_finance_manager_daily_stock_price_recording_event');
		wp_clear_scheduled_hook('pn_personal_finance_manager_update_crypto_symbols_event');
		wp_clear_scheduled_hook('pn_personal_finance_manager_daily_crypto_price_recording_event');
		wp_clear_scheduled_hook('pn_personal_finance_manager_watchlist_check_alerts_event');

		if (get_option('pn_personal_finance_manager_options_remove') == 'on') {
      remove_role('pn_personal_finance_manager_role_manager');

      $pn_personal_finance_manager_asset = get_posts(['fields' => 'ids', 'numberposts' => -1, 'post_type' => 'pnpfm_asset', 'post_status' => 'any', ]);

      if (!empty($pn_personal_finance_manager_asset)) {
        foreach ($pn_personal_finance_manager_asset as $post_id) {
          wp_delete_post($post_id, true);
        }
      }

      foreach ($plugin_post->pn_personal_finance_manager_get_fields() as $pn_personal_finance_manager_option) {
        delete_option($pn_personal_finance_manager_option['id']);
      }
    }

    update_option('pn_personal_finance_manager_options_changed', true);
	}
}