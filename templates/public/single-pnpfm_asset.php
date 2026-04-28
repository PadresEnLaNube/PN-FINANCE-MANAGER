<?php	
/**
 * Provide a common footer area view for the plugin
 *
 * This file is used to markup the common footer facing aspects of the plugin.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 *
 * @package    PN_PERSONAL_FINANCE_MANAGER
 * @subpackage PN_PERSONAL_FINANCE_MANAGER/common/templates
 */

	if (!defined('ABSPATH')) exit; // Exit if accessed directly

	$post_id = get_the_ID();
	
	// Check if user is logged in
	if (!is_user_logged_in()) {
		wp_die(esc_html__('You must be logged in to view assets.', 'pn-personal-finance-manager'), esc_html__('Access Denied', 'pn-personal-finance-manager'), ['response' => 403]);
	}

	// Check if user can view this asset
	if (!PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_user_can_view_post($post_id, 'pnpfm_asset')) {
		wp_die(esc_html__('You do not have permission to view this asset.', 'pn-personal-finance-manager'), esc_html__('Access Denied', 'pn-personal-finance-manager'), ['response' => 403]);
	}

	if(wp_is_block_theme()) {
  	wp_head();
		block_template_part('header');
	} else {
  		get_header();
	}

	$ingredients = get_post_meta($post_id, 'pn_personal_finance_manager_ingredients_name', true);
	$steps = get_post_meta($post_id, 'pn_personal_finance_manager_steps_name', true);
	$steps_description = get_post_meta($post_id, 'pn_personal_finance_manager_steps_description', true);
	$steps_time = get_post_meta($post_id, 'pn_personal_finance_manager_steps_time', true);
	$steps_total_time = get_post_meta($post_id, 'pn_personal_finance_manager_time', true);
	$pn_personal_finance_manager_images = explode(',', get_post_meta($post_id, 'pn_personal_finance_manager_images', true));
	$suggestions = get_post_meta($post_id, 'pn_personal_finance_manager_suggestions', true);
	$steps_count = (!empty($steps) && !empty($steps[0]) && is_array($steps) && count($steps) > 0) ? count($steps) : 0;
	$ingredients_count = (!empty($ingredients) && !empty($ingredients[0]) && is_array($ingredients) && count($ingredients) > 0) ? count($ingredients) : 0;

	function pn_personal_finance_manager_minutes($time){
		if ($time) {
			$time = explode(':', $time);
			return ($time[0] * 60) + ($time[1]);
		} else {
			return 0;
		}
	}
?>
	<body <?php body_class(); ?>>
		<div id="pn-personal-finance-manager-asset-wrapper" class="pn-personal-finance-manager-wrapper pn-personal-finance-manager-asset-wrapper" data-pn-personal-finance-manager-ingredients-count="<?php echo intval($ingredients_count); ?>" data-pn-personal-finance-manager-steps-count="<?php echo intval($steps_count); ?>">
		  <div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
		  	<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-50-percent pn-personal-finance-manager-tablet-display-block pn-personal-finance-manager-tablet-width-100-percent">
		  		<a href="<?php echo esc_url(get_post_type_archive_link('pnpfm_asset')); ?>"><i class="material-icons-outlined pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-mr-10 pn-personal-finance-manager-color-main-0">keyboard_arrow_left</i> <?php esc_html_e('More assets', 'pn-personal-finance-manager'); ?></a>
		  	</div>
		  	<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-50-percent pn-personal-finance-manager-tablet-display-block pn-personal-finance-manager-tablet-width-100-percent pn-personal-finance-manager-text-align-right">
		  		<?php if (current_user_can('administrator') || current_user_can('pn_personal_finance_manager_role_manager')): ?>
		  			<a href="<?php echo esc_url(admin_url('post.php?post=' . $post_id . '&action=edit')); ?>"><i class="material-icons-outlined pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-mr-10 pn-personal-finance-manager-color-main-0">edit</i> <?php esc_html_e('Edit asset', 'pn-personal-finance-manager'); ?></a>
		  		<?php endif ?>
		  	</div>
		  </div>
			
			<h1 class="pn-personal-finance-manager-text-align-center pn-personal-finance-manager-mb-50"><?php echo esc_html(get_the_title($post_id)); ?></h1>

			<div class="pn-personal-finance-manager-display-block pn-personal-finance-manager-width-100-percent pn-personal-finance-manager-mb-30">
				<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-50-percent pn-personal-finance-manager-tablet-display-block pn-personal-finance-manager-tablet-width-100-percent pn-personal-finance-manager-mb-30 pn-personal-finance-manager-vertical-align-top">
					<div class="pn-personal-finance-manager-image pn-personal-finance-manager-p-20 pn-personal-finance-manager-mb-30">
						<?php if (has_post_thumbnail($post_id)): ?>
					    <?php echo get_the_post_thumbnail($post_id, 'full', ['class' => 'pn-personal-finance-manager-border-radius-20']); ?>
					  <?php else: ?>
							<img src="<?php echo esc_url(PN_PERSONAL_FINANCE_MANAGER_URL . 'assets/media/pn-personal-finance-manager-image.jpg'); ?>" class="pn-personal-finance-manager-border-radius-20 pn-personal-finance-manager-width-100-percent">
					  <?php endif ?>
					</div>

					<?php if (!empty($pn_personal_finance_manager_images)): ?>
						<div class="pn-personal-finance-manager-carousel pn-personal-finance-manager-carousel-main-images">
			          <?php if (has_post_thumbnail($post_id)): ?>
			          	<div class="pn-personal-finance-manager-image">
		                <?php echo get_the_post_thumbnail($post_id, 'thumbnail', ['class' => 'pn-personal-finance-manager-border-radius-10']); ?>
		              </div>
							  <?php endif ?>
			            <?php foreach ($pn_personal_finance_manager_images as $image_id): ?>
		              	<?php if (!empty($image_id)): ?>
			              	<div class="pn-personal-finance-manager-image">
			                	<?php echo wp_get_attachment_image($image_id, 'thumbnail', false, ['class' => 'pn-personal-finance-manager-border-radius-10']); ?>
			              	</div>
		              	<?php endif ?>
			            <?php endforeach ?>
			      </div>
					<?php endif ?>
				</div>

				<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-50-percent pn-personal-finance-manager-tablet-display-block pn-personal-finance-manager-tablet-width-100-percent pn-personal-finance-manager-mb-30 pn-personal-finance-manager-vertical-align-top pn-personal-finance-manager-mb-30">
					<div class="pn-personal-finance-manager-asset-content pn-personal-finance-manager-p-20">
						<?php echo wp_kses_post(str_replace(']]>', ']]&gt;', apply_filters('the_content', get_post($post_id)->post_content))); ?>
					</div>
				</div>
			</div>

			<div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent pn-personal-finance-manager-mb-50">
				<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-50-percent pn-personal-finance-manager-tablet-display-block pn-personal-finance-manager-tablet-width-100-percent pn-personal-finance-manager-mb-30 pn-personal-finance-manager-vertical-align-top">
					<div class="pn-personal-finance-manager-ingredients pn-personal-finance-manager-p-20">
						<?php if ($ingredients_count): ?>
							<h2 class="pn-personal-finance-manager-mb-30"><?php esc_html_e('Ingredients', 'pn-personal-finance-manager'); ?></h2>
							<ul>
								<?php foreach ($ingredients as $ingredient): ?>
									<li class="pn-personal-finance-manager-mb-20 pn-personal-finance-manager-font-size-20 pn-personal-finance-manager-list-style-none">
										<?php echo esc_html($ingredient); ?>
									</li>
								<?php endforeach ?>
							</ul>
						<?php endif ?>
					</div>
				</div>

				<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-50-percent pn-personal-finance-manager-tablet-display-block pn-personal-finance-manager-tablet-width-100-percent pn-personal-finance-manager-mb-30 pn-personal-finance-manager-vertical-align-top">
					<div class="pn-personal-finance-manager-steps pn-personal-finance-manager-p-20 pn-personal-finance-manager-mb-50">
						<?php if ($steps_count): ?>
							<div class="pn-personal-finance-manager-mb-30">
								<div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
									<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-80-percent">
										<h2><?php esc_html_e('Elaboration steps', 'pn-personal-finance-manager'); ?></h2>
									</div>
									<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-20-percent">
										<a href="#" class="pn-personal-finance-manager-popup-player-btn" data-src="#pn-personal-finance-manager-popup-player"><i class="material-icons-outlined pn-personal-finance-manager-mr-10 pn-personal-finance-manager-font-size-50 pn-personal-finance-manager-float-right pn-personal-finance-manager-vertical-align-middle pn-personal-finance-manager-tooltip" title="<?php esc_html_e('Play asset', 'pn-personal-finance-manager'); ?>">play_circle_outline</i></a>
									</div>
								</div>
										
								<?php if (!empty($steps_total_time)): ?>
									<div class="pn-personal-finance-manager-text-align-right">
										<i class="material-icons-outlined pn-personal-finance-manager-mr-10 pn-personal-finance-manager-font-size-10 pn-personal-finance-manager-vertical-align-middle">timer</i> <small><strong><?php esc_html_e('Total time', 'pn-personal-finance-manager'); ?></strong> <?php echo esc_html($steps_total_time); ?> (<?php esc_html_e('hours', 'pn-personal-finance-manager'); ?>:<?php esc_html_e('minutes', 'pn-personal-finance-manager'); ?>)</small>
									</div>
								<?php endif ?>
							</div>

							<ol>
								<?php foreach ($steps as $index => $step): ?>
									<li class="pn-personal-finance-manager-mb-50">
										<div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
											<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-80-percent">
												<?php if (!empty($step)): ?>
													<h4 class="pn-personal-finance-manager-mb-10"><?php echo esc_html($step); ?></h4>
												<?php endif ?>
											</div>

											<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-20-percent">
												<h5 class="pn-personal-finance-manager-mb-10"><i class="material-icons-outlined pn-personal-finance-manager-mr-10 pn-personal-finance-manager-font-size-10 pn-personal-finance-manager-vertical-align-middle">timer</i><?php echo !empty($steps_time[$index]) ? esc_html($steps_time[$index]) : '00:00'; ?></h5>
											</div>
										</div>

										<?php if (!empty($steps_description[$index])): ?>
											<p><?php echo esc_html($steps_description[$index]); ?></p>
										<?php endif ?>
									</li>
								<?php endforeach ?>
							</ol>

							<div id="pn-personal-finance-manager-popup-player" class="pn-personal-finance-manager-display-none-soft">
								<div id="pn-personal-finance-manager-popup-steps" class="pn-personal-finance-manager-mb-30" data-pn-personal-finance-manager-current-step="1">
									<?php foreach ($steps as $index => $step): ?>
										<div class="pn-personal-finance-manager-player-step <?php echo $index != 0 ? 'pn-personal-finance-manager-display-none-soft' : ''; ?>" data-pn-personal-finance-manager-step="<?php echo number_format($index + 1); ?>">
											<div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
												<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-80-percent pn-personal-finance-manager-vertical-align-top">
													<?php if (!empty($step)): ?>
														<h3 class="pn-personal-finance-manager-mb-10"><?php echo esc_html($step); ?></h3>
													<?php endif ?>
												</div>
												<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-20-percent pn-personal-finance-manager-vertical-align-top  pn-personal-finance-manager-text-align-right">
													<h3>
														<i class="material-icons-outlined pn-personal-finance-manager-display-inline pn-personal-finance-manager-player-timer-icon pn-personal-finance-manager-mr-10 pn-personal-finance-manager-font-size-30 pn-personal-finance-manager-vertical-align-middle">timer</i> 
														<span class="pn-personal-finance-manager-player-timer pn-personal-finance-manager-display-inline"><?php echo number_format(pn_personal_finance_manager_minutes($steps_time[$index])); ?></span>'
													</h3>
												</div>
											</div>

											<?php if (!empty($steps_description[$index])): ?>
												<div class="pn-personal-finance-manager-step-description"><?php echo esc_html($steps_description[$index]); ?></div>
											<?php endif ?>
										</div>
									<?php endforeach ?>
								</div>

								<div class="pn-personal-finance-manager-display-table pn-personal-finance-manager-width-100-percent">
									<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-50-percent pn-personal-finance-manager-text-align-center pn-personal-finance-manager-mb-20">
										<a href="#" class="pn-personal-finance-manager-steps-prev pn-personal-finance-manager-display-none"><?php esc_html_e('Previous', 'pn-personal-finance-manager'); ?></a>
									</div>
									<div class="pn-personal-finance-manager-display-inline-table pn-personal-finance-manager-width-50-percent pn-personal-finance-manager-text-align-center pn-personal-finance-manager-mb-20">
										<a href="#" class="pn-personal-finance-manager-btn pn-personal-finance-manager-btn-mini pn-personal-finance-manager-steps-next"><?php esc_html_e('Next', 'pn-personal-finance-manager'); ?></a>
									</div>
								</div>
							</div>
						<?php endif ?>
					</div>

					<?php if (!empty($suggestions)): ?>
						<div class="pn-personal-finance-manager-suggestions pn-personal-finance-manager-mb-50">
							<div class="pn-personal-finance-manager-text-align-center pn-personal-finance-manager-mb-10"><i class="material-icons-outlined pn-personal-finance-manager-font-size-50 pn-personal-finance-manager-tooltip" title="<?php esc_html_e('Suggestions', 'pn-personal-finance-manager'); ?>">lightbulb</i></div>

							<?php echo wp_kses_post(wp_specialchars_decode($suggestions)); ?>
						</div>
					<?php endif ?>
				</div>
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