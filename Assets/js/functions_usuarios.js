let tableUsuarios;

let divLoading = document.querySelector("#divLoading");
document.addEventListener('DOMContentLoaded', function () {

    // --- DataTable Usuarios ---
    tableUsuarios = $('#tableUsuarios').DataTable({
        processing: true,
        serverSide: false,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        ajax: {
            url: base_url + "/Usuarios/getUsuarios",
            dataSrc: ""
        },
        columns: [
            { data: "idpersona" },
            { data: "nombre" },
            { data: "apellido" },
            { data: "email_user" },
            { data: "telefono" },
            { data: "nombrerol" },
            { data: "status" },
            { data: "options" }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="far fa-copy"></i> Copiar',
                titleAttr: 'Copiar',
                className: 'btn-export btn-copy'
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                titleAttr: 'Exportar a Excel',
                className: 'btn-export btn-excel'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                titleAttr: 'Exportar a PDF',
                className: 'btn-export btn-pdf'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                titleAttr: 'Exportar a CSV',
                className: 'btn-export btn-csv'
            }
        ],
        responsive: true,
        bDestroy: true,
        iDisplayLength: 10,
        order: [[0, "desc"]]
    });

    // --- Submit Formulario Usuario ---
   if (document.querySelector("#formUsuario")) {
    const formUsuario = document.querySelector("#formUsuario");
    
    formUsuario.addEventListener('submit', function (e) {
        e.preventDefault();

        // 1. Captura de elementos de UI
        const btnAction = document.querySelector("#btnActionForm");
        const btnText = document.querySelector("#btnText");
        const divLoading = document.querySelector("#divLoading"); // Capturamos el loader

        // 2. Captura de valores
        let strIdentificacion = document.querySelector('#txtIdentificacion').value;
        let strNombre = document.querySelector('#txtNombre').value;
        let strApellido = document.querySelector('#txtApellido').value;
        let strEmail = document.querySelector('#txtEmail').value;
        let strTelefono = document.querySelector('#txtTelefono').value;
        let intRol = document.querySelector('#listRolid').value;
        let strPassword = document.querySelector('#txtPassword').value;
        let idUsuario = document.querySelector('#idUsuario').value;

        // RegEx para validaciones
        const regexText = /^[a-zA-Z ]+$/;
        const regexNumber = /^[0-9]+$/;
        const regexEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

        // 3. Validaciones de Negocio
        if (strIdentificacion == '' || strNombre == '' || strApellido == '' || strEmail == '' || strTelefono == '' || intRol == '') {
            Swal.fire("Atención", "Todos los campos son obligatorios.", "error");
            return false;
        }
        if (!regexText.test(strNombre) || !regexText.test(strApellido)) {
            Swal.fire("Atención", "Los nombres y apellidos solo deben contener letras.", "error");
            return false;
        }
        if (!regexNumber.test(strIdentificacion) || !regexNumber.test(strTelefono)) {
            Swal.fire("Atención", "Identificación y Teléfono solo deben contener números.", "error");
            return false;
        }
        if (!regexEmail.test(strEmail)) {
            Swal.fire("Atención", "El formato del correo electrónico es inválido.", "error");
            return false;
        }
        if (idUsuario == "" && strPassword.length < 5) {
            Swal.fire("Atención", "La contraseña es obligatoria para nuevos usuarios (mínimo 5 caracteres).", "error");
            return false;
        }

        // 4. Activar estado de carga (Loading Pantalla Completa)
        if (btnAction) btnAction.disabled = true;
        if (btnText) btnText.innerHTML = "Procesando...";
        
        if (divLoading) {
            divLoading.classList.add("fullscreen"); // Lo sacamos del modal a toda la pantalla
            divLoading.style.display = "flex";
        }

        const ajaxUrl = base_url + '/Usuarios/setUsuario';
        const formData = new FormData(formUsuario);
        const request = new XMLHttpRequest();

        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState === 4) {
                // 5. Desactivar Loading (Siempre)
                if (divLoading) {
                    divLoading.style.display = "none";
                    divLoading.classList.remove("fullscreen");
                }

                if (btnAction) btnAction.disabled = false;
                if (btnText) btnText.innerHTML = (idUsuario == "") ? "Guardar" : "Actualizar";

                if (request.status === 200) {
                    try {
                        const objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            $('#modalFormUsuario').modal('hide');
                            formUsuario.reset();
                            Swal.fire('Usuario', objData.msg, 'success');
                            
                            // Recargar el DataTable si existe
                            if (typeof tableUsuarios !== 'undefined') {
                                tableUsuarios.ajax.reload(null, false);
                            }
                        } else {
                            Swal.fire('Error', objData.msg, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', "Respuesta del servidor corrupta.", 'error');
                    }
                } else {
                    Swal.fire('Error', "No se pudo conectar con el servidor.", 'error');
                }
            }
        };
    });
}

    // --- Submit Formulario Perfil ---
    if (document.querySelector("#formPerfil")) {
    const formPerfil = document.querySelector("#formPerfil");
    
    formPerfil.addEventListener('submit', function (e) {
        e.preventDefault();

        // 1. Captura de valores
        let strIdentificacion = document.querySelector('#txtIdentificacion').value;
        let strNombre = document.querySelector('#txtNombre').value;
        let strApellido = document.querySelector('#txtApellido').value;
        let strTelefono = document.querySelector('#txtTelefono').value;
        let strPassword = document.querySelector('#txtPassword').value;
        let strPasswordConfirm = document.querySelector('#txtPasswordConfirm').value;

        // Elementos de la interfaz
        const btnAction = document.querySelector("#btnActionForm");
        const btnText = document.querySelector("#btnText");
        const divLoading = document.querySelector("#divLoading"); // Asegúrate que esté definido

        // 2. Validaciones
        if (strIdentificacion == '' || strNombre == '' || strApellido == '' || strTelefono == '') {
            Swal.fire("Atención", "Todos los campos con asterisco son obligatorios.", "error");
            return false;
        }

        if (strPassword != "" || strPasswordConfirm != "") {
            if (strPassword !== strPasswordConfirm) {
                Swal.fire("Atención", "Las contraseñas no coinciden.", "error");
                return false;
            }
            if (strPassword.length < 5) {
                Swal.fire("Atención", "La contraseña debe tener al menos 5 caracteres.", "info");
                return false;
            }
        }

        // 3. Activar estado de carga
        if(btnAction) btnAction.disabled = true;
        if(btnText) btnText.innerHTML = "Actualizando...";
        
        if(divLoading) {
            divLoading.classList.add("fullscreen"); // Agregamos la clase para pantalla completa
            divLoading.style.display = "flex";
        }

        const ajaxUrl = base_url + '/Usuarios/putPerfil';
        const formData = new FormData(formPerfil);
        const request = new XMLHttpRequest();

        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState === 4) {
                // 4. Desactivar estado de carga (Siempre al terminar)
                if(divLoading) {
                    divLoading.style.display = "none";
                    divLoading.classList.remove("fullscreen");
                }
                
                if(btnAction) btnAction.disabled = false;
                if(btnText) btnText.innerHTML = "Actualizar";

                if (request.status === 200) {
                    try {
                        const objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            $('#modalFormPerfil').modal('hide');
                            Swal.fire({
                                title: 'Perfil',
                                text: objData.msg,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) location.reload();
                            });
                        } else {
                            Swal.fire('Error', objData.msg, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', "Respuesta inválida del servidor.", 'error');
                    }
                } else {
                    Swal.fire('Error', "Hubo un problema con la petición.", 'error');
                }
            }
        };
    });
}

    // --- Previsualización de Avatar ---
    const fileAvatar = document.getElementById("fileAvatar");
    if (fileAvatar) {
        fileAvatar.addEventListener("change", function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById("avatarPreview");
            if (file && preview) {
                const reader = new FileReader();
                reader.onload = (e) => preview.src = e.target.result;
                reader.readAsDataURL(file);
            }
        });
    }

    // --- Envío de Datos Fiscales ---
   const formFiscal = document.getElementById("formDataFiscal");

if (formFiscal) {
    formFiscal.addEventListener("submit", function(e) {
        e.preventDefault();

        // 1. Obtener los valores de los campos
        const strNit = document.querySelector("#txtNit").value.trim();
        const strNombre = document.querySelector("#txtNombreFiscal").value.trim();
        const strDir = document.querySelector("#txtDirFiscal").value.trim();
        
        // Elementos de la interfaz
        const divLoading = document.querySelector("#divLoading");
        const btn = this.querySelector("button[type='submit']");
        const originalText = btn.innerHTML;

        // 2. Validaciones de campos vacíos
        if (strNit == "" || strNombre == "" || strDir == "") {
            showToast("Todos los campos son obligatorios", "error");
            return false;
        }

        // 3. Validación de longitud mínima
        if (strNit.length < 5) {
            showToast("El NIT o Cédula debe tener al menos 5 dígitos", "error");
            return false;
        }

        // --- ACTIVAR LOADING Y BLOQUEAR BOTÓN ---
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        if (divLoading) {
            divLoading.classList.add("fullscreen"); // Asegura que cubra toda la ventana
            divLoading.style.display = "flex";
        }
        // ----------------------------------------

        const formData = new FormData(this);
        
        fetch(base_url + "/Usuarios/updateFiscal", {
            method: "POST",
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error('Error en la respuesta del servidor');
            return res.json();
        })
        .then(data => {
            if (data.status) {
                // Si usas SweetAlert2 (Toast) o tu función showToast
                showToast(data.msg || "Información tributaria actualizada", "success");
            } else {
                showToast(data.msg || "Error al actualizar", "error");
            }
        })
        .catch(err => {
            console.error(err);
            showToast("Error de conexión con el servidor", "error");
        })
        .finally(() => {
            // --- DESACTIVAR LOADING Y RESTAURAR BOTÓN ---
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            if (divLoading) {
                divLoading.style.display = "none";
                divLoading.classList.remove("fullscreen"); // Importante: limpiar la clase
            }
            // -------------------------------------------
        });
    });
}

}, false);

// ==========================================
// 2. LLAMADAS AUTOMÁTICAS (WINDOW LOAD)
// ==========================================
window.addEventListener('load', function () {
    fntRolesUsuario();
    fntViewUsuario();
    fntEditUsuario();
    fntDelUsuario();
}, false);

// ==========================================
// 3. FUNCIONES DE APOYO Y EVENTOS DE TABLA
// ==========================================

function fntRolesUsuario() {
    if(document.querySelector('#listRolid')){
        const ajaxUrl = base_url + '/Roles/getSelectRoles';
        const request = new XMLHttpRequest();
        request.open("GET", ajaxUrl, true);
        request.send();
        request.onreadystatechange = function () {
            if (request.readyState === 4 && request.status === 200) {
                document.querySelector('#listRolid').innerHTML = request.responseText;
                $('#listRolid').selectpicker('render');
            }
        }
    }
}

function fntViewUsuario() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btnViewUsuario");
        if (btn) {
            const idpersona = btn.getAttribute("rl");
            const request = new XMLHttpRequest();
            const ajaxUrl = base_url + '/Usuarios/getUsuario/' + idpersona;
            request.open("GET", ajaxUrl, true);
            request.send();
            request.onreadystatechange = function () {
                if (request.readyState === 4 && request.status === 200) {
                    const objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        const estadoUsuario = objData.data.status == 1
                            ? '<span class="badge badge-success">Activo</span>'
                            : '<span class="badge badge-danger">Inactivo</span>';

                        document.querySelector("#celIdentificacion").innerHTML = objData.data.identificacion;
                        document.querySelector("#celNombre").innerHTML = objData.data.nombre;
                        document.querySelector("#celApellido").innerHTML = objData.data.apellido;
                        document.querySelector("#celTelefono").innerHTML = objData.data.telefono;
                        document.querySelector("#celEmail").innerHTML = objData.data.email_user;
                        document.querySelector("#celTipoUsuario").innerHTML = objData.data.nombrerol;
                        document.querySelector("#celEstado").innerHTML = estadoUsuario;
                        document.querySelector("#celFechaRegistro").innerHTML = objData.data.fecharegistro;
                        $('#modalViewUser').modal('show');
                    }
                }
            };
        }
    });
}

function fntEditUsuario() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btnEditUsuario");
        if (btn) {
            const idpersona = btn.getAttribute("rl");
            const request = new XMLHttpRequest();
            const ajaxUrl = base_url + '/Usuarios/getUsuario/' + idpersona;
            request.open("GET", ajaxUrl, true);
            request.send();
            request.onreadystatechange = function () {
                if (request.readyState === 4 && request.status === 200) {
                    const objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        document.querySelector("#idUsuario").value = objData.data.idpersona;
                        document.querySelector("#txtIdentificacion").value = objData.data.identificacion;
                        document.querySelector("#txtNombre").value = objData.data.nombre;
                        document.querySelector("#txtApellido").value = objData.data.apellido;
                        document.querySelector("#txtTelefono").value = objData.data.telefono;
                        document.querySelector("#txtEmail").value = objData.data.email_user;
                        document.querySelector("#listRolid").value = objData.data.rolid;
                        document.querySelector("#listStatus").value = objData.data.status;
                        
                        // Refrescar Selects (Importante)
                        $('#listRolid').selectpicker('refresh');
                        $('#listStatus').selectpicker('refresh');

                        document.querySelector("#titleModal").innerHTML = "Actualizar Usuario";
                        document.querySelector(".modal-header").classList.replace("headerRegister", "headerUpdate");
                        document.querySelector("#btnText").innerHTML = "Actualizar";
                        
                        // Desbloqueo manual por si acaso
                        const btnAction = document.querySelector("#btnActionForm");
                        if(btnAction){ btnAction.disabled = false; btnAction.style.opacity = "1"; }

                        $('#modalFormUsuario').modal('show');
                    }
                }
            };
        }
    });
}

function fntDelUsuario() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btnDelUsuario");
        if (btn) {
            const idpersona = btn.getAttribute("rl");
            Swal.fire({
                title: '¿Eliminar Usuario?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    let request = new XMLHttpRequest();
                    let ajaxUrl = base_url + '/Usuarios/delUsuario/';
                    let strData = "idUsuario=" + idpersona;
                    request.open("POST", ajaxUrl, true);
                    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    request.send(strData);
                    request.onreadystatechange = function () {
                        if (request.readyState == 4 && request.status == 200) {
                            let objData = JSON.parse(request.responseText);
                            if (objData.status) {
                                Swal.fire({ icon: 'success', title: 'Eliminado', text: objData.msg, showConfirmButton: false, timer: 1500 });
                                tableUsuarios.ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', objData.msg, 'error');
                            }
                        }
                    }
                }
            });
        }
    });
}

// ==========================================
// 4. MODALES Y UTILIDADES
// ==========================================

function openModalUsuario() {
    document.querySelector("#formUsuario").reset();
    document.querySelector("#idUsuario").value = "";
    document.querySelector("#titleModal").innerHTML = "Nuevo Usuario";
    document.querySelector(".modal-header").classList.replace("headerUpdate", "headerRegister");
    document.querySelector("#btnActionForm").classList.replace("btn-info", "btn-primary");
    document.querySelector("#btnText").innerHTML = "Guardar";
    
    // Asegurar que el botón esté activo al abrir para uno nuevo
    const btnAction = document.querySelector("#btnActionForm");
    if(btnAction){ btnAction.disabled = false; btnAction.style.opacity = "1"; }

    $('#modalFormUsuario').modal('show');
}

function openModalPerfil() {
    $('#modalFormPerfil').modal('show');
}

function showToast(msg, type = "success") {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: type, // 'success', 'error', 'warning', 'info'
        title: msg
    });
}