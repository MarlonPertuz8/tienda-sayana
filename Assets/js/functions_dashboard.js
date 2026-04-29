let miChartPagos;
let chartCat, chartProd;
let chartVentasMensuales, chartVentasAnuales;


document.addEventListener("DOMContentLoaded", function () {
    const chartCanv = document.getElementById('chartPagos');
    const filtro = document.getElementById('fechaFiltro');

    if (chartCanv) {
        const ctx = chartCanv.getContext('2d');

        // 1. Función para el gráfico de Pagos (NO SE TOCA)
        const renderizar = (wompi, trans, efec) => {
            if (miChartPagos) {
                miChartPagos.destroy();
            }
            miChartPagos = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Wompi', 'Transferencia', 'Contra Entrega'],
                    datasets: [{
                        data: [Number(wompi), Number(trans), Number(efec)],
                        backgroundColor: ['#c9a050', '#274e66', '#ed213a'],
                        hoverBackgroundColor: ['#e0b869', '#346382', '#ff4d5a'],
                        hoverOffset: 20,
                        borderWidth: 0,
                        spacing: 8,
                        weight: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '50%',
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 30,
                                usePointStyle: true,
                                font: { size: 14, family: "'Poppins', sans-serif", weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(39, 78, 102, 0.9)',
                            padding: 12,
                            cornerRadius: 8
                        }
                    }
                }
            });
        };

        // 2. Función para dibujar Categorías y Top Productos
        const renderNuevosGraficos = (data) => {
            if (!data) return;

            const elTicket = document.getElementById('txtTicketPromedio');
            if (elTicket && data.ticket_promedio) elTicket.innerText = data.ticket_promedio;

            const ctxCat = document.getElementById('chartCategorias');
            if (ctxCat) {
                if (chartCat) chartCat.destroy();
                chartCat = new Chart(ctxCat.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: data.cat_labels || [],
                        datasets: [{
                            data: data.cat_values || [],
                            backgroundColor: ['#274e66', '#c9a050', '#5d6d7e', '#17a2b8', '#e83e8c'],
                            borderWidth: 0,
                            spacing: 5
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            const ctxProd = document.getElementById('chartProductos');
            if (ctxProd) {
                if (chartProd) chartProd.destroy();
                chartProd = new Chart(ctxProd.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: data.prod_labels || [],
                        datasets: [{
                            label: 'Vendidos',
                            data: data.prod_values || [],
                            backgroundColor: '#c9a050',
                            borderRadius: 5
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } }
                    }
                });
            }
        };

        const renderGraficasHistoricas = (dataMensual, dataAnual) => {
            // Gráfica Mensual (Línea)
            const ctxMes = document.getElementById('chartVentasMensuales');
            if (ctxMes && dataMensual && dataMensual.labels) {
                if (chartVentasMensuales) chartVentasMensuales.destroy();
                chartVentasMensuales = new Chart(ctxMes.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: dataMensual.labels,
                        datasets: [{
                            label: 'Ventas ($)',
                            data: dataMensual.values,
                            borderColor: '#c9a050',
                            backgroundColor: 'rgba(201, 160, 80, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#274e66'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Gráfica Anual (Barras)
            const ctxAnio = document.getElementById('chartVentasAnuales');
            if (ctxAnio && dataAnual && dataAnual.labels) {
                if (chartVentasAnuales) chartVentasAnuales.destroy();
                chartVentasAnuales = new Chart(ctxAnio.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: dataAnual.labels,
                        datasets: [{
                            label: 'Ventas por Año',
                            data: dataAnual.values,
                            backgroundColor: '#274e66',
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        };

        // 3. Carga Inicial de Pagos
        renderizar(
            chartCanv.dataset.wompi || 0,
            chartCanv.dataset.trans || 0,
            chartCanv.dataset.efec || 0
        );

        // 4. Carga Inicial de Categorías y Productos
        const canvasCat = document.getElementById('chartCategorias');
        const canvasProd = document.getElementById('chartProductos');
        if (canvasCat && canvasCat.dataset.labels) {
            renderNuevosGraficos({
                cat_labels: JSON.parse(canvasCat.dataset.labels),
                cat_values: JSON.parse(canvasCat.dataset.values),
                prod_labels: JSON.parse(canvasProd.dataset.labels),
                prod_values: JSON.parse(canvasProd.dataset.values)
            });
        }

        // --- AÑADIDO: CARGA INICIAL DE HISTÓRICOS (MENSUAL / ANUAL) ---
        const canvasMes = document.getElementById('chartVentasMensuales');
        const canvasAnio = document.getElementById('chartVentasAnuales');
        if (canvasMes && canvasMes.dataset.labels && canvasAnio && canvasAnio.dataset.labels) {
            renderGraficasHistoricas(
                { labels: JSON.parse(canvasMes.dataset.labels), values: JSON.parse(canvasMes.dataset.values) },
                { labels: JSON.parse(canvasAnio.dataset.labels), values: JSON.parse(canvasAnio.dataset.values) }
            );
        }

        // 5. Configuración de Flatpickr
        if (filtro) {
            flatpickr(filtro, {
                locale: (typeof flatpickr.l10ns !== 'undefined') ? flatpickr.l10ns.es : 'default',
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                altInputClass: "form-control flatpickr-input",
                disableMobile: true,
                onChange: function (selectedDates, dateStr) {
                    if (dateStr === "") return;

                    // Mostramos un efecto de "cargando" opcional en los números
                    document.getElementById('txtVentasTotales').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch(base_url + '/Dashboard/getPagosDia?fecha=' + dateStr)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status) {
                                // 1. Actualiza la gráfica de Pay (Doughnut)
                                renderizar(data.wompi, data.trans, data.efec);

                                // 2. Actualiza gráficas de Categorías y Productos si vienen en la respuesta
                                if (data.adicionales) renderNuevosGraficos(data.adicionales);

                                // 3. ACTUALIZA LOS NÚMEROS DEL PANEL SUPERIOR (Consolidado)
                                if (data.consolidado) {
                                    document.getElementById('txtVentasTotales').innerText = data.consolidado.ventas_totales;
                                    document.getElementById('txtPedidosTotales').innerText = data.consolidado.pedidos_totales;
                                    document.getElementById('txtTicketHistorico').innerText = data.consolidado.ticket_historico;
                                }
                            } else {
                                // Si no hay datos, reseteamos todo a cero de forma elegante
                                renderizar(0, 0, 0);
                                document.getElementById('txtVentasTotales').innerText = "$ 0";
                                document.getElementById('txtPedidosTotales').innerText = "0";
                                document.getElementById('txtTicketHistorico').innerText = "$ 0";
                            }
                        })
                        .catch(err => {
                            console.error("Error en el filtro:", err);
                            Swal.fire("Error", "No se pudieron actualizar los datos del dashboard", "error");
                        });
                }
            });
        }
    }
});

// --- FUNCIONES GLOBALES (Modales, Status, etc.) ---

function fntDetalleMetrica(tipo) {
    const titulos = {
        'ventas_mes': 'Reporte de Ventas Mensuales',
        'pedidos_hoy': 'Listado de Pedidos - Hoy',
        'productos_count': 'Distribución de Inventario',
        'riesgo_stock': 'Alerta: Productos con Stock Crítico',
        'top_clientes': 'Ranking: Clientes VIP (Mejores Compradores)',
        'productos_top': 'Ranking de Productos Estrella'
    };

    Swal.fire({
        title: 'Cargando...',
        text: 'Consultando base de datos de Sayana Luxury',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch(base_url + '/Dashboard/getDetalleWidget/' + tipo)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(data => {
            if (data.status) {
                Swal.fire({
                    title: `<span style="color:#274e66; font-weight:bold;">${titulos[tipo]}</span>`,
                    html: data.html,
                    width: '900px',
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#c9a050',
                    didOpen: () => {
                        if ($('#tblDetalleExport').length > 0) {
                            $('#tblDetalleExport').DataTable({
                                "dom": 'Bfrtip',
                                "buttons": [
                                    { "extend": "copyHtml5", "text": "<i class='far fa-copy'></i> Copiar", "className": "btn-export btn-copy" },
                                    { "extend": "excelHtml5", "text": "<i class='fas fa-file-excel'></i> Excel", "className": "btn-export btn-excel", "title": titulos[tipo] },
                                    { "extend": "pdfHtml5", "text": "<i class='fas fa-file-pdf'></i> PDF", "className": "btn-export btn-pdf", "title": titulos[tipo] },
                                    { "extend": "print", "text": "<i class='fas fa-print'></i> Imprimir", "className": "btn-export btn-print" }
                                ],
                                "responsive": true,
                                "destroy": true,
                                "order": [[0, "desc"]],
                                "language": { "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" }
                            });
                        }
                    }
                });
            } else {
                Swal.fire("Atención", data.msg || "No se pudo obtener la información.", "warning");
            }
        })
        .catch(error => {
            console.error("Error Fetch:", error);
            Swal.fire("Error", "Ocurrió un problema con la conexión al servidor.", "error");
        });
}

function fntCambiarStatus(idpedido, status) {
    let titulo, texto, icono, colorConfirm;
    switch (status) {
        case 2:
            titulo = "¿Confirmar Pedido?";
            texto = "El pedido #" + idpedido + " pasará a estado 'Procesando'.";
            icono = "warning"; colorConfirm = "#28a745"; break;
        case 3:
            titulo = "¿Marcar como Enviado?";
            texto = "Se notificará al cliente que el pedido #" + idpedido + " va en camino.";
            icono = "info"; colorConfirm = "#17a2b8"; break;
        case 4:
            titulo = "¿Confirmar Entrega?";
            texto = "El pedido #" + idpedido + " se marcará como ENTREGADO.";
            icono = "success"; colorConfirm = "#28a745"; break;
        default:
            titulo = "¿Actualizar estado?";
            texto = "Se cambiará el estado del pedido #" + idpedido;
            icono = "question"; colorConfirm = "#c9a050";
    }

    Swal.fire({
        title: titulo, text: texto, icon: icono,
        showCancelButton: true, confirmButtonColor: colorConfirm,
        cancelButtonColor: "#274e66", confirmButtonText: "Sí, cambiar", cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) ejecutarCambioEstado(idpedido, status);
    });
}

function fntEnviarPedido(idpedido) {
    Swal.fire({
        title: "¿Marcar como Enviado?",
        text: "Se le notificará al cliente que su pedido va en camino",
        icon: "info", showCancelButton: true,
        confirmButtonColor: "#17a2b8", confirmButtonText: "Sí, enviar"
    }).then((result) => {
        if (result.isConfirmed) ejecutarCambioEstado(idpedido, 3);
    });
}

function ejecutarCambioEstado(idpedido, status) {
    let ajaxUrl = base_url + '/Pedidos/setConfirmarPedido';
    let formData = new FormData();
    formData.append('idpedido', idpedido);
    formData.append('status', status);

    fetch(ajaxUrl, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                Swal.fire("¡Listo!", data.msg, "success").then(() => { location.reload(); });
            } else {
                Swal.fire("Error", data.msg, "error");
            }
        });
}



