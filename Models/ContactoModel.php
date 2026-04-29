<?php 
class ContactoModel extends Mysql {
    public function __construct() {
        parent::__construct();
    }

    public function insertContacto(string $nombre, string $email, string $mensaje, string $useragent, string $ip) {
        $query_insert = "INSERT INTO contacto(nombre, email, mensaje, useragent, ip) VALUES(?,?,?,?,?)";
        $arrData = array($nombre, $email, $mensaje, $useragent, $ip);
        // El método insert pertenece a tu clase base Mysql
        return $this->insert($query_insert, $arrData);
    }
}