<div class="wrap">
	<div class="icon32" id="icon-feeds"><br /></div><h2><?php echo __( 'Add feed wizard', 'LDB_AP' ); ?> - <?php echo sprintf( __( 'Step %d', 'LDB_AP' ), 3 ); ?></h2>
	<?php $this->AP_getMessage(); ?>
	<div id="ap_body">
		<div id="ap_main">
			<form action="?page=affiliate_press_feeds" method="post">
				<?php wp_nonce_field( 'addfeed', 'wp_nonce_add' ); ?>
				<input type="hidden" name="step" id="step" value="3" />
				<input type="hidden" name="title" id="title" value="<?php echo $_POST['title']; ?>" />
				<input type="hidden" name="currency" id="currency" value="<?php echo $_POST['currency']; ?>" />
				<input type="hidden" name="url" id="url" value="<?php echo $_POST['url']; ?>" />
				<input type="hidden" name="item_xpath" id="item_xpath" value="<?php echo $_POST['item_xpath']; ?>" />
				<table class="form-table">
					<tr>
						<th scope="row"><label for="name_xpath"><?php echo __( 'Name XPath', 'LDB_AP' ); ?></label></th>
						<td><?php echo $xpath['name']; ?><br /><span class="description"><?php echo __( 'The XPath to the product name.', 'LDB_AP' ); ?></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="image_xpath"><?php echo __( 'Image XPath', 'LDB_AP' ); ?></label></th>
						<td><?php echo $xpath['image']; ?><br /><span class="description"><?php echo __( 'The XPath to the product image.', 'LDB_AP' ); ?></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="price_xpath"><?php echo __( 'Price XPath', 'LDB_AP' ); ?></label></th>
						<td><?php echo $xpath['price']; ?><br /><span class="description"><?php echo __( 'The XPath to the product price.', 'LDB_AP' ); ?></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="link_xpath"><?php echo __( 'Link XPath', 'LDB_AP' ); ?></label></th>
						<td><?php echo $xpath['link']; ?><br /><span class="description"><?php echo __( 'The XPath to the product link.', 'LDB_AP' ); ?></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="identifier_xpath"><?php echo __( 'Identifier XPath', 'LDB_AP' ); ?></label></th>
						<td><?php echo $xpath['identifier']; ?><br /><span class="description"><?php echo __( 'The XPath to the product identifier.', 'LDB_AP' ); ?></span></td>
					</tr>
                                        <tr>
						<th scope="row"><label for="description_xpath"><?php echo __( 'Description XPath', 'LDB_AP' ); ?></label></th>
						<td><?php echo $xpath['description']; ?><br /><span class="description"><?php echo __( 'The XPath to the product description.', 'LDB_AP' ); ?></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="matches"><?php echo __( 'Matches', 'LDB_AP' ); ?></label></th>
						<td><?php echo __( 'Existing custom field', 'LDB_AP' ); ?> : <?php echo $matches; ?><br /><?php echo __( 'Or a new field', 'LDB_AP' ); ?> : <input name="new_matches" type="text" id="new_matches" value="" class="regular-text" /><br /><span class="description"><?php echo __( 'The custom field for a product that should match with the product identifier.', 'LDB_AP' ); ?></span></td>
					</tr>
				</table>
				<p class="submit"><input type="submit" value="<?php echo __( 'Save feed' ); ?>" class="button-primary" id="submit" name="submit"></p>
			</form>
		</div>
		<?php $this->AP_sidebar(); ?>
	</div>
</div>