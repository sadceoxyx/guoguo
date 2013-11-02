<?php require_once(WPOTPL . '/helper/form.helper.php' ) ?>
<?php require_once(WPOTPL . '/helper/edit.helper.php' ) ?>
<?php $this->adminHeader() ?>

<div class="wrap">
  <?php if(isset($campaign_add)): ?>
  <h2>Add campaign</h2>
  <?php else: ?>
  <h2>Editing campaign</h2>
  <?php endif;?>
  <?php if(isset($this->errno) && $this->errno): ?>
  <div id="edit-warning" class="error">
    <p><strong>
      <?php _e('The following errors have been encountered:', 'ucfeed') ?>
      </strong></p>
    <ul>
      <?php foreach($this->errors as $section => $errs): ?>
      <?php if($errs): ?>
      <li> <?php echo ucfirst($section) ?>
        <ul class="errors">
          <?php foreach($errs as $error): ?>
          <li><?php echo $error ?></li>
          <?php endforeach ?>
        </ul>
      </li>
      <?php endif?>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php else: ?>
  <?php if(isset($addedid)): ?>
  <div id="added-warning" class="updated">
    <p><?php printf(__('Campaign added successfully. <a href="%s">Edit it</a> or <a href="%s">fetch it now</a>', 'ucfeed'), $this->adminurl . '&s=edit&id=' . $addedid, wp_nonce_url($this->adminurl . '&amp;s=forcefetch&amp;id=' . $addedid, 'forcefetch-campaign_' . $addedid)) ?></p>
  </div>
  <?php elseif(isset($this->tool_success)): ?>
  <div id="added-warning" class="updated">
    <p><?php echo $this->tool_success ?></p>
  </div>
  <?php elseif(isset($edited)): ?>
  <div id="added-warning" class="updated">
    <p>
      <?php _e('Campaign edited successfully.', 'ucfeed') ?>
    </p>
  </div>
  <?php endif ?>
  <?php endif; ?>
  <form id="edit_campaign" action="" method="post" accept-charset="utf-8">
    <?php wp_nonce_field('ucfeed-edit-campaign') ?>
    <?php if(isset($campaign_add)): ?>
    <?php echo input_hidden_tag('campaign_add', 1) ?>
    <?php else: ?>
    <?php echo input_hidden_tag('campaign_edit', $id) ?>
    <?php endif; ?>
    <ul id="edit_buttons" class="submit">
      <li><a href="<?php echo $this->helpurl ?>campaigns" class="help_link">
        <?php _e('Help', 'ucfeed') ?>
        </a></li>
      <li>
        <input type="submit" name="edit_submit" value="Submit" id="edit_submit" />
      </li>
    </ul>
    <ul id="edit_tabs">
      <li class="current"><a href="#" id="tab_basic">
        <?php _e('Basic', 'ucfeed') ?>
        </a></li>
      <li><a href="#" id="tab_categories">
        <?php _e('Categories', 'ucfeed') ?>
        </a></li>
      <li><a href="#" id="tab_options">
        <?php _e('Options', 'ucfeed') ?>
        </a></li>
    </ul>
    <div id="edit_sections">
      <!-- Basic section -->
      <div class="section current" id="section_basic">
        <div class="longtext required"> <?php echo label_for('campaign_title', __('Title', 'ucfeed')) ?> <?php echo input_tag('campaign_title', _data_value($data['main'], 'title')) ?>
          <p class="note">
            <?php _e('Tip: pick a name that is general for all the campaign\'s feeds (eg: Paris Hilton)', 'ucfeed' ) ?>
          </p>
        </div>
        <div class="checkbox required"> <?php echo label_for('campaign_active', __('Active?', 'ucfeed')) ?> <?php echo checkbox_tag('campaign_active', 1, _data_value($data['main'], 'active', true)) ?>
          <p class="note">
            <?php _e('If inactive, the parser will ignore these feeds', 'ucfeed' ) ?>
          </p>
        </div>
        <div class="text"> <?php echo label_for('campaign_slug', __('Campaign slug', 'ucfeed')) ?> <?php echo input_tag('campaign_slug', _data_value($data['main'], 'slug')) ?>
          <p class="note">
            <?php _e('Optionally, you can set an identifier for this campaign. Useful for detailed track of your ad-revenue.', 'ucfeed' ) ?>
          </p>
        </div>
      </div>
      <!-- Feeds section -->
      <!-- Categories section -->
      <div class="section" id="section_categories">
      <div id="catocat">
        <p>
          <?php _e('These are the categories where the posts will be created once they\'re fetched from the feeds.', 'ucfeed') ?>
        </p>
        <p>
          <?php _e('You have to select at least one.', 'ucfeed') ?>
        </p>
        <ul id="categories">
          <?php $this->adminEditCategories($data) ?>
          <?php if(isset($data['categories']['new'])): ?>
          <?php foreach($data['categories']['new'] as $i => $catname): ?>
          <li> <?php echo checkbox_tag('campaign_newcat[]', 1, true, 'id=campaign_newcat_' . $i) ?> <?php echo input_tag('campaign_newcatname[]', $catname, 'class=input_text id=campaign_newcatname_' . $i) ?> </li>
          <?php endforeach ?>
          <?php endif ?>
        </ul>
        <a href="#quick_add" id="quick_add">
        <?php _e('Quick add', 'ucfeed') ?>
        </a> 
        </div>
        </div>
      <!-- Rewrite section -->
      <!-- Options -->
      <div class="section" id="section_options">
        <?php if(isset($campaign_edit)): ?>
        <div class="section_warn"> <img src="<?php echo $this->tplpath ?>/images/icon_alert.gif" alt="<?php _e('Warning', 'ucfeed') ?>" class="icon" />
          <h3>
            <?php _e('Remember that', 'ucfeed') ?>
          </h3>
          <p>
            <?php _e('Changing these options only affects the creation of posts after the next time feeds are parsed.', 'ucfeed') ?>
          </p>
          <p>
            <?php _e('If you need to edit existing posts, you can do so by using the options under the Tools tab', 'ucfeed') ?>
          </p>
        </div>
        <?php endif ?>
        <div class="multipletext">
          <?php 

              $f = _data_value($data['main'], 'frequency');



              if($f) {

                $frequency = WPOTools::calcTime($f);                

              }                

              else

                $frequency = array();

            ?>
          <label>
            <?php _e('Frequency', 'ucfeed') ?>
          </label>
          <?php echo input_tag('campaign_frequency_d', _data_value($frequency, 'days', 1), 'size=2 maxlength=3')?>
          <?php _e('d', 'ucfeed') ?>
          <?php echo input_tag('campaign_frequency_h', _data_value($frequency, 'hours', 5), 'size=2 maxlength=2')?>
          <?php _e('h', 'ucfeed') ?>
          <?php echo input_tag('campaign_frequency_m', _data_value($frequency, 'minutes', 0), 'size=2 maxlength=2')?>
          <?php _e('m', 'ucfeed') ?>
          <p class="note">
            <?php _e('How often should feeds be checked? (days, hours and minutes)', 'ucfeed') ?>
          </p>
        </div>
        <div class="checkbox"> <?php echo label_for('campaign_cacheimages', __('Cache images', 'ucfeed')) ?> <?php echo checkbox_tag('campaign_cacheimages', 1, _data_value($data['main'], 'cacheimages', is_writable($this->cachepath))) ?>
          <p class="note">
            <?php _e('Images will be stored in your server, instead of hotlinking from the original site.', 'ucfeed') ?>
            <a href="<?php echo $this->helpurl ?>image_caching" class="help_link">
            <?php _e('More', 'ucfeed') ?>
            </a></p>
        </div>
        <div class="checkbox"> <?php echo label_for('campaign_feeddate', __('Use feed date', 'ucfeed')) ?> <?php echo checkbox_tag('campaign_feeddate', 1, _data_value($data['main'], 'feeddate', false)) ?>
          <p class="note">
            <?php _e('Use the original date from the post instead of the time the post is created by Ultimate-Coupon-Feed.', 'ucfeed') ?>
            <a href="<?php echo $this->helpurl ?>feed_date_option" class="help_link">
            <?php _e('More', 'ucfeed') ?>
            </a></p>
        </div>
        <div class="checkbox"> <?php echo label_for('campaign_dopingbacks', __('Perform pingbacks', 'ucfeed')) ?> <?php echo checkbox_tag('campaign_dopingbacks', 1, _data_value($data['main'], 'dopingbacks', false)) ?> </div>
        <div class="radio">
          <label class="main">
            <?php _e('Type of post to create', 'ucfeed')?>
          </label>
          <?php echo radiobutton_tag('campaign_posttype', 'publish', !isset($data['main']['posttype']) || _data_value($data['main'], 'posttype') == 'publish', 'id=type_published') ?><?php echo label_for('type_published', __('Published', 'ucfeed')) ?> <?php echo radiobutton_tag('campaign_posttype', 'private', _data_value($data['main'], 'posttype') == 'private', 'id=type_private') ?> <?php echo label_for('type_private', __('Private', 'ucfeed')) ?> <?php echo radiobutton_tag('campaign_posttype', 'draft',!isset($data['main']['posttype']) || _data_value($data['main'], 'posttype') == 'draft', 'id=type_draft') ?> <?php echo label_for('type_draft', __('Draft', 'ucfeed')) ?> </div>
        <div class="text"> <?php echo label_for('campaign_author', __('Author:', 'ucfeed')) ?> <?php echo select_tag('campaign_author', options_for_select($author_usernames, _data_value($data['main'], 'author', 'admin'))) ?>
          <p class="note">
            <?php _e("The created posts will be assigned to this author.", 'ucfeed') ?>
          </p>
        </div>
        <div class="text required"> <?php echo label_for('campaign_max', __('Max items to create on each fetch', 'ucfeed')) ?> <?php echo input_tag('campaign_max', _data_value($data['main'], 'max', '10'), 'size=2 maxlength=3') ?>
          <p class="note">
            <?php _e("Set it to 0 for unlimited. If set to a value, only the last X items will be selected, ignoring the older ones.", 'ucfeed') ?>
          </p>
        </div>
        <div class="checkbox"> <?php echo label_for('campaign_linktosource', __('Post title links to source?', 'ucfeed')) ?> <?php echo checkbox_tag('campaign_linktosource', 1, _data_value($data['main'], 'linktosource', false)) ?> </div>
        <div class="radio">
          <label class="main">
            <?php _e('Discussion options:', 'ucfeed')?>
          </label>
          <?php echo select_tag('campaign_commentstatus', 

                        options_for_select(

                          array('open' => __('Open', 'ucfeed'), 

                                'closed' => __('Closed', 'ucfeed'), 

                                'registered_only' => __('Registered only', 'registered_only')

                                ), _data_value($data['main'], 'comment_status', 'open'))) ?> <?php echo checkbox_tag('campaign_allowpings', 1, _data_value($data['main'], 'allowpings', true)) ?> <?php echo label_for('campaign_allowpings', __('Allow pings', 'ucfeed')) ?> </div>
      </div>
      <?php if(isset($campaign_edit)): ?>
      <!-- Tools -->
      <?php endif ?>
    </div>
  </form>
</div>
<?php $this->adminFooter() ?>
