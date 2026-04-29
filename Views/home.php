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
            <?php foreach ($arrSlider as $slider):
                $rutaImagen = $slider['portada'];
            ?>
                <div class="item-slick1 sayana-slide-item" style="background-image: url('<?= $rutaImagen ?>');">
                    <div class="container h-full">
                        <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                            <div class="layer-slick1 animated visible-false" data-appear="fadeInLeft" data-delay="0">
                                <span class="sayana-slide-tag">Sayana Exclusive Collection</span>
                            </div>
                            <div class="layer-slick1 animated visible-false" data-appear="fadeInUp" data-delay="800">
                                <h2 class="sayana-slide-title"><?= $slider['nombre'] ?></h2>
                                <p class="sayana-slide-desc"><?= $slider['descripcion'] ?></p>
                            </div>
                            <div class="layer-slick1 animated visible-false" data-appear="zoomIn" data-delay="1600">
                                <a href="<?= $slider['link']; ?>" class="sayana-btn-luxury-banner">Ver Detalles <i class="fa fa-long-arrow-right m-l-10"></i></a>
                            </div>
                        </div>
                    </div>
                </div> <?php endforeach; ?>
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

<?php footerTienda($data); ?>