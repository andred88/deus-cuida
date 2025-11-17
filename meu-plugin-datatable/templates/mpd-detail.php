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
?>

<div class="wrap">
    <h1><?php _e('Detalhes do Membro', 'meu-plugin'); ?></h1>
    <table class="widefat fixed striped">
        <tbody> <?php 
				$idade = $membro->nascimento ? date_diff(date_create($membro->nascimento), date_create('today'))->y : ''; 
				$tempo_etapa = '';
				if (!empty($membro->etapa)) {
					$intervalo = date_diff(date_create($membro->etapa), date_create('today'));
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
				if (!empty($membro->acolhimento)) {
					$intervalo = date_diff(date_create($membro->acolhimento), date_create('today'));
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
            <tr><th>Foto</th><td><?php if ($membro->foto) echo '<img src="'.esc_url($membro->foto).'" style="max-width:150px;">'; ?></td></tr>
            <tr><th>Nome completo</th><td><?php echo esc_html($membro->nome); ?></td></tr>
            <tr><th>Contato celular</th><td><?php echo esc_html($membro->celular); ?></td></tr>
            <tr><th>Email</th><td><?php echo esc_html($membro->email); ?></td></tr>
            <tr><th>Data de nascimento</th><td><?php echo esc_html($membro->nascimento); ?></td></tr>
			<tr><th>Idade</th><td><?php echo esc_html($idade); ?></td></tr>
            <tr><th>Estado Civil</th><td><?php echo esc_html($membro->estado_civil); ?></td></tr>
            <tr><th>Conjugue</th><td><?php echo esc_html($membro->conjugue); ?></td></tr>
            <tr><th>Filhos</th><td><?php echo esc_html($membro->filhos); ?></td></tr>
            <tr><th>Nível de escolaridade</th><td><?php echo esc_html($membro->escolaridade); ?></td></tr>
            <tr><th>Curso</th><td><?php echo esc_html($membro->curso); ?></td></tr>
            <tr><th>Benefício</th><td><?php echo esc_html($membro->beneficio); ?></td></tr>
            <tr><th>Grau de pertencimento</th><td><?php echo esc_html($membro->grau_pertencimento); ?></td></tr>
            <tr><th>Modo de compromisso</th><td><?php echo esc_html($membro->modo_compromisso); ?></td></tr>
            <tr><th>Acolhimento</th><td><?php echo esc_html($membro->acolhimento); ?></td></tr>
			<tr><th>Tempo na Comunidade</th><td><?php echo esc_html($tempo_comunidade); ?></td></tr>
            <tr><th>Início da etapa</th><td><?php echo esc_html($membro->etapa); ?></td></tr>
            <tr><th>Tempo na etapa</th><td><?php echo esc_html($tempo_etapa); ?></td></tr>
			<tr><th>Férias</th><td><?php echo esc_html($membro->ferias); ?></td></tr>
            <tr><th>Recebeu ajuda de custo?</th><td><?php echo $membro->ajuda_custo ? __('Sim', 'meu-plugin') : __('Não', 'meu-plugin'); ?></td></tr>
            <tr><th>Recanto</th><td><?php echo esc_html($membro->recanto); ?></td></tr>
            <tr><th>Função</th><td><?php echo esc_html($membro->funcao); ?></td></tr>
            <tr><th>Formador responsável</th><td><?php echo esc_html($membro->formador); ?></td></tr>
            <tr><th>Observações</th><td><?php echo esc_html($membro->observacoes); ?></td></tr>
            <tr><th>Anexos</th><td>
                <?php
                $anexos = $membro->anexos ? json_decode($membro->anexos, true) : [];
                if (!empty($anexos)) {
                    echo '<ul>';
                    foreach ($anexos as $file) {
                        echo '<li><a href="'.esc_url($file).'" target="_blank">'.basename($file).'</a></li>';
                    }
                    echo '</ul>';
                } else {
                    echo __('Nenhum anexo', 'meu-plugin');
                }
                ?>
			</td></tr>
		</tbody>
    </table>
	
	<hr>

	<h2>Acompanhamentos</h2>
	<?php
	$acomp_table = $wpdb->prefix . 'mpd_acompanhamentos';
	$acompanhamentos = $wpdb->get_results($wpdb->prepare(
		"SELECT * FROM $acomp_table WHERE member_id = %d ORDER BY data_hora DESC",
		$member_id
	));

	if ($acompanhamentos) {
		echo '<table class="widefat fixed striped">';
		echo '<thead><tr><th>Data/Hora</th><th>Formador</th><th>Descrição</th></tr></thead><tbody>';
		foreach ($acompanhamentos as $a) {
			echo "<tr>
				<td>" . esc_html($a->data_hora) . "</td>
				<td>" . esc_html($a->formador) . "</td>
				<td>" . esc_html($a->descricao) . "</td>
			</tr>";
		}
		echo '</tbody></table>';
	} else {
		echo '<p>Nenhum acompanhamento registrado para este membro.</p>';
	}
	?>

    <p style="margin-top:20px;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=mpd-list')); ?>" class="button"><?php _e('Voltar à lista', 'meu-plugin'); ?></a>
        <a href="<?php echo esc_url(admin_url('admin.php?page=mpd-edit&id=' . intval($membro->id))); ?>" class="button button-primary"><?php _e('Editar', 'meu-plugin'); ?></a>
		<a href="<?php echo admin_url('admin.php?page=mpd-acompanhamentos&action=add&member_id=' . $member_id); ?>" class="button button-primary">
        Adicionar Acompanhamento
		</a>
    </p>
</div>
