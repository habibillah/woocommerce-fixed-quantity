<?php
/**
 * Discount info in both Cart and Checkout page
 *
 * This template can be overrided by copying it to yourtheme/woocommerce-fixed-quantity/discount-info.php
 *
 * @var string $price
 * @var string $discount
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<span class="discount-info">
    <span class="new-price">
        <span class="woocommerce-Price-amount amount">
            <?php echo $price; ?>
            <span>(Incl. <?php echo $discount; ?> discount)</span>
        </span>
    </span>
</span>
