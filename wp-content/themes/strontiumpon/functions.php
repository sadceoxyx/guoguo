<?php

define('PADD_THEME_NAME','Strontiumpon');
define('PADD_THEME_VERS','1.0');
define('PADD_THEME_SLUG','strontiumpon');
define('PADD_NAME_SPACE','padd');
define('PADD_GALL_THUMB_W',133);
define('PADD_GALL_THUMB_H',133);
define('PADD_LIST_THUMB_W',125);
define('PADD_LIST_THUMB_H',125);
define('PADD_YTUBE_W',300);
define('PADD_YTUBE_H',250);
define('PADD_THEME_FWVER','2.7.1');

define('PADD_THEME_PATH', get_stylesheet_directory());
define('PADD_FUNCT_PATH', PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR);

require PADD_FUNCT_PATH . 'classes' . DIRECTORY_SEPARATOR . 'socialnetwork.php';
require PADD_FUNCT_PATH . 'classes' . DIRECTORY_SEPARATOR . 'socialbookmark.php';
require PADD_FUNCT_PATH . 'classes' . DIRECTORY_SEPARATOR . 'widgets.php';
require PADD_FUNCT_PATH . 'classes' . DIRECTORY_SEPARATOR . 'twitter.php';
require PADD_FUNCT_PATH . 'classes' . DIRECTORY_SEPARATOR . 'pagination.php';
require PADD_FUNCT_PATH . 'classes' . DIRECTORY_SEPARATOR . 'input' . DIRECTORY_SEPARATOR . 'input-option.php';
require PADD_FUNCT_PATH . 'classes' . DIRECTORY_SEPARATOR . 'input' . DIRECTORY_SEPARATOR . 'input-socialnetwork.php';

require PADD_FUNCT_PATH . 'administration' . DIRECTORY_SEPARATOR . 'options-functions.php';
require PADD_FUNCT_PATH . 'administration' . DIRECTORY_SEPARATOR . 'posting-functions.php';

require PADD_FUNCT_PATH . 'defaults.php';
require PADD_FUNCT_PATH . 'library.php';
require PADD_FUNCT_PATH . 'hooks.php';
require PADD_FUNCT_PATH . 'setup.php';
