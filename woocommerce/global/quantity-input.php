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
            <option value="<?php echo $item['woofix_qty'] ?>" data-qty="<?php echo $item['woofix_qty'] ?>" data-price="<?php echo $item['woofix_price']; ?>">
                <?php
                $price = wc_price($item['woofix_price']);
                $total = wc_price($item['woofix_price'] * $item['woofix_qty']);
                $description_template = empty($item['woofix_desc'])? "{qty} items @{price} {total}" : str_replace(' ', '&nbsp;', $item['woofix_desc']);
                $description = str_replace(array('{qty}', '{price}', '{total}'), array($item['woofix_qty'],  $price, $total), $description_template);
                if ($description_template == $description) {
                    echo "{$item['woofix_qty']} $description @$price &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $total";
                } else {
                    echo $description;
                }
                ?>
            </option>

        <?php endforeach; ?>
    </select>
</div>
