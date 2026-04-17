<?php
/**
 * Asset taxonomies creator.
 *
 * This class defines Asset taxonomies.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Taxonomies_Asset {
	/**
	 * Register taxonomies.
	 *
	 * @since    1.0.0
	 */
	public static function pn_personal_finance_manager_register_taxonomies() {
		$taxonomies = [
			'pnpfm_asset_category' => [
				'name'              		=> _x('Asset category', 'Taxonomy general name', 'pn-personal-finance-manager'),
				'singular_name'     		=> _x('Asset category', 'Taxonomy singular name', 'pn-personal-finance-manager'),
				'search_items'     			=> esc_html(__('Search Asset categories', 'pn-personal-finance-manager')),
	        'all_items'         			=> esc_html(__('All Asset categories', 'pn-personal-finance-manager')),
	        'parent_item'       			=> esc_html(__('Parent Asset category', 'pn-personal-finance-manager')),
	        'parent_item_colon' 			=> esc_html(__('Parent Asset category:', 'pn-personal-finance-manager')),
	        'edit_item'         			=> esc_html(__('Edit Asset category', 'pn-personal-finance-manager')),
	        'update_item'       			=> esc_html(__('Update Asset category', 'pn-personal-finance-manager')),
	        'add_new_item'      			=> esc_html(__('Add New Asset category', 'pn-personal-finance-manager')),
	        'new_item_name'     			=> esc_html(__('New Asset category', 'pn-personal-finance-manager')),
	        'menu_name'         			=> esc_html(__('Asset categories', 'pn-personal-finance-manager')),
				'archive'			      	=> true,
				'slug'			      		=> 'asset-category',
			],
		];

	  foreach ($taxonomies as $taxonomy => $options) {
	  	$labels = [
				'name'          			=> $options['name'],
				'singular_name' 			=> $options['singular_name'],
			];

			$args = [
				'labels'            		=> $labels,
				'hierarchical'      		=> true,
				'public'            		=> true,
				'show_ui' 					=> true,
				'query_var'         		=> true,
				'rewrite'           		=> true,
				'show_in_rest'      		=> true,
	    		'capabilities'      		=> PN_PERSONAL_FINANCE_MANAGER_ROLE_PNPFM_ASSET_CAPABILITIES,
			];

			if ($options['archive']) {
				$args['public'] = true;
				$args['publicly_queryable'] = true;
				$args['show_in_nav_menus'] = true;
				$args['query_var'] = $taxonomy;
				$args['show_ui'] = true;
				$args['rewrite'] = [
					'slug' 					=> $options['slug'],
				];
			}

			register_taxonomy($taxonomy, 'pnpfm_asset', $args);
			register_taxonomy_for_object_type($taxonomy, 'pnpfm_asset');
		}
	}
}