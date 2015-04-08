<?php
/**
 * Product quantity inputs
 *
 * @author WooThemes
 * @package WooCommerce/Templates
 * @version 2.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

$data = WoofixUtility::isFixedQtyPrice($product->id);
?>
<div class="quantity_select">
    <select name="<?php echo esc_attr( $input_name ); ?>"
            title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ); ?>"
            class="qty">
        <?php foreach ($data['woofix'] as $item): ?>
            <option value="<?php echo $item['woofix_qty'] ?>">
                <?php
                $price = wc_price($item['woofix_price']);
                $total = wc_price($item['woofix_price'] * $item['woofix_qty']);
                echo "{$item['woofix_qty']} {$item['woofix_desc']} (@{$price})&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$total}&nbsp;&nbsp;&nbsp;";
                ?>
            </option>

        <?php endforeach; ?>
    </select>
</div>