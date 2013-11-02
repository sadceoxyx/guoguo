<?php $title = __('Post Templates', 'ucfeed') ?>
<h2><?php _e('Post Templates', 'ucfeed') ?></h2>

<h3><?php _e('Basics', 'ucfeed') ?></h3>
<p><?php _e('Ultimate-Coupon-Feed takes the full text of each feed item it encounters, and then uses it as the post content. A post template, if used, allows you to alter that content, by adding extra information, such as text, images, campaign data, etc.', 'ucfeed') ?></p>

<h3><?php _e('Supported tags', 'ucfeed') ?></h3>
<a href="#" class="link_top">&uarr; <?php _e('top', 'ucfeed') ?></a>
<p><?php _e('A tag is a piece of text that gets replaced dynamically when the post is created. Currently, these tags are supported:', 'ucfeed') ?></p>

<ul>
  <li><strong>{content}</strong> <?php _e('The feed item content', 'ucfeed') ?></li>
  <li><strong>{title}</strong> <?php _e('The feed item title', 'ucfeed') ?></li>
  <li><strong>{permalink}</strong> <?php _e('The feed item permalink', 'ucfeed') ?></li>
  <li><strong>{feedurl}</strong> <?php _e('The feed URL', 'ucfeed') ?></li>
  <li><strong>{feedtitle}</strong> <?php _e('The feed title', 'ucfeed') ?></li>
  <li><strong>{feedlogo}</strong> <?php _e('The feed logo image', 'ucfeed') ?></li>
  <li><strong>{campaigntitle}</strong> <?php _e('This campaign title', 'ucfeed') ?></li>
  <li><strong>{campaignid}</strong> <?php _e('This campaign id', 'ucfeed') ?></li>
  <li><strong>{campaignslug}</strong> <?php _e('This campaign slug', 'ucfeed') ?></li>
</ul>

<h3><?php _e('Example', 'ucfeed') ?></h3>
<a href="#" class="link_top">&uarr; <?php _e('top', 'ucfeed') ?></a>
<p><a href="<?php echo $PHP_SELF ?>?item=post_templates_examples" class="link_main"><?php _e('Main article', 'ucfeed')  ?></a></p>
<p><?php _e('If you want to add a link to the source at the bottom of every post, the post template would look like this:') ?></p>
<div class="code"><p>{content}<br />&lt;a href=&quot;{feedurl}&quot;&gt;Go to Source&lt;/a&gt;</p></div>
<p><?php _e('The <em>{content}</em> string gets replaced by the feed content, and then {feedurl} by the url, which makes it a working link.') ?></p>
