
(function ($) {
    "use strict";

    /*[ Load page ]
    ===========================================================*/
    $(".animsition").animsition({
        inClass: 'fade-in',
        outClass: 'fade-out',
        inDuration: 1500,
        outDuration: 800,
        linkElement: '.animsition-link',
        loading: true,
        loadingParentElement: 'html',
        loadingClass: 'animsition-loading-1',
        loadingInner: '<div class="loader05"></div>',
        timeout: false,
        timeoutCountdown: 5000,
        onLoadEvent: true,
        browser: [ 'animation-duration', '-webkit-animation-duration'],
        overlay : false,
        overlayClass : 'animsition-overlay-slide',
        overlayParentElement : 'html',
        transition: function(url){ window.location.href = url; }
    });
    
   /*[ Back to top ]
    ===========================================================*/
    var windowH = $(window).height()/2;

    $(window).on('scroll',function(){
        if ($(this).scrollTop() > windowH) {
            // En lugar de usar .css, usamos una clase para que el CSS anime
            $("#myBtn").addClass('show-btn-back-to-top');
        } else {
            $("#myBtn").removeClass('show-btn-back-to-top');
        }
    });

    $('#myBtn').on("click", function(){
        $('html, body').animate({scrollTop: 0}, 300);
    });

 /*==================================================================
    [ Fixed Header Sayana - Lógica Inteligente ]*/
    var headerDesktop = $('.container-menu-desktop');
    var wrapMenu = $('.wrap-menu-desktop');
    var scrollThreshold = 100; 

    // Detectamos si estamos en el Home buscando el slider
    var isHome = $('.section-slide').length > 0;

/*==================================================================
 [ Fixed Header Sayana - Lógica Inteligente Separada ]*/
 var headerDesktop = $('.container-menu-desktop');
 var wrapMenu = $('.wrap-menu-desktop');
 var scrollThreshold = 100; 
 var isHome = $('.section-slide').length > 0;

 function checkScroll() {
     // SI ES ESCRITORIO (Ancho mayor a 991px)
     if ($(window).width() > 991) {
         if(isHome) {
             if($(window).scrollTop() > scrollThreshold) {
                 $(headerDesktop).addClass('fix-menu-desktop');
             } else {
                 $(headerDesktop).removeClass('fix-menu-desktop');
             }
         } else {
             $(headerDesktop).addClass('fix-menu-desktop');
         }
     } else {
         // SI ES MOBILE: Nos aseguramos de limpiar la clase de desktop 
         // para que no interfiera con el CSS de mobile
         $(headerDesktop).removeClass('fix-menu-desktop');
     }
     $(wrapMenu).css('top', 0); 
 }

 // Ejecutar al cargar, al hacer scroll y al cambiar el tamaño de ventana
 $(window).on('scroll resize load', function(){
     checkScroll();
 });


    /*==================================================================
    [ Menu mobile ]*/
        document.addEventListener("DOMContentLoaded", function() {
        // 1. Detectar la URL actual para activar el icono correspondiente
        const currentUrl = window.location.href;
        const navLinks = document.querySelectorAll('.sayana-mobile-nav a');

        navLinks.forEach(link => {
            // Si la URL del enlace coincide con la actual, añadimos la clase 'active'
            if (currentUrl === link.href || currentUrl.includes(link.href) && link.href !== "<?= base_url(); ?>/") {
                link.style.color = "#f77870"; // Color coral de tu marca
                link.querySelector('i').style.fontWeight = "bold";
            }
        });

        // 2. Ajuste dinámico para el botón de WhatsApp en móviles
        const whatsappBtn = document.querySelector('.float-whatsapp');
        if (whatsappBtn && window.innerWidth < 992) {
            whatsappBtn.style.bottom = "80px"; // Lo subimos para que no choque con la nav
        }
    });

    // 3. Slider de categorías
    new Swiper('.swiper-bubbles', {
        // Muestra 3.5 burbujas en móvil para invitar a deslizar
        slidesPerView: 3.5, 
        spaceBetween: 15,
        centeredSlides: false,
        loop: false, // Mejor sin loop si son pocas categorías para que no se repitan
        grabCursor: true,
        freeMode: true, // Movimiento fluido tipo scroll de Instagram
        
        breakpoints: {
            // En tablets muestra 5
            768: {
                slidesPerView: 5,
                spaceBetween: 25
            },
            // En computadoras muestra 7 o las que quepan
            1024: {
                slidesPerView: 7,
                spaceBetween: 35
            }
        }
    });

    // Slider de confianza
    new Swiper('.swiper-trust', {
        slidesPerView: 1.2,
        spaceBetween: 15,
        loop: true,
        allowTouchMove: false, // Evita que el usuario lo mueva manualmente
        speed: 5000, // Velocidad en milisegundos (ajusta para ir más rápido o lento)
        
        autoplay: {
            delay: 0, // Sin esperas entre deslizamientos
            disableOnInteraction: false,
        },
        
        // Esto quita el efecto de acelerar/frenar y lo deja constante
        freeMode: true, 
        
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
                allowTouchMove: true, // En PC podrías querer dejarlo fijo o móvil
            }
        }
    });



   /*==================================================================
[ 4. Slider de Instagram ]*/
const beholdURL = 'https://feeds.behold.so/Q0y5lSM4xbYErc6xRSOb';
const contenedor = document.getElementById('instafeed-sayana');

// Solo ejecutamos la lógica si el contenedor existe en la página actual
if (contenedor) {
    fetch(beholdURL)
        .then(response => {
            if (!response.ok) throw new Error('Respuesta de red no exitosa');
            return response.json();
        })
        .then(data => {
            const posts = data.posts || data;
            
            // Limpiamos el contenedor antes de agregar (por seguridad)
            contenedor.innerHTML = "";

            posts.forEach((post) => {
                // Selecciona miniatura si es video, o la imagen principal
                const imgSource = post.thumbnailUrl || post.mediaUrl;
                
                const slide = document.createElement('div');
                slide.className = 'swiper-slide';
                
                slide.innerHTML = `
                    <a href="${post.permalink}" target="_blank" class="sayana-insta-item">
                        <div class="sayana-insta-img-wrap">
                            <img src="${imgSource}" alt="Sayana Joyería" loading="lazy">
                            <div class="sayana-insta-overlay">
                                <i class="fa fa-instagram"></i>
                            </div>
                        </div>
                    </a>
                `;
                contenedor.appendChild(slide);
            });

            // Inicialización de Swiper (Solo se activa si hay posts cargados)
            new Swiper('.swiper-insta-sayana', {
                slidesPerView: 2,
                spaceBetween: 25,
                loop: posts.length > 5, // Solo hace loop si hay suficientes posts
                speed: 1000,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    768: { slidesPerView: 3 },
                    1024: { slidesPerView: 5 }
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        })
        .catch(err => {
            console.error("Error cargando Instagram:", err);
            // Evitamos error de innerHTML si el contenedor desapareció justo en la carga
            if (contenedor) {
                contenedor.innerHTML = '<p class="text-center w-full">Síguenos en @sayana.col</p>';
            }
        });
}
    
    /*==================================================================
   /*==================================================================
    [ Show / hide Search Bar Sayana ]*/
    $('.js-toggle-search').on('click', function(){
        // Expandimos o contraemos la barra
        $('.sayana-search-bar').toggleClass('active');
        $(this).toggleClass('active');

        // Ponemos el foco en el input si se abre
        if($('.sayana-search-bar').hasClass('active')){
            $('.sayana-input-search').focus();
        }
    });

    // Cerrar si se hace clic fuera del buscador
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.pos-relative').length) {
            $('.sayana-search-bar').removeClass('active');
            $('.js-toggle-search').removeClass('active');
        }
    });

    /* Comentamos el modal viejo para que no interfiera */
    /* $('.js-show-modal-search').on('click', function(){
        $('.modal-search-header').addClass('show-modal-search');
        $(this).css('opacity','0');
    });
    ... 
    */


 /*==================================================================
[ Lógica Maestra de Filtros Sayana ]*/
$(window).on('load', function () {
    var $topeContainer = $('.isotope-grid');

    // Inicializar Isotope SOLO para la rejilla de productos
    var $grid = $topeContainer.isotope({
        itemSelector: '.isotope-item',
        layoutMode: 'fitRows',
        percentPosition: true,
        getSortData: {
            name: '[data-name]',
            price: function(itemElem) {
                return parseFloat($(itemElem).attr('data-price'));
            }
        }
    });

    // --- FILTRADO (Categorías, Materiales y Rangos de Precio) ---
    $('.filter-tope-group').on('click', 'button', function (e) {
        var filterValue = $(this).attr('data-filter');
        $grid.isotope({ filter: filterValue });

        // Manejo de clase activa
        $(this).parent().find('.how-active1').removeClass('how-active1');
        $(this).addClass('how-active1');
    });

    // --- ORDENAMIENTO (Precio y Alfabeto) ---
    $('.filter-col1').on('click', 'a', function (e) {
        e.preventDefault();
        var sortValue = $(this).attr('data-sort');
        
        $('.filter-col1 .filter-link').removeClass('filter-link-active');
        $(this).addClass('filter-link-active');

        if (sortValue === 'price-low') {
            $grid.isotope({ sortBy: 'price', sortAscending: true });
        } else if (sortValue === 'price-high') {
            $grid.isotope({ sortBy: 'price', sortAscending: false });
        } else if (sortValue === 'name-az') {
            $grid.isotope({ sortBy: 'name', sortAscending: true });
        } else if (sortValue === 'name-za') {
            $grid.isotope({ sortBy: 'name', sortAscending: false });
        } else {
            $grid.isotope({ sortBy: 'original-order' });
        }
    });
});

    /*==================================================================
    [ Filter / Search product Sayana Smart ]*/
    
    // Lógica para el botón de Filtrar (Mantiene el slideToggle original)
    $('.js-show-filter').on('click',function(){
        $(this).toggleClass('show-filter');
        $('.panel-filter').slideToggle(400);

        // Si la búsqueda está abierta, la cerramos al abrir filtros
        if($('.js-show-search').hasClass('show-search')) {
            $('.js-show-search').removeClass('show-search');
            // Si usas panel, descomenta la siguiente línea:
            // $('.panel-search').slideUp(400); 
        }    
    });

    // Lógica para el botón de Buscar EXPANSIVO
    $('.js-show-search').on('click', function(){
        // Alternamos la clase para que el CSS expanda el botón
        $(this).toggleClass('show-search');
        
        // Si se expande, ponemos el foco en el input interno
        if($(this).hasClass('show-search')){
            $(this).find('input').focus();
            
            // Cerramos los filtros si están abiertos
            $('.js-show-filter').removeClass('show-filter');
            $('.panel-filter').slideUp(400);
        }
    });


/*==================================================================
[ FIX: Buscador Mobile Sayana - Versión Final ]*/
$('.js-show-modal-search').on('click', function(e){
    e.preventDefault();
    // 1. Usamos la clase .search-mobile-integrated que definiste en tu CSS
    $('.search-mobile-integrated').fadeIn(300);
    
    // 2. Le damos foco al input para que se abra el teclado móvil
    $('.search-input-mobile').focus();
});

// 3. Usamos la clase .js-hide-modal-search para cerrar el buscador
$('.js-hide-modal-search').on('click', function(e){
    e.preventDefault();
    $('.search-mobile-integrated').fadeOut(200);
});

    /*==================================================================
    [ Cart ]*/
    $('.js-show-cart').on('click',function(){
        $('.js-panel-cart').addClass('show-header-cart');
    });

    $('.js-hide-cart').on('click',function(){
        $('.js-panel-cart').removeClass('show-header-cart');
    });

    /*==================================================================
    [ Cart ]*/
    $('.js-show-sidebar').on('click',function(){
        $('.js-sidebar').addClass('show-sidebar');
    });

    $('.js-hide-sidebar').on('click',function(){
        $('.js-sidebar').removeClass('show-sidebar');
    });

    /*==================================================================
    [ +/- num product ]*/
    $('.btn-num-product-down').on('click', function(){
        var numProduct = Number($(this).next().val());
        if(numProduct > 0) $(this).next().val(numProduct - 1);
    });

    $('.btn-num-product-up').on('click', function(){
        var numProduct = Number($(this).prev().val());
        $(this).prev().val(numProduct + 1);
    });

    /*==================================================================
    [ Rating ]*/
    $('.wrap-rating').each(function(){
        var item = $(this).find('.item-rating');
        var rated = -1;
        var input = $(this).find('input');
        $(input).val(0);

        $(item).on('mouseenter', function(){
            var index = item.index(this);
            var i = 0;
            for(i=0; i<=index; i++) {
                $(item[i]).removeClass('zmdi-star-outline');
                $(item[i]).addClass('zmdi-star');
            }

            for(var j=i; j<item.length; j++) {
                $(item[j]).addClass('zmdi-star-outline');
                $(item[j]).removeClass('zmdi-star');
            }
        });

        $(item).on('click', function(){
            var index = item.index(this);
            rated = index;
            $(input).val(index+1);
        });

        $(this).on('mouseleave', function(){
            var i = 0;
            for(i=0; i<=rated; i++) {
                $(item[i]).removeClass('zmdi-star-outline');
                $(item[i]).addClass('zmdi-star');
            }

            for(var j=i; j<item.length; j++) {
                $(item[j]).addClass('zmdi-star-outline');
                $(item[j]).removeClass('zmdi-star');
            }
        });
    });
    
    /*==================================================================
    [ Show modal1 ]*/
    $('.js-show-modal1').on('click',function(e){
        e.preventDefault();
        $('.js-modal1').addClass('show-modal1');
    });

    $('.js-hide-modal1').on('click',function(){
        $('.js-modal1').removeClass('show-modal1');
    });

/*[ Slider Sayana - Inicialización Segura ]*/
setTimeout(function() {
    $('.slick1').each(function(){
        var slider = $(this);
        var wrapSlick1 = slider.parent();

        // Si el slider ya fue inicializado por otro script, lo destruimos para evitar duplicados
        if (slider.hasClass('slick-initialized')) {
            slider.slick('unslick');
        }

        slider.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: true,
            speed: 1000,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 6000,
            arrows: true,
            dots: true,
            appendDots: wrapSlick1,
            dotsClass: 'slick-dots',
            accessibility: false, // Evita el error de ADA 'add'
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        dots: true,
                        arrows: false
                    }
                }
            ],
            prevArrow: '<button class="arrow-slick1 prev-slick1"><i class="zmdi zmdi-caret-left"></i></button>',
            nextArrow: '<button class="arrow-slick1 next-slick1"><i class="zmdi zmdi-caret-right"></i></button>',
        });
    });
}, 300); // Esperamos 300ms para asegurarnos de que el DOM este listo

})(jQuery);

function fntCalificar(element) {
    let puntuacion = element.getAttribute("data-value");
    let idProducto = element.parentElement.parentElement.getAttribute("data-idproducto");
    
    // 1. Capturamos todas las estrellas de este producto específico
    let contenedorEstrellas = element.parentElement;
    let estrellas = contenedorEstrellas.querySelectorAll('.item-star');
    
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Tienda/setCalificacion'; 
    let formData = new FormData();
    formData.append('idproducto', idProducto);
    formData.append('puntuacion', puntuacion);

    request.open("POST", ajaxUrl, true);
    request.send(formData);

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            
            if (objData.status) {
                // 2. AQUÍ ESTÁ EL CAMBIO: Pintamos físicamente las estrellas
                estrellas.forEach((star) => {
                    let starValue = star.getAttribute("data-value");
                    
                    if (starValue <= puntuacion) {
                        // Pintamos como estrella llena (Solid)
                        star.classList.remove('far', 'fa-star-o');
                        star.classList.add('fas', 'fa-star');
                    } else {
                        // Dejamos como estrella vacía (Regular)
                        star.classList.remove('fas', 'fa-star');
                        star.classList.add('far', 'fa-star-o');
                    }
                });
                
                swal("SAYANA Luxury", objData.msg, "success");
            } else {
                swal("Atención", objData.msg, "error");
            }
        }
    }
}
function fntAddWishlist(idproducto, element) {
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Tienda/addWishlist';
    let formData = new FormData();
    formData.append('idproducto', idproducto);

    request.open("POST", ajaxUrl, true);
    request.send(formData);

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if (objData.status) {
                let icon = element.querySelector('i');
                
                if (objData.action == "add") {
                    icon.classList.replace('fa-heart-o', 'fa-heart');
                    element.style.color = "#f77870"; // Ponle el color de tu marca
                } else {
                    icon.classList.replace('fa-heart', 'fa-heart-o');
                    element.style.color = ""; // Color por defecto
                }
                swal("Wishlist", objData.msg, "success");
            } else {
                swal("Atención", objData.msg, "error");
            }
        }
    }
}

