<?php
/**
 * Platform shortcodes.
 *
 * This class defines all shortcodes of the platform.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Shortcodes {
	/**
	 * Manage the shortcodes in the platform.
	 *
	 * @since    1.0.0
	 */
	public function pn_personal_finance_manager_test($atts) {
    $a = extract(shortcode_atts([
      'user_id' => 0,
      'post_id' => 0,
    ], $atts));

    ob_start();
    ?>
      <div class="pn-personal-finance-manager-shortcode-example">
      	Shortcode example
      	<p>User id: <?php echo intval($user_id); ?></p>
      	<p>Post id: <?php echo intval($post_id); ?></p>
      </div>
    <?php
    $pn_personal_finance_manager_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $pn_personal_finance_manager_return_string;
	}

  public function pn_personal_finance_manager_call_to_action($atts) {
    // echo do_shortcode('[pn-personal-finance-manager-call-to-action pn_personal_finance_manager_call_to_action_icon="error_outline" pn_personal_finance_manager_call_to_action_title="' . esc_html(__('Default title', 'pn-personal-finance-manager')) . '" pn_personal_finance_manager_call_to_action_content="' . esc_html(__('Default content', 'pn-personal-finance-manager')) . '" pn_personal_finance_manager_call_to_action_button_link="#" pn_personal_finance_manager_call_to_action_button_text="' . esc_html(__('Button text', 'pn-personal-finance-manager')) . '" pn_personal_finance_manager_call_to_action_button_class="pn-personal-finance-manager-class"]');
    $a = extract(shortcode_atts(array(
      'pn_personal_finance_manager_call_to_action_class' => '',
      'pn_personal_finance_manager_call_to_action_icon' => '',
      'pn_personal_finance_manager_call_to_action_title' => '',
      'pn_personal_finance_manager_call_to_action_content' => '',
      'pn_personal_finance_manager_call_to_action_button_link' => '#',
      'pn_personal_finance_manager_call_to_action_button_text' => '',
      'pn_personal_finance_manager_call_to_action_button_class' => '',
      'pn_personal_finance_manager_call_to_action_button_data_key' => '',
      'pn_personal_finance_manager_call_to_action_button_data_value' => '',
      'pn_personal_finance_manager_call_to_action_button_blank' => 0,
    ), $atts));

    ob_start();
    ?>
      <div class="pn-personal-finance-manager-call-to-action pn-personal-finance-manager-text-align-center pn-personal-finance-manager-pt-30 pn-personal-finance-manager-pb-50 <?php echo esc_attr($pn_personal_finance_manager_call_to_action_class); ?>">
        <div class="pn-personal-finance-manager-call-to-action-icon">
          <i class="material-icons-outlined pn-personal-finance-manager-font-size-75 pn-personal-finance-manager-color-main-0"><?php echo esc_html($pn_personal_finance_manager_call_to_action_icon); ?></i>
        </div>

        <h4 class="pn-personal-finance-manager-call-to-action-title pn-personal-finance-manager-text-align-center pn-personal-finance-manager-mt-10 pn-personal-finance-manager-mb-20"><?php echo esc_html($pn_personal_finance_manager_call_to_action_title); ?></h4>
        
        <?php if (!empty($pn_personal_finance_manager_call_to_action_content)): ?>
          <p class="pn-personal-finance-manager-text-align-center"><?php echo wp_kses_post($pn_personal_finance_manager_call_to_action_content); ?></p>
        <?php endif ?>

        <?php if (!empty($pn_personal_finance_manager_call_to_action_button_text)): ?>
          <div class="pn-personal-finance-manager-text-align-center pn-personal-finance-manager-mt-20">
            <a class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-transparent pn-personal-finance-manager-margin-auto <?php echo esc_attr($pn_personal_finance_manager_call_to_action_button_class); ?>" <?php echo ($pn_personal_finance_manager_call_to_action_button_blank) ? 'target="_blank"' : ''; ?> href="<?php echo esc_url($pn_personal_finance_manager_call_to_action_button_link); ?>" <?php echo (!empty($pn_personal_finance_manager_call_to_action_button_data_key) && !empty($pn_personal_finance_manager_call_to_action_button_data_value)) ? esc_attr($pn_personal_finance_manager_call_to_action_button_data_key) . '="' . esc_attr($pn_personal_finance_manager_call_to_action_button_data_value) . '"' : ''; ?>><?php echo esc_html($pn_personal_finance_manager_call_to_action_button_text); ?></a>
          </div>
        <?php endif ?>
      </div>
    <?php 
    $pn_personal_finance_manager_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $pn_personal_finance_manager_return_string;
  }

	/**
	 * Shortcode to display user assets portfolio.
	 *
	 * @since    1.0.5
	 * @param    array     $atts      Shortcode attributes.
	 * @return   string                HTML output.
	 */
	public function pn_personal_finance_manager_user_assets_shortcode($atts) {
		$atts = shortcode_atts([
			'user_id' => get_current_user_id(),
			'display_type' => 'portfolio' // portfolio, summary, stocks_only
		], $atts, 'pn_personal_finance_manager_user_assets');
		
		// Check if user is logged in or if admin is viewing
		if (!is_user_logged_in() && !current_user_can('manage_options')) {
			$cta_button_class = class_exists('USERSPN') ? 'userspn-profile-popup-btn' : '';
			$cta_button_data_key = class_exists('USERSPN') ? 'data-userspn-action' : '';
			$cta_button_data_value = class_exists('USERSPN') ? 'register' : '';
			$cta_button_link = class_exists('USERSPN') ? '#' : wp_registration_url();

			return $this->pn_personal_finance_manager_call_to_action([
				'pn_personal_finance_manager_call_to_action_icon' => 'account_balance_wallet',
				'pn_personal_finance_manager_call_to_action_title' => __('You need an account', 'pn-personal-finance-manager'),
				'pn_personal_finance_manager_call_to_action_content' => __('You have to be registered in the platform to access your financial portfolio.', 'pn-personal-finance-manager'),
				'pn_personal_finance_manager_call_to_action_button_link' => $cta_button_link,
				'pn_personal_finance_manager_call_to_action_button_class' => $cta_button_class,
				'pn_personal_finance_manager_call_to_action_button_data_key' => $cta_button_data_key,
				'pn_personal_finance_manager_call_to_action_button_data_value' => $cta_button_data_value,
				'pn_personal_finance_manager_call_to_action_button_text' => __('Create new account', 'pn-personal-finance-manager'),
			]);
		}
		
		// Check if user has permission to view this user's assets
		$current_user_id = get_current_user_id();
		$requested_user_id = intval($atts['user_id']);
		
		if ($current_user_id !== $requested_user_id && !current_user_can('manage_options')) {
			return '<div class="pn-personal-finance-manager-user-assets-error">' . 
				   '<p>' . __('You do not have permission to view these assets.', 'pn-personal-finance-manager') . '</p>' .
				   '</div>';
		}
		
		$stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
		
		switch ($atts['display_type']) {
			case 'summary':
				return $this->pn_personal_finance_manager_display_user_assets_summary($requested_user_id, $stocks);
			case 'stocks_only':
				return $this->pn_personal_finance_manager_display_user_stocks_only($requested_user_id, $stocks);
			default:
				return $stocks->pn_personal_finance_manager_display_user_assets($requested_user_id);
		}
	}

	/**
	 * Display user assets summary.
	 *
	 * @since    1.0.5
	 * @param    int       $user_id    User ID.
	 * @param    object    $stocks     Stocks object.
	 * @return   string                HTML output.
	 */
	private function pn_personal_finance_manager_display_user_assets_summary($user_id, $stocks) {
		$user_assets = $stocks->pn_personal_finance_manager_get_user_assets($user_id);
		
		if (empty($user_assets)) {
			return '<div class="pn-personal-finance-manager-user-assets-empty">' . 
				   '<p>' . __('No assets found.', 'pn-personal-finance-manager') . '</p>' .
				   '</div>';
		}
		
		$total_portfolio_value = 0;
		$total_profit_loss = 0;
		$total_assets = 0;
		
		foreach ($user_assets as $type_data) {
			$total_portfolio_value += $type_data['total_value'];
			$total_assets += $type_data['count'];
			foreach ($type_data['assets'] as $asset) {
				$total_profit_loss += $asset['profit_loss'];
			}
		}
		
		$currency = get_option('pn_personal_finance_manager_currency', 'eur');
		$converted_total_portfolio_value = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($total_portfolio_value, $currency), $currency);
		$converted_total_profit_loss = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($total_profit_loss, $currency), $currency);
		
		ob_start();
		?>
		<div class="pn-personal-finance-manager-user-assets-summary">
			<div class="pn-personal-finance-manager-summary-stats">
				<div class="pn-personal-finance-manager-stat-item">
					<span class="pn-personal-finance-manager-stat-label"><?php esc_html_e('Total Assets:', 'pn-personal-finance-manager'); ?></span>
					<span class="pn-personal-finance-manager-stat-value"><?php echo intval($total_assets); ?></span>
				</div>
				<div class="pn-personal-finance-manager-stat-item">
					<span class="pn-personal-finance-manager-stat-label"><?php esc_html_e('Portfolio Value:', 'pn-personal-finance-manager'); ?></span>
					<span class="pn-personal-finance-manager-stat-value"><?php echo esc_html($converted_total_portfolio_value['full']); ?></span>
				</div>
				<div class="pn-personal-finance-manager-stat-item">
					<span class="pn-personal-finance-manager-stat-label"><?php esc_html_e('Total P&L:', 'pn-personal-finance-manager'); ?></span>
					<span class="pn-personal-finance-manager-stat-value <?php echo $total_profit_loss >= 0 ? 'positive' : 'negative'; ?>">
						<?php echo esc_html($converted_total_profit_loss['full']); ?>
					</span>
				</div>
			</div>
			
			<div class="pn-personal-finance-manager-asset-categories">
				<?php foreach ($user_assets as $type_key => $type_data): ?>
					<div class="pn-personal-finance-manager-category-item">
						<span class="pn-personal-finance-manager-category-name"><?php echo esc_html($type_data['label']); ?></span>
						<span class="pn-personal-finance-manager-category-count"><?php echo intval($type_data['count']); ?></span>
						<span class="pn-personal-finance-manager-category-value"><?php echo esc_html(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($type_data['total_value'], $currency)['full']); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Display user stocks only.
	 *
	 * @since    1.0.5
	 * @param    int       $user_id    User ID.
	 * @param    object    $stocks     Stocks object.
	 * @return   string                HTML output.
	 */
	private function pn_personal_finance_manager_display_user_stocks_only($user_id, $stocks) {
		$user_assets = $stocks->pn_personal_finance_manager_get_user_assets($user_id);
		
		if (empty($user_assets) || !isset($user_assets['stocks'])) {
			return '<div class="pn-personal-finance-manager-user-assets-empty">' . 
				   '<p>' . __('No stock assets found for this user.', 'pn-personal-finance-manager') . '</p>' .
				   '</div>';
		}
		
		$stocks_data = $user_assets['stocks'];
		
		$currency = get_option('pn_personal_finance_manager_currency', 'eur');
		$converted_stocks_total_value = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($stocks_data['total_value'], $currency), $currency);
		
		ob_start();
		?>
		<div class="pn-personal-finance-manager-user-stocks-only">
			<div class="pn-personal-finance-manager-stocks-summary">
				<h3><?php esc_html_e('Stock Portfolio', 'pn-personal-finance-manager'); ?></h3>
				<div class="pn-personal-finance-manager-stocks-totals">
					<div class="pn-personal-finance-manager-stocks-value">
						<span class="pn-personal-finance-manager-label"><?php esc_html_e('Total Value:', 'pn-personal-finance-manager'); ?></span>
						<span class="pn-personal-finance-manager-value"><?php echo esc_html($converted_stocks_total_value['full']); ?></span>
					</div>
				</div>
			</div>
			
			<div class="pn-personal-finance-manager-stocks-list">
				<?php foreach ($stocks_data['assets'] as $asset): ?>
					<div class="pn-personal-finance-manager-stock-item">
						<div class="pn-personal-finance-manager-stock-info">
							<h4><?php echo esc_html($asset['title']); ?></h4>
							<span class="pn-personal-finance-manager-stock-symbol"><?php echo esc_html(strtoupper($asset['symbol'])); ?></span>
						</div>
						<div class="pn-personal-finance-manager-stock-value-row">
							<span class="pn-personal-finance-manager-stock-value">
								<?php
								$currency = get_option('pn_personal_finance_manager_currency', 'eur');
								$money_invested = $asset['total_invested'];
								$converted_invested = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(
									PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($money_invested, $currency),
									$currency
								);
								echo esc_html($converted_invested['full']);
								?>
							</span>
							<span class="pn-personal-finance-manager-stock-change <?php echo $asset['stock_data']['change'] >= 0 ? 'positive' : 'negative'; ?>">
								<?php
								$change = $asset['stock_data']['change'] * $asset['shares'];
								$converted_change = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(
									PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($change, $currency),
									$currency
								);
								echo esc_html(($change >= 0 ? '+' : '') . $converted_change['full']);
								?>
								(<?php echo esc_html($asset['stock_data']['change_percent']); ?>)
							</span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		// --- Gráfico global de la cartera de acciones ---
		$portfolio_history = [];
		foreach ($stocks_data['assets'] as $asset) {
			if (empty($asset['historical_data']) || empty($asset['shares'])) continue;
			foreach ($asset['historical_data'] as $row) {
				if (empty($row['recorded_date']) || !isset($row['price'])) continue;
				$date = $row['recorded_date'];
				$value = floatval($row['price']) * floatval($asset['shares']);
				if (!isset($portfolio_history[$date])) {
					$portfolio_history[$date] = 0;
				}
				$portfolio_history[$date] += $value;
			}
		}
		// Ordenar por fecha ascendente
		ksort($portfolio_history);
		if (!empty($portfolio_history)) {
			$currency = get_option('pn_personal_finance_manager_currency', 'eur');
			$symbol = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_currency_symbol($currency);
			$portfolio_chart_data = [];
			foreach ($portfolio_history as $date => $value) {
				$converted_value = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($value, $currency);
				$portfolio_chart_data[] = [
					'recorded_date' => $date,
					'value' => $converted_value
				];
			}
			?>
			<div class="pn-personal-finance-manager-portfolio-performance-chart">
				<h4><?php esc_html_e('Portfolio Performance', 'pn-personal-finance-manager'); ?></h4>
				<div class="pn-personal-finance-manager-chart-container">
					<canvas id="pn-personal-finance-manager-portfolio-chart" width="400" height="200"></canvas>
				</div>
			</div>
			<?php
			$_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	function drawPortfolioChart() {
		if (typeof Chart === 'undefined') return setTimeout(drawPortfolioChart, 200);
		var ctx = document.getElementById('pn-personal-finance-manager-portfolio-chart').getContext('2d');
		var data = __PNPFM_DATA__;
		var labels = data.map(function(item) { return item.recorded_date; });
		var values = data.map(function(item) { return item.value; });
		new Chart(ctx, {
			type: 'line',
			data: {
				labels: labels,
				datasets: [{
					label: '__PNPFM_SYMBOL__ Portfolio Value',
					data: values,
					borderColor: '#007bff',
					backgroundColor: 'rgba(0, 123, 255, 0.1)',
					borderWidth: 2,
					fill: true,
					tension: 0.1,
					pointRadius: 3,
					pointHoverRadius: 5
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				interaction: { intersect: false, mode: 'index' },
				scales: {
					x: { display: true, title: { display: true, text: 'Date' }, ticks: { maxTicksLimit: 10, maxRotation: 45 } },
					y: { display: true, title: { display: true, text: 'Portfolio Value (__PNPFM_SYMBOL__)' }, beginAtZero: false, ticks: { callback: function(value) { return '__PNPFM_SYMBOL__' + value.toFixed(2); } } }
				},
				plugins: {
					legend: { display: true, position: 'top' },
					tooltip: { callbacks: { label: function(context) { return '__PNPFM_SYMBOL__' + context.parsed.y.toFixed(2); } } }
				}
			}
		});
	}
	drawPortfolioChart();
});
PNPFM_JS;
			$_pnpfm_js = str_replace(
				['__PNPFM_DATA__', '__PNPFM_SYMBOL__'],
				[wp_json_encode($portfolio_chart_data), esc_js($symbol)],
				$_pnpfm_js
			);
			wp_add_inline_script('pn-personal-finance-manager-chartjs', $_pnpfm_js, 'after');
			?>
			<?php
		}
		return ob_get_clean();
	}

	/**
	 * Shortcode to display stock performance for a specific asset.
	 *
	 * @since    1.0.5
	 * @param    array     $atts      Shortcode attributes.
	 * @return   string                HTML output.
	 */
	public function pn_personal_finance_manager_stock_performance_shortcode($atts) {
		$atts = shortcode_atts([
			'asset_id' => 0,
			'days' => 30
		], $atts, 'pn_personal_finance_manager_stock_performance');
		
		$asset_id = intval($atts['asset_id']);
		$days = intval($atts['days']);
		
		if (!$asset_id) {
			return '<div class="pn-personal-finance-manager-stock-performance-error">' . 
				   '<p>' . __('Asset ID is required.', 'pn-personal-finance-manager') . '</p>' .
				   '</div>';
		}
		
		// Get asset data
		$asset = get_post($asset_id);
		if (!$asset || $asset->post_type !== 'pnpfm_asset') {
			return '<div class="pn-personal-finance-manager-stock-performance-error">' . 
				   '<p>' . __('Asset not found.', 'pn-personal-finance-manager') . '</p>' .
				   '</div>';
		}
		
		// Check if it's a stock asset
		$asset_type = get_post_meta($asset_id, 'pn_personal_finance_manager_asset_type', true);
		if ($asset_type !== 'stocks') {
			return '<div class="pn-personal-finance-manager-stock-performance-error">' . 
				   '<p>' . __('This asset is not a stock.', 'pn-personal-finance-manager') . '</p>' .
				   '</div>';
		}
		
		// Get stock symbol
		$symbol = get_post_meta($asset_id, 'pn_personal_finance_manager_stock_symbol', true);
		if (empty($symbol)) {
			return '<div class="pn-personal-finance-manager-stock-performance-error">' . 
				   '<p>' . __('No stock symbol found for this asset.', 'pn-personal-finance-manager') . '</p>' .
				   '</div>';
		}
		
		// Get purchase date and amount
		$purchase_date = get_post_meta($asset_id, 'pn_personal_finance_manager_asset_date', true);
		$total_amount = get_post_meta($asset_id, 'pn_personal_finance_manager_stock_total_amount', true);
		
		// Get or cache purchase price
		$purchase_price = get_post_meta($asset_id, 'pn_personal_finance_manager_stock_purchase_price', true);
		if (empty($purchase_price) && !empty($symbol) && !empty($purchase_date)) {
			$stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
			$purchase_price_data = $stocks->pn_personal_finance_manager_get_stock_price_for_date($symbol, $purchase_date, $asset->post_author);
			if ($purchase_price_data && !empty($purchase_price_data['price'])) {
				$purchase_price = $purchase_price_data['price'];
				update_post_meta($asset_id, 'pn_personal_finance_manager_stock_purchase_price', $purchase_price);
			}
		}
		
		// Get current stock data
		$stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
		$current_stock_data = $stocks->pn_personal_finance_manager_get_stock_data($symbol);
		
		// Get historical data - try to get from API first, then fall back to stored data
		$historical_data = $stocks->pn_personal_finance_manager_get_historical_stock_data($symbol, $days, $asset->post_author);
		if (empty($historical_data)) {
			$historical_data = $stocks->pn_personal_finance_manager_get_stock_price_history($symbol, $days, $asset->post_author);
		}
		
		// Calcular métricas usando cantidad y precio de compra
		$performance_metrics = $this->pn_personal_finance_manager_calculate_stock_performance_metrics_amount(
			$symbol,
			$purchase_date,
			$total_amount,
			$purchase_price,
			$current_stock_data,
			$historical_data,
			$asset->post_author
		);
		
		// Enqueue Chart.js
		wp_enqueue_script('pn-personal-finance-manager-chartjs', PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/chart.min.js', [], '4.5.1', true);

		// Enqueue PnPersonalFinanceManager Stocks JS
		wp_enqueue_script('pn-personal-finance-manager-stocks', PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/pn-personal-finance-manager-stocks.js', ['jquery', 'pn-personal-finance-manager-chartjs'], PN_PERSONAL_FINANCE_MANAGER_VERSION, true);
		
		// Localize scripts with global variables
		wp_localize_script('pn-personal-finance-manager-stocks', 'pn_personal_finance_manager_ajax', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'pn_personal_finance_manager_ajax_nonce' => wp_create_nonce('pn-personal-finance-manager-nonce'),
		]);

		wp_localize_script('pn-personal-finance-manager-stocks', 'pn_personal_finance_manager_i18n', [
			'an_error_has_occurred' => esc_html(__('An error has occurred. Please try again in a few minutes.', 'pn-personal-finance-manager')),
			'select_stock_symbol' => esc_html(__('Select a stock symbol', 'pn-personal-finance-manager')),
			'loading_stock_symbols' => esc_html(__('Loading stock symbols...', 'pn-personal-finance-manager')),
			'no_stock_symbols' => esc_html(__('No stock symbols available', 'pn-personal-finance-manager')),
			'error_loading_symbols' => esc_html(__('Error loading stock symbols', 'pn-personal-finance-manager')),
		]);
		
		$currency = get_option('pn_personal_finance_manager_currency', 'eur');
		$converted_total_invested = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($performance_metrics['total_invested'], $currency), $currency);
		$converted_current_total_value = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_convert_from_usd($performance_metrics['current_total_value'], $currency), $currency);
		$converted_profit_loss = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($performance_metrics['profit_loss'], $currency);
		$converted_total_return_percent = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($performance_metrics['total_return_percent'], $currency);
		
		ob_start();
		?>
		<div class="pn-personal-finance-manager-stock-performance-container" data-asset-id="<?php echo esc_attr($asset_id); ?>">
			<?php if ($performance_metrics): ?>
				<!-- Performance Metrics -->
				<div class="pn-personal-finance-manager-performance-metrics">
					<div class="pn-personal-finance-manager-metric-row">
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Shares', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value pn-personal-finance-manager-shares">
								<?php echo esc_html(number_format($performance_metrics['shares'], 2)); ?>
							</span>
						</div>
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Purchase Price', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value pn-personal-finance-manager-purchase-price">
								<?php echo esc_html(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($performance_metrics['purchase_price'], $currency)['full']); ?>
							</span>
						</div>
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Current Price', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value pn-personal-finance-manager-current-price">
								<?php echo esc_html(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_format_currency($performance_metrics['current_price'], $currency)['full']); ?>
							</span>
						</div>
					</div>
					<div class="pn-personal-finance-manager-metric-row">
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Invested', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value pn-personal-finance-manager-total-invested">
								<?php echo esc_html($converted_total_invested['full']); ?>
							</span>
						</div>
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Current Value', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value pn-personal-finance-manager-current-total-value">
								<?php echo esc_html($converted_current_total_value['full']); ?>
							</span>
						</div>
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Days Held', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value pn-personal-finance-manager-days-held">
								<?php echo intval($performance_metrics['days_held']); ?>
							</span>
						</div>
					</div>
					<div class="pn-personal-finance-manager-metric-row">
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Profit/Loss', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value pn-personal-finance-manager-profit-loss <?php echo $performance_metrics['profit_loss'] >= 0 ? 'positive' : 'negative'; ?>">
								<?php echo esc_html($converted_profit_loss['full']); ?>
							</span>
						</div>
						<div class="pn-personal-finance-manager-metric-item">
							<span class="pn-personal-finance-manager-metric-label"><?php esc_html_e('Total Return', 'pn-personal-finance-manager'); ?></span>
							<span class="pn-personal-finance-manager-metric-value pn-personal-finance-manager-total-return <?php echo $performance_metrics['total_return_percent'] >= 0 ? 'positive' : 'negative'; ?>">
								<?php echo esc_html($converted_total_return_percent['full']); ?>
							</span>
						</div>
						
					</div>
				</div>

				<!-- Performance Chart -->
				<?php if (!empty($historical_data)): ?>
					<div class="pn-personal-finance-manager-performance-chart">
						<h4><?php esc_html_e('Position Performance', 'pn-personal-finance-manager'); ?></h4>
						<div class="pn-personal-finance-manager-chart-container">
							<canvas id="pn-personal-finance-manager-chart-<?php echo esc_attr($asset_id); ?>" width="400" height="200"></canvas>
						</div>
					</div>
					<?php
					$_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	window.pn_personal_finance_manager_shares = window.pn_personal_finance_manager_shares || {};
	window.pn_personal_finance_manager_shares[__PNPFM_ASSET_ID__] = __PNPFM_SHARES__;
	function initChart() {
		if (typeof Chart !== 'undefined' && typeof window.pn_personal_finance_manager_init_performance_chart === 'function') {
			window.pn_personal_finance_manager_init_performance_chart(__PNPFM_ASSET_ID__, __PNPFM_HISTORICAL_DATA__);
			return true;
		}
		return false;
	}
	if (!initChart()) {
		setTimeout(function() {
			if (!initChart()) {
				setTimeout(function() {
					initChart();
				}, 2000);
			}
		}, 1000);
	}
});
PNPFM_JS;
					$_pnpfm_js = str_replace(
						['__PNPFM_ASSET_ID__', '__PNPFM_SHARES__', '__PNPFM_HISTORICAL_DATA__'],
						[intval($asset_id), wp_json_encode($performance_metrics['shares']), wp_json_encode($historical_data)],
						$_pnpfm_js
					);
					wp_add_inline_script('pn-personal-finance-manager-chartjs', $_pnpfm_js, 'after');
					?>
				<?php else: ?>
					<div class="pn-personal-finance-manager-no-chart-data">
						<p><?php esc_html_e('No historical data available for chart display.', 'pn-personal-finance-manager'); ?></p>
					</div>
				<?php endif; ?>
				
			<?php else: ?>
				<div class="pn-personal-finance-manager-performance-error">
					<p><?php esc_html_e('Unable to calculate performance metrics. Please check:', 'pn-personal-finance-manager'); ?></p>
					<ul>
						<li><?php esc_html_e('• Stock symbol is configured', 'pn-personal-finance-manager'); ?></li>
						<li><?php esc_html_e('• Purchase date is set', 'pn-personal-finance-manager'); ?></li>
						<li><?php esc_html_e('• Total amount (shares) is entered', 'pn-personal-finance-manager'); ?></li>
						<li><?php esc_html_e('• Alpha Vantage API is working', 'pn-personal-finance-manager'); ?></li>
					</ul>
					<p><strong><?php esc_html_e('Debug Info:', 'pn-personal-finance-manager'); ?></strong></p>
					<ul>
						<li><?php esc_html_e('Symbol:', 'pn-personal-finance-manager'); ?> <?php echo esc_html($symbol ?? 'Not set'); ?></li>
						<li><?php esc_html_e('Purchase Date:', 'pn-personal-finance-manager'); ?> <?php echo esc_html($purchase_date ?? 'Not set'); ?></li>
						<li><?php esc_html_e('Total Amount:', 'pn-personal-finance-manager'); ?> <?php echo esc_html($total_amount ?? 'Not set'); ?></li>
						<li><?php esc_html_e('Purchase Price:', 'pn-personal-finance-manager'); ?> <?php echo esc_html($purchase_price ?? 'Not set'); ?></li>
						<li><?php esc_html_e('Current Data:', 'pn-personal-finance-manager'); ?> <?php echo !empty($current_stock_data) ? 'Available' : 'Not available'; ?></li>
					</ul>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Calculate stock performance metrics.
	 *
	 * @since    1.0.5
	 * @param    string    $symbol           Stock symbol.
	 * @param    string    $purchase_date    Purchase date.
	 * @param    float     $purchase_value   Total purchase value.
	 * @param    array     $current_data     Current stock data.
	 * @param    array     $historical_data  Historical price data.
	 * @param    int       $user_id          User ID.
	 * @return   array|false                 Performance metrics or false on failure.
	 */
	private function pn_personal_finance_manager_calculate_stock_performance_metrics_amount($symbol, $purchase_date, $total_amount, $purchase_price, $current_data, $historical_data, $user_id) {
		// Validar datos requeridos
		if (empty($current_data)) {
			return false;
		}
		if (empty($purchase_date)) {
			return false;
		}
		if (empty($total_amount) || floatval($total_amount) <= 0) {
			return false;
		}
		// Si no hay precio de compra, intentar obtenerlo de la API
		if (empty($purchase_price) && !empty($symbol) && !empty($purchase_date)) {
			$stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
			$purchase_price_data = $stocks->pn_personal_finance_manager_get_stock_price_for_date($symbol, $purchase_date, $user_id);
			if ($purchase_price_data && !empty($purchase_price_data['price'])) {
				$purchase_price = floatval($purchase_price_data['price']);
			} else {
				$purchase_price = floatval($current_data['price']);
			}
		}
		if (empty($purchase_price) || floatval($purchase_price) <= 0) {
			return false;
		}
		// Buscar el precio de compra en el histórico
		$purchase_price_historical = null;
		if (!empty($historical_data) && is_array($historical_data) && !empty($purchase_date)) {
			$purchase_timestamp = strtotime($purchase_date);
			$closest = null;
			foreach ($historical_data as $row) {
				if (empty($row['recorded_date']) || !isset($row['price'])) continue;
				$row_timestamp = strtotime($row['recorded_date']);
				if ($row_timestamp == $purchase_timestamp) {
					$purchase_price_historical = floatval($row['price']);
					break;
				}
				// Busca el más cercano anterior
				if ($row_timestamp < $purchase_timestamp) {
					if ($closest === null || $row_timestamp > strtotime($closest['recorded_date'])) {
						$closest = $row;
					}
				}
			}
			if ($purchase_price_historical === null && $closest !== null) {
				$purchase_price_historical = floatval($closest['price']);
			}
			// Si no hay anterior, busca el más cercano posterior
			if ($purchase_price_historical === null) {
				$closest_after = null;
				foreach ($historical_data as $row) {
					if (empty($row['recorded_date']) || !isset($row['price'])) continue;
					$row_timestamp = strtotime($row['recorded_date']);
					if ($row_timestamp > $purchase_timestamp) {
						if ($closest_after === null || $row_timestamp < strtotime($closest_after['recorded_date'])) {
							$closest_after = $row;
						}
					}
				}
				if ($closest_after !== null) {
					$purchase_price_historical = floatval($closest_after['price']);
				}
			}
		}
		if ($purchase_price_historical !== null) {
			$purchase_price = $purchase_price_historical;
		}
		// Buscar el precio más reciente del histórico
		$current_price = null;
		if (!empty($historical_data) && is_array($historical_data)) {
			$latest = null;
			foreach ($historical_data as $row) {
				if (empty($row['recorded_date']) || !isset($row['price'])) continue;
				if ($latest === null || strtotime($row['recorded_date']) > strtotime($latest['recorded_date'])) {
					$latest = $row;
				}
			}
			if ($latest && isset($latest['price'])) {
				$current_price = floatval($latest['price']);
			}
		}
		if ($current_price === null) {
			$current_price = floatval($current_data['price']);
		}
		$shares = floatval($total_amount);
		$total_invested = $shares * $purchase_price;
		$current_total_value = $shares * $current_price;
		$profit_loss = $current_total_value - $total_invested;
		$total_return_percent = $total_invested > 0 ? (($current_total_value - $total_invested) / $total_invested) * 100 : 0;
		$purchase_timestamp = strtotime($purchase_date);
		$current_timestamp = current_time('timestamp');
		$days_held = $purchase_timestamp ? floor(($current_timestamp - $purchase_timestamp) / (60 * 60 * 24)) : 0;

		return [
			'current_price' => $current_price,
			'purchase_price' => $purchase_price,
			'total_invested' => $total_invested,
			'shares' => $shares,
			'current_total_value' => $current_total_value,
			'profit_loss' => $profit_loss,
			'total_return_percent' => $total_return_percent,
			'days_held' => $days_held
		];
	}
}

add_action('init', function() {
    if (!shortcode_exists('pn_personal_finance_manager_user_assets')) {
        add_shortcode('pn_personal_finance_manager_user_assets', [new PN_PERSONAL_FINANCE_MANAGER_Shortcodes(), 'pn_personal_finance_manager_user_assets_shortcode']);
    }
});