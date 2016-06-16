/* globals woofix_admin */

jQuery(document).ready(function($) {

    var woofixjs_admin = { decimal_point: '.', num_decimals: 2 };
    if (typeof woofix_admin !== 'undefined')
        woofixjs_admin = woofix_admin;


    var woofixPriceTableSelector = '#woofix_price_table';
    var inputWoofixSelector = 'input[name="_woofix"]';

    var regenerateData = function() {
        var data = $(woofixPriceTableSelector + ' :input').serializeJSON({
            "customTypes": {
                "woodecimal": function (value) {
                    return window.accounting.unformat(value, woofixjs_admin.decimal_point);
                }
            }
        });
        $(inputWoofixSelector).val(JSON.stringify(data));
    };

    var regenerateIndex = function() {
        $(woofixPriceTableSelector).find('tbody tr').each(function(index) {
            $(this).find('[data-name]').each(function() {
                var name = $(this).data('name');
                var inputName = 'woofix[' + index + '][' + name + ']';
                if (name === "woofix_disc" || name === "woofix_price")
                    inputName += ":woodecimal";

                if (name === "woofix_qty")
                    inputName += ":number";

                $(this).attr('name', inputName);
                $(this).attr('id', inputName);
            });
        });
    };

    var showHideWoofix = function () {
        var productType = $('#product-type').val();
        if (productType == 'simple') {
            $('.woofix_options').show();
        } else {
            $('.woofix_options').hide();
        }
    };
    showHideWoofix();


    var validateMonetary = function (selector) {
        var value = $(selector).val();
        var regex = new RegExp('[^\-0-9\%\\' + woofixjs_admin.decimal_point + ']+', 'gi');
        var newvalue = value.replace(regex, '');

        if ( value !== newvalue ) {
            $(selector).val(newvalue);
            $(document.body).triggerHandler('wc_add_error_tip', [$(selector), 'i18n_mon_decimal_error']);
        } else {
            $(document.body).triggerHandler('wc_remove_error_tip', [$(selector), 'i18n_mon_decimal_error']);
        }
    };

    var formatNumberToSave = function (number) {
        return window.accounting.format(number, woofixjs_admin.num_decimals, '', woofixjs_admin.decimal_point);
    };

    //=========================================

    $('#product-type').on('change', function () {
        showHideWoofix();
    });

    if ($(inputWoofixSelector).length > 0) {
        var existingVal = $(inputWoofixSelector).val();
        if (existingVal != '') {
            existingVal = JSON.parse(existingVal);
            $.each(existingVal['woofix'], function(key, value) {
                var row = $('#woofix_template').find('tr').clone();
                row.find('input[data-name="woofix_desc"]').val(value['woofix_desc']);
                row.find('input[data-name="woofix_qty"]').val(value['woofix_qty']);
                row.find('input[data-name="woofix_disc"]').val(formatNumberToSave(value['woofix_disc']));
                row.find('input[data-name="woofix_price"]').val(formatNumberToSave(value['woofix_price']));
                row.appendTo('#woofix_price_table tbody');
            });

            regenerateIndex();
        }
    }

    $('#woofix_add_price').on('click', function() {
        var regularPrice = $('#_regular_price').val();
        if (regularPrice == '' || regularPrice <= 0) {
            alert ('Please add regular price.');
            return false;
        }
        $('#woofix_template').find('tr').clone().appendTo('#woofix_price_table tbody');

        regenerateIndex();
    });
    
    $('#_regular_price').on('change', function() {

        var regularPriceValue = $(this).val();
        var regularPrice = window.accounting.unformat(regularPriceValue, woofixjs_admin.decimal_point);

        if (regularPrice > 0) {
            
            $('input[name*="woofix_price"]').each(function() {
                var discountValue = $(this).closest('tr').find('input[name*="woofix_disc"]').val();
                var discount = window.accounting.unformat(discountValue, woofixjs_admin.decimal_point);
                if (discount > 100 || discount < 0) {
                    discount = 0;
                }

                $(this).val(formatNumberToSave(regularPrice - ((discount/100) * regularPrice)));

                regenerateData();                
            });
        }
    });

    $(woofixPriceTableSelector).on('click', '.woofix_delete', function() {
        $(this).closest('tr').remove();

        regenerateIndex();
        regenerateData();
    });

    $(woofixPriceTableSelector).on('change', 'input[name*="woofix_qty"]', function() {
        var newVal = $(this).val();
        if (newVal == "" || isNaN(newVal) || parseInt(newVal) <= 0) {
            newVal = 1;
        }

        $(this).val(parseInt(newVal));

        regenerateData();
    });

    $(woofixPriceTableSelector).on('change', 'input[name*="woofix_desc"]', function() {
        regenerateData();
    });

    $(woofixPriceTableSelector).on('change', 'input[name*="woofix_disc"]', function() {
        validateMonetary(this);

        var newVal = window.accounting.unformat($(this).val(), woofixjs_admin.decimal_point);
        if (newVal > 100 || newVal < 0) {
            newVal = 0;
        }
        $(this).val(formatNumberToSave(newVal));

        var regularPriceValue = $('#_regular_price').val();
        var regularPrice = window.accounting.unformat(regularPriceValue, woofixjs_admin.decimal_point);
        var $price = $(this).closest('tr').find('input[name*="woofix_price"]');

        $price.val(formatNumberToSave(regularPrice - ((newVal/100) * regularPrice)));

        regenerateData();
    });


    $(woofixPriceTableSelector).on('change', 'input[name*="woofix_price"]', function() {

        var regularPriceValue = $('#_regular_price').val();
        var regularPrice = window.accounting.unformat(regularPriceValue, woofixjs_admin.decimal_point);

        validateMonetary(this);

        var newVal = window.accounting.unformat($(this).val(), woofixjs_admin.decimal_point);
        if (regularPrice < newVal || newVal < 0) {
            newVal = regularPrice;
        }
        $(this).val(formatNumberToSave(newVal));

        var $disc = $(this).closest('tr').find('input[name*="woofix_disc"]');
        $disc.val(formatNumberToSave(((regularPrice - newVal) / regularPrice) * 100));

        regenerateData();
    });

});
