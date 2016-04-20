=== WP QUADS - Quick AdSense Reloaded === 

Author URL: https://profiles.wordpress.org/renehermi/
Plugin URL: https://wordpress.org/plugins/quick-adsense-reloaded/
Contributors: ReneHermi, WP-Staging, toddynho
Donate link: 
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: advertising, ads, adsense, google, google adsense, posts, post, page, sidebar, shortcode, admin, plugin 
Requires at least: 3.6+
Tested up to: 4.5
Stable tag: 1.1.2

Quick Adsense Reloaded! The quickest way to insert Google AdSense and other ads code into your website.

== Description == 

> #### WP QUADS - Quick AdSense Reloaded 
> This free Google AdSense plugin is a fork of the discontinued AdSense ads plugin [Quick AdSense](https://wordpress.org/plugins/quick-adsense/) <br /> 
> Its rewritten from scratch with a solid code base and will be maintained and updated to be compatible with all future WordPress versions<br />
> This plugin is brandnew, well tested but still work in progress! <br>
> Find a bug? Open a ticket in the [support request](https://wordpress.org/support/plugin/quick-adsense-reloaded/ "support forum"). Issue will be fixed asap!
> 
> <strong>NEW: </strong>
> Create custom ad positions in your template files with quads_register_ad();
> Check out the sample code and complete API in <em>includes/api.php</em> <br /> 

= Why a Quick AdSense fork? =

Quick Adsense is a great plugin and used by more than 100.000 websites. 
Although is not under maintainance and development for more than 3 years by the original author it is still downloaded hundred times a day.
So i decided to continue the project to make sure the plugin will also work in future with new WordPress versions.

Deprecated functions removed, bugs fixed and new filters and hooks created to make this plugin extensible by third party developers!


= Main Features =

* Import all ads settings from Quick AdSense v. 1.9.2 and convert them into serialized options.
* No revenue sharing from your Google AdSense advertising income.
* Quicktags of Quick Adsense are 100% compatible to Quick AdSense Reloaded
* No external script dependencies. All plugin code reside on your site. 
* Dynamic AdSense positioning: Assign Google AdSense ads to the beginning, middle and end of post, assign dds after 'more' tag, before last paragraph, after certain paragraphs & assign Ads after certain images.
* Insert Google AdSense ads specifically or randomly anywhere within a post.
* Support any Ads code, not limited to Google Adsense dds only.
* Display up to a maximum of 10 Ads on a page. Google TOS allows publishers to place up to 3 Google Adsense for Content on a page. If you are using other dds, you may display up to 10 Ads.
* Support up to a maximum of 10 Ads codes on Sidebar Widgets.
* Support up to a maximum of 10 Ads codes for specific placement & randomization within a post.
* Insert Google AdSense ads on-the-fly, insert &lt;!--Ads1--&gt;, &lt;!--Ads2--&gt; ... , &lt;!--RndAds--&gt; to a post to accomplish this.
* Disable Ads on-the-fly, insert &lt;!--NoAds--&gt;, &lt;!--OffDef--&gt;, &lt;!--OffWidget--&gt;, &lt;!--OffBegin--&gt; ... and more to a post to accomplish this.
* The above quicktags can be inserted into a post easily via the additional Quicktag Buttons added to the HTML Edit Post SubPanel.

= Improvements to original Quick AdSense Ads plugin =

* Performance improvements
* Serialized storing of Ad options instead storing every single option as separate table entry all over
* Multi language support
* Remove of small coding issues like "unexpected output" message when plugin is activated on several sites
* Import / Export function makes plugin migrating to other sites easier. Copy your dds code to other sites.

= Safety improvements = 

* Exit code if Quick AdSense plugin is not called by WordPress directly 
* Better sanitizing of user input


@todo:

* Support for responsive Google AdSense ads
* GUI improvements

Do you have suggestions for more features?

= High Performance =

Quick AdSense Reloaded is *coded well and developed for high performance*.
It loads only the code it needs at the moment of execution, making it small and fast and with a lot of hooks easy extensible by third party developers.

** GitHub **
Follow the development and improve the plugin.
You find it on [GitHub](https://github.com/rene-hermenau/quick-adsense-reloaded/)


== Frequently Asked Questions ==

Post your question in the [support forum](https://wordpress.org/support/plugin/quick-adsense-reloaded)

== Installation ==

Recommended Installation:

1. Go to YourWebsite->Plugins->Add New
2. Search for "Quick Adsense reloaded"
3. Click "install Now"

Alternative Installation: 

1. [Download the plugin](https://downloads.wordpress.org/plugin/quick-adsense-reloaded.latest-stable.zip) , unzip and place it in your wp-content/plugins/ folder.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==

1. The Quick AdSense Settings page
2. Import / Export tab: Import your Quick AdSense settings to Quick AdSense reloaded
3. The Quick AdSense Reloaded help tab
4. Create Quick AdSense Widgets
5. AdSense widgets available from the widgets admin page
6. Copy and paste the Google AdSense code
7. Quicktag Buttons added to the post editor.


== Changelog == 

= 1.1.2 =
* New: Specify plugin load priority

= 1.1.1 =
* Fix: fopen error message on import settings page when Quick AdSense ist not installed and inactive

= 1.1.0 =
* Fix: Rating container not always hiding after rating the plugin

= 1.0.9 =
* Tweak: Disable all AdSense ads on 404 pages (Google AdSense does not allow this.)
* New: A nice looking rating div for asking to rate this plugin. Can be deactivated with one click and will never appear again.

= 1.0.8 =
* Tweak: Default alignment is center 

= 1.0.7 =
* Fix: Change shortcode description to echo do_shortcode('[quads id="4"]');

= 1.0.6 =
* Fix: Url to widget section leads to 127.0.0.1

= 1.0.5 =
* Fix: Check if Quick AdSense is installed is throwing error message when it is not installed.
* New: Show a <!--NoAds--> Quicktag button in the html editor. Necessary to hide ads on not defined post_types like woocommerce product pages
* Tweak: Tested up to WP 4.5
* Tweak: Change author name to Rene Hermenau

= 1.0.4 =
* Tweak: Clean up code and remove admin-notices.php
* Tweak: Add new class quads-locations for ads wrapper in content

= 1.0.3 =
* Fix: php 5.2 does not support anonymous function
* Tweak: Lower amount of tags in readme.txt

= 1.0.2 =
* Fix: undefined var notice if plugin is installed first time
* Fix: Hide AdsWidget option on front page is ignored

= 1.0.1 =
* Fix: Undefined variable notice if plugin is activated first time

= 1.0.0 = 
* Tweak: Remove is_main_query() check


= 0.9.9 =
* New: API for easy integration of custom ad positions. See /includes/api.php for how to use custom ads positions in your theme
       (Custom ad positions are calculated within max allowed ads setting)

= 0.9.8 =
* Fix: Still not fixed on all systems: Invalid argument supplied for foreach()
* Fix: Undefined index in widgets.php

= 0.9.7 =
* Fix: Invalid argument supplied for foreach()
* Fix: undefined var cusrnd

= 0.9.6 =
* New: Use new Meta Box on post and pages for disabling ads instead using quicktags
* New: Remove quicktags for disabling ads from editor (backward compatible)

= 0.9.5 =
* New: Rebirth - Change name to WP QUADS
* New: Modify official shortcodes to [quads]. Old shortcodes [quads_ad] are still supported

= 0.9.4 =
* New: Allow the use of shortcodes to integrate google adsense ads, e.g. [quad_ad id="1"] or echo do_shortcode('[[quad_ad id="1"]]'); in template files. Max ad setting will be used for shortcode embeded ads as well.
* Tweak: Cleaner GUI
* Tweak: Clean up code and remove deprecated ads code
* Fix: Stored setting for ad position 9 was not shown properly in option field.
* Fix: Quicktags not shown

= 0.9.3 =
* Fix: Alignment is ignored

= 0.9.2 =
* Fix: AdSense Widgets shown although setting "Hide Ads when user is logged in" is enabled
* Fix: Empty quads.min.js loaded on frontend
* Fix: AdSense Widgets not created correctly
* Fix: Google AdSense max ads value ignored for widgets
* Tweak: Removing of create_function() due to security reasons ( Dont be evil() )


= 0.9.1 =
* Hooray! Quick Adsense Reloaded is alive

== Upgrade Notice ==

= 1.0.3 =
1.0.3  <a href="https://wordpress.org/plugins/quick-adsense-reloaded/changelog/" style="color:white;text-decoration: underline;">Complete changelog! </a> It's recommended to plugin updates on a staging website first before installing them on a live site. (Use <a href="https://wordpress.org/plugins/wp-staging/" target="_blank" style="color:white;text-decoration: underline;">WP Staging</a> for creating a test site or any other prefered staging tool)
