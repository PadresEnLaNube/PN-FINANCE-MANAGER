<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Check if user is logged in
if (!is_user_logged_in()) {
	wp_die(esc_html__('You must be logged in to view liabilities.', 'pn-personal-finance-manager'), esc_html__('Access Denied', 'pn-personal-finance-manager'), ['response' => 403]);
}

get_header();

// Get all liabilities and filter by user permissions
if (class_exists('Polylang')) {
	$liabilities = get_posts(['numberposts' => -1, 'fields' => 'ids', 'post_type' => 'pnpfm_liability', 'lang' => pll_current_language(), 'post_status' => ['publish'], 'order' => 'DESC', ]);
} else {
	$liabilities = get_posts(['numberposts' => -1, 'fields' => 'ids', 'post_type' => 'pnpfm_liability', 'post_status' => ['publish'], 'order' => 'DESC', ]);
}

// Filter liabilities based on user permissions
$liabilities = PN_PERSONAL_FINANCE_MANAGER_Functions_User::pn_personal_finance_manager_filter_user_posts($liabilities, 'pnpfm_liability');
?>
<div class="pn-personal-finance-manager-liability-archive">
  <h1><?php esc_html_e('Liabilities', 'pn-personal-finance-manager'); ?></h1>
  <div class="pn-personal-finance-manager-liability-list">
    <?php if (!empty($liabilities)) : ?>
      <?php foreach ($liabilities as $liability_id) : ?>
        <div class="pn-personal-finance-manager-liability-archive-item">
          <h2><a href="<?php echo esc_url(get_permalink($liability_id)); ?>"><?php echo esc_html(get_the_title($liability_id)); ?></a></h2>
          <div><?php echo esc_html(get_the_excerpt($liability_id)); ?></div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p><?php esc_html_e('No liabilities found.', 'pn-personal-finance-manager'); ?></p>
    <?php endif; ?>
  </div>
</div>
<?php
get_footer(); 