<?php
require_once("Libraries/Core/Mysql.php");

trait TSlider {
    private $con;

    public function getSlidersT() {
        $this->con = new Mysql();
        // Usamos 'link' porque así se llama en tu tabla de phpMyAdmin
        $sql = "SELECT idslider, nombre, descripcion, portada, link 
                FROM slider 
                WHERE status != 0 
                ORDER BY idslider DESC";
        
        $request = $this->con->select_all($sql);
        
        if(count($request) > 0){
            for ($i=0; $i < count($request); $i++) { 
                // Asegúrate que la carpeta sea 'uploads' y no falte ninguna subcarpeta
                $request[$i]['portada'] = media().'/images/uploads/'.$request[$i]['portada'];
            }
        }
        return $request;
    }
}