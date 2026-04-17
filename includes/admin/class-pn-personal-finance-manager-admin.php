<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/admin
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Admin {

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function pn_personal_finance_manager_enqueue_styles() {
		wp_enqueue_style($this->plugin_name . '-admin', PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/css/admin/pn-personal-finance-manager-admin.css', [], $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function pn_personal_finance_manager_enqueue_scripts() {
		error_log('PnPersonalFinanceManager Debug: Admin enqueue_scripts called');
		
		wp_enqueue_media();
		wp_enqueue_script($this->plugin_name . '-admin', PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/admin/pn-personal-finance-manager-admin.js', ['jquery'], $this->version, false);
		
		// Load stocks script for admin area
		error_log('PnPersonalFinanceManager Debug: Loading stocks script: ' . PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/pn-personal-finance-manager-stocks.js');
		wp_enqueue_script($this->plugin_name . '-stocks', PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/pn-personal-finance-manager-stocks.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		
		error_log('PnPersonalFinanceManager Debug: Localizing scripts');
		// Localize scripts with global variables for admin
		wp_localize_script($this->plugin_name . '-stocks', 'pn_personal_finance_manager_ajax', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'pn_personal_finance_manager_ajax_nonce' => wp_create_nonce('pn-personal-finance-manager-nonce'),
		]);

		wp_localize_script($this->plugin_name . '-stocks', 'pn_personal_finance_manager_i18n', [
			'an_error_has_occurred' => esc_html(__('An error has occurred. Please try again in a few minutes.', 'pn-personal-finance-manager')),
			'user_unlogged' => esc_html(__('Please create a new user or login to save the information.', 'pn-personal-finance-manager')),
			'saved_successfully' => esc_html(__('Saved successfully', 'pn-personal-finance-manager')),
			'removed_successfully' => esc_html(__('Removed successfully', 'pn-personal-finance-manager')),
			'edit_image' => esc_html(__('Edit image', 'pn-personal-finance-manager')),
			'edit_images' => esc_html(__('Edit images', 'pn-personal-finance-manager')),
			'select_image' => esc_html(__('Select image', 'pn-personal-finance-manager')),
			'select_images' => esc_html(__('Select images', 'pn-personal-finance-manager')),
			'edit_video' => esc_html(__('Edit video', 'pn-personal-finance-manager')),
			'edit_videos' => esc_html(__('Edit videos', 'pn-personal-finance-manager')),
			'select_video' => esc_html(__('Select video', 'pn-personal-finance-manager')),
			'select_videos' => esc_html(__('Select videos', 'pn-personal-finance-manager')),
			'edit_audio' => esc_html(__('Edit audio', 'pn-personal-finance-manager')),
			'edit_audios' => esc_html(__('Edit audios', 'pn-personal-finance-manager')),
			'select_audio' => esc_html(__('Select audio', 'pn-personal-finance-manager')),
			'select_audios' => esc_html(__('Select audios', 'pn-personal-finance-manager')),
			'edit_file' => esc_html(__('Edit file', 'pn-personal-finance-manager')),
			'edit_files' => esc_html(__('Edit files', 'pn-personal-finance-manager')),
			'select_file' => esc_html(__('Select file', 'pn-personal-finance-manager')),
			'select_files' => esc_html(__('Select files', 'pn-personal-finance-manager')),
			'ordered_element' => esc_html(__('Ordered element', 'pn-personal-finance-manager')),
			'select_option' => esc_html(__('Select option', 'pn-personal-finance-manager')),
			'select_options' => esc_html(__('Select options', 'pn-personal-finance-manager')),
			'copied' => esc_html(__('Copied', 'pn-personal-finance-manager')),

			// Stock-related translations
			'select_stock_symbol' => esc_html(__('Select a stock symbol', 'pn-personal-finance-manager')),
			'loading_stock_symbols' => esc_html(__('Loading stock symbols...', 'pn-personal-finance-manager')),
			'no_stock_symbols' => esc_html(__('No stock symbols available', 'pn-personal-finance-manager')),
			'error_loading_symbols' => esc_html(__('Error loading stock symbols', 'pn-personal-finance-manager')),

			// Audio recorder translations
			'ready_to_record' => esc_html(__('Ready to record', 'pn-personal-finance-manager')),
			'recording' => esc_html(__('Recording...', 'pn-personal-finance-manager')),
			'recording_stopped' => esc_html(__('Recording stopped. Ready to play or transcribe.', 'pn-personal-finance-manager')),
			'recording_completed' => esc_html(__('Recording completed. Ready to transcribe.', 'pn-personal-finance-manager')),
			'microphone_error' => esc_html(__('Error: Could not access microphone', 'pn-personal-finance-manager')),
			'no_audio_to_transcribe' => esc_html(__('No audio to transcribe', 'pn-personal-finance-manager')),
			'invalid_response_format' => esc_html(__('Invalid server response format', 'pn-personal-finance-manager')),
			'invalid_server_response' => esc_html(__('Invalid server response', 'pn-personal-finance-manager')),
			'transcription_completed' => esc_html(__('Transcription completed', 'pn-personal-finance-manager')),
			'no_transcription_received' => esc_html(__('No transcription received from server', 'pn-personal-finance-manager')),
			'transcription_error' => esc_html(__('Error in transcription', 'pn-personal-finance-manager')),
			'connection_error' => esc_html(__('Connection error', 'pn-personal-finance-manager')),
			'connection_error_server' => esc_html(__('Connection error: Could not connect to server', 'pn-personal-finance-manager')),
			'permission_error' => esc_html(__('Permission error: Security verification failed', 'pn-personal-finance-manager')),
			'server_error' => esc_html(__('Server error: Internal server problem', 'pn-personal-finance-manager')),
			'unknown_error' => esc_html(__('Unknown error', 'pn-personal-finance-manager')),
			'processing_error' => esc_html(__('Error processing audio', 'pn-personal-finance-manager')),

			// Fetch purchase price translations
			'fetch_price' => esc_html(__('Fetch price', 'pn-personal-finance-manager')),
			'fetching_price' => esc_html(__('Fetching...', 'pn-personal-finance-manager')),
			'price_fetch_error' => esc_html(__('Could not fetch price. Try again later.', 'pn-personal-finance-manager')),

			// Crypto-related translations
			'select_crypto_symbol' => esc_html(__('Select a cryptocurrency', 'pn-personal-finance-manager')),
			'loading_crypto_symbols' => esc_html(__('Loading cryptocurrencies...', 'pn-personal-finance-manager')),
			'no_crypto_symbols' => esc_html(__('No cryptocurrencies available', 'pn-personal-finance-manager')),
			'search_new_crypto' => esc_html(__('Search cryptocurrency', 'pn-personal-finance-manager')),
			'searching_crypto' => esc_html(__('Searching...', 'pn-personal-finance-manager')),
			'crypto_found' => esc_html(__('Cryptocurrency found and added!', 'pn-personal-finance-manager')),
			'crypto_not_found' => esc_html(__('Cryptocurrency not found. Check the name and try again.', 'pn-personal-finance-manager')),
			'enter_crypto_name' => esc_html(__('Enter cryptocurrency name (e.g. Solana)', 'pn-personal-finance-manager')),

			// Watchlist translations
			'watchlist' => esc_html(__('Watchlist', 'pn-personal-finance-manager')),
			'add_to_watchlist' => esc_html(__('Add to Watchlist', 'pn-personal-finance-manager')),
			'remove_from_watchlist' => esc_html(__('Remove', 'pn-personal-finance-manager')),
			'enable_alert' => esc_html(__('Enable Price Alert', 'pn-personal-finance-manager')),
			'alert_threshold' => esc_html(__('Alert Threshold', 'pn-personal-finance-manager')),
			'daily_change' => esc_html(__('Daily Change', 'pn-personal-finance-manager')),
			'current_price' => esc_html(__('Current Price', 'pn-personal-finance-manager')),
			'select_type' => esc_html(__('Select Type', 'pn-personal-finance-manager')),
			'confirm_remove_watchlist' => esc_html(__('Are you sure you want to remove this item from your watchlist?', 'pn-personal-finance-manager')),
			'watchlist_empty' => esc_html(__('Your watchlist is empty. Add stocks or cryptocurrencies to track.', 'pn-personal-finance-manager')),
			'item_added' => esc_html(__('Item added to watchlist', 'pn-personal-finance-manager')),
			'save_alerts' => esc_html(__('Save alerts', 'pn-personal-finance-manager')),
			'alerts_saved' => esc_html(__('Saved!', 'pn-personal-finance-manager')),
		]);
		
		error_log('PnPersonalFinanceManager Debug: Admin scripts enqueued successfully');
	}
}
