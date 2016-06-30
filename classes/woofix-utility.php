<?php
if (!defined('ABSPATH'))
    exit;

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
         * @param $product
         * @return int
         */
        public static function getActualId($product)
        {
            if ($product instanceof WC_Product_Variation) {
                return $product->variation_id;
            }
            if (is_object($product)) {
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

                        $returnValue[] = $qty_data;
                    }
                }
            }
            return $returnValue;
        }
    }
}