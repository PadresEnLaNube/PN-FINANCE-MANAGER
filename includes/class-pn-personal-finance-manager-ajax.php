<?php
/**
 * Load the plugin Ajax functions.
 *
 * Load the plugin Ajax functions to be executed in background.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Ajax {
  public function __construct() {
    // This needs to be included as it's not always loaded in the AJAX context
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-pn-personal-finance-manager-stocks.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-pn-personal-finance-manager-settings.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-pn-personal-finance-manager-watchlist.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-pn-personal-finance-manager-export-import.php';

    error_log('PnPersonalFinanceManager Debug: AJAX class instantiated');

    // Add AJAX actions for both logged in and non-logged in users
    add_action('wp_ajax_pn_personal_finance_manager_ajax', array($this, 'pn_personal_finance_manager_ajax_server'));
    add_action('wp_ajax_nopriv_pn_personal_finance_manager_ajax', array($this, 'pn_personal_finance_manager_ajax_server'));
    
    // Add AJAX action for checking API status
    add_action('wp_ajax_pn_personal_finance_manager_check_api_status', array($this, 'pn_personal_finance_manager_check_api_status_handler'));
  }

  /**
   * Load ajax functions.
   *
   * @since    1.0.0
   */
  public function pn_personal_finance_manager_ajax_server() {
    error_log('PnPersonalFinanceManager Debug: AJAX server function called');
    error_log('PnPersonalFinanceManager Debug: POST data: ' . print_r($_POST, true));
    
    // Verify nonce
    if (!isset($_POST['pn_personal_finance_manager_ajax_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_personal_finance_manager_ajax_nonce'])), 'pn-personal-finance-manager-nonce')) {
      error_log('PnPersonalFinanceManager Debug: Nonce verification failed');
      echo wp_json_encode(['success' => false, 'error' => 'Nonce verification failed']);
      exit;
    }
    
    error_log('PnPersonalFinanceManager Debug: Nonce verification passed');
    
    $pn_personal_finance_manager_ajax_type = !empty($_POST['pn_personal_finance_manager_ajax_type']) ? sanitize_text_field(wp_unslash($_POST['pn_personal_finance_manager_ajax_type'])) : '';
    error_log('PnPersonalFinanceManager Debug: AJAX type: ' . $pn_personal_finance_manager_ajax_type);

    $pn_personal_finance_manager_ajax_keys = !empty($_POST['pn_personal_finance_manager_ajax_keys']) ? array_map(function($key) {
      return array(
        'id' => sanitize_key($key['id']),
        'node' => sanitize_key($key['node']),
        'type' => sanitize_key($key['type']),
        'field_config' => !empty($key['field_config']) ? array_map('sanitize_text_field', $key['field_config']) : []
      );
    }, wp_unslash($_POST['pn_personal_finance_manager_ajax_keys'])) : [];

    $pn_personal_finance_manager_asset_id = !empty($_POST['pn_personal_finance_manager_asset_id']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['pn_personal_finance_manager_asset_id'])) : 0;
    $pn_personal_finance_manager_liability_id = !empty($_POST['pn_personal_finance_manager_liability_id']) ? PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(wp_unslash($_POST['pn_personal_finance_manager_liability_id'])) : 0;
    
    $pn_personal_finance_manager_key_value = [];

    if (!empty($pn_personal_finance_manager_ajax_keys)) {
      foreach ($pn_personal_finance_manager_ajax_keys as $pn_personal_finance_manager_key) {
        if (strpos($pn_personal_finance_manager_key['id'], '[]') !== false) {
          $pn_personal_finance_manager_clear_key = str_replace('[]', '', $pn_personal_finance_manager_key['id']);
          ${$pn_personal_finance_manager_clear_key} = $pn_personal_finance_manager_key_value[$pn_personal_finance_manager_clear_key] = [];

          if (!empty($_POST[$pn_personal_finance_manager_clear_key])) {
            $unslashed_array = wp_unslash($_POST[$pn_personal_finance_manager_clear_key]);
            $sanitized_array = array_map(function($value) use ($pn_personal_finance_manager_key) {
              return PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(
                $value,
                $pn_personal_finance_manager_key['node'],
                $pn_personal_finance_manager_key['type'],
                $pn_personal_finance_manager_key['field_config']
              );
            }, $unslashed_array);
            
            foreach ($sanitized_array as $multi_key => $multi_value) {
              $final_value = !empty($multi_value) ? $multi_value : '';
              ${$pn_personal_finance_manager_clear_key}[$multi_key] = $pn_personal_finance_manager_key_value[$pn_personal_finance_manager_clear_key][$multi_key] = $final_value;
            }
          } else {
            ${$pn_personal_finance_manager_clear_key} = '';
            $pn_personal_finance_manager_key_value[$pn_personal_finance_manager_clear_key][$multi_key] = '';
          }
        } else {
          $sanitized_key = sanitize_key($pn_personal_finance_manager_key['id']);
          $pn_personal_finance_manager_key_id = !empty($_POST[$sanitized_key]) ? 
            PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_sanitizer(
              wp_unslash($_POST[$sanitized_key]), 
              $pn_personal_finance_manager_key['node'], 
              $pn_personal_finance_manager_key['type'],
              $pn_personal_finance_manager_key['field_config']
            ) : '';
          ${$pn_personal_finance_manager_key['id']} = $pn_personal_finance_manager_key_value[$pn_personal_finance_manager_key['id']] = $pn_personal_finance_manager_key_id;
        }
      }
    }

    switch ($pn_personal_finance_manager_ajax_type) {
      case 'pn_personal_finance_manager_asset_view':
        if (!empty($pn_personal_finance_manager_asset_id)) {
          $plugin_post_type_asset = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset();
          echo wp_json_encode([
            'error_key' => '', 
            'html' => $plugin_post_type_asset->pn_personal_finance_manager_asset_view($pn_personal_finance_manager_asset_id), 
          ]);

          exit;
        }else{
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_asset_view_error', 
            'error_content' => esc_html(__('An error occurred while showing the Asset.', 'pn-personal-finance-manager')), 
          ]);

          exit;
        }
        break;
      case 'pn_personal_finance_manager_asset_edit':
        // Check if the Asset exists
        $pn_personal_finance_manager_asset = get_post($pn_personal_finance_manager_asset_id);
        

        if (!empty($pn_personal_finance_manager_asset_id)) {
          $plugin_post_type_asset = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset();
          echo wp_json_encode([
            'error_key' => '', 
            'html' => $plugin_post_type_asset->pn_personal_finance_manager_asset_edit($pn_personal_finance_manager_asset_id), 
          ]);

          exit;
        }else{
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_asset_edit_error', 
            'error_content' => esc_html(__('An error occurred while showing the Asset.', 'pn-personal-finance-manager')), 
          ]);

          exit;
        }
        break;
      case 'pn_personal_finance_manager_asset_new':
        if (!is_user_logged_in()) {
          echo wp_json_encode([
            'error_key' => 'not_logged_in',
            'error_content' => esc_html(__('You must be logged in to create a new asset.', 'pn-personal-finance-manager')),
          ]);
          exit;
        }
        $plugin_post_type_asset = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset();
        echo wp_json_encode([
          'error_key' => '',
          'html' => $plugin_post_type_asset->pn_personal_finance_manager_asset_new($pn_personal_finance_manager_asset_id),
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_asset_duplicate':
        if (!empty($pn_personal_finance_manager_asset_id)) {
          $plugin_post_type_post = new PN_PERSONAL_FINANCE_MANAGER_Functions_Post();
          $plugin_post_type_post->pn_personal_finance_manager_duplicate_post($pn_personal_finance_manager_asset_id, 'publish');

          $plugin_post_type_asset = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset();
          echo wp_json_encode([
            'error_key' => '',
            'html' => $plugin_post_type_asset->pn_personal_finance_manager_asset_list(),
            'statistics_html' => PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset::pn_personal_finance_manager_asset_statistics(),
          ]);

          exit;
        }else{
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_asset_duplicate_error', 
            'error_content' => esc_html(__('An error occurred while duplicating the Asset.', 'pn-personal-finance-manager')), 
          ]);

          exit;
        }
        break;
      case 'pn_personal_finance_manager_asset_remove':
        if (!empty($pn_personal_finance_manager_asset_id)) {
          wp_delete_post($pn_personal_finance_manager_asset_id, true);

          $plugin_post_type_asset = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset();
          echo wp_json_encode([
            'error_key' => '',
            'html' => $plugin_post_type_asset->pn_personal_finance_manager_asset_list(),
            'statistics_html' => PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset::pn_personal_finance_manager_asset_statistics(),
          ]);

          exit;
        }else{
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_asset_remove_error', 
            'error_content' => esc_html(__('An error occurred while removing the Asset.', 'pn-personal-finance-manager')), 
          ]);

          exit;
        }
        break;
      case 'pn_personal_finance_manager_asset_share':
        $plugin_post_type_asset = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset();
        echo wp_json_encode([
          'error_key' => '', 
          'html' => $plugin_post_type_asset->pn_personal_finance_manager_asset_share(), 
        ]);

        exit;
        break;
      // Liability AJAX cases
      case 'pn_personal_finance_manager_liability_view':
        if (!empty($pn_personal_finance_manager_liability_id)) {
          $plugin_post_type_liability = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability();
          echo wp_json_encode([
            'error_key' => '', 
            'html' => $plugin_post_type_liability->pn_personal_finance_manager_liability_view($pn_personal_finance_manager_liability_id), 
          ]);

          exit;
        }else{
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_liability_view_error', 
            'error_content' => esc_html(__('An error occurred while showing the Liability.', 'pn-personal-finance-manager')), 
          ]);

          exit;
        }
        break;
      case 'pn_personal_finance_manager_liability_edit':
        if (!empty($pn_personal_finance_manager_liability_id)) {
          $plugin_post_type_liability = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability();
          echo wp_json_encode([
            'error_key' => '', 
            'html' => $plugin_post_type_liability->pn_personal_finance_manager_liability_edit($pn_personal_finance_manager_liability_id), 
          ]);

          exit;
        }else{
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_liability_edit_error', 
            'error_content' => esc_html(__('An error occurred while showing the Liability.', 'pn-personal-finance-manager')), 
          ]);

          exit;
        }
        break;
      case 'pn_personal_finance_manager_liability_new':
        if (!is_user_logged_in()) {
          echo wp_json_encode([
            'error_key' => 'not_logged_in',
            'error_content' => esc_html(__('You must be logged in to create a new liability.', 'pn-personal-finance-manager')),
          ]);
          exit;
        }
        $plugin_post_type_liability = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability();
        echo wp_json_encode([
          'error_key' => '',
          'html' => $plugin_post_type_liability->pn_personal_finance_manager_liability_new($pn_personal_finance_manager_liability_id),
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_liability_duplicate':
        if (!empty($pn_personal_finance_manager_liability_id)) {
          $plugin_post_type_post = new PN_PERSONAL_FINANCE_MANAGER_Functions_Post();
          $plugin_post_type_post->pn_personal_finance_manager_duplicate_post($pn_personal_finance_manager_liability_id, 'publish');

          $plugin_post_type_liability = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability();
          echo wp_json_encode([
            'error_key' => '',
            'html' => $plugin_post_type_liability->pn_personal_finance_manager_liability_list(),
            'statistics_html' => PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability::pn_personal_finance_manager_liability_statistics(),
          ]);

          exit;
        }else{
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_liability_duplicate_error', 
            'error_content' => esc_html(__('An error occurred while duplicating the Liability.', 'pn-personal-finance-manager')), 
          ]);

          exit;
        }
        break;
      case 'pn_personal_finance_manager_liability_remove':
        if (!empty($pn_personal_finance_manager_liability_id)) {
          wp_delete_post($pn_personal_finance_manager_liability_id, true);

          $plugin_post_type_liability = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability();
          echo wp_json_encode([
            'error_key' => '',
            'html' => $plugin_post_type_liability->pn_personal_finance_manager_liability_list(),
            'statistics_html' => PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability::pn_personal_finance_manager_liability_statistics(),
          ]);

          exit;
        }else{
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_liability_remove_error', 
            'error_content' => esc_html(__('An error occurred while removing the Liability.', 'pn-personal-finance-manager')), 
          ]);

          exit;
        }
        break;
      case 'pn_personal_finance_manager_liability_share':
        $plugin_post_type_liability = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability();
        echo wp_json_encode([
          'error_key' => '', 
          'html' => $plugin_post_type_liability->pn_personal_finance_manager_liability_share(), 
        ]);

        exit;
        break;
      // Stock AJAX cases
      case 'pn_personal_finance_manager_get_stock_symbols':
        error_log('PnPersonalFinanceManager Debug: pn_personal_finance_manager_get_stock_symbols AJAX called');
        
        $plugin_stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
        error_log('PnPersonalFinanceManager Debug: Stocks class instantiated');
        
        $symbols = $plugin_stocks->pn_personal_finance_manager_get_stock_symbols_for_form();
        error_log('PnPersonalFinanceManager Debug: Symbols returned: ' . print_r($symbols, true));
        
        // Convert to format expected by JavaScript
        $symbols_data = [];
        foreach ($symbols as $symbol => $name) {
          if (!empty($symbol)) {
            $symbols_data[] = [
              'symbol' => $symbol,
              'name' => $name
            ];
          }
        }
        
        error_log('PnPersonalFinanceManager Debug: Symbols data for JS: ' . print_r($symbols_data, true));
        
        $response = [
          'success' => true,
          'data' => $symbols_data
        ];
        
        error_log('PnPersonalFinanceManager Debug: Sending response: ' . print_r($response, true));
        echo wp_json_encode($response);
        
        exit;
        break;
      case 'pn_personal_finance_manager_search_stock_symbol':
        $symbol = !empty($_POST['symbol']) ? strtoupper(sanitize_text_field(wp_unslash($_POST['symbol']))) : '';

        if (empty($symbol)) {
          echo wp_json_encode([
            'success' => false,
            'data' => [
              'error_content' => __('No symbol provided', 'pn-personal-finance-manager')
            ]
          ]);
          exit;
        }

        // Check if already in cache — user confirmed before, auto-add
        $cached_symbols = get_option('pn_personal_finance_manager_stock_symbols_cache', []);
        if (is_array($cached_symbols) && isset($cached_symbols[$symbol])) {
          echo wp_json_encode([
            'success' => true,
            'data' => [
              'symbol' => $symbol,
              'name' => $cached_symbols[$symbol]
            ]
          ]);
          exit;
        }

        // Search via Twelve Data symbol_search endpoint (no API key required)
        $search_url = 'https://api.twelvedata.com/symbol_search?symbol=' . urlencode($symbol);
        $response = wp_remote_get($search_url, ['timeout' => 15]);

        if (is_wp_error($response)) {
          echo wp_json_encode([
            'success' => false,
            'data' => [
              'error_content' => __('Unable to connect to stock data service', 'pn-personal-finance-manager')
            ]
          ]);
          exit;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        // Collect ALL exact symbol matches
        $matches = [];
        if (!empty($body['data']) && is_array($body['data'])) {
          foreach ($body['data'] as $item) {
            if (strtoupper($item['symbol']) === $symbol) {
              $matches[] = [
                'symbol'          => strtoupper($item['symbol']),
                'name'            => $item['instrument_name'],
                'exchange'        => !empty($item['exchange']) ? $item['exchange'] : '',
                'country'         => !empty($item['country']) ? $item['country'] : '',
                'instrument_type' => !empty($item['instrument_type']) ? $item['instrument_type'] : '',
              ];
            }
          }
        }

        if (empty($matches)) {
          echo wp_json_encode([
            'success' => false,
            'data' => [
              'error_content' => __('Symbol not found. Check the ticker and try again.', 'pn-personal-finance-manager')
            ]
          ]);
          exit;
        }

        echo wp_json_encode([
          'success' => true,
          'data' => [
            'matches' => $matches
          ]
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_confirm_stock_symbol':
        $symbol   = !empty($_POST['symbol']) ? strtoupper(sanitize_text_field(wp_unslash($_POST['symbol']))) : '';
        $name     = !empty($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $exchange = !empty($_POST['exchange']) ? sanitize_text_field(wp_unslash($_POST['exchange'])) : '';

        if (empty($symbol) || empty($name)) {
          echo wp_json_encode([
            'success' => false,
            'data' => [
              'error_content' => __('Missing symbol or name', 'pn-personal-finance-manager')
            ]
          ]);
          exit;
        }

        // Save in symbols cache
        $cached_symbols = get_option('pn_personal_finance_manager_stock_symbols_cache', []);
        $cached_symbols[$symbol] = $name;
        ksort($cached_symbols);
        update_option('pn_personal_finance_manager_stock_symbols_cache', $cached_symbols, false);

        // Save exchange map
        if (!empty($exchange)) {
          $exchange_map = get_option('pn_personal_finance_manager_stock_exchange_map', []);
          $exchange_map[$symbol] = $exchange;
          update_option('pn_personal_finance_manager_stock_exchange_map', $exchange_map, false);
        }

        echo wp_json_encode([
          'success' => true,
          'data' => [
            'symbol' => $symbol,
            'name' => $name
          ]
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_manual_stock_update':
        $this->pn_personal_finance_manager_manual_stock_update_handler();
        break;
      case 'pn_personal_finance_manager_fetch_purchase_price':
        $asset_id = !empty($_POST['asset_id']) ? intval($_POST['asset_id']) : 0;

        if (empty($asset_id)) {
          echo wp_json_encode(['success' => false, 'error' => __('Invalid asset ID.', 'pn-personal-finance-manager')]);
          exit;
        }

        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => __('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $asset_post = get_post($asset_id);
        if (!$asset_post) {
          echo wp_json_encode(['success' => false, 'error' => __('Asset not found.', 'pn-personal-finance-manager')]);
          exit;
        }

        $current_user_id = get_current_user_id();
        if ((int) $asset_post->post_author !== $current_user_id && !current_user_can('manage_options')) {
          echo wp_json_encode(['success' => false, 'error' => __('You do not have permission to update this asset.', 'pn-personal-finance-manager')]);
          exit;
        }

        $symbol = get_post_meta($asset_id, 'pn_personal_finance_manager_stock_symbol', true);
        $purchase_date = get_post_meta($asset_id, 'pn_personal_finance_manager_asset_date', true);

        if (empty($symbol) || empty($purchase_date)) {
          echo wp_json_encode(['success' => false, 'error' => __('Missing stock symbol or purchase date.', 'pn-personal-finance-manager')]);
          exit;
        }

        $stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();

        // Fetch historical data (up to 365 days) to populate the cache
        $stocks->pn_personal_finance_manager_get_historical_stock_data($symbol, 365, $current_user_id);

        // Now look up the price for the purchase date
        $price_data = $stocks->pn_personal_finance_manager_get_stock_price_for_date($symbol, $purchase_date, $current_user_id);

        if ($price_data && isset($price_data['price']) && floatval($price_data['price']) > 0) {
          $price = floatval($price_data['price']);
          update_post_meta($asset_id, 'pn_personal_finance_manager_stock_purchase_price', $price);
          echo wp_json_encode(['success' => true, 'price' => $price]);
        } else {
          echo wp_json_encode(['success' => false, 'error' => __('Could not fetch price. Try again later.', 'pn-personal-finance-manager')]);
        }
        exit;
        break;
      // Crypto AJAX cases
      case 'pn_personal_finance_manager_get_crypto_symbols':
        $plugin_stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
        $symbols = $plugin_stocks->pn_personal_finance_manager_get_crypto_symbols_for_form();

        $symbols_data = [];
        foreach ($symbols as $coin_id => $name) {
          if (!empty($coin_id)) {
            $symbols_data[] = [
              'symbol' => $coin_id,
              'name' => $name
            ];
          }
        }

        echo wp_json_encode([
          'success' => true,
          'data' => $symbols_data
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_search_crypto_symbol':
        $query = !empty($_POST['query']) ? sanitize_text_field(wp_unslash($_POST['query'])) : '';

        if (empty($query)) {
          echo wp_json_encode([
            'success' => false,
            'data' => [
              'error_content' => __('No search query provided', 'pn-personal-finance-manager')
            ]
          ]);
          exit;
        }

        // Check if already in cache
        $cached_symbols = get_option('pn_personal_finance_manager_crypto_symbols_cache', []);
        foreach ($cached_symbols as $coin_id => $display_name) {
          if (stripos($display_name, $query) !== false || stripos($coin_id, $query) !== false) {
            echo wp_json_encode([
              'success' => true,
              'data' => [
                'symbol' => $coin_id,
                'name' => $display_name
              ]
            ]);
            exit;
          }
        }

        // Search via CoinGecko search endpoint
        $search_url = 'https://api.coingecko.com/api/v3/search?query=' . urlencode($query);
        $response = wp_remote_get($search_url, ['timeout' => 15, 'user-agent' => 'PnPersonalFinanceManager/1.1']);

        if (is_wp_error($response)) {
          echo wp_json_encode([
            'success' => false,
            'data' => [
              'error_content' => __('Unable to connect to crypto data service', 'pn-personal-finance-manager')
            ]
          ]);
          exit;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        $found_coin = null;
        if (!empty($body['coins']) && is_array($body['coins'])) {
          foreach ($body['coins'] as $coin) {
            if (!empty($coin['id']) && !empty($coin['symbol']) && !empty($coin['name'])) {
              $found_coin = $coin;
              break;
            }
          }
        }

        if (empty($found_coin)) {
          echo wp_json_encode([
            'success' => false,
            'data' => [
              'error_content' => __('Cryptocurrency not found. Check the name and try again.', 'pn-personal-finance-manager')
            ]
          ]);
          exit;
        }

        $display_name = strtoupper($found_coin['symbol']) . ' - ' . $found_coin['name'];

        // Add to cache
        $cached_symbols[$found_coin['id']] = $display_name;
        update_option('pn_personal_finance_manager_crypto_symbols_cache', $cached_symbols, false);

        // Update id→ticker mapping
        $id_to_ticker = get_option('pn_personal_finance_manager_crypto_id_to_ticker', []);
        $id_to_ticker[$found_coin['id']] = strtolower($found_coin['symbol']);
        update_option('pn_personal_finance_manager_crypto_id_to_ticker', $id_to_ticker, false);

        echo wp_json_encode([
          'success' => true,
          'data' => [
            'symbol' => $found_coin['id'],
            'name' => $display_name
          ]
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_fetch_crypto_purchase_price':
        $asset_id = !empty($_POST['asset_id']) ? intval($_POST['asset_id']) : 0;

        if (empty($asset_id)) {
          echo wp_json_encode(['success' => false, 'error' => __('Invalid asset ID.', 'pn-personal-finance-manager')]);
          exit;
        }

        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => __('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $asset_post = get_post($asset_id);
        if (!$asset_post) {
          echo wp_json_encode(['success' => false, 'error' => __('Asset not found.', 'pn-personal-finance-manager')]);
          exit;
        }

        $current_user_id = get_current_user_id();
        if ((int) $asset_post->post_author !== $current_user_id && !current_user_can('manage_options')) {
          echo wp_json_encode(['success' => false, 'error' => __('You do not have permission to update this asset.', 'pn-personal-finance-manager')]);
          exit;
        }

        $coin_id = get_post_meta($asset_id, 'pn_personal_finance_manager_crypto_symbol', true);
        $purchase_date = get_post_meta($asset_id, 'pn_personal_finance_manager_asset_date', true);

        if (empty($coin_id) || empty($purchase_date)) {
          echo wp_json_encode(['success' => false, 'error' => __('Missing cryptocurrency or purchase date.', 'pn-personal-finance-manager')]);
          exit;
        }

        $stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();

        // Fetch historical data (up to 365 days) to populate the cache
        $stocks->pn_personal_finance_manager_get_historical_crypto_data($coin_id, 365);

        // Now look up the price for the purchase date
        $price_data = $stocks->pn_personal_finance_manager_get_crypto_price_for_date($coin_id, $purchase_date);

        if ($price_data && isset($price_data['price']) && floatval($price_data['price']) > 0) {
          $price = floatval($price_data['price']);
          update_post_meta($asset_id, 'pn_personal_finance_manager_crypto_purchase_price', $price);
          echo wp_json_encode(['success' => true, 'price' => $price]);
        } else {
          echo wp_json_encode(['success' => false, 'error' => __('Could not fetch price. Try again later.', 'pn-personal-finance-manager')]);
        }
        exit;
        break;
      case 'pn_personal_finance_manager_create_page':
        if (!current_user_can('manage_options')) {
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_create_page_error',
            'error_content' => esc_html(__('You do not have permission to perform this action.', 'pn-personal-finance-manager')),
          ]);
          exit;
        }

        $page_key = !empty($_POST['page_key']) ? sanitize_text_field(wp_unslash($_POST['page_key'])) : '';
        $required_pages = PN_PERSONAL_FINANCE_MANAGER_Settings::pn_personal_finance_manager_get_required_pages();

        if (empty($page_key) || !isset($required_pages[$page_key])) {
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_create_page_error',
            'error_content' => esc_html(__('Invalid page type.', 'pn-personal-finance-manager')),
          ]);
          exit;
        }

        $page_info = $required_pages[$page_key];
        $existing_page = PN_PERSONAL_FINANCE_MANAGER_Settings::pn_personal_finance_manager_find_page_by_shortcode($page_info['shortcode']);

        if ($existing_page) {
          echo wp_json_encode([
            'error_key' => '',
            'redirect_url' => get_edit_post_link($existing_page, 'raw'),
          ]);
          exit;
        }

        // Use block markup when the block editor is active, shortcode otherwise.
        if ( PN_PERSONAL_FINANCE_MANAGER_Blocks::pn_personal_finance_manager_is_block_editor_active() ) {
          $post_content = PN_PERSONAL_FINANCE_MANAGER_Blocks::pn_personal_finance_manager_get_block_markup( $page_info['shortcode'] );
        }

        if ( empty( $post_content ) ) {
          $post_content = '[' . $page_info['shortcode'] . ']';
        }

        $page_id = wp_insert_post([
          'post_title'   => $page_info['title'],
          'post_content' => $post_content,
          'post_status'  => 'draft',
          'post_type'    => 'page',
        ]);

        if (is_wp_error($page_id)) {
          echo wp_json_encode([
            'error_key' => 'pn_personal_finance_manager_create_page_error',
            'error_content' => esc_html($page_id->get_error_message()),
          ]);
          exit;
        }

        // Auto-save the page setting so the selector picks it up.
        update_option('pn_personal_finance_manager_page_' . $page_key, $page_id);

        // If Yoast SEO is active, populate focus keyword and meta description.
        if ( defined( 'WPSEO_VERSION' ) ) {
          if ( ! empty( $page_info['seo_focus_kw'] ) ) {
            update_post_meta( $page_id, '_yoast_wpseo_focuskw', sanitize_text_field( $page_info['seo_focus_kw'] ) );
          }
          if ( ! empty( $page_info['seo_metadesc'] ) ) {
            update_post_meta( $page_id, '_yoast_wpseo_metadesc', sanitize_text_field( $page_info['seo_metadesc'] ) );
          }
        }

        echo wp_json_encode([
          'error_key'    => '',
          'redirect_url' => get_edit_post_link($page_id, 'raw'),
          'page_id'      => $page_id,
          'page_key'     => $page_key,
          'page_title'   => get_the_title($page_id),
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_update_user_role':
        if (!current_user_can('manage_options')) {
          echo wp_json_encode(['error_key' => 'pn_personal_finance_manager_role_error', 'error_content' => esc_html__('Unauthorized access.', 'pn-personal-finance-manager')]);
          exit;
        }

        $role_action = !empty($_POST['role_action']) ? sanitize_text_field(wp_unslash($_POST['role_action'])) : '';
        $role = !empty($_POST['role']) ? sanitize_text_field(wp_unslash($_POST['role'])) : '';
        $user_ids = !empty($_POST['user_ids']) ? array_map('intval', wp_unslash($_POST['user_ids'])) : [];
        $role_nonce = !empty($_POST['role_nonce']) ? sanitize_text_field(wp_unslash($_POST['role_nonce'])) : '';

        if (!wp_verify_nonce($role_nonce, 'pn-personal-finance-manager-role-assignment')) {
          echo wp_json_encode(['error_key' => 'pn_personal_finance_manager_role_nonce_error', 'error_content' => esc_html__('Security check failed.', 'pn-personal-finance-manager')]);
          exit;
        }

        $plugin_roles = ['pn_personal_finance_manager_role_manager'];
        $role_labels = ['pn_personal_finance_manager_role_manager' => __('Personal Finance Manager - PN', 'pn-personal-finance-manager')];

        if (!in_array($role, $plugin_roles)) {
          echo wp_json_encode(['error_key' => 'pn_personal_finance_manager_role_invalid', 'error_content' => esc_html__('Invalid role specified.', 'pn-personal-finance-manager')]);
          exit;
        }

        if (empty($user_ids)) {
          echo wp_json_encode(['error_key' => 'pn_personal_finance_manager_role_no_users', 'error_content' => esc_html__('No users selected.', 'pn-personal-finance-manager')]);
          exit;
        }

        $updated_count = 0;
        foreach ($user_ids as $user_id) {
          $user = get_user_by('id', $user_id);
          if ($user) {
            if ($role_action === 'assign') {
              $user->add_role($role);
              $updated_count++;
            } elseif ($role_action === 'remove') {
              $user->remove_role($role);
              $updated_count++;
            }
          }
        }

        $role_label_text = isset($role_labels[$role]) ? $role_labels[$role] : $role;
        if ($role_action === 'assign') {
          $message = sprintf(
            /* translators: %1$d: number of users, %2$s: role name */
            __('%1$d user(s) have been assigned the %2$s role.', 'pn-personal-finance-manager'),
            $updated_count,
            $role_label_text
          );
        } else {
          $message = sprintf(
            /* translators: %1$d: number of users, %2$s: role name */
            __('%1$d user(s) have been removed from the %2$s role.', 'pn-personal-finance-manager'),
            $updated_count,
            $role_label_text
          );
        }

        echo wp_json_encode(['error_key' => '', 'error_content' => $message]);
        exit;
        break;
      // Watchlist AJAX cases
      case 'pn_personal_finance_manager_watchlist_add':
        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $watchlist_type = !empty($_POST['watchlist_type']) ? sanitize_text_field(wp_unslash($_POST['watchlist_type'])) : '';
        $watchlist_symbol = !empty($_POST['watchlist_symbol']) ? sanitize_text_field(wp_unslash($_POST['watchlist_symbol'])) : '';
        $watchlist_alert_enabled = !empty($_POST['watchlist_alert_enabled']) ? true : false;
        $watchlist_alert_threshold = !empty($_POST['watchlist_alert_threshold']) ? intval($_POST['watchlist_alert_threshold']) : 5;

        $plugin_watchlist = new PN_PERSONAL_FINANCE_MANAGER_Watchlist();
        $result = $plugin_watchlist->pn_personal_finance_manager_watchlist_add_item(
          get_current_user_id(),
          $watchlist_type,
          $watchlist_symbol,
          $watchlist_alert_enabled,
          $watchlist_alert_threshold
        );

        if ($result) {
          // Fetch current data from API and record initial price history
          $stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
          if ($watchlist_type === 'stock') {
            $data = $stocks->pn_personal_finance_manager_get_stock_data($watchlist_symbol);
            if ($data && isset($data['price'])) {
              $stocks->pn_personal_finance_manager_record_stock_price($watchlist_symbol, $data, get_current_user_id());
            }
          } elseif ($watchlist_type === 'crypto') {
            $data = $stocks->pn_personal_finance_manager_get_crypto_data($watchlist_symbol);
            if ($data && isset($data['price'])) {
              $stocks->pn_personal_finance_manager_record_crypto_price($watchlist_symbol, $data);
            }
          }

          echo wp_json_encode([
            'success' => true,
            'html' => $plugin_watchlist->pn_personal_finance_manager_watchlist_list_items(get_current_user_id()),
          ]);
        } else {
          echo wp_json_encode([
            'success' => false,
            'error' => esc_html__('Could not add item. It may already exist in your watchlist.', 'pn-personal-finance-manager'),
          ]);
        }
        exit;
        break;
      case 'pn_personal_finance_manager_watchlist_update':
        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $watchlist_item_id = !empty($_POST['watchlist_item_id']) ? sanitize_text_field(wp_unslash($_POST['watchlist_item_id'])) : '';
        $watchlist_alert_enabled = !empty($_POST['watchlist_alert_enabled']) ? true : false;
        $watchlist_alert_threshold = !empty($_POST['watchlist_alert_threshold']) ? intval($_POST['watchlist_alert_threshold']) : 5;

        $plugin_watchlist = new PN_PERSONAL_FINANCE_MANAGER_Watchlist();
        $result = $plugin_watchlist->pn_personal_finance_manager_watchlist_update_item(
          get_current_user_id(),
          $watchlist_item_id,
          $watchlist_alert_enabled,
          $watchlist_alert_threshold
        );

        echo wp_json_encode([
          'success' => $result,
          'html' => $plugin_watchlist->pn_personal_finance_manager_watchlist_list_items(get_current_user_id()),
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_watchlist_remove':
        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $watchlist_item_id = !empty($_POST['watchlist_item_id']) ? sanitize_text_field(wp_unslash($_POST['watchlist_item_id'])) : '';

        $plugin_watchlist = new PN_PERSONAL_FINANCE_MANAGER_Watchlist();
        $result = $plugin_watchlist->pn_personal_finance_manager_watchlist_remove_item(
          get_current_user_id(),
          $watchlist_item_id
        );

        echo wp_json_encode([
          'success' => $result,
          'html' => $plugin_watchlist->pn_personal_finance_manager_watchlist_list_items(get_current_user_id()),
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_watchlist_refresh':
        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $plugin_watchlist = new PN_PERSONAL_FINANCE_MANAGER_Watchlist();
        echo wp_json_encode([
          'success' => true,
          'html' => $plugin_watchlist->pn_personal_finance_manager_watchlist_list_items(get_current_user_id()),
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_watchlist_load_history':
        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $wl_item_id = !empty($_POST['watchlist_item_id']) ? sanitize_text_field(wp_unslash($_POST['watchlist_item_id'])) : '';
        $plugin_watchlist = new PN_PERSONAL_FINANCE_MANAGER_Watchlist();
        $user_id = get_current_user_id();
        $items = $plugin_watchlist->pn_personal_finance_manager_watchlist_get_user_items($user_id);

        $target_item = null;
        foreach ($items as $wl_item) {
          if ($wl_item['id'] === $wl_item_id) {
            $target_item = $wl_item;
            break;
          }
        }

        if (!$target_item) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('Watchlist item not found.', 'pn-personal-finance-manager')]);
          exit;
        }

        $stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
        $history_result = false;

        if ($target_item['type'] === 'stock') {
          $history_result = $stocks->pn_personal_finance_manager_get_historical_stock_data($target_item['symbol'], 30, $user_id);
        } elseif ($target_item['type'] === 'crypto') {
          $history_result = $stocks->pn_personal_finance_manager_get_historical_crypto_data($target_item['symbol'], 30);
        }

        echo wp_json_encode([
          'success' => true,
          'loaded'  => !empty($history_result),
          'html'    => $plugin_watchlist->pn_personal_finance_manager_watchlist_list_items($user_id),
        ]);
        exit;
        break;
      // Export/Import AJAX cases
      case 'pn_personal_finance_manager_export_portfolio':
        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $plugin_export_import = new PN_PERSONAL_FINANCE_MANAGER_Export_Import();
        $export_data = $plugin_export_import->pn_personal_finance_manager_export_portfolio(get_current_user_id());

        echo wp_json_encode([
          'success' => true,
          'data'    => $export_data,
        ]);
        exit;
        break;
      case 'pn_personal_finance_manager_import_portfolio':
        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $import_json_raw = !empty($_POST['pn_personal_finance_manager_import_data']) ? sanitize_text_field(wp_unslash($_POST['pn_personal_finance_manager_import_data'])) : '';
        $import_json = json_decode($import_json_raw, true);

        if (empty($import_json) || !is_array($import_json)) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('Invalid file format.', 'pn-personal-finance-manager')]);
          exit;
        }

        $plugin_export_import = new PN_PERSONAL_FINANCE_MANAGER_Export_Import();
        $result = $plugin_export_import->pn_personal_finance_manager_import_portfolio(get_current_user_id(), $import_json);

        echo wp_json_encode($result);
        exit;
        break;
        case 'pn_personal_finance_manager_settings_export':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          $settings  = new PN_PERSONAL_FINANCE_MANAGER_Settings();
          $options   = $settings->pn_personal_finance_manager_get_options();
          $export    = [];

          foreach ($options as $key => $config) {
            if (!isset($config['input']) || in_array($config['input'], ['html_multi'])) continue;
            if (isset($config['type']) && in_array($config['type'], ['nonce', 'submit'])) continue;
            if (isset($config['section'])) continue;

            $value = get_option($key, '');
            if ($value !== '') {
              $export[$key] = $value;
            }
          }

          echo wp_json_encode(['error_key' => '', 'settings' => $export]);
          exit;
          break;

        case 'pn_personal_finance_manager_settings_import':
          if (!current_user_can('manage_options')) {
            echo wp_json_encode(['error_key' => 'permission_denied']);
            exit;
          }

          $raw = isset($_POST['settings']) ? sanitize_text_field(wp_unslash($_POST['settings'])) : '';
          $import = json_decode($raw, true);

          if (!is_array($import) || empty($import)) {
            echo wp_json_encode(['error_key' => 'invalid_data', 'error_content' => 'Invalid settings data.']);
            exit;
          }

          $settings  = new PN_PERSONAL_FINANCE_MANAGER_Settings();
          $options   = $settings->pn_personal_finance_manager_get_options();
          $allowed   = array_keys($options);
          $count     = 0;

          foreach ($import as $key => $value) {
            if (in_array($key, $allowed)) {
              update_option($key, sanitize_text_field($value));
              $count++;
            }
          }

          echo wp_json_encode(['error_key' => '', 'count' => $count]);
          exit;
          break;

      case 'pn_personal_finance_manager_save_comparison_period':
        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $period = !empty($_POST['comparison_period']) ? sanitize_text_field(wp_unslash($_POST['comparison_period'])) : 'daily';
        $allowed_periods = ['daily', 'weekly', 'monthly', 'yearly', 'since_purchase'];

        if (!in_array($period, $allowed_periods, true)) {
          $period = 'daily';
        }

        update_user_meta(get_current_user_id(), 'pn_personal_finance_manager_comparison_period', $period);

        echo wp_json_encode(['success' => true]);
        exit;
        break;

      case 'pn_personal_finance_manager_load_asset_history':
        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $asset_id = !empty($_POST['asset_id']) ? intval($_POST['asset_id']) : 0;
        $symbol = !empty($_POST['symbol']) ? sanitize_text_field(wp_unslash($_POST['symbol'])) : '';
        $asset_type = !empty($_POST['asset_type']) ? sanitize_text_field(wp_unslash($_POST['asset_type'])) : '';

        if (empty($asset_id) || empty($symbol) || empty($asset_type)) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('Missing required data.', 'pn-personal-finance-manager')]);
          exit;
        }

        $stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
        $user_id = get_current_user_id();
        $history_result = false;

        if ($asset_type === 'stock') {
          $history_result = $stocks->pn_personal_finance_manager_get_historical_stock_data($symbol, 365, $user_id);
        } elseif ($asset_type === 'crypto') {
          $history_result = $stocks->pn_personal_finance_manager_get_historical_crypto_data($symbol, 365);
        }

        echo wp_json_encode([
          'success' => true,
          'loaded'  => !empty($history_result),
        ]);
        exit;
        break;

      case 'pn_pfm_create_plugin_page':
        if (!current_user_can('manage_options')) {
          echo wp_json_encode([
            'success' => false,
            'message' => esc_html(__('You do not have permission to create pages.', 'pn-personal-finance-manager')),
          ]);
          exit;
        }

        $page_title = !empty($_POST['page_title']) ? sanitize_text_field(wp_unslash($_POST['page_title'])) : '';
        $shortcode = !empty($_POST['shortcode']) ? sanitize_text_field(wp_unslash($_POST['shortcode'])) : '';
        $page_option = !empty($_POST['page_option']) ? sanitize_key(wp_unslash($_POST['page_option'])) : '';

        if (empty($page_title) || empty($shortcode) || empty($page_option)) {
          echo wp_json_encode([
            'success' => false,
            'message' => esc_html(__('Missing required fields.', 'pn-personal-finance-manager')),
          ]);
          exit;
        }

        $allowed_options = array_keys(PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_auto_detect_pages(
          method_exists('PN_PERSONAL_FINANCE_MANAGER_Settings', 'pn_personal_finance_manager_get_managed_pages')
            ? PN_PERSONAL_FINANCE_MANAGER_Settings::pn_personal_finance_manager_get_managed_pages()
            : []
        ));
        if (empty($allowed_options)) {
          $allowed_options = [$page_option];
        }
        if (!in_array($page_option, $allowed_options, true) && strpos($page_option, 'pn_personal_finance_manager_') !== 0) {
          echo wp_json_encode([
            'success' => false,
            'message' => esc_html(__('Invalid page option.', 'pn-personal-finance-manager')),
          ]);
          exit;
        }

        $post_content = '[' . $shortcode . ']';
        $post_id = wp_insert_post([
          'post_title'   => $page_title,
          'post_content' => $post_content,
          'post_status'  => 'publish',
          'post_type'    => 'page',
        ]);

        if (is_wp_error($post_id)) {
          echo wp_json_encode([
            'success' => false,
            'message' => esc_html($post_id->get_error_message()),
          ]);
          exit;
        }

        update_option($page_option, $post_id);

        echo wp_json_encode([
          'success'    => true,
          'message'    => esc_html(__('Page created successfully.', 'pn-personal-finance-manager')),
          'page_id'    => $post_id,
          'page_title' => esc_html($page_title),
          'page_url'   => esc_url(get_permalink($post_id)),
          'edit_url'   => esc_url(get_edit_post_link($post_id, 'raw')),
        ]);
        exit;
        break;

      case 'pn_pfm_unlink_plugin_page':
        if (!current_user_can('manage_options')) {
          echo wp_json_encode([
            'success' => false,
            'message' => esc_html(__('You do not have permission to manage pages.', 'pn-personal-finance-manager')),
          ]);
          exit;
        }

        $page_option = !empty($_POST['page_option']) ? sanitize_key(wp_unslash($_POST['page_option'])) : '';

        if (empty($page_option) || strpos($page_option, 'pn_personal_finance_manager_') !== 0) {
          echo wp_json_encode([
            'success' => false,
            'message' => esc_html(__('Invalid page option.', 'pn-personal-finance-manager')),
          ]);
          exit;
        }

        delete_option($page_option);

        echo wp_json_encode([
          'success' => true,
          'message' => esc_html(__('Page unlinked successfully.', 'pn-personal-finance-manager')),
        ]);
        exit;
        break;

      case 'pn_personal_finance_manager_amortization_table':
        if (!is_user_logged_in()) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('You must be logged in.', 'pn-personal-finance-manager')]);
          exit;
        }

        $liability_id = !empty($_POST['pn_personal_finance_manager_liability_id']) ? intval($_POST['pn_personal_finance_manager_liability_id']) : 0;

        if (empty($liability_id)) {
          echo wp_json_encode(['success' => false, 'error' => esc_html__('Missing liability ID.', 'pn-personal-finance-manager')]);
          exit;
        }

        $stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
        $html = $stocks->pn_personal_finance_manager_render_amortization_table($liability_id);

        echo wp_json_encode([
          'error_key' => '',
          'html' => $html,
        ]);
        exit;
        break;
    }

    echo wp_json_encode([
      'error_key' => 'pn_personal_finance_manager_save_error',
    ]);

    exit;
  }

  /**
   * Handle AJAX request for checking API status.
   *
   * @since    1.0.0
   */
  public function pn_personal_finance_manager_check_api_status_handler() {
    error_log('PnPersonalFinanceManager Debug: API status check requested');
    
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'pn_personal_finance_manager_nonce')) {
      wp_die('Security check failed');
    }
    
    // Only allow admin users to check API status
    if (!current_user_can('manage_options')) {
      wp_die('Insufficient permissions');
    }
    
    $settings = new PN_PERSONAL_FINANCE_MANAGER_Settings();
    $status = $settings->pn_personal_finance_manager_check_api_status();
    
    wp_send_json($status);
  }

  /**
   * Handle AJAX request for updating stock symbols.
   *
   * @since    1.0.0
   */
  public function pn_personal_finance_manager_manual_stock_update_handler() {
    error_log('PnPersonalFinanceManager Debug: Manual stock update requested');
    
    // Check nonce for security
    if (!isset($_POST['pn_personal_finance_manager_ajax_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_personal_finance_manager_ajax_nonce'])), 'pn-personal-finance-manager-nonce')) {
      wp_send_json_error(['error' => 'Security check failed.']);
    }
    
    // Only allow admin users to perform this action
    if (!current_user_can('manage_options')) {
      wp_send_json_error(['error' => 'Insufficient permissions.']);
    }
    
    $stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
    $result = $stocks->pn_personal_finance_manager_force_update_stock_symbols_cache();
    
    if ($result && is_array($result)) {
      $stats = $stocks->pn_personal_finance_manager_get_stock_symbols_cache_stats();
      wp_send_json_success([
        'message' => 'Stock symbols updated successfully.',
        'symbols_count' => $stats['symbols_count'],
        'cache_size' => $stats['cache_size_mb']
      ]);
    } else {
      wp_send_json_error(['error' => 'Failed to update stock symbols. Please check your API configuration and try again.']);
    }
  }
}