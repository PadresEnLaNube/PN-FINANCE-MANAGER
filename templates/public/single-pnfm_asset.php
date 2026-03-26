<?php	
/**
 * Provide a common footer area view for the plugin
 *
 * This file is used to markup the common footer facing aspects of the plugin.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 *
 * @package    PN_FINANCE_MANAGER
 * @subpackage PN_FINANCE_MANAGER/common/templates
 */

	if (!defined('ABSPATH')) exit; // Exit if accessed directly

	$post_id = get_the_ID();
	
	// Check if user is logged in
	if (!is_user_logged_in()) {
		wp_die(esc_html__('You must be logged in to view assets.', 'pn-finance-manager'), esc_html__('Access Denied', 'pn-finance-manager'), ['response' => 403]);
	}

	// Check if user can view this asset
	if (!PN_FINANCE_MANAGER_Functions_User::pn_finance_manager_user_can_view_post($post_id, 'pnfm_asset')) {
		wp_die(esc_html__('You do not have permission to view this asset.', 'pn-finance-manager'), esc_html__('Access Denied', 'pn-finance-manager'), ['response' => 403]);
	}

	if(wp_is_block_theme()) {
  	wp_head();
		block_template_part('header');
	} else {
  		get_header();
	}

	$ingredients = get_post_meta($post_id, 'pn_finance_manager_ingredients_name', true);
	$steps = get_post_meta($post_id, 'pn_finance_manager_steps_name', true);
	$steps_description = get_post_meta($post_id, 'pn_finance_manager_steps_description', true);
	$steps_time = get_post_meta($post_id, 'pn_finance_manager_steps_time', true);
	$steps_total_time = get_post_meta($post_id, 'pn_finance_manager_time', true);
	$pn_finance_manager_images = explode(',', get_post_meta($post_id, 'pn_finance_manager_images', true));
	$suggestions = get_post_meta($post_id, 'pn_finance_manager_suggestions', true);
	$steps_count = (!empty($steps) && !empty($steps[0]) && is_array($steps) && count($steps) > 0) ? count($steps) : 0;
	$ingredients_count = (!empty($ingredients) && !empty($ingredients[0]) && is_array($ingredients) && count($ingredients) > 0) ? count($ingredients) : 0;

	function pn_finance_manager_minutes($time){
		if ($time) {
			$time = explode(':', $time);
			return ($time[0] * 60) + ($time[1]);
		} else {
			return 0;
		}
	}
?>
	<body <?php body_class(); ?>>
		<div id="pn-finance-manager-asset-wrapper" class="pn-finance-manager-wrapper pn-finance-manager-asset-wrapper" data-pn-finance-manager-ingredients-count="<?php echo intval($ingredients_count); ?>" data-pn-finance-manager-steps-count="<?php echo intval($steps_count); ?>">
		  <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
		  	<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent">
		  		<a href="<?php echo esc_url(get_post_type_archive_link('pnfm_asset')); ?>"><i class="material-icons-outlined pn-finance-manager-font-size-30 pn-finance-manager-vertical-align-middle pn-finance-manager-mr-10 pn-finance-manager-color-main-0">keyboard_arrow_left</i> <?php esc_html_e('More assets', 'pn-finance-manager'); ?></a>
		  	</div>
		  	<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-text-align-right">
		  		<?php if (current_user_can('administrator') || current_user_can('pn_finance_manager_role_manager')): ?>
		  			<a href="<?php echo esc_url(admin_url('post.php?post=' . $post_id . '&action=edit')); ?>"><i class="material-icons-outlined pn-finance-manager-font-size-30 pn-finance-manager-vertical-align-middle pn-finance-manager-mr-10 pn-finance-manager-color-main-0">edit</i> <?php esc_html_e('Edit asset', 'pn-finance-manager'); ?></a>
		  		<?php endif ?>
		  	</div>
		  </div>
			
			<h1 class="pn-finance-manager-text-align-center pn-finance-manager-mb-50"><?php echo esc_html(get_the_title($post_id)); ?></h1>

			<div class="pn-finance-manager-display-block pn-finance-manager-width-100-percent pn-finance-manager-mb-30">
				<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-mb-30 pn-finance-manager-vertical-align-top">
					<div class="pn-finance-manager-image pn-finance-manager-p-20 pn-finance-manager-mb-30">
						<?php if (has_post_thumbnail($post_id)): ?>
					    <?php echo get_the_post_thumbnail($post_id, 'full', ['class' => 'pn-finance-manager-border-radius-20']); ?>
					  <?php else: ?>
							<img src="<?php echo esc_url(PN_FINANCE_MANAGER_URL . 'assets/media/pn-finance-manager-image.jpg'); ?>" class="pn-finance-manager-border-radius-20 pn-finance-manager-width-100-percent">
					  <?php endif ?>
					</div>

					<?php if (!empty($pn_finance_manager_images)): ?>
						<div class="pn-finance-manager-carousel pn-finance-manager-carousel-main-images">
			        <div class="owl-carousel owl-theme">
			          <?php if (!empty($pn_finance_manager_images)): ?>
			          	<?php if (has_post_thumbnail($post_id)): ?>
				          	<div class="pn-finance-manager-image pn-finance-manager-cursor-grab">
			                <a href="#" data-fancybox="gallery" data-src="<?php echo esc_url(get_the_post_thumbnail_url($post_id, 'full', ['class' => 'pn-finance-manager-border-radius-10'])); ?>"><?php echo esc_html(get_the_post_thumbnail($post_id, 'thumbnail', ['class' => 'pn-finance-manager-border-radius-10'])); ?></a>  
			              </div>
								  <?php endif ?>

			            <?php foreach ($pn_finance_manager_images as $image_id): ?>
		              	<?php if (!empty($image_id)): ?>
			              	<div class="pn-finance-manager-image pn-finance-manager-cursor-grab">
			                	<a href="#" data-fancybox="gallery" data-src="<?php echo esc_url(wp_get_attachment_image_src($image_id, 'full')[0]); ?>"><?php echo esc_html(wp_get_attachment_image($image_id, 'thumbnail', false, ['class' => 'pn-finance-manager-border-radius-10'])); ?></a>  
			              	</div>
		              	<?php endif ?>
			            <?php endforeach ?>
			          <?php endif ?>
			        </div>
			      </div>
					<?php endif ?>
				</div>

				<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-mb-30 pn-finance-manager-vertical-align-top pn-finance-manager-mb-30">
					<div class="pn-finance-manager-asset-content pn-finance-manager-p-20">
						<?php echo wp_kses_post(str_replace(']]>', ']]&gt;', apply_filters('the_content', get_post($post_id)->post_content))); ?>
					</div>
				</div>
			</div>

			<div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent pn-finance-manager-mb-50">
				<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-mb-30 pn-finance-manager-vertical-align-top">
					<div class="pn-finance-manager-ingredients pn-finance-manager-p-20">
						<?php if ($ingredients_count): ?>
							<h2 class="pn-finance-manager-mb-30"><?php esc_html_e('Ingredients', 'pn-finance-manager'); ?></h2>
							<ul>
								<?php foreach ($ingredients as $ingredient): ?>
									<li class="pn-finance-manager-mb-20 pn-finance-manager-font-size-20 pn-finance-manager-list-style-none">
										<?php echo esc_html($ingredient); ?>
									</li>
								<?php endforeach ?>
							</ul>
						<?php endif ?>
					</div>
				</div>

				<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-mb-30 pn-finance-manager-vertical-align-top">
					<div class="pn-finance-manager-steps pn-finance-manager-p-20 pn-finance-manager-mb-50">
						<?php if ($steps_count): ?>
							<div class="pn-finance-manager-mb-30">
								<div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
									<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-80-percent">
										<h2><?php esc_html_e('Elaboration steps', 'pn-finance-manager'); ?></h2>
									</div>
									<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-20-percent">
										<a href="#" class="pn-finance-manager-popup-player-btn" data-fancybox data-src="#pn-finance-manager-popup-player"><i class="material-icons-outlined pn-finance-manager-mr-10 pn-finance-manager-font-size-50 pn-finance-manager-float-right pn-finance-manager-vertical-align-middle pn-finance-manager-tooltip" title="<?php esc_html_e('Play asset', 'pn-finance-manager'); ?>">play_circle_outline</i></a>
									</div>
								</div>
										
								<?php if (!empty($steps_total_time)): ?>
									<div class="pn-finance-manager-text-align-right">
										<i class="material-icons-outlined pn-finance-manager-mr-10 pn-finance-manager-font-size-10 pn-finance-manager-vertical-align-middle">timer</i> <small><strong><?php esc_html_e('Total time', 'pn-finance-manager'); ?></strong> <?php echo esc_html($steps_total_time); ?> (<?php esc_html_e('hours', 'pn-finance-manager'); ?>:<?php esc_html_e('minutes', 'pn-finance-manager'); ?>)</small>
									</div>
								<?php endif ?>
							</div>

							<ol>
								<?php foreach ($steps as $index => $step): ?>
									<li class="pn-finance-manager-mb-50">
										<div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
											<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-80-percent">
												<?php if (!empty($step)): ?>
													<h4 class="pn-finance-manager-mb-10"><?php echo esc_html($step); ?></h4>
												<?php endif ?>
											</div>

											<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-20-percent">
												<h5 class="pn-finance-manager-mb-10"><i class="material-icons-outlined pn-finance-manager-mr-10 pn-finance-manager-font-size-10 pn-finance-manager-vertical-align-middle">timer</i><?php echo !empty($steps_time[$index]) ? esc_html($steps_time[$index]) : '00:00'; ?></h5>
											</div>
										</div>

										<?php if (!empty($steps_description[$index])): ?>
											<p><?php echo esc_html($steps_description[$index]); ?></p>
										<?php endif ?>
									</li>
								<?php endforeach ?>
							</ol>

							<div id="pn-finance-manager-popup-player" class="pn-finance-manager-display-none-soft">
								<div id="pn-finance-manager-popup-steps" class="pn-finance-manager-mb-30" data-pn-finance-manager-current-step="1">
									<?php foreach ($steps as $index => $step): ?>
										<div class="pn-finance-manager-player-step <?php echo $index != 0 ? 'pn-finance-manager-display-none-soft' : ''; ?>" data-pn-finance-manager-step="<?php echo number_format($index + 1); ?>">
											<div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
												<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-80-percent pn-finance-manager-vertical-align-top">
													<?php if (!empty($step)): ?>
														<h3 class="pn-finance-manager-mb-10"><?php echo esc_html($step); ?></h3>
													<?php endif ?>
												</div>
												<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-20-percent pn-finance-manager-vertical-align-top  pn-finance-manager-text-align-right">
													<h3>
														<i class="material-icons-outlined pn-finance-manager-display-inline pn-finance-manager-player-timer-icon pn-finance-manager-mr-10 pn-finance-manager-font-size-30 pn-finance-manager-vertical-align-middle">timer</i> 
														<span class="pn-finance-manager-player-timer pn-finance-manager-display-inline"><?php echo number_format(pn_finance_manager_minutes($steps_time[$index])); ?></span>'
													</h3>
												</div>
											</div>

											<?php if (!empty($steps_description[$index])): ?>
												<div class="pn-finance-manager-step-description"><?php echo esc_html($steps_description[$index]); ?></div>
											<?php endif ?>
										</div>
									<?php endforeach ?>
								</div>

								<div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
									<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-text-align-center pn-finance-manager-mb-20">
										<a href="#" class="pn-finance-manager-steps-prev pn-finance-manager-display-none"><?php esc_html_e('Previous', 'pn-finance-manager'); ?></a>
									</div>
									<div class="pn-finance-manager-display-inline-table pn-finance-manager-width-50-percent pn-finance-manager-text-align-center pn-finance-manager-mb-20">
										<a href="#" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-steps-next"><?php esc_html_e('Next', 'pn-finance-manager'); ?></a>
									</div>
								</div>
							</div>
						<?php endif ?>
					</div>

					<?php if (!empty($suggestions)): ?>
						<div class="pn-finance-manager-suggestions pn-finance-manager-mb-50">
							<div class="pn-finance-manager-text-align-center pn-finance-manager-mb-10"><i class="material-icons-outlined pn-finance-manager-font-size-50 pn-finance-manager-tooltip" title="<?php esc_html_e('Suggestions', 'pn-finance-manager'); ?>">lightbulb</i></div>

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