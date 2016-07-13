<?php
/**
 * Product quantity inputs
 *
 * This template can be overrided by copying it to yourtheme/woocommerce-fixed-quantity/global/quantity-input.php
 *
 * @var string $input_name
 * @var WC_Product $product
 * @var int $selected_quantity
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (empty($product))
    global $product;

$selected_quantity = !empty($selected_quantity)? $selected_quantity : '';

$data = WoofixUtility::isFixedQtyPrice($product->id);

?>
<div class="quantity_select">
    <select name="<?php echo esc_attr( $input_name ); ?>"
            title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ); ?>"
            class="qty">
        <?php foreach ($data['woofix'] as $item): ?>

            <?php
            $woofix_price = $item['woofix_price'];
            $woofix_qty = $item['woofix_qty'];
            $price = wc_price($woofix_price);
            $total = wc_price($woofix_price * $woofix_qty);

            $woofix_desc = !empty($item['woofix_desc'])? $item['woofix_desc'] : WOOFIXCONF_QTY_DESC;
            $description = str_replace(
                array('{qty}', '{price}', '{total}', ' '),
                array($woofix_qty,  $price, $total, '&nbsp;'),
                $woofix_desc
            );
            ?>

            <option value="<?php echo $woofix_qty; ?>"
                    <?php if(isset($item['variation'])): ?>
                        data-variation="<?php echo $item['variation'] ?>"
                    <?php endif ?>
                    data-qty="<?php echo $woofix_qty; ?>"
                    data-price="<?php echo $woofix_price; ?>" <?php echo ($selected_quantity == $woofix_qty)? "selected" : ""; ?>>

                <?php echo $description; ?>
            </option>

        <?php endforeach; ?>
    </select>
</div>
