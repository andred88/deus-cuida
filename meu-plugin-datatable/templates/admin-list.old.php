<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
$table = $wpdb->prefix . 'mpd_membros';

// Paginação
$limit = 20;
$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($page - 1) * $limit;

// Buscar membros
$membros = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT %d OFFSET %d", $limit, $offset));

// Total para paginação
$total = $wpdb->get_var("SELECT COUNT(*) FROM $table");
$total_pages = ceil($total / $limit);

// Mensagem de exclusão
if (isset($_GET['deleted'])) {
    echo '<div class="notice notice-success"><p>Membro excluído com sucesso!</p></div>';
}
?>

<div class="wrap">
    <h1><?php _e('Lista de Membros', 'meu-plugin'); ?></h1>
    <table id="mpd-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Foto', 'meu-plugin'); ?></th>
                <th><?php _e('Nome', 'meu-plugin'); ?></th>
                <th><?php _e('Idade', 'meu-plugin'); ?></th>
                <th><?php _e('Pertença', 'meu-plugin'); ?></th>
				<th><?php _e('Tempo na etapa', 'meu-plugin'); ?></th>
                <th><?php _e('Recanto', 'meu-plugin'); ?></th>
                <th><?php _e('Ações', 'meu-plugin'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($membros as $m): ?>
                <?php
                $idade = $m->nascimento ? date_diff(date_create($m->nascimento), date_create('today'))->y : '';
                $edit_url = admin_url('admin.php?page=mpd-edit&id=' . intval($m->id));
                $detail_url = admin_url('admin.php?page=mpd-detail&id=' . intval($m->id));
				$delete_url = wp_nonce_url(admin_url('admin.php?page=mpd-list&action=delete&id=' . intval($m->id)), 'mpd_delete_' . intval($m->id));
				$tempo_etapa = '';
				if (!empty($m->etapa)) {
					$intervalo = date_diff(date_create($m->etapa), date_create('today'));
					$anos = $intervalo->y;
					$meses = $intervalo->m;

					// Monta o texto
					if ($anos > 0) {
						$tempo_etapa .= $anos . ' ano' . ($anos > 1 ? 's' : '');
					}
					if ($meses > 0) {
						$tempo_etapa .= ($tempo_etapa ? ' ' : '') . $meses . ' mes' . ($meses > 1 ? 'es' : '');
					}

					if ($tempo_etapa === '') {
						$tempo_etapa = 'Menos de 1 mês';
					}
				}
				$tempo_comunidade = '';
				if (!empty($m->acolhimento)) {
					$intervalo = date_diff(date_create($m->acolhimento), date_create('today'));
					$anos = $intervalo->y;
					$meses = $intervalo->m;

					// Monta o texto
					if ($anos > 0) {
						$tempo_comunidade .= $anos . ' ano' . ($anos > 1 ? 's' : '');
					}
					if ($meses > 0) {
						$tempo_comunidade .= ($tempo_etapa ? ' ' : '') . $meses . ' mes' . ($meses > 1 ? 'es' : '');
					}

					if ($tempo_comunidade === '') {
						$tempo_comunidade = 'Menos de 1 mês';
					}
				}
				?>
                ?>
                <tr>
                    <td>
                        <?php if (!empty($m->foto)): ?>
                            <img src="<?php echo esc_url($m->foto); ?>" style="max-width:50px;">
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($m->nome); ?></td>
                    <td><?php echo esc_html($idade); ?></td>
                    <td><?php echo esc_html($m->grau_pertencimento); ?></td>
                    <td><?php echo esc_html($tempo_etapa); ?></td>
					<td><?php echo esc_html($m->recanto); ?></td>
                    <td>
                        <a href="<?php echo esc_url($edit_url); ?>" class="button"><?php _e('Editar', 'meu-plugin'); ?></a>
                        <a href="<?php echo esc_url($detail_url); ?>" class="button"><?php _e('Visualizar', 'meu-plugin'); ?></a>
                        <a href="<?php echo esc_url($delete_url); ?>" class="button button-danger" onclick="return confirm('<?php _e('Tem certeza que deseja excluir?', 'meu-plugin'); ?>');"><?php _e('Excluir', 'meu-plugin'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Paginação -->
    <div class="tablenav">
        <div class="tablenav-pages">
            <?php
            echo paginate_links([
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('« Anterior', 'meu-plugin'),
                'next_text' => __('Próximo »', 'meu-plugin'),
                'total' => $total_pages,
                'current' => $page
            ]);
            ?>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($){
    $('#mpd-table').DataTable({
        pageLength: 20,
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        }
    });
});
</script>