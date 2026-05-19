let tableSlider;
let divLoading = document.querySelector("#divLoading");

document.addEventListener('DOMContentLoaded', function(){

    // 1. DataTable
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
    { 
        "data": null, // Cambiamos a null para poder acceder a todas las columnas de la fila
        "render": function(data, type, row) {
            let archivo = "";
            let esVideo = false;

            // ✅ Verificamos si el tipo es video según tu base de datos
            if(row.tipo === "video"){
                archivo = row.video; // Usamos la columna 'video'
                esVideo = true;
            }else{
                archivo = row.portada; // Usamos la columna 'portada'
            }

            if(!archivo || archivo == "" || archivo == "1"){
                return '<div style="height:50px; width:70px; background:#eee;" class="rounded border"></div>';
            }

            let ruta = base_url + '/Assets/images/uploads/' + archivo;
            
            if (esVideo) {
                return `
                    <div style="height:50px; width:70px;" class="rounded border overflow-hidden">
                        <video src="${ruta}" muted autoplay loop playsinline style="height:50px; width:70px; object-fit:cover; background:#000;"></video>
                    </div>`;
            } 
            
            return `
                <div style="height:50px; width:70px;" class="rounded border overflow-hidden">
                    <img src="${ruta}" style="height:50px; width:70px; object-fit:cover;">
                </div>`;
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

    // 🔥 CAMBIO DINÁMICO IMAGEN / VIDEO (MISMO BOTÓN)
    let selectTipo = document.querySelector('#listTipo');
    let inputFile = document.querySelector('#foto');
    let labelBtn = document.querySelector('label[for="foto"]');

    if(selectTipo && inputFile && labelBtn){
        selectTipo.addEventListener('change', function(){

            if(this.value === "video"){
                inputFile.accept = "video/mp4,video/webm";
                labelBtn.innerHTML = '<i class="fas fa-video"></i> Seleccionar Video';
            }else{
                inputFile.accept = "image/*";
                labelBtn.innerHTML = '<i class="fas fa-image"></i> Seleccionar Imagen';
            }

            inputFile.value = "";
            removePhoto();
        });
    }

    // 2. Submit
    let formSlider = document.querySelector("#formSlider");
    if(formSlider){
        formSlider.onsubmit = function(e){
            e.preventDefault();

            let strNombre = document.querySelector('#txtNombre').value.trim();
            let strLink = document.querySelector('#txtLink').value.trim();
            let intStatus = document.querySelector('#listStatus').value;
            let inputFile = document.querySelector("#foto");
            let idSlider = document.querySelector("#idSlider").value;
            let tipo = document.querySelector('#listTipo') ? document.querySelector('#listTipo').value : "imagen";

            if(strNombre == '' || strLink == '' || intStatus == ''){
                Swal.fire("Atención", "Nombre y link son obligatorios", "error");
                return false;
            }

            // ✅ VALIDACIÓN CORRECTA
            if(tipo === "imagen" && idSlider == "" && inputFile.files.length === 0){
                Swal.fire("Error", "Debes subir una imagen", "error");
                return false;
            }

            if(tipo === "video" && idSlider == "" && inputFile.files.length === 0){
                Swal.fire("Error", "Debes subir un video", "error");
                return false;
            }

            divLoading.style.display = "flex";

            let request = new XMLHttpRequest();
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
                        }else{
                            Swal.fire("Error", objData.msg, "error");
                        }
                    } catch (error){
                        Swal.fire("Error", "Error en servidor", "error");
                    }
                }
                divLoading.style.display = "none";
            }
        }
    }

    // 3. Preview IMAGEN + VIDEO
    if(document.querySelector("#foto")){
        let foto = document.querySelector("#foto");
        foto.onchange = function(){

            let file = this.files[0];
            let container = document.querySelector('.prevPhoto div');
            let contactAlert = document.querySelector('#form_alert');

            if(contactAlert) contactAlert.innerHTML = '';

            if(file){

                let type = file.type;

                // 📸 IMAGEN
                if(type.startsWith("image/")){
                    let reader = new FileReader();
                    reader.onload = function(e){
                        container.innerHTML =
                            "<img src='"+e.target.result+"' style='width:100%;height:100%;object-fit:cover;'>";
                    };
                    reader.readAsDataURL(file);
                }

                // 🎥 VIDEO
                else if(type.startsWith("video/")){
                    let url = URL.createObjectURL(file);
                    container.innerHTML = `
                        <video controls style="width:100%;height:100%;object-fit:cover;">
                            <source src="${url}" type="${type}">
                        </video>`;
                }

                // ❌ ERROR
                else{
                    if(contactAlert){
                        contactAlert.innerHTML = '<p class="errorArchivo">Formato inválido</p>';
                    }
                    removePhoto();
                    return;
                }

                document.querySelector('.delPhoto').classList.remove("notBlock");
            }
        }
    }

    // eliminar archivo
    if(document.querySelector(".delPhoto")){
        document.querySelector(".delPhoto").onclick = function(){
            if(document.querySelector("#foto_remove")){
                document.querySelector("#foto_remove").value = 1;
            }
            removePhoto();
        }
    }

}, false);


// ================= FUNCIONES =================

function fntViewInfo(idslider){
    let request = new XMLHttpRequest();
    let ajaxUrl = base_url+'/Slider/getSlider/'+idslider;
    request.open("GET", ajaxUrl, true);
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

                // ✅ RENDER DINÁMICO (Soporta MP4 y WEBM)
                if(objData.data.tipo === "video" && objData.data.video != ""){
                    document.querySelector("#imgSliderView").innerHTML = `
                        <video controls style="width:100%; border-radius:10px; background:#000;">
                            <source src="${base_url}/Assets/images/uploads/${objData.data.video}" type="video/mp4">
                            <source src="${base_url}/Assets/images/uploads/${objData.data.video}" type="video/webm">
                            Tu navegador no soporta el tag de video.
                        </video>`;
                }else{
                    document.querySelector("#imgSliderView").innerHTML = 
                        '<img src="'+objData.data.url_portada+'" class="img-fluid rounded border">';
                }
                $('#modalViewSlider').modal('show');
            }
        }
    }
}

function fntEditInfo(element, idslider){
    document.querySelector('#titleModal').innerHTML ="Actualizar Slider";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnText').innerHTML ="Actualizar";

    let request = new XMLHttpRequest();
    let ajaxUrl = base_url+'/Slider/getSlider/'+idslider;
    request.open("GET", ajaxUrl, true);
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
                
                // ✅ IMPORTANTE: Sincronizar el video actual para el controlador
                if(document.querySelector("#video_actual")){
                    document.querySelector("#video_actual").value = objData.data.video || "";
                }

                $('#listStatus').selectpicker('render');

                // ✅ TIPO DE SLIDER
                if(document.querySelector("#listTipo")){
                    document.querySelector("#listTipo").value = objData.data.tipo;
                    $('#listTipo').selectpicker('render'); // Si usas bootstrap-select
                    document.querySelector("#listTipo").dispatchEvent(new Event('change'));
                }

                // ✅ LÓGICA DE PREVIEW EN MODAL DE EDICIÓN
                let containerPrev = document.querySelector('.prevPhoto div');
                
                if(objData.data.tipo === "video" && objData.data.video != ""){
                    // Previsualización de video existente
                    containerPrev.innerHTML = `
                        <video style="width:100%; height:100%; object-fit:cover;" muted loop autoplay>
                            <source src="${base_url}/Assets/images/uploads/${objData.data.video}">
                        </video>`;
                    document.querySelector('.delPhoto').classList.remove("notBlock");
                } 
                else if(objData.data.portada != 'slider_default.png' && objData.data.tipo === "imagen"){
                    // Previsualización de imagen existente
                    containerPrev.innerHTML = "<img src='"+objData.data.url_portada+"' style='width:100%;height:100%;object-fit:cover;'>";
                    document.querySelector('.delPhoto').classList.remove("notBlock");
                }
                else{
                    // Si no hay archivo o es el default
                    removePhoto();
                }

                $('#modalFormSlider').modal('show');
            }
        }
    }
}


function fntDelInfo(idslider){
    Swal.fire({
        title: "Eliminar Slider",
        text: "¿Realmente quiere eliminar?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Si",
        cancelButtonText: "No"
    }).then((result)=>{
        if(result.isConfirmed){

            let request = new XMLHttpRequest();
            let ajaxUrl = base_url+'/Slider/delSlider';
            let strData = "idSlider="+idslider;

            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);

            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        Swal.fire("Eliminado", objData.msg, "success");
                        tableSlider.ajax.reload();
                    }else{
                        Swal.fire("Error", objData.msg, "error");
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
    document.querySelector('.prevPhoto div').innerHTML = "";
}


function openModal(){
    document.querySelector("#idSlider").value = "";
    document.querySelector("#formSlider").reset();

    document.querySelector("#titleModal").innerHTML = "Nuevo Slider";
    document.querySelector(".modal-header").classList.replace("headerUpdate", "headerRegister");
    document.querySelector("#btnText").innerHTML = "Guardar";

    removePhoto();

    if(document.querySelector("#listTipo")){
        document.querySelector("#listTipo").value = "imagen";
    }

    let containerVideo = document.querySelector('#containerVideo');
    if(containerVideo){
        containerVideo.style.display = "none";
    }

    $('#modalFormSlider').modal('show');
}