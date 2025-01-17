<?php
// Ajoute une page d'options dans l'admin
add_action('admin_menu', function () {
  add_menu_page(
    __('Manage Gutenberg JS', 'focus-gutenberg-js'),
    __('Focus Settings', 'focus-gutenberg-js'),
    'manage_options',
    'gestion-js-gutenberg',
    'gutenberg_js_management_page',
    'data:image/svg+xml;base64,' . base64_encode(file_get_contents(plugin_dir_path(dirname(__FILE__)) . 'assets/svg/menu-icon.svg')),
    100
  );
});

// Charge les styles et scripts spécifiques à la page admin
add_action('admin_enqueue_scripts', function ($hook_suffix) {
  if ($hook_suffix === 'toplevel_page_gestion-js-gutenberg') {
    wp_enqueue_style(
      'focus-gutenberg-js-admin-style',
      plugin_dir_url(dirname(__FILE__)) . 'assets/admin-style.css',
      [],
      '1.1'
    );

    wp_enqueue_script(
      'focus-gutenberg-js-admin-script',
      plugin_dir_url(dirname(__FILE__)) . 'assets/admin-script.js',
      [],
      '1.1',
      true
    );
  }
});

// Gère l'affichage de la page admin
function gutenberg_js_management_page()
{
  $upload_dir = plugin_dir_path(dirname(__FILE__)) . 'js-uploads';

  // Vérifie si le dossier existe, sinon le créer
  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
  }

  $success_message = $error_message = '';
  $files = array_diff(scandir($upload_dir), ['.', '..']); // Liste des fichiers existants

  // Inclut le formulaire et la liste des fichiers
  include plugin_dir_path(__FILE__) . 'template-admin.php';
}
