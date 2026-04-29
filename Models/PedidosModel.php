<?php
class PedidosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectPedidosUsuario($idpersona)
    {

        if (!is_numeric($idpersona)) {
            return array();
        }

        $sql = "SELECT idpedido, fecha, monto, status, tipopagoid, nro_guia 
            FROM pedido 
            WHERE personaid = $idpersona 
            ORDER BY idpedido DESC";

        $request = $this->select_all($sql);
        return $request;
    }

    public function getPedido(int $idpedido)
    {
        $request = array();
        // Cambiamos 'guia' por 'nro_guia' que es como está en tu phpMyAdmin
        $sql = "SELECT *, nro_guia FROM pedido WHERE idpedido = $idpedido";
        $request['pedido'] = $this->select($sql);

        if (!empty($request['pedido'])) {
            $sql_det = "SELECT * FROM detalle_pedido WHERE pedidoid = $idpedido";
            $request['detalle'] = $this->select_all($sql_det);
        }
        return $request;
    }

    public function updateStatusPedido(int $idpedido, int $status)
    {
        $query = "UPDATE pedido SET status = ? WHERE idpedido = $idpedido";
        $arrData = array($status);
        $request = $this->update($query, $arrData);
        return $request;
    }

    public function insertNotificacion(int $personaid, string $titulo, string $descripcion)
    {
        $query = "INSERT INTO notificacion(usuarioid, titulo, mensaje, leido) VALUES(?,?,?,?)";
        $arrData = array($personaid, $titulo, $descripcion, 0);
        $this->insert($query, $arrData);
    }

    public function getPersonaIdByPedido(int $idpedido)
    {
        $sql = "SELECT personaid FROM pedido WHERE idpedido = $idpedido";
        $request = $this->select($sql);
        return $request['personaid'] ?? 0;
    }

    public function getNumeroSecuencial(int $idpersona, int $idpedido)
    {
        // Contamos todos los pedidos de este usuario que tengan un ID menor o igual al actual
        $sql = "SELECT COUNT(*) as total 
            FROM pedido 
            WHERE personaid = $idpersona 
            AND idpedido <= $idpedido";
        $request = $this->select($sql);
        return $request['total'];
    }
}
