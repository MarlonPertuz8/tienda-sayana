<?php
class AdminNosotrosModel extends Mysql {
    public function __construct() {
        parent::__construct();
    }

    public function selectNosotros() {
        // Como solo hay un "Nosotros", traemos el primer registro
        $sql = "SELECT * FROM nosotros LIMIT 1";
        return $this->select($sql);
    }

    public function updateNosotros(int $id, string $titulo, string $contenido, string $portada, string $imgSecundaria) {
        $query = "UPDATE nosotros SET titulo = ?, contenido = ?, portada = ?, imagen_secundaria = ? WHERE id = ?";
        $arrData = array($titulo, $contenido, $portada, $imgSecundaria, $id);
        return $this->update($query, $arrData);
    }
}   