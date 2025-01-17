<?php
// Gère les uploads et suppressions de fichiers
add_action('admin_post_handle_gutenberg_js', function () {
  if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized user', 'focus-gutenberg-js'));
  }

  $upload_dir = plugin_dir_path(dirname(__FILE__)) . 'js-uploads';
  $success_message = $error_message = '';

  // Gestion des uploads
  if (!empty($_FILES['js_files']['name'][0])) {
    foreach ($_FILES['js_files']['name'] as $key => $name) {
      $file_tmp = $_FILES['js_files']['tmp_name'][$key];
      $file_name = sanitize_file_name($name);

      if (pathinfo($file_name, PATHINFO_EXTENSION) === 'js') {
        $file_path = $upload_dir . '/' . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
          $success_message = __('JS files added successfully!', 'focus-gutenberg-js');
        } else {
          $error_message = __('Error while uploading the file.', 'focus-gutenberg-js');
        }
      } else {
        $error_message = __('Please upload valid .js files.', 'focus-gutenberg-js');
      }
    }
  }

  // Gestion des suppressions
  if (!empty($_POST['js_to_delete'])) {
    $file_to_delete = $upload_dir . '/' . sanitize_file_name($_POST['js_to_delete']);
    if (file_exists($file_to_delete)) {
      unlink($file_to_delete);
      $success_message = __('JS file deleted successfully!', 'focus-gutenberg-js');
    } else {
      $error_message = __('File not found.', 'focus-gutenberg-js');
    }
  }

  // Redirige avec des messages de retour
  wp_redirect(admin_url('admin.php?page=gestion-js-gutenberg&success=' . urlencode($success_message) . '&error=' . urlencode($error_message)));
  exit;
});
