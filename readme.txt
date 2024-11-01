=== Sponsor Redirect ===
Contributors: shahalom, microsolutions
Donate Link: http://microsolutionsbd.com/
Tags: sponsor redirect, affiliate url redirect, manage affiliate partners, manage affiliate links, manage sponsor links, show sponsor image, sponsor page
Requires at least: 3.6
Tested up to: 4.4.1
Stable tag: 0.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sponsor Redirect plugin helps you to manage url/links of your affiliate partners. You can also show some of your sponsor info including image anywhere on your site using the shortcode provided by this plugin.


== Description ==
Sponsor Redirect plugin helps you to manage your sponsors or affiliate link on your own site easily. Most of the time we get an affiliate link which is almost impossible to memorise and for which we have to share the affiliate links by copy and paste everytime. In that case we can create an url on your site that will redirect to the specific url. For example: if you try to browse the url ( http://microsolutionsbd.com/go/payoneer/ ), it will redirect you to Payoneer site with my affiliate id.

We have use Masonary library to looks good the grid of sponsor. If the masonary library already exist on your theme then please use the plugin settings page to unload the library from this plugin. If you are not interested to show your sponsors in any page then it will batter to disable loading masonary library using plugin settings section.

Please browse the page on our site to see this plugin in action: http://microsolutionsbd.com/sponsor/

Features of Sponsor Redirect plugin:
    - This plugin enables you to manage all your affiliate link centrally from your own site.
    - You can update link of any sponsor anytime without getting headache about where you have shared the url previously.
    - This plugin offer create post including featured image which enable you to create a page showing your special affiliate partners.
    - Number of clicks is printed on the manage page.

Currently [msbd-srp] offeres the following attributes:
    * type - default value is empty which will retrieve all type of affiliate partner except "Hide". Permitted values for this attribute are - 'premium', 'golden', 'silver', 'standard', or "all". If you like to retireve all type of sponsor link (including hide type) then you can use "all" for this attribute.
    * columns - you can specify the columns number to show the sponsors in grid. Permitted values for this attribute are: 2,3, or 4
    * limit - you can limit the number of sponsor to show using the shortcode among a lot of sponsors. Default is 0 (unlimited/all)
    * sorting - permitted value for this attribute are "ASC" or "DESC". Default is DESC
    * wrap_class - if you like to add any css class to the container of the grid to style yourself, you can write the css class using this attribute. Default is empty.
    * thumbnail - By default this plugin use the default thumbnail image of wordpress. If you know what type of thumbnail sizes are stored on your site and want to use different type of thumbnail then this option will be useful for you. Write the thumbnail size here. By default the value is "thumbnail"

Examples of Shortcode [msbd-srp]:

    Following two short code will output same:
    [msbd-srp]
    and
    [msbd-srp type="" columns="3" wrap_class="" limit="0" thumbnail="thumbnail" sorting="DESC"]

    Following two short code will output only golden type of sponsors:
    [msbd-srp type="golden"]
    and
    [msbd-srp type="golden" columns="3" wrap_class="" limit="0" thumbnail="thumbnail" sorting="DESC"]



== Installation ==
1. You can download and install the Sponsor Redirect plugin through the built-in Wordpress plugin installer. Alternately, download the zip file then extract and upload the '/sponsor-redirect/' folder to your '../wp-content/plugins/' folder.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Cheers! Now you have a menu item "Sponsors" of this plugin to the admin panel :)


== Screenshots ==
1. A page to show your affiliate partners post in a page.
2. Shortcode used in the page editor.
3. Admin settings page.


== Changelog ==

= 0.0.5 =
* Initial beta release



== Upgrade Notice ==

