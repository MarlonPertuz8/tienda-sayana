<?php 
require_once("Libraries/Core/Mysql.php");

trait TCategoria {
    private $con;

    // Cambiamos a ?string para que acepte NULL y le damos un valor por defecto
    public function getCategoriasT(string $categorias = null) {
        $this->con = new Mysql();
        
        $where = "";
        // Si se envían IDs (ej: "1,2,3"), aplicamos el filtro IN
        if($categorias != null){
            $where = " AND idcategoria IN ($categorias)";
        }

        $sql = "SELECT idcategoria, nombre, descripcion, portada,ruta
                FROM categoria 
                WHERE status != 0" . $where;
        
        $request = $this->con->select_all($sql);

        if (count($request) > 0) {
            for ($c = 0; $c < count($request); $c++) {
                if (!empty($request[$c]['portada'])) {
                    $request[$c]['portada'] = BASE_URL . "/Assets/images/uploads/" . $request[$c]['portada'];
                } else {
                    $request[$c]['portada'] = BASE_URL . "/Assets/images/uploads/default.jpg";
                }
            }
        }
        return $request;
    }
}