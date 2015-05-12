<?php
global $post;
$custom_fields = get_post_custom($post->ID);
?>

<div id="woofix_product_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
	<div class="options_group">
        <input type="hidden" name="_woofix" value="<?php echo !empty($custom_fields["_woofix"][0])? $custom_fields["_woofix"][0] : ''; ?>" />
        <p>
            <a id="woofix_add_price" class="button button-primary"><?php _e('Add Price', 'woofix'); ?></a>
        </p>
        <p>
        <table id="woofix_price_table">
            <thead>
            <tr>
                <th class="woofix_desc"><?php _e('Description', 'woofix'); ?></th>
                <th class="woofix_qty"><?php _e('Qty', 'woofix'); ?></th>
                <th class="woofix_disc"><?php _e('Disc (%)', 'woofix'); ?></th>
                <th class="woofix_price"><?php _e('Price Per Qty', 'woofix'); ?></th>
                <th><?php _e('Action', 'woofix'); ?></th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
        </p>
    </div>

    <table id="woofix_template" class="woofix hidden">
        <tr>
            <td><input type="text" class="woofix_input_desc" data-name="woofix_desc" /></td>
            <td><input type="text" class="woofix_input_qty" data-name="woofix_qty" /></td>
            <td><input type="text" class="woofix_input_disc" data-name="woofix_disc" /></td>
            <td><input type="text" class="woofix_input_price" data-name="woofix_price" /></td>
            <td><a class="woofix_delete button"><?php _e('Remove', 'woofix'); ?></a></td>
        </tr>
    </table>
</div>
