<div class="wrap focus-gutenberg-js">

  <?php if ($success_message) : ?>
    <div class="notice notice-success is-dismissible">
      <p><?php echo esc_html($success_message); ?></p>
    </div>
  <?php endif; ?>

  <?php if ($error_message) : ?>
    <div class="notice notice-error is-dismissible">
      <p><?php echo esc_html($error_message); ?></p>
    </div>
  <?php endif; ?>

  <form id="js-upload-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
    <input type="hidden" name="action" value="handle_gutenberg_js">
    <h2><?php _e('Add JS Files', 'focus-gutenberg-js'); ?></h2>
    <input id="js-file-input" type="file" name="js_files[]" class="input-file" multiple required>
    <ul id="js-file-list"></ul>
    <button type="submit" class="button button-primary"><?php _e('Upload', 'focus-gutenberg-js'); ?></button>
  </form>

  <h2><?php _e('Existing JS Files', 'focus-gutenberg-js'); ?></h2>
  <?php if (!empty($files)) : ?>
    <ul>
      <?php foreach ($files as $file) : ?>
        <li>
          <strong><?php echo esc_html($file); ?></strong>
          <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline;">
            <input type="hidden" name="action" value="handle_gutenberg_js">
            <input type="hidden" name="js_to_delete" value="<?php echo esc_attr($file); ?>">
            <button type="submit" class="button button-secondary"><?php _e('Delete', 'focus-gutenberg-js'); ?></button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else : ?>
    <p><?php _e('No JS files found.', 'focus-gutenberg-js'); ?></p>
  <?php endif; ?>

</div>