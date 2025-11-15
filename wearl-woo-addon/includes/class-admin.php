<?php
class WWA_Admin {

    public function enqueue_styles() {
        wp_enqueue_style('wwa-admin-style', WWA_URL . 'admin/css/admin-style.css', [], '1.0.0');
        wp_enqueue_script('wwa-admin-script', WWA_URL . 'admin/js/admin-script.js', ['jquery'], '1.0.0', true);
    }
}
