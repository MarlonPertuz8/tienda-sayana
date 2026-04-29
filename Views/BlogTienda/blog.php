<?php headerTienda($data); ?>

<div class="container p-t-100 p-b-100">
    <div class="row">

        <div class="col-md-8 col-lg-8">

            <div class="blog-hero-intro">
                <h1>Inspiración & Estilo</h1>
                <p>Descubre tendencias, consejos y el significado detrás de cada joya</p>
            </div>

            <?php if(!empty($data['posts'])): 
                $first = $data['posts'][0];
                $rutaFirst = base_url().'/blogtienda/articulo/'.$first['ruta'];
                $imgFirst = ($first['portada'] != "") 
                    ? media().'/images/uploads/'.$first['portada'] 
                    : media().'/images/uploads/portada_categoria.png';
            ?>

                <div class="blog-featured">
                    <a href="<?= $rutaFirst ?>">
                        <img src="<?= $imgFirst ?>" alt="<?= $first['titulo'] ?>">
                    </a>
                    <div class="blog-featured-content">
                        <h2><?= $first['titulo'] ?></h2>
                        <p><?= mb_substr(strip_tags($first['contenido']),0,140) ?>...</p>
                        <a href="<?= $rutaFirst ?>">Leer artículo</a>
                    </div>
                </div>

                <div class="posts-list">
                    <?php foreach ($data['posts'] as $key => $post): 
                        if($key == 0) continue; // Saltamos el destacado

                        $rutaPost = base_url().'/blogtienda/articulo/'.$post['ruta'];
                        $imagen = ($post['portada'] != "") 
                            ? media().'/images/uploads/'.$post['portada'] 
                            : media().'/images/uploads/portada_categoria.png';
                        $fecha = strtotime(str_replace('/', '-', $post['fecha']));
                    ?>

                    <article class="p-b-63 border-bottom-light blog-pro">
                        <div class="row align-items-center">
                            <div class="col-sm-5">
                                <div class="hov-img0 how-pos5-parent">
                                    <a href="<?= $rutaPost ?>">
                                        <img src="<?= $imagen ?>" alt="<?= $post['titulo'] ?>" class="img-fluid custom-blog-img">
                                    </a>
                                    <div class="date-badge-custom">
                                        <span class="day-text"><?= date("d", $fecha) ?></span>
                                        <span class="month-text"><?= date("M", $fecha) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-7 p-t-10">
                                <h4 class="p-b-12">
                                    <a href="<?= $rutaPost ?>" class="ltext-108 cl2 hov-cl1 trans-04">
                                        <?= $post['titulo'] ?>
                                    </a>
                                </h4>
                                <p class="stext-117 cl6 p-b-20">
                                    <?= mb_substr(strip_tags($post['contenido']), 0, 120) ?>...
                                </p>
                                <a href="<?= $rutaPost ?>" class="stext-101 cl2 hov-cl1 trans-04 blog-btn-pro">
                                    DESCUBRIR MÁS <i class="fa fa-long-arrow-right m-l-9"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </div>

        <aside class="col-md-4 col-lg-4">
            
            <div class="sidebar-widget">
                <div class="search-group-pro">
                    <input type="text" id="blogSearch" placeholder="¿Qué buscas hoy?">
                    
                </div>
            </div>

         
        

        </aside>

    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="blog-cta">
                <h3>Eleva tu estilo con nuestras joyas</h3>
                <p>Descubre piezas únicas diseñadas para ti</p>
                <a href="<?= base_url(); ?>/tienda">Explorar tienda</a>
            </div>
        </div>
    </div>
</div>

<?php footerTienda($data); ?>

<script>
document.getElementById("blogSearch").addEventListener("keyup", function() {
    let value = this.value.toLowerCase();
    document.querySelectorAll(".blog-pro").forEach(item => {
        item.style.display = item.innerText.toLowerCase().includes(value) ? "block" : "none";
    });
});
</script>