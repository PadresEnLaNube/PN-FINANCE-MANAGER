<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin so that it is ready for translation.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Data {
	/**
	 * The main data array.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PN_PERSONAL_FINANCE_MANAGER_Data    $data    Empty array.
	 */
	protected $data = [];

	/**
	 * Load the plugin most usefull data.
	 *
	 * @since    1.0.0
	 */
	public function pn_personal_finance_manager_load_plugin_data() {
		$this->data['user_id'] = get_current_user_id();

		if (is_admin()) {
			$this->data['post_id'] = !empty($GLOBALS['_REQUEST']['post']) ? $GLOBALS['_REQUEST']['post'] : 0;
		} else {
			$this->data['post_id'] = get_the_ID();
		}

		$GLOBALS['pn_personal_finance_manager_data'] = $this->data;
	}

	/**
	 * Flush wp rewrite rules.
	 *
	 * @since    1.0.0
	 */
	public function pn_personal_finance_manager_flush_rewrite_rules() {
    if (get_option('pn_personal_finance_manager_options_changed')) {
      flush_rewrite_rules();
      update_option('pn_personal_finance_manager_options_changed', false);
    }
  }

  /**
	 * Gets the mini loader.
	 *
	 * @since    1.0.0
	 */
	public static function pn_personal_finance_manager_loader($display = false) {
		?>
			<div class="pn-personal-finance-manager-waiting <?php echo ($display) ? 'pn-personal-finance-manager-display-block' : 'pn-personal-finance-manager-display-none'; ?>">
				<div class="pn-personal-finance-manager-loader-circle-waiting"><div></div><div></div><div></div><div></div></div>
			</div>
		<?php
  }

  /**
	 * Load popup loader.
	 *
	 * @since    1.0.0
	 */
	public static function pn_personal_finance_manager_popup_loader() {
		?>
			<div class="pn-personal-finance-manager-popup-content">
				<div class="pn-personal-finance-manager-loader-circle-wrapper"><div class="pn-personal-finance-manager-text-align-center"><div class="pn-personal-finance-manager-loader-circle"><div></div><div></div><div></div><div></div></div></div></div>
			</div>
		<?php
	}

  /**
	 * Gets the asset types options.
	 *
	 * @since    1.0.0
	 * @return   array    Array of asset types with their keys and labels.
	 */
	public static function pn_personal_finance_manager_get_asset_types() {
		return [
			'stocks' => esc_html(__('Stocks', 'pn-personal-finance-manager')),
			'real_estate' => esc_html(__('Real Estate', 'pn-personal-finance-manager')),
			'intellectual_property' => esc_html(__('Intellectual Property', 'pn-personal-finance-manager')),
			'bonds' => esc_html(__('Bonds', 'pn-personal-finance-manager')),
			'commodities' => esc_html(__('Commodities', 'pn-personal-finance-manager')),
			'cryptocurrencies' => esc_html(__('Cryptocurrencies', 'pn-personal-finance-manager')),
			'precious_metals' => esc_html(__('Precious Metals', 'pn-personal-finance-manager')),
			'art_collectibles' => esc_html(__('Art & Collectibles', 'pn-personal-finance-manager')),
			'vehicles' => esc_html(__('Vehicles', 'pn-personal-finance-manager')),
			'business_equity' => esc_html(__('Business Equity', 'pn-personal-finance-manager')),
			'retirement_accounts' => esc_html(__('Retirement Accounts', 'pn-personal-finance-manager')),
			'insurance_policies' => esc_html(__('Insurance Policies', 'pn-personal-finance-manager')),
			'other' => esc_html(__('Other', 'pn-personal-finance-manager')),
		];
	}

	/**
	 * Gets the liability types options.
	 *
	 * @since    1.0.8
	 * @return   array    Array of liability types with their keys and labels.
	 */
	public static function pn_personal_finance_manager_get_liability_types() {
		return [
			'mortgage' => esc_html(__('Mortgage', 'pn-personal-finance-manager')),
			'car_loan' => esc_html(__('Car Loan', 'pn-personal-finance-manager')),
			'student_loan' => esc_html(__('Student Loan', 'pn-personal-finance-manager')),
			'credit_card' => esc_html(__('Credit Card Debt', 'pn-personal-finance-manager')),
			'personal_loan' => esc_html(__('Personal Loan', 'pn-personal-finance-manager')),
			'medical_debt' => esc_html(__('Medical Debt', 'pn-personal-finance-manager')),
			'business_loan' => esc_html(__('Business Loan', 'pn-personal-finance-manager')),
			'tax_debt' => esc_html(__('Tax Obligations', 'pn-personal-finance-manager')),
			'other' => esc_html(__('Other', 'pn-personal-finance-manager')),
		];
	}

	/**
	 * Gets the available currencies options.
	 *
	 * @since    1.0.5
	 * @return   array    Array of currencies with their codes and labels.
	 */
	public static function pn_personal_finance_manager_get_currencies() {
		return [
			'eur' => esc_html(__('Euro (€)', 'pn-personal-finance-manager')),
			'usd' => esc_html(__('US Dollar ($)', 'pn-personal-finance-manager')),
			'gbp' => esc_html(__('British Pound (£)', 'pn-personal-finance-manager')),
			'jpy' => esc_html(__('Japanese Yen (¥)', 'pn-personal-finance-manager')),
			'chf' => esc_html(__('Swiss Franc (CHF)', 'pn-personal-finance-manager')),
			'cad' => esc_html(__('Canadian Dollar (C$)', 'pn-personal-finance-manager')),
			'aud' => esc_html(__('Australian Dollar (A$)', 'pn-personal-finance-manager')),
			'cny' => esc_html(__('Chinese Yuan (¥)', 'pn-personal-finance-manager')),
			'inr' => esc_html(__('Indian Rupee (₹)', 'pn-personal-finance-manager')),
			'brl' => esc_html(__('Brazilian Real (R$)', 'pn-personal-finance-manager')),
			'rub' => esc_html(__('Russian Ruble (₽)', 'pn-personal-finance-manager')),
			'krw' => esc_html(__('South Korean Won (₩)', 'pn-personal-finance-manager')),
			'mxn' => esc_html(__('Mexican Peso ($)', 'pn-personal-finance-manager')),
			'sgd' => esc_html(__('Singapore Dollar (S$)', 'pn-personal-finance-manager')),
			'hkd' => esc_html(__('Hong Kong Dollar (HK$)', 'pn-personal-finance-manager')),
			'nok' => esc_html(__('Norwegian Krone (kr)', 'pn-personal-finance-manager')),
			'sek' => esc_html(__('Swedish Krona (kr)', 'pn-personal-finance-manager')),
			'dkk' => esc_html(__('Danish Krone (kr)', 'pn-personal-finance-manager')),
			'pln' => esc_html(__('Polish Złoty (zł)', 'pn-personal-finance-manager')),
			'czk' => esc_html(__('Czech Koruna (Kč)', 'pn-personal-finance-manager')),
			'huf' => esc_html(__('Hungarian Forint (Ft)', 'pn-personal-finance-manager')),
			'try' => esc_html(__('Turkish Lira (₺)', 'pn-personal-finance-manager')),
			'zar' => esc_html(__('South African Rand (R)', 'pn-personal-finance-manager')),
			'thb' => esc_html(__('Thai Baht (฿)', 'pn-personal-finance-manager')),
			'myr' => esc_html(__('Malaysian Ringgit (RM)', 'pn-personal-finance-manager')),
			'idr' => esc_html(__('Indonesian Rupiah (Rp)', 'pn-personal-finance-manager')),
			'php' => esc_html(__('Philippine Peso (₱)', 'pn-personal-finance-manager')),
			'vnd' => esc_html(__('Vietnamese Dong (₫)', 'pn-personal-finance-manager')),
		];
	}

	/**
	 * Gets the currency symbol for a given currency code.
	 *
	 * @since    1.0.5
	 * @param    string    $currency_code    The currency code (e.g., 'EUR', 'USD').
	 * @return   string    The currency symbol.
	 */
	public static function pn_personal_finance_manager_get_currency_symbol($currency_code) {
		$currency_symbols = [
			'eur' => '€',
			'usd' => '$',
			'gbp' => '£',
			'jpy' => '¥',
			'chf' => 'CHF',
			'cad' => 'C$',
			'aud' => 'A$',
			'cny' => '¥',
			'inr' => '₹',
			'brl' => 'R$',
			'rub' => '₽',
			'krw' => '₩',
			'mxn' => '$',
			'sgd' => 'S$',
			'hkd' => 'HK$',
			'nok' => 'kr',
			'sek' => 'kr',
			'dkk' => 'kr',
			'pln' => 'zł',
			'czk' => 'Kč',
			'huf' => 'Ft',
			'try' => '₺',
			'zar' => 'R',
			'thb' => '฿',
			'myr' => 'RM',
			'idr' => 'Rp',
			'php' => '₱',
			'vnd' => '₫',
		];
		
		return isset($currency_symbols[$currency_code]) ? $currency_symbols[$currency_code] : $currency_code;
	}

	/**
	 * Formats a monetary value with the current 1.1.4.
	 *
	 * @since    1.0.5
	 * @param    float     $amount           The amount to format.
	 * @param    string    $currency_code    Optional currency code (defaults to 1.1.4).
	 * @return   string    Formatted currency string.
	 */
	/**
	 * Get the effective currency for the current user.
	 * Checks user meta first, then falls back to the global option.
	 *
	 * @since    1.1.0
	 * @return   string    Currency code (lowercase).
	 */
	public static function pn_personal_finance_manager_get_effective_currency() {
		if (is_user_logged_in()) {
			$user_currency = get_user_meta(get_current_user_id(), 'pn_personal_finance_manager_user_currency', true);
			if (!empty($user_currency)) {
				return strtolower($user_currency);
			}
		}
		return strtolower(get_option('pn_personal_finance_manager_currency', 'eur'));
	}

	/**
	 * Get the effective number format for the current user.
	 * Returns decimal and thousands separators based on user preference.
	 *
	 * @since    1.1.0
	 * @return   array    Array with 'dec_sep' and 'thousands_sep' keys.
	 */
	public static function pn_personal_finance_manager_get_effective_number_format() {
		$format = '';
		if (is_user_logged_in()) {
			$format = get_user_meta(get_current_user_id(), 'pn_personal_finance_manager_number_format', true);
		}
		if (empty($format)) {
			$format = 'dot_comma'; // default: 1,234.56
		}

		switch ($format) {
			case 'comma_dot':    // 1.234,56
				return ['dec_sep' => ',', 'thousands_sep' => '.'];
			case 'space_comma':  // 1 234,56
				return ['dec_sep' => ',', 'thousands_sep' => ' '];
			case 'dot_comma':    // 1,234.56
			default:
				return ['dec_sep' => '.', 'thousands_sep' => ','];
		}
	}

	public static function pn_personal_finance_manager_format_currency($amount, $currency_code = null) {
		if ($currency_code === null) {
			$currency_code = self::pn_personal_finance_manager_get_effective_currency();
		}

		$symbol = self::pn_personal_finance_manager_get_currency_symbol($currency_code);
		$nf = self::pn_personal_finance_manager_get_effective_number_format();
		$formatted_amount = number_format($amount, 2, $nf['dec_sep'], $nf['thousands_sep']);

		return [
			'symbol' => $symbol,
			'amount' => $formatted_amount,
			'code' => $currency_code,
			'full' => $symbol . ' ' . $formatted_amount,
			'with_code' => $symbol . ' ' . $formatted_amount . ' (' . $currency_code . ')'
		];
	}

	/**
	 * Gets the exchange rate from USD to the selected currency (or 1 if USD).
	 * Uses open.er-api.com API and caches the result for 12 hours.
	 *
	 * @since    1.0.6
	 * @param    string $currency_code
	 * @return   float  Exchange rate (USD to currency_code)
	 */
	public static function pn_personal_finance_manager_get_usd_exchange_rate($currency_code = null) {
		if ($currency_code === null) {
			$currency_code = self::pn_personal_finance_manager_get_effective_currency();
		}
		$currency_code = strtolower($currency_code);
		if ($currency_code === 'usd') return 1.0;
		$transient_key = 'pn_personal_finance_manager_usd_rate_' . $currency_code;
		$rate = get_transient($transient_key);
		if ($rate !== false && is_numeric($rate)) {
			return floatval($rate);
		}
		// Llamada a la API open.er-api.com SIN access_key
		$url = 'https://open.er-api.com/v6/latest/USD';
		$response = wp_remote_get($url, ['timeout' => 10]);
		error_log('PnPersonalFinanceManager Debug: Consultando tipo de cambio para ' . $currency_code);
		error_log('PnPersonalFinanceManager Debug: URL llamada: ' . $url);
		if (is_wp_error($response)) {
			error_log('PnPersonalFinanceManager Debug: Error en la respuesta de la API de tipo de cambio');
			return 1.0;
		}
		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);
		error_log('PnPersonalFinanceManager Debug: Respuesta: ' . print_r($data, true));
		
		// Convertir la moneda a mayúsculas para buscar en la API
		$currency_upper = strtoupper($currency_code);
		error_log('PnPersonalFinanceManager Debug: Buscando tipo de cambio para clave: ' . $currency_upper);
		
		if (isset($data['result']) && $data['result'] === 'success' && isset($data['rates'][$currency_upper]) && is_numeric($data['rates'][$currency_upper])) {
			$rate = floatval($data['rates'][$currency_upper]);
			set_transient($transient_key, $rate, 12 * HOUR_IN_SECONDS);
			error_log('PnPersonalFinanceManager Debug: Guardando transient ' . $transient_key . ' con valor ' . $rate);
			return $rate;
		}
		error_log('PnPersonalFinanceManager Debug: No se encontró el tipo de cambio en la respuesta.');
		return 1.0;
	}

	/**
	 * Converts a value in USD to the selected currency.
	 *
	 * @since    1.0.6
	 * @param    float $amount
	 * @param    string $currency_code
	 * @return   float
	 */
	public static function pn_personal_finance_manager_convert_from_usd($amount, $currency_code = null) {
		$rate = self::pn_personal_finance_manager_get_usd_exchange_rate($currency_code);
		return $amount * $rate;
	}
}