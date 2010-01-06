<?php

/*
Plugin Name: Spam Honey Pot
Plugin URI: http://wordpress.org/extend/plugins/spam-honeypot
Description: Adds a hidden text field to the comment form to trap spam bots.
Version: 1.0.0
Author: Matthew Turland
Author URI: http://matthewturland.com
*/

/*
ADMIN
*/

if (is_admin()) {
	add_action('admin_menu','menu_honeypot');
	add_action('admin_init','init_honeypot');
	register_activation_hook(__FILE__,'register_honeypot');
}

function menu_honeypot() {
	add_options_page('Honeypot Settings','Spam-Honeypot','administrator',__FILE__,'options_page_honeypot');
}

function init_honeypot() {
	if (function_exists('register_setting')) {
		register_setting('options-honeypot', 'hash_honeypot');
	}
}

function options_page_honeypot() {
	echo '<div class="wrap">';
	echo '<p>Here is where the Honeypot options form will go.</p>';
	echo '</div>';
}

function register_honeypot() {
	add_option('hash_honeypot',sha1(uniqid(time(), true));
}

/*
MAIN
*/

add_action('comment_form', 'add_honeypot');
add_filter('pre_comment_approved', 'check_honeypot');

function add_honeypot($postID) {
	$hash = get_option('hash_honeypot');
	echo '<textarea name="'.$hash.'" style="display: none;"></textarea>';
}

function check_honeypot($approved) {
	$hash = get_option('hash_honeypot');
	if (!empty($_POST[$hash])) {
		$approved = 'spam';
	}
	return $approved;
}
