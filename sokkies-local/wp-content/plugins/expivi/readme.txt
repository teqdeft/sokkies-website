=== 3D Product configurator for WooCommerce ===
Contributors: expivi
Tags: 3D visualisation, product configuration, ar, augmented reality, visual product configurator
Requires at least: 5.3
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 2.16.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easy-to-use 3D product configurator to show your products in 360°

== Description ==

This plugin allows your WooCommerce-powered webshop to display your products in a 3D environment. It integrates with the Expivi platform, so you will need to register an account on [Expivi.com](https://www.expivi.com/).

Not sure if Expivi is a good fit for your products? [Book a free 15-minute demo](https://www.expivi.com/demo/) and we'll show you.

You will need to use the Expivi online configurator to create and configure your products. Afterwards, you can link your WooCommerce products to your Expivi products.

== Installation ==

1. Upload the Expivi plugin to your Woocommerce installation, activate it and enter the API key and API URL (https://expivi.net/api/).
2. Add a product and go to the Expivi product tab in the product configuration of Woocommerce.
3. Here you can select which product reflects the Woocommerce product on expivi.net

Visit our [Expivi guide](https://knowledge.expivi.com/knowledge-base/website-integration/expivi-woocommerce-integration/) for more information.

== Frequently Asked Questions ==

= Knowledge Base =
You can find solutions and answers at the [Expivi guide](https://knowledge.expivi.com/knowledge-base/website-integration/expivi-woocommerce-integration/).

= Is this plugin compatible with latest version of Woocommerce? =

We try to keep our plugin up-to-date and compatible with the latest Woocommerce version.
Any issues found can be reported at https://www.expivi.com/contact
We would love to hear your feedback!

== Changelog ==
= 2.16.1 05/18/2026 =
* Update Expivi Viewer to 1.77.2

= 2.16.0 05/18/2026 =
* Improved: Updated viewer to 1.75.0

= 2.15.6 21/10/2025 =
* Improved: Updated viewer to 1.61.0

= 2.15.5 24/07/2025 =
* Added new Cart Redirect Hook (expivi_after_add_to_cart_redirect)

= 2.15.4 30/06/2025 =
* Added new JS Hook xpv_init_custom_components_before_create

= 2.15.3 30/06/2025 =
* Improved: Updated viewer to 1.57.2

= 2.15.2 03/03/2025 =
* Improved: Updated viewer to 1.56.2 and options to 1.50.0

= 2.15.1 03/03/2025 =
* Improved: Updated viewer to 1.53.0 and options to 1.47.0

= 2.15.0 13/02/2025 =
* Improved: Support HPOS in woocommerce.
* Improved: Updated viewer to 1.45.3 and options to 1.42.2.

= 2.14.0 03/10/2024 =
* Improved: Updated viewer to 1.45.3 and options to 1.42.2.

= 2.13.0 19/09/2024 =
* Improved: Updated viewer to 1.45.1 and options to 1.42.1.

= 2.12.0 01/08/2024 =
* Improved: Updated viewer to 1.44.1 and options to 1.41.1.
* Fixed: Resolved issue where viewer was not loading product in Product settings (Admin area).

= 2.11.0 24/06/2024 =
* Improved: Update viewer to 1.42.2 and options to 1.39.2.
* Notice: The options update also brings changes to the tooltip.
		The underlying library is now using 'Floating UI' which brings changes in HTML structure & CSS classes.
		Any custom (CSS) changes on the tooltip or dropdown, might need to be checked during upgrade to this version.
* Improved: Changed WpOrg usage to WP_HTTP to support older versions of WordPress.

= 2.10.1 01/03/2024 =
* Improved: Update viewer to 1.40.3 and options to 1.37.1.

= 2.10.0 28/02/2024 =
* Improved: Type-hinting and other code modernizations.
* New: Allow user data to be passed to product configuration PDF.
* Improved: Update viewer to 1.40.0 and options to 1.37.0.

= 2.9.0 14/12/2023 =
* Fixed: Showing thumbnail of configured product in cart, if cart process is set to 'Multiple'.
* Fixed: Update viewer to 1.37.0 and options to 1.35.0.
		Changed naming of certain registered scripts/styles.
		Please note that the viewer update includes the new (beta) renderer.
		Certain scripts will also be loaded dynamically, so if you notice any issues, please let us know!

= 2.8.0 15/11/2023 =
* New: Important: This plugin now requires PHP 7.4.
* New: Print Ready File generation. You now have the ability to let the plugin automatically generate PRF's during  checkout.
* New: New add-to-cart flow: 'Multiple'. This option will use the SKU's in the configured Expivi product to search for WC products and add them to the cart.
		The amount of SKU's in the configured product will tell the quantity of the WC product.

= 2.7.8 2023/07/20 =
* Fixed: Update viewer to 1.28.1 and options to 1.27.1.

= 2.7.7 2023/06/27 =
* Fixed: Update viewer to 1.27.4 and options to 1.26.0.
* New: The viewer update include the use of Print Ready Files. This feature is backwards-compatible.

= 2.7.6 2023/04/17 =
* Fixed: Fixed 'Camera position' and 'Show progress' setting in product settings.
* New: Configured product details are now shown in Save My Design mails.

= 2.7.5 2023/04/11 =
* Fixed: Update viewer to 1.25.2.

= 2.7.4 2023/04/03 =
* Fixed: Update viewer to 1.25.0.
* Fixed: Show additional information in cart/order for SVG Text attributes.

= 2.7.3 2023/03/16 =
* Fixed: Don't show total weight text in cart when not defined.
* Fixed: Update viewer to 1.24.0 and options to 1.24.0.

= 2.7.2 2023/01/26 =
* Fixed: Update viewer to 1.20 and options to 1.20.1.

= 2.7.1 2023/01/09 =
* Fixed: Added weight to grouped product to help third party plugins with calculating weight.
* Fixed: Improved stock-management for sub-products of a grouped product.
* New: The (sub) products in 'cart/cart.phtml' template are now split off into a new separate template: 'cart/cart_sub_product_item.phtml'.

= 2.7.0 - 2022/12/21 =
* New: Add an additional (hidden) email-address to save-design emails that are being sent to customer.
* Fixed: Resolve price calculation issue where prices were being added of hidden attribute(s). Now, hidden attribute prices are not being added to total price.

= 2.6.2 - 2022/12/09 =
* Fixed: Updated viewer/options to 2022.17.1.

= 2.6.1 - 2022/12/09 =
* Fixed: Removed support for pll_current_language due to incorrect formatting.
* Fixed: Small improvement regarding product bundled feature, which will now ignore incorrect articles to connect sub-products and improve validation of stock.
* Fixed: Certain JS objects will now use window space to avoid being overridden by third party plugins.

= 2.6.0 - 2022/11/28 =
* New: Introducing product bundles!
	This feature allows you to convert your simple Expivi product into a grouped Expivi product.
	An Expivi grouped product will contain WC products based on SKU's defined in the Expivi grouped product.
	There is an option to either use the price from the WC products, or use a custom price defined by Expivi.

= 2.5.3 - 2022/09/14 =
* New: Include attribute (value) keys in configuration during checkout process + order.
* New: Added hook to request configured bundle id from order item.
* New: Added SVG Conversion Controller as part of preparation for generating Print Ready Files in orders.

= 2.5.2 - 2022/09/06 =
* Fixed: Updated viewer & options to Expivi 2022.08R5
* Fixed: Replaced 'get_query_var' by '$_GET' to avoid conflictions with third party plugins.

= 2.5.1 - 2022/07/12 =
* Fixed: Updated viewer/options to 1.474 (patch 1)

= 2.5.0 - 2022/04/25 =
* New: Introducing save and share my configured 3D product by email!
       A product can be switched from the add-to-cart shop flow to a
       save-my-design flow, so that users can create, save and share their customized products.
       Without needing the WooCommerce add-to-cart shopping flow!
       You can even add a follow-up action by adding a representative, whom will be notified
       when the users want to get in contact, after customizing their product.
       To whom can be setup in our Expivi settings.

= 2.4.6 - 2022/03/09 =
* Fixed: Update viewer/options to v1.468 (patch 3)

= 2.4.5 - 2022/02/17 =
* Fixed: Update viewer/options to v1.468

= 2.4.4 - 2021/10/01 =
* Fixed: Update viewer/options to v1.461
* Fixed: Improve script loading for templates. This should fix the ability to use Expivi products by shortcode ([product_page id="99"]).

= 2.4.3 - 2021/06/18 =
* Fixed: Multi-product issue where replica products where denied.
* New: Pass configuration to cart, check-out, e-mail templates to have the ability to visualize more data.

= 2.4.2 - 2021/06/15 =
* Fixed: Plugin is now PHP 8.0 compatible.
* Fixed: Encoding issues when adding products to cart.
* Fixed: Crash when trying to refund orders.
* New: We've added support for cron-jobs to automatically clean-up logs created by the plugin.

= 2.4.1 - 2021/06/04 =
* Fixed: An accidental package slipped through which required a higher PHP version in regards to our minimum PHP version.

= 2.4.0 - 2021/06/02 =
* New: Introducing log-file viewer and system settings in Expivi settings!
       The 'Logs' tab will show all the expivi logs which are present on the server.
       Any errors regarding our plugin will be logged in these log files.
       The 'Info' tab will show a wide range of system information.
* New: Introducing Social Sharing! Once your account supports social sharing,
       a social media icon will be present in the viewer. This will help users share
       their configured product. Of course, which social media platform and position of the
       icons can be setup in our BackOffice!
* Fixed: We have improved the way how our assets are loaded. This should improve the loading times on most pages.
Note: We have removed all JavaScript in templates and moved them to separate files. This means that odler overriden templates will create conflicts.
      Please update your template files or contact us for help.

= 2.3.4 - 2021/05/26 =
* Fixed: Add support for material groups in dynamic sku option.

= 2.3.3 - 2021/05/26 =
* New: Dynamic SKU Generation feature which allows to generate sku based on defined/selected attributes.
* Fixed: Update PDF library to fix style issues in PDF and provide new 'themes_folder_url' to point to active theme folder by url.
* Fixed: Remove certain php 7.1 features as plugin is set to 7.0

= 2.3.2 - 2021/05/17 =
* New: Add transformation controls (Offset/Rotation/Scale) to image-upload attribute.

= 2.3.1 - 2021/05/05 =
* New: New and improved toolbar UI
* New: Multi-products is now integrated into options to keep functionality and UI consist.
* New: Thumbnails will now appear next to slider.
* Fixed: Issue which caused too many price requests.
* Fixed: Update libs (bug fixes).
Note: The multi-product changes will not work if you have overridden the viewer/configurator.phtml template.
      Please update your template files or contact us to help you upgrade your template files.

= 2.3.0 - 2021/03/25 =
* New: Improve drag-and-drop support for multi-products.
Note: The drag-and-drop changes will not work if you have overridden the viewer/configurator.phtml template.
      Please update your template files or contact us to help you upgrade your template files.

= 2.2.55 - 2021/03/24 =
* Fixed: Touch scroll blocked.
* Fixed: Scroll to step

= 2.2.54 - 2021/01/22 =
* Fixed: Small issue where single font option is visible in texttoimage attributes

= 2.2.52 - 2021/01/22 =
* New: Add support for Specular-Glossiness material.
* Fixed: Small bugfix in the usage of blacklisted words.
* Fixed: Small bugfix in steps (options) not firing event for rules.

= 2.2.51 - 2021/01/04 =
* Fixed: Quotation's attribute visibility

= 2.2.5 - 2020/12/28 =
* Fixed: Update libs to include functionality for shadow camera near/far setting and social share feature.
* Fixed: Error showing up after activating the Expivi plugin in WordPress.
* New: Logging system is improved as it will now use our new file system.

= 2.2.4 - 2020/12/14 =
* Fixed: Small decoding issue for special characters.

= 2.2.3 - 2020/12/11 =
* Fixed: Avoid double-tap on certain options on iOS devices.
* Fixed: Small issue where loading screen didn't disappear when fully loaded.
* Fixed: Small issue in validation when spaces were used after comma-separated words in blacklisted words textfield.
* Feature: Added support for auto-resize for text-to-image attributes.
* Plugin tested up to WordPress 5.6

= 2.2.2 - 2020/11/23 =
* Fixed: Viewer will now show a loading screen when loading the configuration.
* Fixed: Swiping of horizontal tiles will now stop propagation to prevent swiping of tabs.

= 2.2.1 - 2020/11/10 =
* Hotfix: Fixed freeze when selecting product in product settings.

= 2.2.0 - 2020/11/05 =
* Hotfix: Fixed infinite loop bug in validation messages.

= 2.1.8 - 2020/11/05 =
* Introducing: Option in product setting to change text of add-to-cart button!
* Introducing: Option in product settings to change camera position for generating thumbnail!
* Introducing: Notification text of validation errors can now be changed.
* Introducing: Actions for banned words: Do nothing, replace with asterisks, remove word.
* Updated engine of viewer.
* Some validation improvements.

= 2.1.7 - 2020/10/30 =
* Introducing: Blacklist for text-input or text-to-image attributes!
* Bugfixes

= 2.1.6.1 - 2020/09/30 =
* Bugfixes

= 2.1.6 - 2020/09/30 =
* Introduce option to use price calculation based on language (WordPress) or country (WooCommerce).
* Partly support WCML by mapping the following currencies: USD => us, EUR => nl, GBP => gb, CAD => ca.
* Fixes issue in reconfigure button.

= 2.1.5 - 2020/09/25 =
* Bug fixes and add backwards compatibility to certain functions

= 2.1.4 - 2020/09/22 =
* Adding internal logging system.
* Renamed some variables/functions to prevent conflicts with other plugins.
* Adding default to API URL.
* Increase API call timeout.
* Bug fixes

= 2.1.3 - 2020/09/16 =
* Ability to add a 'reconfigure' link in the cart page below each Expivi product.
* Bumped default thumbnail size from 128x128 to 512x512. This is also adjusted in PDF.
* Fixed issue where thumbnail of Expivi product in cart does not hold identifier to saved configuration. This resulted in the product page showing default configuration.

= 2.1.1 - 2020/09/9 =
* Bug fixes

= 2.1.0 - 2020/06/28 =
* Initial release as WooCommerce Extension.
* Update core functions / entry point.
* Add several options like: show/hide price, show/hide price when zero, price position.
* Add PDF support (download configuration as PDF) + option to change orientation.
* Add ability to override expivi templates by using 'expivi' folder in theme.

= 2.0.2 =
* Fix - 3D viewer styling
* Update - Description

= 2.0.1 =
* Fix - Update shadow during auto-rotate
* Temp Fix - Make every expivi product unique in cart to avoid merging of same expivi products with different options

= 2.0.0 =
* Upgrade expivi options
* Upgrade expivi library
* Support localization
* Added new options to show/hide: hover icon, auto rotate, show progress, show options
* Options & sku's shown correctly in order & emails

= 1.6.08 =
* Feature currency respect number of decimals defined in WooCommerce

= 1.6.07 =
* Fix expivi library caching

= 1.6.06 =
* New feature: localization
* New feature: Custom API Price calculation
* Bug fixes

= 1.6.04 =
* Fix viewer aliasing
* Add support for initial & upload image components

= 1.6.03 =
* Fix question tiles

= 1.6.02 =
* Fix dragging products

= 1.6.01 =
* Fix option visibility
* Fix text to image

= 1.6 =
* Upgrade Expivi library

= 1.5.535 =
* Fix simple & configurable products coexist in cart

= 1.5.533 =
* Security improvements

= 1.5.532 =
* Hotfix vulnerability bug on uploading files

= 1.5.53 =
* Move cameras independently of views
* Move to views by rules
* Fix drag&drop bug

= 1.5.52 =
* Check for faulty results on API Calls

= 1.5.51 =
* bug fixes
* show info toolbar

= 1.5.3 =
* Updated components
* Should work with WP5.0
* Added image upload configuration
* Added price selector configuration
* Takes WooCommerce currency settings as setting for formatting currency

= 1.5 =
* Re-worked components

= 1.4 =
* Made order articles visible in WooCommerce order pages

= 1.2 =
* Updated readme

= 1.1 =
* Updated title and readme

= 1.0 =
* First released version

== Screenshots ==

1. With Expivi's 3d configurator your client can easily create, personalize and visualize their own products in 360.
2. The backend of Expivi is easy to use. No code is needed. You create the setups with the advanced drag & drop system
3. You can create configurable products via your normal woocommerce pages, all you have to do is to link Expivi to your existing Wordpress products so that you can give your customers a pre configured product or scene.

== Upgrade notice ==

= 2.0.0 =
* Upgrading to version 2.0 include breaking changes which may affect you website. We recommend to backup your website before updating to this version.

= 1.0 =
* First release, no notes
