=== WooCommerce Fixed Quantity ===
Contributors: habibillah
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JXW9V2RRZNDXW
Tags: woocomerce, fixed quantity, ecommerce, fixed price
Requires at least: 3.0.1
Tested up to: 4.2
Stable tag: 1.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customize woocomerce price based on fixed quantity. Each price for each quantity defined.

== Description ==

Sometime you need to sell fixed quantity instead of incremental quantity that supported by default by woocommerce.
So this is the right plugins for you. For example you need sell 100, 200, 300, and more quantities, and prevent user to
purchase with unlisted quantity.

This also will avoid **Sale Price** form in **General** tab, but will add the discount/specific price directly when
define fixed quantity list.

How to use
------------------------

1. When add/ edit product, make sure you have add **Regular Price** in general tab
2. Go to **Fixed Quantity Price** and define the list of various quantity allowed

Limitations
------------------------

Right now this plugins has limitations that not check **Stock Qty**. So when **Stock status** is **In stock** or
allowed back orders, the defined quantity list will shown, whatever quantity in **Stock Qty**.

== Installation ==

1. unzip downloaded `woocommerce-fixed-quantity` plugin
2. upload `woocommerce-fixed-quantity` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How can I change the listbox template? =

1. copy `/wp-content/plugins/woocommerce-fixed-quantity/woocommerce/ directory to `/wp-content/themes/[your-active-theme]/woocommerce-fixed-quantity`
2. you may modify the files inside `/wp-content/themes/[your-active-theme]/woocommerce-fixed-quantity` directory to fit your need.

== Screenshots ==

1. admin screen
2. quantity on product
3. quantity on cart
4. quantity on checkout

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

== Upgrade Notice ==

= 1.0.1 =
This is the initial public release. Fill free to ask, report bugs, etc.
