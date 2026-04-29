let tableSlider;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){

    // 1. Inicialización de DataTable
    tableSlider = $('#tableSlider').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        "ajax": { "url": base_url + "/Slider/getSliders", "dataSrc": "" },
        "columns": [
            { "data": "idslider" },
            { "data": "portada",
              "render": function(data, type, row){
                  return '<img src="'+base_url+'/Assets/images/uploads/'+data+'" class="img-fluid rounded border p-1" style="max-height: 50px;">';
              }
            },
            { "data": "nombre" },
            { "data": "link" },
            { "data": "status" },
            { "data": "options" }
        ],
        "responsive": true, 
        "bDestroy": true, 
        "iDisplayLength": 10, 
        "order": [[0, "desc"]]
    });

    // 2. Envío del formulario
    let formSlider = document.querySelector("#formSlider");
    if(formSlider){
        formSlider.onsubmit = function(e) {
            e.preventDefault();

            let strNombre = document.querySelector('#txtNombre').value.trim();
            let strLink = document.querySelector('#txtLink').value.trim();
            let intStatus = document.querySelector('#listStatus').value;
            let inputFile = document.querySelector("#foto");
            let idSlider = document.querySelector("#idSlider").value;

            if(strNombre == '' || strLink == '' || intStatus == ''){
                Swal.fire("Atención", "El nombre y el enlace son obligatorios.", "error");
                return false;
            }

            if(idSlider == "" && inputFile.files.length === 0){
                Swal.fire("Error", "Debes seleccionar una imagen para el banner.", "error");
                return false;
            }

            divLoading.style.display = "flex";

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Slider/setSlider'; 
            let formData = new FormData(formSlider);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    try {
                        let objData = JSON.parse(request.responseText);
                        if(objData.status){
                            $('#modalFormSlider').modal("hide");
                            formSlider.reset();
                            removePhoto(); 
                            Swal.fire("Slider", objData.msg, "success");
                            tableSlider.ajax.reload(); 
                        } else {
                            Swal.fire("Error", objData.msg, "error");
                        }
                    } catch (error) {
                        Swal.fire("Error Técnico", "Error al procesar la respuesta del servidor.", "error");
                    }
                }
                divLoading.style.display = "none";
            }
        }
    }

    // 3. Manejo de Preview de Foto (Mejorado para evitar el error 404)
// Manejo de Preview de Foto (Actualizado para formatos modernos)
    if(document.querySelector("#foto")){
        let foto = document.querySelector("#foto");
        foto.onchange = function(e) {
            let uploadFoto = this.value;
            let fileimg = this.files[0]; 
            let contactAlert = document.querySelector('#form_alert');
            
            if(contactAlert) contactAlert.innerHTML = '';

            if(uploadFoto != ''){
                let type = fileimg.type;
                // Se agregaron 'image/webp' e 'image/avif' a la validación
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png' && 
                   type != 'image/webp' && type != 'image/avif'){
                    
                    if(contactAlert){
                        contactAlert.innerHTML = '<p class="errorArchivo">Formato no válido (Solo JPG, PNG, WEBP, AVIF).</p>';
                    }
                    removePhoto();
                    return false;
                } else {  
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        // Inyectamos el HTML de la imagen dinámicamente con estilos para que luzca bien
                        document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src='"+event.target.result+"' style='width:100%; height:100%; object-fit:cover;'>";
                        document.querySelector('.delPhoto').classList.remove("notBlock");
                    };
                    reader.readAsDataURL(fileimg);
                }
            }
        }
    }

    // Botón para eliminar la foto seleccionada
    if(document.querySelector(".delPhoto")){
        let delPhoto = document.querySelector(".delPhoto");
        delPhoto.onclick = function(e) {
            // Marcamos para el servidor que la foto debe ser removida
            if(document.querySelector("#foto_remove")){
                document.querySelector("#foto_remove").value = 1;
            }
            removePhoto();
        }
    }

}, false);

// --- FUNCIONES DE ACCIÓN ---

function fntViewInfo(idslider){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Slider/getSlider/'+idslider;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                let estado = objData.data.status == 1 ? 
                    '<span class="badge badge-success">Activo</span>' : 
                    '<span class="badge badge-danger">Inactivo</span>';

                document.querySelector("#celId").innerHTML = objData.data.idslider;
                document.querySelector("#celNombre").innerHTML = objData.data.nombre;
                document.querySelector("#celDescripcion").innerHTML = objData.data.descripcion;
                document.querySelector("#celLink").innerHTML = objData.data.link;
                document.querySelector("#celEstado").innerHTML = estado;
                
                // IMPORTANTE: url_portada ya debe venir procesada desde el controlador 
                // apuntando a Assets/images/uploads/
                document.querySelector("#imgSlider").innerHTML = '<img src="'+objData.data.url_portada+'" class="img-fluid rounded border">';
                
                $('#modalViewSlider').modal('show');
            } else {
                Swal.fire("Error", objData.msg, "error");
            }
        }
    }
}

function fntEditInfo(element, idslider){
    document.querySelector('#titleModal').innerHTML ="Actualizar Slider";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnText').innerHTML ="Actualizar";


    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Slider/getSlider/'+idslider;
    request.open("GET",ajaxUrl,true);
    request.send();

    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                document.querySelector("#idSlider").value = objData.data.idslider;
                document.querySelector("#txtNombre").value = objData.data.nombre;
                document.querySelector("#txtDescripcion").value = objData.data.descripcion;
                document.querySelector("#txtLink").value = objData.data.link;
                document.querySelector('#foto_actual').value = objData.data.portada;
                document.querySelector("#foto_remove").value = 0;
                document.querySelector("#listStatus").value = objData.data.status;
                $('#listStatus').selectpicker('render');

                // Previsualización de la imagen actual en el formulario
                if(objData.data.portada != 'slider_default.png'){
                    document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src='"+objData.data.url_portada+"' style='width:100%; height:100%; object-fit:cover;'>";
                    document.querySelector('.delPhoto').classList.remove("notBlock");
                } else {
                    // Si es la imagen por defecto, limpiamos el div o ponemos placeholder
                    document.querySelector('.prevPhoto div').innerHTML = ""; 
                    document.querySelector('.delPhoto').classList.add("notBlock");
                }

                $('#modalFormSlider').modal('show');
            } else {
                Swal.fire("Error", objData.msg, "error");
            }
        }
    }
}
function fntDelInfo(idslider){
    Swal.fire({
        title: "Eliminar Slider",
        text: "¿Realmente quiere eliminar este banner?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!"
    }).then((result) => {
        if (result.isConfirmed) {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Slider/delSlider';
            let strData = "idSlider="+idslider;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        Swal.fire("Eliminado!", objData.msg , "success");
                        tableSlider.ajax.reload();
                    }else{
                        Swal.fire("Error!", objData.msg , "error");
                    }
                }
            }
        }
    });
}

function removePhoto(){
    let input = document.querySelector('#foto');
    if(input) input.value = "";
    document.querySelector('.delPhoto').classList.add("notBlock");
    // Limpiamos el contenedor para evitar el icono roto
    document.querySelector('.prevPhoto div').innerHTML = ""; 
}

function openModal() {
    document.querySelector("#idSlider").value = "";
    document.querySelector("#formSlider").reset();
    document.querySelector("#titleModal").innerHTML = "Nuevo Slider";
    document.querySelector(".modal-header").classList.replace("headerUpdate", "headerRegister");
    document.querySelector("#btnText").innerHTML = "Guardar";
    removePhoto();
    $('#modalFormSlider').modal('show');
}