<?php $title = __('Feed Date option', 'ucfeed') ?>
<h2><?php _e('Feed Date option', 'ucfeed') ?></h2>

<h3><?php _e('Basics', 'ucfeed') ?></h3>
<p><?php _e('By default, Ultimate-Coupon-Feed sets the date and time of the posts to that of the moment they\'re created. By using this option, however, you can alter that behavior.', 'ucfeed') ?></p>

<h3><?php _e('How it works', 'ucfeed') ?></h3>
<p><?php _e('To avoid incoherent dates due to lousy setup feeds, Ultimate-Coupon-Feed will use the feed date only if these conditions are met:', 'ucfeed') ?></p>
<ul>
  <li>First, of course, if the option is enabled in the campaign settings</li>
  <li>The feed item date is not is not too far in the past (specifically, as much time as the campaign frequency)</li>
  <li>The feed item date is not in the future</li>
</ul>