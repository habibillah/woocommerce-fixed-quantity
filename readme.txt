=== WooCommerce Fixed Quantity ===
Contributors: habibillah
Donate link: https://www.paypal.me/habibillah
Tags: woocomerce, fixed quantity, ecommerce, fixed price
Requires at least: 3.0.1
Tested up to: 4.9
Version: 1.2.1
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customize woocomerce price based on fixed quantity. Each price for each quantity defined.

== Description ==

Sometime you need to sell fixed quantity instead of incremental quantity that supported by default by woocommerce.
So this is the right plugins for you. For example you need sell 100, 200, 300, and more quantities, and prevent user to
purchase with unlisted quantity.

This also will avoid **Sale Price** form in **General** tab, but will add the discount/specific price directly when
define fixed quantity list.

= Demo Website =

Sample product can be accessed at [Woofix demo website](https://woofix.kalicode.com/)

1. [Happy Ninja](https://woofix.kalicode.com/shop/happy-ninja/)
2. [Woo Ninja](https://woofix.kalicode.com/shop/woo-ninja/)
3. [More...](https://woofix.kalicode.com/)

= How to use =

1. When add/ edit product, make sure you have add **Regular Price** in general tab
2. Go to **Fixed Quantity Price** and define the list of various quantity allowed

= Limitations =

Right now this plugins has limitations that not check **Stock Qty**. So when stock status is **In stock** or
**Allowed Back Orders**, the defined quantity list will shown, whatever quantity in **Stock Qty**.

= Available Filters Hook =

1. woofix_quantity_is_not_valid
2. woofix_product_add_to_cart_text
3. woofix_sort_qty_data_to_show
4. woofix_set_item_price

= Available Actions Hook =

1. woofix_before_quantity_input
2. woofix_after_quantity_input

== Installation ==

1. unzip downloaded `woocommerce-fixed-quantity` plugin
2. upload `woocommerce-fixed-quantity` to the `/wp-content/plugins/` directory
3. Activate the plugin through the **Plugins** menu in WordPress
4. Global settings for this plugin located at `WooCommerce Settings >> Products >> Fixed Quantity`

== Frequently Asked Questions ==

= How can I change the listbox and discount templates? =

1. copy `/wp-content/plugins/woocommerce-fixed-quantity/templates/` directory to `/wp-content/themes/[your-theme]/woocommerce-fixed-quantity`
2. you may modify the files inside `/wp-content/themes/[your-theme]/woocommerce-fixed-quantity` directory to fit your need.

= How can I hide discount info both Cart and Checkout pages? =

Go to global settings for this plugin located at `WooCommerce Settings >> Products >> Fixed Quantity`.
Uncheck **Show discount info**. Don't forget to hit **Save Changes** button.

== Screenshots ==

1. admin global settings
2. admin product screen
3. admin product filter
4. quantity on product 1
5. quantity on product 2
6. quantity on cart
7. quantity on checkout

== Changelog ==

= 1.0.1 =
* Initial public release.
* Add fixed quantity to product

= 1.0.2 =
* add screenshot

= 1.0.3 =
* link screenshot to github

= 1.0.5 =
* automatic change fixed qty price if regular price changed
* automatic update cart when quantity changed in cart
* flexible item description template

= 1.0.6 =
* fix quantity selection on cart

= 1.0.7 =
* fix invalid price on mini cart

= 1.0.8 =
* add support for multi currency options

= 1.0.9 =
* add filters: woofix_quantity_is_not_valid, woofix_product_add_to_cart_text
* add global configuration at `WooCommerce Settings >> Products >> Fixed Quantity`
* add show hide discount info in global setting
* move discount info to template

= 1.1.0 =
* Simpilfy quantity input template

= 1.1.1 =
* Add show/hide stock availability
* Support for multiple user role
* Add `woofix_sort_qty_data_to_show` filter

= 1.1.2 =
* Fixing conflict with underscore

= 1.1.3 =
* For to use woofix price when other plugin by-pass add to cart
* Add translation files
* fix template load error

= 1.1.4 =
* Add support for multisite
* Tested with wordpress 4.7

= 1.1.5 =
* Add **{discount}** code templete

= 1.1.6 =
* Add option to force add to cart as new item instead of updating existing product quantity

= 1.1.7 =
* Fix regarding woocommerce major release 3.0

= 1.1.8 =
* tested to WP 4.8

= 1.1.9 =
* Fix devided by zero discount

= 1.2.0 =
* tested up to WP 4.9

= 1.2.1 =
* Fix for WPML multiple currency

== Upgrade Notice ==

= 1.0.1 =
This is the initial public release. Fill free to ask, report bugs, etc.

