<?php

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id = get_the_ID();

// Check if user is logged in
if (!is_user_logged_in()) {
	wp_die(esc_html__('You must be logged in to view liabilities.', 'pn-finance-manager'), esc_html__('Access Denied', 'pn-finance-manager'), ['response' => 403]);
}

// Check if user can view this liability
if (!PN_FINANCE_MANAGER_Functions_User::pn_finance_manager_user_can_view_post($post_id, 'pnfm_liability')) {
	wp_die(esc_html__('You do not have permission to view this liability.', 'pn-finance-manager'), esc_html__('Access Denied', 'pn-finance-manager'), ['response' => 403]);
}

get_header();
?>
<div class="pn-finance-manager-liability-single">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <h1><?php the_title(); ?></h1>
    <div class="pn-finance-manager-liability-content">
      <?php the_content(); ?>
    </div>
  <?php endwhile; endif; ?>
</div>
<?php
get_footer(); 