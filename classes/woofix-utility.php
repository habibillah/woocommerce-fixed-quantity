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
            $returnValue = false;
            $fixedQuantityData = get_post_meta($postId, '_woofix', true);
            if (!empty($fixedQuantityData)) {
                $fixedQuantityData = json_decode(html_entity_decode($fixedQuantityData), true);
                if (count($fixedQuantityData['woofix']) > 0) {
                    $returnValue = $fixedQuantityData;
                }
            }

            return $returnValue;
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
    }
}