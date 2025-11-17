<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MPD_Detail {
    public function register_page() {
        add_submenu_page(
            null,
            __('Detalhes do Membro', 'meu-plugin'),
            __('Detalhes', 'meu-plugin'),
            'manage_options',
            'mpd-detail',
            [$this, 'render_page']
        );
    }

    public function render_page() {
        include MPD_PATH . 'templates/mpd-detail.php';
    }
}