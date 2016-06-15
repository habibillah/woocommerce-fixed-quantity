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

            <?php
            $woofix_price = wc_format_decimal($item['woofix_price']);
            $woofix_qty = $item['woofix_qty'];
            $woofix_desc = empty($item['woofix_desc'])? "" : $item['woofix_desc'];
            ?>

            <option value="<?php echo $woofix_qty; ?>" data-qty="<?php echo $woofix_qty; ?>" data-price="<?php echo $woofix_price; ?>">
                <?php
                $price = wc_price($woofix_price);
                $total = wc_price($woofix_price * $woofix_qty);
                $description_template = empty($woofix_desc)? "{qty} items @{price} {total}" : str_replace(' ', '&nbsp;', $woofix_desc);
                $description = str_replace(array('{qty}', '{price}', '{total}'), array($woofix_qty,  $price, $total), $description_template);
                if ($description_template == $description) {
                    echo "$woofix_qty $description @$price &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $total";
                } else {
                    echo $description;
                }
                ?>
            </option>

        <?php endforeach; ?>
    </select>
</div>
