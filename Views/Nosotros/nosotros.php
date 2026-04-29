<?php headerTienda($data); ?>

<div class="sayana-nosotros-v3">
    <header class="nosotros-hero-simple" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?= media().'/images/uploads/'.$data['info']['portada']; ?>');">
        <div class="hero-content-clean">
            <h1 class="nosotros-title-clean"><?= $data['info']['titulo']; ?></h1>
        </div>
    </header>

    <section class="nosotros-body-wrap">
        <div class="nosotros-grid-equal">
            
            <div class="nosotros-image-side">
                <div class="image-wrapper-mini">
                    <img src="<?= media().'/images/uploads/'.$data['info']['imagen_secundaria']; ?>" 
                         alt="Sayana Boutique" 
                         class="img-nosotros-small">
                </div>
            </div>

            <div class="nosotros-text-side">
                <div class="nosotros-text-inner">
                    <?= $data['info']['contenido']; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?php footerTienda($data); ?>