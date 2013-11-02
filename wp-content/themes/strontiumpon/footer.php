
		</div>
	</div>
	
	<div id="footer">
		<div id="footer-pad" class="append-clear">
			<div class="inner">
				<p class="copyright">
					Copyright &copy; <?php echo date('Y')?>. <?php bloginfo('name'); _e('. All rights reserved.'); ?> 
				</p>
				<?php padd_theme_credits(); ?>
			</div>
		</div>
	</div>

	</div>
</div>
<?php wp_footer(); ?>
<?php
$tracker = get_option(PADD_PREFIX . '_tracker_bot','');
if (!empty($tracker)) {
	echo stripslashes($tracker);
}
?>
</body>
</html>

<?php require 'includes/required/template-bot.php'; ?>
