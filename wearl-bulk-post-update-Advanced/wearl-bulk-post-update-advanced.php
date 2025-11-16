<?php
/**
 * Plugin Name: Wearl Bulk Post Update Date - Advanced
 * Description: Bulk update Published/Modified dates for posts, pages and custom post types with category and per-item controls.
 * Version: 1.0.0
 * Author: Wearl Technologies
 * Author URI: https://wearl.co.in
 * License: GPL2
 */

if (!defined('ABSPATH')) exit;

class WWA_Advanced {

    public function __construct() {
        add_action('admin_menu', [$this,'menu']);
        add_action('admin_enqueue_scripts', [$this,'assets']);
        add_action('admin_post_wwa_process', [$this,'process']);
    }

    public function menu(){
        add_menu_page(
            'Wearl Bulk Post Update Date',
            'Wearl Bulk Update',
            'manage_options',
            'wwa-bulk',
            [$this,'page'],
            'dashicons-update',
            80
        );
    }

    public function assets($hook){
        if($hook !== 'toplevel_page_wwa-bulk') return;
        // jQuery UI datepicker is included with WP as jquery-ui-datepicker
        wp_enqueue_style('jquery-ui-css','https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('wwa-admin-js', plugin_dir_url(__FILE__) . 'assets/admin.js', ['jquery','jquery-ui-datepicker'], '1.0', true);
        wp_enqueue_style('wwa-admin-css', plugin_dir_url(__FILE__) . 'assets/admin.css');
        // pass data if needed
        wp_localize_script('wwa-admin-js','WWAData',[]);
    }

    public function page(){
        if(!current_user_can('manage_options')) wp_die('Not allowed');

        // Get all categories and all pages
        $cats = get_categories(['hide_empty'=>false]);
        $pages = get_posts(['post_type'=>'page','posts_per_page'=>-1]);

        ?>
        <div class="wrap wwa-wrap">
            <h1>Wearl Bulk Post Update Date</h1>
            <p>Update dates in bulk. Choose categories or pages, select mode and dates.</p>

            <?php if(isset($_GET['updated'])): ?>
                <div class="updated notice"><p>Bulk update completed.</p></div>
            <?php endif; ?>

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('wwa_bulk_action'); ?>
                <input type="hidden" name="action" value="wwa_process">

                <h2>Posts by Category</h2>
                <p>Select categories and set a date/time or a random range for each category.</p>
                <div class="wwa-cats">
                    <?php foreach($cats as $cat): ?>
                        <div class="wwa-cat">
                            <label class="wwa-cat-title">
                                <input type="checkbox" name="cats[]" value="<?php echo esc_attr($cat->term_id); ?>"> 
                                <?php echo esc_html($cat->name); ?>
                            </label>
                            <div class="wwa-controls">
                                <label>Date: <input type="text" name="cat_date[<?php echo $cat->term_id; ?>]" class="wwa-datepicker" placeholder="YYYY-MM-DD"></label>
                                <label>Time: <input type="time" name="cat_time[<?php echo $cat->term_id; ?>]"></label>
                                <label>Or random days back: <input type="number" name="cat_rand[<?php echo $cat->term_id; ?>]" min="0" max="365" value="0"></label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h2>Pages</h2>
                <p>Select pages to update individually.</p>
                <div class="wwa-pages">
                    <?php foreach($pages as $p): ?>
                        <div class="wwa-page-item">
                            <label><input type="checkbox" name="pages[]" value="<?php echo esc_attr($p->ID); ?>"> <?php echo esc_html($p->post_title); ?></label>
                            <div class="wwa-controls-inline">
                                <input type="text" name="page_date[<?php echo $p->ID; ?>]" class="wwa-datepicker" placeholder="YYYY-MM-DD">
                                <input type="time" name="page_time[<?php echo $p->ID; ?>]">
                                <label>Rand days back: <input type="number" name="page_rand[<?php echo $p->ID; ?>]" min="0" max="365" value="0"></label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h2>General Options</h2>
                <label><input type="radio" name="mode" value="modified" checked> Update Modified Date</label>
                &nbsp;
                <label><input type="radio" name="mode" value="published"> Update Published Date</label>
                &nbsp;
                <label><input type="radio" name="mode" value="both"> Update Both</label>

                <p><button class="button button-primary" type="submit">Run Bulk Update</button></p>
            </form>
        </div>
        <?php
    }

    public function process(){
        if(!current_user_can('manage_options')) wp_die('Not allowed');
        check_admin_referer('wwa_bulk_action');

        $mode = isset($_POST['mode'])?sanitize_text_field($_POST['mode']):'modified';

        // Handle categories
        if(!empty($_POST['cats']) && is_array($_POST['cats'])){
            foreach($_POST['cats'] as $cat){
                $cat = intval($cat);
                $args = ['category' => $cat, 'posts_per_page'=>-1, 'post_type'=>'post'];
                $posts = get_posts($args);
                foreach($posts as $post){
                    $new_dt = $this->compute_date(
                        isset($_POST['cat_date'][$cat])?sanitize_text_field($_POST['cat_date'][$cat]):'',
                        isset($_POST['cat_time'][$cat])?sanitize_text_field($_POST['cat_time'][$cat]):'',
                        isset($_POST['cat_rand'][$cat])?intval($_POST['cat_rand'][$cat]):0
                    );
                    if($new_dt){
                        $this->update_post_date($post->ID, $new_dt, $mode);
                    }
                }
            }
        }

        // Handle pages
        if(!empty($_POST['pages']) && is_array($_POST['pages'])){
            foreach($_POST['pages'] as $pid){
                $pid = intval($pid);
                $new_dt = $this->compute_date(
                    isset($_POST['page_date'][$pid])?sanitize_text_field($_POST['page_date'][$pid]):'',
                    isset($_POST['page_time'][$pid])?sanitize_text_field($_POST['page_time'][$pid]):'',
                    isset($_POST['page_rand'][$pid])?intval($_POST['page_rand'][$pid]):0
                );
                if($new_dt){
                    $this->update_post_date($pid, $new_dt, $mode);
                }
            }
        }

        wp_redirect(admin_url('admin.php?page=wwa-bulk&updated=1'));
        exit;
    }

    private function compute_date($date_str, $time_str, $rand_days){
        if($rand_days && intval($rand_days)>0){
            $new = date('Y-m-d H:i:s', strtotime('-'.intval($rand_days).' days'));
            return $new;
        }
        if($date_str){
            $time = $time_str?:'00:00:00';
            return date('Y-m-d H:i:s', strtotime($date_str . ' ' . $time));
        }
        return false;
    }

    private function update_post_date($post_id, $datetime, $mode){
        $gmt = get_gmt_from_date($datetime);
        $data = ['ID'=>$post_id];
        if($mode==='modified' || $mode==='both'){
            $data['post_modified'] = $datetime;
            $data['post_modified_gmt'] = $gmt;
        }
        if($mode==='published' || $mode==='both'){
            $data['post_date'] = $datetime;
            $data['post_date_gmt'] = $gmt;
        }
        wp_update_post($data);
    }
}

new WWA_Advanced();
