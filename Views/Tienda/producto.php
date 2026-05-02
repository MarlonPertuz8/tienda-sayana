<?php
headerTienda($data);
$producto = $data['producto'];
$imagenes = $producto['images'];

// --- LÓGICA DE PRECIOS Y DESCUENTOS ---
$precioOriginal = $producto['precio'];
$precioFinal = $producto['precio'];
$etiquetaImagen = "";

if (!empty($producto['precio_oferta']) && $producto['precio_oferta'] > 0 && $producto['precio_oferta'] < $precioOriginal) {
    $precioFinal = $producto['precio_oferta'];
    $porcentaje = round((($precioOriginal - $precioFinal) / $precioOriginal) * 100);
    $etiquetaImagen = '<div class="badge-oferta-flotante">-' . $porcentaje . '%</div>';
}

$metodo = defined('METHODENCRIPT') ? METHODENCRIPT : "AES-128-ECB";
$key = defined('KEY') ? KEY : "sayana_key";
$id_encriptado = openssl_encrypt($producto['idproducto'], $metodo, $key);
?>

<div class="container bg0 p-t-100 p-b-80">
    <div class="row">
        <div class="col-md-6 col-lg-7 p-b-30">
            <div class="p-l-25 p-r-30 p-lr-0-lg">
                <div class="wrap-slick3 flex-sb flex-w">

                    <?php if (!empty($imagenes) && count($imagenes) > 1): ?>
                        <div class="wrap-slick3-dots"></div>
                    <?php endif; ?>

                    <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                    <div class="slick3 dot-main">
                        <?php if (!empty($imagenes)) {
                            foreach ($imagenes as $img) { ?>
                                <div class="item-slick3" data-thumb="<?= $img['url_image']; ?>">
                                    <div class="wrap-pic-w pos-relative">
                                        <?= $etiquetaImagen; ?>
                                        <img src="<?= $img['url_image']; ?>"
                                            alt="<?= htmlspecialchars($producto['nombre']); ?>"
                                            onerror="this.src='<?= media(); ?>/images/uploads/default.png';">
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <div class="item-slick3" data-thumb="<?= media(); ?>/images/uploads/product-default.jpg">
                                <div class="wrap-pic-w pos-relative">
                                    <?= $etiquetaImagen; ?>
                                    <img src="<?= media(); ?>/images/uploads/product-default.jpg" alt="Sayana Joyas">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-5 p-b-30">
            <div class="p-r-50 p-t-5 p-lr-0-lg">
                <h4 class="mtext-105 cl2 p-b-10 sayana-titulo-gradiente">
                    <?= htmlspecialchars($producto['nombre']); ?>
                </h4>

                <div class="block2-rating p-b-14" data-idproducto="<?= $producto['idproducto'] ?>">
                    <div class="stars-content">
                        <?php
                        $puntuacionActual = !empty($producto['promedio']) ? round($producto['promedio']) : 0;
                        for ($i = 5; $i >= 1; $i--):
                            $claseEstrella = ($i <= $puntuacionActual) ? 'fa fa-star' : 'fa fa-star-o';
                        ?>
                            <i class="item-star <?= $claseEstrella ?>"
                                data-value="<?= $i ?>"
                                style="color: #d4af37; font-size: 16px; cursor: pointer;"
                                onclick="fntCalificar(this)">
                            </i>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="p-b-14">
                    <?php if ($precioFinal < $precioOriginal): ?>
                        <span class="stext-105 cl3 precio-tachado">
                            <?= SMONEY . formatMoneda($precioOriginal); ?>
                        </span>
                        <span class="mtext-106 cl2 text-verde" style="font-weight: 700;">
                            <?= SMONEY . formatMoneda($precioFinal); ?>
                        </span>
                    <?php else: ?>
                        <span class="mtext-106 cl2 text-gold">
                            <?= SMONEY . formatMoneda($precioOriginal); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="stext-102 cl3 p-t-10 p-b-20 sayana-descripcion-limpia">
                    <?= $producto['descripcion']; ?>
                </div>

                <div class="p-t-33">
                    <?php $tieneColores = (!empty($producto['colores']) && $producto['colores'] !== "NULL" && trim($producto['colores']) !== ""); ?>

                    <?php if ($tieneColores): ?>
                        <div class="flex-w flex-r-m p-b-20" id="containerColor">
                            <div class="size-203 flex-c-m respon6 stext-101 cl2" style="font-weight: 700;">Color</div>
                            <div class="size-204 respon6-next">
                                <div class="rs1-select2 custom-luxe bg0">
                                    <select class="js-select2" id="listColor" name="color">
                                        <option value="">Seleccionar color</option>
                                        <?php
                                        $arrColores = explode(",", $producto['colores']);
                                        foreach ($arrColores as $color) {
                                            $val = trim($color);
                                            if ($val != "") echo '<option value="' . $val . '">' . $val . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="dropDownSelect2"></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="flex-w flex-r-m p-b-10">
                        <div class="size-204 flex-w flex-m respon6-next">
                            <!-- Selector de Cantidad -->
                            <div class="wrap-num-product flex-w m-r-20 m-tb-10" style="border-radius: 25px; overflow: hidden; border: 1px solid #e1e1e1;">
                                <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m"><i class="fs-16 zmdi zmdi-minus"></i></div>
                                <input class="mtext-104 cl3 txt-center num-product" type="number" name="num-product" value="1" id="cant-product">
                                <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m"><i class="fs-16 zmdi zmdi-plus"></i></div>
                            </div>

                            <!-- Botón 1: Agregar -->
                            <button onclick="fntAddCarrito('<?= $id_encriptado; ?>')" class="flex-c-m stext-101 cl0 size-101 p-lr-15 trans-04 sayana-btn-luxury m-b-10">
                                Agregar al Carrito
                            </button>

                            <!-- Botón 2: Comprar -->
                            <button onclick="fntAddCarrito('<?= $id_encriptado; ?>', true)" class="flex-c-m stext-101 cl0 size-101 p-lr-15 trans-04 btn-buy-now m-b-10">
                                ¡COMPRAR AHORA!
                            </button>

                            <!-- ACCIÓN DE SEGUIR COMPRANDO (Estilo Limpio) -->
                            <div class="w-full flex-c-m p-t-8">
                                <a href="<?= base_url(); ?>/tienda" class="stext-101 cl2 hov-cl1 trans-04 font-weight-bold" style="font-size: 14px; letter-spacing: 1px; text-transform: uppercase;">
                                    <i class="zmdi zmdi-arrow-left m-r-6"></i> Seguir comprando
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-t-40">
                    <div class="sayana-social-row">
                        <div class="bor9-r">
                            <button class="fs-20 cl3 hov-cl1 trans-04 lh-10 p-r-15"
                                onclick="fntAddWishlist(<?= $producto['idproducto'] ?>, this)"
                                title="Añadir a Deseos">
                                <i class="fa <?= (isset($producto['is_fav']) && $producto['is_fav'] > 0) ? 'fa-heart' : 'fa-heart-o' ?>"
                                    style="<?= (isset($producto['is_fav']) && $producto['is_fav'] > 0) ? 'color: #f77870;' : '' ?>"></i>
                            </button>
                        </div>
                        <a href="https://wa.me/<?= WHATSAPP; ?>?text=Hola Sayana, consulto por: <?= urlencode($producto['nombre']); ?>" class="sayana-icon-btn" target="_blank"><i class="fa fa-brands fa fa-whatsapp"></i></a>
                        <a href="<?= INSTAGRAM; ?>" class="sayana-icon-btn" target="_blank"><i class="fa fa-brands fa fa-instagram"></i></a>
                        <a href="mailto:<?= EMAIL_CONTACTO; ?>?subject=Consulta: <?= htmlspecialchars($producto['nombre']); ?>" class="sayana-icon-btn"><i class="fa fa-solid fa fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="sec-relate-product bg0 p-t-45 p-b-105">
</section>


<?php footerTienda($data); ?>