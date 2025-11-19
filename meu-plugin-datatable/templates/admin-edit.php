<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
$table = $wpdb->prefix . 'mpd_membros';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar membro
$membro = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $id));
if (!$membro) {
    echo '<div class="notice notice-error"><p>' . __('Membro não encontrado.', 'meu-plugin') . '</p></div>';
    return;
}

// verificar permissoes

if ( ! current_user_can('edit_members') ) {
    wp_die(__('Você não tem permissão para editar membros.', 'meu-plugin'));
}

// Processar atualização
if (isset($_POST['mpd_update']) && check_admin_referer('mpd_edit_member', 'mpd_nonce')) {
    // Upload da foto
    $foto_url = $membro->foto;
    if (!empty($_FILES['foto']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $upload = wp_handle_upload($_FILES['foto'], ['test_form' => false]);
        if (!isset($upload['error'])) {
            $foto_url = esc_url_raw($upload['url']);
        }
    }

    // Upload múltiplo de anexos
    $anexos_urls = $membro->anexos ? json_decode($membro->anexos, true) : [];
    if (!empty($_FILES['anexos']['name'][0])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        foreach ($_FILES['anexos']['name'] as $key => $value) {
            if ($_FILES['anexos']['name'][$key]) {
                $file = [
                    'name'     => $_FILES['anexos']['name'][$key],
                    'type'     => $_FILES['anexos']['type'][$key],
                    'tmp_name' => $_FILES['anexos']['tmp_name'][$key],
                    'error'    => $_FILES['anexos']['error'][$key],
                    'size'     => $_FILES['anexos']['size'][$key]
                ];

                $upload = wp_handle_upload($file, ['test_form' => false]);
                if (!isset($upload['error'])) {
                    $anexos_urls[] = esc_url_raw($upload['url']);
                }
            }
        }
    }

    // Atualizar no banco
    $wpdb->update($table, [
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
    ], ['id' => $id]);

    echo '<div class="notice notice-success"><p>' . __('Membro atualizado com sucesso!', 'meu-plugin') . '</p></div>';
    $membro = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id=%d", $id));
}
?>

<div class="wrap">
    <h1><?php _e('Editar Membro', 'meu-plugin'); ?></h1>
    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('mpd_edit_member', 'mpd_nonce'); ?>

        <table class="form-table">
            <tr><th>Foto</th><td>
                <?php if (!empty($membro->foto)): ?>
                    <img src="<?php echo esc_url($membro->foto); ?>" style="max-width:150px;"><br>
                <?php endif; ?>
                <input type="file" name="foto" id="foto" accept="image/*">
                <div id="foto-preview" style="margin-top:10px;"></div>
            </td></tr>
            <tr><th>Nome completo *</th><td><input type="text" name="nome" value="<?php echo esc_attr($membro->nome); ?>" required></td></tr>
            <tr><th>Contato celular *</th><td><input type="text" name="celular" value="<?php echo esc_attr($membro->celular); ?>" required></td></tr>
            <tr><th>Email</th><td><input type="email" name="email" value="<?php echo esc_attr($membro->email); ?>"></td></tr>
            <tr><th>Data de nascimento *</th><td><input type="date" name="nascimento" value="<?php echo esc_attr($membro->nascimento); ?>" required></td></tr>
            <tr><th>Estado Civil *</th><td>
                <select name="estado_civil" required>
                    <?php $options = ['Casado','Solteiro','Viúvo','Divorciado']; foreach($options as $opt): ?>
                        <option value="<?php echo $opt; ?>" <?php selected($membro->estado_civil, $opt); ?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                </select>
            </td></tr>
            <tr><th>Conjugue</th><td><input type="text" name="conjugue" value="<?php echo esc_attr($membro->conjugue); ?>"></td></tr>
            <tr><th>Filhos</th><td><textarea name="filhos"><?php echo esc_textarea($membro->filhos); ?></textarea></td></tr>
            <tr><th>Nível de escolaridade *</th><td>
                <select name="escolaridade" required>
                    <?php $options = ['Ensino fundamental incompleto','Ensino fundamental completo','Ensino médio incompleto','Ensino médio completo','Ensino superior incompleto','Ensino superior completo'];
                    foreach($options as $opt): ?>
                        <option value="<?php echo $opt; ?>" <?php selected($membro->escolaridade, $opt); ?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                </select>
            </td></tr>
            <tr><th>Curso (se superior)</th><td><input type="text" name="curso" value="<?php echo esc_attr($membro->curso); ?>"></td></tr>
            <tr><th>Benefício *</th><td>
                <select name="beneficio" required>
                    <?php $options = ['Não possui','Aposentadoria','Bolsa família','BPC'];
                    foreach($options as $opt): ?>
                        <option value="<?php echo $opt; ?>" <?php selected($membro->beneficio, $opt); ?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                </select>
            </td></tr>
            <tr><th>Grau de pertencimento *</th><td>
                <select name="grau_pertencimento" required>
                    <?php $options = ['Filho em reinserção','Auxiliar de missão','Vocacionado','Vocacionado em experiência','Aspirante','Discípulo','Discípulo em Missão','Consagrado'];
                    foreach($options as $opt): ?>
                        <option value="<?php echo $opt; ?>" <?php selected($membro->grau_pertencimento, $opt); ?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                </select>
            </td></tr>
            <tr><th>Modo de Compromisso *</th><td>
                <select name="modo_compromisso" required>
                    <?php $options = ['Vida','Aliança'];
                    foreach($options as $opt): ?>
                        <option value="<?php echo $opt; ?>" <?php selected($membro->modo_compromisso, $opt); ?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                </select>
            </td></tr>
            <tr><th>Acolhimento *</th><td><input type="date" name="acolhimento" value="<?php echo esc_attr($membro->acolhimento); ?>" required></td></tr>
            <tr><th>Início da etapa *</th><td><input type="date" name="etapa" value="<?php echo esc_attr($membro->etapa); ?>" required></td></tr>
            <tr><th>Férias</th><td><textarea name="ferias"><?php echo esc_textarea($membro->ferias); ?></textarea></td></tr>
            <tr><th>Recebeu ajuda de custo?</th><td><input type="checkbox" name="ajuda_custo" value="1" <?php checked($membro->ajuda_custo, 1); ?>></td></tr>
            <tr><th>Recanto de missão *</th><td>
                <select name="recanto" required>
                    <?php $options = ['São João Batista - SC','Curitiba - PR','Irati - PR','Guarapuava - PR','Cianorte - PR','Lorena - SP','Italva - RJ','Uberlândia - MG'];
                    foreach($options as $opt): ?>
                        <option value="<?php echo $opt; ?>" <?php selected($membro->recanto, $opt); ?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                </select>
            </td></tr>
            <tr><th>Função específica</th><td><input type="text" name="funcao" value="<?php echo esc_attr($membro->funcao); ?>"></td></tr>
            <tr><th>Formador responsável *</th><td><input type="text" name="formador" value="<?php echo esc_attr($membro->formador); ?>" required></td></tr>
            <tr><th>Observações</th><td><textarea name="observacoes"><?php echo esc_textarea($membro->observacoes); ?></textarea></td></tr>
            <tr><th>Anexos (múltiplos)</th><td>
                <?php if (!empty($anexos_urls)): ?>
                    <ul>
                        <?php foreach ($anexos_urls as $file): ?>
                            <li><a href="<?php echo esc_url($file); ?>" target="_blank"><?php echo basename($file); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <input type="file" name="anexos[]" multiple>
            </td></tr>
        </table>

        <p class="submit">
            <input type="submit" name="mpd_update" class="button-primary" value="<?php _e('Atualizar', 'meu-plugin'); ?>">
        </$){
    $('#foto').on('change', function(){
        var reader = new FileReader();
        reader.onload = function(e){
            $('#foto-preview').html('<img src="'+e.target.result+'" style="max-width:150px;">');
        }
        reader.readAsDataURL(this.files[0]);
    });
});
</script>