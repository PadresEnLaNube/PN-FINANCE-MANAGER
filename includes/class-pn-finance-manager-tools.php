<?php
/**
 * The Tools admin page for cache diagnostics and actions.
 *
 * @since      1.0.13
 * @package    PN_FINANCE_MANAGER
 * @subpackage PN_FINANCE_MANAGER/includes
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_FINANCE_MANAGER_Tools {

	/**
	 * Initialize the class.
	 *
	 * @since    1.0.13
	 */
	public function __construct() {
		add_action('wp_ajax_pn_finance_manager_tools_force_update', [$this, 'pn_finance_manager_tools_force_update']);
		add_action('wp_ajax_pn_finance_manager_tools_load_popular', [$this, 'pn_finance_manager_tools_load_popular']);
		add_action('wp_ajax_pn_finance_manager_tools_clear_cache', [$this, 'pn_finance_manager_tools_clear_cache']);
	}

	/**
	 * Register the Tools submenu page.
	 *
	 * @since    1.0.13
	 */
	public function pn_finance_manager_tools_menu() {
		add_submenu_page(
			'pn_finance_manager_options',
			esc_html__('Tools', 'pn-finance-manager'),
			esc_html__('Tools', 'pn-finance-manager'),
			'manage_options',
			'pn-finance-manager-tools',
			[$this, 'pn_finance_manager_tools_page']
		);
	}

	/**
	 * Render the Tools page with tabs.
	 *
	 * @since    1.0.13
	 */
	public function pn_finance_manager_tools_page() {
		$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'cache-status';
		$tabs = [
			'cache-status'  => __('Cache Status', 'pn-finance-manager'),
			'cache-actions'	=> __('Cache Actions', 'pn-finance-manager'),
			'diagnostics'   => __('Diagnostics', 'pn-finance-manager'),
		];
		?>
		<div class="wrap">
			<h1><?php esc_html_e('Finance Manager - Tools', 'pn-finance-manager'); ?></h1>

			<nav class="nav-tab-wrapper">
				<?php foreach ($tabs as $slug => $label) : ?>
					<a href="<?php echo esc_url(add_query_arg(['page' => 'pn-finance-manager-tools', 'tab' => $slug], admin_url('admin.php'))); ?>"
					   class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>">
						<?php echo esc_html($label); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<div class="pn-finance-manager-tools-content" style="margin-top:20px;">
				<?php
				switch ($active_tab) {
					case 'cache-actions':
						$this->pn_finance_manager_tools_tab_cache_actions();
						break;
					case 'diagnostics':
						$this->pn_finance_manager_tools_tab_diagnostics();
						break;
					default:
						$this->pn_finance_manager_tools_tab_cache_status();
						break;
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Tab 1 - Cache Status.
	 *
	 * @since    1.0.13
	 */
	private function pn_finance_manager_tools_tab_cache_status() {
		$cached_symbols = get_option('pn_finance_manager_stock_symbols_cache', []);
		$last_update    = get_option('pn_finance_manager_stock_symbols_last_update', 0);
		$cache_expiry   = get_option('pn_finance_manager_stock_symbols_cache_expiry', 24 * HOUR_IN_SECONDS);
		$total_symbols  = is_array($cached_symbols) ? count($cached_symbols) : 0;
		$is_valid       = $total_symbols > 0 && ($last_update + $cache_expiry) > current_time('timestamp');
		$cache_size_mb  = round(strlen(serialize($cached_symbols)) / 1024 / 1024, 2);
		?>
		<h2><?php esc_html_e('Cache Overview', 'pn-finance-manager'); ?></h2>
		<table class="widefat striped" style="max-width:600px;">
			<tbody>
				<tr>
					<td><strong><?php esc_html_e('Total symbols', 'pn-finance-manager'); ?></strong></td>
					<td><?php echo number_format($total_symbols); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Last update', 'pn-finance-manager'); ?></strong></td>
					<td>
						<?php
						if ($last_update) {
							echo esc_html(date_i18n(get_option('date_format') . ' @ ' . get_option('time_format'), $last_update));
							echo ' (' . esc_html(human_time_diff($last_update, current_time('timestamp'))) . ' ' . esc_html__('ago', 'pn-finance-manager') . ')';
						} else {
							esc_html_e('Never', 'pn-finance-manager');
						}
						?>
					</td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Cache status', 'pn-finance-manager'); ?></strong></td>
					<td>
						<span style="color:<?php echo $is_valid ? 'green' : 'red'; ?>; font-weight:bold;">
							<?php echo $is_valid ? esc_html__('Valid', 'pn-finance-manager') : esc_html__('Expired', 'pn-finance-manager'); ?>
						</span>
					</td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Expiry', 'pn-finance-manager'); ?></strong></td>
					<td>
						<?php
						if ($last_update) {
							echo esc_html(date_i18n(get_option('date_format') . ' @ ' . get_option('time_format'), $last_update + $cache_expiry));
						} else {
							esc_html_e('N/A', 'pn-finance-manager');
						}
						?>
					</td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Cache size', 'pn-finance-manager'); ?></strong></td>
					<td><?php echo esc_html($cache_size_mb); ?> MB</td>
				</tr>
			</tbody>
		</table>

		<?php if ($total_symbols > 0) : ?>
			<h2 style="margin-top:30px;"><?php esc_html_e('Symbol Distribution by First Letter', 'pn-finance-manager'); ?></h2>
			<?php
			$letter_count = [];
			foreach ($cached_symbols as $symbol => $name) {
				$first_letter = strtoupper(substr($symbol, 0, 1));
				if (!isset($letter_count[$first_letter])) {
					$letter_count[$first_letter] = 0;
				}
				$letter_count[$first_letter]++;
			}
			ksort($letter_count);

			$alphabet       = range('A', 'Z');
			$missing_letters = array_diff($alphabet, array_keys($letter_count));
			$last_letter     = !empty($letter_count) ? array_key_last($letter_count) : '';
			?>

			<?php if (!empty($missing_letters)) : ?>
				<div class="notice notice-warning inline" style="max-width:600px;">
					<p>
						<strong><?php esc_html_e('Missing letters:', 'pn-finance-manager'); ?></strong>
						<?php echo esc_html(implode(', ', $missing_letters)); ?>
					</p>
					<?php if ($last_letter === 'S') : ?>
						<p><?php esc_html_e('Symbols appear to end at letter S - possible truncation detected.', 'pn-finance-manager'); ?></p>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="notice notice-success inline" style="max-width:600px;">
					<p><?php esc_html_e('All letters A-Z are present.', 'pn-finance-manager'); ?></p>
				</div>
			<?php endif; ?>

			<table class="widefat striped" style="max-width:600px;">
				<thead>
					<tr>
						<th><?php esc_html_e('Letter', 'pn-finance-manager'); ?></th>
						<th><?php esc_html_e('Count', 'pn-finance-manager'); ?></th>
						<th><?php esc_html_e('Percentage', 'pn-finance-manager'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($letter_count as $letter => $count) : ?>
						<tr>
							<td><strong><?php echo esc_html($letter); ?></strong></td>
							<td><?php echo number_format($count); ?></td>
							<td><?php echo number_format(($count / $total_symbols) * 100, 2); ?>%</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<div class="notice notice-warning inline" style="max-width:600px; margin-top:20px;">
				<p><?php esc_html_e('No symbols found in cache. Use the Cache Actions tab to populate it.', 'pn-finance-manager'); ?></p>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Tab 2 - Cache Actions.
	 *
	 * @since    1.0.13
	 */
	private function pn_finance_manager_tools_tab_cache_actions() {
		$nonce = wp_create_nonce('pn-finance-manager-tools-nonce');
		?>
		<h2><?php esc_html_e('Cache Actions', 'pn-finance-manager'); ?></h2>
		<p><?php esc_html_e('Use these buttons to manage the stock symbols cache. Actions are executed via AJAX.', 'pn-finance-manager'); ?></p>

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e('Force Update from API', 'pn-finance-manager'); ?></th>
				<td>
					<button type="button" class="button button-primary" id="pn-finance-manager-tools-force-update">
						<?php esc_html_e('Force Update', 'pn-finance-manager'); ?>
					</button>
					<p class="description"><?php esc_html_e('Clears existing cache and fetches fresh symbols from the configured API provider.', 'pn-finance-manager'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e('Load Popular Symbols', 'pn-finance-manager'); ?></th>
				<td>
					<button type="button" class="button" id="pn-finance-manager-tools-load-popular">
						<?php esc_html_e('Load Popular', 'pn-finance-manager'); ?>
					</button>
					<p class="description"><?php esc_html_e('Loads a curated list of ~20 popular stock symbols as a fallback (no API key required).', 'pn-finance-manager'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e('Clear Cache', 'pn-finance-manager'); ?></th>
				<td>
					<button type="button" class="button" id="pn-finance-manager-tools-clear-cache">
						<?php esc_html_e('Clear Cache', 'pn-finance-manager'); ?>
					</button>
					<p class="description"><?php esc_html_e('Removes all cached symbol data. The cache will be rebuilt on next access.', 'pn-finance-manager'); ?></p>
				</td>
			</tr>
		</table>

		<div id="pn-finance-manager-tools-result" style="margin-top:20px;"></div>

		<script>
		(function($) {
			var nonce = <?php echo wp_json_encode($nonce); ?>;

			function toolsAjax(action, buttonEl) {
				var $btn = $(buttonEl);
				var $result = $('#pn-finance-manager-tools-result');

				$btn.prop('disabled', true).text('<?php echo esc_js(__('Processing...', 'pn-finance-manager')); ?>');
				$result.html('<p><em><?php echo esc_js(__('Please wait...', 'pn-finance-manager')); ?></em></p>');

				$.post(ajaxurl, {
					action: action,
					_ajax_nonce: nonce
				}, function(response) {
					if (response.success) {
						$result.html('<div class="notice notice-success inline"><p>' + response.data + '</p></div>');
					} else {
						$result.html('<div class="notice notice-error inline"><p>' + response.data + '</p></div>');
					}
				}).fail(function() {
					$result.html('<div class="notice notice-error inline"><p><?php echo esc_js(__('Request failed. Please try again.', 'pn-finance-manager')); ?></p></div>');
				}).always(function() {
					$btn.prop('disabled', false);
					// Restore button text
					if (action.indexOf('force_update') !== -1) $btn.text('<?php echo esc_js(__('Force Update', 'pn-finance-manager')); ?>');
					else if (action.indexOf('load_popular') !== -1) $btn.text('<?php echo esc_js(__('Load Popular', 'pn-finance-manager')); ?>');
					else $btn.text('<?php echo esc_js(__('Clear Cache', 'pn-finance-manager')); ?>');
				});
			}

			$('#pn-finance-manager-tools-force-update').on('click', function() {
				toolsAjax('pn_finance_manager_tools_force_update', this);
			});
			$('#pn-finance-manager-tools-load-popular').on('click', function() {
				toolsAjax('pn_finance_manager_tools_load_popular', this);
			});
			$('#pn-finance-manager-tools-clear-cache').on('click', function() {
				toolsAjax('pn_finance_manager_tools_clear_cache', this);
			});
		})(jQuery);
		</script>
		<?php
	}

	/**
	 * Tab 3 - Diagnostics.
	 *
	 * @since    1.0.13
	 */
	private function pn_finance_manager_tools_tab_diagnostics() {
		$api_enabled  = get_option('pn_finance_manager_stocks_api_enabled', '');
		$api_provider = 'Twelve Data';
		$api_key      = get_option('pn_finance_manager_stocks_api_key', '');
		?>

		<h2><?php esc_html_e('API Configuration', 'pn-finance-manager'); ?></h2>
		<table class="widefat striped" style="max-width:600px;">
			<tbody>
				<tr>
					<td><strong><?php esc_html_e('API Enabled', 'pn-finance-manager'); ?></strong></td>
					<td>
						<span style="color:<?php echo $api_enabled === 'on' ? 'green' : 'red'; ?>; font-weight:bold;">
							<?php echo $api_enabled === 'on' ? esc_html__('Yes', 'pn-finance-manager') : esc_html__('No', 'pn-finance-manager'); ?>
						</span>
					</td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('API Provider', 'pn-finance-manager'); ?></strong></td>
					<td><?php echo esc_html($api_provider); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('API Key', 'pn-finance-manager'); ?></strong></td>
					<td>
						<?php
						if ($api_key) {
							echo esc_html(substr($api_key, 0, 4) . str_repeat('*', max(0, strlen($api_key) - 8)) . substr($api_key, -4));
						} else {
							esc_html_e('Not set', 'pn-finance-manager');
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>

		<?php
		$cached_symbols = get_option('pn_finance_manager_stock_symbols_cache', []);
		$last_update    = get_option('pn_finance_manager_stock_symbols_last_update', 0);
		$cache_expiry   = get_option('pn_finance_manager_stock_symbols_cache_expiry', 24 * HOUR_IN_SECONDS);
		$total_symbols  = is_array($cached_symbols) ? count($cached_symbols) : 0;
		$is_valid       = $total_symbols > 0 && ($last_update + $cache_expiry) > current_time('timestamp');
		$age_seconds    = $last_update ? current_time('timestamp') - $last_update : 0;
		?>

		<h2 style="margin-top:30px;"><?php esc_html_e('Cache Health', 'pn-finance-manager'); ?></h2>
		<table class="widefat striped" style="max-width:600px;">
			<tbody>
				<tr>
					<td><strong><?php esc_html_e('Status', 'pn-finance-manager'); ?></strong></td>
					<td>
						<span style="color:<?php echo $is_valid ? 'green' : 'red'; ?>; font-weight:bold;">
							<?php echo $is_valid ? esc_html__('Valid', 'pn-finance-manager') : esc_html__('Expired / Empty', 'pn-finance-manager'); ?>
						</span>
					</td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Symbols count', 'pn-finance-manager'); ?></strong></td>
					<td><?php echo number_format($total_symbols); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Cache age', 'pn-finance-manager'); ?></strong></td>
					<td>
						<?php
						if ($last_update) {
							echo esc_html(human_time_diff($last_update, current_time('timestamp')));
						} else {
							esc_html_e('N/A', 'pn-finance-manager');
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>

		<h2 style="margin-top:30px;"><?php esc_html_e('Cron Schedule', 'pn-finance-manager'); ?></h2>
		<table class="widefat striped" style="max-width:600px;">
			<tbody>
				<?php
				$cron_hooks = [
					'pn_finance_manager_update_stock_symbols_event' => __('Weekly symbol update', 'pn-finance-manager'),
					'pn_finance_manager_update_stock_symbols_cron'  => __('Daily symbol update', 'pn-finance-manager'),
					'pn_finance_manager_daily_stock_price_recording_event' => __('Daily price recording', 'pn-finance-manager'),
				];
				foreach ($cron_hooks as $hook => $label) :
					$next = wp_next_scheduled($hook);
					?>
					<tr>
						<td><strong><?php echo esc_html($label); ?></strong></td>
						<td>
							<?php
							if ($next) {
								echo esc_html(date_i18n(get_option('date_format') . ' @ ' . get_option('time_format'), $next));
								echo ' (' . esc_html(human_time_diff(current_time('timestamp'), $next)) . ')';
							} else {
								echo '<span style="color:orange;">' . esc_html__('Not scheduled', 'pn-finance-manager') . '</span>';
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2 style="margin-top:30px;"><?php esc_html_e('Class Availability', 'pn-finance-manager'); ?></h2>
		<table class="widefat striped" style="max-width:600px;">
			<tbody>
				<?php
				$classes = [
					'PN_FINANCE_MANAGER_Stocks',
					'PN_FINANCE_MANAGER_Ajax',
					'PN_FINANCE_MANAGER_Common',
					'PN_FINANCE_MANAGER_Forms',
				];
				foreach ($classes as $class) :
					$exists = class_exists($class);
					?>
					<tr>
						<td><strong><?php echo esc_html($class); ?></strong></td>
						<td>
							<span style="color:<?php echo $exists ? 'green' : 'red'; ?>; font-weight:bold;">
								<?php echo $exists ? esc_html__('Loaded', 'pn-finance-manager') : esc_html__('Missing', 'pn-finance-manager'); ?>
							</span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * AJAX: Force update stock symbols from API.
	 *
	 * @since    1.0.13
	 */
	public function pn_finance_manager_tools_force_update() {
		check_ajax_referer('pn-finance-manager-tools-nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('Permission denied.', 'pn-finance-manager'));
		}

		$stocks = new PN_FINANCE_MANAGER_Stocks();
		$result = $stocks->pn_finance_manager_force_update_stock_symbols_cache();

		if ($result && is_array($result)) {
			wp_send_json_success(
				sprintf(
					/* translators: %s: number of symbols */
					__('Cache updated successfully. Total symbols: %s', 'pn-finance-manager'),
					number_format(count($result))
				)
			);
		} else {
			wp_send_json_error(__('Cache update failed. Check that the API is configured and enabled in Settings.', 'pn-finance-manager'));
		}
	}

	/**
	 * AJAX: Load popular symbols as fallback.
	 *
	 * @since    1.0.13
	 */
	public function pn_finance_manager_tools_load_popular() {
		check_ajax_referer('pn-finance-manager-tools-nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('Permission denied.', 'pn-finance-manager'));
		}

		$stocks     = new PN_FINANCE_MANAGER_Stocks();
		$reflection = new ReflectionClass($stocks);
		$method     = $reflection->getMethod('pn_finance_manager_get_popular_symbols');
		$method->setAccessible(true);
		$popular_symbols = $method->invoke($stocks);

		if (is_array($popular_symbols) && !empty($popular_symbols)) {
			update_option('pn_finance_manager_stock_symbols_cache', $popular_symbols, false);
			update_option('pn_finance_manager_stock_symbols_last_update', current_time('timestamp'), false);
			update_option('pn_finance_manager_stock_symbols_cache_expiry', 24 * HOUR_IN_SECONDS, false);

			wp_send_json_success(
				sprintf(
					/* translators: %d: number of symbols */
					__('Popular symbols loaded successfully. Total: %d', 'pn-finance-manager'),
					count($popular_symbols)
				)
			);
		} else {
			wp_send_json_error(__('Failed to load popular symbols.', 'pn-finance-manager'));
		}
	}

	/**
	 * AJAX: Clear cache.
	 *
	 * @since    1.0.13
	 */
	public function pn_finance_manager_tools_clear_cache() {
		check_ajax_referer('pn-finance-manager-tools-nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('Permission denied.', 'pn-finance-manager'));
		}

		delete_option('pn_finance_manager_stock_symbols_cache');
		delete_option('pn_finance_manager_stock_symbols_last_update');
		delete_option('pn_finance_manager_stock_symbols_cache_expiry');

		wp_send_json_success(__('Cache cleared successfully.', 'pn-finance-manager'));
	}
}
