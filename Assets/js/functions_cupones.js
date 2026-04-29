let tableCupones;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function() {
    // Inicialización de DataTable
    tableCupones = $('#tableCupones').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": base_url + "/Cupones/getCupones",
            "dataSrc": ""
        },
        "columns": [
            { "data": "idcupon" },
            { "data": "codigo" },
            { "data": "descuento" },
            { "data": "status" },
            { "data": "options" }
        ],
        "columnDefs": [
            { "className": "text-center", "targets": [3, 4] } 
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // Envío del Formulario
    let formCupon = document.querySelector("#formCupon");
    if (formCupon) {
        formCupon.onsubmit = function(e) {
            e.preventDefault();

            let strCodigo = document.querySelector('#txtCodigo').value;
            let intDescuento = document.querySelector('#txtDescuento').value;
            let intLimite = document.querySelector('#txtLimite').value;
            let strFecha = document.querySelector('#txtFechaVencimiento').value;

            if (strCodigo == '' || intDescuento == '' || intLimite == '' || strFecha == '') {
                // ✅ CORREGIDO: Swal.fire
                Swal.fire("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            if(divLoading) divLoading.style.display = "flex";

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Cupones/setCupon';
            let formData = new FormData(formCupon);
            
            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        // ✅ CORREGIDO: Cierre de modal y recarga de tabla
                        $('#modalFormCupon').modal("hide");
                        formCupon.reset();
                        
                        Swal.fire({
                            title: "Cupones",
                            text: objData.msg,
                            icon: "success",
                            confirmButtonColor: '#009688'
                        });

                        // Recarga la tabla usando la API de DataTable
                        $('#tableCupones').DataTable().ajax.reload();
                    } else {
                        // ✅ CORREGIDO: Swal.fire
                        Swal.fire("Error", objData.msg, "error");
                    }
                }
                if(divLoading) divLoading.style.display = "none";
            }
        }
    }
}, false);

// FUNCIÓN PARA VER INFO DEL CUPÓN
function fntViewCupon(idcupon) {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Cupones/getCupon/' + idcupon;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if (objData.status) {
                // ✅ USANDO SINTAXIS CORRECTA
                Swal.fire({
                    title: "<strong>Detalles del Cupón</strong>",
                    icon: "info",
                    html: `
                        <div class="text-left">
                            <p><strong>Código:</strong> ${objData.data.codigo}</p>
                            <p><strong>Descuento:</strong> ${objData.data.descuento}%</p>
                            <p><strong>Límite de uso:</strong> ${objData.data.limite_uso}</p>
                            <p><strong>Vencimiento:</strong> ${objData.data.fecha_vencimiento}</p>
                            <p><strong>Estado:</strong> ${objData.data.status == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>'}</p>
                        </div>
                    `,
                    showCloseButton: true,
                    focusConfirm: false,
                    confirmButtonText: '<i class="fa fa-thumbs-up"></i> Entendido',
                    confirmButtonColor: '#009688'
                });
            } else {
                Swal.fire("Error", objData.msg, "error");
            }
        }
    }
}

function fntEditCupon(idcupon) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Cupón";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    
    document.querySelector('#btnActionForm').classList.remove("btn-info");
    document.querySelector('#btnActionForm').classList.add("btn-primary");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Cupones/getCupon/' + idcupon;
    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if (objData.status) {
                document.querySelector("#idCupon").value = objData.data.idcupon;
                document.querySelector("#txtCodigo").value = objData.data.codigo;
                document.querySelector("#txtDescuento").value = objData.data.descuento;
                document.querySelector("#txtLimite").value = objData.data.limite_uso;
                document.querySelector("#txtFechaVencimiento").value = objData.data.fecha_vencimiento;
                document.querySelector("#listStatus").value = objData.data.status;
                
                $('#modalFormCupon').modal('show');
            } else {
                // ✅ CORREGIDO: Swal.fire
                Swal.fire("Error", objData.msg, "error");
            }
        }
    }
}

function fntDelCupon(idcupon) {
    Swal.fire({
        title: "Eliminar Cupón",
        text: "¿Realmente quiere eliminar el cupón?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#009688",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Cupones/delCupon';
            let strData = "idCupon=" + idcupon;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            
            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        Swal.fire("Eliminar!", objData.msg, "success");
                        $('#tableCupones').DataTable().ajax.reload();
                    } else {
                        Swal.fire("Atención!", objData.msg, "error");
                    }
                }
            }
        }
    });
}

function openModal() {
    document.querySelector('#idCupon').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.add("btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Cupón";
    document.querySelector("#formCupon").reset();
    $('#modalFormCupon').modal('show');
}