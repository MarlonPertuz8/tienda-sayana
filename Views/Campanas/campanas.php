<?php 
    headerAdmin($data); 
    getModal('modalCampanas',$data); 
?>
<main class="app-content">
  <div class="app-title bg-white shadow-sm rounded-lg p-4">
    <div>
      <h1 class="text-dark font-weight-bold">
        <i class="fas fa-bullhorn" style="color: #c9a050;"></i> <?= $data['page_title'] ?>
      </h1>
      <p class="text-muted mb-0">Gestión de landings, promociones y eventos especiales</p>
    </div>
    <div class="d-flex align-items-center">
        <?php if($_SESSION['permiso_modulo']['w']){ ?>
          <button class="btn btn-primary shadow-sm px-4" type="button" onclick="openModal();">
            <i class="fas fa-plus-circle"></i> Nueva Campaña
          </button>
        <?php } ?>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="tile border-0 shadow-sm rounded-lg">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tableCampanas">
              <thead class="thead-light">
                <tr>
                  <th>ID</th>
                  <th>Multimedia</th> <th>Nombre</th>
                  <th>Vigencia (Inicio - Fin)</th>
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