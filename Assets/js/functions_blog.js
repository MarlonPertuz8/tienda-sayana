let tableBlog;
let rowTable = "";
let divLoading = document.querySelector("#divLoading");
let inputImage = 0; // Contador para IDs únicos de la galería

document.addEventListener('DOMContentLoaded', function () {

    // 1. Inicializar DataTable
    tableBlog = $('#tableBlog').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": base_url + "/Blog/getPosts",
            "dataSrc": ""
        },
        "columns": [
            { "data": "idpost" },
            { "data": "titulo" },
            { "data": "fecha" },
            { "data": "status" },
            { "data": "options" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // 2. Inicializar Editor de Texto (TinyMCE)
    if (document.querySelector("#txtContenido")) {
        tinymce.init({
            selector: '#txtContenido',
            width: "100%",
            height: 400,
            statustab: true,
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                "table directionality emoticons paste code"
            ],
            toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
            toolbar2: "| link unlink anchor | image media | forecolor backcolor | print preview code ",
            image_advtab: true,
            relative_urls: false,
            remove_script_host: false,
            // language: 'es'  <-- COMENTA O BORRA ESTA LÍNEA
        });
    }

    // 3. Manejo de la Foto de Portada Principal
    if (document.querySelector("#foto")) {
        let foto = document.querySelector("#foto");
        foto.onchange = function (e) {
            let uploadFoto = document.querySelector("#foto").value;
            let fileimg = document.querySelector("#foto").files;
            let nav = window.URL || window.webkitURL;
            let contactAlert = document.querySelector('#form_alert');
            if (uploadFoto != '') {
                let type = fileimg[0].type;
                if (type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png') {
                    contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';
                    if (document.querySelector('#imgNav')) {
                        document.querySelector('#imgNav').src = base_url + '/Assets/images/uploads/portada_categoria.png';
                    }
                    document.querySelector('#foto').value = "";
                    return false;
                } else {
                    contactAlert.innerHTML = '';
                    if (document.querySelector('#imgNav')) {
                        let objeto_url = nav.createObjectURL(this.files[0]);
                        document.querySelector('#imgNav').src = objeto_url;
                        document.querySelector('.delPhoto').classList.remove("notBlock");
                    }
                }
            }
        }
    }

    if (document.querySelector(".delPhoto")) {
        let delPhoto = document.querySelector(".delPhoto");
        delPhoto.onclick = function (e) {
            document.querySelector("#foto_remove").value = 1;
            removePhoto();
        }
    }

    // 4. Envío del Formulario
    let formPost = document.querySelector("#formPost");
    formPost.onsubmit = function (e) {
        e.preventDefault();

        // IMPORTANTE: Sincronizar TinyMCE con el textarea antes de validar
        if (tinyMCE.activeEditor) tinyMCE.triggerSave();

        let strTitulo = document.querySelector('#txtTitulo').value;
        let strContenido = document.querySelector('#txtContenido').value;
        let intStatus = document.querySelector('#listStatus').value;

        if (strTitulo == '' || strContenido == '' || intStatus == '') {
            Swal.fire("Atención", "El título y contenido son obligatorios.", "error");
            return false;
        }

        if (divLoading) divLoading.style.display = "flex";
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + '/Blog/setPost';
        let formData = new FormData(formPost);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);
                if (objData.status) {
                    $('#modalFormPost').modal("hide");
                    formPost.reset();
                    Swal.fire("Blog", objData.msg, "success");
                    tableBlog.api().ajax.reload();
                    // Limpiar galería después de guardar
                    document.querySelector("#containerImages").innerHTML = "";
                } else {
                    Swal.fire("Error", objData.msg, "error");
                }
            }
            if (divLoading) divLoading.style.display = "none";
        }
    }
}, false);

// --- FUNCIONES DE GALERÍA DE IMÁGENES (Reutilizadas de Productos) ---

function fntAddImage() {
    inputImage++;
    let container = document.querySelector("#containerImages");
    let div = document.createElement("div");
    div.id = "div" + inputImage;
    div.className = "position-relative";
    div.innerHTML = `
        <div class="prevImage border dashed d-flex align-items-center justify-content-center" style="width:110px; height:110px; border: 2px dashed #ccc; border-radius: 10px;">
            <button class="btnDeleteImage btn btn-danger btn-sm position-absolute" type="button" onclick="fntDelImg('div${inputImage}')" style="top: -5px; right: -5px; border-radius: 50%; padding: 2px 6px; z-index:10;">
                <i class="fas fa-trash-alt"></i>
            </button>
            <label for="img${inputImage}" class="mb-0" style="cursor:pointer;">
                <i class="fas fa-cloud-upload-alt fa-2x text-primary"></i>
            </label>
            <input type="file" name="foto_galeria[]" id="img${inputImage}" class="d-none" onchange="fntShowImg(this)">
        </div>
    `;
    container.appendChild(div);
}

function fntDelImg(id) {
    let element = document.querySelector("#" + id);
    element.remove();
}

function fntShowImg(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        let parent = input.parentElement;
        reader.onload = function (e) {
            parent.innerHTML = `
                <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
                <button class="btnDeleteImage btn btn-danger btn-sm position-absolute" type="button" onclick="fntDelImg('${input.closest('div').id}')" style="top: -5px; right: -5px; border-radius: 50%; padding: 2px 6px; z-index:10;">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// --- FUNCIONES DE MODAL Y CONTROLADOR ---

function openModal() {
    rowTable = "";
    document.querySelector('#idPost').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Artículo";
    document.querySelector("#formPost").reset();
    if (tinyMCE.activeEditor) tinyMCE.activeEditor.setContent(""); // Limpiar editor
    document.querySelector('#imgNav').src = base_url + '/Assets/images/uploads/portada_categoria.png';
    document.querySelector('.delPhoto').classList.add("notBlock");
    document.querySelector("#containerImages").innerHTML = ""; // Limpiar galería
    $('#modalFormPost').modal('show');
}

function fntEditPost(idpost) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Artículo";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Blog/getPost/' + idpost;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if (objData.status) {
                document.querySelector("#idPost").value = objData.data.idpost;
                document.querySelector("#txtTitulo").value = objData.data.titulo;
                // Cargar contenido en TinyMCE
                tinymce.activeEditor.setContent(objData.data.contenido);
                document.querySelector("#listStatus").value = objData.data.status;
                document.querySelector('#foto_actual').value = objData.data.portada;
                document.querySelector("#foto_remove").value = 0;

                if (objData.data.portada != 'portada_categoria.png') {
                    document.querySelector('.delPhoto').classList.remove("notBlock");
                }
                document.querySelector('#imgNav').src = objData.data.url_portada;

                $('#modalFormPost').modal('show');
            }
        }
    }
}

function fntDelPost(idpost) {
    Swal.fire({
        title: "Eliminar Artículo",
        text: "¿Realmente quiere eliminar el artículo?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#009688",
        confirmButtonText: "Sí, eliminar!"
    }).then((result) => {
        if (result.isConfirmed) {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Blog/delPost';
            let strData = "idPost=" + idpost;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        Swal.fire("Eliminado!", objData.msg, "success");
                        tableBlog.api().ajax.reload();
                    }
                }
            }
        }
    });
}

function removePhoto() {
    document.querySelector('#foto').value = "";
    document.querySelector('.delPhoto').classList.add("notBlock");
    if (document.querySelector('#imgNav')) {
        document.querySelector('#imgNav').src = base_url + '/Assets/images/uploads/portada_categoria.png';
    }
}

function fntViewPost(idpost) {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Blog/getPost/' + idpost;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if (objData.status) {
                // Aquí puedes llenar un modal de lectura si lo tienes creado
                // Por ahora, un log para confirmar que llegan los datos:
                Swal.fire(objData.data.titulo, "Contenido cargado correctamente.", "info");
            }
        }
    }
}