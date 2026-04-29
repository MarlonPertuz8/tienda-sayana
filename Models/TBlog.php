<?php 
require_once("Libraries/Core/Mysql.php");

trait TBlog {
    private $con;

    // Añadimos el parámetro $limite con valor nulo por defecto
    public function getPostsT(int $limite = null) {
        $this->con = new Mysql();
        
        // Creamos la base de la consulta
        $sql = "SELECT idpost, titulo, contenido, portada, ruta, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha 
                FROM post WHERE status = 1 ORDER BY idpost DESC";
        
        // Si mandamos un límite desde el controlador, lo concatenamos al SQL
        if($limite != null){
            $sql .= " LIMIT $limite";
        }

        $request = $this->con->select_all($sql);
        return $request;
    }

    public function getPostT(string $ruta) {
        $this->con = new Mysql();
        $sql = "SELECT idpost, titulo, contenido, portada, ruta, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha 
                FROM post WHERE status = 1 AND ruta = '$ruta'";
        $request = $this->con->select($sql);
        return $request;
    }

    // Opcional: Función para categorías si la necesitas luego
    public function getCategoriasT(){
        $this->con = new Mysql();
        $sql = "SELECT * FROM categoria WHERE status != 0";
        $request = $this->con->select_all($sql);
        return $request;
    }
}
