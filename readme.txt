=== Magento ===
Contributors: pronamic, remcotolsma, stefanboonstra
Tags: magento, webshop, e-commerce
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 0.1-beta

Integrate Magento content into your WordPress website. 

== Description ==

This plugin makes it possible to show products from your Magento store on your WordPress
website. You can either show your products with a shortcode on your pages or in posts or 
using the widget to show it in the sidebar. An ideal solution for Magento store owners 
who'd like to advertise their products on their WordPress website.


== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your 
WordPress installation and then activate the Plugin from Plugins page. 

After avtivating the plugin, you should see a Magento tab in your admin menu. Go there and
click settings. On this page there are a few settings that need to be set. 

The first field should be set to your store's WSDL for most sites this would be 
'http://yourdomain.extension/api/soap/?wsdl' where you should replace 'yourdomain.extension' 
with your store's domain. For example: 'magentostore.com'. 

The store URL should be similar to 'http://magentostore.com/', there should be nothing 
behind the last trailing slash.

Before you can set the username and API key field you'll need to have created a webservices
account in your Magento store Admin Panel. To do this, go to your Admin Panel and go to
'System' -> 'Webservices' -> 'Users'. Here you can create a new user (there's your user name).
The user's password will be the API key. Now fill out the username and API key field.

Lastly, the caching option. If you set caching to 'Yes', you will notice a much shorter page
loading time once the cache has been saved.


== User instructions ==

When you have configured the plugin correctly (as described above), you'll be able to
contact your Magento store and start showing some products on your WordPress site.
This short guide will show you how to use both the plugin and the widget of our plugin.

= User instructions - For showing products in posts/pages =
To show your products in one of your posts or pages you can write a line of shortcode into it.
Where ever you write the line of shortcode in your post or page, the products will apear. A
piece of shortcode would look a bit like the following:

[magento pid='' cat='' latest='']

This shortcode will not show any products yet, because we haven't set any of the values yet.
Lets say we have two products, a t-shirt with product ID 1 and a pair of pants with product 
ID 2. We would like to show the pair of pants we're selling in one of our posts, the shortcode
for this would like like this:

[magento pid='2']

We now see that 'pid' stands for product ID since a pair of pants showed up in our post when we
added this piece of shortcode. Now we would like to show both the shirt and the pair of pants, 
we could add an extra peace of shortcode to accomplish this:

[magento pid='2'] [magento pid='1']

But see how the t-shirt and pair of pants might not line up nicely next to eachother, even though
there is enough space to do so. We can resolve this (unless the page is not wide enough to line
them up next to eachother, as is often the case with widgets in the sidebar) by using the same
piece of shortcode to show both the t-shirt and the pair of pants:

[magento pid='2, 1']

See how we used the same 'pid' in the shortcode to show multiple products which will line up
next to eachother, given there's enough space to do so. Note that when we remove the apostrophe,
the shortcode does not function properly anymore:

[magento pid=2, 1]	<- This is wrong.

Also note that using a key like 'pid', 'cat' or 'latest' a second time in the same piece of
shortcode will replace the the previously set value and thus there's no use in doing this:

[magento pid='2' pid='1']	<- This is not recommended.

The shortcode above will only output the t-shirt (which in our example has product ID 1). Other
things might worth knowing about the 'pid' key is that there's no limitation to the number of
values you put in as long as you keep separating them with commas. The apostrophes can be
removed when you put in just one value, but removing them doesn't really gain you anything.

Moving on to the next key, 'cat'. The key 'cat' stands for category and will accept either a
category name or ID. The result of 'cat', if any, will output all products found in the
requested category. Let's say there's a category 'jeans' with category ID 5. Here's the shortcode:

[magento cat='jeans']

The example shortcode would output all products in the category 'jeans'. Now let's try to get
the same result using the category's ID:

[magento cat='5']

Using the 'cat' key with one value will output all products in the category. You might not always
want that, so as a second argument for this key you can define a number which will serve as the
maximum number of products shown. If you only want three get three products the shortcode would
look a bit like this:

[magento cat='jeans, 3']

Or:

[magento cat='5, 3']

Pay good attention to how the arguments are separated: Using a comma. If you want the function
to show three products but there just aren't that many products in that category, as many products
as possible will be output. Which means that if there are just two products in the category, two
products are shown. Not using the second argument or setting the second argument to zero, will
make the function show all products in the category.

Note that using the 'cat' key twice in the same piece of shortcode is useless. Also a nice thing 
to know is that getting the category by ID is always faster, especially for the larger stores. The 
larger the category, the longer the loading time.

The last key would be 'latest'. This key will show all latest products when not given a
value, or given a value smaller than or equal to zero. When given a positive number greater
than zero, it wil output that given number of products. The function cannot show anymore
products than available in the database. An example:

[magento latest='']

Or:

[magento latest='3']

= User instructions - For showing products in a widget =
The widget should be a little more easy to configure, when going to your theme's widget page
you can instantly see the Pronamic Magento Plugin Sidebar Widget. Drag it into one of the
sidebars you will be able to configure it. By default, it doesn't show anything.

Basicly, the first two fields work the same way as they do in the shortcode. The product ID's
or SKU's should be put in separated by commas. The category accepts a category name or ID and
does not accept multiple entries. Setting the 'Show latest products' to 'Yes' will show the
3 latest products by default. After saving a field apears in which this can be changed to any
number, including zero. Setting the number of latest products shown to zero will make the widget
output all products.

= User instructions - Creating custom templates =
The predefined template in the templates map of this plugin should give you a good idea of how
to create a custom template for your plugin. We recommend copying the template file to your theme 
map (without changing it's name) and changing it's content to your likings. The plugin will 
automatically fetch the custom template file if it's found in your theme map (don't put it in a 
subfolder). The same goes for the widget template.

The CSS can be overridden by your own in your style.css file. Our default stylesheet is registered
under the name 'pronamic-magento-plugin-stylesheet' and thus can be deregistered.


== Changelog ==

= beta-0.1 =
*	Initial release
*	Made multiple 'pid' input possible. Comma seperated.
*	Added shortcode 'cat'. Input a String with a category name to get a fixed number of three random (or less if there are less than three products with this category) products.
*	Shortcode 'cat' accepts category_id (int) now as well, which is faster.
*	Caching added. Caching can be switched on or off in the settings.
*	Made plugin available as widget.
*	New shortcode latest='$numberofshownproducts' implemented specifically for the widget, but useable for the shortcode as well. Shows latest $number of products. If no number is given (string, array or boolean put in), no products will be shown.
*	Improved caching on every API call.
*	Added function for showing multiple images of one product to the templates.
*	Improved product fetching for bigger stores.
*	Added second argument for 'cat' key, maximum number of products shown.
*	Added new shortcode key 'name_like' which will search a product name that is like the given value. The second argument is how many products should maximally show.


== Links ==

*	[Pronamic](http://pronamic.eu/)
*	[Remco Tolsma](http://remcotolsma.nl/)
*	[Markdown's Syntax Documentation][markdown syntax]
*	[Wordgento](http://wordpress.org/extend/plugins/wordgento/)


== Pronamic plugins ==

*	[Pronamic Google Maps](http://wordpress.org/extend/plugins/pronamic-google-maps/)
*	[Gravity Forms (nl)](http://wordpress.org/extend/plugins/gravityforms-nl/)
*	[Pronamic Page Widget](http://wordpress.org/extend/plugins/pronamic-page-widget/)
*	[Pronamic Page Teasers](http://wordpress.org/extend/plugins/pronamic-page-teasers/)
*	[Maildit](http://wordpress.org/extend/plugins/maildit/)
*	[Pronamic Framework](http://wordpress.org/extend/plugins/pronamic-framework/)