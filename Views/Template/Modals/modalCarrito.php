<div id="modalCarrito" class="w-full">
    <?php
    $total = 0;
    $productos = (isset($data) && !empty($data)) ? $data : ($_SESSION['arrCarrito'] ?? []);

    if (!empty($productos)) {
    ?>
        <ul class="header-cart-wrapitem w-full">
            <?php
            foreach ($productos as $indice => $producto) {
                $total += $producto['precio'] * $producto['cantidad'];
                $imagen = !empty($producto['imagen']) ? $producto['imagen'] : media() . '/images/uploads/default.png';

                // La llave debe ser ID + Color para coincidir con el índice de la sesión
                $idReferencia = $producto['idproducto'] . $producto['color'];
            ?>
                <li class="header-cart-item flex-w flex-t m-b-12">
                    <div class="header-cart-item-img" onclick="fntDelItem('<?= $idReferencia; ?>')">
                        <img src="<?= $imagen ?>"
                            alt="<?= $producto['producto'] ?>"
                            onerror="this.src='<?= media(); ?>/images/uploads/default.png';">
                    </div>

                    <div class="header-cart-item-txt p-t-8">
                        <a href="#" class="header-cart-item-name m-b-4 hov-cl1 trans-04">
                            <?= $producto['producto'] ?>
                        </a>

                        <?php if (!empty($producto['color'])) { ?>
                            <p class="stext-102 cl6">
                                <small>Color: <?= $producto['color'] ?></small>
                            </p>
                        <?php } ?>

                        <div class="header-cart-item-info">
                            <div class="flex-w m-t-5 m-b-5">
                                <button class="btn-qty btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m"
                                    onclick="fntUpdateQty('<?= $idReferencia; ?>','sub')">
                                    <i class="fs-12 fa fa-minus"></i>
                                </button>
                                <!-- Agregamos un ID a la cantidad central para actualizarla rápido -->
                                <span id="cant-modal-<?= $idReferencia; ?>" class="m-lr-15 fs-16 flex-c-m">
                                    <?= $producto['cantidad'] ?>
                                </span>
                                <button class="btn-qty btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m"
                                    onclick="fntUpdateQty('<?= $idReferencia; ?>','add')">
                                    <i class="fs-12 fa fa-plus"></i>
                                </button>
                            </div>

                            <!-- AGREGAMOS LA CLASE clase-subtotal-modal- CON EL ID DE REFERENCIA -->
                            <span class="fs-14 cl3 clase-subtotal-modal-<?= $idReferencia; ?>">
                                <?= $producto['cantidad'] ?> x
                                <?php if (isset($producto['precio_original']) && $producto['precio_original'] > $producto['precio']) { ?>
                                    <span style="text-decoration: line-through; color: #999; font-size: 0.8em;">
                                        <?= SMONEY . formatmoneda($producto['precio_original']) ?>
                                    </span>
                                    <span style="color: #f3635a; font-weight: bold;">
                                        <?= SMONEY . formatmoneda($producto['precio']) ?>
                                    </span>
                                <?php } else { ?>
                                    <?= SMONEY . formatmoneda($producto['precio']) ?>
                                <?php } ?>
                            </span>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>

        <div class="w-full">
            <div class="header-cart-total w-full p-tb-40">
                Total: <?= SMONEY . formatmoneda($total); ?>
            </div>

            <div class="header-cart-buttons flex-w w-full">
                <a href="<?= base_url(); ?>/carrito/procesarpago" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                    VER CARRITO
                </a>

                <button onclick="fntClearCart()" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                    Vaciar
                </button>

                <?php if (!empty($_SESSION['login'])) { ?>
                    <a href="<?= base_url(); ?>/carrito/procesarpago" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                        Pagar
                    </a>
                <?php } else { ?>
                    <a href="<?= base_url(); ?>/login" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10" title="Inicia sesión para pagar">
                        Identificarse
                    </a>
                <?php } ?>
            </div>
        </div>

        <?php if (empty($_SESSION['login'])) { ?>
            <div class="p-b-20 m-t-25 text-center w-full">
                <p class="stext-102 cl6">
                    ¿Ya tienes cuenta?
                    <a href="<?= base_url(); ?>/login/tienda" class="hov-cl1 trans-04 font-weight-bold" style="color: #d4af37;">
                        Inicia sesión
                    </a>
                    o
                    <a href="<?= base_url(); ?>/carrito/procesarpago" class="hov-cl1 trans-04 font-weight-bold" style="color: #d4af37;">
                        Regístrate
                    </a>
                </p>
            </div>
        <?php } ?>

    <?php } else { ?>
        <!-- VISTA CORREGIDA PARA MANTENER EL CENTRADO -->
        <div class="header-cart-content flex-w flex-c-m w-full">
            <div class="w-full p-t-30 text-center container-empty-cart">
                <div class="m-b-20 icon-wrapper-empty">
                    <i class="zmdi zmdi-shopping-cart-plus" style="font-size: 60px; color: #d4af37;"></i>
                </div>
                <p class="mtext-103 cl3 text-empty" style="margin-bottom: 20px;">Tu joyero está vacío.</p>
                <a href="<?= base_url(); ?>/tienda" class="btn-sayana-perfil m-t-20" style="margin: 15px auto;">
                    Ir a la tienda
                </a>
            </div>
        </div>
    <?php } ?>
</div>