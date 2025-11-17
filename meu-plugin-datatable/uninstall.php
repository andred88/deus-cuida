<?php
/**
 * Executado quando o plugin é desinstalado.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Verificar permissão
if ( ! current_user_can( 'activate_plugins' ) ) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'mpd_membros';

// Remover tabela
$wpdb->query("DROP TABLE IF EXISTS $table");