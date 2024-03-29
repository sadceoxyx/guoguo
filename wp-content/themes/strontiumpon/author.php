<?php
/*
Template Name: Archive
*/
?>
<?php get_header(); ?>

<div id="content" class="content-group content-author">
	<div id="content-pad">
		<div class="post-group">
			<?php the_post(); ?>
			<div class="content-title">
				<h1 class="title"><?php _e('Posts By'); the_author(); ?></h1>
			</div>
			<?php rewind_posts(); ?>		
			<?php get_template_part('loop','archive'); ?>
		</div>
	</div>
</div>
		
<?php get_sidebar(); ?>

<?php get_footer(); ?>