<?php
/*
Plugin Name: Hexatenberg
Description: Add and remove JS files for Gutenberg blocks from the admin interface.
Version: 1.0
Author: Olivier Roy
Text Domain: Hexatenberg
*/



// Inclut les fichiers nécessaires
require_once plugin_dir_path(__FILE__) . 'includes/activation.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'includes/file-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/enqueue-scripts.php';

// Charge le textdomain pour la traduction
add_action('plugins_loaded', function () {
  load_plugin_textdomain('hexatenberg', false, dirname(plugin_basename(__FILE__)) . '/languages');
});
