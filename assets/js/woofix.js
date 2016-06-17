jQuery(document).ready(function($) {
    $('body').on('change', 'select[name$="[qty]"]', function() {
        var that = this;
        setTimeout(function () {
            $(that).closest('form').find('input[name="update_cart"]').click();
        }, 300);
    });
});
