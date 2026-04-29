<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $data['tag_page']; ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?= media() ?>/tienda/images/icons/icono.ico" />
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/fonts/iconic/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/fonts/linearicons-v1.0.0/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/vendor/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/vendor/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/vendor/MagnificPopup/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/vendor/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/css/util.css">
    <link rel="stylesheet" type="text/css" href="<?= media() ?>/tienda/css/main.css">
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>/tienda/css/custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <script>
        const base_url = "<?= base_url(); ?>";
    </script>
</head>

<body class="animsition <?= (isset($data['page_name']) && $data['page_name'] == 'inicio') ? 'home-page' : ''; ?>" data-baseurl="<?= base_url(); ?>">

    <?php
    $cantCarrito = 0;
    if (isset($_SESSION['arrCarrito']) && !empty($_SESSION['arrCarrito'])) {
        foreach ($_SESSION['arrCarrito'] as $producto) {
            $cantCarrito += $producto['cantidad'];
        }
    }
    ?>

   <header id="header-sayana">
        <div class="container-menu-desktop">
            <div class="wrap-menu-desktop">
                <nav class="limiter-menu-desktop container">

                    <a href="<?= base_url(); ?>" class="logo">
                        <img src="<?= media(); ?>/tienda/images/icons/logo-01.png" alt="Sayana Luxury">
                    </a>

                    <div class="menu-desktop">
                        <ul class="main-menu">
                            <li class="<?= ($data['page_name'] == 'inicio') ? 'active-menu' : ''; ?>"><a href="<?= base_url(); ?>">Inicio</a></li>
                            <li class="<?= ($data['page_name'] == 'tienda') ? 'active-menu' : ''; ?>"><a href="<?= base_url(); ?>/tienda">Tienda</a></li>
                            <li><a href="<?= base_url(); ?>/blogtienda/blog">Blog</a></li>
                            <li><a href="<?= base_url(); ?>/nosotros/nosotros">Nosotros</a></li>
                            <li><a href="<?= base_url(); ?>/contacto">Contacto</a></li>
                        </ul>
                    </div>

                    <div class="wrap-icon-header flex-w flex-r-m">
                        <div class="pos-relative dis-flex align-items-center">
                            <form action="<?= base_url(); ?>/tienda" method="get" class="sayana-search-bar">
                                <input type="text" name="s" placeholder="Buscar joyas..." class="sayana-input-search">
                            </form>
                            <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-toggle-search">
                                <i class="zmdi zmdi-search"></i>
                            </div>
                        </div>

                        <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart" data-notify="<?= $cantCarrito; ?>">
                            <i class="zmdi zmdi-shopping-cart"></i>
                        </div>


                        <div class="wrap-header-user pos-relative p-l-22">
                            <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 icon-header-noti js-show-notifications cantNotificacion" data-notify="<?= (isset($data['cant_notif']) && $data['cant_notif'] > 0) ? $data['cant_notif'] : '0'; ?>">
                                <i class="zmdi zmdi-notifications"></i>
                            </div>

                            <div class="user-dropdown-content" style="width: 300px;">
                                <div class="user-header">
                                    <span class="user-name">Notificaciones</span>
                                </div>
                                <div id="listNotificaciones" class="user-body" style="max-height: 350px; overflow-y: auto;">
                                    <p style="text-align:center; padding:20px; color:#ccc;">Cargando...</p>
                                </div>
                                <div class="user-footer" style="padding: 10px; border-top: 1px solid #eee; text-align: center;">
                                    <a href="<?= base_url(); ?>/pedidos" class="user-link" style="justify-content: center; color: #b2944d;">
                                        Ver todo el historial
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="wrap-header-user pos-relative p-l-22">
                            <div class="icon-header-item cl2 hov-cl1 trans-04 pointer js-show-user">
                                <i class="zmdi zmdi-account-circle"></i>
                            </div>

                            <div class="user-dropdown-content">
                                <div class="user-header">
                                    <span class="user-name">Hola, <?= $_SESSION['userData']['nombre'] ?? 'Invitado'; ?></span>
                                </div>
                                <div class="user-body">
                                    <a href="<?= base_url(); ?>/tienda/wishlist" class="user-link">
                                        <i class="zmdi zmdi-favorite-outline"></i> Mi Wishlist
                                    </a>
                                    <div class="user-divider"></div>

                                    <?php if (isset($_SESSION['login'])): ?>
                                        <a href="<?= base_url(); ?>/clientes/perfil" class="user-link">
                                            <i class="zmdi zmdi-account"></i> Mi Perfil
                                        </a>
                                        <div class="user-divider"></div>
                                        <a href="<?= base_url(); ?>/logout" class="user-link logout">
                                            <i class="zmdi zmdi-power"></i> Salir
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url(); ?>/login/tienda" class="user-link">
                                            <i class="zmdi zmdi-key"></i> Iniciar Sesión
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <div class="wrap-header-mobile">
            <div class="search-mobile-integrated">
                <form class="sayana-form-full" method="get" action="<?= base_url(); ?>/tienda">
                    <input class="search-input-mobile" type="text" name="s" placeholder="Buscar en Sayana...">
                    <button type="button" class="btn-hide-search js-hide-modal-search">
                        <i class="zmdi zmdi-close-circle"></i>
                    </button>
                </form>
            </div>

            <div class="logo-mobile">
                <a href="<?= base_url(); ?>"><img src="<?= media(); ?>/tienda/images/icons/logo-01.png" alt="IMG-LOGO"></a>
            </div>

            <div class="wrap-icon-header flex-w flex-r-m m-r-15">
                <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 js-show-modal-search">
                    <i class="zmdi zmdi-search"></i>
                </div>

                <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti js-show-cart" data-notify="<?= $cantCarrito; ?>">
                    <i class="zmdi zmdi-shopping-cart"></i>
                </div>

                <div class="wrap-header-user pos-relative p-l-22">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 icon-header-noti js-show-notifications cantNotificacion" data-notify="<?= (isset($data['cant_notif']) && $data['cant_notif'] > 0) ? $data['cant_notif'] : '0'; ?>">
                        <i class="zmdi zmdi-notifications"></i>
                    </div>

                    <div class="user-dropdown-content" style="width: 300px;">
                        <div class="user-header">
                            <span class="user-name">Notificaciones</span>
                        </div>
                        <div id="listNotificaciones" class="user-body" style="max-height: 350px; overflow-y: auto;">
                            <p style="text-align:center; padding:20px; color:#ccc;">Cargando...</p>
                        </div>
                        <div class="user-footer" style="padding: 10px; border-top: 1px solid #eee; text-align: center;">
                            <a href="<?= base_url(); ?>/notificaciones" class="user-link" style="justify-content: center; color: #b2944d;">
                                Ver todo el historial
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="wrap-header-user pos-relative p-l-10">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 js-show-user">
                        <i class="zmdi zmdi-account-circle"></i>
                    </div>

                    <div class="user-dropdown-content">
                        <div class="user-header">
                            <span class="user-name">Hola, <?= $_SESSION['userData']['nombre'] ?? 'Invitado'; ?></span>
                        </div>

                        <div class="user-body">
                            <a href="<?= base_url(); ?>/tienda/wishlist" class="user-link">
                                <i class="zmdi zmdi-favorite-outline"></i> Mi Wishlist
                            </a>
                            <div class="user-divider"></div>

                            <?php if (isset($_SESSION['login'])): ?>
                                <a href="<?= base_url(); ?>/dashboard" class="user-link">
                                    <i class="zmdi zmdi-view-dashboard"></i> Mi Panel
                                </a>
                                <a href="<?= base_url(); ?>/clientes/perfil" class="user-link">
                                    <i class="zmdi zmdi-account"></i> Mi Perfil
                                </a>
                                <div class="user-divider"></div>
                                <a href="<?= base_url(); ?>/logout" class="user-link logout">
                                    <i class="zmdi zmdi-power"></i> Salir
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url(); ?>/login/tienda" class="user-link">
                                    <i class="zmdi zmdi-key"></i> Iniciar Sesión
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <audio id="sonidoNotificacion" preload="auto">
        <source src="<?= media(); ?>/tienda/sound/notificacion.mp3" type="audio/mpeg">
    </audio>

    <div class="wrap-header-cart js-panel-cart">
        <div class="s-full js-hide-cart"></div>
        <div class="header-cart flex-col-l p-l-25 p-r-25">
            <div class="header-cart-title flex-w flex-sb-m p-b-8">
                <span class="mtext-103 cl2">Tu Carrito</span>
                <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                    <i class="zmdi zmdi-close"></i>
                </div>
            </div>

            <div class="header-cart-content flex-w js-pscroll w-full">
                <div id="modalCarrito" class="w-full">
                    <?php getModal('modalCarrito', $_SESSION['arrCarrito'] ?? []); ?>
                </div>
            </div>
        </div>
    </div>