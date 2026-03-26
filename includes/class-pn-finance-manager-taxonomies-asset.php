<?php
/**
 * Asset taxonomies creator.
 *
 * This class defines Asset taxonomies.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_FINANCE_MANAGER
 * @subpackage PN_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_FINANCE_MANAGER_Taxonomies_Asset {
	/**
	 * Register taxonomies.
	 *
	 * @since    1.0.0
	 */
	public static function pn_finance_manager_register_taxonomies() {
		$taxonomies = [
			'pnfm_asset_category' => [
				'name'              		=> _x('Asset category', 'Taxonomy general name', 'pn-finance-manager'),
				'singular_name'     		=> _x('Asset category', 'Taxonomy singular name', 'pn-finance-manager'),
				'search_items'     			=> esc_html(__('Search Asset categories', 'pn-finance-manager')),
	        'all_items'         			=> esc_html(__('All Asset categories', 'pn-finance-manager')),
	        'parent_item'       			=> esc_html(__('Parent Asset category', 'pn-finance-manager')),
	        'parent_item_colon' 			=> esc_html(__('Parent Asset category:', 'pn-finance-manager')),
	        'edit_item'         			=> esc_html(__('Edit Asset category', 'pn-finance-manager')),
	        'update_item'       			=> esc_html(__('Update Asset category', 'pn-finance-manager')),
	        'add_new_item'      			=> esc_html(__('Add New Asset category', 'pn-finance-manager')),
	        'new_item_name'     			=> esc_html(__('New Asset category', 'pn-finance-manager')),
	        'menu_name'         			=> esc_html(__('Asset categories', 'pn-finance-manager')),
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
	    		'capabilities'      		=> PN_FINANCE_MANAGER_ROLE_PNFM_ASSET_CAPABILITIES,
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

			register_taxonomy($taxonomy, 'pnfm_asset', $args);
			register_taxonomy_for_object_type($taxonomy, 'pnfm_asset');
		}
	}
}