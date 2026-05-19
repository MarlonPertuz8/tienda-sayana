<?php
class CampanasModel extends Mysql {
    public function __construct() {
        parent::__construct();
    }

    public function selectCampanas() {
        $sql = "SELECT id_campana, nombre, banner_landing, fecha_inicio, fecha_fin, estado 
                FROM campanas 
                WHERE estado != 0";
        return $this->select_all($sql);
    }

public function insertCampana(string $nombre, string $slug, string $banner, string $desc, string $html, string $json, string $f_inicio, string $f_fin, string $enlace, int $status) {
    // Agregamos json_contenido a la consulta
    $query_insert = "INSERT INTO campanas(nombre, slug, banner_landing, descripcion_corta, html_contenido, json_contenido, fecha_inicio, fecha_fin, enlace_boton, estado) VALUES(?,?,?,?,?,?,?,?,?,?)";
    $arrData = array($nombre, $slug, $banner, $desc, $html, $json, $f_inicio, $f_fin, $enlace, $status);
    return $this->insert($query_insert, $arrData);
}

public function updateCampana(int $id, string $nombre, string $slug, string $banner, string $desc, string $html, string $json, string $f_inicio, string $f_fin, string $enlace, int $status) {
    // Agregamos json_contenido al UPDATE
    $sql = "UPDATE campanas SET nombre=?, slug=?, banner_landing=?, descripcion_corta=?, html_contenido=?, json_contenido=?, fecha_inicio=?, fecha_fin=?, enlace_boton=?, estado=? WHERE id_campana = $id";
    $arrData = array($nombre, $slug, $banner, $desc, $html, $json, $f_inicio, $f_fin, $enlace, $status);
    return $this->update($sql, $arrData);
}

    public function selectCampana(int $id) {
        $sql = "SELECT * FROM campanas WHERE id_campana = $id";
        return $this->select($sql);
    }

    public function deleteCampana(int $id) {
        $sql = "UPDATE campanas SET estado = ? WHERE id_campana = $id";
        $arrData = array(0);
        return $this->update($sql, $arrData);
    }
}