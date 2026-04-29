<?php 
class BlogModel extends Mysql {
    public function selectPosts() {
        $sql = "SELECT idpost, titulo, status, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha 
                FROM post WHERE status != 0";
        return $this->select_all($sql);
    }

    // Seleccionar un solo post - CORREGIDO (Seguro contra SQL Injection)
    public function selectPost(int $idpost) {
        $sql = "SELECT idpost, titulo, contenido, portada, status, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha 
                FROM post WHERE idpost = ?";
        $arrData = array($idpost);
        return $this->select($sql, $arrData);
    }

    public function insertPost(string $titulo, string $contenido, string $portada, string $ruta, int $status) {
        $query_insert = "INSERT INTO post(titulo, contenido, portada, ruta, status) VALUES(?,?,?,?,?)";
        $arrData = array($titulo, $contenido, $portada, $ruta, $status);
        return $this->insert($query_insert, $arrData);
    }

    // Actualizar post - CORREGIDO (Seguro contra SQL Injection)
    public function updatePost(int $idpost, string $titulo, string $contenido, string $portada, string $ruta, int $status) {
        $sql = "UPDATE post SET titulo=?, contenido=?, portada=?, ruta=?, status=? WHERE idpost = ?";
        $arrData = array($titulo, $contenido, $portada, $ruta, $status, $idpost);
        return $this->update($sql, $arrData);
    }

    // Borrado lógico - CORREGIDO (Seguro contra SQL Injection)
    public function deletePost(int $idpost) {
        $sql = "UPDATE post SET status = ? WHERE idpost = ?";
        $arrData = array(0, $idpost);
        return $this->update($sql, $arrData);
    }
    
}
