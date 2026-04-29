let tableClientes;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){
    let formCliente = document.querySelector("#formCliente");

    // 1. Inicialización de DataTables con Idioma Local
    tableClientes = $('#tableClientes').DataTable({
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
        "ajax": { "url": base_url + "/Clientes/getClientes", "dataSrc": "" },
        "columns": [
            { "data": "idpersona" },
            { "data": "identificacion" },
            { "data": "nombre" },
            { "data": "apellido" },
            { "data": "email_user" },
            { "data": "telefono" },
            { "data": "status" },
            { "data": "options" }
        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copyHtml5', text: '<i class="far fa-copy"></i> Copiar', className: 'btn-export btn-copy' },
            { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn-export btn-excel' },
            { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn-export btn-pdf' },
            { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn-export btn-csv' }
        ],
        "responsive": true, "autoWidth": false, "bDestroy": true, "iDisplayLength": 10, "order": [[0, "desc"]]
    });

    // --- SALTO AUTOMÁTICO DE PESTAÑA INTELIGENTE ---
    const camposObligatoriosPersonales = ['txtNombre', 'txtApellido', 'txtEmail', 'txtTelefono'];
    
    const saltarAFacturacion = () => {
        $('#clienteTab a[href="#tributario"]').tab('show');
        setTimeout(() => { document.getElementById('txtNit').focus(); }, 400);
    };

    document.getElementById('txtPassword').addEventListener('blur', function() {
        if(this.value.trim() !== "") saltarAFacturacion();
    });

    document.getElementById('txtTelefono').addEventListener('blur', function() {
        let idUsuario = document.querySelector('#idUsuario').value;
        let todosLlenos = camposObligatoriosPersonales.every(id => document.getElementById(id).value.trim() !== "");

        if(todosLlenos) {
            if(idUsuario !== "") {
                saltarAFacturacion();
            } else {
                setTimeout(() => {
                    if(document.activeElement.id !== 'txtPassword' && document.getElementById('txtPassword').value === ""){
                        saltarAFacturacion();
                    }
                }, 100);
            }
        }
    });

    // 2. Envío del formulario por AJAX
    if(formCliente)
            {formCliente.onsubmit = function(e) {
            e.preventDefault();
            
            // 1. Captura de valores y elementos
            let idUsuario   = document.querySelector('#idUsuario').value;
            let strNit      = document.querySelector('#txtNit').value.trim();
            let strNombre   = document.querySelector('#txtNombre').value.trim();
            let strApellido = document.querySelector('#txtApellido').value.trim();
            let strEmail    = document.querySelector('#txtEmail').value.trim();
            let intTelefono = document.querySelector('#txtTelefono').value.trim();
            let strPassword = document.querySelector('#txtPassword').value;

            // 2. BLINDAJE: Validación de campos vacíos obligatorios
            if(strNit == '' || strNombre == '' || strApellido == '' || strEmail == '' || intTelefono == ''){
                Swal.fire("Atención", "Todos los campos con (*) son obligatorios.", "error");
                return false;
            }

            // 3. BLINDAJE: Validación de formato de email
            let emailFormat = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if(!emailFormat.test(strEmail)){
                Swal.fire("Atención", "La dirección de correo electrónico no es válida.", "error");
                return false;
            }

            // 4. BLINDAJE: Validación de teléfono (mínimo 7 dígitos, solo números)
            if(intTelefono.length < 7 || isNaN(intTelefono)){
                Swal.fire("Atención", "El número de teléfono debe tener al menos 7 dígitos numéricos.", "error");
                return false;
            }

            // 5. BLINDAJE: Contraseña obligatoria solo para NUEVOS
            if(idUsuario == "" && strPassword.trim() == ""){
                $(`#clienteTab a[href="#personal"]`).tab('show');
                document.getElementById('txtPassword').classList.add('is-invalid');
                document.getElementById('txtPassword').focus();
                Swal.fire("Atención", "La contraseña es obligatoria para nuevos clientes.", "error");
                return false;
            }

            // --- Si pasa las validaciones, procedemos al envío ---

            // Mostrar Loading para evitar doble envío
            divLoading.style.display = "flex";

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Clientes/setCliente'; 
            let formData = new FormData(formCliente);
            
            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    try {
                        let objData = JSON.parse(request.responseText);
                        if(objData.status){
                            $('#modalFormCliente').modal("hide");
                            formCliente.reset();
                            Swal.fire("Clientes", objData.msg, "success");
                            tableClientes.ajax.reload(); 
                        } else {
                            Swal.fire("Error", objData.msg, "error");
                        }
                    } catch (error) {
                        console.error("Error parseando JSON:", request.responseText);
                        Swal.fire("Error", "Ocurrió un error inesperado en el servidor.", "error");
                    }
                }
                // Ocultar Loading al terminar
                divLoading.style.display = "none";
                return false;
            }
        }
    }
});

// --- FUNCIONES DE ACCIÓN ---

function fntViewCliente(idpersona){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Clientes/getCliente/'+idpersona;
    
    request.open("GET",ajaxUrl,true);
    request.send();
    
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                let estadoCliente = objData.data.status == 1 
                    ? '<span class="badge badge-success">Activo</span>' 
                    : '<span class="badge badge-danger">Inactivo</span>';

                document.querySelector("#celIdentificacion").innerHTML = objData.data.identificacion;
                document.querySelector("#celNombre").innerHTML = objData.data.nombre;
                document.querySelector("#celApellido").innerHTML = objData.data.apellido;
                document.querySelector("#celTelefono").innerHTML = objData.data.telefono;
                document.querySelector("#celEmail").innerHTML = objData.data.email_user;
                document.querySelector("#celNit").innerHTML = objData.data.nit;
                document.querySelector("#celNomFiscal").innerHTML = objData.data.nombrefiscal;
                document.querySelector("#celDirFiscal").innerHTML = objData.data.direccionfiscal;
                document.querySelector("#celEstado").innerHTML = estadoCliente;
                document.querySelector("#celFechaRegistro").innerHTML = objData.data.fechaRegistro;
                
                $('#modalViewCliente').modal('show');
            } else {
                Swal.fire("Error", objData.msg, "error");
            }
        }
    }
}

function fntEditCliente(idpersona){
    document.querySelector('#titleModal').innerHTML ="Actualizar Cliente";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnText').innerHTML ="Actualizar";

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Clientes/getCliente/'+idpersona;
    request.open("GET",ajaxUrl,true);
    request.send();

    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                document.querySelector("#idUsuario").value = objData.data.idpersona;
                document.querySelector("#txtNit").value = objData.data.identificacion;
                document.querySelector("#txtNombre").value = objData.data.nombre;
                document.querySelector("#txtApellido").value = objData.data.apellido;
                document.querySelector("#txtTelefono").value = objData.data.telefono;
                document.querySelector("#txtEmail").value = objData.data.email_user;
                document.querySelector("#txtNombreFiscal").value = objData.data.nombrefiscal;
                document.querySelector("#txtDirFiscal").value = objData.data.direccionfiscal;
                document.querySelector("#listStatus").value = objData.data.status;
                $('#listStatus').selectpicker('render');
                $('#modalFormCliente').modal('show');
            }
        }
    }
}

function fntDelCliente(idpersona){
    Swal.fire({
        title: "Eliminar Cliente",
        text: "¿Realmente quiere eliminar al cliente?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!"
    }).then((result) => {
        if (result.isConfirmed) {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Clientes/delCliente';
            let strData = "idUsuario="+idpersona;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        Swal.fire("Eliminar!", objData.msg , "success");
                        tableClientes.ajax.reload();
                    }else{
                        Swal.fire("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });
}

function openModal() {
    document.querySelector("#idUsuario").value = "";
    document.querySelector("#formCliente").reset();
    document.querySelector("#titleModal").innerHTML = "Nuevo Cliente";
    document.querySelector(".modal-header").classList.replace("headerUpdate", "headerRegister");
    document.querySelector("#btnActionForm").classList.replace("btn-info", "btn-primary");
    document.querySelector("#btnText").innerHTML = "Guardar Cliente";
    $('#clienteTab a[href="#personal"]').tab('show');
    $(".form-control").removeClass("is-invalid");
    $('#modalFormCliente').modal('show');
}