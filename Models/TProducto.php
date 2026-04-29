<?php
require_once("Libraries/Core/Mysql.php");

trait TProducto
{
    private $con;
    public $intIdProducto;
    public $strRuta;

    public function getProductosT()
    {
        $this->con = new Mysql();
        $idUser = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : 0; // Capturamos el usuario actual

        $sql = "SELECT p.idproducto, p.nombre, p.precio, p.precio_oferta, p.stock, p.ruta,
               p.categoriaid AS idcategoria, p.materialid, -- Usamos AS para estandarizar
               (SELECT i.img FROM imagen i WHERE i.productoid = p.idproducto LIMIT 1) as imagen,
               (SELECT COUNT(*) FROM wishlist WHERE productoid = p.idproducto AND personaid = $idUser) as is_fav
        FROM producto p 
        WHERE p.status != 0 
        ORDER BY p.idproducto DESC LIMIT 8";
        $request = $this->con->select_all($sql);
        if (count($request) > 0) {
            for ($c = 0; $c < count($request); $c++) {
                $request[$c]['portada'] = !empty($request[$c]['imagen']) ? media() . '/images/uploads/' . $request[$c]['imagen'] : media() . '/images/uploads/default.png';
            }
        }
        return $request;
    }

    public function getProductosCategoriaT(int $idcategoria)
    {
        $this->con = new Mysql();
        $intIdCategoria = $idcategoria;

        $sql_cat = "SELECT nombre FROM categoria WHERE idcategoria = $intIdCategoria";
        $res_cat = $this->con->select($sql_cat);
        $nombreCategoria = $res_cat['nombre'];

        // AGREGADO: p.stock
        $sql = "SELECT p.idproducto, 
                       p.categoriaid,
                       p.materialid,
                       p.nombre, 
                       p.descripcion, 
                       p.precio, 
                       p.precio_oferta,
                       p.stock,
                       p.colores,
                       (SELECT i.img FROM imagen i WHERE i.productoid = p.idproducto LIMIT 1) as imagen,
                       c.nombre as categoria
                FROM producto p 
                INNER JOIN categoria c ON p.categoriaid = c.idcategoria 
                WHERE p.status != 0 AND p.categoriaid = $intIdCategoria
                ORDER BY p.idproducto DESC";

        $request = $this->con->select_all($sql);

        if (count($request) > 0) {
            for ($c = 0; $c < count($request); $c++) {
                $request[$c]['portada'] = !empty($request[$c]['imagen'])
                    ? media() . '/images/uploads/' . $request[$c]['imagen']
                    : media() . '/images/uploads/product.png';
            }
        }

        return array(
            'categoria' => $nombreCategoria,
            'productos' => $request
        );
    }

    public function getProductoT(string $ruta)
    {
        $this->con = new Mysql();
        $this->strRuta = $ruta;

        // AGREGADO: p.stock
        $sql = "SELECT p.idproducto, 
                       p.nombre, 
                       p.descripcion, 
                       p.precio, 
                       p.precio_oferta,
                       p.stock,
                       p.ruta,
                       p.colores,
                       p.categoriaid,
                       c.nombre as categoria
                FROM producto p 
                INNER JOIN categoria c ON p.categoriaid = c.idcategoria 
                WHERE p.status != 0 AND p.ruta = '{$this->strRuta}'";

        $request = $this->con->select($sql);

        if (!empty($request)) {
            $intIdProducto = $request['idproducto'];
            $sqlImg = "SELECT img FROM imagen WHERE productoid = $intIdProducto";
            $requestImg = $this->con->select_all($sqlImg);

            if (count($requestImg) > 0) {
                for ($i = 0; $i < count($requestImg); $i++) {
                    $request['images'][$i]['url_image'] = media() . '/images/uploads/' . $requestImg[$i]['img'];
                }
            } else {
                $request['images'][0]['url_image'] = media() . '/images/uploads/product.png';
            }
        }
        return $request;
    }

    public function getProductosRelacionadosT(int $idcategoria, int $idproducto)
    {
        $this->con = new Mysql();
        // AGREGADO: p.stock
        $sql = "SELECT p.idproducto, p.nombre, p.ruta, p.precio, p.precio_oferta, p.stock, p.colores, i.img
                FROM producto p
                LEFT JOIN imagen i ON p.idproducto = i.productoid
                WHERE p.categoriaid = $idcategoria 
                AND p.idproducto != $idproducto 
                AND p.status != 0 
                GROUP BY p.idproducto
                ORDER BY RAND() LIMIT 8";

        $request = $this->con->select_all($sql);

        if (count($request) > 0) {
            for ($c = 0; $c < count($request); $c++) {
                if (!empty($request[$c]['img'])) {
                    $request[$c]['images'][0]['url_image'] = media() . '/images/uploads/' . $request[$c]['img'];
                } else {
                    $request[$c]['images'][0]['url_image'] = media() . '/images/uploads/product.png';
                }
            }
        }
        return $request;
    }

    public function getProductoIDT(int $idproducto)
    {
        $this->con = new Mysql();
        $this->intIdProducto = $idproducto;

        // AGREGADO: p.stock
        $sql = "SELECT p.idproducto, 
                       p.nombre, 
                       p.descripcion, 
                       p.precio, 
                       p.precio_oferta,
                       p.stock,
                       p.ruta,
                       p.colores,
                       p.categoriaid,
                       c.nombre as categoria
                FROM producto p 
                INNER JOIN categoria c ON p.categoriaid = c.idcategoria 
                WHERE p.status != 0 AND p.idproducto = {$this->intIdProducto}";

        $request = $this->con->select($sql);

        if (!empty($request)) {
            $sqlImg = "SELECT img FROM imagen WHERE productoid = {$this->intIdProducto}";
            $requestImg = $this->con->select_all($sqlImg);

            if (count($requestImg) > 0) {
                for ($i = 0; $i < count($requestImg); $i++) {
                    $request['images'][$i]['url_image'] = media() . '/images/uploads/' . $requestImg[$i]['img'];
                }
            } else {
                $request['images'][0]['url_image'] = media() . '/images/uploads/product.png';
            }
        }
        return $request;
    }

    public function updateStockProducto(int $idproducto, int $cantidad)
    {
        $this->con = new Mysql();
        $sql = "UPDATE producto SET stock = stock - ? WHERE idproducto = ?";
        $arrData = array($cantidad, $idproducto);
        $request = $this->con->update($sql, $arrData);
        return $request;
    }

    public function getBusquedaT(string $busqueda)
    {
        $this->con = new Mysql();
        // AGREGADO: p.stock
        $sql = "SELECT p.idproducto,
                       p.codigo,
                       p.nombre,
                       p.descripcion,
                       p.categoriaid,
                       p.precio,
                       p.precio_oferta,
                       p.stock,
                       p.ruta,
                       c.nombre as categoria
                FROM producto p 
                INNER JOIN categoria c ON p.categoriaid = c.idcategoria
                WHERE p.status != 0 AND (p.nombre LIKE '%$busqueda%' OR p.descripcion LIKE '%$busqueda%')";
        $request = $this->con->select_all($sql);

        if (count($request) > 0) {
            for ($c = 0; $c < count($request); $c++) {
                $intIdProducto = $request[$c]['idproducto'];
                $sqlImg = "SELECT img FROM imagen WHERE productoid = $intIdProducto";
                $arrImg = $this->con->select_all($sqlImg);

                if (count($arrImg) > 0) {
                    for ($i = 0; $i < count($arrImg); $i++) {
                        $arrImg[$i]['url_image'] = media() . '/images/uploads/' . $arrImg[$i]['img'];
                    }
                    $request[$c]['portada'] = $arrImg[0]['url_image'];
                } else {
                    $request[$c]['portada'] = media() . '/images/uploads/product.png';
                }
                $request[$c]['images'] = $arrImg;
            }
        }
        return $request;
    }

    public function getMaterialesT()
    {
        $this->con = new Mysql();
        $sql = "SELECT idmaterial, nombre FROM material WHERE status != 0";
        $request = $this->con->select_all($sql);
        return $request;
    }

    public function setCalificacionT(int $idproducto, int $idpersona, int $puntuacion)
    {
        $this->con = new Mysql();
        // Usamos una consulta que ignore si ya existe o puedes validarlo antes
        $query_insert = "INSERT INTO calificaciones(productoid, personaid, puntuacion) VALUES(?,?,?)";
        $arrData = array($idproducto, $idpersona, $puntuacion);
        $request_insert = $this->con->insert($query_insert, $arrData);
        return $request_insert;
    }
    public function setWishlistT(int $idproducto, int $idpersona)
    {
        $this->con = new Mysql();
        $sql = "SELECT * FROM wishlist WHERE personaid = $idpersona AND productoid = $idproducto";
        $request = $this->con->select($sql);

        if (empty($request)) {
            // No existe: Lo agregamos
            $query_insert = "INSERT INTO wishlist(personaid, productoid) VALUES(?,?)";
            $arrData = array($idpersona, $idproducto);
            $request_insert = $this->con->insert($query_insert, $arrData);
            return "add";
        } else {
            // Ya existe: Lo quitamos
            $query_del = "DELETE FROM wishlist WHERE personaid = $idpersona AND productoid = $idproducto";
            $this->con->delete($query_del);
            return "del";
        }
    }

    public function getWishlistT(int $idpersona)
    {
        $this->con = new Mysql();
        // Quitamos p.portada de la consulta para evitar el error
        $sql = "SELECT p.idproducto, p.nombre, p.precio, p.precio_oferta, p.ruta
            FROM wishlist w
            INNER JOIN producto p ON w.productoid = p.idproducto
            WHERE w.personaid = $idpersona";
        $request = $this->con->select_all($sql);

        // Si hay productos, les buscamos su imagen de portada real
        if (count($request) > 0) {
            for ($i = 0; $i < count($request); $i++) {
                $idProducto = $request[$i]['idproducto'];
                // Buscamos la imagen en la tabla 'imagen' (ajusta el nombre si es distinto)
                $sqlImg = "SELECT img FROM imagen WHERE productoid = $idProducto LIMIT 1";
                $arrImg = $this->con->select($sqlImg);

                if (!empty($arrImg)) {
                    $request[$i]['portada'] = media() . '/images/uploads/' . $arrImg['img'];
                } else {
                    $request[$i]['portada'] = media() . '/images/uploads/default.png';
                }
            }
        }
        return $request;
    }

    public function cantProductosT()
{
    $this->con = new Mysql();
    $sql = "SELECT COUNT(*) as total FROM producto WHERE status != 0";
    $request = $this->con->select($sql);
    return $request['total'];
}

public function getProductosPageT(int $desde, int $porPagina)
{
    $this->con = new Mysql();
    $idUser = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : 0;

    // Usamos EXACTAMENTE tu misma consulta de getProductosT pero con LIMIT
    $sql = "SELECT p.idproducto, p.nombre, p.precio, p.precio_oferta, p.stock, p.ruta,
                   p.categoriaid AS idcategoria, p.materialid,
                   (SELECT i.img FROM imagen i WHERE i.productoid = p.idproducto LIMIT 1) as imagen,
                   (SELECT COUNT(*) FROM wishlist WHERE productoid = p.idproducto AND personaid = $idUser) as is_fav
            FROM producto p 
            WHERE p.status != 0 
            ORDER BY p.idproducto DESC LIMIT $desde, $porPagina";
            
    $request = $this->con->select_all($sql);

    // Esta es la parte que faltaba para que se vean las imágenes:
    if (count($request) > 0) {
        for ($c = 0; $c < count($request); $c++) {
            $request[$c]['portada'] = !empty($request[$c]['imagen']) 
                ? media() . '/images/uploads/' . $request[$c]['imagen'] 
                : media() . '/images/uploads/default.png';
        }
    }
    return $request;
}
    
}
