=== AdSense Plugin WP QUADS === 

Author URL: https://profiles.wordpress.org/renehermi/
Plugin URL: https://wpquads.com
Contributors: ReneHermi, WP-Staging
Donate link: https://wpquads.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: adsense, ads, ad, google adsense, advertising, amp, ad injection, ad inserter, ad manager
Requires at least: 3.6+
Tested up to: 5.1
Requires PHP: 5.3
Stable tag: {{ version }} 

Quick Adsense Reloaded! Quickest way to insert Google AdSense & other ads into your website. Google AdSense integration with Google AMP support

== Description == 

#### WPQUADS - Quick AdSense Reloaded 
This free Google AdSense advanced inserting plugin is an improvement of the successfull but discontinued plugin Quick AdSense which is used on more than 100.000 websites.
WP QUADS is coded well with no overhead and is used on huge websites with millions of monthly page impressions.
<br />
[See all features](https://wpquads.com/)
<br /> 
* AMP support! Add AMP ads automatically to your site (WP QUADS PRO needed)<br /> 
* Use mobile optimized and responsive AdSense ads<br /> 
* Disable AdSense ads on phone, tablet or desktop devices<br /> 
* Define AdSense sizes for different devices<br />

AMP feature requires [Automattic AMP plugin](http://wpquads.com/?utm_source=wp_org&utm_medium=plugin_page&utm_term=check_out_wp_quads&utm_campaign=wpquads) or any other AMP plugin installed <br />
Get WP QUADS PRO: [wpquads.com](http://wpquads.com/?utm_source=wp_org&utm_medium=plugin_page&utm_term=check_out_wp_quads&utm_campaign=wpquads) <br /> 

This AdSense plugin is rewritten from scratch with a solid code
base and will be maintained and updated to be compatible with all future WordPress versions<br />
Found a issue? Open a ticket in the [support forum](https://wordpress.org/support/plugin/quick-adsense-reloaded/ "support forum").

= Why a Quick AdSense fork? =

Quick Adsense is a great plugin and used by more than 100.000 websites. 
Although is not under maintainance and development for more than 3 years by the original author it is still downloaded hundred times a day.
So i decided to continue the project to make sure the plugin will also work in future with new WordPress versions.

Deprecated functions removed, bugs fixed and new filters and hooks created to make this plugin extensible by third party developers!

<strong>We Guarantee: </strong><br>
This plugin does not do any revenue sharing with your Google AdSense ads. We never show our on ads ads on your website.<br>
We are an active and dedicated member of the WordPress community and we are following strictly the WordPress Codex in terms of code quality and good behave.

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

= 1.8.4 =
* Fix: Fatal error if Gutenberg is disabled

= 1.8.3 =
* New: WordPress 5.0 Gutenberg support
* New: WordPress 5.1 support
* New: WordPress 5.1.1 support
* Fix: Undefined offset in wpquads\adsense


= 1.8.2 =
* Fix: Gutenberg breaks traditional admin notices so we remove our admin notices from the edit screen

= 1.8.1 =
* New: Allow to show widget ads on homepage but disable all content ads
* Fix: Widget ads are not counted correctly if the settings were saved incomplete

= 1.8.0 =
* Fix: If post has more than 10 blockquotes the resulting blockquote order will be wrong

= 1.7.9 =
* New: Add up to 11 paragraph ads with WP QUADS PRO
* New: Fix tag condition in wp quads pro
* New: Support for Google Auto Ads with WP QUADS PRO
* Tweak: Rename ad tabs

= 1.7.8 =
* Fix: License notice shown even though license is not expired
* Fix: Ads are injected into blog loop because of forgotten development code

= 1.7.7 =
* New: Option to prevent multiple injection of https://cdn.ampproject.org/v0/amp-ad-0.1.js into AMP pages
* Fix: Revert load priority to 20
* Fix: Change vi default background and text color
* Fix: vi password field too large - css issue
* Fix: Error in_array() if post_type condition is empty
* Fix: If last counted paragraph is empty, ad is injected in wrong position
* Fix: Ads are injected into blockquote elements
* Fix: Do not show vi notice on all admin pages. If ad blocker is enabled it can not be closed
* Fix: Do not update vi ad code when vi api returns null
* Tweak: Show error message when vi ad can not be created
* Tweak: Show notice if WP QUADS Pro license has been expired but make sure that the pro plugin does not stop working
* Tweak: Better ad blocker notice

Complete changelog: https://wpquads.com/changelog

== Upgrade Notice ==

= 1.8.3 =
1.8.3 Gutenberg Support
