<?php
/**
 * The class responsible for watchlist functionality.
 *
 * Allows users to track stocks and cryptocurrencies without owning them,
 * with optional price alert notifications.
 *
 * @since      1.1.0
 * @package    PnFinanceManager
 * @subpackage PnFinanceManager/includes
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_FINANCE_MANAGER_Watchlist {

	/**
	 * User meta key for storing watchlist data.
	 *
	 * @since    1.1.0
	 * @var      string
	 */
	const META_KEY = 'pn_finance_manager_watchlist';

	/**
	 * Initialize the class.
	 *
	 * @since    1.1.0
	 */
	public function __construct() {
		// Constructor
	}

	/**
	 * Get all watchlist items for a user.
	 *
	 * @since    1.1.0
	 * @param    int    $user_id    The user ID.
	 * @return   array              Array of watchlist items.
	 */
	public function pn_finance_manager_watchlist_get_user_items($user_id) {
		$items = get_user_meta($user_id, self::META_KEY, true);
		return is_array($items) ? $items : [];
	}

	/**
	 * Add an item to the user's watchlist.
	 *
	 * @since    1.1.0
	 * @param    int       $user_id          The user ID.
	 * @param    string    $type             'stock' or 'crypto'.
	 * @param    string    $symbol           Stock ticker or CoinGecko coin_id.
	 * @param    bool      $alert_enabled    Whether price alert is enabled.
	 * @param    int       $alert_threshold  Alert threshold percentage (1-50).
	 * @return   array|false                 The new item on success, false on failure.
	 */
	public function pn_finance_manager_watchlist_add_item($user_id, $type, $symbol, $alert_enabled, $alert_threshold) {
		if (empty($type) || empty($symbol)) {
			return false;
		}

		if (!in_array($type, ['stock', 'crypto'], true)) {
			return false;
		}

		$alert_threshold = max(1, min(50, intval($alert_threshold)));

		// Resolve display name
		$display_name = $symbol;
		if ($type === 'stock') {
			$cached_symbols = get_option('pn_finance_manager_stock_symbols_cache', []);
			if (isset($cached_symbols[$symbol])) {
				$display_name = $symbol;
			}
		} else {
			$id_to_ticker = get_option('pn_finance_manager_crypto_id_to_ticker', []);
			$display_name = isset($id_to_ticker[$symbol]) ? strtoupper($id_to_ticker[$symbol]) : strtoupper($symbol);
		}

		$items = $this->pn_finance_manager_watchlist_get_user_items($user_id);

		// Check for duplicate
		foreach ($items as $item) {
			if ($item['type'] === $type && $item['symbol'] === $symbol) {
				return false;
			}
		}

		$new_item = [
			'id'              => uniqid('wl_', true),
			'type'            => $type,
			'symbol'          => $symbol,
			'display_name'    => $display_name,
			'alert_enabled'   => (bool) $alert_enabled,
			'alert_threshold' => $alert_threshold,
			'added_date'      => current_time('Y-m-d'),
			'last_alert_sent' => null,
		];

		$items[] = $new_item;
		update_user_meta($user_id, self::META_KEY, $items);

		return $new_item;
	}

	/**
	 * Update an existing watchlist item's alert settings.
	 *
	 * @since    1.1.0
	 * @param    int       $user_id          The user ID.
	 * @param    string    $item_id          The watchlist item ID.
	 * @param    bool      $alert_enabled    Whether price alert is enabled.
	 * @param    int       $alert_threshold  Alert threshold percentage (1-50).
	 * @return   bool                        True on success, false on failure.
	 */
	public function pn_finance_manager_watchlist_update_item($user_id, $item_id, $alert_enabled, $alert_threshold) {
		$items = $this->pn_finance_manager_watchlist_get_user_items($user_id);
		$alert_threshold = max(1, min(50, intval($alert_threshold)));
		$found = false;

		foreach ($items as &$item) {
			if ($item['id'] === $item_id) {
				$item['alert_enabled'] = (bool) $alert_enabled;
				$item['alert_threshold'] = $alert_threshold;
				$found = true;
				break;
			}
		}
		unset($item);

		if (!$found) {
			return false;
		}

		update_user_meta($user_id, self::META_KEY, $items);
		return true;
	}

	/**
	 * Remove an item from the user's watchlist.
	 *
	 * @since    1.1.0
	 * @param    int       $user_id    The user ID.
	 * @param    string    $item_id    The watchlist item ID.
	 * @return   bool                  True on success, false on failure.
	 */
	public function pn_finance_manager_watchlist_remove_item($user_id, $item_id) {
		$items = $this->pn_finance_manager_watchlist_get_user_items($user_id);
		$original_count = count($items);

		$items = array_values(array_filter($items, function($item) use ($item_id) {
			return $item['id'] !== $item_id;
		}));

		if (count($items) === $original_count) {
			return false;
		}

		update_user_meta($user_id, self::META_KEY, $items);
		return true;
	}

	/**
	 * Render the watchlist shortcode.
	 *
	 * @since    1.1.0
	 * @param    array    $atts    Shortcode attributes.
	 * @return   string            HTML output.
	 */
	public function pn_finance_manager_watchlist_render($atts) {
		if (!is_user_logged_in()) {
			return '<p class="pn-finance-manager-watchlist-login-notice">' . esc_html__('Please log in to view your watchlist.', 'pn-finance-manager') . '</p>';
		}

		$user_id = get_current_user_id();

		ob_start();
		?>
		<div class="pn-finance-manager-watchlist-container">
			<div class="pn-finance-manager-watchlist-header">
				<h2><?php echo esc_html__('Watchlist', 'pn-finance-manager'); ?></h2>
				<button type="button" class="pn-finance-manager-watchlist-refresh-btn" title="<?php echo esc_attr__('Refresh prices', 'pn-finance-manager'); ?>">
					<i class="material-icons-outlined">refresh</i>
				</button>
			</div>

			<?php echo wp_kses($this->pn_finance_manager_watchlist_add_form(), PN_FINANCE_MANAGER_KSES); ?>

			<div class="pn-finance-manager-watchlist-items">
				<?php echo wp_kses($this->pn_finance_manager_watchlist_list_items($user_id), PN_FINANCE_MANAGER_KSES); ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the add-to-watchlist form.
	 *
	 * @since    1.1.0
	 * @return   string    HTML output.
	 */
	public function pn_finance_manager_watchlist_add_form() {
		ob_start();
		?>
		<div class="pn-finance-manager-watchlist-add-form">
			<div class="pn-finance-manager-watchlist-form-row">
				<div class="pn-finance-manager-watchlist-form-field">
					<label for="pn_finance_manager_watchlist_type"><?php echo esc_html__('Type', 'pn-finance-manager'); ?></label>
					<?php PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_builder([
						'input'       => 'select',
						'id'          => 'pn_finance_manager_watchlist_type',
						'class'       => 'pn-finance-manager-select pn-finance-manager-watchlist-type-select',
						'parent'      => 'this',
						'placeholder' => __('Select Type', 'pn-finance-manager'),
						'options'     => [
							'stock'  => __('Stock', 'pn-finance-manager'),
							'crypto' => __('Cryptocurrency', 'pn-finance-manager'),
						],
					], 'none'); ?>
				</div>

				<div class="pn-finance-manager-input-wrapper pn-finance-manager-watchlist-form-field pn-finance-manager-watchlist-stock-field">
					<label for="pn_finance_manager_watchlist_stock_symbol"><?php echo esc_html__('Stock Symbol', 'pn-finance-manager'); ?></label>
					<?php PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_builder([
						'input'         => 'select',
						'id'            => 'pn_finance_manager_watchlist_stock_symbol',
						'class'         => 'pn-finance-manager-select pn-finance-manager-watchlist-symbol-select',
						'parent'        => 'pn_finance_manager_watchlist_type',
						'parent_option' => 'stock',
						'options'       => ['' => __('Select a stock symbol', 'pn-finance-manager')],
					], 'none'); ?>
				</div>

				<div class="pn-finance-manager-input-wrapper pn-finance-manager-watchlist-form-field pn-finance-manager-watchlist-crypto-field">
					<label for="pn_finance_manager_watchlist_crypto_symbol"><?php echo esc_html__('Cryptocurrency', 'pn-finance-manager'); ?></label>
					<?php PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_builder([
						'input'         => 'select',
						'id'            => 'pn_finance_manager_watchlist_crypto_symbol',
						'class'         => 'pn-finance-manager-select pn-finance-manager-watchlist-symbol-select',
						'parent'        => 'pn_finance_manager_watchlist_type',
						'parent_option' => 'crypto',
						'options'       => ['' => __('Select a cryptocurrency', 'pn-finance-manager')],
					], 'none'); ?>
				</div>
			</div>

			<div class="pn-finance-manager-watchlist-form-row pn-finance-manager-watchlist-alert-row">
				<div class="pn-finance-manager-watchlist-form-field pn-finance-manager-watchlist-alert-toggle-field">
					<label><?php echo esc_html__('Enable Price Alert', 'pn-finance-manager'); ?></label>
					<?php PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_builder([
						'input' => 'input',
						'type'  => 'checkbox',
						'id'    => 'pn_finance_manager_watchlist_alert_enabled',
						'parent' => 'this',
					], 'none'); ?>
				</div>

				<div class="pn-finance-manager-watchlist-form-field pn-finance-manager-watchlist-threshold-field pn-finance-manager-display-none-soft">
					<label for="pn_finance_manager_watchlist_threshold"><?php echo esc_html__('Alert Threshold', 'pn-finance-manager'); ?></label>
					<?php PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_builder([
						'input'                  => 'input',
						'type'                   => 'range',
						'id'                     => 'pn_finance_manager_watchlist_threshold',
						'pn_finance_manager_min' => 1,
						'pn_finance_manager_max' => 50,
						'value'                  => '5',
						'parent'                 => 'pn_finance_manager_watchlist_alert_enabled',
						'parent_option'          => 'on',
					], 'none'); ?>
				</div>
			</div>

			<div class="pn-finance-manager-watchlist-form-row">
				<?php PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_builder([
					'input' => 'input',
					'type'  => 'button',
					'id'    => 'pn_finance_manager_watchlist_add',
					'class' => 'pn-finance-manager-watchlist-add-btn',
					'label' => __('Add to Watchlist', 'pn-finance-manager'),
				], 'none'); ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the list of watchlist items.
	 *
	 * @since    1.1.0
	 * @param    int    $user_id    The user ID.
	 * @return   string             HTML output.
	 */
	public function pn_finance_manager_watchlist_list_items($user_id) {
		$items = $this->pn_finance_manager_watchlist_get_user_items($user_id);

		if (empty($items)) {
			return '<p class="pn-finance-manager-watchlist-empty">' . esc_html__('Your watchlist is empty. Add stocks or cryptocurrencies to track.', 'pn-finance-manager') . '</p>';
		}

		$stocks = new PN_FINANCE_MANAGER_Stocks();
		$stock_symbols_cache = get_option('pn_finance_manager_stock_symbols_cache', []);
		$crypto_symbols_cache = get_option('pn_finance_manager_crypto_symbols_cache', []);
		ob_start();

		foreach ($items as $item) {
			$data = false;
			if ($item['type'] === 'stock') {
				$data = $stocks->pn_finance_manager_get_stock_data($item['symbol']);
			} else {
				$data = $stocks->pn_finance_manager_get_crypto_data($item['symbol']);
			}

			$price_display = '—';
			$change_display = '—';
			$change_class = '';

			if ($data && isset($data['price'])) {
				$converted_price = PN_FINANCE_MANAGER_Data::pn_finance_manager_convert_from_usd(floatval($data['price']));
				$price_formatted = PN_FINANCE_MANAGER_Data::pn_finance_manager_format_currency($converted_price);
				$price_display = is_array($price_formatted) ? $price_formatted['full'] : $price_formatted;

				$change_percent = isset($data['change_percent']) ? $data['change_percent'] : '0%';
				$change_val = floatval(str_replace('%', '', $change_percent));
				$change_class = $change_val >= 0 ? 'positive' : 'negative';
				$change_sign = $change_val >= 0 ? '+' : '';

				$converted_change = PN_FINANCE_MANAGER_Data::pn_finance_manager_convert_from_usd(floatval($data['change']));
				$change_formatted = PN_FINANCE_MANAGER_Data::pn_finance_manager_format_currency($converted_change);
				$change_str = is_array($change_formatted) ? $change_formatted['full'] : $change_formatted;
				$change_display = $change_sign . $change_str . ' (' . esc_html($change_percent) . ')';
			}

			$type_label = $item['type'] === 'stock' ? esc_html__('Stock', 'pn-finance-manager') : esc_html__('Crypto', 'pn-finance-manager');
			$type_class = $item['type'] === 'stock' ? 'pn-finance-manager-watchlist-type-stock' : 'pn-finance-manager-watchlist-type-crypto';
			$threshold_val = isset($item['alert_threshold']) ? intval($item['alert_threshold']) : 5;

			// Resolve full name from symbol caches
			$symbol_name = '';
			if ($item['type'] === 'stock' && isset($stock_symbols_cache[$item['symbol']])) {
				$symbol_name = $stock_symbols_cache[$item['symbol']];
			} elseif ($item['type'] === 'crypto' && isset($crypto_symbols_cache[$item['symbol']])) {
				$cached_name = $crypto_symbols_cache[$item['symbol']];
				// Cache stores "TICKER - Name", extract just the name
				$dash_pos = strpos($cached_name, ' - ');
				$symbol_name = $dash_pos !== false ? substr($cached_name, $dash_pos + 3) : $cached_name;
			}
			?>
			<div class="pn-finance-manager-watchlist-item" data-pn-finance-manager-watchlist-item-id="<?php echo esc_attr($item['id']); ?>">
				<div class="pn-finance-manager-watchlist-item-header">
					<div class="pn-finance-manager-watchlist-item-symbol">
						<div>
							<span class="pn-finance-manager-watchlist-display-name"><?php echo esc_html($item['display_name']); ?></span>
							<span class="pn-finance-manager-watchlist-type-badge <?php echo esc_attr($type_class); ?>"><?php echo esc_html($type_label); ?></span>
							<?php if (!empty($symbol_name)): ?>
								<div class="pn-finance-manager-watchlist-symbol-name"><?php echo esc_html($symbol_name); ?></div>
							<?php endif; ?>
						</div>
					</div>
					<div class="pn-finance-manager-watchlist-item-actions">
						<button type="button" class="pn-finance-manager-watchlist-edit-btn" title="<?php echo esc_attr__('Edit alerts', 'pn-finance-manager'); ?>">
							<i class="material-icons-outlined">edit</i>
						</button>
						<button type="button" class="pn-finance-manager-watchlist-remove-btn" data-pn-finance-manager-item-id="<?php echo esc_attr($item['id']); ?>" title="<?php echo esc_attr__('Remove', 'pn-finance-manager'); ?>">
							<i class="material-icons-outlined">delete</i>
						</button>
					</div>
				</div>

				<div class="pn-finance-manager-watchlist-item-body">
					<div class="pn-finance-manager-watchlist-price-section">
						<div class="pn-finance-manager-watchlist-current-price">
							<span class="pn-finance-manager-watchlist-price-label"><?php echo esc_html__('Current Price', 'pn-finance-manager'); ?></span>
							<span class="pn-finance-manager-watchlist-price-value"><?php echo esc_html($price_display); ?></span>
						</div>
						<div class="pn-finance-manager-watchlist-price-change <?php echo esc_attr($change_class); ?>">
							<span class="pn-finance-manager-watchlist-change-label"><?php echo esc_html__('Daily Change', 'pn-finance-manager'); ?></span>
							<span class="pn-finance-manager-watchlist-change-value"><?php echo esc_html($change_display); ?></span>
						</div>
					</div>

					<div class="pn-finance-manager-watchlist-alert-controls pn-finance-manager-display-none-soft">
						<div class="pn-finance-manager-watchlist-alert-field">
							<label class="pn-finance-manager-watchlist-alert-toggle-label"><?php echo esc_html__('Enable Price Alert', 'pn-finance-manager'); ?></label>
							<?php PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_builder([
								'input' => 'input',
								'type'  => 'checkbox',
								'id'    => 'pn_finance_manager_watchlist_alert_' . $item['id'],
								'class' => 'pn-finance-manager-watchlist-alert-toggle',
								'value' => !empty($item['alert_enabled']) ? 'on' : '',
							], 'none'); ?>
						</div>
						<div class="pn-finance-manager-watchlist-threshold-field-item <?php echo empty($item['alert_enabled']) ? 'pn-finance-manager-display-none-soft' : ''; ?>">
							<label><?php echo esc_html__('Alert Threshold', 'pn-finance-manager'); ?></label>
							<?php PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_builder([
								'input'                  => 'input',
								'type'                   => 'range',
								'id'                     => 'pn_finance_manager_watchlist_threshold_' . $item['id'],
								'class'                  => 'pn-finance-manager-watchlist-threshold',
								'pn_finance_manager_min' => 1,
								'pn_finance_manager_max' => 50,
								'value'                  => $threshold_val,
							], 'none'); ?>
						</div>
						<div class="pn-finance-manager-watchlist-save-field">
							<?php PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_builder([
								'input' => 'input',
								'type'  => 'button',
								'id'    => 'pn_finance_manager_watchlist_save_' . $item['id'],
								'class' => 'pn-finance-manager-watchlist-save-btn pn-finance-manager-btn',
								'label' => __('Save alerts', 'pn-finance-manager'),
							], 'none'); ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		return ob_get_clean();
	}

	/**
	 * Daily cron callback to check price alerts and send emails.
	 *
	 * @since    1.1.0
	 */
	public function pn_finance_manager_watchlist_check_alerts() {
		$users = get_users(['fields' => 'ID']);
		$stocks = new PN_FINANCE_MANAGER_Stocks();
		$today = current_time('Y-m-d');

		foreach ($users as $user_id) {
			// Read user alert preferences
			$alert_prefs = PN_FINANCE_MANAGER_Export_Import::pn_finance_manager_get_alert_preferences($user_id);
			$default_threshold = max(1, min(50, intval($alert_prefs['default_threshold'])));
			$alerts_assets_enabled = !empty($alert_prefs['alerts_assets_enabled']);
			$alerts_watchlist_enabled = !empty($alert_prefs['alerts_watchlist_enabled']);

			$alerts_to_send = [];
			$watchlist_updated = false;

			// --- Watchlist alerts ---
			if ($alerts_watchlist_enabled) {
				$items = $this->pn_finance_manager_watchlist_get_user_items($user_id);

				if (!empty($items)) {
					foreach ($items as &$item) {
						if (empty($item['alert_enabled'])) {
							continue;
						}

						if (!empty($item['last_alert_sent']) && $item['last_alert_sent'] === $today) {
							continue;
						}

						$data = false;
						if ($item['type'] === 'stock') {
							$data = $stocks->pn_finance_manager_get_stock_data($item['symbol']);
						} else {
							$data = $stocks->pn_finance_manager_get_crypto_data($item['symbol']);
							if ($data && $item['type'] === 'crypto') {
								sleep(3); // CoinGecko rate limit
							}
						}

						if (!$data || !isset($data['change_percent'])) {
							continue;
						}

						$change_percent_abs = abs(floatval(str_replace('%', '', $data['change_percent'])));

						if ($change_percent_abs >= $item['alert_threshold']) {
							$alerts_to_send[] = [
								'item'   => $item,
								'data'   => $data,
								'source' => 'watchlist',
							];
							$item['last_alert_sent'] = $today;
							$watchlist_updated = true;
						}
					}
					unset($item);

					if ($watchlist_updated) {
						update_user_meta($user_id, self::META_KEY, $items);
					}
				}
			}

			// --- Portfolio asset alerts ---
			if ($alerts_assets_enabled) {
				$asset_posts = get_posts([
					'post_type'      => 'pnfm_asset',
					'post_status'    => 'publish',
					'author'         => $user_id,
					'posts_per_page' => -1,
					'meta_query'     => [
						[
							'key'     => 'pn_finance_manager_asset_type',
							'value'   => ['stocks', 'cryptocurrencies'],
							'compare' => 'IN',
						],
					],
				]);

				foreach ($asset_posts as $asset_post) {
					$last_alert = get_post_meta($asset_post->ID, 'pn_finance_manager_asset_last_alert', true);
					if ($last_alert === $today) {
						continue;
					}

					$asset_type = get_post_meta($asset_post->ID, 'pn_finance_manager_asset_type', true);
					$data = false;
					$symbol = '';
					$display_name = $asset_post->post_title;
					$item_type = '';

					if ($asset_type === 'stocks') {
						$symbol = get_post_meta($asset_post->ID, 'pn_finance_manager_stock_symbol', true);
						if (!empty($symbol)) {
							$data = $stocks->pn_finance_manager_get_stock_data($symbol);
							$item_type = 'stock';
						}
					} elseif ($asset_type === 'cryptocurrencies') {
						$symbol = get_post_meta($asset_post->ID, 'pn_finance_manager_crypto_symbol', true);
						if (!empty($symbol)) {
							$data = $stocks->pn_finance_manager_get_crypto_data($symbol);
							$item_type = 'crypto';
							if ($data) {
								sleep(3); // CoinGecko rate limit
							}
						}
					}

					if (!$data || !isset($data['change_percent'])) {
						continue;
					}

					$change_percent_abs = abs(floatval(str_replace('%', '', $data['change_percent'])));

					if ($change_percent_abs >= $default_threshold) {
						$alerts_to_send[] = [
							'item'   => [
								'display_name'    => $display_name,
								'type'            => $item_type,
								'symbol'          => $symbol,
								'alert_threshold' => $default_threshold,
							],
							'data'   => $data,
							'source' => 'portfolio',
						];
						update_post_meta($asset_post->ID, 'pn_finance_manager_asset_last_alert', $today);
					}
				}
			}

			if (!empty($alerts_to_send)) {
				$this->pn_finance_manager_watchlist_send_alert_email($user_id, $alerts_to_send);
			}
		}
	}

	/**
	 * Send price alert email to a user.
	 *
	 * @since    1.1.0
	 * @param    int      $user_id    The user ID.
	 * @param    array    $alerts     Array of triggered alerts with item and data.
	 */
	private function pn_finance_manager_watchlist_send_alert_email($user_id, $alerts) {
		$user = get_user_by('id', $user_id);
		if (!$user) {
			return;
		}

		$subject = __('Price Alert - Finance Manager', 'pn-finance-manager');

		$html_body = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">';
		$html_body .= '<h2 style="color: #333;">' . esc_html__('Price Alert Notification', 'pn-finance-manager') . '</h2>';
		$html_body .= '<p>' . esc_html__('The following items have exceeded their price alert threshold:', 'pn-finance-manager') . '</p>';
		$html_body .= '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">';
		$html_body .= '<tr style="background-color: #f8f9fa;">';
		$html_body .= '<th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">' . esc_html__('Symbol', 'pn-finance-manager') . '</th>';
		$html_body .= '<th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">' . esc_html__('Type', 'pn-finance-manager') . '</th>';
		$html_body .= '<th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">' . esc_html__('Source', 'pn-finance-manager') . '</th>';
		$html_body .= '<th style="padding: 10px; text-align: right; border-bottom: 2px solid #dee2e6;">' . esc_html__('Current Price', 'pn-finance-manager') . '</th>';
		$html_body .= '<th style="padding: 10px; text-align: right; border-bottom: 2px solid #dee2e6;">' . esc_html__('Daily Change', 'pn-finance-manager') . '</th>';
		$html_body .= '<th style="padding: 10px; text-align: right; border-bottom: 2px solid #dee2e6;">' . esc_html__('Alert Threshold', 'pn-finance-manager') . '</th>';
		$html_body .= '</tr>';

		foreach ($alerts as $alert) {
			$item = $alert['item'];
			$data = $alert['data'];

			$converted_price = PN_FINANCE_MANAGER_Data::pn_finance_manager_convert_from_usd(floatval($data['price']));
			$price_formatted = PN_FINANCE_MANAGER_Data::pn_finance_manager_format_currency($converted_price);
			$price_str = is_array($price_formatted) ? $price_formatted['full'] : $price_formatted;
			$change_percent = isset($data['change_percent']) ? $data['change_percent'] : '0%';
			$change_val = floatval(str_replace('%', '', $change_percent));
			$change_color = $change_val >= 0 ? '#155724' : '#721c24';
			$change_sign = $change_val >= 0 ? '+' : '';

			$source = !empty($alert['source']) && $alert['source'] === 'portfolio'
				? __('Portfolio', 'pn-finance-manager')
				: __('Watchlist', 'pn-finance-manager');

			$html_body .= '<tr>';
			$html_body .= '<td style="padding: 10px; border-bottom: 1px solid #dee2e6;">' . esc_html($item['display_name']) . '</td>';
			$html_body .= '<td style="padding: 10px; border-bottom: 1px solid #dee2e6;">' . esc_html(ucfirst($item['type'])) . '</td>';
			$html_body .= '<td style="padding: 10px; border-bottom: 1px solid #dee2e6;">' . esc_html($source) . '</td>';
			$html_body .= '<td style="padding: 10px; text-align: right; border-bottom: 1px solid #dee2e6;">' . esc_html($price_str) . '</td>';
			$html_body .= '<td style="padding: 10px; text-align: right; border-bottom: 1px solid #dee2e6; color: ' . esc_attr($change_color) . ';">' . esc_html($change_sign . $change_percent) . '</td>';
			$html_body .= '<td style="padding: 10px; text-align: right; border-bottom: 1px solid #dee2e6;">' . esc_html($item['alert_threshold'] . '%') . '</td>';
			$html_body .= '</tr>';
		}

		$html_body .= '</table>';
		$html_body .= '<p style="color: #6c757d; font-size: 12px;">' . esc_html__('This is an automated alert from Finance Manager. You can change your alert preferences in your profile settings.', 'pn-finance-manager') . '</p>';
		$html_body .= '</div>';

		// Send via mailpn plugin if available, otherwise wp_mail
		if (class_exists('MAILPN_Mailing')) {
			$mailing = new MAILPN_Mailing();
			$mailing->mailpn_sender([
				'mailpn_user_to' => $user_id,
				'mailpn_subject' => $subject,
			], $html_body);
		} else {
			$headers = ['Content-Type: text/html; charset=UTF-8'];
			wp_mail($user->user_email, $subject, $html_body, $headers);
		}
	}
}
