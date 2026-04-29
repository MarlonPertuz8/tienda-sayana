<?php headerAdmin($data); ?>
<main class="app-content">
  <div class="app-title bg-white shadow-sm rounded-lg p-4">
    <div>
      <h1 class="text-dark font-weight-bold">
        <i class="fas fa-tasks" style="color: #c9a050;"></i> <?= $data['page_title'] ?>
      </h1>
      <p class="text-muted mb-0">Panel de control de ventas globales y despachos</p>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="tile border-0 shadow-sm rounded-lg p-4">
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="tablePedidosA">
            <thead class="thead-dark text-center">
              <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Monto</th>
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
</main>
<?php footerAdmin($data); ?>