
/**
 * Función Principal para agregar productos
 * @param {int} id - ID del producto
 * @param {boolean} checkout - Si es true, redirecciona al pago tras agregar
 */
function fntAddCarrito(id, checkout = false) {
    // Definimos base_url desde el atributo del body (asegúrate que tu body lo tenga)
    const base_url = document.body.getAttribute('data-baseurl');
    let cant = document.querySelector('#cant-product') ? document.querySelector('#cant-product').value : 1;
    let selectColor = document.querySelector('#listColor');
    let color = "";

    // 1. Validación de cantidad
    if (isNaN(cant) || cant < 1) {
        swal("Atención", "La cantidad debe ser mayor a 0.", "warning");
        return;
    }

    // 2. Validación de Color (Solo si el selector existe y es visible)
    if (selectColor && $("#containerColor").is(':visible')) {
        color = selectColor.value;
        if (color == "") {
            swal("Atención", "Por favor, selecciona un color para tu joya.", "warning");
            return;
        }
    }

    let formData = new FormData();
    formData.append('id', id);
    formData.append('cant', cant);
    formData.append('color', color);

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Carrito/addCarrito';

    request.open("POST", ajaxUrl, true);
    request.send(formData);

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            try {
                let objData = JSON.parse(request.responseText);
                
                if (objData.status) {
                    if (checkout) {
                        // Si es "Comprar Ahora", vamos directo al pago
                        window.location.href = base_url + '/carrito/procesarpago';
                    } else {
                        // Notificación de éxito
                        swal("¡Excelente!", "Producto añadido al carrito", "success");
                        
                        // 1. Actualiza el número de la burbuja (notificación)
                        $('.js-show-cart').attr('data-notify', objData.cantCarrito);
                        
                        // 2. Actualiza el contenido visual del carrito lateral
                        if (objData.htmlCarrito) {
                            // Reemplazamos el contenido del contenedor
                            // Si el HTML recibido ya trae el id="modalCarrito", usamos replaceWith
                            if ($('#modalCarrito').length > 0) {
                                $('#modalCarrito').replaceWith(objData.htmlCarrito);
                            } else {
                                // Si no, lo insertamos en el panel lateral que usualmente envuelve al carrito
                                $('.header-cart-content').html(objData.htmlCarrito);
                            }
                            
                            // 3. Reiniciar el scroll personalizado si existe
                            if(typeof fntInitCanvasScroll === 'function') fntInitCanvasScroll();
                        }
                        
                        // Cerrar modal de vista rápida si estaba abierto
                        $('.js-modal1').removeClass('show-modal1'); 
                    }
                } else {
                    swal("Atención", objData.msg, "error");
                }
            } catch (e) {
                // Si llegamos aquí es porque el JSON está mal formado o hubo un error de servidor
                console.error("Error al procesar la respuesta:", e);
            }
        }
    }
}

function fntUpdateQty(idp, action) {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Carrito/updateCarrito';
    let formData = new FormData();
    formData.append('id', idp);
    formData.append('action', action);
    request.open("POST", ajaxUrl, true);
    request.send(formData);

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if (objData.status) {
                $('.js-show-cart').attr('data-notify', objData.cantCarrito);
                $('#modalCarrito').html(objData.htmlCarrito);
                if(typeof fntInitCanvasScroll === 'function') fntInitCanvasScroll();
                
                if (document.querySelector("#btnFinalizarPedido")) {
                    location.reload();
                }
            }
        }
    }
}

function fntDelItem(idProducto) {
    let ajaxUrl = base_url + '/Carrito/delCarrito';
    let formData = new FormData();
    formData.append('id', idProducto);

    $.ajax({
        type: "POST",
        url: ajaxUrl,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            let objData = JSON.parse(response);
            if (objData.status) {
                $('.js-show-cart').attr('data-notify', objData.cantCarrito);
                $('#modalCarrito').html(objData.htmlCarrito);
                if(typeof fntInitCanvasScroll === 'function') fntInitCanvasScroll();
                swal("Joyero", "Producto eliminado", "success");
                if (document.querySelector("#btnFinalizarPedido")) location.reload();
            }
        }
    });
}