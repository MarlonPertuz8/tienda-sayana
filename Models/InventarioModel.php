<?php
class InventarioModel extends Mysql {
    
    public function __construct() {
        parent::__construct();
    }

    // 1. Insertar entrada y actualizar stock (El que ya tienes)
public function insertarEntrada(int $idProducto, string $color, int $cantidad, float $costo, int $idProveedor) {
    // Cambiamos 'proveedor' por 'proveedor_id'
    $query_insert = "INSERT INTO inventario_entrada(producto_id, color, cantidad, precio_costo, proveedor_id) 
                     VALUES(?,?,?,?,?)";
    $arrData = array($idProducto, $color, $cantidad, $costo, $idProveedor);
    $request_insert = $this->insert($query_insert, $arrData);

    if($request_insert > 0) {
        $sql_update = "UPDATE producto SET stock = stock + ? WHERE idproducto = ?";
        $this->update($sql_update, array($cantidad, $idProducto));
    }
    return $request_insert;
}

    // 2. Obtener todos los productos para el Select del formulario
    public function selectProductosInventario() {
    // Agregamos 'colores' a la consulta SQL
    $sql = "SELECT idproducto, nombre, codigo, colores FROM producto WHERE status != 0";
    return $this->select_all($sql);
}

    // 3. Obtener el historial de entradas (Kardex de entradas)
  public function selectEntradas() {
    $sql = "SELECT e.identrada, 
                   p.nombre as producto, 
                   e.color, 
                   e.cantidad, 
                   e.precio_costo, 
                   pr.nombre as proveedor, 
                   DATE_FORMAT(e.fecha_entrada, '%d-%m-%Y %H:%i') as fecha 
            FROM inventario_entrada e
            INNER JOIN producto p ON e.producto_id = p.idproducto
            INNER JOIN proveedor pr ON e.proveedor_id = pr.idproveedor
            ORDER BY e.identrada DESC";
    return $this->select_all($sql);
}

public function selectResumenTotales() {
    $sqlInversion = "SELECT SUM(cantidad * precio_costo) as total FROM inventario_entrada";
    $resInversion = $this->select($sqlInversion);

    $sqlStock = "SELECT SUM(stock) as total FROM producto WHERE status != 0";
    $resStock = $this->select($sqlStock);

    $sqlAlerta = "SELECT COUNT(*) as total FROM producto WHERE stock <= 5 AND status != 0";
    $resAlerta = $this->select($sqlAlerta);

    // Corregido: Ahora cuenta directamente de la tabla proveedor
    $sqlProv = "SELECT COUNT(*) as total FROM proveedor WHERE status != 0";
    $resProv = $this->select($sqlProv);

    return array(
        "total_inversion" => $resInversion['total'] ?? 0,
        "total_stock" => $resStock['total'] ?? 0,
        "total_alerta" => $resAlerta['total'] ?? 0,
        "total_proveedores" => $resProv['total'] ?? 0
    );
}

public function deleteEntrada(int $idEntrada) {
    $sql = "SELECT producto_id, cantidad FROM inventario_entrada WHERE identrada = $idEntrada";
    $request = $this->select($sql);

    if(!empty($request)){
        $idProducto = $request['producto_id'];
        $cantidad = $request['cantidad'];
        $sql_update = "UPDATE producto SET stock = stock - ? WHERE idproducto = ?";
        $this->update($sql_update, array($cantidad, $idProducto));
        $sql_delete = "DELETE FROM inventario_entrada WHERE identrada = ?";
        $request_delete = $this->delete($sql_delete, array($idEntrada));
        return $request_delete; 
    }
    return 0;
}

public function selectEntrada(int $idEntrada) {
    $sql = "SELECT identrada, producto_id, color, cantidad, precio_costo, proveedor 
            FROM inventario_entrada 
            WHERE identrada = $idEntrada";
    $request = $this->select($sql);
    return $request;
}

public function updateEntrada(int $idEntrada, int $idProducto, string $color, int $cantidad, float $costo, int $idProveedor) {
    $sql = "UPDATE inventario_entrada 
            SET producto_id = ?, color = ?, cantidad = ?, precio_costo = ?, proveedor_id = ? 
            WHERE identrada = ?";
    $arrData = array($idProducto, $color, $cantidad, $costo, $idProveedor, $idEntrada);
    $request = $this->update($sql, $arrData);
    return $request;
}
public function selectProveedores() {
    $sql = "SELECT idproveedor, nombre FROM proveedor WHERE status != 0 ORDER BY nombre ASC";
    return $this->select_all($sql);
}
    
}