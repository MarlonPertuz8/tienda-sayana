<?php
headerTienda($data);
getModal('modalCarrito', $data);

$esInicio = (isset($data['page_name']) && $data['page_name'] == 'inicio') ? true : false;
$metodo = defined('METHODENCRIPT') ? METHODENCRIPT : "AES-128-ECB";
$key = defined('KEY') ? KEY : "sayana_key";
?>

<div class="bg0 p-t-80 p-b-140">
    <div class="container">
        <div class="flex-w flex-sb-m p-b-52">
            <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1" data-filter="*">
                    Todas las joyas
                </button>

                <?php if (!empty($data['categorias'])) {
                    foreach ($data['categorias'] as $cat) { ?>
                        <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5"
                            data-filter=".cat_<?= $cat['idcategoria'] ?>">
                            <?= strip_tags($cat['nombre']) ?>
                        </button>
                <?php }
                } ?>
            </div>

            <div class="sayana-actions-wrapper flex-w flex-c-m m-tb-10">
                <div class="action-item js-show-filter" style="cursor:pointer;">
                    <i class="zmdi zmdi-filter-list"></i>
                    <span>Filtrar</span>
                </div>
            </div>

            <div class="dis-none panel-filter w-full p-t-10">
                <div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm">
                    <div class="filter-col1 p-r-15 p-b-27">
                        <div class="mtext-102 cl2 p-b-15">Ordenar por</div>
                        <ul>
                            <li class="p-b-6"><a href="#" class="filter-link stext-106 trans-04" data-sort="default">Por defecto</a></li>
                            <li class="p-b-6"><a href="#" class="filter-link stext-106 trans-04" data-sort="price-low">Precio: Bajo a Alto</a></li>
                            <li class="p-b-6"><a href="#" class="filter-link stext-106 trans-04" data-sort="price-high">Precio: Alto a Bajo</a></li>
                        </ul>
                    </div>
                    
                    <div class="filter-col2 p-r-15 p-b-27">
                        <div class="mtext-102 cl2 p-b-15">Precio</div>
                        <ul>
                            <li class="p-b-6"><a href="#" class="filter-link stext-106 trans-04" data-filter="*">Todos</a></li>
                            <li class="p-b-6"><a href="#" class="filter-link stext-106 trans-04" data-filter=".range-1">$0 - $100.000</a></li>
                            <li class="p-b-6"><a href="#" class="filter-link stext-106 trans-04" data-filter=".range-2">$100.000 - $500.000</a></li>
                            <li class="p-b-6"><a href="#" class="filter-link stext-106 trans-04" data-filter=".range-3">$500.000+</a></li>
                        </ul>
                    </div>

                    <div class="filter-col3 p-r-15 p-b-27">
                        <div class="mtext-102 cl2 p-b-15">Material</div>
                        <ul>
                            <li class="p-b-6"><a href="#" class="filter-link stext-106 trans-04" data-filter="*">Todos</a></li>
                            <?php if (!empty($data['materiales'])) {
                                foreach ($data['materiales'] as $material) { ?>
                                    <li class="p-b-6">
                                        <a href="#" class="filter-link stext-106 trans-04"
                                            data-filter=".mat_<?= $material['idmaterial'] ?>">
                                            <?= $material['nombre'] ?>
                                        </a>
                                    </li>
                            <?php }
                            } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row isotope-grid">
            <?php
            if (!empty($data['productos'])) {
                foreach ($data['productos'] as $producto):
                    // CORRECCIÓN: Asegurar que los IDs existan para las clases del filtro
                    $idCat = !empty($producto['idcategoria']) ? $producto['idcategoria'] : ($producto['categoriaid'] ?? '0');
                    $idMat = !empty($producto['idmaterial']) ? $producto['idmaterial'] : ($producto['materialid'] ?? '0');
                    
                    $precioFinal = $producto['precio_final'];
                    $tieneOferta = $producto['on_sale'];
                    
                    $stockActual = isset($producto['stock']) ? (int)$producto['stock'] : 0;
                    $sinStock = ($stockActual <= 0) ? true : false;
                    $claseAgotado = $sinStock ? "sayana-card-exhausted" : "";
                    
                    // Asignación de rangos para Isotope
                    $claseRango = "range-3";
                    if ($precioFinal <= 100000) $claseRango = "range-1";
                    elseif ($precioFinal <= 500000) $claseRango = "range-2";

                    $id_encriptado = openssl_encrypt($producto['idproducto'], $metodo, $key);
                    $tieneColores = (!empty($producto['colores']) && $producto['colores'] != "" && $producto['colores'] != "[]") ? true : false;
            ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item cat_<?= $idCat ?> mat_<?= $idMat ?> <?= $claseRango ?>"
                        data-price="<?= $precioFinal ?>"
                        data-name="<?= strtolower(strip_tags($producto['nombre'])) ?>">
                        
                        <div class="block2 <?= $claseAgotado ?>">
                            <div class="<?= $sinStock ? 'js-block-click' : '' ?>">
                                
                                <div class="block2-pic hov-img0 pos-relative">
                                    <a href="<?= base_url() . '/tienda/producto/' . $producto['ruta']; ?>">
                                        <img src="<?= !empty($producto['portada']) ? $producto['portada'] : media() . '/images/uploads/default.png'; ?>" alt="<?= strip_tags($producto['nombre']); ?>">
                                    </a>

                                    <button class="btn-wishlist-sayana" onclick="fntAddWishlist(<?= $producto['idproducto'] ?>, this)">
                                        <i class="fa <?= ($producto['is_fav'] > 0) ? 'fa-heart' : 'fa-heart-o' ?>"
                                           style="<?= ($producto['is_fav'] > 0) ? 'color: #f77870;' : '' ?>"></i>
                                    </button>

                                    <?php if ($sinStock): ?>
                                        <div class="label-agotado">Agotado</div>
                                    <?php elseif ($tieneOferta): ?>
                                        <div class="label-oferta"><?= $producto['descuento'] ?></div>
                                    <?php endif; ?>
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
                                        <?php if ($tieneOferta): ?>
                                            <span class="text-muted"><del><?= SMONEY . $producto['precio_viejo']; ?></del></span>
                                            <span class="text-danger font-weight-bold m-l-5"><?= SMONEY . $producto['precio_actual']; ?></span>
                                        <?php else: ?>
                                            <span class="text-gold" style="color: #d4af37;"><?= SMONEY . $producto['precio_actual']; ?></span>
                                        <?php endif; ?>
                                    </span>

                                    <div class="sayana-actions-wrapper">
                                        <a href="<?= base_url() . '/tienda/producto/' . $producto['ruta']; ?>" 
                                           class="btn-ver-joya <?= $sinStock ? 'disabled-total' : '' ?>">
                                            Ver Más
                                        </a>

                                        <?php if ($sinStock): ?>
                                            <button class="btn-add-cart disabled-total" disabled>
                                                <i class="fa fa-shopping-cart"></i>
                                            </button>
                                        <?php elseif ($tieneColores): ?>
                                            <a href="<?= base_url() . '/tienda/producto/' . $producto['ruta']; ?>" class="btn-add-cart">
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
</div>

<div class="flex-c-m flex-w w-full p-t-45">
    <?php if($data['total_paginas'] > 1): ?>
        <div class="pagination-wrapper flex-w">
            <?php if($data['pagina'] > 1): ?>
                <a href="<?= base_url(); ?>/tienda/index/<?= $data['pagina']-1; ?>" class="flex-c-m how-pagination1 trans-04 m-all-7">
                    <i class="zmdi zmdi-chevron-left"></i>
                </a>
            <?php endif; ?>
            <?php for ($i=1; $i <= $data['total_paginas']; $i++): ?>
                <a href="<?= base_url(); ?>/tienda/index/<?= $i; ?>"
                   class="flex-c-m how-pagination1 trans-04 m-all-7 <?= ($i == $data['pagina']) ? 'active-pagination1' : ''; ?>">
                    <?= $i; ?>
                </a>
            <?php endfor; ?>
            <?php if($data['pagina'] < $data['total_paginas']): ?>
                <a href="<?= base_url(); ?>/tienda/index/<?= $data['pagina']+1; ?>" class="flex-c-m how-pagination1 trans-04 m-all-7">
                    <i class="zmdi zmdi-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php footerTienda($data); ?>