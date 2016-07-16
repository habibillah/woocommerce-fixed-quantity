/* globals woofix_admin */

jQuery(document).ready(function($) {

    var woofixjs_admin = { decimal_point: '.', num_decimals: 2 };
    if (typeof woofix_admin !== 'undefined')
        woofixjs_admin = woofix_admin;


    var woofix_product_data = '#woofix_product_data_table';
    var inputWoofixSelector = 'input[name="_woofix"]';

    var regenerateData = function() {
        var data = $(woofix_product_data + ' :input').serializeWofix(woofixjs_admin);
        $(inputWoofixSelector).val(JSON.stringify(data));
    };

    var get_regular_price = function() {
        return get_product_type() == 'variable'?
            $('.remove_variation[rel="'+get_selected_variation()+'"]')
                .parents('.woocommerce_variation')
                    .find('.wc_input_price').val() :
            $('#_regular_price').val();
    }

    var get_product_type = function() {
        return $('#product-type').val();
    }

    var get_selected_variation = function() {
        return $('#variations-fixed-price').val();
    }

    var regenerateIndex = function(role) {
        $('.woofix_price_table_container[data-role-key="' + role + '"]')
            .find('tbody tr')
            .each(function(index) {

            $(this).find('[data-name]').each(function() {
                var name = $(this).data('name');
                var inputName = 'woofix[' + role + '][' + index + '][' + name + ']';
                if (name === "woofix_disc" || name === "woofix_price")
                    inputName += ":woodecimal";

                if (name === "woofix_qty")
                    inputName += ":number";

                $(this).attr('name', inputName);
                $(this).attr('id', inputName);
            });
        });
    };

    var updateBox = function () {
        var productType = get_product_type();
        if (productType == 'simple') {
            $('.woofix_options').show();
            $('.woofix-variation-zone').hide();
            feed_table();
        } else if(productType == 'variable') {
            $('.woofix_options').show();
            $('.woofix-variation-zone').show();
            var options = $('.woocommerce_variation').map(function(i, e) {
                var variation_id = $(this).find('.remove_variation').attr('rel');
                var attributes = $(this).find('h3 select').map(function() {
                    return $(this).find('option:selected').text();
                });
                var variation_name = attributes.toArray().join(' | ');
                return '<option value='+variation_id+'>'+variation_name+'</option>';
            });
            if(options.length) {
                $('#variations-fixed-price').html(options.toArray().join());
            }
            if(get_selected_variation()) feed_table();
        } else {
            $('.woofix_options').hide();
        }

    };

    var hideShowVariationRow = function() {
        $('.woofix_price_table_container tbody tr').hide();
        $('.woofix_price_table_container tbody tr.variation_'+get_selected_variation()).show();
    }


    $('.woofix_refresh_variation').on('click', function(e) {
        e.preventDefault();
        updateBox();
        hideShowVariationRow();
    });

    $('#variations-fixed-price').on('change', hideShowVariationRow);

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
        updateBox();
    });

    var feed_table = function() {
        if ($(inputWoofixSelector).length > 0) {
            var existingVal = $(inputWoofixSelector).val();
            if (existingVal != '') {
                existingVal = JSON.parse(existingVal);
                $.each(existingVal['woofix'], function(roleKey, data) {

                    var needRegenerate = false;
                    if (!isNaN(roleKey)) {
                        needRegenerate = true;
                        roleKey = $('.woofix_price_table_container:first-child').data('role-key');
                        var newdata = {};
                        newdata[roleKey] = data;
                        data = newdata;
                    }

                    var tableContainer = $('.woofix_price_table_container[data-role-key="' + roleKey + '"]');
                    var table = tableContainer.find('.woofix_price_table tbody');
                    table.find('tr').remove();

                    $.each(data, function (index, value) {
                        if(!value) return
                        var row = $('#woofix_template').find('tr').clone();
                        row.find('input[data-name="woofix_desc"]').val(value['woofix_desc']);
                        row.find('input[data-name="woofix_qty"]').val(value['woofix_qty']);
                        row.find('input[data-name="woofix_disc"]').val(formatNumberToSave(value['woofix_disc']));
                        row.find('input[data-name="woofix_price"]').val(formatNumberToSave(value['woofix_price']));
                        row.find('input[data-name="woofix_variation"]').val(value['woofix_variation'])
                        if(value['woofix_variation']) {
                            row.addClass('variation_'+value['woofix_variation']);
                        }
                        row.appendTo(table);
                    });

                    regenerateIndex(roleKey);

                    if (needRegenerate)
                        regenerateData();
                });
            }
        }
    }

    $('.woofix_add_price').on('click', function() {
        var regularPrice = get_regular_price();
        if (regularPrice == '' || regularPrice <= 0) {
            alert ('Please add regular price.');
            return false;
        }
        var tableContainer = $(this).closest('.woofix_price_table_container');
        var row = $('#woofix_template').find('tr').clone();
        row.addClass('variation_'+get_selected_variation());
        row.data('variation', get_selected_variation());
        row.appendTo(tableContainer.find('.woofix_price_table tbody'));
        if(get_selected_variation()) {
            row.find('.woofix_input_variation').val(get_selected_variation());
        }
        regenerateIndex(tableContainer.data('role-key'));
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

    $(woofix_product_data).on('click', '.woofix_delete', function() {
        var roleKey = $(this).closest('.woofix_price_table_container').data('role-key');
        $(this).closest('tr').remove();

        regenerateIndex(roleKey);
        regenerateData();
    });

    $(woofix_product_data).on('change', 'input[name*="woofix_qty"]', function() {
        var newVal = $(this).val();
        if (newVal == "" || isNaN(newVal) || parseInt(newVal) <= 0) {
            newVal = 1;
        }

        $(this).val(parseInt(newVal));

        regenerateData();
    });

    $(woofix_product_data).on('change', 'input[name*="woofix_desc"]', function() {
        regenerateData();
    });

    $(woofix_product_data).on('change', 'input[name*="woofix_disc"]', function() {
        validateMonetary(this);

        var newVal = window.accounting.unformat($(this).val(), woofixjs_admin.decimal_point);
        if (newVal > 100 || newVal < 0) {
            newVal = 0;
        }
        $(this).val(formatNumberToSave(newVal));

        var regularPriceValue = get_regular_price();
        var regularPrice = window.accounting.unformat(regularPriceValue, woofixjs_admin.decimal_point);
        var $price = $(this).closest('tr').find('input[name*="woofix_price"]');

        $price.val(formatNumberToSave(regularPrice - ((newVal/100) * regularPrice)));

        regenerateData();
    });


    $(woofix_product_data).on('change', 'input[name*="woofix_price"]', function() {

        var regularPriceValue = get_regular_price();
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

    $('.woofix_price_table_container h2').click(function () {
        $(this).closest('.woofix_price_table_container').find('.button-link').click();
    });

    updateBox();
});
