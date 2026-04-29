<?php 

class CategoriasModel extends Mysql
{
    private $intIdcategoria;
    private $strCategoria;
    private $strDescripcion;
    private $intStatus;
    private $strPortada;
    private $strRuta;

    public function __construct()
    {
        parent::__construct();
    }

    public function insertCategoria(string $nombre, string $descripcion, string $portada, string $ruta, int $status){

        $this->strCategoria = $nombre;
        $this->strDescripcion = $descripcion;
        $this->strPortada = $portada;
        $this->strRuta = $ruta;
        $this->intStatus = $status;
        $return = 0;

        $sql = "SELECT * FROM categoria WHERE nombre = ?";
        $request = $this->select_all($sql, array($this->strCategoria));

        if(empty($request))
        {
            $query_insert  = "INSERT INTO categoria(nombre, descripcion, portada, ruta, status) VALUES(?,?,?,?,?)";
            $arrData = array($this->strCategoria, 
                             $this->strDescripcion, 
                             $this->strPortada, 
                             $this->strRuta,
                             $this->intStatus);
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        }else{
            $return = "exist";
        }
        return $return;
    }

    public function selectCategorias()
    {
        $sql = "SELECT * FROM categoria WHERE status != 0";
        return $this->select_all($sql);
    }

    public function selectCategoria(int $idcategoria){
        $this->intIdcategoria = $idcategoria;
        $sql = "SELECT * FROM categoria WHERE idcategoria = $this->intIdcategoria";
        return $this->select($sql);
    }

    public function updateCategoria(int $idcategoria, string $categoria, string $descripcion, string $portada, string $ruta, int $status){
        
        $this->intIdcategoria = $idcategoria;
        $this->strCategoria = $categoria;
        $this->strDescripcion = $descripcion;
        $this->strPortada = $portada;
        $this->strRuta = $ruta;
        $this->intStatus = $status;

        $sql = "SELECT * FROM categoria WHERE nombre = ? AND idcategoria != ?";
        $arrParams = array($this->strCategoria, $this->intIdcategoria);
        $request = $this->select_all($sql, $arrParams);

        if(empty($request))
        {
            $sql = "UPDATE categoria SET nombre = ?, descripcion = ?, portada = ?, ruta = ?, status = ? WHERE idcategoria = ?";
            $arrData = array($this->strCategoria, 
                             $this->strDescripcion, 
                             $this->strPortada, 
                             $this->strRuta,
                             $this->intStatus, 
                             $this->intIdcategoria);
            $request = $this->update($sql, $arrData);
            
            // Si el update tiene éxito o simplemente no hubo cambios en el texto, retornamos 1
            return ($request || $request >= 0) ? 1 : 0;
        }else{
            return "exist";
        }
    }

    public function deleteCategoria(int $idcategoria)
    {
        $this->intIdcategoria = $idcategoria;
        $sql = "SELECT * FROM producto WHERE categoriaid = $this->intIdcategoria";
        $request = $this->select_all($sql);
        
        if(empty($request))
        {
            $sql = "UPDATE categoria SET status = ? WHERE idcategoria = ?";
            $arrData = array(0, $this->intIdcategoria);
            $request = $this->update($sql, $arrData);
            return ($request) ? 'ok' : 'error';
        }else{
            return 'exist';
        }
    }

    // MÉTODO PARA ACTUALIZAR ÚNICAMENTE LA IMAGEN
    public function updateImageCategoria(int $idcategoria, string $portada)
    {
        $this->intIdcategoria = $idcategoria;
        $this->strPortada = $portada;
        $sql = "UPDATE categoria SET portada = ? WHERE idcategoria = ?";
        $arrData = array($this->strPortada, $this->intIdcategoria);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}
?>