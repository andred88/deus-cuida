<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$ferias = new MPD_Ferias();
$membros = $ferias->get_membros();

// Pegar ID da URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$registro = $ferias->get($id);

if (!$registro) {
    echo '<div class="notice notice-error"><p>Registro não encontrado.</p></div>';
    return;
}

// Processar atualização
if ( isset($_POST['mpd_ferias_nonce']) && wp_verify_nonce($_POST['mpd_ferias_nonce'], 'mpd_edit_ferias') ) {
    $data = [
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

    $updated = $ferias->update($id, $data);

    if ($updated !== false) {
        echo '<div class="notice notice-success"><p>Férias atualizadas com sucesso!</p></div>';
        // Atualiza os dados exibidos
        $registro = $ferias->get($id);
    } else {
        echo '<div class="notice notice-error"><p>Erro ao atualizar férias.</p></div>';
    }
}
?>

<div class="wrap">
    <h1>Editar Férias</h1>
    <form method="post">
        <?php wp_nonce_field('mpd_edit_ferias', 'mpd_ferias_nonce'); ?>

        <table class="form-table">
            <tr>
                <th>Membro</th>
                <td>
                    <strong><?php echo esc_html($registro->nome); ?></strong><br>
                    <em><?php echo esc_html($registro->grau_pertencimento . ' - ' . $registro->recanto); ?></em>
                </td>
            </tr>

            <tr>
                <th><label for="ja_tirou_ferias">Já tirou férias neste ano?</label></th>
                <td><input type="checkbox" name="ja_tirou_ferias" id="ja_tirou_ferias" <?php checked($registro->ja_tirou_ferias, 1); ?>></td>
            </tr>

            <tr>
                <th>Datas da programação das férias</th>
                <td>
                    <input type="date" name="programacao_inicio_1" value="<?php echo esc_attr($registro->programacao_inicio_1); ?>"> até 
                    <input type="date" name="programacao_fim_1" value="<?php echo esc_attr($registro->programacao_fim_1); ?>"><br>
                    <input type="date" name="programacao_inicio_2" value="<?php echo esc_attr($registro->programacao_inicio_2); ?>"> até 
                    <input type="date" name="programacao_fim_2" value="<?php echo esc_attr($registro->programacao_fim_2); ?>">
                </td>
            </tr>

            <tr>
                <th>Datas já tiradas</th>
                <td>
                    <input type="date" name="tiradas_inicio_1" value="<?php echo esc_attr($registro->tiradas_inicio_1); ?>"> até 
                    <input type="date" name="tiradas_fim_1" value="<?php echo esc_attr($registro->tiradas_fim_1); ?>"><br>
                    <input type="date" name="tiradas_inicio_2" value="<?php echo esc_attr($registro->tiradas_inicio_2); ?>"> até 
                    <input type="date" name="tiradas_fim_2" value="<?php echo esc_attr($registro->tiradas_fim_2); ?>">
                </td>
            </tr>

            <tr>
                <th><label for="ajuda_custo">Já recebeu ajuda de custo?</label></th>
                <td><input type="checkbox" name="ajuda_custo" id="ajuda_custo" <?php checked($registro->ajuda_custo, 1); ?>></td>
            </tr>

            <tr>
                <th><label for="valor_ajuda">Valor da ajuda</label></th>
                <td><input type="number" step="0.01" name="valor_ajuda" id="valor_ajuda" value="<?php echo esc_attr($registro->valor_ajuda); ?>"></td>
            </tr>

            <tr>
                <th><label for="observacoes">Observações formativas</label></th>
                <td><textarea name="observacoes" id="observacoes" rows="4" cols="50"><?php echo esc_textarea($registro->observacoes); ?></textarea></td>
            </tr>
        </table>

        <?php submit_button('Atualizar Férias'); ?>
    </form>
</div>