=== Affiliate Press ===
Contributors: ldebrouwer
Donate link: http://luc.me/40
Tags: affiliate, affiliates, product, products, product feed, product feeds, feed, feeds
Requires at least: 3.3
Tested up to: 3.4
Stable tag: 0.3.8

Affiliate Press allows you to set up an affiliate website based on product feeds as easy as 1-2-3.

== Description ==

Affiliate Press is a plugin that allows you to create products ( as a custom post type ) based on product feeds. It also collects other data for these products, such as prices and affiliate links, which are shown on the product pages in the front end, effectively allowing you to set up your own affiliate website as easy as 1-2-3.

Affiliate Press was specifically developed to be compatible with multiple affiliate programs and networks, aiming to support a broader range of feeds and therefor more competitive prices for your visitor.

Currently I'm looking for people who are willing to send me examples of their product feeds so I can improve this plugin further. Please send your XML product feeds as attachment to [affiliatepress@lucdebrouwer.nl](mailto:affiliatepress@lucdebrouwer.nl).

Follow Luc De Brouwer on [Facebook](https://www.facebook.com/lucdebrouwernl) & [Twitter](http://twitter.com/ldebrouwer).

== Installation ==

1. Upload the `affiliate-press` folder to the `/wp-content/plugins/` directory
1. Activate the Affiliate Press plugin through the 'Plugins' menu in WordPress
1. Configure the plugin by going to the `Affiliate Press` menu that appears in your admin menu

== Frequently Asked Questions ==

= How do I display the prices and links on a product page? =
You can do this by copying the following PHP code in your product template.
<pre>
	if( class_exists( 'LDB_Affiliate_Press' ) ) {
		$ap = new LDB_Affiliate_Press;
		echo $ap->AP_getPrices( true );
	}
</pre>

To load the products into a page template you can use this code.
<pre>
	$posts = get_posts( array( 'post_type' => 'product' ) );
	foreach( $posts as $post ) {
		setup_postdata( $post );
		the_title();
		the_post_thumbnail();
		the_content();
		if( class_exists( 'LDB_Affiliate_Press' ) ) {
			$ap = new LDB_Affiliate_Press;
			echo $ap->AP_getPrices( true );
		}
	}
</pre>

If you'd rather retrieve just the data and not the pre-formatted table you can use the following code.

<pre>
	if( class_exists( 'LDB_Affiliate_Press' ) ) {
		$ap = new LDB_Affiliate_Press;
		$data = $ap->AP_getPrices();
	}
</pre>

The affiliate data is now stored in the $data variable.

= Why am I not seeing the products on a category page? =
By default custom post types are not shown on a category page. To achieve this you'll have to add something along the lines of the code below to your themes' function.php.

<pre>
add_filter( 'pre_get_posts', 'my_get_posts' );

function my_get_posts( $query ) {

	if ( is_category() && $query->is_main_query() )
		$query->set( 'post_type', array( 'post', 'product' ) );

	return $query;

}
</pre>

= Why aren't the images from the feed being attached to the post? =
The permissions on you 'uploads' folder in 'wp-content' are most likely not set correctly.

= Where's the help section? =
This plugin is still under development and in beta. Most of the functionality is still subject to change and I hope to bring some more features to the plugin. Compiling an extensive help and support section would therefor be somewhat of a hassle at the moment. I strongly advise you to post in the [support forums](http://wordpress.org/tags/affiliate-press) if you have any questions. ( And please check there first if your question wasn't answered yet. )

== Screenshots ==

== Changelog ==

= 0.3.8 =
* Added ways to contact me to the sidebar.
* Expanded the FAQ.

= 0.3.7 =
* Added support for categories and tags to the product post type.
* Updated the FAQ.

= 0.3.6 =
* Added contextual help to all the pages of the plugin. I'll try to expand these in the near future.
* Removed a dependency on the $_SERVER['HTTP_REFERER'] variable.
* Confirmed that the wizard works perfectly with TradeDoubler product feeds.

= 0.3.5 =
* Confirmed that the wizard also works with tradetracker.net product feeds.
* Made some changes to the way how the dashboard statistics are calculated.
* Added the currency to the price table for the front-end.
* Fixed some typos.

= 0.3.4 =
* Added a first version of the dashboard.

= 0.3.3 =
* Added the 'Add New Feed Wizard'. It's still rudimentary and feedback would be highly appreciated. Should work like a charm with Daisycon feeds.

= 0.3.2 =
* Minor CSS change.
* Alerts/Messages are now dismissible.

= 0.3.1 =
* Added support for cron jobs. They will process all prices every hour for now. Later I'll make this manageable through a settings page.
* Fixed a small bug where performing a bulk actions redirected you to the wrong page.
* Tweaked the styling on the messages/warnings a little.
* Made the icons stand out more.
* The title tags for the 'hidden pages' are now also rendered correctly.
* Switched from POST to GET methods on the feed and item index pages to allow keyboard entry of page numbers for the pagination.
* Added support to return you to the right index page if you've been using sortable columns.

= 0.3 =
* Rewrite of a substantial part of the code.
* You're now able to link a feed item to an existing product.
* The number of prices linked to a product is now shown on the products index page.

= 0.2.1 =

* Fixed some typos.
* Added a pointer to help adding a feed.

= 0.2 =

* Second beta release.
* Added support for automatic image import from the feed when creating drafts.

= 0.1 =

* Initial beta release.

== Upgrade Notice ==

Please use WordPress' automatic plugin update functionality to update the plugin. If you want to do a manual update please de-activate the plugin before uploading the new files. Re-activating the plugin afterwards will allow the plugin to perform database updates if necessary.