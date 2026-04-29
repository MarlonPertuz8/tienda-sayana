<?php 
  headerAdmin($data); 
  getModal('modalUsuarios', $data);
?>
<main class="app-content">    
  <div class="app-title bg-white shadow-sm rounded-lg p-4">
    <div>
      <h1 class="text-dark font-weight-bold">
        <i class="fas fa-user-friends"  style="color: #c9a050;" ></i> <?= $data['page_title'] ?>
      </h1>
      <p class="text-muted mb-0">Gestión de usuarios registrados en el sistema</p>
    </div>
    <div>
       <?php if($_SESSION['permiso_modulo']['w']){ ?>
          <button class="btn btn-primary shadow-sm px-4" type="button" onclick="openModalUsuario();">
            <i class="fas fa-plus-circle"></i> Nuevo Usuario
          </button>
        <?php } ?>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="tile border-0 shadow-sm rounded-lg">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tableUsuarios">
              <thead class="thead-light">
                <tr>
                  <th>ID</th>
                  <th>Nombres</th>
                  <th>Apellidos</th>
                  <th>Email</th>
                  <th>Teléfono</th>
                  <th>Rol</th>
                  <th>Status</th>
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