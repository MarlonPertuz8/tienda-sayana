let tableProveedores;

document.addEventListener('DOMContentLoaded', function(){
    // Inicialización de DataTable con Idioma Local (Evita error CORS)
    tableProveedores = $('#tableProveedores').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sSearch":         "Buscar:",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        "ajax": { "url": base_url + "/Proveedores/getProveedores", "dataSrc": "" },
        "columns": [
            { "data": "idproveedor" },
            { "data": "nombre" },
            { "data": "nit" },
            { "data": "telefono" },
            { "data": "options" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // Envío del Formulario
    let formProveedor = document.querySelector("#formProveedor");
    formProveedor.onsubmit = function(e) {
        e.preventDefault();
        let formData = new FormData(formProveedor);
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("POST", base_url + '/Proveedores/setProveedor', true);
        request.send(formData);
        
        request.onreadystatechange = function() {
            if(request.readyState == 4 && request.status == 200) {
                try {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status) {
                        $('#modalFormProveedor').modal("hide");
                        formProveedor.reset();
                        Swal.fire({
                            title: "Proveedores",
                            text: objData.msg,
                            icon: "success",
                            confirmButtonColor: "#009688"
                        });
                        tableProveedores.ajax.reload();
                    } else {
                        Swal.fire("Error", objData.msg, "error");
                    }
                } catch (error) {
                    console.error("Error en la respuesta del servidor:", request.responseText);
                    Swal.fire("Error Técnico", "La respuesta del servidor no es un JSON válido.", "error");
                }
            }
        }
    }
}, false);

// --- Funciones Globales ---

function openModal() {
    document.querySelector('#idProveedor').value = "";
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Proveedor";
    document.querySelector('#formProveedor').reset();
    $('#modalFormProveedor').modal('show');
}

function fntViewInfo(idproveedor) {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url + '/Proveedores/getProveedor/' + idproveedor, true);
    request.send();
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if(objData.status) {
                Swal.fire({
                    title: '<span style="color:#009688">Detalles del Proveedor</span>',
                    html: `<div class="text-left" style="padding: 10px; font-family: sans-serif;">
                            <p><strong>Nombre:</strong> ${objData.data.nombre}</p>
                            <p><strong>NIT:</strong> ${objData.data.nit}</p>
                            <p><strong>Teléfono:</strong> ${objData.data.telefono}</p>
                            <p><strong>Dirección:</strong> ${objData.data.direccion}</p>
                           </div>`,
                    icon: 'info',
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#009688',
                    showClass: { popup: 'animate__animated animate__fadeInDown' }
                });
            }
        }
    }
}

function fntEditInfo(idproveedor) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Proveedor";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", base_url + '/Proveedores/getProveedor/' + idproveedor, true);
    request.send();
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if(objData.status) {
                document.querySelector("#idProveedor").value = objData.data.idproveedor;
                document.querySelector("#txtNombre").value = objData.data.nombre;
                document.querySelector("#txtNit").value = objData.data.nit;
                document.querySelector("#txtTelefono").value = objData.data.telefono;
                document.querySelector("#txtDireccion").value = objData.data.direccion;
                $('#modalFormProveedor').modal('show');
            }
        }
    }
}

function fntDelInfo(idproveedor) {
    Swal.fire({
        title: "Eliminar Proveedor",
        text: "¿Realmente quiere eliminar al proveedor?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            request.open("POST", base_url + '/Proveedores/delProveedor', true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send("idProveedor=" + idproveedor);
            request.onreadystatechange = function() {
                if(request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status) {
                        Swal.fire("Eliminado", objData.msg, "success");
                        tableProveedores.ajax.reload();
                    }
                }
            }
        }
    });
}