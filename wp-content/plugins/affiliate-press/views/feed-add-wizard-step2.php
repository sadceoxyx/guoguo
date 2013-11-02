<div class="wrap">
	<div class="icon32" id="icon-feeds"><br /></div><h2><?php echo __( 'Add feed wizard', 'LDB_AP' ); ?> - <?php echo sprintf( __( 'Step %d', 'LDB_AP' ), 2 ); ?></h2>
	<?php $this->AP_getMessage(); ?>
	<div id="ap_body">
		<div id="ap_main">
			<form action="?page=affiliate_press_add_wizard" method="post">
				<input type="hidden" name="step" id="step" value="2" />
				<input type="hidden" name="title" id="title" value="<?php echo $_POST['title']; ?>" />
				<input type="hidden" name="currency" id="currency" value="<?php echo $_POST['currency']; ?>" />
				<input type="hidden" name="url" id="url" value="<?php echo $_POST['url']; ?>" />
				<table class="form-table">
					<tr>
						<th scope="row"><label for="item_xpath"><?php echo __( 'Item XPath', 'LDB_AP' ); ?></label></th>
						<td><?php echo $item_xpath; ?><br /><span class="description"><?php echo __( 'The XPath to a product within the feed.', 'LDB_AP' ); ?></span></td>
					</tr>
				</table>
				<p class="submit"><input type="submit" value="<?php echo __( 'Next step' ); ?>" class="button-primary" id="submit" name="submit"></p>
			</form>
		</div>
		<?php $this->AP_sidebar(); ?>
	</div>
</div>