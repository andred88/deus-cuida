<h1>Acompanhamentos</h1>
<p>
    <a href="<?php echo esc_url(admin_url('admin.php?page=mpd-acompanhamentos&action=add')); ?>" class="button button-primary">
        Adicionar Acompanhamento
    </a>
</p>

<!-- Filtro por membro -->
<form method="get" style="margin-bottom:15px;">
    <input type="hidden" name="page" value="mpd-acompanhamentos">
    <label for="filter_member">Filtrar por Membro:</label>
    <select name="filter_member" id="filter_member" onchange="this.form.submit()">
        <option value="">Todos</option>
        <?php
        global $wpdb;
        $members_table = $wpdb->prefix . 'mpd_membros';
        $members = $wpdb->get_results("SELECT id, nome FROM $members_table ORDER BY nome ASC");
        foreach ($members as $member) {
            $selected = (isset($_GET['filter_member']) && $_GET['filter_member'] == $member->id) ? 'selected' : '';
            echo "<option value='" . esc_attr($member->id) . "' $selected>" . esc_html($member->nome) . "</option>";
        }
        ?>
    </select>
</form>

<?php
$acomp_table = $wpdb->prefix . 'mpd_acompanhamentos';
$where = '';
if (!empty($_GET['filter_member'])) {
    $where = $wpdb->prepare("WHERE a.member_id = %d", intval($_GET['filter_member']));
}

$results = $wpdb->get_results("
    SELECT a.id, a.member_id, a.formador, a.data_hora, a.descricao, m.nome AS membro_nome
    FROM $acomp_table AS a
    LEFT JOIN $members_table AS m ON a.member_id = m.id
    $where
    ORDER BY a.data_hora DESC
");

if ($results) {
    echo '<table id="mpd-acomp-table" class="widefat fixed striped">';
    echo '<thead><tr><th>ID</th><th>Membro</th><th>Formador</th><th>Data/Hora</th><th>Descrição</th><th>Ações</th></tr></thead><tbody>';
    foreach ($results as $row) {
        $edit_url = wp_nonce_url(admin_url('admin.php?page=mpd-acompanhamentos&action=edit&id=' . $row->id), 'mpd_edit_acomp_' . $row->id);
        $delete_url = wp_nonce_url(admin_url('admin.php?page=mpd-acompanhamentos&action=delete&id=' . $row->id), 'mpd_delete_acomp_' . $row->id);

        echo "<tr>
            <td>" . esc_html($row->id) . "</td>
            <td>" . esc_html($row->membro_nome ?: 'Não encontrado') . "</td>
            <td>" . esc_html($row->formador) . "</td>
            <td>" . esc_html($row->data_hora) . "</td>
            <td>" . esc_html($row->descricao) . "</td>
            <td>
                <a href='" . esc_url($edit_url) . "' class='button button-primary'>Editar</a>
                <a href='" . esc_url($delete_url) . "' class='button button-secondary' onclick='return confirm(\"Tem certeza que deseja excluir?\");'>Excluir</a>
            </td>
        </tr>";
    }
    echo '</tbody></table>';
} else {
    echo '<p>Nenhum acompanhamento registrado.</p>';
}
?>