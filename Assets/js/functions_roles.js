var tableRoles;
var divLoading = document.querySelector("#divLoading");
document.addEventListener('DOMContentLoaded', function () {

    tableRoles = $('#tableRoles').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            // ✅ CORREGIDO: evita CORS usando local
            "url": base_url + "/Assets/js/plugins/Spanish.json"
        },
        "ajax": {
            "url": base_url + "/Roles/getRoles",
            "dataSrc": ""
        },
        "columns": [
            { "data": "idrol" },
            { "data": "nombrerol" },
            { "data": "descripcion" },
            { "data": "status" },
            { "data": "options" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        "drawCallback": function () {
            fntEditRol();
            fntDelRol();
            fntPermisos();
        }
    });

    // =========================
    // FORMULARIO ROLES
    // =========================
    var formRol = document.querySelector("#formRol");

        formRol.onsubmit = function (e) {
        e.preventDefault();

        // 1. Captura de datos (Solo quitamos espacios al principio y al final)
        let strNombre = document.querySelector('#txtNombre').value.trim();
        let strDescripcion = document.querySelector('#txtDescripcion').value.trim();
        let intStatus = document.querySelector('#listStatus').value;
        
        // Elementos de UI
        const btnAction = document.querySelector("#btnActionForm");
        const btnText = document.querySelector("#btnText");
        const divLoading = document.querySelector("#divLoading");

        // 2. Validación de campos obligatorios
        if (strNombre === '' || strDescripcion === '' || intStatus === '') {
            Swal.fire("Atención", "Todos los campos son obligatorios.", "error");
            return false;
        }

        // 3. Activar estado de carga (Pantalla Completa)
        if (btnAction) btnAction.disabled = true;
        if (btnText) btnText.innerHTML = "Guardando...";
        
        if (divLoading) {
            divLoading.classList.add("fullscreen");
            divLoading.style.display = "flex";
        }

        // 4. Preparación y envío mediante AJAX
        const ajaxUrl = base_url + '/Roles/setRol';
        const formData = new FormData(formRol);
        // Nota: El FormData tomará el valor directamente del input tal cual esté escrito.

        const request = new XMLHttpRequest();
        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState === 4) {
                
                // 5. Desactivar Loader y Reactivar Botón (Se ejecuta siempre al terminar)
                if (divLoading) {
                    divLoading.style.display = "none";
                    divLoading.classList.remove("fullscreen");
                }

                if (btnAction) btnAction.disabled = false;
                if (btnText) btnText.innerHTML = "Guardar";

                if (request.status === 200) {
                    try {
                        const objData = JSON.parse(request.responseText);

                        if (objData.status) {
                            // Éxito: Cerrar modal, limpiar form y avisar al usuario
                            $('#modalFormRoles').modal("hide");
                            formRol.reset();

                            Swal.fire({
                                title: "Roles de usuario",
                                text: objData.msg,
                                icon: "success",
                                confirmButtonColor: '#28a745'
                            });

                            // Recargar la tabla de datos si existe
                            if (typeof tableRoles !== 'undefined') {
                                tableRoles.ajax.reload(null, false);
                            }

                        } else {
                            // Error lógico del servidor (ej: nombre duplicado)
                            Swal.fire("Atención", objData.msg, "error");
                        }
                    } catch (error) {
                        Swal.fire("Error", "Error al procesar la respuesta del servidor.", "error");
                    }
                } else {
                    // Error de conexión (ej: servidor caído o error 500)
                    Swal.fire("Error", "Fallo de conexión con el servidor.", "error");
                }
            }
        };
    };

});


// =========================
// CORRECCIÓN DE ARIA-HIDDEN
// =========================
$('#modalFormRoles').on('hidden.bs.modal', function () {
    document.activeElement.blur();
});


// =========================
// NUEVO ROL
// =========================
function openModalRol() {
    document.querySelector('#idRol').value = "";
    document.querySelector('#titleModal').innerHTML = "Nuevo Rol";
    document.querySelector('#btnText').innerHTML = "Guardar";

    document.querySelector('.modal-header').classList.remove("headerUpdate");
    document.querySelector('.modal-header').classList.add("headerRegister");

    document.querySelector('#btnActionForm').classList.remove("btn-info");
    document.querySelector('#btnActionForm').classList.add("btn-primary");

    document.querySelector("#formRol").reset();

    // ✅ Si usas selectpicker
    $('#listStatus').selectpicker('refresh');

    $('#modalFormRoles').modal('show');
}


// =========================
// EDITAR ROL
// =========================
function fntEditRol() {
    document.querySelectorAll(".btnEditRol").forEach(function (btn) {
        btn.onclick = function () {

            document.querySelector('#titleModal').innerHTML = "Actualizar Rol";
            document.querySelector('#btnText').innerHTML = "Actualizar";

            document.querySelector('.modal-header').classList.remove("headerRegister");
            document.querySelector('.modal-header').classList.add("headerUpdate");

            // ✅ CORREGIDO: eliminar btn-primary antes de agregar btn-info
            // document.querySelector('#btnActionForm').classList.remove("btn-primary");
            document.querySelector('#btnActionForm').classList.add("btn-info");

            var idrol = this.getAttribute("rl");

            var request = new XMLHttpRequest();
            var ajaxUrl = base_url + '/Roles/getRol/' + idrol;

            request.open("GET", ajaxUrl, true);
            request.send();

            request.onreadystatechange = function () {
                if (request.readyState === 4 && request.status === 200) {
                    var objData = JSON.parse(request.responseText);

                    if (objData.status) {
                        document.querySelector("#idRol").value = objData.data.idrol;
                        document.querySelector("#txtNombre").value = objData.data.nombrerol;
                        document.querySelector("#txtDescripcion").value = objData.data.descripcion;

                        var htmlSelect = "";

                        if (objData.data.status == 1) {
                            htmlSelect = `<option value="1" selected>Activo</option>
                                          <option value="2">Inactivo</option>`;
                        } else {
                            htmlSelect = `<option value="1">Activo</option>
                                          <option value="2" selected>Inactivo</option>`;
                        }

                        document.querySelector("#listStatus").innerHTML = htmlSelect;

                        // ✅ CORRECTO: refresh (no render)
                        $('#listStatus').selectpicker('refresh');

                        $('#modalFormRoles').modal('show');
                    } else {
                        Swal.fire("Error", objData.msg, "error");
                    }
                }
            };
        };
    });
}


// =========================
// ELIMINAR ROL
// =========================
function fntDelRol() {
    document.querySelectorAll(".btnDelRol").forEach(function (btn) {
        btn.onclick = function () {

            let idrol = this.getAttribute("rl");

            Swal.fire({
                title: '¿Eliminar rol?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> Sí, eliminar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true
            }).then((result) => {

                if (result.isConfirmed) {

                    let request = new XMLHttpRequest();
                    let ajaxUrl = base_url + '/Roles/delRol/';
                    let strData = "idrol=" + idrol;

                    request.open("POST", ajaxUrl, true);
                    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    request.send(strData);

                    request.onreadystatechange = function () {
                        if (request.readyState == 4 && request.status == 200) {

                            let objData = JSON.parse(request.responseText);

                            if (objData.status) {

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Rol eliminado',
                                    text: objData.msg,
                                    confirmButtonColor: '#28a745',
                                    timer: 1800,
                                    showConfirmButton: false
                                });

                                tableRoles.ajax.reload(null, false);

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: objData.msg,
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        }
                    }
                }
            });
        };
    });
}


// =========================
// PERMISOS
// =========================
function fntPermisos() {
    var btnPermisosRol = document.querySelectorAll(".btnPermisoRol");

    btnPermisosRol.forEach(function (btn) {
        btn.onclick = function () { // Usar onclick es más directo aquí
            var idrol = this.getAttribute("rl");
            var ajaxUrl = base_url + '/Permisos/getPermisosRol/' + idrol;
            var request = new XMLHttpRequest();

            request.open("GET", ajaxUrl, true);
            request.send();

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    // Inyectamos el HTML que debe venir con data-labels desde PHP
                    document.querySelector('#contentAjax').innerHTML = request.responseText;
                    $('.modalPermisos').modal('show');

                    document.querySelector('#formPermisos')
                            .addEventListener('submit', fntSavePermisos, false);
                }
            };
        };
    });
}


function fntSavePermisos(event) {
    event.preventDefault();

    var request = new XMLHttpRequest();
    var ajaxUrl = base_url + '/Permisos/setPermisos';
    var formElement = document.querySelector("#formPermisos");
    var formData = new FormData(formElement);

    request.open("POST", ajaxUrl, true);
    request.send(formData);

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);

            if (objData.status) {
                // ✅ CERRAMOS EL MODAL AUTOMÁTICAMENTE
                $('.modalPermisos').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Permisos guardados',
                    text: objData.msg,
                    confirmButtonColor: '#28a745',
                    timer: 2000 // Opcional: se cierra la alerta sola en 2 seg
                });
            } else {
                Swal.fire("Error", objData.msg, "error");
            }
        }
    }
}
