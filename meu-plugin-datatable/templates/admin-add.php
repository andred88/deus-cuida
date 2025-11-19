<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
$table = $wpdb->prefix . 'mpd_membros';
// verificação de permissao 

if ( ! current_user_can('add_members') ) {
    wp_die(__('Você não tem permissão para adicionar membros.', 'meu-plugin'));
}


// Processar formulário
if (isset($_POST['mpd_add']) && check_admin_referer('mpd_add_member', 'mpd_nonce')) {
    // Upload da foto
    $foto_url = '';
    if (!empty($_FILES['foto']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $upload = wp_handle_upload($_FILES['foto'], ['test_form' => false]);
        if (!isset($upload['error'])) {
            $foto_url = esc_url_raw($upload['url']);
        }
    }

    // Upload múltiplo de anexos
    $anexos_urls = [];
    if (!empty($_FILES['anexos']['name'][0])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        foreach ($_FILES['anexos']['name'] as $key => $value) {
            if ($_FILES['anexos']['name'][$key]) {
                $file = [
                    'name'     => $_FILES['anexos']['name'][$key],
                    'type'     => $_FILES['anexos']['type'][$key],
                    'tmp_name' => $_FILES['anexos']['tmp_name'][$key],
                    'error'    => $_FILES['anexos']['error'][$key],
                   ['anexos']['size'][$key]
                ];
                $upload = wp_handle_upload($file, ['test_form' => false]);
                if (!isset($upload['error'])) {
                    $anexos_urls[] = esc_url_raw($upload['url']);
                }
            }
        }
    }

    // Inserir no banco
    $wpdb->insert($table, [
		'foto'              => $foto_url,
		'nome'              => sanitize_text_field($_POST['nome']),
		'celular'           => sanitize_text_field($_POST['celular']),
		'email'             => sanitize_email($_POST['email']),
		'nascimento'        => sanitize_text_field($_POST['nascimento']),
		'estado_civil'      => sanitize_text_field($_POST['estado_civil']),
		'conjugue'          => sanitize_text_field($_POST['conjugue']),
		'filhos'            => sanitize_textarea_field($_POST['filhos']),
		'escolaridade'      => sanitize_text_field($_POST['escolaridade']),
		'curso'             => sanitize_text_field($_POST['curso']),
		'beneficio'         => sanitize_text_field($_POST['beneficio']),
		'grau_pertencimento'=> sanitize_text_field($_POST['grau_pertencimento']),
		'modo_compromisso'  => sanitize_text_field($_POST['modo_compromisso']),
		'acolhimento'       => sanitize_text_field($_POST['acolhimento']),
		'etapa'             => sanitize_text_field($_POST['etapa']),
		'ferias'            => sanitize_textarea_field($_POST['ferias']),
		'ajuda_custo'       => isset($_POST['ajuda_custo']) ? 1 : 0,
		'recanto'           => sanitize_text_field($_POST['recanto']),
		'funcao'            => sanitize_text_field($_POST['funcao']),
		'formador'          => sanitize_text_field($_POST['formador']),
		'observacoes'       => sanitize_textarea_field($_POST['observacoes']),
		'anexos'            => json_encode($anexos_urls)
	]);

    echo '<div class="notice notice-success"><p>' . __('Membro adicionado com sucesso!', 'meu-plugin') . '</p></div>';
}
?>

<div class="wrap">
    <h1><?php _e('Adicionar Membro', 'meu-plugin'); ?></h1>
    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('mpd_add_member', 'mpd_nonce'); ?>

        <table class="form-table">
            <!-- Todos os campos conforme versão anterior -->
            <tr><th>Foto</th><td><input type="file" name="foto" id="foto" accept="image/*"><div id="foto-preview" style="margin-top:10px;"></div></td></tr>
            <tr><th>Nome completo *</th><td><input type="text" name="nome" required></td></tr>
            <tr><th>Contato celular *</th><td><input type="text" name="celular" required></td></tr>
            <tr><th>Email</th><td><input type="email" name="email"></td></tr>
            <tr><th>Data de nascimento *</th><td><input type="date" name="nascimento" required></td></tr>
            <tr><th>Estado Civil *</th><td>
                <select name="estado_civil" required>
                    <option value="Casado">Casado</option>
                    <option value="Solteiro">Solteiro</option>
                    <option value="Viúvo">Viúvo</option>
                    <option value="Divorciado">Divorciado</option>
                </select>
            </td></tr>
            <tr><th>Conjugue</th><td><input type="text" name="conjugue"></td></tr>
            <tr><th>Filhos</th><td><textarea name="filhos"></textarea></td></tr>
            <tr><th>Nível de escolaridade *</th><td>
                <select name="escolaridade" required>
                    <option value="Ensino fundamental incompleto">Ensino fundamental incompleto</option>
                    <option value="Ensino fundamental completo">Ensino fundamental completo</option>
                    <option value="Ensino médio incompleto">Ensino médio incompleto</option>
                    <option value="Ensino médio completo">Ensino médio completo</option>
                    <option value="Ensino superior incompleto">Ensino superior incompleto</option>
                    <option value="Ensino superior completo">Ensino superior completo</option>
                </select>
            </td></tr>
            <tr><th>Curso (se superior)</th><td><input type="text" name="curso"></td></tr>
            <tr><th>Benefício *</th><td>
                <select name="beneficio" required>
                    <option value="Não possui">Não possui</option>
                    <option value="Aposentadoria">Aposentadoria</option>
                    <option value="Bolsa família">Bolsa família</option>
                    <option value="BPC">BPC</option>
                </select>
            </td></tr>
            <tr><th>Grau de pertencimento *</th><td>
                <select name="grau_pertencimento" required>
                    <option value="Filho em reinserção">Filho em reinserção</option>
                    <option value="Auxiliar de missão">Auxiliar de missão</option>
                    <option value="Vocacionado">Vocacionado</option>
                    <option value="Vocacionado em experiência">Vocacionado em experiência</option>
                    <option value="Aspirante">Aspirante</option>
                    <option value="Discípulo">Discípulo</option>
                    <option value="Discípulo em Missão">Discípulo em Missão</option>
                    <option value="Consagrado">Consagrado</option>
                </select>
            </td></tr>
            <tr><th>Modo de Compromisso *</th><td>
                <select name="modo_compromisso" required>
                    <option value="Vida">Vida</option>
                    <option value="Aliança">Aliança</option>
                </select>
            </td></tr>
            <tr><th>Acolhimento *</th><td><input type="date" name="acolhimento" required></td></tr>
            <tr><th>Início da etapa *</th><td><input type="date" name="etapa" required></td></tr>
            <tr><th>Férias</th><td><textarea name="ferias"></textarea></td></tr>
            <tr><th>Recebeu ajuda de custo?</th><td><input type="checkbox" name="ajuda_custo" value="1"></td></tr>
            <tr><th>Recanto de missão *</th><td>
                <select name="recanto" required>
                    <option value="São João Batista - SC">São João Batista - SC</option>
                    <option value="Curitiba - PR">Curitiba - PR</option>
                    <option value="Irati - PR">Irati - PR</option>
                    <option value="Guarapuava - PR">Guarapuava - PR</option>
                    <option value="Cianorte - PR">Cianorte - PR</option>
                    <option value="Lorena - SP">Lorena - SP</option>
                    <option value="Italva - RJ">Italva - RJ</option>
                    <option value="Uberlândia - MG">Uberlândia - MG</option>
                </select>
            </td></tr>
            <tr><th>Função específica</th><td><input type="text" name="funcao"></td></tr>
            <tr><th>Formador responsável *</th><td><input type="text" name="formador" required></td></tr>
            <tr><th>Observações</th><td><textarea name="observacoes"></textarea></td></tr>
            <tr><th>Anexos (múltiplos)</th><td><input type="file" name="anexos[]" multiple></td></tr>
        </table>

        <p class="submit">
            <input type="submit" name="mpd_add" class="button-primary" value="<?php _e('Salvar', 'meu-plugin'); ?>">
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($){
    $('#foto').on('change', function(){
        var reader = new FileReader();
        reader.onload = function(e){
            $('#foto-preview').html('<img src="'+e.target.result+'" style="max-width:150px;">');
        }
        reader.readAsDataURL(this.files[0]);
    });
});
</script>