<?php 
  headerAdmin($data); 
  // Cargamos los dos modales que vamos a usar
  getModal('modalContactos', $data); 
  getModal('modalRespuesta', $data); 
?>
<main class="app-content">
  <div class="app-title">
    <div>
      <h1 class="text-dark font-weight-bold">
        <i class="fa fa-envelope-o" style="color: #c9a050;"></i> <?= $data['page_title']; ?></h1>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="tile" style="border-radius: 0;"> 
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tableContactos" style="border-radius: 0; width: 100%;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Fecha</th>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th>Estado</th> <th>Acciones</th>
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