<?php

if (!class_exists('LDB_Affiliate_Press')) {

    class LDB_Affiliate_Press {
        /* Set the main variables */

        public $messages = array();
        public $action = false;
        public $titles = array();

        /* Load the custom post type, menu, meta box, stylesheet. Perform actions if needed. And assign something to the cronjob. */

        function __construct() {
            add_filter('cron_schedules', array(&$this, 'cron_add_min'));
            add_action('init', array(&$this, 'AP_loadCpt'));
            add_action('AP_create_post', array(&$this, 'AP_post_feed'));
            add_action('admin_menu', array(&$this, 'AP_menu'));
            add_action('admin_init', array(&$this, 'AP_addCustomMetaBox'));
            add_action('admin_print_scripts-post-new.php', array(&$this, 'AP_loadStyle'));
            add_action('admin_print_scripts-post.php', array(&$this, 'AP_loadStyle'));
            add_action('admin_init', array(&$this, 'AP_performAction'));
            add_action('AP_cronjob', array(&$this, 'AP_processAllPrices'));
            add_theme_support('post-thumbnails');
        }

        function cron_add_min() {
            // Adds once weekly to the existing schedules.
            //return array('min' => array('interval' => 20, 'display' => 'One min'));
            $schedules['min'] = array(
                'interval' => 20,
                'display' => __('Min')
            );
            return $schedules;
        }

        /* Add the menu items and add some hidden ones. */

        function AP_menu() {
            $help = new LDB_Affiliate_Press_Help;
            $pages = array();
            $parent = add_menu_page(__('Affiliate Press', 'LDB_AP'), __('Affiliate Press', 'LDB_AP'), 'manage_options', 'affiliate_press', array(&$this, 'AP_dashboard'), LDB_AP_URL . 'images/icon16.png');
            $pages[] = $parent;
            add_submenu_page('affiliate_press', __('Dashboard', 'LDB_AP'), __('Dashboard', 'LDB_AP'), 'manage_options', 'affiliate_press');
            $pages[] = add_submenu_page('affiliate_press', __('Feeds', 'LDB_AP'), __('Feeds', 'LDB_AP'), 'manage_options', 'affiliate_press_feeds', array(&$this, 'AP_feeds'));
            $pages[] = add_submenu_page('affiliate_press', __('Interval', 'LDB_AP'), __('Interval', 'LDB_AP'), 'manage_options', 'affiliate_press_interval', array(&$this, 'AP_interval'));
            $pages[] = add_submenu_page('affiliate_press', __('Add New Feed', 'LDB_AP'), __('Add New Feed', 'LDB_AP'), 'manage_options', 'affiliate_press_add', array(&$this, 'AP_addFeed'));
            $pages[] = add_submenu_page('affiliate_press', __('Add New Feed Wizard', 'LDB_AP'), __('Add New Feed Wizard', 'LDB_AP'), 'manage_options', 'affiliate_press_add_wizard', array(&$this, 'AP_addFeedWizard'));
            foreach ($pages as $page) {
                add_action('admin_print_styles-' . $page, array(&$this, 'AP_loadStyle'));
                add_action('load-' . $page, array(&$help, 'AP_loadHelp'));
            }

            $hiddenpages = array(
                'affiliate_press_edit' => array(array(&$this, 'AP_editFeed'), __('Edit feed', 'LDB_AP')),
                'affiliate_press_view' => array(array(&$this, 'AP_viewFeed'), __('View feed', 'LDB_AP')),
                'affiliate_press_linktoproduct' => array(array(&$this, 'AP_linkToProduct'), __('Link item to an existing product', 'LDB_AP'))
            );
            $this->AP_registerHiddenPages($hiddenpages, $parent);
        }

        /* Function to register the hidden pages */

        function AP_registerHiddenPages($pages, $parent) {
            global $_registered_pages;
            $help = new LDB_Affiliate_Press_Help;
            foreach ($pages as $slug => $function) {
                $hookname = get_plugin_page_hookname($slug, $parent);
                $this->titles[$slug] = $function[1];
                if (!empty($hookname)) {
                    add_action($hookname, $function[0]);
                }
                $_registered_pages[$hookname] = true;
                add_action('admin_print_styles-' . $hookname, array(&$this, 'AP_loadStyle'));
                add_action('load-' . $hookname, array(&$help, 'AP_loadHelp'));
            }
            if (isset($_GET['page']) && isset($pages[$_GET['page']]) && isset($pages[$_GET['page']][1]))
                add_filter('admin_title', array(&$this, 'AP_changeTitle'), 10, 2);
        }

        /* Function to change the title on hidden pages */

        function AP_changeTitle($admin_title, $title) {
            if (isset($_GET['page']) && isset($this->titles[$_GET['page']]))
                return $this->titles[$_GET['page']] . $admin_title;
        }

        /* Function to load the stylesheet and some scripts. */

        function AP_loadStyle() {
            wp_enqueue_style('APstyle', LDB_AP_URL . 'styles.css');
            wp_enqueue_script('ap_message', LDB_AP_SCRIPTS_URL . 'message.js');
            wp_enqueue_script('postbox');
            wp_enqueue_script('dashboard');
        }

        /* Function to load the custom post type. */

        function AP_loadCpt() {
            $labels = array(
                'name' => __('Products', 'LDB_AP'),
                'singular_name' => __('Product', 'LDB_AP'),
                'add_new' => __('Add New', 'LDB_AP'),
                'add_new_item' => __('Add New Product', 'LDB_AP'),
                'edit_item' => __('Edit Product', 'LDB_AP'),
                'new_item' => __('New Product', 'LDB_AP'),
                'all_items' => __('All Products', 'LDB_AP'),
                'view_item' => __('View Product', 'LDB_AP'),
                'search_items' => __('Search Products', 'LDB_AP'),
                'not_found' => __('No products found', 'LDB_AP'),
                'not_found_in_trash' => __('No products found in Trash', 'LDB_AP'),
                'parent_item_colon' => '',
                'menu_name' => __('Products', 'LDB_AP')
            );
            $args = array(
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields'),
                'taxonomies' => array('category', 'post_tag')
            );
            register_post_type('product', $args); //xyx_need change.
            add_filter('manage_product_posts_columns', array(&$this, 'AP_changeColumns'));
            add_filter('manage_product_posts_custom_column', array(&$this, 'AP_addColumnContent'));
            add_filter('manage_edit-product_sortable_columns', array(&$this, 'AP_sortableColumns'));
        }

        /* Function that adds additional columns on the products index page. */

        function AP_changeColumns($cols) {
            $newcols = array();
            foreach ($cols as $key => $value) {
                $newcols[$key] = $value;
                if ($key === 'title')
                    $newcols['prices'] = __('Number of prices', 'LDB_AP');
            }
            return $newcols;
        }

        /* Function that adds content to the additional columns on the products index page. */

        function AP_addColumnContent($column_name) { //xyx_change
            global $post, $wpdb;
            switch ($column_name) {
                case 'prices' :
                    $results = $wpdb->get_results('SELECT apfeeds.title, apfeeds.currency, apprices.price FROM ' . $wpdb->prefix . 'apprices apprices, ' . $wpdb->prefix . 'apfeeds apfeeds WHERE apprices.productID = ' . $post->ID . ' AND apprices.feedID = apfeeds.ID AND apprices.online = 1 ORDER BY apprices.price ASC');
                    echo count($results);
                    break;
            }
        }

        /* Function that determines the sortable columns on the products index page. */

        function AP_sortableColumns() {
            return array(
                'prices' => 'prices',
                'title' => 'title',
                'author' => 'author',
                'date' => 'date',
            );
        }

        /* Function that fetches the current action, partly stolen from core. */

        function AP_currentAction() {
            $action = false;

            if (isset($_REQUEST['action']) && -1 != $_REQUEST['action'])
                $action = $_REQUEST['action'];

            if (isset($_REQUEST['action2']) && -1 != $_REQUEST['action2'])
                $action = $_REQUEST['action2'];

            if (!$action && isset($_GET['action']))
                $action = $_GET['action'];

            return $action;
        }

        /* Function to perform the actions issues by forms. */

        function AP_performAction() {
            global $wpdb;
            $action = $this->AP_currentAction();
            switch ($action) {
                case 'process':
//					if( isset( $_GET['feed'] ) && is_array( $_GET['feed'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'bulk-feeds' ) ) {
//						foreach( $_GET['feed'] as $feed ) {
//							$this->AP_processPrices( $feed );
//						}
//						$this->AP_setMessage( __( 'The feeds were processed succesfully.', 'LDB_AP' ), 'success' );
//					} else if( isset( $_GET['feed'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'process' . $_GET['feed'] ) ) {
//						$this->AP_processPrices( $_GET['feed'] );
//						$this->AP_setMessage( __( 'The feed was processed succesfully.', 'LDB_AP' ), 'success' );
//					} else {
//						wp_die( __( "You're not allowed to perform the requested action." ) );
//					}
                    if (isset($_GET['feed']) && is_array($_GET['feed']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'bulk-feeds')) {
                        foreach ($_GET['feed'] as $feed) {
                            $items = $this->AP_processFeed($feed);
                            foreach ($items as $item) {
                                $this->AP_createDraft($item);
                            }
                        }
                    }
                    break;
                case 'delete':
                    if (isset($_GET['feed']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'bulk-feeds')) {
                        $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'apfeeds WHERE ID IN (' . implode(', ', $_GET['feed']) . ')');
                        $this->AP_setMessage(__('The feeds were succesfully deleted.', 'LDB_AP'), 'success');
                    }
                    if (isset($_REQUEST['feed']) && isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'delete' . $_REQUEST['feed'])) {
                        $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'apfeeds WHERE id=' . $_GET['feed']);
                        $this->AP_setMessage(__('The feed was succesfully deleted.', 'LDB_AP'), 'success');
                    }
                    break;
                case 'draft':
                    if (isset($_GET['identifier']) && isset($_GET['feed']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'bulk-feeds')) {
                        $items = @$this->AP_processFeed($_GET['feed']);
                        foreach ($_GET['identifier'] as $identifier) {
                            foreach ($items as $item) {
                                if ($item['identifier'] === $identifier)
                                    if ($this->AP_createDraft($item))
                                        $this->AP_setMessage(sprintf(__('The draft for %s was created succesfully.', 'LDB_AP'), $item['name']), 'success');
                            }
                        }
                        $feed = @$this->AP_getFeed($_GET['feed']);
                        $this->AP_processPrices($_GET['feed']);
                        $this->action = 'view';
                    } else if (isset($_GET['identifier']) && isset($_GET['feed']) && isset($_GET['_draftnonce']) && wp_verify_nonce($_GET['_draftnonce'], 'draft' . $_GET['identifier'])) {
                        $items = @$this->AP_processFeed($_GET['feed']);
                        $post = '';
                        $postID = false;
                        $message = '';
                        $messagetype = '';
                        foreach ($items as $item) {
                            if ($item['identifier'] === $_GET['identifier'])
                                $postID = $this->AP_createDraft($item);
                        }
                        if ($postID && !is_array($postID))
                            $post = '&post=' . $postID;
                        $feed = @$this->AP_getFeed($_GET['feed']);
                        $this->AP_processPrices($_GET['feed']);
                        $paged = '';
                        $orderby = '';
                        $order = '';
                        if (isset($_GET['paged']))
                            $paged = '&paged=' . $_GET['paged'];
                        if (isset($_GET['orderby']))
                            $orderby = '&orderby=' . $_GET['orderby'];
                        if (isset($_GET['order']))
                            $order = '&order=' . $_GET['order'];
                        header('Location:?page=' . $_GET['page'] . '&action=view&feed=' . $_GET['feed'] . '&_viewnonce=' . $_GET['_viewnonce'] . $paged . $orderby . $order . $post);
                    }
                    break;
                default:
                    if (isset($_POST['wp_nonce_add']) && wp_verify_nonce($_POST['wp_nonce_add'], 'addfeed')) {
                        $data = $this->AP_prepareFeedData();
                        if(DEBUG)
                            print_r($data);
                        $right = $wpdb->insert($wpdb->prefix . 'apfeeds', $data);
                        if ($right)
                            $this->AP_setMessage(__('The feed was succesfully saved.', 'LDB_AP'), 'success');
                        else
                            $this->AP_setMessage(__('The feed was failed.', 'LDB_AP'), 'warning');
                    } else if (isset($_POST['wp_nonce_edit']) && wp_verify_nonce($_POST['wp_nonce_edit'], 'editfeed')) {
                        $data = $this->AP_prepareFeedData();
                        $wpdb->update($wpdb->prefix . 'apfeeds', $data, array('ID' => $_POST['ID']));
                        $this->AP_setMessage(__('The feed was succesfully updated.', 'LDB_AP'), 'success');
                    } else if (isset($_POST['wp_nonce_linkto']) && isset($_POST['identifier']) && wp_verify_nonce($_POST['wp_nonce_linkto'], 'linkto' . $_POST['identifier'])) {
                        add_post_meta($_POST['product'], $_POST['matches'], $_POST['identifier']);
                        header('Location:' . $_POST['view_referer'] . '&linkto=' . $_POST['product']);
                    }
                    break;
            }
        }

        /* Function that process all the feeds. */

        function AP_post_feed() {
            global $wpdb;
            $feed_ids = $wpdb->get_results('SELECT ID FROM ' . $wpdb->prefix . 'apfeeds');
            if ($feed_ids) {
                foreach ($feed_ids as $feed_id) {
                    $items = $this->AP_processFeed((int) $feed_id->ID);
                    foreach ($items as $item) {
                        $this->AP_createDraft($item);
                    }
                }
            }
        }

        /* Function that adds the custom meta box for the products. */

        function AP_addCustomMetaBox() {
            add_meta_box('affiliate-press', __('Prices from feeds', 'LDB_AP'), array(&$this, 'AP_pricesBox'), 'product', 'side', 'default');
        }

        /* Function that fetches and show the content for the meta box on the edit product page. */

        function AP_pricesBox() {
            global $post, $wpdb;
            $productID = $post->ID;
            $results = $wpdb->get_results('SELECT apfeeds.title, apfeeds.currency, apprices.price FROM ' . $wpdb->prefix . 'apprices apprices, ' . $wpdb->prefix . 'apfeeds apfeeds WHERE apprices.productID = ' . $productID . ' AND apprices.feedID = apfeeds.ID AND apprices.online = 1 ORDER BY apprices.price ASC');
            $pricetable = '<table cellpadding="0" cellspacing="0" border="0" class="pricetable">';
            if ($results) {
                $r = 1;
                foreach ($results as $result) {
                    $class = 'normal';
                    if (count($results) === 1) {
                        $class = '';
                    } else if ($r === 1) {
                        $class = 'first';
                    } else if ($r === count($results)) {
                        $class = 'last';
                    }
                    $pricetable.= '<tr class="' . $class . '"><th class="feedtitle">' . $result->title . '</th><td class="feedprice">' . $result->currency . ' ' . $result->price . '</td></tr>';
                    $r++;
                }
            } else {
                $pricetable.= '<td>' . __('No prices were found.', 'LDB_AP') . '</td>';
            }
            $pricetable.= '</table>';
            echo $pricetable;
        }

        /* Function that shows the dashboard. */

        function AP_dashboard() {
            global $wpdb;
            $dashboard['products'] = count(get_posts(array('post_type' => 'product', 'post_status' => array('publish', 'draft'))));
            $dashboard['feeds'] = count($wpdb->get_results('SELECT ID FROM ' . $wpdb->prefix . 'apfeeds', ARRAY_A));
            $dashboard['multipleprices'] = count($wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'posts AS products WHERE (SELECT COUNT(*) FROM ' . $wpdb->prefix . 'apprices AS prices WHERE prices.productID = products.ID AND prices.online = 1 ) > 1 AND post_type="product" AND ( post_status = "draft" OR post_status = "publish" )', ARRAY_A));
            $dashboard['oneprice'] = count($wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'posts AS products WHERE (SELECT COUNT(*) FROM ' . $wpdb->prefix . 'apprices AS prices WHERE prices.productID = products.ID AND prices.online = 1 ) = 1 AND post_type="product" AND ( post_status = "draft" OR post_status = "publish" )', ARRAY_A));
            $dashboard['noprices'] = count($wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'posts AS products WHERE (SELECT COUNT(*) FROM ' . $wpdb->prefix . 'apprices AS prices WHERE prices.productID = products.ID AND prices.online = 1 ) = 0 AND post_type="product" AND ( post_status = "draft" OR post_status = "publish" )', ARRAY_A));
            include( LDB_AP_VIEW_PATH . 'dashboard.php' );
        }

        /* Function that shows the feed index page. */

        function AP_feeds() {
            include( LDB_AP_VIEW_PATH . 'feed-index.php' );
        }

        /* Function that can set certain feed interval based on feed id */

        function AP_interval() {
            //global $wpdb;
            //echo wp_get_schedule('AP_create_post'). 'yes';
            if(DEBUG){
                echo '<pre>';
                print_r(wp_get_schedules());
            }
            if ($_POST) {
                $interval = $_POST['AP_feed_interval'];
                if (wp_next_scheduled('AP_create_post')) {
                    wp_clear_scheduled_hook('AP_create_post');
                }
                switch ($interval) {
                    case 'now':
                        $this->AP_post_feed();
                        break;
                    case 'min':
                        wp_schedule_event(current_time('timestamp') + 20, 'min', 'AP_create_post');
                        break;
                    case 'hourly':
                        wp_schedule_event(current_time('timestamp') + 3600, 'hourly', 'AP_create_post');
                        break;
                    case 'daily':
                        wp_schedule_event(current_time('timestamp') + 86400, 'daily', 'AP_create_post');
                        break;
                    case 'weekly':
                        wp_schedule_event(current_time('timestamp') + 604800, 'weekly', 'AP_create_post');
                        break;
                }
                echo wp_get_schedule('AP_create_post') . 'yes';
            }
            include( LDB_AP_VIEW_PATH . 'feed-interval.php');
        }

        /* Function that shows the add feed page. */

        function AP_addFeed() {
            $matches = $this->AP_buildSelect('matches', $this->AP_getCustomFields());
            include( LDB_AP_VIEW_PATH . 'feed-add.php' );
        }

        /* Function that shows the add feed page. */

        function AP_addFeedWizard() {
            $step = 1;
            if (isset($_POST['step'])) {
                $step = $_POST['step'] + 1;
            }
            if ($step === 2) {
                if ($this->AP_issetAndNotEmpty($_POST, array('title', 'currency', 'url'))) {
                    $nodes = $this->AP_fetchNodesWithChildren($_POST['url']);
                    $item_xpath = $this->AP_buildSelect('item_xpath', $nodes, false, true);
                } else {
                    $this->AP_setMessage(__("You didn't fill out all the required fields.", 'LDB_AP'), 'warning');
                    $step = 1;
                }
            } else if ($step === 3) {
                if ($this->AP_issetAndNotEmpty($_POST, array('title', 'currency', 'url', 'item_xpath'))) {
                    $xpath = array();
                    $fields = array('name', 'image', 'price', 'link', 'description', 'identifier');
                    $nodes = $this->AP_fetchChildNodes($_POST['url'], $_POST['item_xpath']);
                    foreach ($fields as $field) {
                        $xpath[$field] = $this->AP_buildSelect($field . '_xpath', $nodes, false, true);
                    }
                    $matches = $this->AP_buildSelect('matches', $this->AP_getCustomFields());
                } else {
                    $nodes = $this->AP_fetchNodesWithChildren($_POST['url']);
                    $item_xpath = $this->AP_buildSelect('item_xpath', $nodes, false, true);
                    $this->AP_setMessage(__("You didn't fill out all the required fields.", 'LDB_AP'), 'warning');
                    $step = 2;
                }
            }
            include( LDB_AP_VIEW_PATH . 'feed-add-wizard-step' . $step . '.php' );
        }

        /* Function that check if the data in an array is set and not empty */

        function AP_issetAndNotEmpty($var, $keys) {
            $check = true;
            foreach ($keys as $key) {
                if (!isset($var[$key]) || empty($var[$key]))
                    $check = false;
            }
            return $check;
        }

        /* Function that fetches all XPath nodes that have children and occur multiple times. */

        function AP_fetchNodesWithChildren($url) {
            $nodes = array();
            $temp = array();
            $data = wp_remote_get($url);
            $body = new DOMDocument();
            @$body->loadXML($data['body']);
            $xpath = new DOMXPath($body);
            $items = $xpath->query('//*[count(*)>0]');
            foreach ($items as $item) {
                if (in_array($item->nodeName, $temp) && !in_array($item->nodeName, $nodes)) {
                    $nodes['//' . $item->nodeName] = '//' . $item->nodeName;
                }
                $temp[] = $item->nodeName;
            }
            return $nodes;
        }

        /* Function that fetches the childnodes for the first occurence of a certain node. */

        function AP_fetchChildNodes($url, $item_xpath) {
            $nodes = array();
            $data = wp_remote_get($url);
            $body = new DOMDocument();
            @$body->loadXML($data['body']);
            $xpath = new DOMXPath($body);
            $query = $item_xpath . '[1]/*';
            $items = $xpath->query($item_xpath . '[1]/*');
            foreach ($items as $item) {
                if (!in_array($item->nodeName, $nodes)) {
                    $nodes['.//' . $item->nodeName] = './/' . $item->nodeName;
                }
            }
            return $nodes;
        }

        /* Function that shows the edit feed page. */

        function AP_editFeed() {
            wp_enqueue_script('ap_expand', LDB_AP_SCRIPTS_URL . 'expand.js');
            wp_enqueue_script('ap_message', LDB_AP_SCRIPTS_URL . 'message.js');
            if (isset($_GET['feed']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'edit' . $_GET['feed'])) {
                $feed = @$this->AP_getFeed($_REQUEST['feed']);
                $customfields = $this->AP_getCustomFields();
                $matches = $this->AP_buildSelect('matches', $this->AP_getCustomFields(), $feed['matches']);
                include( LDB_AP_VIEW_PATH . 'feed-edit.php' );
            } else {
                wp_die(__("You're not allowed to perform the requested action."));
            }
        }

        /* Function that shows the view feed page. */

        function AP_viewFeed() {
            wp_enqueue_script('ap_expand', LDB_AP_SCRIPTS_URL . 'expand.js');
            wp_enqueue_script('ap_message', LDB_AP_SCRIPTS_URL . 'message.js');
            if (isset($_GET['post'])) {
                $postinfo = get_post($_GET['post']);
                $this->AP_setMessage(sprintf(__('The draft for %s was created successfully.', 'LDB_AP'), $postinfo->post_title), 'success');
            } else if (isset($_GET['linkto'])) {
                $postinfo = get_post($_GET['linkto']);
                $this->AP_setMessage(sprintf(__('The item was succesfully linked to the product with title "%s".', 'LDB_AP'), $postinfo->post_title), 'success');
            }
            if (isset($_GET['feed']) && isset($_GET['_viewnonce']) && wp_verify_nonce($_GET['_viewnonce'], 'view' . $_GET['feed'])) {
                $feed = @$this->AP_getFeed($_GET['feed']);
                $items = @$this->AP_processFeed($_GET['feed']);
                include( LDB_AP_VIEW_PATH . 'feed-view.php' );
            } else {
                wp_die(__("You're not allowed to perform the requested action."));
            }
        }

        /* Function that adds the item data to an existing product. */

        function AP_linkToProduct() {
            wp_enqueue_script('ap_expand', LDB_AP_SCRIPTS_URL . 'expand.js');
            wp_enqueue_script('ap_message', LDB_AP_SCRIPTS_URL . 'message.js');
            if (isset($_GET['identifier']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'linkto' . $_GET['identifier'])) {
                $products = array();
                $setproducts = array();
                $productsarray = array();
                $allproducts = get_posts(
                        array(
                            'post_type' => 'product',
                            'post_status' => array('publish', 'draft')
                        )
                );
                foreach ($allproducts as $product) {
                    if (!$meta = get_post_meta($product->ID, $_GET['matches'])) {
                        $products[$product->ID] = $product->post_title;
                    } else {
                        if (!in_array($_GET['identifier'], $meta)) {
                            $setproducts[$product->ID] = $product->post_title;
                        }
                    }
                }
                if (count($products) > 0)
                    $productsarray[sprintf(__('Products without %s set', 'LDB_AP'), $_GET['matches'])] = $products;

                if (count($setproducts) > 0)
                    $productsarray[sprintf(__('Products with %s already set', 'LDB_AP'), $_GET['matches'])] = $setproducts;

                $view_referer = $_GET['referer'];
                $linkto = $this->AP_buildSelect('product', $productsarray, false, true);
                include( LDB_AP_VIEW_PATH . 'feed-linkto.php' );
            } else {
                wp_die(__("You're not allowed to perform the requested action."));
            }
        }

        /* Function to show a postbox. */

        function AP_postbox($id, $title, $content) {
            $postbox = '<div id="' . $id . '" class="postbox"><div class="handlediv" title="' . __('Click to toggle', 'LDB_AP') . '"><br /></div><h3 class="hndle"><span>' . $title . '</span></h3><div class="inside">' . $content . '</div></div>';
            echo $postbox;
        }

        /* Function that shows the sidebar. */

        function AP_sidebar() {
            include( LDB_AP_VIEW_PATH . 'sidebar.php' );
        }

        /* Function that catches the attachment id and assigns it as the feature image to it's parent. */

        function AP_catchAttachment($att_id) {
            $post = get_post($att_id);
            update_post_meta($post->post_parent, '_thumbnail_id', $att_id);
        }

        /* Function that creates a product draft based on the feed item data. */

        function AP_createDraft($item) {
            $data = array(
                'post_title' => '<strong style="color:red;">' . '$' . $item['price'] . ' </strong>' . $item['name'],
                'post_status' => 'draft',
                'post_author' => 1,
            );

            $post_id = wp_insert_post($data);
            $this->AP_attach_content($item, $post_id);
        }

        /* Function that insert image to media liberay and post. */

        function AP_attach_content($item, $post_id) {
            $upload_dir = wp_upload_dir();
            $imghtmltag = media_sideload_image($item['image'], $post_id);
            $redefine = simplexml_load_string($imghtmltag);
            $src = $redefine['src'];
            $fullfilename = end(explode('/', $src));
            $filename = current(explode('.', $fullfilename));
            $suffix = end(explode('.', $fullfilename));
            $newfile = $filename . '-150x150.' . $suffix;
            $thumbnailsrc = $upload_dir['url'] . '/' . $newfile;

            $data = array(
                'ID' => $post_id,
                'post_content' => $this->AP_content_template($item, $src, $thumbnailsrc, $filename)
            );

            wp_update_post($data);
        }

        /* Function that returns the post template. */

        function AP_content_template($item, $src, $thumbnailsrc, $filename) {
            return '<div><ul><li><img class="alignnone size-thumbnail" title="' . $filename . '" src="' . $thumbnailsrc . '" alt="" width="150" height="150" /></li><li>' . $item['name'] . ' only ' . '<strong style="color:red;">' . '$' . $item['price'] . '</strong></li><li><a href="' . $item['link'] . '">Go check it out</a></li></ul></div>';
        }

        /* Function that creates a product draft based on the feed item data. */

        function AP_prepareFeedData() {
            $fields = array('title', 'currency', 'url', 'item_xpath', 'name_xpath', 'image_xpath', 'price_xpath', 'link_xpath', 'identifier_xpath', 'description_xpath', 'msrp_xpath', 'saving_rate_xpath', 'store_xpath', 'hot_xpath', 'free_shipping_xpath', 'matches');
            $data = array();
            foreach ($fields as $value) {
                if (isset($_POST[$value])) {
                    $data[$value] = $_POST[$value];
                } else {
                    $data[$value] = '';
                }
            }
            if (isset($_POST['new_matches']) && !empty($_POST['new_matches'])) {
                $data['matches'] = $_POST['new_matches'];
            }
            return $data;
        }

        /* Function that sets a message. */

        function AP_setMessage($msg, $type) {
            $this->messages[] = array($msg, $type);
        }

        /* Function that gets, displays and clears all messages. */

        function AP_getMessage() {
            $messages = '';
            foreach ($this->messages as $message) {
                $messages.= '<div class="message ' . $message[1] . '">' . $message[0] . '<a class="ap_message_close" href="javascript:;">' . __('Close', 'LDB_AP') . '</a></div>' . "\n";
            }
            $this->messages = array();
            echo $messages;
        }

        /* Function that gets all the info for a feed. */

        function AP_getFeed($id) {
            global $wpdb;
            if ($id === null)
                return false;
            $feed = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'apfeeds WHERE id=' . $id, ARRAY_A);
            return $feed;
        }

        /* Function that fetches the custom fields for matching the identifier to. */

        function AP_getCustomFields() {
            $fields = array();
            $products = get_posts(
                    array(
                        'post_type' => 'products',
                        'post_status' => array('publish', 'draft')
                    )
            );
            foreach ($products as $product) {
                $fields = array_merge($fields, get_post_custom_keys($product->ID));
            }
            foreach ($fields as $key => $field) {
                if ($field[0] === '_')
                    unset($fields[$key]);
            }
            $fields = array_unique($fields);
            return $fields;
        }

        /* Function to build a <select>. */

        function AP_buildSelect($name, $values, $default = false, $split = false) {
            $select = '<select name="' . $name . '" id="' . $name . '"><option></option>';
            if ($split) {
                foreach ($values as $key => $value) {
                    if (is_array($value)) {
                        $select.= '<optgroup label="' . $key . '">';
                        foreach ($value as $subkey => $subvalue) {
                            if ($subkey === $default) {
                                $select.= '<option value="' . $subkey . '" selected="selected">' . $subvalue . '</option>';
                            } else {
                                $select.= '<option value="' . $subkey . '">' . $subvalue . '</option>';
                            }
                        }
                        $select.= '</optgroup>';
                    } else {
                        if ($key === $default) {
                            $select.= '<option value="' . $key . '" selected="selected">' . $value . '</option>';
                        } else {
                            $select.= '<option value="' . $key . '">' . $value . '</option>';
                        }
                    }
                }
            } else {
                foreach ($values as $value) {
                    if ($value === $default) {
                        $select.= '<option value="' . $value . '" selected="selected">' . $value . '</option>';
                    } else {
                        $select.= '<option value="' . $value . '">' . $value . '</option>';
                    }
                }
            }
            $select.= '</select>';
            return $select;
        }

        /* Function that processes a feed based on a feed ID. */

        function AP_processFeed($feed) {
            global $wpdb;
            $info = json_decode(get_option('ap_feed_' . $feed, false), true);
            if (isset($info['timestamp']) && $info['timestamp'] < ( time() - 300 )) {
                return $info['data'];
            } else {
                $info = array();
                $feedinfo = $this->AP_getFeed($feed);
                $data = wp_remote_get($feedinfo['url']);
                $body = new DOMDocument();
                $body->loadXML($data['body']);
                $xpath = new DOMXPath($body);
                $items = $xpath->query($feedinfo['item_xpath']);
                foreach ($items as $item) {
                    $price = $this->AP_getValue(@$xpath->query($feedinfo['price_xpath'], $item));
                    $name = $this->AP_getValue(@$xpath->query($feedinfo['name_xpath'], $item));
                    $image = $this->AP_getValue(@$xpath->query($feedinfo['image_xpath'], $item));
                    $link = $this->AP_getValue(@$xpath->query($feedinfo['link_xpath'], $item));
                    $identifier = $this->AP_getValue(@$xpath->query($feedinfo['identifier_xpath'], $item));
                    $description = $this->AP_getValue(@$xpath->query($feedinfo['description_xpath'], $item));
                    $msrp = $this->AP_getValue(@$xpath->query($feedinfo['msrp_xpath'], $item));
                    $saving_rate = $this->AP_getValue(@$xpath->query($feedinfo['saving_rate_xpath'], $item));
                    $store = $this->AP_getValue(@$xpath->query($feedinfo['store_xpath'], $item));
                    $hot = $this->AP_getValue(@$xpath->query($feedinfo['hot_xpath'], $item));
                    $free_shipping = $this->AP_getValue(@$xpath->query($feedinfo['free_shipping_xpath'], $item));
                    $info[] = array(
                        'identifier' => $identifier,
                        'matches' => $feedinfo['matches'],
                        'currency' => $feedinfo['currency'],
                        'price' => $price,
                        'name' => $name,
                        'image' => $image,
                        'link' => $link,
                        'description' => $description,
                        'msrp' => $msrp,
                        'saving_rate' => $saving_rate,
                        'store' => $store,
                        'hot' => $hot,
                        'free_shipping' => $free_shipping,
                        'feed_id' => $feedinfo['ID']
                    );
                }
                $option = array(
                    'data' => $info,
                    'time' => time()
                );
                update_option('ap_feed_' . $feed, json_encode($option));
                return $info;
            }
        }

        /* Function that gets the value of an XPath node. */

        function AP_getValue($e) {
            if ($e) {
                return trim(preg_replace('/\s+/', ' ', @$e->item(0)->nodeValue));
            } else {
                return false;
            }
        }

        /* Function that processes the prices found for a product. */

        function AP_processPrices($feed) {
            global $wpdb;
            $info = array();
            $feedinfo = $this->AP_getFeed($feed);
            $wpdb->query('UPDATE ' . $wpdb->prefix . 'apprices SET online = 0 WHERE feedID = ' . $feedinfo['ID']);
            $info = @$this->AP_processFeed($feed);
            foreach ($info as $item) {
                $query = 'SELECT wposts.ID FROM ' . $wpdb->posts . ' wposts, ' . $wpdb->postmeta . ' wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = "' . $item['matches'] . '" AND wpostmeta.meta_value = "' . $item['identifier'] . '" AND wposts.post_type = "product"';
                $results = $wpdb->get_results($query);
                foreach ($results as $result) {
                    $data = array(
                        'productID' => $result->ID,
                        'feedID' => $item['feed_id'],
                        'price' => $item['price'],
                        'link' => $item['link'],
                        'online' => 1
                    );
                    $update = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'apprices WHERE productID = ' . $result->ID . ' AND feedID = ' . $item['feed_id']);
                    if ($update) {
                        $wpdb->update($wpdb->prefix . 'apprices', $data, array('ID' => $update->ID));
                    } else {
                        $wpdb->insert($wpdb->prefix . 'apprices', $data);
                    }
                }
            }
        }

        /* Function that processes all the prices. For cron purposes. */

        function AP_processAllPrices() {
            global $wpdb;
            $feeds = $wpdb->get_results('SELECT ID FROM ' . $wpdb->prefix . 'apfeeds ORDER BY ID ASC', ARRAY_A);
            foreach ($feeds as $feed) {
                $this->AP_processPrices($feed['ID']);
            }
        }

        /* Function that builds a table to display the prices in the front-end. */

        function AP_buildTable($data) {
            $table = '<table cellpadding="0" cellspacing="0" border="0" class="aptable">';
            if ($data) {
                foreach ($data as $row) {
                    $table.= '<tr><td><a href="' . $row->link . '" target="_blank">' . $row->title . '</a></td><td>' . $row->currency . ' ' . $row->price . '</td></tr>';
                }
            } else {
                $table.= '<td>' . __('No prices were found.', 'LDB_AP') . '</td>';
            }
            $table.= '</table>';
            return $table;
        }

        /* Function that gets the prices to display the prices in the front-end. */

        function AP_getPrices($table = false) {
            global $post, $wpdb;
            $results = $wpdb->get_results('SELECT apfeeds.title, apfeeds.currency, apprices.price, apprices.link FROM ' . $wpdb->prefix . 'apprices apprices, ' . $wpdb->prefix . 'apfeeds apfeeds WHERE apprices.productID = ' . $post->ID . ' AND apprices.feedID = apfeeds.ID AND apprices.online = 1 ORDER BY apprices.price ASC');
            if ($table)
                $results = $this->AP_buildTable($results);

            return $results;
        }

    }

}