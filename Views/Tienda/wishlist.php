<?php headerTienda($data); ?>

<div class="container bg0 p-t-120 p-b-80">
    <div class="text-center p-b-50">
        <h2 class="ltext-105 cl5 premium-title">Mis Favoritos</h2>
        <div class="sayana-line-center"></div> 
    </div>

    <div class="row justify-content-center">
        <?php 
        if(!empty($data['productos'])){
            foreach ($data['productos'] as $producto) {
                $ruta = base_url().'/tienda/producto/'.$producto['ruta'];
        ?>
        <div class="col-sm-6 col-md-4 col-lg-3 p-b-40">
            <div class="block2 premium-card-full">
                <button class="btn-del-wishlist-top" onclick="fntDelWishlist(<?= $producto['idproducto'] ?>, this)">
                    <i class="zmdi zmdi-close"></i>
                </button>

                <div class="block2-pic hov-img0">
                    <img src="<?= $producto['portada'] ?>" alt="<?= $producto['nombre'] ?>" class="img-wishlist-full">
                    
                    <a href="<?= $ruta ?>" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                        Ver Detalles
                    </a>
                </div>

                <div class="block2-txt text-center p-t-20 p-b-20 p-lr-15">
                    <a href="<?= $ruta ?>" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-5 block font-weight-bold">
                        <?= $producto['nombre'] ?>
                    </a>
                    <span class="stext-105 cl3 color-gold">
                        <?= SMONEY.formatMoneda($producto['precio']) ?>
                    </span>
                </div>
            </div>
        </div>
        <?php 
            }
        } ?>
    </div>
</div>

<?php footerTienda($data); ?>