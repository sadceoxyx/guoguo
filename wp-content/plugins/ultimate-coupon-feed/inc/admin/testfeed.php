<?php if($works): ?>
<?php printf(__('The feed %s has been parsed successfully.', 'ucfeed'), $url) ?>
<?php else: ?>
<?php printf(__('The feed %s cannot be parsed. Simplepie said: %s', 'ucfeed'), $url, $works) ?>
<?php endif ?>