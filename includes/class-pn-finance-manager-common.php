<?php
/**
 * The-global functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to enqueue the-global stylesheet and JavaScript.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_FINANCE_MANAGER
 * @subpackage PN_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_FINANCE_MANAGER_Common {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets.
	 *
	 * @since    1.0.0
	 */
	public function pn_finance_manager_enqueue_styles() {
		if (!wp_style_is('wph-material-icons-outlined', 'enqueued')) {
			wp_enqueue_style('wph-material-icons-outlined', PN_FINANCE_MANAGER_URL . 'assets/css/material-icons-outlined.min.css', [], $this->version, 'all');
		}

		if (!wp_style_is($this->plugin_name . '-popups', 'enqueued')) {
				wp_enqueue_style($this->plugin_name . '-popups', PN_FINANCE_MANAGER_URL . 'assets/css/pn-finance-manager-popups.css', [], $this->version, 'all');
		}

		if (!wp_style_is($this->plugin_name . '-selector', 'enqueued')) {
				wp_enqueue_style($this->plugin_name . '-selector', PN_FINANCE_MANAGER_URL . 'assets/css/pn-finance-manager-selector.css', [], $this->version, 'all');
		}

		if (!wp_style_is('wph-trumbowyg', 'enqueued')) {
				wp_enqueue_style('wph-trumbowyg', PN_FINANCE_MANAGER_URL . 'assets/css/trumbowyg.min.css', [], $this->version, 'all');
		}

		if (!wp_style_is('wph-tooltipster', 'enqueued')) {
				wp_enqueue_style('wph-tooltipster', PN_FINANCE_MANAGER_URL . 'assets/css/tooltipster.min.css', [], $this->version, 'all');
		}

		if (!wp_style_is('wph-owl', 'enqueued')) {
				wp_enqueue_style('wph-owl', PN_FINANCE_MANAGER_URL . 'assets/css/owl.min.css', [], $this->version, 'all');
		}

		if (!wp_style_is($this->plugin_name . '-stocks', 'enqueued')) {
				wp_enqueue_style($this->plugin_name . '-stocks', PN_FINANCE_MANAGER_URL . 'assets/css/pn-finance-manager-stocks.css', [], $this->version, 'all');
		}

		if (!wp_style_is($this->plugin_name . '-statistics', 'enqueued')) {
				wp_enqueue_style($this->plugin_name . '-statistics', PN_FINANCE_MANAGER_URL . 'assets/css/pn-finance-manager-statistics.css', [], $this->version, 'all');
		}

		if (!wp_style_is($this->plugin_name . '-watchlist', 'enqueued')) {
				wp_enqueue_style($this->plugin_name . '-watchlist', PN_FINANCE_MANAGER_URL . 'assets/css/pn-finance-manager-watchlist.css', [], $this->version, 'all');
		}

		wp_enqueue_style($this->plugin_name, PN_FINANCE_MANAGER_URL . 'assets/css/pn-finance-manager.css', [], $this->version, 'all');

		$primary_color = get_option('pn_finance_manager_color_primary', '#008080');
		if (!empty($primary_color)) {
			$safe_color = sanitize_hex_color($primary_color);
			if ($safe_color) {
				$inline_css = ":root { --color-main: {$safe_color}; --bg-color-main: {$safe_color}; --border-color-main: {$safe_color}; }";
				wp_add_inline_style($this->plugin_name, $inline_css);
			}
		}
	}

	/**
	 * Register the JavaScript.
	 *
	 * @since    1.0.0
	 */
	public function pn_finance_manager_enqueue_scripts() {
		error_log('PnFinanceManager Debug: Common enqueue_scripts called');
		
    if(!wp_script_is('jquery-ui-sortable', 'enqueued')) {
			wp_enqueue_script('jquery-ui-sortable');
    }

    if(!wp_script_is('wph-trumbowyg', 'enqueued')) {
			wp_enqueue_script('wph-trumbowyg', PN_FINANCE_MANAGER_URL . 'assets/js/trumbowyg.min.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
    }

		wp_localize_script('wph-trumbowyg', 'pn_finance_manager_trumbowyg', [
			'path' => PN_FINANCE_MANAGER_URL . 'assets/media/trumbowyg-icons.svg',
		]);

    if(!wp_script_is($this->plugin_name . '-popups', 'enqueued')) {
      wp_enqueue_script($this->plugin_name . '-popups', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-popups.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
    }

    if(!wp_script_is($this->plugin_name . '-selector', 'enqueued')) {
      wp_enqueue_script($this->plugin_name . '-selector', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-selector.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
    }

    if(!wp_script_is('wph-tooltipster', 'enqueued')) {
			wp_enqueue_script('wph-tooltipster', PN_FINANCE_MANAGER_URL . 'assets/js/tooltipster.min.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
    }

    if(!wp_script_is('wph-owl', 'enqueued')) {
			wp_enqueue_script('wph-owl', PN_FINANCE_MANAGER_URL . 'assets/js/owl.min.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
    }

		wp_enqueue_script($this->plugin_name, PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		wp_enqueue_script($this->plugin_name . '-aux', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-aux.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		wp_enqueue_script($this->plugin_name . '-forms', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-forms.js', ['jquery', 'jquery-ui-sortable'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		wp_enqueue_script($this->plugin_name . '-ajax', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-ajax.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		
		error_log('PnFinanceManager Debug: Loading stocks script in common: ' . PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-stocks.js');
		wp_enqueue_script($this->plugin_name . '-stocks', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-stocks.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);

		wp_enqueue_script('chartjs', PN_FINANCE_MANAGER_URL . 'assets/js/chart.min.js', [], '3.9.1', true);
		wp_enqueue_script($this->plugin_name . '-statistics', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-statistics.js', ['jquery', 'chartjs'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);

		wp_enqueue_script($this->plugin_name . '-watchlist', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-watchlist.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);

		// Localize scripts with global variables - moved to be available for all scripts
		wp_localize_script($this->plugin_name . '-stocks', 'pn_finance_manager_ajax', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'pn_finance_manager_ajax_nonce' => wp_create_nonce('pn-finance-manager-nonce'),
		]);

		wp_localize_script($this->plugin_name . '-stocks', 'pn_finance_manager_i18n', [
			'an_error_has_occurred' => esc_html(__('An error has occurred. Please try again in a few minutes.', 'pn-finance-manager')),
			'user_unlogged' => esc_html(__('Please create a new user or login to save the information.', 'pn-finance-manager')),
			'saved_successfully' => esc_html(__('Saved successfully', 'pn-finance-manager')),
			'removed_successfully' => esc_html(__('Removed successfully', 'pn-finance-manager')),
			'edit_image' => esc_html(__('Edit image', 'pn-finance-manager')),
			'edit_images' => esc_html(__('Edit images', 'pn-finance-manager')),
			'select_image' => esc_html(__('Select image', 'pn-finance-manager')),
			'select_images' => esc_html(__('Select images', 'pn-finance-manager')),
			'edit_video' => esc_html(__('Edit video', 'pn-finance-manager')),
			'edit_videos' => esc_html(__('Edit videos', 'pn-finance-manager')),
			'select_video' => esc_html(__('Select video', 'pn-finance-manager')),
			'select_videos' => esc_html(__('Select videos', 'pn-finance-manager')),
			'edit_audio' => esc_html(__('Edit audio', 'pn-finance-manager')),
			'edit_audios' => esc_html(__('Edit audios', 'pn-finance-manager')),
			'select_audio' => esc_html(__('Select audio', 'pn-finance-manager')),
			'select_audios' => esc_html(__('Select audios', 'pn-finance-manager')),
			'edit_file' => esc_html(__('Edit file', 'pn-finance-manager')),
			'edit_files' => esc_html(__('Edit files', 'pn-finance-manager')),
			'select_file' => esc_html(__('Select file', 'pn-finance-manager')),
			'select_files' => esc_html(__('Select files', 'pn-finance-manager')),
			'ordered_element' => esc_html(__('Ordered element', 'pn-finance-manager')),
			'select_option' => esc_html(__('Select option', 'pn-finance-manager')),
			'select_options' => esc_html(__('Select options', 'pn-finance-manager')),
			'copied' => esc_html(__('Copied', 'pn-finance-manager')),

			// Stock-related translations
			'select_stock_symbol' => esc_html(__('Select a stock symbol', 'pn-finance-manager')),
			'loading_stock_symbols' => esc_html(__('Loading stock symbols...', 'pn-finance-manager')),
			'no_stock_symbols' => esc_html(__('No stock symbols available', 'pn-finance-manager')),
			'error_loading_symbols' => esc_html(__('Error loading stock symbols', 'pn-finance-manager')),
			'search_new_symbol' => esc_html(__('Search new symbol', 'pn-finance-manager')),
			'search' => esc_html(__('Search', 'pn-finance-manager')),
			'searching_symbol' => esc_html(__('Searching...', 'pn-finance-manager')),
			'symbol_found' => esc_html(__('Symbol found and added!', 'pn-finance-manager')),
			'symbol_not_found' => esc_html(__('Symbol not found. Check the ticker and try again.', 'pn-finance-manager')),
			'enter_symbol' => esc_html(__('Enter a stock symbol (e.g. AAPL)', 'pn-finance-manager')),
			'select_correct_match' => esc_html(__('Multiple matches found. Select the correct one:', 'pn-finance-manager')),
			'add_symbol' => esc_html(__('Add', 'pn-finance-manager')),

			// Audio recorder translations
			'ready_to_record' => esc_html(__('Ready to record', 'pn-finance-manager')),
			'recording' => esc_html(__('Recording...', 'pn-finance-manager')),
			'recording_stopped' => esc_html(__('Recording stopped. Ready to play or transcribe.', 'pn-finance-manager')),
			'recording_completed' => esc_html(__('Recording completed. Ready to transcribe.', 'pn-finance-manager')),
			'microphone_error' => esc_html(__('Error: Could not access microphone', 'pn-finance-manager')),
			'no_audio_to_transcribe' => esc_html(__('No audio to transcribe', 'pn-finance-manager')),
			'invalid_response_format' => esc_html(__('Invalid server response format', 'pn-finance-manager')),
			'invalid_server_response' => esc_html(__('Invalid server response', 'pn-finance-manager')),
			'transcription_completed' => esc_html(__('Transcription completed', 'pn-finance-manager')),
			'no_transcription_received' => esc_html(__('No transcription received from server', 'pn-finance-manager')),
			'transcription_error' => esc_html(__('Error in transcription', 'pn-finance-manager')),
			'connection_error' => esc_html(__('Connection error', 'pn-finance-manager')),
			'connection_error_server' => esc_html(__('Connection error: Could not connect to server', 'pn-finance-manager')),
			'permission_error' => esc_html(__('Permission error: Security verification failed', 'pn-finance-manager')),
			'server_error' => esc_html(__('Server error: Internal server problem', 'pn-finance-manager')),
			'unknown_error' => esc_html(__('Unknown error', 'pn-finance-manager')),
			'processing_error' => esc_html(__('Error processing audio', 'pn-finance-manager')),

			// Portfolio & Liabilities translations
			'liabilities' => esc_html(__('Liabilities', 'pn-finance-manager')),
			'net_worth' => esc_html(__('Net Worth', 'pn-finance-manager')),
			'total_assets' => esc_html(__('Total Assets', 'pn-finance-manager')),
			'total_liabilities' => esc_html(__('Total Liabilities', 'pn-finance-manager')),
			'no_liabilities_found' => esc_html(__('No liabilities found.', 'pn-finance-manager')),
			'financial_overview' => esc_html(__('Financial Overview', 'pn-finance-manager')),

			// Fetch purchase price translations
			'fetch_price' => esc_html(__('Fetch price', 'pn-finance-manager')),
			'fetching_price' => esc_html(__('Fetching...', 'pn-finance-manager')),
			'price_fetch_error' => esc_html(__('Could not fetch price. Try again later.', 'pn-finance-manager')),

			// Crypto-related translations
			'select_crypto_symbol' => esc_html(__('Select a cryptocurrency', 'pn-finance-manager')),
			'loading_crypto_symbols' => esc_html(__('Loading cryptocurrencies...', 'pn-finance-manager')),
			'no_crypto_symbols' => esc_html(__('No cryptocurrencies available', 'pn-finance-manager')),
			'search_new_crypto' => esc_html(__('Search cryptocurrency', 'pn-finance-manager')),
			'searching_crypto' => esc_html(__('Searching...', 'pn-finance-manager')),
			'crypto_found' => esc_html(__('Cryptocurrency found and added!', 'pn-finance-manager')),
			'crypto_not_found' => esc_html(__('Cryptocurrency not found. Check the name and try again.', 'pn-finance-manager')),
			'enter_crypto_name' => esc_html(__('Enter cryptocurrency name (e.g. Solana)', 'pn-finance-manager')),

			// Watchlist translations
			'watchlist' => esc_html(__('Watchlist', 'pn-finance-manager')),
			'add_to_watchlist' => esc_html(__('Add to Watchlist', 'pn-finance-manager')),
			'remove_from_watchlist' => esc_html(__('Remove', 'pn-finance-manager')),
			'enable_alert' => esc_html(__('Enable Price Alert', 'pn-finance-manager')),
			'alert_threshold' => esc_html(__('Alert Threshold', 'pn-finance-manager')),
			'daily_change' => esc_html(__('Daily Change', 'pn-finance-manager')),
			'current_price' => esc_html(__('Current Price', 'pn-finance-manager')),
			'select_type' => esc_html(__('Select Type', 'pn-finance-manager')),
			'confirm_remove_watchlist' => esc_html(__('Are you sure you want to remove this item from your watchlist?', 'pn-finance-manager')),
			'watchlist_empty' => esc_html(__('Your watchlist is empty. Add stocks or cryptocurrencies to track.', 'pn-finance-manager')),
			'item_added' => esc_html(__('Item added to watchlist', 'pn-finance-manager')),
			'save_alerts' => esc_html(__('Save alerts', 'pn-finance-manager')),
			'alerts_saved' => esc_html(__('Saved!', 'pn-finance-manager')),

			// Export/Import translations
			'export_portfolio' => esc_html(__('Export Portfolio', 'pn-finance-manager')),
			'import_portfolio' => esc_html(__('Import Portfolio', 'pn-finance-manager')),
			'exporting' => esc_html(__('Exporting...', 'pn-finance-manager')),
			'importing' => esc_html(__('Importing...', 'pn-finance-manager')),
			'export_success' => esc_html(__('Portfolio exported successfully!', 'pn-finance-manager')),
			'import_success' => esc_html(__('Portfolio imported successfully!', 'pn-finance-manager')),
			'import_error' => esc_html(__('Error importing portfolio.', 'pn-finance-manager')),
			'no_file_selected' => esc_html(__('No file selected', 'pn-finance-manager')),
			'select_file_first' => esc_html(__('Please select a file first.', 'pn-finance-manager')),
			'invalid_file_format' => esc_html(__('Invalid file format.', 'pn-finance-manager')),
			'assets_imported' => esc_html(__('assets imported', 'pn-finance-manager')),
			'liabilities_imported' => esc_html(__('liabilities imported', 'pn-finance-manager')),
			'watchlist_items_imported' => esc_html(__('watchlist items imported', 'pn-finance-manager')),
			'confirm_import' => esc_html(__('This will add new entries to your portfolio. Existing data will not be overwritten. Continue?', 'pn-finance-manager')),

			// Currency preference translations
			'display_currency' => esc_html(__('Display Currency', 'pn-finance-manager')),
			'display_currency_desc' => esc_html(__('Choose the currency used to display your investments and portfolio values.', 'pn-finance-manager')),

			// Number format translations
			'number_format' => esc_html(__('Number Format', 'pn-finance-manager')),
			'number_format_desc' => esc_html(__('Choose how numbers are displayed (decimal and thousands separators).', 'pn-finance-manager')),

			// Portfolio performance translations
			'stock_portfolio_performance' => esc_html(__('Stock Portfolio Performance', 'pn-finance-manager')),
			'crypto_portfolio_performance' => esc_html(__('Crypto Portfolio Performance', 'pn-finance-manager')),
			'stock_portfolio_value' => esc_html(__('Stock Portfolio Value', 'pn-finance-manager')),
			'crypto_portfolio_value' => esc_html(__('Crypto Portfolio Value', 'pn-finance-manager')),
			'invested' => esc_html(__('Invested', 'pn-finance-manager')),
			'current' => esc_html(__('Current', 'pn-finance-manager')),

			// Comparison period translations
			'comparison_period' => esc_html(__('Comparison Period', 'pn-finance-manager')),
			'comparison_period_desc' => esc_html(__('Choose the time period for price comparison displayed on your investment cards.', 'pn-finance-manager')),
			'period_daily' => esc_html(__('Daily', 'pn-finance-manager')),
			'period_weekly' => esc_html(__('Weekly (7d)', 'pn-finance-manager')),
			'period_monthly' => esc_html(__('Monthly (30d)', 'pn-finance-manager')),
			'period_yearly' => esc_html(__('Yearly (365d)', 'pn-finance-manager')),
			'period_since_purchase' => esc_html(__('Since purchase', 'pn-finance-manager')),

			// Alert Preferences translations
			'alert_preferences' => esc_html(__('Alert Preferences', 'pn-finance-manager')),
			'alert_preferences_desc' => esc_html(__('Configure email alert preferences for price changes on your assets and watchlist items.', 'pn-finance-manager')),
			'default_threshold' => esc_html(__('Default alert threshold', 'pn-finance-manager')),
			'alerts_on_assets' => esc_html(__('Enable alerts on my assets', 'pn-finance-manager')),
			'alerts_on_watchlist' => esc_html(__('Enable alerts on watchlist items', 'pn-finance-manager')),
			'save_preferences' => esc_html(__('Save Preferences', 'pn-finance-manager')),
			'preferences_saved' => esc_html(__('Preferences saved successfully!', 'pn-finance-manager')),
			'saving' => esc_html(__('Saving...', 'pn-finance-manager')),
			'primary_color' => sanitize_hex_color(get_option('pn_finance_manager_color_primary', '#008080')),

			// Sold asset translations
			'sold' => esc_html(__('Sold', 'pn-finance-manager')),
			'sale_date' => esc_html(__('Sale Date', 'pn-finance-manager')),
			'sale_price' => esc_html(__('Sale Price', 'pn-finance-manager')),

			// Sort translations
			'sort_ascending' => esc_html(__('Sort ascending', 'pn-finance-manager')),
			'sort_descending' => esc_html(__('Sort descending', 'pn-finance-manager')),
		]);

		// Keep the original localizations for backward compatibility
		wp_localize_script($this->plugin_name . '-ajax', 'pn_finance_manager_ajax', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'pn_finance_manager_ajax_nonce' => wp_create_nonce('pn-finance-manager-nonce'),
		]);

		// Add CPTs data to JavaScript
		wp_localize_script($this->plugin_name . '-ajax', 'pn_finance_manager_cpts', PN_FINANCE_MANAGER_CPTS);

		// Verify nonce for GET parameters
		$nonce_verified = false;
		if (!empty($_GET['pn_finance_manager_nonce'])) {
			$nonce_verified = wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['pn_finance_manager_nonce'])), 'pn-finance-manager-get-nonce');
		}

		// Only process GET parameters if nonce is verified
		$pn_finance_manager_action = '';
		$pn_finance_manager_btn_id = '';
		$pn_finance_manager_popup = '';
		$pn_finance_manager_tab = '';

		if ($nonce_verified) {
			$pn_finance_manager_action = !empty($_GET['pn_finance_manager_action']) ? PN_FINANCE_MANAGER_Forms::pn_finance_manager_sanitizer(wp_unslash($_GET['pn_finance_manager_action'])) : '';
			$pn_finance_manager_btn_id = !empty($_GET['pn_finance_manager_btn_id']) ? PN_FINANCE_MANAGER_Forms::pn_finance_manager_sanitizer(wp_unslash($_GET['pn_finance_manager_btn_id'])) : '';
			$pn_finance_manager_popup = !empty($_GET['pn_finance_manager_popup']) ? PN_FINANCE_MANAGER_Forms::pn_finance_manager_sanitizer(wp_unslash($_GET['pn_finance_manager_popup'])) : '';
			$pn_finance_manager_tab = !empty($_GET['pn_finance_manager_tab']) ? PN_FINANCE_MANAGER_Forms::pn_finance_manager_sanitizer(wp_unslash($_GET['pn_finance_manager_tab'])) : '';
		}

		wp_localize_script($this->plugin_name, 'pn_finance_manager_path', [
			'main' => PN_FINANCE_MANAGER_URL,
			'assets' => PN_FINANCE_MANAGER_URL . 'assets/',
			'css' => PN_FINANCE_MANAGER_URL . 'assets/css/',
			'js' => PN_FINANCE_MANAGER_URL . 'assets/js/',
			'media' => PN_FINANCE_MANAGER_URL . 'assets/media/',
		]);
		
		wp_localize_script($this->plugin_name, 'pn_finance_manager_action', [
			'action' => $pn_finance_manager_action,
			'btn_id' => $pn_finance_manager_btn_id,
			'popup' => $pn_finance_manager_popup,
			'tab' => $pn_finance_manager_tab,
			'nonce' => wp_create_nonce('pn-finance-manager-get-nonce'),
		]);

		// Initialize popups
		PN_FINANCE_MANAGER_Popups::instance();

		// Initialize selectors
		PN_FINANCE_MANAGER_Selector::instance();
	}

  public function pn_finance_manager_body_classes($classes) {
	  $classes[] = 'pn-finance-manager-body';

	  if (!is_user_logged_in()) {
      $classes[] = 'pn-finance-manager-body-unlogged';
    } else {
      $classes[] = 'pn-finance-manager-body-logged-in';

      $user = new WP_User(get_current_user_id());
      foreach ($user->roles as $role) {
        $classes[] = 'pn-finance-manager-body-' . $role;
      }
    }

	  return $classes;
  }
}
