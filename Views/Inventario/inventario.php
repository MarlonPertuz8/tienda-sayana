<?php 
  headerAdmin($data); 
  getModal('modalInventario', $data); 
?>
<main class="app-content">
  <div class="app-title">
    <div>
      <h1>
        <i class="fas fa-boxes"style="color: #c9a050;"></i> <?= $data['page_title'] ?>
        <?php if ($data['permisos_modulo']['w']) { ?>
          <button class="btn btn-primary ml-3" type="button" onclick="openModal();">
            <i class="fas fa-plus-circle"></i> Nuevo Ingreso
          </button>
        <?php } ?>
      </h1>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="<?= base_url(); ?>/inventario">Inventario</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-6 col-lg-3">
      <div class="widget-small primary coloured-icon shadow-sm rounded-lg border-0">
        <i class="icon fas fa-coins fa-3x"></i>
        <div class="info">
          <h4>Inversión</h4>
          <p><b id="totalInversion">0</b></p>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="widget-small info coloured-icon shadow-sm rounded-lg border-0">
        <i class="icon fas fa-gem fa-3x"></i>
        <div class="info">
          <h4>Stock Total</h4>
          <p><b id="totalPiezas">0 Unds</b></p>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="widget-small warning coloured-icon shadow-sm rounded-lg border-0">
        <i class="icon fas fa-exclamation-triangle fa-3x"></i>
        <div class="info">
          <h4>Bajo Stock</h4>
          <p><b id="productosAlerta">0 Prods</b></p>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="widget-small danger coloured-icon shadow-sm rounded-lg border-0">
        <i class="icon fas fa-shuttle-van fa-3x"></i>
        <div class="info">
          <h4>Proveedores</h4>
          <p><b id="totalProveedores">0</b></p>
        </div>
      </div>
    </div>
  </div>

 <div class="row mb-4">
  <div class="col-md-12">
    <div class="tile border-0 shadow-sm rounded-lg p-4"> <!-- Mantenemos tus bordes redondeados -->
      <div class="tile-body">
        <div class="row align-items-center">
          
          <!-- Reporte: Abajo le damos margen en móvil con mb-3 -->
          <div class="col-12 col-md-4 mb-3 mb-md-0">
            <button class="btn btn-lg-capsule btn-pdf-report w-100" type="button" onclick="generarReportePDF();">
              <i class="fas fa-file-pdf mr-2"></i> Reporte Inventario 
            </button>
          </div>

          <!-- Fecha Inicio -->
          <div class="col-12 col-sm-6 col-md-3 mb-3 mb-md-0">
            <div class="custom-date-container shadow-sm">
              <i class="fas fa-calendar-alt"></i>
              <input type="text" id="txtFechaInicio" class="flatpickr-input" placeholder="Fecha Inicio">
            </div>
          </div>

          <!-- Fecha Fin -->
          <div class="col-12 col-sm-6 col-md-3 mb-3 mb-md-0">
            <div class="custom-date-container shadow-sm">
              <i class="fas fa-calendar-check"></i>
              <input type="text" id="txtFechaFin" class="flatpickr-input" placeholder="Fecha Fin">
            </div>
          </div>

          <!-- Filtrar -->
          <div class="col-12 col-md-2 mt-2 mt-md-0">
            <button class="btn btn-lg-capsule btn-filter-custom w-100" type="button" onclick="generarReporteMovimientos();">
              <i class="fas fa-filter mr-2"></i> Filtrar
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

  <div class="row">
    <div class="col-md-12">
      <div class="tile border-0 shadow-sm rounded-lg">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tableInventario">
              <thead class="thead-dark">
                <tr>
                  <th>Fecha</th>
                  <th>Producto</th>
                  <th>Tipo</th>
                  <th>Cant.</th>
                  <th>Costo Unit.</th>
                  <th>Proveedor / Referencia</th>
                  <th>Usuario</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php footerAdmin($data); ?>