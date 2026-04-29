/*=============================================================================
    FUNCIONES DE TIENDA - SAYANA LUXURY (ESTÉTICA Y NAVEGACIÓN)
=============================================================================*/

$(document).ready(function () {

    // 1. Inicialización de Select2 para los selectores de color/talla
    if ($(".js-select2").length > 0) {
        $(".js-select2").each(function () {
            $(this).select2({
                minimumResultsForSearch: 20,
                dropdownParent: $(this).next('.dropDownSelect2')
            });
        });
    }

    // 2. Efecto Parallax en banners
    if ($('.parallax100').length > 0) {
        $('.parallax100').parallax100();
    }

    // 3. Galería de Imágenes (Magnific Popup)
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
    
    /*-------------------------------------------------------------------------
        GESTIÓN DE WISHLIST (LISTA DE DESEOS)
    --------------------------------------------------------------------------*/
    $('.js-addwish-detail, .js-addwish-b2').on('click', function (e) {
        e.preventDefault();
        let nameProduct = $(this).closest('.block2').find('.stext-104').text().trim() || $('.sayana-titulo-gradiente').text().trim();

        swal(nameProduct, "¡Se ha añadido a tu lista de deseos!", "success");

        $(this).addClass('js-addedwish-detail');
        $(this).find('i').removeClass('fa-regular').addClass('fa-solid');
        $(this).off('click');
    });

    /*-------------------------------------------------------------------------
        SCROLLBAR PERSONALIZADO (Modal Carrito)
    --------------------------------------------------------------------------*/
    fntInitCanvasScroll();

});

// Función para refrescar el scroll cuando sea necesario
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

/*-------------------------------------------------------------------------
    GESTIÓN DEL JOYERO (SOLO ELIMINAR Y VACIAR)
--------------------------------------------------------------------------*/

// Función para eliminar un producto específico (la "X")
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
                fntInitCanvasScroll(); // Reiniciamos el scroll tras la carga
                swal("Joyero", "Producto eliminado correctamente", "success");
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    });
};

// Función para limpiar TODO el joyero
window.fntClearCart = function() {
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