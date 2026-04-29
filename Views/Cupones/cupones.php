<?php 
  headerAdmin($data); 
  getModal('modalCupones',$data);
?>
<main class="app-content">
  <div class="app-title">
    <div>
      <h1 class="text-dark font-weight-bold">
        <i class="fa fa-ticket" style="color: #c9a050;"></i> <?= $data['page_title'] ?>
        <?php if($_SESSION['permiso_modulo']['w']){ ?>
        <button class="btn btn-primary" type="button" onclick="openModal();" ><i class="fas fa-plus-circle"></i> Nuevo Cupón</button>
        <?php } ?>
      </h1>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tableCupones">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Código</th>
                  <th>Descuento (%)</th>
                  <th>Estado</th>
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