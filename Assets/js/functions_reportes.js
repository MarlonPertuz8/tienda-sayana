document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector("#btnFiltrar")) {
        document.querySelector("#btnFiltrar").addEventListener('click', e => {
            e.preventDefault();
            generarReporteMovimientos();
        });
    }
    if (document.querySelector("#btnReporteTotal")) {
        document.querySelector("#btnReporteTotal").addEventListener('click', e => {
            e.preventDefault();
            generarReportePDF();
        });
    }
}, false);

function generarReportePDF() {
    window.open(base_url + '/Reportes/inventarioPDF', '_blank');
}

function generarReporteMovimientos() {
    const inputInicio = document.querySelector("#txtFechaInicio") || document.querySelector("#fechaInicio");
    const inputFin = document.querySelector("#txtFechaFin") || document.querySelector("#fechaFin");

    if (!inputInicio.value || !inputFin.value) {
        Swal.fire("Atención", "Seleccione el rango de fechas.", "error");
        return;
    }

    // Usamos parámetros GET para máxima compatibilidad
    const url = `${base_url}/Reportes/movimientosPDF?f1=${inputInicio.value}&f2=${inputFin.value}`;
    window.open(url, '_blank');
}