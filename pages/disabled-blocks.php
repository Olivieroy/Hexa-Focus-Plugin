<?php
$upload_dir = plugin_dir_path(dirname(__FILE__)) . '/js-uploads';
$disabled_file = $upload_dir . '/disabled-blocks.json';
$history_file = $upload_dir . '/block-history.json';

// Charger les fichiers JSON
$disabled_blocks = json_decode(file_get_contents($disabled_file), true) ?: [];
$block_history = json_decode(file_get_contents($history_file), true) ?: [];

$categorized_disabled_blocks = [];

foreach ($disabled_blocks as $file) {
  $title = 'Unknown Title';
  $category = 'Uncategorized';
  $last_modified_by = 'Unknown User';

  // Vérifier si une entrée existe dans l'historique
  foreach ($block_history as $entry) {
    if ($entry['file'] === $file) {
      $title = $entry['title'] ?? $title;
      $category = ucfirst($entry['category'] ?? $category);
      $last_modified_by = $entry['name'] ?? $last_modified_by;
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
    'last_modified_by' => $last_modified_by
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
      <ul>
        <?php foreach ($blocks as $block) : ?>
          <li class="block-item"
            data-title="<?php echo esc_attr($block['title']); ?>"
            data-category="<?php echo esc_attr($block['category']); ?>"
            data-file="<?php echo esc_attr($block['file']); ?>">
            <strong><?php echo esc_html($block['title']); ?></strong> (<?php echo esc_html($block['file']); ?>)
            <span class="last-modified"><?php _e('Last modified by', 'hexatenberg-js'); ?>: <?php echo esc_html($block['last_modified_by']); ?></span>
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
          </li>
        <?php endforeach; ?>
      </ul>
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