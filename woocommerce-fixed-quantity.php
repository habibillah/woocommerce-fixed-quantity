<?php
/**
 * Plugin Name: WooCommerce Fixed Quantity
 * Plugin URI: http://habibillah.github.io/woocommerce-fixed-quantity/
 * Description: Customize price based on fixed quantity.
 * Version: 1.0.8
 * Author: Habibillah
 * Author URI: http://habibillah.kalicode.com/
 * Requires at least: 3.0.1
 * Tested up to: 4.3
 * Stable tag: 1.0.8
 * Text Domain: woofix
 * Domain Path: /languages/
 */

if (!defined('ABSPATH'))
    exit;
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
    return;

if (!class_exists('WooFixedQuantity')) {

    class WooFixedQuantity
    {
        public $woo_admin_fixed_price;
        public $woo_client_fixed_price;

        public function __construct()
        {
            add_action('init', array(&$this, 'load_init'));
            add_action('admin_enqueue_scripts', array(&$this, 'load_admin_scripts'));
            add_action('wp_enqueue_scripts', array(&$this, 'load_public_scripts'));
        }

        public function load_init()
        {
            load_plugin_textdomain('woofix', false, dirname(plugin_basename(__FILE__)) . '/languages/');

            require_once('classes/woofix-utility.php');
            require_once('classes/admin-fixed-quantity-price.php');
            require_once('classes/client-fixed-quantity-price.php');

            if (is_admin()) {
                $this->woo_admin_fixed_price = new WooAdminFixedQuantity(__FILE__);
            }

            $this->woo_client_fixed_price = new WooClientFixedQuantity(__FILE__);
        }

        public function load_admin_scripts()
        {
            wp_enqueue_script('woofix_admin_js', plugins_url('/assets/js/admin-woofix.js', __FILE__), array('jquery'));
            wp_enqueue_script('woofix_admin_serialize_json_js', plugins_url('/assets/js/jquery.serializejson.min.js', __FILE__), array('jquery'));

            wp_enqueue_style('woofix_admin_css', plugins_url('/assets/css/admin-woofix.css', __FILE__));
        }
        
        public function load_public_scripts()
        {
            wp_enqueue_script('woofix_admin_js', plugins_url('/assets/js/woofix.js', __FILE__), array('jquery'));
        }
    }
}

new WooFixedQuantity();
