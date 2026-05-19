<?php
headerTienda($data);
getModal('modalCarrito', $data);
getModal('modalViewProducto', $data);

$arrSlider = $data['slider'];
$arrBanner = $data['banner'];
$arrProductos = $data['productos'];
?>

<section class="section-slide">
    <div class="wrap-slick1">
        <div class="slick1">
            <?php 
            foreach ($data['slider'] as $slider): 
                $rutaImagen = $slider['portada'];
                $tipo       = $slider['tipo'];
                $urlVideo   = $slider['video']; // Esta ya viene con la ruta completa desde el Trait
                $titulo     = $slider['nombre'];
                $desc       = $slider['descripcion'];
                $textoBoton = $slider['boton_texto'];
                $link       = $slider['link'];
            ?>
                
            <div class="item-slick1 sayana-slide-item" style="position:relative; overflow:hidden;">

                <?php if($tipo == "video" && !empty($urlVideo)): ?>
                    <!-- VIDEO COMO FONDO -->
                    <video autoplay muted loop playsinline
                        style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; z-index:1;">
                        <source src="<?= $urlVideo ?>" type="video/mp4">
                    </video>
                <?php else: ?>
                    <!-- IMAGEN COMO FONDO -->
                    <div style="background-image: url('<?= $rutaImagen ?>'); 
                                position:absolute; top:0; left:0; width:100%; height:100%; 
                                background-size:cover; background-position:center; z-index:1;">
                    </div>
                <?php endif; ?>

                <!-- OVERLAY (Capa oscura para legibilidad del texto) -->
                <div style="position:absolute; top:0; left:0; width:100%; height:100%; 
                            background: rgba(0,0,0,0.3); z-index:2;"></div>

                <!-- CONTENIDO DEL SLIDE -->
                <div class="container h-full" style="position:relative; z-index:3;">
                    <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                        
                        <!-- Etiqueta superior animada -->
                        <div class="layer-slick1 animated visible-false" data-appear="fadeInLeft" data-delay="0">
                            <span class="sayana-slide-tag">Sayana Exclusive Collection</span>
                        </div>
                        
                        <!-- Título y Descripción -->
                        <div class="layer-slick1 animated visible-false" data-appear="fadeInUp" data-delay="800">
                            <h2 class="sayana-slide-title"><?= $titulo ?></h2>
                            <p class="sayana-slide-desc"><?= $desc ?></p>
                        </div>
                        
                        <!-- Botón personalizado -->
                        <div class="layer-slick1 animated visible-false" data-appear="zoomIn" data-delay="1600">
                            <a href="<?= $link ?>" class="sayana-btn-luxury-banner">
                                <?= $textoBoton ?> <i class="fa fa-long-arrow-right m-l-10"></i>
                            </a>
                        </div>

                    </div>
                </div>

            </div>

            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="sec-categories-bubbles p-t-40 p-b-40">
    <div class="container">
        <div class="swiper swiper-bubbles">
            <div class="swiper-wrapper">
                <?php foreach ($arrBanner as $banner):
                    $imgBanner = $banner['portada'];
                    $link_categoria = base_url() . '/tienda/categoria/' . $banner['idcategoria'] . '/' . $banner['ruta'];
                ?>
                    <div class="swiper-slide text-center">
                        <a href="<?= $link_categoria; ?>" class="sayana-bubble-link">
                            <div class="sayana-bubble-wrap">
                                <img src="<?= $imgBanner ?>" alt="<?= $banner['nombre'] ?>">
                            </div>
                            <span class="sayana-bubble-name"><?= $banner['nombre'] ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="swiper-pagination-bubbles p-t-20"></div>
        </div>
    </div>
</section>

<section class="bg0 p-t-23 p-b-140">
    <div class="container">
        <div class="p-b-10">
            <h3 class="ltext-103 cl5">Productos Recientes</h3>
        </div>
        <hr class="sayana-hr">

        <div class="row isotope-grid">
            <?php
            if (!empty($arrProductos)) {
                foreach ($arrProductos as $producto):
                    $imagen_url = !empty($producto['portada']) ? $producto['portada'] : media() . '/images/uploads/default.png';

                    // LÓGICA DE STOCK
                    $stockActual = isset($producto['stock']) ? (int)$producto['stock'] : 0;
                    $sinStock = ($stockActual <= 0) ? true : false;

                    // Clases según tus estilos
                    $claseAgotado = $sinStock ? "sayana-card-exhausted" : "";

                    $precioOriginal = $producto['precio'];
                    $precioOferta = $producto['precio_oferta'];
                    $tieneOferta = ($precioOferta > 0 && $precioOferta < $precioOriginal);
            ?>

                    <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item">
                        <div class="block2 <?= $claseAgotado ?>">
                            <div class="<?= $sinStock ? 'js-block-click' : '' ?>">
                                <div class="block2-pic hov-img0 pos-relative">
                                    <a href="<?= base_url() . '/tienda/producto/' . $producto['ruta']; ?>">
                                        <img src="<?= $imagen_url; ?>" alt="<?= strip_tags($producto['nombre']); ?>">
                                    </a>

                                    <?php
                                    $iconWish = " fa fa-heart-o";
                                    if (isset($_SESSION['login']) && !empty($producto['favorite'])) {
                                        $iconWish = "fa fa-heart";
                                    }
                                    ?>
                                    <button class="btn-wishlist-sayana"
                                        onclick="fntAddWishlist(<?= $producto['idproducto'] ?>, this)">
                                        <i class="fa <?= ($producto['is_fav'] > 0) ? 'fa-heart' : 'fa-heart-o' ?>"
                                            style="<?= ($producto['is_fav'] > 0) ? 'color: #f77870;' : '' ?>"></i>
                                    </button>

                                    <?php if ($sinStock): ?>
                                        <div class="label-agotado">AGOTADO</div>
                                    <?php elseif ($tieneOferta): ?>
                                        <div class="label-oferta">OFERTA</div>
                                    <?php endif; ?>

                                    <button class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1"
                                        onclick="<?= $sinStock ? '' : 'fntViewProducto(' . $producto['idproducto'] . ')' ?>"
                                        <?= $sinStock ? 'disabled' : '' ?>>
                                        Vista Rápida
                                    </button>
                                </div>

                                <div class="block2-txt p-3 text-center">
                                    <div class="block2-rating m-b-5" data-idproducto="<?= $producto['idproducto'] ?>">
                                        <div class="stars-content" style="cursor: pointer;">
                                            <?php
                                            $puntuacionActual = !empty($producto['promedio']) ? round($producto['promedio']) : 0;
                                            for ($i = 5; $i >= 1; $i--):
                                                $claseEstrella = ($i <= $puntuacionActual) ? 'fa fa-star' : 'fa fa-star-o';
                                            ?>
                                                <i class="item-star <?= $claseEstrella ?>"
                                                    data-value="<?= $i ?>"
                                                    style="color: #d4af37; font-size: 13px;"
                                                    onclick="fntCalificar(this)">
                                                </i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <a href="<?= base_url() . '/tienda/producto/' . $producto['ruta']; ?>" class="stext-104 cl4 hov-cl1 trans-04 d-block mb-1 font-weight-bold">
                                        <?= strip_tags($producto['nombre']); ?>
                                    </a>

                                    <span class="stext-105 d-block mb-2">
                                        <?php if ($tieneOferta) { ?>
                                            <span class="text-muted"><del><?= SMONEY . formatMoneda($precioOriginal); ?></del></span>
                                            <span class="text-danger font-weight-bold m-l-5">
                                                <?= SMONEY . formatMoneda($precioOferta); ?>
                                            </span>
                                        <?php } else { ?>
                                            <span class="text-gold" style="color: #d4af37;">
                                                <?= SMONEY . formatMoneda($precioOriginal); ?>
                                            </span>
                                        <?php } ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach;
            } ?>
        </div>
    </div>
</section>

<section class="sec-trust-icons p-t-50 p-b-50">
    <div class="container">
        <div class="swiper swiper-trust">
            <div class="swiper-wrapper">

                <div class="swiper-slide">
                    <div class="trust-card">
                        <div class="trust-icon">
                            <i class="fa fa-truck"></i>
                        </div>
                        <div class="trust-text">
                            <h4 class="trust-title">Envíos Seguros</h4>
                            <p class="trust-desc">Llegamos a todo el país con amor.</p>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="trust-card">
                        <div class="trust-icon">
                            <i class="fa fa-diamond"></i>
                        </div>
                        <div class="trust-text">
                            <h4 class="trust-title">Calidad Premium</h4>
                            <p class="trust-desc">Piezas diseñadas para durar.</p>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="trust-card">
                        <div class="trust-icon">
                            <i class="fa fa-credit-card"></i>
                        </div>
                        <div class="trust-text">
                            <h4 class="trust-title">Pago Contraentrega</h4>
                            <p class="trust-desc">Compra con total tranquilidad.</p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="swiper-pagination-trust p-t-20 visible-xs"></div>
        </div>
    </div>
</section>

<section class="sec-video-bts p-t-60 p-b-60">
    <div class="container">
        <div class="video-container-wrap">
            <video autoplay muted loop playsinline class="bts-video">
                <source src="<?= base_url(); ?>/assets/images/video/behind-the-scenes.mp4" type="video/mp4">
                Tu navegador no soporta videos.
            </video>

            <div class="video-overlay flex-col-c-m">
                <h3 class="ltext-105 cl0 txt-center p-b-10">Hecho con Amor</h3>
                <p class="stext-117 cl0 txt-center">Conoce el proceso detrás de cada pedido en @sayana.col</p>
                <div class="m-t-20">
                    <div class="sayana-line" style="background-color: #f77870; width: 60px; margin: 0 auto;"></div>
                </div>
            </div>
        </div>
    </div>
</section>




<section class="p-t-60 p-b-100">
    <div class="container">
        <div class="p-b-45 text-center">
            <h3 class="ltext-105 cl5">Instagram Feed</h3>
            <p class="stext-117 cl6">@sayana.col</p>
        </div>

        <div class="swiper swiper-insta-sayana">
            <div class="swiper-wrapper" id="instafeed-sayana">
            </div>
            <div class="swiper-button-prev" style="color: #C5A059;"></div>
            <div class="swiper-button-next" style="color: #C5A059;"></div>
        </div>
    </div>
</section>


<section class="sec-blog bg0 p-t-40 ">
    <div class="container">
        <div class="p-b-40">
            <h3 class="ltext-105 cl5 txt-center respon1">
                Nuestro Blog
            </h3>
            <p class="stext-117 cl6 txt-center">
                Descubre tendencias y consejos de joyería en Sayana
            </p>
        </div>

        <div class="row">
            <?php if (!empty($data['posts'])): ?>
                <?php foreach ($data['posts'] as $post):
                    $rutaPost = base_url() . '/blogtienda/articulo/' . $post['ruta'];
                    $imagen = ($post['portada'] != "")
                        ? media() . '/images/uploads/' . $post['portada']
                        : media() . '/images/uploads/portada_categoria.png';

                    $fecha = strtotime(str_replace('/', '-', $post['fecha']));
                ?>
                    <div class="col-sm-6 col-md-4 p-b-40">
                        <div class="blog-item-home">
                            <div class="hov-img0 how-pos5-parent">
                                <a href="<?= $rutaPost ?>">
                                    <img src="<?= $imagen ?>" alt="<?= $post['titulo'] ?>" class="img-fluid custom-blog-img">
                                </a>

                                <div class="date-badge-custom">
                                    <span class="day-text"><?= date("d", $fecha) ?></span>
                                    <span class="month-text"><?= date("M", $fecha) ?></span>
                                </div>
                            </div>

                            <div class="p-t-25">
                                <h4 class="p-b-12">
                                    <a href="<?= $rutaPost ?>" class="ltext-108 cl2 hov-cl1 trans-04" style="font-size: 22px !important;">
                                        <?= $post['titulo'] ?>
                                    </a>
                                </h4>

                                <p class="stext-117 cl6 p-b-20">
                                    <?= mb_substr(strip_tags($post['contenido']), 0, 100) ?>...
                                </p>

                                <a href="<?= $rutaPost ?>" class="stext-101 cl2 hov-cl1 trans-04">
                                    LEER MÁS <i class="fa fa-long-arrow-right m-l-9"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="txt-center w-full">Próximamente nuevas entradas...</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php if (!empty($data['campana_popup'])): 
    // ... (Tu lógica de bloques se mantiene igual para no romper nada) ...
    $bloques = json_decode($data['campana_popup']['json_contenido'], true);
    $imagenPopup = ""; $subtitulo = "¡No te lo pierdas!";
    $tieneRuleta = false; $segmentosRuleta = [];

    if (!empty($bloques)) {
        foreach ($bloques as $bloque) {
            if (isset($bloque['tipo']) && $bloque['tipo'] === 'popup') {
                $imagenPopup = $bloque['fileUrl'] ?? "";
                $subtitulo = $bloque['subtitulo'] ?? $subtitulo;
            }
            if (isset($bloque['tipo']) && $bloque['tipo'] === 'ruleta') {
                $tieneRuleta = true;
                $segmentosRuleta = $bloque['segmentos'] ?? [];
            }
        }
    }

    $urlImagen = (!empty($imagenPopup)) 
        ? ((strpos($imagenPopup, 'http') !== false || strpos($imagenPopup, 'data:image') !== false) 
            ? $imagenPopup : media().'/images/uploads/'.$imagenPopup)
        : media().'/images/uploads/'.$data['campana_popup']['banner_landing'];
?>

<style>
/* Z-INDEX Y BLUR */
#modalSmartPopup, #modalRuletaSayana { z-index:99999 !important; }
.modal-backdrop { z-index:99998 !important; backdrop-filter:blur(10px); background:rgba(0,0,0,.8); }

/* POPUP PRINCIPAL */
#modalSmartPopup .modal-content { border:none; border-radius:35px; overflow:hidden; box-shadow:0 30px 90px rgba(0,0,0,.5); }
#modalSmartPopup .btn-cerrar-popup { position:absolute; right:15px; top:15px; width:40px; height:40px; border:none; border-radius:50%; background:#fff; z-index:10; font-size:18px; transition:.3s; cursor:pointer; display:flex; align-items:center; justify-content:center; }
#modalSmartPopup .btn-cerrar-popup:hover { transform:rotate(90deg); background:#f77870; color:#fff; }
#modalSmartPopup .btn-accion {
    display: inline-block;
    padding: 15px 35px;
    border-radius: 60px;
    text-decoration: none;
    font-weight: 800;
    background: linear-gradient(45deg, #f77870, #ff9472);
    color: #fff !important;
    box-shadow: 0 15px 40px rgba(247,120,112, 0.4);
    transition: .3s ease;
    border: none;
    cursor: pointer;
}

#modalSmartPopup .btn-accion:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 45px rgba(247,120,112, 0.5);
}
/* RULETA SAYANA - DISEÑO VIBRANTE */
#modalRuletaSayana .modal-content { background: #111; border:none; border-radius:45px; color:#fff; }
.ruleta-title { font-size:38px; font-weight:900; background: linear-gradient(45deg, #FF007A, #FFD166); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
.ruleta-sub { color:#999; font-size:16px; margin-bottom:25px; }

.ruleta-wrapper { position:relative; width:100%; max-width:480px; margin:auto; padding:10px; }
#canvasRuleta { width:100%; height:auto; border-radius:50%; border:10px solid #222; box-shadow: 0 0 50px rgba(247,120,112,0.3); }

/* PUNTERO NEÓN */
.puntero-ruleta { position:absolute; top:-20px; left:50%; transform:translateX(-50%); width:0; height:0; border-left:20px solid transparent; border-right:20px solid transparent; border-top:40px solid #FF007A; z-index:200; filter:drop-shadow(0 0 10px #FF007A); }

/* BOTÓN CENTRAL */
#btnGirarTienda { 
    position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); 
    width:95px; height:95px; border-radius:50%; border:6px solid #111;
    background:#fff; color:#111; font-weight:900; font-size:14px; 
    cursor:pointer; z-index:300; transition:.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display:flex; align-items:center; justify-content:center; box-shadow:0 0 20px rgba(0,0,0,0.5);
}
#btnGirarTienda:hover { transform:translate(-50%,-50%) scale(1.1); background:#f77870; color:#fff; }
</style>

<div class="modal fade" id="modalSmartPopup" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-cerrar-popup" data-dismiss="modal"><i class="fa fa-times"></i></button>
            <div class="modal-body p-0">
                <a href="<?= base_url() ?>/tienda/campana/<?= $data['campana_popup']['slug'] ?>">
                    <img src="<?= $urlImagen ?>" class="img-fluid" style="width:100%; max-height:450px; object-fit:cover;">
                </a>
                <div class="p-5 text-center">
                    <h2 style="font-weight:900;"><?= $data['campana_popup']['nombre'] ?></h2>
                    <p class="text-muted mb-4"><?= $subtitulo ?></p>
                    <a href="<?= base_url() ?>/tienda/campana/<?= $data['campana_popup']['slug'] ?>" class="btn-accion">Ver Más</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRuletaSayana" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <div class="logo-ruleta"><img src="<?= media(); ?>/images/logoSayana.png" style="width:100px;"></div>
                <h2 class="ruleta-title">RULETA SAYANA</h2>
                <p class="ruleta-sub">¿Qué te depara el destino hoy?</p>
                <div class="ruleta-wrapper">
                    <div class="puntero-ruleta"></div>
                    <canvas id="canvasRuleta" width="500" height="500"></canvas>
                    <button id="btnGirarTienda">GIRAR</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('load', function () {
    const config = {
        popupKey: 'home_popup_<?= $data['campana_popup']['id_campana'] ?>',
        tieneRuleta: <?= json_encode($tieneRuleta) ?>,
        datosRuleta: <?= json_encode($segmentosRuleta) ?>
    };

    const handleRuleta = () => {
        if(config.tieneRuleta && !sessionStorage.getItem('ruleta_jugada')){
            setTimeout(() => {
                if(typeof abrirRuleta === 'function') abrirRuleta(config.datosRuleta);
            }, 800);
        }
    };

    if(!localStorage.getItem(config.popupKey)){
        setTimeout(() => {
            $('#modalSmartPopup').modal('show');
            sessionStorage.setItem(config.popupKey,'true');
            $('#modalSmartPopup').on('hidden.bs.modal', handleRuleta);
        }, 1500);
    } else {
        handleRuleta();
    }
});
</script>
<?php endif; ?>

<?php footerTienda($data); ?>