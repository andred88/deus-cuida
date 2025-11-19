// verificar permissoes`
<?php
if ( ! current_user_can('add_acomp') ) {
    wp_die(__('Você não tem permissão para adicionar Acompanhamento.', 'meu-plugin'));
}
?>

<h2>Novo Acompanhamento</h2>

<?php
if (isset($_POST['submit_acomp'])) {
    check_admin_referer('mpd_add_acomp_action', 'mpd_add_acomp_nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'mpd_acompanhamentos';

    $member_id = intval($_POST['member_id']);
    $formador = sanitize_text_field($_POST['formador']);
    $data_hora = sanitize_text_field($_POST['data_hora']);
    $descricao = sanitize_textarea_field($_POST['descricao']);

    $wpdb->insert($table_name, array(
        'member_id' => $member_id,
        'formador' => $formador,
        'data_hora' => $data_hora,
        'descricao' => $descricao
    ));

    echo '<div class="updated"><p>Acompanhamento salvo com sucesso!</p></div>';
}
?>

<form method="post">
    <?php wp_nonce_field('mpd_add_acomp_action', 'mpd_add_acomp_nonce'); ?>

    <p>
        <label>Membro:</label>
        <select name="member_id" required>
            <option value="">Selecione um membro</option>
            <?php
            global $wpdb;
            $members_table = $wpdb->prefix . 'mpd_membros';
            $members = $wpdb->get_results("SELECT id, nome FROM $members_table ORDER BY nome ASC");
            foreach ($members as $member) {
                $selected = (isset($_GET['member_id']) && $_GET['member_id'] == $member->id) ? 'selected' : '';
                echo "<option value='" . esc_attr($member->id) . "' $selected>" . esc_html($member->nome) . "</option>";
            }
            ?>
        </select>
    </p>
    <p>
        <label>Formador:</label>
        <input type="text" name="formador" required>
    </p>
    <p>
        <label>Data e Hora:</label>
        <input type="datetime-local" name="data_hora" required>
    </p>
    <p>
        <label>Descrição:</label>
        <textarea name="descricao" rows="5" required></textarea>
    </p>
    <p>
        <input type="submit" name="submit_acomp" value="Salvar" class="button button-primary">
    </p>
</form>