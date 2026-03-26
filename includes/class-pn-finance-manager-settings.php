<?php
/**
 * Settings manager.
 *
 * This class defines plugin settings, both in dashboard or in front-end.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_FINANCE_MANAGER
 * @subpackage PN_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_FINANCE_MANAGER_Settings {
  public function pn_finance_manager_get_options() {
    $pn_finance_manager_options = [];

    // --- Pages Section ---
    $pn_finance_manager_options['pn_finance_manager_section_pages_start'] = [
      'id' => 'pn_finance_manager_section_pages_start',
      'section' => 'start',
      'label' => __('Pages', 'pn-finance-manager'),
      'description' => __('Select the pages where each plugin feature will be displayed. Each page should contain the corresponding shortcode.', 'pn-finance-manager'),
    ];

    $pages = get_pages(['sort_column' => 'post_title', 'sort_order' => 'ASC']);
    $page_options = ['' => __('-- Select page --', 'pn-finance-manager')];
    if (!empty($pages)) {
      foreach ($pages as $page) {
        $page_options[$page->ID] = $page->post_title;
      }
    }

    $pn_finance_manager_options['pn_finance_manager_page_assets'] = [
      'id' => 'pn_finance_manager_page_assets',
      'class' => 'pn-finance-manager-select pn-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => $page_options,
      'label' => __('Assets page', 'pn-finance-manager'),
      'description' => __('Select the page where users will manage their assets. This page should contain the shortcode: [pn-finance-manager-asset-list]', 'pn-finance-manager'),
    ];

    $pn_finance_manager_options['pn_finance_manager_page_liabilities'] = [
      'id' => 'pn_finance_manager_page_liabilities',
      'class' => 'pn-finance-manager-select pn-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => $page_options,
      'label' => __('Liabilities page', 'pn-finance-manager'),
      'description' => __('Select the page where users will manage their liabilities. This page should contain the shortcode: [pn-finance-manager-liability-list]', 'pn-finance-manager'),
    ];

    $pn_finance_manager_options['pn_finance_manager_page_portfolio'] = [
      'id' => 'pn_finance_manager_page_portfolio',
      'class' => 'pn-finance-manager-select pn-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => $page_options,
      'label' => __('Portfolio page', 'pn-finance-manager'),
      'description' => __('Select the page where users will see their portfolio summary and performance. This page should contain the shortcode: [pn-finance-manager-user-assets]', 'pn-finance-manager'),
    ];

    $pn_finance_manager_options['pn_finance_manager_page_watchlist'] = [
      'id' => 'pn_finance_manager_page_watchlist',
      'class' => 'pn-finance-manager-select pn-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => $page_options,
      'label' => __('Watchlist page', 'pn-finance-manager'),
      'description' => __('Select the page where users will manage their watchlist. This page should contain the shortcode: [pn-finance-manager-watchlist]', 'pn-finance-manager'),
    ];

    $pn_finance_manager_options['pn_finance_manager_section_pages_end'] = [
      'id' => 'pn_finance_manager_section_pages_end',
      'section' => 'end',
    ];

    // --- API Section ---
    $pn_finance_manager_options['pn_finance_manager_section_api_start'] = [
      'id' => 'pn_finance_manager_section_api_start',
      'section' => 'start',
      'label' => __('API', 'pn-finance-manager'),
      'description' => __('Configure the Twelve Data API integration for real-time stock data and cache settings.', 'pn-finance-manager'),
    ];

    $pn_finance_manager_options['pn_finance_manager_stocks_api_enabled'] = [
      'id' => 'pn_finance_manager_stocks_api_enabled',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'parent' => 'this',
      'label' => __('Enable Stocks API', 'pn-finance-manager'),
      'description' => __('Enable the stocks API integration for real-time stock data.', 'pn-finance-manager'),
    ];

    $pn_finance_manager_options['pn_finance_manager_stocks_api_key'] = [
      'id' => 'pn_finance_manager_stocks_api_key',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Twelve Data API Key', 'pn-finance-manager'),
      'placeholder' => __('Enter your Twelve Data API key', 'pn-finance-manager'),
      'description' => __('Enter your free API key from Twelve Data. Get a free key at: https://twelvedata.com/register (800 calls/day, 8 calls/minute).', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_stocks_api_enabled',
      'parent_option' => 'on',
    ];

    $pn_finance_manager_options['pn_finance_manager_stocks_cache_duration'] = [
      'id' => 'pn_finance_manager_stocks_cache_duration',
      'class' => 'pn-finance-manager-select pn-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => [
        '300' => __('5 minutes', 'pn-finance-manager'),
        '900' => __('15 minutes', 'pn-finance-manager'),
        '1800' => __('30 minutes', 'pn-finance-manager'),
        '3600' => __('1 hour', 'pn-finance-manager'),
        '7200' => __('2 hours', 'pn-finance-manager'),
        '86400' => __('24 hours', 'pn-finance-manager'),
      ],
      'label' => __('Cache Duration', 'pn-finance-manager'),
      'placeholder' => __('Select cache duration', 'pn-finance-manager'),
      'description' => __('How long to cache stock data to avoid excessive API calls.', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_stocks_api_enabled',
      'parent_option' => 'on',
    ];

    $pn_finance_manager_options['pn_finance_manager_stocks_manual_update'] = [
      'id' => 'pn_finance_manager_stocks_manual_update',
      'class' => 'pn-finance-manager-btn',
      'input' => 'input',
      'type' => 'button',
      'label' => __('Update Stock Symbols Now', 'pn-finance-manager'),
      'description' => __('Click this button to manually update the list of stock symbols from the API. Last update: ', 'pn-finance-manager') . $this->get_last_symbol_update_time(),
      'parent' => 'pn_finance_manager_stocks_api_enabled',
      'parent_option' => 'on',
    ];

    $pn_finance_manager_options['pn_finance_manager_section_api_end'] = [
      'id' => 'pn_finance_manager_section_api_end',
      'section' => 'end',
    ];

    // --- System Section ---
    $pn_finance_manager_options['pn_finance_manager_section_system_start'] = [
      'id' => 'pn_finance_manager_section_system_start',
      'section' => 'start',
      'label' => __('System', 'pn-finance-manager'),
      'description' => __('General system settings: URL slugs, currency, and plugin cleanup options.', 'pn-finance-manager'),
    ];

    $pn_finance_manager_options['pn_finance_manager_asset_slug'] = [
      'id' => 'pn_finance_manager_asset_slug',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Asset slug', 'pn-finance-manager'),
      'placeholder' => __('Asset slug', 'pn-finance-manager'),
      'description' => __('This option sets the slug of the main Asset archive page, and the Asset pages. By default they will be:', 'pn-finance-manager') . '<br><a href="' . esc_url(home_url('/pn_finance_manager_asset')) . '" target="_blank">' . esc_url(home_url('/pn_finance_manager_asset')) . '</a><br>' . esc_url(home_url('/pn_finance_manager_asset/asset-name')),
    ];

    $pn_finance_manager_options['pn_finance_manager_liability_slug'] = [
      'id' => 'pn_finance_manager_liability_slug',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Liabilities slug', 'pn-finance-manager'),
      'placeholder' => __('Liabilities slug', 'pn-finance-manager'),
      'description' => __('This option sets the slug of the main Liabilities archive page, and the Liabilities pages. By default they will be:', 'pn-finance-manager') . '<br><a href="' . esc_url(home_url('/pn-finance-manager-liability')) . '" target="_blank">' . esc_url(home_url('/pn-finance-manager-liability')) . '</a><br>' . esc_url(home_url('/pn-finance-manager-liability/liability-name')),
    ];

    $pn_finance_manager_options['pn_finance_manager_currency'] = [
      'id' => 'pn_finance_manager_currency',
      'class' => 'pn-finance-manager-select pn-finance-manager-width-100-percent',
      'input' => 'select',
      'options' => PN_FINANCE_MANAGER_Data::pn_finance_manager_get_currencies(),
      'value' => get_option('pn_finance_manager_currency', 'eur'),
      'label' => __('Application Currency', 'pn-finance-manager'),
      'placeholder' => __('Select currency', 'pn-finance-manager'),
      'description' => __('Select the currency that will be used throughout the application for financial calculations and displays.', 'pn-finance-manager'),
    ];

    $pn_finance_manager_options['pn_finance_manager_options_remove'] = [
      'id' => 'pn_finance_manager_options_remove',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'label' => __('Remove plugin options on deactivation', 'pn-finance-manager'),
      'description' => __('If you activate this option the plugin will remove all options on deactivation. Please, be careful. This process cannot be undone.', 'pn-finance-manager'),
    ];

    $pn_finance_manager_options['pn_finance_manager_section_system_end'] = [
      'id' => 'pn_finance_manager_section_system_end',
      'section' => 'end',
    ];

    // --- Colors Section ---
    $pn_finance_manager_options['pn_finance_manager_section_colors_start'] = [
      'id' => 'pn_finance_manager_section_colors_start',
      'section' => 'start',
      'label' => __('Colors', 'pn-finance-manager'),
      'description' => __('Customize the main color used throughout the application for menus, section titles, asset labels, and charts.', 'pn-finance-manager'),
    ];

    $pn_finance_manager_options['pn_finance_manager_color_primary'] = [
      'id' => 'pn_finance_manager_color_primary',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Primary Color', 'pn-finance-manager'),
      'description' => __('Main color for menus, section titles, asset labels, and charts.', 'pn-finance-manager'),
      'value' => '#008080',
    ];

    $pn_finance_manager_options['pn_finance_manager_section_colors_end'] = [
      'id' => 'pn_finance_manager_section_colors_end',
      'section' => 'end',
    ];

    // --- User Management Section ---
    $pn_finance_manager_options['pn_finance_manager_section_roles_start'] = [
      'id' => 'pn_finance_manager_section_roles_start',
      'section' => 'start',
      'label' => __('User Role Management', 'pn-finance-manager'),
      'description' => __('Manage which users have the Finance Manager role. Users with this role can access financial management features.', 'pn-finance-manager'),
    ];
      $pn_finance_manager_options['pn_finance_manager_role_manager_selector'] = [
        'id' => 'pn_finance_manager_role_manager_selector',
        'input' => 'user_role_selector',
        'label' => __('Finance Manager - PN Role', 'pn-finance-manager'),
        'role' => 'pn_finance_manager_role_manager',
        'role_label' => __('Finance Manager - PN', 'pn-finance-manager'),
      ];
    $pn_finance_manager_options['pn_finance_manager_section_roles_end'] = [
      'id' => 'pn_finance_manager_section_roles_end',
      'section' => 'end',
    ];

    $pn_finance_manager_options['pn_finance_manager_nonce'] = [
      'id' => 'pn_finance_manager_nonce',
      'input' => 'input',
      'type' => 'hidden',
    ];
    $pn_finance_manager_options['pn_finance_manager_submit'] = [
      'id' => 'pn_finance_manager_submit',
      'input' => 'input',
      'type' => 'submit',
      'value' => __('Save options', 'pn-finance-manager'),
    ];

    return $pn_finance_manager_options;
  }

  private function get_last_symbol_update_time() {
    $last_update = get_option('pn_finance_manager_stock_symbols_last_update', 0);
    if ($last_update) {
      return date_i18n(get_option('date_format') . ' @ ' . get_option('time_format'), $last_update);
    }
    return __('Never', 'pn-finance-manager');
  }

	/**
	 * Administrator menu.
	 *
	 * @since    1.0.0
	 */
	public function pn_finance_manager_admin_menu() {
    add_menu_page(
      esc_html__('Finance Manager - PN', 'pn-finance-manager'), 
      esc_html__('Finance Manager - PN', 'pn-finance-manager'), 
      'administrator', 
      'pn_finance_manager_options', 
      [$this, 'pn_finance_manager_options'], 
      esc_url(PN_FINANCE_MANAGER_URL . 'assets/media/pn-finance-manager-menu-icon.svg'),
    );
		
    add_submenu_page(
      // 'edit.php?post_type=pnfm_asset', 
      'pn_finance_manager_options',
      esc_html__('Settings', 'pn-finance-manager'), 
      esc_html__('Settings', 'pn-finance-manager'), 
      'manage_pn_finance_manager_options', 
      'pn-finance-manager-options', 
      [$this, 'pn_finance_manager_options'], 
    );
	}

	public function pn_finance_manager_options() {
    $required_pages = self::pn_finance_manager_get_required_pages();
    $missing_pages = [];

    foreach ($required_pages as $page_key => $page_info) {
      if (!self::pn_finance_manager_find_page_by_shortcode($page_info['shortcode'])) {
        $missing_pages[$page_key] = $page_info;
      }
    }
	  ?>
	    <div class="pn-finance-manager-options pn-finance-manager-max-width-1000 pn-finance-manager-margin-auto pn-finance-manager-mt-50 pn-finance-manager-mb-50">
        <img src="<?php echo esc_url(PN_FINANCE_MANAGER_URL . 'assets/media/banner-1544x500.png'); ?>" alt="<?php esc_html_e('Plugin main Banner', 'pn-finance-manager'); ?>" title="<?php esc_html_e('Plugin main Banner', 'pn-finance-manager'); ?>" class="pn-finance-manager-width-100-percent pn-finance-manager-border-radius-20 pn-finance-manager-mb-30">
        <h1 class="pn-finance-manager-mb-30"><?php esc_html_e('Finance Manager - PN Settings', 'pn-finance-manager'); ?></h1>
        <?php if (!empty($missing_pages)): ?>
          <div class="pn-finance-manager-options-fields pn-finance-manager-mb-30">
            <div class="pn-finance-manager-p-30">
              <p class="pn-finance-manager-mb-15">
                <?php esc_html_e('The following required pages have not been detected on your site. Click the buttons below to automatically create them as drafts with the corresponding shortcode. You will be redirected to the editor to review and publish each page.', 'pn-finance-manager'); ?>
              </p>
              <?php foreach ($missing_pages as $page_key => $page_info): ?>
                <button type="button" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-mr-10 pn-finance-manager-mb-10 pn-finance-manager-create-page-btn" data-page-key="<?php echo esc_attr($page_key); ?>">
                  <?php echo esc_html(sprintf(
                    /* translators: %s: Page name */
                    __('Create %s page', 'pn-finance-manager'),
                    $page_info['title']
                  )); ?>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
          <script>
            document.querySelectorAll('.pn-finance-manager-create-page-btn').forEach(function(btn) {
              btn.addEventListener('click', function() {
                var pageKey = this.getAttribute('data-page-key');
                var originalText = this.textContent;
                this.disabled = true;
                this.textContent = '<?php echo esc_js(__('Creating page...', 'pn-finance-manager')); ?>';

                var data = new FormData();
                data.append('action', 'pn_finance_manager_ajax');
                data.append('pn_finance_manager_ajax_type', 'pn_finance_manager_create_page');
                data.append('pn_finance_manager_ajax_nonce', '<?php echo esc_js(wp_create_nonce('pn-finance-manager-nonce')); ?>');
                data.append('page_key', pageKey);

                var currentBtn = this;

                fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                  method: 'POST',
                  body: data
                })
                .then(function(response) { return response.json(); })
                .then(function(result) {
                  if (result.error_key === '' && result.redirect_url) {
                    window.location.href = result.redirect_url;
                  } else {
                    currentBtn.disabled = false;
                    currentBtn.textContent = originalText;
                    alert(result.error_content || '<?php echo esc_js(__('An error occurred while creating the page.', 'pn-finance-manager')); ?>');
                  }
                })
                .catch(function() {
                  currentBtn.disabled = false;
                  currentBtn.textContent = originalText;
                  alert('<?php echo esc_js(__('An error occurred while creating the page.', 'pn-finance-manager')); ?>');
                });
              });
            });
          </script>
        <?php endif; ?>
        <div class="pn-finance-manager-options-fields pn-finance-manager-mb-30">
          <form action="" method="post" id="pn-finance-manager-form-settings" class="pn-finance-manager-form pn-finance-manager-p-30">
          <?php
            $options = self::pn_finance_manager_get_options();

            foreach ($options as $pn_finance_manager_option) {
              PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_wrapper_builder($pn_finance_manager_option, 'option', 0, 0, 'half');
            }
          ?>
          </form> 
          
          <!-- API Status Section -->
          <div class="pn-finance-manager-api-status pn-finance-manager-mt-30 pn-finance-manager-p-20 pn-finance-manager-border-radius-10" style="background-color: #f9f9f9; border: 1px solid #ddd;">
            <h3><?php esc_html_e('API Status & Information', 'pn-finance-manager'); ?></h3>
            <div id="pn-finance-manager-api-status-info">
              <p><?php esc_html_e('Click the button below to check your API status and limits.', 'pn-finance-manager'); ?></p>
              <button type="button" id="pn_finance_manager_check_api_status" class="pn-finance-manager-btn pn-finance-manager-btn-mini">
                <?php esc_html_e('Check API Status', 'pn-finance-manager'); ?>
              </button>
            </div>
            <div id="pn-finance-manager-api-status-result" style="display: none; margin-top: 15px; padding: 10px; border-radius: 5px;"></div>
          </div>
        </div>
      </div>
      <script>
        jQuery(document).ready(function($) {
          $('#pn_finance_manager_stocks_manual_update').on('click', function() {
            var $this = $(this);
            var original_text = $this.val();
            $this.val('Updating...').prop('disabled', true);

            $.ajax({
              url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
              type: 'POST',
              data: {
                action: 'pn_finance_manager_ajax',
                pn_finance_manager_ajax_type: 'pn_finance_manager_manual_stock_update',
                pn_finance_manager_ajax_nonce: '<?php echo esc_js(wp_create_nonce('pn-finance-manager-nonce')); ?>'
              },
              success: function(response) {
                // Manually parse the JSON response if it's a string.
                if (typeof response === 'string') {
                  try {
                    response = JSON.parse(response);
                  } catch (e) {
                    console.error("Failed to parse server response:", e);
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
          
          // API Status Check
          $('#pn_finance_manager_check_api_status').on('click', function() {
            var $this = $(this);
            var original_text = $this.text();
            $this.text('Checking...').prop('disabled', true);
            
            $.ajax({
              url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
              type: 'POST',
              data: {
                action: 'pn_finance_manager_check_api_status',
                nonce: '<?php echo esc_js(wp_create_nonce('pn_finance_manager_nonce')); ?>'
              },
              success: function(response) {
                var $result = $('#pn-finance-manager-api-status-result');
                var statusClass = '';
                var statusText = '';
                
                switch(response.status) {
                  case 'working':
                    statusClass = 'notice-success';
                    statusText = '✅ ' + response.message;
                    break;
                  case 'limit_exceeded':
                    statusClass = 'notice-warning';
                    statusText = '⚠️ ' + response.message;
                    break;
                  case 'error':
                    statusClass = 'notice-error';
                    statusText = '❌ ' + response.message;
                    break;
                  default:
                    statusClass = 'notice-info';
                    statusText = 'ℹ️ ' + response.message;
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
                $('#pn-finance-manager-api-status-result').html(
                  '<div class="notice-error"><p>❌ Failed to check API status. Please try again.</p></div>'
                ).show();
                $this.text(original_text).prop('disabled', false);
              }
            });
          });
        });
      </script>
	  <?php
	}

  public function pn_finance_manager_check_activation() {
    // Only run in admin and not during AJAX requests
    if (!is_admin() || defined('DOING_AJAX')) {
      return;
    }

    // Check if we're already in the redirection process
    if (get_option('pn_finance_manager_redirecting')) {
      delete_option('pn_finance_manager_redirecting');
      return;
    }

    if (get_transient('pn_finance_manager_just_activated')) {
      $target_url = admin_url('admin.php?page=pn_finance_manager_options');
      
      if ($target_url) {
        // Mark that we're in the redirection process
        update_option('pn_finance_manager_redirecting', true);
        
        // Remove the transient
        delete_transient('pn_finance_manager_just_activated');
        
        // Redirect and exit
        wp_safe_redirect(esc_url($target_url));
        exit;
      }
    }
  }

  /**
   * Adds the Settings link to the plugin list
   */
  public function pn_finance_manager_plugin_action_links($links) {
      $settings_link = '<a href="admin.php?page=pn_finance_manager_options">' . esc_html__('Settings', 'pn-finance-manager') . '</a>';
      array_unshift($links, $settings_link);

      return $links;
  }

  /**
   * Find a page containing a specific shortcode.
   *
   * @param string $shortcode The shortcode name to search for.
   * @return int|false Page ID if found, false otherwise.
   */
  public static function pn_finance_manager_find_page_by_shortcode($shortcode) {
    $pages = get_posts([
      'post_type'   => 'page',
      'post_status' => ['publish', 'draft', 'private'],
      'numberposts' => -1,
      'fields'      => 'ids',
    ]);

    foreach ($pages as $page_id) {
      $content = get_post_field('post_content', $page_id);

      if (has_shortcode($content, $shortcode)) {
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
  public static function pn_finance_manager_get_required_pages() {
    return [
      'assets' => [
        'shortcode' => 'pn-finance-manager-asset-list',
        'title'     => __('Assets', 'pn-finance-manager'),
        'label'     => __('Assets page', 'pn-finance-manager'),
      ],
      'liabilities' => [
        'shortcode' => 'pn-finance-manager-liability-list',
        'title'     => __('Liabilities', 'pn-finance-manager'),
        'label'     => __('Liabilities page', 'pn-finance-manager'),
      ],
      'portfolio' => [
        'shortcode' => 'pn-finance-manager-user-assets',
        'title'     => __('Portfolio', 'pn-finance-manager'),
        'label'     => __('Portfolio page', 'pn-finance-manager'),
      ],
      'watchlist' => [
        'shortcode' => 'pn-finance-manager-watchlist',
        'title'     => __('Watchlist', 'pn-finance-manager'),
        'label'     => __('Watchlist page', 'pn-finance-manager'),
      ],
    ];
  }

	/**
	 * Check API status and limits.
	 *
	 * @since    1.0.0
	 * @return   array    API status information.
	 */
	public function pn_finance_manager_check_api_status() {
		$api_key = get_option('pn_finance_manager_stocks_api_key', '');

		if (empty($api_key)) {
			return [
				'status' => 'error',
				'message' => 'API key not configured.',
				'provider' => 'twelvedata',
				'cached_symbols' => 0,
				'last_update' => __('Never', 'pn-finance-manager')
			];
		}

		$stocks = new PN_FINANCE_MANAGER_Stocks();
		$cache_stats = $stocks->pn_finance_manager_get_stock_symbols_cache_stats();

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
add_action('update_option_pn_finance_manager_currency', function($old_value, $value) {
    $transient_key = 'pn_finance_manager_usd_rate_' . strtolower($value);
    delete_transient($transient_key);
}, 10, 2);

// DEBUG: Comprobar si el transient del tipo de cambio existe
add_action('init', function() {
    $currency = get_option('pn_finance_manager_currency', 'eur');
    $transient_key = 'pn_finance_manager_usd_rate_' . strtolower($currency);
    if (false !== get_transient($transient_key)) {
        error_log('Transient ' . $transient_key . ' existe.');
    } else {
        error_log('Transient ' . $transient_key . ' NO existe.');
    }
});