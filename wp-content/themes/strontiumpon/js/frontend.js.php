<?php

require '../../../../wp-load.php';

header('Content-Type: text/javascript');

if (function_exists('ob_start') && function_exists('ob_end_flush')) {
	ob_start('ob_gzhandler');
}

include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'jquery.cookie.js';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'jquery.superfish.js';
include PADD_THEME_PATH . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'jquery.jcarousel.js';

?>

function padd_append_clear() {
	jQuery('.append-clear').append('<div class="clear"></div>');
}

function padd_toggle(classname,value) {
	jQuery(classname).focus(function() {
		if (value == jQuery(classname).val()) {
			jQuery(this).val('');
		}
	});
	jQuery(classname).blur(function() {
		if ('' == jQuery(classname).val()) {
			jQuery(this).val(value);
		}
	});
}

function padd_slideshow_init(carousel) {
	carousel.clip.hover(function() {
		carousel.stopAuto();
	}, function() {
		carousel.startAuto();
	});
}

function padd_create_slideshow() {
	jQuery('#slideshow > .list').jcarousel({
		auto: <?php echo get_option(PADD_NAME_SPACE . '_featured_slide_wait','5'); ?>,
		animation: <?php echo get_option(PADD_NAME_SPACE . '_featured_slide_speed','1000'); ?>,
		wrap: 'circular',
		initCallback: padd_slideshow_init
	});
}


jQuery(document).ready(function() {
	jQuery.noConflict();
	
	jQuery('div#menubar div > ul').superfish({
		autoArrows: false,
		hoverClass: 'hover',
		speed: 500,
		animation: { opacity: 'show', height: 'show' }
	});
	jQuery('div#menubar div > ul > li:last-child').css({
		'background': 'transparent none',
		'padding-right' : '0'
	});

	padd_append_clear();
	padd_create_slideshow();
	
	jQuery('p.coupon a').click(function() {
		window.open(jQuery(this).attr('name'));
	});
	
	jQuery('input#s').val('Find a coupon');
	padd_toggle('input#s','Find a coupon');

	jQuery('div.search form').click(function () {
		jQuery('input#s').focus();
	});

});

<?php

if (function_exists('ob_start') && function_exists('ob_end_flush')) {
	ob_end_flush();
}
