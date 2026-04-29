
document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.body.getAttribute('data-baseurl');
    const inputCupon = document.querySelector('#txtCupon');
    const btnCupon = document.querySelector('#btnAplicarCupon');

    if (inputCupon) {
        inputCupon.addEventListener('input', function () {
            if (this.value.trim().length > 0) {
                // Si hay texto, mostramos el botón
                btnCupon.classList.add('btn-cupon-visible');
            } else {
                // Si está vacío, lo ocultamos
                btnCupon.classList.remove('btn-cupon-visible');
            }
        });
    }

    // --- 1. GESTIÓN DE NOTIFICACIONES ---
    // Seleccionamos tanto la campana de móvil como la de escritorio
    const btnNotif = $('.js-show-notifications');

    if (btnNotif.length > 0) {
        btnNotif.on('click', function (e) {
            e.preventDefault();

            $('.js-panel-notif').addClass('show-header-cart');
            fntGetNotificaciones();
            fntMarcarNotificacionesLeidas();

            setTimeout(() => {
                fntGetNotificacionesCount();
            }, 500);
        });
    }

    // Botón para cerrar (la 'X' dentro del panel o el fondo oscuro)
    if (document.querySelector('.js-hide-notif')) {
        $('.js-hide-notif').on('click', function () {
            $('.js-panel-notif').removeClass('show-header-cart');
        });
    }

    // --- 2. SELECCIÓN DE MÉTODOS DE PAGO (CORREGIDO) ---
    const paymentRadios = document.querySelectorAll('input[name="payment-method"]');
    const bankDetails = document.getElementById('bank-details');
    const inputHiddenPago = document.querySelector('#intTipopago');

    if (paymentRadios.length > 0) {
        paymentRadios.forEach((radio) => {
            radio.addEventListener("change", function () {
                // Actualizamos el valor del hidden inmediatamente
                if (inputHiddenPago) {
                    inputHiddenPago.value = this.value;
                }

                // Mostramos u ocultamos el cuadro de transferencia
                if (bankDetails) {
                    // Si el valor es 2 (Transferencia), se muestra
                    bankDetails.style.display = (this.value == "2") ? 'block' : 'none';
                }

                console.log("Método seleccionado: ", this.value);
            });
        });
    }

    // --- 3. BOTÓN MIS PEDIDOS (PERFIL CLIENTE) ---
    let btnPedidos = document.querySelector(".btnMisPedidos");
    if (btnPedidos) {
        btnPedidos.onclick = function (e) {
            e.preventDefault();
            let contenedor = document.querySelector('#renderPerfil');
            if (contenedor) {
                contenedor.style.opacity = "0.5";
                let ajaxUrl = base_url + '/clientes/getPedidosTab';
                let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                request.open("GET", ajaxUrl, true);
                request.send();
                request.onreadystatechange = function () {
                    if (request.readyState == 4 && request.status == 200) {
                        contenedor.innerHTML = request.responseText;
                        contenedor.style.opacity = "1";
                        document.querySelectorAll(".nav-link-sayana").forEach(el => el.classList.remove("active"));
                        btnPedidos.classList.add("active");
                    }
                }
            }
        }
    }

    if (document.querySelector("#btnAplicarCupon")) {
        let btnCupon = document.querySelector("#btnAplicarCupon");

        btnCupon.addEventListener('click', function (e) {
            e.preventDefault();

            let strCupon = document.querySelector("#txtCupon").value;

            if (strCupon == "") {
                swal("Atención", "Escribe un código de cupón.", "warning");
                return false;
            }

            const base_url = document.body.getAttribute('data-baseurl');
            let ajaxUrl = base_url + '/Carrito/aplicarCupon';

            let formData = new FormData();
            formData.append('cupon', strCupon);

            btnCupon.disabled = true;
            btnCupon.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(objData => {

                    let msgCupon = document.querySelector("#msgCupon");

                    if (objData.status) {

                        msgCupon.style.display = "block";
                        msgCupon.innerHTML = `<span style="color: #28a745;">${objData.msg}</span>`;

                        let totalLimpio = objData.total_descuento_format
                            .replace(/\./g, '')   // quita separadores de miles
                            .replace(',', '.')    // convierte decimal a punto
                            .replace(/[^0-9.]/g, ''); // limpia $

                        totalLimpio = parseFloat(totalLimpio);

                        // 🔥 ACTUALIZAMOS SUBTOTAL CORRECTAMENTE
                        let elSubtotal = document.querySelector("#subtotalCarrito");
                        elSubtotal.innerHTML = objData.total_descuento_format;
                        elSubtotal.setAttribute('data-value', totalLimpio);

                        // 🔥 RECALCULAR TOTAL
                        let costoEnvioActual = parseFloat(document.querySelector("#txtCostoEnvio")?.value || 0);
                        actualizarTotalPedido(costoEnvioActual);

                        // Animación
                        const elTotal = document.querySelector("#totalCarrito");
                        if (elTotal) {
                            elTotal.style.transition = "transform 0.3s ease, color 0.3s ease";
                            elTotal.style.color = "#28a745";
                            elTotal.style.transform = "scale(1.1)";
                            setTimeout(() => {
                                elTotal.style.transform = "scale(1)";
                                elTotal.style.color = "#f3635a";
                            }, 400);
                        }

                        swal("¡Excelente!", objData.msg, "success");

                    } else {
                        msgCupon.style.display = "block";
                        msgCupon.innerHTML = `<span style="color: #f3635a;">${objData.msg}</span>`;
                        swal("Error", objData.msg, "error");
                    }

                    btnCupon.disabled = false;
                    btnCupon.innerHTML = 'Aplicar';
                })
                .catch(error => {
                    console.error("Error:", error);
                    btnCupon.disabled = false;
                    btnCupon.innerHTML = 'Aplicar';
                });
        });
    }

    fntGetNotificacionesCount();
});


function fntGetNotificacionesCount() {
    const base_url = document.body.getAttribute('data-baseurl');
    let ajaxUrl = base_url + '/Tienda/getNotificacionesCount';
    let request = new XMLHttpRequest();

    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            try {
                if (request.responseText.trim().startsWith('{')) {
                    let objData = JSON.parse(request.responseText);
                    const campanas = document.querySelectorAll('.js-show-notifications');

                    campanas.forEach(campana => {
                        let count = objData.count;

                        // Actualiza número
                        campana.setAttribute('data-notify', count);

                        // 🔥 CLAVE: manejar estilos visuales
                        if (count == 0) {
                            campana.classList.remove('icon-header-noti');
                        } else {
                            campana.classList.add('icon-header-noti');
                        }
                    });
                }
            } catch (e) {
                console.error("Error en NotificacionesCount:", e);
            }
        }
    }
}

function reproducirSonido() {
    let audio = document.getElementById("sonidoNotificacion");

    if (audio) {
        audio.currentTime = 0;
        audio.play().catch(e => {
            console.warn("El navegador bloqueó el sonido automático:", e);
        });
    }
}

function escucharNotificaciones() {
    const base_url = document.body.getAttribute('data-baseurl');

    fetch(base_url + '/Tienda/getNotificacionesCount')
        .then(res => res.json())
        .then(data => {

            if (data.status) {
                let count = data.count;

                let campanas = document.querySelectorAll('.js-show-notifications');

                campanas.forEach(campana => {
                    let actual = parseInt(campana.getAttribute('data-notify')) || 0;

                    // 🔥 SOLO SI HAY NUEVAS
                    if (count > actual) {
                        animarCampana(campana);
                        reproducirSonido();
                    }

                    campana.setAttribute('data-notify', count);

                    if (count == 0) {
                        campana.classList.remove('icon-header-noti');
                    } else {
                        campana.classList.add('icon-header-noti');
                    }
                });
            }

            // 🔁 vuelve a escuchar
            setTimeout(escucharNotificaciones, 5000);

        })
        .catch(() => {
            setTimeout(escucharNotificaciones, 8000);
        });
}

// iniciar
escucharNotificaciones();

function animarCampana(el) {
    el.classList.add('bell-anim');

    setTimeout(() => {
        el.classList.remove('bell-anim');
    }, 600);
}



function fntViewProducto(idproducto) {
    const base_url = document.body.getAttribute('data-baseurl');
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Tienda/getProducto/' + idproducto;

    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            try {
                let objData = JSON.parse(request.responseText);

                if (objData.status) {
                    // 1. CARGA DE TEXTOS
                    document.querySelector('.js-name-detail').innerHTML = objData.data.nombre;
                    document.querySelector('#descripcionProducto').innerHTML = objData.data.descripcion;
                    document.querySelector('#btnAnadirCarrito').setAttribute("onclick", "fntAddCarrito('" + objData.data.idproducto + "')");

                    // 2. LÓGICA DE PRECIOS
                    const txtPrecioOriginal = document.querySelector('#precioOriginalModal');
                    const txtPrecioFinal = document.querySelector('#precioProducto');
                    let precioBase = parseFloat(objData.data.precio);
                    let precioOferta = parseFloat(objData.data.precio_oferta);
                    let moneda = (objData.data.smoney && objData.data.smoney !== "undefined") ? objData.data.smoney : "$";

                    if (precioOferta > 0 && precioOferta < precioBase) {
                        if (txtPrecioOriginal) {
                            txtPrecioOriginal.style.display = "inline-block";
                            txtPrecioOriginal.innerHTML = moneda + precioBase.toLocaleString('de-DE', { minimumFractionDigits: 2 });
                        }
                        txtPrecioFinal.innerHTML = moneda + precioOferta.toLocaleString('de-DE', { minimumFractionDigits: 2 });
                    } else {
                        if (txtPrecioOriginal) txtPrecioOriginal.style.display = "none";
                        txtPrecioFinal.innerHTML = moneda + precioBase.toLocaleString('de-DE', { minimumFractionDigits: 2 });
                    }

                    // 3. LÓGICA DE COLORES
                    const contenedorColor = document.querySelector("#containerColor");
                    const selectColor = document.querySelector("#listColor");

                    if ($(selectColor).data('select2')) {
                        $(selectColor).select2('destroy');
                    }

                    let valorColores = objData.data.colores;

                    if (valorColores && valorColores !== "null" && valorColores.trim() !== "") {
                        let htmlColor = "<option value=''>Seleccionar color</option>";
                        let coloresArray = valorColores.split(",");
                        let contador = 0;

                        coloresArray.forEach(color => {
                            if (color.trim() !== "") {
                                htmlColor += `<option value="${color.trim()}">${color.trim()}</option>`;
                                contador++;
                            }
                        });

                        if (contador > 0) {
                            selectColor.innerHTML = htmlColor;
                            contenedorColor.style.setProperty("display", "flex", "important");
                        } else {
                            contenedorColor.style.setProperty("display", "none", "important");
                        }
                    } else {
                        contenedorColor.style.setProperty("display", "none", "important");
                    }

                    // 4. IMÁGENES (SLICK)
                    let htmlImage = "";
                    let images = objData.data.images;
                    // Ajustado al nombre exacto: default.png
                    let imgDefault = base_url + "/Assets/images/uploads/default.png";

                    if (images && images.length > 0) {
                        images.forEach(img => {
                            htmlImage += `
                            <div class="item-slick3" data-thumb="${img.url_image}">
                                <div class="wrap-pic-w pos-relative">
                                    <img src="${img.url_image}" onerror="this.src='${imgDefault}';">
                                    <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="${img.url_image}">
                                        <i class="fa fa-expand"></i>
                                    </a>
                                </div>
                            </div>`;
                        });
                    } else {
                        // Si el array está vacío, forzamos la imagen default.png
                        htmlImage = `
                        <div class="item-slick3" data-thumb="${imgDefault}">
                            <div class="wrap-pic-w pos-relative">
                                <img src="${imgDefault}">
                            </div>
                        </div>`;
                    }

                    // Insertar en el contenedor de tu modal
                    document.querySelector('#imagesProducto').innerHTML = htmlImage;

                    // Lógica para ocultar el cuadro de miniaturas si solo hay 1 imagen o ninguna
                    if (!images || images.length <= 1) {
                        document.querySelector('.wrap-slick3-dots').style.setProperty('display', 'none', 'important');
                    } else {
                        document.querySelector('.wrap-slick3-dots').style.setProperty('display', 'block', 'important');
                    }

                    if ($('.slick3').hasClass('slick-initialized')) $('.slick3').slick('unslick');
                    $('.slick3').html(htmlImage);
                    $('.slick3').slick({
                        slidesToShow: 1, slidesToScroll: 1, fade: true,
                        infinite: true, arrows: true,
                        appendArrows: $('.wrap-slick3-arrows'),
                        prevArrow: '<button class="arrow-slick3 prev-slick3"><i class="fa fa-angle-left"></i></button>',
                        nextArrow: '<button class="arrow-slick3 next-slick3"><i class="fa fa-angle-right"></i></button>',
                        dots: true, appendDots: $('.wrap-slick3-dots'),
                        dotsClass: 'slick3-dots',
                        customPaging: function (slick, index) {
                            var portrait = $(slick.$slides[index]).data('thumb');
                            return '<img src="' + portrait + '"/><div class="slick3-dot-overlay"></div>';
                        },
                    });

                    // 5. MOSTRAR MODAL E INICIALIZAR SELECT2 CORREGIDO
                    $('.js-modal1').addClass('show-modal1');

                    setTimeout(() => {
                        $(selectColor).select2({
                            dropdownParent: $(selectColor).parent(), // Mantiene el anclaje al div gris
                            minimumResultsForSearch: Infinity,
                            width: '100%',
                            dir: 'ltr' // Fuerza la dirección estándar
                        });

                        // Forzamos que Select2 no calcule posiciones "smart" que lo suban
                        $(selectColor).on('select2:open', function (e) {
                            $('.select2-dropdown').hide();
                            setTimeout(function () {
                                $('.select2-dropdown').fadeIn(200);
                            }, 10);
                        });
                    }, 450);

                }
            } catch (e) {
                console.error("Error en JS:", e);
            }
        }
    }
}

// Función para asegurar que no se vea NADA de la sección color
function ocultarSeccionColor(contenedor, select) {
    if (contenedor) {
        contenedor.style.setProperty("display", "none", "important");
    }
    if (select) {
        select.innerHTML = "";
    }
}

/* =============================================================================
   LOGUEO DESDE LA TIENDA (CORREGIDO PARA SAYANA LUXURY)
============================================================================= */
if (document.querySelector("#formLogin")) {
    let formLogin = document.querySelector("#formLogin");
    formLogin.onsubmit = function (e) {
        e.preventDefault();

        // Usamos los IDs de tu diseño de Sayana Luxury
        let strEmail = document.querySelector("#txtEmail").value;
        let strPassword = document.querySelector("#txtPassword").value;

        if (strEmail == "" || strPassword == "") {
            swal("Atención", "Escriba email y contraseña.", "error");
            return false;
        }

        let divLoading = document.querySelector("#divLoading");
        if (divLoading) { divLoading.style.display = "flex"; }

        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + '/Login/loginUser';
        let formData = new FormData(formLogin);

        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                try {
                    let objData = JSON.parse(request.responseText);

                    if (objData.status) {
                        /* LÓGICA DE REDIRECCIÓN INTELIGENTE:
                           Si el usuario tiene rol de cliente, recargamos la tienda.
                           Si es administrador o empleado, lo mandamos al dashboard.
                        */
                        if (objData.role == 'cliente') {
                            window.location.reload();
                        } else {
                            window.location = base_url + '/dashboard';
                        }
                    } else {
                        // Aviso cualitativo de error (Riesgo alto de acceso fallido)
                        swal("Atención", objData.msg, "error");
                        document.querySelector('#txtPassword').value = "";
                        if (divLoading) { divLoading.style.display = "none"; }
                    }
                } catch (error) {
                    console.error("Error al procesar la respuesta del servidor", error);
                    if (divLoading) { divLoading.style.display = "none"; }
                }
            }
        }
    }
}

if (document.querySelector("#formRegistro")) {
    let formRegistro = document.querySelector("#formRegistro");
    formRegistro.onsubmit = function (e) {
        e.preventDefault();
        let strNombre = document.querySelector("#txtNombre").value;
        let strApellido = document.querySelector("#txtApellido").value;
        let strEmail = document.querySelector("#txtEmail").value;
        // 1. CAPTURAMOS EL VALOR DEL NUEVO CAMPO
        let strTelefono = document.querySelector("#txtTelefonoRegistro").value;

        // 2. LO AÑADIMOS A LA VALIDACIÓN OBLIGATORIA
        if (strNombre == "" || strApellido == "" || strEmail == "" || strTelefono == "") {
            swal("Atención", "Todos los campos son obligatorios.", "error");
            return false;
        }

        let divLoading = document.querySelector("#divLoading");
        if (divLoading) {
            divLoading.style.display = "flex";
        }

        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + '/Carrito/procesarRegistro';

        // 3. EL FormData AUTOMÁTICAMENTE TOMARÁ EL VALOR DE "txtTelefonoRegistro" 
        // Siempre que el "name" en el HTML coincida.
        let formData = new FormData(formRegistro);

        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);
                if (objData.status) {
                    window.location.reload();
                } else {
                    swal("Atención", objData.msg, "error");
                    if (divLoading) {
                        divLoading.style.display = "none";
                    }
                }
            }
            if (request.readyState == 4 && divLoading) {
                divLoading.style.display = "none";
            }
        }
    }
}
/* =============================================================================
   VALIDACIÓN Y ACTUALIZACIÓN DE PERFIL CLIENTE
============================================================================= */
if (document.querySelector("#formPerfilCliente")) {
    let formPerfilCliente = document.querySelector("#formPerfilCliente");

    formPerfilCliente.onsubmit = function (e) {
        e.preventDefault();

        // Captura de campos
        let strNombre = document.querySelector("#txtNombre").value.trim();
        let strApellido = document.querySelector("#txtApellido").value.trim();

        // --- VALIDACIONES DE ENTRADA ---

        // 1. Validación de campos vacíos (Riesgo: Alto para la integridad del perfil)
        if (strNombre == "" || strApellido == "") {
            swal("Atención", "Todos los campos marcados son obligatorios.", "error");
            return false;
        }

        // 2. Validación de formato (Solo letras para nombres y apellidos)
        let regExpNombre = /^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/;
        if (!regExpNombre.test(strNombre) || !regExpNombre.test(strApellido)) {
            swal("Atención", "El nombre y apellido solo pueden contener letras.", "warning");
            return false;
        }

        // 3. Validación de longitud mínima
        if (strNombre.length < 3 || strApellido.length < 3) {
            swal("Atención", "El nombre y apellido deben tener al menos 3 caracteres.", "info");
            return false;
        }

        // --- PROCESO DE ENVÍO ---

        let divLoading = document.querySelector("#divLoading");
        if (divLoading) { divLoading.style.display = "flex"; }

        const base_url = document.body.getAttribute('data-baseurl');
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + '/Clientes/putPerfil';
        let formData = new FormData(formPerfilCliente);

        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                try {
                    let objData = JSON.parse(request.responseText);

                    if (objData.status) {
                        // SWAL DE ÉXITO: Confirmación cualitativa para el cliente
                        swal({
                            title: "¡Actualizado!",
                            text: objData.msg,
                            type: "success",
                            confirmButtonText: "Excelente",
                            closeOnConfirm: true
                        }, function (isConfirm) {
                            if (isConfirm) {
                                location.reload(); // Recarga para actualizar el saludo en el header
                            }
                        });
                    } else {
                        // Error devuelto por el servidor (ej. sesión expirada)
                        swal("Error", objData.msg, "error");
                    }
                } catch (error) {
                    console.error("Error en la respuesta JSON", error);
                    swal("Error", "No se pudo procesar la solicitud. Intente más tarde.", "error");
                }
            }

            if (request.readyState == 4 && divLoading) {
                divLoading.style.display = "none";
            }
        }
    }
}
if (document.querySelector("#btnFinalizarPedido")) {
    let btnFinalizar = document.querySelector("#btnFinalizarPedido");

    btnFinalizar.addEventListener('click', function (e) {
        e.preventDefault();

        let intTipopago = document.querySelector("#intTipopago").value;
        let elDireccion = document.querySelector("#txtDireccion");
        let elCiudad = document.querySelector("#txtCiudad");
        let elBarrio = document.querySelector("#listBarrio");
        let tipoEnvio = document.querySelector("#tipoEnvio").value;

        // Capturamos los elementos del botón para el loader
        let btnText = document.querySelector("#btnText");
        let btnLoader = document.querySelector("#btnLoader");

        // 1. VALIDACIÓN MÉTODO DE PAGO
        if (intTipopago == "") {
            swal("Atención", "Debe seleccionar un método de pago.", "warning");
            return false;
        }

        // 2. VALIDACIÓN SEGÚN TIPO DE ENVÍO
        let costoEnvio = 0;
        if (tipoEnvio == "1") {
            if (elBarrio.value == "") {
                swal("Atención", "Por favor, seleccione su barrio en Cartagena.", "warning");
                return false;
            }
            let selectedOption = elBarrio.options[elBarrio.selectedIndex];
            costoEnvio = selectedOption.getAttribute('data-costo') || 0;
            elCiudad.value = "Cartagena";
        } else {
            let inputCiudadNacional = document.querySelector("#txtCiudadNacional");
            if (inputCiudadNacional) {
                elCiudad.value = inputCiudadNacional.value.trim();
            }
            if (elCiudad.value == "" || elCiudad.value.toLowerCase() == "cartagena") {
                swal("Atención", "Por favor, escriba la ciudad de destino para el envío nacional.", "warning");
                return false;
            }
            costoEnvio = 0;
        }

        // 3. VALIDACIÓN DIRECCIÓN
        if (elDireccion.value.trim() == "") {
            swal("Atención", "Por favor, complete la dirección de entrega.", "warning");
            return false;
        }

        // === ACTIVAR LOADER EN EL BOTÓN ===
        btnFinalizar.disabled = true;
        btnFinalizar.style.opacity = "0.7";
        if (btnText) btnText.style.display = "none";
        if (btnLoader) btnLoader.style.display = "inline-block";

        let divLoading = document.querySelector("#divLoading");
        if (divLoading) divLoading.style.display = "flex";

        const base_url = document.body.getAttribute('data-baseurl');
        let ajaxUrl = base_url + '/Carrito/procesarPedido';

        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let formData = new FormData();

        formData.append('intTipopago', intTipopago);
        formData.append('txtDireccion', elDireccion.value.trim());
        formData.append('txtCiudad', elCiudad.value.trim());
        formData.append('listBarrio', elBarrio.value);
        formData.append('txtCostoEnvio', costoEnvio);

        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                let res = request.responseText;
                let firstBrace = res.indexOf('{');
                let lastBrace = res.lastIndexOf('}');

                if (firstBrace !== -1 && lastBrace !== -1) {
                    let cleanJson = res.substring(firstBrace, lastBrace + 1);
                    try {
                        let objData = JSON.parse(cleanJson);
                        if (objData.status) {
                            window.location = base_url + "/carrito/confirmacion?p=" + objData.orden;
                        } else {
                            swal("Error", objData.msg, "error");
                            // Revertir botón si hay error
                            resetBtnFinalizar(btnFinalizar, btnText, btnLoader);
                        }
                    } catch (error) {
                        swal("Error", "Ocurrió un error al procesar la respuesta.", "error");
                        resetBtnFinalizar(btnFinalizar, btnText, btnLoader);
                    }
                }
            }
            if (request.readyState == 4) {
                if (divLoading) divLoading.style.display = "none";
            }
        }
    });
}

// Función auxiliar para resetear el botón si algo sale mal
function resetBtnFinalizar(btn, text, loader) {
    btn.disabled = false;
    btn.style.opacity = "1";
    if (text) text.style.display = "inline-block";
    if (loader) loader.style.display = "none";
}

// Lógica para cambiar entre Local (Cartagena) y Nacional
if (document.querySelector("#tipoEnvio")) {
    let selectEnvio = document.querySelector("#tipoEnvio");

    selectEnvio.addEventListener('change', function () {
        const sectionCartagena = document.querySelector('#sectionCartagena');
        const sectionNacional = document.querySelector('#sectionNacional');
        const displayCosto = document.querySelector('#displayCostoEnvio');
        const inputCiudad = document.querySelector('#txtCiudad');

        // El contenedor del método de pago contra entrega
        // Buscamos el radio y luego subimos a su contenedor principal (.method-item)
        const optContraEntrega = document.querySelector("#method-delivery");
        const containerContraEntrega = optContraEntrega ? optContraEntrega.closest('.method-item') : null;
        const radioWompi = document.querySelector("#method-wompi");

        if (this.value == "1") {
            // --- CASO: CARTAGENA ---
            sectionCartagena.style.display = "block";
            sectionNacional.style.display = "none";
            inputCiudad.value = "Cartagena";
            displayCosto.innerHTML = "$ 0";

            // MOSTRAMOS la opción de contra entrega
            if (containerContraEntrega) containerContraEntrega.style.display = "block";

        } else {
            // --- CASO: NACIONAL ---
            sectionCartagena.style.display = "none";
            sectionNacional.style.display = "block";
            inputCiudad.value = "";
            displayCosto.innerHTML = "Cobro en Destino (Coordinadora)";

            // OCULTAMOS la opción de contra entrega por completo
            if (containerContraEntrega) {
                containerContraEntrega.style.display = "none";

                // Si el usuario lo tenía marcado, forzamos el cambio a Wompi (ID 1)
                if (optContraEntrega.checked) {
                    radioWompi.checked = true;
                    document.querySelector('#intTipopago').value = 1;
                    swal("Información", "Para envíos nacionales, aceptamos pagos por Tarjeta, PSE o Transferencia. El envío lo pagas al recibir.", "info");
                }
            }

            if (document.querySelector('#txtCiudadNacional')) document.querySelector('#txtCiudadNacional').value = "";
        }
    });
}
function fntGetNotificaciones() {
    const base_url = document.body.getAttribute('data-baseurl');
    let ajaxUrl = base_url + '/tienda/getNotificacionesList';

    let request = new XMLHttpRequest();
    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            try {
                let objData = JSON.parse(request.responseText);

                if (objData.status) {
                    let htmlNotif = "";
                    let notificaciones = objData.data;

                    // --- ACTUALIZACIÓN DEL CONTADOR ---
                    let badges = document.querySelectorAll(".js-show-notifications");
                    badges.forEach(badge => {
                        let count = notificaciones.filter(n => n.status == 0 || n.leido == 0).length;
                        badge.setAttribute('data-notify', count);
                        if (count == 0) badge.classList.remove('icon-header-noti');
                        else badge.classList.add('icon-header-noti');
                    });

                    // --- GENERACIÓN DE LA LISTA DINÁMICA ---
                    if (notificaciones.length > 0) {
                        notificaciones.forEach(function (notif) {
                            let fecha = notif.fecha || "";
                            let mensajeFinal = notif.mensaje || notif.descripcion || "Nueva actualización";

                            // 🛠️ SOLUCIÓN DEFINITIVA: 
                            // Usamos el nuevo campo pedido_id que creaste en la DB.
                            // Si por alguna razón está vacío, intentamos extraer del texto como respaldo.
                            let idExtraido = null;
                            let match = mensajeFinal.match(/#(\d+)/);
                            if (match) idExtraido = match[1];

                            // Prioridad: 1. Campo DB -> 2. Regex -> 3. ID Notificación (fallback)
                            let realOrderId = notif.pedido_id || idExtraido || notif.idnotificacion;

                            htmlNotif += `
                            <a href="${base_url}/pedidos/orden/${realOrderId}" class="notif-item-mini" 
                               style="display: block; padding: 18px 15px; border-bottom: 1px solid #f2f2f2; text-decoration: none;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <p style="font-size: 14px; color: #333; margin: 0; line-height: 1.2;">
                                        ${mensajeFinal}
                                    </p>
                                    <small style="font-size: 10px; color: #bbb; margin-left: 10px; white-space: nowrap;">
                                        ${fecha}
                                    </small>
                                </div>
                            </a>`;
                        });
                    } else {
                        htmlNotif = '<div style="padding:20px; font-size:12px; color:#999; text-align:center;">Sin novedades</div>';
                    }

                    document.querySelectorAll("#listNotificaciones").forEach(el => {
                        el.innerHTML = htmlNotif;
                    });
                }
            } catch (e) {
                console.error("Error al parsear notificaciones:", e);
            }
        }
    }
}

fntGetNotificaciones();

function fntMarcarNotificacionesLeidas() {
    const base_url = document.body.getAttribute('data-baseurl');
    let ajaxUrl = base_url + '/tienda/marcarNotificacionesLeidas';

    fetch(ajaxUrl)
        .then(response => response.json())
        .then(objData => {
            if (objData.status) {
                let badges = document.querySelectorAll(".js-show-notifications");

                badges.forEach(badge => {
                    badge.setAttribute('data-notify', '0');

                    // 🔥 quitar efecto rojo inmediatamente
                    badge.classList.remove('icon-header-noti');
                });
            }
        })
        .catch(err => console.error("Error:", err));
}

if (document.querySelector("#listBarrio")) {
    let listBarrio = document.querySelector("#listBarrio");

    listBarrio.addEventListener('change', function () {
        // 1. Obtener el costo del atributo data-costo
        let option = this.options[this.selectedIndex];
        let costo = parseFloat(option.getAttribute('data-costo')) || 0;

        // 2. Formatear para mostrar
        let formatter = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        });

        // 3. Actualizar el texto del Envío (ID correcto: displayCostoEnvio)
        if (document.querySelector("#displayCostoEnvio")) {
            document.querySelector("#displayCostoEnvio").innerHTML = formatter.format(costo);
        }

        // 4. Actualizar el input oculto para el PHP
        if (document.querySelector("#txtCostoEnvio")) {
            document.querySelector("#txtCostoEnvio").value = costo;
        }

        // 5. Calcular y actualizar el Total Final
        actualizarTotalPedido(costo);

        console.log("Costo de envío actualizado: " + costo);
    });
}

function actualizarTotalPedido(costoEnvio) {
    const elSubtotal = document.querySelector("#subtotalCarrito");
    const elTotal = document.querySelector("#totalCarrito");

    if (elSubtotal && elTotal) {
        // Leemos el subtotal base (si ya se aplicó cupón, este valor ya viene rebajado)
        let subtotalBase = parseFloat(elSubtotal.getAttribute('data-value') || 0);
        let envio = parseFloat(costoEnvio || 0);

        let totalFinal = subtotalBase + envio;

        // Formateador de moneda colombiana
        let formatter = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        });

        // Actualizamos la vista y el atributo de valor
        elTotal.innerHTML = formatter.format(totalFinal);
        elTotal.setAttribute('data-value', totalFinal);
    }
}


document.addEventListener("click", function activarAudio() {
    let audio = document.getElementById("sonidoNotificacion");

    if (audio) {
        audio.play().then(() => {
            audio.pause();
            audio.currentTime = 0;
            console.log("🔓 audio desbloqueado");
        }).catch(e => {
            console.warn("Sigue bloqueado:", e);
        });
    }

    document.removeEventListener("click", activarAudio);
});

