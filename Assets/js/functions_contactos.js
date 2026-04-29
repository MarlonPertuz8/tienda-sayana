let tableContactos;


document.addEventListener('DOMContentLoaded', function() {
    // Inicialización de la DataTable
    tableContactos = $('#tableContactos').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": base_url + "/Contactos/getContactos",
            "dataSrc": ""
        },
        "columns": [
            {"data": "id"},
            {"data": "fecha"},
            {"data": "nombre"},
            {"data": "email"},
            {"data": "status"},
            {"data": "options"}
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        "drawCallback": function(settings) {
            $('.page-link').css('border-radius', '0px');
        }
    });

    // Envío de la Respuesta desde el modalRespuesta
    let formRespuesta = document.querySelector("#formRespuesta");
    if (formRespuesta) {
        formRespuesta.onsubmit = function(e) {
            e.preventDefault();
            let strRespuesta = document.querySelector('#txtRespuesta').value;
            
            if (strRespuesta == "") {
                swal("Atención", "Por favor escribe una respuesta.", "error");
                return false;
            }

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Contactos/setRespuesta';
            let formData = new FormData(formRespuesta);
            
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            
            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    try {
                        // Limpiamos la respuesta de posibles espacios o errores de PHP
                        let objData = JSON.parse(request.responseText.trim());
                        
                        if (objData.status) {
                            $('#modalRespuesta').modal('hide');
                            formRespuesta.reset();
                            
                            // Usamos el callback de SweetAlert para asegurar que la tabla refresque después del click
                            swal("Éxito", objData.msg, "success");
                            
                            // Recarga segura de DataTables
                            $('#tableContactos').DataTable().ajax.reload();
                        } else {
                            swal("Error", objData.msg, "error");
                        }
                    } catch (e) {
                        console.error("Error parseando respuesta:", e);
                        // Si falla el parseo, forzamos recarga para no quedar bloqueados
                        $('#tableContactos').DataTable().ajax.reload();
                    }
                }
            }
        }
    }
});

// Función 1: Ver detalles del mensaje (modalContactos)
function fntViewContacto(idcontacto) {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Contactos/getContacto/' + idcontacto;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            try {
                let objData = JSON.parse(request.responseText.trim());
                if (objData.status) {
                    document.getElementById("celNombre").innerHTML = objData.data.nombre;
                    document.getElementById("celEmail").innerHTML = objData.data.email;
                    document.getElementById("celFecha").innerHTML = objData.data.fecha;
                    document.getElementById("celMensaje").innerHTML = objData.data.mensaje;
                    $('#modalViewMessage').modal('show'); 
                }
            } catch (e) {
                console.error("Error cargando contacto:", e);
            }
        }
    };
}

// Función 2: Abrir modal de Respuesta (modalRespuesta)
function fntRespuesta(idcontacto) {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Contactos/getContacto/' + idcontacto;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            try {
                let objData = JSON.parse(request.responseText.trim());
                if (objData.status) {
                    document.getElementById("idContacto").value = objData.data.id;
                    document.getElementById("txtEmail").value = objData.data.email;
                    document.getElementById("txtRespuesta").value = "";
                    $('#modalRespuesta').modal('show');
                }
            } catch (e) {
                console.error("Error preparando respuesta:", e);
            }
        }
    };
}

// Función 3: Eliminar Contacto
function fntDelContacto(idcontacto) {
    swal({
        title: "Eliminar Mensaje",
        text: "¿Realmente desea eliminar este mensaje?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if (isConfirm) {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Contactos/delContacto';
            let strData = "idContacto=" + idcontacto;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText.trim());
                    if (objData.status) {
                        swal("Eliminado", objData.msg, "success");
                        $('#tableContactos').DataTable().ajax.reload();
                    } else {
                        swal("Error", objData.msg, "error");
                    }
                }
            }
        }
    });
}
