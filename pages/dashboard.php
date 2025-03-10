<?php
$upload_dir = plugin_dir_path(dirname(__FILE__)) . '/js-uploads';
$disabled_file = $upload_dir . '/disabled-blocks.json';
$block_history_file = $upload_dir . '/block-history.json';
$error_file = $upload_dir . '/error-blocks.json';


if (!file_exists($error_file)) {
  file_put_contents($error_file, json_encode([]));
}

if (!file_exists($disabled_file)) {
  file_put_contents($disabled_file, json_encode([]));
}

if (!file_exists($block_history_file)) {
  file_put_contents($block_history_file, json_encode([]));
}


$disabled_blocks = json_decode(file_get_contents($disabled_file), true) ?: [];
$block_history = json_decode(file_get_contents($block_history_file), true) ?: [];
$error_blocks = json_decode(file_get_contents($error_file), true) ?: [];


usort($block_history, function ($a, $b) {
  return strtotime($b['date']) - strtotime($a['date']);
});

$files = array_diff(scandir($upload_dir), ['.', '..', 'disabled-blocks.json', 'block-history.json', 'error-blocks.json']);
$active_blocks = array_diff($files, $disabled_blocks);
$errored_blocks = array_intersect($files, $error_blocks);

$total_blocks = count($files);
$error_count = count($errored_blocks);
$status_color = '#4CAF50';

if ($total_blocks > 0) {
  $status_percentage = round((($total_blocks - $error_count) / $total_blocks) * 100);
} else {
  $status_percentage = 100;
}


if ($status_percentage >= 80) {
  $status_color = '#4CAF50';
} elseif ($status_percentage >= 40) {
  $status_color = '#FFA500';
} else {
  $status_color = '#FF0000';
}

$circle_radius = 30;
$circumference = 2 * M_PI * $circle_radius;
$stroke_dashoffset = $circumference * ((100 - $status_percentage) / 100);
?>

<div class="wrap hexatenberg-js ">
  <h1>
    <span class="dashicons dashicons-dashboard"></span>
    <?php _e('Dashboard', 'hexatenberg-js'); ?>
  </h1>

  <div class="dashboard-stats">
    <div class="stat-box">
      <h3><?php _e('Active Blocks', 'hexatenberg-js'); ?></h3>
      <p class="number"><?php echo count($active_blocks); ?></p>
    </div>
    <div class="stat-box">
      <h3><?php _e('Errored Blocks', 'hexatenberg-js'); ?></h3>
      <p class="number error"><?php echo count($errored_blocks); ?></p>
    </div>
    <div class="stat-box status-box">
      <h3><?php _e('System Status', 'hexatenberg-js'); ?></h3>
      <svg width="80" height="80" viewBox="0 0 80 80" class="donut">
        <circle cx="40" cy="40" r="30" stroke="#ddd" stroke-width="6" fill="none" />
        <circle cx="40" cy="40" r="30" stroke="<?php echo esc_attr($status_color); ?>" stroke-width="6"
          fill="none" stroke-linecap="round"
          stroke-dasharray="<?php echo $circumference; ?>"
          stroke-dashoffset="<?php echo $circumference; ?>"
          data-percentage="<?php echo $stroke_dashoffset; ?>"
          class="donut-progress"
          transform="rotate(-90 40 40) scaleX(-1)" />
        <text x="40" y="45" font-size="16" text-anchor="middle" fill="<?php echo esc_attr($status_color); ?>">
          <?php echo $status_percentage; ?>%
        </text>
      </svg>
    </div>
  </div>

  <h2><?php _e('Action History', 'hexatenberg-js'); ?></h2>
  <?php if (!empty($block_history)) : ?>
    <table class="widefat fixed history-table">
      <thead>
        <tr>
          <th>
            <?php _e('User', 'hexatenberg-js'); ?>
          </th>
          <th class="filter-section">
            <?php _e('Action', 'hexatenberg-js'); ?>
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
          <th>
            <?php _e('File Name', 'hexatenberg-js'); ?>
            <button class="sort-button" data-sort="file"><?php _e('↕', 'hexatenberg-js'); ?></button>
          </th>
          <th>
            <?php _e('Date', 'hexatenberg-js'); ?>
            <button class="sort-button" data-sort="date"><?php _e('↕', 'hexatenberg-js'); ?></button>
          </th>
        </tr>
      </thead>

      <tbody class="dashboard-history">
        <?php
        $grouped_history = [];

        foreach ($block_history as $entry) {
          $user_id = $entry['id'] ?? 'unknown';
          $grouped_history[$user_id]['name'] = $entry['name'] ?? __('Unknown User', 'hexatenberg-js');
          $grouped_history[$user_id]['avatar'] = $entry['avatar'] ?? 'https://www.gravatar.com/avatar/?d=mp';
          $grouped_history[$user_id]['actions'][] = $entry;
        }

        foreach ($grouped_history as $user_id => $user) :
          usort($user['actions'], function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
          });

          $displayed_actions = array_slice($user['actions'], 0, 5);
          $has_more = count($user['actions']) > 5;
        ?>
          <tr class="user-header" data-user="<?php echo esc_attr($user['name']); ?>">
            <td colspan="4">
              <img src="<?php echo esc_url($user['avatar']); ?>" class="avatar-small" alt="<?php echo esc_attr($user['name']); ?>">
              <strong><?php echo esc_html($user['name']); ?></strong>
            </td>
          </tr>

          <?php foreach ($displayed_actions as $entry) : ?>
            <tr class="history-entry"
              data-user="<?php echo esc_attr($user['name']); ?>"
              data-action="<?php echo esc_attr($entry['action']); ?>"
              data-file="<?php echo esc_attr($entry['file']); ?>"
              data-date="<?php echo esc_attr(strtotime($entry['date'])); ?>">
              <td></td>
              <td>
                <?php
                $action_labels = [
                  'add'     => __('Added', 'hexatenberg-js'),
                  'update'  => __('Updated', 'hexatenberg-js'),
                  'disable' => __('Disabled', 'hexatenberg-js'),
                  'enable'  => __('Enabled', 'hexatenberg-js'),
                  'delete'  => __('Deleted', 'hexatenberg-js'),
                  'error'   => __('Error Detected', 'hexatenberg-js')
                ];

                $action_classes = [
                  'add'     => 'status-added',
                  'update'  => 'status-updated',
                  'disable' => 'status-disabled',
                  'enable'  => 'status-enabled',
                  'delete'  => 'status-deleted',
                  'error'   => 'status-error'
                ];

                $action = $entry['action'] ?? 'unknown';
                $label = $action_labels[$action] ?? __('Unknown', 'hexatenberg-js');
                $class = $action_classes[$action] ?? 'status-unknown';
                ?>

                <span class="<?php echo esc_attr($class); ?>"><?php echo esc_html($label); ?></span>
              </td>
              <td><?php echo esc_html($entry['file'] ?? 'Unknown File'); ?></td>
              <td><?php echo esc_html($entry['date'] ?? __('No Date', 'hexatenberg-js')); ?></td>
            </tr>
          <?php endforeach; ?>

          <?php if ($has_more) : ?>
            <tr class="see-more-row">
              <td colspan="4" class="see-more">
                <a href="<?php echo admin_url('admin.php?page=hexatenberg-user-history&user=' . urlencode($user_id)); ?>" class="see-more-link">
                  <?php _e('See all modifications', 'hexatenberg-js'); ?>
                </a>
              </td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>

  <?php else : ?>
    <p><?php _e('No history found.', 'hexatenberg-js'); ?></p>
  <?php endif; ?>
</div>



<script>
  document.addEventListener("DOMContentLoaded", function() {
    const donut = document.querySelector(".donut-progress");
    if (donut) {
      const percentage = donut.getAttribute("data-percentage");
      donut.style.strokeDashoffset = percentage;
    }
  });

  document.addEventListener("DOMContentLoaded", function() {
    const tableBody = document.querySelector(".dashboard-history");
    const rows = Array.from(document.querySelectorAll(".history-entry"));
    const userHeaders = Array.from(document.querySelectorAll(".user-header"));

    function sortTable(sortKey) {
      const sortedRows = [...rows].sort((a, b) => {
        let aValue = a.getAttribute(`data-${sortKey}`);
        let bValue = b.getAttribute(`data-${sortKey}`);

        if (sortKey === "date") {
          return Number(bValue) - Number(aValue); 
        } else {
          return aValue.localeCompare(bValue, undefined, {
            numeric: true
          });
        }
      });

      
      tableBody.innerHTML = "";

      
      const groupedData = {};

      sortedRows.forEach(row => {
        const userName = row.getAttribute("data-user");
        if (!groupedData[userName]) {
          groupedData[userName] = [];
        }
        groupedData[userName].push(row);
      });

      
      userHeaders.forEach(header => {
        const userName = header.getAttribute("data-user");
        if (groupedData[userName]) {
          tableBody.appendChild(header); 
          groupedData[userName].forEach(row => tableBody.appendChild(row)); 
        }
      });
    }

    
    document.querySelectorAll(".sort-button").forEach(button => {
      button.addEventListener("click", function() {
        sortTable(this.getAttribute("data-sort"));
      });
    });

    
    document.querySelector("#filter-action").addEventListener("change", function() {
      const selectedAction = this.value;
      rows.forEach(row => {
        if (selectedAction === "all" || row.getAttribute("data-action") === selectedAction) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });
  });
</script>