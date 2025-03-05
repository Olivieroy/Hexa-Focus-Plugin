<?php
add_action('enqueue_block_editor_assets', function () {
  $upload_dir = plugin_dir_path(dirname(__FILE__)) . '/js-uploads';
  $disabled_file = $upload_dir . '/disabled-blocks.json';

  // Vérifie et crée le dossier js-uploads s'il n'existe pas
  if (!file_exists($upload_dir)) {
    wp_mkdir_p($upload_dir);
  }

  // Vérifie et crée le fichier disabled-blocks.json s'il n'existe pas
  if (!file_exists($disabled_file)) {
    file_put_contents($disabled_file, json_encode([]));
  }

  // Charge la liste des blocs désactivés
  $disabled_blocks = json_decode(file_get_contents($disabled_file), true);
  if (!is_array($disabled_blocks)) {
    $disabled_blocks = [];
  }

  // Vérifie que c'est bien un dossier avant de scanner
  if (!is_dir($upload_dir)) {
    return;
  }

  $files = array_filter(scandir($upload_dir), function ($file) use ($upload_dir, $disabled_blocks) {
    return is_file($upload_dir . '/' . $file)
      && pathinfo($file, PATHINFO_EXTENSION) === 'js'
      && !in_array($file, $disabled_blocks);
  });

  // Enregistre et charge chaque fichier JS non désactivé
  foreach ($files as $file) {
    wp_enqueue_script(
      'gutenberg-block-' . pathinfo($file, PATHINFO_FILENAME),
      plugins_url('js-uploads/' . $file, dirname(__FILE__)),
      ['wp-blocks', 'wp-element', 'wp-editor'],
      filemtime($upload_dir . '/' . $file),
      true
    );
  }
});
