<?php
if (!defined('ABSPATH'))
    exit;

define("WOOFIX_DISCOUNT_RAW", "woofix_price_raw_discount");
define("WOOFIX_DISCOUNT_FORMATTED", "woofix_price_formatted_discount");
define("WOOFIX_PRICE_RAW_BEFORE_DISCOUNT", "woofix_price_raw_before_discount");
define("WOOFIX_PRICE_RAW_AFTER_DISCOUNT", "woofix_price_raw_after_discount");
define("WOOFIX_PRICE_FORMATTED_BEFORE_DISCOUNT", "woofix_price_formatted_before_discount");
define("WOOFIX_PRICE_FORMATTED_AFTER_DISCOUNT", "woofix_price_formatted_after_discount");

if (!class_exists('WooAdminFixedQuantity')) {
    class WoofixUtility
    {
        /**
         * @param $postId
         * @return array|bool|mixed
         */
        public static function isFixedQtyPrice($postId)
        {
            $current_user = wp_get_current_user();
            $current_user_roles = $current_user->roles;
            $default_role = get_option(WOOFIXOPT_DEFAULT_ROLE, WOOFIXCONF_DEFAULT_ROLE);


            $returnValue = self::constructQtyData($postId, $current_user_roles);

            if (empty($returnValue)) {
                if (!in_array($default_role, $current_user_roles)) {
                    array_unshift($current_user_roles, $default_role);
                }
                $returnValue = self::constructQtyData($postId, $current_user_roles);
            }

            if (empty($returnValue))
                return false;

            $woofixData = array_unique($returnValue, SORT_REGULAR);
            usort($woofixData, function ($a, $b) {
                if ($a['woofix_qty'] == $b['woofix_qty'])
                    return 0;

                return ($a['woofix_qty'] < $b['woofix_qty']) ? -1 : 1;
            });
            $woofixData = apply_filters('woofix_sort_qty_data_to_show', $woofixData);
            return array('woofix' => $woofixData);
        }

        /**
         * @param WC_Product | array $product
         * @return int
         */
        public static function getActualId($product)
        {
            if (is_object($product)) {
                if (method_exists($product,'get_id'))
                    return $product->get_id();

                /**
                 * @deprecated keep it for backward compatible
                 */
                return $product->id;
            }
            if (!empty($product['variation_id'])) {
                return $product['variation_id'];
            }
            if (!empty($product['product_id'])) {
                return $product['product_id'];
            }

            return null;
        }

        /**
         * @param $postId
         * @param $current_user_roles
         * @return array
         */
        private static function constructQtyData($postId, $current_user_roles)
        {
            $returnValue = array();

            $fixedQuantityText = get_post_meta($postId, '_woofix', true);
            $fixedQuantityData = json_decode(html_entity_decode($fixedQuantityText), true);
            if (!empty($fixedQuantityData['woofix'])) {

                foreach ($fixedQuantityData['woofix'] as $key => $value) {

                    if (is_numeric($key)) {
                        $returnValue[] = $value;
                        continue;
                    }

                    if (!in_array($key, $current_user_roles))
                        continue;

                    foreach ($value as $qty_data) {
                        if (empty($qty_data['woofix_qty']))
                            continue;

                        if (empty($qty_data['woofix_disc']) && empty($qty_data['woofix_price']))
                            continue;

                        // used for WPML multi currency
                        $qty_data['woofix_price'] = apply_filters('wcml_raw_price_amount', $qty_data['woofix_price']);
                        $returnValue[] = $qty_data;
                    }
                }
            }
            return $returnValue;
        }

        /**
         * @param WC_Product $product
         * @param int $qty
         * @return array
         */
        public static function calculatePrice($product, $qty)
        {
            $productId = WoofixUtility::getActualId($product);
            $fixedPriceData = WoofixUtility::isFixedQtyPrice($productId);
            if ($fixedPriceData !== false) {
                $discount = 0;
                foreach ($fixedPriceData['woofix'] as $disc) {
                    if ($disc['woofix_qty'] == $qty) {
                        $discount = $disc['woofix_disc'];
                    }
                }

                $rawPriceAfterDisc = $product->get_price();
                $regularPrice = $product->get_regular_price('');

                $formattedPriceAfterDisc = wc_price($rawPriceAfterDisc);
                $rawPriceBeforeDisc = ($discount < 100)? ($rawPriceAfterDisc * 100) / (100 - $discount) : $regularPrice;
                $formattedPriceBeforeDisc = wc_price($rawPriceBeforeDisc);

                return array(
                    WOOFIX_DISCOUNT_RAW => $discount,
                    WOOFIX_DISCOUNT_FORMATTED => "$discount%",
                    WOOFIX_PRICE_RAW_BEFORE_DISCOUNT => $rawPriceBeforeDisc,
                    WOOFIX_PRICE_RAW_AFTER_DISCOUNT => $rawPriceAfterDisc,
                    WOOFIX_PRICE_FORMATTED_BEFORE_DISCOUNT => $formattedPriceBeforeDisc,
                    WOOFIX_PRICE_FORMATTED_AFTER_DISCOUNT => $formattedPriceAfterDisc,
                );
            } else {
                return array();
            }

        }
    }
}