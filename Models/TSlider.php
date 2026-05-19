<?php
require_once("Libraries/Core/Mysql.php");

trait TSlider {
    private $con;

    public function getSlidersT() {
        $this->con = new Mysql();
        // 1. Incluimos las nuevas columnas identificadas en la base de datos
        $sql = "SELECT idslider, nombre, descripcion, portada, link, status, tipo, video, boton_texto 
                FROM slider 
                WHERE status != 0 
                ORDER BY idslider DESC";
        
        $request = $this->con->select_all($sql);
        
        if(count($request) > 0){
            for ($i=0; $i < count($request); $i++) { 
                
                // 2. Procesar la imagen de portada
                if(!empty($request[$i]['portada'])){
                    $request[$i]['portada'] = media().'/images/uploads/'.$request[$i]['portada'];
                } else {
                    $request[$i]['portada'] = media().'/images/uploads/default.png';
                }

                // 3. Procesar el archivo de video (si existe)
                if(!empty($request[$i]['video'])){
                    $request[$i]['video'] = media().'/images/uploads/'.$request[$i]['video'];
                }

                // 4. Valores por defecto para evitar "Undefined index" en la vista
                // Si el tipo está vacío en registros viejos, asumimos 'imagen'
                $request[$i]['tipo'] = $request[$i]['tipo'] ?: 'imagen';
                
                // Si el texto del botón está vacío, ponemos uno estándar
                $request[$i]['boton_texto'] = $request[$i]['boton_texto'] ?: 'Ver más';
            }
        }
        return $request;
    }
}