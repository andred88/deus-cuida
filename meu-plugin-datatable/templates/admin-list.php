<h1>Lista de Membros</h1>

<!-- Filtros avançados -->
<form method="get" style="margin-bottom:15px;">
    <label for="filter-recanto">Filtrar por Recanto:</label>
    <select id="filter-recanto">
        <option value="">Todos</option>
        <?php
        global $wpdb;
        $recantos = $wpdb->get_col("SELECT DISTINCT recanto FROM {$wpdb->prefix}mpd_membros ORDER BY recanto ASC");
        foreach ($recantos as $recanto) {
            echo "<option value='" . esc_attr($recanto) . "'>" . esc_html($recanto) . "</option>";
        }
        ?>
    </select>

    <label for="filter-grau">Filtrar por Grau de Pertencimento:</label>
    <select id="filter-grau">
        <option value="">Todos</option>
        <?php
        $graus = $wpdb->get_col("SELECT DISTINCT grau_pertencimento FROM {$wpdb->prefix}mpd_membros ORDER BY grau_pertencimento ASC");
        foreach ($graus as $grau) {
            echo "<option value='" . esc_attr($grau) . "'>" . esc_html($grau) . "</option>";
        }
        ?>
    </select>
</form>

<?php
$table = $wpdb->prefix . 'mpd_membros';
$membros = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
?>

<table id="mpd-membros-table" class="widefat fixed striped">
    <thead>
        <tr>
            <th>Foto</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Grau de Pertencimento</th>
            <th>Recanto</th>
            <th>Idade</th>
            <th>Tempo na Etapa</th>
            <th>Tempo na Comunidade</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($membros as $m): ?>
            <?php
            $idade = $m->nascimento ? date_diff(date_create($m->nascimento), date_create('today'))->y : '';
            $edit_url = admin_url('admin.php?page=mpd-edit&id=' . intval($m->id));
            $detail_url = admin_url('admin.php?page=mpd-detail&id=' . intval($m->id));
            $delete_url = wp_nonce_url(admin_url('admin.php?page=mpd-list&action=delete&id=' . intval($m->id)), 'mpd_delete_' . intval($m->id));

            // Tempo na etapa
            $tempo_etapa = '';
            if (!empty($m->etapa)) {
                $intervalo = date_diff(date_create($m->etapa), date_create('today'));
                $anos = $intervalo->y;
                $meses = $intervalo->m;
                if ($anos > 0) $tempo_etapa .= $anos . ' ano' . ($anos > 1 ? 's' : '');
                if ($meses > 0) $tempo_etapa .= ($tempo_etapa ? ' ' : '') . $meses . ' mes' . ($meses > 1 ? 'es' : '');
                if ($tempo_etapa === '') $tempo_etapa = 'Menos de 1 mês';
            }

            // Tempo na comunidade
            $tempo_comunidade = '';
            if (!empty($m->acolhimento)) {
                $intervalo = date_diff(date_create($m->acolhimento), date_create('today'));
                $anos = $intervalo->y;
                $meses = $intervalo->m;
                if ($anos > 0) $tempo_comunidade .= $anos . ' ano' . ($anos > 1 ? 's' : '');
                if ($meses > 0) $tempo_comunidade .= ($tempo_comunidade ? ' ' : '') . $meses . ' mes' . ($meses > 1 ? 'es' : '');
                if ($tempo_comunidade === '') $tempo_comunidade = 'Menos de 1 mês';
            }
            ?>
            <tr>
                <td><?php echo $m->foto ? '<img src="' . esc_url($m->foto) . '" style="width:50px;height:50px;border-radius:50%;">' : ''; ?></td>
                <td><?php echo esc_html($m->nome); ?></td>
                <td><?php echo esc_html($m->email); ?></td>
                <td><?php echo esc_html($m->grau_pertencimento); ?></td>
                <td><?php echo esc_html($m->recanto); ?></td>
                <td><?php echo esc_html($idade); ?></td>
                <td><?php echo esc_html($tempo_etapa); ?></td>
                <td><?php echo esc_html($tempo_comunidade); ?></td>
                <td>
                <a href="<?php echo esc_url($detail_url); ?>" class="button">Detalhes</a>
                <a href="<?php echo esc_url($edit_url); ?>" class="button button-primary">Editar</a>
                <a href="<?php echo esc_url($delete_url); ?>" class="button button-secondary" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>