<?php

class ProductosModel extends Mysql
{
    private $intIdProducto;
    private $strNombre;
    private $strRuta;
    private $strDescripcion;
    private $strCodigo;
    private $intCategoriaId;
    private $intMaterialId;
    private $floatPrecio;
    private $floatPrecioOferta; 
    private $intStock;
    private $intStatus;
    private $strImagen;
    private $strColores;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectProductos()
    {
        $sql = "SELECT p.idproducto,
                       p.codigo,
                       p.nombre,
                       p.descripcion,
                       p.categoriaid,
                       c.nombre as categoria,
                       p.materialid,
                       IFNULL(m.nombre, 'Sin material') as material, 
                       p.precio,
                       p.precio_oferta, -- <--- AGREGADO
                       p.stock,
                       p.status,
                       p.colores
                FROM producto p 
                INNER JOIN categoria c ON p.categoriaid = c.idcategoria
                LEFT JOIN material m ON p.materialid = m.idmaterial
                WHERE p.status != 0 
                ORDER BY p.idproducto DESC";
        return $this->select_all($sql);
    }

    // Se agrega el parámetro $precio_oferta
    public function insertProducto(string $nombre, string $descripcion, string $codigo, int $categoriaid, int $materialid, string $precio, string $precio_oferta, int $stock, int $status, string $ruta, string $colores)
    {

        $this->strNombre = $nombre;
        $this->strRuta = $ruta;
        $this->strDescripcion = $descripcion;
        $this->strCodigo = $codigo;
        $this->intCategoriaId = $categoriaid;
        $this->intMaterialId = $materialid;
        $this->floatPrecio = $precio;
        $this->floatPrecioOferta = $precio_oferta; // <--- ASIGNACIÓN
        $this->intStock = $stock;
        $this->intStatus = $status;
        $this->strColores = $colores;

        $sql = "SELECT idproducto FROM producto WHERE codigo = ? AND status != 0";
        $request = $this->select($sql, [$this->strCodigo]);

        if (empty($request)) {
            // Añadimos precio_oferta y un '?' extra
            $query_insert = "INSERT INTO producto(categoriaid, materialid, codigo, nombre, ruta, descripcion, precio, precio_oferta, stock, status, colores) 
                             VALUES(?,?,?,?,?,?,?,?,?,?,?)";
            $arrData = [
                $this->intCategoriaId,
                $this->intMaterialId,
                $this->strCodigo,
                $this->strNombre,
                $this->strRuta,
                $this->strDescripcion,
                $this->floatPrecio,
                $this->floatPrecioOferta, // <--- DATO PARA LA DB
                $this->intStock,
                $this->intStatus,
                $this->strColores
            ];
            $request_insert = $this->insert($query_insert, $arrData);
            return $request_insert;
        }
        return "exist";
    }

    // Se agrega el parámetro $precio_oferta
    public function updateProducto(int $idproducto, string $nombre, string $descripcion, string $codigo, int $categoriaid, int $materialid, string $precio, string $precio_oferta, int $stock, int $status, string $ruta, string $colores)
    {

        $this->intIdProducto = $idproducto;
        $this->strNombre = $nombre;
        $this->strRuta = $ruta;
        $this->strDescripcion = $descripcion;
        $this->strCodigo = $codigo;
        $this->intCategoriaId = $categoriaid;
        $this->intMaterialId = $materialid;
        $this->floatPrecio = $precio;
        $this->floatPrecioOferta = $precio_oferta; // <--- ASIGNACIÓN
        $this->intStock = $stock;
        $this->intStatus = $status;
        $this->strColores = $colores;

        $sql = "SELECT idproducto FROM producto WHERE codigo = ? AND idproducto != ? AND status != 0";
        $request = $this->select($sql, [$this->strCodigo, $this->intIdProducto]);

        if (empty($request)) {
            // Añadimos precio_oferta=? al UPDATE
            $sql = "UPDATE producto 
                    SET categoriaid=?, materialid=?, codigo=?, nombre=?, ruta=?, descripcion=?, precio=?, precio_oferta=?, stock=?, status=?, colores=? 
                    WHERE idproducto = ?";
            $arrData = [
                $this->intCategoriaId,
                $this->intMaterialId,
                $this->strCodigo,
                $this->strNombre,
                $this->strRuta,
                $this->strDescripcion,
                $this->floatPrecio,
                $this->floatPrecioOferta, // <--- ACTUALIZACIÓN EN DB
                $this->intStock,
                $this->intStatus,
                $this->strColores,
                $this->intIdProducto
            ];
            return $this->update($sql, $arrData);
        }
        return "exist";
    }

    public function selectProducto(int $idproducto)
    {
        $sql = "SELECT p.idproducto, 
                       p.codigo, 
                       p.nombre, 
                       p.descripcion, 
                       p.precio, 
                       p.precio_oferta, -- <--- AGREGADO
                       p.stock, 
                       p.categoriaid, 
                       c.nombre as categoria, 
                       p.materialid,
                       IFNULL(m.nombre, 'Sin material') as material,
                       p.status,
                       p.colores,
                       DATE_FORMAT(p.datecreated, '%d-%m-%Y') as fecha_registro
                FROM producto p
                INNER JOIN categoria c ON p.categoriaid = c.idcategoria
                LEFT JOIN material m ON p.materialid = m.idmaterial
                WHERE p.idproducto = ? AND p.status != 0";
        return $this->select($sql, [$idproducto]);
    }

    // Métodos de imágenes y delete se mantienen igual...
    public function insertImage(int $idproducto, string $imagen)
    {
        $this->intIdProducto = $idproducto;
        $this->strImagen = $imagen;
        $query_insert  = "INSERT INTO imagen(productoid,img) VALUES(?,?)";
        $arrData = array($this->intIdProducto, $this->strImagen);
        $request_insert = $this->insert($query_insert, $arrData);
        return $request_insert;
    }

    public function selectImages(int $idproducto)
    {
        $sql = "SELECT productoid, img FROM imagen WHERE productoid = ?";
        return $this->select_all($sql, [$idproducto]);
    }

    public function deleteImage(int $idproducto, string $imagen)
    {
        $sql = "DELETE FROM imagen WHERE productoid = {$idproducto} AND img = '{$imagen}'";
        return $this->delete($sql);
    }

    public function deleteProducto(int $idproducto)
    {
        $sql = "UPDATE producto SET status = ? WHERE idproducto = ?";
        return $this->update($sql, [0, $idproducto]);
    }
    public function selectProductosStockCritico()
    {
        $sql = "SELECT idproducto, nombre, stock 
            FROM producto 
            WHERE status != 0 AND stock <= 1";
        $request = $this->select_all($sql);
        return $request;
    }

    public function getIdByName(string $tabla, string $campo, string $valor)
{
    // Buscamos el ID donde el nombre coincida exactamente
    // Asumimos que el ID de la tabla se llama 'idcategoria' o 'idmaterial'
    $idName = "id".$tabla;
    $sql = "SELECT $idName FROM $tabla WHERE $campo = '$valor' AND status != 0";
    $request = $this->select($sql);
    
    if (!empty($request)) {
        return $request[$idName];
    }
    return 0;
}
}
