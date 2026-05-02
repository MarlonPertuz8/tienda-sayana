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
                    // 1. Manejo de Rutas y Enlaces
                    $ruta_p = !empty($producto['ruta']) ? $producto['ruta'] : $producto['idproducto'];
                    $link_detalle = base_url() . '/tienda/producto/' . $ruta_p;
                    
                    // 2. CORRECCIÓN: Foto por defecto (Validación robusta)
                    $img_producto = media() . '/images/uploads/default.png'; // Imagen base
                    if (!empty($producto['portada'])) {
                        $img_producto = $producto['portada'];
                    }

                    // 3. Lógica de Stock
                    $stockActual = isset($producto['stock']) ? (int)$producto['stock'] : 0;
                    $sinStock = ($stockActual <= 0);
                    $claseAgotado = $sinStock ? "sayana-card-exhausted" : "";

                    // 4. Lógica de Precios y Ofertas
                    $precioOriginal = !empty($producto['precio']) ? $producto['precio'] : 0;
                    // Buscamos precio_oferta o precio_final según lo que envíe tu controlador
                    $precioOferta = isset($producto['precio_oferta']) ? $producto['precio_oferta'] : (isset($producto['precio_final']) ? $producto['precio_final'] : 0);
                    $tieneOferta = ($precioOferta > 0 && $precioOferta < $precioOriginal);
                    
                    // 5. Validaciones de Seguridad (Evitan Warnings de PHP)
                    $isFav = isset($producto['is_fav']) ? $producto['is_fav'] : 0;
                    $puntuacionActual = !empty($producto['promedio']) ? round($producto['promedio']) : 0;
            ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item">
                        <div class="block2 <?= $claseAgotado ?>">
                            <div class="<?= $sinStock ? 'js-block-click' : '' ?>">
                                <div class="block2-pic hov-img0 pos-relative">
                                    <a href="<?= $link_detalle; ?>">
                                        <img src="<?= $img_producto; ?>"
                                            alt="<?= strip_tags($producto['nombre']); ?>"
                                            onerror="this.src='<?= media(); ?>/images/uploads/default.png';">
                                    </a>

                                    <button class="btn-wishlist-sayana" onclick="fntAddWishlist(<?= $producto['idproducto'] ?>, this)">
                                        <i class="fa <?= ($isFav > 0) ? 'fa-heart' : 'fa-heart-o' ?>"
                                           style="<?= ($isFav > 0) ? 'color: #f77870;' : '' ?>"></i>
                                    </button>

                                    <?php if ($sinStock): ?>
                                        <div class="label-agotado">AGOTADO</div>
                                    <?php elseif ($tieneOferta): ?>
                                        <div class="label-oferta">OFERTA</div>
                                    <?php endif; ?>
                                </div>

                                <div class="block2-txt p-3 text-center">
                                    <!-- Estrellas de Calificación -->
                                    <div class="block2-rating m-b-5" data-idproducto="<?= $producto['idproducto'] ?>">
                                        <div class="stars-content" style="cursor: pointer;">
                                            <?php
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

                                        <?php
                                        // Encriptación y lógica de colores
                                        $id_encriptado = openssl_encrypt($producto['idproducto'], METHODENCRIPT, KEY);
                                        // Verificamos si tiene colores (ajustado a tu formato de datos)
                                        $tieneColores = (!empty($producto['colores']) && $producto['colores'] != "[]") ? true : (isset($producto['tiene_colores']) && $producto['tiene_colores'] ? true : false);
                                        ?>

                                        <?php if ($sinStock): ?>
                                            <button class="btn-add-cart disabled-total" disabled>
                                                <i class="fa fa-shopping-cart"></i>
                                            </button>
                                        <?php elseif ($tieneColores): ?>
                                            <a href="<?= $link_detalle . '?v=1'; ?>" class="btn-add-cart">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn-add-cart" onclick="fntAddCarrito('<?= $id_encriptado; ?>')">
                                                <i class="fa fa-shopping-cart"></i>
                                            </button>
                                        <?php endif; ?>
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