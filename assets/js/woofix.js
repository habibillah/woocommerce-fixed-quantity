jQuery(document).ready(function($) {

    function filterAvalaibleQty() {
        if($('.variation_id').length) {
            var variation = $('.variation_id').val();
            $('select.qty option')
                .attr('disabled', true)
                .hide();
            $('select.qty option[data-variation="'+variation+'"]')
                .attr('disabled', false)
                .show();
        }
        console.log(variation)

    }

    $('body').on('change', '.variation_id', filterAvalaibleQty);
    filterAvalaibleQty();

    $('body').on('change', 'select[name$="[qty]"]', function() {
        var that = this;
        setTimeout(function () {
            $(that).closest('form').find('input[name="update_cart"]').click();
        }, 300);
    });
});
