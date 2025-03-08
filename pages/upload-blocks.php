<?php

$upload_dir = plugin_dir_path(dirname(__FILE__)) . '/js-uploads';

if (!file_exists($upload_dir)) {
  wp_mkdir_p($upload_dir);
}
?>



<div class="wrap hexatenberg-js">
  <h1>
    <span class="dashicons dashicons-upload"></span>
    <?php _e('Add Gutenberg Blocks', 'hexatenberg-js'); ?>
  </h1>
  <form id="js-upload-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
    <input type="hidden" name="action" value="handle_gutenberg_js">
    <h2><?php _e('Upload JS or ZIP Files', 'hexatenberg-js'); ?></h2>
    <input id="js-file-input" type="file" name="js_files[]" class="input-file" multiple required>
    <ul id="js-file-list"></ul>
    <button type="submit" class=" button-update"><?php _e('Upload', 'hexatenberg-js'); ?></button>
  </form>
</div>