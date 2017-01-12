<?php
/**
 * Plugin Name: WooCommerce Fixed Quantity
 * Plugin URI: http://habibillah.github.io/woocommerce-fixed-quantity/
 * Description: Customize price based on fixed quantity.
 * Author: Habibillah
 * Author URI: http://habibillah.kalicode.com/
 * Version: 1.1.3
 * Stable tag: 1.1.3
 * Tested up to: 4.6
 * Requires at least: 3.0.1
 * Text Domain: woofix
 * Domain Path: /languages/
 */

if (!defined('ABSPATH'))
    exit;
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
    return;

if (!class_exists('WooFixedQuantity')) {

    define("WOOFIXCONF_QTY_DESC", "{qty} items @{price} {total}");
    define("WOOFIXCONF_SHOW_DISC", "yes");
    define("WOOFIXCONF_SHOW_STOCK", "yes");
    define("WOOFIXCONF_CHECK_STOCK", "no");
    define("WOOFIXCONF_DEFAULT_ROLE", "customer");

    define("WOOFIXOPT_QTY_DESC", "woofix_qty_desc");
    define("WOOFIXOPT_SHOW_DISC", "woofix_show_disc");
    define("WOOFIXOPT_SHOW_STOCK", "woofix_show_stock");
    define("WOOFIXOPT_CHECK_STOCK", "woofix_check_stock");
    define("WOOFIXOPT_DEFAULT_ROLE", "woofix_default_role");
    define("WOOFIXOPT_AVAILABLE_ROLES", "woofix_available_roles");

    if (!defined('WOOFIX_PLUGIN_VIEW_DIR'))
        define( 'WOOFIX_PLUGIN_VIEW_DIR', dirname(__FILE__) . '/views' );

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

        public function load_admin_scripts($hook)
        {
            global $post;
            //woocommerce post types
		    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {

		    	//product
		        if ( 'product' === $post->post_type ) {
                    $params = array(
             'decimal_point' => wc_get_price_decimal_separator(),
             'num_decimals' => wc_get_price_decimals()
         );

                    if (!wp_script_is('underscore', 'registered')) {
                        wp_register_script('underscore', plugins_url('/assets/js/lodash.min.js', __FILE__), array(), '1.8.3');
                    }

                    wp_register_script('woofix_serializer',
                        plugins_url('/assets/js/woofix-serializer.js', __FILE__),
                        array('jquery', 'underscore'),
                        '1.1.1');
                    wp_register_script('woofix_admin_js',
                        plugins_url('/assets/js/admin-woofix.js', __FILE__),
                        array('jquery', 'underscore', 'woofix_serializer'),
                        '1.1.1');

                    wp_localize_script('woofix_admin_js', 'woofix_admin', $params);

                    wp_enqueue_script('underscore');
                    wp_enqueue_script('woofix_serializer');
                    wp_enqueue_script('woofix_admin_js');

                    wp_enqueue_style('woofix_admin_css', plugins_url('/assets/css/admin-woofix.css', __FILE__));

		        }
		    }

        }

        public function load_public_scripts()
        {
            if(is_cart())
            {
                wp_enqueue_script('woofix_public_js', plugins_url('/assets/js/woofix.js', __FILE__), array('jquery'));
            }
            if(is_product())
            {
                wp_enqueue_script('woofix_public_variable_js', plugins_url('/assets/js/woofix-client-variations.js', __FILE__), array('jquery'));
                wp_localize_script( 'woofix_public_variable_js', 'woofix', array(
                 // URL to wp-admin/admin-ajax.php to process the request
                 'ajaxurl' => admin_url( 'admin-ajax.php' )
               ));
            }


        }
    }
}

new WooFixedQuantity();
