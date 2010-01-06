=== Plugin Name ===
Contributors: tobias382
Tags: comments, spam
Requires at least: 2.9.0
Tested up to: 2.9.1
Stable tag: 1.0.0 

Adds a hidden text field to the comment form to trap spam bots.

== Description ==

This plugin works by adding a textarea field to the comment form that’s hidden using a CSS style. Since bots don’t 
generally detect CSS like this, they proceed to fill out the field like any other field. This implies that they 
aren’t a human (or they wouldn't even see the field with CSS hiding it) being using a browser, in which case the 
plugin marks the comment as spam. This seems to catch the vast majority of spam with very few false results and 
doesn't require contacting a third party service like most spam plugins.

== Installation ==

1. Upload `spam-honeypot.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Be sure that your theme implements `<?php do_action('comment_form'); ?>` in comments.php and comments-popup.php 

== Frequently Asked Questions ==

= How can I get a question added to this FAQ? =

Just contact me at <tobias382@gmail.com> with your question. I'll respond with an answer and, if it appears 
relevant to the plugin's userbase, I'll add it to the FAQ in the next release.

== Changelog ==

= 1.0.0 =
* Initial release. 
