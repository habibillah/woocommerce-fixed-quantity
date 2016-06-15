jQuery(document).ready(function($) {
    $('body').on('change', '.woofix_qty_on_cart', function() {
        var that = this;
        setTimeout(function () {
            $(that).closest('form').find('input[name="update_cart"]').click();
        }, 300);
    });
});
