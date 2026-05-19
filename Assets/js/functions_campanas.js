let tableCampanas;
let divLoading = document.querySelector("#divLoading");
let bloquesLanding = [];

// Objeto global para manejar la configuración actual del bloque seleccionado
let currentBlockConfig = null;

document.addEventListener('DOMContentLoaded', function () {

    tableCampanas = $('#tableCampanas').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": { "url": base_url + "/Campanas/getCampanas", "dataSrc": "" },
        "columns": [
            { "data": "id_campana" },
            { "data": "multimedia" },
            { "data": "nombre" },
            { "data": "vigencia" },
            { "data": "estado" },
            { "data": "options" }
        ],
        "columnDefs": [
            { 'className': "text-center", "targets": [0, 1, 4, 5] },
            { "width": "80px", "targets": 1 }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // --- ESCUCHADOR PARA ACTUALIZACIÓN AUTOMÁTICA ---
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('editor-control')) {
            renderizarPreview(); // Asegúrate de que la función se llame renderizarPreview como en el resto de tu código
        }
    });

    // Vista previa de imagen al seleccionar archivo
    if (document.querySelector("#foto")) {
        let foto = document.querySelector("#foto");
        foto.onchange = function () {
            let file = this.files[0];
            let iconUpload = document.querySelector('#iconUpload');
            let imgNav = document.querySelector('#imgNav');

            if (file) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    if (iconUpload) iconUpload.classList.add("d-none");
                    if (imgNav) {
                        imgNav.src = e.target.result;
                        imgNav.classList.remove("d-none");
                        imgNav.style.width = "100%";
                        imgNav.style.height = "100%";
                        imgNav.style.objectFit = "contain";
                    }
                };
                reader.readAsDataURL(file);
                document.querySelector('.delPhoto').classList.remove("notBlock");
            }
        }
    }

    if (document.querySelector(".delPhoto")) {
        document.querySelector(".delPhoto").onclick = function () {
            removePhoto();
        }
    }
    // ONSUBMIT DEL FORMULARIO DE CAMPAÑA (CREAR/EDITAR)
    let formCampana = document.querySelector("#formCampana");
    if (formCampana) {
        formCampana.onsubmit = function (e) {
            e.preventDefault();
            let strNombre = document.querySelector('#txtNombre').value.trim();
            let strFechaI = document.querySelector('#txtFechaInicio').value;
            let strFechaF = document.querySelector('#txtFechaFin').value;

            if (strNombre == '' || strFechaI == '' || strFechaF == '') {
                Swal.fire("Atención", "El nombre y las fechas son obligatorios", "error");
                return false;
            }

            if (divLoading) divLoading.style.display = "flex";
            // GENERAR HTML LIMPIO
            renderizarPreview();
            let request = new XMLHttpRequest();
            let ajaxUrl = base_url + '/Campanas/setCampana';
            let formData = new FormData(formCampana);
            // JSON DEL CONSTRUCTOR
            formData.set(
                "txtJsonContenido",
                JSON.stringify(bloquesLanding)
            );
            // HTML LIMPIO FINAL
            let htmlReal = document.querySelector("#txtContenidoHtml").value;
            formData.set(
                "txtContenidoHtml",
                htmlReal
            );
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    if (divLoading) divLoading.style.display = "none";
                    try {
                        let objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            $('#modalFormCampana').modal("hide");
                            formCampana.reset();
                            removePhoto();
                            Swal.fire(
                                "Campañas",
                                objData.msg,
                                "success"
                            );
                            tableCampanas.ajax.reload();
                        } else {
                            Swal.fire(
                                "Error",
                                objData.msg,
                                "error"
                            );
                        }
                    } catch (error) {
                        console.error(
                            "Error parseando respuesta:",
                            request.responseText
                        );
                        Swal.fire(
                            "Error",
                            "Respuesta inválida del servidor",
                            "error"
                        );
                    }
                }
            }
        }
    }
}, false);


// --- FUNCIONES DE GESTIÓN DE CAMPAÑA (AJAX & UI) ---

function openModal() {
    document.querySelector("#idCampana").value = "";
    if (document.querySelector("#formCampana")) document.querySelector("#formCampana").reset();
    document.querySelector("#livePreview").innerHTML = "";
    const listaEditor = document.querySelector("#blocksEditorList");
    if (listaEditor) listaEditor.innerHTML = '<p class="text-center text-muted p-4">Añade un bloque para empezar.</p>';

    bloquesLanding = [];
    removePhoto();

    document.querySelector("#titleText").innerHTML = "Nueva Campaña";
    document.querySelector(".modal-header").classList.replace("headerUpdate", "headerRegister");
    document.querySelector("#btnText").innerHTML = "Guardar Campaña";
    $('#modalFormCampana').modal('show');
}

function fntViewCampana(idcampana) {
    let request = new XMLHttpRequest();
    request.open("GET", base_url + '/Campanas/getCampana/' + idcampana, true);
    request.send();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if (objData.status) {
                let data = objData.data;
                let estado = data.estado == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>';

                const setHtml = (id, val) => {
                    const el = document.querySelector(id);
                    if (el) el.innerHTML = val;
                };

                setHtml('#celNombre', data.nombre);
                setHtml('#celDescripcion', data.descripcion_corta);
                setHtml('#celEstado', estado);
                setHtml('#celFechaInicio', data.fecha_inicio);
                setHtml('#celFechaFin', data.fecha_fin);
                setHtml('#celEnlace', data.enlace_boton);
                setHtml('#celContenido', data.html_contenido);

                // Si detectamos la ruleta en el JSON, avisamos en la vista
                if (data.json_contenido && data.json_contenido.includes('"tipo":"ruleta"')) {
                    const infoExtra = document.querySelector('#celContenido');
                    if (infoExtra) {
                        infoExtra.innerHTML += `
                        <div class="mt-3 p-2 border-left-warning bg-light">
                            <i class="fas fa-dharmachakra text-warning"></i> 
                            <strong>Nota:</strong> Esta campaña incluye una Ruleta Física configurada.
                        </div>`;
                    }
                }

                let imgBanner = document.querySelector('#imgBanner');
                if (imgBanner) {
                    imgBanner.src = data.url_banner ? data.url_banner : '';
                }

                $('#modalViewCampana').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntEditCampana(idcampana) {
    document.querySelector("#titleText").innerHTML = "Actualizar Campaña";
    document.querySelector(".modal-header").classList.replace("headerRegister", "headerUpdate");
    document.querySelector("#btnText").innerHTML = "Actualizar";

    // Limpieza previa de imágenes para evitar flashes de la anterior
    if (document.querySelector('#imgNav')) document.querySelector('#imgNav').src = "";
    bloquesLanding = [];

    let request = new XMLHttpRequest();
    request.open("GET", base_url + '/Campanas/getCampana/' + idcampana, true);
    request.send();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if (objData.status) {
                // 1. Campos básicos
                document.querySelector("#idCampana").value = objData.data.id_campana;
                document.querySelector("#txtNombre").value = objData.data.nombre;
                document.querySelector("#txtDescripcionCorta").value = objData.data.descripcion_corta;
                document.querySelector("#txtContenidoHtml").value = objData.data.html_contenido;
                document.querySelector("#txtFechaInicio").value = objData.data.fecha_inicio.replace(" ", "T");
                document.querySelector("#txtFechaFin").value = objData.data.fecha_fin.replace(" ", "T");
                document.querySelector("#txtEnlaceBoton").value = objData.data.enlace_boton;
                document.querySelector("#listStatus").value = objData.data.estado;
                document.querySelector("#foto_actual").value = objData.data.banner_landing;

                // 2. Imagen principal (Banner)
                if (objData.data.banner_landing != "" && objData.data.banner_landing != null) {
                    if (document.querySelector('#iconUpload')) document.querySelector('#iconUpload').classList.add("d-none");
                    if (document.querySelector('#imgNav')) {
                        document.querySelector('#imgNav').src = objData.data.url_banner;
                        document.querySelector('#imgNav').classList.remove("d-none");
                    }
                    if (document.querySelector('.delPhoto')) document.querySelector('.delPhoto').classList.remove("notBlock");
                } else {
                    removePhoto();
                }

                // 3. CARGAR BLOQUES
                if (objData.data.json_contenido && objData.data.json_contenido !== "" && objData.data.json_contenido !== "null") {
                    try {
                        // Si el JSON viene como string (doble encode), lo parseamos
                        bloquesLanding = typeof objData.data.json_contenido === 'string'
                            ? JSON.parse(objData.data.json_contenido)
                            : objData.data.json_contenido;
                    } catch (e) {
                        console.error("Error parseando bloques:", e);
                        bloquesLanding = [];
                    }
                }

                // 4. Redibujar
                actualizarInterfaz();
                $('#modalFormCampana').modal('show');
            }
        }
    }
}
function fntDelCampana(idCampana) {
    Swal.fire({
        title: "Eliminar campaña",
        text: "¿Realmente deseas eliminar esta campaña?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#e74a3b",
        cancelButtonColor: "#858796",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            let request = new XMLHttpRequest();
            let ajaxUrl = base_url + '/Campanas/delCampana';
            let strData = "idCampana=" + idCampana;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader(
                "Content-type",
                "application/x-www-form-urlencoded"
            );
            request.send(strData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    try {
                        let objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            Swal.fire(
                                "Eliminada",
                                objData.msg,
                                "success"
                            );
                            tableCampanas.ajax.reload();
                        } else {
                            Swal.fire(
                                "Error",
                                objData.msg,
                                "error"
                            );
                        }
                    } catch (e) {
                        console.error(request.responseText);
                        Swal.fire(
                            "Error",
                            "Respuesta inválida del servidor",
                            "error"
                        );
                    }
                }
            };
        }
    });
}

function removePhoto() {
    if (document.querySelector('#foto')) document.querySelector('#foto').value = "";
    if (document.querySelector('#foto_actual')) document.querySelector('#foto_actual').value = ""; // Limpia el nombre de la imagen vieja
    if (document.querySelector('.delPhoto')) document.querySelector('.delPhoto').classList.add("notBlock");
    let iconUpload = document.querySelector('#iconUpload');
    let imgNav = document.querySelector('#imgNav');
    if (imgNav) { imgNav.src = ""; imgNav.classList.add("d-none"); }
    if (iconUpload) { iconUpload.classList.remove("d-none"); }
}

// --- MOTOR DE DISEÑO VISUAL ---

function agregarNuevoBloque(tipo) {
    const id = Date.now();
    let nuevoBloque = { id, tipo };

    if (tipo === 'hero') {
        // 🛠️ ¡CORRECCIÓN: Inicialización con variables de color independientes para Título, Descripción y Botón!
        Object.assign(nuevoBloque, {
            titulo: "NUEVA COLECCIÓN",
            subtitulo: "Descubre productos increíbles con una experiencia moderna.",
            bg: "#fff5f6",
            txt: "#111111",          
            txtDescHero: "#666666",  
            btn: "#ff6b81",          
            btnTxtColor: "#ffffff",  
            btnTxt: "Comprar Ahora",
            btnLink: "#",
            padding: "80"
        });

    } else if (tipo === 'banner') {
        Object.assign(nuevoBloque, {
            fileUrl: "",
            alto: "300"
        });
    } else if (tipo === 'media') {
        Object.assign(nuevoBloque, {
            tipoMedia: "imagen",
            fileUrl: ""
        });
    } else if (tipo === 'cards') {
        Object.assign(nuevoBloque, {
            tituloCard: "Nuestros Servicios",
            bgCard: "#ffffff",
            txtCard: "#333333",
            txtDescCard: "#777777",
            items: [
                { id: 1, icono: "fas fa-star", titulo: "Opción 1", desc: "Descripción personalizable." },
                { id: 2, icono: "fas fa-heart", titulo: "Opción 2", desc: "Descripción personalizable." },
                { id: 3, icono: "fas fa-rocket", titulo: "Opción 3", desc: "Descripción personalizable." }
            ]
        });
    } else if (tipo === 'cta') {
        Object.assign(nuevoBloque, {
            texto: "¡Regístrate ahora mismo!",
            color: "#f6c23e",
            btnTxt: "Unirme",
            btnLink: "#" // Nuevo campo para el enlace
        });
    } else if (tipo === 'popup') {
        Object.assign(nuevoBloque, {
            titulo: "¡Oferta Especial!",
            subtitulo: "Suscríbete y recibe un 10% de descuento en tu primera compra.",
            btnTxt: "Obtener Descuento",
            btnLink: "#",
            fileUrl: "" // Aquí se guardará la ruta de la imagen
        });
    } else if (tipo === 'ruleta') {
        Object.assign(nuevoBloque, {
            tipo: 'ruleta',
            titulo: "¡Gira la Ruleta Sayana!",
            // Configuración de tus 8 segmentos físicos
            segmentos: [
                { texto: "Bono $10.000", codigo: "BONO10K", color: "#f77870" },
                { texto: "5% DCTO", codigo: "SAYANA5", color: "#333333" },
                { texto: "10% DCTO", codigo: "SAYANA10", color: "#f77870" },
                { texto: "15% DCTO", codigo: "SAYANA15", color: "#333333" },
                { texto: "Premio Sorpresa", codigo: "SORPRESA", color: "#f77870" },
                { texto: "5% DCTO", codigo: "SAYANA5_B", color: "#333333" },
                { texto: "10% DCTO", codigo: "SAYANA10_B", color: "#f77870" },
                { texto: "Gira de Nuevo", codigo: "REINTENTO", color: "#333333" }
            ]
        });
    }

    bloquesLanding.push(nuevoBloque);
    actualizarInterfaz();
}

function eliminarBloque(id) {
    bloquesLanding = bloquesLanding.filter(b => b.id !== id);
    actualizarInterfaz();
}

function editarBloque(id, campo, valor) {
    const bloque = bloquesLanding.find(b => b.id === id);
    if (bloque) {
        bloque[campo] = valor;
        renderizarPreview();
    }
}

function editarItemCard(bloqueId, itemId, campo, valor) {
    const bloque = bloquesLanding.find(b => b.id === bloqueId);
    if (bloque && bloque.items) {
        const item = bloque.items.find(i => i.id === itemId);
        if (item) {
            item[campo] = valor;
            renderizarPreview();
        }
    }
}

function cargarArchivoLocal(bloqueId, input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const bloque = bloquesLanding.find(b => b.id === bloqueId);

        if (bloque) {
            // USAMOS FILEREADER PARA CONVERTIR A BASE64 (Texto permanente)
            const reader = new FileReader();
            
            reader.onload = function (e) {
                // e.target.result contiene la imagen real codificada en texto
                const contenidoBase64 = e.target.result;

                bloque.fileUrl = contenidoBase64; // Se guarda como texto en el JSON
           
                actualizarInterfaz();
                renderizarPreview();
            };

            // Esto lee el archivo y activa el 'onload' de arriba
            reader.readAsDataURL(file);
        }
    }
}

function actualizarInterfaz() {
    const listaEditor = document.querySelector("#blocksEditorList");
    if (!listaEditor) return;

    listaEditor.innerHTML = "";

    if (bloquesLanding.length === 0) {
        listaEditor.innerHTML = '<p class="text-center text-muted p-4">Añade un bloque para empezar.</p>';
        renderizarPreview();
        return;
    }

    bloquesLanding.forEach((b, index) => {
        let miniaturaHtml = "";

        // --- LÓGICA DE MINIATURA CORREGIDA ---
        if (b.fileUrl) {
            let rutaPreview = b.fileUrl;
            
            // Si no tiene 'data:' (Base64) ni es un link 'http', entonces es una imagen vieja del servidor
            if (!rutaPreview.startsWith('data:') && !rutaPreview.startsWith('http')) {
                rutaPreview = base_url + '/Assets/images/uploads/' + b.fileUrl;
            }

            if (b.tipoMedia === 'video' || b.tipo === 'video') {
                miniaturaHtml = `<div class="mb-2 small text-muted"><i class="fas fa-video"></i> Video cargado</div>`;
            } else {
                miniaturaHtml = `<img src="${rutaPreview}" style="width:100%; max-height:80px; object-fit:cover; border-radius:5px; margin-bottom:8px; border:1px solid #ddd;">`;
            }
        }

        let formHtml = `
            <div class="card mb-3 border-left-primary shadow-sm animate__animated animate__fadeIn" data-id="${b.id}">
                <div class="card-header d-flex justify-content-between align-items-center py-2 bg-white" style="cursor: move;">
                    <div>
                        <i class="fas fa-grip-vertical text-muted mr-2"></i>
                        <span class="badge badge-primary text-uppercase">${b.tipo}</span>
                    </div>
                    <button type="button" class="btn btn-sm text-danger border-0" onclick="eliminarBloque(${b.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-body p-3">`;

        if (b.tipo === 'hero') {
            
            formHtml += `
                <label class="small font-weight-bold">Título</label>
                <input type="text" class="form-control form-control-sm mb-2" value="${b.titulo || ''}" oninput="editarBloque(${b.id}, 'titulo', this.value)">
                <label class="small font-weight-bold">Subtítulo</label>
                <textarea class="form-control form-control-sm mb-2" oninput="editarBloque(${b.id}, 'subtitulo', this.value)">${b.subtitulo || ''}</textarea>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="small font-weight-bold">Texto Botón</label>
                        <input type="text" class="form-control form-control-sm" value="${b.btnTxt || ''}" oninput="editarBloque(${b.id}, 'btnTxt', this.value)">
                    </div>
                    <div class="col-6">
                        <label class="small font-weight-bold">Enlace (URL)</label>
                        <input type="text" class="form-control form-control-sm" value="${b.btnLink || ''}" oninput="editarBloque(${b.id}, 'btnLink', this.value)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-4">
                        <label class="small font-weight-bold">Fondo Sección</label>
                        <input type="color" class="form-control" value="${b.bg || '#ffffff'}" oninput="editarBloque(${b.id}, 'bg', this.value)">
                    </div>
                    <div class="col-4">
                        <label class="small font-weight-bold">Color Título</label>
                        <input type="color" class="form-control" value="${b.txt || '#111111'}" oninput="editarBloque(${b.id}, 'txt', this.value)">
                    </div>
                    <div class="col-4">
                        <label class="small font-weight-bold">Color Desc.</label>
                        <input type="color" class="form-control" value="${b.txtDescHero || '#666666'}" oninput="editarBloque(${b.id}, 'txtDescHero', this.value)">
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-4">
                        <label class="small font-weight-bold">Color Botón</label>
                        <input type="color" class="form-control" value="${b.btn || '#ff6b81'}" oninput="editarBloque(${b.id}, 'btn', this.value)">
                    </div>
                    <div class="col-4">
                        <label class="small font-weight-bold">Texto Botón</label>
                        <input type="color" class="form-control" value="${b.btnTxtColor || '#ffffff'}" oninput="editarBloque(${b.id}, 'btnTxtColor', this.value)">
                    </div>
                    <div class="col-4">
                        <label class="small font-weight-bold">Padding (px)</label>
                        <input type="number" class="form-control form-control-sm" value="${b.padding || '60'}" oninput="editarBloque(${b.id}, 'padding', this.value)">
                    </div>
                </div>`;

        } else if (b.tipo === 'banner') {
            formHtml += `
                <label class="small font-weight-bold text-muted">Imagen Seleccionada:</label>
                ${miniaturaHtml}
                <div class="custom-file-upload">
                    <label class="custom-file-label-btn" for="fileBanner_${b.id}">
                        <i class="fas fa-cloud-upload-alt"></i> Cambiar Archivo
                    </label>
                    <input id="fileBanner_${b.id}" type="file" accept="image/*" onchange="cargarArchivoLocal(${b.id}, this)" style="display:none;">
                </div>
                <label class="small font-weight-bold text-muted mt-2">Altura (px)</label>
                <input type="number" class="form-control form-control-sm" value="${b.alto || '300'}" oninput="editarBloque(${b.id}, 'alto', this.value)">`;

        } else if (b.tipo === 'media') {
            formHtml += `
                <label class="small font-weight-bold text-uppercase text-muted">Multimedia</label>
                <select class="form-control form-control-sm mb-3" onchange="editarBloque(${b.id}, 'tipoMedia', this.value)">
                    <option value="imagen" ${b.tipoMedia === 'imagen' ? 'selected' : ''}>Imagen</option>
                    <option value="video" ${b.tipoMedia === 'video' ? 'selected' : ''}>Video</option>
                </select>
                ${miniaturaHtml}
                <div class="custom-file-upload">
                    <label class="custom-file-label-btn" for="fileMedia_${b.id}">
                        <i class="fas fa-file-import"></i> Subir Archivo
                    </label>
                    <input id="fileMedia_${b.id}" type="file" accept="${b.tipoMedia === 'video' ? 'video/*' : 'image/*'}" onchange="cargarArchivoLocal(${b.id}, this)" style="display:none;">
                </div>`;

        }  else if (b.tipo === 'cards') {
            formHtml += `
                <label class="small font-weight-bold">Título Sección</label>
                <input type="text" class="form-control form-control-sm mb-2" value="${b.tituloCard || ''}" oninput="editarBloque(${b.id}, 'tituloCard', this.value)">
                
                <div class="row mb-3">
                    <div class="col-4">
                        <label class="small font-weight-bold">Color Card</label>
                        <input type="color" class="form-control" value="${b.bgCard || '#ffffff'}" oninput="editarBloque(${b.id}, 'bgCard', this.value)">
                    </div>
                    <div class="col-4">
                        <label class="small font-weight-bold">Color Título</label>
                        <input type="color" class="form-control" value="${b.txtCard || '#000000'}" oninput="editarBloque(${b.id}, 'txtCard', this.value)">
                    </div>
                    <div class="col-4">
                        <label class="small font-weight-bold">Color Desc.</label>
                        <input type="color" class="form-control" value="${b.txtDescCard || '#777777'}" oninput="editarBloque(${b.id}, 'txtDescCard', this.value)">
                    </div>
                </div>
                
                <p class="small font-weight-bold text-muted mb-2 border-top pt-2"><i class="fas fa-list"></i> Editar Tarjetas Individuales:</p>
                <div class="cards-items-editor">
                    ${(b.items || []).map((item, i) => `
                        <div class="p-2 mb-2 bg-light rounded border" style="font-size: 12px;">
                            <span class="badge badge-secondary mb-1">Tarjeta ${i + 1}</span>
                            <div class="mb-1">
                                <label class="mb-0 font-weight-bold">Clase Icono (FontAwesome)</label>
                                <input type="text" class="form-control form-control-sm" value="${item.icono || ''}" oninput="editarItemCard(${b.id}, ${item.id}, 'icono', this.value)">
                            </div>
                            <div class="mb-1">
                                <label class="mb-0 font-weight-bold">Título</label>
                                <input type="text" class="form-control form-control-sm" value="${item.titulo || ''}" oninput="editarItemCard(${b.id}, ${item.id}, 'titulo', this.value)">
                            </div>
                            <div>
                                <label class="mb-0 font-weight-bold">Descripción</label>
                                <textarea class="form-control form-control-sm" rows="2" oninput="editarItemCard(${b.id}, ${item.id}, 'desc', this.value)">${item.desc || ''}</textarea>
                            </div>
                        </div>
                    `).join('')}
                </div>`;

        } else if (b.tipo === 'cta') {
            formHtml += `
                <label class="small font-weight-bold">Texto Principal</label>
                <input type="text" class="form-control form-control-sm mb-3" value="${b.texto || ''}" oninput="editarBloque(${b.id}, 'texto', this.value)">
                <div class="row">
                    <div class="col-6">
                        <label class="small">Texto Botón</label>
                        <input type="text" class="form-control form-control-sm" value="${b.btnTxt || ''}" oninput="editarBloque(${b.id}, 'btnTxt', this.value)">
                    </div>
                    <div class="col-6">
                        <label class="small">Color</label>
                        <input type="color" class="form-control" value="${b.color || '#000000'}" oninput="editarBloque(${b.id}, 'color', this.value)">
                    </div>
                </div>
                <label class="small mt-2">Enlace</label>
                <input type="text" class="form-control form-control-sm" value="${b.btnLink || '#'}" oninput="editarBloque(${b.id}, 'btnLink', this.value)">`;

        } else if (b.tipo === 'popup') {
            formHtml += `
                <div style="background: #f0f7fd; padding: 10px; border-radius: 5px; border: 1px solid #3498db;">
                    <label class="small font-weight-bold">Título Popup</label>
                    <input type="text" class="form-control form-control-sm mb-2" value="${b.titulo || ''}" oninput="editarBloque(${b.id}, 'titulo', this.value)">
                    <label class="small">Subtítulo</label>
                    <textarea class="form-control form-control-sm mb-2" oninput="editarBloque(${b.id}, 'subtitulo', this.value)">${b.subtitulo || ''}</textarea>
                    ${miniaturaHtml}
                    <div class="custom-file-upload">
                        <label class="custom-file-label-btn" for="filePop_${b.id}">Cambiar Imagen</label>
                        <input id="filePop_${b.id}" type="file" accept="image/*" onchange="cargarArchivoLocal(${b.id}, this)" style="display:none;">
                    </div>
                </div>`;
        } else if (b.tipo === 'ruleta') {
            formHtml += `
                <div style="border: 2px dashed #f77870; background: #fff5f6; padding: 15px; text-align: center; border-radius: 8px;">
                    <i class="fas fa-dharmachakra" style="color: #f77870; font-size: 20px;"></i>
                    <p style="margin: 5px 0; color: #f77870; font-weight: bold; font-size: 13px;">Ruleta Física Configurada</p>
                    <div class="text-left small mt-2">
                        ${b.segmentos.map(s => `<div class="d-flex justify-content-between border-bottom pb-1 mb-1"><span>${s.texto}</span> <span class="badge badge-light">${s.codigo}</span></div>`).join('')}
                    </div>
                </div>`;
        }

        formHtml += `</div></div>`;
        listaEditor.insertAdjacentHTML('beforeend', formHtml);
    });

    if (typeof Sortable !== 'undefined') {
        new Sortable(listaEditor, {
            animation: 150,
            handle: '.card-header',
            onEnd: function (evt) {
                const itemOriginal = bloquesLanding.splice(evt.oldIndex, 1)[0];
                bloquesLanding.splice(evt.newIndex, 0, itemOriginal);
                renderizarPreview();
            },
        });
    }
    renderizarPreview();
}

function renderizarPreview() {
    const preview = document.querySelector("#livePreview");
    if (!preview) return;

    let htmlVisualLanding = "";
    let htmlAvisoPopup = "";

    bloquesLanding.forEach(b => {

        // --- HELPER PARA RUTAS CORREGIDO ---
        let imgPath = '';
        if (b.fileUrl) {
            // Si ya es Base64 (data:) o link externo, se usa tal cual.
            // Si no, se le concatena la ruta del servidor Assets/images/uploads/
            imgPath = (b.fileUrl.startsWith('data:') || b.fileUrl.startsWith('http'))
                ? b.fileUrl
                : base_url + '/Assets/images/uploads/' + b.fileUrl;
        }

        // --- BLOQUE HERO ---
        if (b.tipo === 'hero') {
            htmlVisualLanding += `
                <section style="background:${b.bg || '#fff'}; color:${b.txt || '#000'}; padding:${b.padding || 60}px 20px; text-align:center; border-radius:20px; margin-bottom:20px;">
                    <h1 style="font-size:32px; font-weight:900; margin-bottom:15px;">${b.titulo || ''}</h1>
                    <p style="font-size:16px; opacity:0.8;">${b.subtitulo || ''}</p>
                    <a href="${b.btnLink || '#'}" target="_blank" style="display:inline-block; margin-top:20px; background:${b.btn || '#000'}; color:#fff; padding:10px 25px; border-radius:50px; text-decoration:none; font-weight:bold;">
                        ${b.btnTxt || 'Saber más'}
                    </a>
                </section>`;
        }

        // --- BLOQUE BANNER ---
        else if (b.tipo === 'banner') {
            let src = imgPath || 'https://via.placeholder.com/1200x400?text=Suba+una+Imagen';
            htmlVisualLanding += `
                <div style="width:100%; height:${b.alto || 300}px; background: url('${src}') center/cover no-repeat; margin-bottom:20px; border-radius:10px; border: 1px solid #eee;">
                </div>`;
        }

        // --- BLOQUE MULTIMEDIA ---
        else if (b.tipo === 'media') {
            if (!imgPath) {
                htmlVisualLanding += `<div style="padding:40px; text-align:center; background:#eee; margin-bottom:20px; border-radius:10px; border:2px dashed #ccc; color:#777;">[ Espacio para ${b.tipoMedia} ]</div>`;
            } else if (b.tipoMedia === 'video' || b.tipo === 'video') {
                htmlVisualLanding += `
                <div style="margin-bottom:20px; background:#000; border-radius:10px; overflow:hidden;">
                    <video controls playsinline style="width:100%; display:block; max-height:450px;"><source src="${imgPath}" type="video/mp4"></video>
                </div>`;
            } else {
                htmlVisualLanding += `<div style="margin-bottom:20px;"><img src="${imgPath}" style="width:100%; height:auto; border-radius:10px; display:block;"></div>`;
            }
        }

        // --- BLOQUE CARDS ---
        else if (b.tipo === 'cards') {
            const cardsItems = b.items || [];
            htmlVisualLanding += `
                <section style="padding:30px 0; margin-bottom:20px;">
                    <h2 style="text-align:center; margin-bottom:25px; font-weight:700;">${b.tituloCard || ''}</h2>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
                        ${cardsItems.map(item => `
                            <div style="background:${b.bgCard || '#fff'}; color:${b.txtCard || '#000'}; padding:25px; border-radius:15px; text-align:center; border:1px solid rgba(0,0,0,0.05);">
                                <i class="${item.icono}" style="font-size:28px; margin-bottom:12px; display:block;"></i>
                                <h4 style="font-size:17px; margin-bottom:8px; font-weight:bold;">${item.titulo}</h4>
                                <p style="font-size:13px; opacity:0.9; margin:0;">${item.desc}</p>
                            </div>
                        `).join('')}
                    </div>
                </section>`;
        }

        // --- BLOQUE CTA ---
        else if (b.tipo === 'cta') {
            htmlVisualLanding += `
            <section style="background:#f8f9fc; padding:40px 20px; text-align:center; border-radius:15px; border-bottom:5px solid ${b.color || '#000'}; margin-bottom:20px;">
                <h3 style="color:#333; font-weight:800; margin-bottom:20px; font-size:24px;">${b.texto || ''}</h3>
                <a href="${b.btnLink || '#'}" target="_blank" style="display:inline-block; background:${b.color || '#000'}; color:white; padding:14px 30px; border-radius:10px; text-decoration:none; font-weight:bold;">
                    ${b.btnTxt || 'Click aquí'}
                </a>
            </section>`;
        }

        // --- BLOQUE POPUP (Aviso editor) ---
        else if (b.tipo === 'popup') {
            let imgPop = b.fileUrl ? `<img src="${imgPath}" style="width:80px; height:auto; margin:10px auto; display:block; border-radius:5px;">` : "";
            htmlAvisoPopup += `
            <div id="aviso-popup-editor" style="border: 2px dashed #3498db; background: #ebf5fb; padding: 15px; text-align: center; border-radius: 10px; margin-bottom: 20px;">
                <i class="fas fa-window-restore" style="color: #3498db; font-size: 20px;"></i>
                <p style="margin: 0; color: #3498db; font-weight: bold;">Popup: ${b.titulo || 'Sin título'}</p>
                ${imgPop}
                <small class="text-muted">Este elemento no se mostrará en el cuerpo de la Landing.</small>
            </div>`;
        }

        // --- BLOQUE RULETA (Aviso editor + Config) ---
        else if (b.tipo === 'ruleta') {
            htmlAvisoPopup += `
            <div id="aviso-ruleta-editor" style="border: 2px dashed #e67e22; background: #fef5e7; padding: 15px; text-align: center; border-radius: 10px; margin-bottom: 20px;">
                <i class="fas fa-dharmachakra fa-spin" style="color: #e67e22; font-size: 24px;"></i>
                <p style="margin: 5px 0 0 0; color: #e67e22; font-weight: bold;">Ruleta Activa</p>
            </div>`;

            const segmentosJson = JSON.stringify(b.segmentos || []).replace(/'/g, "&apos;");
            htmlVisualLanding += `<div id="data-ruleta-config" data-titulo="${b.titulo || ''}" data-segmentos='${segmentosJson}' style="display:none;"></div>`;
        }
    });

    // Renderizamos combinados en el preview
    preview.innerHTML = htmlAvisoPopup + htmlVisualLanding;

    // Guardamos SOLO el visual en el input oculto
    const inputHtml = document.querySelector("#txtContenidoHtml");
    if (inputHtml) {
        inputHtml.value = htmlVisualLanding;
    }

    preview.querySelectorAll('video').forEach(v => v.load());
}

function insertarPlantilla(tipo) {
    agregarNuevoBloque(tipo);
}