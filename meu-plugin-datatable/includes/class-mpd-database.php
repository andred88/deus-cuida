<?php
class MPD_Database {
    public static function get_membros($limit = 20, $offset = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'mpd_membros';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table LIMIT %d OFFSET %d", $limit, $offset));
    }

    public static function get_membro($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'mpd_membros';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $id));
    }
}