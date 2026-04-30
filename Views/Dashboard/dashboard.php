<?php headerAdmin($data); ?>
<main class="app-content">
  <div class="app-title bg-white shadow-sm rounded-lg p-4">
    <div>
      <h1 class="text-dark font-weight-bold">
        <i class="fas fa-chart-line" style="color: #c9a050;"></i> <?= $data['page_title'] ?>
      </h1>
      <p class="text-muted mb-0">Resumen ejecutivo y análisis de riesgo de Sayana Luxury</p>
    </div>
    <div>
      <button class="btn btn-outline-gold shadow-sm px-4" type="button" onclick="location.reload();">
        <i class="fas fa-sync-alt"></i> Actualizar Métricas
      </button>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-12">
      <div class="tile border-0 shadow-sm rounded-lg p-3" style="background: linear-gradient(135deg, #274e66 0%, #1a3344 100%); color: white;">
        <div class="row text-center">
          <div class="col-md-4 border-right border-secondary">
            <p class="text-uppercase mb-0" style="font-size: 0.7rem; color: #c9a050; letter-spacing: 1px;">Ventas Históricas</p>
            <h3 class="mb-0" id="txtVentasTotales"><?= formatMoneda($data['consolidado']['ventas_totales']); ?></h3>
          </div>
          <div class="col-md-4 border-right border-secondary">
            <p class="text-uppercase mb-0" style="font-size: 0.7rem; color: #c9a050; letter-spacing: 1px;">Pedidos Totales</p>
            <h3 class="mb-0" id="txtPedidosTotales"><?= $data['consolidado']['pedidos_totales']; ?></h3>
          </div>
          <div class="col-md-4">
            <p class="text-uppercase mb-0" style="font-size: 0.7rem; color: #c9a050; letter-spacing: 1px;">Ticket Promedio Total</p>
            <h3 class="mb-0" id="txtTicketHistorico"><?= formatMoneda($data['consolidado']['ticket_historico']); ?></h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-6 col-lg-3">
      <div class="widget-small info coloured-icon shadow-sm rounded-lg border-0"
        onclick="fntDetalleMetrica('ventas_mes')"
        style="cursor: pointer; transition: transform 0.2s ease;">
        <i class="icon fas fa-dollar-sign fa-3x" style="background-color: #c9a050;"></i>
        <div class="info">
          <h4 class="text-uppercase font-weight-bold mb-1">Ventas Mes</h4>
          <p><b><?= formatMoneda($data['ventas_mes']); ?></b></p>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-3">
      <div class="widget-small primary coloured-icon shadow-sm rounded-lg border-0"
        onclick="fntDetalleMetrica('pedidos_hoy')"
        style="cursor: pointer; transition: transform 0.2s ease;">
        <i class="icon fas fa-shopping-cart fa-3x" style="background: #274e66;"></i>
        <div class="info">
          <h4 class="text-uppercase font-weight-bold mb-1">Pedidos</h4>
          <p><b><?= $data['pedidos_hoy']; ?> Hoy</b></p>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-3">
      <div class="widget-small danger coloured-icon shadow-sm rounded-lg border-0"
        onclick="fntDetalleMetrica('productos_count')"
        style="cursor: pointer; transition: transform 0.2s ease;">
        <i class="icon fas fa-boxes fa-3x" style="background-color: #5d6d7e;"></i>
        <div class="info">
          <h4 class="text-uppercase font-weight-bold mb-1">Productos</h4>
          <p><b><?= $data['productos_count']; ?></b></p>
        </div>
      </div>
    </div>



    <div class="col-md-6 col-lg-3">
      <div class="widget-small warning coloured-icon shadow-sm rounded-lg border-0"
        onclick="fntDetalleMetrica('riesgo_stock')"
        style="cursor: pointer; transition: transform 0.2s ease;">
        <i class="icon fas fa-exclamation-triangle fa-3x" style="background-color: #f39c12;"></i>
        <div class="info">
          <h4 class="text-uppercase font-weight-bold mb-1">Riesgo Stock</h4>
          <p>
            <span class="badge <?php
                                if ($data['riesgo_stock'] == 'ALTO') echo 'badge-danger';
                                elseif ($data['riesgo_stock'] == 'MEDIO') echo 'badge-warning';
                                else echo 'badge-success';
                                ?>"><?= $data['riesgo_stock']; ?>
            </span>
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-4">
    <div class="col-md-7">
      <div class="tile border-0 shadow-sm rounded-lg p-4">
        <h3 class="tile-title font-weight-bold text-dark"><i class="fas fa-history mr-2 text-muted"></i> Últimos Pedidos</h3>
        <div class="table-responsive" id="contenedorUltimosPedidos">
          <table class="table table-hover table-sm">
            <thead>
              <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th class="text-center">Estado</th>
                <th class="text-right">Monto</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data['ultimos_pedidos'])): ?>
                <?php foreach ($data['ultimos_pedidos'] as $pedido): ?>
                  <tr>
                    <td class="align-middle">#<?= $pedido['idpedido'] ?></td>
                    <td class="align-middle"><?= $pedido['nombre'] ?></td>
                    <td class="text-center align-middle">
                      <?php
                      switch ($pedido['status']) {
                        case 1:
                          echo '<span class="badge badge-danger">Pendiente</span>';
                          break;
                        case 2:
                          echo '<span class="badge badge-warning text-dark">Procesando</span>';
                          break;
                        case 3:
                          echo '<span class="badge badge-info">Enviado</span>';
                          break;
                        case 4:
                          echo '<span class="badge badge-success">Entregado</span>';
                          break;
                      }
                      ?>
                    </td>
                    <td class="text-right font-weight-bold align-middle">
                      <?= formatMoneda($pedido['monto']) ?>
                    </td>
                    <td class="text-center align-middle">
                      <div class="btn-group">
                        <?php if ($pedido['status'] == 1): ?>
                          <button class="btn btn-sm btn-success" title="Confirmar Pedido"
                            onclick="fntCambiarStatus(<?= $pedido['idpedido'] ?>, 2)">
                            <i class="fas fa-check"></i>
                          </button>
                        <?php endif; ?>

                        <?php if ($pedido['status'] == 2): ?>
                          <button class="btn btn-sm btn-info" title="Marcar como Enviado"
                            onclick="fntCambiarStatus(<?= $pedido['idpedido'] ?>, 3)">
                            <i class="fas fa-truck"></i>
                          </button>
                        <?php endif; ?>

                        <?php if ($pedido['status'] == 3): ?>
                          <button class="btn btn-sm btn-primary" title="Confirmar Entrega"
                            onclick="fntCambiarStatus(<?= $pedido['idpedido'] ?>, 4)">
                            <i class="fas fa-box-open"></i>
                          </button>
                        <?php endif; ?>

                        <a href="<?= base_url(); ?>/pedidosA/orden/<?= $pedido['idpedido']; ?>"
                          class="btn btn-sm btn-dark" title="Ver Detalle">
                          <i class="fas fa-eye"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center text-muted p-3">No hay pedidos recientes.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="text-right mt-3">
          <a href="<?= base_url(); ?>/pedidosa" class="text-gold font-weight-bold" style="text-decoration:none;">Ver todo el historial →</a>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="tile border-0 shadow-sm rounded-lg p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="tile-title font-weight-bold text-dark mb-0">
            <i class="fas fa-chart-pie mr-2 text-muted"></i> Análisis Diario
          </h3>
          <input type="date" id="fechaFiltro" class="form-control form-control-sm" style="width: 150px;" value="<?= date('Y-m-d'); ?>">
        </div>

        <div class="mb-4 p-3 rounded" style="background: rgba(201, 160, 80, 0.05); border-left: 4px solid #c9a050;">
          <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.65rem;">Ticket Promedio del Día</small>
          <h4 class="mb-0 font-weight-bold" id="txtTicketPromedio" style="color: #274e66;">$ <?= $data['ticket_promedio']; ?></h4>
        </div>

        <div style="position: relative; height: 250px; width: 100%;">
          <canvas id="chartPagos"
            data-wompi="<?= (int)$data['pagos']['wompi'] ?>"
            data-trans="<?= (int)$data['pagos']['transferencia'] ?>"
            data-efec="<?= (int)$data['pagos']['efectivo'] ?>">
          </canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-6">
      <div class="tile border-0 shadow-sm rounded-lg p-4">
        <h3 class="tile-title font-weight-bold text-dark" style="font-size: 1.1rem;">
          <i class="fas fa-tags mr-2 text-muted"></i> Ventas por Categoría
        </h3>
        <div style="position: relative; height: 250px;">
          <canvas id="chartCategorias"
            data-labels='<?= json_encode($data['cat_labels']); ?>'
            data-values='<?= json_encode($data['cat_values']); ?>'>
          </canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="tile border-0 shadow-sm rounded-lg p-4">
        <h3 class="tile-title font-weight-bold text-dark" style="font-size: 1.1rem;">
          <i class="fas fa-star mr-2 text-muted"></i> Top 5 Productos
        </h3>
        <div style="position: relative; height: 250px;">
          <canvas id="chartProductos"
            data-labels='<?= json_encode($data['prod_labels']); ?>'
            data-values='<?= json_encode($data['prod_values']); ?>'>
          </canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-4">
    <div class="col-md-8">
      <div class="tile border-0 shadow-sm rounded-lg p-4">
        <h3 class="tile-title font-weight-bold text-dark" style="font-size: 1.1rem;">
          <i class="fas fa-chart-line mr-2 text-muted"></i> Tendencia de Ventas (Año Actual)
        </h3>
        <div style="position: relative; height: 320px;">
          <canvas id="chartVentasMensuales"
            data-labels='<?= json_encode($data['graficaMensual']['labels']); ?>'
            data-values='<?= json_encode($data['graficaMensual']['values']); ?>'>
          </canvas>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="tile border-0 shadow-sm rounded-lg p-4">
        <h3 class="tile-title font-weight-bold text-dark" style="font-size: 1.1rem;">
          <i class="fas fa-university mr-2 text-muted"></i> Histórico Anual
        </h3>
        <div style="position: relative; height: 320px;">
          <canvas id="chartVentasAnuales"
            data-labels='<?= json_encode($data['graficaAnual']['labels']); ?>'
            data-values='<?= json_encode($data['graficaAnual']['values']); ?>'>
          </canvas>
        </div>
      </div>
    </div>
  </div>
</main>
<div class="fab-container">
  <button class="fab-main shadow" title="Analítica VIP">
    <i class="fas fa-crown"></i>
  </button>
  <ul class="fab-options">
    <li>
      <span class="fab-label">Mejores Clientes</span>
      <button class="fab-btn btn-vip" onclick="fntDetalleMetrica('top_clientes')">
        <i class="fas fa-users"></i>
      </button>
    </li>
    <li>
      <span class="fab-label">Productos Estrella</span>
      <button class="fab-btn btn-star" onclick="fntDetalleMetrica('productos_top')">
        <i class="fas fa-star"></i>
      </button>
    </li>
  </ul>
</div>
<?php footerAdmin($data); ?>