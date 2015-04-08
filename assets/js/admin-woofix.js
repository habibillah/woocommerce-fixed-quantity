jQuery(document).ready(function($) {

    var woofixPriceTableSelector = '#woofix_price_table';

    var regenerateData = function() {
        var data = $(woofixPriceTableSelector + ' :input').serializeJSON();
        $('input[name="_woofix"]').val(JSON.stringify(data));
    };

    var regenerateIndex = function() {
        $(woofixPriceTableSelector).find('tbody tr').each(function(index) {
            $(this).find('[data-name]').each(function() {
                var inputName = 'woofix[' + index + '][' + $(this).data('name') + ']';
                $(this).attr('name', inputName);
                $(this).attr('id', inputName);
            });
        });
    };

    //=========================================

    var existingVal = $('input[name="_woofix"]').val();
    if (existingVal != '') {
        existingVal = JSON.parse(existingVal);
        $.each(existingVal.woofix, function(key, value) {
            var row = $('#woofix_template').find('tr').clone();
            row.find('input[data-name="woofix_desc"]').val(value.woofix_desc);
            row.find('input[data-name="woofix_qty"]').val(value.woofix_qty);
            row.find('input[data-name="woofix_disc"]').val(value.woofix_disc);
            row.find('input[data-name="woofix_price"]').val(value.woofix_price);
            row.appendTo('#woofix_price_table tbody');
        });

        regenerateIndex();
    }

    $('#woofix_add_price').on('click', function() {

        if ($('#_regular_price').val() == '') {
            alert ('Please add regular price.');
            return false;
        }
        $('#woofix_template').find('tr').clone().appendTo('#woofix_price_table tbody');

        regenerateIndex();
    });

    $(woofixPriceTableSelector).on('click', '.woofix_delete', function() {
        $(this).closest('tr').remove();

        regenerateIndex();
        regenerateData();
    });

    $(woofixPriceTableSelector).on('change', 'input[name*="woofix_qty"]', function() {
        var newVal = $(this).val();
        if (newVal == "" || isNaN(newVal) || parseFloat(newVal) < 0) {
            newVal = 0;
        }

        $(this).val(parseInt(newVal).toFixed());

        regenerateData();
    });

    $(woofixPriceTableSelector).on('change', 'input[name*="woofix_desc"]', function() {
        regenerateData();
    });

    $(woofixPriceTableSelector).on('change', 'input[name*="woofix_disc"]', function() {
        var newVal = $(this).val();
        if (newVal == "" || isNaN(newVal) || parseFloat(newVal) > 100 || parseFloat(newVal) < 0) {
            newVal = 0;
            $(this).val(0);
        }

        var regularPrice = $('#_regular_price').val();
        var $price = $(this).closest('tr').find('input[name*="woofix_price"]');

        $price.val((regularPrice -  ((newVal/100) * regularPrice)).toFixed(2));

        regenerateData();
    });


    $(woofixPriceTableSelector).on('change', 'input[name*="woofix_price"]', function() {

        var regularPrice = $('#_regular_price').val();

        var newVal = $(this).val();
        if (newVal == "" || isNaN(newVal) || parseFloat(regularPrice) < parseFloat(newVal) || parseFloat(newVal) < 0) {
            newVal = regularPrice;
            $(this).val(regularPrice);
        }

        var $disc = $(this).closest('tr').find('input[name*="woofix_disc"]');
        $disc.val((((regularPrice - newVal) / regularPrice) * 100).toFixed());

        regenerateData();
    });
});