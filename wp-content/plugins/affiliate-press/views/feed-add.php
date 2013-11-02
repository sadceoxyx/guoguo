<div class="wrap">
    <div class="icon32" id="icon-feeds"><br /></div><h2><?php echo __('Add feed', 'LDB_AP'); ?></h2>
    <div id="ap_body">
        <div id="ap_main">
            <form action="?page=affiliate_press_feeds" method="post">
                <?php wp_nonce_field('addfeed', 'wp_nonce_add'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="title"><?php echo __('Title', 'LDB_AP'); ?></label></th>
                        <td><input name="title" type="text" id="title" value="" class="regular-text" /><br /><span class="description"><?php echo __('The title for this feed.', 'LDB_AP'); ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="currency"><?php echo __('Currency', 'LDB_AP'); ?></label></th>
                        <td><input name="currency" type="text" id="currency" value="" class="regular-text" /><br /><span class="description"><?php echo __('The currency that applies to the prices in this feed. ', 'LDB_AP'); ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="url"><?php echo __('URL', 'LDB_AP'); ?></label></th>
                        <td><input name="url" type="text" id="url" value="" class="regular-text" /><br /><span class="description"><?php echo __('The URL for the product feed.', 'LDB_AP'); ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="item_xpath"><?php echo __('Item XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="item_xpath" type="text" id="item_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to a product within the feed.', 'LDB_AP'); ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="name_xpath"><?php echo __('Name XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="name_xpath" type="text" id="name_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product name.', 'LDB_AP'); ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="image_xpath"><?php echo __('Image XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="image_xpath" type="text" id="image_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product image.', 'LDB_AP'); ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="price_xpath"><?php echo __('Price XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="price_xpath" type="text" id="price_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product price.', 'LDB_AP'); ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="link_xpath"><?php echo __('Link XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="link_xpath" type="text" id="link_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product link.', 'LDB_AP'); ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="identifier_xpath"><?php echo __('Identifier XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="identifier_xpath" type="text" id="identifier_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product identifier.', 'LDB_AP'); ?></span></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="description_xpath"><?php echo __('Description XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="description_xpath" type="text" id="description_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product description.', 'LDB_AP'); ?></span></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="msrp_xpath"><?php echo __('MSRP XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="msrp_xpath" type="text" id="msrp_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product msrp.', 'LDB_AP'); ?></span></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="saving_rate_xpath"><?php echo __('Saving Rate XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="saving_rate_xpath" type="text" id="saving_rate_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product saving rate.', 'LDB_AP'); ?></span></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="store_xpath"><?php echo __('Store XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="store_xpath" type="text" id="store_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product selling store.', 'LDB_AP'); ?></span></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="hot_xpath"><?php echo __('HOT XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="hot_xpath" type="text" id="hot_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product whather hot or not.', 'LDB_AP'); ?></span></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="free_shipping_xpath"><?php echo __('Free Shipping XPath', 'LDB_AP'); ?></label></th>
                        <td><input name="free_shipping_xpath" type="text" id="free_shipping_xpath" value="" class="regular-text" /><br /><span class="description"><?php echo __('The XPath to the product\'s free shipping status.', 'LDB_AP'); ?></span></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="matches"><?php echo __('Matches', 'LDB_AP'); ?></label></th>
                        <td><?php echo __('Existing custom field', 'LDB_AP'); ?> : <?php echo $matches; ?><br /><?php echo __('Or a new field', 'LDB_AP'); ?> : <input name="new_matches" type="text" id="new_matches" value="" class="regular-text" /><br /><span class="description"><?php echo __('The custom field for a product that should match with the product identifier.', 'LDB_AP'); ?></span></td>
                    </tr>
                </table>
                <p class="submit"><input type="submit" value="<?php echo __('Save feed'); ?>" class="button-primary" id="submit" name="submit"></p>
            </form>
        </div>
        <?php $this->AP_sidebar(); ?>
    </div>
</div>