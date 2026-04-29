<?php
headerTienda($data);
$post = $data['post']; // Datos del artículo actual
?>

<div class="container p-t-100 p-b-100">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8 p-b-80">
            <div class="p-r-45 p-r-0-lg">
                <div class="bread-crumb flex-w p-b-30">
                    <a href="<?= base_url(); ?>" class="stext-109 cl8 hov-cl1 trans-04">
                        Inicio
                        <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
                    </a>
                    <a href="<?= base_url(); ?>/blogtienda" class="stext-109 cl8 hov-cl1 trans-04">
                        Blog
                        <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
                    </a>
                    <span class="stext-109 cl4">
                        <?= $post['titulo'] ?>
                    </span>
                </div>

                <div class="wrap-pic-w how-pos5-parent">
                    <?php
                    $img = ($post['portada'] != "")
                        ? media() . '/images/uploads/' . $post['portada']
                        : media() . '/images/uploads/portada_categoria.png';
                    ?>
                    <img src="<?= $img ?>" alt="<?= $post['titulo'] ?>" style="border-radius: 4px; width: 100%;">

                    <div class="flex-col-c-m size-123 bg9 how-pos5">
                        <span class="ltext-107 cl2 txt-center">
                            <?= explode("/", $post['fecha'])[0]; ?>
                        </span>
                        <span class="stext-109 cl3 txt-center">
                            <?= date("M", strtotime(str_replace('/', '-', $post['fecha']))); ?>
                        </span>
                    </div>
                </div>

                <div class="p-t-32">
                    <h2 class="ltext-109 cl2 p-b-28">
                        <?= $post['titulo'] ?>
                    </h2>

                    <div class="stext-117 cl6 p-b-26 article-content">
                        <?= $post['contenido'] ?>
                    </div>
                </div>
                <div class="volver-blog-container">
                    <a href="<?= base_url(); ?>/blogtienda/blog" class="btn-volver-blog">
                        <i class="fa fa-chevron-left m-r-10"></i> Volver al Blog
                    </a>
                </div>
            </div>
        </div>

        <aside class="col-md-2 col-lg-4">
        </aside>
    </div>
</div>

<style>
    .btn-volver-blog {
    display: inline-flex;
    align-items: center;
    background-color: #ffffff;
    color: #333;
    font-family: 'Poppins-Medium', sans-serif;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;

    padding: 10px 22px;
    border: 1px solid #e6e6e6;
    border-radius: 30px;

    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    cursor: pointer;
}

.btn-volver-blog i {
    font-size: 14px;
    margin-right: 12px;
    color: #f3635a;
    transition: transform 0.3s ease;
}

.btn-volver-blog:hover {
    border-color: #D4AF37;
    color: #D4AF37;
}
    /* Estilo pro para el contenido del artículo */
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        margin: 20px 0;
    }

    .article-content p {
        line-height: 1.8;
        font-size: 1.1rem;
        margin-bottom: 20px;
    }
</style>

<?php footerTienda($data); ?>