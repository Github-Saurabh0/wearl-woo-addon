<?php
/**
 * Plugin Name: Wearl Bulk Post Update Date
 * Plugin URI: https://wearl.co.in/
 * Description: Change the Post Update date for posts and pages in bulk. Keep your blog fresh for SEO.
 * Version: 1.0.0
 * Author: Wearl Technologies
 * Author URI: https://wearl.co.in/
 * License: GPL2
 * Text Domain: wearl-bulk-post-update-date
 */

if (! defined('ABSPATH')) exit;

define('WWPUD_PATH', plugin_dir_path(__FILE__));
define('WWPUD_URL', plugin_dir_url(__FILE__));

// Include core functions
require_once WWPUD_PATH . 'inc.php';

// Admin menu
add_action('admin_menu', function(){
    add_menu_page('Bulk Post Update Date', 'Bulk Post Update Date', 'manage_options', 'wearl-bulk-post-update-date', 'wwpud_page', 'dashicons-clock', 80);
});

function wwpud_page() {
    // tabs: posts | pages | comments | custom
    $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'posts';
    ?>
    <div class="wrap wwpud-wrap">
        <h1>Bulk Post Update Date</h1>
        <p class="description">Change the Post Update date for all posts and pages in one click. This will help your blog in search engines and your blog will look alive. Do this every week or month.</p>

        <h2 class="nav-tab-wrapper">
            <a class="nav-tab <?php echo $tab === 'posts' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('admin.php?page=wearl-bulk-post-update-date&tab=posts'); ?>">Posts</a>
            <a class="nav-tab <?php echo $tab === 'pages' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('admin.php?page=wearl-bulk-post-update-date&tab=pages'); ?>">Pages</a>
            <a class="nav-tab <?php echo $tab === 'comments' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('admin.php?page=wearl-bulk-post-update-date&tab=comments'); ?>">Comments</a>
            <a class="nav-tab <?php echo $tab === 'custom' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('admin.php?page=wearl-bulk-post-update-date&tab=custom'); ?>">Custom</a>
        </h2>

        <div class="wwpud-tab-content">
            <?php
            if ($tab === 'posts') {
                include WWPUD_PATH . 'posts.php';
            } elseif ($tab === 'pages') {
                include WWPUD_PATH . 'pages.php';
            } elseif ($tab === 'comments') {
                include WWPUD_PATH . 'comments.php';
            } else {
                include WWPUD_PATH . 'custom.php';
            }
            ?>
        </div>

        <div class="wwpud-footer-panel">
            <div class="wwpud-coffee">
                <?php echo file_get_contents(WWPUD_PATH . 'coffee.svg'); ?>
            </div>
            <div class="wwpud-coffee-text">
                <h3>Buy me a coffee!</h3>
                <p>Thank you for using Bulk Post Update Date. If you found the plugin useful buy me a coffee! Your donation will motivate and make me happy for all the efforts.</p>
                <p class="wwpud-links">Developed with ♥ by Wearl Technologies</p>
                <a class="button button-primary" href="https://wearl.co.in/" target="_blank" rel="noopener">Visit Wearl</a>
            </div>
        </div>
    </div>
    <?php
}
?>
