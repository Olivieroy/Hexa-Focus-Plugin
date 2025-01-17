<?php
// Crée le dossier pour stocker les fichiers JS si non existant
register_activation_hook(__FILE__, function () {
  $upload_dir = plugin_dir_path(dirname(__FILE__)) . 'js-uploads';
  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
  }
});
