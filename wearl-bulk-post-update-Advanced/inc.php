<?php
// Core processing functions and handlers

if (! defined('ABSPATH')) exit;

// Register handlers for admin-post actions
add_action('admin_post_wwpud_update_posts', 'wwpud_handle_update_posts');
add_action('admin_post_wwpud_update_pages', 'wwpud_handle_update_pages');
add_action('admin_post_wwpud_update_comments', 'wwpud_handle_update_comments');

function wwpud_handle_update_posts() {
    if (! current_user_can('manage_options')) wp_die('Not allowed');
    check_admin_referer('wwpud_posts_action', 'wwpud_posts_nonce');

    $mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : 'modified';
    $distribute = isset($_POST['distribute']) ? sanitize_text_field($_POST['distribute']) : '7_days';
    $cats = isset($_POST['cats']) ? array_map('intval', (array) $_POST['cats']) : [];

    // build list of posts
    $args = ['post_type'=>'post','posts_per_page'=>-1,'post_status'=>'publish'];
    if (! empty($cats)) $args['category__in'] = $cats;
    $posts = get_posts($args);

    // compute times for distribution
    $dates = wwpud_generate_dates_for_items($posts, $distribute, 'cat_date', 'cat_rand');

    foreach ($posts as $i => $post) {
        if (!isset($dates[$i])) continue;
        $dt = $dates[$i];
        wwpud_update_post_dates($post->ID, $dt, $mode);
    }

    wp_redirect(admin_url('admin.php?page=wearl-bulk-post-update-date&tab=posts&updated=1'));
    exit;
}

function wwpud_handle_update_pages() {
    if (! current_user_can('manage_options')) wp_die('Not allowed');
    check_admin_referer('wwpud_pages_action', 'wwpud_pages_nonce');

    $mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : 'modified';
    $distribute = isset($_POST['distribute']) ? sanitize_text_field($_POST['distribute']) : '7_days';
    $sel_pages = isset($_POST['pages']) ? array_map('intval', (array) $_POST['pages']) : [];

    $args = ['post_type'=>'page','posts_per_page'=>-1,'post_status'=>'publish'];
    if (! empty($sel_pages)) $args['post__in'] = $sel_pages;
    $pages = get_posts($args);

    $dates = wwpud_generate_dates_for_items($pages, $distribute, 'page_date', 'page_rand');

    foreach ($pages as $i => $page) {
        if (!isset($dates[$i])) continue;
        $dt = $dates[$i];
        wwpud_update_post_dates($page->ID, $dt, $mode);
    }

    wp_redirect(admin_url('admin.php?page=wearl-bulk-post-update-date&tab=pages&updated=1'));
    exit;
}

function wwpud_handle_update_comments() {
    if (! current_user_can('manage_options')) wp_die('Not allowed');
    check_admin_referer('wwpud_comments_action', 'wwpud_comments_nonce');

    // Placeholder: no-op, redirect back
    wp_redirect(admin_url('admin.php?page=wearl-bulk-post-update-date&tab=comments&updated=1'));
    exit;
}

/**
 * Update post dates (post_date/post_modified) safely.
 */
function wwpud_update_post_dates($post_id, $datetime, $mode = 'modified') {
    $gmt = get_gmt_from_date($datetime);
    $data = ['ID' => $post_id];
    if ($mode === 'modified' || $mode === 'both') {
        $data['post_modified'] = $datetime;
        $data['post_modified_gmt'] = $gmt;
    }
    if ($mode === 'published' || $mode === 'both') {
        $data['post_date'] = $datetime;
        $data['post_date_gmt'] = $gmt;
    }
    wp_update_post($data);
}

/**
 * Generate array of datetimes for items based on distribution rule
 * Simple approach:
 * - If distribute is 'custom' we look for custom_from/custom_to in $_POST (YYYY-MM-DD)
 * - If random days provided per item (like cat_rand or page_rand) we honor it (handled by caller names)
 */
function wwpud_generate_dates_for_items($items, $distribute, $date_field_prefix = '', $rand_field_prefix = '') {
    $count = count($items);
    $results = [];

    // Custom range
    if ($distribute === 'custom') {
        $from = isset($_POST['custom_from']) ? sanitize_text_field($_POST['custom_from']) : '';
        $to = isset($_POST['custom_to']) ? sanitize_text_field($_POST['custom_to']) : '';
        if ($from && $to) {
            $from_ts = strtotime($from);
            $to_ts = strtotime($to);
            if ($from_ts === false || $to_ts === false || $to_ts < $from_ts) {
                // invalid range - fallback to today
                $to_ts = time();
                $from_ts = strtotime('-7 days', $to_ts);
            }
            // distribute evenly
            for ($i=0;$i<$count;$i++) {
                $frac = $count === 1 ? 0.5 : ($i / ($count - 1));
                $ts = intval($from_ts + ($to_ts - $from_ts) * $frac);
                $results[$i] = date('Y-m-d H:i:s', $ts);
            }
            return $results;
        }
    }

    // Predefined ranges: interpret as max days back
    $map = [
        '1_hour' => 1/24,
        '6_hours' => 6/24,
        '12_hours' => 12/24,
        '1_day' => 1,
        '7_days' => 7,
        '30_days' => 30,
    ];
    $days = isset($map[$distribute]) ? $map[$distribute] : 7;
    $now = time();
    // Spread dates randomly across the range for realistic distribution
    foreach ($items as $i => $it) {
        // check if there's a specific rand field for the item (e.g., page_rand[ID])
        $rand_days = 0;
        if ($rand_field_prefix && isset($_POST[$rand_field_prefix]) && is_array($_POST[$rand_field_prefix])) {
            $id = isset($it->ID) ? $it->ID : null;
            if ($id && isset($_POST[$rand_field_prefix][$id])) {
                $rand_days = intval($_POST[$rand_field_prefix][$id]);
            }
        }
        if ($rand_days > 0) {
            $ts = strtotime("-{$rand_days} days", $now);
            // randomize time during that day
            $ts = $ts - rand(0, 86400);
        } else {
            // random timestamp within the last $days
            $ts = $now - rand(0, intval($days * 86400));
        }
        $results[$i] = date('Y-m-d H:i:s', $ts);
    }
    return $results;
}
