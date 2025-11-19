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

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_membros);
        dbDelta($sql_acomp);

        
        if (!get_role('formador')) {
            add_role(
                'formador',
                    __('Formador', 'meu-plugin'),
                    array('read' => true)
                );
        }

        // Adiciona capabilities à role
        $role = get_role('formador');
        if ($role) {
            $role->add_cap('manage_members');        // Gerenciar membros
            $role->add_cap('edit_members');          // Editar membros
            $role->add_cap('delete_members');        // Remover membros
            $role->add_cap('add_acompanhamentos');   // Adicionar acompanhamentos
        }

    }
}