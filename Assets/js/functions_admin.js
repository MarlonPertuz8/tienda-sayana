// ===============================
// EXPRESIONES
// ===============================
let lastMensajesCount = -1;
let lastPedidosCount = -1;

const regexText = /^[a-zA-Z ]+$/;
const regexNumber = /^[0-9]+$/;
const regexEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;


// ===============================
// VALIDACIÓN GENERAL
// ===============================

function validateInput(input, regex) {
    const value = input.value.trim();

    if (!regex.test(value)) {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");
    } else {
        input.classList.remove("is-invalid");
        input.classList.add("is-valid");
    }

    // 🔥 detectar el form automáticamente
    const form = input.closest("form");

    if (form) {
        checkFormValidity(form);
    }
}


// ===============================
// VERIFICAR FORM COMPLETO
// ===============================

function checkFormValidity(form) {

    if (!form) return;

    const btn = form.querySelector("#btnActionForm");

    // 🔥 SI NO HAY BOTÓN, SALIR
    if (!btn) return;

    const invalid = form.querySelectorAll(".is-invalid");
    const requiredFields = form.querySelectorAll("input[required], select[required]");

    let hasEmpty = false;

    requiredFields.forEach(input => {
        if (input.value.trim() === "") {
            hasEmpty = true;
        }
    });

    if (invalid.length > 0 || hasEmpty) {
        btn.disabled = true;
        btn.style.opacity = "0.6";
        btn.style.cursor = "not-allowed";
    } else {
        btn.disabled = false;
        btn.style.opacity = "1";
        btn.style.cursor = "pointer";
    }
}


// ===============================
// INICIALIZAR
// ===============================

window.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".validText").forEach(input => {
        input.addEventListener("input", () => validateInput(input, regexText));
    });

    document.querySelectorAll(".validNumber").forEach(input => {
        input.addEventListener("input", () => validateInput(input, regexNumber));
    });

    document.querySelectorAll(".validEmail").forEach(input => {
        input.addEventListener("input", () => validateInput(input, regexEmail));
    });

    if (window.location.pathname.includes('/contactos')) {
        // Al entrar a contactos, igualamos el contador local al del servidor
        fetch(base_url + '/Contactos/getContactosCount')
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    lastMensajesCount = parseInt(data.total);
                    // Ocultamos el badge visualmente de inmediato para que se sienta fluido
                    let badgeM = document.getElementById('badgeMensajes');
                    if (badgeM) badgeM.style.display = "none";
                }
            });
    }

    if (window.location.pathname.includes('/PedidosA')) {
        // Al entrar a Pedidos, igualamos el contador de hoy
        fetch(base_url + '/Dashboard/getUpdates')
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    lastPedidosCount = parseInt(data.pedidos_hoy);
                    let badgeP = document.getElementById('badgePedidosSidebar');
                    if (badgeP) badgeP.style.display = "none";
                }
            });
    }

});

// ===============================
// MONITOREO GLOBAL (SIDEBAR + ALERTAS)
// ===============================

// Función para reproducir el sonido manejando el bloqueo del navegador
function playNotif() {
    let audio = new Audio(base_url + '/Assets/sounds/notification.mp3');
    let playPromise = audio.play();

    if (playPromise !== undefined) {
        playPromise.then(_ => {
            console.log("Sonido reproducido correctamente");
        }).catch(error => {
            console.warn("Navegador bloqueó el audio. Haz clic en cualquier parte de la página.");
        });
    }
}

function fntCheckGlobalNotifications() {
    // 1. MONITOREO DE MENSAJES
    fetch(base_url + '/Contactos/getContactosCount')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                let totalServidor = parseInt(data.total);
                if (lastMensajesCount === -1) { lastMensajesCount = totalServidor; return; }
                if (totalServidor > lastMensajesCount) {
                    playNotif();
                    let badgeM = document.getElementById('badgeMensajes');
                    if (badgeM) {
                        badgeM.innerText = totalServidor - lastMensajesCount;
                        badgeM.style.display = "inline-block";
                    }
                    lastMensajesCount = totalServidor;
                }
            }
        });

    // 2. MONITOREO DE PEDIDOS (Corrección del 0 y Alerta Global)
    fetch(base_url + '/Dashboard/getUpdates')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                let pedidosHoy = parseInt(data.pedidos_hoy);
                if (lastPedidosCount === -1) { lastPedidosCount = pedidosHoy; return; }

                let badgeP = document.getElementById('badgePedidosSidebar');
                if (badgeP) {
                    if (pedidosHoy > 0) {
                        // SIEMPRE actualizamos con el valor real para evitar el 0 visual
                        badgeP.innerText = pedidosHoy;
                        badgeP.style.display = "inline-block";
                        
                        if (pedidosHoy > lastPedidosCount) {
                            playNotif();
                            Swal.fire({
                                icon: 'success', title: '¡Nuevo Pedido!', text: 'Venta registrada en Sayana Luxury.',
                                toast: true, position: 'top-end', showConfirmButton: false, timer: 5000
                            });
                        }
                    } else {
                        badgeP.style.display = "none";
                    }
                }
                actualizarDashboard(pedidosHoy);
                lastPedidosCount = pedidosHoy;
            }
        });

    // 3. MONITOREO DE STOCK (Cascada: Padre e Hijo)
    fetch(base_url + '/Productos/getStockCritico')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                let totalBajoStock = parseInt(data.total);
                let badgeHijo = document.getElementById('badgeStockSidebar');
                let badgePadre = document.getElementById('badgeTiendaPadre'); // El que pusimos en "Tienda"

                if (totalBajoStock > 0) {
                    // Actualizar submenú (Productos)
                    if (badgeHijo) {
                        badgeHijo.innerText = totalBajoStock;
                        badgeHijo.style.display = "inline-block";
                    }
                    // Actualizar menú principal (Tienda) para que se vea aunque esté cerrado
                    if (badgePadre) {
                        badgePadre.style.display = "inline-block";
                    }

                    // Alerta visual solo si no estamos viendo la lista de productos
                    if (!window.location.pathname.includes('/productos')) {
                        console.log("Sayana Luxury: Hay productos con stock crítico.");
                    }
                } else {
                    if (badgeHijo) badgeHijo.style.display = "none";
                    if (badgePadre) badgePadre.style.display = "none";
                }
            }
        });
}
/**
 * Actualiza la UI del Dashboard sin refrescar la página
 */
function actualizarDashboard(conteo) {
    // 1. Recarga la tabla de pedidos (solo el fragmento HTML)
    if (document.getElementById('contenedorUltimosPedidos')) {
        $("#contenedorUltimosPedidos").load(window.location.href + " #contenedorUltimosPedidos > *");
    }
    // 2. Actualiza el número grande en el widget de arriba
    let elPedidos = document.getElementById('txtPedidosTotales');
    if (elPedidos) {
        elPedidos.innerText = conteo;
    }
}

/**
 * Hace visible el badge (notificación) en el sidebar
 */
function mostrarBadge(id) {
    let badge = document.getElementById(id);
    if (badge) {
        badge.style.display = "inline-block";
        badge.style.transform = "scale(1)";
        badge.style.transition = "transform 0.3s ease-in-out";
    }
}

// Iniciar el ciclo de monitoreo cada 15 segundos
setInterval(fntCheckGlobalNotifications, 15000);