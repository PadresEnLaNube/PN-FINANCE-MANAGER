<?php
/**
 * Gutenberg Blocks registration.
 *
 * Registers custom Gutenberg blocks that mirror the plugin's shortcodes,
 * providing a modern block-editor experience.
 *
 * @since      1.2.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Blocks {

	/**
	 * Register all Gutenberg blocks and the editor script.
	 *
	 * Hooked to 'init'.
	 *
	 * @since 1.2.0
	 */
	public function pn_personal_finance_manager_register_blocks() {

		// Register block category.
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
			add_filter( 'block_categories_all', [ $this, 'pn_personal_finance_manager_register_block_category' ], 10, 2 );
		} else {
			add_filter( 'block_categories', [ $this, 'pn_personal_finance_manager_register_block_category' ], 10, 2 );
		}

		// Register the editor-only script (no build step – uses WP globals).
		wp_register_script(
			'pn-personal-finance-manager-blocks-editor',
			PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/js/admin/pn-personal-finance-manager-blocks-editor.js',
			[ 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n' ],
			PN_PERSONAL_FINANCE_MANAGER_VERSION,
			true
		);

		// Pass the plugin primary color to the editor script.
		wp_localize_script( 'pn-personal-finance-manager-blocks-editor', 'pn_personal_finance_manager_blocks_editor', [
			'primaryColor' => sanitize_hex_color( get_option( 'pn_personal_finance_manager_color_primary', '#008080' ) ),
		] );

		// --- Assets List block ---
		register_block_type( 'pn-personal-finance-manager/assets-list', [
			'editor_script'   => 'pn-personal-finance-manager-blocks-editor',
			'render_callback' => [ $this, 'pn_personal_finance_manager_render_assets_list_block' ],
		] );

		// --- Liabilities List block ---
		register_block_type( 'pn-personal-finance-manager/liabilities-list', [
			'editor_script'   => 'pn-personal-finance-manager-blocks-editor',
			'render_callback' => [ $this, 'pn_personal_finance_manager_render_liabilities_list_block' ],
		] );

		// --- Portfolio block ---
		register_block_type( 'pn-personal-finance-manager/portfolio', [
			'editor_script'   => 'pn-personal-finance-manager-blocks-editor',
			'attributes'      => [
				'display_type' => [
					'type'    => 'string',
					'default' => 'portfolio',
				],
			],
			'render_callback' => [ $this, 'pn_personal_finance_manager_render_portfolio_block' ],
		] );

		// --- Watchlist block ---
		register_block_type( 'pn-personal-finance-manager/watchlist', [
			'editor_script'   => 'pn-personal-finance-manager-blocks-editor',
			'render_callback' => [ $this, 'pn_personal_finance_manager_render_watchlist_block' ],
		] );
	}

	/**
	 * Register a custom block category for the plugin.
	 *
	 * @since 1.2.0
	 * @param array $categories Existing block categories.
	 * @return array
	 */
	public function pn_personal_finance_manager_register_block_category( $categories ) {
		return array_merge( $categories, [
			[
				'slug'  => 'pn-personal-finance-manager',
				'title' => __( 'Personal Finance Manager', 'pn-personal-finance-manager' ),
				'icon'  => 'chart-area',
			],
		] );
	}

	/* ------------------------------------------------------------------
	 * Render callbacks
	 * ----------------------------------------------------------------*/

	/**
	 * Render: Assets List block.
	 *
	 * @since 1.2.0
	 * @return string
	 */
	public function pn_personal_finance_manager_render_assets_list_block() {
		$instance = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Asset();
		return $instance->pn_personal_finance_manager_asset_list_wrapper();
	}

	/**
	 * Render: Liabilities List block.
	 *
	 * @since 1.2.0
	 * @return string
	 */
	public function pn_personal_finance_manager_render_liabilities_list_block() {
		$instance = new PN_PERSONAL_FINANCE_MANAGER_Post_Type_Liability();
		return $instance->pn_personal_finance_manager_liability_list_wrapper();
	}

	/**
	 * Render: Portfolio block.
	 *
	 * @since 1.2.0
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function pn_personal_finance_manager_render_portfolio_block( $attributes ) {
		$display_type = isset( $attributes['display_type'] ) ? $attributes['display_type'] : 'portfolio';
		$instance = new PN_PERSONAL_FINANCE_MANAGER_Shortcodes();
		return $instance->pn_personal_finance_manager_user_assets_shortcode( [
			'display_type' => $display_type,
		] );
	}

	/**
	 * Render: Watchlist block.
	 *
	 * @since 1.2.0
	 * @return string
	 */
	public function pn_personal_finance_manager_render_watchlist_block() {
		$instance = new PN_PERSONAL_FINANCE_MANAGER_Watchlist();
		return $instance->pn_personal_finance_manager_watchlist_render( [] );
	}

	/* ------------------------------------------------------------------
	 * Static helpers
	 * ----------------------------------------------------------------*/

	/**
	 * Detect whether the block editor (Gutenberg) is active for pages.
	 *
	 * @since 1.2.0
	 * @return bool
	 */
	public static function pn_personal_finance_manager_is_block_editor_active() {
		// Classic Editor plugin: check its option.
		if ( function_exists( 'classic_editor_settings' ) || class_exists( 'Classic_Editor' ) ) {
			$editor = get_option( 'classic-editor-replace', 'classic' );
			if ( $editor === 'classic' ) {
				return false;
			}
		}

		// WordPress 5.0+ ships with the block editor by default.
		return version_compare( get_bloginfo( 'version' ), '5.0', '>=' );
	}

	/**
	 * Centralised map of shortcode tags to their Gutenberg block names.
	 *
	 * @since 1.2.0
	 * @return array  [ 'shortcode-tag' => 'namespace/block-name', ... ]
	 */
	public static function pn_personal_finance_manager_get_shortcode_to_block_map() {
		return [
			'pn-personal-finance-manager-asset-list'     => 'pn-personal-finance-manager/assets-list',
			'pn-personal-finance-manager-liability-list'  => 'pn-personal-finance-manager/liabilities-list',
			'pn-personal-finance-manager-user-assets'     => 'pn-personal-finance-manager/portfolio',
			'pn-personal-finance-manager-watchlist'        => 'pn-personal-finance-manager/watchlist',
		];
	}

	/**
	 * Get the block markup for a given shortcode tag.
	 *
	 * @since 1.2.0
	 * @param string $shortcode Shortcode tag.
	 * @return string Block comment markup or empty string.
	 */
	public static function pn_personal_finance_manager_get_block_markup( $shortcode ) {
		$map = self::pn_personal_finance_manager_get_shortcode_to_block_map();

		if ( ! isset( $map[ $shortcode ] ) ) {
			return '';
		}

		$block_name = $map[ $shortcode ];

		return '<!-- wp:' . $block_name . ' /-->';
	}
}
