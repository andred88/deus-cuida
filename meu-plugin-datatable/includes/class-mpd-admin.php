<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MPD_Admin {

    public function run() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Adiciona menus e submenus do plugin
     */
    public function add_menu() {
        // ✅ Menu principal: Membros
        add_menu_page(
            'Membros',
            'Membros',
            'manage_members', // Capacidade personalizada
            'mpd-list',
            [$this, 'list_page'],
            'dashicons-groups',
            25
        );

        // Submenu: Adicionar Membro
        add_submenu_page(
            'mpd-list',
            'Adicionar Membro',
            'Adicionar',
            'manage_members',
            'mpd-add',
            [$this, 'add_page']
        );

        // Submenu oculto: Editar Membro
        add_submenu_page(
            null,
            'Editar Membro',
            'Editar',
            'manage_members',
            'mpd-edit',
            [$this, 'edit_page']
        );

        // ✅ Submenu: Acompanhamentos
        add_submenu_page(
            'mpd-list',
            'Acompanhamentos',
            'Acompanhamentos',
            'manage_members',
            'mpd-acompanhamentos',
            [$this, 'render_acompanhamentos_page']
        );

        // ✅ Menu principal: Férias
        add_menu_page(
            'Férias',
            'Férias',
            'manage_ferias', // Capacidade personalizada
            'mpd-ferias',
            function() {
                include plugin_dir_path(__FILE__) . '../templates/admin-ferias-list.php';
            },
            'dashicons-calendar-alt',
            26
        );

        // Submenu: Adicionar Férias
        add_submenu_page(
            'mpd-ferias',
            'Adicionar Férias',
            'Adicionar Férias',
            'manage_ferias',
            'mpd-ferias-add',
            function() {
                include plugin_dir_path(__FILE__) . '../templates/admin-ferias-add.php';
            }
        );

        // Submenu oculto: Editar Férias
        add_submenu_page(
            null,
            'Editar Férias',
            'Editar Férias',
            'manage_ferias',
            'mpd-ferias-edit',
            function() {
                include plugin_dir_path(__FILE__) . '../templates/admin-ferias-edit.php';
            }
        );
    }

    /**
     * Carrega CSS e JS necessários
     */
    public function enqueue_assets($hook) {
        if (strpos($hook, 'mpd') === false) return;

        // CSS do DataTables
        wp_enqueue_style('mpd-datatables-css', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');
        wp_enqueue_style('datatables-buttons-css', 'https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css');

        // Scripts principais
        wp_enqueue_script('jquery');
        wp_enqueue_script('mpd-datatables-js', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['jquery'], null, true);

        // Scripts para botões de exportação
        wp_enqueue_script('datatables-buttons-js', 'https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['jquery'], null, true);
        wp_enqueue_script('datatables-buttons-html5-js', 'https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', ['jquery'], null, true);
        wp_enqueue_script('datatables-buttons-print-js', 'https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js', ['jquery'], null, true);
        wp_enqueue_script('datatables-buttons-pdf-js', 'https://cdn.datatables.net/buttons/2.4.1/js/buttons.pdf.min.js', ['jquery'], null, true);

        // Script customizado do plugin
        wp_enqueue_script('mpd-js', MPD_URL . 'assets/js/datatable-init.js', ['jquery'], null, true);

        // Para upload com preview (se necessário)
        wp_enqueue_media();
    }

    /**
     * Callbacks das páginas
     */
    public function list_page() {
        include MPD_PATH . 'templates/admin-list.php';
    }

    public function add_page() {
        include MPD_PATH . 'templates/admin-add.php';
    }

    public function edit_page() {
        include MPD_PATH . 'templates/admin-edit.php';
    }

    public function render_acompanhamentos_page() {
        if (isset($_GET['action']) && $_GET['action'] === 'add') {
            include MPD_PATH . 'templates/admin-acomp-add.php';
        } else {
            include MPD_PATH . 'templates/admin-acomp-list.php';
        }

        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'mpd_delete_acomp_' . $id)) {
                wp_die('Ação não autorizada.');
            }
            global $wpdb;
            $table_name = $wpdb->prefix . 'mpd_acompanhamentos';
            $wpdb->delete($table_name, ['id' => $id], ['%d']);
            echo '<div class="updated"><p>Acompanhamento excluído com sucesso!</p></div>';
        }

        if (isset($_GET['action']) && $_GET['action'] === 'edit') {
            include MPD_PATH . 'templates/admin-acomp-edit.php';
            return;
        }
    }
}
