<?php

/*
Plugin Name: Spam Honeypot
Plugin URI: https://wordpress.org/plugins/spam-honeypot/
Description: Adds a hidden text field to the comment form to trap spam bots.
Version: 1.1.0
Author: Matthew Turland
Author URI: http://matthewturland.com
License: GPL2
Text Domain: spam-honeypot
Domain Path: /languages/
*/

/*
Copyright 2010-2015 Matthew Turland (me@matthewturland.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (is_admin()) {
    add_action('admin_menu', 'menu_honeypot');
    add_action('admin_init', 'init_honeypot');
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links_honeypot', 10, 2);
    register_activation_hook(__FILE__, 'register_honeypot');
}

function menu_honeypot()
{
    add_options_page(__('Honeypot Settings', 'spam-honeypot'), __('Spam Honeypot', 'spam-honeypot'), 'manage_options', 'spam-honeypot', 'options_page_honeypot');
}

function init_honeypot()
{
    register_setting('spam-honeypot', 'textarea_name');
    register_setting('spam-honeypot', 'submit_name');
}

function add_action_links_honeypot($links)
{
    $settings_link = '<a href="' . esc_url(admin_page('options-general.php?page=spam-honeypot')) . '">' . esc_html__('Settings', 'spam-honeypot') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

function register_honeypot()
{
    add_option('textarea_name', 'more_comment');
    add_option('submit_name', '');
}

function options_page_honeypot()
{
    $textarea_name = get_option('textarea_name', 'more_comment');
    $submit_name = get_option('submit_name', '');
?>
<div class="wrap">
<h2><?php esc_html_e('Spam Honeypot Settings', 'spam-honeypot'); ?></h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields('spam-honeypot'); ?>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php esc_html_e('Hidden Textarea Name:', 'spam-honeypot'); ?></th>
<td><input type="text" id="textarea_name" name="textarea_name" value="<?php echo esc_attr($textarea_name); ?>" title="<?php esc_attr_e('This field controls the name of a hidden (by CSS) textarea injected into your comment form by the plugin. If a bot fills this field out, the post will be tagged as spam.', 'spam-honeypot'); ?>"></td>
</tr>
<tr valign="top">
<th scope="row"><?php esc_html_e('Submit Button Name (optional):', 'spam-honeypot'); ?></th>
<td><input type="text" id="submit_name" name="submit_name" value="<?php esc_attr($submit_name); ?>" title="<?php esc_attr_e('This field should be set to the value of the &quot;name&quot; attribute for the submit button in your comment form. If a bot does not include this in a form submission, the post will be tagged as spam. If this field is left blank, the check will not be conducted.', 'spam-honeypot'); ?>"></td>
</tr>
</table>
<p class="submit"><input type="submit" class="button-primary" value="<?php esc_attr_e('Update Options','spam-honeypot'); ?>"></p>
</form>
</div>
<?php
}

add_action('comment_form', 'add_honeypot');
add_action('plugins_loaded', 'load_honeypot_translation');
add_filter('pre_comment_approved', 'check_honeypot');

function add_honeypot($postID)
{
    $textarea_name = get_option('textarea_name', 'more_comment');
    echo '<p style="display:none">';
    echo '<textarea name="' . esc_attr($textarea_name) . '" cols="100%" rows="10"></textarea>';
    echo '<label  for="' . esc_attr($textarea_name) . '">' . esc_html__('If you are a human, do not fill in this field.','spam-honeypot') . '</label>';
    echo '</p>';
}

function load_honeypot_translation()
{
    load_plugin_textdomain('spam-honeypot', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

function check_honeypot($approved)
{
    $textarea_name = get_option('textarea_name');
    $submit_name = get_option('submit_name');
    if ($textarea_name !== false && submit_name !== false) {
        // A bot filled out the hidden textarea.
        $textarea_filled_out = !empty($_POST[$textarea_name]);
        // The User specified a value for the submit button and a bot didn't
        // include a value for the submit button.
        $submit_name_missing = !empty($submit_name) && empty($_POST[$submit_name]);
        if ($textarea_filled_out || $submit_name_missing) {
            $approved = 'spam';
        }
    }
    return $approved;
}
