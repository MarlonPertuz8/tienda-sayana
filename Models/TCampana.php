<?php
require_once("Libraries/Core/Mysql.php");

trait TCampana {
    private $con;

    // Obtener una campaña específica por su SLUG
    public function getCampanaT(string $slug) {
        $this->con = new Mysql();
        // Solo traemos la campaña si está activa y en fecha vigente
        $sql = "SELECT id_campana, 
                       nombre, 
                       slug, 
                       banner_landing, 
                       descripcion_corta, 
                       html_contenido, 
                       fecha_inicio, 
                       fecha_fin, 
                       enlace_boton, 
                       estado 
                FROM campanas 
                WHERE slug = '{$slug}' 
                AND estado = 1 
                AND NOW() BETWEEN fecha_inicio AND fecha_fin";
        
        $request = $this->con->select($sql);
        return $request;
    }

    // Obtener todas las campañas activas (por si quieres un listado o carrusel)
    public function getCampanasT() {
        $this->con = new Mysql();
        $sql = "SELECT * FROM campanas 
                WHERE estado = 1 
                AND NOW() BETWEEN fecha_inicio AND fecha_fin 
                ORDER BY id_campana DESC";
        
        $request = $this->con->select_all($sql);
        return $request;
    }

    // Obtener la campaña más reciente (Útil para un banner automático en el Home)
    public function getCampanaRecienteT() {
        $this->con = new Mysql();
        $sql = "SELECT * FROM campanas 
                WHERE estado = 1 
                AND NOW() BETWEEN fecha_inicio AND fecha_fin 
                ORDER BY id_campana DESC LIMIT 1";
        
        $request = $this->con->select($sql);
        return $request;
    }
    public function getPopupActiveT() {
    // Buscamos una campaña vigente y activa
    $sql = "SELECT * FROM campanas 
            WHERE estado = 1 
            AND fecha_inicio <= CURDATE() 
            AND fecha_fin >= CURDATE() 
            ORDER BY id_campana DESC LIMIT 1";
    $request = $this->con->select($sql);

    if(!empty($request)){
        // Aquí suponemos que los bloques están en un campo llamado 'bloques' (JSON)
        // O si tienes una tabla de bloques, tendrías que buscar el tipo 'popup'
        return $request; 
    }
    return false;
}
}