<?php
/**
 * The class responsible for stocks functionality.
 *
 * @since      1.0.0
 * @package    PnPersonalFinanceManager
 * @subpackage PnPersonalFinanceManager/includes
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Stocks {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Constructor can be extended with initialization logic
	}

	/**
	 * Update stock symbols from API via cron.
	 *
	 * @since    1.0.5
	 */
	public function pn_personal_finance_manager_update_stock_symbols_from_api_cron() {
		$api_enabled = get_option('pn_personal_finance_manager_stocks_api_enabled', '');
		if ($api_enabled !== 'on') {
			error_log('PnPersonalFinanceManager: Stocks API not enabled. Aborting symbol update.');
			return new WP_Error('api_config_error', 'Stocks API not enabled.');
		}

		// Use the cache update method
		$result = $this->pn_personal_finance_manager_update_stock_symbols_cache();
		
		if ($result && is_array($result)) {
			error_log('PnPersonalFinanceManager: Stock symbols updated via cron successfully. Total symbols: ' . count($result));
			return true;
		} else {
			error_log('PnPersonalFinanceManager: Failed to update stock symbols via cron.');
			return new WP_Error('api_fetch_error', 'Failed to retrieve stock symbols from API. The list was empty or invalid.');
		}
	}

	/**
	 * Get stock symbols for form dropdown.
	 *
	 * @since    1.0.0
	 * @return   array    Array of stock symbols with symbol as key and name as value.
	 */
	public function pn_personal_finance_manager_get_stock_symbols_for_form() {
		error_log('PnPersonalFinanceManager Debug: pn_personal_finance_manager_get_stock_symbols_for_form() called');
		
		// First, try to get from cache
		$cached_symbols = get_option('pn_personal_finance_manager_stock_symbols_cache', []);
		$last_update = get_option('pn_personal_finance_manager_stock_symbols_last_update', 0);
		$cache_expiry = get_option('pn_personal_finance_manager_stock_symbols_cache_expiry', 24 * HOUR_IN_SECONDS);
		
		error_log('PnPersonalFinanceManager Debug: Cache check - cached_symbols count: ' . count($cached_symbols));
		error_log('PnPersonalFinanceManager Debug: Cache check - last_update: ' . $last_update);
		error_log('PnPersonalFinanceManager Debug: Cache check - cache_expiry: ' . $cache_expiry);
		error_log('PnPersonalFinanceManager Debug: Cache check - current_time: ' . current_time('timestamp'));
		
		// Check if cache is valid
		if (!empty($cached_symbols) && is_array($cached_symbols) && 
			($last_update + $cache_expiry) > current_time('timestamp')) {
			error_log('PnPersonalFinanceManager Debug: Returning cached symbols: ' . count($cached_symbols));
			return $cached_symbols;
		}
		
		error_log('PnPersonalFinanceManager Debug: Cache invalid or expired, trying to update');
		
		// If cache is empty or expired, try to update it
		$update_result = $this->pn_personal_finance_manager_update_stock_symbols_cache();
		
		if ($update_result && !empty($update_result)) {
			error_log('PnPersonalFinanceManager Debug: Cache update successful, returning: ' . count($update_result));
			return $update_result;
		}
		
		error_log('PnPersonalFinanceManager Debug: Cache update failed, checking for old cache');
		
		// If update failed, return cached data if available, otherwise fallback
		if (!empty($cached_symbols) && is_array($cached_symbols)) {
			error_log('PnPersonalFinanceManager Debug: Returning old cached symbols: ' . count($cached_symbols));
			return $cached_symbols;
		}
		
		error_log('PnPersonalFinanceManager Debug: No cache available, returning popular symbols');
		
		// Final fallback to popular symbols
		return $this->pn_personal_finance_manager_get_popular_symbols();
	}

	/**
	 * Update stock symbols cache from API.
	 *
	 * @since    1.0.5
	 * @return   array|false    Array of stock symbols or false on failure.
	 */
	public function pn_personal_finance_manager_update_stock_symbols_cache() {
		// Check if stocks API is enabled
		$api_enabled = get_option('pn_personal_finance_manager_stocks_api_enabled', '');
		if ($api_enabled !== 'on') {
			return false;
		}

		// Twelve Data /stocks endpoint does not require an API key
		$symbols = $this->pn_personal_finance_manager_get_twelvedata_symbols();
		
		if (!empty($symbols) && is_array($symbols)) {
			// Store in cache
			update_option('pn_personal_finance_manager_stock_symbols_cache', $symbols, false);
			update_option('pn_personal_finance_manager_stock_symbols_last_update', current_time('timestamp'), false);
			update_option('pn_personal_finance_manager_stock_symbols_cache_expiry', 24 * HOUR_IN_SECONDS, false);
			
			// Log successful update
			error_log('PnPersonalFinanceManager: Stock symbols cache updated successfully. Total symbols: ' . count($symbols));
			
			return $symbols;
		}
		
		return false;
	}

	/**
	 * Get stock symbols from Twelve Data API.
	 * This endpoint does NOT require an API key.
	 *
	 * @since    1.1.0
	 * @return   array    Array of stock symbols ['AAPL' => 'Apple Inc', ...].
	 */
	private function pn_personal_finance_manager_get_twelvedata_symbols() {
		$url = 'https://api.twelvedata.com/stocks?country=United%20States&type=Common%20Stock';

		$response = wp_remote_get($url, [
			'timeout' => 120,
			'user-agent' => 'PnPersonalFinanceManager/1.1'
		]);

		if (is_wp_error($response)) {
			error_log('PnPersonalFinanceManager: Twelve Data stocks API error: ' . $response->get_error_message());
			return $this->pn_personal_finance_manager_get_popular_symbols();
		}

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if (empty($data['data']) || !is_array($data['data'])) {
			error_log('PnPersonalFinanceManager: Twelve Data stocks API returned empty or invalid data.');
			return $this->pn_personal_finance_manager_get_popular_symbols();
		}

		$symbols = [];
		$count = 0;
		$max_symbols = 10000;

		foreach ($data['data'] as $stock) {
			if ($count >= $max_symbols) {
				break;
			}
			if (!empty($stock['symbol']) && !empty($stock['name'])) {
				$symbols[$stock['symbol']] = $stock['name'];
				$count++;
			}
		}

		if (empty($symbols)) {
			return $this->pn_personal_finance_manager_get_popular_symbols();
		}

		return $symbols;
	}

	/**
	 * Get popular stock symbols as fallback.
	 *
	 * @since    1.0.0
	 * @return   array    Array of popular stock symbols.
	 */
	private function pn_personal_finance_manager_get_popular_symbols() {
		return [
			'AAPL' => 'Apple Inc.',
			'MSFT' => 'Microsoft Corporation',
			'GOOGL' => 'Alphabet Inc.',
			'AMZN' => 'Amazon.com Inc.',
			'TSLA' => 'Tesla Inc.',
			'META' => 'Meta Platforms Inc.',
			'NVDA' => 'NVIDIA Corporation',
			'BRK.A' => 'Berkshire Hathaway Inc.',
			'JNJ' => 'Johnson & Johnson',
			'V' => 'Visa Inc.',
			'JPM' => 'JPMorgan Chase & Co.',
			'WMT' => 'Walmart Inc.',
			'PG' => 'Procter & Gamble Co.',
			'UNH' => 'UnitedHealth Group Inc.',
			'HD' => 'The Home Depot Inc.',
			'MA' => 'Mastercard Inc.',
			'DIS' => 'The Walt Disney Company',
			'PYPL' => 'PayPal Holdings Inc.',
			'BAC' => 'Bank of America Corp.',
			'ADBE' => 'Adobe Inc.',
		];
	}

	/**
	 * Get stock data for a given symbol.
	 *
	 * @since    1.0.0
	 * @param    string    $symbol    The stock symbol to get data for.
	 * @return   array|false          Stock data or false on failure.
	 */
	public function pn_personal_finance_manager_get_stock_data($symbol) {
		// Check if stocks API is enabled
		$api_enabled = get_option('pn_personal_finance_manager_stocks_api_enabled', '');
		if ($api_enabled !== 'on') {
			return false;
		}

		$api_key = get_option('pn_personal_finance_manager_stocks_api_key', '');
		$cache_duration = get_option('pn_personal_finance_manager_stocks_cache_duration', 3600);

		if (empty($api_key)) {
			return false;
		}

		// Try to get cached data first
		$cache_key = 'pn_personal_finance_manager_stock_data_twelvedata_' . $symbol;
		$cached_data = get_transient($cache_key);
		if ($cached_data !== false) {
			return $cached_data;
		}

		// Get data from Twelve Data
		$data = $this->pn_personal_finance_manager_get_twelvedata_quote($api_key, $symbol);

		if ($data) {
			set_transient($cache_key, $data, (int)$cache_duration);
		}

		return $data;
	}

	/**
	 * Get stock quote from Twelve Data API.
	 *
	 * @since    1.1.0
	 * @param    string    $api_key    The API key.
	 * @param    string    $symbol     The stock symbol.
	 * @return   array|false           Stock data or false on failure.
	 */
	private function pn_personal_finance_manager_get_twelvedata_quote($api_key, $symbol) {
		$url = "https://api.twelvedata.com/quote?symbol=" . urlencode($symbol) . "&apikey=" . urlencode($api_key);

		$exchange_map = get_option('pn_personal_finance_manager_stock_exchange_map', []);
		if (isset($exchange_map[strtoupper($symbol)])) {
			$url .= "&exchange=" . urlencode($exchange_map[strtoupper($symbol)]);
		}

		$response = wp_remote_get($url, [
			'timeout' => 15,
			'user-agent' => 'PnPersonalFinanceManager/1.1'
		]);

		if (is_wp_error($response)) {
			error_log('PnPersonalFinanceManager: Twelve Data quote error: ' . $response->get_error_message());
			return false;
		}

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if (empty($data) || isset($data['code']) || empty($data['close'])) {
			$error_msg = isset($data['message']) ? $data['message'] : 'Unknown error';
			error_log('PnPersonalFinanceManager: Twelve Data quote API error: ' . $error_msg);
			return false;
		}

		return [
			'symbol' => $data['symbol'] ?? $symbol,
			'price' => $data['close'] ?? 0,
			'change' => $data['change'] ?? 0,
			'change_percent' => isset($data['percent_change']) ? number_format(floatval($data['percent_change']), 2) . '%' : '0%',
			'volume' => $data['volume'] ?? 0,
			'high' => $data['high'] ?? 0,
			'low' => $data['low'] ?? 0,
			'open' => $data['open'] ?? 0,
			'previous_close' => $data['previous_close'] ?? 0,
		];
	}

	/**
	 * Display stock information.
	 *
	 * @since    1.0.0
	 * @param    string    $symbol    The stock symbol to display.
	 */
	public function pn_personal_finance_manager_display_stock($symbol) {
		// Placeholder for stock display functionality
		echo '<div class="pn-personal-finance-manager-stock-display">Stock: ' . esc_html($symbol) . '</div>';
	}

	/**
	 * Get stock price.
	 *
	 * @since    1.0.0
	 * @param    string    $symbol    The stock symbol.
	 * @return   float|false          Stock price or false on failure.
	 */
	public function pn_personal_finance_manager_get_stock_price($symbol) {
		$data = $this->pn_personal_finance_manager_get_stock_data($symbol);
		return $data ? $data['price'] : false;
	}

	/**
	 * Get stock chart data.
	 *
	 * @since    1.0.0
	 * @param    string    $symbol    The stock symbol.
	 * @param    string    $period    The time period (1d, 1w, 1m, 3m, 1y).
	 * @return   array|false          Chart data or false on failure.
	 */
	public function pn_personal_finance_manager_get_stock_chart($symbol, $period = '1m') {
		// Placeholder for stock chart functionality
		return false;
	}

	/**
	 * Get historical stock data and store in database.
	 *
	 * @since    1.0.5
	 * @param    string    $symbol    The stock symbol.
	 * @param    int       $days      Number of days to retrieve.
	 * @param    int       $user_id   User ID for storing data.
	 * @return   array|false          Historical data or false on failure.
	 */
	public function pn_personal_finance_manager_get_historical_stock_data($symbol, $days = 30, $user_id = null) {
		if ($user_id === null) {
			$user_id = get_current_user_id();
		}

		// Check if stocks API is enabled
		$api_enabled = get_option('pn_personal_finance_manager_stocks_api_enabled', '');
		if ($api_enabled !== 'on') {
			return false;
		}

		$api_key = get_option('pn_personal_finance_manager_stocks_api_key', '');
		if (empty($api_key)) {
			return false;
		}

		// Check if we have recent data in database
		$existing_data = $this->pn_personal_finance_manager_get_stock_price_history($symbol, $days, $user_id);
		$last_update = get_option('pn_personal_finance_manager_historical_data_last_update_' . $symbol, '');

		// If we have recent data (less than 24 hours old), return it
		if (!empty($existing_data) && !empty($last_update)) {
			$last_update_timestamp = strtotime($last_update);
			$current_timestamp = current_time('timestamp');
			$hours_since_update = ($current_timestamp - $last_update_timestamp) / 3600;

			if ($hours_since_update < 24) {
				return $existing_data;
			}
		}

		// Get historical data from Twelve Data
		$historical_data = $this->pn_personal_finance_manager_get_twelvedata_historical_data($api_key, $symbol, $days);

		if ($historical_data) {
			$this->pn_personal_finance_manager_store_historical_data($symbol, $historical_data, $user_id);
			update_option('pn_personal_finance_manager_historical_data_last_update_' . $symbol, current_time('mysql'), false);
			return $historical_data;
		}

		return false;
	}

	/**
	 * Get historical data from Twelve Data API.
	 *
	 * @since    1.1.0
	 * @param    string    $api_key    The API key.
	 * @param    string    $symbol     The stock symbol.
	 * @param    int       $days       Number of days to retrieve.
	 * @return   array|false           Historical data or false on failure.
	 */
	private function pn_personal_finance_manager_get_twelvedata_historical_data($api_key, $symbol, $days) {
		$url = "https://api.twelvedata.com/time_series?symbol=" . urlencode($symbol) . "&interval=1day&outputsize=" . intval($days) . "&apikey=" . urlencode($api_key);

		$exchange_map = get_option('pn_personal_finance_manager_stock_exchange_map', []);
		if (isset($exchange_map[strtoupper($symbol)])) {
			$url .= "&exchange=" . urlencode($exchange_map[strtoupper($symbol)]);
		}

		$response = wp_remote_get($url, [
			'timeout' => 30,
			'user-agent' => 'PnPersonalFinanceManager/1.1'
		]);

		if (is_wp_error($response)) {
			error_log('PnPersonalFinanceManager: Twelve Data time_series error: ' . $response->get_error_message());
			return false;
		}

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if (empty($data['values']) || !is_array($data['values']) || isset($data['code'])) {
			$error_msg = isset($data['message']) ? $data['message'] : 'Unknown error';
			error_log('PnPersonalFinanceManager: Twelve Data time_series API error: ' . $error_msg);
			return false;
		}

		$historical_data = [];

		// Twelve Data returns newest first
		foreach ($data['values'] as $daily_data) {
			$historical_data[] = [
				'symbol' => $symbol,
				'price' => floatval($daily_data['close']),
				'volume' => intval($daily_data['volume']),
				'high' => floatval($daily_data['high']),
				'low' => floatval($daily_data['low']),
				'open_price' => floatval($daily_data['open']),
				'previous_close' => floatval($daily_data['close']),
				'change_amount' => 0,
				'change_percent' => '0%',
				'recorded_date' => $daily_data['datetime'],
				'recorded_time' => '16:00:00',
				'created_at' => current_time('mysql')
			];
		}

		// Calculate change amounts and percentages
		for ($i = 0; $i < count($historical_data) - 1; $i++) {
			$current_price = $historical_data[$i]['price'];
			$previous_price = $historical_data[$i + 1]['price'];

			$historical_data[$i]['previous_close'] = $previous_price;
			$historical_data[$i]['change_amount'] = $current_price - $previous_price;
			$historical_data[$i]['change_percent'] = $previous_price > 0 ?
				round((($current_price - $previous_price) / $previous_price) * 100, 2) . '%' : '0%';
		}

		// Reverse to chronological order (oldest first)
		return array_reverse($historical_data);
	}

	/**
	 * Store historical stock data in global cache (wp_options).
	 *
	 * @since    1.0.5
	 * @param    string    $symbol           Stock symbol.
	 * @param    array     $historical_data  Historical price data.
	 * @param    int       $user_id          User ID (for backward compatibility).
	 * @return   bool                        Success status.
	 */
	private function pn_personal_finance_manager_store_historical_data($symbol, $historical_data, $user_id = null) {
		if (empty($historical_data)) {
			return false;
		}
		
		$option_key = 'pn_personal_finance_manager_stock_price_history_' . strtoupper($symbol);
		
		// Get existing data from global cache
		$existing_data = get_option($option_key, []);
		if (!is_array($existing_data)) {
			$existing_data = [];
		}
		
		// Merge new data with existing data
		foreach ($historical_data as $new_record) {
			$date_exists = false;
			
			// Check if we already have data for this date
			foreach ($existing_data as $index => $existing_record) {
				if ($existing_record['recorded_date'] === $new_record['recorded_date']) {
					// Update existing record
					$existing_data[$index] = $new_record;
					$date_exists = true;
					break;
				}
			}
			
			// Add new record if date doesn't exist
			if (!$date_exists) {
				$existing_data[] = $new_record;
			}
		}
		
		// Sort by date
		usort($existing_data, function($a, $b) {
			return strtotime($a['recorded_date']) - strtotime($b['recorded_date']);
		});
		
		// Keep only last 365 days of data
		$cutoff_date = gmdate('Y-m-d', strtotime('-365 days'));
		$existing_data = array_filter($existing_data, function($record) use ($cutoff_date) {
			return $record['recorded_date'] >= $cutoff_date;
		});
		
		// Store in global cache with expiration
		$cache_expiration = 24 * HOUR_IN_SECONDS; // 24 hours
		return update_option($option_key, array_values($existing_data), false);
	}



	/**
	 * Record stock price to global cache (wp_options).
	 *
	 * @since    1.0.5
	 * @param    string    $symbol    The stock symbol.
	 * @param    array     $data      Stock data array.
	 * @param    int       $user_id   User ID (for backward compatibility).
	 * @return   bool                 Success status.
	 */
	public function pn_personal_finance_manager_record_stock_price($symbol, $data, $user_id = null) {
		$option_key = 'pn_personal_finance_manager_stock_price_history_' . strtoupper($symbol);
		$today = current_time('Y-m-d');
		
		// Get existing price history for this symbol from global cache
		$price_history = get_option($option_key, []);
		if (!is_array($price_history)) {
			$price_history = [];
		}
		
		// Check if we already have a record for today
		$existing_index = null;
		foreach ($price_history as $index => $record) {
			if ($record['recorded_date'] === $today) {
				$existing_index = $index;
				break;
			}
		}
		
		// Prepare the price record
		$price_record = [
			'symbol' => $symbol,
			'price' => floatval($data['price']),
			'volume' => intval($data['volume']),
			'high' => floatval($data['high']),
			'low' => floatval($data['low']),
			'open_price' => floatval($data['open']),
			'previous_close' => floatval($data['previous_close']),
			'change_amount' => floatval($data['change']),
			'change_percent' => $data['change_percent'],
			'recorded_date' => $today,
			'recorded_time' => current_time('H:i:s'),
			'created_at' => current_time('mysql')
		];
		
		if ($existing_index !== null) {
			// Update existing record
			$price_history[$existing_index] = $price_record;
		} else {
			// Add new record
			$price_history[] = $price_record;
		}
		
		// Keep only last 365 days of data to prevent cache from getting too large
		$cutoff_date = gmdate('Y-m-d', strtotime('-365 days'));
		$price_history = array_filter($price_history, function($record) use ($cutoff_date) {
			return $record['recorded_date'] >= $cutoff_date;
		});
		
		// Sort by date
		usort($price_history, function($a, $b) {
			return strtotime($a['recorded_date']) - strtotime($b['recorded_date']);
		});
		
		return update_option($option_key, array_values($price_history), false);
	}

	/**
	 * Get historical stock prices for a symbol from global cache.
	 *
	 * @since    1.0.5
	 * @param    string    $symbol    The stock symbol.
	 * @param    int       $days      Number of days to retrieve.
	 * @param    int       $user_id   User ID (for backward compatibility).
	 * @return   array                Historical price data.
	 */
	public function pn_personal_finance_manager_get_stock_price_history($symbol, $days = 30, $user_id = null) {
		$option_key = 'pn_personal_finance_manager_stock_price_history_' . strtoupper($symbol);
		$price_history = get_option($option_key, []);
		
		if (!is_array($price_history)) {
			return [];
		}
		
		// Filter by days
		$cutoff_date = gmdate('Y-m-d', strtotime("-{$days} days"));
		$filtered_history = array_filter($price_history, function($record) use ($cutoff_date) {
			return $record['recorded_date'] >= $cutoff_date;
		});
		
		// Sort by date
		usort($filtered_history, function($a, $b) {
			return strtotime($a['recorded_date']) - strtotime($b['recorded_date']);
		});
		
		return array_values($filtered_history);
	}

	/**
	 * Get stock price for a specific date from global cache.
	 *
	 * @since    1.0.5
	 * @param    string    $symbol    The stock symbol.
	 * @param    string    $date      Date in Y-m-d format.
	 * @param    int       $user_id   User ID (for backward compatibility).
	 * @return   array|false          Price data or false if not found.
	 */
	public function pn_personal_finance_manager_get_stock_price_for_date($symbol, $date, $user_id = null) {
		$option_key = 'pn_personal_finance_manager_stock_price_history_' . strtoupper($symbol);
		$price_history = get_option($option_key, []);
		
		if (!is_array($price_history)) {
			return false;
		}
		
		// Primero buscar coincidencia exacta
		foreach ($price_history as $record) {
			if ($record['recorded_date'] === $date) {
				return $record;
			}
		}
		
		// Si no hay coincidencia exacta, buscar la fecha más cercana
		$closest_record = null;
		$min_diff = null;
		$target_timestamp = strtotime($date);
		
		foreach ($price_history as $record) {
			if (empty($record['recorded_date'])) continue;
			
			$record_timestamp = strtotime($record['recorded_date']);
			$diff = abs($target_timestamp - $record_timestamp);
			
			// Preferir fechas anteriores o iguales a la fecha de compra
			if ($record_timestamp <= $target_timestamp) {
				if ($min_diff === null || $diff < $min_diff) {
					$min_diff = $diff;
					$closest_record = $record;
				}
			}
		}
		
		// Si no hay fechas anteriores, buscar la posterior más cercana
		if ($closest_record === null) {
			foreach ($price_history as $record) {
				if (empty($record['recorded_date'])) continue;
				
				$record_timestamp = strtotime($record['recorded_date']);
				$diff = abs($target_timestamp - $record_timestamp);
				
				if ($min_diff === null || $diff < $min_diff) {
					$min_diff = $diff;
					$closest_record = $record;
				}
			}
		}
		
		return $closest_record;
	}

	/**
	 * Daily cron job to record stock prices for user assets.
	 *
	 * @since    1.0.5
	 */
	public function pn_personal_finance_manager_daily_stock_price_recording() {
		// Get all stock assets (exclude sold)
		$stock_assets = get_posts([
			'post_type' => 'pnpfm_asset',
			'post_status' => 'publish',
			'numberposts' => -1,
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => 'pn_personal_finance_manager_asset_type',
					'value' => 'stocks',
					'compare' => '='
				],
				[
					'relation' => 'OR',
					[
						'key' => 'pn_personal_finance_manager_asset_sold',
						'compare' => 'NOT EXISTS'
					],
					[
						'key' => 'pn_personal_finance_manager_asset_sold',
						'value' => 'on',
						'compare' => '!='
					]
				]
			]
		]);
		
		$recorded_count = 0;
		$error_count = 0;
		
		// Group assets by user to avoid duplicate API calls for same symbol
		$user_symbols = [];
		
		foreach ($stock_assets as $asset) {
			$symbol = get_post_meta($asset->ID, 'pn_personal_finance_manager_stock_symbol', true);
			$user_id = $asset->post_author;
			
			if (empty($symbol)) {
				continue;
			}
			
			if (!isset($user_symbols[$user_id])) {
				$user_symbols[$user_id] = [];
			}
			
			if (!in_array($symbol, $user_symbols[$user_id])) {
				$user_symbols[$user_id][] = $symbol;
			}
		}
		
		// Include watchlist stock symbols from all users
		$watchlist = new PN_PERSONAL_FINANCE_MANAGER_Watchlist();
		$all_users = get_users(['fields' => 'ID']);
		foreach ($all_users as $uid) {
			$wl_items = $watchlist->pn_personal_finance_manager_watchlist_get_user_items($uid);
			foreach ($wl_items as $wl_item) {
				if ($wl_item['type'] === 'stock' && !empty($wl_item['symbol'])) {
					if (!isset($user_symbols[$uid])) {
						$user_symbols[$uid] = [];
					}
					if (!in_array($wl_item['symbol'], $user_symbols[$uid])) {
						$user_symbols[$uid][] = $wl_item['symbol'];
					}
				}
			}
		}

		// Record prices for each user's unique symbols
		foreach ($user_symbols as $user_id => $symbols) {
			foreach ($symbols as $symbol) {
				// Get current stock data
				$stock_data = $this->pn_personal_finance_manager_get_stock_data($symbol);

				if ($stock_data) {
					$result = $this->pn_personal_finance_manager_record_stock_price($symbol, $stock_data, $user_id);
					if ($result) {
						$recorded_count++;
					} else {
						$error_count++;
					}
				} else {
					$error_count++;
				}
			}
		}
	}

	/**
	 * Get user assets with current values and historical data.
	 *
	 * @since    1.0.5
	 * @param    int       $user_id    User ID to get assets for.
	 * @return   array                 User assets organized by type.
	 */
	public function pn_personal_finance_manager_get_user_assets($user_id) {
		// Get all assets owned by the user
		$user_assets = get_posts([
			'post_type' => 'pnpfm_asset',
			'post_status' => 'publish',
			'numberposts' => -1,
			'author' => $user_id,
			'meta_query' => [
				'relation' => 'OR',
				[
					'key' => 'pn_personal_finance_manager_owners',
					'value' => $user_id,
					'compare' => 'LIKE'
				],
				[
					'key' => 'pn_personal_finance_manager_owners_checkbox',
					'value' => 'on',
					'compare' => '!='
				],
				[
					'key' => 'pn_personal_finance_manager_owners_checkbox',
					'compare' => 'NOT EXISTS'
				]
			]
		]);
		
		// Also get assets where user is explicitly listed as owner
		$owned_assets = get_posts([
			'post_type' => 'pnpfm_asset',
			'post_status' => 'publish',
			'numberposts' => -1,
			'meta_query' => [
				[
					'key' => 'pn_personal_finance_manager_owners',
					'value' => $user_id,
					'compare' => 'LIKE'
				]
			]
		]);
		
		// Merge and remove duplicates
		$all_assets = array_merge($user_assets, $owned_assets);
		$unique_assets = [];
		foreach ($all_assets as $asset) {
			$unique_assets[$asset->ID] = $asset;
		}
		
		// Organize by asset type
		$organized_assets = [];
		$asset_types = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_asset_types();
		
		foreach ($asset_types as $type_key => $type_label) {
			$organized_assets[$type_key] = [
				'label' => $type_label,
				'assets' => [],
				'total_value' => 0,
				'count' => 0
			];
		}
		
		foreach ($unique_assets as $asset) {
			$asset_type = get_post_meta($asset->ID, 'pn_personal_finance_manager_asset_type', true);
			
			if (empty($asset_type) || !isset($organized_assets[$asset_type])) {
				$asset_type = 'other';
			}
			
			$is_sold = get_post_meta($asset->ID, 'pn_personal_finance_manager_asset_sold', true) === 'on';
			$sold_price = floatval(get_post_meta($asset->ID, 'pn_personal_finance_manager_asset_sold_price', true));
			$sold_date = get_post_meta($asset->ID, 'pn_personal_finance_manager_asset_sold_date', true);

			$asset_data = [
				'id' => $asset->ID,
				'title' => $asset->post_title,
				'description' => $asset->post_content,
				'purchase_date' => get_post_meta($asset->ID, 'pn_personal_finance_manager_asset_date', true),
				'purchase_time' => get_post_meta($asset->ID, 'pn_personal_finance_manager_asset_time', true),
				'current_value' => 0,
				'purchase_value' => 0,
				'profit_loss' => 0,
				'profit_loss_percent' => 0,
				'stock_data' => null,
				'price_history' => [],
				'is_sold' => $is_sold,
				'sold_price' => $sold_price,
				'sold_date' => $sold_date,
			];
			
			// Handle stock assets specifically
			if ($asset_type === 'stocks') {
				$symbol = get_post_meta($asset->ID, 'pn_personal_finance_manager_stock_symbol', true);
				if (!empty($symbol)) {
					$asset_data['symbol'] = strtoupper($symbol);
					$stock_symbols_cache = get_option('pn_personal_finance_manager_stock_symbols_cache', []);
					$upper_symbol = strtoupper($symbol);
					$asset_data['symbol_name'] = isset($stock_symbols_cache[$upper_symbol]) ? $stock_symbols_cache[$upper_symbol] : '';
					$asset_data['sector'] = get_post_meta($asset->ID, 'pn_personal_finance_manager_stock_sector', true);
					$asset_data['country'] = get_post_meta($asset->ID, 'pn_personal_finance_manager_stock_country', true);

					if ($is_sold && $sold_price > 0) {
						// Sold asset: use frozen sale price, skip API call
						$asset_data['current_value'] = $sold_price;
						$asset_data['stock_data'] = ['price' => $sold_price, 'change' => 0, 'change_percent' => '0.00%'];
					} else {
						// Get current stock data
						$stock_data = $this->pn_personal_finance_manager_get_stock_data($symbol);
						if ($stock_data) {
							$asset_data['stock_data'] = $stock_data;
							$asset_data['current_value'] = floatval($stock_data['price']);
							// Get historical data for performance chart (from purchase date if available)
							$history_days = 30;
							if (!empty($asset_data['purchase_date'])) {
								$purchase_timestamp = strtotime($asset_data['purchase_date']);
								if ($purchase_timestamp) {
									$days_since_purchase = (int) ceil((time() - $purchase_timestamp) / DAY_IN_SECONDS);
									if ($days_since_purchase > 30) {
										$history_days = $days_since_purchase;
									}
								}
							}
							$asset_data['price_history'] = $this->pn_personal_finance_manager_get_stock_price_history($symbol, $history_days, $user_id);
						}
					}
					$total_amount = get_post_meta($asset->ID, 'pn_personal_finance_manager_stock_total_amount', true);
					$purchase_price = get_post_meta($asset->ID, 'pn_personal_finance_manager_stock_purchase_price', true);
					$purchase_date = get_post_meta($asset->ID, 'pn_personal_finance_manager_asset_date', true);

					if (empty($purchase_price) || floatval($purchase_price) == 0) {
						if ($purchase_date) {
							$purchase_price_data = $this->pn_personal_finance_manager_get_stock_price_for_date($symbol, $purchase_date, $user_id);
							if ($purchase_price_data && isset($purchase_price_data['price'])) {
								$purchase_price = floatval($purchase_price_data['price']);
								update_post_meta($asset->ID, 'pn_personal_finance_manager_stock_purchase_price', $purchase_price);
							} else {
								if (!empty($asset_data['price_history'])) {
									$first_record = reset($asset_data['price_history']);
									if (isset($first_record['price'])) {
										$purchase_price = floatval($first_record['price']);
										update_post_meta($asset->ID, 'pn_personal_finance_manager_stock_purchase_price', $purchase_price);
									}
								}
							}
						}
					}

					$asset_data['shares'] = floatval($total_amount);
					$asset_data['purchase_price'] = floatval($purchase_price);
					$asset_data['total_invested'] = $asset_data['shares'] * $asset_data['purchase_price'];
					$asset_data['current_total_value'] = $asset_data['shares'] * $asset_data['current_value'];

					if ($asset_data['purchase_price'] > 0 && $asset_data['current_value'] > 0 && $asset_data['shares'] > 0) {
						$asset_data['profit_loss'] = $asset_data['current_total_value'] - $asset_data['total_invested'];
						$asset_data['profit_loss_percent'] = ($asset_data['profit_loss'] / $asset_data['total_invested']) * 100;
					} else {
						$asset_data['profit_loss'] = 0;
						$asset_data['profit_loss_percent'] = 0;
					}
				}
			}

			// Handle real estate assets
			elseif ($asset_type === 'real_estate') {
				$re_current_value = get_post_meta($asset->ID, 'real_estate_current_value', true);
				$re_purchase_price = get_post_meta($asset->ID, 'real_estate_purchase_price', true);
				$re_ownership_percent = get_post_meta($asset->ID, 'real_estate_ownership_percent', true);
				$re_ownership_percent = is_numeric($re_ownership_percent) ? floatval($re_ownership_percent) : 100;

				// Real estate values are entered in the user's currency, convert to USD
				// for internal consistency (stocks/crypto come from APIs in USD)
				$re_currency = get_option('pn_personal_finance_manager_currency', 'eur');
				$re_rate = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_usd_exchange_rate($re_currency);
				$re_rate = ($re_rate > 0) ? $re_rate : 1;

				$purchase_val_local = !empty($re_purchase_price) ? floatval($re_purchase_price) : 0;

				if ($is_sold && $sold_price > 0) {
					// Sold: use sold_price (entered in user's currency) as current value
					$current_val_local = $sold_price;
				} else {
					$current_val_local = !empty($re_current_value) ? floatval($re_current_value) : floatval($re_purchase_price);
				}

				$asset_data['current_value'] = $current_val_local / $re_rate;
				$asset_data['purchase_value'] = $purchase_val_local / $re_rate;
				$asset_data['ownership_percent'] = $re_ownership_percent;

				$asset_data['current_total_value'] = $asset_data['current_value'] * ($re_ownership_percent / 100);
				$asset_data['total_invested'] = $asset_data['purchase_value'] * ($re_ownership_percent / 100);

				if ($asset_data['total_invested'] > 0 && $asset_data['current_total_value'] > 0) {
					$asset_data['profit_loss'] = $asset_data['current_total_value'] - $asset_data['total_invested'];
					$asset_data['profit_loss_percent'] = ($asset_data['profit_loss'] / $asset_data['total_invested']) * 100;
				}
			}

			// Handle cryptocurrency assets
			elseif ($asset_type === 'cryptocurrencies') {
				$coin_id = get_post_meta($asset->ID, 'pn_personal_finance_manager_crypto_symbol', true);
				if (!empty($coin_id)) {
					// Resolve ticker from id→ticker mapping
					$id_to_ticker = get_option('pn_personal_finance_manager_crypto_id_to_ticker', []);
					$ticker = isset($id_to_ticker[$coin_id]) ? strtoupper($id_to_ticker[$coin_id]) : strtoupper($coin_id);
					$asset_data['symbol'] = $ticker;
					$crypto_symbols_cache = get_option('pn_personal_finance_manager_crypto_symbols_cache', []);
					if (isset($crypto_symbols_cache[$coin_id])) {
						$cached_name = $crypto_symbols_cache[$coin_id];
						$dash_pos = strpos($cached_name, ' - ');
						$asset_data['symbol_name'] = $dash_pos !== false ? substr($cached_name, $dash_pos + 3) : $cached_name;
					} else {
						$asset_data['symbol_name'] = '';
					}

					if ($is_sold && $sold_price > 0) {
						// Sold asset: use frozen sale price, skip API call
						$asset_data['current_value'] = $sold_price;
						$asset_data['stock_data'] = ['price' => $sold_price, 'change' => 0, 'change_percent' => '0.00%'];
					} else {
						// Get current crypto data
						$crypto_data = $this->pn_personal_finance_manager_get_crypto_data($coin_id);
						if ($crypto_data) {
							$asset_data['stock_data'] = $crypto_data;
							$asset_data['current_value'] = floatval($crypto_data['price']);
						}

						// Get historical data for performance chart (from purchase date if available)
						$history_days = 30;
						if (!empty($asset_data['purchase_date'])) {
							$purchase_timestamp = strtotime($asset_data['purchase_date']);
							if ($purchase_timestamp) {
								$days_since_purchase = (int) ceil((time() - $purchase_timestamp) / DAY_IN_SECONDS);
								if ($days_since_purchase > 30) {
									$history_days = $days_since_purchase;
								}
							}
						}
						$asset_data['price_history'] = $this->pn_personal_finance_manager_get_crypto_price_history($coin_id, $history_days);
					}

					$amount = get_post_meta($asset->ID, 'pn_personal_finance_manager_crypto_amount', true);
					$purchase_price = get_post_meta($asset->ID, 'pn_personal_finance_manager_crypto_purchase_price', true);
					$purchase_date = get_post_meta($asset->ID, 'pn_personal_finance_manager_asset_date', true);

					// Try to resolve purchase price from history if == 0
					if (empty($purchase_price) || floatval($purchase_price) == 0) {
						if ($purchase_date) {
							$purchase_price_data = $this->pn_personal_finance_manager_get_crypto_price_for_date($coin_id, $purchase_date);
							if ($purchase_price_data && isset($purchase_price_data['price'])) {
								$purchase_price = floatval($purchase_price_data['price']);
								update_post_meta($asset->ID, 'pn_personal_finance_manager_crypto_purchase_price', $purchase_price);
							} else {
								if (!empty($asset_data['price_history'])) {
									$first_record = reset($asset_data['price_history']);
									if (isset($first_record['price'])) {
										$purchase_price = floatval($first_record['price']);
										update_post_meta($asset->ID, 'pn_personal_finance_manager_crypto_purchase_price', $purchase_price);
									}
								}
							}
						}
					}

					$asset_data['shares'] = floatval($amount);
					$asset_data['purchase_price'] = floatval($purchase_price);
					$asset_data['total_invested'] = $asset_data['shares'] * $asset_data['purchase_price'];
					$asset_data['current_total_value'] = $asset_data['shares'] * $asset_data['current_value'];

					if ($asset_data['purchase_price'] > 0 && $asset_data['current_value'] > 0 && $asset_data['shares'] > 0) {
						$asset_data['profit_loss'] = $asset_data['current_total_value'] - $asset_data['total_invested'];
						$asset_data['profit_loss_percent'] = ($asset_data['profit_loss'] / $asset_data['total_invested']) * 100;
					} else {
						$asset_data['profit_loss'] = 0;
						$asset_data['profit_loss_percent'] = 0;
					}
				}
			}
			
			$organized_assets[$asset_type]['assets'][] = $asset_data;
			$organized_assets[$asset_type]['total_value'] += isset($asset_data['current_total_value']) ? $asset_data['current_total_value'] : 0;
			$organized_assets[$asset_type]['count']++;
		}
		
		// Remove empty categories
		foreach ($organized_assets as $type_key => $type_data) {
			if ($type_data['count'] === 0) {
				unset($organized_assets[$type_key]);
			}
		}
		
		return $organized_assets;
	}

	/**
	 * Get user liabilities organized by type.
	 *
	 * @since    1.0.5
	 * @param    int       $user_id    User ID to get liabilities for.
	 * @return   array                 User liabilities organized by type.
	 */
	public function pn_personal_finance_manager_get_user_liabilities($user_id) {
		$user_liabilities = get_posts([
			'post_type' => 'pnpfm_liability',
			'post_status' => 'publish',
			'numberposts' => -1,
			'author' => $user_id,
		]);

		if (empty($user_liabilities)) {
			return [];
		}

		$liability_types = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_liability_types();

		$balance_fields = [
			'mortgage'      => 'pn_personal_finance_manager_mortgage_remaining_balance',
			'car_loan'      => 'pn_personal_finance_manager_car_loan_remaining_balance',
			'student_loan'  => 'pn_personal_finance_manager_student_loan_remaining_balance',
			'credit_card'   => 'pn_personal_finance_manager_credit_card_balance',
			'personal_loan' => 'pn_personal_finance_manager_personal_loan_remaining_balance',
			'medical_debt'  => 'pn_personal_finance_manager_medical_debt_remaining_balance',
			'business_loan' => 'pn_personal_finance_manager_business_loan_remaining_balance',
			'tax_debt'      => 'pn_personal_finance_manager_tax_debt_remaining_balance',
			'other'         => 'pn_personal_finance_manager_other_liability_amount',
		];

		$organized_liabilities = [];
		foreach ($liability_types as $type_key => $type_label) {
			$organized_liabilities[$type_key] = [
				'label' => $type_label,
				'liabilities' => [],
				'total_value' => 0,
				'count' => 0
			];
		}

		foreach ($user_liabilities as $liability) {
			$liability_type = get_post_meta($liability->ID, 'pn_personal_finance_manager_liability_type', true);

			if (empty($liability_type) || !isset($organized_liabilities[$liability_type])) {
				$liability_type = 'other';
			}

			$balance_field = isset($balance_fields[$liability_type]) ? $balance_fields[$liability_type] : $balance_fields['other'];
			$balance = floatval(get_post_meta($liability->ID, $balance_field, true));
			$date = get_post_meta($liability->ID, 'pn_personal_finance_manager_liability_date', true);

			$liability_data = [
				'id' => $liability->ID,
				'title' => $liability->post_title,
				'balance' => $balance,
				'date' => $date,
			];

			$organized_liabilities[$liability_type]['liabilities'][] = $liability_data;
			$organized_liabilities[$liability_type]['total_value'] += $balance;
			$organized_liabilities[$liability_type]['count']++;
		}

		// Remove empty categories
		foreach ($organized_liabilities as $type_key => $type_data) {
			if ($type_data['count'] === 0) {
				unset($organized_liabilities[$type_key]);
			}
		}

		return $organized_liabilities;
	}

	/**
	 * Compute price change for an asset based on the comparison period.
	 *
	 * @since    1.1.0
	 * @param    array     $asset    The asset data array.
	 * @param    string    $period   One of: daily, weekly, monthly, yearly, since_purchase.
	 * @return   array               ['change' => float, 'change_percent' => string, 'label' => string]
	 */
	private function pn_personal_finance_manager_compute_period_change($asset, $period) {
		// Default: use API daily data
		if ($period === 'daily' || empty($asset['price_history'])) {
			return [
				'change'         => floatval($asset['stock_data']['change']),
				'change_percent' => $asset['stock_data']['change_percent'],
				'label'          => '1D',
			];
		}

		$current_price = floatval($asset['stock_data']['price']);

		// Determine target date and label
		switch ($period) {
			case 'weekly':
				$target_date = gmdate('Y-m-d', strtotime('-7 days'));
				$label = '7d';
				break;
			case 'monthly':
				$target_date = gmdate('Y-m-d', strtotime('-30 days'));
				$label = '30d';
				break;
			case 'yearly':
				$target_date = gmdate('Y-m-d', strtotime('-365 days'));
				$label = '1Y';
				break;
			case 'since_purchase':
				$target_date = !empty($asset['purchase_date']) ? $asset['purchase_date'] : '';
				$label = 'All';
				break;
			default:
				$target_date = '';
				$label = '1D';
				break;
		}

		if (empty($target_date)) {
			return [
				'change'         => floatval($asset['stock_data']['change']),
				'change_percent' => $asset['stock_data']['change_percent'],
				'label'          => $label,
			];
		}

		// Find the closest price record on or after the target date
		$reference_price = null;
		$closest_diff = PHP_INT_MAX;
		$target_ts = strtotime($target_date);

		foreach ($asset['price_history'] as $record) {
			if (empty($record['recorded_date']) || !isset($record['price'])) {
				continue;
			}
			$record_ts = strtotime($record['recorded_date']);
			$diff = abs($record_ts - $target_ts);
			if ($diff < $closest_diff) {
				$closest_diff = $diff;
				$reference_price = floatval($record['price']);
			}
		}

		if ($reference_price === null || $reference_price <= 0) {
			return [
				'change'         => floatval($asset['stock_data']['change']),
				'change_percent' => $asset['stock_data']['change_percent'],
				'label'          => $label,
			];
		}

		$change = $current_price - $reference_price;
		$change_percent = (($current_price - $reference_price) / $reference_price) * 100;

		return [
			'change'         => $change,
			'change_percent' => number_format($change_percent, 2) . '%',
			'label'          => $label,
		];
	}

	/**
	 * Display user assets portfolio.
	 *
	 * @since    1.0.5
	 * @param    int       $user_id    User ID to display assets for.
	 * @return   string                HTML output of user assets.
	 */
	public function pn_personal_finance_manager_display_user_assets($user_id) {
		$user_assets = $this->pn_personal_finance_manager_get_user_assets($user_id);
		$user_liabilities = $this->pn_personal_finance_manager_get_user_liabilities($user_id);

		if (empty($user_assets) && empty($user_liabilities)) {
			return '<div class="pn-personal-finance-manager-user-assets-empty">' .
				   '<p>' . __('No assets found.', 'pn-personal-finance-manager') . '</p>' .
				   '</div>';
		}

		ob_start();
		$currency = get_option('pn_personal_finance_manager_currency', 'eur');
		$comparison_period = get_user_meta($user_id, 'pn_personal_finance_manager_comparison_period', true);
		if (empty($comparison_period)) {
			$comparison_period = 'daily';
		}
		$usd_to_currency = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_usd_exchange_rate($currency);
		?>
		<div class="pn-personal-finance-manager-user-assets-portfolio">
			<?php echo wp_kses_post(PN_PERSONAL_FINANCE_MANAGER_Common::pn_personal_finance_manager_page_nav('portfolio')); ?>
			<div class="pn-personal-finance-manager-portfolio-summary pn-personal-finance-manager-mb-50">
				<h2 class="pn-personal-finance-manager-section-toggle collapsed"><?php esc_html_e('Financial Overview', 'pn-personal-finance-manager'); ?> <i class="material-icons-outlined pn-personal-finance-manager-section-toggle-icon">expand_less</i></h2>
				<div class="pn-personal-finance-manager-section-body">
				<?php
				$total_portfolio_value = 0;
				$total_profit_loss = 0;
				foreach ($user_assets as $type_data) {
					$total_portfolio_value += $type_data['total_value'];
					foreach ($type_data['assets'] as $asset) {
						$total_profit_loss += $asset['profit_loss'];
					}
				}

				$total_liabilities_value = 0;
				foreach ($user_liabilities as $type_data) {
					$total_liabilities_value += $type_data['total_value'];
				}

				// Calculate global total invested from stock, crypto and real estate assets
				$global_total_invested = 0;
				$investable_type_keys = ['stocks', 'cryptocurrencies', 'real_estate'];
				foreach ($investable_type_keys as $inv_key) {
					if (isset($user_assets[$inv_key])) {
						foreach ($user_assets[$inv_key]['assets'] as $a) {
							if (isset($a['total_invested']) && floatval($a['total_invested']) > 0) {
								$global_total_invested += floatval($a['total_invested']);
							}
						}
					}
				}

				$converted_portfolio_value = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($total_portfolio_value, $currency);
				$converted_liabilities_value = $total_liabilities_value; // Liabilities are already stored in user currency
				$converted_net_worth = $converted_portfolio_value - $converted_liabilities_value;
				$converted_portfolio_pnl = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($total_profit_loss, $currency);
				$converted_global_invested = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($global_total_invested, $currency);
				$formatted_portfolio_value = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($converted_portfolio_value, $currency);
				$formatted_liabilities_value = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($converted_liabilities_value, $currency);
				$formatted_net_worth = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($converted_net_worth, $currency);
				$formatted_portfolio_pnl = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($converted_portfolio_pnl, $currency);
				$formatted_global_invested = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($converted_global_invested, $currency);
				?>
				<div class="pn-personal-finance-manager-performance-metrics pn-personal-finance-manager-global-metrics">
					<div class="pn-personal-finance-manager-metric-row">
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Assets', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value positive">
								<?php echo esc_html($formatted_portfolio_value['full']); ?>
							</span>
						</div>
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Invested', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value">
								<?php echo esc_html($formatted_global_invested['full']); ?>
							</span>
						</div>
					</div>
					<div class="pn-personal-finance-manager-metric-row">
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Liabilities', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value negative">
								<?php echo esc_html($formatted_liabilities_value['full']); ?>
							</span>
						</div>
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Net Worth', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value <?php echo esc_attr($converted_net_worth >= 0 ? 'positive' : 'negative'); ?>">
								<?php echo esc_html($formatted_net_worth['full']); ?>
							</span>
						</div>
					</div>
					<div class="pn-personal-finance-manager-metric-row">
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Profit/Loss', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value <?php echo esc_attr($total_profit_loss >= 0 ? 'positive' : 'negative'); ?>">
								<?php echo esc_html(($total_profit_loss >= 0 ? '+' : '') . $formatted_portfolio_pnl['full']); ?>
							</span>
						</div>
						<div class="pn-personal-finance-manager-metric-item">
							<?php
							$global_return_pct = $global_total_invested > 0 ? (($total_portfolio_value - $global_total_invested) / $global_total_invested) * 100 : 0;
							?>
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Return', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value <?php echo esc_attr($global_return_pct >= 0 ? 'positive' : 'negative'); ?>">
								<?php echo esc_html(($global_return_pct >= 0 ? '+' : '') . number_format($global_return_pct, 2)); ?>%
							</span>
						</div>
					</div>
				</div>

				<div class="pn-personal-finance-manager-comparison-period-wrapper" style="margin-top:15px;display:flex;align-items:center;gap:10px;">
					<label for="pn-personal-finance-manager-comparison-period-select" style="font-weight:600;font-size:14px;">
						<?php esc_html_e('Comparison Period', 'pn-personal-finance-manager'); ?>
					</label>
					<select id="pn-personal-finance-manager-comparison-period-select" class="pn-personal-finance-manager-comparison-period-select pn-personal-finance-manager-input" style="max-width:200px;">
						<option value="daily" <?php selected($comparison_period, 'daily'); ?>><?php esc_html_e('Daily', 'pn-personal-finance-manager'); ?></option>
						<option value="weekly" <?php selected($comparison_period, 'weekly'); ?>><?php esc_html_e('Weekly (7d)', 'pn-personal-finance-manager'); ?></option>
						<option value="monthly" <?php selected($comparison_period, 'monthly'); ?>><?php esc_html_e('Monthly (30d)', 'pn-personal-finance-manager'); ?></option>
						<option value="yearly" <?php selected($comparison_period, 'yearly'); ?>><?php esc_html_e('Yearly (365d)', 'pn-personal-finance-manager'); ?></option>
						<option value="since_purchase" <?php selected($comparison_period, 'since_purchase'); ?>><?php esc_html_e('Since purchase', 'pn-personal-finance-manager'); ?></option>
					</select>
				</div>

				<?php
				// --- Global Distribution Bar Chart (Assets & Liabilities) ---
				$dist_labels = [];
				$dist_values = [];
				$dist_colors = [];
				$dist_grand_total = $converted_portfolio_value + $total_liabilities_value;
				$asset_colors_list = ['#28a745', '#20c997', '#17a2b8', '#6f42c1', '#fd7e14', '#ffc107', '#0dcaf0', '#6610f2'];
				$liability_colors_list = ['#dc3545', '#e83e8c', '#fd7e14', '#343a40'];
				$ac_idx = 0;
				foreach ($user_assets as $dist_key => $dist_data) {
					if ($dist_data['total_value'] > 0) {
						$dist_labels[] = $dist_data['label'];
						$dist_values[] = round(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($dist_data['total_value'], $currency), 2);
						$dist_colors[] = $asset_colors_list[$ac_idx % count($asset_colors_list)];
						$ac_idx++;
					}
				}
				$lc_idx = 0;
				foreach ($user_liabilities as $dist_key => $dist_data) {
					if ($dist_data['total_value'] > 0) {
						$dist_labels[] = $dist_data['label'];
						$dist_values[] = round(-1 * $dist_data['total_value'], 2); // Liabilities already in user currency
						$dist_colors[] = $liability_colors_list[$lc_idx % count($liability_colors_list)];
						$lc_idx++;
					}
				}
				// Net Worth total bar
				$dist_labels[] = __('Net Worth', 'pn-personal-finance-manager');
				$dist_values[] = round($converted_net_worth, 2);
				$dist_colors[] = $converted_net_worth >= 0 ? '#007bff' : '#dc3545';
				if (!empty($dist_values)):
				$dist_currency_symbol = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_currency_symbol($currency);
				$dist_grand_converted = $dist_grand_total; // Both assets and liabilities now in user currency
				?>
				<div class="pn-personal-finance-manager-distribution-chart" style="margin-top:20px;">
					<h4><?php esc_html_e('Asset & Liability Distribution', 'pn-personal-finance-manager'); ?></h4>
					<div class="pn-personal-finance-manager-chart-container" style="height:350px;box-sizing:border-box;">
						<canvas id="pn-personal-finance-manager-global-distribution-chart"></canvas>
					</div>
				</div>
				<?php
				$_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	function drawGlobalDistributionChart() {
		if (typeof Chart === 'undefined') return setTimeout(drawGlobalDistributionChart, 200);
		var ctx = document.getElementById('pn-personal-finance-manager-global-distribution-chart').getContext('2d');
		var labels = __PNPFM_LABELS__;
		var dataValues = __PNPFM_VALUES__;
		var bgColors = __PNPFM_COLORS__;
		var grandTotal = __PNPFM_GRAND_TOTAL__;
		var symbol = '__PNPFM_SYMBOL__';
		new Chart(ctx, {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					data: dataValues,
					backgroundColor: bgColors,
					borderWidth: 0,
					borderRadius: 4
				}]
			},
			options: {
				indexAxis: 'y',
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: false },
					tooltip: {
						callbacks: {
							label: function(context) {
								var val = context.parsed.x;
								var absVal = Math.abs(val);
								var pct = grandTotal > 0 ? ((absVal / grandTotal) * 100).toFixed(1) : '0.0';
								return (val < 0 ? '-' : '') + symbol + absVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + pct + '%)';
							}
						}
					},
					datalabels: false
				},
				scales: {
					x: {
						display: true,
						ticks: {
							callback: function(value) {
								return (value < 0 ? '-' : '') + symbol + Math.abs(value).toLocaleString();
							}
						},
						grid: { display: true, color: 'rgba(255,255,255,0.05)' }
					},
					y: {
						display: true,
						grid: { display: false }
					}
				}
			}
		});
	}
	drawGlobalDistributionChart();
});
PNPFM_JS;
				$_pnpfm_js = str_replace(
					['__PNPFM_LABELS__', '__PNPFM_VALUES__', '__PNPFM_COLORS__', '__PNPFM_GRAND_TOTAL__', '__PNPFM_SYMBOL__'],
					[wp_json_encode($dist_labels), wp_json_encode($dist_values), wp_json_encode($dist_colors), wp_json_encode($dist_grand_converted), esc_js($dist_currency_symbol)],
					$_pnpfm_js
				);
				wp_add_inline_script('pn-personal-finance-manager-chartjs', $_pnpfm_js, 'after');
				?>
				<?php endif; ?>

			<?php
			// --- Bloque de métricas clave del portfolio de acciones y criptomonedas ---
			$has_stocks = isset($user_assets['stocks']) && !empty($user_assets['stocks']['assets']);
			$has_crypto = isset($user_assets['cryptocurrencies']) && !empty($user_assets['cryptocurrencies']['assets']);
			if ($has_stocks || $has_crypto) {
				// Calcular métricas clave del portfolio
				$total_invested = 0;
				$current_value = 0;
				$profit_loss = 0;
				$total_return = 0;
				$total_days = 0;
				$total_weight = 0;
				$investable_assets = [];
				if ($has_stocks) {
					$investable_assets = array_merge($investable_assets, $user_assets['stocks']['assets']);
				}
				if ($has_crypto) {
					$investable_assets = array_merge($investable_assets, $user_assets['cryptocurrencies']['assets']);
				}
				foreach ($investable_assets as $asset) {
					$purchase_value = 0;
					if (isset($asset['total_invested']) && floatval($asset['total_invested']) > 0) {
						$purchase_value = floatval($asset['total_invested']);
					} elseif (isset($asset['shares']) && isset($asset['purchase_price'])) {
						$purchase_value = floatval($asset['shares']) * floatval($asset['purchase_price']);
					}
					$total_invested += $purchase_value;
					$current_value += isset($asset['current_total_value']) ? floatval($asset['current_total_value']) : 0;
					$profit_loss += isset($asset['current_total_value']) ? floatval($asset['current_total_value']) - $purchase_value : 0;
					if (isset($asset['days_held'])) {
						$total_days += floatval($asset['days_held']) * $purchase_value;
						$total_weight += $purchase_value;
					}
				}
				$converted_invested = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($total_invested, $currency);
				$converted_current = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($current_value, $currency);
				$converted_profit = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($profit_loss, $currency);
				$total_return = $total_invested > 0 ? (($current_value - $total_invested) / $total_invested) * 100 : 0;
				$days_held = $total_weight > 0 ? floor($total_days / $total_weight) : 0;
				$formatted_invested = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($converted_invested, $currency);
				$formatted_current = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($converted_current, $currency);
				$formatted_profit = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($converted_profit, $currency);

				// --- Breakdown doughnut charts (inline row) ---
				$breakdown_symbol = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_currency_symbol($currency);
				$primary_color = sanitize_hex_color(get_option('pn_personal_finance_manager_color_primary', '#008080'));
				if (!$primary_color) { $primary_color = '#008080'; }
				$rgb = sscanf($primary_color, "#%02x%02x%02x");
				$primary_color_rgba = "rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, 0.1)";

				// Stocks data (prepared here, rendered under the stocks category)
				$stocks_chart_labels = [];
				$stocks_chart_values = [];
				$stocks_chart_colors = [$primary_color, '#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6610f2', '#dc3545', '#343a40', '#0dcaf0'];
				$stocks_total_current_value = 0;
				// Sector breakdown data
				$sector_labels_map = [
					'technology' => __('Technology', 'pn-personal-finance-manager'),
					'healthcare' => __('Healthcare', 'pn-personal-finance-manager'),
					'financials' => __('Financials', 'pn-personal-finance-manager'),
					'consumer_discretionary' => __('Consumer Discretionary', 'pn-personal-finance-manager'),
					'consumer_staples' => __('Consumer Staples', 'pn-personal-finance-manager'),
					'industrials' => __('Industrials', 'pn-personal-finance-manager'),
					'energy' => __('Energy', 'pn-personal-finance-manager'),
					'utilities' => __('Utilities', 'pn-personal-finance-manager'),
					'real_estate' => __('Real Estate', 'pn-personal-finance-manager'),
					'materials' => __('Materials', 'pn-personal-finance-manager'),
					'communication' => __('Communication Services', 'pn-personal-finance-manager'),
					'other' => __('Other', 'pn-personal-finance-manager'),
				];
				$sector_totals = [];
				$country_totals = [];
				$country_labels_map = [
					'us' => __('United States', 'pn-personal-finance-manager'),
					'cn' => __('China', 'pn-personal-finance-manager'),
					'jp' => __('Japan', 'pn-personal-finance-manager'),
					'gb' => __('United Kingdom', 'pn-personal-finance-manager'),
					'de' => __('Germany', 'pn-personal-finance-manager'),
					'fr' => __('France', 'pn-personal-finance-manager'),
					'ca' => __('Canada', 'pn-personal-finance-manager'),
					'ch' => __('Switzerland', 'pn-personal-finance-manager'),
					'au' => __('Australia', 'pn-personal-finance-manager'),
					'kr' => __('South Korea', 'pn-personal-finance-manager'),
					'tw' => __('Taiwan', 'pn-personal-finance-manager'),
					'in' => __('India', 'pn-personal-finance-manager'),
					'br' => __('Brazil', 'pn-personal-finance-manager'),
					'nl' => __('Netherlands', 'pn-personal-finance-manager'),
					'es' => __('Spain', 'pn-personal-finance-manager'),
					'it' => __('Italy', 'pn-personal-finance-manager'),
					'se' => __('Sweden', 'pn-personal-finance-manager'),
					'ie' => __('Ireland', 'pn-personal-finance-manager'),
					'dk' => __('Denmark', 'pn-personal-finance-manager'),
					'no' => __('Norway', 'pn-personal-finance-manager'),
					'fi' => __('Finland', 'pn-personal-finance-manager'),
					'il' => __('Israel', 'pn-personal-finance-manager'),
					'sg' => __('Singapore', 'pn-personal-finance-manager'),
					'hk' => __('Hong Kong', 'pn-personal-finance-manager'),
					'mx' => __('Mexico', 'pn-personal-finance-manager'),
					'ar' => __('Argentina', 'pn-personal-finance-manager'),
					'other' => __('Other', 'pn-personal-finance-manager'),
				];
				if (isset($user_assets['stocks'])) {
					foreach ($user_assets['stocks']['assets'] as $s_asset) {
						$s_val = isset($s_asset['current_total_value']) ? floatval($s_asset['current_total_value']) : 0;
						$stocks_total_current_value += $s_val;
						if ($s_val > 0) {
							$s_converted = round(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($s_val, $currency), 2);
							$stocks_chart_labels[] = strtoupper($s_asset['symbol']);
							$stocks_chart_values[] = $s_converted;
							// Accumulate sector totals
							$sector_key = !empty($s_asset['sector']) ? $s_asset['sector'] : 'other';
							if (!isset($sector_totals[$sector_key])) {
								$sector_totals[$sector_key] = 0;
							}
							$sector_totals[$sector_key] += $s_converted;
							// Accumulate country totals
							$country_key = !empty($s_asset['country']) ? $s_asset['country'] : 'other';
							if (!isset($country_totals[$country_key])) {
								$country_totals[$country_key] = 0;
							}
							$country_totals[$country_key] += $s_converted;
						}
					}
				}
				$sector_chart_labels = [];
				$sector_chart_values = [];
				$sector_chart_colors = ['#6f42c1', '#28a745', '#17a2b8', '#ffc107', '#fd7e14', '#dc3545', '#20c997', '#e83e8c', '#343a40', '#0dcaf0', $primary_color, '#6610f2'];
				foreach ($sector_totals as $sk => $sv) {
					$sector_chart_labels[] = isset($sector_labels_map[$sk]) ? $sector_labels_map[$sk] : ucfirst($sk);
					$sector_chart_values[] = $sv;
				}
				$country_chart_labels = [];
				$country_chart_values = [];
				$country_chart_colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14', '#e83e8c', '#20c997', '#343a40', '#0dcaf0', '#6610f2', $primary_color, '#f7931a', '#627eea'];
				foreach ($country_totals as $ck => $cv) {
					$country_chart_labels[] = isset($country_labels_map[$ck]) ? $country_labels_map[$ck] : strtoupper($ck);
					$country_chart_values[] = $cv;
				}

				// Crypto data
				$crypto_chart_labels = [];
				$crypto_chart_values = [];
				$crypto_chart_colors = ['#f7931a', '#627eea', '#26a17b', '#f3ba2f', '#9945ff', '#00aae4', '#2775ca', '#0033ad', '#c2a633', '#e6007a', '#e84142', '#2b6def'];
				if (isset($user_assets['cryptocurrencies']) && !empty($user_assets['cryptocurrencies']['assets'])) {
					foreach ($user_assets['cryptocurrencies']['assets'] as $c_asset) {
						$c_val = isset($c_asset['current_total_value']) ? floatval($c_asset['current_total_value']) : 0;
						if ($c_val > 0) {
							$crypto_chart_labels[] = strtoupper($c_asset['symbol']);
							$crypto_chart_values[] = round(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($c_val, $currency), 2);
						}
					}
				}

				// Real Estate data
				$re_chart_labels = [];
				$re_chart_values = [];
				$re_chart_colors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#17a2b8', '#6f42c1', $primary_color, '#e83e8c', '#20c997', '#6610f2', '#343a40', '#0dcaf0'];
				if (isset($user_assets['real_estate']) && !empty($user_assets['real_estate']['assets'])) {
					foreach ($user_assets['real_estate']['assets'] as $re_asset) {
						$re_val = isset($re_asset['current_total_value']) ? floatval($re_asset['current_total_value']) : 0;
						if ($re_val > 0) {
							$re_chart_labels[] = $re_asset['title'];
							$re_chart_values[] = round(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($re_val, $currency), 2);
						}
					}
				}

				// Breakdown chart data prepared above; rendered under each subcategory

				// --- Stock Portfolio Performance Chart ---
				if ($has_stocks) {
					$stocks_perf_assets = $user_assets['stocks']['assets'];
					$stocks_perf_invested = 0;
					$stocks_perf_current = 0;
					foreach ($stocks_perf_assets as $sp_asset) {
						$sp_pv = 0;
						if (isset($sp_asset['total_invested']) && floatval($sp_asset['total_invested']) > 0) {
							$sp_pv = floatval($sp_asset['total_invested']);
						} elseif (isset($sp_asset['shares']) && isset($sp_asset['purchase_price'])) {
							$sp_pv = floatval($sp_asset['shares']) * floatval($sp_asset['purchase_price']);
						}
						$stocks_perf_invested += $sp_pv;
						$stocks_perf_current += isset($sp_asset['current_total_value']) ? floatval($sp_asset['current_total_value']) : 0;
					}
					$stocks_perf_return = $stocks_perf_invested > 0 ? (($stocks_perf_current - $stocks_perf_invested) / $stocks_perf_invested) * 100 : 0;

					// Forward fill for stocks history
					$stocks_perf_history = [];
					$stocks_perf_dates = [];
					$stocks_perf_price_map = [];
					foreach ($stocks_perf_assets as $sp_asset) {
						if (!empty($sp_asset['price_history']) && isset($sp_asset['shares'])) {
							foreach ($sp_asset['price_history'] as $row) {
								if (empty($row['recorded_date']) || !isset($row['price'])) continue;
								$stocks_perf_dates[$row['recorded_date']] = true;
							}
						}
					}
					$stocks_perf_dates = array_keys($stocks_perf_dates);
					sort($stocks_perf_dates);
					foreach ($stocks_perf_assets as $sp_asset) {
						if (empty($sp_asset['price_history']) || !isset($sp_asset['shares'])) continue;
						$last_value = null;
						$price_by_date = [];
						foreach ($stocks_perf_dates as $date) {
							foreach ($sp_asset['price_history'] as $row) {
								if ($row['recorded_date'] === $date && isset($row['price'])) {
									$last_value = floatval($row['price']) * floatval($sp_asset['shares']);
									break;
								}
							}
							$price_by_date[$date] = ($last_value !== null) ? $last_value : 0;
						}
						$stocks_perf_price_map[] = $price_by_date;
					}
					foreach ($stocks_perf_dates as $date) {
						$stocks_perf_history[$date] = 0;
						foreach ($stocks_perf_price_map as $spbd) {
							$stocks_perf_history[$date] += isset($spbd[$date]) ? $spbd[$date] : 0;
						}
					}
					ksort($stocks_perf_history);

					if (!empty($stocks_perf_history)) {
						$conv_sp_inv = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($stocks_perf_invested, $currency);
						$conv_sp_cur = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($stocks_perf_current, $currency);
						$fmt_sp_inv = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($conv_sp_inv, $currency);
						$fmt_sp_cur = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($conv_sp_cur, $currency);
						$stocks_perf_chart_data = [];
						foreach ($stocks_perf_history as $date => $value) {
							$stocks_perf_chart_data[] = [
								'recorded_date' => $date,
								'value' => PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($value, $currency)
							];
						}
					}
				}

				// --- Crypto Portfolio Performance Chart (data prep) ---
				if ($has_crypto) {
					$crypto_perf_assets = $user_assets['cryptocurrencies']['assets'];
					$crypto_perf_invested = 0;
					$crypto_perf_current = 0;
					foreach ($crypto_perf_assets as $cp_asset) {
						$cp_pv = 0;
						if (isset($cp_asset['total_invested']) && floatval($cp_asset['total_invested']) > 0) {
							$cp_pv = floatval($cp_asset['total_invested']);
						} elseif (isset($cp_asset['shares']) && isset($cp_asset['purchase_price'])) {
							$cp_pv = floatval($cp_asset['shares']) * floatval($cp_asset['purchase_price']);
						}
						$crypto_perf_invested += $cp_pv;
						$crypto_perf_current += isset($cp_asset['current_total_value']) ? floatval($cp_asset['current_total_value']) : 0;
					}
					$crypto_perf_return = $crypto_perf_invested > 0 ? (($crypto_perf_current - $crypto_perf_invested) / $crypto_perf_invested) * 100 : 0;

					// Forward fill for crypto history
					$crypto_perf_history = [];
					$crypto_perf_dates = [];
					$crypto_perf_price_map = [];
					foreach ($crypto_perf_assets as $cp_asset) {
						if (!empty($cp_asset['price_history']) && isset($cp_asset['shares'])) {
							foreach ($cp_asset['price_history'] as $row) {
								if (empty($row['recorded_date']) || !isset($row['price'])) continue;
								$crypto_perf_dates[$row['recorded_date']] = true;
							}
						}
					}
					$crypto_perf_dates = array_keys($crypto_perf_dates);
					sort($crypto_perf_dates);
					foreach ($crypto_perf_assets as $cp_asset) {
						if (empty($cp_asset['price_history']) || !isset($cp_asset['shares'])) continue;
						$last_value = null;
						$price_by_date = [];
						foreach ($crypto_perf_dates as $date) {
							foreach ($cp_asset['price_history'] as $row) {
								if ($row['recorded_date'] === $date && isset($row['price'])) {
									$last_value = floatval($row['price']) * floatval($cp_asset['shares']);
									break;
								}
							}
							$price_by_date[$date] = ($last_value !== null) ? $last_value : 0;
						}
						$crypto_perf_price_map[] = $price_by_date;
					}
					foreach ($crypto_perf_dates as $date) {
						$crypto_perf_history[$date] = 0;
						foreach ($crypto_perf_price_map as $cpbd) {
							$crypto_perf_history[$date] += isset($cpbd[$date]) ? $cpbd[$date] : 0;
						}
					}
					ksort($crypto_perf_history);

					if (!empty($crypto_perf_history)) {
						$conv_cp_inv = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($crypto_perf_invested, $currency);
						$conv_cp_cur = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($crypto_perf_current, $currency);
						$fmt_cp_inv = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($conv_cp_inv, $currency);
						$fmt_cp_cur = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($conv_cp_cur, $currency);
						$crypto_perf_chart_data = [];
						foreach ($crypto_perf_history as $date => $value) {
							$crypto_perf_chart_data[] = [
								'recorded_date' => $date,
								'value' => PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($value, $currency)
							];
						}
					}
				}
			}
			?>
				</div><!-- .pn-personal-finance-manager-section-body -->
			</div><!-- .pn-personal-finance-manager-portfolio-summary -->

			<div class="pn-personal-finance-manager-section-header">
				<h2 class="pn-personal-finance-manager-section-toggle collapsed"><?php esc_html_e('Assets', 'pn-personal-finance-manager'); ?> <i class="material-icons-outlined pn-personal-finance-manager-section-toggle-icon">expand_less</i></h2>
				<?php if (is_user_logged_in()) : ?>
				<a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-add" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_asset_new">
					<i class="material-icons-outlined pn-personal-finance-manager-cursor-pointer pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-tooltip pn-personal-finance-manager-color-main-0" title="<?php esc_attr_e('Add new Asset', 'pn-personal-finance-manager'); ?>">add_circle</i>
				</a>
				<?php endif; ?>
			</div>
			<div class="pn-personal-finance-manager-section-body">

			<?php if (isset($formatted_invested)): ?>
			<div class="pn-personal-finance-manager-performance-metrics pn-personal-finance-manager-portfolio-metrics">
				<div class="pn-personal-finance-manager-metric-row">
					<div class="pn-personal-finance-manager-metric-item">
						<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Invested', 'pn-personal-finance-manager'); ?></span>
						<span class="pn-personal-finance-manager-metric-value portfolio-total-invested">
							<?php echo esc_html($formatted_invested['full']); ?>
						</span>
					</div>
					<div class="pn-personal-finance-manager-metric-item">
						<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Current Value', 'pn-personal-finance-manager'); ?></span>
						<span class="pn-personal-finance-manager-metric-value portfolio-current-value">
							<?php echo esc_html($formatted_current['full']); ?>
						</span>
					</div>
				</div>
				<div class="pn-personal-finance-manager-metric-row">
					<div class="pn-personal-finance-manager-metric-item">
						<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Profit/Loss', 'pn-personal-finance-manager'); ?></span>
						<span class="pn-personal-finance-manager-metric-value portfolio-profit-loss <?php echo esc_attr($profit_loss >= 0 ? 'positive' : 'negative'); ?>">
							<?php echo esc_html(($profit_loss >= 0 ? '+' : '-') . $formatted_profit['full']); ?>
						</span>
					</div>
					<div class="pn-personal-finance-manager-metric-item">
						<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Return', 'pn-personal-finance-manager'); ?></span>
						<span class="pn-personal-finance-manager-metric-value portfolio-total-return <?php echo esc_attr($total_return >= 0 ? 'positive' : 'negative'); ?>">
							<?php echo esc_html(($total_return >= 0 ? '+' : '-') . number_format(abs($total_return), 2)); ?>%
						</span>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php foreach ($user_assets as $type_key => $type_data): ?>
				<div class="pn-personal-finance-manager-asset-category">
					<h3 class="pn-personal-finance-manager-section-toggle collapsed"><?php echo esc_html($type_data['label']); ?>
						<span class="pn-personal-finance-manager-asset-count">(<?php echo intval($type_data['count']); ?>)</span>
						<i class="material-icons-outlined pn-personal-finance-manager-section-toggle-icon">expand_less</i>
					</h3>
					<div class="pn-personal-finance-manager-section-body">
					<div class="pn-personal-finance-manager-sort-bar">
						<select class="pn-personal-finance-manager-sort-select">
							<option value="name"><?php esc_html_e('Name', 'pn-personal-finance-manager'); ?></option>
							<option value="value"><?php esc_html_e('Value', 'pn-personal-finance-manager'); ?></option>
							<?php if ($type_key === 'stocks' || $type_key === 'cryptocurrencies' || $type_key === 'real_estate'): ?>
							<option value="pl"><?php esc_html_e('Profit/Loss', 'pn-personal-finance-manager'); ?></option>
							<?php endif; ?>
							<option value="date"><?php esc_html_e('Purchase Date', 'pn-personal-finance-manager'); ?></option>
						</select>
						<button type="button" class="pn-personal-finance-manager-sort-dir-btn" data-dir="asc" title="<?php esc_attr_e('Sort ascending', 'pn-personal-finance-manager'); ?>">
							<i class="material-icons-outlined">arrow_upward</i>
						</button>
					</div>

					<?php if ($type_key === 'stocks' || $type_key === 'cryptocurrencies'): ?>
						<div class="pn-personal-finance-manager-stocks-grid">
							<?php foreach ($type_data['assets'] as $asset): ?>
								<div class="pn-personal-finance-manager-stock-card<?php echo esc_attr(!empty($asset['is_sold']) ? ' pn-personal-finance-manager-stock-card-sold' : ''); ?> pn-personal-finance-manager-asset" data-pn_personal_finance_manager_asset-id="<?php echo esc_attr($asset['id']); ?>" data-sort-name="<?php echo esc_attr($asset['title']); ?>" data-sort-value="<?php echo esc_attr($asset['current_total_value']); ?>" data-sort-pl="<?php echo esc_attr($asset['profit_loss']); ?>" data-sort-date="<?php echo esc_attr($asset['purchase_date']); ?>" data-sort-sold="<?php echo esc_attr(!empty($asset['is_sold']) ? '1' : '0'); ?>">
									<?php
									$asset_pct = ($type_data['total_value'] > 0 && isset($asset['current_total_value']))
										? (floatval($asset['current_total_value']) / $type_data['total_value']) * 100
										: 0;
									?>
									<div class="pn-personal-finance-manager-stock-header pn-personal-finance-manager-stock-toggle">
										<h4>
											<?php echo esc_html($asset['title']); ?>
											<?php if ($asset_pct > 0): ?>
												<span class="pn-personal-finance-manager-asset-pct"><?php echo esc_html(number_format($asset_pct, 1)); ?>%</span>
											<?php endif; ?>
											<?php if (!empty($asset['symbol_name'])): ?>
												<span class="pn-personal-finance-manager-stock-symbol-name"><?php echo esc_html($asset['symbol_name']); ?></span>
											<?php endif; ?>
										</h4>
										<div>
											<span class="pn-personal-finance-manager-stock-symbol"><?php echo esc_html(strtoupper($asset['symbol'])); ?></span>
											<i class="material-icons-outlined pn-personal-finance-manager-stock-toggle-icon">expand_more</i>
										</div>
									</div>

									<?php if ($asset['stock_data']): ?>
										<div class="pn-personal-finance-manager-stock-card-summary pn-personal-finance-manager-mb-10">
											<div class="pn-personal-finance-manager-stock-card-summary-value">
												<?php
												$converted = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['current_total_value'], $currency), $currency);
												echo esc_html($converted['full']);
												?>
											</div>
											<?php if (!empty($asset['is_sold'])): ?>
												<div class="pn-personal-finance-manager-stock-change pn-personal-finance-manager-sold-badge">
													<?php esc_html_e('Sold', 'pn-personal-finance-manager'); ?>
												</div>
											<?php else: ?>
												<?php
												$period_change = $this->pn_personal_finance_manager_compute_period_change($asset, $comparison_period);
												$badge_total_change = $period_change['change'] * floatval($asset['shares']);
												$badge_converted = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($badge_total_change, $currency);
												$badge_formatted = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(abs($badge_converted), $currency);
												?>
												<div class="pn-personal-finance-manager-stock-change <?php echo esc_attr($badge_converted >= 0 ? 'positive' : 'negative'); ?>">
													<?php echo $badge_converted >= 0 ? '+' : '-'; ?><?php echo esc_html($badge_formatted['full']); ?>
													(<?php echo esc_html($period_change['change_percent']); ?>)
													<span class="pn-personal-finance-manager-period-label"><?php echo esc_html($period_change['label']); ?></span>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>

									<div class="pn-personal-finance-manager-stock-body" style="display:none;">
										<div class="pn-personal-finance-manager-stock-body-actions">
											<a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none pn-personal-finance-manager-edit-asset-link" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-edit" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_asset_edit" title="<?php esc_attr_e('Edit Asset', 'pn-personal-finance-manager'); ?>">
												<i class="material-icons-outlined">edit</i> <?php esc_html_e('Edit Asset', 'pn-personal-finance-manager'); ?>
											</a>
										</div>
									<?php if ($asset['stock_data']): ?>
										<div class="pn-personal-finance-manager-performance-metrics pn-personal-finance-manager-stock-detail-metrics">
											<div class="pn-personal-finance-manager-metric-row">
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php echo esc_html($type_key === 'cryptocurrencies' ? __('Amount', 'pn-personal-finance-manager') : __('Shares', 'pn-personal-finance-manager')); ?></span>
													<span class="pn-personal-finance-manager-metric-value"><?php echo esc_html(number_format($asset['shares'], 2)); ?></span>
												</div>
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Purchase Price', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php if ($asset['purchase_price'] > 0): ?>
															<?php
															$fmt_purchase = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['purchase_price'], $currency), $currency);
															echo esc_html($fmt_purchase['full']);
															?>
														<?php else: ?>
															<button class="<?php echo esc_attr($type_key === 'cryptocurrencies' ? 'pn-personal-finance-manager-fetch-crypto-purchase-price' : 'pn-personal-finance-manager-fetch-purchase-price'); ?> pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini"
																data-asset-id="<?php echo esc_attr($asset['id']); ?>"
																data-symbol="<?php echo esc_attr(strtoupper($asset['symbol'])); ?>">
																<i class="material-icons-outlined">sync</i> <?php esc_html_e('Fetch price', 'pn-personal-finance-manager'); ?>
															</button>
														<?php endif; ?>
													</span>
												</div>
											</div>
											<div class="pn-personal-finance-manager-metric-row">
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Current Price', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php
														$fmt_current_price = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['current_value'], $currency), $currency);
														echo esc_html($fmt_current_price['full']);
														?>
													</span>
												</div>
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Price Variation', 'pn-personal-finance-manager'); ?></span>
													<?php
													$price_variation = $asset['current_value'] - $asset['purchase_price'];
													$price_variation_pct = $asset['purchase_price'] > 0 ? ($price_variation / $asset['purchase_price']) * 100 : 0;
													?>
													<span class="pn-personal-finance-manager-metric-value <?php echo esc_attr($price_variation >= 0 ? 'positive' : 'negative'); ?>">
														<?php echo $price_variation >= 0 ? '+' : ''; ?><?php echo esc_html(number_format($price_variation_pct, 2)); ?>%
													</span>
												</div>
											</div>
											<?php if ($asset['total_invested'] > 0): ?>
											<div class="pn-personal-finance-manager-metric-row">
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Invested', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php
														$fmt_invested = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['total_invested'], $currency), $currency);
														echo esc_html($fmt_invested['full']);
														?>
													</span>
												</div>
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Profit/Loss', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value <?php echo esc_attr($asset['profit_loss'] >= 0 ? 'positive' : 'negative'); ?>">
														<?php
														$converted_profit = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['profit_loss'], $currency), $currency);
														echo $asset['profit_loss'] >= 0 ? '+' : '';
														echo esc_html($converted_profit['full']);
														?>
														(<?php echo $asset['profit_loss_percent'] >= 0 ? '+' : ''; ?><?php echo esc_html(number_format($asset['profit_loss_percent'], 2)); ?>%)
													</span>
												</div>
											</div>
											<?php endif; ?>
											<?php if (!empty($asset['is_sold'])): ?>
											<div class="pn-personal-finance-manager-metric-row">
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Sale Date', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php echo !empty($asset['sold_date']) ? esc_html(date_i18n(get_option('date_format'), strtotime($asset['sold_date']))) : '—'; ?>
													</span>
												</div>
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Sale Price', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php
														$fmt_sold = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['sold_price'], $currency), $currency);
														echo esc_html($fmt_sold['full']);
														?>
													</span>
												</div>
											</div>
											<?php endif; ?>
										</div>

										<?php if (!empty($asset['price_history']) && empty($asset['is_sold'])): ?>
											<?php
											$stock_chart_id = 'pn-personal-finance-manager-stock-chart-' . esc_attr($asset['id']);
											$chart_symbol = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_currency_symbol($currency);
											$stock_history_data = [];
											foreach ($asset['price_history'] as $row) {
												if (!empty($row['recorded_date']) && isset($row['price'])) {
													$stock_history_data[] = [
														'date' => $row['recorded_date'],
														'price' => round(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd(floatval($row['price']), $currency), 2)
													];
												}
											}
											if (!empty($stock_history_data)):
											?>
											<div class="pn-personal-finance-manager-stock-evolution-chart">
												<button type="button" class="pn-personal-finance-manager-chart-fullscreen-btn pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini" title="<?php esc_attr_e('Fullscreen', 'pn-personal-finance-manager'); ?>">
													<i class="material-icons-outlined">fullscreen</i>
												</button>
												<div class="pn-personal-finance-manager-chart-container">
													<canvas id="<?php echo esc_attr($stock_chart_id); ?>" width="400" height="150"
														data-chart-data="<?php echo esc_attr(json_encode($stock_history_data)); ?>"
														data-chart-symbol="<?php echo esc_attr($chart_symbol); ?>"
														data-chart-label="<?php echo esc_attr(strtoupper($asset['symbol'])); ?>"></canvas>
												</div>
											</div>
											<?php endif; ?>
										<?php elseif (empty($asset['price_history']) && empty($asset['is_sold']) && !empty($asset['symbol'])): ?>
											<?php
											$load_history_asset_type = ($type_key === 'cryptocurrencies') ? 'crypto' : 'stock';
											?>
											<div class="pn-personal-finance-manager-load-history-wrapper" style="text-align:center;margin:10px 0;">
												<button type="button" class="pn-personal-finance-manager-load-asset-history-btn pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini pn-personal-finance-manager-btn-transparent"
													data-asset-id="<?php echo esc_attr($asset['id']); ?>"
													data-symbol="<?php echo esc_attr($asset['symbol']); ?>"
													data-asset-type="<?php echo esc_attr($load_history_asset_type); ?>">
													<?php esc_html_e('Load price history', 'pn-personal-finance-manager'); ?>
												</button>
											</div>
										<?php endif; ?>
									<?php else: ?>
										<div class="pn-personal-finance-manager-stock-no-data">
											<?php esc_html_e('No current data available', 'pn-personal-finance-manager'); ?>
										</div>
									<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<?php if ($type_key === 'stocks' && isset($stocks_perf_chart_data) && !empty($stocks_perf_chart_data)): ?>
						<div class="pn-personal-finance-manager-portfolio-performance-chart" style="margin-top: 30px;">
							<h4><?php esc_html_e('Stock Portfolio Performance', 'pn-personal-finance-manager'); ?></h4>
							<div class="pn-personal-finance-manager-performance-metrics" style="margin-bottom:10px;">
								<div class="pn-personal-finance-manager-metric-row">
									<div class="pn-personal-finance-manager-metric-item">
										<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Invested', 'pn-personal-finance-manager'); ?></span>
										<span class="pn-personal-finance-manager-metric-value"><?php echo esc_html($fmt_sp_inv['full']); ?></span>
									</div>
									<div class="pn-personal-finance-manager-metric-item">
										<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Current Value', 'pn-personal-finance-manager'); ?></span>
										<span class="pn-personal-finance-manager-metric-value"><?php echo esc_html($fmt_sp_cur['full']); ?></span>
									</div>
									<div class="pn-personal-finance-manager-metric-item">
										<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Return', 'pn-personal-finance-manager'); ?></span>
										<span class="pn-personal-finance-manager-metric-value <?php echo esc_attr($stocks_perf_return >= 0 ? 'positive' : 'negative'); ?>">
											<?php echo esc_html(($stocks_perf_return >= 0 ? '+' : '') . number_format($stocks_perf_return, 2)); ?>%
										</span>
									</div>
								</div>
							</div>
							<div class="pn-personal-finance-manager-chart-container">
								<button type="button" class="pn-personal-finance-manager-chart-fullscreen-btn pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini" title="<?php esc_attr_e('Fullscreen', 'pn-personal-finance-manager'); ?>">
									<i class="material-icons-outlined">fullscreen</i>
								</button>
								<canvas id="pn-personal-finance-manager-stocks-perf-chart"
									data-chart-data="<?php echo esc_attr(json_encode($stocks_perf_chart_data)); ?>"
									data-chart-symbol="<?php echo esc_attr(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_currency_symbol($currency)); ?>"
									data-chart-label="<?php esc_attr_e('Stock Portfolio', 'pn-personal-finance-manager'); ?>"
									data-chart-invested="<?php echo esc_attr($conv_sp_inv); ?>"></canvas>
							</div>
						</div>
						<?php
						$_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	function drawStocksPerfChart() {
		if (typeof Chart === 'undefined') return setTimeout(drawStocksPerfChart, 200);
		var canvas = document.getElementById('pn-personal-finance-manager-stocks-perf-chart');
		var ctx = canvas.getContext('2d');
		var chartData = JSON.parse(canvas.getAttribute('data-chart-data'));
		var symbol = canvas.getAttribute('data-chart-symbol');
		var invested = parseFloat(canvas.getAttribute('data-chart-invested'));
		var labels = chartData.map(function(d) { return d.recorded_date; });
		var values = chartData.map(function(d) { return d.value; });
		new Chart(ctx, {
			type: 'line',
			data: {
				labels: labels,
				datasets: [
					{
						label: canvas.getAttribute('data-chart-label'),
						data: values,
						borderColor: '__PNPFM_PRIMARY_COLOR__',
						backgroundColor: '__PNPFM_PRIMARY_COLOR_RGBA__',
						fill: true,
						tension: 0.3,
						pointRadius: 2
					},
					{
						label: '__PNPFM_INVESTED_LABEL__',
						data: labels.map(function() { return invested; }),
						borderColor: '#6c757d',
						borderDash: [5, 5],
						fill: false,
						pointRadius: 0
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: true, position: 'bottom' },
					tooltip: {
						callbacks: {
							label: function(context) {
								return context.dataset.label + ': ' + symbol + context.parsed.y.toFixed(2);
							}
						}
					}
				},
				scales: {
					x: { display: true, ticks: { maxTicksLimit: 8 } },
					y: {
						display: true,
						ticks: {
							callback: function(value) { return symbol + value.toLocaleString(); }
						}
					}
				}
			}
		});
	}
	drawStocksPerfChart();
});
PNPFM_JS;
						$_pnpfm_js = str_replace(
							['__PNPFM_PRIMARY_COLOR__', '__PNPFM_PRIMARY_COLOR_RGBA__', '__PNPFM_INVESTED_LABEL__'],
							[esc_js($primary_color), esc_js($primary_color_rgba), esc_js(__('Invested', 'pn-personal-finance-manager'))],
							$_pnpfm_js
						);
						wp_add_inline_script('pn-personal-finance-manager-chartjs', $_pnpfm_js, 'after');
						?>
						<?php endif; ?>

						<?php if ($type_key === 'stocks' && isset($stocks_chart_values) && !empty($stocks_chart_values)): ?>
						<div class="pn-personal-finance-manager-breakdown-charts-row" style="margin-top:30px;">
							<div class="pn-personal-finance-manager-breakdown-chart-item">
								<h4><?php esc_html_e('Stocks Breakdown', 'pn-personal-finance-manager'); ?></h4>
								<div class="pn-personal-finance-manager-chart-container">
									<canvas id="pn-personal-finance-manager-stocks-breakdown-doughnut" width="300" height="300"></canvas>
								</div>
							</div>
							<?php
							$_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	function drawStocksBreakdownChart() {
		if (typeof Chart === 'undefined') return setTimeout(drawStocksBreakdownChart, 200);
		var ctx = document.getElementById('pn-personal-finance-manager-stocks-breakdown-doughnut').getContext('2d');
		var colors = __PNPFM_COLORS__;
		var dataValues = __PNPFM_VALUES__;
		var bgColors = dataValues.map(function(_, i) { return colors[i % colors.length]; });
		new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: __PNPFM_LABELS__,
				datasets: [{
					data: dataValues,
					backgroundColor: bgColors,
					borderWidth: 2,
					borderColor: '#fff'
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: { display: true, position: 'bottom' },
					tooltip: {
						callbacks: {
							label: function(context) {
								var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
								var percentage = ((context.parsed / total) * 100).toFixed(2);
								return context.label + ': __PNPFM_SYMBOL__' + context.parsed.toFixed(2) + ' (' + percentage + '%)';
							}
						}
					}
				}
			}
		});
	}
	drawStocksBreakdownChart();
});
PNPFM_JS;
							$_pnpfm_js = str_replace(
								['__PNPFM_COLORS__', '__PNPFM_VALUES__', '__PNPFM_LABELS__', '__PNPFM_SYMBOL__'],
								[wp_json_encode($stocks_chart_colors), wp_json_encode($stocks_chart_values), wp_json_encode($stocks_chart_labels), esc_js($breakdown_symbol)],
								$_pnpfm_js
							);
							wp_add_inline_script('pn-personal-finance-manager-chartjs', $_pnpfm_js, 'after');
							?>
							<?php if (!empty($sector_chart_values)): ?>
							<div class="pn-personal-finance-manager-breakdown-chart-item">
								<h4><?php esc_html_e('Sector Breakdown', 'pn-personal-finance-manager'); ?></h4>
								<div class="pn-personal-finance-manager-chart-container">
									<canvas id="pn-personal-finance-manager-sector-breakdown-doughnut" width="300" height="300"></canvas>
								</div>
							</div>
							<?php
							$_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	function drawSectorBreakdownChart() {
		if (typeof Chart === 'undefined') return setTimeout(drawSectorBreakdownChart, 200);
		var ctx = document.getElementById('pn-personal-finance-manager-sector-breakdown-doughnut').getContext('2d');
		var colors = __PNPFM_COLORS__;
		var dataValues = __PNPFM_VALUES__;
		var bgColors = dataValues.map(function(_, i) { return colors[i % colors.length]; });
		new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: __PNPFM_LABELS__,
				datasets: [{
					data: dataValues,
					backgroundColor: bgColors,
					borderWidth: 2,
					borderColor: '#fff'
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: { display: true, position: 'bottom' },
					tooltip: {
						callbacks: {
							label: function(context) {
								var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
								var percentage = ((context.parsed / total) * 100).toFixed(2);
								return context.label + ': __PNPFM_SYMBOL__' + context.parsed.toFixed(2) + ' (' + percentage + '%)';
							}
						}
					}
				}
			}
		});
	}
	drawSectorBreakdownChart();
});
PNPFM_JS;
							$_pnpfm_js = str_replace(
								['__PNPFM_COLORS__', '__PNPFM_VALUES__', '__PNPFM_LABELS__', '__PNPFM_SYMBOL__'],
								[wp_json_encode($sector_chart_colors), wp_json_encode($sector_chart_values), wp_json_encode($sector_chart_labels), esc_js($breakdown_symbol)],
								$_pnpfm_js
							);
							wp_add_inline_script('pn-personal-finance-manager-chartjs', $_pnpfm_js, 'after');
							?>
							<?php endif; ?>
							<?php if (!empty($country_chart_values)): ?>
							<div class="pn-personal-finance-manager-breakdown-chart-item">
								<h4><?php esc_html_e('Country Diversification', 'pn-personal-finance-manager'); ?></h4>
								<div class="pn-personal-finance-manager-chart-container">
									<canvas id="pn-personal-finance-manager-country-breakdown-doughnut" width="300" height="300"></canvas>
								</div>
							</div>
							<?php
							$_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	function drawCountryBreakdownChart() {
		if (typeof Chart === 'undefined') return setTimeout(drawCountryBreakdownChart, 200);
		var ctx = document.getElementById('pn-personal-finance-manager-country-breakdown-doughnut').getContext('2d');
		var colors = __PNPFM_COLORS__;
		var dataValues = __PNPFM_VALUES__;
		var bgColors = dataValues.map(function(_, i) { return colors[i % colors.length]; });
		new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: __PNPFM_LABELS__,
				datasets: [{
					data: dataValues,
					backgroundColor: bgColors,
					borderWidth: 2,
					borderColor: '#fff'
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: { display: true, position: 'bottom' },
					tooltip: {
						callbacks: {
							label: function(context) {
								var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
								var percentage = ((context.parsed / total) * 100).toFixed(2);
								return context.label + ': __PNPFM_SYMBOL__' + context.parsed.toFixed(2) + ' (' + percentage + '%)';
							}
						}
					}
				}
			}
		});
	}
	drawCountryBreakdownChart();
});
PNPFM_JS;
							$_pnpfm_js = str_replace(
								['__PNPFM_COLORS__', '__PNPFM_VALUES__', '__PNPFM_LABELS__', '__PNPFM_SYMBOL__'],
								[wp_json_encode($country_chart_colors), wp_json_encode($country_chart_values), wp_json_encode($country_chart_labels), esc_js($breakdown_symbol)],
								$_pnpfm_js
							);
							wp_add_inline_script('pn-personal-finance-manager-chartjs', $_pnpfm_js, 'after');
							?>
							<?php endif; ?>
						</div>
						<?php endif; ?>

						<?php if ($type_key === 'cryptocurrencies' && isset($crypto_perf_chart_data) && !empty($crypto_perf_chart_data)): ?>
						<div class="pn-personal-finance-manager-portfolio-performance-chart" style="margin-top: 30px;">
							<h4><?php esc_html_e('Crypto Portfolio Performance', 'pn-personal-finance-manager'); ?></h4>
							<div class="pn-personal-finance-manager-performance-metrics" style="margin-bottom:10px;">
								<div class="pn-personal-finance-manager-metric-row">
									<div class="pn-personal-finance-manager-metric-item">
										<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Invested', 'pn-personal-finance-manager'); ?></span>
										<span class="pn-personal-finance-manager-metric-value"><?php echo esc_html($fmt_cp_inv['full']); ?></span>
									</div>
									<div class="pn-personal-finance-manager-metric-item">
										<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Current Value', 'pn-personal-finance-manager'); ?></span>
										<span class="pn-personal-finance-manager-metric-value"><?php echo esc_html($fmt_cp_cur['full']); ?></span>
									</div>
									<div class="pn-personal-finance-manager-metric-item">
										<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Return', 'pn-personal-finance-manager'); ?></span>
										<span class="pn-personal-finance-manager-metric-value <?php echo esc_attr($crypto_perf_return >= 0 ? 'positive' : 'negative'); ?>">
											<?php echo esc_html(($crypto_perf_return >= 0 ? '+' : '') . number_format($crypto_perf_return, 2)); ?>%
										</span>
									</div>
								</div>
							</div>
							<div class="pn-personal-finance-manager-chart-container">
								<button type="button" class="pn-personal-finance-manager-chart-fullscreen-btn pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini" title="<?php esc_attr_e('Fullscreen', 'pn-personal-finance-manager'); ?>">
									<i class="material-icons-outlined">fullscreen</i>
								</button>
								<canvas id="pn-personal-finance-manager-crypto-perf-chart"
									data-chart-data="<?php echo esc_attr(json_encode($crypto_perf_chart_data)); ?>"
									data-chart-symbol="<?php echo esc_attr(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_currency_symbol($currency)); ?>"
									data-chart-label="<?php esc_attr_e('Crypto Portfolio', 'pn-personal-finance-manager'); ?>"
									data-chart-invested="<?php echo esc_attr($conv_cp_inv); ?>"></canvas>
							</div>
						</div>
						<?php
						$_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	function drawCryptoPerfChart() {
		if (typeof Chart === 'undefined') return setTimeout(drawCryptoPerfChart, 200);
		var canvas = document.getElementById('pn-personal-finance-manager-crypto-perf-chart');
		var ctx = canvas.getContext('2d');
		var chartData = JSON.parse(canvas.getAttribute('data-chart-data'));
		var symbol = canvas.getAttribute('data-chart-symbol');
		var invested = parseFloat(canvas.getAttribute('data-chart-invested'));
		var labels = chartData.map(function(d) { return d.recorded_date; });
		var values = chartData.map(function(d) { return d.value; });
		new Chart(ctx, {
			type: 'line',
			data: {
				labels: labels,
				datasets: [
					{
						label: canvas.getAttribute('data-chart-label'),
						data: values,
						borderColor: '#f7931a',
						backgroundColor: 'rgba(247, 147, 26, 0.1)',
						fill: true,
						tension: 0.3,
						pointRadius: 2
					},
					{
						label: '__PNPFM_INVESTED_LABEL__',
						data: labels.map(function() { return invested; }),
						borderColor: '#6c757d',
						borderDash: [5, 5],
						fill: false,
						pointRadius: 0
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: true, position: 'bottom' },
					tooltip: {
						callbacks: {
							label: function(context) {
								return context.dataset.label + ': ' + symbol + context.parsed.y.toFixed(2);
							}
						}
					}
				},
				scales: {
					x: { display: true, ticks: { maxTicksLimit: 8 } },
					y: {
						display: true,
						ticks: {
							callback: function(value) { return symbol + value.toLocaleString(); }
						}
					}
				}
			}
		});
	}
	drawCryptoPerfChart();
});
PNPFM_JS;
						$_pnpfm_js = str_replace(
							'__PNPFM_INVESTED_LABEL__',
							esc_js(__('Invested', 'pn-personal-finance-manager')),
							$_pnpfm_js
						);
						wp_add_inline_script('pn-personal-finance-manager-chartjs', $_pnpfm_js, 'after');
						?>
						<?php endif; ?>

						<?php if ($type_key === 'cryptocurrencies' && !empty($crypto_chart_values)): ?>
						<div class="pn-personal-finance-manager-breakdown-charts-row" style="margin-top:30px;">
							<div class="pn-personal-finance-manager-breakdown-chart-item">
								<h4><?php esc_html_e('Crypto Breakdown', 'pn-personal-finance-manager'); ?></h4>
								<div class="pn-personal-finance-manager-chart-container">
									<canvas id="pn-personal-finance-manager-crypto-breakdown-doughnut" width="300" height="300"></canvas>
								</div>
							</div>
							<?php
							$_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	function drawCryptoBreakdownChart() {
		if (typeof Chart === 'undefined') return setTimeout(drawCryptoBreakdownChart, 200);
		var ctx = document.getElementById('pn-personal-finance-manager-crypto-breakdown-doughnut').getContext('2d');
		var colors = __PNPFM_COLORS__;
		var dataValues = __PNPFM_VALUES__;
		var bgColors = dataValues.map(function(_, i) { return colors[i % colors.length]; });
		new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: __PNPFM_LABELS__,
				datasets: [{
					data: dataValues,
					backgroundColor: bgColors,
					borderWidth: 2,
					borderColor: '#fff'
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: { display: true, position: 'bottom' },
					tooltip: {
						callbacks: {
							label: function(context) {
								var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
								var percentage = ((context.parsed / total) * 100).toFixed(2);
								return context.label + ': __PNPFM_SYMBOL__' + context.parsed.toFixed(2) + ' (' + percentage + '%)';
							}
						}
					}
				}
			}
		});
	}
	drawCryptoBreakdownChart();
});
PNPFM_JS;
							$_pnpfm_js = str_replace(
								['__PNPFM_COLORS__', '__PNPFM_VALUES__', '__PNPFM_LABELS__', '__PNPFM_SYMBOL__'],
								[wp_json_encode($crypto_chart_colors), wp_json_encode($crypto_chart_values), wp_json_encode($crypto_chart_labels), esc_js($breakdown_symbol)],
								$_pnpfm_js
							);
							wp_add_inline_script('pn-personal-finance-manager-chartjs', $_pnpfm_js, 'after');
							?>
						</div>
						<?php endif; ?>

					<?php elseif ($type_key === 'real_estate'): ?>
						<div class="pn-personal-finance-manager-stocks-grid">
							<?php foreach ($type_data['assets'] as $asset): ?>
								<div class="pn-personal-finance-manager-stock-card<?php echo esc_attr(!empty($asset['is_sold']) ? ' pn-personal-finance-manager-stock-card-sold' : ''); ?> pn-personal-finance-manager-asset" data-pn_personal_finance_manager_asset-id="<?php echo esc_attr($asset['id']); ?>" data-sort-name="<?php echo esc_attr($asset['title']); ?>" data-sort-value="<?php echo esc_attr($asset['current_total_value']); ?>" data-sort-pl="<?php echo esc_attr($asset['profit_loss']); ?>" data-sort-date="<?php echo esc_attr($asset['purchase_date']); ?>" data-sort-sold="<?php echo esc_attr(!empty($asset['is_sold']) ? '1' : '0'); ?>">
									<div class="pn-personal-finance-manager-stock-header pn-personal-finance-manager-stock-toggle">
										<h4><?php echo esc_html($asset['title']); ?></h4>
										<div>
											<?php if (isset($asset['ownership_percent']) && $asset['ownership_percent'] < 100): ?>
												<span class="pn-personal-finance-manager-stock-symbol"><?php echo esc_html(number_format($asset['ownership_percent'], 1)); ?>%</span>
											<?php endif; ?>
											<i class="material-icons-outlined pn-personal-finance-manager-stock-toggle-icon">expand_more</i>
										</div>
									</div>

									<?php
									$converted = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['current_total_value'], $currency), $currency);
									?>
									<div class="pn-personal-finance-manager-stock-card-summary pn-personal-finance-manager-mb-10">
										<div class="pn-personal-finance-manager-stock-card-summary-value">
											<?php echo esc_html($converted['full']); ?>
										</div>
										<?php if (!empty($asset['is_sold'])): ?>
											<div class="pn-personal-finance-manager-stock-change pn-personal-finance-manager-sold-badge">
												<?php esc_html_e('Sold', 'pn-personal-finance-manager'); ?>
											</div>
										<?php elseif ($asset['profit_loss'] != 0): ?>
											<div class="pn-personal-finance-manager-stock-change <?php echo esc_attr($asset['profit_loss'] >= 0 ? 'positive' : 'negative'); ?>">
												<?php echo $asset['profit_loss'] >= 0 ? '+' : ''; ?><?php echo esc_html(number_format($asset['profit_loss_percent'], 2)); ?>%
											</div>
										<?php endif; ?>
									</div>

									<div class="pn-personal-finance-manager-stock-body" style="display:none;">
										<div class="pn-personal-finance-manager-stock-body-actions">
											<a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none pn-personal-finance-manager-edit-asset-link" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-edit" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_asset_edit" title="<?php esc_attr_e('Edit Asset', 'pn-personal-finance-manager'); ?>">
												<i class="material-icons-outlined">edit</i> <?php esc_html_e('Edit Asset', 'pn-personal-finance-manager'); ?>
											</a>
										</div>
										<div class="pn-personal-finance-manager-performance-metrics pn-personal-finance-manager-stock-detail-metrics">
											<div class="pn-personal-finance-manager-metric-row">
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Current Value', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php
														$fmt_current = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['current_value'], $currency), $currency);
														echo esc_html($fmt_current['full']);
														?>
													</span>
												</div>
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Purchase Price', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php
														$fmt_purchase = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['purchase_value'], $currency), $currency);
														echo esc_html($fmt_purchase['full']);
														?>
													</span>
												</div>
											</div>
											<?php if (isset($asset['ownership_percent']) && $asset['ownership_percent'] < 100): ?>
											<div class="pn-personal-finance-manager-metric-row">
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Ownership', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value"><?php echo esc_html(number_format($asset['ownership_percent'], 2)); ?>%</span>
												</div>
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Your Share', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php echo esc_html($converted['full']); ?>
													</span>
												</div>
											</div>
											<?php endif; ?>
											<?php if ($asset['total_invested'] > 0): ?>
											<div class="pn-personal-finance-manager-metric-row">
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Invested', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php
														$fmt_invested = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['total_invested'], $currency), $currency);
														echo esc_html($fmt_invested['full']);
														?>
													</span>
												</div>
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Profit/Loss', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value <?php echo esc_attr($asset['profit_loss'] >= 0 ? 'positive' : 'negative'); ?>">
														<?php
														$converted_profit = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['profit_loss'], $currency), $currency);
														echo $asset['profit_loss'] >= 0 ? '+' : '';
														echo esc_html($converted_profit['full']);
														?>
														(<?php echo $asset['profit_loss_percent'] >= 0 ? '+' : ''; ?><?php echo esc_html(number_format($asset['profit_loss_percent'], 2)); ?>%)
													</span>
												</div>
											</div>
											<?php endif; ?>
											<?php if (!empty($asset['is_sold'])): ?>
											<div class="pn-personal-finance-manager-metric-row">
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Sale Date', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php echo !empty($asset['sold_date']) ? esc_html(date_i18n(get_option('date_format'), strtotime($asset['sold_date']))) : '—'; ?>
													</span>
												</div>
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Sale Price', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php
														$fmt_sold_re = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($asset['current_value'], $currency), $currency);
														echo esc_html($fmt_sold_re['full']);
														?>
													</span>
												</div>
											</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<?php if (!empty($re_chart_values)): ?>
						<div class="pn-personal-finance-manager-breakdown-charts-row" style="margin-top:30px;">
							<div class="pn-personal-finance-manager-breakdown-chart-item">
								<h4><?php esc_html_e('Real Estate Breakdown', 'pn-personal-finance-manager'); ?></h4>
								<div class="pn-personal-finance-manager-chart-container">
									<canvas id="pn-personal-finance-manager-realestate-breakdown-doughnut" width="300" height="300"></canvas>
								</div>
							</div>
							<?php
							$_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	function drawRealEstateBreakdownChart() {
		if (typeof Chart === 'undefined') return setTimeout(drawRealEstateBreakdownChart, 200);
		var ctx = document.getElementById('pn-personal-finance-manager-realestate-breakdown-doughnut').getContext('2d');
		var colors = __PNPFM_COLORS__;
		var dataValues = __PNPFM_VALUES__;
		var bgColors = dataValues.map(function(_, i) { return colors[i % colors.length]; });
		new Chart(ctx, {
			type: 'doughnut',
			data: {
				labels: __PNPFM_LABELS__,
				datasets: [{
					data: dataValues,
					backgroundColor: bgColors,
					borderWidth: 2,
					borderColor: '#fff'
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: { display: true, position: 'bottom' },
					tooltip: {
						callbacks: {
							label: function(context) {
								var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
								var percentage = ((context.parsed / total) * 100).toFixed(2);
								return context.label + ': __PNPFM_SYMBOL__' + context.parsed.toFixed(2) + ' (' + percentage + '%)';
							}
						}
					}
				}
			}
		});
	}
	drawRealEstateBreakdownChart();
});
PNPFM_JS;
							$_pnpfm_js = str_replace(
								['__PNPFM_COLORS__', '__PNPFM_VALUES__', '__PNPFM_LABELS__', '__PNPFM_SYMBOL__'],
								[wp_json_encode($re_chart_colors), wp_json_encode($re_chart_values), wp_json_encode($re_chart_labels), esc_js($breakdown_symbol)],
								$_pnpfm_js
							);
							wp_add_inline_script('pn-personal-finance-manager-chartjs', $_pnpfm_js, 'after');
							?>
						</div>
						<?php endif; ?>
					<?php else: ?>
						<div class="pn-personal-finance-manager-other-assets">
							<?php foreach ($type_data['assets'] as $asset): ?>
								<div class="pn-personal-finance-manager-other-asset-card pn-personal-finance-manager-asset" data-pn_personal_finance_manager_asset-id="<?php echo esc_attr($asset['id']); ?>" data-sort-name="<?php echo esc_attr($asset['title']); ?>" data-sort-value="<?php echo esc_attr($asset['current_value'] ?? 0); ?>" data-sort-date="<?php echo esc_attr($asset['purchase_date']); ?>">
									<div class="pn-personal-finance-manager-other-asset-header">
										<h4><?php echo esc_html($asset['title']); ?></h4>
										<a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none pn-personal-finance-manager-edit-asset-link" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_asset-edit" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_asset_edit" title="<?php esc_attr_e('Edit Asset', 'pn-personal-finance-manager'); ?>">
											<i class="material-icons-outlined">edit</i>
										</a>
									</div>
									<p><?php echo esc_html(wp_trim_words($asset['description'], 20)); ?></p>
									<div class="pn-personal-finance-manager-asset-meta">
										<span class="pn-personal-finance-manager-purchase-date">
											<?php esc_html_e('Purchased:', 'pn-personal-finance-manager'); ?>
											<?php echo esc_html($asset['purchase_date'] ? date_i18n(get_option('date_format'), strtotime($asset['purchase_date'])) : __('Unknown', 'pn-personal-finance-manager')); ?>
										</span>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					</div><!-- .pn-personal-finance-manager-section-body (category) -->
				</div>
			<?php endforeach; ?>
			</div><!-- .pn-personal-finance-manager-section-body (Assets) -->

			<div class="pn-personal-finance-manager-section-header">
				<h2 class="pn-personal-finance-manager-section-toggle collapsed"><?php esc_html_e('Liabilities', 'pn-personal-finance-manager'); ?> <i class="material-icons-outlined pn-personal-finance-manager-section-toggle-icon">expand_less</i></h2>
				<?php if (is_user_logged_in()) : ?>
				<a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_liability-add" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_liability_new">
					<i class="material-icons-outlined pn-personal-finance-manager-cursor-pointer pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-tooltip pn-personal-finance-manager-color-main-0" title="<?php esc_attr_e('Add new Liability', 'pn-personal-finance-manager'); ?>">add_circle</i>
				</a>
				<?php endif; ?>
			</div>
			<div class="pn-personal-finance-manager-section-body">

			<?php if (!empty($user_liabilities)): ?>
				<?php foreach ($user_liabilities as $type_key => $type_data): ?>
					<div class="pn-personal-finance-manager-asset-category">
						<h3 class="pn-personal-finance-manager-section-toggle collapsed"><?php echo esc_html($type_data['label']); ?>
							<span class="pn-personal-finance-manager-asset-count">(<?php echo esc_html($type_data['count']); ?>)</span>
							<i class="material-icons-outlined pn-personal-finance-manager-section-toggle-icon">expand_less</i>
						</h3>
						<div class="pn-personal-finance-manager-section-body">
						<div class="pn-personal-finance-manager-sort-bar">
							<select class="pn-personal-finance-manager-sort-select">
								<option value="name"><?php esc_html_e('Name', 'pn-personal-finance-manager'); ?></option>
								<option value="balance"><?php esc_html_e('Balance', 'pn-personal-finance-manager'); ?></option>
								<option value="date"><?php esc_html_e('Date', 'pn-personal-finance-manager'); ?></option>
							</select>
							<button type="button" class="pn-personal-finance-manager-sort-dir-btn" data-dir="asc" title="<?php esc_attr_e('Sort ascending', 'pn-personal-finance-manager'); ?>">
								<i class="material-icons-outlined">arrow_upward</i>
							</button>
						</div>
						<div class="pn-personal-finance-manager-stocks-grid">
							<?php foreach ($type_data['liabilities'] as $liability): ?>
								<?php
								$converted_balance = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(
									$liability['balance'],
									$currency
								);
								?>
								<div class="pn-personal-finance-manager-stock-card pn-personal-finance-manager-liability" data-pn_personal_finance_manager_liability-id="<?php echo esc_attr($liability['id']); ?>" data-sort-name="<?php echo esc_attr($liability['title']); ?>" data-sort-balance="<?php echo esc_attr($liability['balance']); ?>" data-sort-date="<?php echo esc_attr($liability['date']); ?>">
									<div class="pn-personal-finance-manager-stock-header pn-personal-finance-manager-stock-toggle">
										<h4><?php echo esc_html($liability['title']); ?></h4>
										<div>
											<i class="material-icons-outlined pn-personal-finance-manager-stock-toggle-icon">expand_more</i>
										</div>
									</div>
									<div class="pn-personal-finance-manager-stock-card-summary pn-personal-finance-manager-mb-10">
										<div class="pn-personal-finance-manager-stock-card-summary-value negative">
											<?php echo esc_html($converted_balance['full']); ?>
										</div>
									</div>
									<div class="pn-personal-finance-manager-stock-body" style="display:none;">
										<div class="pn-personal-finance-manager-stock-body-actions">
											<a href="#" class="pn-personal-finance-manager-popup-open-ajax pn-personal-finance-manager-text-decoration-none pn-personal-finance-manager-edit-asset-link" data-pn-personal-finance-manager-popup-id="pn-personal-finance-manager-popup-pn_personal_finance_manager_liability-edit" data-pn-personal-finance-manager-ajax-type="pn_personal_finance_manager_liability_edit" title="<?php esc_attr_e('Edit Liability', 'pn-personal-finance-manager'); ?>">
												<i class="material-icons-outlined">edit</i> <?php esc_html_e('Edit Liability', 'pn-personal-finance-manager'); ?>
											</a>
										</div>
										<div class="pn-personal-finance-manager-performance-metrics pn-personal-finance-manager-stock-detail-metrics">
											<div class="pn-personal-finance-manager-metric-row">
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Balance', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value negative">
														<?php echo esc_html($converted_balance['full']); ?>
													</span>
												</div>
												<?php if (!empty($liability['date'])): ?>
												<div class="pn-personal-finance-manager-metric-item">
													<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Date', 'pn-personal-finance-manager'); ?></span>
													<span class="pn-personal-finance-manager-metric-value">
														<?php echo esc_html(date_i18n(get_option('date_format'), strtotime($liability['date']))); ?>
													</span>
												</div>
												<?php endif; ?>
											</div>
										</div>
										<?php
										$amort_interest = floatval(get_post_meta($liability['id'], 'pn_personal_finance_manager_' . $type_key . '_interest_rate', true));
										$amort_payment = floatval(get_post_meta($liability['id'], 'pn_personal_finance_manager_' . $type_key . '_monthly_payment', true));
										if ($amort_payment <= 0 && $type_key === 'credit_card') {
											$amort_payment = floatval(get_post_meta($liability['id'], 'pn_personal_finance_manager_credit_card_minimum_payment', true));
										}
										if ($amort_interest > 0 && $amort_payment > 0): ?>
										<div style="margin-top:10px;text-align:center;">
											<button type="button" class="pn-personal-finance-manager-amortization-btn pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini"
												data-liability-id="<?php echo esc_attr($liability['id']); ?>">
												<i class="material-icons-outlined">calendar_month</i> <?php esc_html_e('Amortization Schedule', 'pn-personal-finance-manager'); ?>
											</button>
										</div>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						</div><!-- .pn-personal-finance-manager-section-body (liability category) -->
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			</div><!-- .pn-personal-finance-manager-section-body (Liabilities) -->
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Render an amortization table for a liability.
	 *
	 * @since  1.3.0
	 * @param  int    $liability_id  Post ID of the liability.
	 * @return string                HTML table.
	 */
	public function pn_personal_finance_manager_render_amortization_table($liability_id) {
		$liability_type = get_post_meta($liability_id, 'pn_personal_finance_manager_liability_type', true);

		if (empty($liability_type)) {
			return '<p>' . esc_html__('Invalid liability.', 'pn-personal-finance-manager') . '</p>';
		}

		// Get balance
		$balance_keys = [
			'mortgage'      => 'pn_personal_finance_manager_mortgage_remaining_balance',
			'car_loan'      => 'pn_personal_finance_manager_car_loan_remaining_balance',
			'student_loan'  => 'pn_personal_finance_manager_student_loan_remaining_balance',
			'credit_card'   => 'pn_personal_finance_manager_credit_card_balance',
			'personal_loan' => 'pn_personal_finance_manager_personal_loan_remaining_balance',
			'medical_debt'  => 'pn_personal_finance_manager_medical_debt_remaining_balance',
			'business_loan' => 'pn_personal_finance_manager_business_loan_remaining_balance',
			'tax_debt'      => 'pn_personal_finance_manager_tax_debt_remaining_balance',
			'other'         => 'pn_personal_finance_manager_other_liability_amount',
		];

		$balance_key = isset($balance_keys[$liability_type]) ? $balance_keys[$liability_type] : '';
		$balance = $balance_key ? floatval(get_post_meta($liability_id, $balance_key, true)) : 0;

		// Get interest rate and monthly payment
		$interest_rate = floatval(get_post_meta($liability_id, 'pn_personal_finance_manager_' . $liability_type . '_interest_rate', true));
		$monthly_payment_key = 'pn_personal_finance_manager_' . $liability_type . '_monthly_payment';
		$monthly_payment = floatval(get_post_meta($liability_id, $monthly_payment_key, true));

		// Fallback for credit card: minimum_payment
		if ($monthly_payment <= 0 && $liability_type === 'credit_card') {
			$monthly_payment = floatval(get_post_meta($liability_id, 'pn_personal_finance_manager_credit_card_minimum_payment', true));
		}

		if ($balance <= 0 || $interest_rate <= 0 || $monthly_payment <= 0) {
			return '<p>' . esc_html__('Insufficient data for amortization calculation. Interest rate and monthly payment are required.', 'pn-personal-finance-manager') . '</p>';
		}

		$currency = get_option('pn_personal_finance_manager_currency', 'eur');
		$monthly_rate = $interest_rate / 12 / 100;

		// Check if payment covers at least the interest
		$first_month_interest = $balance * $monthly_rate;
		if ($monthly_payment <= $first_month_interest) {
			return '<p>' . esc_html__('Monthly payment is too low to cover the interest. The debt will never be paid off.', 'pn-personal-finance-manager') . '</p>';
		}

		// Calculate amortization schedule
		$rows = [];
		$remaining = $balance;
		$total_interest = 0;
		$total_principal = 0;
		$max_months = 360; // 30 years cap

		for ($month = 1; $month <= $max_months && $remaining > 0.01; $month++) {
			$interest = $remaining * $monthly_rate;
			$payment = min($monthly_payment, $remaining + $interest);
			$principal = $payment - $interest;
			$remaining -= $principal;

			if ($remaining < 0) {
				$remaining = 0;
			}

			$total_interest += $interest;
			$total_principal += $principal;

			$rows[] = [
				'month'     => $month,
				'payment'   => $payment,
				'principal' => $principal,
				'interest'  => $interest,
				'remaining' => $remaining,
			];
		}

		// Render HTML
		$fmt = function($val) use ($currency) {
			$formatted = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($val, $currency);
			return $formatted['full'];
		};

		$total_paid = $total_principal + $total_interest;
		$months_count = count($rows);
		$years = floor($months_count / 12);
		$extra_months = $months_count % 12;
		$duration_text = '';
		if ($years > 0) {
			// translators: %d is the number of years.
		$duration_text .= sprintf(_n('%d year', '%d years', $years, 'pn-personal-finance-manager'), $years);
		}
		if ($extra_months > 0) {
			if ($years > 0) {
				$duration_text .= ', ';
			}
			// translators: %d is the number of months.
		$duration_text .= sprintf(_n('%d month', '%d months', $extra_months, 'pn-personal-finance-manager'), $extra_months);
		}

		ob_start();
		?>
		<div class="pn-personal-finance-manager-amortization-summary">
			<div class="pn-personal-finance-manager-amortization-summary-item">
				<span class="pn-personal-finance-manager-amortization-summary-label"><?php esc_html_e('Total Paid', 'pn-personal-finance-manager'); ?></span>
				<span class="pn-personal-finance-manager-amortization-summary-value"><?php echo esc_html($fmt($total_paid)); ?></span>
			</div>
			<div class="pn-personal-finance-manager-amortization-summary-item">
				<span class="pn-personal-finance-manager-amortization-summary-label"><?php esc_html_e('Total Interest', 'pn-personal-finance-manager'); ?></span>
				<span class="pn-personal-finance-manager-amortization-summary-value"><?php echo esc_html($fmt($total_interest)); ?></span>
			</div>
			<div class="pn-personal-finance-manager-amortization-summary-item">
				<span class="pn-personal-finance-manager-amortization-summary-label"><?php esc_html_e('Duration', 'pn-personal-finance-manager'); ?></span>
				<span class="pn-personal-finance-manager-amortization-summary-value"><?php echo esc_html($duration_text); ?></span>
			</div>
		</div>
		<div class="pn-personal-finance-manager-amortization-table-wrapper">
			<table class="pn-personal-finance-manager-amortization-table">
				<thead>
					<tr>
						<th>#</th>
						<th><?php esc_html_e('Payment', 'pn-personal-finance-manager'); ?></th>
						<th><?php esc_html_e('Principal', 'pn-personal-finance-manager'); ?></th>
						<th><?php esc_html_e('Interest', 'pn-personal-finance-manager'); ?></th>
						<th><?php esc_html_e('Remaining', 'pn-personal-finance-manager'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($rows as $row): ?>
					<tr>
						<td><?php echo esc_html($row['month']); ?></td>
						<td><?php echo esc_html($fmt($row['payment'])); ?></td>
						<td><?php echo esc_html($fmt($row['principal'])); ?></td>
						<td><?php echo esc_html($fmt($row['interest'])); ?></td>
						<td><?php echo esc_html($fmt($row['remaining'])); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Clean old stock price history data from global cache.
	 *
	 * @since    1.0.5
	 * @param    int       $days_to_keep    Number of days to keep (default 365).
	 * @return   int                        Number of cleaned records.
	 */
	public function pn_personal_finance_manager_clean_old_stock_price_data($days_to_keep = 365) {
		global $wpdb;
		
		$cutoff_date = gmdate('Y-m-d', strtotime("-{$days_to_keep} days"));
		$cleaned_count = 0;
		
		// Get all stock price history options from global cache
		$options_with_history = $wpdb->get_results(
			"SELECT option_name, option_value 
			FROM {$wpdb->options} 
			WHERE option_name LIKE 'pn_personal_finance_manager_stock_price_history_%'"
		);
		
		foreach ($options_with_history as $option) {
			$price_history = maybe_unserialize($option->option_value);
			
			if (is_array($price_history)) {
				$original_count = count($price_history);
				
				// Filter out old records
				$filtered_history = array_filter($price_history, function($record) use ($cutoff_date) {
					return $record['recorded_date'] >= $cutoff_date;
				});
				
				$new_count = count($filtered_history);
				$removed_count = $original_count - $new_count;
				
				if ($removed_count > 0) {
					// Update global cache with filtered data
					update_option($option->option_name, array_values($filtered_history), false);
					$cleaned_count += $removed_count;
				}
			}
		}
		
		return $cleaned_count;
	}

	/**
	 * Get all stock symbols from global cache.
	 *
	 * @since    1.0.5
	 * @return   array                 Array of stock symbols.
	 */
	public function pn_personal_finance_manager_get_all_stock_symbols() {
		global $wpdb;
		
		$symbols = [];
		$options = $wpdb->get_results(
			"SELECT option_name FROM {$wpdb->options} 
			WHERE option_name LIKE 'pn_personal_finance_manager_stock_price_history_%'"
		);
		
		foreach ($options as $option) {
			$symbol = str_replace('pn_personal_finance_manager_stock_price_history_', '', $option->option_name);
			$symbols[] = $symbol;
		}
		
		return $symbols;
	}

	/**
	 * Get stock symbols for a specific user from their assets.
	 *
	 * @since    1.0.5
	 * @param    int       $user_id    User ID.
	 * @return   array                 Array of stock symbols.
	 */
	public function pn_personal_finance_manager_get_user_stock_symbols($user_id) {
		// Get user's stock assets to find symbols
		$user_assets = get_posts([
			'post_type' => 'pnpfm_asset',
			'post_status' => 'publish',
			'numberposts' => -1,
			'author' => $user_id,
			'meta_query' => [
				[
					'key' => 'pn_personal_finance_manager_asset_type',
					'value' => 'stocks',
					'compare' => '='
				]
			]
		]);
		
		$symbols = [];
		foreach ($user_assets as $asset) {
			$symbol = get_post_meta($asset->ID, 'pn_personal_finance_manager_stock_symbol', true);
			if (!empty($symbol) && !in_array($symbol, $symbols)) {
				$symbols[] = $symbol;
			}
		}
		
		return $symbols;
	}

	/**
	 * Get total size of stock price history data in global cache.
	 *
	 * @since    1.0.5
	 * @return   int                   Total size in bytes.
	 */
	public function pn_personal_finance_manager_get_cache_size() {
		global $wpdb;
		
		$total_size = 0;
		$options = $wpdb->get_results(
			"SELECT option_value FROM {$wpdb->options} 
			WHERE option_name LIKE 'pn_personal_finance_manager_stock_price_history_%'"
		);
		
		foreach ($options as $option) {
			$total_size += strlen($option->option_value);
		}
		
		return $total_size;
	}

	/**
	 * Get cache statistics.
	 *
	 * @since    1.0.5
	 * @return   array                 Cache statistics.
	 */
	public function pn_personal_finance_manager_get_cache_stats() {
		global $wpdb;
		
		$symbols = $this->pn_personal_finance_manager_get_all_stock_symbols();
		$cache_size = $this->pn_personal_finance_manager_get_cache_size();
		
		$total_records = 0;
		foreach ($symbols as $symbol) {
			$option_key = 'pn_personal_finance_manager_stock_price_history_' . strtoupper($symbol);
			$price_history = get_option($option_key, []);
			$total_records += count($price_history);
		}
		
		return [
			'symbols_count' => count($symbols),
			'total_records' => $total_records,
			'cache_size_bytes' => $cache_size,
			'cache_size_mb' => round($cache_size / 1024 / 1024, 2),
			'symbols' => $symbols
		];
	}

	/**
	 * Get stock symbols cache statistics.
	 *
	 * @since    1.0.5
	 * @return   array                 Stock symbols cache statistics.
	 */
	public function pn_personal_finance_manager_get_stock_symbols_cache_stats() {
		$cached_symbols = get_option('pn_personal_finance_manager_stock_symbols_cache', []);
		$last_update = get_option('pn_personal_finance_manager_stock_symbols_last_update', 0);
		$cache_expiry = get_option('pn_personal_finance_manager_stock_symbols_cache_expiry', 24 * HOUR_IN_SECONDS);
		
		$is_valid = !empty($cached_symbols) && is_array($cached_symbols) && 
					($last_update + $cache_expiry) > current_time('timestamp');
		
		return [
			'symbols_count' => count($cached_symbols),
			'last_update' => $last_update,
			'last_update_formatted' => $last_update ? date_i18n(get_option('date_format') . ' @ ' . get_option('time_format'), $last_update) : __('Never', 'pn-personal-finance-manager'),
			'cache_expiry' => $cache_expiry,
			'is_valid' => $is_valid,
			'next_expiry' => $last_update + $cache_expiry,
			'next_expiry_formatted' => $last_update ? date_i18n(get_option('date_format') . ' @ ' . get_option('time_format'), $last_update + $cache_expiry) : __('N/A', 'pn-personal-finance-manager'),
			'cache_size_bytes' => strlen(serialize($cached_symbols)),
			'cache_size_mb' => round(strlen(serialize($cached_symbols)) / 1024 / 1024, 2)
		];
	}

	/**
	 * Clear stock symbols cache.
	 *
	 * @since    1.0.5
	 * @return   bool                  Success status.
	 */
	public function pn_personal_finance_manager_clear_stock_symbols_cache() {
		$result1 = delete_option('pn_personal_finance_manager_stock_symbols_cache');
		$result2 = delete_option('pn_personal_finance_manager_stock_symbols_last_update');
		$result3 = delete_option('pn_personal_finance_manager_stock_symbols_cache_expiry');
		
		error_log('PnPersonalFinanceManager: Stock symbols cache cleared.');
		
		return $result1 && $result2 && $result3;
	}

	/**
	 * Force update stock symbols cache.
	 *
	 * @since    1.0.5
	 * @return   array|false           Updated symbols or false on failure.
	 */
	public function pn_personal_finance_manager_force_update_stock_symbols_cache() {
		// Clear existing cache first
		$this->pn_personal_finance_manager_clear_stock_symbols_cache();
		
		// Force update
		$result = $this->pn_personal_finance_manager_update_stock_symbols_cache();
		
		if ($result && is_array($result)) {
			error_log('PnPersonalFinanceManager: Stock symbols cache force updated successfully. Total symbols: ' . count($result));
		} else {
			error_log('PnPersonalFinanceManager: Failed to force update stock symbols cache.');
		}
		
		return $result;
	}

	/**
	 * Get stock symbols cache info for admin display.
	 *
	 * @since    1.0.5
	 * @return   string                HTML formatted cache info.
	 */
	public function pn_personal_finance_manager_get_stock_symbols_cache_info() {
		$stats = $this->pn_personal_finance_manager_get_stock_symbols_cache_stats();

		ob_start();
		?>
		<div class="pn-personal-finance-manager-cache-info">
			<h3><?php esc_html_e('Stock Symbols Cache Information', 'pn-personal-finance-manager'); ?></h3>
			<table class="widefat">
				<tbody>
					<tr>
						<td><strong><?php esc_html_e('Symbols in Cache:', 'pn-personal-finance-manager'); ?></strong></td>
						<td><?php echo esc_html(number_format($stats['symbols_count'])); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e('Last Update:', 'pn-personal-finance-manager'); ?></strong></td>
						<td><?php echo esc_html($stats['last_update_formatted']); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e('Cache Status:', 'pn-personal-finance-manager'); ?></strong></td>
						<td>
							<span class="<?php echo esc_attr($stats['is_valid'] ? 'pn-personal-finance-manager-status-valid' : 'pn-personal-finance-manager-status-expired'); ?>">
								<?php echo esc_html($stats['is_valid'] ? __('Valid', 'pn-personal-finance-manager') : __('Expired', 'pn-personal-finance-manager')); ?>
							</span>
						</td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e('Next Expiry:', 'pn-personal-finance-manager'); ?></strong></td>
						<td><?php echo esc_html($stats['next_expiry_formatted']); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e('Cache Size:', 'pn-personal-finance-manager'); ?></strong></td>
						<td><?php echo esc_html($stats['cache_size_mb']); ?> MB</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
		return ob_get_clean();
	}

	// =========================================================================
	// CRYPTOCURRENCY METHODS (CoinGecko API)
	// =========================================================================

	/**
	 * Get crypto symbols for form dropdown.
	 *
	 * @since    1.1.0
	 * @return   array    Array of crypto symbols with CoinGecko ID as key and display name as value.
	 */
	public function pn_personal_finance_manager_get_crypto_symbols_for_form() {
		$cached_symbols = get_option('pn_personal_finance_manager_crypto_symbols_cache', []);
		$last_update = get_option('pn_personal_finance_manager_crypto_symbols_last_update', 0);
		$cache_expiry = 24 * HOUR_IN_SECONDS;

		if (!empty($cached_symbols) && is_array($cached_symbols) &&
			($last_update + $cache_expiry) > current_time('timestamp')) {
			return $cached_symbols;
		}

		$update_result = $this->pn_personal_finance_manager_update_crypto_symbols_cache();

		if ($update_result && !empty($update_result)) {
			return $update_result;
		}

		if (!empty($cached_symbols) && is_array($cached_symbols)) {
			return $cached_symbols;
		}

		return $this->pn_personal_finance_manager_get_popular_crypto_symbols();
	}

	/**
	 * Update crypto symbols cache from CoinGecko API.
	 *
	 * @since    1.1.0
	 * @return   array|false    Array of crypto symbols or false on failure.
	 */
	public function pn_personal_finance_manager_update_crypto_symbols_cache() {
		$symbols = $this->pn_personal_finance_manager_get_coingecko_coins_list();

		if (!empty($symbols) && is_array($symbols)) {
			update_option('pn_personal_finance_manager_crypto_symbols_cache', $symbols, false);
			update_option('pn_personal_finance_manager_crypto_symbols_last_update', current_time('timestamp'), false);
			return $symbols;
		}

		return false;
	}

	/**
	 * Get coins list from CoinGecko API sorted by market cap.
	 * Also stores id→ticker mapping for display purposes.
	 *
	 * @since    1.1.0
	 * @return   array    Array of crypto symbols ['bitcoin' => 'BTC - Bitcoin', ...].
	 */
	private function pn_personal_finance_manager_get_coingecko_coins_list() {
		$url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=250&page=1&sparkline=false';

		$response = wp_remote_get($url, [
			'timeout' => 30,
			'user-agent' => 'PnPersonalFinanceManager/1.1'
		]);

		if (is_wp_error($response)) {
			error_log('PnPersonalFinanceManager: CoinGecko coins/markets API error: ' . $response->get_error_message());
			return $this->pn_personal_finance_manager_get_popular_crypto_symbols();
		}

		$http_code = wp_remote_retrieve_response_code($response);
		if ($http_code !== 200) {
			error_log('PnPersonalFinanceManager: CoinGecko API returned HTTP ' . $http_code);
			return $this->pn_personal_finance_manager_get_popular_crypto_symbols();
		}

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if (empty($data) || !is_array($data)) {
			error_log('PnPersonalFinanceManager: CoinGecko API returned empty or invalid data.');
			return $this->pn_personal_finance_manager_get_popular_crypto_symbols();
		}

		$symbols = [];
		$id_to_ticker = [];

		foreach ($data as $coin) {
			if (!empty($coin['id']) && !empty($coin['symbol']) && !empty($coin['name'])) {
				$ticker = strtoupper($coin['symbol']);
				$symbols[$coin['id']] = $ticker . ' - ' . $coin['name'];
				$id_to_ticker[$coin['id']] = $coin['symbol'];
			}
		}

		if (empty($symbols)) {
			return $this->pn_personal_finance_manager_get_popular_crypto_symbols();
		}

		// Store id→ticker mapping
		update_option('pn_personal_finance_manager_crypto_id_to_ticker', $id_to_ticker, false);

		return $symbols;
	}

	/**
	 * Get popular crypto symbols as fallback.
	 *
	 * @since    1.1.0
	 * @return   array    Array of popular crypto symbols.
	 */
	private function pn_personal_finance_manager_get_popular_crypto_symbols() {
		$symbols = [
			'bitcoin' => 'BTC - Bitcoin',
			'ethereum' => 'ETH - Ethereum',
			'tether' => 'USDT - Tether',
			'binancecoin' => 'BNB - BNB',
			'solana' => 'SOL - Solana',
			'ripple' => 'XRP - XRP',
			'usd-coin' => 'USDC - USD Coin',
			'cardano' => 'ADA - Cardano',
			'dogecoin' => 'DOGE - Dogecoin',
			'polkadot' => 'DOT - Polkadot',
			'avalanche-2' => 'AVAX - Avalanche',
			'chainlink' => 'LINK - Chainlink',
			'tron' => 'TRX - TRON',
			'matic-network' => 'MATIC - Polygon',
			'shiba-inu' => 'SHIB - Shiba Inu',
			'litecoin' => 'LTC - Litecoin',
			'bitcoin-cash' => 'BCH - Bitcoin Cash',
			'uniswap' => 'UNI - Uniswap',
			'stellar' => 'XLM - Stellar',
			'cosmos' => 'ATOM - Cosmos',
		];

		// Also set fallback id→ticker mapping
		$id_to_ticker = [];
		foreach ($symbols as $id => $display) {
			$parts = explode(' - ', $display);
			$id_to_ticker[$id] = strtolower($parts[0]);
		}
		update_option('pn_personal_finance_manager_crypto_id_to_ticker', $id_to_ticker, false);

		return $symbols;
	}

	/**
	 * Get crypto data for a given CoinGecko coin ID.
	 *
	 * @since    1.1.0
	 * @param    string    $coin_id    The CoinGecko coin ID (e.g. 'bitcoin').
	 * @return   array|false           Crypto data or false on failure.
	 */
	public function pn_personal_finance_manager_get_crypto_data($coin_id) {
		if (empty($coin_id)) {
			return false;
		}

		$cache_key = 'pn_personal_finance_manager_crypto_data_' . $coin_id;
		$cached_data = get_transient($cache_key);
		if ($cached_data !== false) {
			return $cached_data;
		}

		$data = $this->pn_personal_finance_manager_get_coingecko_price($coin_id);

		if ($data) {
			$cache_duration = get_option('pn_personal_finance_manager_stocks_cache_duration', 3600);
			set_transient($cache_key, $data, (int)$cache_duration);
		}

		return $data;
	}

	/**
	 * Get crypto price from CoinGecko API.
	 *
	 * @since    1.1.0
	 * @param    string    $coin_id    The CoinGecko coin ID.
	 * @return   array|false           Crypto data or false on failure.
	 */
	private function pn_personal_finance_manager_get_coingecko_price($coin_id) {
		$url = 'https://api.coingecko.com/api/v3/simple/price?ids=' . urlencode($coin_id) . '&vs_currencies=usd&include_24hr_change=true&include_24hr_vol=true';

		$response = wp_remote_get($url, [
			'timeout' => 15,
			'user-agent' => 'PnPersonalFinanceManager/1.1'
		]);

		if (is_wp_error($response)) {
			error_log('PnPersonalFinanceManager: CoinGecko simple/price error: ' . $response->get_error_message());
			return false;
		}

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if (empty($data) || !isset($data[$coin_id])) {
			error_log('PnPersonalFinanceManager: CoinGecko simple/price returned no data for ' . $coin_id);
			return false;
		}

		$coin_data = $data[$coin_id];
		$price = isset($coin_data['usd']) ? floatval($coin_data['usd']) : 0;
		$change_24h = isset($coin_data['usd_24h_change']) ? floatval($coin_data['usd_24h_change']) : 0;
		$volume = isset($coin_data['usd_24h_vol']) ? floatval($coin_data['usd_24h_vol']) : 0;

		// Resolve ticker from mapping
		$id_to_ticker = get_option('pn_personal_finance_manager_crypto_id_to_ticker', []);
		$ticker = isset($id_to_ticker[$coin_id]) ? strtoupper($id_to_ticker[$coin_id]) : strtoupper($coin_id);

		return [
			'symbol' => $ticker,
			'price' => $price,
			'change' => round($price * ($change_24h / 100), 2),
			'change_percent' => number_format($change_24h, 2) . '%',
			'volume' => $volume,
			'high' => $price,
			'low' => $price,
			'open' => $price,
			'previous_close' => $price - round($price * ($change_24h / 100), 2),
		];
	}

	/**
	 * Get historical crypto data and store in cache.
	 *
	 * @since    1.1.0
	 * @param    string    $coin_id    The CoinGecko coin ID.
	 * @param    int       $days       Number of days to retrieve.
	 * @return   array|false           Historical data or false on failure.
	 */
	public function pn_personal_finance_manager_get_historical_crypto_data($coin_id, $days = 30) {
		if (empty($coin_id)) {
			return false;
		}

		$existing_data = $this->pn_personal_finance_manager_get_crypto_price_history($coin_id, $days);
		$last_update = get_option('pn_personal_finance_manager_historical_crypto_data_last_update_' . $coin_id, '');

		if (!empty($existing_data) && !empty($last_update)) {
			$last_update_timestamp = strtotime($last_update);
			$current_timestamp = current_time('timestamp');
			$hours_since_update = ($current_timestamp - $last_update_timestamp) / 3600;

			if ($hours_since_update < 24) {
				return $existing_data;
			}
		}

		$historical_data = $this->pn_personal_finance_manager_get_coingecko_historical($coin_id, $days);

		if ($historical_data) {
			$this->pn_personal_finance_manager_store_crypto_historical_data($coin_id, $historical_data);
			update_option('pn_personal_finance_manager_historical_crypto_data_last_update_' . $coin_id, current_time('mysql'), false);
			return $historical_data;
		}

		return $existing_data ?: false;
	}

	/**
	 * Get historical data from CoinGecko API.
	 *
	 * @since    1.1.0
	 * @param    string    $coin_id    The CoinGecko coin ID.
	 * @param    int       $days       Number of days to retrieve.
	 * @return   array|false           Historical data or false on failure.
	 */
	private function pn_personal_finance_manager_get_coingecko_historical($coin_id, $days) {
		$url = 'https://api.coingecko.com/api/v3/coins/' . urlencode($coin_id) . '/market_chart?vs_currency=usd&days=' . intval($days);

		$response = wp_remote_get($url, [
			'timeout' => 30,
			'user-agent' => 'PnPersonalFinanceManager/1.1'
		]);

		if (is_wp_error($response)) {
			error_log('PnPersonalFinanceManager: CoinGecko market_chart error: ' . $response->get_error_message());
			return false;
		}

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if (empty($data['prices']) || !is_array($data['prices'])) {
			error_log('PnPersonalFinanceManager: CoinGecko market_chart returned empty data for ' . $coin_id);
			return false;
		}

		$id_to_ticker = get_option('pn_personal_finance_manager_crypto_id_to_ticker', []);
		$ticker = isset($id_to_ticker[$coin_id]) ? strtoupper($id_to_ticker[$coin_id]) : strtoupper($coin_id);

		$historical_data = [];
		$prev_price = 0;

		// CoinGecko returns [timestamp_ms, price] pairs
		// Group by date to get one entry per day
		$daily_prices = [];
		foreach ($data['prices'] as $point) {
			$date = gmdate('Y-m-d', intval($point[0] / 1000));
			$daily_prices[$date] = floatval($point[1]);
		}

		foreach ($daily_prices as $date => $price) {
			$change = $prev_price > 0 ? $price - $prev_price : 0;
			$change_pct = $prev_price > 0 ? round(($change / $prev_price) * 100, 2) . '%' : '0%';

			$historical_data[] = [
				'symbol' => $ticker,
				'price' => $price,
				'volume' => 0,
				'high' => $price,
				'low' => $price,
				'open_price' => $price,
				'previous_close' => $prev_price > 0 ? $prev_price : $price,
				'change_amount' => $change,
				'change_percent' => $change_pct,
				'recorded_date' => $date,
				'recorded_time' => '00:00:00',
				'created_at' => current_time('mysql')
			];

			$prev_price = $price;
		}

		return $historical_data;
	}

	/**
	 * Store historical crypto data in wp_options cache.
	 *
	 * @since    1.1.0
	 * @param    string    $coin_id          CoinGecko coin ID.
	 * @param    array     $historical_data  Historical price data.
	 * @return   bool                        Success status.
	 */
	private function pn_personal_finance_manager_store_crypto_historical_data($coin_id, $historical_data) {
		if (empty($historical_data)) {
			return false;
		}

		$option_key = 'pn_personal_finance_manager_crypto_price_history_' . $coin_id;

		$existing_data = get_option($option_key, []);
		if (!is_array($existing_data)) {
			$existing_data = [];
		}

		foreach ($historical_data as $new_record) {
			$date_exists = false;
			foreach ($existing_data as $index => $existing_record) {
				if ($existing_record['recorded_date'] === $new_record['recorded_date']) {
					$existing_data[$index] = $new_record;
					$date_exists = true;
					break;
				}
			}
			if (!$date_exists) {
				$existing_data[] = $new_record;
			}
		}

		usort($existing_data, function($a, $b) {
			return strtotime($a['recorded_date']) - strtotime($b['recorded_date']);
		});

		$cutoff_date = gmdate('Y-m-d', strtotime('-365 days'));
		$existing_data = array_filter($existing_data, function($record) use ($cutoff_date) {
			return $record['recorded_date'] >= $cutoff_date;
		});

		return update_option($option_key, array_values($existing_data), false);
	}

	/**
	 * Record crypto price to cache (daily).
	 *
	 * @since    1.1.0
	 * @param    string    $coin_id    The CoinGecko coin ID.
	 * @param    array     $data       Crypto data array.
	 * @return   bool                  Success status.
	 */
	public function pn_personal_finance_manager_record_crypto_price($coin_id, $data) {
		$option_key = 'pn_personal_finance_manager_crypto_price_history_' . $coin_id;
		$today = current_time('Y-m-d');

		$price_history = get_option($option_key, []);
		if (!is_array($price_history)) {
			$price_history = [];
		}

		$existing_index = null;
		foreach ($price_history as $index => $record) {
			if ($record['recorded_date'] === $today) {
				$existing_index = $index;
				break;
			}
		}

		$price_record = [
			'symbol' => $data['symbol'],
			'price' => floatval($data['price']),
			'volume' => isset($data['volume']) ? floatval($data['volume']) : 0,
			'high' => isset($data['high']) ? floatval($data['high']) : floatval($data['price']),
			'low' => isset($data['low']) ? floatval($data['low']) : floatval($data['price']),
			'open_price' => isset($data['open']) ? floatval($data['open']) : floatval($data['price']),
			'previous_close' => isset($data['previous_close']) ? floatval($data['previous_close']) : 0,
			'change_amount' => isset($data['change']) ? floatval($data['change']) : 0,
			'change_percent' => isset($data['change_percent']) ? $data['change_percent'] : '0%',
			'recorded_date' => $today,
			'recorded_time' => current_time('H:i:s'),
			'created_at' => current_time('mysql')
		];

		if ($existing_index !== null) {
			$price_history[$existing_index] = $price_record;
		} else {
			$price_history[] = $price_record;
		}

		$cutoff_date = gmdate('Y-m-d', strtotime('-365 days'));
		$price_history = array_filter($price_history, function($record) use ($cutoff_date) {
			return $record['recorded_date'] >= $cutoff_date;
		});

		usort($price_history, function($a, $b) {
			return strtotime($a['recorded_date']) - strtotime($b['recorded_date']);
		});

		return update_option($option_key, array_values($price_history), false);
	}

	/**
	 * Get historical crypto prices for a coin from cache.
	 *
	 * @since    1.1.0
	 * @param    string    $coin_id    The CoinGecko coin ID.
	 * @param    int       $days       Number of days to retrieve.
	 * @return   array                 Historical price data.
	 */
	public function pn_personal_finance_manager_get_crypto_price_history($coin_id, $days = 30) {
		$option_key = 'pn_personal_finance_manager_crypto_price_history_' . $coin_id;
		$price_history = get_option($option_key, []);

		if (!is_array($price_history)) {
			return [];
		}

		$cutoff_date = gmdate('Y-m-d', strtotime("-{$days} days"));
		$filtered_history = array_filter($price_history, function($record) use ($cutoff_date) {
			return $record['recorded_date'] >= $cutoff_date;
		});

		usort($filtered_history, function($a, $b) {
			return strtotime($a['recorded_date']) - strtotime($b['recorded_date']);
		});

		return array_values($filtered_history);
	}

	/**
	 * Get crypto price for a specific date from cache.
	 *
	 * @since    1.1.0
	 * @param    string    $coin_id    The CoinGecko coin ID.
	 * @param    string    $date       Date in Y-m-d format.
	 * @return   array|false           Price data or false if not found.
	 */
	public function pn_personal_finance_manager_get_crypto_price_for_date($coin_id, $date) {
		$option_key = 'pn_personal_finance_manager_crypto_price_history_' . $coin_id;
		$price_history = get_option($option_key, []);

		if (!is_array($price_history)) {
			return false;
		}

		// Exact match first
		foreach ($price_history as $record) {
			if ($record['recorded_date'] === $date) {
				return $record;
			}
		}

		// Find closest previous date
		$closest_record = null;
		$min_diff = null;
		$target_timestamp = strtotime($date);

		foreach ($price_history as $record) {
			if (empty($record['recorded_date'])) continue;
			$record_timestamp = strtotime($record['recorded_date']);
			$diff = abs($target_timestamp - $record_timestamp);

			if ($record_timestamp <= $target_timestamp) {
				if ($min_diff === null || $diff < $min_diff) {
					$min_diff = $diff;
					$closest_record = $record;
				}
			}
		}

		// If no previous date found, try closest future date
		if ($closest_record === null) {
			foreach ($price_history as $record) {
				if (empty($record['recorded_date'])) continue;
				$record_timestamp = strtotime($record['recorded_date']);
				$diff = abs($target_timestamp - $record_timestamp);

				if ($min_diff === null || $diff < $min_diff) {
					$min_diff = $diff;
					$closest_record = $record;
				}
			}
		}

		return $closest_record;
	}

	/**
	 * Cron callback: update crypto symbols cache.
	 *
	 * @since    1.1.0
	 */
	public function pn_personal_finance_manager_update_crypto_symbols_from_api_cron() {
		$result = $this->pn_personal_finance_manager_update_crypto_symbols_cache();
		if ($result && is_array($result)) {
			error_log('PnPersonalFinanceManager: Crypto symbols updated via cron. Total: ' . count($result));
		} else {
			error_log('PnPersonalFinanceManager: Failed to update crypto symbols via cron.');
		}
	}

	/**
	 * Cron callback: daily crypto price recording for all user crypto assets.
	 *
	 * @since    1.1.0
	 */
	public function pn_personal_finance_manager_daily_crypto_price_recording() {
		// Get all crypto assets (exclude sold)
		$crypto_assets = get_posts([
			'post_type' => 'pnpfm_asset',
			'post_status' => 'publish',
			'numberposts' => -1,
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => 'pn_personal_finance_manager_asset_type',
					'value' => 'cryptocurrencies',
					'compare' => '='
				],
				[
					'relation' => 'OR',
					[
						'key' => 'pn_personal_finance_manager_asset_sold',
						'compare' => 'NOT EXISTS'
					],
					[
						'key' => 'pn_personal_finance_manager_asset_sold',
						'value' => 'on',
						'compare' => '!='
					]
				]
			]
		]);

		$unique_coins = [];

		foreach ($crypto_assets as $asset) {
			$coin_id = get_post_meta($asset->ID, 'pn_personal_finance_manager_crypto_symbol', true);
			if (!empty($coin_id) && !in_array($coin_id, $unique_coins)) {
				$unique_coins[] = $coin_id;
			}
		}

		// Include watchlist crypto symbols from all users
		$watchlist = new PN_PERSONAL_FINANCE_MANAGER_Watchlist();
		$all_users = get_users(['fields' => 'ID']);
		foreach ($all_users as $uid) {
			$wl_items = $watchlist->pn_personal_finance_manager_watchlist_get_user_items($uid);
			foreach ($wl_items as $wl_item) {
				if ($wl_item['type'] === 'crypto' && !empty($wl_item['symbol']) && !in_array($wl_item['symbol'], $unique_coins)) {
					$unique_coins[] = $wl_item['symbol'];
				}
			}
		}

		$recorded_count = 0;
		foreach ($unique_coins as $coin_id) {
			$crypto_data = $this->pn_personal_finance_manager_get_crypto_data($coin_id);

			if ($crypto_data) {
				$result = $this->pn_personal_finance_manager_record_crypto_price($coin_id, $crypto_data);
				if ($result) {
					$recorded_count++;
				}
			}

			// Rate limiting: CoinGecko free tier allows ~10-30 calls/min
			sleep(3);
		}

		error_log('PnPersonalFinanceManager: Daily crypto price recording completed. Recorded: ' . $recorded_count . '/' . count($unique_coins));
	}
}