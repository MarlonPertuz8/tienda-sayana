<?php
class PedidosAModel extends Mysql
{

    public function selectPedidosTotal()
    {
        $sql = "SELECT p.idpedido, p.fecha, p.monto, p.status, 
                       concat(per.nombre, ' ', per.apellido) as cliente 
                FROM pedido p 
                INNER JOIN persona per ON p.personaid = per.idpersona 
                ORDER BY p.idpedido DESC";
        return $this->select_all($sql);
    }

    public function selectPedido(int $idpedido)
    {
        $request = array();
        // ✅ CORREGIDO: Usando email_user y telefono según image_b7878a.jpg
        $sql = "SELECT p.idpedido, 
                       p.direccion_envio, 
                       p.ciudad_envio, 
                       p.costo_envio, 
                       DATE_FORMAT(p.fecha, '%d/%m/%Y %H:%i:%s') as fecha, 
                       p.monto, 
                       p.tipopagoid,
                       p.status,
                       per.nombre, 
                       per.apellido, 
                       per.email_user, 
                       per.telefono
                FROM pedido p
                INNER JOIN persona per ON p.personaid = per.idpersona
                WHERE p.idpedido = $idpedido";

        $requestPedido = $this->select($sql);

        if (!empty($requestPedido)) {
            // Obtenemos los productos (confirmado en image_b7836b.jpg)
            $sql_detalle = "SELECT pr.nombre as producto, d.precio, d.cantidad, d.color 
                            FROM detalle_pedido d 
                            INNER JOIN producto pr ON d.productoid = pr.idproducto 
                            WHERE d.pedidoid = $idpedido";
            $requestProductos = $this->select_all($sql_detalle);

            $request = array(
                'orden' => $requestPedido,
                'detalle' => $requestProductos
            );
        }
        return $request;
    }

    public function updateGuia(int $idpedido, string $guia)
    {
        $sql = "UPDATE pedido SET nro_guia = ?, status = ? WHERE idpedido = ?";
        // Ponemos status 3 (Enviado) automáticamente al asignar guía
        $arrData = array($guia, 3, $idpedido);
        return $this->update($sql, $arrData);
    }
}
