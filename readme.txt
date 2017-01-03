=== AdSense Plugin WP QUADS === 

Author URL: https://profiles.wordpress.org/renehermi/
Plugin URL: https://wordpress.org/plugins/quick-adsense-reloaded/
Contributors: ReneHermi, WP-Staging
Donate link: 
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: adsense, ads, ad, google adsense, advertising, amp, ad injection, ad inserter, ad manager
Requires at least: 3.6+
Tested up to: 4.7
Stable tag: {{ version }}

Quick Adsense Reloaded! Quickest way to insert Google AdSense & other ads into your website. Google AdSense integration with Google AMP support

== Description == 

> #### WPQUADS - Quick AdSense Reloaded 
> This free Google AdSense plugin is a fork of the discontinued Quick AdSense ads 
> plugin with more than 100k installations. Its rewritten from scratch with a solid code
> base and will be maintained and updated to be compatible with all future WordPress versions<br />
> Found a issue? Open a ticket in the [support forum](https://wordpress.org/support/plugin/quick-adsense-reloaded/ "support forum").
> 
> Support of the popular Click Fraud Monitor plugin <br /> 
> Visit the site: [clickfraud-monitoring.com](http://demo.clickfraud-monitoring.com/) <br /> 
>
> <strong>NEW: AMP SUPPORT WITH WP QUADS PRO </strong><br>
> - AMP support! Add AMP ads automatically to your site<br /> 
> - Use mobile optimized and responsive AdSense ads<br /> 
> - Disable AdSense ads on phone, tablet or desktop devices<br /> 
> - Define AdSense size for different devices<br />
>
> AMP feature requires [Automattic AMP plugin](http://wpquads.com/?utm_source=wp_org&utm_medium=plugin_page&utm_term=check_out_wp_quads&utm_campaign=wpquads) installed <br />
> Get WP QUADS PRO: [wpquads.com](http://wpquads.com/?utm_source=wp_org&utm_medium=plugin_page&utm_term=check_out_wp_quads&utm_campaign=wpquads) <br /> 

= Why a Quick AdSense fork? =

Quick Adsense is a great plugin and used by more than 100.000 websites. 
Although is not under maintainance and development for more than 3 years by the original author it is still downloaded hundred times a day.
So i decided to continue the project to make sure the plugin will also work in future with new WordPress versions.

Deprecated functions removed, bugs fixed and new filters and hooks created to make this plugin extensible by third party developers!

<strong>We Guarantee: </strong><br>
No revenue sharing from your Google AdSense advertising income. We never show our ads on your website.<br>
We are an active and engaged member of the WordPress community and we are following strongly the WordPress Codex in terms of code quality and good behave.

= Main Features =

* Import all ads settings from Quick AdSense v. 1.9.2 and convert them into serialized options.
* Visibility conditions, show / hide ads based on post type and user roles (needs WP QUADS PRO)
* Quicktags of Quick Adsense are 100% compatible to Quick AdSense Reloaded
* No external script dependencies. All plugin code reside on your site. 
* Dynamic AdSense positioning: Assign Google AdSense ads to the beginning, middle and end of post, assign ads after 'more' tag, before last paragraph, after certain paragraphs & assign Ads after certain images.
* Insert Google AdSense ads specifically or randomly anywhere within a post.
* Support any Ads code, not limited to Google Adsense ads only.
* Display up to a maximum of 10 Ads on a page. Google TOS allows publishers to place up to 3 Google Adsense for Content on a page. If you are using other ads, you may display up to 10 Ads.
* Support up to a maximum of 10 Ads codes on Sidebar Widgets.
* Support up to a maximum of 10 Ads codes for specific placement & randomization within a post.
* Insert Google AdSense ads on-the-fly, insert &lt;!--Ads1--&gt;, &lt;!--Ads2--&gt; ... , &lt;!--RndAds--&gt; to a post to accomplish this.
* Disable Ads on-the-fly, insert &lt;!--NoAds--&gt;, &lt;!--OffDef--&gt;, &lt;!--OffWidget--&gt;, &lt;!--OffBegin--&gt; ... and more to a post to accomplish this.
* The above quicktags can be inserted into a post easily via the additional Quicktag Buttons added to the HTML Edit Post SubPanel.
* Use shortcodes within ads (Suppport advertisements from other ad plugins for example Simple Ads Manager or AdRotate)

= Improvements to original Quick AdSense Ads plugin =

* Performance improvements
* Serialized storing of Ad options instead storing every single option as separate table entry all over
* Multi language support
* Remove of small coding issues like "unexpected output" message when plugin is activated on several sites
* Import / Export function makes plugin migrating to other sites easier. Copy your ads code to other sites.

= Safety improvements = 

* Exit code if Quick AdSense plugin is not called by WordPress directly 
* Better sanitizing of user input

= We Distance Ourself From =
These are known AdSense plugins which are removed from the WordPress repository because of non ethic behavior:

* AdSense Extreme
* AdSense Insert 

Make sure to switch to WP QUADS or any other AdSense plugin if you are still using these plugins!

<h3>WP QUADS PRO:</h3>

* Support for responsive Google AdSense ads
* GUI improvements

Check out WP QUADS PRO: [wpquads.com](http://wpquads.com/?utm_source=wp_org&utm_medium=plugin_page&utm_term=check_out_wp_quads&utm_campaign=wpquads) <br /> 

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
2. Search for "Quick Adsense reloaded" or "WP QUADS"
3. Click "install Now"

Alternative Installation: 

1. [Download the plugin](https://downloads.wordpress.org/plugin/quick-adsense-reloaded.latest-stable.zip) , unzip and place it in your wp-content/plugins/ folder.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==

1. The Quick AdSense Settings page
2. General Settings
3. AdSense Widgets
4. AdSense Options from the post editor


== Changelog == 

= {{ version }} =
* New: Add three more paragraph options with WP QUADS PRO
* New: New filter to show ads above or below specific header tags like h1 or h2 (needs WP QUADS PRO)
* Tweak: Rename toggle button to "Open All Ads"

= 1.3.9 =
* Fix: Redirect after first time installation not working
* New: Support for tag visibility condition with WP QUADS 1.3.1
* New: Add a "Rate Later" option to the rating container

= 1.3.8 =
* Fix: Remove "three dots" content on unused adverts spots
* Fix: Even empty ads are taken into account for random ads
* Fix: Visibility conditionals for widget ads are not used
* Fix: Old align settings are not imported from old plugin Quick AdSense

* New: Show/Hide ads on custom post types with WP QUADS PRO
* New: Hide adverts for specific custom user role with WP QUADS PRO

* Tweak: Do not show menu link to WP QUADS PRO plugin if its already installed

= 1.3.7 =
* Fix: Do not show ads on 404 error pages
* New: Allow use of other amp vendor codes with use of wp quads pro

= 1.3.6 =
* Fix: Do not show ads on search pages
* Fix: Change ad condition for Tablet viewport to 1024px
* New: Allow recursive use of shortcodes in ad codes

= 1.3.5 =
* New: Support for multiple margin values: top, right, bottom, left (Needs WP QUADS PRO min. version 1.2.7)
* Fix: Undefined adalign notice

= 1.3.4 =
* Fix: Meta Box Settings "Hide ads" are ignored. Error caused by code changes in 1.3.2

= 1.3.3 =
* Skip version

= 1.3.2 =
* New: Button for opening all ads for easier editing
* Fix: Plain Text / HTML as default mode
* Fix: Invalid argument supplied for foreach() message when using custom shortcodes in ad content

= 1.3.1 =
* Tweak: Cleaner graphical admin tabs

= 1.3.0 =
* Fix: Make sure existing adsense code is not changed after updating
* Fix: Missing quads-ad class in custom Ad spots
* Fix: Hide on (mobile, desktop, tablet) device rules not working for custom Ad spots
* Fix: WP QUADS PRO can not be detected if folder name is not default wp-content/plugins/wp-quads-pro
* Fix: Can not export settings without reloading page
* Tweak: Change description of load order

= 1.2.9 =
* New: AMP support with WP QUADS PRO and Automattic AMP plugin

= 1.2.8 =
* Fix: Can not parse responsive AdSense async code when its custom modified
* Fix: Use custom AdSense ads with modified css rules
* Fix: Do not change adsense default format

= 1.2.7 =
* New: Ignore Cloudflare Rocket Script Loader for AdSense generated code
* New: Settings are saved without page reload
* New: Basic Responsive Support for Ads including widget ads
* New: No more script security errors when saving settings
* New: Improved Graphical User Interface
* New: Another class name for ad container
* Tweak: Remove deprecated debug code
* Tweak: Better description what to do after creating widget adsense code
* Fix: Adsense ad label 'Advertisment' not shown
* Fix: Remove ad container completely when it is deactivated via WP QUADS PRO

= 1.2.6 =
* Fix: WP auto P tags breaks inline javascript
* New: Support for Custom Banner Sizes and AdSense Label in WP QUADS PRO
* New: Banner for click fraud monitor plugin in admin settings
* Tweak: UI improvements in admin settings

= 1.2.5 =
* New: New cleaner design for adsense admin dashboard

= 1.2.4 =
* Fix: Error warning when plugin is activated

= 1.2.3 =
* Fix: AdSense Custom Theme API integration is ignoring the new responsive ads when WP QUADS PRO plugin is installed
* New: Ad Background color white per default
* Tweak: clean up code

= 1.2.2 =
* Fix: Some AdSense setting are not stored

= 1.2.1 =
* Fix: Fatal Error on frontpage

= 1.2.0 =
* New: Create AdSense pro version with mobile support
* New: AdSense WP QUADS Pro Version with responsive adsense support

= 1.1.9 =
* Fix: Change rating urls

= 1.1.8 =
* New: Click Fraud Monitor integration

= 1.1.7 =
* New: Tested up to WP 4.6.0
* New: Allow shortcodes in adsense input fields
* New: Add css class quads-id1-10 for ad container

= 1.1.6 =
* Tweak: Plugin Title
* Fix: Show ads only when query is main query

= 1.1.5 =
* Fix: Max ad count sometimes not working as expected and ads are not shown and max count is not reached, though
* Fix: Link to widget section not working

= 1.1.4 =
* Fix: Undefined var $showall

= 1.1.3 =
* Fix: Import Screen is showing No such file or directory error notice
* New: Tested up to WP 4.5.2

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

= 1.3.1 =
1.3.1 Smaller, Faster, Quicker, Better - WP QUADS<a href="https://wordpress.org/plugins/quick-adsense-reloaded/changelog/" style="color:white;text-decoration: underline;">Complete changelog! </a>
