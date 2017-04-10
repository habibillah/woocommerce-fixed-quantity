<?php
if (!defined('ABSPATH'))
    exit;

if (!class_exists('WooAdminFixedQuantity')) {
    class WooAdminFixedQuantity
    {
        private $file;

        public function __construct($file)
        {
            $this->file = $file;

            add_filter('plugin_action_links_' . plugin_basename($this->file), array($this, 'add_action_links'));
            add_filter('woocommerce_product_data_tabs', array($this, 'add_product_data_tab'));
            add_action('woocommerce_product_data_panels', array($this, 'add_product_data_panel'));
            add_action('woocommerce_process_product_meta', array($this, 'save_custom_fields'), 10);
            add_filter('woocommerce_get_sections_products', array($this, 'global_setting_section'));
            add_filter('woocommerce_get_settings_products', array($this, 'global_setting_configuration'), 10, 2);

            add_filter('parse_query', array($this, 'filter_query'));
            add_filter('woocommerce_product_filters', array($this, 'filter_products'));
        }

        /**
         * @param array $links
         * @return array
         *
         */
        public function add_action_links($links)
        {
            $settingURL = admin_url('admin.php?page=wc-settings&tab=products&section=woofixconf');
            array_unshift($links, '<a href="' . $settingURL . '">Settings</a>');

            return $links;
        }

        public function global_setting_configuration($settings, $current_section)
        {
            if ($current_section !== 'woofixconf')
                return $settings;

            $woofix_config = array();

            $woofix_config[] = array(
                'name' => __('Woocommerce Fixed Quantity Global Settings', 'woofix'),
                'type' => 'title',
                'desc' => __('The following options are used to configure Woocommerce Fixed Quantity globally', 'woofix'),
                'id' => 'woofixconf_title'
            );

            $woofix_config[] = array(
                'name'     => __('Qty Desc Template', 'woofix'),
                'desc_tip' => __('The default template to show description on each quantity. Off course, you can change the template on each product.', 'woofix'),
                'id'       => WOOFIXOPT_QTY_DESC,
                'default'  => WOOFIXCONF_QTY_DESC,
                'type'     => 'text',
                'class'    => 'input-text regular-input ',
                'required' => true,
                'desc'     => __('Available variable are: <code>{qty}</code>, <code>{discount}</code>, <code>{price}</code>, and <code>{total}</code>', 'woofix'),
            );

            $woofix_config[] = array(
                'name'     => __('Show Discount Info', 'woofix'),
                'id'       => WOOFIXOPT_SHOW_DISC,
                'default'  => WOOFIXCONF_SHOW_DISC,
                'type'     => 'checkbox',
                'desc'     => __('Show discount info in both cart and checkout page. See <a target="_blank" href="https://wordpress.org/plugins/woocommerce-fixed-quantity/faq/">FAQ</a> to customize discount info template.', 'woofix'),
            );

            $woofix_config[] = array(
                'name'     => __('Add to Cart as New Item', 'woofix'),
                'id'       => WOOFIXOPT_ADD_TO_CART_AS_NEW,
                'default'  => WOOFIXCONF_ADD_TO_CART_AS_NEW,
                'type'     => 'checkbox',
                'desc'     => __('When adding product to cart twice or more, add it as new instead of updating Qty.', 'woofix'),
            );

            $woofix_config[] = array(
                'name'     => __('Show Stock Availability', 'woofix'),
                'id'       => WOOFIXOPT_SHOW_STOCK,
                'default'  => WOOFIXCONF_SHOW_STOCK,
                'type'     => 'checkbox',
                'desc'     => __('Show stock availability in product page.', 'woofix'),
            );

            $woofix_config[] = array(
                'name'     => __('Check Stock', 'woofix'),
                'id'       => WOOFIXOPT_CHECK_STOCK,
                'default'  => WOOFIXCONF_CHECK_STOCK,
                'type'     => 'checkbox',
                'desc'     => __('Check stock availability before purchase/check out. <b>Not implemented yet.</b>', 'woofix'),
            );

            $available_roles = array();
            $all_roles = wp_roles()->roles;
            foreach ($all_roles as $role_name => $role_info) {
                $available_roles[$role_name] = $role_info['name'];
            }
            
            $woofix_config[] = array(
                'name'     => __('Default Role', 'woofix'),
                'id'       => WOOFIXOPT_DEFAULT_ROLE,
                'default'  => WOOFIXCONF_DEFAULT_ROLE,
                'type'     => 'select',
                'options'  => $available_roles,
                'class'  => 'wc-enhanced-select',
                'required' => true,
                'desc'     => __('The role logged in with no data for item price will use default role data.', 'woofix'),
            );

            $woofix_config[] = array(
                'name'     => __('Available Roles', 'woofix'),
                'id'       => WOOFIXOPT_AVAILABLE_ROLES,
                'type'     => 'multiselect',
                'class'  => 'wc-enhanced-select',
                'options'  => $available_roles,
                'desc'     => __('Available Roles will be shown when you add/edit product. Default Role will be shown even not listed here.', 'woofix'),
            );
            
            $woofix_config[] = array(
                'type' => 'sectionend',
                'id' => 'woofixconf'
            );

            return $woofix_config;
        }

        public function global_setting_section($sections)
        {
            $sections['woofixconf'] = __('Fixed Quantity', 'woofix');
            return $sections;
        }

        public function add_product_data_tab($tabs)
        {
            $tabs['woofix'] = array(
                'label' => __('Fixed Quantity Price', 'woofix'),
                'target' => 'woofix_product_data',
                'class' => array('woofix_product_data'),
            );

            return $tabs;
        }

        public function add_product_data_panel()
        {
            /** @noinspection PhpIncludeInspection */
            require_once plugin_dir_path($this->file) . 'views/html-admin-meta-box.php';
        }

        public function save_custom_fields($post_id)
        {
            if (!empty($_POST['_woofix']) && $_POST['_woofix'] != "{}") {
                update_post_meta($post_id, '_woofix', htmlentities($_POST['_woofix']));
            } else {
                delete_post_meta($post_id, '_woofix');
            }
        }
        
        function filter_products($output)
        {
            $html = new DOMDocument();
            if ($html->loadHTML(mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8'))) {
                $element = $html->createElement('option', __('Fixed Qty', 'woofix'));
                $element->setAttribute('value', '_woofix');
                if (!empty($_GET['product_type']) && $_GET['product_type'] == '_woofix') {
                    $element->setAttribute('selected', 'selected');
                }
                $select_node = $html->getElementsByTagName('select')->item(0);
                $select_node->appendChild($element);
                $output = $html->saveXML($select_node);
            }

            return $output;
        }

        function filter_query($query)
        {
            /** @noinspection PhpUnusedLocalVariableInspection */
            global $typenow, $wp_query;
            if ($typenow == 'product') {
                if ( isset( $query->query_vars['product_type'] ) ) {
                    if ( '_woofix' == $query->query_vars['product_type'] ) {
                        $query->query_vars['product_type']  = '';
                        $query->is_tax = false;
                        $query->query_vars['meta_key'] = '_woofix';
                    }
                }
            }
        }

    }
}
