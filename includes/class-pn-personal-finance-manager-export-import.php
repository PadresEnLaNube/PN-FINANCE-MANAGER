<?php
/**
 * Export/Import portfolio functionality.
 *
 * Provides export and import capabilities for user portfolios
 * via the userspn profile popup tab.
 *
 * @link       padresenlanube.com/
 * @since      1.0.19
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Export_Import {

	/**
	 * Add the Portfolio tab to the userspn profile popup.
	 *
	 * Callback for the 'userspn_profile_content' filter.
	 *
	 * @since    1.0.19
	 * @param    string    $content    The profile popup HTML content.
	 * @param    int       $user_id    The user ID.
	 * @return   string                Modified content with the Portfolio tab injected.
	 */
	public function pn_personal_finance_manager_add_profile_tab($content, $user_id) {
		if (!is_user_logged_in()) {
			return $content;
		}

		// Tab link: plain text, no icon
		$tab_link = '<div class="userspn-tab-links" data-userspn-id="userspn-tab-portfolio">'
			. esc_html__('Portfolio', 'pn-personal-finance-manager')
			. '</div>';

		// Inject tab link inside .userspn-tabs: before the </div> that precedes the first tab content panel
		$content = preg_replace(
			'/(<\/div>\s*)(<div\s+id="userspn-tab-)/s',
			$tab_link . '$1$2',
			$content,
			1
		);

		// Build tab content
		$tab_content = $this->pn_personal_finance_manager_render_tab_content($user_id);
		$tab_content_wrapper = '<div id="userspn-tab-portfolio" class="userspn-tab-content userspn-display-none">'
			. $tab_content
			. '</div>';

		// Inject tab content inside userspn-tabs-wrapper using DOM-aware string parsing
		$tabs_wrapper_pos = strpos($content, 'userspn-tabs-wrapper');
		if ($tabs_wrapper_pos !== false) {
			// Find the opening > of the tabs-wrapper div
			$start_pos = strpos($content, '>', $tabs_wrapper_pos);
			if ($start_pos !== false) {
				$start_pos++;
				// Walk through the HTML counting div depth to find the matching closing </div>
				$depth = 1;
				$pos = $start_pos;
				$len = strlen($content);
				while ($pos < $len && $depth > 0) {
					$next_open = strpos($content, '<div', $pos);
					$next_close = strpos($content, '</div>', $pos);
					if ($next_close === false) {
						break;
					}
					if ($next_open !== false && $next_open < $next_close) {
						$depth++;
						$pos = $next_open + 4;
					} else {
						$depth--;
						if ($depth === 0) {
							// Insert tab content just before the closing </div> of tabs-wrapper
							$content = substr($content, 0, $next_close) . $tab_content_wrapper . substr($content, $next_close);
						}
						$pos = $next_close + 6;
					}
				}
			}
		}

		return $content;
	}

	/**
	 * Render the HTML content for the Portfolio export/import tab.
	 *
	 * @since    1.0.19
	 * @param    int       $user_id    The user ID.
	 * @return   string                The tab HTML content.
	 */
	/**
	 * Get user alert preferences with defaults.
	 *
	 * @since    1.1.0
	 * @param    int    $user_id    The user ID.
	 * @return   array              Alert preferences.
	 */
	public static function pn_personal_finance_manager_get_alert_preferences($user_id) {
		$defaults = [
			'default_threshold'        => 10,
			'alerts_assets_enabled'    => true,
			'alerts_watchlist_enabled' => true,
		];

		// Try individual keys first (new format)
		$threshold = get_user_meta($user_id, 'pn_personal_finance_manager_default_threshold', true);
		if ($threshold !== '') {
			return [
				'default_threshold'        => max(1, min(50, intval($threshold))),
				'alerts_assets_enabled'    => get_user_meta($user_id, 'pn_personal_finance_manager_alerts_assets_enabled', true) === 'on',
				'alerts_watchlist_enabled' => get_user_meta($user_id, 'pn_personal_finance_manager_alerts_watchlist_enabled', true) === 'on',
			];
		}

		// Fallback to old array format
		$prefs = get_user_meta($user_id, 'pn_personal_finance_manager_alert_preferences', true);
		if (is_array($prefs)) {
			return wp_parse_args($prefs, $defaults);
		}

		return $defaults;
	}

	private function pn_personal_finance_manager_render_tab_content($user_id) {
		$global_currency = strtolower(get_option('pn_personal_finance_manager_currency', 'eur'));
		$currencies = PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_currencies();

		$currency_options = [];
		foreach ($currencies as $code => $label) {
			$currency_options[$code] = wp_strip_all_tags($label);
		}

		$fields = [
			'pn_personal_finance_manager_user_currency' => [
				'id'          => 'pn_personal_finance_manager_user_currency',
				'input'       => 'select',
				'class'       => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
				'label'       => __('Display Currency', 'pn-personal-finance-manager'),
				'description' => __('Choose the currency used to display your investments and portfolio values.', 'pn-personal-finance-manager'),
				'placeholder' => sprintf(
					/* translators: %s: default currency label */
					__('Use site default (%s)', 'pn-personal-finance-manager'),
					isset($currencies[$global_currency]) ? wp_strip_all_tags($currencies[$global_currency]) : strtoupper($global_currency)
				),
				'options'     => $currency_options,
			],
			'pn_personal_finance_manager_number_format' => [
				'id'          => 'pn_personal_finance_manager_number_format',
				'input'       => 'select',
				'class'       => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
				'label'       => __('Number Format', 'pn-personal-finance-manager'),
				'description' => __('Choose how numbers are displayed (decimal and thousands separators).', 'pn-personal-finance-manager'),
				'options'     => [
					'dot_comma'   => '1,234.56',
					'comma_dot'   => '1.234,56',
					'space_comma' => '1 234,56',
				],
				'value'       => 'dot_comma',
			],
			'pn_personal_finance_manager_comparison_period' => [
				'id'          => 'pn_personal_finance_manager_comparison_period',
				'input'       => 'select',
				'class'       => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
				'label'       => __('Comparison Period', 'pn-personal-finance-manager'),
				'description' => __('Choose the time period for price comparison displayed on your investment cards.', 'pn-personal-finance-manager'),
				'options'     => [
					'daily'          => __('Daily', 'pn-personal-finance-manager'),
					'weekly'         => __('Weekly (7d)', 'pn-personal-finance-manager'),
					'monthly'        => __('Monthly (30d)', 'pn-personal-finance-manager'),
					'yearly'         => __('Yearly (365d)', 'pn-personal-finance-manager'),
					'since_purchase' => __('Since purchase', 'pn-personal-finance-manager'),
				],
				'value'       => 'daily',
			],
			'pn_personal_finance_manager_alerts_assets_enabled' => [
				'id'    => 'pn_personal_finance_manager_alerts_assets_enabled',
				'input' => 'input',
				'type'  => 'checkbox',
				'label' => __('Enable alerts on my assets', 'pn-personal-finance-manager'),
				'value' => 'on',
			],
			'pn_personal_finance_manager_alerts_watchlist_enabled' => [
				'id'    => 'pn_personal_finance_manager_alerts_watchlist_enabled',
				'input' => 'input',
				'type'  => 'checkbox',
				'label' => __('Enable alerts on watchlist items', 'pn-personal-finance-manager'),
				'value' => 'on',
			],
			'pn_personal_finance_manager_default_threshold' => [
				'id'                     => 'pn_personal_finance_manager_default_threshold',
				'input'                  => 'input',
				'type'                   => 'range',
				'label'                  => __('Default alert threshold', 'pn-personal-finance-manager'),
				'pn_personal_finance_manager_min' => 1,
				'pn_personal_finance_manager_max' => 50,
				'value'                  => 10,
			],
			'pn_personal_finance_manager_cartera_submit' => [
				'id'    => 'pn_personal_finance_manager_cartera_submit',
				'input' => 'input',
				'type'  => 'submit',
				'value' => __('Save preferences', 'pn-personal-finance-manager'),
			],
		];

		ob_start();
		?>
		<div class="pn-personal-finance-manager-export-import-container">
			<form action="" method="post" id="pn-personal-finance-manager-form-cartera" class="pn-personal-finance-manager-form pn-personal-finance-manager-p-30">
				<?php
				foreach ($fields as $field) {
					PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_wrapper_builder($field, 'user', $user_id, 0, 'full');
				}
				?>
			</form>

			<!-- Export Section -->
			<div class="pn-personal-finance-manager-ei-section">
				<h4>
					<span class="material-icons-outlined" style="font-size:20px;vertical-align:middle;margin-right:6px;">file_download</span>
					<?php echo esc_html__('Export Portfolio', 'pn-personal-finance-manager'); ?>
				</h4>
				<p class="pn-personal-finance-manager-ei-description">
					<?php echo esc_html__('Download a backup of your assets, liabilities and watchlist as a JSON file.', 'pn-personal-finance-manager'); ?>
				</p>
				<button type="button" id="pn-personal-finance-manager-export-btn" class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini">
					<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;">download</span>
					<?php echo esc_html__('Export Portfolio', 'pn-personal-finance-manager'); ?>
				</button>
				<div id="pn-personal-finance-manager-export-status" class="pn-personal-finance-manager-ei-status" style="display:none;"></div>
			</div>

			<!-- Import Section -->
			<div class="pn-personal-finance-manager-ei-section">
				<h4>
					<span class="material-icons-outlined" style="font-size:20px;vertical-align:middle;margin-right:6px;">file_upload</span>
					<?php echo esc_html__('Import Portfolio', 'pn-personal-finance-manager'); ?>
				</h4>
				<p class="pn-personal-finance-manager-ei-description">
					<?php echo esc_html__('Import assets, liabilities and watchlist from a previously exported JSON file. Existing data will not be overwritten.', 'pn-personal-finance-manager'); ?>
				</p>
				<div class="pn-personal-finance-manager-ei-file-wrapper">
					<label for="pn-personal-finance-manager-import-file" class="pn-personal-finance-manager-ei-file-label">
						<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;">attach_file</span>
						<span id="pn-personal-finance-manager-import-file-name"><?php echo esc_html__('No file selected', 'pn-personal-finance-manager'); ?></span>
					</label>
					<input type="file" id="pn-personal-finance-manager-import-file" accept=".json" style="display:none;" />
				</div>
				<button type="button" id="pn-personal-finance-manager-import-btn" class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini" style="margin-top:10px;">
					<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;">upload</span>
					<?php echo esc_html__('Import Portfolio', 'pn-personal-finance-manager'); ?>
				</button>
				<div id="pn-personal-finance-manager-import-status" class="pn-personal-finance-manager-ei-status" style="display:none;"></div>
			</div>
		</div>
		<?php
		$_pnpfm_js = <<<'PNPFM_JS'
(function($) {
	'use strict';

	$('#pn-personal-finance-manager-export-btn').on('click', function(e) {
		e.preventDefault();
		var $btn = $(this);
		var $status = $('#pn-personal-finance-manager-export-status');
		var originalText = $btn.html();

		$btn.prop('disabled', true).html('<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;">hourglass_empty</span>' + (pn_personal_finance_manager_i18n.exporting || 'Exporting...'));
		$status.hide();

		$.ajax({
			url: pn_personal_finance_manager_ajax.ajax_url,
			type: 'POST',
			data: {
				action: 'pn_personal_finance_manager_ajax',
				pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
				pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_export_portfolio'
			},
			success: function(response) {
				try {
					var data = typeof response === 'string' ? JSON.parse(response) : response;
					if (data.success && data.data) {
						var json_str = JSON.stringify(data.data, null, 2);
						var blob = new Blob([json_str], {type: 'application/json'});
						var url = URL.createObjectURL(blob);
						var a = document.createElement('a');
						var date = new Date().toISOString().slice(0, 10);
						a.href = url;
						a.download = 'portfolio-backup-' + date + '.json';
						document.body.appendChild(a);
						a.click();
						document.body.removeChild(a);
						URL.revokeObjectURL(url);

						$status.html('<span class="pn-personal-finance-manager-ei-status-success">' + (pn_personal_finance_manager_i18n.export_success || 'Portfolio exported successfully!') + '</span>').show();
					} else {
						$status.html('<span class="pn-personal-finance-manager-ei-status-error">' + (data.error || pn_personal_finance_manager_i18n.an_error_has_occurred) + '</span>').show();
					}
				} catch(err) {
					$status.html('<span class="pn-personal-finance-manager-ei-status-error">' + pn_personal_finance_manager_i18n.an_error_has_occurred + '</span>').show();
				}
				$btn.prop('disabled', false).html(originalText);
			},
			error: function() {
				$status.html('<span class="pn-personal-finance-manager-ei-status-error">' + pn_personal_finance_manager_i18n.an_error_has_occurred + '</span>').show();
				$btn.prop('disabled', false).html(originalText);
			}
		});
	});

	$('#pn-personal-finance-manager-import-file').on('change', function() {
		var fileName = this.files.length > 0 ? this.files[0].name : (pn_personal_finance_manager_i18n.no_file_selected || 'No file selected');
		$('#pn-personal-finance-manager-import-file-name').text(fileName);
	});

	$('.pn-personal-finance-manager-ei-file-label').on('click', function(e) {
		e.preventDefault();
		$('#pn-personal-finance-manager-import-file').trigger('click');
	});

	$('#pn-personal-finance-manager-import-btn').on('click', function(e) {
		e.preventDefault();
		var $btn = $(this);
		var $status = $('#pn-personal-finance-manager-import-status');
		var fileInput = document.getElementById('pn-personal-finance-manager-import-file');

		if (!fileInput.files || fileInput.files.length === 0) {
			$status.html('<span class="pn-personal-finance-manager-ei-status-error">' + (pn_personal_finance_manager_i18n.select_file_first || 'Please select a file first.') + '</span>').show();
			return;
		}

		var file = fileInput.files[0];
		if (!file.name.endsWith('.json')) {
			$status.html('<span class="pn-personal-finance-manager-ei-status-error">' + (pn_personal_finance_manager_i18n.invalid_file_format || 'Invalid file format.') + '</span>').show();
			return;
		}

		var reader = new FileReader();
		reader.onload = function(ev) {
			try {
				var jsonData = JSON.parse(ev.target.result);
				if (!jsonData.pn_personal_finance_manager_export) {
					$status.html('<span class="pn-personal-finance-manager-ei-status-error">' + (pn_personal_finance_manager_i18n.invalid_file_format || 'Invalid file format.') + '</span>').show();
					return;
				}
			} catch(err) {
				$status.html('<span class="pn-personal-finance-manager-ei-status-error">' + (pn_personal_finance_manager_i18n.invalid_file_format || 'Invalid file format.') + '</span>').show();
				return;
			}

			if (!confirm(pn_personal_finance_manager_i18n.confirm_import || 'This will add new entries to your portfolio. Existing data will not be overwritten. Continue?')) {
				return;
			}

			var originalText = $btn.html();
			$btn.prop('disabled', true).html('<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;">hourglass_empty</span>' + (pn_personal_finance_manager_i18n.importing || 'Importing...'));
			$status.hide();

			$.ajax({
				url: pn_personal_finance_manager_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'pn_personal_finance_manager_ajax',
					pn_personal_finance_manager_ajax_nonce: pn_personal_finance_manager_ajax.pn_personal_finance_manager_ajax_nonce,
					pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_import_portfolio',
					pn_personal_finance_manager_import_data: JSON.stringify(jsonData)
				},
				success: function(response) {
					try {
						var data = typeof response === 'string' ? JSON.parse(response) : response;
						if (data.success) {
							var msg = '<span class="pn-personal-finance-manager-ei-status-success">' + (pn_personal_finance_manager_i18n.import_success || 'Portfolio imported successfully!') + '<br>';
							if (data.counts) {
								msg += data.counts.assets + ' ' + (pn_personal_finance_manager_i18n.assets_imported || 'assets imported') + ', ';
								msg += data.counts.liabilities + ' ' + (pn_personal_finance_manager_i18n.liabilities_imported || 'liabilities imported') + ', ';
								msg += data.counts.watchlist + ' ' + (pn_personal_finance_manager_i18n.watchlist_items_imported || 'watchlist items imported');
							}
							msg += '</span>';
							$status.html(msg).show();
						} else {
							$status.html('<span class="pn-personal-finance-manager-ei-status-error">' + (data.error || pn_personal_finance_manager_i18n.import_error || 'Error importing portfolio.') + '</span>').show();
						}
					} catch(err) {
						$status.html('<span class="pn-personal-finance-manager-ei-status-error">' + (pn_personal_finance_manager_i18n.import_error || 'Error importing portfolio.') + '</span>').show();
					}
					$btn.prop('disabled', false).html(originalText);
				},
				error: function() {
					$status.html('<span class="pn-personal-finance-manager-ei-status-error">' + pn_personal_finance_manager_i18n.an_error_has_occurred + '</span>').show();
					$btn.prop('disabled', false).html(originalText);
				}
			});
		};
		reader.readAsText(file);
	});
})(jQuery);
PNPFM_JS;
		wp_add_inline_script('pn-personal-finance-manager-stocks', $_pnpfm_js, 'after');
		?>
		<?php
		return ob_get_clean();
	}

	/**
	 * Export the user's portfolio data.
	 *
	 * @since    1.0.19
	 * @param    int       $user_id    The user ID.
	 * @return   array                 Structured export data.
	 */
	public function pn_personal_finance_manager_export_portfolio($user_id) {
		$export = [
			'version'     => '1.0',
			'exported_at' => gmdate('c'),
		];

		// Export assets
		$export['assets'] = $this->pn_personal_finance_manager_export_posts($user_id, 'pnpfm_asset');

		// Export liabilities
		$export['liabilities'] = $this->pn_personal_finance_manager_export_posts($user_id, 'pnpfm_liability');

		// Export watchlist
		$watchlist = get_user_meta($user_id, 'pn_personal_finance_manager_watchlist', true);
		$export['watchlist'] = is_array($watchlist) ? $watchlist : [];

		// Export settings
		$currency = get_user_meta($user_id, 'pn_personal_finance_manager_currency', true);
		$user_currency = get_user_meta($user_id, 'pn_personal_finance_manager_user_currency', true);
		$alert_preferences = self::pn_personal_finance_manager_get_alert_preferences($user_id);
		$comparison_period = get_user_meta($user_id, 'pn_personal_finance_manager_comparison_period', true);
		$export['settings'] = [
			'currency'          => !empty($currency) ? $currency : 'usd',
			'user_currency'     => !empty($user_currency) ? $user_currency : '',
			'alert_preferences' => $alert_preferences,
			'comparison_period' => !empty($comparison_period) ? $comparison_period : 'daily',
		];

		return ['pn_personal_finance_manager_export' => $export];
	}

	/**
	 * Export posts of a given type for a user.
	 *
	 * @since    1.0.19
	 * @param    int       $user_id     The user ID.
	 * @param    string    $post_type   The post type slug.
	 * @return   array                  Array of post data with meta.
	 */
	private function pn_personal_finance_manager_export_posts($user_id, $post_type) {
		$posts = get_posts([
			'post_type'      => $post_type,
			'post_status'    => ['publish', 'draft', 'private'],
			'author'         => $user_id,
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
		]);

		$exported = [];
		foreach ($posts as $post) {
			$all_meta = get_post_meta($post->ID);
			$filtered_meta = [];

			foreach ($all_meta as $key => $values) {
				if (strpos($key, 'pn_personal_finance_manager_') === 0 || strpos($key, 'real_estate_') === 0) {
					$filtered_meta[$key] = maybe_unserialize($values[0]);
				}
			}

			$exported[] = [
				'title'   => $post->post_title,
				'content' => $post->post_content,
				'status'  => $post->post_status,
				'meta'    => $filtered_meta,
			];
		}

		return $exported;
	}

	/**
	 * Import portfolio data for a user.
	 *
	 * @since    1.0.19
	 * @param    int       $user_id     The user ID.
	 * @param    array     $json_data   The decoded JSON import data.
	 * @return   array                  Result with counts and errors.
	 */
	public function pn_personal_finance_manager_import_portfolio($user_id, $json_data) {
		if (empty($json_data['pn_personal_finance_manager_export']) || empty($json_data['pn_personal_finance_manager_export']['version'])) {
			return ['success' => false, 'error' => __('Invalid export file format.', 'pn-personal-finance-manager')];
		}

		$data = $json_data['pn_personal_finance_manager_export'];
		$counts = [
			'assets'      => 0,
			'liabilities' => 0,
			'watchlist'   => 0,
			'errors'      => 0,
		];

		// Import assets
		if (!empty($data['assets']) && is_array($data['assets'])) {
			foreach ($data['assets'] as $asset) {
				$result = $this->pn_personal_finance_manager_import_post($user_id, $asset, 'pnpfm_asset');
				if ($result) {
					$counts['assets']++;
				} else {
					$counts['errors']++;
				}
			}
		}

		// Import liabilities
		if (!empty($data['liabilities']) && is_array($data['liabilities'])) {
			foreach ($data['liabilities'] as $liability) {
				$result = $this->pn_personal_finance_manager_import_post($user_id, $liability, 'pnpfm_liability');
				if ($result) {
					$counts['liabilities']++;
				} else {
					$counts['errors']++;
				}
			}
		}

		// Import watchlist (merge, avoid duplicates)
		if (!empty($data['watchlist']) && is_array($data['watchlist'])) {
			$existing_watchlist = get_user_meta($user_id, 'pn_personal_finance_manager_watchlist', true);
			if (!is_array($existing_watchlist)) {
				$existing_watchlist = [];
			}

			// Build lookup of existing items by type+symbol
			$existing_lookup = [];
			foreach ($existing_watchlist as $item) {
				if (!empty($item['type']) && !empty($item['symbol'])) {
					$existing_lookup[$item['type'] . '_' . $item['symbol']] = true;
				}
			}

			foreach ($data['watchlist'] as $wl_item) {
				if (empty($wl_item['type']) || empty($wl_item['symbol'])) {
					continue;
				}

				$lookup_key = $wl_item['type'] . '_' . $wl_item['symbol'];
				if (isset($existing_lookup[$lookup_key])) {
					continue; // Skip duplicates
				}

				// Create new watchlist item with fresh ID
				$new_item = [
					'id'              => uniqid('wl_', true),
					'type'            => sanitize_text_field($wl_item['type']),
					'symbol'          => sanitize_text_field($wl_item['symbol']),
					'display_name'    => !empty($wl_item['display_name']) ? sanitize_text_field($wl_item['display_name']) : sanitize_text_field($wl_item['symbol']),
					'alert_enabled'   => !empty($wl_item['alert_enabled']),
					'alert_threshold' => !empty($wl_item['alert_threshold']) ? max(1, min(50, intval($wl_item['alert_threshold']))) : 5,
					'added_date'      => current_time('Y-m-d'),
					'last_alert_sent' => null,
				];

				$existing_watchlist[] = $new_item;
				$existing_lookup[$lookup_key] = true;
				$counts['watchlist']++;
			}

			update_user_meta($user_id, 'pn_personal_finance_manager_watchlist', $existing_watchlist);
		}

		// Import currency setting
		if (!empty($data['settings']['currency'])) {
			update_user_meta($user_id, 'pn_personal_finance_manager_currency', sanitize_text_field($data['settings']['currency']));
		}

		// Import user display currency
		if (!empty($data['settings']['user_currency'])) {
			$valid_currencies = array_keys(PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_currencies());
			$imported_currency = sanitize_text_field($data['settings']['user_currency']);
			if (in_array($imported_currency, $valid_currencies, true)) {
				update_user_meta($user_id, 'pn_personal_finance_manager_user_currency', $imported_currency);
			}
		}

		// Import comparison period
		if (!empty($data['settings']['comparison_period'])) {
			$valid_periods = ['daily', 'weekly', 'monthly', 'yearly', 'since_purchase'];
			$imported_period = sanitize_text_field($data['settings']['comparison_period']);
			if (in_array($imported_period, $valid_periods, true)) {
				update_user_meta($user_id, 'pn_personal_finance_manager_comparison_period', $imported_period);
			}
		}

		// Import alert preferences (individual keys)
		if (!empty($data['settings']['alert_preferences']) && is_array($data['settings']['alert_preferences'])) {
			$imported_prefs = $data['settings']['alert_preferences'];
			$threshold = isset($imported_prefs['default_threshold']) ? max(1, min(50, intval($imported_prefs['default_threshold']))) : 10;
			update_user_meta($user_id, 'pn_personal_finance_manager_default_threshold', $threshold);
			update_user_meta($user_id, 'pn_personal_finance_manager_alerts_assets_enabled',
				!empty($imported_prefs['alerts_assets_enabled']) ? 'on' : '');
			update_user_meta($user_id, 'pn_personal_finance_manager_alerts_watchlist_enabled',
				!empty($imported_prefs['alerts_watchlist_enabled']) ? 'on' : '');
		}

		return [
			'success' => true,
			'counts'  => $counts,
		];
	}

	/**
	 * Import a single post (asset or liability).
	 *
	 * @since    1.0.20
	 * @param    int       $user_id     The user ID.
	 * @param    array     $post_data   The post data from the export.
	 * @param    string    $post_type   The post type slug.
	 * @return   int|false              The new post ID on success, false on failure.
	 */
	private function pn_personal_finance_manager_import_post($user_id, $post_data, $post_type) {
		if (empty($post_data['title'])) {
			return false;
		}

		$post_id = wp_insert_post([
			'post_title'   => sanitize_text_field($post_data['title']),
			'post_content' => !empty($post_data['content']) ? wp_kses_post($post_data['content']) : '',
			'post_status'  => in_array($post_data['status'], ['publish', 'draft', 'private'], true) ? $post_data['status'] : 'publish',
			'post_type'    => $post_type,
			'post_author'  => $user_id,
		]);

		if (is_wp_error($post_id) || !$post_id) {
			return false;
		}

		// Import meta (whitelist: only pn_personal_finance_manager_* and real_estate_* keys)
		if (!empty($post_data['meta']) && is_array($post_data['meta'])) {
			foreach ($post_data['meta'] as $key => $value) {
				$key = sanitize_key($key);
				if (strpos($key, 'pn_personal_finance_manager_') === 0 || strpos($key, 'real_estate_') === 0) {
					update_post_meta($post_id, $key, $value);
				}
			}
		}

		return $post_id;
	}
}
