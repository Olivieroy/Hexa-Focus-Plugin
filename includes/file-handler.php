<?php

add_action('admin_post_handle_gutenberg_js', function () {
  if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized user', 'focus-gutenberg-js'));
  }

  $upload_dir = plugin_dir_path(dirname(__FILE__)) . '/js-uploads';
  $disabled_file = $upload_dir . '/disabled-blocks.json';
  $block_history_file = $upload_dir . '/block-history.json';

  $success_message = $error_message = '';

  if (!file_exists($upload_dir)) {
    wp_mkdir_p($upload_dir);
  }

  foreach ([$disabled_file, $block_history_file] as $file) {
    if (!file_exists($file)) {
      file_put_contents($file, json_encode([]));
    }
  }

  $disabled_blocks = json_decode(file_get_contents($disabled_file), true) ?: [];
  $block_history = json_decode(file_get_contents($block_history_file), true) ?: [];

  $redirect_page = $_SERVER['HTTP_REFERER'] ?? admin_url('admin.php?page=focus-gutenberg-js');

  $current_user = wp_get_current_user();
  $user_info = [
    'id'     => $current_user->ID,
    'name'   => $current_user->display_name,
    'avatar' => get_avatar_url($current_user->ID),
    'date'   => date("Y-m-d H:i:s")
  ];

  function get_block_info($file_path)
  {
    $content = file_get_contents($file_path);

    preg_match("/title:\s*['\"](.+?)['\"]/", $content, $title_match);
    preg_match("/category:\s*['\"](.+?)['\"]/", $content, $category_match);
    preg_match("/icon:\s*['\"](.+?)['\"]/", $content, $icon_match);

    return [
      'title' => $title_match[1] ?? 'Unknown Title',
      'category' => $category_match[1] ?? 'Uncategorized',
      'icon' => $icon_match[1] ?? 'admin-generic'
    ];
  }

  if (!empty($_FILES['js_files']['name'][0])) {
    foreach ($_FILES['js_files']['name'] as $key => $name) {
      $file_tmp = $_FILES['js_files']['tmp_name'][$key];
      $file_name = sanitize_file_name($name);
      $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
      $file_path = $upload_dir . '/' . $file_name;

      if ($file_extension === 'js') {
        if (move_uploaded_file($file_tmp, $file_path)) {
          $block_info = get_block_info($file_path);

          $block_history[] = array_merge($user_info, [
            'block'    => $file_name,
            'action'   => 'add',
            'file'     => $file_name,
            'title'    => $block_info['title'],
            'category' => $block_info['category'],
            'icon'     => $block_info['icon']
          ]);

          file_put_contents($block_history_file, json_encode($block_history, JSON_PRETTY_PRINT));
          $success_message = __('JS file added successfully!', 'focus-gutenberg-js');
        } else {
          $error_message = __('Error while uploading the file.', 'focus-gutenberg-js');
        }
      }
    }
  }

  if (!empty($_POST['js_to_delete'])) {
    $file_to_delete = sanitize_file_name($_POST['js_to_delete']);
    $file_path = $upload_dir . '/' . $file_to_delete;

    $block_info = get_block_info($file_path);

    if (file_exists($file_path)) {
      unlink($file_path);
      $block_history[] = array_merge($user_info, [
        'block'    => $file_to_delete,
        'action'   => 'delete',
        'file'     => $file_to_delete,
        'title'    => $block_info['title'],
        'category' => $block_info['category'],
        'icon'     => $block_info['icon']
      ]);

      file_put_contents($block_history_file, json_encode($block_history, JSON_PRETTY_PRINT));
      $success_message = __('JS file deleted successfully!', 'focus-gutenberg-js');
    } else {
      $error_message = __('File not found.', 'focus-gutenberg-js');
    }
  }

  if (!empty($_POST['toggle_block'])) {
    $block_to_toggle = sanitize_file_name($_POST['toggle_block']);
    $file_path = $upload_dir . '/' . $block_to_toggle;
    $block_info = get_block_info($file_path);

    if (in_array($block_to_toggle, $disabled_blocks)) {
      $disabled_blocks = array_diff($disabled_blocks, [$block_to_toggle]);
      $block_history[] = array_merge($user_info, [
        'block'    => $block_to_toggle,
        'action'   => 'enable',
        'file'     => $block_to_toggle,
        'title'    => $block_info['title'],
        'category' => $block_info['category'],
        'icon'     => $block_info['icon']
      ]);
      $success_message = __('Block enabled successfully!', 'focus-gutenberg-js');
    } else {
      $disabled_blocks[] = $block_to_toggle;
      $block_history[] = array_merge($user_info, [
        'block'    => $block_to_toggle,
        'action'   => 'disable',
        'file'     => $block_to_toggle,
        'title'    => $block_info['title'],
        'category' => $block_info['category'],
        'icon'     => $block_info['icon']
      ]);
      $success_message = __('Block disabled successfully!', 'focus-gutenberg-js');
    }

    file_put_contents($disabled_file, json_encode(array_values($disabled_blocks), JSON_PRETTY_PRINT));
    file_put_contents($block_history_file, json_encode($block_history, JSON_PRETTY_PRINT));
  }

  wp_redirect($redirect_page . '&success=' . urlencode($success_message) . '&error=' . urlencode($error_message));
  exit;
});
