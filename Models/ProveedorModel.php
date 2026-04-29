<?php 
class ProveedoresModel extends Mysql {
    public function __construct() {
        parent::__construct();
    }

    public function insertProveedor(string $nombre, string $ruc, string $telefono, string $direccion) {
        $query_insert = "INSERT INTO proveedor(nombre, ruc, telefono, direccion) VALUES(?,?,?,?)";
        $arrData = array($nombre, $ruc, $telefono, $direccion);
        return $this->insert($query_insert, $arrData);
    }

    public function selectProveedores() {
        $sql = "SELECT idproveedor, nombre, ruc, telefono, direccion FROM proveedor WHERE status != 0";
        return $this->select_all($sql);
    }

    public function selectProveedor(int $id) {
        $sql = "SELECT * FROM proveedor WHERE idproveedor = $id";
        return $this->select($sql);
    }

    public function updateProveedor(int $id, string $nombre, string $ruc, string $telefono, string $direccion) {
        $sql = "UPDATE proveedor SET nombre=?, ruc=?, telefono=?, direccion=? WHERE idproveedor = ?";
        $arrData = array($nombre, $ruc, $telefono, $direccion, $id);
        return $this->update($sql, $arrData);
    }

    public function deleteProveedor(int $id) {
        $sql = "UPDATE proveedor SET status = ? WHERE idproveedor = ?";
        return $this->update($sql, array(0, $id));
    }
}