<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Instanciar a classe
$ferias = new MPD_Ferias();
$membros = $ferias->get_membros();

// Processar formulário
if ( isset($_POST['mpd_ferias_nonce']) && wp_verify_nonce($_POST['mpd_ferias_nonce'], 'mpd_add_ferias') ) {
    $data = [
        'membro_id' => $_POST['membro_id'],
        'ja_tirou_ferias' => isset($_POST['ja_tirou_ferias']) ? 1 : 0,
        'programacao_inicio_1' => $_POST['programacao_inicio_1'],
        'programacao_fim_1' => $_POST['programacao_fim_1'],
        'programacao_inicio_2' => $_POST['programacao_inicio_2'],
        'programacao_fim_2' => $_POST['programacao_fim_2'],
        'tiradas_inicio_1' => $_POST['tiradas_inicio_1'],
        'tiradas_fim_1' => $_POST['tiradas_fim_1'],
        'tiradas_inicio_2' => $_POST['tiradas_inicio_2'],
        'tiradas_fim_2' => $_POST['tiradas_fim_2'],
        'ajuda_custo' => isset($_POST['ajuda_custo']) ? 1 : 0,
        'valor_ajuda' => $_POST['valor_ajuda'],
        'observacoes' => $_POST['observacoes'],
    ];

    $insert_id = $ferias->insert($data);

    if ($insert_id) {
        echo '<div class="notice notice-success"><p>Férias cadastradas com sucesso!</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>Erro ao cadastrar férias.</p></div>';
    }
}
?>

<div class="wrap">
    <h1>Cadastrar Férias</h1>
    <form method="post">
        <?php wp_nonce_field('mpd_add_ferias', 'mpd_ferias_nonce'); ?>

        <table class="form-table">
            <tr>
                <th><label for="membro_id">Membro</label></th>
                <td>
                    <select name="membro_id" id="membro_id" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($membros as $m): ?>
                            <option value="<?php echo esc_attr($m->id); ?>">
                                <?php echo esc_html($m->nome . ' (' . $m->grau_pertencimento . ' - ' . $m->recanto . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="ja_tirou_ferias">Já tirou férias neste ano?</label></th>
                <td><input type="checkbox" name="ja_tirou_ferias" id="ja_tirou_ferias"></td>
            </tr>

            <tr>
                <th>Datas da programação das férias</th>
                <td>
                    <input type="date" name="programacao_inicio_1"> até <input type="date" name="programacao_fim_1"><br>
                    <input type="date" name="programacao_inicio_2"> até <input type="date" name="programacao_fim_2">
                </td>
            </tr>

            <tr>
                <th>Datas já tiradas</th>
                <td>
                    <input type="date" name="tiradas_inicio_1"> até <input type="date" name="tiradas_fim_1"><br>
                    <input type="date" name="tiradas_inicio_2"> até <input type="date" name="tiradas_fim_2">
                </td>
            </tr>

            <tr>
                <th><label for="ajuda_custo">Já recebeu ajuda de custo?</label></th>
                <td><input type="checkbox" name="ajuda_custo" id="ajuda_custo"></td>
            </tr>

            <tr>
                <th><label for="valor_ajuda">Valor da ajuda</label></th>
                <td><input type="number" step="0.01" name="valor_ajuda" id="valor_ajuda"></td>
            </tr>

            <tr>
                <th><label for="observacoes">Observações formativas</label></th>
                <td><textarea name="observacoes" id="observacoes" rows="4" cols="50"></textarea></td>
            </tr>
        </table>

        <?php submit_button('Salvar Férias'); ?>
    </form>
</div>