jQuery(document).ready(function($) {
    $('.woofix_qty_on_cart').on('change', function() {
        $('input[name="update_cart"]').click();
    });
});
