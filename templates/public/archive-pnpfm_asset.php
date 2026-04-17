<?php 
/**
 * Provide an archive page for Assets
 *
 * This file is used to provide an archive page for Asset
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 *
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/common/templates
 */

	if (!defined('ABSPATH')) exit; // Exit if accessed directly

	// Check if user is logged in
	if (!is_user_logged_in()) {
		wp_die(esc_html__('You must be logged in to view assets.', 'pn-personal-finance-manager'), esc_html__('Access Denied', 'pn-personal-finance-manager'), ['response' => 403]);
	}

	if(wp_is_block_theme()) {
  		wp_head();
		block_template_part('header');
	} else {
  		get_header();
	}

	if (class_exists('Polylang')) {
		$assets = get_posts(['numberposts' => -1, 'fields' => 'ids', 'post_type' => 'pnpfm_asset', 'lang' => pll_current_language(), 'post_status' => ['publish'], 'order' => 'DESC', ]);
	} else {
		$assets = get_posts(['numberposts' => -1, 'fields' => 'ids', 'post_type' => 'pnpfm_asset', 'post_status' => ['publish'], 'order' => 'DESC', ]);
	}
	
	// Filter assets based on user permissions
	$assets = PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_filter_user_posts($assets, 'pnpfm_asset');
?>
	<body <?php body_class(); ?>>
		<div class="pn-personal-finance-manager-wrapper pn-personal-finance-manager-asset-wrapper">
		  <h1 class="pn-personal-finance-manager-p-20"><?php esc_html_e('Base CPT', 'pn-personal-finance-manager'); ?></h1>
			
			<div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent pn-personal-finance-manager-mt-50 pn-personal-finance-manager-mb-50">
				<?php if (!empty($assets)): ?>
			  	<?php foreach ($assets as $asset_id): ?>
						<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-33-percent pn-personal-finance-manager-tablet-display-block pn-personal-finance-manager-tablet-width-100-percent pn-personal-finance-manager-p-20 pn-personal-finance-manager-text-align-center pn-personal-finance-manager-vertical-align-top">
							<div class="pn-personal-finance-manager-mb-30">
								<a href="<?php echo esc_url(get_permalink($asset_id)); ?>">
									<?php if (has_post_thumbnail($asset_id)): ?>
								    <?php echo get_the_post_thumbnail($asset_id, 'full', ['class' => 'pn-personal-finance-manager-border-radius-20 pn-personal-finance-manager-width-100-percent']); ?>
								  <?php else: ?>
								  	<img src="<?php echo esc_url(PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/media/pn-personal-finance-manager-image.jpg'); ?>" class="pn-personal-finance-manager-border-radius-20 pn-personal-finance-manager-width-100-percent">
								  <?php endif ?>
								</a>
							</div>

							<a href="<?php echo esc_url(get_permalink($asset_id)); ?>"><h4 class="pn-personal-finance-manager-color-main-hover pn-personal-finance-manager-mb-20"><?php echo esc_html(get_the_title($asset_id)); ?></h4></a>

							<?php if (current_user_can('administrator') || current_user_can('pn_personal_finance_manager_role_manager')): ?>
				  			<a href="<?php echo esc_url(admin_url('post.php?post=' . $asset_id . '&action=edit')); ?>"><i class="material-icons-outlined pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-mr-10 pn-personal-finance-manager-color-main-0">edit</i> <?php esc_html_e('Edit asset', 'pn-personal-finance-manager'); ?></a>
				  		<?php endif ?>
						</div>
			  	<?php endforeach ?>
				<?php endif ?>

				<?php if (current_user_can('administrator') || current_user_can('pn_personal_finance_manager_role_manager')): ?>
					<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-33-percent pn-personal-finance-manager-tablet-display-block pn-personal-finance-manager-tablet-width-100-percent pn-personal-finance-manager-p-20 pn-personal-finance-manager-text-align-center pn-personal-finance-manager-vertical-align-top">
						<div class="pn-personal-finance-manager-mb-30">
							<a href="<?php echo esc_url(admin_url('post-new.php?post_type=pnpfm_asset')); ?>">
								<img src="<?php echo esc_url(PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/media/pn-personal-finance-manager-image.jpg'); ?>" class="pn-personal-finance-manager-border-radius-20 pn-personal-finance-manager-width-100-percent pn-personal-finance-manager-filter-grayscale">
							</a>
						</div>

						<a href="<?php echo esc_url(admin_url('post-new.php?post_type=pnpfm_asset')); ?>"><h4 class="pn-personal-finance-manager-color-main-hover pn-personal-finance-manager-mb-20"><i class="material-icons-outlined pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-mr-10">add</i> <?php esc_html_e('Add asset', 'pn-personal-finance-manager'); ?></h4></a>
					</div>
				<?php endif ?>
			</div>
		</div>
	</body>
<?php 
	if(wp_is_block_theme()) {
  	wp_footer();
		block_template_part('footer');
	} else {
  	get_footer();
	}
?>