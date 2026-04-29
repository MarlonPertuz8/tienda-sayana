<?php headerTienda($data); ?>

<style>
    /* Separación del header para que respire */
    .section-login-sayana {
        background-color: #f7f7f7;
        padding-top: 130px; /* Suficiente espacio para que no pegue al header */
        padding-bottom: 100px;
        min-height: 70vh;
        display: flex;
        align-items: center;
    }

    .login-box-sayana {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 380px; /* Tamaño compacto premium */
        margin: 0 auto;
        overflow: hidden;
        border: 1px solid #eee;
    }

    /* Logo compacto */
    .sayana-logo-header {
        text-align: center;
        padding-top: 30px;
    }
    .sayana-logo-header img {
        width: 110px;
        height: auto;
    }

    /* Botón Unificado Sayana */
    .btn-sayana-unificado {
        background-color: #1a1a1a;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        width: 100%;
        height: 45px;
    }

    .btn-sayana-unificado:hover {
        background-color: #f3635a; /* Coral al pasar el mouse */
        color: white;
        transform: translateY(-2px);
    }

    .link-sayana {
        color: #888;
        font-size: 13px;
        transition: 0.3s;
        text-decoration: none !important;
    }
    .link-sayana:hover {
        color: #f3635a !important;
    }

    #divLoading {
        position: fixed;
        top: 0; width: 100%; height: 100%;
        display: none; align-items: center; justify-content: center;
        background: rgba(255, 255, 255, 0.8); z-index: 9999;
    }
</style>

<div id="divLoading">
    <div><img src="<?= media(); ?>/images/spinner-double.svg" alt="Cargando"></div>
</div>

<section class="section-login-sayana">
    <div class="container">
        <div class="login-box-sayana">
            
            <div class="sayana-logo-header">
                <img src="<?= media(); ?>/images/logoSayana.png" alt="Sayana Luxury">
            </div>

            <div class="p-lr-40 p-t-15 p-b-40">
                <form id="formRecetPass" name="formRecetPass">
                    <h4 class="mtext-105 cl2 txt-center p-b-20">RECUPERAR CLAVE</h4>
                    
                    <p class="stext-107 cl6 txt-center p-b-20">
                        Ingresa tu correo electrónico para enviarte las instrucciones de restablecimiento.
                    </p>

                    <div class="bor8 m-b-25 how-pos4-parent">
                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="email" id="txtEmailReset" name="txtEmailReset" placeholder="Tu correo electrónico" required autofocus>
                        <i class="how-pos4 zmdi zmdi-email"></i>
                    </div>

                    <button type="submit" class="flex-c-m stext-101 btn-sayana-unificado trans-04 pointer m-b-20">
                        ENVIAR INSTRUCCIONES
                    </button>

                    <div class="txt-center">
                        <a href="<?= base_url(); ?>/login" class="link-sayana">
                            <i class="zmdi zmdi-chevron-left"></i> Volver al inicio de sesión
                        </a>
                    </div>
                </form>
            </div>

            <div class="p-t-20 p-b-25 txt-center" style="background-color: #fafafa; border-top: 1px solid #f0f0f0;">
                <p class="stext-107 cl6">¿Necesitas ayuda? <a href="<?= base_url(); ?>/contacto" class="link-sayana" style="font-weight: bold;">Contáctanos</a></p>
            </div>
        </div>
    </div>
</section>

<?php footerTienda($data); ?>