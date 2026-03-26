<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin admin area. This file also includes all of the dependencies used by the plugin, registers the activation and deactivation functions, and defines a function that starts the plugin.
 *
 * @link              padresenlanube.com/
 * @since             1.0.0
 * @package           PN_FINANCE_MANAGER
 *
 * @wordpress-plugin
 * Plugin Name:       Finance Manager - PN
 * Plugin URI:        https://padresenlanube.com/plugins/pn-finance-manager/
 * Description:       Personal finance manager with asset and liability tracking, real-time stock portfolio monitoring (Twelve Data), historical price charts, multi-currency support (27 currencies), user role management, and admin diagnostic tools.
 * Version:           1.1.3
 * Requires at least: 3.0
 * Requires PHP:      7.2
 * Author:            Padres en la Nube
 * Author URI:        https://padresenlanube.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pn-finance-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PN_FINANCE_MANAGER_VERSION', '1.1.3');
define('PN_FINANCE_MANAGER_DIR', plugin_dir_path(__FILE__));
define('PN_FINANCE_MANAGER_URL', plugin_dir_url(__FILE__));
define('PN_FINANCE_MANAGER_FILE', __FILE__);
define('PN_FINANCE_MANAGER_CPTS', [
	'pnfm_asset' => 'Asset',
	'pnfm_liability' => 'Liability',
]);

/**
 * Plugin role baseCPT capabilities
 */
/**
 * Plugin role card capabilities
 */
$pn_finance_manager_role_cpt_capabilities = [];

foreach (PN_FINANCE_MANAGER_CPTS as $cpt_key => $cpt_value) {
	$pn_finance_manager_role_cpt_capabilities[$cpt_key] = [
		'edit_post' 				=> 'edit_' . $cpt_key,
		'edit_posts' 				=> 'edit_' . $cpt_key,
		'edit_private_posts' 		=> 'edit_private_' . $cpt_key,
		'edit_published_posts' 		=> 'edit_published_' . $cpt_key,
		'edit_others_posts' 		=> 'edit_others_' . $cpt_key,
		'publish_posts' 			=> 'publish_' . $cpt_key,

		// Post reading capabilities
		'read_post' 				=> 'read_' . $cpt_key,
		'read_private_posts' 		=> 'read_private_' . $cpt_key,
		
		// Post deletion capabilities
		'delete_post' 				=> 'delete_' . $cpt_key,
		'delete_posts' 				=> 'delete_' . $cpt_key,
		'delete_private_posts' 		=> 'delete_private_' . $cpt_key,
		'delete_published_posts' 	=> 'delete_published_' . $cpt_key,
		'delete_others_posts'		=> 'delete_others_' . $cpt_key,

		// Media capabilities
		'upload_files' 				=> 'upload_files',

		// Taxonomy capabilities
		'manage_terms' 				=> 'manage_' . $cpt_key . '_category',
		'edit_terms' 				=> 'edit_' . $cpt_key . '_category',
		'delete_terms' 				=> 'delete_' . $cpt_key . '_category',
		'assign_terms' 				=> 'assign_' . $cpt_key . '_category',

		// Options capabilities
		'manage_options' 			=> 'manage_' . $cpt_key . '_options'
	];
	
	define('PN_FINANCE_MANAGER_ROLE_' . strtoupper($cpt_key) . '_CAPABILITIES', $pn_finance_manager_role_cpt_capabilities[$cpt_key]);
}

/**
 * Plugin KSES allowed HTML elements and attributes
 */
$pn_finance_manager_kses = [
	// Basic text elements
	'div' => ['id' => [], 'class' => [], 'data-pn-finance-manager-watchlist-item-id' => []],
	'section' => ['id' => [], 'class' => []],
	'article' => ['id' => [], 'class' => []],
	'aside' => ['id' => [], 'class' => []],
	'footer' => ['id' => [], 'class' => []],
	'header' => ['id' => [], 'class' => []],
	'main' => ['id' => [], 'class' => []],
	'nav' => ['id' => [], 'class' => []],
	'p' => ['id' => [], 'class' => []],
	'span' => ['id' => [], 'class' => [], 'style' => []],
	'small' => ['id' => [], 'class' => []],
	'em' => [],
	'strong' => [],
	'br' => [],

	// Headings
	'h1' => ['id' => [], 'class' => []],
	'h2' => ['id' => [], 'class' => []],
	'h3' => ['id' => [], 'class' => []],
	'h4' => ['id' => [], 'class' => []],
	'h5' => ['id' => [], 'class' => []],
	'h6' => ['id' => [], 'class' => []],

	// Lists
	'ul' => ['id' => [], 'class' => []],
	'ol' => ['id' => [], 'class' => []],
	'li' => [
		'id' => [],
		'class' => [],
		'style' => [],
		'data-pn-finance-manager-asset-type' => [],
		'data-pn-finance-manager-asset-value' => [],
		'data-pn-finance-manager-asset-portfolio-percent' => [],
		'data-pn-finance-manager-liability-type' => [],
	],

	// Links and media
	'a' => [
		'id' => [],
		'class' => [],
		'href' => [],
		'title' => [],
		'target' => [],
		'data-pn-finance-manager-ajax-type' => [],
		'data-pn-finance-manager-popup-id' => [],
	],
	'img' => [
		'id' => [],
		'class' => [],
		'src' => [],
		'alt' => [],
		'title' => [],
	],
	'i' => [
		'id' => [], 
		'class' => [], 
		'title' => []
	],

	// Forms and inputs
	'form' => [
		'id' => [],
		'class' => [],
		'action' => [],
		'method' => [],
	],
	'input' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'type' => [],
		'checked' => [],
		'multiple' => [],
		'disabled' => [],
		'value' => [],
		'placeholder' => [],
		'data-pn-finance-manager-parent' => [],
		'data-pn-finance-manager-parent-option' => [],
		'data-pn-finance-manager-type' => [],
		'data-pn-finance-manager-subtype' => [],
		'data-pn-finance-manager-user-id' => [],
		'data-pn-finance-manager-post-id' => [],
		'data-pn-finance-manager-item-id' => [],
		'min' => [],
		'max' => [],
	],
	'select' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'type' => [],
		'checked' => [],
		'multiple' => [],
		'disabled' => [],
		'value' => [],
		'placeholder' => [],
		'data-placeholder' => [],
		'data-pn-finance-manager-parent' => [],
		'data-pn-finance-manager-parent-option' => [],
	],
	'option' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'disabled' => [],
		'selected' => [],
		'value' => [],
		'placeholder' => [],
	],
	'textarea' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'type' => [],
		'multiple' => [],
		'disabled' => [],
		'value' => [],
		'placeholder' => [],
		'data-pn-finance-manager-parent' => [],
		'data-pn-finance-manager-parent-option' => [],
	],
	'label' => [
		'id' => [],
		'class' => [],
		'for' => [],
	],
	'button' => [
		'id' => [],
		'class' => [],
		'type' => [],
		'title' => [],
		'data-pn-finance-manager-item-id' => [],
	],
];

foreach (PN_FINANCE_MANAGER_CPTS as $cpt_key => $cpt_value) {
	$cpt_full = str_replace('pnfm_', 'pn_finance_manager_', $cpt_key);
	$pn_finance_manager_kses['li']['data-' . $cpt_full . '-id'] = [];
}

// Now define the constant with the complete array
define('PN_FINANCE_MANAGER_KSES', $pn_finance_manager_kses);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pn-finance-manager-activator.php
 */
function pn_finance_manager_activation_hook() {
	// Manually include required class for activation, as it runs before plugin files are loaded.
	require_once plugin_dir_path(__FILE__) . 'includes/class-pn-finance-manager-functions-user.php';

	// Flush rewrite rules
	flush_rewrite_rules();

	// Schedule cron job
	if (!wp_next_scheduled('pn_finance_manager_update_stock_symbols_event')) {
		// Schedule to run once a week.
		wp_schedule_event(time(), 'weekly', 'pn_finance_manager_update_stock_symbols_event');
	}

	// Clear any previous redirect state
	delete_option('pn_finance_manager_redirecting');

	// Set transient for activation redirect
	if (!get_transient('pn_finance_manager_just_activated')) {
		set_transient('pn_finance_manager_just_activated', true, 30);
	}
}

// Register activation hook
register_activation_hook(PN_FINANCE_MANAGER_FILE, 'pn_finance_manager_activation_hook');

/**
 * The code that runs during plugin deactivation.
 * This code should be used to remove all data created by the plugin.
 */
function pn_finance_manager_deactivation_cleanup() {
	// Manually include required class for deactivation.
	require_once plugin_dir_path(__FILE__) . 'includes/class-pn-finance-manager-functions-user.php';

	// Clean redirect state
	delete_option('pn_finance_manager_redirecting');

	if (get_option('pn_finance_manager_options_remove')) {
		// Remove plugin options
		delete_option('pn_finance_manager_options_remove');
		delete_option('pn-finance-manager');
	}

	// Unschedule cron jobs
	wp_clear_scheduled_hook('pn_finance_manager_update_stock_symbols_event');
	wp_clear_scheduled_hook('pn_finance_manager_update_stock_symbols_cron');

	// Flush rewrite rules
	flush_rewrite_rules();
}
register_deactivation_hook(PN_FINANCE_MANAGER_FILE, 'pn_finance_manager_deactivation_cleanup');

/**
 * The core plugin class that is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-pn-finance-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off the plugin from this point in the file does not affect the page life cycle.
 *
 * @since    1.0.0
 */
function pn_finance_manager_run() {
	$plugin = new PN_FINANCE_MANAGER();
	$plugin->pn_finance_manager_run();
}

// Initialize the plugin on init hook instead of plugins_loaded
add_action('init', 'pn_finance_manager_run', 0);

/**
 * Stock Symbols Cache Cron Configuration
 * 
 * @since    1.0.5
 */

// Schedule the cron job for stock symbols cache updates
if (!wp_next_scheduled('pn_finance_manager_update_stock_symbols_cron')) {
    // Schedule for 2:00 AM tomorrow, then daily
    $tomorrow_2am = strtotime('tomorrow 2:00 AM');
    wp_schedule_event($tomorrow_2am, 'daily', 'pn_finance_manager_update_stock_symbols_cron');
}

// Add the cron callback function
add_action('pn_finance_manager_update_stock_symbols_cron', 'pn_finance_manager_update_stock_symbols_cron_callback');

/**
 * Cron callback function for updating stock symbols cache
 * 
 * @since    1.0.5
 */
function pn_finance_manager_update_stock_symbols_cron_callback() {
    try {
        // Include the stocks class
        require_once plugin_dir_path(__FILE__) . 'includes/class-pn-finance-manager-stocks.php';
        
        if (class_exists('PN_FINANCE_MANAGER_Stocks')) {
            $stocks = new PN_FINANCE_MANAGER_Stocks();
            $result = $stocks->pn_finance_manager_update_stock_symbols_from_api_cron();
            
            if ($result === true) {
                error_log('PnFinanceManager: Cron job completed successfully - stock symbols cache updated.');
            } else {
                error_log('PnFinanceManager: Cron job failed - ' . (is_wp_error($result) ? $result->get_error_message() : 'Unknown error'));
            }
        } else {
            error_log('PnFinanceManager: Cron job failed - PN_FINANCE_MANAGER_Stocks class not found.');
        }
    } catch (Exception $e) {
        error_log('PnFinanceManager: Cron job failed with exception - ' . $e->getMessage());
    }
}