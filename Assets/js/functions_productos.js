document.write(`<script src="${base_url}/Assets/js/plugins/JsBarcode.all.min.js"></script>`);
let tableProductos;
let rowTable = "";
let divLoading = document.querySelector("#divLoading");
let listColores = [];

// Solución para el foco de TinyMCE en modales de Bootstrap
$(document).on('focusin', function (e) {
    if ($(e.target).closest(".tox-dialog").length) {
        e.stopImmediatePropagation();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    /// 1. Inicialización de DataTable con diseño completo y botones personalizados
    tableProductos = $('#tableProductos').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        "ajax": { "url": base_url + "/Productos/getProductos", "dataSrc": "" },
        "columns": [
            { "data": "idproducto" },
            { "data": "codigo" },
            { "data": "nombre" },
            { "data": "stock" },
            { "data": "precio" },
            { "data": "status" },
            { "data": "options" }
        ],
        "columnDefs": [
            { 'className': "text-center", "targets": [3, 5] },
            { 'className': "text-right", "targets": [4] }
        ],
        // 'lBfrtip' -> La 'l' devuelve el selector de filas (10, 25, 50...)
        'dom': 'lBfrtip',
        'buttons': [
            {
                extend: 'copyHtml5',
                text: '<i class="far fa-copy"></i> Copiar',
                className: 'btn-export btn-copy',
                titleAttr: 'Copiar',
                exportOptions: { "columns": [0, 1, 2, 3, 4, 5] }
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn-export btn-excel',
                titleAttr: 'Exportar a Excel',
                exportOptions: { "columns": [0, 1, 2, 3, 4, 5] }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn-export btn-pdf',
                titleAttr: 'Exportar a PDF',
                exportOptions: { "columns": [0, 1, 2, 3, 4, 5] }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn-export btn-csv',
                titleAttr: 'Exportar a CSV',
                exportOptions: { "columns": [0, 1, 2, 3, 4, 5] }
            }
        ],
        "responsive": true,
        "autoWidth": false, // Evita que las columnas se colapsen feo
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });
    // 2. Registro y Actualización de Producto
    if (document.querySelector("#formProductos")) {
        let formProductos = document.querySelector("#formProductos");
        formProductos.onsubmit = function (e) {
            e.preventDefault();

            let strNombre = document.querySelector('#txtNombre').value.trim();
            let strCodigo = document.querySelector('#txtCodigo').value.trim();
            let strPrecio = document.querySelector('#txtPrecio').value.trim();

            // Validación de campos obligatorios
            if (strNombre == '' || strCodigo == '' || strPrecio == '') {
                Swal.fire("Atención", "Todos los campos con (*) son obligatorios.", "error");
                return false;
            }

            // Sincronizar TinyMCE
            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }

            divLoading.style.display = "flex";

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Productos/setProducto';
            let formData = new FormData(formProductos);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    try {
                        let objData = JSON.parse(request.responseText);
                        if (objData.status) {

                            // CORRECCIÓN: Para que se actualice la tabla de verdad tras editar o añadir
                            if (rowTable == "") {
                                tableProductos.ajax.reload();
                            } else {
                                // Recarga la tabla manteniendo la posición de la página actual
                                tableProductos.ajax.reload(null, false);
                                rowTable = "";
                            }

                            $('#modalFormProductos').modal("hide");
                            formProductos.reset();

                            Swal.fire("Producto", objData.msg, "success");

                        } else {
                            Swal.fire("Error", objData.msg, "error");
                        }
                    } catch (error) {
                        console.error("Error en respuesta:", request.responseText);
                        Swal.fire("Error", "Error al procesar la respuesta del servidor.", "error");
                    }
                }
                if (document.querySelector("#divLoading")) {
                    document.querySelector("#divLoading").style.display = "none";
                }
            }
        }
    }
    // Lógica para agregar zonas de imagen
    if (document.querySelector(".btnAddImage")) {
        let btnAddImage = document.querySelector(".btnAddImage");
        btnAddImage.onclick = function () {
            let key = Date.now();
            let newElement = document.createElement("div");
            newElement.id = "div" + key;

            // Clases consistentes con el diseño
            newElement.className = "position-relative d-inline-block mr-2 mb-2 image-card-item";

            newElement.innerHTML = `
            <div class="prevImage"></div>
            <input type="file" name="foto" id="img${key}" class="inputUploadfile" accept="image/*">
            <label for="img${key}" class="btnUploadfile">
                <i class="fas fa-cloud-upload-alt"></i>
            </label>
            <button class="btnDeleteImage notblock" type="button" onclick="fntDelItem('#div${key}')">
                <i class="fas fa-times"></i>
            </button>`;

            document.querySelector("#containerImages").appendChild(newElement);

            document.querySelector("#img" + key).onchange = function () {
                fntUploadImg(this); // Asegúrate de tener esta función para procesar la subida
            };

            // Forzar el click para abrir el explorador de archivos
            setTimeout(() => {
                document.querySelector("#img" + key).click();
            }, 100);
        }
    }

    // --- FLUJO AUTOMATIZADO SIN CLICS ---

    // 1. De Identificación a Inventario
    document.querySelector('#txtCodigo').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (this.value.trim().length >= 5) {
                // Cerramos actual y abrimos siguiente
                $('#collapseOne').collapse('hide');
                $('#collapseTwo').collapse('show');

                // Foco en Precio
                setTimeout(() => { document.querySelector('#txtPrecio').focus(); }, 600);
            }
        }
    });


    if (document.querySelector('#txtStock')) {
        document.querySelector('#txtStock').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (this.value !== "") {
                    $('#collapseTwo').collapse('hide');
                    $('#collapseThree').collapse('show');
                    setTimeout(() => { $('#listCategoria').selectpicker('toggle'); }, 600);
                }
            }
        });
    }

    // 3. De Clasificación a Atributos (CORREGIDO)
    if ($('#listMaterial').length) {
        $('#listMaterial').on('changed.bs.select', function (e, clickedIndex, isSelected) {
            if (isSelected) {
                $('#collapseThree').collapse('hide');
                $('#collapseFour').collapse('show');

                setTimeout(() => {
                    if (document.querySelector('#txtColorInput')) {
                        document.querySelector('#txtColorInput').focus();
                    }
                }, 600);
            }
        });
    }

    if (document.querySelector("#formImportarProductos")) {
        let formImportar = document.querySelector("#formImportarProductos");

        formImportar.onsubmit = function (e) {
            e.preventDefault();

            let inputFile = document.querySelector("#fileProductos");

            // Validación de archivo seleccionado
            if (inputFile.files.length == 0) {
                Swal.fire({
                    title: "Atención",
                    text: "Por favor, seleccione un archivo Excel (.xlsx o .xls).",
                    icon: "warning",
                    confirmButtonColor: "#c9a050"
                });
                return false;
            }

            // Validación de extensión
            let fileName = inputFile.files[0].name;
            let extension = fileName.split('.').pop().toLowerCase();
            if (extension !== 'xlsx' && extension !== 'xls') {
                Swal.fire("Error", "El formato del archivo no es válido. Use Excel.", "error");
                return false;
            }

            // Mostrar el loading que ya tienes definido en el sistema
            if (document.querySelector("#divLoading")) {
                document.querySelector("#divLoading").style.display = "flex";
            }

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            // La ruta apunta al método setImport que crearemos en el controlador
            let ajaxUrl = base_url + '/Productos/setImport';
            let formData = new FormData(formImportar);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    try {
                        let objData = JSON.parse(request.responseText);

                        if (objData.status) {
                            // Cerramos el modal
                            $('#modalImportar').modal("hide");
                            formImportar.reset();

                            Swal.fire({
                                title: "Éxito",
                                text: objData.msg,
                                icon: "success",
                                confirmButtonColor: "#c9a050"
                            });

                            // Recargamos la tabla de productos si existe en la vista
                            if (typeof tableProductos !== 'undefined') {
                                tableProductos.ajax.reload();
                            }
                        } else {
                            Swal.fire("Error", objData.msg, "error");
                        }
                    } catch (error) {
                        console.error("Error en respuesta:", request.responseText);
                        Swal.fire("Error", "Ocurrió un error al procesar los datos del servidor.", "error");
                    }
                }

                // Ocultar loading al finalizar
                if (document.querySelector("#divLoading")) {
                    document.querySelector("#divLoading").style.display = "none";
                }
            }
        }
    }


    fntCheckStock();
    fntInputFile();
    fntCategorias();
}, false);

function fntUploadImg(element) {
    let idProducto = document.querySelector("#idProducto").value;
    let parentId = element.parentNode.getAttribute("id");
    let fileimg = element.files;
    let prevImg = document.querySelector("#" + parentId + " .prevImage");

    // 1. Validación de ID
    if (idProducto == "") {
        Swal.fire("Atención", "Primero debe guardar los datos del producto para subir imágenes.", "warning");
        element.value = "";
        return false;
    }

    if (element.value != '') {
        let type = fileimg[0].type;
        let size = fileimg[0].size;

        // 2. Validaciones de archivo
        if (type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png') {
            Swal.fire("Error", "Formato de imagen no válido (Use JPG o PNG).", "error");
            element.value = "";
            return false;
        }
        if (size > 5 * 1024 * 1024) {
            Swal.fire("Error", "La imagen es muy pesada (Máximo 5MB).", "error");
            element.value = "";
            return false;
        }

        // --- INTERFAZ: OCULTAR BOTÓN Y MOSTRAR LOADING ---
        let btnUpload = document.querySelector("#" + parentId + " .btnUploadfile");
        if (btnUpload) btnUpload.classList.add("notblock"); // Ocultar círculo azul
        prevImg.innerHTML = `<img class="loading" src="${base_url}/Assets/images/loading.svg">`;

        // --- PETICIÓN AJAX ---
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + '/Productos/setImage';
        let formData = new FormData();
        formData.append('idproducto', idProducto);
        formData.append("foto", fileimg[0]);

        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                try {
                    let objData = JSON.parse(request.responseText);

                    if (objData.status) {
                        // -------- Renderizar imagen cargada desde el servidor --------
                        prevImg.innerHTML = `<img src="${base_url}/Assets/images/uploads/${objData.imgname}?t=${Date.now()}" class="img-preview">`;

                        // Activar botón eliminar
                        let btnDel = document.querySelector("#" + parentId + " .btnDeleteImage");
                        if (btnDel) {
                            btnDel.setAttribute("imgname", objData.imgname);
                            btnDel.classList.remove("notblock");
                        }

                        // Quitar borde punteado al estar lleno
                        document.querySelector("#" + parentId).style.borderStyle = "solid";

                    } else {
                        Swal.fire("Error", objData.msg, "error");
                        prevImg.innerHTML = "";
                        if (btnUpload) btnUpload.classList.remove("notblock");
                        element.value = "";
                    }

                } catch (e) {
                    Swal.fire("Error", "Error procesando el servidor.", "error");
                    if (btnUpload) btnUpload.classList.remove("notblock");
                }
            }
        }
    }
}

function fntInputFile() {
    let inputUploadfile = document.querySelectorAll(".inputUploadfile");
    inputUploadfile.forEach(function (input) {
        input.onchange = function () {
            fntUploadImg(this); // Llamamos a la lógica centralizada
        };
    });
}

function fntViewInfo(idProducto) {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Productos/getProducto/' + idProducto;

    divLoading.style.display = "flex";
    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            divLoading.style.display = "none";
            try {
                let objData = JSON.parse(request.responseText);

                if (objData.status) {
                    let objProducto = objData.data;

                    let estado = objProducto.status == 1
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';

                    // CORRECCIÓN: Validar que el precio de oferta sea mayor a 0 para evitar el 'undefined'
                    let precioOfertaHTML = (objProducto.precio_oferta && parseFloat(objProducto.precio_oferta) > 0)
                        ? `<span class="text-success font-weight-bold">${objProducto.precio_oferta}</span>`
                        : '<span class="text-muted">Sin oferta</span>';

                    // Datos básicos
                    document.querySelector("#celCodigo").innerHTML = objProducto.codigo;
                    document.querySelector("#celNombre").innerHTML = objProducto.nombre;
                    document.querySelector("#celPrecio").innerHTML = objProducto.precio;
                    document.querySelector("#celPrecioOferta").innerHTML = precioOfertaHTML;
                    document.querySelector("#celStock").innerHTML = objProducto.stock;
                    document.querySelector("#celCategoria").innerHTML = objProducto.categoria;
                    document.querySelector("#celStatus").innerHTML = estado;
                    document.querySelector("#celDescripcion").innerHTML = objProducto.descripcion;

                    // Renderizar Colores
                    let htmlColores = "";
                    if (objProducto.colores && objProducto.colores != "") {
                        let arrColores = objProducto.colores.split(",");
                        arrColores.forEach(color => {
                            htmlColores += `<span class="badge badge-info mr-1" style="padding: 5px 10px;">${color.trim()}</span>`;
                        });
                    } else {
                        htmlColores = '<span class="text-muted">Sin colores definidos</span>';
                    }
                    document.querySelector("#celColor").innerHTML = htmlColores;

                    // Galería de Imágenes
                    let contenedor = document.querySelector("#celImagenes");
                    contenedor.innerHTML = "";

                    if (objProducto.images && objProducto.images.length > 0) {
                        objProducto.images.forEach(function (img) {
                            let imagen = document.createElement("img");
                            imagen.src = img.url_image + "?t=" + new Date().getTime();
                            imagen.className = "img-thumbnail m-1";
                            imagen.style.width = "100px";
                            imagen.style.height = "100px";
                            imagen.style.objectFit = "cover";
                            contenedor.appendChild(imagen);
                        });
                    } else {
                        contenedor.innerHTML = '<span class="text-muted">Sin imágenes.</span>';
                    }

                    $('#modalViewProducto').modal("show");

                } else {
                    swal("Error", objData.msg, "error");
                }
            } catch (error) {
                console.error("Error al parsear JSON:", error);
                swal("Error", "No se pudo obtener la información del servidor.", "error");
            }
        }
    }
}


function fntAddColor() {
    let colorInput = document.querySelector("#txtColorInput");
    let colorValue = colorInput.value.trim();

    if (colorValue != "") {
        if (!listColores.includes(colorValue)) {
            listColores.push(colorValue);
            renderColores(); // Esta función actualiza el input oculto
            colorInput.value = "";
            colorInput.focus();
        } else {
            Swal.fire("Atención", "Este color ya ha sido agregado.", "warning");
        }
    }
}

function renderColores() {
    let container = document.querySelector("#containerColores");
    let inputHidden = document.querySelector("#txtColores");

    if (!container || !inputHidden) return;

    container.innerHTML = "";

    listColores.forEach((color, index) => {
        let nombreColor = color.trim();
        if (nombreColor != "") {
            // Usamos una clase CSS en lugar de estilos inline pesados
            container.innerHTML += `
                <span class="badge badge-sayana">
                    ${nombreColor} 
                    <i class="fas fa-times" onclick="fntDelColor(${index})"></i>
                </span>`;
        }
    });

    inputHidden.value = listColores.join(",");
}

function fntDelColor(index) {
    listColores.splice(index, 1);
    renderColores();
}


function fntEditInfo(element, idProducto) {
    // Referencia a la fila
    rowTable = element.parentNode.parentNode.parentNode;

    // 1. Ajuste de UI del Modal con validación
    let titleText = document.querySelector('#titleText');
    if (titleText) titleText.innerHTML = "Actualizar Producto";

    let modalHeader = document.querySelector('.modal-header');
    if (modalHeader) modalHeader.classList.replace("headerRegister", "headerUpdate");

    let btnAction = document.querySelector('#btnActionForm');
    if (btnAction) btnAction.innerHTML = '<i class="fas fa-check-circle"></i> <span id="btnText">Actualizar</span>';

    // 2. Petición de Datos
    if (typeof divLoading !== 'undefined') divLoading.style.display = "flex";

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Productos/getProducto/' + idProducto;

    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            try {
                let objData = JSON.parse(request.responseText);
                if (objData.status) {
                    let objProducto = objData.data;

                    // --- FUNCIÓN INTERNA PARA ASIGNAR VALOR SEGURO ---
                    const setVal = (selector, value) => {
                        let el = document.querySelector(selector);
                        if (el) el.value = value;
                        else console.warn(`Elemento ${selector} no encontrado en el DOM.`);
                    };

                    // Llenado de campos con validación (Aquí es donde fallaba la línea 482)
                    setVal("#idProducto", objProducto.idproducto);
                    setVal("#txtNombre", objProducto.nombre);
                    setVal("#txtCodigo", objProducto.codigo);
                    setVal("#txtPrecio", objProducto.precio);
                    setVal("#txtStock", objProducto.stock);
                    setVal("#listCategoria", objProducto.categoriaid);
                    setVal("#listStatus", objProducto.status);

                    // Blindaje Precio Oferta
                    let valOferta = objProducto.precio_oferta;
                    let finalOferta = (valOferta == null || valOferta == "undefined" || parseFloat(valOferta) == 0) ? "" : valOferta;
                    setVal("#txtPrecioOferta", finalOferta);

                    // Lógica de Colores
                    listColores = [];
                    setVal("#txtColores", "");
                    let containerColores = document.querySelector("#containerColores");
                    if (containerColores) containerColores.innerHTML = "";

                    if (objProducto.colores && objProducto.colores.trim() !== "") {
                        listColores = objProducto.colores.split(",");
                        if (typeof renderColores === 'function') renderColores();
                    }

                    // Sincronizar TinyMCE
                    if (typeof tinymce !== 'undefined' && tinymce.get('txtDescription')) {
                        tinymce.get('txtDescription').setContent(objProducto.descripcion || "");
                    }

                    // Refrescar selectores (Bootstrap Select)
                    if ($.fn.selectpicker) {
                        $('#listCategoria').selectpicker('render');
                        $('#listStatus').selectpicker('render');
                    }

                    if (typeof fntBarcode === 'function') fntBarcode();

                    // 3. Reconstrucción de la Galería
                    let containerImages = document.querySelector("#containerImages");
                    if (containerImages) {
                        containerImages.innerHTML = "";

                        if (objProducto.images && objProducto.images.length > 0) {
                            objProducto.images.forEach((img, i) => {
                                let key = Date.now() + i;
                                let div = document.createElement('div');
                                div.id = "div" + key;
                                div.className = "position-relative d-inline-block mr-2 mb-2 image-card-item";

                                // Si la imagen es default.png, no mostramos el botón de eliminar
                                let btnDelete = (img.img == 'default.png') ? '' : `
                                    <button type="button" class="btnDeleteImage" onclick="fntDelItem('#div${key}')" imgname="${img.img}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>`;

                                                    div.innerHTML = `
                                    <div class="prevImage">
                                        <img src="${img.url_image}">
                                    </div>
                                    ${btnDelete}`;
                                containerImages.appendChild(div);
                            });
                        }

                        // Slot nuevo
                        let newKey = Date.now() + 999;
                        let newSlot = document.createElement('div');
                        newSlot.id = "div" + newKey;
                        newSlot.className = "position-relative d-inline-block mr-2 mb-2 image-card-item";
                        newSlot.innerHTML = `
                            <div class="prevImage"></div>
                            <input type="file" name="foto" id="img${newKey}" class="inputUploadfile" accept="image/*">
                            <label for="img${newKey}" class="btnUploadfile"><i class="fas fa-cloud-upload-alt"></i></label>
                            <button class="btnDeleteImage notblock" type="button" onclick="fntDelItem('#div${newKey}')"><i class="fas fa-times"></i></button>`;
                        containerImages.appendChild(newSlot);

                        let inputNewImg = document.querySelector("#img" + newKey);
                        if (inputNewImg) inputNewImg.onchange = function () { fntUploadImg(this); };
                    }

                    // Mostrar secciones ocultas
                    document.querySelector("#divBarCode")?.classList.remove("notblock");
                    document.querySelector("#containerGallery")?.classList.remove("notblock");

                    $('#modalFormProductos').modal('show');
                } else {
                    Swal.fire("Error", objData.msg, "error");
                }
            } catch (e) {
                console.error("Error crítico en getProducto:", e);
                Swal.fire("Error", "Error al procesar los datos del servidor.", "error");
            }
            if (typeof divLoading !== 'undefined') divLoading.style.display = "none";
        }
    }
}
function fntDelInfo(idProducto) {
    Swal.fire({
        title: "Eliminar Producto",
        text: "¿Realmente desea eliminar este producto? Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33", // Color rojo para advertencia de borrado
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar"
    }).then((result) => {
        if (result.isConfirmed) {

            // Activamos el loading mientras el servidor procesa el borrado físico y de DB
            divLoading.style.display = "flex";

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Productos/delProducto';
            let strData = "idProducto=" + idProducto;

            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    try {
                        let objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            Swal.fire("Eliminado", objData.msg, "success");
                            // Recargamos el DataTable para reflejar el cambio
                            tableProductos.ajax.reload();
                        } else {
                            Swal.fire("Atención", objData.msg, "error");
                        }
                    } catch (error) {
                        console.error("Error al eliminar:", request.responseText);
                        Swal.fire("Error", "No se pudo procesar la solicitud en el servidor.", "error");
                    }
                }
                // Ocultamos el loading independientemente del resultado
                divLoading.style.display = "none";
            }
        }
    });
}

function openModal() {
    rowTable = "";
    listColores = [];
    if (typeof tinymce !== 'undefined' && tinymce.get('txtDescripcion')) {
        tinymce.get('txtDescripcion').setContent("");
    }
    if (document.querySelector('#idProducto')) document.querySelector('#idProducto').value = "";

    const formProductos = document.querySelector("#formProductos");
    if (formProductos) {

        try {
            formProductos.reset();
        } catch (e) {
            console.warn("Aviso: Reset de formulario interceptado, limpiando campos manualmente.");
        }
    }

    // Limpieza manual de campos específicos
    if (document.querySelector('#txtPrecioOferta')) document.querySelector('#txtPrecioOferta').value = "";
    if (document.querySelector("#txtColores")) document.querySelector("#txtColores").value = "";

    // 3. Restaurar Estética del Modal (Modo Registro)
    let modalHeader = document.querySelector('.modal-header');
    if (modalHeader) {
        modalHeader.classList.remove("headerUpdate");
        modalHeader.classList.add("headerRegister");
    }

    let btnAction = document.querySelector('#btnActionForm');
    if (btnAction) {
        btnAction.classList.remove("btn-primary");
        btnAction.classList.add("btn-success");
        btnAction.innerHTML = '<i class="fas fa-check-circle"></i> <span id="btnText">Guardar</span>';
    }

    if (document.querySelector('#titleText')) document.querySelector('#titleText').innerHTML = "Nuevo Producto";

    // 4. Limpiar Galería, Código de Barras y Colores
    const elementsToHide = ["#divBarCode", "#containerGallery"];
    elementsToHide.forEach(selector => {
        let el = document.querySelector(selector);
        if (el) el.classList.add("notblock");
    });

    const containersToClear = ["#barcode", "#containerImages", "#containerColores"];
    containersToClear.forEach(selector => {
        let el = document.querySelector(selector);
        if (el) el.innerHTML = "";
    });

    // 5. Resetear Selectores de Bootstrap (Selectpicker)
    if ($.fn.selectpicker) {
        $('#listCategoria').val('').selectpicker('render');
        $('#listStatus').val(1).selectpicker('render');
    }

    // 6. Limpiar validaciones visuales
    $(".form-control").removeClass("is-invalid");

    // 7. Abrir Modal
    $('#modalFormProductos').modal('show');
}

// Configuración de TinyMCE
if (typeof tinymce !== 'undefined') {
    tinymce.init({
        selector: '#txtDescripcion',
        width: "100%",
        height: 300,
        statubar: true,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table directionality emoticons template paste"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons",
        branding: false,
        promotion: false,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save(); // Sincroniza automáticamente con el textarea
            });
        }
    });
}

function fntBarcode() {
    let codigo = document.querySelector("#txtCodigo").value;
    let barcodeElement = document.querySelector("#barcode");
    let divBarCode = document.querySelector("#divBarCode"); // Referencia al contenedor

    if (codigo.length >= 5) {
        // Validamos que el contenedor exista antes de quitarle la clase
        if (divBarCode) {
            divBarCode.classList.remove("notblock");
        }

        // Generamos el código con configuración profesional
        if (barcodeElement) {
            JsBarcode("#barcode", codigo, {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 50,
                displayValue: true
            });
        }
    } else {
        // Si el código es corto, limpiamos el SVG si existe
        if (barcodeElement) barcodeElement.innerHTML = "";
        // Opcional: Volver a ocultar el contenedor si existe
        if (divBarCode) divBarCode.classList.add("notblock");
    }
}

function fntCategorias() {
    if (document.querySelector('#listCategoria')) {
        let ajaxUrl = base_url + '/Categorias/getSelectCategorias';
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');

        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                document.querySelector('#listCategoria').innerHTML = request.responseText;

                $('#listCategoria').selectpicker('refresh');
                $('#listCategoria').selectpicker('render');
            } else if (request.readyState == 4 && request.status != 200) {
                console.error("Error al cargar categorías:", request.status);
            }
        }
    }
}

function fntDelItem(element) {
    // 1. Validamos que el atributo 'imgname' exista para evitar errores de selección
    let btnDelete = document.querySelector(element + ' .btnDeleteImage');
    let nameImg = btnDelete ? btnDelete.getAttribute("imgname") : null;
    let idProducto = document.querySelector("#idProducto").value;

    if (!nameImg) {
        Swal.fire("Error", "No se pudo encontrar el nombre de la imagen.", "error");
        return false;
    }

    Swal.fire({
        title: "Eliminar Imagen",
        text: "¿Deseas eliminar esta imagen?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            divLoading.style.display = "flex";

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Productos/delFile';
            let formData = new FormData();
            formData.append('idproducto', idProducto);
            formData.append("file", nameImg);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function () {
                if (request.readyState == 4) {
                    if (request.status == 200) {
                        try {
                            // Intentamos parsear la respuesta
                            let objData = JSON.parse(request.responseText);

                            if (objData.status) {
                                let itemToRemove = document.querySelector(element);
                                if (itemToRemove) itemToRemove.remove();
                                Swal.fire("Eliminada", objData.msg, "success");
                            } else {
                                Swal.fire("Error", objData.msg, "error");
                            }
                        } catch (error) {
                            // SI HAY UN ERROR DE PHP, ESTO EVITA QUE EL JS SE DETENGA
                            console.error("Error del servidor (Response):", request.responseText);
                            Swal.fire("Error Crítico", "El servidor envió una respuesta inválida. Revisa la consola.", "error");
                        }
                    } else {
                        Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
                    }
                    // Siempre ocultamos el loading al terminar la petición
                    divLoading.style.display = "none";
                }
            };
        }
    });
}
function fntCheckStock() {
    let url = base_url + '/Productos/getStockCritico';
    fetch(url)
        .then(response => response.json())
        .then(objData => {
            if (objData.status) {
                objData.data.forEach(producto => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                    Toast.fire({
                        icon: 'warning',
                        title: `¡STOCK CRÍTICO: ${producto.nombre}!`
                    });
                });

                // --- SOLUCIÓN AL ERROR TypeError ---
                // Se elimina .api() porque tableProductos ya es la instancia de la API
                if (document.querySelector("#tableProductos") && tableProductos) {
                    tableProductos.ajax.reload(null, false);
                }
            }
        })
        .catch(err => console.error("Error en monitoreo de stock:", err));
}
function fntModalImportar() {
    const formImportar = document.querySelector("#formImportarProductos");
    if (formImportar) {
        formImportar.reset();
    }
    $('#modalImportar').modal('show');
}
