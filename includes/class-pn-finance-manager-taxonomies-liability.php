<?php
/**
 * Liability taxonomies creator.
 *
 * This class defines Liability taxonomies.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_FINANCE_MANAGER
 * @subpackage PN_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_FINANCE_MANAGER_Taxonomies_Liability {
	/**
	 * Register taxonomies.
	 *
	 * @since    1.0.0
	 */
	public static function pn_finance_manager_register_taxonomies() {
		$taxonomies = [
			'pnfm_liability_category' => [
				'name'              		=> _x('Liability category', 'Taxonomy general name', 'pn-finance-manager'),
				'singular_name'     		=> _x('Liability category', 'Taxonomy singular name', 'pn-finance-manager'),
				'search_items'     			=> esc_html(__('Search Liability categories', 'pn-finance-manager')),
	        'all_items'         			=> esc_html(__('All Liability categories', 'pn-finance-manager')),
	        'parent_item'       			=> esc_html(__('Parent Liability category', 'pn-finance-manager')),
	        'parent_item_colon' 			=> esc_html(__('Parent Liability category:', 'pn-finance-manager')),
	        'edit_item'         			=> esc_html(__('Edit Liability category', 'pn-finance-manager')),
	        'update_item'       			=> esc_html(__('Update Liability category', 'pn-finance-manager')),
	        'add_new_item'      			=> esc_html(__('Add New Liability category', 'pn-finance-manager')),
	        'new_item_name'     			=> esc_html(__('New Liability category', 'pn-finance-manager')),
	        'menu_name'         			=> esc_html(__('Liability categories', 'pn-finance-manager')),
				'archive'			      	=> true,
				'slug'			      		=> 'liability-category',
			],
			'pnfm_liability_type' => [
				'name'              		=> _x('Liability type', 'Taxonomy general name', 'pn-finance-manager'),
				'singular_name'     		=> _x('Liability type', 'Taxonomy singular name', 'pn-finance-manager'),
				'search_items'     			=> esc_html(__('Search Liability types', 'pn-finance-manager')),
	        'all_items'         			=> esc_html(__('All Liability types', 'pn-finance-manager')),
	        'parent_item'       			=> esc_html(__('Parent Liability type', 'pn-finance-manager')),
	        'parent_item_colon' 			=> esc_html(__('Parent Liability type:', 'pn-finance-manager')),
	        'edit_item'         			=> esc_html(__('Edit Liability type', 'pn-finance-manager')),
	        'update_item'       			=> esc_html(__('Update Liability type', 'pn-finance-manager')),
	        'add_new_item'      			=> esc_html(__('Add New Liability type', 'pn-finance-manager')),
	        'new_item_name'     			=> esc_html(__('New Liability type', 'pn-finance-manager')),
	        'menu_name'         			=> esc_html(__('Liability types', 'pn-finance-manager')),
				'archive'			      	=> true,
				'slug'			      		=> 'liability-type',
			],
			'pnfm_liability_status' => [
				'name'              		=> _x('Liability status', 'Taxonomy general name', 'pn-finance-manager'),
				'singular_name'     		=> _x('Liability status', 'Taxonomy singular name', 'pn-finance-manager'),
				'search_items'     			=> esc_html(__('Search Liability statuses', 'pn-finance-manager')),
	        'all_items'         			=> esc_html(__('All Liability statuses', 'pn-finance-manager')),
	        'parent_item'       			=> esc_html(__('Parent Liability status', 'pn-finance-manager')),
	        'parent_item_colon' 			=> esc_html(__('Parent Liability status:', 'pn-finance-manager')),
	        'edit_item'         			=> esc_html(__('Edit Liability status', 'pn-finance-manager')),
	        'update_item'       			=> esc_html(__('Update Liability status', 'pn-finance-manager')),
	        'add_new_item'      			=> esc_html(__('Add New Liability status', 'pn-finance-manager')),
	        'new_item_name'     			=> esc_html(__('New Liability status', 'pn-finance-manager')),
	        'menu_name'         			=> esc_html(__('Liability statuses', 'pn-finance-manager')),
				'archive'			      	=> true,
				'slug'			      		=> 'liability-status',
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
	    		'capabilities'      		=> PN_FINANCE_MANAGER_ROLE_PNFM_LIABILITY_CAPABILITIES,
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

			register_taxonomy($taxonomy, 'pnfm_liability', $args);
			register_taxonomy_for_object_type($taxonomy, 'pnfm_liability');
		}
	}
} 