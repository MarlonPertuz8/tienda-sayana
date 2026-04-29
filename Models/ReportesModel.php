<?php
class ReportesModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectInventarioValorizado($fechaInicio = null, $fechaFin = null)
    {
        $where = " WHERE p.status != 0 ";
        if ($fechaInicio != null && $fechaFin != null) {
            $where .= " AND p.datecreated BETWEEN '{$fechaInicio} 00:00:00' AND '{$fechaFin} 23:59:59' ";
        }

        $sql = "SELECT p.idproducto, p.nombre, p.stock, 
                       IFNULL(e.precio_costo, 0) as precio_costo, 
                       (p.stock * IFNULL(e.precio_costo, 0)) as inversion_total,
                       IFNULL(e.proveedor, 'N/A') as proveedor
                FROM producto p
                LEFT JOIN (
                    SELECT t1.producto_id, t1.precio_costo, t1.proveedor
                    FROM inventario_entrada t1
                    WHERE t1.identrada = (
                        SELECT MAX(t2.identrada) FROM inventario_entrada t2 
                        WHERE t2.producto_id = t1.producto_id
                    )
                ) e ON p.idproducto = e.producto_id 
                $where 
                ORDER BY p.nombre ASC";

        return $this->select_all($sql);
    }

    public function selectMovimientosFiltrado($fechaInicio, $fechaFin) {
        $sql = "
            SELECT * FROM (
                -- Parte 1: Entradas (Proveedores)
                SELECT e.fecha_entrada as fecha_raw, 
                       DATE_FORMAT(e.fecha_entrada, '%d/%m/%Y') as fecha,
                       p.nombre as producto, 'ENTRADA' as tipo, e.cantidad,
                       IFNULL(u.nombre, 'Sistema') as usuario, 
                       IFNULL(e.observaciones, 'Entrada de stock') as observacion
                FROM inventario_entrada e
                INNER JOIN producto p ON e.producto_id = p.idproducto
                LEFT JOIN persona u ON e.usuario_id = u.idpersona
                WHERE e.fecha_entrada BETWEEN '{$fechaInicio} 00:00:00' AND '{$fechaFin} 23:59:59'

                UNION ALL

                -- Parte 2: Salidas (Ventas Online)
                SELECT s.fecha_salida as fecha_raw, 
                       DATE_FORMAT(s.fecha_salida, '%d/%m/%Y') as fecha,
                       p.nombre as producto, 'SALIDA' as tipo, s.cantidad,
                       IFNULL(u.nombre, 'Cliente Online') as usuario, 
                       s.observaciones as observacion
                FROM inventario_salida s
                INNER JOIN producto p ON s.producto_id = p.idproducto
                LEFT JOIN persona u ON s.usuario_id = u.idpersona
                WHERE s.fecha_salida BETWEEN '{$fechaInicio} 00:00:00' AND '{$fechaFin} 23:59:59'
            ) AS movimientos
            ORDER BY fecha_raw DESC"; // Ordenamos por la fecha real para que sea exacto

        return $this->select_all($sql);
    }
}