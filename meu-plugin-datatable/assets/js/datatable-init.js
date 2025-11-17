jQuery(document).ready(function($){
    // Verifica se a tabela já foi inicializada
    if (!$.fn.DataTable.isDataTable('#mpd-table')) {
        var table = $('#mpd-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy', text: 'Copiar' },
                { extend: 'csv', text: 'Exportar CSV' },
                { extend: 'excel', text: 'Exportar Excel' },
                { extend: 'print', text: 'Imprimir' }
            ],
            responsive: true,
            pageLength: 20,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            }
        });

        // Filtro por Recanto
        $('#filter-recanto').on('change', function(){
            table.column(4).search(this.value).draw();
        });

        // Filtro por Grau de Pertencimento
        $('#filter-grau').on('change', function(){
            table.column(3).search(this.value).draw();
        });
    }
});