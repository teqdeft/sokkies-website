=== Tagging ===
Contributors: jeroenfrenken
Donate link: https://trytagging.com
Tags: GTM, Google Tag Manager, Server side, Cookies
Requires at least: 4.9
Tested up to: 6.7
Requires PHP: 7.1
Stable tag: 1.1.19
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

AdPage Tagging is a simple to use solution for setting up Google Tag Manager Server Side.

== Description ==

AdPage Tagging is a simple to use solution for setting up Google Tag Manager Server Side.

Benefits of tagging:
* Increase cookie lifetime (also on Safari 16.4).
* First party tracking with custom domains.
* Get more control over your data sent to GTM 

External depencies:
AdPage Tagging is using external depencies for providing the service. These files are all loaded through the domain you configure inside the plugin and Tagging dashboard.
The files are used to provide our service inside of Wordpress.

We are using the following external depencies:
* /settings.js this file is used for detecting adblockers and for loading the GTM container.
* /adex.js this file is used for detecting adblockers and for loading the GTM container.
* /script.js this file is a proxy of the GTM web container script. This is used for loading the GTM container (Proxy to GTM).
* /order_created this is a endpoint for sending order data to the GTM container (webhooks)

All scripts arebove are loaded from the domain you configure. When entering a domain that is used inside trytagging.com your requests will be proxied through our servers to Google Tag Manager.
See below the terms of service for the external depencies.

Links to the services used:
AdPage Tagging: https://trytagging.com
Google Tag Manager: https://marketingplatform.google.com/about/tag-manager/

Terms of service:
AdPage Tagging terms of service: https://www.adpage.io/general-terms-and-conditions-of-service
Google Tag Manager terms of service: https://www.google.com/analytics/terms/tag-manager/

for any questions regarding the loading of external depencies please contact us at support@adpage.io

This plugin is not affiliated with Google Tag Manager or Google.

== Installation ==

1. Install the AdPage Tagging plugin in the wordpress store
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 2.3.7 =
* Added "Prevent duplicate script injection" advanced setting, which stops the GTM loader from being injected multiple times on themes exposing more than one of the supported injection hooks

= 2.3.6 =
* Fixed bug not sending remove_from_cart when no items are available on page

= 2.3.5 =
* Added option to set the cookie keeper for front end only
* Updated the plugin UI

= 2.3.4 =
* Updated add_to_cart events for iEar

= 2.3.3 =
* Genrate lead send when document is ready

= 2.3.2 =
* Ensure gravity form passwords are not pushed to the datalayer / webhooks

= 2.3.1 =
* Added Disable cookie keeper / marketing object cookies setting

= 2.3.0 =
* Added item_sku to the dataLayer

= 2.2.0 =
* Check if items.eccomerce is available

= 2.1.9 =
* Wait for user_data script before sending events

= 2.1.8 =
* Changed item_variant structure

= 2.1.7 =
* Improved view_item_list post fetching

= 2.1.6 =
* Added a header_sent check when setting cookies

= 2.1.5 =
* Bug fix with wordpress cart

= 2.1.4 =
* Added old purchase flow back with an new setting that toggles it.

= 2.1.3 =
* Bug fixes with webhook sending

= 2.1.2 =
* Bug fix with remove_from_cart event
* Bug fix with checkout related events

= 2.1.1 =
* Added support for form_id data attribute for lead generation event

= 2.1.0 =
* Changing the way the purchase event is sent to the GTM container

= 2.0.10 =
* Bug fix with remove_from_cart event

= 2.0.9 =
* Set default quantity to 1

= 2.0.8 =
* Bug fixes with gravity froms generate_lead event
* Bug fixes with add_to_cart event

= 2.0.7 =
* Added better support for remove_from_cart event

= 2.0.6 =
* Added some old user_data attributes

= 2.0.5 =
* Bug fix with Gravity Forms submission

= 2.0.4 =
* Made plugin compatiable with PHP 7.1
* Updated requirements of plugin

= 2.0.3 =
* Bug fix with add_to_cart event on the category pages

= 2.0.2 =
* Bug fix with add_to_cart event

= 2.0.1 =
* Added option to place GTM scripts your self

= 2.0.0 =
* New dataLayer structure
* Fixed a problem with the GTM container not loading on some pages
* Bug fixes with add_to_cart event on some stores

= 1.1.21 =
* Added add_payment_info and add_shipping_info events
* Gravity Forms dataLayer event bug fix where the event was send before validation
* A fix for view_item_list event in some stores
* A fix for add_to_cart event in some stores

= 1.1.20 =
* Bug fix with Gravity Forms dataLayer event

= 1.1.19 =
* Added webhook support for Gravity Forms and contact form 7

= 1.1.18 =
* Added support for Gravity Forms and contact form 7

= 1.1.17 =
* Bug fix for shops which use a different hook for create order

= 1.1.16 =
* Bug fixes add_to_cart event

= 1.1.15 =
* Performance update for the view_item_list event
* Make sure _taggingmk cookie is set when using caching plugins
* Removed unused body scripts

= 1.1.14 =
* Added debug option
* Added log download and clear options

= 1.1.13 =
* WPML support
* Added support for new script urls

= 1.1.12 =
* Fixed a bug with add_to_cart currency and value swapped

= 1.1.11 =
* Updating price calculation of products to include tax

= 1.1.10 =
* Fixed a bug with add_to_cart event
* Fixed a bug with caching plugins (root domain for scripts was different)
* Fixed a bug with add_to_cart jQuery not defined
* Tested on wordpress 6.6

= 1.1.9 =
* Do not send purchase for checkout-draft
* Added order_status to webhook

= 1.1.8 =
* Push user_data event for every page

= 1.1.7 =
* Removed updating cart session
* Changed marketing object

= 1.1.6 =
* Added /order_refunded webhook

= 1.1.5 =
* Changed item_name to title
* Added meta tag with version

= 1.1.4 =
* Add the permission_callback option

= 1.1.3 =
* Add host to webhook payload

= 1.1.2 =
* Fixed issue with custom marketing object

= 1.1.1 =
* Fixed issue with add to cart

= 1.1.0 =
* Added new datalayer
* Added order notes for order_created webhook

= 1.0.0 =
* Initial release

== Frequently Asked Questions ==

= Why do you use external depencies =

We are using external depencies for the following reasons:
* Detecting adblockers
* Loading the GTM container
* Sending order data to the GTM container

More infomration about the external depencies can be found in the description.

= How do I configure the plugin =

After installing the plugin you can configure the plugin by going to the settings page of the plugin.
You can find the settings page by going to the settings menu in the wordpress admin panel and then clicking on Tagging.
There you can configure the values as found in the Tagging dashboard.

== Upgrade Notice ==

= 1.1.12 =
No upgrade notice

= 1.1.11 =
No upgrade notice

= 1.1.10 =
No upgrade notice

= 1.1.9 =
No upgrade notice

= 1.1.8 =
No upgrade notice

= 1.1.7 =
No upgrade notice

= 1.1.6 =
No upgrade notice

= 1.1.5 =
No upgrade notice

= 1.1.4 =
No upgrade notice

= 1.1.3 =
No upgrade notice

= 1.1.2 =
No upgrade notice

= 1.1.1 =
No upgrade notice

= 1.1.0 =
No upgrade notice

= 1.0.0 =
First release of the plugin

== Screenshots ==

1. Plugin options page in admin page