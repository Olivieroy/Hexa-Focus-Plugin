<?php
// Fonction exécutée à l'activation du plugin
function hexatenberg_plugin_activation()
{
  $upload_dir = plugin_dir_path(__FILE__) . 'js-uploads';
  $history_file = $upload_dir . '/block-history.json';
  $disabled_file = $upload_dir . '/disabled-blocks.json';

  // 📂 Vérifie et crée le dossier js-uploads s'il n'existe pas
  if (!file_exists($upload_dir)) {
    wp_mkdir_p($upload_dir);
  }

  // 📜 Vérifie et crée block-history.json s'il n'existe pas
  if (!file_exists($history_file)) {
    file_put_contents($history_file, json_encode([], JSON_PRETTY_PRINT));
  }

  // ❌ Vérifie et crée disabled-blocks.json s'il n'existe pas
  if (!file_exists($disabled_file)) {
    file_put_contents($disabled_file, json_encode([], JSON_PRETTY_PRINT));
  }
}

// ⚡ Attache la fonction à l'activation du plugin
register_activation_hook(__FILE__, 'hexatenberg_plugin_activation');
