<?php
add_action('enqueue_block_editor_assets', function () {
  $upload_dir = plugin_dir_path(dirname(__FILE__)) . '/js-uploads';

  // Vérifie si le dossier existe, sinon le créer
  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
  }

  // Vérifie que c'est bien un dossier avant de scanner
  if (is_dir($upload_dir)) {
    $files = array_diff(scandir($upload_dir), ['.', '..']);
  } else {
    $files = []; // Si le dossier n'existe toujours pas, initialise une liste vide
  }

  // Enregistre et charge chaque fichier JS
  foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'js') {
      wp_enqueue_script(
        'gutenberg-block-' . pathinfo($file, PATHINFO_FILENAME),
        plugins_url('js-uploads/' . $file, dirname(__FILE__)), // Utilise plugins_url pour générer une URL correcte
        ['wp-blocks', 'wp-element', 'wp-editor'],
        null,
        true
      );
    }
  }
});
