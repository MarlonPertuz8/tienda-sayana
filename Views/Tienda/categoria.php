<?php
headerTienda($data);
getModal('modalCarrito', $data);

$categoria_info = !empty($data['info']) ? $data['info'] : null;
$ruta_categoria = isset($data['ruta']) ? $data['ruta'] : (isset($categoria_info['ruta']) ? $categoria_info['ruta'] : '');
?>
<section class="bg0 p-t-80 p-b-140">
    <div class="container">
        <div class="p-b-10">
            <h3 class="ltext-103 cl5 sayana-titulo-gradiente">
                <?= strip_tags($data['page_title']); ?>
            </h3>
        </div>

        <div class="flex-w flex-sb-m p-b-52">
            <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                <a href="<?= base_url(); ?>/tienda"
                    class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5"
                    style="cursor: pointer;">
                    <i class="fa fa-chevron-left"></i> Volver a la tienda
                </a>
            </div>

            <div class="flex-w flex-c-m m-tb-10">
                <div class="sayana-btn-search-container js-show-search">
                    <i class="zmdi zmdi-search icon-search"></i>
                    <input type="text" class="sayana-input-inline" placeholder="¿Qué joya buscas hoy?">
                    <span class="btn-text">Buscar</span>
                    <i class="zmdi zmdi-close icon-close dis-none"></i>
                </div>
            </div>
        </div>

        <hr class="sayana-hr">

        <div class="row isotope-grid">
            <?php
            if (!empty($data['productos'])) {
                foreach ($data['productos'] as $producto):
                    // Evita el error de "Undefined array key ruta"
                    $ruta_p = !empty($producto['ruta']) ? $producto['ruta'] : $producto['idproducto'];
                    $link_detalle = base_url() . '/tienda/producto/' . $ruta_p;
                    $imagen_url = !empty($producto['portada']) ? $producto['portada'] : media() . '/images/uploads/default.png';

                    $stockActual = isset($producto['stock']) ? (int)$producto['stock'] : 0;
                    $sinStock = ($stockActual <= 0);
                    $claseAgotado = $sinStock ? "sayana-card-exhausted" : "";

                    // Lógica de Oferta (por si la tienes en esta vista)
                    $precioOriginal = $producto['precio'];
                    $precioOferta = isset($producto['precio_oferta']) ? $producto['precio_oferta'] : 0;
                    $tieneOferta = ($precioOferta > 0 && $precioOferta < $precioOriginal);
            ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item">
                        <div class="block2 <?= $claseAgotado ?>">
                            <div class="<?= $sinStock ? 'js-block-click' : '' ?>">
                                <div class="block2-pic hov-img0 pos-relative">
                                    <a href="<?= $link_detalle; ?>">
                                        <?php
                                        // Validamos si existe el dato de la portada
                                        $img_producto = !empty($producto['portada']) ? $producto['portada'] : media() . '/images/uploads/default.png';
                                        ?>
                                        <img src="<?= $img_producto; ?>"
                                            alt="<?= strip_tags($producto['nombre']); ?>"
                                            onerror="this.src='<?= media(); ?>/images/uploads/default.png';">
                                    </a>

                                    <button class="btn-wishlist-sayana" onclick="fntAddWishlist(<?= $producto['idproducto'] ?>, this)">
                                        <i class="fa <?= (isset($producto['is_fav']) && $producto['is_fav'] > 0) ? 'fa-heart' : 'fa-heart-o' ?>"
                                            style="<?= (isset($producto['is_fav']) && $producto['is_fav'] > 0) ? 'color: #f77870;' : '' ?>"></i>
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

                                    <a href="<?= $link_detalle; ?>" class="stext-104 cl4 hov-cl1 trans-04 d-block mb-1 font-weight-bold">
                                        <?= strip_tags($producto['nombre']); ?>
                                    </a>

                                    <span class="stext-105 d-block mb-2">
                                        <?php if ($tieneOferta): ?>
                                            <span class="text-muted"><del><?= SMONEY . formatMoneda($precioOriginal); ?></del></span>
                                            <span class="text-danger font-weight-bold m-l-5"><?= SMONEY . formatMoneda($precioOferta); ?></span>
                                        <?php else: ?>
                                            <span class="text-gold" style="color: #d4af37;"><?= SMONEY . formatMoneda($precioOriginal); ?></span>
                                        <?php endif; ?>
                                    </span>

                                    <div class="sayana-actions-wrapper">
                                        <a href="<?= $link_detalle; ?>" class="btn-ver-joya">Ver Mas</a>
                                        <button class="btn-add-cart <?= $sinStock ? 'disabled-total' : '' ?>"
                                            <?= $sinStock ? 'disabled' : '' ?>>
                                            <i class="fa fa-shopping-cart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach;
            } ?>
        </div>
    </div>
</section>

<?php footerTienda($data); ?>