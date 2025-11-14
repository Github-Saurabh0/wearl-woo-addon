<?php
/**
 * Plugin Name: Wearl Woo Addon
 * Plugin URI: https://wearl.co.in/
 * Description: A WooCommerce addon plugin by Wearl Technologies to enhance store functionality. (Pure Boilerplate)
 * Version: 1.0.0
 * Author: Wearl Technologies
 * Author URI: https://wearl.co.in/
 * License: GPL2
 * Text Domain: wearl-woo-addon
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

if (!defined('WWA_PATH')) {
    define('WWA_PATH', plugin_dir_path(__FILE__));
}
if (!defined('WWA_URL')) {
    define('WWA_URL', plugin_dir_url(__FILE__));
}

// Include Core Files
require_once WWA_PATH . 'includes/class-core.php';

// Activation/Deactivation Hooks
register_activation_hook(__FILE__, ['WWA_Core', 'activate']);
register_deactivation_hook(__FILE__, ['WWA_Core', 'deactivate']);

// Initialize Plugin
function run_wwa() {
    $plugin = new WWA_Core();
    $plugin->run();
}
run_wwa();
