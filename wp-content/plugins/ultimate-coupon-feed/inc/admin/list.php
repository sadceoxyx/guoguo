<?php $this->adminHeader() ?>
<div class="wrap">
  <h2>Campaigns</h2>
  <?php if(isset($this->forcefetched)): ?>
  <div id="fetched-warning" class="updated">
    <p><?php printf(__("Campaign processed. %s posts fetched", 'ucfeed'), $this->forcefetched) ?></p>
  </div>
  <?php endif; ?>
  <table class="widefat">
    <thead>
      <tr>
        <th scope="col" style="text-align: center">ID</th>
        <th scope="col"><?php _e('Title', 'ucfeed') ?></th>
        <th style="text-align: center" scope="col"><?php _e('Active', 'ucfeed') ?></th>
        <th style="text-align: center" scope="col"><?php _e('Total posts', 'ucfeed') ?></th>
        <th scope="col"><?php _e('Last active', 'ucfeed') ?></th>
        <th scope="col" colspan="4" style="text-align: center"><?php _e('Actions', 'ucfeed') ?></th>
      </tr>
    </thead>
    <tbody id="the-list">
      <?php if(!$campaigns): ?>
      <tr>
        <td colspan="5"><?php _e('No campaigns to display', 'ucfeed') ?></td>
      </tr>
      <?php else: ?>
      <?php $class = ''; ?>
      <?php foreach($campaigns as $campaign): ?>
      <?php $class = ('alternate' == $class) ? '' : 'alternate'; ?>
      <tr id='campaign-<?php echo $campaign->id ?>' class='<?php echo $class ?> <?php if($_REQUEST['id'] == $campaign->id) echo 'highlight'; ?>'>
        <th scope="row" style="text-align: center"><?php echo $campaign->id ?></th>
        <td><?php echo attribute_escape($campaign->title) ?></td>
        <td style="text-align: center"><?php echo _e($campaign->active ? 'Yes' : 'No', 'ucfeed') ?></td>
        <td style="text-align: center"><?php echo $campaign->count ?></td>
        <td><?php echo $campaign->lastactive != '0000-00-00 00:00:00' ? WPOTools::timezoneMysql('F j, g:i a', $campaign->lastactive) : __('Never', 'ucfeed') ?></td>
        <td><a href="<?php echo $this->adminurl ?>&amp;s=edit&amp;id=<?php echo $campaign->id ?>" class='edit'>Edit</a></td>
        <td><?php echo "<a href='" . wp_nonce_url($this->adminurl . '&amp;s=forcefetch&amp;id=' . $campaign->id, 'forcefetch-campaign_' . $campaign->id) . "' class='edit' onclick=\"return confirm('". __('Are you sure you want to process all feeds from this campaign?', 'ucfeed') ."')\">" . __('Fetch', 'ucfeed') . "</a>"; ?></td>
        <td><?php echo "<a href='" . wp_nonce_url($this->adminurl . '&amp;s=reset&amp;id=' . $campaign->id, 'reset-campaign_' . $campaign->id) . "' class='delete' onclick=\"return confirm('". __('Are you sure you want to reset this campaign? Resetting does not affect already created wp posts.', 'ucfeed') ."')\">" . __('Reset', 'ucfeed') . "</a>"; ?></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <div id="ajax-response"></div>
</div>
<?php $this->adminFooter() ?>
