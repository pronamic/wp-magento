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

[magento pid='' cat='' latest='' name_like='']

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

The next key would be 'latest'. This key will show all latest products when not given a
value, or given a value smaller than or equal to zero. When given a positive number greater
than zero, it wil output that given number of products. The function cannot show anymore
products than available in the database. An example:

[magento latest='']

Or:

[magento latest='3']

Another key is the 'name_like' key. This key can be used for looking in the database to search for
products with a similar name. This key takes two arguments, the argument is the word or part of a
word to look for and the second argument defines how many products should be shown. Let's say your
database contains three records with the word 'ball' in it: 'red ball', 'blue ball' and 'balls'.
To show all of these records, we would write a shortcode which look a little like this:

[magento name_like='ball']

This piece of shortcode would show every product with the word 'ball' in it. Of course you don't 
always want to show all products, but only a few. Let's try to only get two products with the word
'ball' in them.

[magento name_like='ball, 2']

Now only two products are put out by the shortcode. The shortcode does not output more products than
it finds. The 'name_like' key works with what usually are called 'wildcards'. In our plugin, these
wildcards are '%' signs. If you decide you would like to search only at one side of the word, for
instance the right side, you could use a shortcode like the following: 

[magento name_like='ball%']

Assuming we are still using the same database with the balls, this piece of shortcode would output
only the 'balls' record, because it searched the database for words that started with the word 'ball'.

= User instructions - For showing products in a widget =
The widgets should be a little more easy to configure. After activating the plugin on your plugins
page, the widgets should show in your widget admin page. There are two widgets, 'Magento Products' and 
'Magento Latest Products'. The first one can show products by ID or SKU, by category and by a word to
look for in the database. All fields work exactly like the ones described in the shortcode tutorial.
The second widget shows the latest products, it only takes one field which is how many products should
be shown. If this field is set to zero, the widget will show all products. Multiple instances of each
widget can be created.

= User instructions - Creating custom templates =
This part of the tutorial will explain how to create custom layouts for your shortcode and widget
output. For every widget you can create a different layout, this is not possible for shortcode
output. The templates in our plugin's templates folder should give you a good idea of how to
customize a template of your own. We disencourage attempting to adapt the default template files.

= Custom shortcode template =
If you want to customize the layout of the outputted html of the shortcode, you should copy a
template file from our plugin's template folder and paste it into your wordpress theme folder.
The template file contains a description of the different functions you can use to show your
products with. The template should be named: "magento-products-shortcode.php".

= Custom widget template =
Customizing the widget template can be done with one collective custom widget template (which
means it's a custom templates for every widget, except the ones that already have a custom template
file of their own), but also every widget can has his own separate template. This last feature
comes in handy when you use our widgets both in the sidebar and in the footer. You might want to 
use separate layouts for both of them. 
Customizing the collective template file for widgets is a lot like creating a custom template for
the output of the shortcode, you copy a template file to your theme's folder and name it:
"magento-products-widget.php".
When you'd like a widget to have a separate layout from the others, you'll have to activate this
widget and load the page on your website it's shown on. When you right click in your browser a
little menu will appear with an option in it for looking at the website's source code. Click it and
scroll through the page of code that shows up. You should look for a line of code that looks like
this: "<!-- -- -- The Widget ID for this widget is: "magento-products3" -- -- -->". The ID of that 
specific widget is shown there. This you can use to name a custom template file with. Again, you 
copy a default template file from our plugin's template folder and paste it to your theme folder. 
You should rename the file to something like this: "magento-products-magento-products3.php".

= Custom CSS =
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