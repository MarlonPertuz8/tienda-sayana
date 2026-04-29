<?php headerTienda($data); ?>

<style>
    .sayana-logo-header {
        text-align: center;
        padding-top: 20px;
        /* Reducido de 35px */
        padding-bottom: 0px;
        /* Eliminado para pegar más el título */
        margin-bottom: -10px;
        /* Margen negativo para subir el título */
    }

    .sayana-logo-header img {
        width: 110px;
        /* Un poco más pequeño ayuda a compactar */
        height: auto;
    }

    .login-box-sayana {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        /* Reducido un poco para mayor elegancia */
        margin: 0 auto;
        overflow: hidden;
    }

    /* CONTENEDOR DEL LOGO */
    .sayana-logo-header {
        text-align: center;
        padding-top: 35px;
        /* Espacio superior */
        padding-bottom: 10px;
    }

    .sayana-logo-header img {
        width: 130px;
        /* Tamaño controlado para no estirar la card */
        height: auto;
    }

    #divLoading {
        position: fixed;
        top: 0;
        width: 100%;
        height: 100%;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
    }

    .btn-sayana-unificado {
        background-color: #222;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-sayana-unificado:hover {
        background-color: #f3635a;
        color: white;
    }

    .link-sayana {
        color: #666;
        transition: 0.3s;
    }

    .link-sayana:hover {
        color: #f3635a !important;
        text-decoration: none;
    }
</style>

<div id="divLoading">
    <div><img src="<?= media(); ?>/images/spinner-double.svg" alt="Cargando"></div>
</div>

<section class="p-t-130 p-b-80" style="background-color: #f7f7f7;">
    <div class="container">
        <div class="login-box-sayana">

            <div class="sayana-logo-header">
                <img src="<?= media(); ?>/images/logoSayana.png" alt="Sayana Luxury">
            </div>

            <form id="formLogin" name="formLogin">

                <input type="hidden" name="origen" value="tienda">

                <h4 class="mtext-105 cl2 txt-center p-b-25">INICIAR SESIÓN</h4>

                <div class="bor8 m-b-20 how-pos4-parent">
                    <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30"
                        type="email"
                        id="txtEmail"
                        name="txtEmail"
                        placeholder="Correo electrónico"
                        required autofocus>
                    <i class="how-pos4 zmdi zmdi-email"></i>
                </div>

                <div class="bor8 m-b-20 how-pos4-parent">
                    <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30"
                        type="password"
                        id="txtPassword"
                        name="txtPassword"
                        placeholder="Contraseña"
                        required>
                    <i class="how-pos4 zmdi zmdi-lock"></i>
                </div>

                <div class="p-b-25 txt-right">
                    <a href="#" class="stext-107 link-sayana" onclick="toggleForm(event)">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button type="submit"
                    class="flex-c-m stext-101 size-121 btn-sayana-unificado trans-04 pointer">
                    INGRESAR
                </button>
            </form>

            <form class="d-none" id="formRecetPass" name="formRecetPass">
                <h4 class="mtext-105 cl2 txt-center p-b-30">RECUPERAR CLAVE</h4>
                <div class="bor8 m-b-30 how-pos4-parent">
                    <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="email" id="txtEmailReset" name="txtEmailReset" placeholder="Tu correo electrónico">
                    <i class="how-pos4 zmdi zmdi-email"></i>
                </div>
                <button type="submit" class="flex-c-m stext-101 size-121 btn-sayana-unificado trans-04 pointer m-b-20">ENVIAR</button>
                <div class="txt-center">
                    <a href="#" class="stext-107 link-sayana" onclick="toggleForm(event)">Volver al Login</a>
                </div>
            </form>
        </div>

        <div class=" p-b-25 txt-center" style="background-color: #f9f9f9; border-top: 1px solid #eee;">
            <p class="stext-107 cl6">¿No tienes cuenta? <a href="<?= base_url(); ?>/carrito/procesarpago" class="link-sayana" style="font-weight: bold;">Regístrate</a></p>
        </div>
    </div>
    </div>
</section>

<script>
    function toggleForm(e) {
        e.preventDefault();
        document.getElementById('formLogin').classList.toggle('d-none');
        document.getElementById('formRecetPass').classList.toggle('d-none');
    }
</script>

<?php footerTienda($data); ?>