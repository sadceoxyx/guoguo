<div class="wrap">
	<div class="icon32" id="icon-feeds"><br /></div><h2><?php echo __( 'Add feed wizard', 'LDB_AP' ); ?> - <?php echo sprintf( __( 'Step %d', 'LDB_AP' ), 1 ); ?></h2>
	<?php $this->AP_getMessage(); ?>
	<div id="ap_body">
		<div id="ap_main">
			<form action="?page=affiliate_press_add_wizard" method="post">
				<input type="hidden" name="step" id="step" value="1" />
				<table class="form-table">
					<tr>
						<th scope="row"><label for="title"><?php echo __( 'Title', 'LDB_AP' ); ?></label></th>
						<td><input name="title" type="text" id="title" value="" class="regular-text" /><br /><span class="description"><?php echo __( 'The title for this feed.', 'LDB_AP' ); ?></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="currency"><?php echo __( 'Currency', 'LDB_AP' ); ?></label></th>
						<td><input name="currency" type="text" id="currency" value="" class="regular-text" /><br /><span class="description"><?php echo __( 'The currency that applies to the prices in this feed. ', 'LDB_AP' ); ?></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="url"><?php echo __( 'URL', 'LDB_AP' ); ?></label></th>
						<td><input name="url" type="text" id="url" value="" class="regular-text" /><br /><span class="description"><?php echo __( 'The URL for the product feed.', 'LDB_AP' ); ?></span></td>
					</tr>
				</table>
				<p class="submit"><input type="submit" value="<?php echo __( 'Next step' ); ?>" class="button-primary" id="submit" name="submit"></p>
			</form>
		</div>
		<?php $this->AP_sidebar(); ?>
	</div>
</div>