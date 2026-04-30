/*=============================================================================
    FUNCIONES DE TIENDA - SAYANA LUXURY (ESTÉTICA Y NAVEGACIÓN)
=============================================================================*/

// 1. PARCHE GLOBAL PARA AUTO-CIERRE (Debe ir al inicio)
// Esto intercepta tanto swal() como Swal.fire()
(function() {
    // Si usas SweetAlert2
    if (typeof Swal !== 'undefined' && Swal.fire) {
        const originalSwalFire = Swal.fire;
        Swal.fire = function(...args) {
            let options = args[0];
            if (typeof options === 'object' && options !== null) {
                if (options.showConfirmButton === undefined && !options.buttons) {
                    options.timer = options.timer || 2500;
                    options.timerProgressBar = true;
                    options.showConfirmButton = false;
                }
            } else if (typeof options === 'string') {
                options = {
                    title: args[0],
                    text: args[1] || "",
                    icon: args[2] || "success",
                    timer: 2500,
                    showConfirmButton: false,
                    timerProgressBar: true
                };
                return originalSwalFire.call(Swal, options);
            }
            return originalSwalFire.apply(Swal, args);
        };
    }

    // Si usas SweetAlert 1 (la función swal)
    if (typeof swal !== 'undefined') {
        const originalSwal = window.swal;
        window.swal = function(...args) {
            let options = args[0];
            // Si es un mensaje simple swal("titulo", "msg", "icono")
            if (typeof options === 'string' && args.length >= 1) {
                let config = {
                    title: args[0],
                    text: args[1] || "",
                    icon: args[2] || "success",
                    timer: 2500,
                    buttons: false
                };
                return originalSwal(config);
            }
            // Si es un objeto pero no tiene botones de confirmación
            if (typeof options === 'object' && !options.buttons) {
                options.timer = options.timer || 2500;
                options.buttons = false;
            }
            return originalSwal.apply(window, args);
        };
    }
})();

$(document).ready(function () {

    // 1. Inicialización de Select2
    if ($(".js-select2").length > 0) {
        $(".js-select2").each(function () {
            $(this).select2({
                minimumResultsForSearch: 20,
                dropdownParent: $(this).next('.dropDownSelect2')
            });
        });
    }

    // 2. Efecto Parallax
    if ($('.parallax100').length > 0) {
        $('.parallax100').parallax100();
    }

    // 3. Galería de Imágenes
    if ($('.gallery-lb').length > 0) {
        $('.gallery-lb').each(function () {
            $(this).magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery: { enabled: true },
                mainClass: 'mfp-fade'
            });
        });
    }
    
    /* GESTIÓN DE WISHLIST */
    $('.js-addwish-detail, .js-addwish-b2').on('click', function (e) {
        e.preventDefault();
        let nameProduct = $(this).closest('.block2').find('.stext-104').text().trim() || $('.sayana-titulo-gradiente').text().trim();

        // Esta llamada ahora se cerrará sola gracias al parche de arriba
        swal(nameProduct, "¡Se ha añadido a tu lista de deseos!", "success");

        $(this).addClass('js-addedwish-detail');
        $(this).find('i').removeClass('fa-regular').addClass('fa-solid');
        $(this).off('click');
    });

    fntInitCanvasScroll();
});

window.fntInitCanvasScroll = function() {
    $('.js-pscroll').each(function () {
        $(this).css('position', 'relative');
        $(this).css('overflow', 'hidden');
        var ps = new PerfectScrollbar(this, {
            wheelSpeed: 1,
            scrollingThreshold: 1000,
            wheelPropagation: false,
        });

        $(window).on('resize', function () {
            ps.update();
        })
    });
};

/* GESTIÓN DEL JOYERO */
window.fntDelItem = function(idProducto) {
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
                $('#modalCarrito').empty().html(objData.htmlCarrito);
                fntInitCanvasScroll();
                swal("Joyero", "Producto eliminado correctamente", "success");
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    });
};

window.fntClearCart = function() {
    // Esta NO se cierra sola porque tiene la propiedad "buttons" definida
    swal({
        title: "¿Vaciar Joyero?",
        text: "¿Estás seguro de que quieres retirar todas tus joyas del carrito?",
        icon: "warning",
        buttons: ["Cancelar", "Sí, vaciar"],
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            let ajaxUrl = base_url + '/Carrito/clearCarrito';
            $.post(ajaxUrl, function(response) {
                let objData = JSON.parse(response);
                if (objData.status) {
                    $('.js-show-cart').attr('data-notify', '0');
                    $('#modalCarrito').html(objData.htmlCarrito);
                    swal("Joyero", "Tu joyero está ahora vacío.", "success");
                }
            });
        }
    });
};