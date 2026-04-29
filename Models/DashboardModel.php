<?php
class DashboardModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getVentasMes()
    {
        // Suma ventas del mes actual que no estén canceladas (status != 0)
        $sql = "SELECT SUM(monto) as total FROM pedido 
            WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) 
            AND YEAR(fecha) = YEAR(CURRENT_DATE()) 
            AND status != 0";
        $request = $this->select($sql);
        return $request['total'];
    }

    public function getPedidosHoyCount()
    {
        $sql = "SELECT COUNT(*) as total FROM pedido WHERE DATE(fecha) = CURRENT_DATE()";
        $request = $this->select($sql);
        return $request['total'];
    }

    public function getClientesCount()
    {
        $sql = "SELECT COUNT(*) as total FROM persona WHERE rolid = " . RCLIENTES; // Asegura tener definida la constante RCLIENTES
        $request = $this->select($sql);
        return $request['total'];
    }

    public function getUltimosPedidos()
    {
        $sql = "SELECT p.idpedido, pr.nombre, p.monto, p.status 
                FROM pedido p 
                INNER JOIN persona pr ON p.personaid = pr.idpersona 
                ORDER BY p.idpedido DESC LIMIT 5";
        $request = $this->select_all($sql);
        return $request;
    }

    public function getProductosBajoStock()
    {
        $sql = "SELECT COUNT(*) as total FROM producto WHERE stock <= 10 AND status != 0";
        $request = $this->select($sql);
        return $request['total'];
    }
    public function getProductosCount()
    {
        $sql = "SELECT COUNT(*) as total FROM producto WHERE status != 0";
        $request = $this->select($sql);
        return $request['total'];
    }
    public function getPagosDia(string $fecha)
    {
        // 1. Métodos de Pago (Gráfico de Rosca)
        $sqlWompi = "SELECT SUM(monto) as total FROM pedido WHERE DATE(fecha) = '$fecha' AND tipopagoid = 1 AND status != 0";
        $sqlTrans = "SELECT SUM(monto) as total FROM pedido WHERE DATE(fecha) = '$fecha' AND tipopagoid = 2 AND status != 0";
        $sqlEfec  = "SELECT SUM(monto) as total FROM pedido WHERE DATE(fecha) = '$fecha' AND tipopagoid = 3 AND status != 0";

        $wompi = $this->select($sqlWompi);
        $trans = $this->select($sqlTrans);
        $efec  = $this->select($sqlEfec);

        // 2. Consolidado Panel Superior (Ventas, Pedidos y Ticket)
        $sqlConsolidado = "SELECT SUM(monto) as ventas, COUNT(idpedido) as pedidos 
                       FROM pedido 
                       WHERE DATE(fecha) = '$fecha' AND status != 0";
        $resConsolidado = $this->select($sqlConsolidado);

        $ventasTotales  = $resConsolidado['ventas'] ? (float)$resConsolidado['ventas'] : 0;
        $pedidosTotales = $resConsolidado['pedidos'] ? (int)$resConsolidado['pedidos'] : 0;
        $ticketPromedio = ($pedidosTotales > 0) ? ($ventasTotales / $pedidosTotales) : 0;

        // 3. Lógica para las Gráficas de Categorías y Productos (Lo que te salía en la captura)
        // Aquí usamos las consultas que ya tienes para Categorías y Top Productos filtradas por fecha
        $sqlCat = "SELECT c.nombre, SUM(d.precio * d.cantidad) as total 
               FROM detalle_pedido d 
               INNER JOIN producto p ON d.productoid = p.idproducto 
               INNER JOIN categoria c ON p.categoriaid = idcategoria 
               INNER JOIN pedido pe ON d.pedidoid = pe.idpedido 
               WHERE DATE(pe.fecha) = '$fecha' AND pe.status != 0 
               GROUP BY c.idcategoria";
        $resCat = $this->select_all($sqlCat);

        $cat_labels = [];
        $cat_values = [];
        foreach ($resCat as $cat) {
            $cat_labels[] = $cat['nombre'];
            $cat_values[] = $cat['total'];
        }

        $data = [
            'status' => true,
            'wompi'  => $wompi['total'] ? (float)$wompi['total'] : 0,
            'trans'  => $trans['total'] ? (float)$trans['total'] : 0,
            'efec'   => $efec['total']  ? (float)$efec['total']  : 0,
            'consolidado' => [
                'ventas_totales'   => formatMoneda($ventasTotales),
                'pedidos_totales'  => $pedidosTotales,
                'ticket_historico' => formatMoneda($ticketPromedio)
            ],
            'adicionales' => [
                'cat_labels' => $cat_labels,
                'cat_values' => $cat_values,
                'ticket_promedio' => formatMoneda($ticketPromedio)
            ]
        ];

        return $data;
    }
    // Obtener ventas por categoría para una fecha específica
    public function getVentasCategorias(string $fecha)
    {
        $sql = "SELECT c.nombre as categoria, SUM(d.cantidad) as cantidad
            FROM detalle_pedido d
            INNER JOIN pedido p ON d.pedidoid = p.idpedido
            INNER JOIN producto pr ON d.productoid = pr.idproducto
            INNER JOIN categoria c ON pr.categoriaid = c.idcategoria
            WHERE DATE(p.fecha) = '$fecha' AND p.status != 0
            GROUP BY c.idcategoria
            LIMIT 5";
        return $this->select_all($sql);
    }

    // Obtener los 5 productos más vendidos en una fecha específica
    public function getTopProductos(string $fecha)
    {
        $sql = "SELECT p.nombre as producto, SUM(d.cantidad) as total
            FROM detalle_pedido d
            INNER JOIN pedido pe ON d.pedidoid = pe.idpedido
            INNER JOIN producto p ON d.productoid = p.idproducto
            WHERE DATE(pe.fecha) = '$fecha' AND pe.status != 0
            GROUP BY d.productoid
            ORDER BY total DESC
            LIMIT 5";
        return $this->select_all($sql);
    }

    // Obtener el monto promedio de los pedidos de una fecha
    public function getTicketPromedio(string $fecha)
    {
        $sql = "SELECT AVG(monto) as promedio 
            FROM pedido 
            WHERE DATE(fecha) = '$fecha' AND status != 0";
        $request = $this->select($sql);
        return ($request['promedio'] > 0) ? $request['promedio'] : 0;
    }
    public function getConsolidadoTotal()
    {
        // 1. Suma total de todas las ventas (que no estén canceladas status=0)
        $sqlVentas = "SELECT SUM(monto) as total FROM pedido WHERE status != 0";
        $requestVentas = $this->select($sqlVentas);
        $totalVentas = $requestVentas['total'] ?? 0;

        // 2. Conteo total de todos los pedidos
        $sqlPedidos = "SELECT COUNT(idpedido) as conteo FROM pedido WHERE status != 0";
        $requestPedidos = $this->select($sqlPedidos);
        $totalPedidos = $requestPedidos['conteo'] ?? 0;

        // 3. Cálculo del ticket promedio histórico
        $ticketPromedio = ($totalPedidos > 0) ? ($totalVentas / $totalPedidos) : 0;

        return [
            "ventas_totales"  => $totalVentas,
            "pedidos_totales" => $totalPedidos,
            "ticket_historico" => $ticketPromedio
        ];
    }

    // --- MÉTODOS PARA DETALLES DE WIDGETS ---

    // 1. Obtener lista de productos con stock bajo (Riesgo Stock)
    public function getDetalleStockBajo()
    {
        $sql = "SELECT nombre, stock 
                FROM producto 
                WHERE stock <= 10 AND status != 0 
                ORDER BY stock ASC";
        return $this->select_all($sql);
    }

    // 2. Obtener pedidos realizados el día de hoy (Pedidos Hoy)
    public function getDetallePedidosHoy()
    {
        $sql = "SELECT p.idpedido, pr.nombre, p.monto 
                FROM pedido p
                INNER JOIN persona pr ON p.personaid = pr.idpersona
                WHERE DATE(p.fecha) = CURRENT_DATE() AND p.status != 0
                ORDER BY p.idpedido DESC";
        return $this->select_all($sql);
    }

    // 3. Obtener resumen de ventas por día del mes actual (Ventas Mes)
    public function getDetalleVentasMes()
    {
        $sql = "SELECT DATE(fecha) as fecha, SUM(monto) as total 
                FROM pedido 
                WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) 
                AND YEAR(fecha) = YEAR(CURRENT_DATE()) 
                AND status != 0
                GROUP BY DATE(fecha)
                ORDER BY fecha DESC";
        return $this->select_all($sql);
    }

    // 4. Obtener cantidad de productos por categoría (Productos Count)
    public function getConteoPorCategorias()
    {
        $sql = "SELECT c.nombre, COUNT(p.idproducto) as cantidad 
                FROM producto p 
                INNER JOIN categoria c ON p.categoriaid = c.idcategoria 
                WHERE p.status != 0
                GROUP BY c.idcategoria
                ORDER BY cantidad DESC";
        return $this->select_all($sql);
    }
    public function getTopClientes()
    {
        // Cambiamos 'Completo' por una validación de status numérico o por exclusión
        // Asumiendo que status != 0 significa que el pedido es válido
        $sql = "SELECT p.idpersona, p.nombre, p.apellido, 
                   SUM(o.monto) as total_gastado, 
                   COUNT(o.idpedido) as cantidad_pedidos
            FROM persona p
            INNER JOIN pedido o ON p.idpersona = o.personaid
            WHERE o.status != 0 
            GROUP BY p.idpersona
            ORDER BY total_gastado DESC
            LIMIT 10";

        $request = $this->select_all($sql);
        return $request;
    }
    public function getProductosEstrella()
    {
        $sql = "SELECT p.nombre, SUM(d.cantidad) as total_vendido
            FROM producto p
            INNER JOIN detalle_pedido d ON p.idproducto = d.productoid
            INNER JOIN pedido o ON d.pedidoid = o.idpedido
            WHERE o.status != 0
            GROUP BY p.idproducto
            ORDER BY total_vendido DESC
            LIMIT 10";
        return $this->select_all($sql);
    }

    public function getVentasMesGrafica(int $anio)
    {
        $sql = "SELECT MONTH(fecha) as mes, SUM(monto) as total 
                FROM pedido 
                WHERE YEAR(fecha) = $anio AND status != 0 
                GROUP BY MONTH(fecha)";
        $request = $this->select_all($sql);
        return $request;
    }

    // Obtener el total de ventas agrupado por año (últimos 5 años)
    public function getVentasAnioGrafica()
    {
        $sql = "SELECT YEAR(fecha) as anio, SUM(monto) as total 
                FROM pedido 
                WHERE status != 0 
                GROUP BY YEAR(fecha) 
                ORDER BY YEAR(fecha) DESC 
                LIMIT 5";
        $request = $this->select_all($sql);
        return $request;
    }

    public function getPedidosNuevosCount()
    {
        // Asumiendo que status = 1 es 'Pendiente' o 'Nuevo'
        $sql = "SELECT COUNT(*) as total FROM pedido WHERE DATE(fecha) = CURRENT_DATE() AND status = 1";
        $request = $this->select($sql);
        return $request['total'] ?? 0;
    }
    public function getNotifSinLeerCount()
    {
        // Por ahora retornamos 0 para que no rompa el sistema
        // Más adelante puedes hacer un SELECT COUNT a una tabla de mensajes si la tienes
        return 0;
    }
}
