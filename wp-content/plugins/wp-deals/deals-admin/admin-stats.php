<?php

//secure included files
deals_secure();

/**
 * Run statistic
 * @return void
 */
function deals_stats() {    
    _deals_stats_view();    
}

/**
 * Manage request and output to screen
 * @return void
 */
function _deals_stats_view() {
    
    $tabs = array(
            'sales' => __('Sales', 'wpdeals'),
            'download' => __('Download', 'wpdeals')
        );
    
    $url = admin_url('/admin.php?page=deal-stats');
    
    ?>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            
            <?php
            
            foreach($tabs as $tab => $anchor) {
                
                if(!isset($_GET['tab']) && $tab == 'sales') {
                    echo '<a class="nav-tab nav-tab-active" href="'.$url.'&tab='.$tab.'">'.$anchor.'</a>';   
                }else{
                    
                    if(isset($_GET['tab']) && $tab == $_GET['tab']) {
                        echo '<a class="nav-tab nav-tab-active" href="'.$url.'&tab='.$tab.'">'.$anchor.'</a>';   
                    }else{
                        echo '<a class="nav-tab" href="'.$url.'&tab='.$tab.'">'.$anchor.'</a>';   
                    }
                    
                }
                
            }
            
            ?>
            
        </h2>
        
        <?php
        
            $tab    = $_GET['tab'];
            switch ($tab) {
                case 'download':
                    
                    _deals_stats_view_download();
                    break;
                
                default:
                    
                    _deals_stats_view_sales();
                    break;
            }
        
        ?>
        
    </div>
    <?php
    
}

/**
 *
 * Filter post clauses
 * 
 * @global WPDB $wpdb
 * @param array $pieces
 * @return array 
 */
function _deals_stats_sales_filter_clauses($pieces) {
    global $wpdb;
    
    $pieces['fields'] = 'DATE_FORMAT('.$wpdb->posts.'.post_date,"%d-%m-%Y") AS newdate,SUM('.$wpdb->postmeta.'.meta_value) AS sum_price';    
    $pieces['groupby'] = 'newdate';
    $pieces['orderby'] = 'newdate DESC';    
    return $pieces;
    
}

/**
 *
 * Filter where sql
 * 
 * @global WPDB $wpdb
 * @param string $where
 * @return string 
 */
function _deals_stats_sales_filter_where($where) {
    global $wpdb;
    //$where .= ' AND '.$wpdb->posts.'.post_date <= STR_TO_DATE("'.date('d-m-Y').'","%d-%m-%Y")';
    $where .= ' AND '.$wpdb->posts.'.post_date BETWEEN "'.date('Y-m-01 00:00:00').'" AND "'.date('Y-m-d H:i:s').'"';
    return $where;
}

/**
 *
 * Filter where for submitted date
 * 
 * @global WPDB $wpdb
 * @param string $where
 * @return string 
 */
function _deals_stats_sales_filter_where_submitted($where) {
    global $wpdb;
    $where .= ' AND '.$wpdb->posts.'.post_date BETWEEN "'.$_POST['start_date'].'" AND "'.$_POST['end_date'].'"';
    return $where;
}

/**
 * Filter for total money process
 * 
 * @global WPDB $wpdb
 * @param array $pieces
 * @return array
 */
function _deals_stats_sales_filter_clauses_total_money($pieces) {
    global $wpdb;
    
    $pieces['fields'] = 'DATE_FORMAT('.$wpdb->posts.'.post_date,"%d-%m-%Y") AS newdate,SUM('.$wpdb->postmeta.'.meta_value) AS sum_price';  
    $pieces['groupby'] = 'newdate DESC';
    $pieces['orderby'] = '';
    
    if(isset($_POST['total_sales_per_month'])) {
        
        $start_date = date('Y-'.$_POST['total_sales_per_month'].'-01 00:00:00');
        $end_date = date('Y-'.$_POST['total_sales_per_month'].'-31 00:00:00');
        
        $pieces['where'] .= ' AND '.$wpdb->posts.'.post_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"';        
        
    }
    
    return $pieces;
}

/**
 * Filter clauses for top sales
 * 
 * @global WPDB $wpdb
 * @param array $pieces
 * @return array
 */
function _deals_stats_sales_filter_clauses_top_sales($pieces) {
    global $wpdb;
    
    $pieces['fields'] = $wpdb->posts.'.ID, '.$wpdb->postmeta.'.meta_value, COUNT('.$wpdb->postmeta.'.meta_value) AS item_fav';  
    $pieces['orderby'] = 'item_fav DESC';
    $pieces['groupby'] = $wpdb->postmeta.'.meta_value';
    return $pieces;
    
}

/**
 *
 * Filter clauses for latest sales
 * 
 * @global WPDB $wpdb
 * @param array $pieces
 * @return array
 */
function _deals_stats_sales_filter_clauses_latest_sales($pieces) {
    
    global $wpdb;
    
    $pieces['fields'] = $wpdb->posts.'.ID, '.$wpdb->postmeta.'.meta_value';  
    return $pieces;
    
}

function _deals_selected_months($given) {
    if(isset($_POST['total_sales_per_month'])) {
        $requested = $_POST['total_sales_per_month'];
        if($requested == $given) {
            echo 'selected="selected"';
        }
    }
}

/**
 * Manage sales statistic
 * 
 * @return void
 */
function _deals_stats_view_sales() {
    
    global $wpdb;        
    
    /*
     * set data overview
     */
    if(!isset($_POST['submit'])) {//if no POST submitted
     
        $graph_title = __('Last 10 Sales On This Month', 'wpdeals');
        
        add_filter('posts_where','_deals_stats_sales_filter_where');
        add_filter('posts_clauses','_deals_stats_sales_filter_clauses');        
        $posts = new WP_Query(array(
            'posts_per_page' => 10,            
            'post_type' => 'deals-sales',
            'post_status' => 'publish',    
            'meta_key' => '_deals_sales_amount'
        ));        
        remove_filter('posts_clauses','_deals_stats_sales_filter_clauses');
        remove_filter('posts_where','_deals_stats_sales_filter_where');
        
        $data_overview = array();
        foreach($posts->posts as $post) {
            $stdpost = new stdClass();
            $stdpost->newdate = $post->newdate;
            $stdpost->sum_price = $post->sum_price;
            $data_overview[] = $stdpost;
        }                
        
    }else{
    
        if(wp_verify_nonce($_POST['_wpnonce'],'deal-stats-sales')) {
            
            $graph_title = sprintf( __('Data between %s AND %s', 'wpdeals'), $_POST['start_date'], $_POST['end_date'] );
        
            add_filter('posts_where','_deals_stats_sales_filter_where_submitted');
            add_filter('posts_clauses','_deals_stats_sales_filter_clauses');        
            $posts = new WP_Query(array(
                'posts_per_page' => -1,            
                'post_type' => 'deals-sales',
                'post_status' => 'publish',    
                'meta_key' => '_deals_sales_amount'
            ));        
            remove_filter('posts_clauses','_deals_stats_sales_filter_clauses');
            remove_filter('posts_where','_deals_stats_sales_filter_where_submitted');
            
            $data_overview = array();
            foreach($posts->posts as $post) {
                $stdpost = new stdClass();
                $stdpost->newdate = $post->newdate;
                $stdpost->sum_price = $post->sum_price;
                $data_overview[] = $stdpost;
            }
            
        }
        
    }        
    
    if(!isset($_POST['total_sales_per_month'])) {
        
        /*
        * count total sales
        */
       $post_total = new WP_Query(array(
           'posts_per_page' => -1,
           'post_type' => 'deals-sales',
           'post_status' => 'publish',        
       ));    
            
    }else{
        
        /*
        * count total sales
        */
       $post_total = new WP_Query(array(
           'posts_per_page' => -1,
           'post_type' => 'deals-sales',
           'post_status' => 'publish',
           'monthnum' => intval($_POST['total_sales_per_month'])
       ));    
        
    }
    
    $data_total_sales = $post_total->post_count;
    $data_total_sales = !empty($data_total_sales) ? $data_total_sales : 0;
       
    /*
     * count total money from sales
     */
    add_filter('posts_clauses','_deals_stats_sales_filter_clauses_total_money');
    
    $post_total_money = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type' => 'deals-sales',
        'post_status' => 'publish',        
        'meta_key' => '_deals_sales_amount'
    ));
    
    $total_money = array();
    foreach($post_total_money->posts as $post) {
        $total_money[] = $post->sum_price;
    }
    
    remove_filter('posts_clauses','_deals_stats_sales_filter_clauses_total_money');        
    $data_total_money = deals_price_format(array_sum($total_money));
    
    /*
     * get top sales
     */    
    add_filter('posts_clauses','_deals_stats_sales_filter_clauses_top_sales');
    $post_top_sales = new WP_Query(array(
        'posts_per_page' => 5,
        'post_type' => 'deals-sales',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,        
        'meta_query' => array(
            array(
                'key' => '_deals_sales_item_id'
            ),
            array(
                'key' => '_deals_sales_transaction_status',
                'value' => 'completed'
            )
        )
    ));    
    
    $data_top_sales = null;
    if(!empty($post_top_sales->posts)) {
        
        $data_top_sales = new stdClass();
        $top_sales = array();
        foreach($post_top_sales->posts as $post2) {
            //$data_top_sales->item_id = $post2->meta_value;
            //$data_top_sales->item_fav = $post2->item_fav;
            //$data_top_sales->id = $post2->ID;
            $top_sales[$post2->ID]['item_id'] = $post2->meta_value;
            $top_sales[$post2->ID]['item_fav'] = $post2->item_fav;
            $top_sales[$post2->ID]['id'] = $post2->ID;
        }
        
    }
    
    remove_filter('posts_clauses','_deals_stats_sales_filter_clauses_top_sales');
    
    //$data_top_sales = !empty($data_top_sales) ? $data_top_sales : null;
    $top_sales_props = array();
    
    if(!empty($top_sales)) {
        
        foreach($top_sales as $postid => $data_top_sales) {
            $item_props = get_post($data_top_sales['item_id']);
            //$data_top_sales_props = array('item_name' => $item_props->post_title,
            //                          'item_permalink' => admin_url('/post.php?post='.$data_top_sales->item_id.'&action=edit'),
            //                          'item_total_sales' => $data_top_sales->item_fav);
            $top_sales_props[$postid]['item_name'] = $item_props->post_title;
            $top_sales_props[$postid]['item_permalink'] = admin_url('/post.php?post='.$data_top_sales->item_id.'&action=edit');
            $top_sales_props[$postid]['item_total_sales'] = $data_top_sales['item_fav'];
        }
        
    }        
    
    /*
     * 5 latest sales
     */
    add_filter('posts_clauses','_deals_stats_sales_filter_clauses_latest_sales');
    $post_latest_sales = new WP_Query(array(
        'post_type' => 'deals-sales',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        'meta_query' => array(
            array(
                'key' => '_deals_sales_item_id'
            ),
            array(
                'key' => '_deals_sales_transaction_status',
                'value' => 'completed'
            )
        )
    ));
    
    $data_last_5_sales = array();
    if(!empty($post_latest_sales)) {
                
        foreach($post_latest_sales->posts as $post) {
            
            $sales = new stdClass();
            $sales->item_id = $post->meta_value;
            $sales->id = $post->ID;
            
            $data_last_5_sales[] = $sales;
            
        }
        
    }
    
    remove_filter('posts_clauses','_deals_stats_sales_filter_clauses_latest_sales');

    /*
     * populate data for js graph
     */
    $data_js = null;
    $total_ticks = 0;
    
    if(!empty($data_overview)) {
        
        $data_js_overview = array();
        foreach($data_overview as $dataO) {
            
            $date_js = date('Y-m-d',strtotime($dataO->newdate));
            
            if(count($data_overview) < 2) {
                $date_js_before = date('Y-m-d',strtotime('yesterday',strtotime($dataO->newdate)));
                $data_js_overview[] = '{date:"'.$date_js_before.'",amount:0}';
            }
            
            $data_js_overview[] = '{date:"'.$date_js.'",amount:'.$dataO->sum_price.'}';
            
        }
        
        $data_js = join(',',$data_js_overview);        
        
    }            
    
    ?>
    <div id="wpdeals-stats-wrapper">
            
        <div class="wpdeals-stats-clear"></div>
        <div id="wpdeals-stats-left" class="wpdeals-stats-go-left">
            
            <div id="poststuff">
                
                <div class="postbox">
                    <h3><?php _e('Date range', 'wpdeals'); ?></h3>
                    <div class="inside">
                        <form id="deal-stats" method="post" action="<?php echo admin_url('/admin.php?page=deal-stats'); ?>">
                            <?php wp_nonce_field('deal-stats-sales'); ?>
                            <?php _e('Start','wpdeals'); ?> <input type="text" name="start_date" id="js-start-date" value="<?php if(isset($_POST)) : echo $_POST['start_date']; endif; ?>"/><br />
                            <?php _e('End','wpdeals'); ?> <input type="text" name="end_date" id="js-end-date" value="<?php if(isset($_POST)) : echo $_POST['end_date']; endif; ?>" /><br />
                            <input type="submit" name="submit" value="<?php _e('Submit','wpdeals'); ?>" />
                        </form>
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                                jQuery('#js-start-date').datetimepicker({
                                    dateFormat: 'yy-mm-dd',
                                    timeFormat: 'hh:mm:ss'
                                });
                                jQuery('#js-end-date').datetimepicker({
                                    dateFormat: 'yy-mm-dd',
                                    timeFormat: 'hh:mm:ss'
                                });                                
                            });
                        </script>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3><?php _e('Total Sales', 'wpdeals'); ?></h3>
                    <div class="inside">                    
                        <p><strong><?php echo $data_total_sales.' '.__('sales','wpdeals'); ?> - <?php echo $data_total_money; ?></strong></p>
                        <form id="total_sales_month" method="post" action="<?php echo admin_url('admin.php?page=deal-stats'); ?>">
                            <select name="total_sales_per_month" id="select_total_sales_month">
                                <option value="01" <?php _deals_selected_months('01'); ?>><?php _e('January','wpdeals'); ?></option>
                                <option value="02" <?php _deals_selected_months('02'); ?>><?php _e('February','wpdeals'); ?></option>
                                <option value="03" <?php _deals_selected_months('03'); ?>><?php _e('March','wpdeals'); ?></option>
                                <option value="04" <?php _deals_selected_months('04'); ?>><?php _e('April','wpdeals'); ?></option>
                                <option value="05" <?php _deals_selected_months('05'); ?>><?php _e('May','wpdeals'); ?></option>
                                <option value="06" <?php _deals_selected_months('06'); ?>><?php _e('June','wpdeals'); ?></option>
                                <option value="07" <?php _deals_selected_months('07'); ?>><?php _e('July','wpdeals'); ?></option>
                                <option value="08" <?php _deals_selected_months('08'); ?>><?php _e('August','wpdeals'); ?></option>
                                <option value="09" <?php _deals_selected_months('09'); ?>><?php _e('September','wpdeals'); ?></option>
                                <option value="10" <?php _deals_selected_months('10'); ?>><?php _e('October','wpdeals'); ?></option>
                                <option value="11" <?php _deals_selected_months('11'); ?>><?php _e('November','wpdeals'); ?></option>
                                <option value="12" <?php _deals_selected_months('12'); ?>><?php _e('December','wpdeals'); ?></option>
                            </select>
                            <label><?php _e('Select total sales per month','wpdeals'); ?></label>
                        </form>
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                                jQuery('#select_total_sales_month').change(function(e) {
                                    jQuery('#total_sales_month').submit();
                                });
                            });
                        </script>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3><?php _e('Top Sales', 'wpdeals'); ?></h3>
                    <div class="inside">
                        <p>
                            <?php
                            //if(!is_null($data_top_sales_props['item_name'])) {
                            //    echo '<a href="'.$data_top_sales_props['item_permalink'].'">'.$data_top_sales_props['item_name'].'</a> - <span>'.$data_top_sales_props['item_total_sales'].' sales</span>';
                            //}else{
                            //    echo __('n/a','wpdeals');
                            //}
                            if(!empty($top_sales_props)) {
                                
                                echo '<ul>';
                                foreach($top_sales_props as $postid3 => $data8) {
                                    echo '<li>';
                                    echo '<a href="'.$data8['item_permalink'].'">'.$data8['item_name'].'</a> - <span>'.$data8['item_total_sales'].' sales</span>';
                                    echo '</li>';
                                }
                                echo '</ul>';
                                
                            }else{
                                echo __('n/a','wpdeals');
                            }
                            ?>
                        </p>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3><?php _e('Last 5 sales', 'wpdeals'); ?></h3>
                    <div class="inside">                                                
                        <?php
                        if(!empty($data_last_5_sales)) {
                            
                            echo '<ul>';
                            foreach($data_last_5_sales as $sales) {
                                
                                $items = get_post($sales->item_id);                            
                                echo '<li>
                                <a href="'.admin_url('/post.php?post='.$sales->item_id.'&action=edit').'">'.$items->post_title.'</a></li>';
                                
                            }
                            echo '</ul>';
                            
                        }else{
                            echo '<p>'.__('n/a','wpdeals').'</p>';
                        }
                        ?>
                    </div>
                </div>
                
            </div>
            
        </div>
        <div id="wpdeals-stats-right" class="wpdeals-stats-go-left">
            <div id="poststuff">
                <div class="postbox">
                    <h3><?php printf( __('Statistic - %s', 'wpdeals'), $graph_title); ?></h3>
                    <div class="inside">
                        <div id="wpdeals-stats-graph" style="height:400px;">
                            <!-- jqplot -->
                        </div>
                        <script type="text/javascript">
                            Morris.Line({
                                element: 'wpdeals-stats-graph',
                                data: [
                                    <?php echo $data_js; ?>
                                ],
                                xkey: 'date',
                                preUnits: '$',
                                ykeys: ['amount'],                                
                                hideHover: true,                                
                                labels: ['<?php _e('Total sales','wpdeals');?>']
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="wpdeals-stats-clear"></div>
        
    </div>
    <?php
    
}

/**
 *
 * Filter sql stats, for download overview
 * 
 * @global WPDB $wpdb
 * @param array $data
 * @return array
 */
function _deals_filter_clause_download($data) {
    
    global $wpdb;
    
    $data['fields'] = 'DATE_FORMAT(mt1.meta_value,"%d-%m-%Y") AS newdate, COUNT('.$wpdb->posts.'.ID) AS total_download';
    $data['groupby'] = 'newdate';
    return $data;
    
}

/**
 * 
 * Filter where sql query for download overview
 * 
 * @global WPDB $wpdb
 * @param string $where
 * @return string 
 */
function _deals_filter_where_download($where) {
    
    global $wpdb;
    //$where .= ' AND DATE_FORMAT(mt1.meta_value,"%d-%m-%Y") <= STR_TO_DATE("'.date('d-m-Y').'","%d-%m-%Y")';
    //$where .= ' AND mt1.meta_value BETWEEN "'.date('01-m-y').'" AND "'.date('d-m-Y').'"';
    //$where .= ' AND '.$wpdb->posts.'.post_date BETWEEN "'.date('Y-m-1 00:00:00').'" AND "'.date('Y-m-d H:i:s').'"';
    return $where;
}

/**
 *
 * Filter where sql for submitted date download data
 * 
 * @global WPDB $wpdb
 * @param string $where
 * @return string 
 */
function _deals_filter_where_dl_submitted($where) {
    
    global $wpdb;    
    $where .= ' AND DATE_FORMAT(mt1.meta_value,"%Y-%m-%d") BETWEEN "'.$_POST['start_date'].'" AND "'.$_POST['end_date'].'"';
    return $where;
}

/**
 *
 * Filter clauses total download
 * 
 * @global WPDB $wpdb
 * @param array $data
 * @return array
 */
function _deals_filter_clause_total_dl($data) {
    
    global $wpdb;
    
    $data['fields'] = 'SUM('.$wpdb->postmeta.'.meta_value) AS total_download';
    return $data;
}

/**
 * Filter clauses top download
 * 
 * @global WPDB $wpdb
 * @param array $data
 * @return $data
 */
function _deals_filter_clause_top_dl($data) {
    
    global $wpdb;
    
    $data['fields'] = 'SUM('.$wpdb->postmeta.'.meta_value) AS total_download,'.$wpdb->posts.'.ID';
    $data['groupby'] = $wpdb->posts.'.ID';
    $data['orderby'] = 'total_download DESC';
    return $data;
}

/**
 * 
 * Filter clauses last download
 * 
 * @global WPDB $wpdb
 * @param array $data
 * @return array
 */
function _deals_filter_clause_last_dl($data) {
    
    global $wpdb;
    
    $data['orderby'] = $wpdb->postmeta.'.meta_value DESC';
    $data['limit'] = 5;
    return $data;
}

/**
 * Manage download statistic
 * @return void
 */
function _deals_stats_view_download() {
    
    global $wpdb;
        
    if(!isset($_POST['submit'])) {
     
        $graph_title = __('Last 10 Download On This Month', 'wpdeals');
        
        add_filter('posts_where','_deals_filter_where_download');
        add_filter('posts_clauses','_deals_filter_clause_download');   
        
        $posts = new WP_Query(array(
            'posts_per_page' => 10,            
            'post_type' => 'daily-deals',
            'post_status' => 'publish',    
            'meta_query' => array(
                array(
                    'key' => '_download_count'
                ),
                array(
                    'key' => '_download_date',
                    'type' => 'datetime',
                    'compare' => 'BETWEEN',
                    'value' => array(date('Y-m-01 00:00:00'),date('Y-m-d H:i:s'))
                )
            )
        ));  
        
        remove_filter('posts_clauses','_deals_filter_clause_download');
        remove_filter('posts_where','_deals_filter_where_download');
        
        $data_overview = array();
        foreach($posts->posts as $post) {
            $stdpost = new stdClass();
            $stdpost->newdate = $post->newdate;
            $stdpost->total_download = $post->total_download;
            $data_overview[] = $stdpost;
        }        
        
    }else{
        
        if(wp_verify_nonce($_POST['_wpnonce'],'deal-stats-download')) {
            
            $graph_title = 'Data between '.$_POST['start_date'].' AND '.$_POST['end_date'].'';
        
            add_filter('posts_where','_deals_filter_where_dl_submitted');
            add_filter('posts_clauses','_deals_filter_clause_download');   
            
            $posts = new WP_Query(array(
                'posts_per_page' => 10,            
                'post_type' => 'daily-deals',
                'post_status' => 'publish',    
                'meta_query' => array(
                    array(
                        'key' => '_download_count'
                    ),
                    array(
                        'key' => '_download_date'                    
                    )
                )
            ));  
            
            remove_filter('posts_clauses','_deals_filter_clause_download');
            remove_filter('posts_where','_deals_filter_where_dl_submitted');
            
            $data_overview = array();
            foreach($posts->posts as $post) {
                $stdpost = new stdClass();
                $stdpost->newdate = $post->newdate;
                $stdpost->total_download = $post->total_download;
                $data_overview[] = $stdpost;
            }   
            
        }
        
    }        
    
    /*
     * compute total download
     */
    add_filter('posts_clauses','_deals_filter_clause_total_dl');
    $post_total_dl = new WP_Query(array(
        'post_type' => 'daily-deals',
        'post_status' => 'publish',
        'meta_key' => '_download_count'
    ));
    
    $data_total_dl = 0;
    if(!empty($post_total_dl)) {
        
        foreach($post_total_dl->posts as $post) {
            $data_total_dl += $post->total_download;
        }
        
    }
    
    remove_filter('posts_clauses','_deals_filter_clause_total_dl');    
    
    /*
     * compute top download
     */
    add_filter('posts_clauses','_deals_filter_clause_top_dl');
    $post_top_dl = new WP_Query(array(
        'posts_per_page' => 1,
        'post_type' => 'daily-deals',
        'post_status' => 'publish',
        'meta_key' => '_download_count'
    ));
        
    $data_top_dl = null;
    if(!empty($post_top_dl)) {
        
        $data_top_dl = new stdClass();
        foreach($post_top_dl as $post) {
            $data_top_dl->item_id = $post->ID;
            $data_top_dl->item_fav = $post->total_download;
        }
        
    }
    remove_filter('posts_clauses','_deals_filter_clause_top_dl');    
    wp_reset_query();
    
    $data_top_dl = !empty($data_top_dl) ? $data_top_dl : null;
    
    if(!is_null($data_top_dl)) {    
                            
        $manual_query = new WP_Query(array(
            'p' => $data_top_dl->item_id,
            'post_type' => 'daily-deals',
            'post_status' => 'publish'
        ));
        
        $item_props = $manual_query->post;
        $data_top_dl_props = array('item_name' => $item_props->post_title,
                                    'item_permalink' => admin_url('post.php?post='.$data_top_dl->item_id.'&action=edit'),
                                    'item_total_download' => $data_top_dl->item_fav);        
        
    }else{
        $data_top_dl_props = array('item_name' => null,
                                    'item_permalink' => null,
                                    'item_total_download' => null);
    }
    
    /*
     * compute last 5 download
     */
    add_filter('posts_clauses','_deals_filter_clause_last_dl');
    $post_last_five = new WP_Query(array(
        'posts_per_page' => 5,
        'post_type' => 'daily-deals',
        'post_status' => 'publish',
        'meta_key' => '_download_date'
    ));
    
    $data_last_5_dl = '';
    if(!empty($post_last_five)) {
        
        $data_last_5_dl = array();
        $postlast = new stdClass();
        
        foreach($post_last_five->posts as $post) {
            
            $postlast->item_id = $post->ID;
            $data_last_5_dl[] = $postlast;
            
        }
        
    }
    
    remove_filter('posts_clauses','_deals_filter_clause_last_dl');    
    
    $data_js = null;    
    
    if(!empty($data_overview)) {
        
        $data_js_overview = array();
        foreach($data_overview as $dataO) {
            
            $date_js = date('Y-m-d',strtotime($dataO->newdate));
            
            if(count($data_overview) < 2) {
                $date_js_before = date('Y-m-d',strtotime('yesterday',strtotime($dataO->newdate)));
                $data_js_overview[] = '{date:"'.$date_js_before.'",total:0}';
            }
            
            $data_js_overview[] = '{date:"'.$date_js.'",total:'.$dataO->total_download.'}';
            
        }
        
        $data_js = join(',',$data_js_overview);        
        
    }                
    
    ?>
    <div id="wpdeals-stats-wrapper">
            
        <div class="wpdeals-stats-clear"></div>
        <div id="wpdeals-stats-left" class="wpdeals-stats-go-left">
            
            <div id="poststuff">
                
                <div class="postbox">
                    <h3><?php _e('Date range','wpdeals'); ?></h3>
                    <div class="inside">
                        <form method="post" action="<?php echo admin_url('/admin.php?page=deal-stats&tab=download'); ?>">
                            <?php wp_nonce_field('deal-stats-download'); ?>
                            <?php _e('Start','wpdeals'); ?> <input type="text" name="start_date" id="js-start-date" value="<?php if(isset($_POST)) : echo $_POST['start_date']; endif; ?>" /><br />
                            <?php _e('End','wpdeals'); ?> <input type="text" name="end_date" id="js-end-date" value="<?php if(isset($_POST)) : echo $_POST['end_date']; endif; ?>" /><br />
                            <input type="submit" name="submit" value="<?php _e('Submit','wpdeals'); ?>" />
                        </form>
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                                jQuery('#js-start-date').datetimepicker({
                                    dateFormat: 'yy-mm-dd',
                                    timeFormat: 'hh:mm:ss'
                                });
                                jQuery('#js-end-date').datetimepicker({
                                    dateFormat: 'yy-mm-dd',
                                    timeFormat: 'hh:mm:ss'
                                });  
                            });
                        </script>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3><?php _e('Total Download','wpdeals'); ?></h3>
                    <div class="inside">
                        <p><strong><?php echo $data_total_dl; ?></strong></p>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3><?php _e('Top Download','wpdeals'); ?></h3>
                    <div class="inside">
                        <p>
                            <?php                            
                            if(!is_null($data_top_dl_props['item_name'])) {
                                echo '<a href="'.$data_top_dl_props['item_permalink'].'">'.$data_top_dl_props['item_name'].'</a> - <span>'.$data_top_dl_props['item_total_download'].' downloads</span>';
                            }else{
                                echo __('n/a','wpdeals');
                            }
                            ?>
                        </p>
                    </div>
                </div>
                
                <div class="postbox">
                    <h3><?php _e('Last 5 Download','wpdeals'); ?></h3>
                    <div class="inside">                                                
                        <?php
                        if(!empty($data_last_5_dl)) {
                            
                            echo '<ul>';
                            foreach($data_last_5_dl as $dl) {
                                
                                $manual_query = new WP_Query(array(
                                    'p' => $dl->item_id,
                                    'post_type' => 'daily-deals',
                                    'post_status' => 'publish'
                                ));
                                
                                $items = $manual_query->post;                                
                                echo '<li>
                                <a href="'.admin_url('post.php?post='.$dl->item_id.'&action=edit').'">'.$items->post_title.'</a></li>';
                                
                            }
                            echo '</ul>';
                            
                        }else{
                            echo '<p>'.__('n/a','wpdeals').'</p>';
                        }
                        ?>
                    </div>
                </div>
                
            </div>
            
        </div>
        <div id="wpdeals-stats-right" class="wpdeals-stats-go-left">
            <div id="poststuff">
                <div class="postbox">
                    <h3><?php _e('Statistic','wpdeals');?> - <?php echo $graph_title; ?></h3>
                    <div class="inside">
                        <div id="wpdeals-dl-graph" style="height:400px;">
                            <!-- jqplot -->                            
                        </div>
                        <script type="text/javascript">
                            Morris.Line({
                                element: 'wpdeals-dl-graph',
                                data: [<?php echo $data_js; ?>],                                
                                xkey: 'date',
                                ykeys: ['total'],                                
                                hideHover: true,
                                labels: ['<?php _e('Total download','wpdeals'); ?>']
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="wpdeals-stats-clear"></div>
        
    </div>
    <?php
    
}