<?php
/**
 * Settings manager.
 *
 * This class defines plugin settings, both in dashboard or in front-end.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Settings {
  public function pn_personal_finance_manager_get_options() {
    $pn_personal_finance_manager_options = [];

    // --- Pages Section ---
    $pn_personal_finance_manager_options['pn_personal_finance_manager_section_pages_start'] = [
      'id' => 'pn_personal_finance_manager_section_pages_start',
      'section' => 'start',
      'label' => __('Pages', 'pn-personal-finance-manager'),
      'description' => __('Select the pages where each plugin feature will be displayed. Each page should contain the corresponding shortcode.', 'pn-personal-finance-manager'),
    ];

    $pages = get_pages(['sort_column' => 'post_title', 'sort_order' => 'ASC']);
    $page_options = ['' => __('-- Select page --', 'pn-personal-finance-manager')];
    if (!empty($pages)) {
      foreach ($pages as $page) {
        $page_options[$page->ID] = $page->post_title;
      }
    }

    // Also include draft pages created by the plugin (get_pages only returns published).
    $draft_pages = get_posts([
      'post_type'   => 'page',
      'post_status' => 'draft',
      'numberposts' => -1,
      'orderby'     => 'title',
      'order'       => 'ASC',
    ]);
    foreach ($draft_pages as $draft) {
      if (!isset($page_options[$draft->ID])) {
        $page_options[$draft->ID] = $draft->post_title . ' ' . __('(Draft)', 'pn-personal-finance-manager');
      }
    }

    // Auto-detect pages containing the correct shortcode or block for each selector.
    $required_pages = self::pn_personal_finance_manager_get_required_pages();
    $auto_detected = [];
    foreach ($required_pages as $key => $info) {
      $option_name = 'pn_personal_finance_manager_page_' . $key;
      $current = get_option($option_name);
      if (empty($current)) {
        $detected = self::pn_personal_finance_manager_find_page_by_shortcode($info['shortcode']);
        if ($detected) {
          update_option($option_name, $detected);
          $auto_detected[$key] = $detected;
        }
      }
    }

    $pn_personal_finance_manager_options['pn_personal_finance_manager_page_assets'] = [
      'id' => 'pn_personal_finance_manager_page_assets',
      'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => $page_options,
      'label' => __('Assets page', 'pn-personal-finance-manager'),
      'description' => __('Select the page where users will manage their assets.', 'pn-personal-finance-manager'),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_page_liabilities'] = [
      'id' => 'pn_personal_finance_manager_page_liabilities',
      'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => $page_options,
      'label' => __('Liabilities page', 'pn-personal-finance-manager'),
      'description' => __('Select the page where users will manage their liabilities.', 'pn-personal-finance-manager'),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_page_portfolio'] = [
      'id' => 'pn_personal_finance_manager_page_portfolio',
      'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => $page_options,
      'label' => __('Portfolio page', 'pn-personal-finance-manager'),
      'description' => __('Select the page where users will see their portfolio summary and performance.', 'pn-personal-finance-manager'),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_page_watchlist'] = [
      'id' => 'pn_personal_finance_manager_page_watchlist',
      'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => $page_options,
      'label' => __('Watchlist page', 'pn-personal-finance-manager'),
      'description' => __('Select the page where users will manage their watchlist.', 'pn-personal-finance-manager'),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_section_pages_end'] = [
      'id' => 'pn_personal_finance_manager_section_pages_end',
      'section' => 'end',
    ];

    // --- API Section ---
    $pn_personal_finance_manager_options['pn_personal_finance_manager_section_api_start'] = [
      'id' => 'pn_personal_finance_manager_section_api_start',
      'section' => 'start',
      'label' => __('API', 'pn-personal-finance-manager'),
      'description' => __('Configure the Twelve Data API integration for real-time stock data and cache settings.', 'pn-personal-finance-manager'),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_stocks_api_enabled'] = [
      'id' => 'pn_personal_finance_manager_stocks_api_enabled',
      'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'parent' => 'this',
      'label' => __('Enable Stocks API', 'pn-personal-finance-manager'),
      'description' => __('Enable the stocks API integration for real-time stock data.', 'pn-personal-finance-manager'),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_stocks_api_key'] = [
      'id' => 'pn_personal_finance_manager_stocks_api_key',
      'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Twelve Data API Key', 'pn-personal-finance-manager'),
      'placeholder' => __('Enter your Twelve Data API key', 'pn-personal-finance-manager'),
      'description' => __('Enter your free API key from Twelve Data. Get a free key at: https://twelvedata.com/register (800 calls/day, 8 calls/minute).', 'pn-personal-finance-manager'),
      'parent' => 'pn_personal_finance_manager_stocks_api_enabled',
      'parent_option' => 'on',
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_stocks_cache_duration'] = [
      'id' => 'pn_personal_finance_manager_stocks_cache_duration',
      'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => [
        '300' => __('5 minutes', 'pn-personal-finance-manager'),
        '900' => __('15 minutes', 'pn-personal-finance-manager'),
        '1800' => __('30 minutes', 'pn-personal-finance-manager'),
        '3600' => __('1 hour', 'pn-personal-finance-manager'),
        '7200' => __('2 hours', 'pn-personal-finance-manager'),
        '86400' => __('24 hours', 'pn-personal-finance-manager'),
      ],
      'label' => __('Cache Duration', 'pn-personal-finance-manager'),
      'placeholder' => __('Select cache duration', 'pn-personal-finance-manager'),
      'description' => __('How long to cache stock data to avoid excessive API calls.', 'pn-personal-finance-manager'),
      'parent' => 'pn_personal_finance_manager_stocks_api_enabled',
      'parent_option' => 'on',
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_stocks_manual_update'] = [
      'id' => 'pn_personal_finance_manager_stocks_manual_update',
      'class' => 'pn-personal-finance-manager-btn',
      'input' => 'input',
      'type' => 'button',
      'label' => __('Update Stock Symbols Now', 'pn-personal-finance-manager'),
      'description' => __('Click this button to manually update the list of stock symbols from the API. Last update: ', 'pn-personal-finance-manager') . $this->get_last_symbol_update_time(),
      'parent' => 'pn_personal_finance_manager_stocks_api_enabled',
      'parent_option' => 'on',
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_section_api_end'] = [
      'id' => 'pn_personal_finance_manager_section_api_end',
      'section' => 'end',
    ];

    // --- System Section ---
    $pn_personal_finance_manager_options['pn_personal_finance_manager_section_system_start'] = [
      'id' => 'pn_personal_finance_manager_section_system_start',
      'section' => 'start',
      'label' => __('System', 'pn-personal-finance-manager'),
      'description' => __('General system settings: URL slugs, currency, and plugin cleanup options.', 'pn-personal-finance-manager'),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_currency'] = [
      'id' => 'pn_personal_finance_manager_currency',
      'class' => 'pn-personal-finance-manager-select pn-personal-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => PN_PERSONAL_FINANCE_MANAGER_Data::pn_personal_finance_manager_get_currencies(),
      'value' => get_option('pn_personal_finance_manager_currency', 'eur'),
      'label' => __('1.1.4', 'pn-personal-finance-manager'),
      'placeholder' => __('Select currency', 'pn-personal-finance-manager'),
      'description' => __('Select the currency that will be used throughout the application for financial calculations and displays.', 'pn-personal-finance-manager'),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_asset_slug'] = [
      'id' => 'pn_personal_finance_manager_asset_slug',
      'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Asset slug', 'pn-personal-finance-manager'),
      'placeholder' => __('Asset slug', 'pn-personal-finance-manager'),
      'description' => __('This option sets the slug of the main Asset archive page, and the Asset pages. By default they will be:', 'pn-personal-finance-manager') . '<br><a href="' . esc_url(home_url('/pn_personal_finance_manager_asset')) . '" target="_blank">' . esc_url(home_url('/pn_personal_finance_manager_asset')) . '</a><br>' . esc_url(home_url('/pn_personal_finance_manager_asset/asset-name')),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_liability_slug'] = [
      'id' => 'pn_personal_finance_manager_liability_slug',
      'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Liabilities slug', 'pn-personal-finance-manager'),
      'placeholder' => __('Liabilities slug', 'pn-personal-finance-manager'),
      'description' => __('This option sets the slug of the main Liabilities archive page, and the Liabilities pages. By default they will be:', 'pn-personal-finance-manager') . '<br><a href="' . esc_url(home_url('/pn-personal-finance-manager-liability')) . '" target="_blank">' . esc_url(home_url('/pn-personal-finance-manager-liability')) . '</a><br>' . esc_url(home_url('/pn-personal-finance-manager-liability/liability-name')),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_options_remove'] = [
      'id' => 'pn_personal_finance_manager_options_remove',
      'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'label' => __('Remove plugin options on deactivation', 'pn-personal-finance-manager'),
      'description' => __('If you activate this option the plugin will remove all options on deactivation. Please, be careful. This process cannot be undone.', 'pn-personal-finance-manager'),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_section_system_end'] = [
      'id' => 'pn_personal_finance_manager_section_system_end',
      'section' => 'end',
    ];

    // --- Colors Section ---
    $pn_personal_finance_manager_options['pn_personal_finance_manager_section_colors_start'] = [
      'id' => 'pn_personal_finance_manager_section_colors_start',
      'section' => 'start',
      'label' => __('Colors', 'pn-personal-finance-manager'),
      'description' => __('Customize the main color used throughout the application for menus, section titles, asset labels, and charts.', 'pn-personal-finance-manager'),
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_color_primary'] = [
      'id' => 'pn_personal_finance_manager_color_primary',
      'class' => 'pn-personal-finance-manager-input pn-personal-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Primary Color', 'pn-personal-finance-manager'),
      'description' => __('Main color for menus, section titles, asset labels, and charts.', 'pn-personal-finance-manager'),
      'value' => '#008080',
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_section_colors_end'] = [
      'id' => 'pn_personal_finance_manager_section_colors_end',
      'section' => 'end',
    ];

    // --- User Management Section ---
    $pn_personal_finance_manager_options['pn_personal_finance_manager_section_roles_start'] = [
      'id' => 'pn_personal_finance_manager_section_roles_start',
      'section' => 'start',
      'label' => __('User Role Management', 'pn-personal-finance-manager'),
      'description' => __('Manage which users have the Personal Finance Manager role. Users with this role can access financial management features.', 'pn-personal-finance-manager'),
    ];
      $pn_personal_finance_manager_options['pn_personal_finance_manager_role_manager_selector'] = [
        'id' => 'pn_personal_finance_manager_role_manager_selector',
        'input' => 'user_role_selector',
        'label' => __('Personal Finance Manager - PN Role', 'pn-personal-finance-manager'),
        'role' => 'pn_personal_finance_manager_role_manager',
        'role_label' => __('Personal Finance Manager - PN', 'pn-personal-finance-manager'),
      ];
    $pn_personal_finance_manager_options['pn_personal_finance_manager_section_roles_end'] = [
      'id' => 'pn_personal_finance_manager_section_roles_end',
      'section' => 'end',
    ];

    $pn_personal_finance_manager_options['pn_personal_finance_manager_nonce'] = [
      'id' => 'pn_personal_finance_manager_nonce',
      'input' => 'input',
      'type' => 'hidden',
    ];
    return $pn_personal_finance_manager_options;
  }

  private function get_last_symbol_update_time() {
    $last_update = get_option('pn_personal_finance_manager_stock_symbols_last_update', 0);
    if ($last_update) {
      return date_i18n(get_option('date_format') . ' @ ' . get_option('time_format'), $last_update);
    }
    return __('Never', 'pn-personal-finance-manager');
  }

	/**
	 * Administrator menu.
	 *
	 * @since    1.0.0
	 */
	public function pn_personal_finance_manager_admin_menu() {
    add_menu_page(
      esc_html__('Personal Finance Manager - PN', 'pn-personal-finance-manager'),
      esc_html__('Personal Finance Manager - PN', 'pn-personal-finance-manager'),
      'manage_options',
      'pn_personal_finance_manager_options',
      [$this, 'pn_personal_finance_manager_options'],
      esc_url(PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/media/pn-personal-finance-manager-menu-icon.svg'),
    );
		
    add_submenu_page(
      'pn_personal_finance_manager_options',
      esc_html__('Settings', 'pn-personal-finance-manager'),
      esc_html__('Settings', 'pn-personal-finance-manager'),
      'manage_options',
      'pn_personal_finance_manager_options',
      [$this, 'pn_personal_finance_manager_options'],
    );
	}

	public function pn_personal_finance_manager_options() {
    $required_pages = self::pn_personal_finance_manager_get_required_pages();
    $missing_pages = [];

    foreach ($required_pages as $page_key => $page_info) {
      if (!self::pn_personal_finance_manager_find_page_by_shortcode($page_info['shortcode'])) {
        $missing_pages[$page_key] = $page_info;
      }
    }
	  ?>
	    <div class="pn-personal-finance-manager-options pn-personal-finance-manager-max-width-1000 pn-personal-finance-manager-margin-auto pn-personal-finance-manager-mt-50 pn-personal-finance-manager-mb-50">
        <img src="<?php echo esc_url(PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/media/banner-1544x500.png'); ?>" alt="<?php esc_html_e('Plugin main Banner', 'pn-personal-finance-manager'); ?>" title="<?php esc_html_e('Plugin main Banner', 'pn-personal-finance-manager'); ?>" class="pn-personal-finance-manager-width-100-percent pn-personal-finance-manager-border-radius-20 pn-personal-finance-manager-mb-30">
        <h1 class="pn-personal-finance-manager-mb-30"><?php esc_html_e('Personal Finance Manager - PN Settings', 'pn-personal-finance-manager'); ?></h1>
        <?php if (!empty($missing_pages)): ?>
          <div class="pn-personal-finance-manager-options-fields pn-personal-finance-manager-mb-30">
            <div class="pn-personal-finance-manager-p-30">
              <p class="pn-personal-finance-manager-mb-15">
                <?php esc_html_e('The following required pages have not been detected on your site. Click the buttons below to automatically create them as drafts. Each page will open in a new tab for you to review and publish.', 'pn-personal-finance-manager'); ?>
              </p>
              <?php foreach ($missing_pages as $page_key => $page_info): ?>
                <button type="button" class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini pn-personal-finance-manager-mr-10 pn-personal-finance-manager-mb-10 pn-personal-finance-manager-create-page-btn" data-page-key="<?php echo esc_attr($page_key); ?>">
                  <?php echo esc_html(sprintf(
                    /* translators: %s: Page name */
                    __('Create %s page', 'pn-personal-finance-manager'),
                    $page_info['title']
                  )); ?>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
          <?php
          // Enqueue settings footer script early so handle is available for inline scripts.
      wp_enqueue_script(
        'pn-personal-finance-manager-settings-footer',
        PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/admin/pn-personal-finance-manager-settings-footer.js',
        [],
        PN_PERSONAL_FINANCE_MANAGER_VERSION,
        true
      );

      wp_localize_script('pn-personal-finance-manager-settings-footer', 'pn_personal_finance_manager_settings_footer', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('pn-personal-finance-manager-nonce'),
        'i18n'    => [
          'confirmImport'  => __('This will overwrite your current settings. Continue?', 'pn-personal-finance-manager'),
          'importSuccess'  => __('Settings imported successfully. Reloading...', 'pn-personal-finance-manager'),
          'importError'    => __('Error importing settings.', 'pn-personal-finance-manager'),
          'invalidFile'    => __('Invalid JSON file.', 'pn-personal-finance-manager'),
          'exportError'    => __('Error exporting settings.', 'pn-personal-finance-manager'),
        ],
      ]);

          $_pnpfm_js = <<<'PNPFM_JS'
document.querySelectorAll('.pn-personal-finance-manager-create-page-btn').forEach(function(btn) {
	btn.addEventListener('click', function() {
		var pageKey = this.getAttribute('data-page-key');
		var originalText = this.textContent;
		this.disabled = true;
		this.textContent = '__PNPFM_CREATING__';

		var data = new FormData();
		data.append('action', 'pn_personal_finance_manager_ajax');
		data.append('pn_personal_finance_manager_ajax_type', 'pn_personal_finance_manager_create_page');
		data.append('pn_personal_finance_manager_ajax_nonce', '__PNPFM_NONCE__');
		data.append('page_key', pageKey);

		var currentBtn = this;

		fetch('__PNPFM_AJAX_URL__', {
			method: 'POST',
			body: data
		})
		.then(function(response) { return response.json(); })
		.then(function(result) {
			if (result.error_key === '' && result.redirect_url) {
				window.open(result.redirect_url, '_blank');
				var container = currentBtn.closest('.pn-personal-finance-manager-options-fields');
				currentBtn.style.display = 'none';
				var visibleBtns = container.querySelectorAll('.pn-personal-finance-manager-create-page-btn:not([style*="display: none"])');
				if (visibleBtns.length === 0) {
					container.style.display = 'none';
				}
				if (result.page_id && result.page_title) {
					var selects = document.querySelectorAll('select[name^="pn_personal_finance_manager_page_"]');
					selects.forEach(function(select) {
						if (!select.querySelector('option[value="' + result.page_id + '"]')) {
							var opt = document.createElement('option');
							opt.value = result.page_id;
							opt.textContent = result.page_title + ' __PNPFM_DRAFT__';
							select.appendChild(opt);
						}
						if (result.page_key && select.name === 'pn_personal_finance_manager_page_' + result.page_key) {
							select.value = String(result.page_id);
						}
					});
				}
			} else {
				currentBtn.disabled = false;
				currentBtn.textContent = originalText;
				alert(result.error_content || '__PNPFM_ERROR__');
			}
		})
		.catch(function() {
			currentBtn.disabled = false;
			currentBtn.textContent = originalText;
			alert('__PNPFM_ERROR__');
		});
	});
});
PNPFM_JS;
          $_pnpfm_js = str_replace(
            ['__PNPFM_CREATING__', '__PNPFM_NONCE__', '__PNPFM_AJAX_URL__', '__PNPFM_DRAFT__', '__PNPFM_ERROR__'],
            [esc_js(__('Creating page...', 'pn-personal-finance-manager')), esc_js(wp_create_nonce('pn-personal-finance-manager-nonce')), esc_url(admin_url('admin-ajax.php')), esc_js(__('(Draft)', 'pn-personal-finance-manager')), esc_js(__('An error occurred while creating the page.', 'pn-personal-finance-manager'))],
            $_pnpfm_js
          );
          wp_add_inline_script('pn-personal-finance-manager-settings-footer', $_pnpfm_js, 'after');
          ?>
        <?php endif; ?>
        <div class="pn-personal-finance-manager-options-fields pn-personal-finance-manager-mb-30 pn-personal-finance-manager-settings-pb-80">
          <form action="" method="post" id="pn-personal-finance-manager-form-settings" class="pn-personal-finance-manager-form pn-personal-finance-manager-p-30">
          <?php
            $options = self::pn_personal_finance_manager_get_options();

            foreach ($options as $option_key => $pn_personal_finance_manager_option) {
              // Render API Status block at the end of the API section.
              if ($option_key === 'pn_personal_finance_manager_section_api_end') {
                ?>
                <div class="pn-personal-finance-manager-api-status pn-personal-finance-manager-mt-20 pn-personal-finance-manager-p-20 pn-personal-finance-manager-border-radius-10" style="background-color: #f9f9f9; border: 1px solid #ddd;">
                  <h3><?php esc_html_e('API Status & Information', 'pn-personal-finance-manager'); ?></h3>
                  <div id="pn-personal-finance-manager-api-status-info">
                    <p><?php esc_html_e('Click the button below to check your API status and limits.', 'pn-personal-finance-manager'); ?></p>
                    <button type="button" id="pn_personal_finance_manager_check_api_status" class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini">
                      <?php esc_html_e('Check API Status', 'pn-personal-finance-manager'); ?>
                    </button>
                  </div>
                  <div id="pn-personal-finance-manager-api-status-result" style="display: none; margin-top: 15px; padding: 10px; border-radius: 5px;"></div>
                </div>
                <?php
              }
              PN_PERSONAL_FINANCE_MANAGER_Forms::pn_personal_finance_manager_input_wrapper_builder($pn_personal_finance_manager_option, 'option', 0, 0, 'half');
            }
          ?>
          <input type="submit" name="pn_personal_finance_manager_submit" id="pn_personal_finance_manager_submit" class="pn-personal-finance-manager-settings-hidden-submit" data-pn-personal-finance-manager-type="option" value="<?php esc_attr_e('Save options', 'pn-personal-finance-manager'); ?>">
          </form>
        </div>
      </div>

      <!-- Sticky settings footer bar -->
      <div id="pn-personal-finance-manager-settings-footer" class="pn-personal-finance-manager-settings-footer">
        <div class="pn-personal-finance-manager-settings-footer-inner">
          <div class="pn-personal-finance-manager-settings-footer-left">
            <span class="pn-personal-finance-manager-settings-footer-plugin-name">Personal Finance Manager - PN</span>
            <span class="pn-personal-finance-manager-settings-footer-version">v<?php echo esc_html(PN_PERSONAL_FINANCE_MANAGER_VERSION); ?></span>
          </div>
          <div class="pn-personal-finance-manager-settings-footer-right">
            <input type="file" id="pn-personal-finance-manager-settings-import-file" class="pn-personal-finance-manager-settings-hidden-input" accept=".json">
            <button type="button" id="pn-personal-finance-manager-settings-import" class="pn-personal-finance-manager-settings-footer-icon-btn pn-personal-finance-manager-tooltip" title="<?php esc_attr_e('Import settings', 'pn-personal-finance-manager'); ?>">
              <span class="material-icons-outlined">file_upload</span>
            </button>
            <button type="button" id="pn-personal-finance-manager-settings-export" class="pn-personal-finance-manager-settings-footer-icon-btn pn-personal-finance-manager-tooltip" title="<?php esc_attr_e('Export settings', 'pn-personal-finance-manager'); ?>">
              <span class="material-icons-outlined">file_download</span>
            </button>
            <button type="button" id="pn-personal-finance-manager-settings-save" class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini">
              <?php esc_html_e('Save options', 'pn-personal-finance-manager'); ?>
            </button>
          </div>
        </div>
      </div>

      <?php
      wp_enqueue_style('pn-personal-finance-manager-tooltips', PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/css/pn-personal-finance-manager-tooltips.css', [], PN_PERSONAL_FINANCE_MANAGER_VERSION);
      wp_enqueue_script('pn-personal-finance-manager-tooltips', PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/pn-personal-finance-manager-tooltips.js', ['jquery'], PN_PERSONAL_FINANCE_MANAGER_VERSION, true);

?>
      <?php
      $_pnpfm_js = <<<'PNPFM_JS'
jQuery(document).ready(function($) {
	$('#pn_personal_finance_manager_stocks_manual_update').on('click', function() {
		var $this = $(this);
		var original_text = $this.val();
		$this.val('Updating...').prop('disabled', true);

		$.ajax({
			url: '__PNPFM_AJAX_URL__',
			type: 'POST',
			data: {
				action: 'pn_personal_finance_manager_ajax',
				pn_personal_finance_manager_ajax_type: 'pn_personal_finance_manager_manual_stock_update',
				pn_personal_finance_manager_ajax_nonce: '__PNPFM_NONCE__'
			},
			success: function(response) {
				if (typeof response === 'string') {
					try {
						response = JSON.parse(response);
					} catch (e) {
						console.error('Failed to parse server response:', e);
						alert('An error occurred: Invalid server response.');
						$this.val(original_text).prop('disabled', false);
						return;
					}
				}
				if(response && response.success) {
					alert('Stock symbols updated successfully!');
					location.reload();
				} else {
					var error_message = (response && response.data && response.data.error) ? response.data.error : 'An unknown error occurred.';
					alert('Error updating symbols: ' + error_message);
					$this.val(original_text).prop('disabled', false);
				}
			},
			error: function() {
				alert('An unknown server error occurred during the update.');
				$this.val(original_text).prop('disabled', false);
			}
		});
	});
	$('#pn_personal_finance_manager_check_api_status').on('click', function() {
		var $this = $(this);
		var original_text = $this.text();
		$this.text('Checking...').prop('disabled', true);
		$.ajax({
			url: '__PNPFM_AJAX_URL__',
			type: 'POST',
			data: {
				action: 'pn_personal_finance_manager_check_api_status',
				nonce: '__PNPFM_API_NONCE__'
			},
			success: function(response) {
				var $result = $('#pn-personal-finance-manager-api-status-result');
				var statusClass = '';
				var statusText = '';
				switch(response.status) {
					case 'working':
						statusClass = 'notice-success';
						statusText = '\u2705 ' + response.message;
						break;
					case 'limit_exceeded':
						statusClass = 'notice-warning';
						statusText = '\u26a0\ufe0f ' + response.message;
						break;
					case 'error':
						statusClass = 'notice-error';
						statusText = '\u274c ' + response.message;
						break;
					default:
						statusClass = 'notice-info';
						statusText = '\u2139\ufe0f ' + response.message;
				}
				var html = '<div class="' + statusClass + '">' +
					'<h4>API Status: Twelve Data</h4>' +
					'<p><strong>Status:</strong> ' + statusText + '</p>' +
					'<p><strong>Cached Symbols:</strong> ' + response.cached_symbols + '</p>' +
					'<p><strong>Last Update:</strong> ' + response.last_update + '</p>';
				if (response.status === 'limit_exceeded') {
					html += '<p><strong>Note:</strong> Twelve Data free tier allows 8 calls/minute, 800 calls/day. ' +
						'Please wait a few minutes before trying again.</p>';
				}
				html += '</div>';
				$result.html(html).show();
				$this.text(original_text).prop('disabled', false);
			},
			error: function() {
				$('#pn-personal-finance-manager-api-status-result').html(
					'<div class="notice-error"><p>\u274c Failed to check API status. Please try again.</p></div>'
				).show();
				$this.text(original_text).prop('disabled', false);
			}
		});
	});
});
PNPFM_JS;
      $_pnpfm_js = str_replace(
        ['__PNPFM_AJAX_URL__', '__PNPFM_NONCE__', '__PNPFM_API_NONCE__'],
        [esc_url(admin_url('admin-ajax.php')), esc_js(wp_create_nonce('pn-personal-finance-manager-nonce')), esc_js(wp_create_nonce('pn_personal_finance_manager_nonce'))],
        $_pnpfm_js
      );
      wp_add_inline_script('pn-personal-finance-manager-settings-footer', $_pnpfm_js, 'after');
      ?>
	  <?php
	}

  public function pn_personal_finance_manager_check_activation() {
    // Only run in admin and not during AJAX requests
    if (!is_admin() || defined('DOING_AJAX')) {
      return;
    }

    // Check if we're already in the redirection process
    if (get_option('pn_personal_finance_manager_redirecting')) {
      delete_option('pn_personal_finance_manager_redirecting');
      return;
    }

    if (get_transient('pn_personal_finance_manager_just_activated')) {
      $target_url = admin_url('admin.php?page=pn_personal_finance_manager_options');
      
      if ($target_url) {
        // Mark that we're in the redirection process
        update_option('pn_personal_finance_manager_redirecting', true);
        
        // Remove the transient
        delete_transient('pn_personal_finance_manager_just_activated');
        
        // Redirect and exit
        wp_safe_redirect(esc_url($target_url));
        exit;
      }
    }
  }

  /**
   * Adds the Settings link to the plugin list
   */
  public function pn_personal_finance_manager_plugin_action_links($links) {
      $settings_link = '<a href="admin.php?page=pn_personal_finance_manager_options">' . esc_html__('Settings', 'pn-personal-finance-manager') . '</a>';
      array_unshift($links, $settings_link);

      return $links;
  }

  /**
   * Find a page containing a specific shortcode.
   *
   * @param string $shortcode The shortcode name to search for.
   * @return int|false Page ID if found, false otherwise.
   */
  public static function pn_personal_finance_manager_find_page_by_shortcode($shortcode) {
    $pages = get_posts([
      'post_type'   => 'page',
      'post_status' => ['publish', 'draft', 'private'],
      'numberposts' => -1,
      'fields'      => 'ids',
    ]);

    $block_map = PN_PERSONAL_FINANCE_MANAGER_Blocks::pn_personal_finance_manager_get_shortcode_to_block_map();
    $block_name = isset( $block_map[ $shortcode ] ) ? $block_map[ $shortcode ] : '';

    foreach ($pages as $page_id) {
      $content = get_post_field('post_content', $page_id);

      if (has_shortcode($content, $shortcode)) {
        return $page_id;
      }

      if ($block_name && has_block($block_name, $page_id)) {
        return $page_id;
      }
    }

    return false;
  }

  /**
   * Get the list of required pages with their shortcodes and labels.
   *
   * @return array
   */
  public static function pn_personal_finance_manager_get_required_pages() {
    return [
      'assets' => [
        'shortcode'        => 'pn-personal-finance-manager-asset-list',
        'title'            => __('Assets', 'pn-personal-finance-manager'),
        'label'            => __('Assets page', 'pn-personal-finance-manager'),
        'seo_focus_kw'     => __('financial assets', 'pn-personal-finance-manager'),
        'seo_metadesc'     => __('Manage and track your financial assets including stocks, crypto, real estate, and more. Monitor performance and portfolio value.', 'pn-personal-finance-manager'),
      ],
      'liabilities' => [
        'shortcode'        => 'pn-personal-finance-manager-liability-list',
        'title'            => __('Liabilities', 'pn-personal-finance-manager'),
        'label'            => __('Liabilities page', 'pn-personal-finance-manager'),
        'seo_focus_kw'     => __('financial liabilities', 'pn-personal-finance-manager'),
        'seo_metadesc'     => __('Track and manage your financial liabilities including mortgages, loans, credit cards, and other debts in one place.', 'pn-personal-finance-manager'),
      ],
      'portfolio' => [
        'shortcode'        => 'pn-personal-finance-manager-user-assets',
        'title'            => __('Portfolio', 'pn-personal-finance-manager'),
        'label'            => __('Portfolio page', 'pn-personal-finance-manager'),
        'seo_focus_kw'     => __('investment portfolio', 'pn-personal-finance-manager'),
        'seo_metadesc'     => __('View your complete investment portfolio with performance charts, profit/loss tracking, and asset allocation overview.', 'pn-personal-finance-manager'),
      ],
      'watchlist' => [
        'shortcode'        => 'pn-personal-finance-manager-watchlist',
        'title'            => __('Watchlist', 'pn-personal-finance-manager'),
        'label'            => __('Watchlist page', 'pn-personal-finance-manager'),
        'seo_focus_kw'     => __('stock watchlist', 'pn-personal-finance-manager'),
        'seo_metadesc'     => __('Keep track of stocks and cryptocurrencies you are interested in with price alerts and real-time data.', 'pn-personal-finance-manager'),
      ],
    ];
  }

	/**
	 * Check API status and limits.
	 *
	 * @since    1.0.0
	 * @return   array    API status information.
	 */
	public function pn_personal_finance_manager_check_api_status() {
		$api_key = get_option('pn_personal_finance_manager_stocks_api_key', '');

		if (empty($api_key)) {
			return [
				'status' => 'error',
				'message' => 'API key not configured.',
				'provider' => 'twelvedata',
				'cached_symbols' => 0,
				'last_update' => __('Never', 'pn-personal-finance-manager')
			];
		}

		$stocks = new PN_PERSONAL_FINANCE_MANAGER_Stocks();
		$cache_stats = $stocks->pn_personal_finance_manager_get_stock_symbols_cache_stats();

		$status_info = [
			'provider' => 'twelvedata',
			'cached_symbols' => $cache_stats['symbols_count'],
			'last_update' => $cache_stats['last_update_formatted'],
			'status' => 'unknown'
		];

		// Test with a simple quote request
		$url = "https://api.twelvedata.com/quote?symbol=AAPL&apikey=" . urlencode($api_key);
		$response = wp_remote_get($url, ['timeout' => 10]);

		if (is_wp_error($response)) {
			$status_info['status'] = 'error';
			$status_info['message'] = 'Connection error: ' . $response->get_error_message();
			return $status_info;
		}

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if (isset($data['code'])) {
			if ($data['code'] == 429) {
				$status_info['status'] = 'limit_exceeded';
				$status_info['message'] = 'API rate limit exceeded. Free tier: 8 calls/minute, 800 calls/day.';
			} else {
				$status_info['status'] = 'error';
				$status_info['message'] = 'API error: ' . ($data['message'] ?? 'Unknown error');
			}
		} elseif (!empty($data['close'])) {
			$status_info['status'] = 'working';
			$status_info['message'] = 'Twelve Data API is working correctly.';
		} else {
			$status_info['status'] = 'error';
			$status_info['message'] = 'Unexpected API response.';
		}

		return $status_info;
	}
}

// Hook para borrar el transient del tipo de cambio al cambiar la moneda
add_action('update_option_pn_personal_finance_manager_currency', function($old_value, $value) {
    $transient_key = 'pn_personal_finance_manager_usd_rate_' . strtolower($value);
    delete_transient($transient_key);
}, 10, 2);

// DEBUG: Comprobar si el transient del tipo de cambio existe
add_action('init', function() {
    $currency = get_option('pn_personal_finance_manager_currency', 'eur');
    $transient_key = 'pn_personal_finance_manager_usd_rate_' . strtolower($currency);
    if (false !== get_transient($transient_key)) {
        error_log('Transient ' . $transient_key . ' existe.');
    } else {
        error_log('Transient ' . $transient_key . ' NO existe.');
    }
});