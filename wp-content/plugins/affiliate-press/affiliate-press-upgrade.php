<?php

global $wpdb;
$v = get_option('LDB_Affiliate_Press_Version', 0);

if (true) {
    $table = $wpdb->prefix . 'apfeeds';
    $sql = "CREATE TABLE IF NOT EXISTS $table (
				ID int(11) NOT NULL AUTO_INCREMENT,
				title varchar(256) NOT NULL,
				currency varchar(12) NOT NULL,
				url varchar(512) NOT NULL,
				item_xpath longtext NOT NULL,
				name_xpath longtext NOT NULL,
				price_xpath longtext NOT NULL,
				link_xpath longtext NOT NULL,
				identifier_xpath longtext NOT NULL,
                                description_xpath longtext NOT NULL,
                                msrp_xpath longtext NOT NULL,
                                saving_rate_xpath longtext NOT NULL,
                                store_xpath longtext NOT NULL, 
                                hot_xpath bit NOT NULL,
                                free_shipping_xpath bit NOT NULL,
				matches varchar(256) NOT NULL,
				PRIMARY KEY (ID)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $wpdb->query($sql);

    $table = $wpdb->prefix . 'apprices';
    $sql = "CREATE TABLE IF NOT EXISTS $table (
				ID int(11) NOT NULL AUTO_INCREMENT,
				productID int(11) NOT NULL,
				feedID int(11) NOT NULL,
				price float(10,2) NOT NULL,
				link varchar(512) NOT NULL,
				online int(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (ID)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $wpdb->query($sql);
}

if (true) {
    $table = $wpdb->prefix . 'apfeeds';
    $sql = "ALTER TABLE $table ADD image_xpath longtext NOT NULL AFTER name_xpath;";
    $wpdb->query($sql);
}