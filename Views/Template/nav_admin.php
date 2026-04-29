<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
  <div class="app-sidebar__user">
    <img class="app-sidebar__user-avatar" src="<?= media(); ?>/images/avatar.png" alt="User Image">
    <div>
      <p class="app-sidebar__user-name"><?= $_SESSION['userData']['nombre']; ?></p>
      <p class="app-sidebar__user-designation"><?= $_SESSION['userData']['nombrerol']; ?></p>
    </div>
  </div>

  <ul class="app-menu">
    <li>
      <a class="app-menu__item" href="<?= base_url(); ?>" target="_blank">
        <i class="app-menu__icon fa fa-external-link-alt"></i>
        <span class="app-menu__label">Ver Tienda</span>
      </a>
    </li>

    <?php if (!empty($_SESSION['permisos'][1]['r'])) { ?>
      <li><a class="app-menu__item" href="<?= base_url(); ?>/dashboard"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Dashboard</span></a></li>
    <?php } ?>

    <?php if (!empty($_SESSION['permisos'][2]['r'])) { ?>
      <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-solid fa-users"></i><span class="app-menu__label">Usuarios</span><i class="treeview-indicator fa fa-angle-right"></i></a>
        <ul class="treeview-menu">
          <li><a class="treeview-item" href="<?= base_url(); ?>/usuarios"><i class="icon fa fa-users"></i> Usuarios</a></li>
          <li><a class="treeview-item" href="<?= base_url(); ?>/roles"><i class="icon fa fa-user-shield"></i> Roles</a></li>
        </ul>
      </li>
    <?php } ?>

    <?php if (!empty($_SESSION['permisos'][3]['r'])) { ?>
      <li><a class="app-menu__item" href="<?= base_url(); ?>/clientes"><i class="app-menu__icon fa fa-solid fa-user"></i><span class="app-menu__label">Clientes</span></a></li>
    <?php } ?>

    <?php if (!empty($_SESSION['permisos'][4]['r']) || !empty($_SESSION['permisos'][6]['r']) || !empty($_SESSION['permisos'][7]['r']) || !empty($_SESSION['permisos'][8]['r']) || !empty($_SESSION['permisos'][9]['r']) || !empty($_SESSION['permisos'][10]['r'])) { ?>
      <li class="treeview">
        <a class="app-menu__item" href="#" data-toggle="treeview">
          <i class="app-menu__icon fa fa-store"></i>
          <span class="app-menu__label">Tienda</span>
          <span id="badgeTiendaPadre" class="badge badge-danger" style="display:none; margin-left: 8px; border-radius: 10px; font-size: 10px;">!</span>
          <i class="treeview-indicator fa fa-angle-right"></i>
        </a>
        <ul class="treeview-menu">
          <?php if (!empty($_SESSION['permisos'][4]['r'])) { ?>
            <li>
              <a class="treeview-item" href="<?= base_url(); ?>/productos">
                <i class="icon fa fa-shopping-bag"></i>
                Productos
                <span id="badgeStockSidebar" class="badge badge-danger" style="display:none; margin-left: 8px; border-radius: 10px; font-size: 10px;">0</span>
              </a>
            </li>
            <li>
              <a class="treeview-item" href="<?= base_url(); ?>/inventario">
                <i class="icon fas fa-boxes"></i>
                Inventario
              </a>
            </li>
            <li>
              <a class="treeview-item" href="<?= base_url(); ?>/proveedores">
                <i class="icon fas fa-hands-helping"></i>
                Proveedores
              </a>
            </li>
          <?php } ?>

          <?php if (!empty($_SESSION['permisos'][6]['r'])) { ?>
            <li><a class="treeview-item" href="<?= base_url(); ?>/categorias"><i class="icon fa fa-tags"></i> Categorias</a></li>
          <?php } ?>

          <?php if (!empty($_SESSION['permisos'][7]['r'])) { ?>
            <li><a class="treeview-item" href="<?= base_url(); ?>/slider"><i class="icon fas fa-image"></i> Sliders</a></li>
          <?php } ?>

          <?php if (!empty($_SESSION['permisos'][8]['r'])) { ?>
            <li><a class="treeview-item" href="<?= base_url(); ?>/cupones"><i class="icon fas fa-ticket-alt"></i> Cupones</a></li>
          <?php } ?>

          <?php if (!empty($_SESSION['permisos'][9]['r'])) { ?>
            <li><a class="treeview-item" href="<?= base_url(); ?>/blog"><i class="icon fa fa-file-text"></i> Blog</a></li>
          <?php } ?>

          <?php if (!empty($_SESSION['permisos'][10]['r'])) { ?>
            <li><a class="treeview-item" href="<?= base_url(); ?>/AdminNosotros"><i class="icon fa fa-info-circle"></i> Nosotros</a></li>
          <?php } ?>
        </ul>
      </li>
    <?php } ?>

    <?php if (!empty($_SESSION['permisos'][11]['r'])) { ?>
      <li>
        <a class="app-menu__item" href="<?= base_url(); ?>/contactos">
          <i class="app-menu__icon fa fa-envelope"></i>
          <span class="app-menu__label">Mensajes</span>
          <span id="badgeMensajes" class="badge badge-danger" style="display:none; margin-left: 8px; border-radius: 10px;">0</span>
        </a>
      </li>
    <?php } ?>

    <?php if (!empty($_SESSION['permisos'][5]['r'])) { ?>
      <li>
        <a class="app-menu__item" href="<?= ($_SESSION['userData']['idrol'] == 1) ? base_url() . '/PedidosA' : base_url() . '/pedidos'; ?>">
          <i class="app-menu__icon fa fa-solid fa-shopping-cart"></i>
          <span class="app-menu__label">
            <?= ($_SESSION['userData']['idrol'] == 1) ? "Gestión Pedidos" : "Mis Pedidos"; ?>
          </span>
          <span id="badgePedidosSidebar" class="badge badge-success" style="display:none; margin-left: 8px; border-radius: 10px;">0</span>
        </a>
      </li>
    <?php } ?>

    <li><a class="app-menu__item" href="<?= base_url(); ?>/logout"><i class="app-menu__icon fa fa-solid fa-sign-out"></i><span class="app-menu__label">Logout</span></a></li>
  </ul>
</aside>