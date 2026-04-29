let tableCategorias;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){

    // 1. Inicialización de DataTable (Estilo Clientes)
    tableCategorias = $('#tableCategorias').DataTable({
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
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        "ajax": { "url": base_url + "/Categorias/getCategorias", "dataSrc": "" },
        "columns": [
            { "data": "idcategoria" },
            // MODIFICADO: Generar HTML para la imagen
            { "data": "portada",
            "render": function(data, type, row){
                return '<img src="'+base_url+'/Assets/images/uploads/'+data+'" class="img-fluid rounded border p-1" style="max-height: 40px;">';
            }
            },
            { "data": "nombre" },
            { "data": "descripcion" },
            { "data": "status" },
            { "data": "options" }
        ],
        "dom": 'Bfrtip',
        "buttons": [
            { extend: 'copyHtml5', text: '<i class="far fa-copy"></i> Copiar', className: 'btn-export btn-copy' },
            { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn-export btn-excel' },
            { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn-export btn-pdf' },
            { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn-export btn-csv' }
        ],
        "responsive": true, 
        "autoWidth": false, 
        "bDestroy": true, 
        "iDisplayLength": 10, 
        "order": [[0, "desc"]]
    });

    // 2. Envío del formulario de Categoría (Blindado)
   let formCategoria = document.querySelector("#formCategoria");
if(formCategoria){
    formCategoria.onsubmit = function(e) {
        e.preventDefault();

        let strNombre = document.querySelector('#txtNombre').value.trim();
        let strDescripcion = document.querySelector('#txtDescripcion').value.trim();
        let intStatus = document.querySelector('#listStatus').value;
        let inputFile = document.querySelector("#foto");
        let idCategoria = document.querySelector("#idCategoria").value;

        // Validación campos
        if(strNombre == '' || strDescripcion == '' || intStatus == ''){
            Swal.fire("Atención", "Todos los campos con (*) son obligatorios.", "error");
            return false;
        }

        // 🔥 VALIDACIÓN CLAVE (EVITA TU ERROR)
        if(idCategoria == "" && inputFile.files.length === 0){
            Swal.fire("Error", "Debes seleccionar una imagen.", "error");
            return false;
        }

        divLoading.style.display = "flex";

        // DEBUG (puedes quitar después)
        console.log("Archivo enviado:", inputFile.files);

        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url+'/Categorias/setCategoria'; 
        
        let formData = new FormData(formCategoria);

        console.log("FILES:", document.querySelector("#foto").files);

        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                try {
                    let objData = JSON.parse(request.responseText);
                    
                    if(objData.status){
                        $('#modalFormCategorias').modal("hide");

                        // 🔥 ORDEN CORRECTO
                        formCategoria.reset();
                        removePhoto();

                        Swal.fire("Categorías", objData.msg, "success");
                        tableCategorias.ajax.reload(); 

                    } else {
                        Swal.fire("Error", objData.msg, "error");
                    }

                } catch (error) {
                    console.error("Error en el servidor:", request.responseText);
                    Swal.fire("Error Técnico", "El servidor devolvió una respuesta inválida. Revisa la consola (F12).", "error");
                }
            }
            divLoading.style.display = "none";
        }
    }
}

    // Manejo de la vista previa de la foto (Si aplica)
  // Manejo de Preview de Foto para Categorías (Actualizado)
    if(document.querySelector("#foto")){
        let foto = document.querySelector("#foto");
        foto.onchange = function(e) {
            let uploadFoto = document.querySelector("#foto").value;
            let fileimg = document.querySelector("#foto").files;
            let nav = window.URL || window.webkitURL;
            let contactAlert = document.querySelector('#form_alert');
            
            if(uploadFoto !=''){
                let type = fileimg[0].type;
                let name = fileimg[0].name;

                // Se agregaron 'image/webp' e 'image/avif' a la validación
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png' && 
                   type != 'image/webp' && type != 'image/avif'){
                    
                    contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido (Solo JPG, PNG, WEBP, AVIF).</p>';
                    
                    if(document.querySelector('#img')){
                        document.querySelector('#img').remove();
                    }
                    document.querySelector('.delPhoto').classList.add("notBlock");
                    foto.value="";
                    return false;
                }else{  
                    contactAlert.innerHTML='';
                    if(document.querySelector('#img')){
                        document.querySelector('#img').remove();
                    }
                    document.querySelector('.delPhoto').classList.remove("notBlock");
                    
                    // Generamos la URL temporal para la vista previa
                    let objeto_url = nav.createObjectURL(this.files[0]);
                    document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src="+objeto_url+" style='width:100%; height:100%; object-fit:cover;'>";
                }
            }else{
                // En lugar de alert, usamos la consola o limpiamos silenciosamente
                if(document.querySelector('#img')){
                    document.querySelector('#img').remove();
                }
            }
        }
    }

    // Botón para eliminar la foto en Categorías
    if(document.querySelector(".delPhoto")){
        let delPhoto = document.querySelector(".delPhoto");
        delPhoto.onclick = function(e) {
            if(document.querySelector("#foto_remove")){
                document.querySelector("#foto_remove").value = 1;
            }
            removePhoto();
        }
    }

}, false);
// --- FUNCIONES DE ACCIÓN ---

function fntViewInfo(idcategoria){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Categorias/getCategoria/'+idcategoria;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                let estado = objData.data.status == 1 ? 
                    '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>';

                document.querySelector("#celId").innerHTML = objData.data.idcategoria;
                document.querySelector("#celNombre").innerHTML = objData.data.nombre;
                document.querySelector("#celDescripcion").innerHTML = objData.data.descripcion;
                document.querySelector("#celEstado").innerHTML = estado;
                document.querySelector("#imgCategoria").innerHTML = '<img src="'+objData.data.url_portada+'" class="img-fluid">';
                $('#modalViewCategoria').modal('show');
            }else{
                Swal.fire("Error", objData.msg, "error");
            }
        }
    }
}

function fntEditInfo(element, idcategoria){
    document.querySelector('#titleModal').innerHTML ="Actualizar Categoría";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
   
    document.querySelector('#btnText').innerHTML ="Actualizar";

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Categorias/getCategoria/'+idcategoria;
    request.open("GET",ajaxUrl,true);
    request.send();

    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                document.querySelector("#idCategoria").value = objData.data.idcategoria;
                document.querySelector("#txtNombre").value = objData.data.nombre;
                document.querySelector("#txtDescripcion").value = objData.data.descripcion;
                document.querySelector('#foto_actual').value = objData.data.portada;
                document.querySelector("#foto_remove").value = 0;
                document.querySelector("#listStatus").value = objData.data.status;
                $('#listStatus').selectpicker('render');

                if(document.querySelector('#img')){
                    document.querySelector('#img').src = objData.data.url_portada;
                }else{
                    document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src="+objData.data.url_portada+">";
                }

                if(objData.data.portada == 'default.png'){
                    document.querySelector('.delPhoto').classList.add("notBlock");
                }else{
                    document.querySelector('.delPhoto').classList.remove("notBlock");
                }

                $('#modalFormCategorias').modal('show');
            }
        }
    }
}

function fntDelInfo(idcategoria){
    Swal.fire({
        title: "Eliminar Categoría",
        text: "¿Realmente quiere eliminar la categoría?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!"
    }).then((result) => {
        if (result.isConfirmed) {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Categorias/delCategoria';
            let strData = "idCategoria="+idcategoria;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        Swal.fire("Eliminar!", objData.msg , "success");
                        tableCategorias.ajax.reload();
                    }else{
                        Swal.fire("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });
}

function removePhoto(){
    let input = document.querySelector('#foto');

    // 🔥 REINICIO REAL DEL INPUT FILE (CLAVE)
    input.value = "";
    input.type = "text";
    input.type = "file";

    document.querySelector('.delPhoto').classList.add("notBlock");
    document.querySelector('#img').src = base_url + "/Assets/images/uploads/default.png";
}

function openModal() {
    document.querySelector("#idCategoria").value = "";
    document.querySelector("#formCategoria").reset();
    document.querySelector("#titleModal").innerHTML = "Nueva Categoría";
    document.querySelector(".modal-header").classList.replace("headerUpdate", "headerRegister");
    document.querySelector("#btnActionForm").classList.replace("btn-info", "btn-primary");
    document.querySelector("#btnText").innerHTML = "Guardar";
    removePhoto();
    $('#modalFormCategorias').modal('show');
}