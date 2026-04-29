<?php
trait TCarrito {
    private $con;

public function insertPedido(int $idusuario, string $direccion, string $ciudad, ?int $idbarrio, float $costo_envio, float $total, int $tipopago, int $status) {
    $this->con = new Mysql();
    
    // El orden de las columnas según tu imagen de phpMyAdmin
    $query_insert = "INSERT INTO pedido(personaid, direccion_envio, ciudad_envio, barrioid, costo_envio, monto, tipopagoid, status) 
                     VALUES(?,?,?,?,?,?,?,?)";
    
    // El array DEBE seguir el orden de los signos de interrogación arriba
    $arrData = array($idusuario, 
                     $direccion, 
                     $ciudad, 
                     $idbarrio, // Aquí viajará el NULL sin problemas
                     $costo_envio, 
                     $total, 
                     $tipopago, 
                     $status);
    
    $request_insert = $this->con->insert($query_insert, $arrData);
    return $request_insert;
}

    public function insertDetallePedido(int $idpedido, int $idproducto, float $precio, int $cantidad, string $color) {
        $this->con = new Mysql();
        $query_insert = "INSERT INTO detalle_pedido(pedidoid, productoid, precio, cantidad, color) 
                         VALUES(?,?,?,?,?)";
        
        $arrData = array($idpedido, $idproducto, $precio, $cantidad, $color);
        $request_insert = $this->con->insert($query_insert, $arrData);
        return $request_insert;
    }
    public function getBarrios()
{
    $this->con = new Mysql();
    // Consultamos los barrios que estén activos (status != 0)
    $sql = "SELECT idbarrio, nombre, costo FROM barrio WHERE status != 0 ORDER BY nombre ASC";
    $request = $this->con->select_all($sql);
    return $request;
}

public function registrarSalidaInventario(int $idproducto, int $idusuario, int $cantidad, string $color, string $observacion) {
    $this->con = new Mysql();
    $query_insert = "INSERT INTO inventario_salida(producto_id, usuario_id, cantidad, color, fecha_salida, observaciones) 
                     VALUES(?,?,?,?,NOW(),?)";
    $arrData = array($idproducto, $idusuario, $cantidad, $color, $observacion);
    $request_insert = $this->con->insert($query_insert, $arrData);
    return $request_insert;
}
}