jQuery(document).ready(function ($) {
    var variationForm = $(".single_variation_wrap");
    var $el = $(".qty");

    //On Variation selection r
    variationForm.on("show_variation", function (event, variation) {
        var data = {
            action: 'get_dropdown',
            variation: variation.variation_id
        };

        $.post(woofix.ajaxurl, data, function (response) {
            $el.empty(); // remove old options
            $.each(response, function (key, value) {
                $el.append($("<option></option>")
                   .attr("value", value.qty).attr("data-price", value.price).html(value.text));
            });
        });
    });

    $(".variations_form").on("reset_data", function () {
        $el.empty(); // remove old options
        $el.append("<option>    Please select your attributes...</option>");
    });


});
