
<div id="sidebar">
	<div id="sidebar-pad">

		<h2>Sidebar</h2>

		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar') ) : ?>
	
		<div class="box box-ads">
			<div class="title">
				<h3>Sponsors</h3>
			</div>
			<div class="interior">
				<?php padd_theme_widget_sponsors(); ?>
			</div>
		</div>
		
		<div class="box box-socialnet">
			<div class="title">
				<h3>Subscribe</h3>
			</div>
			<div class="interior">
				<?php padd_theme_widget_socialnet(); ?>
			</div>
		</div>
		
		<div class="box box-popular-posts">
			<div class="title">
				<h3>Popular Coupons</h3>
			</div>
			<div class="interior">
				<?php 
					if (function_exists('get_mostpopular')) {
						get_mostpopular('pages=0&stats_comments=1&range=all&limit=5&thumbnail_width=57&thumbnail_height=57&do_pattern=1&pattern_form={image}{title}{stats}');
					} else {
						echo '<p style="padding: 10px 0 0 0; color: #fff;">Please install the <a href="http://wordpress.org/extend/plugins/wordpress-popular-posts/">Wordpress Popular Posts plugin</a>.</p>';
					}
				?>
			</div>
		</div>
		
		<div class="box box-categories">
			<div class="title">
				<h3>Categories</h3>
			</div>
			<div class="interior">
				<ul>
					<?php wp_list_categories('title_li='); ?>
				</ul>
			</div>
		</div>
		
		<div class="box box-fb-like">
			<div class="title">
				<h3>Facebook Like Box</h3>
			</div>
			<div class="interior">
				<?php padd_theme_widget_facebook_likebox(); ?>
			</div>
		</div>


		<?php endif; ?>

	</div>
</div>


