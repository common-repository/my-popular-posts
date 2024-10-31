=== Plugin Name ===
Contributors: gorakh shrestha
Tags: Post, Posts, Popular post, Popular posts, Popular
Tested up to: 4.5.2
License: GPLv2 or later

Get popular posts easily using orderby in your query string with 'popularity' argument.

== Description ==

This plugin allow you to easily fetch popular post and get total view in post. Simply use `'orderby'=>'popularity'` in query string.

Add `<?php 
$args = array('post_type'=>'post','posts_per_page'=>-1,'orderby'=>'popularity'); 
$query = new WP_Query($args); 

?>`   

<h2>Features</h2> 
1. Support the custom post type 
2. Option page to allow popular post option in post type.(<i>Setting > MPP Setting</i>)
3. Can easily get total view for particular post using `get_view_number()`, `get_view_string()`. Function must be within loop. 
4. To print the total view for particular post `the_view_number()` and `the_view_string()` both function are almost same but `the_view_string()` return with 'views' text.


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to <strong>Setting > MPP Setting</strong> and select post type.
4. Put the code `<?php $query = new WP_Query($query_string."&orderby=popularity"); ?>`.  