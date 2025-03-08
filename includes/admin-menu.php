<?php

add_action('admin_menu', function () {
  add_menu_page(
    __('Hexatenberg', 'hexatenberg-js'),
    __('Hexatenberg', 'hexatenberg-js'),
    'manage_options',
    'hexatenberg-dashboard',
    'gutenberg_js_dashboard_page',
    'dashicons-chart-bar',
    1000
  );

  add_submenu_page(
    'hexatenberg-dashboard',
    __('Dashboard', 'hexatenberg-js'),
    __('Dashboard', 'hexatenberg-js'),
    'manage_options',
    'hexatenberg-dashboard',
    'gutenberg_js_dashboard_page'
  );

  add_submenu_page(
    'hexatenberg-dashboard',
    __('Add Blocks', 'hexatenberg-js'),
    __('Add Blocks', 'hexatenberg-js'),
    'manage_options',
    'focus-add-blocks',
    'gutenberg_js_upload_page'
  );

  add_submenu_page(
    'hexatenberg-dashboard',
    __('My Custom Blocks', 'hexatenberg-js'),
    __('My Custom Blocks', 'hexatenberg-js'),
    'manage_options',
    'focus-custom-blocks',
    'gutenberg_js_custom_blocks_page'
  );

  add_submenu_page(
    'hexatenberg-dashboard',
    __('Disabled Blocks', 'hexatenberg-js'),
    __('Disabled Blocks', 'hexatenberg-js'),
    'manage_options',
    'focus-disabled-blocks',
    'gutenberg_js_disabled_blocks_page'
  );

  add_submenu_page(
    'hexatenberg-dashboard',
    __('User History', 'hexatenberg-js'),
    __('User History', 'hexatenberg-js'),
    'edit_posts',
    'hexatenberg-user-history',
    'hexatenberg_user_history_page'
  );
});

add_action('admin_enqueue_scripts', function ($hook_suffix) {
  if (strpos($hook_suffix, 'hexatenberg') !== false) {
    wp_enqueue_style(
      'hexatenberg-js-admin-style',
      plugin_dir_url(dirname(__FILE__)) . 'assets/admin-style.css',
      [],
      '1.1',
      'all'
    );

    wp_enqueue_script(
      'hexatenberg-js-admin-script',
      plugin_dir_url(dirname(__FILE__)) . 'assets/admin-script.js',
      [],
      '1.1',
      true
    );
  }
});


$user_history_path = plugin_dir_path(dirname(__FILE__)) . 'pages/user-history.php';
if (file_exists($user_history_path)) {
  require_once $user_history_path;
}

function gutenberg_js_dashboard_page()
{
  include plugin_dir_path(dirname(__FILE__)) . 'pages/dashboard.php';
}

function gutenberg_js_upload_page()
{
  include plugin_dir_path(dirname(__FILE__)) . 'pages/upload-blocks.php';
}

function gutenberg_js_custom_blocks_page()
{
  include plugin_dir_path(dirname(__FILE__)) . 'pages/custom-blocks.php';
}

function gutenberg_js_disabled_blocks_page()
{
  include plugin_dir_path(dirname(__FILE__)) . 'pages/disabled-blocks.php';
}

