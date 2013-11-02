<?php

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class itemsTable extends WP_List_Table {

    function __construct() {
        global $status, $page;
        parent::__construct(
                array(
                    'singular' => 'feed',
                    'plural' => 'feeds',
                    'ajax' => false
                )
        );
    }

    function pagination($which) {
        if (empty($this->_pagination_args))
            return;

        extract($this->_pagination_args, EXTR_SKIP);

        $output = '<span class="displaying-num">' . sprintf(_n('1 item', '%s items', $total_items), number_format_i18n($total_items)) . '</span>';

        $current = $this->get_pagenum();

        $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $current_url = remove_query_arg(array('hotkeys_highlight_last', 'hotkeys_highlight_first', 'post', 'linkto'), $current_url);

        $page_links = array();

        $disable_first = $disable_last = '';
        if ($current == 1)
            $disable_first = ' disabled';
        if ($current == $total_pages)
            $disable_last = ' disabled';

        $page_links[] = sprintf("<a class='%s' title='%s' href='%s'>%s</a>", 'first-page' . $disable_first, esc_attr__('Go to the first page'), esc_url(remove_query_arg('paged', $current_url)), '&laquo;'
        );

        $page_links[] = sprintf("<a class='%s' title='%s' href='%s'>%s</a>", 'prev-page' . $disable_first, esc_attr__('Go to the previous page'), esc_url(add_query_arg('paged', max(1, $current - 1), $current_url)), '&lsaquo;'
        );

        if ('bottom' == $which)
            $html_current_page = $current;
        else
            $html_current_page = sprintf("<input class='current-page' title='%s' type='text' name='paged' value='%s' size='%d' />", esc_attr__('Current page'), $current, strlen($total_pages)
            );

        $html_total_pages = sprintf("<span class='total-pages'>%s</span>", number_format_i18n($total_pages));
        $page_links[] = '<span class="paging-input">' . sprintf(_x('%1$s of %2$s', 'paging'), $html_current_page, $html_total_pages) . '</span>';

        $page_links[] = sprintf("<a class='%s' title='%s' href='%s'>%s</a>", 'next-page' . $disable_last, esc_attr__('Go to the next page'), esc_url(add_query_arg('paged', min($total_pages, $current + 1), $current_url)), '&rsaquo;'
        );

        $page_links[] = sprintf("<a class='%s' title='%s' href='%s'>%s</a>", 'last-page' . $disable_last, esc_attr__('Go to the last page'), esc_url(add_query_arg('paged', $total_pages, $current_url)), '&raquo;'
        );

        $pagination_links_class = 'pagination-links';
        if (!empty($infinite_scroll))
            $pagination_links_class = ' hide-if-js';
        $output .= "\n<span class='$pagination_links_class'>" . join("\n", $page_links) . '</span>';

        if ($total_pages)
            $page_class = $total_pages < 2 ? ' one-page' : '';
        else
            $page_class = ' no-pages';

        $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

        echo $this->_pagination;
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'name':
            case 'identifier':
            case 'matches':
                return $item[$column_name];
            case 'price':
                return $item['currency'] . ' ' . $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function column_name($item) {
        global $wpdb;
        $results = $wpdb->get_results('SELECT wposts.ID FROM ' . $wpdb->posts . ' wposts, ' . $wpdb->postmeta . ' wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = "' . $item['matches'] . '" AND wpostmeta.meta_value = "' . $item['identifier'] . '" AND wposts.post_type = "product"');
        $actions = array();
        $paged = '';
        $orderby = '';
        $order = '';
        if (isset($_GET['paged']))
            $paged = '&paged=' . $_GET['paged'];
        if (isset($_GET['orderby']))
            $orderby = '&orderby=' . $_GET['orderby'];
        if (isset($_GET['order']))
            $order = '&order=' . $_GET['order'];

        if (count($results) > 0) {
            return $item['name'];
        } else {
            $actions['draft'] = sprintf('<a href="%s">%s</a>', '?page=' . $_REQUEST['page'] . '&action=draft&feed=' . $item['feed_id'] . '&identifier=' . $item['identifier'] . $paged . $orderby . $order . '&_draftnonce=' . wp_create_nonce('draft' . $item['identifier']) . '&_viewnonce=' . wp_create_nonce('view' . $item['feed_id']), __('Create as draft', 'LDB_AP'));
            $products = get_posts(
                    array(
                        'post_type' => 'product',
                        'post_status' => array('publish', 'draft')
                    )
            );
            $actions['additem'] = sprintf('<a href="%s">%s</a>', wp_nonce_url('?page=affiliate_press_linktoproduct&identifier=' . $item['identifier'] . '&matches=' . $item['matches'] . '&name=' . $item['name'] . '&referer=' . urlencode($_SERVER['REQUEST_URI']), 'linkto' . $item['identifier']), __('Link to an existing product', 'LDB_AP'));
            return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions));
        }
    }

    function column_cb($item) {
        global $wpdb;
        $disabled = '';
        $query = 'SELECT wposts.ID FROM ' . $wpdb->posts . ' wposts, ' . $wpdb->postmeta . ' wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = "' . $item['matches'] . '" AND wpostmeta.meta_value = "' . $item['identifier'] . '" AND wposts.post_type = "product"';
        $results = $wpdb->get_results($query);
        if (count($results) > 0)
            $disabled = ' disabled = "disabled" readonly = "readonly"';

        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s"' . $disabled . ' />', 'identifier', $item['identifier']);
    }

    function get_columns() {
        $columns = array(
            'name' => __('Name', 'LDB_AP'),
            'identifier' => __('Identifier', 'LDB_AP'),
            'matches' => __('Matches', 'LDB_AP'),
            'price' => __('Price', 'LDB_AP')
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array(__('Name', 'LDB_AP'), true),
            'identifier' => array(__('Identifier', 'LDB_AP'), true),
            'price' => array(__('Price', 'LDB_AP'), false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'draft' => __('Create as draft', 'LDB_AP')
        );
        return $actions;
    }

    function usort_reorder($a, $b) {
        $orderby = (!empty($_REQUEST['orderby']) ) ? $_REQUEST['orderby'] : 'name';
        $order = (!empty($_REQUEST['order']) ) ? $_REQUEST['order'] : 'asc';
        $result = strcmp(floatval($a[$orderby]), floatval($b[$orderby]));
        return ( $order === 'asc' ) ? $result : -$result;
    }

    function prepare_items($data) {
        global $wpdb;
        $sortable = array(
            'name' => array(__('Name', 'LDB_AP'), true),
            'price' => array(__('Price', 'LDB_AP'), false)
        );
        $columns = array(
            'name' => __('Name', 'LDB_AP'),
            'price' => __('Price', 'LDB_AP')
        );
        if (isset($data[0])) {
            $sortable = array(
                'name' => array('name', true),
                'identifier' => array('identifier', true),
                'price' => array('price', false)
            );
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'name' => __('Name', 'LDB_AP'),
                'identifier' => $data[0]['matches'],
                'price' => __('Price', 'LDB_AP')
            );
        }
        $per_page = 25;
        $hidden = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $orderby = (!empty($_REQUEST['orderby']) ) ? $_REQUEST['orderby'] : 'name';
        $order = (!empty($_REQUEST['order']) ) ? $_REQUEST['order'] : 'asc';
        if ($orderby === 'price') {
            $tmp = array();
            foreach ($data as $item => $value) {
                $tmp[] = floatval($value['price']);
            }
            if ($order === 'desc') {
                array_multisort($tmp, SORT_NUMERIC, SORT_DESC, $data);
            } else {
                array_multisort($tmp, SORT_NUMERIC, SORT_ASC, $data);
            }
        } else {
            usort($data, array(&$this, 'usort_reorder'));
        }
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data, ( ( $current_page - 1 ) * $per_page), $per_page);
        $this->items = $data;
        $this->set_pagination_args(
                array(
                    'total_items' => $total_items,
                    'per_page' => $per_page,
                    'total_pages' => ceil($total_items / $per_page)
                )
        );
    }

}