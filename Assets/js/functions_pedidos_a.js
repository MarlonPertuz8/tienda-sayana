let tablePedidosA;

document.addEventListener('DOMContentLoaded', function () {
    // Inicialización de DataTable
    if (document.querySelector("#tablePedidosA")) {
        tablePedidosA = $('#tablePedidosA').dataTable({
            "aProcessing": true,
            "aServerSide": true,
            "language": { "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" },
            "ajax": {
                "url": base_url + "/PedidosA/getPedidosA",
                "dataSrc": ""
            },
            "columns": [
                { "data": "idpedido" },
                { "data": "fecha" },
                { "data": "cliente" },
                { "data": "monto" },
                { "data": "status" },
                { "data": "options" }
            ],
            "responsive": true,
            "bDestroy": true,
            "iDisplayLength": 10,
            "order": [[0, "desc"]]
        });
    }
});

/**
 * Cambia el estado del pedido desde el listado general
 */
function fntCambiarStatus(idpedido, status) {
    Swal.fire({
        title: "Cambiar Estado",
        text: "¿Realmente desea actualizar el estado del pedido?",
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Sí, cambiar",
        cancelButtonText: "No, cancelar",
        confirmButtonColor: "#c9a050",
        cancelButtonColor: "#5d6d7e"
    }).then((result) => {
        if (result.isConfirmed) {
            let ajaxUrl = base_url + '/Pedidos/setConfirmarPedido';
            let formData = new FormData();
            formData.append('idpedido', idpedido);
            formData.append('status', status);

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(objData => {
                if (objData.status) {
                    Swal.fire("¡Listo!", objData.msg, "success");
                    if (tablePedidosA) tablePedidosA.api().ajax.reload();
                } else {
                    Swal.fire("Error", objData.msg, "error");
                }
            })
            .catch(error => {
                Swal.fire("Error", "No se pudo conectar con el servidor", "error");
            });
        }
    });
}

/**
 * Guarda el número de guía de despacho nacional
 * Esta función DEBE estar fuera de cualquier otra función para ser global
 */
function fntGuardarGuia(idpedido) {
    const inputGuia = document.querySelector("#txtGuia");
    
    if (!inputGuia) {
        console.error("El elemento #txtGuia no existe en el DOM.");
        return;
    }

    let guia = inputGuia.value.trim();

    if (guia == "") {
        Swal.fire("Atención", "Escriba el número de guía antes de guardar.", "warning");
        return false;
    }

    // Mostrar loading si tienes el div configurado
    if (typeof divLoading !== 'undefined') divLoading.style.display = "flex";

    let ajaxUrl = base_url + '/PedidosA/setGuia';
    let formData = new FormData();
    formData.append('idpedido', idpedido);
    formData.append('guia', guia);

    fetch(ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status) {
            Swal.fire("Éxito", data.msg, "success").then(() => {
                location.reload(); 
            });
        } else {
            Swal.fire("Error", data.msg || "No se pudo guardar la guía", "error");
        }
        if (typeof divLoading !== 'undefined') divLoading.style.display = "none";
    })
    .catch(error => {
        console.error("Error en fetch:", error);
        Swal.fire("Error", "Ocurrió un error en el servidor", "error");
        if (typeof divLoading !== 'undefined') divLoading.style.display = "none";
    });
}