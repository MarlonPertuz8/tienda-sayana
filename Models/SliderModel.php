<?php 

class SliderModel extends Mysql
{
    private $intIdSlider;
    private $strNombre;
    private $strDescripcion;
    private $strPortada;
    private $strLink;
    private $intStatus;

    public function __construct()
    {
        parent::__construct();
    }

    public function insertSlider(string $nombre, string $descripcion, string $portada, string $link, int $status){

        $this->strNombre = $nombre;
        $this->strDescripcion = $descripcion;
        $this->strPortada = $portada;
        $this->strLink = $link;
        $this->intStatus = $status;
        $return = 0;

        // Validamos si ya existe un slider con el mismo nombre (opcional, igual que en categorías)
        $sql = "SELECT * FROM slider WHERE nombre = ?";
        $request = $this->select_all($sql, array($this->strNombre));

        if(empty($request))
        {
            $query_insert  = "INSERT INTO slider(nombre, descripcion, portada, link, status) VALUES(?,?,?,?,?)";
            $arrData = array($this->strNombre, 
                             $this->strDescripcion, 
                             $this->strPortada, 
                             $this->strLink,
                             $this->intStatus);
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        }else{
            $return = "exist";
        }
        return $return;
    }

    public function selectSliders()
    {
        // Traemos todos los sliders que no estén eliminados (status 0)
        $sql = "SELECT * FROM slider WHERE status != 0";
        return $this->select_all($sql);
    }

    public function selectSlider(int $idslider){
        $this->intIdSlider = $idslider;
        $sql = "SELECT * FROM slider WHERE idslider = $this->intIdSlider";
        return $this->select($sql);
    }

    public function updateSlider(int $idslider, string $nombre, string $descripcion, string $portada, string $link, int $status){
        
        $this->intIdSlider = $idslider;
        $this->strNombre = $nombre;
        $this->strDescripcion = $descripcion;
        $this->strPortada = $portada;
        $this->strLink = $link;
        $this->intStatus = $status;

        $sql = "SELECT * FROM slider WHERE nombre = ? AND idslider != ?";
        $arrParams = array($this->strNombre, $this->intIdSlider);
        $request = $this->select_all($sql, $arrParams);

        if(empty($request))
        {
            $sql = "UPDATE slider SET nombre = ?, descripcion = ?, portada = ?, link = ?, status = ? WHERE idslider = ?";
            $arrData = array($this->strNombre, 
                             $this->strDescripcion, 
                             $this->strPortada, 
                             $this->strLink,
                             $this->intStatus, 
                             $this->intIdSlider);
            $request = $this->update($sql, $arrData);
            
            return ($request || $request >= 0) ? 1 : 0;
        }else{
            return "exist";
        }
    }

    public function deleteSlider(int $idslider)
    {
        $this->intIdSlider = $idslider;
        // En slider no solemos tener dependencias como productos, así que borramos de forma lógica (status 0)
        $sql = "UPDATE slider SET status = ? WHERE idslider = ?";
        $arrData = array(0, $this->intIdSlider);
        $request = $this->update($sql, $arrData);
        return ($request) ? 'ok' : 'error';
    }

    // MÉTODO PARA ACTUALIZAR ÚNICAMENTE LA IMAGEN (siguiendo tu lógica de categorías)
    public function updateImageSlider(int $idslider, string $portada)
    {
        $this->intIdSlider = $idslider;
        $this->strPortada = $portada;
        $sql = "UPDATE slider SET portada = ? WHERE idslider = ?";
        $arrData = array($this->strPortada, $this->intIdSlider);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
?>