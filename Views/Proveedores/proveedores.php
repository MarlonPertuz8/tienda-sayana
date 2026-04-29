<?php 
  headerAdmin($data); 
  getModal('modalProveedores', $data); 
?>
<main class="app-content">
  <div class="app-title">
    <div>
      <h1>
        <i class="fas fa-truck-loading"style="color: #c9a050;"></i> <?= $data['page_title'] ?>
        <?php if (!empty($_SESSION['permisos'][4]['w'])) { ?>
          <button class="btn btn-primary ml-3 shadow-sm" type="button" onclick="openModal();" style="border-radius: 10px;">
            <i class="fas fa-plus-circle"></i> Nuevo Proveedor
          </button>
        <?php } ?>
      </h1>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="<?= base_url(); ?>/proveedores">Proveedores</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="tile border-0 shadow-sm" style="border-radius: 15px;">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tableProveedores">
              <thead class="thead-dark">
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>NIT</th>
                  <th>Teléfono</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php footerAdmin($data); ?>