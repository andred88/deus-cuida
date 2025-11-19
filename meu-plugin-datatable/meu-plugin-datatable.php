<?php
/**
 * Plugin Name: Deus Cuida - Gestao de Membros BTH 1.1
 * Description: Gerenciamento de membros com DataTables, segurança reforçada e estrutura modular.
 * Version: 1.1
 * Author: André Deitos - Bethania TECH
 * Text Domain: meu-plugin
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('MPD_PATH', plugin_dir_path(__FILE__));
define('MPD_URL', plugin_dir_url(__FILE__));

// Carregar classes
require_once MPD_PATH . 'includes/class-mpd-activator.php';
require_once MPD_PATH . 'includes/class-mpd-deactivator.php';
require_once MPD_PATH . 'includes/class-mpd-admin.php';
require_once MPD_PATH . 'includes/class-mpd-database.php';
require_once MPD_PATH . 'includes/class-mpd-detail.php';

register_activation_hook(__FILE__, ['MPD_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['MPD_Deactivator', 'deactivate']);

function run_meu_plugin() {
    $plugin = new MPD_Admin();
    $plugin->run();
}
run_meu_plugin();


add_action('admin_menu', function() {
    $detail = new MPD_Detail();
    $detail->register_page();
});
