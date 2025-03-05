<?php

if (!function_exists('hexatenberg_user_history_page')) {
  function hexatenberg_user_history_page()
  {
    if (!current_user_can('edit_posts')) {
      wp_die(__('You do not have permission to view this page.', 'hexatenberg-js'));
    }

    $upload_dir = plugin_dir_path(dirname(__FILE__)) . '/js-uploads';
    $block_history_file = $upload_dir . '/block-history.json';
    $block_history = json_decode(file_get_contents($block_history_file), true) ?: [];

    if (!file_exists($block_history_file)) {
      file_put_contents($block_history_file, json_encode([], JSON_PRETTY_PRINT));
    }


    // 📌 Regrouper les utilisateurs ayant effectué des actions
    $users = [];
    foreach ($block_history as $entry) {
      $user_id = $entry['id'] ?? 'unknown';
      if (!isset($users[$user_id])) {
        $users[$user_id] = [
          'name' => $entry['name'] ?? __('Unknown User', 'hexatenberg-js'),
          'avatar' => $entry['avatar'] ?? 'https://www.gravatar.com/avatar/?d=mp',
          'id' => $user_id
        ];
      }
    }

    // 📌 Trier les utilisateurs par ordre alphabétique
    uasort($users, function ($a, $b) {
      return strcasecmp($a['name'], $b['name']);
    });

    // 📌 Sélection d'un utilisateur
    if (empty($_GET['user'])) { ?>
      <div class="wrap hexatenberg-js">
        <h1>
          <span class="dashicons dashicons-groups"></span>
          <?php _e('Select a User', 'hexatenberg-js'); ?>
        </h1>

        <input type="text" id="search-user" placeholder="<?php _e('Search user...', 'hexatenberg-js'); ?>" style="width: 100%; padding: 8px; margin-bottom: 10px;">

        <div class="user-list">
          <?php foreach ($users as $user) { ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=hexatenberg-user-history&user=' . $user['id'])); ?>" class="user-item">
              <img src="<?php echo esc_url($user['avatar']); ?>" class="user-avatar" alt="<?php echo esc_attr($user['name']); ?>">
              <p class="user-name">
                <?php echo esc_html($user['name']); ?>
              </p>
            </a>
          <?php } ?>
        </div>
      </div>

      <script>
        document.getElementById('search-user').addEventListener('input', function() {
          const searchValue = this.value.toLowerCase();
          document.querySelectorAll('.user-item').forEach(user => {
            const userName = user.textContent.toLowerCase();
            user.style.display = userName.includes(searchValue) ? '' : 'none';
          });
        });
      </script>

    <?php return;
    }

    // 📌 Afficher l'historique du user sélectionné
    $user_id = $_GET['user'];
    $user_history = array_filter($block_history, function ($entry) use ($user_id) {
      return $entry['id'] == $user_id;
    });

    // 📌 Trier par date décroissante
    usort($user_history, function ($a, $b) {
      return strtotime($b['date']) - strtotime($a['date']);
    });

    if (empty($user_history)) {
      echo '<p>' . __('No history found for this user.', 'hexatenberg-js') . '</p>';
      return;
    }

    // 📌 Pagination
    $per_page = 40;
    $total_entries = count($user_history);
    $total_pages = ceil($total_entries / $per_page);
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $start_index = ($current_page - 1) * $per_page;
    $paged_history = array_slice($user_history, $start_index, $per_page);
    ?>
    <div class="wrap hexatenberg-js">
      <h1>
        <?php
        if (isset($users[$user_id])) {
          $selected_user_name = esc_html($users[$user_id]['name']);
          $selected_user_avatar = esc_url($users[$user_id]['avatar']);
        } else {
          $selected_user_name = __('Unknown User', 'hexatenberg-js');
          $selected_user_avatar = 'https://www.gravatar.com/avatar/?d=mp'; // Avatar par défaut
        }
        ?>

        <img src="<?php echo $selected_user_avatar; ?>" class="avatar-small" alt="<?php echo $selected_user_name; ?>">
        <?php printf(__('%s history', 'hexatenberg-js'), $selected_user_name); ?>
      </h1>

      <input type="text" id="search-input" placeholder="<?php _e('Search by Title, Category or File Name...', 'hexatenberg-js'); ?>" style="width: 100%; padding: 8px; margin-bottom: 10px;">


      <table class="widefat fixed history-table">
        <thead>
          <tr>

            <th><?php _e('Title', 'hexatenberg-js'); ?>
              <button class="sort-button" data-sort="title"><?php _e('↕', 'hexatenberg-js'); ?></button>
            </th>
            <th><?php _e('Category', 'hexatenberg-js'); ?>
              <button class="sort-button" data-sort="category"><?php _e('↕', 'hexatenberg-js'); ?></button>
            </th>
            <th><?php _e('File Name', 'hexatenberg-js'); ?>
              <button class="sort-button" data-sort="file"><?php _e('↕', 'hexatenberg-js'); ?></button>
            </th>
            <th><?php _e('Action', 'hexatenberg-js'); ?>
              <select id="filter-action">
                <option value="all"><?php _e('All', 'hexatenberg-js'); ?></option>
                <option value="add"><?php _e('Added', 'hexatenberg-js'); ?></option>
                <option value="update"><?php _e('Updated', 'hexatenberg-js'); ?></option>
                <option value="disable"><?php _e('Disabled', 'hexatenberg-js'); ?></option>
                <option value="enable"><?php _e('Enabled', 'hexatenberg-js'); ?></option>
                <option value="delete"><?php _e('Deleted', 'hexatenberg-js'); ?></option>
                <option value="error"><?php _e('Error Detected', 'hexatenberg-js'); ?></option>
              </select>
            </th>
            <th><?php _e('Date', 'hexatenberg-js'); ?>
              <button class="sort-button" data-sort="date"><?php _e('↕', 'hexatenberg-js'); ?></button>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($paged_history as $entry) { ?>
            <tr class="history-entry"
              data-file="<?php echo esc_attr($entry['file']); ?>"
              data-title="<?php echo esc_attr($entry['title'] ?? ''); ?>"
              data-category="<?php echo esc_attr($entry['category'] ?? ''); ?>"
              data-action="<?php echo esc_attr($entry['action']); ?>"
              data-date="<?php echo esc_attr(strtotime($entry['date'])); ?>">
              <td><?php echo esc_html($entry['title'] ?? 'N/A'); ?></td>
              <td><?php echo esc_html($entry['category'] ?? 'N/A'); ?></td>
              <td><?php echo esc_html($entry['file']); ?></td>
              <td><span class="status-<?php echo esc_attr($entry['action']); ?>"><?php echo esc_html(ucfirst($entry['action'])); ?></span></td>
              <td><?php echo esc_html($entry['date']); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <script>
      document.getElementById('search-input').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        document.querySelectorAll(".history-entry").forEach(row => {
          const fileName = row.getAttribute("data-file").toLowerCase();
          const title = row.getAttribute("data-title").toLowerCase();
          const category = row.getAttribute("data-category").toLowerCase();

          row.style.display = (fileName.includes(searchValue) || title.includes(searchValue) || category.includes(searchValue)) ? "" : "none";
        });
      });

      document.getElementById("filter-action").addEventListener("change", function() {
        const selectedAction = this.value;
        document.querySelectorAll(".history-entry").forEach(row => {
          row.style.display = (selectedAction === "all" || row.getAttribute("data-action") === selectedAction) ? "" : "none";
        });
      });

      document.querySelectorAll(".sort-button").forEach(button => {
        button.addEventListener("click", function() {
          const sortKey = this.getAttribute("data-sort");
          const rows = [...document.querySelectorAll(".history-entry")];

          rows.sort((a, b) => {
            return sortKey === "date" ?
              Number(b.dataset.date) - Number(a.dataset.date) :
              a.dataset[sortKey].localeCompare(b.dataset[sortKey]);
          });

          rows.forEach(row => document.querySelector("tbody").appendChild(row));
        });
      });

      document.querySelectorAll(".sort-button").forEach(button => {
        button.addEventListener("click", function() {
          const sortKey = this.getAttribute("data-sort");
          const rows = [...document.querySelectorAll(".history-entry")];

          rows.sort((a, b) => a.dataset[sortKey].localeCompare(b.dataset[sortKey]));

          rows.forEach(row => document.querySelector("tbody").appendChild(row));
        });
      });
    </script>
<?php
  }
}
