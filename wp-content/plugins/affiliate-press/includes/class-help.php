<?php

if( !class_exists( 'LDB_Affiliate_Press_Help' ) ) {

	class LDB_Affiliate_Press_Help {

		function AP_loadHelp() {
			$page = $_GET['page'];
			$tabs = array();
			switch( $page ){
				case 'affiliate_press':
					$tabs[] = array(
						'id' => 'dashboard',
						'title' => __( 'Dashboard', 'LDB_AP' ),
						'content' => '<p>' . __( 'This screen provides a quick overview of the status of Affiliate Press. It shows you how many products and feeds you have and how many products have no, one or multiple prices attached to them.', 'LDB_AP' ) . '</p>'
					);
				break;
				case 'affiliate_press_feeds':
					$tabs[] = array(
						'id' => 'feeds',
						'title' => __( 'Feeds', 'LDB_AP' ),
						'content' => '<p>' . __( "On this page you'll find an overview if all the feeds you've added to Affiliate Press. Per feed you'll be able to:", 'LDB_AP' ) . '</p><ul><li>' . __( 'View the feed', 'LDB_AP' ) . '</li><li>' . __( 'Edit the feed', 'LDB_AP' ) . '</li><li>' . __( 'Process the feed', 'LDB_AP' ) . '</li><li>' . __( 'Delete the feed', 'LDB_AP' ) . '</li></ul><p>' . __( 'You can also choose to add a new feed.', 'LDB_AP' ) . '</p>'
					);
					$tabs[] = array(
						'id' => 'process',
						'title' => __( 'Processing a feed', 'LDB_AP' ),
						'content' => '<p>' . __( 'When processing a feed it is actually fetched, temporarily stored in the database and then processed item by item.', 'LDB_AP' ) . '</p><p>' . __( "First all prices related to the feed are disabled. Then for every item in the product feed it is checked if it's linked to a product, through the identifier and the custom field selected for this feed, updating the existing price or inserting it if it's a new one. In both cases the price is being enabled (again) to be shown in the frontend.", 'LDB_AP' ) . '</p>'
					);
				break;
				case 'affiliate_press_add':
					$tabs[] = array(
						'id' => 'add',
						'title' => __( 'Adding a feed', 'LDB_AP' ),
						'content' => '<p>' . __( 'In this screen you can easily add a product feed to Affiliate Press. The XPath items should all be valid XPath paths.', 'LDB_AP' ) . '</p><p>' . __( 'Since the name, image, price, link and identifier XPaths should be relative to the item XPath they will have to start with ".//".', 'LDB_AP' ) . '</p><p>' . __( 'Looking to learn more about XPath? Google is your friend!', 'LDB_AP' ) . '</p>'
					);
				break;
				case 'affiliate_press_add_wizard':
					$tabs[] = array(
						'id' => 'add',
						'title' => __( 'Adding a feed', 'LDB_AP' ),
						'content' => '<p>' . __( 'Adding a feed through the wizard should allow everyone to add a feed to Affiliate Press with the greatest of ease. It really speaks for itself.', 'LDB_AP' ) . '</p><p>' . sprintf( __( "To improve the wizard I'm stil looking for more examples of product feeds. If you would like to help please send me the link to your product feed or send it as an attachment to <a href='%s'>%s</a>. Thank you in advance!", 'LDB_AP' ), 'mailto:affiliatepress@lucdebrouwer.nl', 'affiliatepress@lucdebrouwer.nl' ) . '</p>'
					);
				break;
				case 'affiliate_press_edit':
					$tabs[] = array(
						'id' => 'edit',
						'title' => __( 'Editing a feed', 'LDB_AP' ),
						'content' => '<p>' . __( 'In this screen you can easily edit a product feed. The XPath items should all be valid XPath paths.', 'LDB_AP' ) . '</p><p>' . __( 'Since the name, image, price, link and identifier XPaths should be relative to the item XPath they will have to start with ".//".', 'LDB_AP' ) . '</p><p>' . __( 'Looking to learn more about XPath? Google is your friend!', 'LDB_AP' ) . '</p>'
					);
				break;
				case 'affiliate_press_view':
					$tabs[] = array(
						'id' => 'view',
						'title' => __( 'Viewing a feed', 'LDB_AP' ),
						'content' => '<p>' . __( "In this screen you'll find an overview of all the items currently in the product feed as defined by the XPaths you entered when adding the feed.", 'LDB_AP' ) . '</p>'
					);
					$tabs[] = array(
						'id' => 'draft',
						'title' => __( 'Creating a draft', 'LDB_AP' ),
						'content' => '<p>' . __( 'When you create a draft from a product feed item you essentially create a new product ( custom post type ) as a draft after with the name of the item as the title. Affiliate Press also attempts to fetch the image belonging to the item and setting that as the featured image for the product. After that it links the price to the product.', 'LDB_AP' ) . '</p>'
					);
					$tabs[] = array(
						'id' => 'linking',
						'title' => __( 'Linking to an existing product', 'LDB_AP' ),
						'content' => '<p>' . __( "When you want to link a certain item to an existing product you're essentially creating a new custom field for this product, named after whatever you set for the feed's 'matches' field, containing the item's identifier. This effectively links the product feed item, and it's price and affiliate link to the product.", 'LDB_AP' ) . '</p>'
					);
				break;
				case 'affiliate_press_linktoproduct':
					$tabs[] = array(
						'id' => 'linking',
						'title' => __( 'Linking to an existing product', 'LDB_AP' ),
						'content' => '<p>' . __( "When you want to link a certain item to an existing product you're essentially creating a new custom field for this product, named after whatever you set for the feed's 'matches' field, containing the item's identifier. This effectively links the product feed item, and it's price and affiliate link to the product.", 'LDB_AP' ) . '</p>'
					);
				break;
			}
			foreach( $tabs as $tab )
				get_current_screen()->add_help_tab( $tab );

		}
	}
}