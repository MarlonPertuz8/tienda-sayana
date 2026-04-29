<?php 
    headerAdmin($data); 
    getModal('modalProductos',$data);
    getModal('modalImportar',$data);

    // Cargamos también un modal pequeño para la subida del Excel
?>
<main class="app-content">
  <div class="app-title bg-white shadow-sm rounded-lg p-4">
    <div>
      <h1 class="text-dark font-weight-bold">
        <i class="fas fa-box-open" style="color: #c9a050;"></i> <?= $data['page_title'] ?>
      </h1>
      <p class="text-muted mb-0">Gestión de inventario, precios y stock de productos</p>
    </div>
    <div class="d-flex align-items-center">
        <?php if($_SESSION['permiso_modulo']['w']){ ?>
          <button class="btn btn-primary shadow-sm px-4 mr-2" type="button" onclick="fntModalImportar();">
            <i class="fas fa-file-import"></i> Importar
          </button>

          <button class="btn btn-primary shadow-sm px-4" type="button" onclick="openModal();">
            <i class="fas fa-plus-circle"></i> Nuevo Producto
          </button>
        <?php } ?>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="tile border-0 shadow-sm rounded-lg">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tableProductos">
              <thead class="thead-light">
                <tr>
                  <th>ID</th>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Stock</th>
                  <th>Precio</th>
                  <th>Estado</th>
                  <th class="text-center">Acciones</th>
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