<?php
if (!defined('ABSPATH'))
    exit;

if (!class_exists('WooClientFixedQuantity')) {
    class WooClientFixedQuantity
    {
        private $file;

        /**
         * @param $file
         */
        public function __construct($file)
        {
            $this->file = $file;

            add_filter('woocommerce_product_add_to_cart_url', array(&$this, 'add_to_cart_url'));
            add_filter('woocommerce_product_add_to_cart_text', array(&$this, 'add_to_cart_text'));
            add_filter('woocommerce_loop_add_to_cart_link', array(&$this, 'loop_add_to_cart_link'));

            add_filter('woocommerce_locate_template', array(&$this, 'locate_template'), 20, 3);
            add_filter('woocommerce_cart_item_subtotal', array(&$this, 'filter_subtotal_price'), 20, 2);
            add_filter('woocommerce_checkout_item_subtotal', array(&$this, 'filter_subtotal_price'), 20, 2);
            add_filter('woocommerce_order_formatted_line_subtotal', array(&$this, 'order_formatted_line_subtotal'), 10, 2);
            add_filter('woocommerce_add_to_cart_validation', array(&$this, 'validate_quantity'), 10, 3);
            add_filter('woocommerce_update_cart_validation', array(&$this, 'validate_quantity_update'), 10, 4);
            add_filter('woocommerce_cart_item_quantity', array(&$this, 'filter_woocommerce_cart_item_quantity'), 10, 2);
            add_filter('woocommerce_get_availability', array(&$this, 'get_availability'), 1, 2);

            add_action('woocommerce_before_calculate_totals', array(&$this, 'action_before_calculate_totals'), 10, 1);
            add_action('woocommerce_calculate_totals', array(&$this, 'action_before_calculate_totals'), 10, 1);
            add_action('woocommerce_after_calculate_totals', array(&$this, 'action_before_calculate_totals'), 10, 1);
            add_action('woocommerce_cart_loaded_from_session', array(&$this, 'action_before_calculate_totals'), 10, 1);

            if (version_compare(WOOCOMMERCE_VERSION, "2.1.0") >= 0) {
                add_filter('woocommerce_cart_item_price', array(&$this, 'filter_item_price'), 20, 3);
            } else {
                add_filter('woocommerce_cart_item_price_html', array(&$this, 'filter_item_price'), 20, 3);
            }
        }

        function get_availability($availability, $_product)
        {
            $id = WoofixUtility::getActualId($_product);
            if (WoofixUtility::isFixedQtyPrice($id)) {
                $show_stock = get_option(WOOFIXOPT_SHOW_STOCK);
                if ($show_stock == 'no') {
                    $availability['availability'] = '';
                }
            }

            return $availability;
        }

        public function filter_woocommerce_cart_item_quantity($input_html, $cart_item_key)
        {
            $cart = WC()->cart->get_cart();
            if (!empty($cart[$cart_item_key])) {
                $cart_item = $cart[$cart_item_key];
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $productId = WoofixUtility::getActualId($_product);
                $fixedPriceData = WoofixUtility::isFixedQtyPrice($productId);
                if ($fixedPriceData !== false) {

                    /** @noinspection PhpUnusedLocalVariableInspection */
                    $input_name = 'cart[' . $cart_item_key . '][qty]';

                    /** @noinspection PhpUnusedLocalVariableInspection */
                    $product = $_product;

                    /** @noinspection PhpUnusedLocalVariableInspection */
                    $selected_quantity = $cart_item['quantity'];

                    $template = $this->woofix_locate_template('global/quantity-input.php', false);
                    if ($template !== false) {
                        ob_start();
                        /** @noinspection PhpIncludeInspection */
                        include($template);
                        $input_html = ob_get_clean();
                    }
                }
            }

            return $input_html;
        }

        /**
         * @param $link
         * @return bool|string
         */
        public function add_to_cart_url($link)
        {
            global $product;
            $productId = WoofixUtility::getActualId($product);
            if (WoofixUtility::isFixedQtyPrice($productId) !== false) {
                return get_permalink($product->id);
            }
            return $link;
        }

        /**
         * @param $text
         * @return string|void
         */
        public function add_to_cart_text($text)
        {
            global $product;
            $productId = WoofixUtility::getActualId($product);
            if (WoofixUtility::isFixedQtyPrice($productId) !== false) {
                return apply_filters('woofix_product_add_to_cart_text', __('Select Options', 'woofix'));
            }
            return $text;
        }

        /**
         * @param $link
         * @return mixed
         */
        function loop_add_to_cart_link($link)
        {
            global $product;
            $productId = WoofixUtility::getActualId($product);
            if (WoofixUtility::isFixedQtyPrice($productId) !== false) {
                return str_replace('add_to_cart_button', '', $link);
            }
            return $link;
        }

        /**
         * Filter product price so that the discount is visible.
         *
         * @param $price
         * @param $cart_item
         * @param $cart_item_key
         * @return string
         */
        public function filter_item_price($price, $cart_item, $cart_item_key)
        {
            if (empty($cart_item['data'])) {
                return $price;
            }

            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $productId = WoofixUtility::getActualId($_product);
            $fixedPriceData = WoofixUtility::isFixedQtyPrice($productId);
            if ($fixedPriceData !== false) {
                $discount = 0;
                foreach ($fixedPriceData['woofix'] as $disc) {
                    if ($disc['woofix_qty'] == $cart_item['quantity']) {
                        $discount = $disc['woofix_disc'];
                    }
                }

                $itemPrice = $_product->get_price();
                $discprice = wc_price($itemPrice);
                $oldprice = ($itemPrice * 100) / (100 - $discount);
                $oldprice = wc_price($oldprice);
                if ($oldprice == $discprice) {
                    $price = "<span class='discount-info'><span class='new-price'>$discprice</span></span>";

                } else {
                    $price = "<span class='discount-info'>" .
                        "<span class='old-price'><del>$oldprice</del></span>&nbsp;" .
                        "<span class='new-price'><strong>$discprice</strong></span></span>";
                }
            }

            return $price;
        }

        /**
         * Hook to woocommerce_before_calculate_totals action.
         *
         * @param WC_Cart $cart
         */
        public function action_before_calculate_totals(WC_Cart $cart)
        {
            foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

                $productId = WoofixUtility::getActualId($_product);
                $fixedPriceData = WoofixUtility::isFixedQtyPrice($productId);
                if ($fixedPriceData !== false) {
                    foreach ($fixedPriceData['woofix'] as $data) {
                        if ($data['woofix_qty'] == $cart_item['quantity']) {
                            $cart_item['data']->set_price(floatval($data['woofix_price']));
                        }
                    }
                }
            }
        }

        public function validate_quantity($passed, $product_id, $quantity)
        {
            $fixedPriceData = WoofixUtility::isFixedQtyPrice($product_id);
            if ($fixedPriceData !== false) {
                $qtyInCart = 0;

                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $productId = WoofixUtility::getActualId($cart_item['data']);
                    if ($product_id == $productId) {
                        $qtyInCart = !empty($cart_item['quantity']) ? $cart_item['quantity'] : 0;
                    }
                }

                $newQty = $qtyInCart + $quantity;

                $passed = false;
                $quantityList = array();
                foreach ($fixedPriceData['woofix'] as $data) {
                    $quantityList[] = $data['woofix_qty'];
                    if ($data['woofix_qty'] == $newQty) {
                        $passed = true;
                    }
                }

                if (!$passed) {
                    $product = wc_get_product($product_id);
                    $product_title = $product->post->post_title;

                    $additionalMessage = (empty($qtyInCart) || $qtyInCart < 1) ? '' : sprintf(__('You have added %s qty in your cart.', 'woofix'), $qtyInCart);
                    $message = sprintf(__("Product %s can be ordered using this listed quantity : %s. %s", "woofix"), $product_title, implode(', ', $quantityList), $additionalMessage);

                    wc_add_notice(apply_filters('woofix_quantity_is_not_valid', $message), 'error');
                }
            }

            return $passed;
        }

        public function validate_quantity_update($passed, $cart_item_key, $cart_item, $quantity)
        {
            $product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $productId = WoofixUtility::getActualId($product);
            $fixedPriceData = WoofixUtility::isFixedQtyPrice($productId);
            if ($fixedPriceData !== false) {
                $passed = false;
                $quantityList = array();
                foreach ($fixedPriceData['woofix'] as $data) {
                    $quantityList[] = $data['woofix_qty'];
                    if ($data['woofix_qty'] == $quantity) {
                        $passed = true;
                    }
                }

                if (!$passed) {
                    $product_title = $product->post->post_title;

                    $message = sprintf(__("Product %s can be ordered using this listed quantity : %s.", "woofix"), $product_title, implode(', ', $quantityList));
                    wc_add_notice(apply_filters('woofix_quantity_is_not_valid', $message), 'error');
                }
            }

            return $passed;
        }

        /**
         * Filter product price so that the discount is visible.
         *
         * @param $price
         * @param $cart_item
         * @return string
         */
        public function filter_subtotal_price($price, $cart_item)
        {
            if (!empty($cart_item['data']) && get_option(WOOFIXOPT_SHOW_DISC) === WOOFIXCONF_SHOW_DISC) {

                $_product = $cart_item['data'];
                $productId = WoofixUtility::getActualId($_product);
                $fixedPriceData = WoofixUtility::isFixedQtyPrice($productId);
                if ($fixedPriceData !== false) {

                    /** @noinspection PhpUnusedLocalVariableInspection */
                    $discount = "0%";

                    foreach ( $fixedPriceData['woofix'] as $disc ) {
                        if ( $disc['woofix_qty'] == $cart_item['quantity'] ) {

                            /** @noinspection PhpUnusedLocalVariableInspection */
                            $discount = $disc['woofix_disc'] . "%";
                        }
                    }

                    $template = $this->woofix_locate_template('discount-info.php', false);
                    if ($template !== false) {
                        ob_start();
                        /** @noinspection PhpIncludeInspection */
                        include($template);
                        $price = ob_get_clean();
                    }
                }
            }

            return $price;
        }

        /**
         * Filter product price so that the discount is visible.
         *
         * @param $price
         * @param $product
         * @return string
         */
        public function order_formatted_line_subtotal($price, $product)
        {
            if (get_option(WOOFIXOPT_SHOW_DISC) === WOOFIXCONF_SHOW_DISC) {

                $productId = WoofixUtility::getActualId($product);
                $fixedPriceData = WoofixUtility::isFixedQtyPrice($productId);
                if ($fixedPriceData !== false) {

                    /** @noinspection PhpUnusedLocalVariableInspection */
                    $discount = "0%";

                    foreach ($fixedPriceData['woofix'] as $disc) {
                        if ($disc['woofix_qty'] == $product['qty']) {

                            /** @noinspection PhpUnusedLocalVariableInspection */
                            $discount = $disc['woofix_disc'] . "%";
                        }
                    }

                    $template = $this->woofix_locate_template('discount-info.php', false);
                    if ($template !== false) {
                        ob_start();
                        /** @noinspection PhpIncludeInspection */
                        include($template);
                        $price = ob_get_clean();
                    }
                }
            }

            return $price;
        }

        /**
         * @param $template
         * @param $template_name
         * @param $template_path
         * @return string
         */
        function locate_template($template, $template_name, $template_path)
        {
            $_template = $template;

            if (is_single() && get_post_type() == 'product') {

                $postId = get_the_ID();
                if (WoofixUtility::isFixedQtyPrice($postId) !== false) {
                    $template = $this->woofix_locate_template($template_name, true);
                }
            }

            if (!$template) {

                // Look within passed path within the theme - this is priority
                $template = locate_template(array(
                    trailingslashit($template_path) . $template_name,
                    $template_name
                ));
            }

            // Get default template
            if (!$template)
                $template = $_template;

            return $template;
        }

        function woofix_locate_template($template_name, $require_once)
        {
            $available_templates = array(
                'discount-info.php',
                'global/quantity-input.php'
            );
            
            if (!in_array($template_name, $available_templates))
                return false;
            
            // search template in theme
            $theme_plugin_template = 'woocommerce-fixed-quantity/' . $template_name;
            $template = locate_template($theme_plugin_template, $require_once);
            
            if (!$template) {
                // get default template
                $plugin_template = plugin_dir_path($this->file) . 'templates/' . $template_name;
                if (!file_exists($plugin_template)) {
                    _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $plugin_template ), '2.1' );
                    return false;
                }

                $template = $plugin_template;
            }
            
            return $template;
        }
    }
}
