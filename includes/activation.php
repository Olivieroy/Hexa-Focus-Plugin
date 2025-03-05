<?php
// Crée le dossier pour stocker les fichiers JS et le fichier de gestion des blocs désactivés
register_activation_hook(__FILE__, function () {
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
});
