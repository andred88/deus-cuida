<?php
class MPD_Activator {
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Tabela de membros
        $table_membros = $wpdb->prefix . 'mpd_membros';
        $sql_membros = "CREATE TABLE $table_membros (
            id INT AUTO_INCREMENT,
            foto VARCHAR(255),
            nome VARCHAR(255) NOT NULL,
            celular VARCHAR(20) NOT NULL,
            email VARCHAR(100),
            nascimento DATE NOT NULL,
            estado_civil VARCHAR(50) NOT NULL,
            conjugue VARCHAR(255),
            filhos TEXT,
            escolaridade VARCHAR(100) NOT NULL,
            curso VARCHAR(255),
            beneficio VARCHAR(100) NOT NULL,
            grau_pertencimento VARCHAR(100) NOT NULL,
            modo_compromisso VARCHAR(50) NOT NULL,
            acolhimento DATE NOT NULL,
            etapa DATE NOT NULL,
            ferias TEXT,
            ajuda_custo BOOLEAN,
            recanto VARCHAR(100) NOT NULL,
            funcao VARCHAR(100),
            formador VARCHAR(255) NOT NULL,
            observacoes TEXT,
            anexos TEXT,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Tabela de acompanhamentos
        $table_acomp = $wpdb->prefix . 'mpd_acompanhamentos';
        $sql_acomp = "CREATE TABLE $table_acomp (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            member_id mediumint(9) NOT NULL,
            formador varchar(100) NOT NULL,
            data_hora datetime NOT NULL,
            descricao text NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Tabela de férias

        $table_ferias = $wpdb->prefix . 'mpd_ferias';
        $sql_ferias = "CREATE TABLE $table_ferias (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            membro_id BIGINT(20) UNSIGNED NOT NULL,
            ja_tirou_ferias TINYINT(1) DEFAULT 0,
            programacao_inicio_1 DATE NULL,
            programacao_fim_1 DATE NULL,
            programacao_inicio_2 DATE NULL,
            programacao_fim_2 DATE NULL,
            tiradas_inicio_1 DATE NULL,
            tiradas_fim_1 DATE NULL,
            tiradas_inicio_2 DATE NULL,
            tiradas_fim_2 DATE NULL,
            ajuda_custo TINYINT(1) DEFAULT 0,
            valor_ajuda DECIMAL(10,2) DEFAULT 0.00,
            observacoes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY membro_id (membro_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_membros);
        dbDelta($sql_acomp);
        dbDelta($sql_ferias);

        // Adiciona o papel de 'formador' com capacidades específicas       
        if (!get_role('formador')) {
            add_role('formador', __('Formador', 'meu-plugin'), ['read' => true]);
        }

        $formador = get_role('formador');
        $admin = get_role('administrator');

        $caps = ['manage_members', 'edit_members', 'delete_members', 'add_members', 'add_acomp', 'del_acomp', 'edit_acomp', 'manage_ferias', 'edit_ferias'];

            foreach ($caps as $cap) {
                if ($formador) {
                    $formador->add_cap($cap);
                }
                if ($admin) {
                    $admin->add_cap($cap);
                }
            }     

    }
}