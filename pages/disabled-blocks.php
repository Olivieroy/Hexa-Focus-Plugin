<?php
$upload_dir = plugin_dir_path(dirname(__FILE__)) . '/js-uploads';
$disabled_file = $upload_dir . '/disabled-blocks.json';
$history_file = $upload_dir . '/block-history.json';

if (!file_exists($upload_dir)) {
  wp_mkdir_p($upload_dir);
}

if (!file_exists($history_file)) {
  file_put_contents($history_file, json_encode([], JSON_PRETTY_PRINT));
}

if (!file_exists($disabled_file)) {
  file_put_contents($disabled_file, json_encode([], JSON_PRETTY_PRINT));
}

// Charger les fichiers JSON
$disabled_blocks = json_decode(file_get_contents($disabled_file), true) ?: [];
$block_history = json_decode(file_get_contents($history_file), true) ?: [];

$categorized_disabled_blocks = [];

foreach ($disabled_blocks as $file) {
  $title = 'Unknown Title';
  $category = 'Uncategorized';
  $last_modified_by = 'Unknown User';
  $icon = 'admin-generic';

  // Vérifier si une entrée existe dans l'historique
  foreach ($block_history as $entry) {
    if ($entry['file'] === $file) {
      $title = $entry['title'] ?? $title;
      $category = ucfirst($entry['category'] ?? $category);
      $last_modified_by = $entry['name'] ?? $last_modified_by;
      $icon = $entry['icon'] ?? $icon;
    }
  }

  // Déduire la catégorie depuis le nom du fichier si non définie
  if (preg_match('/^([^-]+)-.+\.js$/', $file, $matches)) {
    $category = ucfirst($matches[1]); // Extrait la première partie du nom de fichier
  }

  $categorized_disabled_blocks[$category][] = [
    'file' => $file,
    'title' => $title,
    'category' => $category,
    'last_modified_by' => $last_modified_by,
    'icon' => $icon
  ];
}

// Tri par défaut des catégories en ordre alphabétique
ksort($categorized_disabled_blocks);
?>

<div class="wrap hexatenberg-js">
  <h1>
    <span class="dashicons dashicons-hidden"></span>
    <?php _e('Disabled Blocks', 'hexatenberg-js'); ?>
  </h1>

  <!-- Search Bar -->
  <input type="text" id="search-input" placeholder="<?php _e('Search by Title, Category or File Name...', 'hexatenberg-js'); ?>" style="width: 100%; padding: 8px; margin-bottom: 10px;">

  <?php if (!empty($categorized_disabled_blocks)) : ?>
    <?php foreach ($categorized_disabled_blocks as $category => $blocks) : ?>
      <h2><?php echo esc_html($category); ?></h2>
      <div class="blocks-list">
        <?php foreach ($blocks as $block) : ?>
          <div class="block-item"
            data-title="<?php echo esc_attr($block['title']); ?>"
            data-category="<?php echo esc_attr($block['category']); ?>"
            data-file="<?php echo esc_attr($block['file']); ?>">
            <span class="dashicons dashicons-<?php echo esc_attr($block['icon']); ?>"></span>


            <div class="block-name">
              <h3><?php echo esc_html($block['title']); ?></h3>
              <span class="file-name">(<?php echo esc_html($block['file']); ?>)</span>
            </div>
            <div class="modified-by">
              <span class="last-modified"><?php _e('Last modified by', 'hexatenberg-js'); ?>:</span>
              <span> <?php echo esc_html($block['last_modified_by']); ?></span>
            </div>

            <div class="grp-button">
              <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline;">
                <input type="hidden" name="action" value="handle_gutenberg_js">
                <input type="hidden" name="toggle_block" value="<?php echo esc_attr($block['file']); ?>">
                <input type="hidden" name="redirect_page" value="focus-disabled-blocks">
                <button type="submit" class="button-enable"><?php _e('Enable', 'hexatenberg-js'); ?></button>
              </form>
              <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline;">
                <input type="hidden" name="action" value="handle_gutenberg_js">
                <input type="hidden" name="js_to_delete" value="<?php echo esc_attr($block['file']); ?>">
                <input type="hidden" name="redirect_page" value="focus-disabled-blocks">
                <button type="submit" class="button-delete"><?php _e('Delete', 'hexatenberg-js'); ?></button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php else : ?>
    <p><?php _e('No disabled blocks found.', 'hexatenberg-js'); ?></p>
  <?php endif; ?>
</div>

<script>
  document.getElementById('search-input').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    document.querySelectorAll(".block-item").forEach(row => {
      const fileName = row.getAttribute("data-file").toLowerCase();
      const title = row.getAttribute("data-title").toLowerCase();
      const category = row.getAttribute("data-category").toLowerCase();

      row.style.display = (fileName.includes(searchValue) || title.includes(searchValue) || category.includes(searchValue)) ? "" : "none";
    });
  });
</script>