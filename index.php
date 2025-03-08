<?php
/*
Plugin Name: Hexatenberg
Description: A powerful WordPress plugin that allows you to add, manage, and remove custom JavaScript files for Gutenberg blocks directly from the admin interface. Includes advanced features like block activation/deactivation, error detection, and version tracking.
Version: 1.0
Author: Olivier Roy
Author URI: https://www.olivieroy.fr/
Text Domain: Hexatenberg
License: GPL-2.0-or-later
*/


if (!defined('ABSPATH')) {
  exit;
}

define('HEXATENBERG_PLUGIN_DIR', plugin_dir_path(__FILE__));

foreach (['activation', 'admin-menu', 'file-handler', 'enqueue-scripts'] as $file) {
  require_once HEXATENBERG_PLUGIN_DIR . "includes/{$file}.php";
}

add_action('plugins_loaded', function () {
  load_plugin_textdomain('hexatenberg', false, dirname(plugin_basename(__FILE__)) . '/languages');
});
