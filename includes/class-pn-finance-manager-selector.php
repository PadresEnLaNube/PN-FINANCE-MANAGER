<?php
/**
 * PN_FINANCE_MANAGER Custom Selector.
 *
 * A custom select plugin with multiple selection and search capabilities.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_FINANCE_MANAGER
 * @subpackage PN_FINANCE_MANAGER/includes
 * @author     Padres en la Nube
 */

if (!defined('ABSPATH')) {
    exit;
}

class PN_FINANCE_MANAGER_Selector {
    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'pn-finance-manager-selector',
            plugin_dir_url(__FILE__) . 'assets/css/pn-finance-manager-selector.css',
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'pn-finance-manager-selector',
            plugin_dir_url(__FILE__) . 'assets/js/pn-finance-manager-selector.js',
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('pn-finance-manager-selector', 'PN_FINANCE_MANAGER_Selector', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pn-finance-manager-selector-nonce')
        ));
    }
}