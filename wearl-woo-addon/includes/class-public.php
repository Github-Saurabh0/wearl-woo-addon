<?php
class WWA_Public {

    public function enqueue_styles() {
        wp_enqueue_style('wwa-public-style', WWA_URL . 'public/css/public-style.css', [], '1.0.0');
        wp_enqueue_script('wwa-public-script', WWA_URL . 'public/js/public-script.js', ['jquery'], '1.0.0', true);
    }
}
