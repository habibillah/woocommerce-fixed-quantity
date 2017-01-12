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
    <select  name="<?php echo esc_attr( $input_name ); ?>"
            title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ); ?>"
            class="qty">
        <?php if($product->is_type('variable')) : ?>
        <option  >
         Please select your attributes...
        </option>

        <?php else : ?>
       
        <?php foreach ($data['woofix'] as $item): ?>

            <?php
            $woofix_price = $item['woofix_price'];
            $woofix_qty = $item['woofix_qty'];
            if(  $product->is_type( 'simple' ) ){
                $price = $woofix_price;
            }elseif($product->is_type( 'variation' ) ){
                $price =  $product->woofixVariationBasePrice * ((100-$item['woofix_disc']) / 100);
            }
            $total = wc_price($price * $woofix_qty);
            $price = wc_price($price);
            $woofix_desc = !empty($item['woofix_desc'])? $item['woofix_desc'] : WOOFIXCONF_QTY_DESC;
            $description = str_replace(
                array('{qty}', '{price}', '{total}', ' '),
                array($woofix_qty,  $price, $total, '&nbsp;'),
                $woofix_desc
            );
            ?>

            <option value="<?php echo $woofix_qty; ?>"
                    data-qty="<?php echo $woofix_qty; ?>"
                    data-price="<?php echo $woofix_price; ?>" <?php echo ($selected_quantity == $woofix_qty)? "selected" : ""; ?>>
                
                <?php echo $description; ?>
            </option>

        <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

 