<?php
/*
Plugin Name: Spam Honey Pot
Plugin URI: http://wordpress.org/extend/plugins/spam-honeypot
Description: Adds a hidden text field to the comment form to trap spam bots.
Version: 1.0.0
Author: Matthew Turland
Author URI: http://matthewturland.com
*/

add_action('comment_form', 'add_honeypot');
add_filter('pre_comment_approved', 'check_honeypot');

function add_honeypot($postID) {
    echo '<textarea name="more_comment" style="display: none;"></textarea>';
}

function check_honeypot($approved) {
    if (!empty($_POST['more_comment'])) {
        $approved = 'spam';
    }
    return $approved;
}
