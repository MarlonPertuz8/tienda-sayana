<?php
require_once("Libraries/Core/Mysql.php");

trait TNosotros {
    private $con;

    public function getNosotrosT() {
        $this->con = new Mysql();
        $sql = "SELECT * FROM nosotros LIMIT 1";
        $request = $this->con->select($sql);
        return $request;
    }
}