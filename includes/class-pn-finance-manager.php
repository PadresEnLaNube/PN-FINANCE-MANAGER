<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current version of the plugin.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_FINANCE_MANAGER
 * @subpackage PN_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_FINANCE_MANAGER {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PN_FINANCE_MANAGER_Loader    $pn_finance_manager_loader    Maintains and registers all hooks for the plugin.
	 */
	protected $pn_finance_manager_loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $pn_finance_manager_plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $pn_finance_manager_plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $pn_finance_manager_version    The current version of the plugin.
	 */
	protected $pn_finance_manager_version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin. Load the dependencies, define the locale, and set the hooks for the admin area and the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if (defined('PN_FINANCE_MANAGER_VERSION')) {
			$this->pn_finance_manager_version = PN_FINANCE_MANAGER_VERSION;
		} else {
			$this->pn_finance_manager_version = '1.1.3';
		}

		$this->pn_finance_manager_plugin_name = 'pn-finance-manager';

		self::pn_finance_manager_load_dependencies();
		self::pn_finance_manager_load_i18n();
		self::pn_finance_manager_define_common_hooks();
		self::pn_finance_manager_define_admin_hooks();
		self::pn_finance_manager_define_public_hooks();
		self::pn_finance_manager_define_custom_post_types();
		self::pn_finance_manager_define_taxonomies();
		self::pn_finance_manager_load_ajax();
		self::pn_finance_manager_load_ajax_nopriv();
		self::pn_finance_manager_load_data();
		self::pn_finance_manager_load_templates();
		self::pn_finance_manager_load_settings();
		self::pn_finance_manager_load_shortcodes();
		self::pn_finance_manager_load_cron_jobs();
	}
			
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 * - PN_FINANCE_MANAGER_Loader. Orchestrates the hooks of the plugin.
	 * - PN_FINANCE_MANAGER_i18n. Defines internationalization functionality.
	 * - PN_FINANCE_MANAGER_Common. Defines hooks used accross both, admin and public side.
	 * - PN_FINANCE_MANAGER_Admin. Defines all hooks for the admin area.
	 * - PN_FINANCE_MANAGER_Public. Defines all hooks for the public side of the site.
	 * - PN_FINANCE_MANAGER_Post_Type_Asset. Defines Asset custom post type.
	 * - PN_FINANCE_MANAGER_Taxonomies_Asset. Defines Asset taxonomies.
	 * - PN_FINANCE_MANAGER_Templates. Load plugin templates.
	 * - PN_FINANCE_MANAGER_Data. Load main usefull data.
	 * - PN_FINANCE_MANAGER_Functions_Post. Posts management functions.
	 * - PN_FINANCE_MANAGER_Functions_User. Users management functions.
	 * - PN_FINANCE_MANAGER_Functions_Attachment. Attachments management functions.
	 * - PN_FINANCE_MANAGER_Functions_Settings. Define settings.
	 * - PN_FINANCE_MANAGER_Functions_Forms. Forms management functions.
	 * - PN_FINANCE_MANAGER_Functions_Ajax. Ajax functions.
	 * - PN_FINANCE_MANAGER_Functions_Ajax_Nopriv. Ajax No Private functions.
	 * - PN_FINANCE_MANAGER_Popups. Define popups functionality.
	 * - PN_FINANCE_MANAGER_Functions_Shortcodes. Define all shortcodes for the platform.
	 * - PN_FINANCE_MANAGER_Functions_Validation. Define validation and sanitization.
	 * - PN_FINANCE_MANAGER_Stocks. Define stocks functionality.
	 *
	 * Create an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-i18n.php';

		/**
		 * The class responsible for defining all actions that occur both in the admin area and in the public-facing side of the site.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-common.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/admin/class-pn-finance-manager-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/public/class-pn-finance-manager-public.php';

		/**
		 * The class responsible for create the Asset custom post type.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-post-type-asset.php';

		/**
		 * The class responsible for create the Asset custom taxonomies.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-taxonomies-asset.php';

		/**
		 * The class responsible for create the Liability custom post type.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-post-type-liability.php';

		/**
		 * The class responsible for create the Liability custom taxonomies.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-taxonomies-liability.php';

		/**
		 * The class responsible for plugin templates.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-templates.php';

		/**
		 * The class responsible for stocks functionality.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-stocks.php';

		/**
		 * The class responsible for watchlist functionality.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-watchlist.php';

		/**
		 * The class getting key data of the platform.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-data.php';

		/**
		 * The class defining posts management functions.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-functions-post.php';

		/**
		 * The class defining users management functions.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-functions-user.php';

		/**
		 * The class defining attahcments management functions.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-functions-attachment.php';

		/**
		 * The class defining settings.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-settings.php';

		/**
		 * The class defining form management.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-forms.php';

		/**
		 * The class defining ajax functions.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-ajax.php';

		/**
		 * The class defining no private ajax functions.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-ajax-nopriv.php';

		/**
		 * The class defining shortcodes.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-shortcodes.php';

		/**
		 * The class defining validation and sanitization.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-validation.php';

		/**
		 * The class responsible for popups functionality.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-popups.php';

		/**
		 * The class managing the custom selector component.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-selector.php';

		/**
		 * The class responsible for the Tools admin page.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-tools.php';

		/**
		 * The class responsible for export/import portfolio functionality.
		 */
		require_once PN_FINANCE_MANAGER_DIR . 'includes/class-pn-finance-manager-export-import.php';

		$this->pn_finance_manager_loader = new PN_FINANCE_MANAGER_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PN_FINANCE_MANAGER_i18n class in order to set the domain and to register the hook with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_load_i18n() {
		$plugin_i18n = new PN_FINANCE_MANAGER_i18n();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('after_setup_theme', $plugin_i18n, 'pn_finance_manager_load_plugin_textdomain');

		if (class_exists('Polylang')) {
			$this->pn_finance_manager_loader->pn_finance_manager_add_filter('pll_get_post_types', $plugin_i18n, 'pn_finance_manager_pll_get_post_types', 10, 2);
    }
	}

	/**
	 * Register all of the hooks related to the main functionalities of the plugin, common to public and admin faces.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_define_common_hooks() {
		$plugin_common = new PN_FINANCE_MANAGER_Common(self::pn_finance_manager_get_plugin_name(), self::pn_finance_manager_get_version());
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_enqueue_scripts', $plugin_common, 'pn_finance_manager_enqueue_styles');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_enqueue_scripts', $plugin_common, 'pn_finance_manager_enqueue_scripts');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_enqueue_scripts', $plugin_common, 'pn_finance_manager_enqueue_styles');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_enqueue_scripts', $plugin_common, 'pn_finance_manager_enqueue_scripts');
		$this->pn_finance_manager_loader->pn_finance_manager_add_filter('body_class', $plugin_common, 'pn_finance_manager_body_classes');

		$plugin_post_type_asset = new PN_FINANCE_MANAGER_Post_Type_Asset();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('pn_finance_manager_asset_form_save', $plugin_post_type_asset, 'pn_finance_manager_asset_form_save', 999, 5);

		$plugin_post_type_liability = new PN_FINANCE_MANAGER_Post_Type_Liability();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('pn_finance_manager_liability_form_save', $plugin_post_type_liability, 'pn_finance_manager_liability_form_save', 999, 5);

		// Export/Import: inject portfolio tab into userspn profile popup
		if (class_exists('USERSPN')) {
			$plugin_export_import = new PN_FINANCE_MANAGER_Export_Import();
			$this->pn_finance_manager_loader->pn_finance_manager_add_filter('userspn_profile_content', $plugin_export_import, 'pn_finance_manager_add_profile_tab', 10, 2);
		}
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_define_admin_hooks() {
		$plugin_admin = new PN_FINANCE_MANAGER_Admin($this->pn_finance_manager_get_plugin_name(), $this->pn_finance_manager_get_version());
		$plugin_settings = new PN_FINANCE_MANAGER_Settings();
		$plugin_stocks = new PN_FINANCE_MANAGER_Stocks();

		$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_enqueue_scripts', $plugin_admin, 'pn_finance_manager_enqueue_styles');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_enqueue_scripts', $plugin_admin, 'pn_finance_manager_enqueue_scripts');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_menu', $plugin_settings, 'pn_finance_manager_admin_menu');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_init', $plugin_settings, 'pn_finance_manager_check_activation');
		$this->pn_finance_manager_loader->pn_finance_manager_add_filter('plugin_action_links_' . plugin_basename(PN_FINANCE_MANAGER_FILE), $plugin_settings, 'pn_finance_manager_plugin_action_links');

		$plugin_tools = new PN_FINANCE_MANAGER_Tools();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_menu', $plugin_tools, 'pn_finance_manager_tools_menu');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_define_public_hooks() {
		$plugin_public = new PN_FINANCE_MANAGER_Public(self::pn_finance_manager_get_plugin_name(), self::pn_finance_manager_get_version());
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_enqueue_scripts', $plugin_public, 'pn_finance_manager_enqueue_styles');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_enqueue_scripts', $plugin_public, 'pn_finance_manager_enqueue_scripts');

		$plugin_user = new PN_FINANCE_MANAGER_Functions_User();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_login', $plugin_user, 'pn_finance_manager_user_wp_login');
	}

	/**
	 * Register all Post Types with meta boxes and templates.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_define_custom_post_types() {
		$plugin_post_type_asset = new PN_FINANCE_MANAGER_Post_Type_Asset();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('init', $plugin_post_type_asset, 'pn_finance_manager_asset_register_post_type');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_init', $plugin_post_type_asset, 'pn_finance_manager_asset_add_meta_box');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('save_post_pnfm_asset', $plugin_post_type_asset, 'pn_finance_manager_asset_save_post', 10, 3);
		$this->pn_finance_manager_loader->pn_finance_manager_add_filter('single_template', $plugin_post_type_asset, 'pn_finance_manager_asset_single_template', 10, 3);
		$this->pn_finance_manager_loader->pn_finance_manager_add_filter('archive_template', $plugin_post_type_asset, 'pn_finance_manager_asset_archive_template', 10, 3);
		$this->pn_finance_manager_loader->pn_finance_manager_add_shortcode('pn-finance-manager-asset-list', $plugin_post_type_asset, 'pn_finance_manager_asset_list_wrapper');

		// Liability Integration
		$plugin_post_type_liability = new PN_FINANCE_MANAGER_Post_Type_Liability();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('init', $plugin_post_type_liability, 'pn_finance_manager_liability_register_post_type');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_init', $plugin_post_type_liability, 'pn_finance_manager_liability_add_meta_box');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('save_post_pnfm_liability', $plugin_post_type_liability, 'pn_finance_manager_liability_save_post', 10, 3);
		$this->pn_finance_manager_loader->pn_finance_manager_add_filter('single_template', $plugin_post_type_liability, 'pn_finance_manager_liability_single_template', 10, 3);
		$this->pn_finance_manager_loader->pn_finance_manager_add_filter('archive_template', $plugin_post_type_liability, 'pn_finance_manager_liability_archive_template', 10, 3);
		$this->pn_finance_manager_loader->pn_finance_manager_add_shortcode('pn-finance-manager-liability-list', $plugin_post_type_liability, 'pn_finance_manager_liability_list_wrapper');
	}

	/**
	 * Register all of the hooks related to Taxonomies.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_define_taxonomies() {
		$plugin_taxonomies_asset = new PN_FINANCE_MANAGER_Taxonomies_Asset();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('init', $plugin_taxonomies_asset, 'pn_finance_manager_register_taxonomies');
		
		// Liability Taxonomies
		$plugin_taxonomies_liability = new PN_FINANCE_MANAGER_Taxonomies_Liability();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('init', $plugin_taxonomies_liability, 'pn_finance_manager_register_taxonomies');
	}

	/**
	 * Load most common data used on the platform.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_load_data() {
		$plugin_data = new PN_FINANCE_MANAGER_Data();

		if (is_admin()) {
			$this->pn_finance_manager_loader->pn_finance_manager_add_action('init', $plugin_data, 'pn_finance_manager_load_plugin_data');
		} else {
			$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_footer', $plugin_data, 'pn_finance_manager_load_plugin_data');
		}

		$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_footer', $plugin_data, 'pn_finance_manager_flush_rewrite_rules');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_footer', $plugin_data, 'pn_finance_manager_flush_rewrite_rules');
	}

	/**
	 * Register templates.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_load_templates() {
		if (!defined('DOING_AJAX')) {
			$plugin_templates = new PN_FINANCE_MANAGER_Templates();
			$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_footer', $plugin_templates, 'load_plugin_templates');
			$this->pn_finance_manager_loader->pn_finance_manager_add_action('admin_footer', $plugin_templates, 'load_plugin_templates');
		}
	}

	/**
	 * Register settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_load_settings() {
		// Hooks already registered in pn_finance_manager_define_admin_hooks()
	}

	/**
	 * Load ajax functions.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_load_ajax() {
		$plugin_ajax = new PN_FINANCE_MANAGER_Ajax();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_ajax_pn_finance_manager_ajax', $plugin_ajax, 'pn_finance_manager_ajax_server');
	}

	/**
	 * Load no private ajax functions.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_load_ajax_nopriv() {
		$plugin_ajax_nopriv = new PN_FINANCE_MANAGER_Ajax_Nopriv();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_ajax_pn_finance_manager_ajax_nopriv', $plugin_ajax_nopriv, 'pn_finance_manager_ajax_nopriv_server');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('wp_ajax_nopriv_pn_finance_manager_ajax_nopriv', $plugin_ajax_nopriv, 'pn_finance_manager_ajax_nopriv_server');
	}

	/**
	 * Register cron jobs.
	 *
	 * @since    1.0.5
	 * @access   private
	 */
	private function pn_finance_manager_load_cron_jobs() {
		$plugin_stocks = new PN_FINANCE_MANAGER_Stocks();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('pn_finance_manager_update_stock_symbols_event', $plugin_stocks, 'pn_finance_manager_update_stock_symbols_from_api_cron');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('pn_finance_manager_daily_stock_price_recording_event', $plugin_stocks, 'pn_finance_manager_daily_stock_price_recording');
		// Crypto cron jobs
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('pn_finance_manager_update_crypto_symbols_event', $plugin_stocks, 'pn_finance_manager_update_crypto_symbols_from_api_cron');
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('pn_finance_manager_daily_crypto_price_recording_event', $plugin_stocks, 'pn_finance_manager_daily_crypto_price_recording');

		// Watchlist cron jobs
		$plugin_watchlist = new PN_FINANCE_MANAGER_Watchlist();
		$this->pn_finance_manager_loader->pn_finance_manager_add_action('pn_finance_manager_watchlist_check_alerts_event', $plugin_watchlist, 'pn_finance_manager_watchlist_check_alerts');
	}

	/**
	 * Register shortcodes of the platform.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_finance_manager_load_shortcodes() {
		$plugin_shortcodes = new PN_FINANCE_MANAGER_Shortcodes();
		$this->pn_finance_manager_loader->pn_finance_manager_add_shortcode('pn-finance-manager-asset', $plugin_shortcodes, 'pn_finance_manager_asset');
		$this->pn_finance_manager_loader->pn_finance_manager_add_shortcode('pn-finance-manager-liability', $plugin_shortcodes, 'pn_finance_manager_liability');
		$this->pn_finance_manager_loader->pn_finance_manager_add_shortcode('pn-finance-manager-test', $plugin_shortcodes, 'pn_finance_manager_test');
		$this->pn_finance_manager_loader->pn_finance_manager_add_shortcode('pn-finance-manager-call-to-action', $plugin_shortcodes, 'pn_finance_manager_call_to_action');
		$this->pn_finance_manager_loader->pn_finance_manager_add_shortcode('pn-finance-manager-user-assets', $plugin_shortcodes, 'pn_finance_manager_user_assets_shortcode');
		$this->pn_finance_manager_loader->pn_finance_manager_add_shortcode('pn-finance-manager-stock-performance', $plugin_shortcodes, 'pn_finance_manager_stock_performance_shortcode');

		$plugin_watchlist = new PN_FINANCE_MANAGER_Watchlist();
		$this->pn_finance_manager_loader->pn_finance_manager_add_shortcode('pn-finance-manager-watchlist', $plugin_watchlist, 'pn_finance_manager_watchlist_render');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress. Then it flushes the rewrite rules if needed.
	 *
	 * @since    1.0.0
	 */
	public function pn_finance_manager_run() {
		$this->pn_finance_manager_loader->pn_finance_manager_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function pn_finance_manager_get_plugin_name() {
		return $this->pn_finance_manager_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    PN_FINANCE_MANAGER_Loader    Orchestrates the hooks of the plugin.
	 */
	public function pn_finance_manager_get_loader() {
		return $this->pn_finance_manager_loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function pn_finance_manager_get_version() {
		return $this->pn_finance_manager_version;
	}
}