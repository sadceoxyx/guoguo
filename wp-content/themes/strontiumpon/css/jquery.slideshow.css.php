

/* = jquery.slideshow.css
--------------------------------------------------- */


/* = Featured Content
--------------------------------------------------- */

div#featured {
	margin: 0 0 10px 0;
}
div#featured .title {
	border-bottom: 1px solid #9d9d9d;
	padding: 6px;
        background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#333), to(#555));
        background: -webkit-linear-gradient(top, #555, #333);
        background: -moz-linear-gradient(top, #555, #333);
        background: -ms-linear-gradient(top, #555, #333);
        background: -o-linear-gradient(top, #555, #333);
        border-radius: 5px;
}
div#featured .title h2 {
        display: block;
	padding: 2px; 
	margin: 0;
	color: #fff;
        font-weight: bolder;
        font:15px/19px "Helvetica Neue", Helvetica, Geogia, serif;
}
div#featured .interior {
	padding: 13px 0;
<!--	background: #ce9639 none;-->
}


/* = Slide Show
--------------------------------------------------- */

div#slideshow .jcarousel-container {
	width: 625px;
	margin: 0 auto;
	position: relative;
}
div#slideshow .jcarousel-container .jcarousel-clip {
	width: 531px;
	margin: 0 auto;
}
div#slideshow .jcarousel-container .jcarousel-prev {
	position: absolute;
     top: 64px;
     left: 0;
     width: 38px;
     height: 52px;
     background: transparent url('../images/slideshow/arrow-l.png') left top no-repeat;
     cursor: pointer;
}
div#slideshow .jcarousel-container .jcarousel-next {
	position: absolute;
	top: 64px;
	right: 0;
	width: 38px;
	height: 52px;
	background: transparent url('../images/slideshow/arrow-r.png') right top no-repeat;
	cursor: pointer;
}
div#slideshow ul.list {
	display: block;
	margin: 0;
	padding: 0;
	list-style-type: none;
}
div#slideshow ul.list li {
	display: block; 
	margin: 0;
	padding: 7px;
}
div#slideshow ul.list li a {
	display: block; 
	margin: 0;
	padding: 0;
	border: 0 none;
}
div#slideshow ul.list li a img {
	padding: 15px;
	width: <?php echo PADD_GALL_THUMB_W; ?>px;
	height: <?php echo PADD_GALL_THUMB_H; ?>px;
	background: transparent url('../images/slideshow/bg-featured-image.png') left top no-repeat;
}