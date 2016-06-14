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

            add_filter('woocommerce_product_data_tabs', array(&$this, 'add_product_data_tab'));
            add_action('woocommerce_product_data_panels', array(&$this, 'add_product_data_panel'));
            add_action('woocommerce_process_product_meta', array(&$this, 'save_custom_fields'), 10);
            
            add_filter('parse_query', array(&$this, 'filter_query'));
            add_filter('woocommerce_product_filters', array(&$this, 'filter_products'));
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
            update_post_meta($post_id, '_woofix', htmlentities($_POST['_woofix']));
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
                        $query->query_vars['meta_key']      = '_woofix';
                    }
                }
            }
        }

    }
}
