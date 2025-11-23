<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MPD_Ferias {
    private $table_ferias;
    private $table_membros;

    public function __construct() {
        global $wpdb;
        $this->table_ferias = $wpdb->prefix . 'mpd_ferias';
        $this->table_membros = $wpdb->prefix . 'mpd_membros';
    }

    /** CREATE */
    public function insert($data) {
        global $wpdb;
        $wpdb->insert($this->table_ferias, [
            'membro_id' => intval($data['membro_id']),
            'ja_tirou_ferias' => intval($data['ja_tirou_ferias']),
            'programacao_inicio_1' => sanitize_text_field($data['programacao_inicio_1']),
            'programacao_fim_1' => sanitize_text_field($data['programacao_fim_1']),
            'programacao_inicio_2' => sanitize_text_field($data['programacao_inicio_2']),
            'programacao_fim_2' => sanitize_text_field($data['programacao_fim_2']),
            'tiradas_inicio_1' => sanitize_text_field($data['tiradas_inicio_1']),
            'tiradas_fim_1' => sanitize_text_field($data['tiradas_fim_1']),
            'tiradas_inicio_2' => sanitize_text_field($data['tiradas_inicio_2']),
            'tiradas_fim_2' => sanitize_text_field($data['tiradas_fim_2']),
            'ajuda_custo' => intval($data['ajuda_custo']),
            'valor_ajuda' => floatval($data['valor_ajuda']),
            'observacoes' => sanitize_textarea_field($data['observacoes']),
        ]);
        return $wpdb->insert_id;
    }

    /** READ single (com dados do membro) */
    public function get($id) {
        global $wpdb;
        $sql = "SELECT f.*, m.nome, m.grau_pertencimento, m.recanto
                FROM {$this->table_ferias} f
                INNER JOIN {$this->table_membros} m ON f.membro_id = m.id
                WHERE f.id = %d";
        return $wpdb->get_row($wpdb->prepare($sql, $id));
    }

    /** READ all (com JOIN) */
    public function get_all($limit = 50, $offset = 0) {
        global $wpdb;
        $sql = "SELECT f.*, m.nome, m.grau_pertencimento, m.recanto
                FROM {$this->table_ferias} f
                INNER JOIN {$this->table_membros} m ON f.membro_id = m.id
                ORDER BY f.created_at DESC
                LIMIT %d OFFSET %d";
        return $wpdb->get_results($wpdb->prepare($sql, $limit, $offset));
    }

    /** UPDATE */
    public function update($id, $data) {
        global $wpdb;
        return $wpdb->update($this->table_ferias, [
            'ja_tirou_ferias' => intval($data['ja_tirou_ferias']),
            'programacao_inicio_1' => sanitize_text_field($data['programacao_inicio_1']),
            'programacao_fim_1' => sanitize_text_field($data['programacao_fim_1']),
            'programacao_inicio_2' => sanitize_text_field($data['programacao_inicio_2']),
            'programacao_fim_2' => sanitize_text_field($data['programacao_fim_2']),
            'tiradas_inicio_1' => sanitize_text_field($data['tiradas_inicio_1']),
            'tiradas_fim_1' => sanitize_text_field($data['tiradas_fim_1']),
            'tiradas_inicio_2' => sanitize_text_field($data['tiradas_inicio_2']),
            'tiradas_fim_2' => sanitize_text_field($data['tiradas_fim_2']),
            'ajuda_custo' => intval($data['ajuda_custo']),
            'valor_ajuda' => floatval($data['valor_ajuda']),
            'observacoes' => sanitize_textarea_field($data['observacoes']),
        ], ['id' => intval($id)]);
    }

    /** DELETE */
    public function delete($id) {
        global $wpdb;
        return $wpdb->delete($this->table_ferias, ['id' => intval($id)]);
    }

    /** Buscar membros para dropdown */
    public function get_membros() {
        global $wpdb;
        $sql = "SELECT id, nome, grau_pertencimento, recanto FROM {$this->table_membros} ORDER BY nome ASC";
        return $wpdb->get_results($sql);
    }
    
    // Filtros avançados
    public function get_filtered($ano = '', $recanto = '') {
        global $wpdb;
        $where = [];
        $params = [];

        if ($ano) {
            $where[] = "YEAR(f.created_at) = %d";
            $params[] = $ano;
        }
        if ($recanto) {
            $where[] = "m.recanto = %s";
            $params[] = $recanto;
        }
        $sql = "SELECT f.*, m.nome, m.grau_pertencimento, m.recanto
                FROM {$this->table_ferias} f
                INNER JOIN {$this->table_membros} m ON f.membro_id = m.id";
        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY f.created_at DESC";
        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }


}
