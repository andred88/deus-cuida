<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$ferias = new MPD_Ferias();
$registros = $ferias->get_all(100, 0); // Ajuste limite conforme necessário

// Processar exportação CSV
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $registros = $ferias->get_filtered($ano, $recanto); // Usa filtros aplicados

    if ($registros) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=ferias_export_' . date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');

        // Cabeçalho do CSV
        fputcsv($output, [
            'ID', 'Nome', 'Nível de Pertença', 'Recanto', 'Já Tirou Férias',
            'Ajuda de Custo', 'Valor Ajuda', 'Programação 1', 'Programação 2',
            'Tiradas 1', 'Tiradas 2', 'Observações'
        ]);

        // Linhas
        foreach ($registros as $r) {
            fputcsv($output, [
                $r->id,
                $r->nome,
                $r->grau_pertencimento,
                $r->recanto,
                $r->ja_tirou_ferias ? 'Sim' : 'Não',
                $r->ajuda_custo ? 'Sim' : 'Não',
                number_format($r->valor_ajuda, 2, ',', '.'),
                $r->programacao_inicio_1 . ' a ' . $r->programacao_fim_1,
                $r->programacao_inicio_2 . ' a ' . $r->programacao_fim_2,
                $r->tiradas_inicio_1 . ' a ' . $r->tiradas_fim_1,
                $r->tiradas_inicio_2 . ' a ' . $r->tiradas_fim_2,
                $r->observacoes
            ]);
        }

        fclose($output);
        exit;
    } else {
        wp_die('Nenhum registro encontrado para exportação.');
    }
}


// Processar exclusão
if ( isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) ) {
    $id = intval($_GET['id']);
    check_admin_referer('mpd_delete_ferias_' . $id);

    $deleted = $ferias->delete($id);
    if ($deleted) {
        echo '<div class="notice notice-success"><p>Registro excluído com sucesso!</p></div>';
        $registros = $ferias->get_all(100, 0); // Atualiza lista
    } else {
        echo '<div class="notice notice-error"><p>Erro ao excluir registro.</p></div>';
    }
}
?>
<?php
// Captura filtros
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : '';
$recanto = isset($_GET['recanto']) ? sanitize_text_field($_GET['recanto']) : '';

// Buscar recantos únicos da tabela de membros
global $wpdb;
$recantos = $wpdb->get_col("SELECT DISTINCT recanto FROM {$wpdb->prefix}mpd_membros ORDER BY recanto ASC");
?>

<form method="get" style="margin-bottom: 15px;">
    <input type="hidden" name="page" value="mpd-ferias">
    <label for="ano">Ano Formativo:</label>
    <input type="number" name="ano" id="ano" value="<?php echo esc_attr($ano); ?>" placeholder="Ex: 2025" style="width:100px;">

    <label for="recanto">Recanto:</label>
    <select name="recanto" id="recanto">
        <option value="">Todos</option>
        <?php foreach ($recantos as $r): ?>
            <option value="<?php echo esc_attr($r); ?>" <?php selected($recanto, $r); ?>><?php echo esc_html($r); ?></option>
        <?php endforeach; ?>
    </select>

    <?php submit_button('Filtrar', 'secondary', '', false); ?>
</form>
<a href="<?php echo esc_url(add_query_arg(['page' => 'mpd-ferias', 'action' => 'export', 'ano' => $ano, 'recanto' => $recanto])); ?>" class="button button-primary">
    Exportar CSV
</a>

<div class="wrap">
    <h1>Lista de Férias 
        <?php if (current_user_can('manage_ferias')): ?>    
            <a href="<?php echo admin_url('admin.php?page=mpd-ferias-add'); ?>" class="page-title-action">Adicionar Novo</a>
        <?php endif; ?>
    </h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Membro</th>
                <th>Nível de Pertença</th>
                <th>Recanto</th>
                <th>Já Tirou Férias?</th>
                <th>Datas das Férias Tiradas</th>
                <th>Datas da Programação</th>
                <th>Total de Dias Tirados</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>            
            <?php if ($registros): ?>
                <?php foreach ($registros as $r): ?>
                    <?php
                    // Combinar datas tiradas
                   
                    // Datas das férias tiradas
                    $tiradas = [];
                    if ($r->tiradas_inicio_1 && $r->tiradas_fim_1) {
                        $tiradas[] = date_i18n(get_option('date_format'), strtotime($r->tiradas_inicio_1)) . ' a ' . date_i18n(get_option('date_format'), strtotime($r->tiradas_fim_1));
                    }
                    if ($r->tiradas_inicio_2 && $r->tiradas_fim_2) {
                        $tiradas[] = date_i18n(get_option('date_format'), strtotime($r->tiradas_inicio_2)) . ' a ' . date_i18n(get_option('date_format'), strtotime($r->tiradas_fim_2));
                    }

                    // Datas da programação
                    $programadas = [];
                    if ($r->programacao_inicio_1 && $r->programacao_fim_1) {
                        $programadas[] = date_i18n(get_option('date_format'), strtotime($r->programacao_inicio_1)) . ' a ' . date_i18n(get_option('date_format'), strtotime($r->programacao_fim_1));
                    }
                    if ($r->programacao_inicio_2 && $r->programacao_fim_2) {
                        $programadas[] = date_i18n(get_option('date_format'), strtotime($r->programacao_inicio_2)) . ' a ' . date_i18n(get_option('date_format'), strtotime($r->programacao_fim_2));
                    }


                    // Calcular total de dias tirados
                    $total_dias = 0;
                    if ($r->tiradas_inicio_1 && $r->tiradas_fim_1) {
                        $d1 = new DateTime($r->tiradas_inicio_1);
                        $d2 = new DateTime($r->tiradas_fim_1);
                        $total_dias += $d1->diff($d2)->days + 1;
                    }
                    if ($r->tiradas_inicio_2 && $r->tiradas_fim_2) {
                        $d3 = new DateTime($r->tiradas_inicio_2);
                        $d4 = new DateTime($r->tiradas_fim_2);
                        $total_dias += $d3->diff($d4)->days + 1;
                    }
                    ?>
                    <tr>
                        <td><?php echo esc_html($r->nome); ?></td>
                        <td><?php echo esc_html($r->grau_pertencimento); ?></td>
                        <td><?php echo esc_html($r->recanto); ?></td>
                        <td><?php echo $r->ja_tirou_ferias ? 'Sim' : 'Não'; ?></td>
                        <td><?php echo implode('<br>', $tiradas); ?></td>
                        <td><?php echo implode('<br>', $programadas); ?></td>
                        <td><?php echo $total_dias . ' dias'; ?></td>
                        <td>
                            <?php if (current_user_can('manage_ferias')): ?>
                                <?php
                                    $edit_url = admin_url('admin.php?page=mpd-ferias-edit&id=' . $r->id);
                                    $delete_url = wp_nonce_url(admin_url('admin.php?page=mpd-ferias&action=delete&id=' . $r->id), 'mpd_delete_ferias_' . $r->id);
                                ?>
                                <a href="<?php echo esc_url($edit_url); ?>">Editar</a> |
                                <a href="<?php echo esc_url($delete_url); ?>" onclick="return confirm('Tem certeza que deseja excluir este registro?');">Excluir</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">Nenhum registro encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
