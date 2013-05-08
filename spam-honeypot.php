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
	add_action('admin_menu', 'menu_honeypot');
	add_action('admin_init', 'init_honeypot');
    add_filter('plugin_action_links', 'add_action_link_honeypot', 10, 2);
	register_activation_hook(__FILE__, 'register_honeypot');
}

function menu_honeypot() {
	add_options_page('Honeypot Settings', 'Spam Honeypot', 'manage_options', 'spam-honeypot', 'options_page_honeypot');
}

function init_honeypot() {
    register_setting('spam-honeypot', 'textarea_name');
    register_setting('spam-honeypot', 'submit_name');
}

function add_action_link_honeypot($links, $file) {
    if ($file == basename(__FILE__)) {
        array_unshift($links, '<a href="options-general.php?page=spam-honeypot">' . __('Settings','spam-honeypot') . '</a>');
    }
    return $links;
}

function register_honeypot() {
	add_option('textarea_name', 'more_comment');
	//die("register_honeypot2: " . get_option('textarea_name'));
	add_option('submit_name', '');
	//die("register_honeypot3");
	load_plugin_textdomain( 'spam-honeypot', WP_PLUGIN_DIR .'/wp-spam-honeypot/languages', '/wp-spam-honeypot/languages' );
	//die("register_honepot4");
}

function options_page_honeypot() {
load_plugin_textdomain( 'spam-honeypot', WP_PLUGIN_DIR .'/wp-spam-honeypot/languages', '/wp-spam-honeypot/languages' );
?>
<div class="wrap">
<h2>Spam Honeypot Settings</h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields('spam-honeypot'); ?>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Hidden Textarea Name:'); ?></th>
<td><input type="text" id="textarea_name" name="textarea_name" value="<?php echo get_option('textarea_name'); ?>" title="This field controls the name of a hidden (by CSS) textarea injected into your comment form by the plugin. If a bot fills this field out, the post will be tagged as spam."></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Submit Button Name (optional):'); ?></th>
<td><input type="text" id="submit_name" name="submit_name" value="<?php echo get_option('submit_name'); ?>" title="This field should be set to the value of the &quot;name&quot; attribute for the submit button in your comment form. If a bot does not include this in a form submission, the post will be tagged as spam. If this field is left blank, the check will not be conducted."></td>
</tr>
</table>
<p class="submit"><input type="submit" class="button-primary" value="<?php echo __('Update Options','spam-honeypot'); ?>"></p>
</form>
</div>
<?php
}

/*
MAIN
*/

add_action('comment_form', 'add_honeypot');
add_filter('pre_comment_approved', 'check_honeypot');

function add_honeypot($postID) {
	load_plugin_textdomain( 'spam-honeypot', WP_PLUGIN_DIR .'/wp-spam-honeypot/languages', '/wp-spam-honeypot/languages' );
    $textarea_name = get_option('textarea_name');
	echo '<p style="display:none">';
	echo '<textarea name="' . $textarea_name . '" cols="100%" rows="10"></textarea>';
	echo '<label  for="' . $textarea_name . '">' . __('If you are a human, do not fill in this field.','spam-honeypot') . '</label>';	
	echo '</p>';
	
}

function check_honeypot($approved) {
    $textarea_name = get_option('textarea_name');
	$submit_name = get_option('submit_name');
	if (!empty($_POST[$textarea_name]) // Bot filled out the hidden textarea
        || (!empty($submit_name) // User specified a value for the submit button
        && empty($_POST[$submit_name]))) { // Bot didn't include a value for the submit button 
		$approved = 'spam';
    }
	return $approved;
}
