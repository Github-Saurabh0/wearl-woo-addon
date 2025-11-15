<?php
class WWA_Core {

    public function __construct() {
        require_once WWA_PATH . 'includes/class-admin.php';
        require_once WWA_PATH . 'includes/class-public.php';
    }

    public static function activate() {
        require_once WWA_PATH . 'includes/class-activator.php';
        WWA_Activator::activate();
    }

    public static function deactivate() {
        require_once WWA_PATH . 'includes/class-deactivator.php';
        WWA_Deactivator::deactivate();
    }

    public function run() {
        $admin = new WWA_Admin();
        $public = new WWA_Public();

        add_action('admin_enqueue_scripts', [$admin, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$public, 'enqueue_styles']);
    }
}
