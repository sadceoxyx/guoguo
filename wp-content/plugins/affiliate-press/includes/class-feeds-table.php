<?php

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class feedsTable extends WP_List_Table {

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

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'url':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function column_title($item) {
        $paged = '';
        $orderby = '';
        $order = '';
        if (isset($_GET['paged']))
            $paged = '&paged=' . $_GET['paged'];
        if (isset($_GET['orderby']))
            $orderby = '&orderby=' . $_GET['orderby'];
        if (isset($_GET['order']))
            $order = '&order=' . $_GET['order'];

        $actions = array(
            'view' => sprintf('<a href="%s">%s</a>', '?page=affiliate_press_view&feed=' . $item['ID'] . '&_viewnonce=' . wp_create_nonce('view' . $item['ID']) . $paged . $orderby . $order, __('View', 'LDB_AP')),
            'edit' => sprintf('<a href="%s">%s</a>', wp_nonce_url('?page=affiliate_press_edit&feed=' . $item['ID'] . $paged . $orderby . $order, 'edit' . $item['ID']), __('Edit', 'LDB_AP')),
            'process' => sprintf('<a href="%s">%s</a>', wp_nonce_url('?page=' . $_REQUEST['page'] . '&action=process&feed=' . $item['ID'] . $paged . $orderby . $order, 'process' . $item['ID']), __('Process', 'LDB_AP')),
            'delete' => sprintf('<a href="%s">%s</a>', wp_nonce_url('?page=' . $_REQUEST['page'] . '&action=delete&feed=' . $item['ID'] . $paged . $orderby . $order, 'delete' . $item['ID']), __('Delete', 'LDB_AP')),
        );
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s', $item['title'], $item['ID'], $this->row_actions($actions));
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['ID']);
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', 'LDB_AP'),
            'url' => __('URL', 'LDB_AP')
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'title' => array('title', true),
            'url' => array('url', false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => __('Delete', 'LDB_AP'),
            'process' => __('Process', 'LDB_AP')
        );
        return $actions;
    }

    function usort_reorder($a, $b) {
        $orderby = (!empty($_REQUEST['orderby']) ) ? $_REQUEST['orderby'] : 'title';
        $order = (!empty($_REQUEST['order']) ) ? $_REQUEST['order'] : 'asc';
        $result = strcmp($a[$orderby], $b[$orderby]);
        return ( $order === 'asc' ) ? $result : -$result;
    }

    function prepare_items() {
        global $wpdb;
        $per_page = 10;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $data = $wpdb->get_results('SELECT id AS ID, title, url FROM ' . $wpdb->prefix . 'apfeeds', ARRAY_A);
        usort($data, array($this, 'usort_reorder'));
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