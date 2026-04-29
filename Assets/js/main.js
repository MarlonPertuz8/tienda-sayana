(function () {
	"use strict";

	var treeviewMenu = $('.app-menu');

	// Toggle Sidebar
	$('[data-toggle="sidebar"]').click(function(event) {
		event.preventDefault();
		$('.app').toggleClass('sidenav-toggled');
	});

	// Activate sidebar treeview toggle
	$("[data-toggle='treeview']").click(function(event) {
		event.preventDefault();
		if(!$(this).parent().hasClass('is-expanded')) {
			treeviewMenu.find("[data-toggle='treeview']").parent().removeClass('is-expanded');
		}
		$(this).parent().toggleClass('is-expanded');
	});

	// Set initial active toggle
	$("[data-toggle='treeview'].is-expanded").parent().toggleClass('is-expanded');

	//Activate bootstrip tooltips
	$("[data-toggle='tooltip']").tooltip();

})();
/**
 * RESPONSIVE STACK GLOBAL - SAYANA LUXURY
 * Este código detecta automáticamente cualquier DataTable y 
 * le asigna etiquetas para la vista de "tarjetas" en móvil.
 */
$(document).on('draw.dt', function (e, settings) {
    // Obtenemos la tabla que se acaba de dibujar
    var api = new $.fn.dataTable.Api(settings);
    var $table = $(api.table().node());
    var headerLabels = [];

    // 1. Extraer los nombres de las columnas desde el header
    $table.find('thead th').each(function () {
        headerLabels.push($(this).text().trim());
    });

    // 2. Asignar cada nombre al atributo data-label de las celdas
    $table.find('tbody tr').each(function () {
        $(this).find('td').each(function (index) {
            if (headerLabels[index] && headerLabels[index] !== "") {
                $(this).attr('data-label', headerLabels[index]);
            }
        });
    });
});