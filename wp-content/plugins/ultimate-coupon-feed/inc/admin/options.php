<?php require_once(WPOTPL . '/helper/form.helper.php' ) ?>
<?php $this->adminHeader() ?>
      
  <div id="wpo-section-options" class="wrap">
    <h2>Options</h2>     
    
    <?php if(isset($updated)): ?>    
      <div id="added-warning" class="updated"><p><?php _e('Options saved.', 'ucfeed') ?></p></div>
    <?php endif ?>

    <?php if(isset($not_writable)): ?>
      <div class="error"><p><?php _e('Image cache path ' . WPODIR . get_option('ucf_cachepath') . ' is not writable!' ) ?></p></div>
    <?php endif ?>

    <form action="" method="post" accept-charset="utf-8">      
      <input type="hidden" name="update" value="1" />
      
      <ul id="options">
        <li id="options_cron">
          <?php echo label_for('option_unixcron', __('Unix cron', 'ucfeed')) ?>
          <?php echo checkbox_tag('option_unixcron', 1, get_option('ucf_unixcron')) ?>
        
          <h3>Cron command:</h3>
          <div id="cron_command" class="command"><?php echo $this->cron_command ?></div>
          
          <h3>WebCron-ready URL:</h3>
          <div id="cron_command" class="command"><?php echo $this->cron_url ?></div>
        
          <p class="note"><?php _e('Cron is set up to handle fetching.', 'ucfeed') ?> <a href="<?php echo $this->helpurl ?>cron" class="help_link"><?php _e('More', 'ucfeed') ?></a></p>
        </li>

        <li>
          <?php echo label_for('option_logging', __('Enable logging', 'ucfeed')) ?>
          <?php echo checkbox_tag('option_logging', 1, get_option('ucf_log')) ?>
        
          <p class="note"><?php _e('Enable database-driven logging of events.', 'ucfeed') ?> <a href="<?php echo $this->helpurl ?>logging" class="help_link"><?php _e('More', 'ucfeed') ?></a></p>
        </li>
      
        <li>
          <?php echo label_for('option_logging_stdout', __('Enable logging stdout', 'ucfeed')) ?>
          <?php echo checkbox_tag('option_logging_stdout', 1, get_option('ucf_log_stdout')) ?>
        
          <p class="note"><?php _e('With this option enabled, Ultimate-Coupon-Feed will attempt to show you logs creation in real time when manual fetching is used.', 'ucfeed') ?> <a href="<?php echo $this->helpurl ?>logging" class="help_link"><?php _e('More', 'ucfeed') ?></a></p>
        </li>
      
        <li>
          <?php echo label_for('option_caching', __('Cache images', 'ucfeed')) ?>
          <?php echo checkbox_tag('option_caching', 1, get_option('ucf_cacheimages')) ?>
        
          <p class="note"><?php _e('This option overrides all campaign-specific settings', 'ucfeed') ?> <a href="<?php echo $this->helpurl ?>image_caching" class="help_link"><?php _e('More', 'ucfeed') ?></a></p>
        </li>
        
        <li>
          <?php echo label_for('option_cachepath', __('Image cache path')) ?>
          <?php echo input_tag('option_cachepath', get_option('ucf_cachepath')) ?>           
          
          <p class="note"><?php printf(__('The path %s must exist, be writable by the server and accessible through browser.', 'ucfeed'), '<span id="cachepath">'. WPODIR . '<span id="cachepath_input">' . get_option('ucf_cachepath') . '</span></span>') ?></p>                 
        </li>
      </ul>     
    
      <p class="submit">
      <?php echo submit_tag(__('Save', 'ucfeed')) ?>
      </p>
    </form>
  </div>
  
<?php $this->adminFooter() ?>