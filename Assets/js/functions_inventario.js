let tableInventario;

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inicializar DataTable con Botones de Exportación
    tableInventario = $('#tableInventario').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": base_url + "/Inventario/getEntradas",
            "dataSrc": ""
        },
        "columns": [
            { "data": "fecha" },
            { "data": "producto" },
            { "data": "tipo_label" },
            { "data": "cantidad" },
            { "data": "precio_costo_format" },
            { "data": "proveedor" },
            { "data": "usuario" },
            { "data": "options" }
        ],

        dom: 'frtip',
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    flatpickr("#txtFechaInicio", {
        locale: "es",
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
    });

    flatpickr("#txtFechaFin", {
        locale: "es",
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
    });

    // 2. Cargar KPIs y Productos al iniciar
    actualizarResumen();
    fntProductos();
    fntProveedores();

    // 3. Manejo del Formulario
    let formInventario = document.querySelector("#formInventario");
    if (formInventario) {
        formInventario.onsubmit = function (e) {
            e.preventDefault();

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Inventario/setEntrada';
            let formData = new FormData(formInventario);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    try {
                        let objData = JSON.parse(request.responseText);

                        if (objData.status) {
                            // Cerrar modal y limpiar formulario
                            $('#modalFormInventario').modal("hide");
                            formInventario.reset();

                            // CORRECCIÓN: Uso de Swal.fire para SweetAlert2
                            Swal.fire("Inventario", objData.msg, "success");

                            // Recargar tabla y widgets
                            if (tableInventario) tableInventario.ajax.reload();
                            actualizarResumen();
                        } else {
                            // Mostrar error devuelto por el servidor
                            Swal.fire("Error", objData.msg, "error");
                        }
                    } catch (error) {
                        console.error("Error al parsear JSON:", request.responseText);
                        Swal.fire("Error", "La respuesta del servidor no es válida.", "error");
                    }
                }
            };
        };
    }
});

// FUNCIÓN PARA LLENAR LOS WIDGETS (KPIs)
function actualizarResumen() {
    let ajaxUrl = base_url + '/Inventario/getResumenWidgets';
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            document.querySelector('#totalInversion').innerHTML = objData.total_inversion_format;
            document.querySelector('#totalPiezas').innerHTML = objData.total_stock + " Unds";
            document.querySelector('#productosAlerta').innerHTML = objData.total_alerta + " Prods";
            document.querySelector('#totalProveedores').innerHTML = objData.total_proveedores;
        }
    }
}

function fntProductos() {
    if (document.querySelector('#listProducto')) {
        let ajaxUrl = base_url + '/Inventario/getSelectProductos';
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET", ajaxUrl, true);
        request.send();
        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                let listProducto = document.querySelector('#listProducto');
                listProducto.innerHTML = request.responseText;
                $('#listProducto').selectpicker('refresh');

                // --- NUEVA LÓGICA DE CONEXIÓN ---
                listProducto.onchange = function () {
                    // 1. Buscamos el atributo data-colores del producto elegido
                    let selectedOption = this.options[this.selectedIndex];
                    let coloresString = selectedOption.getAttribute('data-colores');
                    let listColor = document.querySelector('#listColor');

                    // 2. Limpiamos el selector de colores
                    listColor.innerHTML = "<option value=''>Seleccione color</option>";

                    if (coloresString) {
                        // 3. Convertimos "Dorado,Plateado" en una lista y llenamos el select
                        let arrayColores = coloresString.split(',');
                        arrayColores.forEach(color => {
                            let option = new Option(color.trim(), color.trim());
                            listColor.add(option);
                        });
                    }
                    // 4. Refrescamos el diseño del select
                    $('#listColor').selectpicker('refresh');
                };
            }
        }
    }
}

function fntDelInfo(idEntrada) {
    Swal.fire({
        title: "Anular Entrada",
        text: "¿Realmente quieres eliminar esta entrada? El stock se descontará automáticamente del producto.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            let ajaxUrl = base_url + '/Inventario/delEntrada';
            let strData = "idEntrada=" + idEntrada;
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        Swal.fire("Eliminado", objData.msg, "success");
                        tableInventario.ajax.reload();
                        actualizarResumen(); // Para que los KPIs de inversión y stock se refresquen
                    } else {
                        Swal.fire("Error", objData.msg, "error");
                    }
                }
            }
        }
    });
}

function fntViewInfo(idEntrada) {
    let ajaxUrl = base_url + '/Inventario/getEntrada/' + idEntrada;
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if (objData.status) {
                // Suponiendo que tienes un modal llamado modalViewInventario
                document.querySelector("#celProducto").innerHTML = objData.data.producto;
                document.querySelector("#celCantidad").innerHTML = objData.data.cantidad;
                document.querySelector("#celPrecio").innerHTML = objData.data.precio_costo;
                document.querySelector("#celProveedor").innerHTML = objData.data.proveedor;
                $('#modalViewInventario').modal('show');
            } else {
                Swal.fire("Error", objData.msg, "error");
            }
        }
    }
}
function fntEditInfo(idEntrada) {
    // 1. Ajustes visuales del modal para modo Edición
    document.querySelector('#titleModal').innerHTML = "Actualizar Entrada";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    let ajaxUrl = base_url + '/Inventario/getEntrada/' + idEntrada;
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            try {
                let objData = JSON.parse(request.responseText);
                if (objData.status) {
                    // 2. Llenar campos ocultos y principales
                    document.querySelector("#idEntrada").value = objData.data.identrada;
                    document.querySelector("#listProducto").value = objData.data.producto_id;
                    $('#listProducto').selectpicker('refresh');

                    // 3. Cargar dinámicamente los colores del producto seleccionado
                    document.querySelector("#listProducto").dispatchEvent(new Event('change'));

                    // Pequeña espera para que el select de colores se llene antes de asignar el valor
                    setTimeout(() => {
                        document.querySelector("#listColor").value = objData.data.color;
                        $('#listColor').selectpicker('refresh');
                    }, 150);

                    // 4. Llenar valores numéricos
                    document.querySelector("#txtCantidad").value = objData.data.cantidad;
                    document.querySelector("#txtPrecioCosto").value = objData.data.precio_costo;

                    // 5. CORRECCIÓN CLAVE: Asignar el ID del Proveedor al nuevo Select
                    if(document.querySelector("#listProveedor")){
                        document.querySelector("#listProveedor").value = objData.data.proveedor_id;
                        $('#listProveedor').selectpicker('refresh');
                    }

                    // 6. Mostrar el modal
                    $('#modalFormInventario').modal('show');

                } else {
                    Swal.fire("Error", objData.msg, "error");
                }
            } catch (error) {
                console.error("Error al procesar la edición:", error);
            }
        }
    }
}

function openModal() {
    document.querySelector('#idEntrada').value = ""; // Limpia el ID
    document.querySelector('#titleModal').innerHTML = "Nueva Entrada de Stock";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#formInventario').reset();
    $('#listProducto').selectpicker('refresh');
    $('#listColor').selectpicker('refresh');
    $('#modalFormInventario').modal('show');
}

// Agrega esto al final de tu archivo JS actual
function generarReportePDF() {
    // Reporte General
    const url = base_url + '/Reportes/inventarioPDF';
    window.open(url, '_blank');
}

function generarReporteMovimientos() {
    const fechaInicio = document.querySelector("#txtFechaInicio").value;
    const fechaFin = document.querySelector("#txtFechaFin").value;

    if (fechaInicio == "" || fechaFin == "") {
        Swal.fire("Atención", "Debes seleccionar ambas fechas para filtrar.", "warning");
        return;
    }

    // Cambiamos a formato GET para que el controlador lo reciba sin errores de ruta
    const url = base_url + '/Reportes/movimientosPDF?f1=' + fechaInicio + '&f2=' + fechaFin;
    window.open(url, '_blank');
}
function fntProveedores() {
    if (document.querySelector('#listProveedor')) {
        let ajaxUrl = base_url + '/Inventario/getSelectProveedores';
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET", ajaxUrl, true);
        request.send();
        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                document.querySelector('#listProveedor').innerHTML = request.responseText;
                $('#listProveedor').selectpicker('refresh');
            }
        }
    }
}