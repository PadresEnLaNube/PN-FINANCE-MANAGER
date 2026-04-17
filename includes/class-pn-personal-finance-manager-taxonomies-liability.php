<?php
/**
 * Liability taxonomies creator.
 *
 * This class defines Liability taxonomies.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_PERSONAL_FINANCE_MANAGER_Taxonomies_Liability {
	/**
	 * Register taxonomies.
	 *
	 * @since    1.0.0
	 */
	public static function pn_personal_finance_manager_register_taxonomies() {
		$taxonomies = [
			'pnpfm_liability_category' => [
				'name'              		=> _x('Liability category', 'Taxonomy general name', 'pn-personal-finance-manager'),
				'singular_name'     		=> _x('Liability category', 'Taxonomy singular name', 'pn-personal-finance-manager'),
				'search_items'     			=> esc_html(__('Search Liability categories', 'pn-personal-finance-manager')),
	        'all_items'         			=> esc_html(__('All Liability categories', 'pn-personal-finance-manager')),
	        'parent_item'       			=> esc_html(__('Parent Liability category', 'pn-personal-finance-manager')),
	        'parent_item_colon' 			=> esc_html(__('Parent Liability category:', 'pn-personal-finance-manager')),
	        'edit_item'         			=> esc_html(__('Edit Liability category', 'pn-personal-finance-manager')),
	        'update_item'       			=> esc_html(__('Update Liability category', 'pn-personal-finance-manager')),
	        'add_new_item'      			=> esc_html(__('Add New Liability category', 'pn-personal-finance-manager')),
	        'new_item_name'     			=> esc_html(__('New Liability category', 'pn-personal-finance-manager')),
	        'menu_name'         			=> esc_html(__('Liability categories', 'pn-personal-finance-manager')),
				'archive'			      	=> true,
				'slug'			      		=> 'liability-category',
			],
			'pnpfm_liability_type' => [
				'name'              		=> _x('Liability type', 'Taxonomy general name', 'pn-personal-finance-manager'),
				'singular_name'     		=> _x('Liability type', 'Taxonomy singular name', 'pn-personal-finance-manager'),
				'search_items'     			=> esc_html(__('Search Liability types', 'pn-personal-finance-manager')),
	        'all_items'         			=> esc_html(__('All Liability types', 'pn-personal-finance-manager')),
	        'parent_item'       			=> esc_html(__('Parent Liability type', 'pn-personal-finance-manager')),
	        'parent_item_colon' 			=> esc_html(__('Parent Liability type:', 'pn-personal-finance-manager')),
	        'edit_item'         			=> esc_html(__('Edit Liability type', 'pn-personal-finance-manager')),
	        'update_item'       			=> esc_html(__('Update Liability type', 'pn-personal-finance-manager')),
	        'add_new_item'      			=> esc_html(__('Add New Liability type', 'pn-personal-finance-manager')),
	        'new_item_name'     			=> esc_html(__('New Liability type', 'pn-personal-finance-manager')),
	        'menu_name'         			=> esc_html(__('Liability types', 'pn-personal-finance-manager')),
				'archive'			      	=> true,
				'slug'			      		=> 'liability-type',
			],
			'pnpfm_liability_status' => [
				'name'              		=> _x('Liability status', 'Taxonomy general name', 'pn-personal-finance-manager'),
				'singular_name'     		=> _x('Liability status', 'Taxonomy singular name', 'pn-personal-finance-manager'),
				'search_items'     			=> esc_html(__('Search Liability statuses', 'pn-personal-finance-manager')),
	        'all_items'         			=> esc_html(__('All Liability statuses', 'pn-personal-finance-manager')),
	        'parent_item'       			=> esc_html(__('Parent Liability status', 'pn-personal-finance-manager')),
	        'parent_item_colon' 			=> esc_html(__('Parent Liability status:', 'pn-personal-finance-manager')),
	        'edit_item'         			=> esc_html(__('Edit Liability status', 'pn-personal-finance-manager')),
	        'update_item'       			=> esc_html(__('Update Liability status', 'pn-personal-finance-manager')),
	        'add_new_item'      			=> esc_html(__('Add New Liability status', 'pn-personal-finance-manager')),
	        'new_item_name'     			=> esc_html(__('New Liability status', 'pn-personal-finance-manager')),
	        'menu_name'         			=> esc_html(__('Liability statuses', 'pn-personal-finance-manager')),
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
	    		'capabilities'      		=> PN_PERSONAL_FINANCE_MANAGER_ROLE_PNPFM_LIABILITY_CAPABILITIES,
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

			register_taxonomy($taxonomy, 'pnpfm_liability', $args);
			register_taxonomy_for_object_type($taxonomy, 'pnpfm_liability');
		}
	}
} 