
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
    let ajaxUrl = base_url + '/Carrito/updateCarrito';
    let formData = new FormData();
    formData.append('id', idp);
    formData.append('action', action);
    
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("POST", ajaxUrl, true);
    request.send(formData);

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            try {
                let objData = JSON.parse(request.responseText);
                if (objData.status) {
                    
                    // 1. ELIMINACIÓN (Si llega a 0)
                    if(objData.deleted) {
                        if($('#row-'+idp).length > 0){
                            $('#row-'+idp).fadeOut(300, function(){
                                $(this).remove();
                                if($('.item-carrito').length == 0) location.reload();
                            });
                        }
                        // Animación para el modal
                        $('.clase-subtotal-modal-'+idp).closest('li').fadeOut(300, function(){
                            $(this).remove();
                            if($('.header-cart-item').length == 0) $('#modalCarrito').html(objData.htmlCarrito);
                        });
                    }

                    // 2. ACTUALIZAR EL MODAL
                    if($('#modalCarrito').length > 0) $('#modalCarrito').html(objData.htmlCarrito);
                    
                    // Actualizar texto instantáneo "6 x COP120.000"
                    if(!objData.deleted && $('.clase-subtotal-modal-'+idp).length > 0){
                        if($('#cant-modal-'+idp).length > 0) $('#cant-modal-'+idp).html(objData.cantProducto);
                        let precioUnitario = objData.precioUnitario ? objData.precioUnitario : objData.subtotalProducto;
                        $('.clase-subtotal-modal-'+idp).html(objData.cantProducto + " x " + precioUnitario);
                    }

                    // 3. CORRECCIÓN DE LA BURBUJA (Header)
                    // Actualizamos el número en todos los posibles lugares donde CozaStore lo guarda
                    if($('.cantCarrito').length > 0) $('.cantCarrito').html(objData.cantCarrito);
                    
                    // Esto es lo que hace que cambie el círculo rojo en el icono del carrito
                    $('.js-show-cart').attr('data-notify', objData.cantCarrito); 

                    // 4. ACTUALIZAR TABLA DE PAGO (Página principal)
                    if(!objData.deleted && $('.subtotal-'+idp).length > 0){
                        let htmlPrecio = "";
                        if(objData.precioTachado && objData.precioTachado !== objData.subtotalProducto) {
                             htmlPrecio += `<span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;">${objData.precioTachado}</span>`;
                        }
                        htmlPrecio += `<span style="color: #f3635a; font-weight: bold;">${objData.subtotalProducto}</span>`;
                        $('.subtotal-'+idp).html(htmlPrecio);
                    }

                    // 5. ACTUALIZAR TOTALES GENERALES
                    if($('#subtotalCarrito').length > 0) $('#subtotalCarrito').html(objData.subtotalGeneral);

                    let costoEnvio = parseFloat($('#txtCostoEnvio').val()) || 0;
                    let totalConEnvio = objData.totalFinalNum + costoEnvio;
                    
                    let totalFormateado = "$" + totalConEnvio.toLocaleString('es-CO', {
                        minimumFractionDigits: 0, 
                        maximumFractionDigits: 0
                    });

                    if($('#totalCompra').length > 0) $('#totalCompra').html(totalFormateado);
                    if($('#totalFinalCompra').length > 0) $('#totalFinalCompra').html(totalFormateado);

                }
            } catch (e) { console.error("Error al sincronizar burbuja:", e); }
        }
    }
}



function fntDelItem(idProducto) {
    const base_url = document.body.getAttribute('data-baseurl'); // Asegúrate de tener base_url
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
                // Actualiza burbuja y contenido
                $('.js-show-cart').attr('data-notify', objData.cantCarrito);
                $('#modalCarrito').html(objData.htmlCarrito);
                
                if(typeof fntInitCanvasScroll === 'function') fntInitCanvasScroll();
                
                // Mensaje elegante sin recargar
                swal("Joyero", "Producto eliminado", "success");
            }
        }
    });
}
