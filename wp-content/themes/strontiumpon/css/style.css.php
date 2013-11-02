<?php

require '../../../../wp-load.php';

header('Content-Type: text/css');

if (function_exists('ob_start') && function_exists('ob_end_flush')) {
	ob_start('ob_gzhandler');
}

include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '1.base.css';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '1.required.css';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '2.layout.css';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '3.header.css';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '4.navigation.css';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '5.content.css';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '6.pagination.css';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '7.sidebar.css';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '9.footer.css';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'jquery.slideshow.css.php';

if (function_exists('ob_start') && function_exists('ob_end_flush')) {
	ob_end_flush();
}