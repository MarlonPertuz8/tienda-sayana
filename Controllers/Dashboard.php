<?php
class Dashboard extends Controllers
{

    public function __construct()
    {
        parent::__construct();
        session_start();

        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }

        if ($_SESSION['userData']['idrol'] != 1) {
            header('Location: ' . base_url() . '/pedidos');
            die();
        }

        getPermisos(1);
    }

    public function index()
    {
        $data['page_id'] = 2;
        $data['page_tag'] = "Dashboard - Sayana Tienda Virtual";
        $data['page_title'] = "Dashboard - Sayana Tienda Virtual";
        $data['page_name'] = "dashboard";

        // Datos históricos y operativos
        $data['consolidado'] = $this->model->getConsolidadoTotal();
        $ventasMes = $this->model->getVentasMes();
        $data['ventas_mes'] = $ventasMes ? $ventasMes : 0;
        $data['pedidos_hoy'] = $this->model->getPedidosHoyCount();
        $data['clientes_count'] = $this->model->getClientesCount();
        $data['productos_count'] = $this->model->getProductosCount();

        // Tabla y Riesgo
        $data['ultimos_pedidos'] = $this->model->getUltimosPedidos();
        $stockBajo = $this->model->getProductosBajoStock();
        $data['riesgo_stock'] = ($stockBajo > 20) ? "ALTO" : (($stockBajo > 0) ? "MEDIO" : "BAJO");

        // --- NUEVO: GRÁFICAS DE VENTAS (AÑO Y MES) ---
        $anioActual = date("Y");

        // 1. Datos para Ventas Mensuales (Gráfica de Líneas)
        $ventasMesGrafica = $this->model->getVentasMesGrafica($anioActual);
        $nombresMeses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
        $valoresMeses = array_fill(1, 12, 0); // Inicializar meses en 0

        foreach ($ventasMesGrafica as $venta) {
            $valoresMeses[$venta['mes']] = (float)$venta['total'];
        }

        $data['graficaMensual'] = [
            'labels' => $nombresMeses,
            'values' => array_values($valoresMeses)
        ];

        // 2. Datos para Ventas por Año (Gráfica de Barras)
        $ventasAnioGrafica = $this->model->getVentasAnioGrafica();
        $ventasAnioGrafica = array_reverse($ventasAnioGrafica); // De más antiguo a más reciente

        $data['graficaAnual'] = [
            'labels' => array_column($ventasAnioGrafica, 'anio'),
            'values' => array_column($ventasAnioGrafica, 'total')
        ];
        // ----------------------------------------------

        // Datos para Gráficos de Pagos (Hoy)
        $fechaHoy = date("Y-m-d");
        $pagosHoy = $this->model->getPagosDia($fechaHoy);
        $data['pagos'] = [
            'wompi' => $pagosHoy['wompi'],
            'transferencia' => $pagosHoy['trans'],
            'efectivo' => $pagosHoy['efec']
        ];

        $data['adicionales'] = $this->getMetricasAdicionales($fechaHoy);
        $data['ticket_promedio'] = $data['adicionales']['ticket_promedio'];
        $data['cat_labels'] = $data['adicionales']['cat_labels'];
        $data['cat_values'] = $data['adicionales']['cat_values'];
        $data['prod_labels'] = $data['adicionales']['prod_labels'];
        $data['prod_values'] = $data['adicionales']['prod_values'];

        $data['page_functions_js'] = "functions_dashboard.js";
        $this->views->getView($this, "dashboard", $data);
    }


    private function getMetricasAdicionales($fecha)
    {

        $ventasCat = $this->model->getVentasCategorias($fecha);
        $topProd = $this->model->getTopProductos($fecha);
        $ticket = $this->model->getTicketPromedio($fecha);

        return [
            "ticket_promedio" => formatMoneda($ticket),
            "cat_labels" => array_column($ventasCat, 'categoria'),
            "cat_values" => array_column($ventasCat, 'cantidad'),
            "prod_labels" => array_column($topProd, 'producto'),
            "prod_values" => array_column($topProd, 'total')
        ];
    }

    public function getPagosDia()
    {
        if ($_GET) {
            $fecha = $_GET['fecha'];

            // Obtenemos los pagos y el consolidado desde el modelo
            $pagosData = $this->model->getPagosDia($fecha);

            // Obtenemos las métricas adicionales (categorías, productos, etc.)
            $adicionales = $this->getMetricasAdicionales($fecha);

            // CONSTRUIMOS LA RESPUESTA COMPLETA
            $arrData = array(
                "status" => true,
                "wompi"  => $pagosData['wompi'],
                "trans"  => $pagosData['trans'],
                "efec"   => $pagosData['efec'],
                "consolidado" => $pagosData['consolidado'], // <--- ESTO ES LO QUE TE FALTABA
                "adicionales" => $adicionales
            );

            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function getDetalleWidget(string $tipo)
    {

        $isAdmin = ($_SESSION['idUser'] == 1);
        $hasPermission = !empty($_SESSION['permisosMod']['r']);

        if (!$isAdmin && !$hasPermission) {
            echo json_encode(['status' => false, 'msg' => 'Acceso denegado.'], JSON_UNESCAPED_UNICODE);
            die();
        }

        // 2. Forzamos JSON y limpiamos buffers
        header('Content-Type: application/json');
        if (ob_get_length()) ob_clean();

        $html = "";
        $status = true;
        $thead = "";
        $tbody = "";

        switch ($tipo) {
            case 'riesgo_stock':
                $data = $this->model->getDetalleStockBajo();
                $thead = '<tr><th>Producto</th><th class="text-center">Stock Actual</th></tr>';
                if (!empty($data)) {
                    foreach ($data as $p) {
                        $tbody .= "<tr>
                                 <td>{$p['nombre']}</td>
                                 <td class='text-center text-danger font-weight-bold'>{$p['stock']}</td>
                               </tr>";
                    }
                }
                break;

            case 'pedidos_hoy':
                $data = $this->model->getDetallePedidosHoy();
                $thead = '<tr><th>ID</th><th>Cliente</th><th class="text-right">Monto</th></tr>';
                if (!empty($data)) {
                    foreach ($data as $ped) {
                        $tbody .= "<tr>
                                 <td>#{$ped['idpedido']}</td>
                                 <td>{$ped['nombre']}</td>
                                 <td class='text-right font-weight-bold'>" . formatMoneda($ped['monto']) . "</td>
                               </tr>";
                    }
                }
                break;

            case 'ventas_mes':
                $data = $this->model->getDetalleVentasMes();
                $thead = '<tr><th>Fecha</th><th class="text-right">Total Recaudado</th></tr>';
                if (!empty($data)) {
                    foreach ($data as $v) {
                        $tbody .= "<tr>
                                 <td>{$v['fecha']}</td>
                                 <td class='text-right font-weight-bold'>" . formatMoneda($v['total']) . "</td>
                               </tr>";
                    }
                }
                break;

            case 'productos_count':
                $data = $this->model->getConteoPorCategorias();
                $thead = '<tr><th>Categoría</th><th class="text-center">Cant. Productos</th></tr>';
                if (!empty($data)) {
                    foreach ($data as $c) {
                        $tbody .= "<tr>
                                 <td>{$c['nombre']}</td>
                                 <td class='text-center font-weight-bold'>{$c['cantidad']}</td>
                               </tr>";
                    }
                }
                break;

            case 'top_clientes':
                $data = $this->model->getTopClientes();
                $thead = '<tr><th>Cliente</th><th class="text-center">Pedidos</th><th class="text-right">Total Invertido</th></tr>';
                if (!empty($data)) {
                    foreach ($data as $c) {
                        $tbody .= "<tr>
                        <td>{$c['nombre']} {$c['apellido']}</td>
                        <td class='text-center'>{$c['cantidad_pedidos']}</td>
                        <td class='text-right font-weight-bold'>" . formatMoneda($c['total_gastado']) . "</td>
                    </tr>";
                    }
                }
                break;

            case 'productos_top':
                $data = $this->model->getProductosEstrella();
                $thead = '<tr><th>Producto</th><th class="text-center">Total Vendido</th></tr>';
                if (!empty($data)) {
                    foreach ($data as $p) {
                        $tbody .= "<tr>
                                 <td>{$p['nombre']}</td>
                                 <td class='text-center font-weight-bold'>{$p['total_vendido']} und.</td>
                               </tr>";
                    }
                }
                break;
        }

        // 3. CONSTRUCCIÓN DEL HTML (ESTO ES LO QUE TE FALTABA)
        if ($tbody == "") {
            $html = '
        <div class="text-center p-4">
            <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
            <p class="text-secondary">No se encontraron registros en el sistema para esta métrica.</p>
        </div>';
        } else {
            $html = '
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tblDetalleExport">
                <thead class="thead-dark">
                    ' . $thead . '
                </thead>
                <tbody>
                    ' . $tbody . '
                </tbody>
            </table>
        </div>';
        }

        echo json_encode(['status' => true, 'html' => $html], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getUpdates()
    {
        if (empty($_SESSION['login'])) {
            die();
        }

        // Llamamos a los nuevos métodos del modelo
        $pedidosNuevos = $this->model->getPedidosNuevosCount();
        $notificaciones = $this->model->getNotifSinLeerCount();

        $arrData = array(
            "status" => true,
            "pedidos_hoy" => $pedidosNuevos, // Este es el que compararemos en JS
            "notificaciones" => $notificaciones
        );

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }
}
