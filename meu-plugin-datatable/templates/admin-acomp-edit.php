<?php
if ( ! current_user_can('edit_acomp') ) {
    wp_die(__('Você não tem permissão para editar acompanhamentos.', 'meu-plugin'));
}
?>

<h2>Editar Acompanhamento</h2>

<?php
$id = intval($_GET['id']);
global $wpdb;
$table_name = $wpdb->prefix . 'mpd_acompanhamentos';

$acomp = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

if (!$acomp) {
    echo '<div class="error"><p>Acompanhamento não encontrado.</p></div>';
    return;
}

if (isset($_POST['update_acomp'])) {
    check_admin_referer('mpd_edit_acomp_action_' . $id, 'mpd_edit_acomp_nonce');

    $member_id = intval($_POST['member_id']);
    $formador = sanitize_text_field($_POST['formador']);
    $data_hora = sanitize_text_field($_POST['data_hora']);
    $descricao = sanitize_textarea_field($_POST['descricao']);

    $wpdb->update($table_name, array(
        'member_id' => $member_id,
        'formador' => $formador,
        'data_hora' => $data_hora,
        'descricao' => $descricao
    ), array('id' => $id), array('%d', '%s', '%s', '%s'), array('%d'));

    echo '<div class="updated"><p>Acompanhamento atualizado com sucesso!</p></div>';
}
?>

<form method="post">
    <?php wp_nonce_field('mpd_edit_acomp_action_' . $id, 'mpd_edit_acomp_nonce'); ?>

    <p>
        <label>Membro:</label>
        <select name="member_id" required>
            <?php
            $members_table = $wpdb->prefix . 'mpd_membros';
            $members = $wpdb->get_results("SELECT id, nome FROM $members_table ORDER BY nome ASC");
            foreach ($members as $member) {
                $selected = ($member->id == $acomp->member_id) ? 'selected' : '';
                echo "<option value='" . esc_attr($member->id) . "' $selected>" . esc_html($member->nome) . "</option>";
            }
            ?>
        </select>
    </p>
    <p>
        <label>Formador:</label>
        <input type="text" name="formador" value="<?php echo esc_attr($acomp->formador); ?>" required>
    </p>
    <p>
        <label>Data e Hora:</label>
        <input type="datetime-local" name="data_hora" value="<?php echo esc_attr($acomp->data_hora); ?>" required>
    </p>
    <p>
        <label>Descrição:</label>
        <textarea name="descricao" rows="5" required><?php echo esc_textarea($acomp->descricao); ?></textarea>
    </p>
    <p>
        <input type="submit" name="update_acomp" value="Atualizar" class="button button-primary">
    </p>
</form>