<?php
class HomeModel extends Mysql{
    private $objCategoria;
    public function __construct() {
        parent::__construct();
      
    }
    public function getTestimonios()
{
    // Seleccionamos solo los testimonios que estén activos (status != 0)
    $sql = "SELECT * FROM testimonio WHERE status != 0";
    $request = $this->select_all($sql);
    return $request;
}
}
?>
