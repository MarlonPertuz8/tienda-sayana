<?php headerTienda($data); ?>

<section class="bg-img1 txt-center p-lr-15 p-tb-92 header-perfil-sayana">
    <h2 class="ltext-105 cl0 txt-center">Mi Cuenta</h2>
</section>

<section class="bg0 p-t-70 p-b-116">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-lg-3 p-b-50">
                <div class="bor10 p-t-30 p-b-40 p-lr-30 p-lr-15-sm m-r-20 m-r-0-sm card-perfil-sayana">
                    <div class="flex-col-c p-b-20">
                        <div class="size-211 bor7 flex-c-m p-b-10 m-b-10 avatar-wrapper">
                            <img src="<?= media(); ?>/images/avatar.png" alt="User">
                        </div>
                        <span class="mtext-101 cl2 txt-center"><?= $_SESSION['userData']['nombre'] . ' ' . $_SESSION['userData']['apellido']; ?></span>
                        <span class="stext-109 cl4 txt-center">Cliente VIP</span>
                    </div>

                    <div class="nav-sayana-luxury">
                        <a href="<?= base_url(); ?>/clientes/perfil" class="nav-link-sayana active">
                            <span class="nav-icon"><i class="fa fa-user-circle"></i></span>
                            <span class="nav-text">Mi Perfil</span>
                        </a>

                        <a href="#" class="nav-link-sayana btnMisPedidos"> <span class="nav-icon"><i class="fa fa-shopping-bag"></i></span>
                            <span class="nav-text">Mis Pedidos</span>
                        </a>

                        <div class="nav-divider"></div>

                        <a href="<?= base_url(); ?>/logout" class="nav-link-sayana logout-link">
                            <span class="nav-icon"><i class="fa fa-power-off"></i></span>
                            <span class="nav-text">Cerrar Sesión</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-8 col-lg-9 p-b-50">
                <div id="renderPerfil">
                <div class="bor10 p-lr-40 p-t-40 p-b-40 p-lr-15-sm card-perfil-sayana">
                    <h4 class="mtext-109 cl2 p-b-30 title-perfil-sayana">
                        <i class="fa fa-id-card m-r-10"></i> Información Personal
                    </h4>

                    <form id="formPerfilCliente">
                        <div class="row">
                            <div class="col-md-6 m-b-25">
                                <span class="stext-110 cl2">Nombre</span>
                                <div class="bor8 m-t-10 how-pos4-parent input-sayana-perfil">
                                    <input class="stext-111 cl2 plh3 size-116 p-l-28" type="text" name="txtNombre" value="<?= $_SESSION['userData']['nombre']; ?>">
                                </div>
                            </div>

                            <div class="col-md-6 m-b-25">
                                <span class="stext-110 cl2">Apellido</span>
                                <div class="bor8 m-t-10 how-pos4-parent input-sayana-perfil">
                                    <input class="stext-111 cl2 plh3 size-116 p-l-28" type="text" name="txtApellido" value="<?= $_SESSION['userData']['apellido']; ?>">
                                </div>
                            </div>

                            <div class="col-md-12 m-b-25">
                                <span class="stext-110 cl2">Correo Electrónico</span>
                                <div class="bor8 m-t-10 how-pos4-parent input-email-readonly">
                                    <input class="stext-111 cl2 plh3 size-116 p-l-28" type="email" value="<?= $_SESSION['userData']['email_user']; ?>" readonly>
                                    <i class="fa fa-lock lock-icon-sayana"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex-w flex-m p-t-15">
                            <button class="flex-c-m btn-sayana-perfil trans-04 pointer">
                                GUARDAR CAMBIOS
                            </button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php footerTienda($data); ?>